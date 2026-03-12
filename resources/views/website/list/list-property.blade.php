@extends('website.layouts.app')
@section('content')
<style>
    .captcha{
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
-o-user-select: none;
user-select: none;
}
</style>
    <section class="section listProperty-hero">
        <div class="container-fluid">
            <div class="row justify-content-between align-items-center gy-5">
                <div class="col-12 col-lg-6">
                    <h2>Become a part of our vibrant
                        community to transform your property into a destination of choice.<br>
                        <span>It’s time to redefine hospitality together !</span>
                    </h2>

                    <div class="row gy-3 mt-5">
                        <div class="col-12">
                            <div class="our-services d-flex align-items-center">
                                <div class="icon">
                                    <span class="icon-customer"></span>
                                </div>

                                <div class="content">
                                    <h4>Dedicated & Prompt Customer Support</h4>
                                    <p class="mb-0">To assist the owners, ensuring smooth partnership</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="our-services d-flex align-items-center">
                                <div class="icon">
                                    <span class="icon-price"></span>
                                </div>

                                <div class="content">
                                    <h4>All Inclusive Pricing</h4>
                                    <p class="mb-0">No surprises during check-out/payment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                 <form class="form-section px-3 py-3 rounded" id="listPropertyForm">
    <h3 class="text-dark py-3">Tell us more about your property</h3>
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <input type="text" name="first_name" id="first_name"
                class="form-control @error('first_name') is-invalid @enderror"
                placeholder="First Name*">
            <span class="invalid-feedback"></span>
        </div>
        <div class="col-12 col-md-6">
            <input type="text" name="last_name" id="last_name"
                class="form-control @error('last_name') is-invalid @enderror" placeholder="Last Name*">
            <span class="invalid-feedback"></span>
        </div>
        <div class="col-12 col-md-6">
            <input type="text" name="email" id="email"
                class="form-control @error('email') is-invalid @enderror" placeholder="Email Address*">
            <span class="invalid-feedback"></span>
        </div>
          <div class="col-12 col-md-6">
            <div class="row form-group d-flex gap-1">
                <div class="col-auto" style="padding-right:0;">
                 <select name="country_code" id="country_code" class="form-select code" style="width:100px;">
                      @foreach ($countryCode as $country)
                         <option value="{{ $country->phonecode }}"
                                {{ old('country_code', $guest['country_code'] ?? 91) == $country->phonecode ? 'selected' : '' }}>
                                {{ $country->iso }} (+{{ $country->phonecode }})
                          </option>
                      @endforeach
                    </select>
                  </div>
            <div class="col" style="padding-left:0;">
             <input type="text" name="phone_number" class="form-control" placeholder="Phone Number*">
                </div>
           </div>
        </div>
        <div class="col-12 col-md-6">
            <select class="form-select styled-select @error('location_type_id') is-invalid @enderror"
                name="location_type_id" id="location_type_id">
                <option value="" disabled selected hidden>Location*</option>
                @foreach ($locationTypes as $location)
                    <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback"></span>
        </div>
        <div class="col-12 col-md-6">
            <select class="form-select styled-select @error('property_type_id') is-invalid @enderror"
                name="property_type_id" id="property_type_id">
                <option value="" disabled selected hidden>Property Type*</option>
                @foreach ($propertyTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback"></span>
        </div>
        <div class="col-12">
            <textarea name="description" id="description" placeholder="Describe your property"
                class="form-control @error('description') is-invalid @enderror" rows="4"></textarea>
            <span class="invalid-feedback"></span>
        </div>
        <div class="col-5 col-sm">
            <input type="text" name="captcha" id="captcha"
                class="form-control @error('captcha') is-invalid @enderror" placeholder="Captcha*">
            <span class="invalid-feedback"></span>
        </div>
        <div class="col-5 col-sm-auto captcha">
            <span id="show_captcha" class="text-dark">{{ $captcha }}</span>
            <a href="#" id="refreshCaptcha">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                    viewBox="0 0 24 24" fill="#e41f26">
                    <path d=" M17.65 6.35A8 8 0 1 0 19 12h-2a6 6 0 1 1-1.76-4.24L12
                10h8V2l-2.35 2.35z" />
                </svg>
            </a>
        </div>
        <div class="col-2 col-sm-auto">
        </div>
        <div class="col-12">
            <div id="success-message" class="alert alert-success d-none" role="alert">
                Property information saved successfully.
            </div>
            <button type="submit" class="btn form-submit">Submit</button>
        </div>
    </div>
</form>
                </div>
            </div>
        </div>
    </section>


    <section class="section section-why">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-sm text-center">
                        <h2>India's Trusted <span class="text-primary">Luxury Stays</span></h2>
                    </div>
                </div>
            </div>
            <!--<div class="row align-items-center justify-content-center">-->
            <!--    <div class="col-12 col-lg order-2 order-lg-1">-->
            <!--        <div class="fl flp position-relative">-->
            <!--            <div class="whyBorder"></div>-->
            <!--            <div class="row">-->
            <!--                <div class="col-12">-->
            <!--                    <div class="iconBox">-->
            <!--                        <div class="icon">-->
            <!--                            <span class="icon-customer"></span>-->
            <!--                        </div>-->
            <!--                        <div class="content">-->
            <!--                            <h3>Great Customer Service</h3>-->
            <!--                            <p>Our teams are available 24/7 to promptly address customer complaints and ensure-->
            <!--                                swift resolution.</p>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--                <div class="col-12">-->
            <!--                    <div class="iconBox">-->
            <!--                        <div class="icon">-->
            <!--                            <span class="icon-managed"></span>-->
            <!--                        </div>-->
            <!--                        <div class="content">-->
            <!--                            <h3>Professionally Managed</h3>-->
            <!--                            <p>Properties managed by trained hospitality experts to deliver the best experience.-->
            <!--                            </p>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="col-auto order-1 order-lg-2">-->
            <!--        <div class="roundLogo">-->
            <!--            <img src="{{asset('assets/website/images/logo-shape.svg')}}" alt="">-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="col-12 col-lg order-3 order-lg-3">-->
            <!--        <div class="fr frp position-relative">-->
            <!--            <div class="whyBorder"></div>-->
            <!--            <div class="row">-->
            <!--                <div class="col-12">-->
            <!--                    <div class="iconBox">-->
            <!--                        <div class="icon">-->
            <!--                            <span class="icon-privacy"></span>-->
            <!--                        </div>-->
            <!--                        <div class="content">-->
            <!--                            <h3>Privacy & Flexibility</h3>-->
            <!--                            <p>Homelike stays offering ultimate privacy, flexibility, safety, and comfort.</p>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--                <div class="col-12">-->
            <!--                    <div class="iconBox">-->
            <!--                        <div class="icon">-->
            <!--                            <span class="icon-features"></span>-->
            <!--                        </div>-->
            <!--                        <div class="content">-->
            <!--                            <h3>Best Features</h3>-->
            <!--                            <p>Enjoy 24/7 caretakers, housekeeping, free Wi-Fi, kitchen, swimming pool,-->
            <!--                                home-cooked meals, and more.</p>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            @foreach ( $services as $service)
            <div class="row align-items-center justify-content-center">
                <div class="col-12 col-lg order-2 order-lg-1">
                    <div class="fl flp position-relative">
                        <div class="whyBorder"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                         <img id="propertiesIconPreview"
                                            src="{{ $service->customer_service_icon ? asset('storage/' . $service->customer_service_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$service->customer_service_title}}</h3>
                                      {!! $service->customer_service_short_description !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                      <img id="propertiesIconPreview"
                                            src="{{ $service->privacy_flexibility_icon ? asset('storage/' . $service->privacy_flexibility_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$service->privacy_flexibility_title}}</h3>
                                        {!! $service->privacy_flexibility_short_description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto order-1 order-lg-2">
                    <div class="roundLogo">
                         <img src="{{ asset('storage/' . $service->image) }}" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="col-12 col-lg order-3 order-lg-3">
                    <div class="fr frp position-relative">
                        <div class="whyBorder"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                       <img id="propertiesIconPreview"
                                            src="{{ $service->professionally_managed_icon ? asset('storage/' . $service->professionally_managed_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$service->professionally_managed_title}}</h3>
                                        {!! $service->professionally_managed_description!!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                       <img id="propertiesIconPreview"
                                            src="{{ $service->best_feature_icon ? asset('storage/' . $service->best_feature_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$service->best_feature_title}}</h3>
                                           {!! $service->best_feature_description !!}
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <section class="section section-faq">
        <div class="container-fluid">
            <div class="row">
                <h2>FAQ's</h2>
                <div class="col-12">
                    <div class="accordion mt-3 mb-5">
                        @foreach ($categories as $category)
                            @if ($category->faqs->isNotEmpty())
                                <!-- Category Title -->
                                <div class="faq-category-title mt-5">
                                    <h3><b>{{ $category->title }}</b></h3>
                                </div>
                                <!-- FAQs for this category -->
                                @foreach ($category->faqs as $faq)
                                    <div class="accordion-item">
                                        <div class="accordion-header" onclick="toggleAccordion(this)" role="button"
                                            aria-expanded="false">
                                            <h3>{{ $faq->question }}</h3>
                                            <span class="icon-chevron-down arrow"></span>
                                        </div>
                                        <div class="accordion-content" aria-hidden="true">
                                            {!! $faq->answer !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                        @if ($categories->isEmpty() || $categories->pluck('faqs')->flatten()->isEmpty())
                            <p>No FAQs available.</p>
                        @endif
                    </div>

                    <div class="faq-btn my-4">
                        <a href="{{ route('faq') }}" class="btn">Show more <span
                                class="icon-arrow-up-right1"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
    function refreshCaptcha() {
        return $.ajax({
            url: "{{ route('refresh.captcha') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                $("#show_captcha").text(data.captcha);
            },
            error: function() {
                console.log("Captcha refresh error");
            }
        });
    }

    $("#refreshCaptcha").click(function(e) {
        e.preventDefault();
        refreshCaptcha();
    });

    $('#listPropertyForm').on('submit', function(e) {
        e.preventDefault();
        $('#success-message').addClass('d-none');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Disable submit button and show spinner
        const $submitBtn = $('.form-submit');
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');

        $.ajax({
            url: '{{ route('list.property.submit') }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Show success message
                $('#success-message').removeClass('d-none');
                $('#listPropertyForm')[0].reset();

                // Refresh captcha
                refreshCaptcha().done(function() {
                    // Scroll to success message
                    $('html, body').animate({
                        scrollTop: $('#success-message').offset().top - 100
                    }, 500);

                    // Hide success message after 5 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 5000);
                });

                // Reset button state
                $submitBtn.prop('disabled', false);
                $submitBtn.html('Submit');
            },
            error: function(xhr) {
                // Reset button state
                $submitBtn.prop('disabled', false);
                $submitBtn.html('Submit');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let firstErrorField = null;
                    
                    // Refresh captcha
                    refreshCaptcha().done(function() {
                        $.each(errors, function(key, messages) {
                            const input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            if (!firstErrorField) {
                                firstErrorField = input;
                            }
                        });

                        if (firstErrorField) {
                            $('html, body').animate({
                                scrollTop: firstErrorField.offset().top - 100
                            }, 500);
                        }
                    });
                } else {
                    alert('An unexpected error occurred. Please try again.');
                }
            }
        });
    });
});
   
    </script>

    <script>
        function toggleAccordion(header) {
            const allContents = document.querySelectorAll('.accordion-content');
            const allArrows = document.querySelectorAll('.arrow');

            allContents.forEach((content, index) => {
                const arrow = allArrows[index];
                if (content !== header.nextElementSibling) {
                    content.style.maxHeight = null;
                    content.style.padding = '0 0px';
                    arrow.classList.remove('open');
                }
            });


            const content = header.nextElementSibling;
            const arrow = header.querySelector('.arrow');

            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                content.style.padding = '0 0px';
                arrow.classList.remove('open');
            } else {
                content.style.maxHeight = (content.scrollHeight + 25) + "px";
                content.style.padding = '8px 0px';
                arrow.classList.add('open');
            }
        }
    </script>
@endsection