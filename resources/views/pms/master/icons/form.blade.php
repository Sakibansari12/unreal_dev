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
        background-color: rgba(0, 0, 0, 0.5);
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
    <div class="container">

        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $detail ? 'Update Icon' : 'Add Icon' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.icons.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i>
                        <span>Go to list</span>
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('pms.icons.save') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $detail->id ?? '' }}">
            <input type="hidden" name="remove_image" id="removeImageInput" value="0">

            <div class="content-box p-4 mt-3">

                {{-- Name --}}
                <div class="mb-4">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text"
                        name="icons_name"
                        class="form-control"
                        value="{{ old('icons_name', $detail->icons_name ?? '') }}"
                        required>
                </div>

                {{-- Icon Upload --}}
                <div class="mb-4">
                    <label class="form-label">Icon <span class="text-danger">*</span></label>

                    <div class="upload-wrapper" id="uploadWrapper">
                        <input type="file"
                            name="icons_image"
                            id="imageInput"
                            accept=".svg,.png,.jpg,.jpeg,.webp"
                            onchange="previewImage(event)">

                        <div class="upload-info" id="uploadInfo"
                            style="{{ !empty($detail->icons_image) ? 'display:none' : '' }}">
                            <i class="icon-upload fs-3"></i>
                            <p class="mb-1"><strong>Drag & Drop Or Browse Your File.</strong></p>
                            <small>Max size: 1mb | Image size: 100px × 100px</small>
                        </div>

                        <div id="imagePreview"
                            class="imagePreview {{ empty($detail->icons_image) ? 'd-none' : '' }}">
                            @if(!empty($detail->icons_image) && file_exists(public_path('storage/icons/'.$detail->icons_image)))
                            <img src="{{ asset('storage/icons/'.$detail->icons_image) }}">
                            @endif
                        </div>

                        <button type="button"
                            class="remove-preview {{ empty($detail->icons_image) ? 'd-none' : '' }}"
                            onclick="removeImage()">×</button>
                    </div>
                </div>

                <button class="btn btn-success px-4">
                    {{ $detail ? 'UPDATE' : 'SUBMIT' }}
                </button>

            </div>
        </form>

    </div>
</section>

{{-- JS --}}
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        // 🔥 IMPORTANT FIX
        document.getElementById('removeImageInput').value = "0";

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


    function removeImage() {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        input.value = '';
        preview.innerHTML = '';
        preview.classList.add('d-none');
        info.style.display = 'block';
        removeBtn.classList.add('d-none');

        document.getElementById('removeImageInput').value = "0";

    }
</script>

@endsection