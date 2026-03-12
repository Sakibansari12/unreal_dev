@extends('pms.layouts.app')
@section('content')
    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col-auto">
                    </div>
                </div>
            </div>
            <form method="post" action="{{ route('pms.changepasswordsubmit.submit') }}">
                @csrf
                <div class="content-box p-3">
                    <div class="form-box">
                        <div class="row">
                            <!-- New Password -->
                            <div class="col-6">
                                <div class="form-field position-relative">
                                    <label for="password">New Password<sup>*</sup></label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        value="{{ old('password') }}">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <span class="material-symbols-outlined toggle-password"
                                        style="position: absolute; top: 35px; right: 10px; cursor: pointer;"
                                        onclick="togglePassword('password', this)">
                                        visibility
                                    </span>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-6">
                                <div class="form-field position-relative">
                                    <label for="password_confirmation">Confirm Password<sup>*</sup></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        value="{{ old('password_confirmation') }}">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <span class="material-symbols-outlined toggle-password"
                                        style="position: absolute; top: 35px; right: 10px; cursor: pointer;"
                                        onclick="togglePassword('password_confirmation', this)">
                                        visibility
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <script>
        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "visibility_off";
            } else {
                input.type = "password";
                icon.textContent = "visibility";
            }
        }
    </script>
@endsection
