@extends('pms.layouts.app')
@section('content')
<style>
    .ulTab{list-style-type:none;margin:0;padding:0;overflow-x:auto}@media (max-width: 991.98px){.ulTab{display:-webkit-box;display:-ms-flexbox;display:flex}}.ulTab li{margin:10px 5px}.ulTab li button,.ulTab li a{border:0px;padding:10px 15px;background-color:#fff;width:100%;border-radius:6px!important;text-align:left;border:1px solid #0E0E0E;display:block;text-decoration:none}.ulTab li button.active,.ulTab li a.active{border:0px;padding:10px 15px;color:#fff;background-color:#0e0e0e}.ulTab li button[disabled],.ulTab li a[disabled]{opacity:1;color:#000}@media (max-width: 991.98px){.ulTab li button,.ulTab li a{white-space:nowrap}}
    .ulTab {
        display: block !important; /* Ensure the menu is always visible */
    }
.datepicker__month-button{
    text-indent: 0px !important;
}

.datepicker__month-button .datepicker__month-button--prev{
  display: none;
}
.datepicker__month-button:after {
    background-repeat: no-repeat;
    background-position: center;
    float: left;
    text-indent: 0;
    content: "";
    width: 15px;
    height: 15px;
}
.datepicker__month-button--next:after{
    background-image: url(./right.svg);
    background-size: cover;
}
.datepicker__month-button--prev:after{
    background-image: url(./left.svg);
    background-size: cover;
}
.close-datepicker{
    border-radius: 2px;
    background-color: #15274C;
    border: none;
    -webkit-box-shadow: none;
    box-shadow: none;
    font-size: 10px;
    color: #fff;
    margin-top: 2px;
    margin-left: 8px;
    padding: 6px 13px;
    text-decoration: none;
    text-shadow: none;
    text-transform: uppercase;
}
.close-datepicker:hover{
    background-color: #002164;
    color: #fff;
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
                            <a href="{{route('pms.booking.byproperty')}}" class="{{ $routeName == 'pms.booking.byproperty' ? 'active' : '' }}">By Property</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-md">
                    <div class="page-content">
                        <div class="links-box mb-4">
                            <div class="row g-2 g-md-3">
                                @foreach($locations as $idx => $location)
                                <div class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                                    <div class="label-radio">
                                        <input
                                            type="radio"
                                            name="location"
                                            id="hr{{ $idx }}"
                                            value="{{ $location->id }}"
                                            class="location-radio"
                                            @if(old('location') == $location->id) checked @endif
                                        />
                                        <label for="hr{{ $idx }}">
                                            <h3>{{ $location->location_name }}</h3>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div id="property-list" class="row g-2 g-md-3 mt-4">
                        <!-- Property cards will be loaded here -->
                    </div>
                    <div class="search-location-properties mt-4 mb-4 d-none" id="search-form">
                        <form id="searchFormId">
                            <div class="row gy-3 gx-2 gx-md-3">
                                <div class="col-6 col-lg-auto">
                                    <div class="form-field mb-0">
                                        <label for="checkInDate">Arrival<span class="text-danger">*</span></label>
                                        <input type="date" id="checkInDate" name="check_in_date" class="form-control flatpickr">
                                        <div class="invalid-feedback" id="checkInDateError"></div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-auto">
                                    <div class="form-field mb-0">
                                        <label for="checkOutDate">Departure<span class="text-danger">*</span></label>
                                        <input type="date" id="checkOutDate" name="check_out_date" class="form-control flatpickr">
                                        <div class="invalid-feedback" id="checkOutDateError"></div>
                                    </div>
                                </div>
                                {{-- <div class="col-6 col-lg-auto">
                                    <div class="form-field mb-0">
                                        <label for="check-in-date">Check-In - Check-Out<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="check-in-date" name="check_in_date">
                                    </div>
                                </div> --}}

                                <div class="col-12 col-lg">
                                    <div class="row gx-2 gy-3 g-md-3">
                                        <div class="col-12 col-sm-auto">
                                            <div class="form-field mb-0">
                                                <label for="no_adults">No. of Adults(0-10)</label>
                                                <select id="no_adults" name="no_adults" class="form-control form-select" >
                                                     <option value="">Please Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-auto">
                                            <div class="form-field mb-0">
                                                <label for="no_children">No. of Children (Up to 10 yrs)</label>
                                                <select name="no_children" class="form-control form-select">
                                                    <option value="">Please Select</option>
                                                    <option value="0">0</option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                            </div>
                                        </div>

                                       
                                    </div>
                                </div>
                                
                                <div class="col-12 col-lg-auto align-self-end">
                                            <button type="submit" class="btn w-100 btn-primary fw-bold miw-120" id="search-btn">
                                                SEARCH
                                            </button>
                                </div>
                            </div>
                        </form>
                    </div>


                    <div id="rendered-property-details" class="mt-4">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- <script src="{{asset('assets/pms/js/hoteldatepicker.js')}}"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedPropertyId = null;
    let lastSelectedRadio = null;
    $('.location-radio').on('change', function() {
        const locationId = $(this).val();

        $.ajax({
            url: "{{ route('pms.booking.propertyListByLocationId') }}",
            type: "POST",
            data: {
                location_id: locationId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            beforeSend: function() {
                $('#property-list').html('<p>Loading properties...</p>');
            },
            success: function(response) {
                if (response.status) {
                    let properties = response.data.property_list;
                    let html = '';
                    if (properties.length > 0) {
                        properties.forEach(function(item, idx) {
                            html += `
                                <div class="col-6 col-md-4 col-lg-3 col-xxl-auto">
                                    <div class="label-radio">
                                        <input
                                            type="radio"
                                            name="property"
                                            id="pr${idx}"
                                            value="${item.id}"
                                            data-guests="${item.maximum_number_of_guests}"
                                            data-ptype="${item.pType}"
                                        />
                                        <label for="pr${idx}">
                                            <h3>${item.home_name}</h3>
                                            <p><span class="material-symbols-outlined">person </span> ${item.maximum_number_of_guests} Max Occupancy</p>
                                        </label>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<p>No properties found for this location.</p>';
                    }

                    $('#property-list').html(html);

                    $('#search-form').addClass('d-none');
                    $('#searchFormId')[0].reset();
                    $('#rendered-property-details').empty();

                    $(document).on('click', '#property-list input[type="radio"]', function() {
                        // Hide the search form
                        $('#search-form').addClass('d-none');
                        // Reset the form
                        $('#searchFormId')[0].reset();
                        // Clear the rendered property details
                        $('#rendered-property-details').empty('');
                    });

                } else {
                    $('#property-list').html('<p>' + response.message + '</p>');
                    $('#search-form').addClass('d-none');
                    $('#searchFormId')[0].reset();
                    $('#rendered-property-details').empty('');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                $('#property-list').html('<p>Something went wrong. Please try again.</p>');
            }
        });
    });

    // Use document event for dynamic elements
    $(document).on('click', '#property-list input[type="radio"]', function () {
        const selectedVal = $(this).val();

        const $dropdown = $('#no_adults');
        const maxGuests = $(this).data('guests');

        $dropdown.empty().append('<option value="">Please Select</option>');
        for (let i = 1; i <= maxGuests; i++) {
            $dropdown.append(`<option value="${i}" ${i === 1 ? 'selected' : ''}>${i}</option>`);
        }

        // If same radio is clicked again, uncheck it manually
        if (lastSelectedRadio && lastSelectedRadio.is(this)) {
            $(this).prop('checked', false);
            lastSelectedRadio = null;
            selectedPropertyId = null;

            $('#search-form').addClass('d-none');
            $('#searchFormId')[0].reset();
            $('#rendered-property-details').empty();
        } else {
            lastSelectedRadio = $(this);
            selectedPropertyId = selectedVal;

            // Optional: trigger form or show data here
            // Example: $('#search-form').removeClass('d-none');
        }
    });
});

let fpcheckInDate, fpcheckOutDate;

$(document).on('change', 'input[name="property"]', function () {
    let propertyId = $(this).val();
    let ptype = $(this).data('ptype');
    $.ajax({
        url: '{{route("pms.booking.propertyBookingUnavailableDates")}}',
        type: 'GET',
        data: { property_id: propertyId , pType: ptype},
        success: function (res) {
            if (res.status) {
                let blockedDates = res.propertyUnavailableDates;


                const checkInDateInput = document.getElementById("checkInDate");
                const checkOutDateInput = document.getElementById("checkOutDate");

                // Destroy old instances if they exist
                if (fpcheckInDate) {
                    fpcheckInDate.destroy();
                }
                if (fpcheckOutDate) {
                    fpcheckOutDate.destroy();
                }

                // First, initialize Check-out
                fpcheckOutDate = flatpickr(checkOutDateInput, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: 'd/m/Y',
                    minDate: "today",
                    disable: blockedDates,
                    clickOpens: true,
                    onChange: function (selectedDates, dateStr) {
                        console.log('Check-out Date Selected:', dateStr);
                    }
                });

                checkOutDateInput.addEventListener("click", () => {
                    if (fpcheckOutDate && typeof fpcheckOutDate.open === 'function') {
                        fpcheckOutDate.open();
                    }
                });

                // Now initialize Check-in
                fpcheckInDate = flatpickr(checkInDateInput, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: 'd/m/Y',
                    minDate: "today",
                    disable: blockedDates,
                    clickOpens: true,
                    onChange: function (selectedDates, dateStr) {
                        if (selectedDates.length) {
                            let minCheckOutDate = dayjs(dateStr).add(1, 'day').format('YYYY-MM-DD');
                            fpcheckOutDate.set('minDate', minCheckOutDate);
                            if (checkOutDateInput.value && dayjs(checkOutDateInput.value).isSameOrBefore(dayjs(dateStr))
                            ){
                                checkOutDateInput.value = '';
                            }

                            $.ajax({
                                url: '{{ route("pms.checkBookingDate") }}',
                                type: 'POST',
                                data: {
                                    property_id: propertyId,
                                    checkin_date: dateStr,
                                    pType: ptype,
                                    _token: $('meta[name="_token"]').attr('content')
                                },
                                success: function (response) {
                                    updatedBlockedDates = response.date;
                                    fpcheckOutDate.set('disable', updatedBlockedDates);
                                },
                                error: function (xhr) {
                                    console.error('Error in checkBookingDate API:', xhr.responseText);
                                    alert('Failed to fetch booking date availability. Please try again.');
                                }
                            });

                            fpcheckOutDate.open();
                        }
                    }
                });

                checkInDateInput.addEventListener("click", () => {
                    if (fpcheckInDate && typeof fpcheckInDate.open === 'function') {
                        fpcheckInDate.open();
                    }
                });


                $('#search-form').removeClass('d-none');
            } else {
                alert('Something went wrong!');
            }
        },
        error: function () {
            alert('AJAX Error!');
        }
    });
});

</script>

<script>

    // Helper functions
    function showError(elementId, message) {
        $(`#${elementId}`).addClass('is-invalid');
        // $(`#${elementId}Error`).text(message).show();
    }

    function clearError(elementId) {
        $(`#${elementId}`).removeClass('is-invalid');
        $(`#${elementId}Error`).text('').hide();
    }

    $(document).on('submit', '#searchFormId', function(e) {
        e.preventDefault();

        $('#checkInDate').removeClass('is-invalid');
        let checkInDate = $('#checkInDate').val().trim();
        // Client-side validation
        if (checkInDate === '') {
            $('#checkInDate').addClass('is-invalid');
            // alert(checkInDate);
            return;
        }

        let isValid = true;

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

        // Add the selected location to the form data
        const selectedLocation = $('input[name="location"]:checked').val();
        if (selectedLocation) {
            // Append the selected location to the form data
            $('#searchFormId').append(`<input type="hidden" name="location_id" value="${selectedLocation}">`);
        }

        const selectedPropertyId = $('input[name="property"]:checked').val(); // Get the property ID of the clicked radio button
        if (selectedPropertyId) {
            $('#searchFormId').append(`<input type="hidden" name="propertyId" value="${selectedPropertyId}">`);
        }
        const selectedPropertyInput = $('input[name="property"]:checked');
        const selectedPtype = $('input[name="property"]:checked').data('ptype');
        if (selectedPtype) {
            $('#searchFormId').append(`<input type="hidden" name="pType" value="${selectedPtype}">`);
        }

        const form = $(this);
        const formData = form.serialize();

        // Loader dikhana
        $('#search-btn-loader').removeClass('d-none');
        $('#search-btn-text').text('Searching...').addClass('opacity-50');

        $.ajax({
            url: "{{ route('pms.booking.searchProperties') }}",
            method: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(response) {
                $('#search-btn-loader').addClass('d-none');
                $('#search-btn-text').text('SEARCH').removeClass('opacity-50');
                if (response.status) {
                    if(response.data.propertyDetail){
                        $('#rendered-property-details').empty(); // Clear old content
                        if (typeof calculate === 'function') {
                            calculate();
                        }
                        $('#rendered-property-details').html(response.html);
                    }
                    // Inject the returned HTML into a div
                } else {
                    alert(response.message || 'No properties found.');
                }
            },
            error: function(xhr) {
                $('#search-btn-loader').addClass('d-none');
                $('#search-btn-text').text('SEARCH').removeClass('opacity-50');

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    if (errors.check_in_date) {
                        $('#check-in-date').addClass('is-invalid');
                        // Optionally show error below input
                        if (!$('#check-in-date').next('.invalid-feedback').length) {
                            $('#check-in-date').after('<div class="invalid-feedback">' + errors.check_in_date[0] + '</div>');
                        }
                    }
                } else {
                    // alert('Something went wrong. Please try again.');
                    $('#rendered-property-details').empty();
                    $('#rendered-property-details').html('No record found.');
                    console.error(xhr.responseText);
                }
            }
        });
    });

</script>
@endsection