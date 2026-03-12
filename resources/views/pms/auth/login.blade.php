<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0">
    <title>Unreal Estate PMS</title>
    <link rel="shortcut icon" type="image/png" href="{{asset('assets/pms/favicon.png')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('assets/pms/css/app.css')}}">
</head>
<body class="">                    
    <div class="container-fluid px-0 login-container">
        <div class="login-wrap flex-grow-1">
            <div class="login-logo">
                <img src="assets/images/logo.svg" alt="">
            </div>
            <div class="row g-0">
                <div class="col d-none d-lg-block">
                    <div class="login-img">
                        <img class="w-100 h-100" src="{{asset('assets/pms/images/login.webp')}}" alt="">
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-center">
                    <div class="login-box flex-grow-0">
                        <h2>Login</h2>

                        @error('email')
                            <h5 class="text-danger text-center mb-2" style="font-size: 16px;">{{ $message }} !</h5>
                        @endif 
 

                        <form action="{{route('pms.login')}}" method="POST">
                            @csrf
                            <div class="form-field">
                                <label for="">Username</label>
                                <input type="text" name="email" value="{{old('email')}}"  class="form-control  form-control-lg @error('email') is-invalid @enderror" >
                            </div>
                            <div class="form-field">
                                <label for="">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror">
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="material-icons">visibility_off</i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-field">
                                <!--<div class="form-check">-->
                                <!--    <input class="form-check-input" type="checkbox" value="" id="remember">-->
                                <!--    <label class="form-check-label" for="remember">-->
                                <!--        Remember me-->
                                <!--    </label>-->
                                <!--</div>-->
                                
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <input class="form-check-input" type="checkbox" value="" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <a href="{{ route('password.request') }}" style="text-decoration:none;">
                                            Forgot your password?
                                        </a>
                                    </div>
                                </div>
                                
                                
                            </div>
                            <div class="submit-wrap">
                                <button type="submit" class="btn w-100 btn-primary">LOGIN</button>
                            </div>
                        </form>
                        @if (session('success'))
                            <h5 class="text-danger text-center mb-2 mt-2" style="font-size: 16px;">
                                {{ session('success') }}
                            </h5>
                        @endif

                        @if ($errors->has('session_expired'))
                            <h5 class="text-danger text-center mb-2 mt-2" style="font-size: 16px;">                               
                                {{ $errors->first('session_expired') }}
                            </h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('assets/pms/js/app.js')}}"></script>
    <!-- Add this script at the bottom of your page or in a custom JS file -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            var passwordField = document.getElementById('password');
            var icon = this.querySelector('i');
            
            // Toggle password visibility
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.textContent = "visibility";  // Show password icon
            } else {
                passwordField.type = "password";
                icon.textContent = "visibility_off";  // Hide password icon
            }
        });
    </script>
</body>
</html>