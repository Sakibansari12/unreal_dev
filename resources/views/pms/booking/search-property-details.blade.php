<div class="search-properties mt-4 mb-4" id="search-property">
    <form id="searchPropertyFormId">
        <div class="row gy-3 gx-2 gx-md-3">
            <div class="col-6 col-lg-3">
                <div class="form-field mb-0">
                    <label for="email_address">Email Address<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email_address" name="email_address">
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="form-field form-group mb-0">
                    <label for="mobile_number">Mobile Number<span class="text-danger">*</span></label>
                    <div class="row gx-2">
                        @php
                            $countries = DB::table('countries')->get();
                        @endphp
                        <div class="col-auto">
                            <select id="countryCode" class="form-control form-select pe-2" name="country_code">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->phonecode }}"
                                        {{ $country->phonecode == 91 ? 'selected' : '' }}>
                                        {{ $country->iso }} (+{{ $country->phonecode }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" maxlength="13" oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="form-field mb-0">
                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="first_name" name="first_name">
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="form-field mb-0">
                    <label for="last_name">Last Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="last_name" name="last_name">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12 col-xl-12">
                <div class="form-group mt-3">
                    <div class="form-check">
                        <input class="form-check-input company_info_check" type="checkbox" value="checked" id="company_info" name="company_info">
                        <label class="form-check-label" for="company_info">
                            <b>Check this if you wish to enter company info</b>
                        </label>
                    </div>
                </div>

                <div class="company-info-fields" style="display: none;">
                    <div class="row mt-3">
                        <div class="col-6 col-lg-3 mt-2">
                            <label for="company_name" class="form-label">Company Name<span class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" class="form-control">
                        </div>
                        <div class="col-6 col-lg-3 mt-2">
                            <label for="gst_no" class="form-label">GST No<span class="text-danger">*</span></label>
                            <input type="text" name="gst_no" id="gst_no" class="form-control">
                            <small id="gsterror" class="text-danger"></small>
                        </div>
                        {{-- <div class="col-6 col-lg-3 mt-2">
                            <label for="state" class="form-label">State<span class="text-danger">*</span></label>
                            <input type="text" name="state" id="state" class="form-control">
                        </div>
                        <div class="col-6 col-lg-3 mt-2">
                            <label for="city" class="form-label">City<span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control">
                        </div> --}}
                        <div class="col-6 col-lg-3 mt-2">
                            <div class="form-field">
                                <label for="state">State<sup>*</sup></label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state', $detail->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                            {{ $state->state_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 mt-2">
                            <div class="form-field">
                                <label for="city">City<sup>*</sup></label>
                                <select name="city" id="city" class="form-control">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 mt-2">
                            <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                            <input type="text" name="address" id="address" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

       
        @if(!empty($properties))
            @php
                $price = $properties->price ?? 0;
                $perNight = $properties->per_night_price ?? 0;
                $noOfNights = $no_of_nights ?? 1;
                $addons = $properties->additionalCharge ?? [];
                $no_adults = $no_adults ?? 1;
                $no_children = $no_children ?? 0;
            @endphp

            <div class="col-12 text-end mt-3">
                <div class="row justify-content-end">
                    <div class="col-auto">
                        <table class="table fs-13 table-sm table-borderless w-auto booking-price-info">
                            <tbody>
                                <tr>
                                    <th>Price Per Night:</th>
                                    <td id="pernightprice">Rs. {{ formatIN($perNight ?? 0)}}</td>
                                </tr>
                                <tr>
                                    <td>Number of Nights:</td>
                                    <td id="noofnight">{{ formatIN($noOfNights ?? 0) }}</td>
                                </tr>
                                <tr>
                                    <th>Base Price:</th>
                                    <td id="baseprice">Rs. {{ formatIN($price ?? 0) }}</td>
                                </tr>
                                <tr id="extra_guest_row" style="display: none;">
                                    <td>Extra Guest Charges:</td>
                                    <td id="extra_guest_charges">Rs. 0</td>
                                </tr>

                                <tr>
                                    <td>Discount:</td>
                                    <td>
                                        <div class="input-group small-input-group">
                                            <span class="input-group-text">Rs</span>
                                            <input type="number" class="form-control" id="discount_amount" value="0" onkeyup="calculate()"/>
                                        </div>
                                        <small id="base-discount-warning" class="text-danger d-none">Discount cannot exceed base price.</small>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Sub Total:</th>
                                    <th id="sub_total1">Rs. {{ formatIN($price ?? 0) }}</th>
                                </tr>

                                @if(count($addons) > 0)
                                <tr>
                                    <th>Add-ons</th>
                                    <td></td>
                                </tr>

                                {{-- @foreach($addons as $index => $addon)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input float-none addon-checkbox" id="{{ $addon['id'] }}" value="{{ $addon['price'] }}" onchange="calculate()" />
                                            <label for="{{ $addon['id'] }}" class="fw-normal text-nowrap mb-0 ps-2">{{ $addon['name'] }}:</label>
                                        </div>
                                    </td>
                                    <td>Rs. {{ formatIN($addon['price'] ?? 0) }}</td>
                                </tr>
                                @endforeach --}}

                                @foreach($addons as $index => $addon)
                                    @php
                                        if($addon['type_option'] == 'Per_Night'){
                                            $finalPrice = $addon['price'] * $noOfNights;
                                        }else{
                                            $finalPrice = $addon['price'];
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input addon-checkbox"
                                                    id="addon_{{ $addon['id'] }}"
                                                    value="{{ $finalPrice }}"
                                                    onchange="calculate()"
                                                >
                                                <label for="addon_{{ $addon['id'] }}" class="ps-2">
                                                    {{ $addon['name'] }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>Rs. {{ formatIN($finalPrice) }}</td>
                                    </tr>
                                @endforeach

                                {{-- <tr>
                                    <td>Discount:</td>
                                    <td>
                                        <div class="input-group small-input-group">
                                            <label for="add_ons_discount_amount">Add-on Discount:</label>
                                            <input
                                                type="number"
                                                id="add_ons_discount_amount"
                                                class="form-control"
                                                value="0"
                                                oninput="calculate()"
                                                disabled
                                            >
                                            <small id="add-discount-warning" class="text-danger d-none">Add-on discount cannot exceed selected add-ons total.</small>
                                        </div>
                                    </td>
                                </tr> --}}
                                
                                <tr>
                                    <td>Discount:</td>
                                    <td>
                                        <div class="input-group small-input-group">
                                            <span class="input-group-text">Rs</span>
                                            <input type="number" class="form-control" id="add_ons_discount_amount" value="0" data-bs-toggle="tooltip" data-bs-title="Default tooltip" disabled onkeyup="calculate()" /><br>
                                        </div>
                                        <small id="add-discount-warning" class="text-danger d-none">Discount cannot exceed add-ons total.</small>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Sub Total:</th>
                                    <th id="sub_total2">Rs. 0</th>
                                </tr>
                                @endif

                                <tr>
                                    <th>Total Taxable Amount:</th>
                                    <td id="taxable_amount">Rs. 0</td>
                                </tr>

                                <tr>
                                    <td>GST (<span id="tax_percent">0</span>%):</td>
                                    <td id="gstamount">Rs. 0</td>
                                </tr>

                                <tr class="fs-6">
                                    <th class="text-primary">Total Amount Payable:</th>
                                    <th class="text-primary" id="total_payable_amount">Rs. 0</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @php
                $gst_slab
            @endphp
            
            
        @endif

        <div class="row">
            <div class="col-6 col-lg-3">
                <div class="form-field">
                    <label for="checkInTime">Check-In Time</label>
                    <input type="time" name="check-in-time" id="checkInTime" class="form-control flatpickr">
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="form-field">
                    <label for="checkOutTime">Check-Out Time</label>
                    <input type="time" name="check-out-time" id="checkOutTime" class="form-control flatpickr">
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-field">
                <label for="booking_note">Note</label>
                <textarea name="booking_note" id="booking_note" cols="30" rows="4" class="form-control"></textarea>
            </div>
        </div>

        <input type="hidden" id="no_adults" value="{{ $no_adults ?? 0 }}">
        <input type="hidden" id="max_guests" value="{{ $properties->maximum_number_of_guests ?? 0 }}">
        <input type="hidden" id="website_markup_price" value="{{ $properties->website_markup_price ?? 0 }}">
        <input type="hidden" id="extra_guest_charge_per_person" value="{{ $properties->extra_guest_charges ?? 0 }}">

        
        <div class="col-12">
            <button type="button" class="btn btn-save btn-primary" id="bookingFormBtn">
                SUBMIT
            </button>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        
    var noOfNights = {{ $noOfNights }};
    var price = {{ $price }};
    var perNightPrice = {{ $perNight }};
    var slabList =  @json($gst_slab);

    var noAdults = parseInt({{ $no_adults ?? 0 }}) || 0;
    var maxGuests = parseInt({{ $properties->maximum_number_of_guests ?? 0 }}) || 0;
    var extraGuestChargePerPerson = parseFloat({{ $properties->extra_guest_charges ?? 0 }}) || 0;
    
    function getSlabGST(slabAmount) {
        for (let item of slabList) {
            if (slabAmount >= item.slabs_start && slabAmount <= item.slabs_upto) {
                return item.gst_percentage;
            }
        }
        return 0;
    }
    
    // Initial call
    calculate();
    // console.log("Slab list loaded:", JSON.stringify(slabList));
    function calculate() {
        // const extraGuestInput = document.getElementById("extra_guest_charges");
        // const extraGuestCharges = extraGuestInput ? parseFloat(extraGuestInput.value) || 0 : 0;

        const baseDiscountInput = document.getElementById("discount_amount");
        const addOnDiscountInput = document.getElementById("add_ons_discount_amount");

        const baseDiscountWarning = document.getElementById("base-discount-warning");
        const addOnDiscountWarning = document.getElementById("add-discount-warning");

        const basePrice = {{ $price }};
        const baseDiscount = baseDiscountInput ? parseFloat(baseDiscountInput.value) || 0 : 0;

        let addOnTotal = 0;
        let addOnSubTotal = 0;
        let addOnDiscount = 0;

        
        // Check if there are add-ons and calculate add-on total
        @if(count($addons) > 0)
            @foreach($addons as $addon)
                var addonElement = document.getElementById("addon_{{ $addon['id'] }}");
                if (addonElement && addonElement.checked) {
                    // addOnTotal += {{ $addon['price'] }};
                    var typeOption = "{{ $addon['type_option'] }}";
                    var addonPrice = {{ $addon['price'] }};
                    if (typeOption === 'Per_Night') {
                        addOnTotal += addonPrice * {{ $noOfNights }};
                    } else {
                        addOnTotal += addonPrice;
                    }
                }
            @endforeach

            // Ensure addOnDiscountInput exists before accessing its value
            // const addOnDiscount = addOnDiscountInput ? parseFloat(addOnDiscountInput.value) || 0 : 0;
            if (addOnDiscountInput) {
                addOnDiscount = parseFloat(addOnDiscountInput.value) || 0;
            }
        @else
            // If no add-ons, set addOnTotal and addOnDiscount to 0
            addOnTotal = 0;
            addOnSubTotal = 0;
            addOnDiscount = 0;
        @endif

        // Flag to stop calculation if any discount invalid
        let hasError = false;


        // Extra Guest Charges Calculation
        const totalGuests = noAdults;
        const guestsIncluded = parseInt({{ $properties->guests_included ?? 0 }}) || 0; // Number of guests included in the base price
        let extraGuestChargesTotal = 0;

        if (totalGuests > guestsIncluded) {
            const extraGuests = totalGuests - guestsIncluded;
            extraGuestChargesTotal = extraGuests * extraGuestChargePerPerson * {{ $noOfNights }};
        }

        let extraGuestChargesDisplay = document.getElementById("extra_guest_charges");
        var extraGuestRow = document.getElementById("extra_guest_row");

        if (extraGuestChargesTotal > 0) {
            if (!extraGuestChargesDisplay) {
                extraGuestChargesDisplay = document.createElement('div');
                extraGuestChargesDisplay.id = 'extra_guest_charges';
                document.body.appendChild(extraGuestChargesDisplay); // Or append to a specific parent element
            }

            extraGuestChargesDisplay.innerText = `Rs. ${extraGuestChargesTotal.toLocaleString("en-IN")}`;
            
            if (extraGuestRow) {
                extraGuestRow.style.display = ""; // Show the row
            }
        } else {
            if (extraGuestRow) {
                extraGuestRow.style.display = "none"; // Hide if no extra charges
            }
        }


        // Validate Base Discount
        if (baseDiscountInput && baseDiscountWarning) {
            if (baseDiscount > basePrice + extraGuestChargesTotal) {
                baseDiscountInput.classList.add("border-danger");
                baseDiscountWarning.classList.remove("d-none");
                hasError = true;
            } else {
                baseDiscountInput.classList.remove("border-danger");
                baseDiscountWarning.classList.add("d-none");
            }
        }
        // Validate Add-on Discount only if add-ons exist
        if (addOnDiscount > addOnTotal) {
            if (addOnDiscountInput && addOnDiscountWarning) {  // Check if elements exist
                addOnDiscountInput.classList.add("border-danger");
                addOnDiscountWarning.classList.remove("d-none");
            }
            hasError = true;
        } else {
            if (addOnDiscountInput && addOnDiscountWarning) {  // Check if elements exist
                addOnDiscountInput.classList.remove("border-danger");
                addOnDiscountWarning.classList.add("d-none");
            }
        }

        // If error found, reset all outputs and stop
        if (hasError) {
            const subTotal1 = document.getElementById("sub_total1");
            const subTotal2 = document.getElementById("sub_total2");
            const taxableAmount = document.getElementById("taxable_amount");
            const taxPercent = document.getElementById("tax_percent");
            const gstAmount = document.getElementById("gstamount");
            const totalPayableAmount = document.getElementById("total_payable_amount");
            const extraGuestChargesDisplay = document.getElementById("extra_guest_charges");

            if (subTotal1) subTotal1.innerText = `Rs. 0`;
            if (subTotal2) subTotal2.innerText = `Rs. 0`;
            if (taxableAmount) taxableAmount.innerText = `Rs. 0`;
            if (taxPercent) taxPercent.innerText = `0`;
            if (gstAmount) gstAmount.innerText = `Rs. 0`;
            if (totalPayableAmount) totalPayableAmount.innerText = `Rs. 0`;
            if (extraGuestChargesDisplay) extraGuestChargesDisplay.innerText = `Rs. 0`;

            return;
        }

        
       

        // Valid — continue calculation
        const baseSubTotal = basePrice + extraGuestChargesTotal - baseDiscount;
        addOnSubTotal = addOnTotal - addOnDiscount;

        // If no add-ons, sub_total2 should be Rs. 0
        const subTotal2 = document.getElementById("sub_total2");
        if (subTotal2) {
            subTotal2.innerText = `Rs. ${Math.round(addOnSubTotal).toLocaleString("en-IN")}`;
        }

        const totalTaxable = baseSubTotal + addOnSubTotal;

        const subTotal1 = document.getElementById("sub_total1");
        if (subTotal1) {
            subTotal1.innerText = `Rs. ${Math.round(baseSubTotal).toLocaleString("en-IN")}`;
        }

        const taxableAmount = document.getElementById("taxable_amount");
        if (taxableAmount) {
            taxableAmount.innerText = `Rs. ${Math.round(totalTaxable).toLocaleString("en-IN")}`;
        }

        // GST Calculation
        const slabAmount = Math.round(totalTaxable) / {{ $noOfNights }};
        const taxPercent = getSlabGST(slabAmount);
        const taxAmount = (totalTaxable * taxPercent) / 100;

        const taxPercentElement = document.getElementById("tax_percent");
        if (taxPercentElement) {
            taxPercentElement.innerText = taxPercent;
        }

        // Final Total Calculation with Extra Guest Charges
        const totalTaxableWithExtras = Math.round(totalTaxable) + extraGuestChargesTotal;
        const taxAmountWithExtras = (totalTaxableWithExtras * taxPercent) / 100;
        const totalPayableWithExtras = totalTaxableWithExtras + taxAmountWithExtras;
        

        const gstAmountElement = document.getElementById("gstamount");
        if (gstAmountElement) {
            gstAmountElement.innerText = `Rs. ${Math.round(taxAmount).toLocaleString("en-IN")}`;
        }

        // Final Total
        const totalPayable = Math.round(totalTaxable) + taxAmount;
        const totalPayableAmount = document.getElementById("total_payable_amount");
        if (totalPayableAmount) {
            totalPayableAmount.innerText = `Rs. ${Math.round(totalPayable).toLocaleString("en-IN")}`;
        }

        toggleDiscountInput();
    }

    function toggleDiscountInput() {
        const checkboxes = document.querySelectorAll('.addon-checkbox');
        const discountInput = document.getElementById('add_ons_discount_amount');

        let anyChecked = false;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                anyChecked = true;
            }
        });

        if (discountInput) {
            discountInput.disabled = !anyChecked;
        }
    }

</script>



<script>
    // Booking form submission
    $('#bookingFormBtn').on('click', function(e) {
        e.preventDefault();

        let isValid = true;
        if (!$('#email_address').val()) {
            showError('email_address', 'Please enter email address');
            isValid = false;
        } else if (!/^\S+@\S+\.\S+$/.test($('#email_address').val())) {
            showError('email_address', 'Please enter a valid email address');
            isValid = false;
        } else {
            clearError('email_address');
        }

        if (!$('#mobile_number').val()) {
            showError('mobile_number', 'Please enter mobile number');
            isValid = false;
        } else if (!/^\d+$/.test($('#mobile_number').val())) {
            showError('mobile_number', 'Please enter a valid mobile number');
            isValid = false;
        } else {
            clearError('mobile_number');
        }

        if (!$('#first_name').val()) {
            showError('first_name', 'Please enter first name');
            isValid = false;
        } else if (!/^[a-zA-Z]+$/.test($('#first_name').val())) {
            showError('first_name', 'First name must contain only letters');
            isValid = false;
        } else {
            clearError('first_name');
        }

        if (!$('#last_name').val()) {
            showError('last_name', 'Please enter last name');
            isValid = false;
        } else if (!/^[a-zA-Z]+$/.test($('#last_name').val())) {
            showError('last_name', 'Last name must contain only letters');
            isValid = false;
        } else {
            clearError('last_name');
        }
        
        if ($('#company_info').is(':checked')) {
            const requiredFields = ['company_name', 'gst_no', 'state', 'city', 'address'];
            const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/;
            // initCityDropdown();
            
            requiredFields.forEach(function(field) {
                const val = $('#' + field).val().trim();

                if (!val) {
                    showError(field, 'This field is required');
                    isValid = false;
                } else {
                    // Extra validation for GST No.
                    if (field === 'gst_no') {
                        if (!gstRegex.test(val)) {
                            $('#gsterror').text('Invalid GST number format');
                            isValid = false;
                        } else {
                            $('#gsterror').text('');
                        }
                    } else {
                        clearError(field);
                    }
                }
            });
            if (!$('#city').val()) {
                e.preventDefault();
                showError('city', 'Please select a city');
                return false;
            }

            company_details = {
                company_name: $('#company_name').val().trim(),
                gst_no: $('#gst_no').val().trim(),
                state: $('#state option:selected').text(),
                city: $('#city option:selected').text(),
                address: $('#address').val().trim()
            };
        } else {
            company_details = {
                company_name: null,
                gst_no: null,
                state: null,
                city: null,
                address: null
            };
        }

        if (!isValid) return;
        let checkInDate = $('#checkInDate').val().trim();
        let checkOutDate = $('#checkOutDate').val().trim();
        let tax = $("#tax_percent").text();
        let gstText = $('#gstamount').text(); // "Rs. 0"
        let taxAmount = parseFloat(gstText.replace(/[^0-9]/g, '')) || 0;
        let taxable_amount = $('#taxable_amount').text(); // "Rs. 0"
        let totalAmount = parseFloat(taxable_amount.replace(/[^0-9]+/g, '')); // Removes everything except numbers and dot
        let total_payable_amount = $('#total_payable_amount').text(); // "Rs. 0"
        let totalPayableAmount = parseFloat(total_payable_amount.replace(/[^0-9]/g, '')) || 0;
        let discountAmount = $('#discount_amount').val(); // "Rs. 0"
        let no_adults = $('select[name="no_adults"]').val();
        let website_markup_price = $('#website_markup_price').val();
        let no_children = $('select[name="no_children"]').val() || 0;
        let addOnsDiscountTotalAmount = $('#add_ons_discount_amount').text() || 0; // "Rs. 0"
        let extraguest = $('#extra_guest_charges').text(); // "Rs. 0"
        let extraguestcharges = parseFloat(extraguest.replace(/[^0-9]/g, '')) || 0;
        // console.log(discountAmount);

        isSubmitLoading = true;
        $('#bookingFormBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

        let formData = {
            locationId: "{{ $properties->location_id ?? '' }}",
            propertyId: "{{ $properties->id ?? '' }}",
            checkInDate: checkInDate,
            checkOutDate: checkOutDate,
            noOfNights: "{{ $noOfNights ?? 1 }}",
            tax: tax,
            taxAmount: taxAmount,
            netAmount: totalAmount,
            netPayableAmount: totalPayableAmount,
            discount_amount: discountAmount,
            additional_charges: @json($addons ?? []),
            type: 'Property',
            no_children: no_children ?? 0,
            no_adult: no_adults,
            website_markup_price: website_markup_price,
            per_night_price: "{{$perNight ?? 0}}",
            initial_price: "{{$price ?? 0}}",
            dont_block: $('#cbk-block').is(':checked') ? 1 : 0,
            is_invoice: 0,
            tot_additional_charge_amount: addOnsDiscountTotalAmount ?? 0,
            base_price: "{{$price ?? 0}}",
            extra_guest_charge: extraguestcharges,
            totalTaxableAmount: totalAmount,
            booking_note: $('#booking_note').val(),
            checkin_time: $('#checkInTime').val() || '12:00',
            checkout_time: $('#checkOutTime').val() || '11:00',
            email_address: $('#email_address').val(),
            country_code: $('#countryCode').val(),
            mobile_number: $('#mobile_number').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            pType : "{{ $properties->pType ?? '' }}",
            company_detail : company_details
        };


        // console.log('Form Data before submission:', formData); // Debug formData

        $.ajax({
            url: '{{ route("pms.bookingbyproperty") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(res) {
                if (res.status) {
                    toastr.success(res.message, 'Success', { timeOut: 1000 });
                    setTimeout(function() {
                        window.location.href = '{{ route("pms.booking.list") }}';
                    }, 2000);
                }
            },
            error: function(error) {
                console.error('Booking submission error:', error);
                showToast(error.responseJSON?.message || 'An error occurred', 'error');
                isSubmitLoading = false;
                $('#bookingFormBtn').html('SUBMIT');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {

        let checkInTime = "{{$properties->checkin_time}}" || '00:00';
        let checkOutTime = "{{$properties->checkout_time}}" || '00:00';
    
    
        // Flatpickr for time inputs
        const checkInTimeInput = document.getElementById("checkInTime");
        let fpTimecheckInTime = flatpickr(checkInTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            clickOpens: false,
            onChange: function(selectedDates, timeStr) {
                // console.log('Check-in Time Selected:', timeStr);
            }
        });

        checkInTimeInput.addEventListener("click", () => {
            if (fpTimecheckInTime.isOpen) {
                fpTimecheckInTime.close();
            } else {
                fpTimecheckInTime.open();
            }
        });

        const checkOutTimeInput = document.getElementById("checkOutTime");
        let fpTimecheckOutTime = flatpickr(checkOutTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            clickOpens: false,
            onChange: function(selectedDates, timeStr) {
                // console.log('Check-out Time Selected:', timeStr);
            }
        });

        checkOutTimeInput.addEventListener("click", () => {
            if (fpTimecheckOutTime.isOpen) {
                fpTimecheckOutTime.close();
            } else {
                fpTimecheckOutTime.open();
            }
        });

        function timeFormat(value) {
            if (!value) return "12:00"; // Default time
            if (typeof value === 'string') {
                // Ensure string is in HH:mm format
                const [hours, minutes] = value.split(':');
                return `${hours.padStart(2, '0')}:${minutes?.padStart(2, '0') || '00'}`;
            } else if (typeof value === 'object' && value.hours && value.minutes) {
                // Convert object to HH:mm
                return `${String(value.hours).padStart(2, '0')}:${String(value.minutes).padStart(2, '0')}`;
            }
            return "12:00"; // Fallback
        }

        function currFormat(num) {
            return new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0,
                currency: 'INR'
            }).format(num);
        }

        function currFormatTotal(num) {
            return new Intl.NumberFormat('en-IN', {
                maximumFractionDigits: 0,
                currency: 'INR'
            }).format(num);
        }

        updateTimeFields();
        function updateTimeFields() {
            // console.log('Updating time fields:', { checkInTime, checkOutTime });
            if (checkInTime) {
                $('#checkInTime').val(checkInTime);
                fpTimecheckInTime.setDate(checkInTime, false, 'H:i');
            }
            if (checkOutTime) {
                $('#checkOutTime').val(checkOutTime);
                fpTimecheckOutTime.setDate(checkOutTime, false, 'H:i');
            }
        }
    });    

</script>

<script>
    $(document).on('change', '#company_info', function () {
        if ($(this).is(':checked')) {
            $('.company-info-fields').slideDown();
        } else {
            $('.company-info-fields').slideUp();
        }
    });
    
    initCityDropdown();
    function initCityDropdown() {
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');
        
        if (!stateSelect || !citySelect) {
            console.warn('State or City select not found');
            return;
        }

        stateSelect.addEventListener('change', function () {
            const stateId = this.value;
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
                    },
                    error: function () {
                        citySelect.innerHTML = '<option value="">No cities found</option>';
                    }
                });
            }
        });

        // Trigger city load if a state is already selected
        if (stateSelect.value) {
            stateSelect.dispatchEvent(new Event('change'));
        }
    }

    
</script>