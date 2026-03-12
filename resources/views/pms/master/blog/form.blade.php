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
        height: 300px;
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
    .remove-preview {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0,0,0,0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 18px;
        line-height: 30px;
        text-align: center;
        cursor: pointer;
        z-index: 3;
    }
</style>

<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Blog' : 'Add Blog' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.blog.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="form-box">
                <form action="{{ route('pms.blog.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="title">Blog Title<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="title" 
                                    value="{{ old('title', $detail->title ?? '') }}" 
                                    class=" form-control @error('title') is-invalid @enderror"
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="date">Blog Date<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    id="date" 
                                    name="date" 
                                    class="form-control @error('date') is-invalid @enderror flatpickr" 
                                    value="{{ old('date', isset($detail->date) ? \Carbon\Carbon::parse($detail->date)->format('d-m-Y') : '') }}" 
                                    
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="description">Description<sup>*</sup></label>
                                <textarea name="blog_description" class="form-control h-auto" rows="14">{{ old('blog_description', $detail->description ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="image">Image<sup>*</sup></label>
                            <div class="upload-wrapper @error('image') is-invalid @enderror" id="uploadWrapper">
                                <input type="file" name="image" id="imageInput" accept="image/*" onchange="previewImage(event)">
                                <div class="upload-info" id="uploadInfo" onclick="document.getElementById('imageInput').click()" style="@if(isset($detail->image)) display:none; @endif">
                                    <i class="bi bi-upload fs-4"></i>
                                    <div class="upload-info-text">
                                        <strong><span class="text-primary">Browse</span> Your File.</strong>
                                        <small class="d-block">Max size: 3MB | Image size: 100x100px</small>
                                    </div>
                                </div>
                                <div id="imagePreview" class="imagePreview @if(!isset($detail->image)) d-none @endif">
                                    @php
                                        $imagePath = public_path('storage/blog/' . ($detail->image ?? ''));
                                    @endphp
                                    @if(!empty($detail->image) && file_exists($imagePath))
                                        <img src="{{ asset('storage/blog/' . $detail->image) }}" alt="Blog Image">
                                    @endif
                                </div>
                                <button type="button" class="remove-preview @if(!isset($detail->image)) d-none @endif" onclick="removeImage()">×</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="facebook_url">Facebook URL</label>
                                <input 
                                    type="url" 
                                    name="facebook_url" 
                                    value="{{ old('facebook_url', $detail->facebook_url ?? '') }}" 
                                    class="form-control @error('facebook_url') is-invalid @enderror"
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="linkedin_url">LinkedIn URL</label>
                                <input 
                                    type="url" 
                                    name="linkedin_url" 
                                    value="{{ old('linkedin_url', $detail->linkedin_url ?? '') }}" 
                                    class="form-control @error('linkedin_url') is-invalid @enderror"
                                >
                            </div>
                        </div>
                    </div> -->
                    <!-- <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="position">Position</label>
                                <input 
                                    type="number" 
                                    name="position" 
                                    value="{{ old('position', $detail->position ?? '') }}" 
                                    class="form-control @error('position') is-invalid @enderror"
                                    min="0"
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="status">Status</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $detail->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $detail->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div> -->

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
        // ---------- CKEditor Start -------------//
        $('textarea .blog_description').ckeditor();
        var imgEditor = CKEDITOR.replace('blog_description');
        CKFinder.setupCKEditor( imgEditor, path);
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr('#date', {
        dateFormat: 'd-m-Y',
        maxDate: 'today',
        allowInput: true,
        altInput: true,
        altFormat: 'd-m-Y',
        defaultDate: @if(isset($detail->date)) '{{ \Carbon\Carbon::parse($detail->date)->format('d-m-Y') }}' @else null @endif
    });

    window.previewImage = function(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement("img");
                img.src = e.target.result;
                preview.appendChild(img);

                preview.classList.remove('d-none');
                info.style.display = 'none';
                removeBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    window.removeImage = function() {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        input.value = '';
        preview.innerHTML = '';
        preview.classList.add('d-none');
        info.style.display = 'block';
        removeBtn.classList.add('d-none');

        document.getElementById('removeImageInput').value = "1";
    }
});
</script>
@endsection