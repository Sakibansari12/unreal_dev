@extends('pms.layouts.app')

@section('content')
<style>
    .toast-message { color: white !important; }
    .toast.toast-error {
        background-color: red !important;
        color: white !important;
    }
    .upload-box {
        /*border: 2px dashed #ccc;*/
        padding: 20px;
        text-align: center;
        cursor: pointer;
        display: block;
    }
    .upload-box:hover {
        background-color: #f8f9fa;
    }
    .image-preview img, .video-preview video, .banner-preview img {
        max-width: 100%;
        max-height: 200px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .image-preview .remove-btn, .video-preview .remove-btn, .banner-preview .remove-btn {
        color: red;
        cursor: pointer;
        font-size: 1.2rem;
        position: absolute;
        top: 5px;
        right: 5px;
        background: white;
        border-radius: 50%;
        padding: 2px 6px;
    }
    .image-preview, .video-preview, .banner-preview {
        position: relative;
        text-align: center;
    }
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
</style>
<div class="container">
    <div class="title">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="fs-5 mb-0">{{ $id ? 'Update' : 'Add' }} {{ ucfirst(request()->pType) }} Property: {{ $parentHome->unit_name }})</h1>
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
            </style>

            @include('pms.property.unit-or-multiunit-menu-segments')
            <div class="col-12 col-lg-9">
                <form action="{{ route('pms.property.unit.or.multiunit.review.save') }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $detail->home_id ?? ''}}">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="pType" value="{{ request()->pType ?? ''}}">

                    @if(isset($reviewUpdate))
                        <input type="hidden" name="review_id" value="{{ $reviewUpdate->id }}">
                        <input type="hidden" name="pType" value="{{ $reviewUpdate->pType }}">
                        <input type="hidden" name="unit_id" value="{{ $reviewUpdate->unit_id }}">
                        <input type="hidden" name="property_id" value="{{ $reviewUpdate->pType == 'unit' ? $reviewUpdate->unit_id : $reviewUpdate->multi_unit_id }}">
                    @endif

                    <div class="row g-5">
                        <div class="col-12 col-xxl-6">
                            @if($isLoading ?? false)
                                <div class="d-flex justify-content-center py-5">
                                    <div class="spinner-border" role="status"></div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-field">
                                            <label for="guest_name">Guest Name<span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                name="guest_name"
                                                id="guest_name"
                                                class="form-control @error('guest_name') is-invalid @enderror"
                                                value="{{ old('guest_name', isset($reviewUpdate) ? $reviewUpdate->guest_name : '') }}"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-field">
                                            <label for="review_date">Date<span class="text-danger">*</span></label>
                                            <input
                                                type="date"
                                                name="review_date"
                                                id="review_date"
                                                class="form-control @error('review_date') is-invalid @enderror flatpickr"
                                                value="{{ old('review_date', isset($reviewUpdate) ? \Carbon\Carbon::parse($reviewUpdate->review_date)->format('Y-m-d') : '') }}"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-field">
                                            <label for="rating">Rating<span class="text-danger">*</span></label>
                                            <select
                                                name="rating"
                                                id="rating"
                                                class="form-control @error('rating') is-invalid @enderror"
                                                >
                                                <option value="" disabled {{ old('rating', isset($reviewUpdate) ? $reviewUpdate->rating : '') == '' ? 'selected' : '' }}>Select Rating</option>
                                                @foreach([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5] as $value)
                                                    <option value="{{ $value }}" {{ old('rating', isset($reviewUpdate) ? $reviewUpdate->rating : '') == $value ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <div class="form-field">
                                            <label for="review_type">Channel <span class="text-danger">*</span></label>
                                            <select
                                                name="review_type"
                                                id="review_type"
                                                class="form-control @error('review_type') is-invalid @enderror"
                                            >
                                                <option value="">
                                                    Select Channel
                                                </option>
                                    
                                                @foreach($channelData as $value)
                                                    <option 
                                                        value="{{ $value->id }}" 
                                                        {{ old('review_type', $reviewUpdate->review_type ?? '') == $value->id ? 'selected' : '' }}
                                                    >
                                                        {{ $value->review_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                    
                                            @error('review_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                     <div class="col-12 col-md-6">
                                        <div class="form-field">
                                            <label for="review_date">Link<span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                name="link"
                                                id="link"
                                                class="form-control @error('link') is-invalid @enderror"
                                                value="{{ old('link', isset($reviewUpdate) ? $reviewUpdate->link : '') }}"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-field">
                                            <label for="comment">Comment<span class="text-danger">*</span></label>
                                            <textarea
                                                name="comment"
                                                id="comment"
                                                class="form-control @error('comment') is-invalid @enderror"
                                                rows="8"
                                            >{{ old('comment', isset($reviewUpdate) ? $reviewUpdate->comment : '') }}</textarea>
                                           </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-field d-flex flex-column">
                                            <label>Media Type (Optional)</label>
                                            <div class="d-flex gap-2 mt-2">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="media_type"
                                                        id="media_images"
                                                        value="images"
                                                        {{ old('media_type', isset($reviewUpdate) && $reviewUpdate->file_type == 'image' ? 'images' : (isset($reviewUpdate) && $reviewUpdate->file_type == 'video' ? '' : 'images')) == 'images' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="media_images">Images</label>
                                                </div>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="media_type"
                                                        id="media_video"
                                                        value="video"
                                                        {{ old('media_type', isset($reviewUpdate) && $reviewUpdate->file_type == 'video' ? 'video' : '') == 'video' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="media_video">Video</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" id="imageUploadContainer">
                                        <div class="form-field">
                                            <label>Image (Optional)</label>
                                            <div class="upload-wrapper text-center" onclick="document.getElementById('imageUpload').click();">
                                                <input type="file" id="imageUpload" name="images" accept="image/jpeg,image/png" class="d-none @error('images.*') is-invalid @enderror">
                                                <div class="upload-box" id="imageUploadBox">
                                                    <i class="bi bi-upload fs-1 d-block"></i>
                                                    <strong><span class="text-primary">Browse</span> Your File</strong><br>
                                                    <small>Single image | Max size: 2MB | Min size: 1024px × 683px</small>
                                                </div>
                                                <div class="image-preview" id="image-preview-container"></div>
                                            </div>
                                         </div>
                                    </div>

                                    <div class="col-12" id="videoUploadContainer">
                                        <div class="form-field">
                                            <label>Video (Optional)</label>
                                            <div class="upload-wrapper text-center" onclick="document.getElementById('videoUpload').click();">
                                                <input type="file" id="videoUpload" name="video" accept="video/mp4,video/mov,video/avi" class="d-none @error('video') is-invalid @enderror">
                                                <div class="upload-box" id="videoUploadBox">
                                                    <i class="bi bi-upload fs-1 d-block"></i>
                                                    <strong>Drag & Drop Or <span class="text-primary">Browse</span> Your File</strong><br>
                                                    <small>Single video | Max size: 10MB</small>
                                                </div>
                                                <div class="video-preview" id="video-preview-container"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12" id="bannerImageUploadContainer" style="display: none;">
                                        <div class="form-field">
                                            <label>Banner Image (Optional)</label>
                                            <div class="upload-wrapper text-center" onclick="document.getElementById('bannerImageUpload').click();">
                                                <input type="file" id="bannerImageUpload" name="banner_image" accept="image/jpeg,image/png" class="d-none @error('banner_image') is-invalid @enderror">
                                                <div class="upload-box" id="bannerImageUploadBox">
                                                    <i class="bi bi-upload fs-1 d-block"></i>
                                                    <strong>Drag & Drop Or <span class="text-primary">Browse</span> Your File</strong><br>
                                                    <small>Single image | Max size: 2MB | Min size: 1024px × 683px</small>
                                                </div>
                                                <div class="banner-preview" id="banner-image-preview-container"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-wrap pt-2">
                                        <button type="submit" class="btn btn-primary px-5" id="submitBtn">SUBMIT
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($reviews->count())
                            <div class="col-12 col-xxl-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-field">
                                            <h6 class="fw-bold mb-0">Reviews List</h6>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="table-wrap">
                                            <div class="table-responsive">
                                                <table class="table table-list mb-0 mw-lg table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th width="45px">
                                                                <input
                                                                    class="form-check-input form-check-lg mt-0"
                                                                    type="checkbox"
                                                                    id="checkAll"
                                                                >
                                                            </th>
                                                            <th class="fw-semibold">Guest Name</th>
                                                            <th width="20%" class="text-secondary fw-semibold">Date</th>
                                                            <th class="fw-semibold text-center">Icon</th>
                                                            <th class="fw-semibold text-center">Review Type</th>
                                                            <th class="fw-semibold text-center">Rating</th>
                                                            <th class="fw-semibold text-center">Media</th>
                                                            <th class="fw-semibold text-center">Show on Home</th>
                                                            <th width="90px" class="text-secondary fw-semibold text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($reviews as $review)
                                                            <tr>
                                                                <td>
                                                                    <input
                                                                        class="form-check-input form-check-lg mt-0 review-checkbox"
                                                                        type="checkbox"
                                                                        value="{{ $review->id }}"
                                                                        name="selected_reviews[]"
                                                                    >
                                                                </td>
                                                                <td>{{ $review->guest_name }}</td>
                                                                
                                                                
                                                                
                                                                <td>{{ \Carbon\Carbon::parse($review->review_date)->format('d M, Y') }}</td>
                                                                
                                                                <td>
                                                                    @php
                                                                    $imagePath = public_path('storage/channel/' . ($review->reviewImages->review_image ?? ''));
                                                                    @endphp
                                                                    @if(!empty($review->reviewImages->review_image) && file_exists($imagePath))
                                                                    <img src="{{ asset('storage/channel/' . $review->reviewImages->review_image) }}" alt="Icon" style="width: 50px;" width="100px">
                                                                    @endif
                                                                </td>
                                                                
                                                                
                                                                
                                                                <td class="text-center">{{ $review->reviewImages->review_name }}</td>
                                                                <td class="text-center">{{ $review->rating }}</td>
                                                                @php
                                                                    $imagePath = public_path('storage/review/images/' . $review->file);
                                                                    $videoPath = public_path('storage/review/videos/' . $review->file);
                                                                    $bannerPath = public_path('storage/review/banner_images/' . $review->banner_image);
                                                                @endphp
                                                                
                                                                <td class="text-center">
                                                                    @if($review->file_type == 'images' && $review->file && file_exists($imagePath))
                                                                        <a href="{{ asset('storage/review/images/' . $review->file) }}" target="_blank">
                                                                            <img src="{{ asset('storage/review/images/' . $review->file) }}" alt="Review Image" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                                                        </a>
                                                                    @elseif($review->file_type == 'video' && $review->file && file_exists($videoPath))
                                                                        <a href="{{ asset('storage/review/videos/' . $review->file) }}" target="_blank">
                                                                            <i class="bi bi-camera-video"></i>
                                                                        </a>
                                                                        @if($review->banner_image && file_exists($bannerPath))
                                                                            <img src="{{ asset('storage/review/banner_images/' . $review->banner_image) }}" alt="Review Image" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                                                        @else
                                                                            <span>-</span>
                                                                        @endif
                                                                    @else
                                                                        <span>-</span>
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input btn_show_home" type="checkbox" role="switch" data-value="{{ $review->id }}" {{ $review->display_on_home_page ? 'checked' : '' }}>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <ul class="actions action-btn-group justify-content-center mb-0 mw-0 text-decoration-none">
                                                                        <li>
                                                                            <a href="{{ route('pms.property.unit.or.multiunit.review', [
                                                                                'id' => $id,
                                                                                'pType' => $review->pType,
                                                                                'property_id' => $review->pType == 'unit' ? $review->unit_id : $review->multi_unit_id,
                                                                                'review_id' => $review->id
                                                                            ]) }}" class="btn ps-0 p-1 fs-5 text-black">
                                                                                <i class="bi bi-pencil-square"></i>
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <button type="button" class="btn item_delete" data-value="{{ $review->id }}"><i class="bi bi-trash"></i></button>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="table-footer pt-3">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <button
                                                    type="button"
                                                    class="btn btn-save btn-primary"
                                                    id="deleteSelected"
                                                    disabled
                                                >
                                                    Delete Selected
                                                </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Flatpickr
    flatpickr('#review_date', {
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        allowInput: true,
        altInput: true,
        altFormat: "d/m/Y",
    });

    // Toggle media upload fields based on radio selection
    function toggleMediaFields() {
        const mediaType = $('input[name="media_type"]:checked').val();
        if (mediaType === 'images') {
            $('#imageUploadContainer').show();
            $('#videoUploadContainer').hide();
            $('#bannerImageUploadContainer').hide();
            $('#videoUpload').val('');
            $('#bannerImageUpload').val('');
        } else if (mediaType === 'video') {
            $('#videoUploadContainer').show();
            $('#bannerImageUploadContainer').show();
            $('#imageUploadContainer').hide();
            $('#imageUpload').val('');
        } else {
            $('#imageUploadContainer').hide();
            $('#videoUploadContainer').hide();
            $('#bannerImageUploadContainer').hide();
            $('#imageUpload').val('');
            $('#videoUpload').val('');
            $('#bannerImageUpload').val('');
            $('#image-preview-container').empty();
            $('#video-preview-container').empty();
            $('#banner-image-preview-container').empty();
            $('#imageUploadBox').show();
            $('#videoUploadBox').show();
            $('#bannerImageUploadBox').show();
        }
    }

    // Initial toggle based on default or edit state
    toggleMediaFields();

    // Update on radio change
    $('input[name="media_type"]').on('change', toggleMediaFields);

    // Handle image preview and removal
    const imageInput = $('#imageUpload');
    const imagePreviewContainer = $('#image-preview-container');
    const imageUploadBox = $('#imageUploadBox');

    // Show existing image in preview if present
    @if(isset($reviewUpdate) && $reviewUpdate->file && $reviewUpdate->file_type == 'images')
        (function() {
            const img = $('<img>').attr('src', '{{ asset('storage/review/images/' . $reviewUpdate->file) }}');
            const removeBtn = $('<span>').addClass('remove-btn').html('✖').on('click', function () {
                imagePreviewContainer.empty();
                imageInput.val('');
                imageUploadBox.show();
                $('<input>').attr({
                    type: 'hidden',
                    name: 'remove_existing_image',
                    value: '1'
                }).appendTo('#reviewForm');
            });
            imagePreviewContainer.append(img).append(removeBtn);
            imageUploadBox.hide();
        })();
    @endif

    imageInput.on('change', function () {
        imagePreviewContainer.empty();
        const files = this.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = $('<img>').attr('src', e.target.result);
                    const removeBtn = $('<span>').addClass('remove-btn').html('✖').on('click', function () {
                        imagePreviewContainer.empty();
                        imageInput.val('');
                        imageUploadBox.show();
                    });
                    imagePreviewContainer.append(img).append(removeBtn);
                    imageUploadBox.hide();
                };
                reader.readAsDataURL(file);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please upload a valid image file (JPEG/PNG).'
                });
                imageInput.val('');
                imageUploadBox.show();
            }
        }
    });

    // Prevent multiple file selections for images
    imageInput.on('click', function () {
        this.value = '';
        if (!imagePreviewContainer.is(':empty')) {
            imagePreviewContainer.empty();
            imageUploadBox.show();
        }
    });

    // Handle drag and drop for images
    $('#imageUploadContainer .upload-wrapper').on('dragover', function (e) {
        e.preventDefault();
        imageUploadBox.addClass('bg-light');
    }).on('dragleave', function () {
        imageUploadBox.removeClass('bg-light');
    }).on('drop', function (e) {
        e.preventDefault();
        imageUploadBox.removeClass('bg-light');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            imageInput.prop('files', files);
            imageInput.trigger('change');
        }
    });

    // Handle video preview and removal
    const videoInput = $('#videoUpload');
    const videoPreviewContainer = $('#video-preview-container');
    const videoUploadBox = $('#videoUploadBox');

    // Show existing video in preview if present
    @if(isset($reviewUpdate) && $reviewUpdate->file && $reviewUpdate->file_type == 'video')
        (function() {
            const video = $('<video>').attr({
                src: '{{ asset('storage/review/videos/' . $reviewUpdate->file) }}',
                controls: true
            });
            const removeBtn = $('<span>').addClass('remove-btn').html('✖').on('click', function () {
                videoPreviewContainer.empty();
                videoInput.val('');
                videoUploadBox.show();
                $('<input>').attr({
                    type: 'hidden',
                    name: 'remove_existing_video',
                    value: '1'
                }).appendTo('#reviewForm');
            });
            videoPreviewContainer.append(video).append(removeBtn);
            videoUploadBox.hide();
        })();
    @endif

    videoInput.on('change', function () {
        videoPreviewContainer.empty();
        const files = this.files;
        if (files.length > 0) {
            const file = files[0];
            const maxSize = 10 * 1024 * 1024;
            if (file.type.match('video.*')) {
                if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Video size must not exceed 10MB.'
                });
                videoInput.val('');
                videoUploadBox.show();
                return;
            }
                const reader = new FileReader();
                reader.onload = function (e) {
                    const video = $('<video>').attr({
                        src: e.target.result,
                        controls: true
                    });
                    const removeBtn = $('<span>').addClass('remove-btn').html('✖').on('click', function () {
                        videoPreviewContainer.empty();
                        videoInput.val('');
                        videoUploadBox.show();
                    });
                    videoPreviewContainer.append(video).append(removeBtn);
                    videoUploadBox.hide();
                };
                reader.readAsDataURL(file);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please upload a valid video file (MP4/MOV/AVI).'
                });
                videoInput.val('');
                videoUploadBox.show();
            }
        }
    });

    // Prevent multiple file selections for videos
    videoInput.on('click', function () {
        this.value = '';
        if (!videoPreviewContainer.is(':empty')) {
            videoPreviewContainer.empty();
            videoUploadBox.show();
        }
    });

    // Handle drag and drop for videos
    $('#videoUploadContainer .upload-wrapper').on('dragover', function (e) {
        e.preventDefault();
        videoUploadBox.addClass('bg-light');
    }).on('dragleave', function () {
        videoUploadBox.removeClass('bg-light');
    }).on('drop', function (e) {
        e.preventDefault();
        videoUploadBox.removeClass('bg-light');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            videoInput.prop('files', files);
            videoInput.trigger('change');
        }
    });

    // Handle banner image preview and removal
    const bannerImageInput = $('#bannerImageUpload');
    const bannerImagePreviewContainer = $('#banner-image-preview-container');
    const bannerImageUploadBox = $('#bannerImageUploadBox');

    // Show existing banner image in preview if present
    @if(isset($reviewUpdate) && $reviewUpdate->banner_image && $reviewUpdate->file_type == 'video')
        (function() {
            const img = $('<img>').attr('src', '{{ asset('storage/review/banner_images/' . $reviewUpdate->banner_image) }}');
            const removeBtn = $('<span>').addClass('remove-btn').html('✖').on('click', function () {
                bannerImagePreviewContainer.empty();
                bannerImageInput.val('');
                bannerImageUploadBox.show();
                $('<input>').attr({
                    type: 'hidden',
                    name: 'remove_existing_banner_image',
                    value: '1'
                }).appendTo('#reviewForm');
            });
            bannerImagePreviewContainer.append(img).append(removeBtn);
            bannerImageUploadBox.hide();
        })();
    @endif

    bannerImageInput.on('change', function () {
        bannerImagePreviewContainer.empty();
        const files = this.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = $('<img>').attr('src', e.target.result);
                    const removeBtn = $('<span>').addClass('remove-btn').html('✖').on('click', function () {
                        bannerImagePreviewContainer.empty();
                        bannerImageInput.val('');
                        bannerImageUploadBox.show();
                    });
                    bannerImagePreviewContainer.append(img).append(removeBtn);
                    bannerImageUploadBox.hide();
                };
                reader.readAsDataURL(file);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Please upload a valid image file (JPEG/PNG).'
                });
                bannerImageInput.val('');
                bannerImageUploadBox.show();
            }
        }
    });

    // Prevent multiple file selections for banner images
    bannerImageInput.on('click', function () {
        this.value = '';
        if (!bannerImagePreviewContainer.is(':empty')) {
            bannerImagePreviewContainer.empty();
            bannerImageUploadBox.show();
        }
    });

    // Handle drag and drop for banner images
    $('#bannerImageUploadContainer .upload-wrapper').on('dragover', function (e) {
        e.preventDefault();
        bannerImageUploadBox.addClass('bg-light');
    }).on('dragleave', function () {
        bannerImageUploadBox.removeClass('bg-light');
    }).on('drop', function (e) {
        e.preventDefault();
        bannerImageUploadBox.removeClass('bg-light');
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            bannerImageInput.prop('files', files);
            bannerImageInput.trigger('change');
        }
    });

    // Form submission with FormData for file uploads
    $('#reviewForm').on('submit', function (e) {
        e.preventDefault();
        $('#submitBtn').prop('disabled', true);
        $('#submitText').addClass('d-none');
        $('#submitSpinner').removeClass('d-none');

        let formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                 toastr.success('Review saved successfully', 'Success', { timeOut: 2000 });
                const url = new URL(window.location.href);
                url.searchParams.delete('review_id');
                setTimeout(() => {
                    window.location.href = url.toString();
                }, 1500);
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON.message || 'Something went wrong!',
                });
                $('#submitBtn').prop('disabled', false);
                $('#submitText').removeClass('d-none');
                $('#submitSpinner').addClass('d-none');
            }
        });
    });

    // Check all checkboxes
    $('#checkAll').on('change', function () {
        $('.review-checkbox').prop('checked', $(this).prop('checked'));
        toggleDeleteButton();
    });

    // Individual checkbox change
    $('.review-checkbox').on('change', function () {
        if ($('.review-checkbox:checked').length === $('.review-checkbox').length) {
            $('#checkAll').prop('checked', true);
        } else {
            $('#checkAll').prop('checked', false);
        }
        toggleDeleteButton();
    });

    // Toggle delete selected button
    function toggleDeleteButton() {
        $('#deleteSelected').prop('disabled', $('.review-checkbox:checked').length === 0);
    }

    // Delete single review
    $('.item_delete').on('click', function () {
        const id = $(this).data("value");
        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("pms/property/review/delete") }}/' + id,
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire('Deleted!', response.message || 'The item has been deleted.', 'success');
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function () {
                        Swal.fire('Error', 'Something went wrong!', 'error');
                    }
                });
            }
        });
    });

    // Delete selected reviews
    $('#deleteSelected').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling to parent form
        
        const selectedIds = $('.review-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to delete selected reviews?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("pms/property/review/delete-multiple") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete reviews!'
                        });
                    }
                });
            }
        });
    });

    $('.btn_show_home').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/property/review/show_home/toggle-status") }}/' + id,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message, 'Success', { timeOut: 2000 });
                },
                error: function () {
                    toastr.error('Something went wrong!', 'Error', { timeOut: 2000 });
                }
            });
        });
});
</script>
@endsection