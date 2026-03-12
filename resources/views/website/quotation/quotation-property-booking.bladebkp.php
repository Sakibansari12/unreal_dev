@extends('website.layouts.app')
@section('content')
<style>
    .section-payment-page .bg-subPrimamry .even-col{
        height: 100%;
    }
</style>

    <div class="breadcrum">
        <div class="container-fluid">
            <ul>
                <li><a href="{{route('index')}}">Home</a></li>
                <li>Booking</li>
            </ul>
        </div>
    </div>
    <div class="container-fluid mt-5 payment-pageback">
        <div class="row">
            <div class="col-auto">
                <a href="{{ route('quotation-property-detail', [
                    'slug' => base64_encode($bookingQuotationProperty->id),
                    'ptype' => $propertyDetail->ptype,
                ]) }}"
                    class="back d-flex gap-2 align-items-center text-decoration-none text-dark">
                    <h5 class="mb-0">Go Back</h5>
                </a>
            </div>
        </div>
    </div>
    <section class="section-payment-page">
        <div class="container-fluid">
            <div class="row g-5">
                <div class="col-12 col-lg">
                    <div class="row gy-5">
                        <div class="col-12">
                            <div class="row align-items-center g-4">
                                <div class="col-12 col-md-6 col-lg-5">
                                     @php $firstImage = $propertyDetail->images->first(); @endphp
                                       @if ($firstImage)
                                    <img src="{{ asset($firstImage->medium_image ?? 'assets/images/noimage-property.jpg') }}" alt="" class="img-fluid rounded">
                                     @else
                                        <img src="{{ asset('assets/images/noimage-property.jpg') }}" alt="">
                                    @endif
                                </div>
                                <div class="col-12 col-md-6 col-lg-7">
                                    <h2 class="property-heading">
                                        {{ $propertyDetail->unit_name_website ?? '' }}
                                    </h2>
                                    <div class="d-flex justify-content-between mt-3">
                                        <span>
                                            {{ $propertyDetail->locationData->location_name ?? '' }},
                                            {{ $propertyDetail->state }}
                                        </span>
                                        <div class="ratings d-flex gap-3">
                                            @php
                                                $averageRating = $propertyDetail->homeReviews->avg('rating') ?? 5;
                                            @endphp
                                            <span class="btn btn-outline-primary rounded-pill">
                                                {{ number_format($averageRating, 1) }}
                                                <i class="icon-star"></i>
                                            </span>
                                            <a href="javascript:void()" class="reviews" data-fancybox
                                                data-src="#all-reviews">
                                                {{ $totalReviews == 1 ? $totalReviews . ' Review' : $totalReviews . ' Reviews' }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="bg-subPrimamry mt-3">
                                        <div class="col-12 col-md-4">
                                            <div class="even-col">
                                                <small>Check-in</small>
                                                <br>
                                                <span><b>
                                                        {{ date('d M Y', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkin_date ?? '')) }}</b></span>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="even-col">
                                                <small>Check-out</small>
                                                <br>
                                                <span><b>{{ date('d M Y', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkout_date ?? '')) }}</b></span>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="odd-col">
                                                <small>Guests</small>
                                                <br>
                                                <span><b>{{ $bookingQuotationProperty->bookingQuotationDetail->no_adults == 1 ? $bookingQuotationProperty->bookingQuotationDetail->no_adults . ' Adult' : $bookingQuotationProperty->bookingQuotationDetail->no_adults . ' Adults' }}
                                                        ,
                                                        {{ $bookingQuotationProperty->bookingQuotationDetail->no_children == 1 ? $bookingQuotationProperty->bookingQuotationDetail->no_children . ' Children' : $bookingQuotationProperty->bookingQuotationDetail->no_children . ' Children' }}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <form action="#" method="post" id="yourFormId" class="form-group mt-4 user-form px-4 py-4">
                                @csrf
                                <h3>Guest Information</h3>
                                <div class="row g-4">
                                    <input type="hidden" name="property_id" id="property_id"
                                        value="{{ $propertyDetail->id }}">
                                    <input type="hidden" name="ptype" id="ptype"
                                        value="{{ $propertyDetail->ptype }}">
                                    <input type="hidden" name="booking_quotation_id"
                                        value="{{ $bookingQuotationProperty->booking_quotation_id }}">
                                    <input type="hidden" name="website_markup_price"
                                        value="{{ $bookingQuotationProperty->website_markup_price }}">
                                    <input type="hidden" name="num_formatted_tot_price"
                                        value="{{ number_format($bookingQuotationProperty->payable_amount) }}">
                                    <input type="hidden" name="discountAmount"
                                        value="{{ $bookingQuotationProperty->discountAmount }}">
                                    <input type="hidden" name="formatted_total_taxable_amount"
                                        value="{{ number_format($bookingQuotationProperty->gst_amount) }}">
                                    <input type="hidden" name="childrenCount"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->no_children }}">
                                    <input type="hidden" name="adultsCount"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->no_adults }}">
                                    <input type="hidden" name="ci_date"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->checkin_date }}">
                                    <input type="hidden" name="co_date"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->checkout_date }}">
                                    <input type="hidden" name="price_per_night_num_formatted"
                                        value="{{ number_format($bookingQuotationProperty->per_night_price) }}">
                                    <input type="hidden" name="tot_no_of_days"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights }}">
                                    <input type="hidden" name="tax" value="{{ $bookingQuotationProperty->gst }}">
                                    <input type="hidden" name="base_price"
                                        value="{{ $bookingQuotationProperty->basePrice }}">
                                    <input type="hidden" name="total_price"
                                        value="{{ $bookingQuotationProperty->payable_amount }}">


                                      <input type="hidden" name="channel" id="channel" value="Quotation">

                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="first_name" id="first_name"
                                            value="{{ old('first_name') }}" placeholder="First Name*">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" name="last_name" id="last_name"
                                            value="{{ old('last_name') }}"placeholder="Last Name*">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="row form-group d-flex gap-2">
                                            @php
                                                $countries = DB::table('countries')->get();
                                            @endphp
                                            <div class="col-auto">
                                                <select name="country_code" id="country_code"
                                                    class="form-select country_code" class="form-select">
                                                    @foreach ($countries as $country)
                                                        <option data-countryCode="{{ $country->phonecode }}"
                                                            value="{{ $country->phonecode }}"
                                                            {{ $country->phonecode == 91 ? 'selected' : '' }}>
                                                            {{ $country->iso }} (+{{ $country->phonecode }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control" placeholder="Phone Number*"
                                                    name="phone_number" id="phone_number" maxlength="12" minlength="6">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <input type="email" class="form-control" placeholder="Email Address*"
                                            name="email" id="email" value="{{ old('email') }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" placeholder="State*" name="state"
                                            id="state" value="{{ old('state') }}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <input type="text" class="form-control" placeholder="City*" name="city"
                                            id="city" value="{{ old('city') }}">
                                    </div>

                                    <div class="col-12">
                                        <input type="text" class="form-control" placeholder="Address*" name="address"
                                            id="address" value="{{ old('addresss') }}">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-lg-auto">
                    <div class="booking-form ms-0 detailSearch">
                        <div class="price-container">
                            <div class="price-row py-2">
                                <div class="col-12 mt-3 mx-3 mb-3">
                                    <h3>Price Details</h3>
                                </div>

                                <div class="col-12 bg-white rounded px-0">
                                    <div
                                        class="price d-flex justify-content-between align-items-center py-3 px-3 form-row">
                                        <h6>No. of Nights (<span
                                                class="fw-bold">{{ formatIN($bookingQuotationProperty->per_night_price) }}
                                                X
                                                {{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights == 1 ? $bookingQuotationProperty->bookingQuotationDetail->no_of_nights . ' night' : $bookingQuotationProperty->bookingQuotationDetail->no_of_nights . ' nights' }}</span>)
                                        </h6>
                                        <span
                                            class="fw-bold">₹{{ formatIN($bookingQuotationProperty->basePrice) }}</span>
                                    </div>
                                    @if ($bookingQuotationProperty->extra_guest_charge)
                                        <div
                                            class="price d-flex justify-content-between align-items-center py-3 px-3 form-row">
                                            <h6>Extra Guest Charge</h6>
                                            <span
                                                class="additionalChargesAmount">₹{{ formatIN($bookingQuotationProperty->extra_guest_charge) }}</span>
                                        </div>
                                    @endif
                                    @if ($bookingQuotationProperty->discountAmount)
                                        <div
                                            class="price d-flex justify-content-between align-items-center py-3 px-3 form-row">
                                            <h6>Discount</h6>
                                            <span>-₹{{ formatIN($bookingQuotationProperty->discountAmount) }}</span>
                                        </div>
                                    @endif
                                    @php
                                        $charges = $bookingQuotationProperty->additional_charges_detail ?? [];
                                    @endphp
                                    @if (!empty($charges) && is_array($charges))
                                        @foreach ($charges as $charge)
                                            @php
                                                $amount =
                                                    $charge['type_option'] ?? '' === 'Per_Stay'
                                                        ? $charge['price'] ?? 0
                                                        : $charge['final_additional_charge'] ??
                                                            ($charge['final_price'] ?? 0);
                                            @endphp
                                            <div
                                                class="price d-flex justify-content-between align-items-center py-3 px-3 form-row">
                                                <h6>{{ $charge['name'] ?? 'Unknown Charge' }}</h6>
                                                <span class="additionalChargesAmount">₹{{ formatIN($amount) }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($bookingQuotationProperty->adOnsDiscountAmount)
                                        <div
                                            class="price d-flex justify-content-between align-items-center py-3 px-3 form-row">
                                            <h6>Add on Discount</h6>
                                            <span>-₹{{ formatIN($bookingQuotationProperty->adOnsDiscountAmount) }}</span>
                                        </div>
                                    @endif

                                    <div
                                        class="price d-flex justify-content-between align-items-center py-3 px-3 form-row">
                                        <h6>Taxes (<span
                                                class="tax">{{ $bookingQuotationProperty->gst ?? '' }}%</span>)</h6>
                                        <span>₹{{ formatIN($bookingQuotationProperty->gst_amount) }}</span>
                                    </div>
                                    <div
                                        class="price d-flex justify-content-between align-items-center py-3 px-3 final-price">
                                        <h6>Total incl. taxes</h6>
                                        <span>₹{{ formatIN($bookingQuotationProperty->payable_amount) }}</span>
                                    </div>
                                </div>
                                <div class="col-12 mt-3 px-2 py-2">
                                    <label for="aggrement">
                                        <div class="aggrement d-flex gap-2 align-items-center">
                                            <input type="radio" id="aggrement">
                                            <span>I accept and agree to the Terms and Conditions.</span>
                                        </div>
                                    </label>

                                    <button type="submit"
                                        class="btn py-3 fw-bold w-100 btn-primary rounded text-white mt-3 payNowcreate"
                                        disabled>
                                        Make Payment &nbsp;&nbsp;<span id="spinner_new_form"
                                            class="spinner-border spinner-border-sm " style="color: #fff; display: none;"
                                            role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($propertyDetail && $propertyDetail->homeReviews->isNotEmpty())
        <div class="container section-all-reviews" id="all-reviews" style="display:none;">
            <div class="row">
                <div class="col-12 heading-reviews">
                    <h3>Review</h3>
                </div>
                <div class="feedback-section mt-3">
                    <div class="row g-3">
                        @foreach ($propertyDetail->homeReviews as $reviews)
                            <div class="col-12">
                                <div class="row">
                                    <div class="review-card py-2 px-2">
                                        <div class="row">
                                            <div class="col-12 col-xl">
                                                <div class="col-12 col-xl-12 d-flex gap-3">
                                                    <div class="profile-img small-round-img">
                                                        {{ collect(explode(' ', $reviews->guest_name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('') }}
                                                    </div>
                                                    <div class="about-info d-flex flex-column">
                                                        <h6 class="text-dark mb-0">
                                                            {{ strtoupper($reviews->guest_name) ?? '' }}</h6>
                                                        <span
                                                            class="rating">{{ number_format($reviews->rating, 1) ?? '' }}
                                                            <i class="icon-star"></i></span>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-xl-12 mt-1">
                                                    <div class="feedback-content">
                                                        <p>{{ $reviews->comment ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-12 col-xl-3">
                                                <a href="#" class="gallery-card" data-gallery-type="all">
                                                    <img src="{{ asset('assets/website/images/hero1.webp') }}"
                                                        alt="" class="img-fluid rounded">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Fancybox.bind('[data-fancybox]', {});

        });
    </script>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            $('#aggrement').on('click', function() {
                let isChecked = false;
                isChecked = !isChecked;
                $(this).prop('checked', isChecked);
                $('.payNowcreate').prop('disabled', !isChecked);
            });

            Fancybox.bind("[data-custom-fancy]", {
                hideScrollbar: true,
                closeButton: false,
            })

            $('.payNowcreate').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var spinner = button.find('.spinner-border');
                spinner.show(); // Show the spinner
                button.prop('disabled', true);

                let formdata = $('#yourFormId').serialize();


                $.ajax({

                    url: "{{ route('property-booking-payment') }}",
                    type: "POST",
                    data: formdata,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function(response) {
                        if (response['status'] == true) {

                            spinner.hide();
                            button.prop('disabled', false);

                            if (response.orderId) {
                                let order_id = response.orderId;
                                let razorpay_id = response.razorpayId;
                                let amount = response.amount;
                                var options = {
                                    "key": razorpay_id,
                                    "amount": amount,
                                    "currency": "INR",
                                    "name": "Vendor Payment",
                                    "description": "enter text here",
                                    "order_id": order_id,
                                    "handler": function(response) {
                                        console.log(response.razorpay_order_id,
                                            "payment done");
                                        /* Payment Status */
                                        $.ajax({
                                            url: "{{ route('property-booking-update') }}",
                                            type: 'GET',
                                            data: {
                                                razorpay_order_id: response
                                                    .razorpay_order_id,
                                                razorpay_payment_id: response
                                                    .razorpay_payment_id
                                            },
                                            success: function(data) {
                                                window.location.replace(
                                                    "{{ route('payment.thankYou') }}?orderId=" +
                                                    data.orderId);
                                            }
                                        });
                                    },
                                    "prefill": {
                                        "name": "",
                                        "email": ""
                                    },
                                    "theme": {
                                        "color": "#3399cc"
                                    },
                                    "modal": {
                                        "ondismiss": function() {
                                            window,
                                            location.replace(
                                                "{{ route('payment-failure') }}");
                                        }
                                    }
                                };
                                var rzp = new Razorpay(options);
                                rzp.open();
                                rzp.on('payment.failed', function(response) {
                                    window,
                                    location.replace("{{ route('payment-failure') }}");
                                });
                            }
                        } else {
                            ;
                            if (response.status === false && response.redirect) {
                                window.location.href = response.redirect;
                            }
                            var errors = response['errors'];
                            if (errors) {
                                $.each(errors, function(key, value) {
                                    console.log(value[0], "value[0]");
                                    var elementId = key.replace(/\./g, '_');
                                    console.log(elementId, "key");
                                    var inputElement = $('#' + elementId);
                                    inputElement.addClass('border-danger');
                                    inputElement.next('p').addClass('text-danger').html(
                                        value[0]);
                                });
                            }
                            spinner.hide(); // Hide spinner after 3 seconds
                            button.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request failed:", error);
                    }
                });
            });


            $(document).ready(function() {
                $('#first_name').on('input', function() {
                    $('#first_name').removeClass('border-danger').html('');
                });
                $('#last_name').on('input', function() {
                    $('#last_name').removeClass('border-danger').html('');
                });
                $('#email').on('input', function() {
                    $('#email').removeClass('border-danger').html('');
                });
                $('#phone_number').on('input', function() {
                    $('#phone_number').removeClass('border-danger').html('');
                });
                $('#city').on('input', function() {
                    $('#city').removeClass('border-danger').html('');
                });
                $('#state').on('input', function() {
                    $('#state').removeClass('border-danger').html('');
                });
                $('#address').on('input', function() {
                    $('#address').removeClass('border-danger').html('');
                });

            });


            function toggleButtonState() {
                var phoneNumber = $('#phone_number').val();
                phoneNumber = phoneNumber.replace(/[^0-9]/g, '').slice(0, 12);
                if (phoneNumber[0] == '0') {
                    $('#phone_number').addClass('border-danger');
                    $('#phone_number').siblings('p').addClass('text-danger').html(
                        "Phone number can't start with 0.");

                } else {
                    $('#phone_number').siblings('p').addClass('text-danger').html("");
                    $('#phone_number').removeClass('border-danger').html('');
                }
                $('#phone_number').val(phoneNumber);
            }
            $('#phone_number').on('input', function() {
                toggleButtonState();
            });

        })
    </script>
@endsection