@extends('website.layouts.app')
@section('content')

<div class="cms-pages">
    <section class="section section-account fade section-bg" style="opacity: 1;">
        <div class="container">
            <div class="mb-5">
                <h3 class="h2 mb-0">Hello, {{ $user->name }}</h3>
                <p>Welcome to your account page. you can view all your personal data here.</p>
            </div>

            <div class="row g-5">
                <div class="col-12 col-lg-auto">
                    <div class="list-group">
                        <a href="/my-account" class="list-group-item">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path
                                    d="M224 0a128 128 0 1 1 0 256A128 128 0 1 1 224 0zM178.3 304l91.4 0c11.8 0 23.4 1.2 34.5 3.3c-2.1 18.5 7.4 35.6 21.8 44.8c-16.6 10.6-26.7 31.6-20 53.3c4 12.9 9.4 25.5 16.4 37.6s15.2 23.1 24.4 33c15.7 16.9 39.6 18.4 57.2 8.7l0 .9c0 9.2 2.7 18.5 7.9 26.3L29.7 512C13.3 512 0 498.7 0 482.3C0 383.8 79.8 304 178.3 304zM436 218.2c0-7 4.5-13.3 11.3-14.8c10.5-2.4 21.5-3.7 32.7-3.7s22.2 1.3 32.7 3.7c6.8 1.5 11.3 7.8 11.3 14.8l0 30.6c7.9 3.4 15.4 7.7 22.3 12.8l24.9-14.3c6.1-3.5 13.7-2.7 18.5 2.4c7.6 8.1 14.3 17.2 20.1 27.2s10.3 20.4 13.5 31c2.1 6.7-1.1 13.7-7.2 17.2l-25 14.4c.4 4 .7 8.1 .7 12.3s-.2 8.2-.7 12.3l25 14.4c6.1 3.5 9.2 10.5 7.2 17.2c-3.3 10.6-7.8 21-13.5 31s-12.5 19.1-20.1 27.2c-4.8 5.1-12.5 5.9-18.5 2.4l-24.9-14.3c-6.9 5.1-14.3 9.4-22.3 12.8l0 30.6c0 7-4.5 13.3-11.3 14.8c-10.5 2.4-21.5 3.7-32.7 3.7s-22.2-1.3-32.7-3.7c-6.8-1.5-11.3-7.8-11.3-14.8l0-30.5c-8-3.4-15.6-7.7-22.5-12.9l-24.7 14.3c-6.1 3.5-13.7 2.7-18.5-2.4c-7.6-8.1-14.3-17.2-20.1-27.2s-10.3-20.4-13.5-31c-2.1-6.7 1.1-13.7 7.2-17.2l24.8-14.3c-.4-4.1-.7-8.2-.7-12.4s.2-8.3 .7-12.4L343.8 325c-6.1-3.5-9.2-10.5-7.2-17.2c3.3-10.6 7.7-21 13.5-31s12.5-19.1 20.1-27.2c4.8-5.1 12.4-5.9 18.5-2.4l24.8 14.3c6.9-5.1 14.5-9.4 22.5-12.9l0-30.5zm92.1 133.5a48.1 48.1 0 1 0 -96.1 0 48.1 48.1 0 1 0 96.1 0z" />
                            </svg>
                            <span>My Account</span>
                        </a>
                        <a href="/my-bookings" class="list-group-item list-group-item-action">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">>
                                <path
                                    d="M36.8 192l412.8 0c20.2-19.8 47.9-32 78.4-32c30.5 0 58.1 12.2 78.3 31.9c18.9-1.6 33.7-17.4 33.7-36.7c0-7.3-2.2-14.4-6.2-20.4L558.2 21.4C549.3 8 534.4 0 518.3 0L121.7 0c-16 0-31 8-39.9 21.4L6.2 134.7c-4 6.1-6.2 13.2-6.2 20.4C0 175.5 16.5 192 36.8 192zM384 224l-64 0 0 160-192 0 0-160-64 0 0 160 0 80c0 26.5 21.5 48 48 48l224 0c26.5 0 48-21.5 48-48l0-80 0-32 0-128zm144 16c17.7 0 32 14.3 32 32l0 48-64 0 0-48c0-17.7 14.3-32 32-32zm-80 32l0 48c-17.7 0-32 14.3-32 32l0 128c0 17.7 14.3 32 32 32l160 0c17.7 0 32-14.3 32-32l0-128c0-17.7-14.3-32-32-32l0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80z" />
                            </svg>
                            <span>My Bookings</span>
                        </a>
                        <a href="/change-password" class="list-group-item list-group-item-action active"><svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                <path
                                    d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z" />
                            </svg> <span>Change Password</span></a>
                    </div>
                </div>
                <div class="col">
                    <div class="account-box" style="max-width: 600px;">
                        <form method="post" id="changepassword">
                            @csrf()
                            {{-- <div class="form-group mb-3"> 
                                <input type="text" class="form-control" placeholder="Old Password"> 
                            </div> --}}
                            <div class="form-group mb-3"> 
                                <!--<input type="password" class="form-control" placeholder="New Password" name="new_password" oninput="validatenewPasswordLength(this)"> -->
                                <!--<div class="error text-danger mb-3" id="new_password_error"></div>-->
                                <div class="input-group">
                                    <input type="password" id="password" class="form-control" placeholder="New Password" name="new_password" oninput="validatenewPasswordLength(this)"> 
                                    <button type="button" class="input-group-text fs-5" onclick="togglePasswordVisibility()" id="togglePasswordBtn">
                                        <i class="bi bi-eye lh-1"></i>
                                    </button>
                                </div>
                                <div class="error text-danger mb-3" id="new_password_error"></div>
                            </div>
                            <div class="form-group mb-3"> 
                                <!--<input type="password" class="form-control" placeholder="Confirm New Password" name="confirm_password" oninput="validateconfirmPasswordLength(this)"> -->
                                <!--<div class="error text-danger mb-3" id="confirm_password_error"></div>-->
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Confirm New Password" id="confirm_password" name="confirm_password" oninput="validateconfirmPasswordLength(this)"> 
                                    <button type="button" class="input-group-text fs-5" onclick="toggleCPasswordVisibility()" id="toggleCPasswordBtn">
                                        <i class="bi bi-eye lh-1"></i>
                                    </button>
                               
                                </div>
                                
                                <div class="error text-danger mb-3" id="confirm_password_error"></div>
                            </div>                                                
                          
                            <div class="form-group"> 
                                <button class="btn btn-primary " type="submit"> Change </button> 
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
</div>


<script>
    $(document).on('submit', '#changepassword', function(ev) {
        $('.error').html('');

        ev.preventDefault(); // Prevent browers default submit.
        var formData = new FormData(this);
        var error = false;

        if (error == false) {
            $.ajax({
                url: "{{ url('submitpassword') }} ",
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
                success: function(result) {
                    if (result.code == 200) {
                        swal({
                            title: result.message,
                            text: "",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "OK",
                            closeOnConfirm: false, 
                        }, function() {
                            window.location.href = '/';
                        });
                        // swal(result.message, ' ', 'success');
                        // setTimeout(function() {
                        //     window.location.href = '/';
                        // }, 2000);
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
                    $(".hstack").css('display', 'flex');
                    $(".hstackloader").text('');
                },
            })
        }
    })
</script>

<script>
    function validatenewPasswordLength(input) {
        if (input.value.length < 8) {
            $('#new_password_error').text('Password must be at least 8 characters.');
        } else {
            $('#new_password_error').text('');
        }
    }

    function validateconfirmPasswordLength(input) {
        if (input.value.length < 8) {
            $('#confirm_password_error').text('Password must be at least 8 characters.');
        } else {
            $('#confirm_password_error').text('');
        }
    }
</script>

<script>
    // Check if the URL has a 'uri' parameter and populate the hidden input field
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const uri = urlParams.get('uri');
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

    function toggleCPasswordVisibility() {
        const passwordInput = document.getElementById('confirm_password');
        const toggleButton = document.getElementById('toggleCPasswordBtn');
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
