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
    /* Image preview CSS without cropping: */
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
                    <h1 class="fs-5 mb-0">@if($detail) Modify Location @else Add New Location @endif</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.location.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
        <form method="post" action="{{ route('pms.location.save') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $detail->id ?? '' }}">

            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">

                         {{-- State --}}
                         <div class="col-6">
                            <div class="form-field">
                                <label for="state">State<sup>*</sup></label>
                                <select name="state_id" class="form-control @error('state_id') is-invalid @enderror">
                                   <option value=" ">Select State</option>
                                    @foreach($states as $value)
                                        <option value="{{ $value->id }}" 
                                            {{ old('state_id', $detail->state_id ?? '') == $value->id ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 

                        {{-- active ru location  --}}
                        <div class="col-6">
                            <div class="form-field">
                                <label for="location">OTA Location<sup>*</sup></label>
                                <select name="location" class="form-control @error('location') is-invalid @enderror">
                                   <option value=" ">Select OTA Location</option>
                                    @foreach($ruLocations as $location)
                                        <option value="{{ $location->ru_location_id }}"
                                            {{ old('location', $detail->ru_location_id ?? '') == $location->ru_location_id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label for="state">Location<sup>*</sup></label>
                                <input type="text" name="location_name" id="location_name" value="{{ old('location_name', $detail->location_name ?? '') }}" class="form-control @error('location_name') border-danger @enderror">
                            </div>
                        </div>  


                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label for="state">Title<sup>*</sup></label>
                                <input type="text" name="title" id="title" value="{{ old('title', $detail->title ?? '') }}" class="form-control @error('title') border-danger @enderror">
                            </div>
                        </div>  
                        
                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label for="state">Sub Title<sup>*</sup></label>
                                <input type="text" name="sub_title" id="sub_title" value="{{ old('sub_title', $detail->sub_title ?? '') }}" class="form-control @error('sub_title') border-danger @enderror">
                            </div>
                        </div>  
                        
                        
                        <div class="col-6">
                            <label for="image">Image</label>
                            <div class="upload-wrapper @error('image') border-danger @enderror" id="uploadWrapper">
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
                                        $imagePath = public_path('storage/location/' . ($detail->image ?? ''));
                                    @endphp
                                    @if(!empty($detail->image) && file_exists($imagePath))
                                        <img src="{{ asset('storage/location/' . $detail->image) }}" alt="Home Banner Image">
                                    @endif
                                </div>
                                <button type="button" class="remove-preview @if(!isset($detail->image)) d-none @endif" onclick="removeImage()">×</button>
                            </div>
                        </div>
                        <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                        {{-- SEO Fields Header --}}
                        <div class="col-12 mt-3 mb-2">
                            <h1 class="fs-5 mb-0">SEO Fields</h1>
                        </div>

                        {{-- Meta Title --}}
                        <div class="col-12">
                            <div class="form-field">
                                <label for="meta_title">Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title', $detail->meta_title ?? '') }}" class="form-control">
                            </div>
                        </div>

                        {{-- Meta Keywords --}}
                        <div class="col-6">
                            <div class="form-field">
                                <label for="meta_keyword">Keywords</label>
                                <textarea name="meta_keyword" class="form-control h-auto">{{ old('meta_keyword', $detail->meta_keyword ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Meta Description --}}
                        <div class="col-6">
                            <div class="form-field">
                                <label for="meta_description">Description</label>
                                <textarea name="meta_description" class="form-control h-auto">{{ old('meta_description', $detail->meta_description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-wrap pt-2">
                <button class="btn btn-primary px-5">@if($detail) UPDATE @else SUBMIT @endif</button>
            </div>
        </form>
    </div>
</section>
<script>
    // Global function to preview image without cropping
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        // Clear any existing preview
        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create an img element without forcing width/height
                const img = document.createElement("img");
                img.src = e.target.result;
                preview.appendChild(img);

                // Hide the upload info and show remove button
                preview.classList.remove('d-none');
                info.style.display = 'none';
                removeBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        input.value = ''; // Clear the file input
        preview.innerHTML = '';
        // Display the upload instructions again
        preview.classList.add('d-none');
        info.style.display = 'block';
        removeBtn.classList.add('d-none');

        document.getElementById('removeImageInput').value = "1";
    }
</script>
@endsection
