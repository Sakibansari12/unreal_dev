@extends('website.layouts.landing.app')
@section('content')
<style>
    .section.section-hero .section-bg:before {
        display: none;
    }

    .form-wrap {
        background: rgba(var(--bs-white-rgb), .65);
    }

    .section-testimonials .swiper-slide {
        height: auto;
        padding: 15px 5px;
    }

    .testimonialBox {
        border: 1px solid #eeeeee;
        padding: 15px;
        border-radius: 15px;
        display: flex;
        height: 100%;
        width: 100%;
        align-items: center;
        box-shadow: 0px 5px 8px rgba(0, 0, 0, 0.15);
    }

    .testimonialBox .imgBox {
        width: 80px;
        height: 80px;
        border: 1px solid #cccccc;
        postion: relative;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .testimonialBox .imgBox img {
        width: 70px
    }

    .testimonialBox h6 {
        text-transform: uppercase;
        margin: 0;
        color: #e56d00;
    }

    .testimonialBox small {}

    .card-HighLights {}

    .content ul li:first-child {
        border-top: 1px solid #cae6e4 !important;
        padding-top: 10px !important;
    }

    .content ul li {
        padding-bottom: 0px !important;
    }

    .amenities-list li {
        width: calc(25% - var(--gap)) !important;
        display: block !important;
        text-align: center;
    }

    .amenities-small-icon {
        border: 0px;
    }

    .amenities-small-icon img {
        max-width: 60px;
    }

    .section-amenities .swiper-slide {
        text-align: center;
        height: auto;
    }

    @media (max-width:1300px) {
        .amenities-list li {
            width: calc(25% - var(--gap)) !important;
        }

        .amenities-list li span {
            font-size: 14px !important;
        }
    }

    @media (max-width:991px) {
        .card-HighLights {
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 10px 15px;
            width: 100%;
            border-radius: 8px;
        }
    }

    @media (max-width:767px) {
        .amenities-list li {
            width: calc(33.333333% - var(--gap)) !important;
        }

        .amenities-small-icon img {
            max-width: 48px;
        }
    }

    @media (max-width:575px) {
        .amenities-list li {
            width: calc(50% - var(--gap)) !important;
        }

        .amenities-small-icon img {
            max-width: 36px;
        }

        .section.section-hero {
            padding-top: 50px !important;
        }
    }

    .section.section-hero .section-bg .thumbnails {
        left: 0 !important;
        right: auto !important
    }
</style>
<style>
    .counter {
        display: flex;
        align-items: center;
        gap: 20px;
        border: 1px solid #ccc;
        /*padding: 5px;*/
        border-radius: 5px;
        width: auto;
        justify-content: center;
        background: #fff;
        cursor: pointer;

    }

    .counter button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        font-size: 18px;
        border-radius: 3px;
    }

    .counter button:hover {
        background-color: #0056b3;
    }

    .counter input {
        width: 60px;
        text-align: center;
        font-size: 16px;
        border: none;
        outline: none;
        background: transparent;
    }
</style>
    <section class="section flex-wrap section-hero section-bg d-flex align-items-center">
        <div class="section-bg">
            <div class="swiper hero-banner-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="{{ asset('storage/landing/' . $detail->image ?? '') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="container hero-content-container position-relative">
            <div class="row align-items-center justify-content-center">
                <div class="col-6">
                    <div class="hero-card ">
                        <h2>{{ $detail->title ?? '' }}</h2>
                        <p>{!! $detail->description ?? '' !!}</p>

                        <a href="tel:+918080816490" class="text-decoration-none btn btn-primary rounded text-white"><i
                                class="icon-villa me-2"></i>Reserve Your Room</a>
                    </div>
                </div>

                <div class="col-6 col-lg-5">
                    <div class="hero-card form-wrap p-4 rounded-5">
                        <h3 class="text-black mb-3">Make an Enquiry</h3>
                        <form method="POST" id="ajaxForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <input class="form-control @error('name') is-invalid @enderror" type="text"
                                        placeholder="Name*" @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" />
                                </div>
                                <div class="col-12">
                                    <input class="form-control @error('phone') is-invalid @enderror" type="number"
                                        placeholder="Mobile*" name="phone" value="{{ old('phone') }}" />
                                </div>
                                <div class="col-12">
                                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                                        placeholder="Email Address*" name="email" value="{{ old('email') }}" />
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="text-black">Check-in*</label>
                                    <input class="form-control  @error('check-in') is-invalid @enderror " type="date"
                                        name="check-in" id="check-in" min="{{ date('Y-m-d') }}"
                                        value="{{ old('check-in') }}" />

                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="text-black">Check-out*</label>
                                    <input class="form-control  @error('check-out') is-invalid @enderror " type="date"
                                        name="check-out" id="check-out" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        value="{{ old('check-out') }}" />
                                </div>
                                <div class="col-6">
                                    <label class="text-black">No of people*</label>
                                    <div class="counter">
                                        <a onclick="decrease()">-</a>
                                        <input class="form-control  @error('no-of-people') is-invalid @enderror "
                                            type="number" id="no-of-people" name="no-of-people" value="0"
                                            step="1" readonly>
                                        <a onclick="increase()">+</a>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="text-black">Budget(Per Night)*</label>
                                    <select class="form-select form-control @error('budget') is-invalid @enderror"
                                        name="budget" id="budget">
                                        <option value="">Select</option>
                                        <option value="5,000-10,000 per night">5,000-10,000 per night</option>
                                        <option value="10,000-20,000 per night">10,000-20,000 per night</option>
                                        <option value="20,000-30,000 per night">20,000-30,000 per night</option>
                                        <option value="30,000-40,000 per night">30,000-40,000 per night</option>
                                        <option value="Above 40,000 per night">Above 40,000 per night</option>
                                    </select>
                                </div>

                                <div class="col-md-12">

                                    <div class="form-group form-captcha">
                                        <div class="row gx-3 mb-4 align-items-center">
                                            <div class="col col-lg">
                                                <input type="text" name="captcha" id="captcha"
                                                    class="form-control {{ $errors->has('captcha') ? 'is-invalid' : '' }}"
                                                    placeholder="Captcha*" value="{{ old('captcha') }}">
                                            </div>
                                            <div class="col-auto">
                                                <div class="row g-0 align-items-center">
                                                    <div class="col">
                                                        <div class="captcha-box bg-primary p-2" id="captcha_div"
                                                            style="width:80px; border-radius:4px; text-align:center">
                                                            <span class="captcha-text" id="captcha_text"
                                                                style="user-select: none;">{{ old('captcha_text') }}</span>
                                                            <input type="hidden" name="captcha_hidden"
                                                                value="{{ old('captcha_hidden') }}" id="captcha_hidden"
                                                                class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="button" class="refreshBtn btn"
                                                            id="captcha_reload">
                                                            <svg style="width: 16px;" xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 512 512">
                                                                <path
                                                                    d="M142.9 142.9c-17.5 17.5-30.1 38-37.8 59.8c-5.9 16.7-24.2 25.4-40.8 19.5s-25.4-24.2-19.5-40.8C55.6 150.7 73.2 122 97.6 97.6c87.2-87.2 228.3-87.5 315.8-1L455 55c6.9-6.9 17.2-8.9 26.2-5.2s14.8 12.5 14.8 22.2l0 128c0 13.3-10.7 24-24 24l-8.4 0c0 0 0 0 0 0L344 224c-9.7 0-18.5-5.8-22.2-14.8s-1.7-19.3 5.2-26.2l41.1-41.1c-62.6-61.5-163.1-61.2-225.3 1zM16 312c0-13.3 10.7-24 24-24l7.6 0 .7 0L168 288c9.7 0 18.5 5.8 22.2 14.8s1.7 19.3-5.2 26.2l-41.1 41.1c62.6 61.5 163.1 61.2 225.3-1c17.5-17.5 30.1-38 37.8-59.8c5.9-16.7 24.2-25.4 40.8-19.5s25.4 24.2 19.5 40.8c-10.8 30.6-28.4 59.3-52.9 83.8c-87.2 87.2-228.3 87.5-315.8 1L57 457c-6.9 6.9-17.2 8.9-26.2 5.2S16 449.7 16 440l0-119.6 0-.7 0-7.6z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-12">
                                            <button class="btn btn-dark">SUBMIT</button>
                                        </div> --}}
                                        <button class="btn py-3 fw-bold w-100 btn-primary cbooking rounded" id="submitBtn" type="submit">
                                            <span class="btn-text">SUBMIT</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>

                                    </div>
                                </div>


                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div>
    </section>

    @if (!empty($detail->keyword))
        <section class="section pb-0">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2>About Us</h2>
                        {!! $detail->keyword ?? '' !!}
                    </div>
                </div>
            </div>
        </section>
    @endif
    @if ($combinedPropertyData->isNotEmpty())
        <section class="section property-section">
            <div class="container-fluid">
                <div class="section-heading">
                    <div class="row gy-3">
                        <div class="col-12 col-sm text-center">
                            <h2>Featured Properties</h2>
                        </div>
                    </div>
                </div>
                <div class="properties-wrapper">
                    <div class="row g-4">

                        @foreach ($combinedPropertyData as $property)
                            @php $priceAndAvaliability = getPriceAndAvalibility($property->ru_property_id, $property->pType);   @endphp
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}"
                                    target="_blank" class="card card-properties">
                                    <div class="card-img-top">
                                        <div class="swiper-outer">
                                            <div class="swiper swiper-property-image">
                                                <div class="swiper-wrapper">
                                                    @if ($property->imagesWebsite->where('type', 'image')->isNotEmpty())
                                                        @foreach ($property->imagesWebsite->where('type', 'image') as $image)
                                                            <div class="swiper-slide">
                                                                <div class="imgBox">
                                                                    <img loading="lazy"
                                                                        src="{{ asset($image->website_image) }}"
                                                                        alt="">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="swiper-slide">
                                                            <div class="imgBox">
                                                                <img loading="lazy"
                                                                    src="{{ asset('assets/website/images/no-image.png') }}"
                                                                    alt="">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="swiper-button-prev"></div>
                                                <div class="swiper-button-next"></div>
                                                <div class="swiper-pagination"></div>
                                            </div>
                                        </div>
                                        @php
                                            $averageRating = $property->homeReviews->avg('rating');
                                        @endphp

                                        @if ($averageRating)
                                            @php
                                                $roundedRating = floor((float) $averageRating);
                                            @endphp

                                            <div class="rating">
                                                {{ number_format($averageRating, 1) }}
                                                <i class="icon-star text-primary"></i>
                                            </div>
                                        @endif
                                        <div class="overlayInfo">
                                            Upto
                                            {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }}
                                            +
                                            {{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Room' : $property->no_of_bedrooms . ' Rooms' }}
                                            +
                                            {{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-title h4 mb-0">{{ $property->unit_name_website ?? '' }}</div>
                                            <div class="card-text">{{ $property->locationData->location_name ?? '' }},
                                                {{ $property->state }}</div>
                                            <div class="price">from
                                                &#8377;{{ number_format($priceAndAvaliability['per_night_price']) }} per
                                                night</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                    </div>
                    {{-- <div class="row">
                    <div class="col-12 text-center pt-4">
                        <a href="#/" class="btn btn-plus"><div class="icon"><span class="icon-plus"></span></div> View all properties</a>
                    </div>
                </div> --}}
                </div>
            </div>
        </section>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const heroSwiper = new Swiper('.hero-banner-swiper', {
                loop: true,
                slidesPerView: 1,
                effect: "fade",
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
            })

            new Swiper(".swiper-property-image", {
                spaceBetween: 30,
                // allowTouchMove: false,
                pagination: {
                    el: ".swiper-property-image .swiper-pagination",
                    dynamicBullets: true,
                    clickable: true
                },
                navigation: {
                    nextEl: ".swiper-property-image .swiper-button-next",
                    prevEl: ".swiper-property-image .swiper-button-prev",
                },
                mousewheel: {
                    enabled: true,
                    forceToAxis: true
                },
            });

            $('.back-to-top').on('click', function() {
                $('html, body').stop().animate({
                    scrollTop: 0
                }, 600)
            })
        });
    </script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function increase() {
            let input = document.getElementById("no-of-people");
            let step = parseInt(input.step) || 1;
            let max = 12;

            if (parseInt(input.value) + step <= max) {
                input.value = parseInt(input.value) + step;
            }
        }

        function decrease() {
            let input = document.getElementById("no-of-people");
            let step = parseInt(input.step) || 1;
            let min = 0;

            if (parseInt(input.value) - step >= min) {
                input.value = parseInt(input.value) - step;
            }
        }
    </script>
    <script>
        function generateCaptcha() {
            const digits = Math.floor(Math.random() * 900) + 100;
            let characters = '';
            const alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            for (let i = 0; i < 3; i++) {
                characters += alphanumeric.charAt(Math.floor(Math.random() * alphanumeric.length));
            }
            return digits + characters;
        }

        function updateCaptcha() {
            const newCaptcha = generateCaptcha();
            $('#captcha_text').text(newCaptcha);
            $('#captcha_hidden').val(newCaptcha);
        }

        $(document).ready(function() {
            updateCaptcha();

            $("#captcha_reload").on("click", function() {
                updateCaptcha();
            });

            /* $("#ajaxForm").on("submit", function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = form.serialize();

                $(".is-invalid").removeClass("is-invalid");

                $.ajax({
                    url: "{{ route('landingenquire') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success == true) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                form[0].reset();
                                updateCaptcha();
                                fbq('track', 'Lead');
                                gtag('event', 'conversion', {
                                    'send_to': 'AW-16482594363/oLBgCMjMqpcaELvcwbM9'
                                });

                            });
                        } else {
                            updateCaptcha();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(field) {
                                $('[name="' + field + '"]').addClass("is-invalid");
                            });
                        }
                        updateCaptcha();
                    }
                }); */

                $("#ajaxForm").on("submit", function(e) {
    e.preventDefault();

    let form = $(this);
    let formData = form.serialize();

    // Disable submit button and show loader
    let submitBtn = $("#submitBtn");
    submitBtn.prop("disabled", true);
    submitBtn.find(".btn-text").addClass("d-none");
    submitBtn.find(".spinner-border").removeClass("d-none");

    $(".is-invalid").removeClass("is-invalid");

    $.ajax({
        url: "{{ route('landingenquire') }}",
        type: "POST",
        data: formData,
        dataType: "json",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').val()
        },
        success: function(response) {
            // Enable button & reset UI
            submitBtn.prop("disabled", false);
            submitBtn.find(".btn-text").removeClass("d-none");
            submitBtn.find(".spinner-border").addClass("d-none");

            if (response.success == true) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                }).then(() => {
                    form[0].reset();
                    updateCaptcha();

                    fbq('track', 'Lead');
                    gtag('event', 'conversion', {
                        'send_to': 'AW-16482594363/oLBgCMjMqpcaELvcwbM9'
                    });
                });
            } else {
                updateCaptcha();
            }
        },
            error: function(xhr) {
                // Enable button & reset UI
                submitBtn.prop("disabled", false);
                submitBtn.find(".btn-text").removeClass("d-none");
                submitBtn.find(".spinner-border").addClass("d-none");

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(field) {
                        $('[name="' + field + '"]').addClass("is-invalid");
                    });
                }
                updateCaptcha();
            }
        });


            });

            $("input, textarea").on("blur", function() {
                if ($(this).hasClass("is-invalid")) {
                    updateCaptcha();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            function generateCaptcha() {
                const digits = Math.floor(Math.random() * 900) + 100;
                let characters = '';
                const alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                for (let i = 0; i < 3; i++) {
                    characters += alphanumeric.charAt(Math.floor(Math.random() * alphanumeric.length));
                }
                return digits + characters;
            }

            function updateCaptcha() {
                const newCaptcha = generateCaptcha();
                $('#captcha_text').text(newCaptcha);
                $('#captcha_hidden').val(newCaptcha);
            }

            updateCaptcha();

            $("#captcha_reload").on("click", function(e) {
                e.preventDefault();
                updateCaptcha();
            });

            $("input, textarea").on("blur", function() {
                if ($(this).hasClass("is-invalid")) {
                    $(this).removeClass("is-invalid");
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".card-body").on("click", function(event) {
                event.preventDefault(); // Prevents navigation if it's a link
                $("html, body").animate({
                    scrollTop: 0
                }, "slow");
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let checkIn = document.getElementById("check-in");
            let checkOut = document.getElementById("check-out");

            checkIn.addEventListener("change", function() {
                let checkInDate = new Date(checkIn.value);
                if (!isNaN(checkInDate.getTime())) {
                    checkInDate.setDate(checkInDate.getDate() + 1); // Add 1 day
                    let nextDay = checkInDate.toISOString().split('T')[0]; // Format to YYYY-MM-DD
                    checkOut.min = nextDay;

                    // Agar check-out ka value purane check-in se chhota ho, to update kar do
                    if (checkOut.value < nextDay) {
                        checkOut.value = nextDay;
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.scroll-to-enquiry');

            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const enquiryForm = document.getElementById('enquiryFormSection');
                    enquiryForm.scrollIntoView({
                        behavior: 'smooth'
                    }); // Smooth scroll
                });
            });
        });
    </script>
@endsection