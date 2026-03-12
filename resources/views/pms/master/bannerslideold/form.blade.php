@extends('pms.layouts.app')

@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Hero Slide</h1>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="row g-5">

                {{-- Gallery Section --}}
                <div class="col-12 col-lg-9">
                    <div id="message-container" class="mb-3">
                       
                    </div>
                    
                    @if ($errors->has('images'))
                        <div class="alert alert-danger">
                            {{ $errors->first('images') }}
                        </div>
                    @endif
                    <form id="imageForm" action="{{ route('pms.bannerslide.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-field">
                            <label>Images</label>

                            <!-- Upload Wrapper -->
                            <div class="upload-wrapper text-center" onclick="document.getElementById('imageUpload').click();">
                                <input type="file" id="imageUpload" multiple accept="image/*" class="d-none">
                                <div class="upload-box">
                                    <i class="bi bi-upload fs-1 d-block"></i>
                                    <strong><span class="text-primary">Browse</span> Your File.</strong><br>
                                    <!--<small>Minimum 10 images | Max size: 2MB | Min size: 1024px × 683px</small>-->
                                </div>
                            </div>

                            <!-- Image Preview Section -->
                            <div class="row mt-3" id="image-preview-container"></div>
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
    const existingImages = {!! json_encode($images ?? []) !!};

    document.addEventListener('DOMContentLoaded', function () {
        const previewContainer = document.getElementById('image-preview-container');
        const imageUploadInput = document.getElementById('imageUpload');
        const form = document.getElementById('imageForm');
        const messageContainer = document.getElementById('message-container');
        const selectedFiles = [];

        // Auto-dismiss alerts after 5 seconds
        function autoDismissAlerts() {
            const alerts = messageContainer.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }, 5000);
            });
        }

        // Run auto-dismiss on page load for session messages
        autoDismissAlerts();

        // Show existing images
        existingImages.forEach((img, index) => {
            const imgHtml = `
                <div class="col-3 mb-3 image-box" data-id="${img.id}" data-position="${img.position}" draggable="true">
                    <div class="position-relative">
                        <img src="${img.base64_image}" class="w-100 rounded" style="height:150px; object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                    <input type="hidden" name="existing_images[]" value="${img.id}">
                    <input type="hidden" name="positions[${img.id}]" value="${img.position}">
                </div>`;
            previewContainer.insertAdjacentHTML('beforeend', imgHtml);
        });

        // Delete existing images
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-image')) {
                const imageBox = e.target.closest('.image-box');
                const imageId = imageBox.getAttribute('data-id');

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_image_ids[]';
                input.value = imageId;
                form.appendChild(input);

                imageBox.remove();
                updatePositions();
            }
        });

        // New image previews + store in array
        imageUploadInput.addEventListener('change', function (event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                selectedFiles.push(file);
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imgHtml = `
                        <div class="col-3 mb-3 image-box" draggable="true">
                            <div class="position-relative">
                                <img src="${e.target.result}" class="w-100 rounded" style="height:150px; object-fit:cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-new-image">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </div>`;
                    previewContainer.insertAdjacentHTML('beforeend', imgHtml);
                };
                reader.readAsDataURL(file);
            });

            imageUploadInput.value = ''; // clear input
        });

        // Remove new images from UI and array
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-new-image')) {
                const imageBox = e.target.closest('.image-box');
                const index = Array.from(previewContainer.children).indexOf(imageBox);
                if (index !== -1) {
                    selectedFiles.splice(index, 1);
                }
                imageBox.remove();
            }
        });

        // Drag and Drop for reordering
        previewContainer.addEventListener('dragstart', function (e) {
            const imageBox = e.target.closest('.image-box');
            if (imageBox) {
                e.dataTransfer.setData('text/plain', imageBox.dataset.id || 'new');
                imageBox.classList.add('dragging');
                console.log('Dragging started:', imageBox.dataset.id || 'new');
            }
        });

        previewContainer.addEventListener('dragend', function (e) {
            const imageBox = e.target.closest('.image-box');
            if (imageBox) {
                imageBox.classList.remove('dragging');
                console.log('Dragging ended');
            }
        });

        previewContainer.addEventListener('dragover', function (e) {
            e.preventDefault();
            const imageBox = e.target.closest('.image-box');
            if (imageBox) {
                imageBox.classList.add('drag-over');
            }
        });

        previewContainer.addEventListener('dragleave', function (e) {
            const imageBox = e.target.closest('.image-box');
            if (imageBox) {
                imageBox.classList.remove('drag-over');
            }
        });

        previewContainer.addEventListener('drop', function (e) {
            e.preventDefault();
            const imageBox = e.target.closest('.image-box');
            if (imageBox) {
                imageBox.classList.remove('drag-over');
                const draggedId = e.dataTransfer.getData('text/plain');
                const draggedBox = previewContainer.querySelector(`.image-box[data-id="${draggedId}"]`) || Array.from(previewContainer.children).find(box => !box.dataset.id);

                if (draggedBox && draggedBox !== imageBox) {
                    const allBoxes = Array.from(previewContainer.children);
                    const draggedIndex = allBoxes.indexOf(draggedBox);
                    const targetIndex = allBoxes.indexOf(imageBox);

                    if (draggedIndex < targetIndex) {
                        imageBox.after(draggedBox);
                    } else {
                        imageBox.before(draggedBox);
                    }

                    console.log('Dropped:', draggedId, 'at position:', targetIndex + 1);
                    updatePositions();
                }
            }
        });

        function updatePositions() {
            const imageBoxes = previewContainer.querySelectorAll('.image-box');
            imageBoxes.forEach((box, index) => {
                const imageId = box.dataset.id;
                if (imageId) {
                    const positionInput = box.querySelector(`input[name="positions[${imageId}]"]`);
                    if (positionInput) {
                        positionInput.value = index + 1;
                        box.dataset.position = index + 1;
                    }
                }
            });
            console.log('Positions updated:', Array.from(imageBoxes).map(box => box.dataset.id ? `${box.dataset.id}: ${box.dataset.position}` : 'new'));
        }

        // Submit form with selected files
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Create a hidden file input for selected files
            if (selectedFiles.length > 0) {
                const tempFileInput = document.createElement('input');
                tempFileInput.type = 'file';
                tempFileInput.name = 'images[]';
                tempFileInput.multiple = true;
                tempFileInput.style.display = 'none';

                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                tempFileInput.files = dataTransfer.files;

                form.appendChild(tempFileInput);
            }

            // Submit the form
            form.submit();
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
        position: relative;
        padding: 10px;
    }
    .upload-box {
        pointer-events: none;
    }
    .image-box {
        cursor: move;
        transition: all 0.2s ease;
    }
    .image-box.dragging {
        opacity: 0.5;
        transform: scale(0.95);
    }
    .image-box.drag-over {
        border: 2px solid #007bff;
        border-radius: 5px;
    }
</style>