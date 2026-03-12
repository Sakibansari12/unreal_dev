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
                                    <p class="mb-0">To assist the owners, ensuring smooth partnership    <br>
                                       Call: +91 9999999999 <br>
                                       Email: support@unrealestate.in</p>
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
    <h3 class="text-dark py-3">Contact Us</h3>
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
        <div class="col-12">
            <textarea name="message" id="message" placeholder="Message*"
                class="form-control @error('message') is-invalid @enderror" rows="4"></textarea>
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
               Thank you! Your details have been submitted successfully. Our team will get in touch with you shortly.
            </div>
            <button type="submit" class="btn form-submit">Submit</button>
        </div>
    </div>
</form>
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
            url: '{{ route('contactus.submit') }}',
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