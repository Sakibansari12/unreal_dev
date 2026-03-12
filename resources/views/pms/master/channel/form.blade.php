@extends('pms.layouts.app')
@section('content')
<style>
    .upload-wrapper {
        border: 2px dashed #C7C7C7;
        border-radius: 5px;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 15px;
        user-select: none;
        background-color: #fff;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
    }
    .upload-wrapper input[type=file] {
        position: absolute;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .upload-wrapper.is-invalid {
        border-color: #dc3545;
    }
    .upload-info {
        z-index: 1;
        pointer-events: none;
    }
    .imagePreview {
        position: relative;
        width: 100%;
        /* height: 250px; Reduced height to accommodate icon and text */
        height: 150px;
        overflow: hidden;
    }
    .imagePreview img {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        object-fit: contain;
        transform: translate(-50%, -50%);
    }
    .remove-preview-container {
        text-align: center;
        margin-top: 5px;
    }
    .remove-preview {
        background-color: transparent;
        color: #dc3545; /* Bootstrap danger color for delete icon */
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .remove-preview:hover {
        color: #b02a37; /* Darker shade on hover */
    }
    .change-image-text {
        font-size: 0.9rem;
        color: #6c757d; /* Bootstrap secondary color */
        margin-top: 5px;
        display: block;
    }
</style>

<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Channel Offer' : 'Add Channel' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.channel.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="form-box">
                <form action="{{ route('pms.channel.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="name">Name<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name', $detail->review_name ?? '') }}" 
                                    class="form-control @error('name') is-invalid @enderror"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                    </div>
                   
                    <div class="row">
                        <div class="col-6">
                            <label for="image">Icon<sup>*</sup></label>
                            <div class="upload-wrapper @error('image') is-invalid @endif" id="uploadWrapper">
                                <input type="file" name="image" id="imageInput" accept="image/*" onchange="previewImage(event)">
                                <div class="upload-info" id="uploadInfo" onclick="document.getElementById('imageInput').click()" style="@if(isset($detail->review_image)) display:none; @endif">
                                    <i class="bi bi-upload fs-4"></i>
                                    <div class="upload-info-text">
                                        <strong><span class="text-primary">Browse</span> Your File.</strong>
                                        <small class="d-block">Max size: 3MB | Image size: 100x100px</small>
                                    </div>
                                </div>
                            
                                <div class="flex-column">
                                        <div id="imagePreview" class="imagePreview @if(!isset($detail->review_image)) d-none @endif">
                                    @php
                                        $imagePath = public_path('storage/channel/' . ($detail->review_image ?? ''));
                                    @endphp
                                    @if(!empty($detail->review_image) && file_exists($imagePath))
                                        <img src="{{ asset('storage/channel/' . $detail->review_image) }}" alt="Icon">
                                    @endif
                                </div>
                                <div class="remove-preview-container @if(!isset($detail->review_image)) d-none @endif" id="removePreviewContainer">
                                    <button type="button" class="remove-preview" onclick="removeImage()">
                                        <!--<i class="bi bi-trash"></i>-->
                                    </button>
                                    <span class="change-image-text">To change the icon please click / drag new image here!</span>
                                </div>
                                </div>
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="btn-wrap pt-2">
                        <button class="btn btn-primary px-5" type="submit">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@php 
    $path = asset('public/ckfinder');
@endphp
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let path = "<?php echo $path; ?>";
        $('textarea.description').ckeditor();
        var imgEditor = CKEDITOR.replace('description');
        CKFinder.setupCKEditor(imgEditor, path);
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('#validity', {
        dateFormat: 'd/m/Y',
        minDate: 'today',
        allowInput: true,
        altInput: true,
        altFormat: 'd/m/Y',
        defaultDate: @if(isset($detail->validity)) '{{ \Carbon\Carbon::parse($detail->validity)->format('d/m/Y') }}' @else null @endif
    });

    window.previewImage = function(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeContainer = document.getElementById('removePreviewContainer');

        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                preview.appendChild(img);

                preview.classList.remove('d-none');
                info.style.display = 'none';
                removeContainer.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    window.removeImage = function() {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeContainer = document.getElementById('removePreviewContainer');

        input.value = '';
        preview.innerHTML = '';
        preview.classList.add('d-none');
        info.style.display = 'block';
        removeContainer.classList.add('d-none');

        document.getElementById('removeImageInput').value = "1";
    }
});
</script>
@endsection