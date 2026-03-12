@extends('pms.layouts.app')

@section('content')
<style>
    .upload-wrapper {
        border: 2px dashed #cbd5e1;
        padding: 40px;
        border-radius: 10px;
        cursor: pointer;
        background: #f8fafc;
        transition: all 0.2s ease-in-out;
    }

    .upload-wrapper:hover {
        background: #eef2ff;
        border-color: #3BB6B1;
    }

    .upload-box {
        pointer-events: none;
    }

    .upload-box i {
        color: #3BB6B1;
    }
</style>
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $id ? 'Update' : 'Add' }} {{ ucfirst(request()->pType) }} (Property: {{ $parentHome->unit_name }})</h1>
                </div>
                <div class="col-auto">
                    @php
                    $route = request()->type === 'published'
                    ? route('pms.published.property.list', ['pType' => 'unit'])
                    : route('pms.property.unit.or.multiunit.list', [
                    'property_id' => request()->property_id,
                    'pType' => request()->pType,
                    ]);
                    @endphp

                    <a href="{{ $route }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="row g-5">
                <style>
                    .ulTab {
                        list-style-type: none;
                        margin: 0;
                        padding: 0;
                        overflow-x: auto;
                        display: block !important;
                    }

                    .ulTab li {
                        margin: 10px 5px;
                    }

                    .ulTab li button,
                    .ulTab li a {
                        border: 1px solid #0E0E0E;
                        padding: 10px 15px;
                        background-color: #fff;
                        width: 100%;
                        border-radius: 6px !important;
                        text-align: left;
                        display: block;
                        text-decoration: none;
                    }

                    .ulTab li button.active,
                    .ulTab li a.active {
                        color: #fff;
                        background-color: #0e0e0e;
                    }
                </style>

                @include('pms.property.unit-or-multiunit-menu-segments')
                <div class="col-12 col-lg-9">
                    <div id="message-container" class="mb-3">
                        @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                    </div>

                    <form id="imageForm" method="POST" action="{{ route('pms.property.unit.or.multiunit.video.save') }}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="form-field">
                            <label>Upload Video <span class="text-danger">*</span></label>

                            <!-- Upload Wrapper -->
                            <div class="upload-wrapper text-center" onclick="document.getElementById('imageUpload').click();">
                                <input type="file" id="imageUpload" name="video_file" accept="video/mp4" class="d-none">
                                <div class="upload-box">
                                    <i class="bi bi-upload fs-1 d-block"></i>
                                    <strong><span class="text-primary">Browse</span> Your File.</strong><br>
                                    <small>Max size: 200MB</small>
                                </div>
                            </div>
                            <input type="hidden" name="tbl_image_video_id" value="{{ $existingVideos->id ?? '' }}">
                            <!-- Video Preview Section -->
                            <div class="row mt-3" id="image-preview-container">
                                @if (!empty($existingVideos) && !empty($existingVideos->filename))
                                <div class="col-12 mb-3 image-box">
                                    <div class="position-relative">
                                        <video controls preload="metadata" style="width:100%; height:250px; object-fit:cover;">
                                            <source src="{{ asset($existingVideos->filename) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image"
                                            data-id="{{ $existingVideos->id }}"
                                            data-ptype="{{ $existingVideos->pType }}">
                                            <span class="material-symbols-outlined">delete </span>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const previewContainer = document.getElementById('image-preview-container');
        const imageInput = document.getElementById('imageUpload');
        const form = document.getElementById('imageForm');
        const messageContainer = document.getElementById('message-container');

        function autoDismissAlerts() {
            const alerts = messageContainer.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }, 5000);
            });
        }

        autoDismissAlerts();

        // Show video preview
        imageInput.addEventListener('change', function() {
            previewContainer.innerHTML = ''; // Remove previous previews
            const file = this.files[0];
            if (!file) return;

            // Validation: Only mp4 and max 200MB
            const validTypes = ['video/mp4'];
            const maxSize = 200 * 1024 * 1024;
            if (!validTypes.includes(file.type)) {
                showError('Only MP4 videos are allowed.');
                this.value = '';
                return;
            }

            if (file.size > maxSize) {
                showError('Video size must be under 200MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                console.log(e, "e");
                const videoHtml = `
                    <div class="col-12 mb-3 image-box">
                        <div class="position-relative">
                            <video class="card-img-top" controls style="width:100%; height:250px; object-fit:cover;">
                                <source src="${e.target.result}" type="video/mp4">
                            </video>
                            
                        </div>
                    </div>`;
                previewContainer.insertAdjacentHTML('beforeend', videoHtml);
            };
            reader.readAsDataURL(file); // Still used for preview
        });

        // Remove video
        document.addEventListener('click', function(e) {

            const removeBtn = e.target.closest('.remove-image');
            if (removeBtn) {
                const videoId = removeBtn.dataset.id;
                const pType = removeBtn.dataset.ptype;

                //if (!confirm("Are you sure you want to delete this video?")) return;

                fetch(`{{ route('gallery.delete.video') }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: videoId,
                            pType: pType
                        }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            document.getElementById('image-preview-container').innerHTML = '';
                            toastr.success('Video deleted successfully', {
                                timeOut: 2000
                            });
                            // alert("Video deleted successfully ✅");
                        } else {
                            toastr.error('Something went wrong!', 'Error', {
                                timeOut: 2000
                            });
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                        alert("Something went wrong ❌");
                    });
            }



            if (e.target.closest('.remove-image')) {
                previewContainer.innerHTML = '';
                imageInput.value = '';
            }


        });

        // Error alert
        function showError(message) {
            messageContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            autoDismissAlerts();
        }

        // Form submit validation
        // form.addEventListener('submit', function (e) {
        //     const file = imageInput.files[0];
        //     if (!file) {
        //         e.preventDefault();
        //         showError('Please select a video before uploading.');
        //         return;
        //     }

        // });

        // Form submit validation with loader
        form.addEventListener('submit', function(e) {
            const file = imageInput.files[0];
            if (!file) {
                e.preventDefault();
                showError('Please select a video before uploading.');
                return;
            }

            // Disable button & show loader
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Uploading...
    `;

            // Let the form submit normally (no AJAX)
            form.submit();

            // Optional fallback: in case page doesn’t reload or something goes wrong
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }, 15000); // reset after 15 sec if no response
        });

    });
</script>