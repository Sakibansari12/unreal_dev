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
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Service' : 'Add Service' }}</h1>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <form action="{{ route('pms.service.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $detail->id ?? '' }}">

                <div class="row">
                    <div class="col-12">
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

                <div class="row mt-4">
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="customer_service_title">Title 1<sup>*</sup></label>
                            <input type="text" name="customer_service_title"
                                value="{{ old('customer_service_title', $detail->customer_service_title ?? '') }}"
                                class="form-control @error('customer_service_title') is-invalid @enderror">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="customer_service_icon">Icon 1<sup>*</sup></label>
                            <input type="file" name="customer_service_icon" accept="image/*"
                                class="form-control @error('customer_service_icon') is-invalid @enderror"
                                onchange="previewImage(this, 'propertiesIconPreview')">
                            <div class="mt-2">
                                <img id="propertiesIconPreview"
                                    src="{{ !empty($detail->customer_service_icon) ? asset('storage/' . $detail->customer_service_icon) : '' }}"
                                    alt="Customer Service Icon" width="100"
                                    style="{{ empty($detail->customer_service_icon) ? 'display:none;' : '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-field">
                            <label for="customer_service_short_description">Description 1<sup>*</sup></label>
                            <textarea name="customer_service_short_description" id="customer_service_short_description"
                                class="form-control h-auto" rows="6">{{ old('customer_service_short_description', $detail->customer_service_short_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="privacy_flexibility_title">Title 2<sup>*</sup></label>
                            <input type="text" name="privacy_flexibility_title"
                                value="{{ old('privacy_flexibility_title', $detail->privacy_flexibility_title ?? '') }}"
                                class="form-control @error('privacy_flexibility_title') is-invalid @enderror">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="privacy_flexibility_icon">Icon 2<sup>*</sup></label>
                            <input type="file" name="privacy_flexibility_icon" accept="image/*"
                                class="form-control @error('privacy_flexibility_icon') is-invalid @enderror"
                                onchange="previewImage(this, 'happyGuestsIconPreview')">
                            <div class="mt-2">
                                <img id="happyGuestsIconPreview"
                                    src="{{ !empty($detail->privacy_flexibility_icon) ? asset('storage/' . $detail->privacy_flexibility_icon) : '' }}"
                                    alt="Privacy Flexibility Icon" width="100"
                                    style="{{ empty($detail->privacy_flexibility_icon) ? 'display:none;' : '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-field">
                            <label for="privacy_flexibility_short_description">Description 2<sup>*</sup></label>
                            <textarea name="privacy_flexibility_short_description" id="privacy_flexibility_short_description"
                                class="form-control h-auto" rows="6">{{ old('privacy_flexibility_short_description', $detail->privacy_flexibility_short_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="professionally_managed_title">Title 3<sup>*</sup></label>
                            <input type="text" name="professionally_managed_title"
                                value="{{ old('professionally_managed_title', $detail->professionally_managed_title ?? '') }}"
                                class="form-control @error('professionally_managed_title') is-invalid @enderror">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="professionally_managed_icon">Icon 3<sup>*</sup></label>
                            <input type="file" name="professionally_managed_icon" accept="image/*"
                                class="form-control @error('professionally_managed_icon') is-invalid @enderror"
                                onchange="previewImage(this, 'hostingExperienceIconPreview')">
                            <div class="mt-2">
                                <img id="hostingExperienceIconPreview"
                                    src="{{ !empty($detail->professionally_managed_icon) ? asset('storage/' . $detail->professionally_managed_icon) : '' }}"
                                    alt="Professionally Managed Icon" width="100"
                                    style="{{ empty($detail->professionally_managed_icon) ? 'display:none;' : '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-field">
                            <label for="professionally_managed_description">Description 3 <sup>*</sup></label>
                            <textarea name="professionally_managed_description" id="professionally_managed_description"
                                class="form-control h-auto" rows="6">{{ old('professionally_managed_description', $detail->professionally_managed_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="best_feature_title">Title 4<sup>*</sup></label>
                            <input type="text" name="best_feature_title"
                                value="{{ old('best_feature_title', $detail->best_feature_title ?? '') }}"
                                class="form-control @error('best_feature_title') is-invalid @enderror">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-field">
                            <label for="best_feature_icon"> Icon 4<sup>*</sup></label>
                            <input type="file" name="best_feature_icon" accept="image/*"
                                class="form-control @error('best_feature_icon') is-invalid @enderror"
                                onchange="previewImage(this, 'bestfeatureIconPreview')">
                            <div class="mt-2">
                                <img id="bestfeatureIconPreview"
                                    src="{{ !empty($detail->best_feature_icon) ? asset('storage/' . $detail->best_feature_icon) : '' }}"
                                    alt="Best Feature Icon" width="100"
                                    style="{{ empty($detail->best_feature_icon) ? 'display:none;' : '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-field">
                            <label for="best_feature_description">Description 4<sup>*</sup></label>
                            <textarea name="best_feature_description" id="best_feature_description"
                                class="form-control h-auto" rows="6">{{ old('best_feature_description', $detail->best_feature_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="btn-wrap">
                            <button class="btn btn-primary px-5" type="submit">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @php
        $path = asset('public/ckfinder');
    @endphp
    <!-- CKEditor with CKFinder -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ckfinderPath = "{{ asset('ckfinder/') }}";

            if (document.getElementById('customer_service_short_description')) {
                const shortEditor = CKEDITOR.replace('customer_service_short_description');
                CKFinder.setupCKEditor(shortEditor, ckfinderPath);
            }

            if (document.getElementById('best_feature_description')) {
                const shortEditor = CKEDITOR.replace('best_feature_description');
                CKFinder.setupCKEditor(shortEditor, ckfinderPath);
            }
            if (document.getElementById('privacy_flexibility_short_description')) {
                const shortEditor = CKEDITOR.replace('privacy_flexibility_short_description');
                CKFinder.setupCKEditor(shortEditor, ckfinderPath);
            }
            if (document.getElementById('professionally_managed_description')) {
                const shortEditor = CKEDITOR.replace('professionally_managed_description');
                CKFinder.setupCKEditor(shortEditor, ckfinderPath);
            }

            if (document.getElementById('description')) {
                const fullEditor = CKEDITOR.replace('description');
                CKFinder.setupCKEditor(fullEditor, ckfinderPath);
            }
        });
    </script>



    <!-- JavaScript for Image Preview -->
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
