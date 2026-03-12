@extends('pms.layouts.app')
@section('content')
<style>
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
                    <h1 class="fs-5 mb-0">{{ $id?'Update':'Add' }} {{ ucfirst(request()->pType) }} (Property: {{ $parentHome->unit_name }})</h1>
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

                    <div class="d-flex gap-2">
                        <a href="{{ $route }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                        </a>
                        <!-- @if(in_array(Auth::guard('admin')->user()->role_id, [1,7]))
                        @if(!empty($detail->ptype) && !empty($detail->url_key))
                            <a href="{{ route('property-detail-preview', ['ptype' => strtolower($detail->ptype),'slug' => $detail->url_key]) }}"  class="btn d-flex btn-small rounded-2 btn-success" target="_blank">
                               Preview Property</span>
                            </a>
                        @endif
                        @endif -->
                    </div>
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
                        overflow-x: auto
                    }

                    @media (max-width: 991.98px) {
                        .ulTab {
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex
                        }
                    }

                    .ulTab li {
                        margin: 10px 5px
                    }

                    .ulTab li button,
                    .ulTab li a {
                        border: 0px;
                        padding: 10px 15px;
                        background-color: #fff;
                        width: 100%;
                        border-radius: 6px !important;
                        text-align: left;
                        border: 1px solid #0E0E0E;
                        display: block;
                        text-decoration: none
                    }

                    .ulTab li button.active,
                    .ulTab li a.active {
                        border: 0px;
                        padding: 10px 15px;
                        color: #fff;
                        background-color: #0e0e0e
                    }

                    .ulTab li button[disabled],
                    .ulTab li a[disabled] {
                        opacity: 1;
                        color: #000
                    }

                    @media (max-width: 991.98px) {

                        .ulTab li button,
                        .ulTab li a {
                            white-space: nowrap
                        }
                    }

                    .ulTab {
                        display: block !important;
                    }

                    .invalid-tooltip {
                        position: absolute;
                        top: 100%;
                        left: 0;
                        z-index: 5;
                        display: none;
                        max-width: 100%;
                        padding: 6px 10px;
                        margin-top: 0.1rem;
                        font-size: 12px;
                        color: #fff;
                        background-color: rgba(220, 53, 69, 0.9);
                        border-radius: 0.25rem;
                    }

                    .is-invalid .invalid-tooltip {
                        display: block;
                    }

                    .form-multiselect .dropdown-menu,
                    .dropdown-menu {
                        max-height: 300px;
                        overflow-y: auto;
                    }
                </style>
                @if($id) @include('pms.property.unit-or-multiunit-menu-segments') @endif
                {{-- overview section --}}
                <div class="col-12 {{ $id ? 'col-lg-9' : 'col-lg-12' }}">
                    <form action="{{ route('pms.property.unit.or.multiunit.overview.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($detail->id))
                        <input type="hidden" name="id" value="{{ $id }}">
                        @endif
                        <input type="hidden" name="pType" value="{{ request()->pType }}">
                        <input type="hidden" name="property_id" value="{{ old('property_id', $parentHome->id ?? '') }}">

                        <div class="form-box">
                            <div class="row">
                                @if($reqParameters['pType'] == 'multiunit')
                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="property_ids">Unit (Multiple Selection)</label>
                                        <div class="dropdown form-multiselect" id="propertyDropdown">
                                            <button class="form-control text-start @error('mappedProperties') is-invalid @enderror" type="button" data-bs-toggle="dropdown"
                                                data-bs-auto-close="outside" id="property_ids">
                                                <span class="multi-select-name">Select Properties</span>
                                                <span class="multi-select-count"></span>
                                            </button>
                                            <ul class="dropdown-menu w-100 " id="property_dropdown">
                                                @forelse($unitList as $index => $property)
                                                <li>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="mappedProperties[]" id="ckb-{{ $index + 1 }}"
                                                            data-name="{{ $property->unit_name ?? 'Untitled' }}"
                                                            value="{{ $property->id }}"
                                                            {{ in_array($property->id, old('mappedProperties', $mappedUnites)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ckb-{{ $index + 1 }}">
                                                            {{ $property->unit_name ?? 'Untitled' }}
                                                        </label>
                                                    </div>
                                                </li>
                                                @empty
                                                <li>
                                                    <div class="text-muted px-3 py-2">No properties found.</div>
                                                </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!--<div class="col-3">-->
                                <!--    <div class="form-field">-->
                                <!--        <label for="home_name">Property Name<sup>*</sup></label>-->
                                <!--        <input type="text" name="home_name" class="form-control @error('home_name') is-invalid @enderror" value="{{ old('home_name', $detail->unit_name ?? '') }}">-->

                                <!--    </div>-->
                                <!--</div>-->

                                <div class="col-6">
                                    <div class="form-field">
                                        <label for="home_name">Unit Name (OTA) (Max 50 Characters, No , . &)<sup>*</sup></label>
                                        <input type="text" name="home_name" class="form-control @error('home_name') is-invalid @enderror" value="{{ old('home_name', $detail->unit_name ?? '') }}">

                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-field">
                                        <label for="unit_name_website">Unit Name (Website)<sup>*</sup></label>
                                        <input type="text" name="unit_name_website" class="form-control @error('unit_name_website') is-invalid @enderror" value="{{ old('unit_name_website', $detail->unit_name_website ?? '') }}">

                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-field">
                                        <label for="unit_subtitle_website">Unit Subtitle (Website)</label>
                                        <input type="text" name="unit_subtitle_website" class="form-control @error('unit_subtitle_website') is-invalid @enderror" value="{{ old('unit_subtitle_website', $detail->unit_subtitle_website ?? '') }}">

                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-field">
                                        <label for="no_of_rooms">No. Of Rooms<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="no_of_rooms" class="form-control @error('no_of_rooms') is-invalid @enderror" value="{{ old('no_of_rooms', $detail->no_of_rooms ?? '') }}">

                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="min_stay">Min Stay<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="min_stay" class="form-control @error('min_stay') is-invalid @enderror" value="{{ old('min_stay', $detail->min_stay ?? '') }}" @if($id !='' ) disabled="disabled" @endif>

                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="capacity">Capacity<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $detail->guests_included ?? '') }}">
                                        <span class="text-danger small" id="capacityError"></span>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="maximum_number_of_guests">Max Occupancy<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="maximum_number_of_guests" id="maxOccupancy" class="form-control @error('maximum_number_of_guests') is-invalid @enderror" value="{{ old('maximum_number_of_guests', $detail->maximum_number_of_guests ?? '') }}">
                                        <span class="text-danger small" id="maxOccupancyError"></span>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="per_night_price">Per Night Price<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="per_night_price" class="form-control @error('per_night_price') is-invalid @enderror" value="{{ old('per_night_price', $detail->per_night_price ?? '') }}" @if($id !='' ) disabled="disabled" @endif>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="extra_guest_charge">Extra Guest Charge<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="extra_guest_charge" class="form-control @error('extra_guest_charge') is-invalid @enderror" value="{{ old('extra_guest_charge', $detail->extra_guest_charges ?? '') }}">
                                    </div>
                                </div>
                                <!-- <div class="col-3">
                                    <div class="form-field">
                                        <label for="per_pet_charge">Per Pet Charge<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="per_pet_charge" class="form-control @error('per_pet_charge') is-invalid @enderror" value="{{ old('per_pet_charge', $detail->per_pet_charge ?? '') }}">
                                    </div>
                                </div> -->

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="no_of_staff">Number of Staff<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="no_of_staff" class="form-control @error('no_of_staff') is-invalid @enderror" value="{{ old('no_of_staff', $detail->no_of_staff ?? '') }}">
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="bedroom">Bedrooms<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="bedroom" class="form-control @error('bedroom') is-invalid @enderror" value="{{ old('bedroom', $detail->no_of_bedrooms ?? '') }}">
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="bathroom">Bathrooms<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="bathroom" class="form-control @error('bathroom') is-invalid @enderror" value="{{ old('bathroom', $detail->no_of_bathrooms ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="property_size">Property Size<sup>*</sup></label>
                                        <input type="number" step="1" min="0" name="property_size" class="form-control @error('property_size') is-invalid @enderror" value="{{ old('property_size', $detail->property_size ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="area_unit">Select Area Unit<sup>*</sup></label>
                                        <select id="area_unit" name="area_unit" class="form-control @error('area_unit') is-invalid @enderror">
                                            <option value="">Select area unit</option>
                                            <option value="Sq Ft" {{ old('area_unit', $detail->area_unit ?? '') == 'Sq Ft' ? 'selected' : '' }}>Sq Ft</option>
                                            <option value="Sq Mtr" {{ old('area_unit', $detail->area_unit ?? '') == 'Sq Mtr' ? 'selected' : '' }}>Sq Mtr</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="arrival_time">Arrival Time<sup>*</sup></label>
                                        <select name="arrival_time" class="form-select @error('arrival_time') is-invalid @enderror">
                                            <option value="">Select Arrival</option>
                                            @foreach($timeslots as $slot)
                                            <option value="{{ $slot }}" {{ old('arrival_time', $detail->checkin_time ?? '') == $slot ? 'selected' : '' }}>{{ $slot }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-3">
                                    <div class="form-field">
                                        <label for="departure_time">Departure Time<sup>*</sup></label>
                                        <select name="departure_time" class="form-select @error('departure_time') is-invalid @enderror">
                                            <option value="">Select Departure</option>
                                            @foreach($timeslots as $slot)
                                            <option value="{{ $slot }}" {{ old('departure_time', $detail->checkout_time ?? '') == $slot ? 'selected' : '' }}>{{ $slot }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <!-- <div class="col-6">
                                    <div class="form-field">
                                        <label for="mappedCollections">Collections (Multiple Selection)</label>
                                        <div class="dropdown form-multiselect @error('mappedCollections') is-invalid @enderror" id="locationDropdown">
                                            <button class="form-control text-start" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="location_ids">
                                                <span class="multi-select-name">Select Collections</span>
                                                <span class="multi-select-count"></span>
                                            </button>
                                            @php
                                                $selectedCollections = old('mappedCollections') 
                                                    ?? (collect($detail->homecollections)->pluck('collection_id')->toArray());
                                            @endphp

                                            <ul class="dropdown-menu w-100" id="location_dropdown">
                                                @forelse($collectionData as $index => $collection)
                                                    <li>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="mappedCollections[]" id="ckb-{{ $index + 1 }}"
                                                                   data-name="{{ $collection->collection_name ?? 'Untitled' }}" value="{{ $collection->id }}"
                                                                   {{ in_array($collection->id, $selectedCollections) ? 'checked' : '' }}
                                                                   >
                                                            <label class="form-check-label" for="ckb-{{ $index + 1 }}">
                                                                {{ $collection->collection_name ?? 'Untitled' }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li>
                                                        <div class="text-muted px-3 py-2">No Collections found.</div>
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        @error('mappedCollections')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> -->

                                <div class="col-12">
                                    <div class="form-field">
                                        <label for="descriptionm">Description<sup>*</sup></label>
                                        <textarea name="descriptionm" rows="5" class="form-control h-auto @error('descriptionm') is-invalid @enderror">{{ old('descriptionm', $detail->description ?? '') }}</textarea>

                                    </div>
                                </div>

                                <!--<div class="col-12">-->
                                <!--    <div class="form-field position-relative">-->
                                <!--        <label for="ru_description">OTA Description<sup>*</sup></label>-->
                                <!--        <textarea name="ru_description" id="ru_description" rows="5" class="form-control h-auto @error('ru_description') is-invalid @enderror">{{ old('ru_description', $detail->ru_description ?? '') }}</textarea>-->

                                <!--        <div class="invalid-tooltip" id="ru_description_tooltip">-->
                                <!--            Special characters are not allowed-->
                                <!--        </div>-->

                                <!--    </div>-->
                                <!--</div>-->

                                <div class="col-12">
                                    <div class="form-field position-relative">
                                        <label for="ru_description">OTA Description<sup>*</sup></label>
                                        <textarea name="ru_description" id="ru_description" rows="5" class="form-control h-auto @error('ru_description') is-invalid @enderror">{{ old('ru_description', $detail->ru_description ?? '') }}</textarea>

                                        <div class="invalid-tooltip" id="ru_description_tooltip">
                                            "&" character is not allowed.
                                        </div>
                                    </div>
                                </div>


                                <!--<div class="col-12">-->
                                <!--    <div class="form-field">-->
                                <!--        <label for="property_rules">Property Rules<sup>*</sup></label>-->
                                <!--        <textarea name="property_rules" rows="5" class="form-control h-auto @error('property_rules') is-invalid @enderror">{{ old('property_rules', $detail->house_rules ?? '') }}</textarea>-->
                                <!--    </div>-->
                                <!--    <div class="invalid-tooltip" id="property_rules_tooltip">-->
                                <!--        "&" character is not allowed.-->
                                <!--    </div>-->
                                <!--</div>-->

                                <div class="col-12">
                                    <div class="form-field position-relative">
                                        <label for="property_rules">OTA Rules<sup>*</sup></label>
                                        <textarea name="property_rules" id="property_rules" rows="5" class="form-control h-auto @error('property_rules') is-invalid @enderror">{{ old('property_rules', $detail->house_rules ?? '') }}</textarea>
                                        <div class="invalid-tooltip" id="property_rules_tooltip">
                                            "&" character is not allowed.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-field">
                                        <label for="meals">Meals</label>
                                        <textarea name="meals" rows="5" class=" form-control h-auto @error('meals') is-invalid @enderror">{{ old('meals', $detail->meals ?? '') }}</textarea>
                                    </div>
                                </div>

                                <!-- <div class="col-12">
                                    <div class="form-field">
                                        <label for="property_rules">Website Property Rules<sup>*</sup></label>
                                        <textarea name="website_property_rules" rows="5" class="form-control h-auto @error('website_property_rules') is-invalid @enderror">{{ old('website_property_rules', $detail->website_property_rules ?? '') }}</textarea>
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div class="form-field">
                                        <label for="cancellation_policy">Cancellation Policy<sup>*</sup></label>
                                        <textarea name="cancellation_policy" rows="5" class=" form-control h-auto @error('cancellation_policy') is-invalid @enderror">{{ old('cancellation_policy', $detail->cancellation_policy ?? '') }}</textarea>
                                    </div>
                                </div> -->




                            </div>
                        </div>

                        <div class="btn-wrap pt-2">
                            <!-- <button type="submit" class="btn btn-primary px-5 submit-overview" id="submit_overview">SUBMIT</button> -->
                             <button type="submit" id="submit_overview" class="btn btn-primary px-5 submit-overview">
                                    <span id="btnText">SUBMIT</span>
                                    <span id="btnLoader"
                                        class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"
                                        aria-hidden="true"></span>
                                </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@php $path = asset('public/ckfinder'); @endphp
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('ru_description');
        const tooltip = document.getElementById('ru_description_tooltip');
        const submitBtn = document.getElementById('submit_overview');

        input.addEventListener('input', function() {
            const value = this.value;

            // Match first special character
            const match = value.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/);

            if (match) {
                const invalidChar = match[0];

                this.classList.add('is-invalid');
                tooltip.innerText = `"${invalidChar}" character is not allowed.`;
                tooltip.style.display = 'block';
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                this.classList.remove('is-invalid');
                tooltip.style.display = 'none';
                submitBtn.removeAttribute('disabled');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const propertyRulesInput = document.getElementById('property_rules');
        const tooltip = document.getElementById('property_rules_tooltip');
        const submitBtn = document.getElementById('submit_overview');

        propertyRulesInput.addEventListener('input', function() {
            const value = this.value;
            if (value.includes('&')) {
                this.classList.add('is-invalid');
                tooltip.style.display = 'block';
                submitBtn.setAttribute('disabled', 'disabled');
            } else {
                this.classList.remove('is-invalid');
                tooltip.style.display = 'none';
                submitBtn.removeAttribute('disabled');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        let path = "<?php echo $path; ?>";
        // ---------- CKEditor Start -------------//
        $('textarea .cancellation_policy').ckeditor();
        var imgEditor = CKEDITOR.replace('cancellation_policy');
        CKFinder.setupCKEditor(imgEditor, path);

    });

    document.addEventListener('DOMContentLoaded', () => {
        let pathm = "<?php echo $path; ?>";
        // ---------- CKEditor Start -------------//
        $('textarea .meals').ckeditor();
        var imgEditor = CKEDITOR.replace('meals');
        CKFinder.setupCKEditor(imgEditor, pathm);

    });


    document.addEventListener('DOMContentLoaded', () => {
        let pathw = "<?php echo $path; ?>";
        // ---------- CKEditor Start -------------//
        $('textarea .website_property_rules').ckeditor();
        var imgEditorw = CKEDITOR.replace('website_property_rules');
        CKFinder.setupCKEditor(imgEditorw, pathw);

    });


    document.addEventListener('DOMContentLoaded', () => {
        let pathd = "<?php echo $path; ?>";
        // ---------- CKEditor Start -------------//
        $('textarea .descriptionm').ckeditor();
        var imgEditord = CKEDITOR.replace('descriptionm');
        CKFinder.setupCKEditor(imgEditord, pathd);

    });


    document.addEventListener('DOMContentLoaded', function() {
        const tooltip = document.getElementById('ru_description_tooltip');
        $("#ru_description").on("keyup", function(event) {
            let specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
            let str = $(this).val();

            if (specialCharRegex.test(str)) {
                $(this).addClass('is-invalid').css('border-color', '#dc3545');
                tooltip.style.display = 'block';
                $('#submit_overview').attr('disabled', true);
            } else {
                $(this).removeClass('is-invalid').css('border-color', '');
                tooltip.style.display = 'none';
                $('#submit_overview').removeAttr('disabled');
            }
        });


        initializePropertyField();

        $('#role_id').on('change', function() {
            const selectedText = $(this).find('option:selected').text().toLowerCase().trim();
            if (selectedText === 'owners') {
                $('#property-field').show();
            } else {
                $('#property-field').hide();
            }
            updateDropdownDisplay();
        });

        document.addEventListener('change', function(event) {
            if (event.target.matches('#property_dropdown input[type="checkbox"]')) {
                updateDropdownDisplay();
            }
        });
    });

    function updateDropdownDisplay() {
        const dropdown = document.getElementById('propertyDropdown');
        if (!dropdown) return;
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

    function initializePropertyField() {
        const selectedRoleText = $('#role_id').find('option:selected').text().toLowerCase().trim();
        if (selectedRoleText === 'owners') {
            $('#property-field').show();
        } else {
            $('#property-field').hide();
        }
        updateDropdownDisplay();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateDropdownDisplay() {
            const dropdown = document.getElementById('locationDropdown');
            const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
            const nameSpan = dropdown.querySelector('.multi-select-name');
            const countSpan = dropdown.querySelector('.multi-select-count');
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const names = selected.map(cb => cb.getAttribute('data-name'));
            if (names.length === 0) {
                nameSpan.textContent = 'Select Collections';
                countSpan.textContent = '';
            } else if (names.length === 1) {
                nameSpan.textContent = names[0];
                countSpan.textContent = '';
            } else {
                nameSpan.textContent = `${names.length} Collections Selected`;
                countSpan.textContent = `(${names.length})`;
            }
        }
        // Initialize dropdown display on page load
        updateDropdownDisplay();
        // Update dropdown on checkbox change
        document.querySelectorAll('#locationDropdown input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', updateDropdownDisplay);
        });
    });
</script>
<script>
    $(document).ready(function() {
        function validateCapacityFields() {
            const capacity = parseInt($('#capacity').val());
            const maxOccupancy = parseInt($('#maxOccupancy').val());
            $('#capacity').removeClass('border-danger');
            $('#maxOccupancy').removeClass('border-danger');
            $('#capacityError').text('');
            $('#maxOccupancyError').text('');

            if (!isNaN(capacity) && !isNaN(maxOccupancy)) {
                if (maxOccupancy < capacity) {
                    $('#maxOccupancy').addClass('border-danger');
                    $('#maxOccupancyError').text('Value should be greater than capacity.');
                    $('#maxOccupancy').focus();
                    return false;
                }
            }
            return true;
        }
        $('#capacity, #maxOccupancy').on('input', function() {
            validateCapacityFields();
        });
        $('form').on('submit', function(e) {
            if (!validateCapacityFields()) {
                e.preventDefault();
            }
        });
    });
</script>
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {

        const numericFields = [
            'no_of_rooms',
            'min_stay',
            'capacity',
            'maximum_number_of_guests',
            'per_night_price',
            'extra_guest_charge',
            'per_pet_charge',
            'no_of_staff',
            'bedroom',
            'bathroom',
            'property_size'
        ];

        numericFields.forEach(name => {
            const input = document.querySelector(`[name="${name}"]`);
            if (!input) return;

            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '');
            });
        });

    });
</script> -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const submit_overview = document.getElementById('submit_overview');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');

    if (form) {
        form.addEventListener('submit', function () {
            submit_overview.disabled = true;
            btnText.textContent = 'Submitting...';
            btnLoader.classList.remove('d-none');
        });
    }
});
</script>
@endsection