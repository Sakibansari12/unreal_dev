@extends('pms.layouts.app')
@section('content')
<meta name="_token" content="{{ csrf_token() }}">
<style>
    .ulTab {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow-x: auto;
    }
    @media (max-width: 991.98px) {
        .ulTab {
            display: flex;
        }
    }
    .ulTab li {
        margin: 10px 5px;
    }
    .ulTab li a {
        border: 0px;
        padding: 10px 15px;
        background-color: #fff;
        width: 100%;
        border-radius: 6px !important;
        text-align: left;
        border: 1px solid #0E0E0E;
        display: block;
        text-decoration: none;
    }
    .ulTab li a.active {
        border: 0px;
        padding: 10px 15px;
        color: #fff;
        background-color: #0e0e0e;
    }
    .is-invalid {
        border: 1px solid red;
        border-radius: 6px;
    }
    .border-danger {
        border: 1px solid red;
    }
    .small-input-group input {
        max-width: 100px;
    }
</style>

@php $routeName = Route::currentRouteName(); @endphp
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">New Booking</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.booking.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3"> 
            <div class="row g-3 g-md-5">
                <div class="col-12 col-md-3">
                    <ul class="ulTab">
                        <li class="list-group-item">
                            <a href="{{ route('pms.booking.bylocation') }}" class="{{ $routeName == 'pms.booking.bylocation' ? 'active' : '' }}">By Location</a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('pms.booking.byproperty') }}" class="{{ $routeName == 'pms.booking.byproperty' ? 'active' : '' }}">By Property</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-md">
                    <div class="form-box">
                        <div class="booking-filter">
                            <form id="searchForm">
                                <div class="row gy-4">
                                    <div class="col-12">
                                        <div class="links-box">
                                            <div id="locationLoading" class="d-flex justify-content-center py-4">
                                                <div class="spinner-border" role="status"></div>
                                            </div>
                                            <div class="row g-2 g-md-3" id="locationList">
                                                <!-- Locations will be loaded here -->
                                            </div>
                                            <div class="invalid-feedback d-block" id="locationError"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="search-location-properties">
                                            <div class="row gy-3 gx-2 gx-md-3">
                                                <div class="col-6 col-lg-3">
                                                    <div class="form-field mb-0">
                                                        <label for="checkInDate">Arrival<span class="text-danger">*</span></label>
                                                        <input type="date" id="checkInDate" name="check-in-date" class="form-control flatpickr" required>
                                                        <div class="invalid-feedback" id="checkInDateError"></div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-lg-3">
                                                    <div class="form-field mb-0">
                                                        <label for="checkOutDate">Departure<span class="text-danger">*</span></label>
                                                        <input type="date" id="checkOutDate" name="check-out-date" class="form-control flatpickr" required>
                                                        <div class="invalid-feedback" id="checkOutDateError"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <div class="row gx-2 gy-3 g-md-3">
                                                        <div class="col-12 col-sm">
                                                            <div class="form-field mb-0">
                                                                <label for="no_adults">No. of Adults</label>
                                                                <select name="no_adults" id="no_adults" class="form-control form-select">
                                                                    @for($i = 1; $i <= 20; $i++)
                                                                        <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm">
                                                            <div class="form-field mb-0">
                                                                <label for="no_children">No. of Children(Up to 4 yrs)</label>
                                                                <select name="no_children" id="no_children" class="form-control form-select">
                                                                    <option value="" selected>Please Select</option>
                                                                    <option value="0" selected>0</option>
                                                                    <option value="1">1</option>
                                                                    <option value="2">2</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-lg-auto align-self-end">
                                                            <button type="button" class="btn w-100 btn-primary fw-bold miw-120" id="searchBtn">
                                                                SEARCH
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="searchResults" class="booking-search-results mt-4 pt-4 border-top d-none">
                            <div id="loadingSpinner" class="d-flex justify-content-center py-4 d-none">
                                <div class="spinner-border" role="status"></div>
                            </div>
                            <div id="noRecordMessage" class="message text-center d-none">
                                <h6 class="fw-bold">No Record Found!</h6>
                            </div>
                            <form id="bookingForm">
                                <div class="row gy-4">
                                    <div class="col-12">
                                        <div class="links-box">
                                            <div class="row g-2 g-md-3" id="propertyList">
                                                <!-- Properties will be loaded here -->
                                            </div>
                                            <div class="invalid-feedback d-block" id="propertyError"></div>
                                        </div>
                                    </div>
                                    <div id="propertyBookingForm" class="d-none">
                                        <div id="priceLoadingSpinner" class="d-flex justify-content-center py-4 d-none">
                                            <div class="spinner-border" role="status"></div>
                                        </div>
                                        <div id="propertyFormContent">
                                            <div class="col-12">
                                                <div class="form-fields">
                                                    <div class="row gx-2 gx-md-3">
                                                        <div class="col-6 col-lg-3">
                                                            <div class="form-field">
                                                                <label for="email_address">Email Address<span class="text-danger">*</span></label>
                                                                <input type="text" name="email_address" id="email_address" class="form-control" required>
                                                                <div class="invalid-feedback" id="emailError"></div>
                                                            </div>
                                                        </div>
                    <div class="col-6 col-lg-3">
                        <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                        @php $countries = DB::table('countries')->get(); @endphp
                        <div class="input-group">
                            <select id="countryCode" class="form-control form-select pe-4" name="country_code">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->phonecode }}"
                                        {{ $country->phonecode == 91 ? 'selected' : '' }}>
                                        {{ $country->iso }} (+{{ $country->phonecode }})
                                    </option>
                                @endforeach
                            </select>
                                                              
                            <div class="form-field">
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control" required>
                                <div class="invalid-feedback" id="mobileError"></div>
                            </div>
                        </div>
                    </div>
                                                        <div class="col-6 col-lg-3">
                                                            <div class="form-field">
                                                                <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                                                                <div class="invalid-feedback" id="firstNameError"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-lg-3">
                                                            <div class="form-field">
                                                                <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                                                                <div class="invalid-feedback" id="lastNameError"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 text-end" id="priceDetailsContainer">
                                                        <div class="row justify-content-end">
                                                            <div class="col-auto">
                                                                <table class="table fs-13 table-sm table-borderless w-auto booking-price-info">
                                                                    <tbody id="priceDetails">
                                                                        <!-- Price details will be loaded here -->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 col-lg-3">
                                                            <div class="form-field">
                                                                <label for="checkInTime">Check-In Time</label>
                                                                <input type="time" name="check-in-time" id="checkInTime" class="form-control flatpickr">
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-lg-3">
                                                            <div class="form-field">
                                                                <label for="checkOutTime">Check-Out Time</label>
                                                                <input type="time" name="check-out-time" id="checkOutTime" class="form-control flatpickr">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-field">
                                                            <label for="booking_note">Note</label>
                                                            <textarea name="booking_note" id="booking_note" cols="30" rows="4" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button type="button" class="btn btn-save btn-primary" id="bookingFormBtn">
                                                    SUBMIT
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>  
        </div> 
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
$(document).ready(function() {
    // console.log('Document ready executed');
    try {
        // State variables
        let location = [];
        let homeList = [];
        let homePrices = {};
        let vLocationId = '';
        let vHomeId = '';
        let currentDay = dayjs();
        let checkInDate = '';
        let checkOutDate = '';
        let checkOutMinDate = currentDay.format();
        let noRecord = false;
        let isLoading = false;
        let isSearchLoading = false;
        let isSubmitLoading = false;
        let noOfNights = '';
        let tax = 0;
        let taxAmount = 0;
        let totalAmount = '';
        let discountAmount = '';
        let price = '';
        let no_children = '';
        let no_adults = 1;
        let gstText = 'Exclusive';
        let isInvoiceRequired = 0;
        let state = null;
        let gstSlab = [];
        let guestIncluded = '';
        let extraGuestCharge = 0;
        let extraGuestChargePerNight = '';
        let totalGuest = '';
        let extraAddedGuestNumber = '';
        let isHomePriceLoading = false;
        let isPropertyBookingForm = false;
        let addOnsTotalAmount = '';
        let addOnsDiscountTotalAmount = '';
        let adOnsDiscountAmount = '';
        let isBlock = 0;
        let booking_note = '';
        let checkInTime = '';
        let checkOutTime = '';
        let pType = '';

        // Flatpickr for date inputs
        const checkInDateInput = document.getElementById("checkInDate");
        let fpcheckInDate = flatpickr(checkInDateInput, {
            dateFormat: "d/m/Y",
            minDate: "today", // Disable past dates
            clickOpens: false,
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length) {
                    let minCheckOutDate = dayjs(dateStr).add(1, 'day').format('YYYY-MM-DD');
                    fpcheckOutDate.set('minDate', minCheckOutDate);
                    if (checkOutDateInput.value && dayjs(checkOutDateInput.value).isSameOrBefore(dayjs(dateStr))) {
                        checkOutDateInput.value = '';
                    }
                    // console.log('Check-in Date Selected:', dateStr);
                    // Set checkout calendar view to minCheckOutDate and open
                    fpcheckOutDate.jumpToDate(minCheckOutDate);
                    fpcheckOutDate.open();
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

        const checkOutDateInput = document.getElementById("checkOutDate");
        let fpcheckOutDate = flatpickr(checkOutDateInput, {
            dateFormat: "d/m/Y",
            clickOpens: false,
            onChange: function(selectedDates, dateStr) {
                // console.log('Check-out Date Selected:', dateStr);
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
                // console.log('Check-in Time Selected:', timeStr);
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
                // console.log('Check-out Time Selected:', timeStr);
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

        // Helper functions
        function showError(elementId, message) {
            $(`#${elementId}`).addClass('is-invalid');
            // $(`#${elementId}Error`).text(message).show();
        }

        function clearError(elementId) {
            $(`#${elementId}`).removeClass('is-invalid');
            $(`#${elementId}Error`).text('').hide();
        }

        function showToast(message, type = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        // Get location list
        function getLocation() {
            $('#locationLoading').removeClass('d-none');
            $.ajax({
                url: '{{ route("pms.booking.locationfetch") }}',
                type: 'GET',
                success: function(res) {
                    // console.log('Location fetch response:', res);
                    if (res.status) {
                        location = res.data || [];
                        renderLocationList();
                    } else {
                        showToast('Failed to load locations', 'error');
                        $('#locationList').html('<div class="col-12"><p>No locations available.</p></div>');
                    }
                },
                error: function(error) {
                    console.error('Location fetch error:', error);
                    showToast('Error fetching locations: ' + (error.responseJSON?.message || 'Unknown error'), 'error');
                    $('#locationList').html('<div class="col-12"><p>No locations available.</p></div>');
                },
                complete: function() {
                    $('#locationLoading').addClass('d-none');
                }
            });
        }

        function renderLocationList() {
            let html = '';
            location.forEach((obj, idx) => {
                html += `
                    <div class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                        <div class="label-radio">
                            <input type="radio" name="location[]" id="lr${idx}" value="${obj.id}" ${vLocationId === obj.id ? 'checked' : ''}>
                            <label for="lr${idx}">
                                <h3>${obj.location_name}</h3>
                            </label>
                        </div>
                    </div>
                `;
            });
            $('#locationList').html(html);

            $('input[name="location[]"]').change(function() {
                vLocationId = $(this).val();
                $('#locationError').hide();
                $('input[name="location[]"]').closest('.label-radio').removeClass('border-danger');
                onLocationChange();
            });
        }

        function onLocationChange() {
            if (checkInDate && checkOutDate) {
                homeList = [];
                $('#propertyList').html('');
                $('#searchResults').addClass('d-none');
                $('#propertyBookingForm').addClass('d-none');
            }
        }

        // Search form submission
        $('#searchBtn').on('click', function(e) {
            // console.log('Search button clicked');
            e.preventDefault();

            // Validate form
            let isValid = true;
            if (!$('input[name="location[]"]:checked').val()) {
                $('input[name="location[]"]').closest('.label-radio').addClass('border-danger');
                $('#locationError').hide();
                isValid = false;
            } else {
                $('input[name="location[]"]').closest('.label-radio').removeClass('border-danger');
                $('#locationError').hide();
            }

            if (!$('#checkInDate').val()) {
                showError('checkInDate', 'Please select check-in date');
                isValid = false;
            } else {
                clearError('checkInDate');
            }

            if (!$('#checkOutDate').val()) {
                showError('checkOutDate', 'Please select check-out date');
                isValid = false;
            } else {
                clearError('checkOutDate');
            }

            if (!isValid) return;

            isSearchLoading = true;
            isLoading = true;
            noRecord = true;

            $('#searchBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            $('#loadingSpinner').removeClass('d-none');
            $('#noRecordMessage').addClass('d-none');
            $('#searchResults').removeClass('d-none');

            vHomeId = '';
            isPropertyBookingForm = false;

            checkInDate = $('#checkInDate').val();
            checkOutDate = $('#checkOutDate').val();
            no_adults = $('#no_adults').val();
            no_children = $('#no_children').val();

            $.ajax({
                url: '{{ route("pms.booking.bylocationPropertyList") }}',
                type: 'POST',
                data: {
                    location_id: vLocationId,
                    checkin_date: checkInDate,
                    checkout_date: checkOutDate,
                    no_children: no_children,
                    no_adults: no_adults
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(res) {
                    // console.log('Property list response:', res);
                    if (res.status) {
                        homeList = res.data.property_list || [];
                        gstSlab = res.data.gst_slab || [];
                        noOfNights = res.data.no_of_nights || 0;
                        discountAmount = res.data.discount_amount || 0;
                        state = res.data.states || null;

                        // console.log('GST Slab from backend:', gstSlab); // Debug GST slab

                        if (res.data.is_gst_allowed == 1) {
                            gstText = "Inclusive";
                        }

                        noRecord = !homeList.length;
                        renderPropertyList();

                        if (noRecord) {
                            $('#noRecordMessage').removeClass('d-none');
                        }
                    } else {
                        showToast('Failed to load properties', 'error');
                    }
                },
                error: function(error) {
                    console.error('Property list error:', error);
                    showToast(error.responseJSON?.message || 'An error occurred', 'error');
                },
                complete: function() {
                    isSearchLoading = false;
                    isLoading = false;
                    $('#searchBtn').html('SEARCH');
                    $('#loadingSpinner').addClass('d-none');
                }
            });
        });

        function renderPropertyList() {
            let html = '';
            homeList.forEach((obj, idx) => {
                html += `
                    <div class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                        <div class="label-radio">
                            <input type="radio" name="home[]" id="hr${idx}" value="${obj.id}" ${vHomeId === obj.id ? 'checked' : ''}>
                            <label for="hr${idx}">
                                <h3>${obj.home_name}</h3>
                                <p><strong>₹${currFormat(obj.price)}</strong> ₹${currFormat(obj.per_night_price)}/night</p>
                                <p><i class="bi bi-person"></i> ${obj.maximum_number_of_guests} Max Occupancy</p>
                            </label>
                        </div>
                    </div>
                `;
            });
            $('#propertyList').html(html);

            $('input[name="home[]"]').change(function() {
                vHomeId = $(this).val();
                $('#propertyError').hide();
                onHomeRadio(null, vHomeId, $('input[name="home[]"]').index(this));
            });
        }

        function onHomeRadio(e, id, idx) {
            if (id) {
                isPropertyBookingForm = true;
                $('#propertyBookingForm').removeClass('d-none');
            }
            isHomePriceLoading = true;

            $('#priceLoadingSpinner').removeClass('d-none');
            $('#propertyFormContent').addClass('d-none');

            let homeListFilter = homeList.filter((item) => {
                if (id == item.id) {
                    // console.log('Selected property:', item);
                    tax = item.gst_percentage || 0;
                    extraGuestChargePerNight = item.extra_guest_charges || 0;
                    guestIncluded = item.guests_included || 0;
                    totalGuest = parseInt(no_adults) || 0;
                    checkInTime = timeFormat(item.checkin_time || '12:00');
                    checkOutTime = timeFormat(item.checkout_time || '11:00');
                    pType = item.pType || '';
                    // console.log('Extracted pType:', pType);
                    // console.log('Check-in/out times:', { checkInTime, checkOutTime });
                    if (totalGuest > guestIncluded) {
                        extraAddedGuestNumber = totalGuest - guestIncluded;
                        extraGuestCharge = extraGuestChargePerNight * extraAddedGuestNumber * noOfNights;
                        price = item.price + extraGuestCharge;
                        totalAmount = price;
                        taxAmount = (totalAmount * tax) / 100;
                    } else {
                        price = item.price;
                        totalAmount = price;
                        taxAmount = (totalAmount * tax) / 100;
                        extraGuestCharge = 0;
                    }
                    return item;
                }
            });

            homePrices = homeListFilter[0] || {};

            if (homePrices.additional_charge) {
                homePrices.additional_charge.forEach((item, index) => {
                    item.isAdditionalChargeChecked = 0;
                    let finalPrice = item.price * noOfNights;
                    homePrices.additional_charge[index].final_price = finalPrice;
                });
            }

            addOnsTotalAmount = 0;
            addOnsDiscountTotalAmount = 0;
            isInvoiceRequired = 0;
            discountAmount = '';
            adOnsDiscountAmount = '';

            getSlabBaseGst(gstSlab);
            renderPriceDetails();
            updateTimeFields();

            isHomePriceLoading = false;
            $('#priceLoadingSpinner').addClass('d-none');
            $('#propertyFormContent').removeClass('d-none');
        }

        function updateTimeFields() {
            // console.log('Updating time fields:', { checkInTime, checkOutTime });
            if (checkInTime) {
                $('#checkInTime').val(checkInTime);
                fpTimecheckInTime.setDate(checkInTime, false, 'H:i');
            }
            if (checkOutTime) {
                $('#checkOutTime').val(checkOutTime);
                fpTimecheckOutTime.setDate(checkOutTime, false, 'H:i');
            }
        }

        function renderPriceDetails() {
            // console.log('Rendering price details:', { price, totalAmount, tax, taxAmount, gstSlab });
            let html = `
                <tr>
                    <th>Price Per Night:</th>
                    <td>₹${currFormat(homePrices.per_night_price || 0)}</td>
                </tr>
                <tr>
                    <td>Number of Nights:</td>
                    <td>${noOfNights}</td>
                </tr>
                <tr>
                    <td>Base Price:</td>
                    <td>₹${currFormat(homePrices.price || 0)}</td>
                </tr>
            `;

            if (extraGuestCharge > 0) {
                html += `
                    <tr>
                        <td>Extra Guest:</td>
                        <td>₹${currFormat(extraGuestCharge)}</td>
                    </tr>
                `;
            }

            html += `
                <tr>
                    <td>Discount:</td>
                    <td>
                        <div class="input-group small-input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="discount_amount" class="form-control" value="${discountAmount || ''}" data-max="${price?.toFixed() || 0}" min="0">
                            <div class="invalid-feedback">The value should be less than or equal to ₹${currFormatTotal(price || 0)}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Sub Total:</th>
                    <th>₹${currFormatTotal(totalAmount || 0)}</th>
                </tr>
            `;

            if (homePrices.additional_charge && homePrices.additional_charge.length) {
                html += `
                    <tr>
                        <th>Add-ons</th>
                        <td></td>
                    </tr>
                `;

                homePrices.additional_charge.forEach((obj, index) => {
                    html += `
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input float-none" id="add-charge-${index}" 
                                        name="additional_charge[${index}]" value="1" data-price="${obj.price}" ${obj.isAdditionalChargeChecked ? 'checked' : ''}>
                                    <label for="add-charge-${index}" class="fw-normal text-nowrap mb-0 ps-2">${obj.name}:</label>
                                </div>
                            </td>
                            <td>₹${currFormat(obj.final_price)}</td>
                        </tr>
                    `;
                });

                html += `
                    <tr>
                        <td>Discount:</td>
                        <td>
                            <div class="input-group small-input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="add_ons_discount_amount" class="form-control" value="${adOnsDiscountAmount || ''}" data-max="${addOnsTotalAmount?.toFixed() || 0}" min="0">
                                <div class="invalid-feedback">The value should be less than or equal to ₹${currFormatTotal(addOnsTotalAmount || 0)}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Sub Total:</th>
                        <th>₹${currFormatTotal(addOnsDiscountTotalAmount || 0)}</th>
                    </tr>
                `;
            }

            const totalTaxable = totalTaxableAmount();
            const totalPayable = totalPayableAmount();

            html += `
                <tr>
                    <th>Total Taxable Amount:</th>
                    <td>₹${currFormatTotal(totalTaxable || 0)}</td>
                </tr>
                <tr>
                    <td>GST (${tax}%):</td>
                    <td>₹${currFormat(taxAmount || 0)}</td>
                </tr>
                <tr class="fs-6">
                    <th class="text-primary">Total Amount Payable:</th>
                    <th class="text-primary">₹${currFormatTotal(totalPayable || 0)}</th>
                </tr>
            `;

            $('#priceDetails').html(html);

            // Rebind event listeners [Fix: Discount Input Focus]
            $('input[name="discount_amount"]').off('input change keydown focus blur').on({
                input: function(e) {
                    const input = e.target;
                    const newValue = Number(input.value) || 0;
                    const max = Number(input.dataset.max) || 0;
                    // console.log('Discount Input:', { value: input.value, newValue, max });

                    // Update state without re-rendering
                    discountAmount = newValue;
                    if (discountAmount > max) {
                        input.classList.add('is-invalid');
                        $(input).next('.invalid-feedback').show();
                        totalAmount = price;
                    } else {
                        input.classList.remove('is-invalid');
                        $(input).next('.invalid-feedback').hide();
                        totalAmount = price - discountAmount;
                    }

                    getSlabBaseGst(gstSlab);
                    // Defer rendering to blur or change
                },
                change: function(e) {
                    // Render on change (e.g., Enter or Tab)
                    renderPriceDetails();
                },
                keydown: function(e) {
                    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                        e.preventDefault(); // Prevent number input arrow issues
                    }
                },
                focus: function() {
                    $(this).select(); // Select text for easy editing
                },
                blur: function() {
                    const input = this;
                    if (!input.value) {
                        input.value = discountAmount || '';
                    }
                    renderPriceDetails(); // Render on blur to update table
                }
            });

            $('input[name="add_ons_discount_amount"]').off('input change keydown focus blur').on({
                input: function(e) {
                    const input = e.target;
                    const newValue = Number(input.value) || 0;
                    const max = Number(input.dataset.max) || 0;

                    adOnsDiscountAmount = newValue;
                    if (adOnsDiscountAmount > max) {
                        input.classList.add('is-invalid');
                        $(input).next('.invalid-feedback').show();
                        addOnsDiscountTotalAmount = addOnsTotalAmount;
                    } else {
                        input.classList.remove('is-invalid');
                        $(input).next('.invalid-feedback').hide();
                        addOnsDiscountTotalAmount = addOnsTotalAmount - adOnsDiscountAmount;
                    }

                    getSlabBaseGst(gstSlab);
                    // Defer rendering to blur
                },
                change: function() {
                    renderPriceDetails();
                },
                keydown: function(e) {
                    if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                        e.preventDefault();
                    }
                },
                focus: function() {
                    $(this).select();
                },
                blur: function() {
                    const input = this;
                    if (!input.value) {
                        input.value = adOnsDiscountAmount || '';
                    }
                    renderPriceDetails();
                }
            });

            $('input[name^="additional_charge"]').off('change').on('change', onChangeAdditionalChargeCheckbox);
        }

        function getSlabBaseGst(list) {
            let totalTaxable = totalTaxableAmount();
            let slabAmount = totalTaxable >= 1 && noOfNights > 0 ? totalTaxable / noOfNights : 1;
            // console.log('GST Calculation Inputs:', { 
            //     slabAmount, 
            //     totalTaxable, 
            //     noOfNights, 
            //     gstSlab: list 
            // });

            // Reset tax and taxAmount
            tax = 0;
            taxAmount = 0;

            if (slabAmount < 1 || !list || !list.length) {
                // console.log('No valid slabs or slabAmount < 1, setting tax to 0');
                taxAmount = 0;
            } else {
                // console.log('Checking GST slabs:', list);
                let matched = false;
                list.forEach((item, idx) => {
                    let start = Number(item.slabs_start) || 0;
                    let end = Number(item.slabs_upto) || Infinity;
                    // console.log('Slab Check:', { 
                    //     index: idx, 
                    //     slabs_start: start, 
                    //     slabs_upto: end, 
                    //     gst_percentage: item.gst_percentage, 
                    //     slabAmount 
                    // });

                    if (slabAmount >= start && slabAmount <= end) {
                        tax = Number(item.gst_percentage) || 0;
                        taxAmount = (totalTaxable * tax) / 100;
                        matched = true;
                        // console.log('Slab Matched:', { 
                        //     tax, 
                        //     taxAmount, 
                        //     slabAmount, 
                        //     start, 
                        //     end 
                        // });
                    }
                });

                if (!matched) {
                    // console.log('No slab matched for slabAmount:', slabAmount);
                }
            }

            // console.log('Final GST Result:', { tax, taxAmount, totalTaxable });
        }

        function onChangeAdditionalChargeCheckbox(e) {
            const index = $(e.target).attr('id').split('-')[2];
            const isChecked = e.target.checked ? 1 : 0;

            if (homePrices.additional_charge[index]) {
                homePrices.additional_charge[index].isAdditionalChargeChecked = isChecked;
            }

            let additionalPrice = 0;
            if (homePrices.additional_charge) {
                additionalPrice = homePrices.additional_charge.reduce((value, item) => {
                    let finalPrice = 0;
                    if (Number(item.isAdditionalChargeChecked)) {
                        finalPrice = item.price * noOfNights;
                    }
                    return value + finalPrice;
                }, 0);
            }

            addOnsTotalAmount = additionalPrice;
            addOnsDiscountTotalAmount = additionalPrice;
            adOnsDiscountAmount = '';
            $('input[name="add_ons_discount_amount"]').val('').removeClass('is-invalid');

            getSlabBaseGst(gstSlab);
            renderPriceDetails();
        }

        function totalTaxableAmount() {
            return Number(totalAmount || 0) + Number(addOnsDiscountTotalAmount || 0);
        }

        function totalPayableAmount() {
            return totalTaxableAmount() + Number(taxAmount || 0);
        }

        // Booking form submission
        $('#bookingFormBtn').on('click', function(e) {
            e.preventDefault();

            let isValid = true;
            if (!$('#email_address').val()) {
                showError('email_address', 'Please enter email address');
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test($('#email_address').val())) {
                showError('email_address', 'Please enter a valid email address');
                isValid = false;
            } else {
                clearError('email_address');
            }

            if (!$('#mobile_number').val()) {
                showError('mobile_number', 'Please enter mobile number');
                isValid = false;
            } else if (!/^\d+$/.test($('#mobile_number').val())) {
                showError('mobile_number', 'Please enter a valid mobile number');
                isValid = false;
            } else {
                clearError('mobile_number');
            }

            if (!$('#first_name').val()) {
                showError('first_name', 'Please enter first name');
                isValid = false;
            } else if (!/^[a-zA-Z]+$/.test($('#first_name').val())) {
                showError('first_name', 'First name must contain only letters');
                isValid = false;
            } else {
                clearError('first_name');
            }

            if (!$('#last_name').val()) {
                showError('last_name', 'Please enter last name');
                isValid = false;
            } else if (!/^[a-zA-Z]+$/.test($('#last_name').val())) {
                showError('last_name', 'Last name must contain only letters');
                isValid = false;
            } else {
                clearError('last_name');
            }

            if (!$('input[name="home[]"]:checked').val()) {
                $('#propertyError').text('Please select a property').show();
                $('input[name="home[]"]').closest('.label-radio').addClass('border-danger');
                isValid = false;
            } else {
                $('#propertyError').hide();
                $('input[name="home[]"]').closest('.label-radio').removeClass('border-danger');
            }

            if (!isValid) return;

            isSubmitLoading = true;
            $('#bookingFormBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

            let formData = {
                locationId: vLocationId,
                propertyId: vHomeId,
                checkInDate: checkInDate,
                checkOutDate: checkOutDate,
                noOfNights: noOfNights,
                tax: tax,
                taxAmount: taxAmount,
                netAmount: totalAmount,
                netPayableAmount: totalPayableAmount(),
                discount_amount: discountAmount,
                additional_charges: homePrices.additional_charge,
                type: 'Location',
                no_children: no_children,
                no_adult: no_adults,
                per_night_price: homePrices.per_night_price,
                initial_price: homePrices.initial_price,
                dont_block: $('#cbk-block').is(':checked') ? 1 : 0,
                is_invoice: isInvoiceRequired,
                id: '',
                tot_additional_charge_amount: addOnsDiscountTotalAmount,
                base_price: price,
                extra_guest_charge: extraGuestCharge,
                totalTaxableAmount: totalTaxableAmount(),
                booking_note: $('#booking_note').val(),
                checkin_time: $('#checkInTime').val() || '12:00',
                checkout_time: $('#checkOutTime').val() || '11:00',
                email_address: $('#email_address').val(),
                mobile_number: $('#mobile_number').val(),
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                country_code: $('#countryCode').val(),
                type: pType,
            };

            // console.log('Form Data before submission:', formData); // Debug formData

            $.ajax({
                url: '{{ route("pms.booking.save") }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(res) {
                    if (res.status) {
                        showToast(res.message, 'success');
                        setTimeout(function() {
                            window.location.href = '{{ route("pms.booking.list") }}';
                        }, 2000);
                    }
                },
                error: function(error) {
                    console.error('Booking submission error:', error);
                    showToast(error.responseJSON?.message || 'An error occurred', 'error');
                    isSubmitLoading = false;
                    $('#bookingFormBtn').html('SUBMIT');
                }
            });
        });

        // Initialize on page load
        getLocation();
    } catch (error) {
        console.error('Document ready error:', error);
    }
});
</script>
@endsection