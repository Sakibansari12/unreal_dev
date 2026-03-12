@extends('pms.layouts.app')
@section('content')
    <style>
        .form-multiselect .dropdown-menu,
        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        #property-field {
            display: none;
        }

        .toggle-password {
            cursor: pointer;
        }

        #company, #withoutCompany {
            display: none;
            flex-wrap: wrap;
        }

        #withoutCompany {
            display: flex; /* Show by default */
        }
    </style>

    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">
                            @if ($detail)
                                Modify User
                            @else
                                Add New User
                            @endif
                        </h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.user.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                        </a>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('pms.user.save', $detail->id ?? '') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $detail->id ?? '' }}">
                <div class="content-box p-3">
                    <div class="form-box">
                        <div class="row">
                            <div class="col-12">
                                @php
                                    $currentUser = Auth::guard('admin')->user();
                                    $currentUserRoleName = is_object($currentUser->role) ? $currentUser->role->role_name : $currentUser->role;
                                @endphp

                                <div class="form-field">
                                    <label for="role_id">Role<sup>*</sup></label>
                                    <select name="role_id" id="role_id"
                                        class="form-control @error('role_id') is-invalid @enderror">
                                        <option value="">Select Role</option>
                                        @forelse($roles as $role)
                                            @php
                                                // Hide Travel Agent only for Property Manager
                                              //  $hideTravelAgent = $currentUserRoleName === 'Property Manager' && $role->role_name === 'Travel Agent';
                                              $hideTravelAgent = '';
                                            @endphp

                                            @if(!$hideTravelAgent)
                                                <option value="{{ $role->id }}"
                                                    {{ old('role_id', $detail->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                                    {{ $role->role_name }}
                                                </option>
                                            @endif
                                        @empty
                                            <option value="">No roles found</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12" id="property-field" style="display: none;">
                                <div class="form-field">
                                    <label for="property_ids">Property (Multiple Selection)</label>
                                    <div class="dropdown form-multiselect" id="propertyDropdown">
                                        <button class="form-control text-start" type="button" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" id="property_ids">
                                            <span class="multi-select-name">Select Properties</span>
                                            <span class="multi-select-count"></span>
                                        </button>
                                        <ul class="dropdown-menu w-100" id="property_dropdown">
                                            @forelse($properties as $index => $property)
                                                <li>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="mappedProperties[]" id="ckb-{{ $index + 1 }}"
                                                            data-name="{{ $property->unit_name ?? 'Untitled' }}"
                                                            value="{{ $property->id }}"
                                                            {{ in_array($property->id, old('mappedProperties', $userMappedPropertyIds)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ckb-{{ $index + 1 }}">
                                                            {{ $property->unit_name ?? 'Untitled' }}
                                                        </label>
                                                    </div>
                                                </li>
                                            @empty
                                                <li>
                                                    <div class="text-muted px-3 py-2">No properties found.</div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <!-- Without Company Section -->
                        <div class="row" id="withoutCompany">
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="name_without">Name<sup>*</sup></label>
                                    <input type="text" name="name" id="name_without"
                                        value="{{ old('name', $detail->name ?? '') }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="email_without">Email<sup>*</sup></label>
                                    <input type="email" name="email" id="email_without"
                                        value="{{ old('email', $detail->email ?? '') }}"
                                        class="form-control @error('email') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="mobile_without">Mobile<sup>*</sup></label>
                                    <input type="text" name="mobile" id="mobile_without"
                                        value="{{ old('mobile', $detail->mobile_no ?? '') }}"
                                        class="form-control @error('mobile') is-invalid @enderror" maxlength="13"
                                        oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field position-relative">
                                    <label for="password_without">Password<sup>*</sup></label>
                                    <input type="password" name="password" id="password_without"
                                        class="form-control @error('password') is-invalid @enderror"
                                        value="{{ old('password') }}">
                                    <span class="material-symbols-outlined toggle-password"
                                        style="position: absolute; top: 35px; right: 10px; cursor: pointer;"
                                        onclick="togglePassword('password_without')">
                                        visibility
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Company Section -->
                        <div class="row" id="company">
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="name_company">Company Name<sup>*</sup></label>
                                    <input type="text" name="name" id="name_company"
                                        value="{{ old('name', $detail->name ?? '') }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="contact_person">Contact Person<sup>*</sup></label>
                                    <input type="text" name="contact_person" id="contact_person"
                                        value="{{ old('contact_person', $detail->contact_person ?? '') }}"
                                        class="form-control @error('contact_person') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="mobile_company">Mobile<sup>*</sup></label>
                                    <input type="text" name="mobile" id="mobile_company"
                                        value="{{ old('mobile', $detail->mobile_no ?? '') }}"
                                        class="form-control @error('mobile') is-invalid @enderror" maxlength="13"
                                        oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="email_company">Email<sup>*</sup></label>
                                    <input type="email" name="email" id="email_company"
                                        value="{{ old('email', $detail->email ?? '') }}"
                                        class="form-control @error('email') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field position-relative">
                                    <label for="password_company">Password<sup>*</sup></label>
                                    <input type="password" name="password" id="password_company"
                                        class="form-control @error('password') is-invalid @enderror"
                                        value="{{ old('password') }}">
                                    <span class="material-symbols-outlined toggle-password"
                                        style="position: absolute; top: 35px; right: 10px; cursor: pointer;"
                                        onclick="togglePassword('password_company')">
                                        visibility
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="address">Address<sup>*</sup></label>
                                    <textarea name="address" id="address"
                                        class="form-control @error('address') is-invalid @enderror">{{ old('address', $detail->address ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="state_id">State<sup>*</sup></label>
                                    <select name="state_id" id="state_id"
                                        class="form-control @error('state_id') is-invalid @enderror">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ old('state_id', $detail->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                                {{ $state->state_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="city_id">City<sup>*</sup></label>
                                    <select name="city_id" id="city_id"
                                        class="form-control @error('city_id') is-invalid @enderror">
                                        <option value="">Select City</option>
                                        @if($detail && $detail->state_id)
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}"
                                                    {{ old('city_id', $detail->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->city_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="gst">GST</label>
                                    <input type="text" name="gst" id="gst"
                                        value="{{ old('gst', $detail->gst ?? '') }}"
                                        class="form-control @error('gst') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="sale_plan">Sales Plan</label>
                                    <input type="text" name="sale_plan" id="sale_plan"
                                        value="{{ old('sale_plan', $detail->sale_plan ?? '') }}"
                                        class="form-control @error('sale_plan') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="note">Note</label>
                                    <textarea name="note" id="note"
                                        class="form-control">{{ old('note', $detail->note ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Travel Section -->
                        <div class="row" id="travelagent">
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="name_travel">Company Name<sup>*</sup></label>
                                    <input type="text" name="name" id="name_travel"
                                        value="{{ old('name', $detail->name ?? '') }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="contact_person">Contact Person<sup>*</sup></label>
                                    <input type="text" name="contact_person" id="contact_person"
                                        value="{{ old('contact_person', $detail->contact_person ?? '') }}"
                                        class="form-control @error('contact_person') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="mobile_travel">Mobile<sup>*</sup></label>
                                    <input type="text" name="mobile" id="mobile_travel"
                                        value="{{ old('mobile', $detail->mobile_no ?? '') }}"
                                        class="form-control @error('mobile') is-invalid @enderror" maxlength="13"
                                        oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="email_travel">Email<sup>*</sup></label>
                                    <input type="email" name="email" id="email_travel"
                                        value="{{ old('email', $detail->email ?? '') }}"
                                        class="form-control @error('email') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field position-relative">
                                    <label for="password_travel">Password<sup>*</sup></label>
                                    <input type="password" name="password" id="password_travel"
                                        class="form-control @error('password') is-invalid @enderror"
                                        value="{{ old('password') }}">
                                    <span class="material-symbols-outlined toggle-password"
                                        style="position: absolute; top: 35px; right: 10px; cursor: pointer;"
                                        onclick="togglePassword('password_travel')">
                                        visibility
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="address">Address<sup>*</sup></label>
                                    <textarea name="address" id="address"
                                        class="form-control @error('address') is-invalid @enderror">{{ old('address', $detail->address ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="state_id">State<sup>*</sup></label>
                                    <select name="state_id" id="state_id_travel"
                                        class="form-control @error('state_id') is-invalid @enderror">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ old('state_id', $detail->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                                {{ $state->state_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="city_id">City<sup>*</sup></label>
                                    <select name="city_id" id="city_id_travel"
                                        class="form-control @error('city_id') is-invalid @enderror">
                                        <option value="">Select City</option>
                                        @if($detail && $detail->state_id)
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}"
                                                    {{ old('city_id', $detail->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->city_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="gst">GST</label>
                                    <input type="text" name="gst" id="gst"
                                        value="{{ old('gst', $detail->gst ?? '') }}"
                                        class="form-control @error('gst') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-field">
                                    <label for="discount">Discount Percentage</label>
                                    <input type="number" name="discount" id="discount"
                                        value="{{ old('discount', $detail->discount ?? '') }}"
                                        class="form-control @error('discount') is-invalid @enderror">
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="btn-wrap pt-2">
                    <button class="btn btn-primary px-5">{{ $detail ? 'UPDATE' : 'SUBMIT' }}</button>
                </div>
            </form>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = passwordInput.nextElementSibling;
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = 'visibility';
            }
        }

        function toggleFormSections(roleId) {
            const companySection = document.getElementById('company');
            const withoutCompanySection = document.getElementById('withoutCompany');
            const travelagent = document.getElementById('travelagent');
            
            if (roleId == 7) {
                companySection.style.display = 'flex';
                withoutCompanySection.style.display = 'none';
                withoutCompanySection.querySelectorAll('input, textarea, select').forEach(field => {
                    field.removeAttribute('name');
                });
                companySection.querySelectorAll('input, textarea, select').forEach(field => {
                    field.setAttribute('name', field.id.replace('_company', ''));
                });
                travelagent.style.display = 'none';
                travelagent.querySelectorAll('input, textarea, select').forEach(field => {
                    field.removeAttribute('name');
                });
            }else if (roleId == 8) {
                travelagent.style.display = 'flex';
                withoutCompanySection.style.display = 'none';
                withoutCompanySection.querySelectorAll('input, textarea, select').forEach(field => {
                    field.removeAttribute('name');
                });
                travelagent.querySelectorAll('input, textarea, select').forEach(field => {
                    field.setAttribute('name', field.id.replace('_travel', ''));
                });
                companySection.style.display = 'none';
                companySection.querySelectorAll('input, textarea, select').forEach(field => {
                    field.removeAttribute('name');
                });
            }else {
                companySection.style.display = 'none';
                withoutCompanySection.style.display = 'flex';
                companySection.querySelectorAll('input, textarea, select').forEach(field => {
                    field.removeAttribute('name');
                });
                withoutCompanySection.querySelectorAll('input, textarea, select').forEach(field => {
                    field.setAttribute('name', field.id.replace('_without', ''));
                });
                travelagent.style.display = 'none';
                travelagent.querySelectorAll('input, textarea, select').forEach(field => {
                    field.removeAttribute('name');
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role_id');
            toggleFormSections(roleSelect.value);

            roleSelect.addEventListener('change', function () {
                toggleFormSections(this.value);
            });

            // AJAX for city dropdown
            const stateSelects = [document.getElementById('state_id'), document.getElementById('state_id_travel')];
            const citySelects = [document.getElementById('city_id'), document.getElementById('city_id_travel')];

            stateSelects.forEach((stateSelect, index) => {
                stateSelect.addEventListener('change', function () {
                    const stateId = this.value;
                    const citySelect = citySelects[index];
                    citySelect.innerHTML = '<option value="">Select City</option>';

                    if (stateId) {
                        $.ajax({
                            url: `{{ url("pms/user/get-cities") }}/${stateId}`,
                            type: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                data.forEach(city => {
                                    const option = document.createElement('option');
                                    option.value = city.id;
                                    option.textContent = city.city_name;
                                    citySelect.appendChild(option);
                                });
                                // Restore old or previously selected city
                                const oldCityId = '{{ old('city_id', $detail->city_id ?? '') }}';
                                if (oldCityId && data.some(city => city.id == oldCityId)) {
                                    citySelect.value = oldCityId;
                                }
                            },
                            error: function () {
                                citySelect.innerHTML = '<option value="">No cities found</option>';
                            }
                        });
                    }
                });
            });

            setTimeout(function () {
                stateSelects.forEach((stateSelect, index) => {
                    if (stateSelect.value) {
                        stateSelect.dispatchEvent(new Event('change'));
                    }
                });
            }, 100); 
        });
        
        function updateDropdownDisplay() {
            const dropdown = document.getElementById('propertyDropdown');
            const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
            const nameSpan = dropdown.querySelector('.multi-select-name');
            const countSpan = dropdown.querySelector('.multi-select-count');
    
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const names = selected.map(cb => cb.getAttribute('data-name'));
    
            if (names.length === 0) {
                nameSpan.textContent = 'Select Properties';
                countSpan.textContent = '';
            } else if (names.length === 1) {
                nameSpan.textContent = names[0];
                countSpan.textContent = '';
            } else {
                nameSpan.textContent = `${names.length} Properties Selected`;
                countSpan.textContent = `(${names.length})`;
            }
        }
    
        function initializePropertyField() {
            const selectedRoleText = $('#role_id').find('option:selected').text().toLowerCase().trim();
            if (selectedRoleText === 'owners') {
                $('#property-field').show();
            } else {
                $('#property-field').hide();
            }
            updateDropdownDisplay();
        }
    
        $(document).ready(function() {
            initializePropertyField();
    
            $('#role_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text().toLowerCase().trim();
                if (selectedText === 'owners') {
                    $('#property-field').show();
                } else {
                    $('#property-field').hide();
                }
                updateDropdownDisplay(); 
            });
    
            document.addEventListener('change', function(event) {
                if (event.target.matches('#property_dropdown input[type="checkbox"]')) {
                    updateDropdownDisplay();
                }
            });
        });
    </script>
@endsection