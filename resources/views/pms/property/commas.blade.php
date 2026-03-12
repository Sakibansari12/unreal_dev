@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $id?'Update':'Add' }} {{ ucfirst(request()->pType) }} (Property: {{ $parentHome->unit_name }})</h1>
                </div>
                <div class="col-auto">

                    @php
                    $route = request()->type === 'published'
                    ? route('pms.published.property.list', ['pType' => 'unit'])
                    : route('pms.property.unit.or.multiunit.list', [
                    'property_id' => request()->property_id,
                    'pType' => request()->pType,
                    ]);
                    @endphp

                    <a href="{{ $route }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
            <div class="row g-5">
                <style>
                    .ulTab {
                        list-style-type: none;
                        margin: 0;
                        padding: 0;
                        overflow-x: auto
                    }

                    @media (max-width: 991.98px) {
                        .ulTab {
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex
                        }
                    }

                    .ulTab li {
                        margin: 10px 5px
                    }

                    .ulTab li button,
                    .ulTab li a {
                        border: 0px;
                        padding: 10px 15px;
                        background-color: #fff;
                        width: 100%;
                        border-radius: 6px !important;
                        text-align: left;
                        border: 1px solid #0E0E0E;
                        display: block;
                        text-decoration: none
                    }

                    .ulTab li button.active,
                    .ulTab li a.active {
                        border: 0px;
                        padding: 10px 15px;
                        color: #fff;
                        background-color: #0e0e0e
                    }

                    .ulTab li button[disabled],
                    .ulTab li a[disabled] {
                        opacity: 1;
                        color: #000
                    }

                    @media (max-width: 991.98px) {

                        .ulTab li button,
                        .ulTab li a {
                            white-space: nowrap
                        }
                    }

                    .ulTab {
                        display: block !important;
                    }

                    .multi-row .row.mb-2 {
                        margin-bottom: 0.5rem !important;
                    }

                    .button-group {
                        display: flex;
                        gap: 0.5rem;
                        align-items: center;
                    }

                    .is-invalid {
                        border-color: #dc3545 !important;
                    }

                    .invalid-feedback {
                        color: #dc3545;
                        font-size: 0.875rem;
                    }
                </style>
                @include('pms.property.unit-or-multiunit-menu-segments')
                {{-- comms section --}}
                @php
                $ccEmails = old('ccEmails', $comms->cc_emails ?? []);
                $phones = old('phones', $comms->phones ?? []);
                @endphp

                <div class="col-12 col-lg-9">
                    <form method="POST" action="{{ route('pms.property.unit.or.multiunit.commas.save') }}" id="property-form">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-field">
                                    <label>Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $comms->name ?? '') }}">
                                    <span class="invalid-feedback" id="name-error"></span>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Email<span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $comms->email ?? '') }}">
                                    <span class="invalid-feedback" id="email-error"></span>
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Phone<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $comms->phone ?? '') }}">
                                    <span class="invalid-feedback" id="phone-error"></span>
                                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>CC Email</label>
                                    <div class="multi-row p-3" id="cc-email-wrapper">
                                        @if (!empty($ccEmails))
                                        @foreach ($ccEmails as $index => $ccEmail)
                                        <div class="row gx-3 align-items-center mb-2 cc-email-row">
                                            <div class="col">
                                                <input
                                                    type="email"
                                                    class="form-control cc-email-input"
                                                    name="ccEmails[]"
                                                    value="{{ $ccEmail }}"
                                                    data-index="{{ $index }}">
                                                @error('ccEmails.' . $index)
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-auto" style="width:120px;">
                                                <div class="button-group">
                                                    @if ($loop->count > 1)
                                                    <button type="button" class="btn btn-small btn-danger text-white remove-cc">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                    @endif
                                                    @if ($loop->last)
                                                    <button
                                                        type="button"
                                                        class="btn btn-small btn-success text-white add-cc"
                                                        disabled>
                                                        <span class="material-symbols-outlined">add</span>
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else
                                        <div class="row gx-3 align-items-center mb-2 cc-email-row">
                                            <div class="col">
                                                <input
                                                    type="email"
                                                    class="form-control cc-email-input"
                                                    name="ccEmails[]"
                                                    value=""
                                                    data-index="0"
                                                    placeholder="Enter Email">
                                            </div>
                                            <div class="col-auto" style="width:120px;">
                                                <div class="button-group">
                                                    <button
                                                        type="button"
                                                        class="btn btn-small btn-success text-white add-cc"
                                                        disabled>
                                                        <span class="material-symbols-outlined">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="form-field">
                                    <label>Additional Phone Numbers</label>

                                    <div class="multi-row p-3" id="extra-phone-wrapper">

                                        @if (!empty($phones))
                                        @foreach ($phones as $index => $phone)
                                        <div class="row gx-3 align-items-center mb-2 extra-phone-row">
                                            <div class="col">
                                                <input
                                                    type="text"
                                                    class="form-control extra-phone-input"
                                                    name="phones[]"
                                                    value="{{ $phone }}">
                                            </div>
                                            <div class="col-auto" style="width:120px;">
                                                <div class="button-group">
                                                    @if ($loop->count > 1)
                                                    <button type="button"
                                                        class="btn btn-small btn-danger text-white remove-extra-phone">
                                                        <span class="material-symbols-outlined">delete</span>
                                                    </button>
                                                    @endif

                                                    @if ($loop->last)
                                                    <button type="button"
                                                        class="btn btn-small btn-success text-white add-extra-phone"
                                                        disabled>
                                                        <span class="material-symbols-outlined">add</span>
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else
                                        {{-- Default empty row --}}
                                        <div class="row gx-3 align-items-center mb-2 extra-phone-row">
                                            <div class="col">
                                                <input
                                                    type="text"
                                                    class="form-control extra-phone-input"
                                                    name="phones[]"
                                                    placeholder="Enter phone number">
                                            </div>
                                            <div class="col-auto" style="width:120px;">
                                                <div class="button-group">
                                                    <button type="button"
                                                        class="btn btn-small btn-success text-white add-extra-phone"
                                                        disabled>
                                                        <span class="material-symbols-outlined">add</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        </div>
                        <button class="btn btn-primary mt-3" type="submit">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('property-form');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const nameError = document.getElementById('name-error');
        const emailError = document.getElementById('email-error');
        const phoneError = document.getElementById('phone-error');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^\+?[\d\s-]{10,}$/;

        // Validation functions
        function validateName() {
            const value = nameInput.value.trim();
            if (value === '') {
                nameInput.classList.add('is-invalid');
                // nameError.textContent = 'Name is required';
                return false;
            } else if (value.length < 2) {
                nameInput.classList.add('is-invalid');
                // nameError.textContent = 'Name must be at least 2 characters long';
                return false;
            }
            nameInput.classList.remove('is-invalid');
            nameError.textContent = '';
            return true;
        }

        function validateEmail() {
            const value = emailInput.value.trim();
            if (value === '') {
                emailInput.classList.add('is-invalid');
                // emailError.textContent = 'Email is required';
                return false;
            } else if (!emailRegex.test(value)) {
                emailInput.classList.add('is-invalid');
                // emailError.textContent = 'Please enter a valid email address';
                return false;
            }
            emailInput.classList.remove('is-invalid');
            emailError.textContent = '';
            return true;
        }

        function validatePhone() {
            const value = phoneInput.value.trim();
            if (value === '') {
                phoneInput.classList.add('is-invalid');
                // phoneError.textContent = 'Phone number is required';
                return false;
            } else if (!phoneRegex.test(value)) {
                phoneInput.classList.add('is-invalid');
                // phoneError.textContent = 'Please enter a valid phone number (minimum 10 digits)';
                return false;
            }
            phoneInput.classList.remove('is-invalid');
            phoneError.textContent = '';
            return true;
        }

        // Real-time validation
        nameInput.addEventListener('input', validateName);
        emailInput.addEventListener('input', validateEmail);
        phoneInput.addEventListener('input', validatePhone);

        // Form submission validation
        form.addEventListener('submit', function(e) {
            const isNameValid = validateName();
            const isEmailValid = validateEmail();
            const isPhoneValid = validatePhone();

            if (!isNameValid || !isEmailValid || !isPhoneValid) {
                e.preventDefault();
            }
        });

        // CC Email handling (unchanged)
        // const wrapper = document.getElementById('cc-email-wrapper');
        // const emailRegexCC = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // updateButtons();
        // wrapper.addEventListener('click', function(e) {
        //     if (e.target.closest('.add-cc')) {
        //         const lastRow = wrapper.querySelector('.cc-email-row:last-child');
        //         const newRow = lastRow.cloneNode(true);

        //         const input = newRow.querySelector('input');
        //         // input.value = '';
        //         input.placeholder = 'Enter Email';
        //         newRow.querySelector('.button-group').innerHTML = `
        //             <button type="button" class="btn btn-small btn-danger text-white remove-cc">
        //                 <span class="material-symbols-outlined">delete</span>
        //             </button>
        //             <button type="button" class="btn btn-small btn-success text-white add-cc" disabled>
        //                 <span class="material-symbols-outlined">add</span>
        //             </button>
        //         `;
        //         wrapper.appendChild(newRow);
        //         updateButtons();
        //     }
        //     if (e.target.closest('.remove-cc')) {
        //         const row = e.target.closest('.cc-email-row');
        //         const rows = wrapper.querySelectorAll('.cc-email-row');

        //         if (rows.length > 1) {
        //             row.remove();
        //         }

        //         // If only one row remains, just ensure placeholder (do NOT clear value)
        //         const remaining = wrapper.querySelectorAll('.cc-email-row');
        //         if (remaining.length === 1) {
        //             const input = remaining[0].querySelector('.cc-email-input');
        //             if (!input.value) {
        //                 input.placeholder = 'Enter Email';
        //             }
        //         }

        //         updateButtons();
        //     }

        // });

        // wrapper.addEventListener('input', function(e) {
        //     if (e.target.classList.contains('cc-email-input')) {
        //         updateButtons();
        //     }
        // });

        // function updateButtons() {
        //     const rows = wrapper.querySelectorAll('.cc-email-row');
        //     rows.forEach((row, index) => {
        //         const input = row.querySelector('.cc-email-input');
        //         const removeBtn = row.querySelector('.remove-cc');
        //         const addBtn = row.querySelector('.add-cc');
        //         if (removeBtn) {
        //             removeBtn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        //         }
        //         if (addBtn) {
        //             const isLastRow = index === rows.length - 1;
        //             const isValidEmail = emailRegexCC.test(input.value.trim());
        //             addBtn.style.display = isLastRow ? 'inline-block' : 'none';
        //             addBtn.disabled = isLastRow ? !isValidEmail : true;
        //         }
        //     });
        // }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {

        function setupMultiInput({
            wrapperId,
            rowClass,
            inputClass,
            addBtnClass,
            removeBtnClass,
            placeholder,
            validateFn
        }) {
            const wrapper = document.getElementById(wrapperId);

            wrapper.addEventListener('click', e => {

                // ADD
                if (e.target.closest(`.${addBtnClass}`)) {
                    const row = document.createElement('div');
                    row.className = `row gx-3 align-items-center mb-2 ${rowClass}`;
                    row.innerHTML = `
                    <div class="col">
                        <input type="text"
                               class="form-control ${inputClass}"
                               name="${wrapperId === 'cc-email-wrapper' ? 'ccEmails[]' : 'phones[]'}"
                               placeholder="${placeholder}">
                    </div>
                    <div class="col-auto" style="width:120px;">
                        <div class="button-group">
                            <button type="button" class="btn btn-small btn-danger text-white ${removeBtnClass}">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                            <button type="button" class="btn btn-small btn-success text-white ${addBtnClass}" disabled>
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                    </div>
                `;
                    wrapper.appendChild(row);
                    update();
                }

                // REMOVE
                if (e.target.closest(`.${removeBtnClass}`)) {
                    const rows = wrapper.querySelectorAll(`.${rowClass}`);
                    if (rows.length > 1) {
                        e.target.closest(`.${rowClass}`).remove();
                    }
                    update();
                }
            });

            wrapper.addEventListener('input', update);

            function update() {
                const rows = wrapper.querySelectorAll(`.${rowClass}`);
                rows.forEach((row, i) => {
                    const input = row.querySelector(`.${inputClass}`);
                    const addBtn = row.querySelector(`.${addBtnClass}`);
                    const removeBtn = row.querySelector(`.${removeBtnClass}`);

                    const isLast = i === rows.length - 1;
                    const isValid = validateFn(input.value.trim());

                    if (addBtn) {
                        addBtn.style.display = isLast ? 'inline-block' : 'none';
                        addBtn.disabled = !isValid;
                    }

                    if (removeBtn) {
                        removeBtn.style.display = rows.length > 1 ? 'inline-block' : 'none';
                    }
                });
            }

            update();
        }

        // CC EMAIL
        setupMultiInput({
            wrapperId: 'cc-email-wrapper',
            rowClass: 'cc-email-row',
            inputClass: 'cc-email-input',
            addBtnClass: 'add-cc',
            removeBtnClass: 'remove-cc',
            placeholder: 'Enter Email',
            validateFn: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)
        });

        // ADDITIONAL PHONE
        setupMultiInput({
            wrapperId: 'extra-phone-wrapper',
            rowClass: 'extra-phone-row',
            inputClass: 'extra-phone-input',
            addBtnClass: 'add-extra-phone',
            removeBtnClass: 'remove-extra-phone',
            placeholder: 'Enter phone number',
            validateFn: v => /^\+?[\d\s-]{10,}$/.test(v)
        });

    });
</script>


@endsection