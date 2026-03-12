@extends('pms.layouts.app')
@section('content')
    <style>
        .form-multiselect .dropdown-menu,
        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }
        .toggle-password {
            cursor: pointer;
        }
        #company, #withoutCompany {
            display: none;
            flex-wrap: wrap;
        }
        #withoutCompany {
            display: flex;
        }
        .border-danger {
            border-color: #dc3545 !important;
        }
        .form-multiselect .dropdown-menu li {padding: 5px 10px;}
    </style>

    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">
                            @if ($detail)
                                Modify Coupon
                            @else
                                Add Coupon
                            @endif
                        </h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.coupon.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <form method="post" action="{{ route('pms.coupon.save', $detail->id ?? '') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $detail->id ?? '' }}">
                <div class="content-box p-3">
                    <div class="form-box">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="title">Name<sup>*</sup></label>
                                    <input type="text" name="title" id="title"
                                        value="{{ old('title', $detail->title ?? '') }}"
                                        class="form-control @error('title') is-invalid @enderror">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="start_date">Start Date<sup>*</sup></label>
                                    <input type="text" name="start_date" id="startDate"
                                        value="{{ old('start_date', $detail->start_date ?? '') }}"
                                        placeholder="Start Date"
                                        class="form-control flatpickr @error('start_date') is-invalid @enderror">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="end_date">End Date<sup>*</sup></label>
                                    <input type="text" name="end_date" id="endDate"
                                        value="{{ old('end_date', $detail->end_date ?? '') }}"
                                        placeholder="End Date"
                                        class="form-control flatpickr @error('end_date') is-invalid @enderror">
                                    @error('end_date')
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
                                    <label for="mappedProperties">Property (Multiple Selection)<sup>*</sup></label>
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
                            
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Use Type<sup>*</sup></label>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="user_type" id="use_type_single"
                                                    value="single" {{ old('user_type', $detail->user_type ?? 'single') == 'single' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="use_type_single">Single</label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="user_type" id="use_type_multiple"
                                                    value="multiple" {{ old('user_type', $detail->user_type ?? '') == 'multiple' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="use_type_multiple">Multiple</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg" id="use_limit_field">
                                            <input type="number" name="use_limit" id="use_limit"
                                                placeholder="Use Limit"
                                                value="{{ old('use_limit', $detail->use_limit ?? '') }}"
                                                class="form-control @error('use_limit') is-invalid @enderror"
                                                {{ old('user_type', $detail->user_type ?? 'single') == 'single' ? 'disabled' : '' }}>
                                            @error('use_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--<small class="form-text text-danger fst-italic pt-1 d-block">-->
                                    <!--    For multiple coupons with unlimited use, select "Multiple" and leave the field blank-->
                                    <!--</small>-->
                                    @error('user_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Discount Type<sup>*</sup></label>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="discount_type" id="discount_type_percentage"
                                                    value="percentage" {{ old('discount_type', $detail->discount_type ?? 'percentage') == 'percentage' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="discount_type_percentage">Percentage</label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="discount_type" id="discount_type_flat"
                                                    value="flat" {{ old('discount_type', $detail->discount_type ?? '') == 'flat' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="discount_type_flat">Flat</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg" id="discount_percentage_field">
                                            <input type="number" name="discount_percentage" id="discount_percentage"
                                                placeholder="Discount Percentage (%)"
                                                value="{{ old('discount_percentage', $detail->discount_percentage ?? '') }}"
                                                class="form-control @error('discount_percentage') is-invalid @enderror"
                                                step="0.01" min="0" max="100">
                                            @error('discount_percentage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-lg" id="discount_amount_field">
                                            <input type="number" name="discount_amount" id="discount_amount"
                                                placeholder="Discount Amount"
                                                value="{{ old('discount_amount', $detail->discount_flat ?? '') }}"
                                                class="form-control @error('discount_amount') is-invalid @enderror"
                                                step="0.01" min="0">
                                            @error('discount_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Generate Code By<sup>*</sup></label>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="generated_coupon_code_by" id="generate_code_self"
                                                    value="self" {{ old('generated_coupon_code_by', $detail->generated_coupon_code_by ?? 'self') == 'self' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="generate_code_self">Self</label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="generated_coupon_code_by" id="generate_code_auto"
                                                    value="auto" {{ old('generated_coupon_code_by', $detail->generated_coupon_code_by ?? '') == 'auto' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="generate_code_auto">Auto</label>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg" id="coupon_code_field">
                                            <input type="text" name="self_code" id="self_code"
                                                placeholder="Code"
                                                value="{{ old('self_code', $detail->coupon_code_self ?? '') }}"
                                                class="form-control @error('self_code') is-invalid @enderror">
                                            @error('self_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-lg" id="auto_code_fields">
                                            <div class="row gx-2">
                                                <div class="col-4">
                                                    <input type="text" name="prefix" id="prefix"
                                                        placeholder="Prefix"
                                                        value="{{ old('prefix', $detail->prefix ?? '') }}"
                                                        class="form-control @error('prefix') is-invalid @enderror">
                                                    @error('prefix')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-8">
                                                    <input type="number" name="no_of_codes" id="no_of_codes"
                                                        placeholder="No of Codes"
                                                        value="{{ old('no_of_codes', $detail->coupon_code_auto ?? '') }}"
                                                        class="form-control @error('no_of_codes') is-invalid @enderror" min="1">
                                                    @error('no_of_codes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Stay Dates Between <small>(From/To, including the start and end night)</small><sup>*</sup></label>
                                    <div class="row gy-3">
                                        <div class="col">
                                            <input type="text" name="stay_date_from" id="stayStartDate"
                                                placeholder="Start Date"
                                                class="form-control flatpickr @error('stay_date_from') is-invalid @enderror"
                                                value="{{ old('stay_date_from', $detail->stay_date_from ?? '') }}">
                                            @error('stay_date_from')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <input type="text" name="stay_date_to" id="stayEndDate"
                                                placeholder="End Date"
                                                class="form-control flatpickr @error('stay_date_to') is-invalid @enderror"
                                                value="{{ old('stay_date_to', $detail->stay_date_to ?? '') }}">
                                            @error('stay_date_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-wrap pt-2">
                    <button class="btn btn-primary px-5">{{ $detail ? 'UPDATE' : 'SUBMIT' }}</button>
                </div>
            </form>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
    <script>
    document.addEventListener('DOMContentLoaded', function () {
            
         

    const isEditMode = @json(!empty($detail));
    const today = new Date().toISOString().split('T')[0];

    const startDateValue = @json($detail->start_date ?? null);

    const startDate = flatpickr('#startDate', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        defaultDate: startDateValue,
        onOpen: function(selectedDates, dateStr, instance) {
        instance.set('minDate', today);
        },
        onChange: function (selectedDates, dateStr, instance) {
            if (dateStr) {
                endDate.set('minDate', dateStr);
            } else {
                endDate.set('minDate', null);
            }
        }
    });

    const endDate = flatpickr('#endDate', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        defaultDate: @json($detail->end_date ?? null),
        minDate: (() => {
            const val = @json($detail->start_date ?? null);
            return val === null ? today : val;
        })()
    });

    const stayStartDateValue = @json($detail->stay_date_from ?? null);

    const stayStartDate = flatpickr('#stayStartDate', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        defaultDate: stayStartDateValue,
        onOpen: function(selectedDates, dateStr, instance) {
        instance.set('minDate', today);
        },
        onChange: function (selectedDates, dateStr, instance) {
            if (dateStr) {
                stayEndDate.set('minDate', dateStr);
            } else {
                stayEndDate.set('minDate', null);
            }
        }
    });

    const stayEndDate = flatpickr('#stayEndDate', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        defaultDate: @json($detail->stay_date_to ?? null),
        minDate: (() => {
            const val = @json($detail->stay_date_from ?? null);
            return val === null ? today : val;
        })()
    });

    startDate.config.onChange.push(function (selectedDates, dateStr) {
        const endSelected = endDate.selectedDates[0];
        if (!endSelected || selectedDates[0] > endSelected) {
            endDate.open();
        }
    });


    stayStartDate.config.onChange.push(function (selectedDates, dateStr) {
        const endSelected = stayEndDate.selectedDates[0];
        if (!endSelected || selectedDates[0] > endSelected) {
            stayEndDate.open();
        }
    });

    // Helper function to reset visible altInput if empty but date is set
function fixFlatpickrPlaceholder(flatpickrInstance) {
    const altInput = flatpickrInstance.altInput;
    const selectedDate = flatpickrInstance.selectedDates[0];

    altInput.addEventListener('blur', () => {
        if (altInput.value.trim() === '' && selectedDate) {
            // Reset visible input to formatted date
            altInput.value = flatpickrInstance.formatDate(selectedDate, flatpickrInstance.config.altFormat);
        }
    });
}

// Apply fix for startDate and stayStartDate pickers
fixFlatpickrPlaceholder(startDate);
fixFlatpickrPlaceholder(stayStartDate);

});
</script>
 <script>
document.addEventListener('DOMContentLoaded', function () {
    // Existing functions (updateDropdownDisplay, updatePropertyDropdown, etc.) remain unchanged
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

    // function updatePropertyDropdown(properties, selectedPropertyIds = []) {
    //     const dropdown = document.getElementById('property_dropdown');
    //     const nameSpan = document.getElementById('propertyDropdown').querySelector('.multi-select-name');
    //     const countSpan = document.getElementById('propertyDropdown').querySelector('.multi-select-count');

    //     const selectedIds = Array.isArray(selectedPropertyIds)
    //         ? selectedPropertyIds.map(id => id.toString())
    //         : [];

    //     dropdown.innerHTML = '';

    //     if (properties.length === 0) {
    //         dropdown.innerHTML = '<li><div class="text-muted px-3 py-2">No properties found.</div></li>';
    //         nameSpan.textContent = 'Select Properties';
    //         countSpan.textContent = '';
    //         return;
    //     }

    //     properties.forEach((property, index) => {
    //         const isChecked = selectedIds.includes(property.id.toString()) ? 'checked' : '';
    //         dropdown.innerHTML += `
    //             <li>
    //                 <div class="form-check">
    //                     <input type="checkbox" class="form-check-input" name="mappedProperties[]" id="prop-ckb-${index + 1}"
    //                         data-name="${property.unit_name}" value="${property.id}" ${isChecked}>
    //                     <label class="form-check-label" for="prop-ckb-${index + 1}">
    //                         ${property.unit_name} - ${property.id}
    //                     </label>
    //                 </div>
    //             </li>
    //         `;
    //     });

    //     updatePropertyDropdownDisplay();

    //     document.querySelectorAll('#propertyDropdown input[type="checkbox"]').forEach(function (checkbox) {
    //         checkbox.addEventListener('change', updatePropertyDropdownDisplay);
    //     });
    // }
    
    function updatePropertyDropdown(properties, selectedPropertyIds = [], homeTypeSelected = false) {
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

    // ✅ CASE 2: Add "Select All" checkbox when no home type selected
    if (!homeTypeSelected) {
        dropdown.innerHTML += `
            <li class="border-bottom pb-1 mb-1">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAllProperties">
                    <label class="form-check-label fw-bold" for="selectAllProperties">Select All</label>
                </div>
            </li>
        `;
    }

    properties.forEach((property, index) => {
        // ✅ CASE 1: If homeTypeSelected => all should be checked
        const isChecked =
            homeTypeSelected ? 'checked' :
            selectedIds.includes(property.id.toString()) ? 'checked' : '';

        dropdown.innerHTML += `
            <li>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input property-checkbox"
                        name="mappedProperties[]" id="prop-ckb-${index + 1}"
                        data-name="${property.unit_name}" value="${property.id}" ${isChecked}>
                    <label class="form-check-label" for="prop-ckb-${index + 1}">
                        ${property.unit_name}
                    </label>
                </div>
            </li>
        `;
    });

    updatePropertyDropdownDisplay();

    // Re-bind listeners for checkboxes
    document.querySelectorAll('#propertyDropdown input.property-checkbox').forEach(cb => {
        cb.addEventListener('change', updatePropertyDropdownDisplay);
    });

    // ✅ "Select All" toggle logic
        const selectAllCheckbox = document.getElementById('selectAllProperties');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                const isChecked = this.checked;
                document.querySelectorAll('#propertyDropdown input.property-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                });
                updatePropertyDropdownDisplay();
            });
        }
    }
    

    // function updatePropertyDropdownDisplay() {
    //     const dropdown = document.getElementById('propertyDropdown');
    //     const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
    //     const nameSpan = dropdown.querySelector('.multi-select-name');
    //     const countSpan = dropdown.querySelector('.multi-select-count');
    //     const selected = Array.from(checkboxes).filter(cb => cb.checked);
    //     const names = selected.map(cb => cb.getAttribute('data-name'));

    //     if (names.length === 0) {
    //         nameSpan.textContent = 'Select Properties';
    //         countSpan.textContent = '';
    //     } else if (names.length === 1) {
    //         nameSpan.textContent = names[0];
    //         countSpan.textContent = '';
    //     } else {
    //         nameSpan.textContent = `${names.length} Properties Selected`;
    //         countSpan.textContent = `(${names.length})`;
    //     }
    // }
    
//     function updatePropertyDropdownDisplay() {
//     const dropdown = document.getElementById('propertyDropdown');
//     const checkboxes = dropdown.querySelectorAll('input.property-checkbox'); // ✅ only count actual properties
//     const nameSpan = dropdown.querySelector('.multi-select-name');
//     const countSpan = dropdown.querySelector('.multi-select-count');
//     const selected = Array.from(checkboxes).filter(cb => cb.checked);
//     const names = selected.map(cb => cb.getAttribute('data-name'));

//     if (names.length === 0) {
//         nameSpan.textContent = 'Select Properties';
//         countSpan.textContent = '';
//     } else if (names.length === 1) {
//         nameSpan.textContent = names[0];
//         countSpan.textContent = '';
//     } else {
//         nameSpan.textContent = `${names.length} Properties Selected`;
//         countSpan.textContent = `(${names.length})`;
//     }
// }

function updatePropertyDropdownDisplay() {
    const dropdown = document.getElementById('propertyDropdown');
    const propertyCheckboxes = dropdown.querySelectorAll('input.property-checkbox'); // ✅ only property checkboxes
    const selectAllCheckbox = dropdown.querySelector('#selectAllProperties'); // ✅ find Select All checkbox
    const nameSpan = dropdown.querySelector('.multi-select-name');
    const countSpan = dropdown.querySelector('.multi-select-count');

    const selectedProperties = Array.from(propertyCheckboxes).filter(cb => cb.checked);
    const names = selectedProperties.map(cb => cb.getAttribute('data-name'));

    // ✅ Update display text
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

    // ✅ Automatically check/uncheck "Select All"
    if (selectAllCheckbox) {
        const allChecked = propertyCheckboxes.length > 0 && selectedProperties.length === propertyCheckboxes.length;
        selectAllCheckbox.checked = allChecked;
    }
}



    // Function to fetch properties based on home types
    function fetchProperties(homeTypeIds, selectedPropertyIds = []) {
        $.ajax({
            url: '{{ route("pms.coupon.get-properties") }}',
            method: 'POST',
            data: {
                home_type_ids: homeTypeIds,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                //console.log(response,"response");
               // updatePropertyDropdown(response.properties, selectedPropertyIds);
               const homeTypeSelected = homeTypeIds.length > 0; // ✅ check if home type selected
               updatePropertyDropdown(response.properties, selectedPropertyIds, homeTypeSelected);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching properties:', error);
                updatePropertyDropdown([], selectedPropertyIds); // Fallback to empty list
            }
        });
    }

    // Existing toggle functions (togglePeopleUseLimitField, toggleCodeGenerationRestriction, etc.) remain unchanged
    function togglePeopleUseLimitField() {
        const singleRadio = document.getElementById('use_type_single');
        const multipleRadio = document.getElementById('use_type_multiple');
        const useLimitField = document.getElementById('use_limit_field');
        const useLimitInput = document.getElementById('use_limit');
        
        if (singleRadio.checked) {
            useLimitInput.disabled = true;
            useLimitInput.value = '';
            useLimitInput.classList.remove('border-danger');
        } else {
            useLimitInput.disabled = false;
            useLimitInput.classList.add('border-danger');
            if (isEditMode) {
                useLimitInput.value = originalUseLimit;
            } else {
                if (!@json(old('use_limit'))) {
                    useLimitInput.value = '';
                }
            }
        }
    }

    function toggleCodeGenerationRestriction() {
        const multipleRadio = document.getElementById('use_type_multiple');
        const autoRadio = document.getElementById('generate_code_auto');
        const selfRadio = document.getElementById('generate_code_self');
        
        if (multipleRadio.checked) {
            //autoRadio.disabled = true;
            if (autoRadio.checked) {
               // selfRadio.checked = true;
                toggleCodeGenerationFields();
            }
        } else {
            autoRadio.disabled = false;
        }
    }

    function toggleDiscountFields() {
        const percentageRadio = document.getElementById('discount_type_percentage');
        const percentageField = document.getElementById('discount_percentage_field');
        const amountField = document.getElementById('discount_amount_field');
        const percentageInput = document.getElementById('discount_percentage');
        const amountInput = document.getElementById('discount_amount');

        const multipleRadio = document.getElementById('use_type_multiple');
        const useLimitInput = document.getElementById('use_limit');
        
        if (multipleRadio.checked && useLimitInput.value) {
            useLimitInput.classList.remove('border-danger');
        }

        if (percentageRadio.checked) {
            percentageField.style.display = 'block';
            amountField.style.display = 'none';
            if (isEditMode) {
                percentageInput.value = originalDiscountPercentage;
            } else {
                if (!@json(old('discount_amount'))) {
                    amountInput.value = '';
                }
            }
        } else {
            percentageField.style.display = 'none';
            amountField.style.display = 'block';
            if (isEditMode) {
                amountInput.value = originalDiscountAmount;
            } else {
                if (!@json(old('discount_percentage'))) {
                    percentageInput.value = '';
                }
            }
        }
    }

    function toggleCodeGenerationFields() {
        const selfRadio = document.getElementById('generate_code_self');
        const couponCodeField = document.getElementById('coupon_code_field');
        const autoCodeFields = document.getElementById('auto_code_fields');
        const couponCodeInput = document.getElementById('self_code');
        const prefixInput = document.getElementById('prefix');
      //  const noOfCodesInput = document.getElementById('no_of_codes');
      //  console.log(noOfCodesInput,"noOfCodesInput");
        if (selfRadio.checked) {
            couponCodeField.style.display = 'block';
            autoCodeFields.style.display = 'none';
            if (isEditMode) {
                couponCodeInput.value = originalSelfCode;
            } else {
                if (!@json(old('prefix'))) {
                    prefixInput.value = '';
                }
                if (!@json(old('no_of_codes'))) {
                   // noOfCodesInput.value = '';
                }
            }
        } else {
            couponCodeField.style.display = 'none';
            autoCodeFields.style.display = 'block';
            if (isEditMode) {
                prefixInput.value = originalPrefix;
               // noOfCodesInput.value = originalNoOfCodes;
            } else {
                if (!@json(old('self_code'))) {
                    couponCodeInput.value = '';
                }
            }
        }
    }

    // Initialize variables
    const isEditMode = @json($detail ? true : false);
    const originalDiscountPercentage = @json(old('discount_percentage', $detail->discount_percentage ?? ''));
    const originalDiscountAmount = @json(old('discount_amount', $detail->discount_flat ?? ''));
    const originalUseLimit = @json(old('use_limit', $detail->use_limit ?? ''));
    const originalSelfCode = @json(old('self_code', $detail->coupon_code_self ?? ''));
    const originalPrefix = @json(old('prefix', $detail->prefix ?? ''));
    const originalNoOfCodes = @json(old('no_of_codes', $detail->no_of_codes ?? ''));

    // Initial setup
    updateDropdownDisplay();
    togglePeopleUseLimitField();
    toggleCodeGenerationRestriction();
    toggleDiscountFields();
    toggleCodeGenerationFields();

    // Fetch properties on page load
    const selectedHomeTypes = Array.from(
        document.querySelectorAll('#locationDropdown input[type="checkbox"]:checked')
    ).map(cb => cb.value);
    const selectedPropertyIds = @json($userMappedPropertyIds ?? []);

    // Trigger initial fetch for properties (handles both add and edit modes)
    fetchProperties(selectedHomeTypes, selectedPropertyIds);

    // Event listener for Home Type checkboxes
    document.querySelectorAll('#locationDropdown input[type="checkbox"]').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            updateDropdownDisplay();
            const selectedHomeTypes = Array.from(
                document.querySelectorAll('#locationDropdown input[type="checkbox"]:checked')
            ).map(cb => cb.value);

            fetchProperties(selectedHomeTypes, selectedPropertyIds);
        });
    });

    // Existing event listeners for other fields
    document.querySelectorAll('input[name="user_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            console.log('User type changed to:', radio.value);
            togglePeopleUseLimitField();
            toggleCodeGenerationRestriction();
        });
    });

    document.querySelectorAll('input[name="discount_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            console.log('Discount type changed to:', radio.value);
            toggleDiscountFields();
        });
    });

    document.querySelectorAll('input[name="generated_coupon_code_by"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            console.log('Code generation changed to:', radio.value);
            toggleCodeGenerationFields();
        });
    });
});
</script>
@endsection