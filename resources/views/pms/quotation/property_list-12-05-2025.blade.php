@if (count($properties) > 0)
    <div class="row g-3">
        @foreach ($properties as $property)
            <div class="col-md-4">
                <div class="card" data-property-id="{{ $property['property_id'] }}">
                    <div class="card-body p-0">
                        <div class="label-radio home-radio border-0">
                            <input
                                type="checkbox"
                                id="pr{{ $property['property_id'] }}"
                                name="selected_properties[]"
                                value="{{ $property['property_id'] }}"
                                class="property-checkbox"
                                @if(isset($property['selected']) && $property['selected']) checked @endif
                            >
                            <label for="pr{{ $property['property_id'] }}">
                                <span class="fw-bold">{{ $property['unit_name'] }}</span>
                                <span class="d-flex align-items-center pt-1">
                                    <i class="material-symbols-outlined">person</i> {{ $property['maximum_number_of_guests'] }} Max Occupancy
                                </span>
                            </label>
                        </div>
                        <hr class="mt-0">
                    </div>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th>Price Per Night:</th>
                                <td align="end" class="price-per-night">Rs. {{ number_format($property['price_per_night'] ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td>Number of Nights:</td>
                                <td align="end">{{ number_format($property['no_of_nights'] ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td>Base Price:</td>
                                <td align="end" class="base-price">Rs. {{ number_format($property['base_price'] ?? 0) }}</td>
                            </tr>
                            @if($property['total_extra_guest_charge'] != 0)
                            <tr>
                                <td>Extra Guest:</td>
                                <td align="end" class="extraGuestCharge">Rs. {{ number_format($property['total_extra_guest_charge'] ?? 0) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>Discount:</td>
                                <td align="end">
                                    <div class="input-group small-input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input
                                            type="number"
                                            name="discount_amount[{{ $property['property_id'] }}]"
                                            class="form-control discount-1"
                                            value="{{ isset($property['discount_amount']) ? $property['discount_amount'] : '' }}"
                                            @if(!isset($property['selected']) || !$property['selected']) disabled @endif
                                            min="0"
                                            max="{{ $property['sub_total'] ?? 0 }}"
                                        />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Sub Total:</td>
                                <td align="end" class="sub-total">Rs. {{ number_format(($property['sub_total'] ?? 0) ) }}</td>
                            </tr>
                            @if (!empty($property['additionalCharge']) && count($property['additionalCharge']) > 0)
                            <tr>
                                <th>Add-ons</th>
                                <td></td>
                            </tr>
                            @foreach($property['additionalCharge'] as $obj)
                                <tr>
                                    <td>{{ $obj['name'] ?? '' }}:</td>
                                    <td align="end" class="addon-price" data-addon-price="{{ $obj['price'] ?? 0 }}">Rs. {{ number_format($obj['price'] ?? 0) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>Discount:</td>
                                <td align="end">
                                    <div class="input-group small-input-group">
                                        <span class="input-group-text">Rs</span>
                                        <input
                                            type="number"
                                            name="discount_amount_additional[{{ $property['property_id'] }}]"
                                            class="form-control discount-2"
                                            value="{{ isset($property['discount_amount_additional']) ? $property['discount_amount_additional'] : '' }}"
                                            @if(!isset($property['selected']) || !$property['selected']) disabled @endif
                                            min="0"
                                            max="{{ $property['discount_amount_additional'] ?? 0 }}"
                                        />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Sub Total:</td>
                                <td align="end" class="addons-sub-total">Rs. {{ number_format(($property['total_additional_charges'] ?? 0) - (isset($property['discount_amount_additional']) ? $property['discount_amount_additional'] : 0)) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Total Taxable Amount:</th>
                                <td align="end" class="total-taxable-amount">Rs. {{ number_format(($property['total_taxable_amount'] ?? 0) - (isset($property['discount_amount']) ? $property['discount_amount'] : 0) - (isset($property['discount_amount_additional']) ? $property['discount_amount_additional'] : 0)) }}</td>
                            </tr>
                            <tr>
                                <td>GST ( {{ $property['tax_percentage'] ?? 0 }}%):</td>
                                <td align="end" class="tax-amount">Rs. {{ number_format($property['tax_amount'] ?? 0) }}</td>
                            </tr>
                            <tr>
                                <td>Total Amount Payable:</td>
                                <td align="end" class="final-price">Rs. {{ number_format(($property['price'] ?? 0) - (isset($property['discount_amount']) ? $property['discount_amount'] : 0) - (isset($property['discount_amount_additional']) ? $property['discount_amount_additional'] : 0)) }}</td>
                            </tr>
                            <input type="hidden" class="original-subtotal" value="{{ $property['sub_total'] ?? 0 }}">

                            <input type="hidden" class="base-price-cal" value="{{ $property['base_price'] ?? 0 }}">
                            <input type="hidden" class="extra-guest-charge-cal" value="{{ $property['total_extra_guest_charge'] ?? 0 }}">






                            <input type="hidden" class="additional-charges" value="{{ $property['total_additional_charges'] ?? 0 }}">

                            <input type="hidden" class="tax-percentage" value="{{ $property['tax_percentage'] ?? 0 }}">
                            <input type="hidden" class="property-pType" value="{{ $property['pType'] ?? '' }}">
                            <input type="hidden" class="property-name" value="{{ $property['unit_name'] ?? '' }}">
                            <input type="hidden" class="property-name" value="{{ $property['unit_name'] ?? '' }}">
                            <input type="hidden" class="booking-quotation-property-id" value="{{ $property['booking_quotation_property_id'] ?? '' }}">
                            @if (!empty($property['additionalCharge']) && count($property['additionalCharge']) > 0)
                                <input type="hidden" class="additionalCharge" value='@json($property['additionalCharge'])'>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning text-center">No properties found.</div>
@endif
<script>
$(document).ready(function () {
    // Toggle details form visibility
    function toggleDetailsForm() {
        if ($('.property-checkbox:checked').length > 0) {
            $('#details-form-wrapper').slideDown();
        } else {
            $('#details-form-wrapper').slideUp();
        }
    }

    // Update calculations for a card
    function updateCardCalculations($card, isManualUpdate = false) {
    const baseSubtotal = parseFloat($card.find('.original-subtotal').val()) || 0;
    const additionalCharges = parseFloat($card.find('.additional-charges').val()) || 0;
    const taxPercentage = parseFloat($card.find('.tax-percentage').val()) || 0;
    const discount1 = parseFloat($card.find('.discount-1').val()) || 0;
    const discount2 = parseFloat($card.find('.discount-2').val()) || 0;
    const bookingQuotationPropertyId = $card.find('.booking-quotation-property-id').val();


    const basePriceCal = parseFloat($card.find('.base-price-cal').val()) || 0;
    const extraGuestChargeCal = parseFloat($card.find('.extra-guest-charge-cal').val()) || 0;
    const baseCalSub = basePriceCal + extraGuestChargeCal;


       let totalAddonsPrice = 0;
        $card.find('.addon-price').each(function () {
            totalAddonsPrice += parseFloat($(this).data('addon-price')) || 0;
        });
        let enableSubmitButton = true;

    // Clear any old errors
    $card.find('.discount-error').remove();

    // Validation
    if (discount1 > baseCalSub) {
        $card.find('.discount-1').after('<div class="text-danger discount-error mt-1">Discount cannot exceed Rs. ' + baseCalSub.toLocaleString() + '</div>');
        enableSubmitButton = false;  // Disable submit button
    }
    if (discount2 > totalAddonsPrice) {
        $card.find('.discount-2').after('<div class="text-danger discount-error mt-1">Discount cannot exceed Rs. ' + totalAddonsPrice.toLocaleString() + '</div>');
        enableSubmitButton = false; 
    }

    if (enableSubmitButton) {
            $('#submitButton').prop('disabled', false);
        } else {
            $('#submitButton').prop('disabled', true);
        }

    // Logic for base subtotal
    let updatedBaseSubtotal = (bookingQuotationPropertyId && !isManualUpdate)
        ? baseSubtotal
        : baseCalSub - discount1;

    if (updatedBaseSubtotal < 0) updatedBaseSubtotal = 0;

    // Logic for addons
    let updatedAdditionalCharges = (bookingQuotationPropertyId && !isManualUpdate)
        ? additionalCharges
        : totalAddonsPrice - discount2;

    if (updatedAdditionalCharges < 0) updatedAdditionalCharges = 0;

    const totalTaxableAmount = updatedBaseSubtotal + updatedAdditionalCharges;
    const taxAmount = (totalTaxableAmount * taxPercentage) / 100;
    const finalPrice = totalTaxableAmount + taxAmount;

    // DOM update
    $card.find('.sub-total').text('Rs. ' + updatedBaseSubtotal.toLocaleString());
    $card.find('.addons-sub-total').text('Rs. ' + updatedAdditionalCharges.toLocaleString());
    $card.find('.total-taxable-amount').text('Rs. ' + totalTaxableAmount.toLocaleString());
    $card.find('.tax-amount').text('Rs. ' + taxAmount.toLocaleString(undefined, { maximumFractionDigits: 2 }));
    $card.find('.final-price').text('Rs. ' + finalPrice.toLocaleString(undefined, { maximumFractionDigits: 2 }));
}


   // When checkbox changes
$('.property-checkbox').on('change', function () {
    const $card = $(this).closest('.card');
    const isChecked = $(this).is(':checked');

    $card.find('.discount-1, .discount-2').prop('disabled', !isChecked);

    if (!isChecked) {
        $card.find('.discount-1, .discount-2').val('');
        $card.find('.discount-error').remove();
        updateCardCalculations($card, true); // treat as manual reset
    } else {
        updateCardCalculations($card);
    }

    toggleDetailsForm();
});

// When user types in discount
$(document).on('input', '.discount-1, .discount-2', function () {
    const $card = $(this).closest('.card');
    const isChecked = $card.find('.property-checkbox').is(':checked');
    if (isChecked) {
        updateCardCalculations($card, true); // Manual input = true
    }
});

   

   // Initialize pre-checked cards on load (edit case)
$('.property-checkbox:checked').each(function () {
    const $card = $(this).closest('.card');
    updateCardCalculations($card); // Don't pass true here
});

toggleDetailsForm();

});
</script>