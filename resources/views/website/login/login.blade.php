@extends('website.layouts.app')
@section('content')
<style>
    .btn:disabled, .btn.disabled, fieldset:disabled .btn {
        background-color: #3BB6B1!important;
    }
</style>
<div class="cms-pages">
    <section class="section section-signin pt-5">
        <div class="container">
            <div class="form-wrap">
                <div class="card-info form-box form-inner-wrap form-box-login">
                    <form method="post" id="userlogin">
                        @csrf()
                        <div class="form-title">
                            <h1 class="h3">Login to Your Account</h1>
                            <p>It's good to see you again!</p>
                        </div>
                        {{-- <div class="btn-wrap">
                            <a href="{{ route('google.redirect') }}{{ request()->has('uri') ? '?uri=' . urlencode('booking-confirmation/'.request('uri')) : '' }}" class="btn btn-light w-100"><svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2 mt-0">
                                    <path d="M12.7148 10.0909V14.1573H18.4811C18.2279 15.465 17.4681 16.5723 16.3285 17.3169L19.8058 19.961C21.8318 18.1283 23.0006 15.4365 23.0006 12.2388C23.0006 11.4942 22.9325 10.7783 22.8058 10.0911L12.7148 10.0909Z" fill="#4285F4"></path>
                                    <path d="M6.70875 13.9985L5.92449 14.5869L3.14844 16.7059C4.91144 20.1327 8.52485 22.5 12.7132 22.5C15.606 22.5 18.0313 21.5645 19.8041 19.9609L16.3268 17.3168C15.3722 17.9468 14.1547 18.3287 12.7132 18.3287C9.92745 18.3287 7.56061 16.4864 6.71313 14.0046L6.70875 13.9985Z" fill="#34A853"></path>
                                    <path d="M3.14928 7.29431C2.41879 8.70698 2 10.3011 2 12.0001C2 13.6992 2.41879 15.2933 3.14928 16.706C3.14928 16.7155 6.71432 13.9951 6.71432 13.9951C6.50003 13.3651 6.37337 12.6969 6.37337 12C6.37337 11.3031 6.50003 10.635 6.71432 10.005L3.14928 7.29431Z" fill="#FBBC05"></path>
                                    <path d="M12.7134 5.68089C14.2914 5.68089 15.694 6.21542 16.8141 7.24634L19.8823 4.23957C18.0219 2.54052 15.6063 1.5 12.7134 1.5C8.52507 1.5 4.91144 3.85772 3.14844 7.29408L6.71337 10.005C7.56073 7.52315 9.92767 5.68089 12.7134 5.68089Z" fill="#EA4335"></path>
                                </svg> Continue with Google</a>
                        </div>

                        <div class="or">
                            <span>or continue traditional way</span>
                        </div> --}}

                        <div class="row align-items-center g-3">
                            {{-- @php $uri = Request::segment(1) @endphp --}}
                            {{-- @if(!empty($uri))
                                <input type="text" class="form-control" name="uri" id="uri" value="{{$uri}}">
                            @else --}}
                            <input type="hidden" class="form-control" name="uri" id="uri" value="">
                            <input type="hidden" class="form-control" name="booking_quptation" id="booking_quptation" value="{{ request()->query('type') }}">
                            {{-- @endif --}}
                            <div class="col-12">
                                <label for="">Email<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="email">
                                <div class="error text-danger" id="email_error"></div>
                            </div>
                            <div class="col-12">
                                <label for="">Password<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password" class="form-control" name="password">
                                    <button type="button" class="input-group-text fs-5" onclick="togglePasswordVisibility()" id="togglePasswordBtn">
                                        <i class="bi bi-eye lh-1"></i>
                                    </button>
                                </div>
                                <div class="error text-danger" id="password_error"></div>
                                <div class="text-end pt-3">
                                    <!--<a href="{{ route('forgotpassword') }}@if(!empty(request()->get('uri')))?uri={{ urlencode(request()->get('uri')) }}@endif" class="text-secondary text-decoration-none small">Forgot your password?</a>-->


                                    <a href="{{ route('forgotpassword') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="text-secondary text-decoration-none small">Forgot your password?</a>



                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Login</button>
                            </div>
                            <div class="col-12">
                                <!--<a class="btn btn-light w-100" href="{{ route('customer.signup') }}@if(!empty(request()->get('uri')))?uri={{ urlencode(request()->get('uri')) }}@endif">Create an account</a>-->

                                <a class="btn btn-light w-100" href="{{ route('customer.signup') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}
                                ">Create an account</a>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>


<script>
    $(document).on('submit', '#userlogin', function(ev) {
        $('.error').html('');

        ev.preventDefault(); // Prevent browers default submit.
        var formData = new FormData(this);
        var $btn = $(this).find('button[type="submit"]');
        var originalBtnText = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm me-2 text-white" role="status" aria-hidden="true"></span> Logging in...').prop('disabled', true);
        var error = false;

        if (error == false) {
            $.ajax({
                url: "{{ url('userlogin') }} ",
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $(".hstackloader").html('<lord-icon src="https://cdn.lordicon.com/dpinvufc.json" trigger="loop" colors="primary:#4bb543,secondary:#4bb543" style="width:50px;"> </lord-icon>');
                    $(".hstack").css('display', 'none');
                    $(".error").text('');
                },
                // success: function(result) { 
                //     if (result.code == 200) {
                //         swal(result.message, ' ', 'success');
                //         setTimeout(function() {
                //             if(result.url != ''){
                //                 window.location.href = '/booking-confirmation/'+result.url
                //             }else{
                //                 window.location.href = '/';
                //             }
                //         }, 2000);
                //     } else if (result.code == 401) {
                //         $.each(result.message, function(prefix, val) {
                //             $('#' + prefix + '_error').text(val[0]);
                //         });
                //         // swal(result.message, ' ', 'error');
                //     } else {
                //         swal(result.message, ' ', 'error');
                //     }
                // },
                success: function(result) {
                    if (result.code == 200) {
                        swal(result.message, ' ', 'success');
                        setTimeout(function() {
                            let params = new URLSearchParams(window.location.search);
                            let redirectUrl = params.get('redirect');
                            if (redirectUrl) {
                                window.location.href = decodeURIComponent(redirectUrl);
                                return;
                            }
                            let booking_quptation = $("#booking_quptation").val();


                            if (result.url != '' || booking_quptation) {
                                if (booking_quptation) {
                                    window.location.href = '/property-book/' + booking_quptation
                                } else {
                                    // let ptype = params.get("ptype") || "";
                                    // let adults = params.get("adults") || "1";
                                    // let children = params.get("children") || "0";
                                    // let pets = params.get("pets") || "0";
                                    // let checkin = params.get("checkin") || "";
                                    // let checkout = params.get("checkout") || "";
                                    // let total_guests = params.get("total_guests") || "";
                                    // let tot_no_of_days = params.get("tot_no_of_days") || "";
                                    // let slug = params.get("slug") || "";
                                    // let per_pet_charge = params.get("per_pet_charge") || "";
                                    // let total_pet_charge = params.get("total_pet_charge") || "";
                                    
                                    // let newParams = new URLSearchParams();
                                    // newParams.set("ptype", ptype);
                                    // newParams.set("adults", adults);
                                    // newParams.set("children", children);
                                    // newParams.set("pets", pets);
                                    // newParams.set("checkin", checkin);
                                    // newParams.set("checkout", checkout);
                                    // newParams.set("total_guests", total_guests);
                                    // newParams.set("tot_no_of_days", tot_no_of_days);
                                    // newParams.set("slug", slug);
                                    // newParams.set("per_pet_charge", per_pet_charge);
                                    // newParams.set("total_pet_charge", total_pet_charge);
                                    // window.location.href = `/property-book?${newParams.toString()}`;

                                    let query = window.location.search;
                                    if (query) {
                                        window.location.href =  '/property-book' + (query ? query : '');
                                    } else {
                                        window.location.href = '/property-book';
                                    }
                                }
                            } else {
                                window.location.href = '/';
                            }


                            // if(result.url != '' || booking_quptation){
                            //   if(booking_quptation){
                            //         window.location.href = '/booking-quotation/' + booking_quptation
                            //       }
                            //       else{
                            //         window.location.href = '/booking-confirmation/'+result.url
                            //       }
                            // }else{
                            //     window.location.href = '/';
                            // }


                        }, 2000);
                    } else if (result.code == 401) {
                        $.each(result.message, function(prefix, val) {
                            $('#' + prefix + '_error').text(val[0]);
                        });
                        // swal(result.message, ' ', 'error');
                    } else {
                        swal(result.message, ' ', 'error');
                    }
                },
                error: function(xhr) {
                    $(".hstack").css('display', 'flex');
                },
                complete: function() {
                    $btn.html(originalBtnText).prop('disabled', false);
                    $(".hstack").css('display', 'flex');
                    $(".hstackloader").text('');
                },
            })
        }
    })
</script>

<script>
    // Check if the URL has a 'uri' parameter and populate the hidden input field
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const uri = urlParams.get('slug');
        if (uri) {
            document.getElementById('uri').value = uri;
        }
    });

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePasswordBtn');
        const icon = toggleButton.querySelector('i');

        if (passwordInput) {
            const inputType = passwordInput.getAttribute('type');
            const isPassword = inputType === 'password';

            // Toggle input type
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            // Toggle icon class
            if (isPassword) {
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        } else {
            console.error('Password input field not found');
        }
    }
</script>
@endsection