@extends('website.layouts.app')
@section('content')
<style>
    [type="radio"]:checked,
    [type="radio"]:not(:checked) {
        position: absolute;
        left: -9999px;
    }
    [type="radio"]:checked + label,
    [type="radio"]:not(:checked) + label
    {
        position: relative;
        padding-left: 28px;
        cursor: pointer;
        line-height: 20px;
        display: inline-block;
        color: #666;
    }
    [type="radio"]:checked + label:before,
    [type="radio"]:not(:checked) + label:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 20px;
        height: 20px;
        border: 2px solid #000000;
        border-radius: 100%;
        background: #fff;
    }
    [type="radio"]:checked + label:after,
    [type="radio"]:not(:checked) + label:after {
        content: '';
        width: 10px;
        height: 10px;
        background: #000000;
        position: absolute;
        top: 5px;
        left: 5px;
        border-radius: 100%;
        -webkit-transition: all 0.2s ease;
        transition: all 0.2s ease;
    }
    [type="radio"]:not(:checked) + label:after {
        opacity: 0;
        -webkit-transform: scale(0);
        transform: scale(0);
    }
    [type="radio"]:checked + label:after {
        opacity: 1;
        -webkit-transform: scale(1);
        transform: scale(1);
    }
</style>
<div class="page-wrapper cms-pages">
    <section class="section pb-0">
        <div class="container">
            <div class="section-heading">
                <div class="row">
                    <div class="col">
                        <div class="row gx-3 align-items-baseline">
                            <div class="col-12 col-lg-auto">
                                <div class="back-nav">
                                    <a href="{{ route('quotation-property-detail', [
                    'slug' => base64_encode($bookingQuotationProperty->id),
                    'ptype' => $propertyDetail->ptype,
                ]) }}" class="icon-link icon-link-hover back-link h2">
                                        <i class="icon-arrow-left"></i>
                                        <span class="d-lg-none">Confirm your booking</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-lg d-none d-lg-block">
                                <h2>Confirm your booking</h2>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="section-booking-header booking-confirmation-header bg-white section-pb-40">
        <div class="container">
            <div class="booking-header p-4 mt-0">
                <div class="row">
                    <div class="col-6 pt-2">
                        <h1>{{ $propertyDetail->unit_name_website ?? '' }}</h1>
                        <div class="tags">
                            <div class="row g-2">
                                @if($propertyDetail->tags->isNotEmpty())
                                  @foreach($propertyDetail->tags as $image)
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" class="card-tag">
                                              <img src="{{ isset($image->tags_image) && $image->tags_image ? url('storage/home/images/' . $image->tags_image) : '' }}" alt="">
                                              <span>{{ $image->tags_name ?? '' }}</span>
                                         </a>
                                    </div>
                                  @endforeach
                                @endif
                                
                            </div>
                        </div>
                        <div class="fs-12">
                            {{ isset($bookingQuotationProperty->bookingQuotationDetail->checkin_date, $bookingQuotationProperty->bookingQuotationDetail->checkout_date) ? \Carbon\Carbon::parse($bookingQuotationProperty->bookingQuotationDetail->checkin_date)->format('d') . '-' . \Carbon\Carbon::parse($bookingQuotationProperty->bookingQuotationDetail->checkout_date)->format('d M') : '' }}
                             &bull; {{ isset($bookingQuotationProperty->bookingQuotationDetail->no_of_nights) ? $bookingQuotationProperty->bookingQuotationDetail->no_of_nights . ' ' . ($bookingQuotationProperty->bookingQuotationDetail->no_of_nights == 1 ? 'night' : 'nights') : '' }} 
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="table-subtotal">
                            <table class="table mb-0 table-borderless">
                                <tr class="first-tr">
                                    <td>&#8377;<span class="PricePerNight">{{ number_format($bookingQuotationProperty->per_night_price) }}</span> 
                                            x <span class="totalNight">{{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights }}</span></td>
                                    <td align="right">&#8377;<span class="PriceWithPerNight">{{ number_format($bookingQuotationProperty->basePrice) }} </td>
                                </tr>
                                @if($bookingQuotationProperty->extra_guest_charge)
                                <tr class="second-tr">
                                    <td> Extra charge </td>
                                    
                                <td align="right">&#8377;<span class="totalExtraGuestCharge">{{ number_format($bookingQuotationProperty->extra_guest_charge) }}</span></td>
                                </tr>
                                @endif
                                @if($bookingQuotationProperty->discountAmount)
                                <tr>
                                    <td class="text-link">Discount</td> 
                                    <td align="right" class="text-link">-&#8377;{{ $bookingQuotationProperty->discountAmount }}</td>
                                     
                                </tr>
                                @endif

                                

                                <tr class="fw-bold">
                                    <td>Sub Total</td>
                                    <td align="right">₹{{ formatIN($bookingQuotationProperty->total_amount) }}</td>
                                </tr>


                                @if($bookingQuotationProperty->total_pet_charge)
                                    <tr class="second-tr">
                                        <td> Pet Fees </td>
                                        <td align="right">&#8377;<span class="totalExtraGuestCharge">{{ number_format($bookingQuotationProperty->total_pet_charge) }}</span></td>
                                    </tr>
                                @endif

                            
                                @php
                                    $charges = $bookingQuotationProperty->additional_charges_detail ?? [];
                                @endphp
                                @if (!empty($charges) && is_array($charges))
                                    @foreach ($charges as $charge)
                                        @php
                                            $amount =
                                                $charge['type_option'] ?? '' === 'Per_Stay'
                                                    ? $charge['price'] ?? 0
                                                    : $charge['final_additional_charge'] ??
                                                        ($charge['final_price'] ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="text-link">{{ $charge['name'] ?? 'Unknown Charge' }}</td> 
                                            <td align="right" class="text-link">&#8377;{{ $amount }}</td>
                                        </tr>
                                    @endforeach
                                @endif


                                
                                 
                                

                                @if($bookingQuotationProperty->adOnsDiscountAmount)
                                <tr>
                                    <td class="text-link">Add on discount</td> 
                                    <td align="right" class="text-link">-&#8377;{{ $bookingQuotationProperty->adOnsDiscountAmount }}</td> 
                                </tr>
                                @endif

                                <tr class="fw-bold">
                                    <td >Total Taxable Amount</td> 
                                    <td align="right" >&#8377;{{  number_format($bookingQuotationProperty->taxable_amount)  }}</td> 
                                </tr>
        
                                <tr>
                                    <td>Govt. Taxes (<span class="tax">{{ $bookingQuotationProperty->gst ?? '' }}</span>%)</td>
                                    <td align="right">&#8377;<span class="taxAmount">{{ number_format($bookingQuotationProperty->gst_amount) }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <table class="table mb-0 table-borderless">
                            <tr class="fw-bold">
                                <td>Total incl. taxes</td>
                                <td align="right"><td align="right">&#8377;<span class="TotalAmount">{{ number_format($bookingQuotationProperty->payable_amount) }}</span></td>
                            </tr>
                        </table>
                        <!-- <div class="promo-link text-center">
                            <a href="javascript:void(0)" data-custom-fancy data-src="#coupon">Have a promo code?</a>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="section py-0">
        <div class="container">
            <div class="card-info">
                <div class="row">
                    <div class="col-12">
                        <h3 class="ci-title">Your information</h3>
                    </div>
                    <div class="col-12">
                        <form action="" id="yourFormId">
                             @csrf
                            
                            <input type="hidden" name="property_id" id="property_id"
                                        value="{{ $propertyDetail->id }}">
                                    <input type="hidden" name="ptype" id="ptype"
                                        value="{{ $propertyDetail->ptype }}">
                                    <input type="hidden" name="booking_quotation_id"
                                        value="{{ $bookingQuotationProperty->booking_quotation_id }}">
                                    <input type="hidden" name="website_markup_price"
                                        value="{{ $bookingQuotationProperty->website_markup_price }}">
                                    <input type="hidden" name="num_formatted_tot_price"
                                        value="{{ number_format($bookingQuotationProperty->payable_amount) }}">
                                    <input type="hidden" name="discountAmount"
                                        value="{{ $bookingQuotationProperty->discountAmount }}">
                                    <input type="hidden" name="formatted_total_taxable_amount"
                                        value="{{ number_format($bookingQuotationProperty->gst_amount) }}">
                                    <input type="hidden" name="childrenCount"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->no_children }}">
                                    <input type="hidden" name="adultsCount"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->no_adults }}">
                                    <input type="hidden" name="ci_date"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->checkin_date }}">
                                    <input type="hidden" name="co_date"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->checkout_date }}">
                                    <input type="hidden" name="price_per_night_num_formatted"
                                        value="{{ number_format($bookingQuotationProperty->per_night_price) }}">
                                    <input type="hidden" name="tot_no_of_days"
                                        value="{{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights }}">
                                    <input type="hidden" name="tax" value="{{ $bookingQuotationProperty->gst }}">
                                    <input type="hidden" name="base_price"
                                        value="{{ $bookingQuotationProperty->basePrice }}">
                                    <input type="hidden" name="total_price"
                                        value="{{ $bookingQuotationProperty->payable_amount }}">


                                      <input type="hidden" name="channel" id="channel" value="Quotation">




                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="form-group cs-input">
                                        <label for="">First Name</label>
                                        <input type="text" name="first_name" id="first_name"  placeholder="First Name"  class="form-control " value="{{ $userData->name ?? ''}}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group cs-input">
                                        <label for="">Last Name</label>
                                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="{{ $userData->last_name ?? ''}}">
                                    </div>
                                </div>
                                <!-- <div class="col-12 col-md-6">
                                    <div class="form-group cs-input">
                                        <label for="">Phone</label>
                                        <input type="text" class="form-control " name="phone_number" id="phone_number" placeholder="Phone Number" value="{{ $userData->mobile_no ?? ''}}">
                                        <p></p>
                                    </div>
                                </div> -->

                                 <!-- @php $countries = DB::table('countries')->get(); @endphp
                                <div class="col-12 col-md-6">
                                    <div class="form-group cs-input">
                                        <label for="">Phone</label>

                                        <div class="input-group">
                                            <select id="countryCode"
                                                class="form-select country-select"
                                                name="country_code">
                                                @foreach ($countries as $country)
                                                <option value="{{ $country->phonecode }}"
                                                    {{ $country->phonecode == 91 ? 'selected' : '' }}>
                                                    {{ $country->iso }} (+{{ $country->phonecode }})
                                                </option>
                                                @endforeach
                                            </select>

                                            <input type="text"
                                                name="phone_number"
                                                id="phone_number"
                                                class="form-control"
                                                placeholder="Mobile Number"
                                                required value="{{ $userData->mobile_no ?? ''}}">
                                        </div>

                                        <div class="invalid-feedback" id="mobileError"></div>
                                    </div>
                                </div> -->


                                @php $countries = DB::table('countries')->get(); @endphp
                                 <div class="col-12 col-md-6">
                                    <div class="row form-group d-flex gap-2">
                                        <label for="">Phone</label>

                                        <div class="col-auto">
                                            <select id="countryCode"
                                                class="form-select country-select"
                                                name="country_code">
                                                @foreach ($countries as $country)
                                                <option value="{{ $country->phonecode }}"
                                                    {{ $country->phonecode == 91 ? 'selected' : '' }}>
                                                    {{ $country->iso }} (+{{ $country->phonecode }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                         <div class="col">
                                            <input type="text"
                                                name="phone_number"
                                                id="phone_number"
                                                class="form-control"
                                                placeholder="Mobile Number"
                                                required value="{{ $userData->mobile_no ?? ''}}">
                                        
                                         </div>
                                        <div class="invalid-feedback" id="mobileError"></div>
                                    </div>
                                </div> 


                                <div class="col-12 col-md-6">
                                    <div class="form-group cs-input">
                                        <label for="">Email</label>
                                        <input type="email" class="form-control " name="email" id="email" placeholder="Email" value="{{ $userData->email ?? ''}}">
                                        <p></p>
                                        <!--<span class="error">Incorrect Email</span>-->
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group cs-input">
                                        <label for="">Address Line 1</label>
                                        <input type="text" name="address_line_1" id="address_line_1" class="form-control " placeholder="Apartment number, Street" value="{{ $userData->address ?? ''}}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group cs-input">
                                        <label for="">Address Line 2</label>
                                        <input type="text" name="address_line_2" id="address_line_2" class="form-control" placeholder="Sector, Town/city" value="">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group cs-input">
                                        <label for="">City</label>
                                        <input type="text" name="city" id="city" class="form-control  " value="{{ $userData->city ?? ''}}">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group cs-input">
                                        <label for="">Postal Code</label>
                                        <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ $userData->zipcode ?? ''}}">
                                        <!--<span class="error">Incorrect Post Code</span>-->
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group cs-input">
                                        <label for="">Country</label>
                                        <input type="text" name="country" id="country" class="form-control" value="{{ $userData->country_name ?? ''}}">
                                    </div>
                                </div>
                                
                                @if(empty($userData))
                                <div class="col-12">
                                    <div class="form-group cs-input">
                                        <label for=""><b>Do you want to create an account or checkout as guest?</b></label>
                                    </div>
                                </div>

                               
                                <div class="col-12 col-md-4">
                                    <div class="form-group cs-input">
                                        <input type="radio" id="create_account" name="option" value="create_account"
                                            {{ old('option', 'create_account') == 'create_account' ? 'checked' : '' }}>
                                        <label for="create_account" style="color:#000000;">Create a new account</label>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-4">
                                    <div class="form-group cs-input">
                                        <input type="radio" id="checkout_guest" name="option" value="checkout_guest"
                                            {{ old('option') == 'checkout_guest' ? 'checked' : '' }}>
                                        <label for="checkout_guest" style="color:#000000;">Checkout as a guest</label>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="col-12 col-md-4 text-md-end">
                                    <div class="form-group cs-input text-md-end">
                                         <a href="
                                         {{ route('customer.login', ['redirect' => request()->fullUrl()]) }}"style="text-decoration:none">Already have an account? Sign In</a>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="col-12 mt-0">
                                    <div class="section-pt-40">
                                        <div class="row gy-3 flex-column-reverse flex-md-row">
                                            <div class="col-12 col-md-6">
                                                <div class="row flex-row-reverse flex-md-row gx-3 align-items-center justify-content-center">
                                                    <div class="col-auto">
                                                        <ul class="payment-list">
                                                            <li>
                                                                <img src="{{ asset('assets/website/images/phonepe.png') }}" alt="Name">
                                                            </li>
                                                            <li>
                                                                <img src="{{ asset('assets/website/images/paytm.png') }}" alt="Name">
                                                            </li>
                                                            <li>
                                                                <img src="{{ asset('assets/website/images/google.png') }}" alt="Name">
                                                            </li>
                                                            <li>
                                                                <img src="{{ asset('assets/website/images/card.png') }}" alt="Name">
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-auto col-md">
                                                        <div class="fs-12 text-gray-500 fw-medium">Pay securely <span class="d-none d-md-inline">with UPI, Debit & Credit Cards</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group cs-input">
                                                    <button type="button" id="payNowcreate"   class="btn btn-primary icon-link icon-link-hover w-100">
                                                        <div class="group">
                                                            <span>Pay via</span> 
                                                            <img src="{{ asset('assets/website/images/razorpay.png') }} ">
                                                        </div>
                                                        <i class="bi icon-chevron-right"></i>
                                                        <span id="spinner_new_form" class="spinner-border spinner-border-sm " style="color: #fff; display: none;" role="status"  aria-hidden="true" ></span>
                                                    </button> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-none d-lg-block mt-2">
                                    <div class="fs-12 text-gray-500 fw-medium text-center">Your booking details will be shared via email</div>
                                </div>
                            </div>
                        </form>

                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container">
            <div class="row g-equal">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-info">
                        <h3 class="ci-title">Your trip</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="tb-title">Dates</div>
                                            <div>   {{ isset($bookingQuotationProperty->bookingQuotationDetail->checkin_date, $bookingQuotationProperty->bookingQuotationDetail->checkout_date) ? \Carbon\Carbon::parse($bookingQuotationProperty->bookingQuotationDetail->checkin_date)->format('d') . '-' . \Carbon\Carbon::parse($bookingQuotationProperty->bookingQuotationDetail->checkout_date)->format('d M') : '' }}</div>
                                        </td>
                                        <th class="text-end"> 
                                            {{-- <a href="">EDIT</a> --}}
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                           <div class="tb-title">Guests</div>
                                           
                                                @if($bookingQuotationProperty->bookingQuotationDetail->no_adults =='1') 
                                                    {{ $bookingQuotationProperty->bookingQuotationDetail->no_adults }} Adult 
                                                @else {{ $bookingQuotationProperty->bookingQuotationDetail->no_adults }} 
                                                  Adults 
                                                @endif
                                                
                                                @if($bookingQuotationProperty->bookingQuotationDetail->no_children) 
                                                <br>
                                                  {{ $bookingQuotationProperty->bookingQuotationDetail->no_children }} Children 
                                                @endif
                                                @if($bookingQuotationProperty->bookingQuotationDetail->pets) 
                                                <br>
                                                  {{ $bookingQuotationProperty->bookingQuotationDetail->pets }} Pet 
                                                @endif
                                        </td>
                                        <th class="text-end">
                                            {{-- <a href="">EDIT</a> --}}
                                        </th>
                                    </tr>
                                    <tr>
                                        <td colspan="2">{{ $bookingQuotationProperty->bookingQuotationDetail->no_adults + $bookingQuotationProperty->bookingQuotationDetail->no_children }} guests total</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-info">
                        <h3 class="ci-title">Cancellation Policy</h3>
                        <div class="table-responsive">
                            <table class="table">
                                @foreach($CancellationPolicy as $item)
                                <tr>
                                    <td>
                                        <div class="tb-title li-title">  
                                            <!--<i>-->
                                            <!--    {!! $item->iconImage->icons_code ?? '' !!}-->
                                            <!--</i>                                              -->
                                            @if($item->iconImage && !empty($item->iconImage->icons_image))
                                              <img src="{{ asset('storage/icons/' . $item->iconImage->icons_image) }}" style="width: 20px;" alt="icon">
                                            @endif
                                            <span>{{ $item->title }}</span>
                                        </div>
                                        <div>{{ $item->sub_title  }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card-info">
                        <h3 class="ci-title">House Rules</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    @foreach($HouseRules as $item)
                                    <tr>
                                        <td>
                                            <div class="tb-title li-title">  
                                                @if($item->iconImage && !empty($item->iconImage->icons_image))
                                              <img src="{{ asset('storage/icons/' . $item->iconImage->icons_image) }}" style="width: 20px;" alt="icon">
                                            @endif                                             
                                                <span>{{ $item->title }}</span>
                                            </div>
                                            <div>{{ $item->sub_title  }}</div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<style>
    .card-info .tb-title i svg{
        width:20px;
        height: 20px;
    }
</style>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $('#payNowcreate').on('click', function(e) {
        e.preventDefault(); 
        var spinner = document.getElementById('spinner_new_form');
        spinner.style.display = 'inline-block'; 
        
        let formdata = $('#yourFormId').serialize();
        
        // console.log(formdata);
        $.ajax({
            url: "{{ route('property-booking-payment') }}",  
            type: "POST",  
            data: formdata,  
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  
            },
            success: function(response) {
                if (response['status'] == true) {
                    spinner.style.display = 'none';
                    if(response.orderId){
                    let order_id =  response.orderId;
                    let razorpay_id =  response.razorpayId;
                    let amount =  response.amount;
                        var options = {
                            "key": razorpay_id,
                            "amount": amount, 
                            "currency": "INR",
                            "name": "Vendor Payment",
                            "description": "enter text here",
                            "order_id": order_id, 
                            "handler": function (response){
                                console.log(response.razorpay_order_id,"payment done");
                                /* Payment Status */
                                $.ajax({
                                    type:'GET',
                                    url:"{{ route('property-booking-update') }}",
                                    data:{
                                        razorpay_order_id:response.razorpay_order_id,
                                        razorpay_payment_id:response.razorpay_payment_id
                                    },
                                    success:function(data){
                                        window.location.replace("{{ route('payment.thankYou') }}?orderId=" + data.orderId);
                                    }
                                });
                            },
                            "prefill": {
                                "name": "",
                                "email": ""
                            },
                            "theme": {
                                "color": "#3399cc"
                            },
                            "modal": {
                                "ondismiss": function () {
                                    window,location.replace("{{ route('payment-failure') }}");

                                }
                            }
                        };
                        var rzp = new Razorpay(options);
                        rzp.open();
                        rzp.on('payment.failed', function (response){
                            window,location.replace("{{ route('payment-failure') }}");
                        });
                    }
                }
                else {
                    spinner.style.display = 'none';
                    if (response.status === false && response.redirect) {
                    window.location.href = response.redirect;
                    }
                    
                    if (response.message_email) {
                        $('#email').addClass('border-danger'); 
                        $('#email').next('p').addClass('text-danger').html(response.message_email); 
                    }
                    
                    var errors = response['errors']; 
                    if (errors) {
                        $.each(errors, function(key, value) {
                            console.log(value[0], "value[0]"); 
                            var elementId = key.replace(/\./g, '_'); 
                            console.log(elementId, "key");
                            var inputElement = $('#' + elementId);
                            inputElement.addClass('border-danger');  
                           // inputElement.next('p').addClass('text-danger').html(value[0]);
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);  // Log error if the AJAX request fails
            }
        });
    });
    $(document).ready(function() {
        $('#first_name').on('input', function() {
            $('#first_name').removeClass('border-danger').html('');
        });
        $('#last_name').on('input', function() {
            $('#last_name').removeClass('border-danger').html('');
        });
        // $('#phone').on('input', function() {
        //     $('#phone').removeClass('border-danger').html('');
        // });
        $('#email').on('input', function() {
            $('#email').removeClass('border-danger').html('');
            $(this).next('p').removeClass('text-danger').html('');
        });
        $('#city').on('input', function() {
            $('#city').removeClass('border-danger').html('');
        });
        $('#address_line_1').on('input', function() {
            $('#address_line_1').removeClass('border-danger').html('');
        });
        $('#address_line_2').on('input', function() {
            $('#address_line_2').removeClass('border-danger').html('');
        });
        $('#postal_code').on('input', function() {
            $('#postal_code').removeClass('border-danger').html('');
        });
        $('#country').on('input', function() {
            $('#country').removeClass('border-danger').html('');
        });
    });
     function toggleButtonState() {
        var phoneNumber = $('#phone').val();
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '').slice(0, 12);
        if(phoneNumber[0] == '0'){
            $('#phone').addClass('border-danger');
            $('#phone').siblings('p').addClass('text-danger').html("Phone number can't start with 0.");
            
        }else{
            $('#phone').siblings('p').addClass('text-danger').html("");
            $('#phone').removeClass('border-danger').html('');
        }
        $('#phone').val(phoneNumber);
    }
    $('#phone').on('input', function() {
        toggleButtonState();
    }); 
</script>
@endsection