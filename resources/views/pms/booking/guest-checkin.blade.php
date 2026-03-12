@extends('pms.layouts.app')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
<style>
    .form-field {
        margin-bottom: 1rem;
    }

    .form-field label {
        font-weight: 500;
    }

    .form-control.border-danger {
        border-color: #dc3545 !important;
    }

    .small-action-btn .btn-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .multi-row {
        border: 1px solid #dee2e6;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }

    .btn-save {
        padding: 0.5rem 1.5rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
    }

    .upload-wrapper {
        border: 2px dashed #C7C7C7;
        border-radius: 5px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 15px;
        user-select: none;
        background-color: #fff;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
    }

    .upload-wrapper input[type=file] {
        position: absolute;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .upload-wrapper.is-invalid {
        border-color: #dc3545;
    }

    .upload-info {
        z-index: 1;
        pointer-events: none;
    }

    .imagePreview {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .imagePreview img {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        object-fit: contain;
        transform: translate(-50%, -50%);
    }

    .remove-preview {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 18px;
        line-height: 30px;
        text-align: center;
        cursor: pointer;
        z-index: 3;
    }

    .border-success {
        border: 2px solid green !important;
    }

    .border-danger {
        border: 2px solid red !important;
    }

    .otp {
        background-color: #C79F62;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
    }

    .verify {
        background-color: green;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 8px 6px;
    }

    /* addd */
    .form-field {
        margin-bottom: 1.5rem;
    }

    .form-field label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .input-group {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        /* Reduced gap for tighter layout */
    }

    .country-code-select {
        max-width: 120px;
        flex: 0 0 120px;
    }

    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
        height: 38px;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* .country-code-select {
                        width: 30% !important; /* Further reduced width */
    /* border-radius: 0.375rem 0 0 0.375rem;
                        background-color: #f8f9fa;
                        border-right: none; */
    /* font-size: 0.875rem; Smaller font for compact look */
    /* padding: 0.5rem; Adjusted padding */
    /* } */
    /* .mobile-input {
                        width: 40%
                        flex: 1;
                        border-radius: 0;
                    }  */
    .otp-icon {
        background-color: #cda972;
        color: white;
        padding: 0.5rem;
        border-radius: 0 0.375rem 0.375rem 0;
        cursor: pointer;
        transition: background-color 0.3s;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
    }

    .otp-icon:hover {
        background-color: #0056b3;
    }

    .text-danger {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .border-danger {
        border-color: #dc3545 !important;
    }

    @media (max-width: 576px) {
        .country-code-select {
            width: 50px !important;
            /* Even smaller width on mobile */
            font-size: 0.8rem;
        }

        .otp-icon {
            width: 34px;
            padding: 0.4rem;
        }

        .input-group {
            gap: 0.2rem;
        }
    }

    .country-code-select {
        width: 20% !important;
    }

    .mobile-input {
        width: 40% !important
    }

    .otp {
        padding: 8px 6px;
    }

    .upload-wrapper.has-preview input[type=file] {
        pointer-events: none;
    }
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Upload Guest ID</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.booking.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>
        <input type="hidden" id="booking_id" value="{{ $bookingDetail->id }}">
        <div class="content-box">
            <div class="form-box p-3">
                @if ($isLoading)
                <div class="d-flex justify-content-center py-5">
                    <div class="spinner-border" role="status"></div>
                </div>
                @else
                <form action="{{ route('property.booking.ids.save') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="property_booking_id" id="property_booking_id"
                        value="{{ $bookingDetail->id }}">
                    <input type="hidden" name="checkin_date" value="{{ $bookingDetail->checkin_date }}">
                    <input type="hidden" name="checkout_date" value="{{ $bookingDetail->checkout_date }}">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-field">
                                <h6 class="mb-0 fw-bold">Booking ID: {{ $bookingDetail->booking_id }}</h6>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row nth-row g-4" id="guest-rows">
                                @foreach ($guests as $index => $guest)
                                <div class="col-12 col-lg-6">
                                    <div class="multi-row">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-field">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="small-action-btn">
                                                                <div class="btn btn-icon btn-dark pe-none">
                                                                    {{ $index + 1 }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row gx-3">
                                                <div class="col-lg-6">
                                                    <div class="form-field">
                                                        <label for="name_{{ $index }}">Name @if ($index == 0)
                                                            <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text"
                                                            name="guest_list[{{ $index }}][name]"
                                                            id="name_{{ $index }}"
                                                            class="form-control @error('guest_list.' . $index . '.name') border-danger @enderror"
                                                            value="{{ old('guest_list.' . $index . '.name', $guest['name']) }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-field">
                                                        <label for="email_{{ $index }}">Email
                                                            @if ($index == 0)
                                                            <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="email"
                                                            name="guest_list[{{ $index }}][email]"
                                                            id="email_{{ $index }}"
                                                            class="form-control @error('guest_list.' . $index . '.email') border-danger @enderror"
                                                            value="{{ old('guest_list.' . $index . '.email', $guest['email']) }}">
                                                    </div>
                                                </div>
                                                <input type="hidden"
                                                    name="booking_guest_id_{{ $index }}"
                                                    id="booking_guest_id_{{ $index }}"
                                                    value="{{ $guest['id'] }}">
                                                <div class="col-lg-7">
                                                    <div class="form-field">
                                                        <label for="mobile_no_{{ $index }}">Mobile No
                                                            @if ($index == 0)
                                                            <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <div class="input-group">
                                                            <select id="country_code_{{ $index }}"
                                                                name="guest_list[{{ $index }}][country_code]"
                                                                class="form-control w-25 country-code-select">
                                                                @foreach ($countryCode as $country)
                                                                @php
                                                                $selectedCountryCode = old(
                                                                'guest_list.' . $index . '.country_code',
                                                                ($index === 0 && !empty($guest['country_code']))
                                                                ? $guest['country_code']
                                                                : 91
                                                                );
                                                                @endphp

                                                                <option value="{{ $country->phonecode }}"
                                                                    {{ (string)$selectedCountryCode === (string)$country->phonecode ? 'selected' : '' }}>
                                                                    {{ $country->iso }} (+{{ $country->phonecode }})
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="text"
                                                                name="guest_list[{{ $index }}][mobile_no]"
                                                                id="mobile_no_{{ $index }}"
                                                                class="form-control mobile-input w-30 @error('guest_list.' . $index . '.mobile_no') border-danger @enderror"
                                                                value="{{ $guest['mobile_no'] }}"
                                                                placeholder="Enter mobile number">
                                                        </div>
                                                        <div id="otp_error_{{ $index }}"
                                                            class="text-danger" style="display: none;"></div>
                                                        @error('guest_list.' . $index . '.mobile_no')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-field">
                                                        <label
                                                            for="anniversary_{{ $index }}">Anniversary
                                                            @if ($index == 0)
                                                            <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text"
                                                            name="guest_list[{{ $index }}][anniversary]"
                                                            id="anniversary_{{ $index }}"
                                                            class="form-control flatpickr @error('guest_list.' . $index . '.anniversary') border-danger @enderror"
                                                            value="{{ old('guest_list.' . $index . '.anniversary', $guest['anniversary']) }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-field">
                                                        <label for="dob_{{ $index }}">DOB @if ($index == 0)
                                                            <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text"
                                                            name="guest_list[{{ $index }}][dob]"
                                                            id="dob_{{ $index }}"
                                                            class="form-control flatpickr @error('guest_list.' . $index . '.dob') border-danger @enderror"
                                                            value="{{ old('guest_list.' . $index . '.dob', $guest['dob']) }}">
                                                    </div>
                                                </div>
                                                <input type="hidden"
                                                    name="guest_list[{{ $index }}][guest_id]"
                                                    value="{{ $guest['id'] }}">
                                                <!-- <input type="hidden"
                                                    name="guest_list[{{ $index }}][remove_image]"
                                                    id="removeImageInput_{{ $index }}"
                                                    value="0"> -->
                                                <!-- <input type="hidden"
                                                    name="guest_list[{{ $index }}][remove_front_image]"
                                                    id="removeImageFrontInput_{{ $index }}"
                                                    value="0">
                                                <input type="hidden"
                                                    name="guest_list[{{ $index }}][remove_back_image]"
                                                    id="removeImageBackInput_{{ $index }}"
                                                    value="0"> -->
                                            </div>
                                            <div class="col-12">
                                                <div class="form-field">
                                                    <label>ID Proof <span class="text-danger">*</span></label>

                                                    <div class="upload-wrapper" id="uploadWrapper_{{ $index }}">
                                                        @if(!empty($guest['id_proof_img']))
                                                        <script>
                                                            document.addEventListener('DOMContentLoaded', function() {
                                                                const wrapper = document.getElementById('uploadWrapper_{{ $index }}');
                                                                const preview = document.getElementById('imagePreview_{{ $index }}');
                                                                const info = document.getElementById('uploadInfo_{{ $index }}');
                                                                const removeBtn = document.getElementById('removePreview_{{ $index }}');

                                                                preview.classList.remove('d-none');
                                                                info.style.display = 'none';
                                                                removeBtn.classList.remove('d-none');

                                                                wrapper.classList.add('has-preview'); // ✅ KEY LINE

                                                                @if(Str::endsWith($guest['id_proof_img'], '.pdf'))
                                                                preview.innerHTML = `
                                                                        <div style="text-align:center">
                                                                            <i class="fa fa-file-pdf fa-3x text-danger"></i>
                                                                            <p class="mt-2">{{ $guest['id_proof_img'] }}</p>
                                                                            <a href="{{ asset('storage/guest_idproof/'.$guest['id_proof_img']) }}"
                                                                            target="_blank"
                                                                            class="btn btn-sm btn-outline-primary mt-2">
                                                                            View PDF
                                                                            </a>
                                                                        </div>
                                                                    `;
                                                                @else
                                                                preview.innerHTML = `
                                                                    <img src="{{ asset('storage/guest_idproof/'.$guest['id_proof_img']) }}"
                                                                        alt="ID Proof">
                                                                `;
                                                                @endif
                                                            });
                                                        </script>
                                                        @endif
                                                        <input
                                                            type="file"
                                                            name="guest_list[{{ $index }}][id_proof_img]"
                                                            id="id_proof_img_{{ $index }}"
                                                            accept="image/*,.pdf"
                                                            onchange="previewFile(event, {{ $index }})">

                                                        <div class="upload-info" id="uploadInfo_{{ $index }}">
                                                            <i class="fa fa-upload mb-2"></i><br>
                                                            <strong>Drag & Drop</strong> Or <strong>Browse</strong> Your File<br>
                                                            <small>Allowed: JPG, PNG, WEBP, PDF · Max 5MB</small>
                                                        </div>

                                                        <div class="imagePreview d-none" id="imagePreview_{{ $index }}"></div>

                                                        <button type="button"
                                                            class="remove-preview d-none"
                                                            id="removePreview_{{ $index }}"
                                                            onclick="removeFile({{ $index }})">
                                                            ×
                                                        </button>
                                                    </div>

                                                    <input type="hidden"
                                                        name="guest_list[{{ $index }}][remove_image]"
                                                        id="removeImageInput_{{ $index }}"
                                                        value="0">
                                                </div>


                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row pt-4">
                        <div class="col-12">
                            <div class="form-field mb-0">
                                <button type="submit" class="btn btn-save btn-primary px-5"
                                    @if ($isLoading) disabled @endif
                                    id="submit-button">
                                    @if ($isLoading)
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                    @else
                                    SUBMIT
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('.flatpickr', {
            dateFormat: "Y-m-d",
            maxDate: 'today',
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            disableMobile: true
        });
s
        @foreach ($guests as $index => $guest)
            // Only call toggleAadharFields if id_type select exists
            if (document.getElementById('id_type_' + {{ $index }})) {
                toggleAadharFields({{ $index }});
            }
        @endforeach
    });
    

    function previewFile(event, index) {
        const input = event.target;
        const preview = document.getElementById('imagePreview_' + index);
        const info = document.getElementById('uploadInfo_' + index);
        const removeBtn = document.getElementById('removePreview_' + index);
        const removeInput = document.getElementById('removeImageInput_' + index);

        preview.innerHTML = '';

        if (!input.files || !input.files[0]) return;

        const file = input.files[0];

        // 5MB size check
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            input.value = '';
            return;
        }

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            preview.innerHTML = `
                <div style="text-align:center">
                    <i class="fa fa-file-pdf fa-3x text-danger"></i>
                    <p class="mt-2">${file.name}</p>
                </div>
            `;
        } else {
            alert('Only images or PDF allowed');
            input.value = '';
            return;
        }

        preview.classList.remove('d-none');
        info.style.display = 'none';
        removeBtn.classList.remove('d-none');
        removeInput.value = '0';
    }

    function removeFile(index) {
        const wrapper = document.getElementById('uploadWrapper_' + index);

        document.getElementById('id_proof_img_' + index).value = '';
        document.getElementById('imagePreview_' + index).innerHTML = '';
        document.getElementById('imagePreview_' + index).classList.add('d-none');
        document.getElementById('uploadInfo_' + index).style.display = 'block';
        document.getElementById('removePreview_' + index).classList.add('d-none');
        document.getElementById('removeImageInput_' + index).value = '1';

        wrapper.classList.remove('has-preview'); // ✅ re-enable upload
    }
</script>

{{-- send otp ajax --}}
<!-- <script>
    function sendOtp(index) {
        const mobileNo = $(`#mobile_no_${index}`).val();
        const country_code = $(`#country_code_${index}`).val();
        const booking_guest_id = $(`#booking_guest_id_${index}`).val();
        const errorSpan = $(`#otp_error_${index}`);
        const sendBtn = $(`#send_otp_${index}`);
        const bookingId = $('#booking_id').val();
        if (!mobileNo) {
            return;
        }
        sendBtn.prop('disabled', true).text('Sending...');
        $.ajax({
            url: "{{ route('send.guest.otp') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                mobile_no: mobileNo,
                country_code: country_code,
                booking_guest_id: booking_guest_id,
                booking_id: bookingId
            },
            success: function(response) {
                sendBtn.prop('disabled', false).text('Send OTP');
                if (response.status) {
                    errorSpan.hide();
                    alert(response.message);
                } else {
                    errorSpan.show().text(response.message);
                }
            },
            error: function(xhr) {
                sendBtn.prop('disabled', false).text('Send OTP');
                let message = xhr.responseJSON?.message || 'Error occurred while sending OTP.';
                errorSpan.show().text(message);
            }
        });
    }
</script> -->

{{-- verify otp ajax --}}
<script>
    // function verifyOtp(index) {
    //     const mobileNo = $(`#mobile_no_${index}`).val();
    //     const country_code = $(`#country_code_${index}`).val();
    //     const booking_guest_id = $(`#booking_guest_id_${index}`).val();
    //     const property_booking_id = $('#property_booking_id').val()
    //     const otpInput = $(`#otp_${index}`);
    //     const otp = otpInput.val();
    //     const errorSpan = $(`#otp_error_${index}`);
    //     const bookingId = $('#booking_id').val();
    //     if (!otp) {
    //         otpInput.removeClass('border-success').addClass('border-danger');
    //         return;
    //     }
    //     $.ajax({
    //         url: "{{ route('guest.verify.otp') }}",
    //         method: "POST",
    //         data: {
    //             _token: $('meta[name="csrf-token"]').attr('content'),
    //             mobile_no: mobileNo,
    //             country_code: country_code,
    //             booking_guest_id: booking_guest_id,
    //             otp: otp,
    //             booking_id: bookingId
    //         },
    //         success: function(response) {
    //             if (response.status) {
    //                 otpInput.removeClass('border-danger').addClass('border-success');
    //                 errorSpan.removeClass('text-danger').addClass('text-success')
    //                 // .text('OTP verified successfully ✅').show();
    //                 setTimeout(() => {
    //                     otpInput.val('').removeClass('border-success');
    //                     errorSpan.fadeOut();
    //                 }, 2000);
    //             } else {
    //                 errorSpan.text(response.message).removeClass('text-success').addClass('text-danger')
    //                     .show();
    //                 otpInput.removeClass('border-success').addClass('border-danger');
    //             }
    //         },
    //         error: function(xhr) {
    //             const message = xhr.responseJSON?.message || '';
    //             errorSpan.text(message).removeClass('text-success').addClass('text-danger').show();
    //             otpInput.removeClass('border-success').addClass('border-danger');
    //         }
    //     });
    // }
</script>
<script>
    // Assuming jQuery is available
    $(document).ready(function() {
        // Listen for form submission
        $('form').on('submit', function() {
            // Disable the button and show the loading spinner
            $('#submit-button').prop('disabled', true);
            $('#submit-button').html('<div class="spinner-border spinner-border-sm" role="status"></div>');

            // You can optionally show a loading indicator on the page as well.
        });
    });
</script>
@endsection