<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">
    <title>Unreal Estate PMS</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/pms/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/pms/css/app.css') }}">
</head>
<style>
   .spinner-border-sm-custom {
    width: 16px;
    height: 16px;
    border: 2px solid #fff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Don't use .d-none — custom name */
.my-hidden {
    display: none !important;
}

</style>
<body class="">
    <div class="container-fluid px-0 login-container">
        <div class="login-wrap flex-grow-1">
            <div class="login-logo">
                <img src="assets/images/logo.svg" alt="">
            </div>
            <div class="row g-0">
                <div class="col d-none d-lg-block">
                    <div class="login-img">
                        <img class="w-100 h-100" src="{{ asset('assets/pms/images/login.webp') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-center">
                    <div class="login-box flex-grow-0">
                        <h2>Forgot Password</h2>
                        @error('email')
                            <h5 class="text-danger text-center mb-2" style="font-size: 16px;">{{ $message }} !</h5>
                            @endif
                            <!-- resources/views/pms/auth/forgot-password.blade.php -->
                            <form action="{{ route('pms.forgot.password.send') }}" method="POST">
                                @csrf
                                <div class="form-field">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="form-control">
                                </div>
                                <!--<div class="submit-wrap">-->
                                <!--    <button type="submit" class="btn w-100 btn-primary">Send Reset Link</button>-->
                                <!--</div>-->
                                
                                <div class="submit-wrap">
                                    <button type="submit" class="btn w-100 btn-primary position-relative" id="submitBtn"
                                        style="height: 45px;">
                                        <span id="btnText">Send Reset Link</span>
                                        <span id="btnLoader" class="spinner-border-sm-custom d-none"
                                            style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%);"></span>
                                    </button>
                                </div>
                            </form>

                            @if (session('success'))
                                <p class="text-success text-center">{{ session('success') }}</p>
                            @endif

                           {{--  @if ($errors->has('email'))
                                <p class="text-danger text-center">{{ $errors->first('email') }}</p>
                            @endif --}}

                            <div class="text-center mt-3">
                                <a href="{{ route('pms.form') }}">Back to Login</a>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/pms/js/app.js') }}"></script>
        <!-- Add this script at the bottom of your page or in a custom JS file -->
        <script>
            // Toggle new password
            document.getElementById('togglePassword').addEventListener('click', function() {
                var passwordField = document.getElementById('password');
                var icon = this.querySelector('i');

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    icon.textContent = "visibility";
                } else {
                    passwordField.type = "password";
                    icon.textContent = "visibility_off";
                }
            });

            // Toggle confirm password
            document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
                var confirmPasswordField = document.getElementById('confirm_password');
                var icon = this.querySelector('i');

                if (confirmPasswordField.type === "password") {
                    confirmPasswordField.type = "text";
                    icon.textContent = "visibility";
                } else {
                    confirmPasswordField.type = "password";
                    icon.textContent = "visibility_off";
                }
            });
        </script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('form');
                const submitBtn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnLoader = document.getElementById('btnLoader');
        
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    btnText.textContent = 'Please wait...';
                    btnLoader.classList.remove('my-hidden');
                });
            });
        </script>
    </body>

    </html>