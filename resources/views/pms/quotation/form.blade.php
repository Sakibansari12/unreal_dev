@extends('pms.layouts.app')
@section('content')
<style>
    .ulTab{list-style-type:none;margin:0;padding:0;overflow-x:auto}@media (max-width: 991.98px){.ulTab{display:-webkit-box;display:-ms-flexbox;display:flex}}.ulTab li{margin:10px 5px}.ulTab li button,.ulTab li a{border:0px;padding:10px 15px;background-color:#fff;width:100%;border-radius:6px!important;text-align:left;border:1px solid #0E0E0E;display:block;text-decoration:none}.ulTab li button.active,.ulTab li a.active{border:0px;padding:10px 15px;color:#fff;background-color:#0e0e0e}.ulTab li button[disabled],.ulTab li a[disabled]{opacity:1;color:#000}@media (max-width: 991.98px){.ulTab li button,.ulTab li a{white-space:nowrap}}
    .ulTab {
        display: block !important;
    }
    .search-btnq{
        padding: 7px 20px;
    }
    .total-person{
        position: relative
    }
    .total-person::before{
        content: "";
        position: absolute;
        width: 100%;
        height: 1px;
        background-color: #000;
        bottom:0;
    }
    .is-invalid {
        border: 1px solid red;
    }
    .label-radio.border-danger {
        border: 2px solid red;
        border-radius: 5px;
        padding: 0px;
    }
</style>
@php $routeName = Route::currentRouteName(); @endphp
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $detail ? 'Edit Quotation' : 'New Quotation' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.quotation.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
            <div class="row g-3 g-md-5">
                <div class="col-12 col-md">
                    <div class="page-content">
                        <div class="links-box mb-4">
                            <form id="quotation-search-form">
                                @csrf
                                <div class="row g-3">
                                    @foreach($locations as $idx => $location)
                                        <div class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                                            <div class="label-radio">
                                                <input
                                                    type="radio"
                                                    name="location"
                                                    id="hr{{ $idx }}"
                                                    value="{{ $location->id }}"
                                                    class="location-radio"
                                                    @if(old('location', $detail?->location_id) == $location->id) checked @endif
                                                />
                                                <label for="hr{{ $idx }}">
                                                    <h3>{{ $location->location_name }}</h3>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-12">
                                        <div class="row g-3 align-items-end">
                                            {{-- <div class="col-md-3">
                                                <label for="checkin_date" class="form-label">Arrival <span class="text-danger">*</span></label>
                                                <input type="date" name="checkin_date" id="checkin_date" value="{{ old('checkin_date', $detail ? date('Y-m-d', strtotime($detail->checkin_date)) : '') }}" min="{{ date('Y-m-d') }}" class="form-control">
                                            </div> --}}
                                            <div class="col-6 col-lg-3">
                                            <div class="form-field mb-0">
                                                <label for="checkin_date">Arrival<span class="text-danger">*</span></label>
                                                <input type="date" id="checkin_date" name="checkin_date" value="{{ old('checkin_date', $detail ? date('Y-m-d', strtotime($detail->checkin_date)) : '') }}" min="{{ date('Y-m-d') }}" class="form-control flatpickr">
                                                <div class="invalid-feedback" id="checkInDateError"></div>
                                            </div>
                                        </div>
                                            {{-- <div class="col-md-3">
                                                <label for="checkout_date" class="form-label">Departure <span class="text-danger">*</span></label>
                                                <input type="date" name="checkout_date" id="checkout_date" value="{{ old('checkout_date', $detail ? date('Y-m-d', strtotime($detail->checkout_date)) : '') }}" min="{{ date('Y-m-d') }}" class="form-control">
                                            </div> --}}
                                            <div class="col-6 col-lg-3">
                                                <div class="form-field mb-0">
                                                    <label for="checkout_date">Departure<span class="text-danger">*</span></label>
                                                    <input type="date" id="checkout_date" name="checkout_date" value="{{ old('checkout_date', $detail ? date('Y-m-d', strtotime($detail->checkout_date)) : '') }}" min="{{ date('Y-m-d') }}" class="form-control flatpickr">
                                                    <div class="invalid-feedback" id="checkOutDateError"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="no_adults" class="form-label">Ages 10+</label>
                                                <select name="no_adults" id="no_adults" class="form-select">
                                                    @for($i = 1; $i <= 20; $i++)
                                                        <option value="{{ $i }}" @if(old('no_adults', $detail?->no_adults ?? 1) == $i) selected @endif>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="no_children" class="form-label">Ages 0-10</label>
                                                <select name="no_children" id="no_children" class="form-select">
                                                    @for($i = 0; $i <= 4; $i++)
                                                        <option value="{{ $i }}" @if(old('no_children', $detail?->no_children ?? 0) == $i) selected @endif>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary search-btnq">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div id="property-results" class="mt-4">
                                @if(isset($preRenderedProperties))
                                    {!! $preRenderedProperties !!}
                                @endif
                            </div>
                        </div>
                        <div id="details-form-wrapper" class="mt-4" style="{{ $detail && $preRenderedProperties ? 'display: block;' : 'display: none;' }}">
                            <form id="propertyForm">
                                @csrf
                                <input type="hidden" name="id" value="{{ $detail?->id ?? '' }}">
                                <div class="row gx-2 gx-md-3">
                                    <div class="col-6 col-lg">
                                        <div class="form-group">
                                            <label for="email_address">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" name="email_address" value="{{ old('email_address', $detail?->email ?? '') }}" class="form-control">
                                        </div>
                                    </div>
                                    <!--<div class="col-6 col-lg">-->
                                    <!--    <div class="form-group">-->
                                    <!--        <label for="mobile_number">Mobile Number <span class="text-danger">*</span></label>-->
                                    <!--        <input type="text" name="mobile_number" value="{{ old('mobile_number', $detail?->mobile_number ?? '') }}" maxlength="12" minlength="6" oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')" class="form-control">-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <div class="col-6 col-lg-3">
                                        <div class="form-field form-group mb-0">
                                            <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                                            <div class="row gx-2">
                                                @php
                                                    $countries = DB::table('countries')->get();
                                                @endphp
                                                @php
                                                $selectedCode = old('country_code', $detail?->country_code ?? 91);
                                                @endphp
                                                <div class="col-auto">
                                                    <select id="countryCode" class="form-control form-select pe-2" name="country_code">
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->phonecode }}"
                                                                {{ $selectedCode == $country->phonecode ? 'selected' : '' }}>
                                                                {{ $country->iso }} (+{{ $country->phonecode }})
                                                            </option>
                                                        @endforeach
                                                    </select>


                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $detail?->mobile_number ?? '') }}" maxlength="13" oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 col-lg">
                                        <div class="form-group">
                                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" value="{{ old('first_name', $detail?->first_name ?? '') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg">
                                        <div class="form-group">
                                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" value="{{ old('last_name', $detail?->last_name ?? '') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6 col-lg">
                                        <div class="form-group">
                                            <label for="validity">Validity <span class="text-danger">*</span></label>
                                            <select name="validity" class="form-select">
                                                <option value="" disabled {{ old('validity', $detail?->validity ?? '') == '' ? 'selected' : '' }}>Select Validity</option>
                                                <option value="1" {{ old('validity', $detail?->validity ?? '') == '1' ? 'selected' : '' }}>1 Hour</option>
                                                <option value="24" {{ old('validity', $detail?->validity ?? '') == '24' ? 'selected' : '' }}>24 Hours</option>
                                                <option value="48" {{ old('validity', $detail?->validity ?? '') == '48' ? 'selected' : '' }}>48 Hours</option>
                                                <option value="72" {{ old('validity', $detail?->validity ?? '') == '72' ? 'selected' : '' }}>72 Hours</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3" id="submitButton">Submit</button>
                                {{-- <div id="form-success" class="text-success mt-2" style="display:none;"></div> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
.swal-toast-success {
    background-color: #28a745 !important; /* green */
    color: white;
}
.swal-toast-error {
    background-color: #dc3545 !important; /* red */
    color: white;
}
.swal-toast-warning {
    background-color: #ffc107 !important; /* yellow */
    color: black;
}
.swal-toast-info {
    background-color: #17a2b8 !important; /* blue */
    color: white;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
    function showToast(message, type = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    popup: `swal-toast-${type}`
                }
            });
        }
$(document).ready(function () {
    $('#propertyForm').on('submit', function (e) {
        e.preventDefault();


    // Disable button and show spinner
    let $submitButton = $('#submitButton');
    $submitButton.prop('disabled', true);
    $submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');



        let formData = new FormData();
        $('#quotation-search-form').serializeArray().forEach(function (field) {
            formData.append(field.name, field.value);
        });
        $('#propertyForm')
            .find('input:not(.property-checkbox), select, textarea')
            .each(function () {
                let name = $(this).attr('name');
                let value = $(this).val();
                if (name) {
                    formData.append(name, value);
                }
            });
        let propertyList = [];
        $('.property-checkbox:checked').each(function () {
            let card = $(this).closest('.card');
            let propertyId = $(this).val();
            let additionalChargesJson = card.find('.additionalCharge').val();
            let additionalCharges = [];
            if (additionalChargesJson) {
                try {
                    additionalCharges = JSON.parse(additionalChargesJson);
                } catch (e) {
                    console.error("Invalid additionalCharge JSON", e);
                }
            }
            propertyList.push({
                id: propertyId,
                ptype: card.find('.property-pType').val() || '',
                propertyName: card.find('.property-name').val() || '',
                price: card.find('.original-subtotal').val() || 0,
                website_markup_price: card.find('.original-website-markup-price').val() || 0,
                totalAmount: card.find('.sub-total').text().replace(/[^\d]/g, '') || 0,
                extraGuestCharge: card.find('.extraGuestCharge').text().replace(/[^\d]/g, '') || 0,
                taxAmount: card.find('.tax-amount').text().replace(/[^\d]/g, '') || 0,
                totalTaxableAmount: card.find('.total-taxable-amount').text().replace(/[^\d]/g, '') || 0,
                addOnsTotalAmount: card.find('.addons-sub-total').text().replace(/[^\d]/g, '') || 0,
                addOnsDiscountTotalAmount: card.find('input[name="discount_amount_additional[' + propertyId + ']"]').val() || 0,
                additionalCharges: additionalCharges,
                tax: card.find('.tax-percentage').val() || 0,
                totalPayableAmount: card.find('.final-price').text().replace(/[^\d]/g, '') || 0,
                perNightPrice: card.find('.price-per-night').text().replace(/[^\d]/g, '') || 0,
                basePrice: card.find('.base-price').text().replace(/[^\d]/g, '') || 0,
                discountAmount: card.find('input[name="discount_amount[' + propertyId + ']"]').val() || 0,
                adOnsDiscountAmount: card.find('input[name="discount_amount_additional[' + propertyId + ']"]').val() || 0,
            });
        });
        formData.append('property_list', JSON.stringify(propertyList));
        $.ajax({
            type: 'POST',
            url: "{{ route('quotation.form.save') }}",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function (response) {
                
                // setTimeout(function () {
                    // window.location.href = "{{ route('pms.quotation.list') }}";
                // }, 1000);
                showToast(response.message, 'success');
                setTimeout(function() {
                    window.location.href = '{{ route("pms.quotation.list") }}';
                }, 2000);
                // $('#form-success').text(response.message).show();
            },
            error: function (xhr) {
                $('.invalid-feedback').remove();
                $('.border-danger').removeClass('border-danger');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        let input = $('[name="' + field + '"]');
                        if (input.length) {
                            input.addClass('border-danger');
                           // input.after('<div class="invalid-feedback" style="display:block;">' + messages[0] + '</div>');
                        } else if (field === 'location') {
                            $('.location-radio').closest('.label-radio').addClass('border border-danger');
                        }
                    });
                }
            },
            complete: function () {
           
            $submitButton.prop('disabled', false);
            $submitButton.html('Submit');
        }
        });
    });

    $('#quotation-search-form').on('change', 'input[name="location"], #checkin_date, #checkout_date, #no_adults', function () {
        $('#property-results').empty();
        $('#details-form-wrapper').slideUp();
    });

    $('#quotation-search-form').on('submit', function(e) {
        e.preventDefault();
        let isValid = true;
        $('#quotation-search-form .form-control, #quotation-search-form .form-select').removeClass('is-invalid');
        $('.location-radio').closest('.label-radio').removeClass('border border-danger');
        $('.invalid-feedback').remove();
        if (!$('input[name="location"]:checked').val()) {
            $('.location-radio').closest('.label-radio').addClass('border border-danger');
            isValid = false;
        }
        if (!$('#checkin_date').val()) {
            $('#checkin_date').addClass('border-danger');
            isValid = false;
        }
        if (!$('#checkout_date').val()) {
            $('#checkout_date').addClass('border-danger');
            isValid = false;
        }
        if (!$('#no_adults').val()) {
            $('#no_adults').addClass('border-danger');
            isValid = false;
        }
        if (!isValid) {
            return;
        }
        $.ajax({
            url: '{{ route("property.search.ajax") }}',
            type: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#property-results').html('<p>Loading...</p>');
            },
            success: function(response) {
                $('#property-results').html(response.html);
                if ($('.property-checkbox:checked').length > 0) {
                    $('#details-form-wrapper').slideDown();
                }
            },
            error: function(xhr) {
                $('#property-results').html('<div class="alert alert-danger">Something went wrong!</div>');
            }
        });
    });
});
</script>

<script>
    $(document).ready(function() {
        
        // Flatpickr for date inputs
        const checkInDateInput = document.getElementById("checkin_date");
        let fpcheckInDate = flatpickr(checkInDateInput, {
           // dateFormat: "d/m/Y",
          //  minDate: "today", // 

            dateFormat: "Y-m-d",
            altInput: true, 
            altFormat: "d/m/Y",
            minDate: "today",

           // clickOpens: false,
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length) {
                    // Use selectedDates[0] directly to calculate +1 day
                    let minCheckOutDate = new Date(selectedDates[0]);
                    minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
                    
                    fpcheckOutDate.set('minDate', minCheckOutDate);

                    if (checkOutDateInput.value && dayjs(checkOutDateInput.value, 'YYYY-MM-DD').isSameOrBefore(dayjs(selectedDates[0]))) {
                        checkOutDateInput.value = '';
                    }

                    console.log('Check-in Date Selected:', dateStr);
                    fpcheckOutDate.open(); // Auto-open checkout calendar
                }
            }

        });

        checkInDateInput.addEventListener("click", () => {
            if (fpcheckInDate.isOpen) {
                fpcheckInDate.close();
            } else {
                fpcheckInDate.open();
            }
        });

        const checkOutDateInput = document.getElementById("checkout_date");
        let fpcheckOutDate = flatpickr(checkOutDateInput, {
           // dateFormat: "d/m/Y",
           // clickOpens: false,
            dateFormat: "Y-m-d",
            altInput: true, 
            altFormat: "d/m/Y",
            onChange: function(selectedDates, dateStr) {
                console.log('Check-out Date Selected:', dateStr);
            }
        });

        checkOutDateInput.addEventListener("click", () => {
            if (fpcheckOutDate.isOpen) {
                fpcheckOutDate.close();
            } else {
                fpcheckOutDate.open();
            }
        });

        // Flatpickr for time inputs
        const checkInTimeInput = document.getElementById("checkInTime");
        let fpTimecheckInTime = flatpickr(checkInTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            clickOpens: false,
            onChange: function(selectedDates, timeStr) {
                console.log('Check-in Time Selected:', timeStr);
            }
        });

        checkInTimeInput.addEventListener("click", () => {
            if (fpTimecheckInTime.isOpen) {
                fpTimecheckInTime.close();
            } else {
                fpTimecheckInTime.open();
            }
        });

        const checkOutTimeInput = document.getElementById("checkOutTime");
        let fpTimecheckOutTime = flatpickr(checkOutTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            clickOpens: false,
            onChange: function(selectedDates, timeStr) {
                console.log('Check-out Time Selected:', timeStr);
            }
        });

        checkOutTimeInput.addEventListener("click", () => {
            if (fpTimecheckOutTime.isOpen) {
                fpTimecheckOutTime.close();
            } else {
                fpTimecheckOutTime.open();
            }
        });

        function timeFormat(value) {
            if (!value) return "12:00"; // Default time
            if (typeof value === 'string') {
                // Ensure string is in HH:mm format
                const [hours, minutes] = value.split(':');
                return `${hours.padStart(2, '0')}:${minutes?.padStart(2, '0') || '00'}`;
            } else if (typeof value === 'object' && value.hours && value.minutes) {
                // Convert object to HH:mm
                return `${String(value.hours).padStart(2, '0')}:${String(value.minutes).padStart(2, '0')}`;
            }
            return "12:00"; // Fallback
        }

        function currFormat(num) {
            return new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0,
                currency: 'INR'
            }).format(num);
        }

        function currFormatTotal(num) {
            return new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0,
                currency: 'INR'
            }).format(num);
        }
        
    });
</script>
@endsection