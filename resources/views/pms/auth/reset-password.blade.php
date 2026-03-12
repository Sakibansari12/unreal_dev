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
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container-fluid px-0 login-container">
        <div class="login-wrap flex-grow-1">
            <div class="login-logo">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="">
            </div>
            <div class="row g-0">
                <div class="col d-none d-lg-block">
                    <div class="login-img">
                        <img class="w-100 h-100" src="{{ asset('assets/pms/images/login.webp') }}" alt="">
                    </div>
                </div>
                <div class="col-lg-5 d-flex align-items-center justify-content-center">
                    <div class="login-box flex-grow-0">
                        <h2>Reset Password</h2>



                        <form id="resetPasswordForm">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">
                            <div class="form-field">
                                <label for="">New Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" required
                                        class="form-control form-control-lg">
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="material-icons">visibility_off</i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-field">
                                <label style="font-style: italic; " class="text-danger">Your password must be more than 6 characters
                                    long.</label>
                            </div>


                            <div class="form-field">
                                <label for="">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                        class="form-control form-control-lg">
                                    <span class="input-group-text" id="toggleConfirmPassword">
                                        <i class="material-icons">visibility_off</i>
                                    </span>
                                </div>
                            </div>
                            <div id="response-message"></div>
                            <div class="submit-wrap">
                                <button type="submit" class="btn w-100 btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/pms/js/app.js') }}"></script>
    <script>
        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.textContent = input.type === 'password' ? 'visibility_off' : 'visibility';
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const input = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.textContent = input.type === 'password' ? 'visibility_off' : 'visibility';
        });

        // AJAX Password Reset
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const responseBox = document.getElementById('response-message');

            fetch("{{ route('password.update') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    responseBox.innerHTML = '';

                    if (data.success) {
                        responseBox.innerHTML =
                            `<div class="text-success text-center mb-2 mt-2" style="font-size: 16px;">${data.success}</div>`;
                        setTimeout(() => {
                            window.location.href = '/pms/login';
                        }, 2000);
                    } else if (data.errors) {
                        Object.values(data.errors).forEach(msgArr => {
                            msgArr.forEach(msg => {
                                responseBox.innerHTML +=
                                    `<div class="text-danger text-center mb-1" style="font-size: 14px;">${msg}</div>`;
                            });
                        });
                    }
                }).catch(err => {
                    responseBox.innerHTML =
                        `<div class="text-danger text-center mb-2 mt-2">Something went wrong.</div>`;
                    console.error(err);
                });
        });
        $(document).ready(function() {
            $('#password').on('input', function() {
                $('#password').removeClass('border-danger').html('');
                $('#response-message').removeClass('text-danger').html('');
            });
            $('#password_confirmation').on('input', function() {
                $('#password_confirmation').removeClass('border-danger').html('');
                $('#response-message').removeClass('text-danger').html('');
            });
        });
    </script>
</body>

</html>