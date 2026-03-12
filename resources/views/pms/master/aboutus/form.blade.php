@extends('pms.layouts.app')
@section('content')

<style>
    .upload-wrapper {
        border: 2px dashed #dcdcdc;
        border-radius: 8px;
        height: 305px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
        background: #fafafa;
        overflow: hidden;
    }

    .upload-preview {
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: none;
    }

    .upload-wrapper input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .remove-image-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
    }

    .upload-wrapper.has-image .remove-image-btn {
        display: flex;
    }

    .service-box {
        background: #eef7ee;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .remove-service {
        position: absolute;
        margin: 1px;
        top: 12px;
        right: 12px;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: none;
        background: #dc3545;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>

<section class="section">
    <div class="container-fluid">
        <div class="content-box p-3">

            <form method="POST" action="{{ route('pms.aboutus.save') }}" enctype="multipart/form-data">
                @csrf

                {{-- ================= BANNER IMAGE ================= --}}
                <label class="fw-bold">Banner Image</label>

                <div class="upload-wrapper mb-4 {{ !empty($data->banner) ? 'has-image' : '' }}">
                    <input type="file" name="banner_image" accept="image/*" onchange="previewImage(this)">
                    <div class="upload-preview">
                        <button type="button" class="remove-image-btn" onclick="removeImageFromWrapper(this)">
                            <i class="bi bi-x"></i>
                        </button>
                        <img src="{{ !empty($data->banner) ? asset('storage/about_us/'.$data->banner) : '' }}"
                            style="{{ empty($data->banner) ? 'display:none' : 'display:block' }}">
                        <p class="text-muted {{ !empty($data->banner) ? 'd-none' : '' }}">
                            Click / drag banner image here
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Banner Text</label>
                    <input type="text" name="banner_text" class="form-control"
                        value="{{ old('banner_text', $data->banner_text ?? '') }}">
                </div>

                {{-- ================= ABOUT TEXT ================= --}}
                <div class="row">
                    <div class="col-md-12">
                        <label>About Text</label>
                        <textarea name="about_text" id="about_text">
{!! old('about_text', $data->about_content ?? '') !!}
</textarea>
                    </div>

                    <!-- <div class="col-md-6">
                        <label>About Service Content</label>
                        <textarea name="about_service_text" id="about_service_text">
{!! old('about_service_text', $data->service_content ?? '') !!}
</textarea>
                    </div> -->
                </div>


                {{-- ================= SERVICE CERTAINTY ================= --}}
                <!-- <h5>About Service Certainty</h5>

                <div id="serviceContainer">
                    @foreach($data->aboutInformation as $i => $service)
                    <div class="service-box row g-3">

                        <button type="button" class="remove-service"><i class="bi bi-trash"></i></button>
                        <input type="hidden" name="services[{{ $i }}][id]" value="{{ $service->id }}">

                        <div class="col-12">
                            <label>Title</label>
                            <input type="text" class="form-control"
                                name="services[{{ $i }}][title]" value="{{ $service->title }}">
                        </div>

                        <div class="col-md-6">
                            <label>Image</label>
                            <div class="upload-wrapper {{ $service->image ? 'has-image' : '' }}">
                                <input type="file" name="services[{{ $i }}][image]" accept="image/*"
                                    onchange="previewImage(this)">
                                <div class="upload-preview">
                                    <button type="button" class="remove-image-btn"
                                        onclick="removeImageFromWrapper(this)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <img src="{{ $service->image_path }}"
                                        style="{{ $service->image ? 'display:block' : 'display:none' }}">
                                    <p class="text-muted {{ $service->image ? 'd-none' : '' }}">Click / drag image</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Content</label>
                            <textarea name="services[{{ $i }}][content]" class="ckeditor">
                            {{ $service->text }}
                            </textarea>
                        </div>

                    </div>
                    @endforeach
                </div>

                <button type="button" id="addService" class="btn btn-secondary mt-3">
                    <i class="bi bi-plus-lg"></i> Add More
                </button> -->

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">SUBMIT</button>
                </div>

            </form>
        </div>
    </div>
</section>

{{-- ================= CKEDITOR (UNCHANGED) ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ckfinderPath = "{{ asset('ckfinder/') }}";
        CKEDITOR.replace('about_text');
        // CKEDITOR.replace('about_service_text');
        CKEDITOR.replaceAll('ckeditor');
    });
</script>

{{-- ================= IMAGE JS ================= --}}
<script>
    function previewImage(input) {
        const file = input.files[0];
        if (!file) return;

        const wrapper = input.closest('.upload-wrapper');
        const img = wrapper.querySelector('img');
        const text = wrapper.querySelector('p');

        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            text.classList.add('d-none');
            wrapper.classList.add('has-image');
        };
        reader.readAsDataURL(file);
    }

    function removeImageFromWrapper(btn) {
        const wrapper = btn.closest('.upload-wrapper');
        const img = wrapper.querySelector('img');
        const input = wrapper.querySelector('input[type=file]');
        const text = wrapper.querySelector('p');

        img.src = '';
        img.style.display = 'none';
        input.value = '';
        text.classList.remove('d-none');
        wrapper.classList.remove('has-image');
    }

    // let serviceIndex = {{$data->aboutInformation->count() ?? 0 }};

    // document.getElementById('addService').addEventListener('click', () => {
    //     const html = `
    // <div class="service-box row g-3">
    //     <button type="button" class="remove-service"><i class="bi bi-trash"></i></button>
    //     <div class="col-12">
    //         <label>Title</label>
    //         <input type="text" name="services[${serviceIndex}][title]" class="form-control">
    //     </div>
    //     <div class="col-md-6">
    //         <label>Image</label>
    //         <div class="upload-wrapper">
    //             <input type="file" name="services[${serviceIndex}][image]" accept="image/*"
    //                    onchange="previewImage(this)">
    //             <div class="upload-preview">
    //                 <button type="button" class="remove-image-btn"
    //                         onclick="removeImageFromWrapper(this)">
    //                     <i class="bi bi-x"></i>
    //                 </button>
    //                 <img>
    //                 <p class="text-muted">Click / drag image</p>
    //             </div>
    //         </div>
    //     </div>
    //     <div class="col-md-6">
    //         <label>Content</label>
    //         <textarea name="services[${serviceIndex}][content]" class="ckeditor"></textarea>
    //     </div>
    // </div>`;
    //     document.getElementById('serviceContainer').insertAdjacentHTML('beforeend', html);
    //     CKEDITOR.replaceAll('ckeditor');
    //     serviceIndex++;
    // });

    document.addEventListener('click', e => {
        if (e.target.closest('.remove-service')) {
            e.target.closest('.service-box').remove();
        }
    });
</script>

@endsection