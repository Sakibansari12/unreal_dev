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
                    
                    <a href="{{ $route }}"  class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
        
        <style>
            /* Full screen loader */
            .page-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.95);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 9999;
            }
        
            body.loading {
                overflow: hidden; /* Page scroll disable jab tak load ho raha ho */
            }
        
            .page-loader.hide {
                display: none !important;
            }
        </style>
        
        
     

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
                    
                    #submitBtn {
                        cursor: pointer !important;
                        pointer-events: auto !important;
                    }
                </style>

                @include('pms.property.unit-or-multiunit-menu-segments')

                {{-- Gallery Section --}}
                <div class="col-12 col-lg-9">
                    <div id="message-container" class="mb-3">

                    </div>
                    <form id="imageForm" method="POST" enctype="multipart/form-data" action="{{ route('pms.property.unit.or.multiunit.gallery.save') }}">
                        @csrf

                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="form-field">
                            <label>Images <span class="text-danger">*</span></label>

                            <!-- Upload Wrapper -->
                            <div class="upload-wrapper text-center" onclick="document.getElementById('imageUpload').click();">
                                <input type="file" id="imageUpload" multiple accept="image/*" class="d-none">
                                <div class="upload-box">
                                    <i class="bi bi-upload fs-1 d-block"></i>
                                    <strong><span class="text-primary">Browse</span> Your File.</strong><br>
                                    <small>Minimum 10 images | Max size: 5MB | Min size Dimensions:1024px x 683px , Max size: 3000px x 2000px | 72 DPI</small>
                                </div>
                            </div>

                            <!-- Image Preview Section -->
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
    const existingImages = {!! json_encode($existingImages) !!};
    const categoryOptions = {!! json_encode($options) !!};

    document.addEventListener('DOMContentLoaded', function () {
        const previewContainer = document.getElementById('image-preview-container');
        const imageInput = document.getElementById('imageUpload');
        const form = document.getElementById('imageForm');
        const messageContainer = document.getElementById('message-container');

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

        // Run auto-dismiss on page load
        autoDismissAlerts();

        // Show existing images
        existingImages.forEach((img, index) => {
            const selectedOption = categoryOptions.map(opt =>
                `<option value="${opt}" ${opt === img.title ? 'selected' : ''}>${opt}</option>`
            ).join('');

            const imgHtml = `
                <div class="col-3 mb-3 image-box" data-id="${img.id}" data-position="${img.position}" draggable="true">
                    <div class="position-relative">
                        <img src="${img.img_url}" class="w-100 rounded" style="height:150px; object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                    <select name="image_categories[${img.id}]" class="form-select mt-2">
                        ${selectedOption}
                    </select>
                    <input type="hidden" name="existing_images[]" value="${img.id}">
                    <input type="hidden" name="positions[${img.id}]" value="${img.position}">
                </div>`;
            previewContainer.insertAdjacentHTML('beforeend', imgHtml);
        });

        imageInput.addEventListener('change', function () {
    const maxSize = 5 * 1024 * 1024; // 2MB
    const minWidth = 1024;
    const minHeight = 683;
    const maxWidth = 3000;
    const maxHeight = 2000;
    const MAX_NEW_FILES = 25;
    const files = Array.from(this.files);

    // start image limits 08_08_2025
        const alreadyNewCount = previewContainer.querySelectorAll('.image-box[data-id^="new-"]').length;
        if (alreadyNewCount + files.length > MAX_NEW_FILES) {
            showError(`You can upload a maximum of ${MAX_NEW_FILES} images. At one time.`);
            this.value = '';
            return;
        }

      // start image limits 08_08_2025



    // files.forEach((file, index) => {
    //     if (file.size > maxSize) {
    //         showError('Image size must be under 5MB.');
    //         return;
    //     }

    //     const reader = new FileReader();

    //     reader.onload = function (e) {
    //         const img = new Image();
    //         img.src = e.target.result;

    //         img.onload = function () {
    //             const { width, height } = img;

    //             if (width < minWidth || height < minHeight) {
    //                 showError(`Image must be at least ${minWidth}px × ${minHeight}px. Uploaded: ${width}px × ${height}px`);
    //                 return;
    //             }

    //             if (width > maxWidth || height > maxHeight) {
    //                 showError(`Image must not exceed ${maxWidth}px × ${maxHeight}px. Uploaded: ${width}px × ${height}px`);
    //                 return;
    //             }

    //             const uniqueId = Math.random().toString(36).substring(2, 10);
    //             const imgHtml = `
    //                 <div class="col-3 mb-3 image-box" data-id="new-${uniqueId}" draggable="true">
    //                     <div class="position-relative">
    //                         <img src="${e.target.result}" class="w-100 rounded" style="height:150px; object-fit:cover;">
    //                         <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image">
    //                             <span class="material-symbols-outlined">delete</span>
    //                         </button>
    //                     </div>
    //                     <select name="image_categories[new-${uniqueId}]" class="form-select mt-2">
                         
    //                         ${categoryOptions.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
    //                     </select>
    //                     <input type="hidden" name="new_images[]" value="${e.target.result}">
    //                 </div>`;
    //             previewContainer.insertAdjacentHTML('beforeend', imgHtml);
    //         };
    //     };

    //     reader.readAsDataURL(file);
    // });
    
    
    files.forEach((file, index) => {
        if (file.size > maxSize) {
            showError('Image size must be under 5MB.');
            return;
        }
    
        const reader = new FileReader();
    
        reader.onload = function (e) {
            const img = new Image();
            img.src = e.target.result;
    
            img.onload = function () {
                const { width, height } = img;
    
                if (width < minWidth || height < minHeight) {
                    showError(`Image must be at least ${minWidth}px × ${minHeight}px. Uploaded: ${width}px × ${height}px`);
                    return;
                }
    
                // if (width > maxWidth || height > maxHeight) {
                //     showError(`Image must not exceed ${maxWidth}px × ${maxHeight}px. Uploaded: ${width}px × ${height}px`);
                //     return;
                // }
    
                const uniqueId = Math.random().toString(36).substring(2, 10);
    
                // Check if Main Image already exists
                const hasMainImage = Array.from(document.querySelectorAll('select.form-select')).some(select => select.value === 'Main Image');
    
                // Assign category: First image as Main Image only if none exists, rest as Interior
                let selectedCategory;
                if (!hasMainImage && index === 0) {
                    selectedCategory = 'Main Image';
                }
                else {
                    selectedCategory = 'Interiors';
                }
    
                const imgHtml = `
                    <div class="col-3 mb-3 image-box" data-id="new-${uniqueId}" draggable="true">
                        <div class="position-relative">
                            <img src="${e.target.result}" class="w-100 rounded" style="height:150px; object-fit:cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 remove-image">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                        <select name="image_categories[new-${uniqueId}]" class="form-select mt-2">
                            ${categoryOptions.map(opt => {
                                return `<option value="${opt}" ${opt === selectedCategory ? 'selected' : ''}>${opt}</option>`;
                            }).join('')}
                        </select>
                        <input type="hidden" name="new_images[]" value="${e.target.result}">
                    </div>`;
                previewContainer.insertAdjacentHTML('beforeend', imgHtml);
            };
        };
    
        reader.readAsDataURL(file);
    });




    this.value = ''; // reset the file input

    // Error display function
    function showError(message) {
        messageContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        autoDismissAlerts();
    }
});
        // Remove image
        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-image')) {
                const imageBox = e.target.closest('.image-box');
                const imageId = imageBox.getAttribute('data-id');
                if (!imageId.startsWith('new-')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'deleted_image_ids[]';
                    input.value = imageId;
                    form.appendChild(input);
                }
                imageBox.remove();
                updatePositions();
            }
        });

        // Drag and Drop for reordering
        previewContainer.addEventListener('dragstart', function (e) {
            const imageBox = e.target.closest('.image-box');
            if (imageBox) {
                e.dataTransfer.setData('text/plain', imageBox.dataset.id);
                imageBox.classList.add('dragging');
                console.log('Dragging started:', imageBox.dataset.id);
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
                const draggedBox = previewContainer.querySelector(`.image-box[data-id="${draggedId}"]`);

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
                if (imageId && !imageId.startsWith('new-')) {
                    const positionInput = box.querySelector(`input[name="positions[${imageId}]"]`);
                    if (positionInput) {
                        positionInput.value = index + 1;
                        box.dataset.position = index + 1;
                    }
                }
            });
            console.log('Positions updated:', Array.from(imageBoxes).map(box => box.dataset.id ? `${box.dataset.id}: ${box.dataset.position || 'new'}` : 'new'));
        }

        // Client-side validation for category selection
        form.addEventListener('submit', function (e) {
            // Validation check
            let isValid = true;
            const imageBoxes = previewContainer.querySelectorAll('.image-box');
        
            // Reset all dropdown borders
            imageBoxes.forEach(box => {
                const select = box.querySelector('.form-select');
                select.style.border = '';
            });
        
            // Check each image box for category selection
            imageBoxes.forEach(box => {
                const select = box.querySelector('.form-select');
                if (!select.value) {
                    select.style.border = '2px solid red';
                    isValid = false;
                }
            });
        
            if (!isValid) {
                e.preventDefault(); // Sirf validation fail hone par rokna
                messageContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Please select a category for all uploaded images.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                autoDismissAlerts();
                return;
            }
        
            // Show loader & disable button
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
        
            submitBtn.disabled = true;
            btnText.textContent = "Uploading...";
            btnLoader.classList.remove('d-none');
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