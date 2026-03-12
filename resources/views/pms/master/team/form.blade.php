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
        <div class="name">
          <div class="row gx-3 align-items-center mb-4">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Team' : 'Add Team' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.team.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="form-box">
                <form action="{{ route('pms.team.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
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
                                    value="{{ old('name', $detail->name ?? '') }}" 
                                    class=" form-control @error('name') is-invalid @enderror"
                                >
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="date">Designation<sup>*</sup></label>
                                 <input 
                                    type="text" 
                                    name="designation" 
                                    value="{{ old('designation', $detail->designation ?? '') }}" 
                                    class=" form-control @error('designation') is-invalid @enderror"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="description">Description<sup>*</sup></label>
    <textarea name="description" id="description" class="form-control h-auto" rows="14">
            {{ old('description', $detail->description ?? '') }}
        </textarea>                            </div>
                        </div>
                        <div class="col-6">
                    
                           <div class="form-field">
                            <label for="image">Main Image<sup>*</sup></label>
                            <input type="file" name="image" accept="image/*"
                                class="form-control @error('image') is-invalid @enderror"
                                onchange="previewImage(this, 'mainImagePreview')">
                                <div class="mt-2">
                                    <img id="mainImagePreview"
                                        src="{{ !empty($detail->image) ? asset('storage/' . $detail->image) : '' }}"
                                        alt="Main Image" width="150"
                                        style="{{ empty($detail->image) ? 'display:none;' : '' }}">
                                </div>
                            </div>
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
            const ckfinderPath = "{{ asset('ckfinder/') }}";
            if (document.getElementById('description')) {
                const fullEditor = CKEDITOR.replace('description');
                CKFinder.setupCKEditor(fullEditor, ckfinderPath);
            }
        });
    </script>
  <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection