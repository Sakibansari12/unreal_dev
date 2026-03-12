@extends('pms.layouts.app')

@section('content')
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

        <style>
            .page-loader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.95); display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 9999; }
            body.loading { overflow: hidden; }
            .page-loader.hide { display: none !important; }
            .ulTab { list-style-type: none; margin: 0; padding: 0; overflow-x: auto; display: block !important; }
            .ulTab li { margin: 10px 5px; }
            .ulTab li button, .ulTab li a { border: 1px solid #0E0E0E; padding: 10px 15px; background-color: #fff; width: 100%; border-radius: 6px !important; text-align: left; display: block; text-decoration: none; }
            .ulTab li button.active, .ulTab li a.active { color: #fff; background-color: #0e0e0e; }
            #submitBtn { cursor: pointer !important; pointer-events: auto !important; }
        </style>

        <div class="content-box p-3">
            <div class="row g-5">
                @include('pms.property.unit-or-multiunit-menu-segments')

                <!-- Gallery Section -->
                <div class="col-12 col-lg-9">
                    <div id="message-container" class="mb-3"></div>

                    <form id="imageForm" method="POST" enctype="multipart/form-data" action="{{ route('pms.property.unit.or.multiunit.layoutimages.save') }}">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="form-field">
                            <label>Images </label>

                            <div class="upload-wrapper text-center" onclick="document.getElementById('imageUpload').click();">
                                <input type="file" id="imageUpload" multiple accept="image/*" class="d-none">
                                <div class="upload-box">
                                    <i class="bi bi-upload fs-1 d-block"></i>
                                    <strong><span class="text-primary">Browse</span> Your File.</strong><br>
                                    <small>Max size: 5MB | Min Dimensions: 1024px × 683px</small>
                                </div>
                            </div>

                            <div class="row mt-3" id="image-preview-container"></div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-primary mt-3">
                            <span id="btnText">Upload</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script>
     const imageBasePath = "{{ asset('storage/layoutImages') }}";
    const existingImages = {!! json_encode($existingImages) !!};

    document.addEventListener('DOMContentLoaded', function () {
        const previewContainer = document.getElementById('image-preview-container');
        const imageInput = document.getElementById('imageUpload');
        const form = document.getElementById('imageForm');
        const messageContainer = document.getElementById('message-container');

        function autoDismissAlerts() {
            setTimeout(() => {
                const alerts = messageContainer.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);
        }
        autoDismissAlerts();

        // Show existing images
        existingImages.forEach((img, index) => {  
            const imgHtml = `
                <div class="col-3 mb-3 image-box" data-id="${img.id}" data-position="${img.position}" draggable="true">
                    <div class="position-relative">
                        <img src="${imageBasePath}/${img.filename}"" class="w-100 rounded" style="height:150px; object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                    <input type="hidden" name="existing_images[]" value="${img.id}">
                    <input type="hidden" name="positions[${img.id}]" value="${img.position}">
                </div>`;
            previewContainer.insertAdjacentHTML('beforeend', imgHtml);
        });

        imageInput.addEventListener('change', function () {
            const maxSize = 5 * 1024 * 1024;
            const minWidth = 1024;
            const minHeight = 683;
            const MAX_NEW_FILES = 25;
            const files = Array.from(this.files);
            const alreadyNewCount = previewContainer.querySelectorAll('.image-box[data-id^="new-"]').length;

            if (alreadyNewCount + files.length > MAX_NEW_FILES) {
                showError(`You can upload a maximum of ${MAX_NEW_FILES} images at a time.`);
                this.value = '';
                return;
            }

            files.forEach((file) => {
                if (file.size > maxSize) {
                    showError('Each image must be under 5MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.onload = function () {
                        if (img.width < minWidth || img.height < minHeight) {
                            showError(`Image must be at least ${minWidth}×${minHeight}px. This is ${img.width}×${img.height}px`);
                            return;
                        }

                        const uniqueId = Math.random().toString(36).substr(2, 9);
                        const imgHtml = `
                            <div class="col-3 mb-3 image-box" data-id="new-${uniqueId}" draggable="true">
                                <div class="position-relative">
                                    <img src="${e.target.result}" class="w-100 rounded" style="height:150px; object-fit:cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                                <input type="hidden" name="new_images[]" value="${e.target.result}">
                            </div>`;
                        previewContainer.insertAdjacentHTML('beforeend', imgHtml);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });

            this.value = '';
        });

        function showError(msg) {
            messageContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            autoDismissAlerts();
        }

        // Remove image
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-image')) {
                const box = e.target.closest('.image-box');
                const id = box.dataset.id;
                if (id && !id.startsWith('new-')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'deleted_image_ids[]';
                    input.value = id;
                    form.appendChild(input);
                }
                box.remove();
                updatePositions();
            }
        });

        // Drag & Drop
        let draggedBox = null;

        previewContainer.addEventListener('dragstart', e => {
            draggedBox = e.target.closest('.image-box');
            if (draggedBox) draggedBox.classList.add('dragging');
        });

        previewContainer.addEventListener('dragend', () => {
            if (draggedBox) draggedBox.classList.remove('dragging');
            draggedBox = null;
        });

        previewContainer.addEventListener('dragover', e => e.preventDefault());
        previewContainer.addEventListener('drop', e => {
            e.preventDefault();
            const targetBox = e.target.closest('.image-box');
            if (targetBox && draggedBox && draggedBox !== targetBox) {
                if (previewContainer.children[Array.from(previewContainer.children).indexOf(draggedBox)] < targetBox) {
                    targetBox.after(draggedBox);
                } else {
                    targetBox.before(draggedBox);
                }
                updatePositions();
            }
        });

        function updatePositions() {
            previewContainer.querySelectorAll('.image-box').forEach((box, index) => {
                const id = box.dataset.id;
                if (id && !id.startsWith('new-')) {
                    const posInput = box.querySelector(`input[name="positions[${id}]"]`);
                    if (posInput) posInput.value = index + 1;
                    box.dataset.position = index + 1;
                }
            });
        }

        // Submit loader
        form.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('btnText');
            const loader = document.getElementById('btnLoader');
            btn.disabled = true;
            text.textContent = 'Uploading...';
            loader.classList.remove('d-none');
        });
    });
</script>

<style>
    .upload-wrapper {
        border: 2px dashed #C7C7C7;
        border-radius: 5px;
        height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        cursor: pointer;
        padding: 10px;
    }
    .upload-box { pointer-events: none; }
    .image-box { cursor: move; transition: all 0.2s ease; }
    .image-box.dragging { opacity: 0.5; transform: scale(0.95); }
</style>