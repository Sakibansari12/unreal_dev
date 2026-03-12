@if(!empty($properties))
    @php
        $price = $properties->price ?? 0;
        $perNight = $properties->per_night_price ?? 0;
        $noOfNights = $no_of_nights ?? 1;
        $addons = $properties->additionalCharge ?? [];
    @endphp

    <div class="col-12 text-end mt-3">
        <div class="row justify-content-end">
            <div class="col-auto">
                <table class="table fs-13 table-sm table-borderless w-auto booking-price-info">
                    <tbody>
                        <tr>
                            <th>Price Per Night:</th>
                            <td id="pernightprice">Rs. {{ number_format($perNight ?? 0)}}</td>
                        </tr>
                        <tr>
                            <td>Number of Nights:</td>
                            <td id="noofnight">{{ number_format($noOfNights ?? 0) }}</td>
                        </tr>
                        <tr>
                            <th>Base Price:</th>
                            <td id="baseprice">Rs. {{ number_format($price ?? 0) }}</td>
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
                            <th id="sub_total1">Rs. {{ number_format($price ?? 0) }}</th>
                        </tr>

                        @if(count($addons) > 0)
                            <tr>
                                <th>Add-ons</th>
                                <td></td>
                            </tr>

                            @foreach($addons as $index => $addon)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input float-none" id="{{ $addon['id'] }}" value="{{ $addon['price'] }}" onchange="calculate()" />
                                            <label for="{{ $addon['id'] }}" class="fw-normal text-nowrap mb-0 ps-2">{{ $addon['name'] }}:</label>
                                        </div>
                                    </td>
                                    <td>Rs. {{ number_format($addon['price'] ?? 0) }}</td>
                                </tr>
                            @endforeach

                            <tr>
                                <td>Discount:</td>
                                <td>
                                    <div class="input-group small-input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input type="number" class="form-control" id="add_ons_discount_amount" value="0" data-bs-toggle="tooltip" data-bs-title="Default tooltip" onkeyup="calculate()" /><br>
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

    <script>
        noOfNights = {{ $noOfNights }};
        price = {{ $price }};
        perNightPrice = {{ $perNight }};
        slabList =  @json($gst_slab);

        function getSlabGST(slabAmount) {
            for (let item of slabList) {
                if (slabAmount >= item.slabs_start && slabAmount <= item.slabs_upto) {
                    return item.gst_percentage;
                }
            }
            return 0;
        }
        // console.log("Slab list loaded:", JSON.stringify(slabList));
        function calculate() {
            const baseDiscountInput = document.getElementById("discount_amount");
            const addOnDiscountInput = document.getElementById("add_ons_discount_amount");

            const baseDiscountWarning = document.getElementById("base-discount-warning");
            const addOnDiscountWarning = document.getElementById("add-discount-warning");

            const basePrice = {{ $price }};
            const baseDiscount = parseFloat(baseDiscountInput.value) || 0;

            let addOnTotal = 0;
            let addOnSubTotal = 0;

            // Check if there are add-ons and calculate add-on total
            @if(count($addons) > 0)
                @foreach($addons as $addon)
                    const addonElement = document.getElementById("{{ $addon['id'] }}");
                    if (addonElement && addonElement.checked) {
                        addOnTotal += {{ $addon['price'] }};
                    }
                @endforeach

                // Ensure addOnDiscountInput exists before accessing its value
                const addOnDiscount = addOnDiscountInput ? parseFloat(addOnDiscountInput.value) || 0 : 0;
            @else
                // If no add-ons, set addOnTotal and addOnDiscount to 0
                addOnTotal = 0;
                addOnSubTotal = 0;
            @endif

            // Flag to stop calculation if any discount invalid
            let hasError = false;

            // Validate Base Discount
            if (baseDiscount > basePrice) {
                baseDiscountInput.classList.add("border-danger");
                baseDiscountWarning.classList.remove("d-none");
                hasError = true;
            } else {
                baseDiscountInput.classList.remove("border-danger");
                baseDiscountWarning.classList.add("d-none");
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

                if (subTotal1) subTotal1.innerText = `Rs. 0`;
                if (subTotal2) subTotal2.innerText = `Rs. 0`;
                if (taxableAmount) taxableAmount.innerText = `Rs. 0`;
                if (taxPercent) taxPercent.innerText = `0`;
                if (gstAmount) gstAmount.innerText = `Rs. 0`;
                if (totalPayableAmount) totalPayableAmount.innerText = `Rs. 0`;

                return;
            }

            // Valid — continue calculation
            const baseSubTotal = basePrice - baseDiscount;
            addOnSubTotal = addOnTotal - addOnDiscount;

            // If no add-ons, sub_total2 should be Rs. 0
            const subTotal2 = document.getElementById("sub_total2");
            if (subTotal2) {
                subTotal2.innerText = `Rs. ${addOnSubTotal.toLocaleString("en-IN")}`;
            }

            const totalTaxable = baseSubTotal + addOnSubTotal;

            const subTotal1 = document.getElementById("sub_total1");
            if (subTotal1) {
                subTotal1.innerText = `Rs. ${baseSubTotal.toLocaleString("en-IN")}`;
            }

            const taxableAmount = document.getElementById("taxable_amount");
            if (taxableAmount) {
                taxableAmount.innerText = `Rs. ${totalTaxable.toLocaleString("en-IN")}`;
            }

            // GST Calculation
            const slabAmount = totalTaxable / {{ $noOfNights }};
            const taxPercent = getSlabGST(slabAmount);
            const taxAmount = (totalTaxable * taxPercent) / 100;

            const taxPercentElement = document.getElementById("tax_percent");
            if (taxPercentElement) {
                taxPercentElement.innerText = taxPercent;
            }

            const gstAmountElement = document.getElementById("gstamount");
            if (gstAmountElement) {
                gstAmountElement.innerText = `Rs. ${Math.round(taxAmount).toLocaleString("en-IN")}`;
            }

            // Final Total
            const totalPayable = totalTaxable + taxAmount;
            const totalPayableAmount = document.getElementById("total_payable_amount");
            if (totalPayableAmount) {
                totalPayableAmount.innerText = `Rs. ${Math.round(totalPayable).toLocaleString("en-IN")}`;
            }
        }
        // Initial call
        calculate();
    </script>
@endif
