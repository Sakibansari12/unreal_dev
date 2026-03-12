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
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Landing Page' : 'Add Landing Page' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.landing.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="form-box">
                <form action="{{ route('pms.landing.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-field">
                                <label for="title">Title<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="title" 
                                    value="{{ old('title', $detail->title ?? '') }}" 
                                    class="form-control @error('title') is-invalid @enderror"
                                >
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-field">
                                <label for="mappedLocations">Home Type (Multiple Selection)</label>
                                <div class="dropdown form-multiselect @error('mappedLocations') is-invalid @enderror" id="locationDropdown">
                                    <button class="form-control text-start" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="location_ids">
                                        <span class="multi-select-name">Select Home Type</span>
                                        <span class="multi-select-count"></span>
                                    </button>
                                    <ul class="dropdown-menu w-100" id="location_dropdown">
                                        @forelse($homeTypeData as $index => $location)
                                            <li>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="mappedLocations[]" id="ckb-{{ $index + 1 }}"
                                                        data-name="{{ $location->name ?? 'Untitled' }}" value="{{ $location->id }}"
                                                        {{ in_array($location->id, old('mappedLocations', $userMappedLocationIds ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ckb-{{ $index + 1 }}">
                                                        {{ $location->name ?? 'Untitled' }}
                                                    </label>
                                                </div>
                                            </li>
                                        @empty
                                            <li>
                                                <div class="text-muted px-3 py-2">No Home Type <div>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                                @error('mappedLocations')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Property (Multiple Selection) -->
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="mappedProperties">Property (Multiple Selection)</label>
                                    <div class="dropdown form-multiselect @error('mappedProperties') is-invalid @enderror" id="propertyDropdown">
                                        <button class="form-control text-start" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="property_ids">
                                            <span class="multi-select-name">Select Properties</span>
                                            <span class="multi-select-count"></span>
                                        </button>
                                        <ul class="dropdown-menu w-100" id="property_dropdown">
                                            <!-- Properties will be populated dynamically via AJAX -->
                                            @forelse($allProperties as $index => $property)
                                                <li>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="mappedProperties[]" id="prop-{{ $index + 1 }}"
                                                            data-name="{{ $property->unit_name }}" value="{{ $property->id }}"
                                                            {{ in_array($property->id, old('mappedProperties', $userMappedPropertyIds ?? [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="prop-{{ $index + 1 }}">
                                                            {{ $property->unit_name }}
                                                        </label>
                                                    </div>
                                                </li>
                                            @empty
                                                <li>
                                                    <div class="text-muted px-3 py-2">No Properties</div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <!-- Debug hidden input -->
                                    <input type="hidden" name="debug_mapped_properties" value="{{ json_encode($userMappedPropertyIds ?? []) }}">
                                    @error('mappedProperties')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                    
                    <div class="col-6">
                            <div class="form-field">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control h-auto" id="description" rows="14">{{ old('description', $detail->description ?? '') }}</textarea>
                            </div>
                    </div>
                    <div class="col-6">
                        <div class="form-field">
                            <label for="Keywords">About Us</label>
                            <textarea name="keyword" class="form-control h-auto " id="keyword" rows="14">{{ old('keyword', $detail->keyword ?? '') }}</textarea>
                        </div>
                    </div>
                    
                    </div>

                    

                    <div class="row">
                        <div class="col-12">
                            <label for="image">Banner Image<sup>*</sup></label>
                            <div class="upload-wrapper @error('image') is-invalid @enderror" id="uploadWrapper">
                                <input type="file" name="image" id="imageInput" accept="image/*" onchange="previewImage(event)">
                                <div class="upload-info" id="uploadInfo" onclick="document.getElementById('imageInput').click()" style="@if(isset($detail->image)) display:none; @endif">
                                    <i class="bi bi-upload fs-4"></i>
                                    <div class="upload-info-text">
                                        <strong><span class="text-primary">Browse</span> Your File.</strong>
                                        <small class="d-block">Max size: 3MB</small>
                                    </div>
                                </div>
                                <div id="imagePreview" class="imagePreview @if(!isset($detail->image)) d-none @endif">
                                    @php
                                        $imagePath = public_path('storage/landing/' . ($detail->image ?? ''));
                                    @endphp
                                    @if(!empty($detail->image) && file_exists($imagePath))
                                        <img src="{{ asset('storage/landing/' . $detail->image) }}" alt="Home Banner Image">
                                    @endif
                                </div>
                                <button type="button" class="remove-preview @if(!isset($detail->image)) d-none @endif" onclick="removeImage()">×</button>
                            </div>
                            <!--@error('image')-->
                            <!--    <div class="invalid-feedback">{{ $message }}</div>-->
                            <!--@enderror-->
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
@php 
    $path = asset('public/ckfinder');
@endphp
 <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ckfinderPath = "{{ asset('ckfinder/') }}";

            if (document.getElementById('keyword')) {
                const shortEditor = CKEDITOR.replace('keyword');
                CKFinder.setupCKEditor(shortEditor, ckfinderPath);
            }

            if (document.getElementById('description')) {
                const fullEditor = CKEDITOR.replace('description');
                CKFinder.setupCKEditor(fullEditor, ckfinderPath);
            }
        });
    </script>
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


 document.addEventListener('DOMContentLoaded', function () {

            function updateDropdownDisplay() {
                const dropdown = document.getElementById('locationDropdown');
                const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
                const nameSpan = dropdown.querySelector('.multi-select-name');
                const countSpan = dropdown.querySelector('.multi-select-count');
                const selected = Array.from(checkboxes).filter(cb => cb.checked);
                const names = selected.map(cb => cb.getAttribute('data-name'));

                if (names.length === 0) {
                    nameSpan.textContent = 'Select Home Type';
                    countSpan.textContent = '';
                } else if (names.length === 1) {
                    nameSpan.textContent = names[0];
                    countSpan.textContent = '';
                } else {
                    nameSpan.textContent = `${names.length} Home Type Selected`;
                    countSpan.textContent = `(${names.length})`;
                }
            }

            function updatePropertyDropdown(properties, selectedPropertyIds = []) {
                const dropdown = document.getElementById('property_dropdown');
                const nameSpan = document.getElementById('propertyDropdown').querySelector('.multi-select-name');
                const countSpan = document.getElementById('propertyDropdown').querySelector('.multi-select-count');

                const selectedIds = Array.isArray(selectedPropertyIds)
                    ? selectedPropertyIds.map(id => id.toString())
                    : [];
                
                dropdown.innerHTML = '';

                if (properties.length === 0) {
                    dropdown.innerHTML = '<li><div class="text-muted px-3 py-2">No properties found.</div></li>';
                    nameSpan.textContent = 'Select Properties';
                    countSpan.textContent = '';
                    return;
                }

                properties.forEach((property, index) => {
                    // const isChecked = selectedPropertyIds.includes(property.id.toString()) ? 'checked' : '';
                    const isChecked = selectedIds.includes(property.id.toString()) ? 'checked' : '';
                    dropdown.innerHTML += `
                        <li>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="mappedProperties[]" id="prop-ckb-${index + 1}"
                                    data-name="${property.unit_name}" value="${property.id}" ${isChecked}>
                                <label class="form-check-label" for="prop-ckb-${index + 1}">
                                    ${property.unit_name}
                                </label>
                            </div>
                        </li>
                    `;
                });

                updatePropertyDropdownDisplay();

                document.querySelectorAll('#propertyDropdown input[type="checkbox"]').forEach(function (checkbox) {
                    checkbox.addEventListener('change', updatePropertyDropdownDisplay);
                });
            }

            function updatePropertyDropdownDisplay() {
                const dropdown = document.getElementById('propertyDropdown');
                const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
                const nameSpan = dropdown.querySelector('.multi-select-name');
                const countSpan = dropdown.querySelector('.multi-select-count');
                const selected = Array.from(checkboxes).filter(cb => cb.checked);
                const names = selected.map(cb => cb.getAttribute('data-name'));

                if (names.length === 0) {
                    nameSpan.textContent = 'Select Properties';
                    countSpan.textContent = '';
                } else if (names.length === 1) {
                    nameSpan.textContent = names[0];
                    countSpan.textContent = '';
                } else {
                    nameSpan.textContent = `${names.length} Properties Selected`;
                    countSpan.textContent = `(${names.length})`;
                }
            }

            
            updateDropdownDisplay();
            document.querySelectorAll('#locationDropdown input[type="checkbox"]').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    updateDropdownDisplay();
                    const selectedHomeTypes = Array.from(
                        document.querySelectorAll('#locationDropdown input[type="checkbox"]:checked')
                    ).map(cb => cb.value);

                    $.ajax({
                        url: '{{ route("pms.coupon.get-properties") }}',
                        method: 'POST',
                        data: {
                            home_type_ids: selectedHomeTypes,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            updatePropertyDropdown(response.properties, @json($userMappedPropertyIds));
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching properties:',error);
                        }
                    });
                });
            });

           

           

           

            @if($detail && !empty($userMappedPropertyIds))
                $.ajax({
                    url: '{{ route("pms.coupon.get-properties") }}',
                    method: 'POST',
                    data: {
                       home_type_ids: @json(old('mappedLocations', $userMappedLocationIds ?? [])),
                       _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        updatePropertyDropdown(response.properties, @json($userMappedPropertyIds));
                    },
                    error: function () {
                        console.error('Error fetching properties',error);
                    }
                });
            @endif

        });
document.addEventListener('DOMContentLoaded', function () {
        function updateDropdownDisplay() {
            const dropdown = document.getElementById('locationDropdown');
            const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
            const nameSpan = dropdown.querySelector('.multi-select-name');
            const countSpan = dropdown.querySelector('.multi-select-count');
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const names = selected.map(cb => cb.getAttribute('data-name'));

            if (names.length === 0) {
                nameSpan.textContent = 'Select Home Type';
                countSpan.textContent = '';
            } else if (names.length === 1) {
                nameSpan.textContent = names[0];
                countSpan.textContent = '';
            } else {
                nameSpan.textContent = `${names.length} Home Type Selected`;
                countSpan.textContent = `(${names.length})`;
            }
        }

        const locationDropdownCheckboxes = document.querySelectorAll('#locationDropdown input[type="checkbox"]');
        locationDropdownCheckboxes.forEach(cb => cb.addEventListener('change', updateDropdownDisplay));
        updateDropdownDisplay();

        const propertyDropdownCheckboxes = document.querySelectorAll('#propertyDropdown input[type="checkbox"]');
        function updatePropertyDropdownDisplay() {
            const nameSpan = document.getElementById('propertyDropdown').querySelector('.multi-select-name');
            const countSpan = document.getElementById('propertyDropdown').querySelector('.multi-select-count');
            const selected = Array.from(propertyDropdownCheckboxes).filter(cb => cb.checked);
            const names = selected.map(cb => cb.getAttribute('data-name'));

            if (names.length === 0) {
                nameSpan.textContent = 'Select Properties';
                countSpan.textContent = '';
            } else if (names.length === 1) {
                nameSpan.textContent = names[0];
                countSpan.textContent = '';
            } else {
                nameSpan.textContent = `${names.length} Properties Selected`;
                countSpan.textContent = `(${names.length})`;
            }
        }
        propertyDropdownCheckboxes.forEach(cb => cb.addEventListener('change', updatePropertyDropdownDisplay));
        updatePropertyDropdownDisplay();
    });
</script>
@endsection