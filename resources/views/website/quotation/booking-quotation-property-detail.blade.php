@extends('website.layouts.app')
@section('content')
<style>
    .strike{
        position: relative;
        display: inline-block;
    }
    .strike:after{position: absolute; content:''; width: 100%; height: 1px; background: #ff0000; top: 50%; left: 0px;}

    .status-text {
        font-size: 13px;
        color: #28a745;
        margin-left: auto;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .share-item.copied .status-text {
        opacity: 1;
    }

    .share-item.copied .text {
        opacity: 0.6;
    }
    </style>
<div class="page-wrapper cms-pages">
    <!--- For mobile -->
    <section class="section pb-0 d-lg-none">
        <div class="container">
            <div class="section-heading mb-0">
                <div class="row">
                    <div class="col">
                        <div class="row gx-3 align-items-baseline">
                            <div class="col-12 col-lg-auto">
                                <div class="back-nav mb-0">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <a href="/" class="icon-link icon-link-hover back-link h2">
                                                <i class="bi icon-arrow-left"></i>
                                                <span class="d-lg-none">Back</span>
                                            </a>
                                        </div>
                                        <div class="col">
                                                <a href="javascript:void(0)" class="icon-link icon-link-hover back-link h2 justify-content-end " data-custom-fancy data-close-button="false" data-src="#share">
                                                <i class="icon-share"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--- For mobile -->
    <!--- For Desktop -->
    <div class="section-booking-header list-booking-header bg-white" id="scroll-fixed">
        
            <div class="container">
                <div class="booking-header p-4">
                    <div class="row">
                        <div class="col-6 pt-2">
                            <h1>{{ isset($property->unit_name_website) ? $property->unit_name_website : '' }}</h1>
                            <div class="tags">
                                <div class="row g-2">
                                    @if($property->tags->isNotEmpty())
                                    @foreach($property->tags as $image)
                                        <div class="col-auto">
                                            <a href="#" class="card-tag">
                                                <img src="{{ isset($image->tags_image) && $image->tags_image ? url('storage/home/images/' . $image->tags_image) : '' }}" alt="">
                                                <span>{{ $image->tags_name ?? '' }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                @endif

                                </div>
                            </div>
                            <div class="bh-price fw-bold">
                                <!--&#8377;<span class="PricePerNight">{{ number_format($property->per_night_price) }}</span> <small class="fw-normal">per night</small>-->
                                @if($bookingQuotationProperty->payable_amount >0)
                                @if(round($bookingQuotationProperty->discounted_per_night_price) < round($bookingQuotationProperty->current_per_night_price))
                                <span class="strike">&#8377;{{ number_format($bookingQuotationProperty->current_per_night_price) }} /night</span></small> 
                                @endif
                                <strong class="text-primary">INR {{ number_format($bookingQuotationProperty->discounted_per_night_price) }} /night</strong>
                            @else
                                
                                @if(round($bookingQuotationProperty->discounted_per_night_price) < round($bookingQuotationProperty->current_per_night_price))
                                <span class="strike">&#8377;{{ number_format($bookingQuotationProperty->current_per_night_price) }} /night</span></small> 
                                @endif
                                <strong class="text-primary">&#8377;0 /night</strong>
                            @endif
                            </div>
                            <div class="main-search-outer detail-page-search ms-0">
                                <div class="main-search">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-8 field-col d-flex position-relative">
                                            <button class="btn text-start btn-checkin toggle-date search-field">
                                                <strong>Check in</strong>
                                                <div class="search-field-value js-checkin-text"> {{ date('jS M', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkin_date)) ?? 'Add dates' }}</div>
                                                <span class="clear-dates">&times;</span>                         
                                            </button>
                                            <button class="btn text-start btn-checkout toggle-date search-field">
                                                <strong>Check out</strong>
                                                <div class="search-field-value js-checkout-text-page-detail">{{ date('jS M', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkout_date)) ?? 'Add dates' }}</div>
                                                <span class="clear-dates">&times;</span>
                                            </button>
                                            <!--fill calendra-->
                                            <div class="custom-dropdown calendar-dropdown">
                                                <input id="detail-page-calendar" type="text" style="display:none;" value="@if($bookingQuotationProperty->bookingQuotationDetail->checkin_date){{ date('Y-m-d', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkin_date)) . ' - ' . date('Y-m-d', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkout_date)) }}@endif""/>
                                            </div>
                                        </div>
                                        <div class="col field-col position-relative">
                                            <button class="btn text-start search-field ">
                                                <strong>Guests </strong>
                                                 <div class="search-field-value-detail add-guests-detail-value" style="color: #8b8b8b; font-size: 1rem;  line-height: 1; padding-top: 2px;">Add guests</div>
                                            </button>   
                                            <div class="custom-dropdown guests-counter guests-dropdown" style="box-shadow:none">
                                                <input type="hidden" name="capacity" id="capacity" value="{{ $property->guests_included ?? '' }}">
                                                <input type="hidden" name="max_capacity" id="max_capacity" value="{{ $property->maximum_number_of_guests ?? '' }}">
                                                <input type="hidden" name="extra_guest_charges" id="extra_guest_charges" value="">
                                                <input type="hidden" name="total_extra_guest_charge" id="total_extra_guest_charge" value="">
                                                <input type="hidden" name="totalGuests" id="totalGuests">

                                                
                                            </div>
                                        </div>            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="table-subtotal">
                                <table class="table mb-0 table-borderless">
                                    <tr class="first-tr">
                                        <td>&#8377;<span class="PricePerNight">{{ number_format($bookingQuotationProperty->per_night_price) }}</span> 
                                                x <span class="totalNight">{{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights  }}</span></td>
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
                                        <td >Sub Total</td> 
                                        <td align="right" >&#8377;{{ number_format($bookingQuotationProperty->total_amount) }}</td> 
                                    </tr>
                                    @if($bookingQuotationProperty->total_pet_charge)
                                        <tr class="second-tr">
                                            <td> Pet Fees </td>
                                            <td align="right">&#8377;<span class="totalExtraGuestCharge">{{ number_format($bookingQuotationProperty->total_pet_charge) }}</span></td>
                                        </tr>
                                    @endif
                                    <!-- @if($property->addon_total_amount)
                                    <tr class="second-tr">
                                        <td> Cleaning Fees</td>
                                        <td align="right">&#8377; <span class="totalExtraGuestCharge">{{ number_format($property->addon_total_amount - $property->total_pet_charge ) }} </td>
                                    </tr>
                                    @endif -->


                                   <!--  @if(!empty($bookingQuotationProperty))
                                        @foreach($bookingQuotationProperty->additional_charges_detail as $charge)
                                        <tr class="second-tr">
                                            <td>{{ $charge['name'] }}</td>
                                            <td align="right">₹ <span class="totalExtraGuestCharge">{{ number_format($charge['price']) }}</span></td>
                                        </tr>
                                        @endforeach
                                    @endif -->

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



                                

                                    <!-- @if($property->adOnsDiscountAmount)
                                    <tr>
                                        <td class="text-link">Add on discount</td> 
                                        <td align="right" class="text-link">-&#8377; {{ $property->adOnsDiscountAmount }}</td> 
                                    </tr>
                                    @endif -->

                                    @if($bookingQuotationProperty->adOnsDiscountAmount)
                                        <tr>
                                            <td class="text-link">Add on discount</td>
                                            <td align="right" class="text-link">-₹ {{ $bookingQuotationProperty->adOnsDiscountAmount }}</td>
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
                            {{-- <div class="promo-link text-center">
                                <a href="javascript:void(0)" data-src="#coupon" data-custom-fancy id="coupon-href">Have a promo code?</a>
                            </div> --}}
                            <!---------------Discount--------------->
                            <form action="#">
                                @csrf
                                <div class="section-fancybox half-fancybox mxw-600" id="coupon" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="fancy-heading">
                                                <div class="row">
                                                    <div class="col">
                                                        <h3>Apply Promo Code</h3>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href="javascript:void(0)" class="fancy-close" onclick="Fancybox.close()">
                                                            <i class="icon-close"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <div class="form-group cs-input">
                                                        <label for="">Enter your promo code</label>
                                                        <input type="hidden" name="discount_amount" id="discount_amount"/>
                                                        <input type="hidden" name="discount" id="discount"/>
                                                        <input type="text" class="form-control text-uppercase" name="coupon_code" id="coupon_code">
                                                    </div>
                                                    <span class="error justify-content-center coupon_error " ></span>
                                                </div>
                                
                                                <div class="col-12">
                                                    <div class="form-group cs-input bottom-toolbar">
                                                        <div class="row align-items-center">
                                                            <div class="col-auto d-md-none">
                                                                <a href="javascript:void(0)" class="clear-btn">Clear</a>
                                                            </div>
                                                            <div class="col text-end">
                                                                <button type="submit" class="btn btn-primary icon-link icon-link-hover w-md-100 apply_coupon" disabled>Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>       
                            <!------------------End of Discount-------------->
                            <div class="reserve-button">
                                <a href="{{ route('quotation-property-book', ['ptype' => $property->ptype, 'slug' => base64_encode($bookingQuotationProperty->id)]) }}"
                                 class="btn w-100 btn-primary icon-link icon-link-hover ">Make a Reservation <i class="bi icon-chevron-right"></i></a>  
                            </div>

                            



                            <div class="note small text-center">You will be charged on the next screen</div>
                        </div>
                    </div>
                </div>
            </div>
         
    </div>
    <!--- For Desktop -->
    <!--- For Mobile -->
    <section class="section swiper-property-image section-mobile-gallery pt-0">
        <div class="container-fluid px-0">
            <div class="swiper swiper-gallery">
                <div class="swiper-wrapper">
                    @if($property->imagesWebsite->isNotEmpty())
                        @foreach($property->imagesWebsite as $image)
                            <div class="swiper-slide">
                                <a href="javascript:void(0)" data-gallery-type="all">
                                    <img src="{{ asset($image->website_image ?? '') }}" alt="{{ $image->title }}">
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <!--- For Mobile -->
    <!--- For Desktop -->
    <section class="section section-property-gallery">
        <div class="container">
            <div class="row g-equal">
                @if($property->imagesWebsite->isNotEmpty())
                    {{-- Display the first image --}}
                    <div class="col-12 col-lg-6">
                        <a href="#" class="gallery-card" data-gallery-type="{{$property->imagesWebsite->first()->title}}">
                            <img src="{{ asset($property->imagesWebsite->first()->website_image ?? '') }}" alt="Living Room">
                            @if($property->imagesWebsite->first()->title)
                               <span>{{ ($property->imagesWebsite->first()->title ?? '') }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="row g-equal">
                            @php
                                $firstImageId = $property->imagesWebsite->first()->id;
                                $groupedImages = collect($property->imagesWebsite)
                                                ->groupBy('title');
                                $displayCount = 0;                   
                            @endphp                     
                    
                            @foreach($groupedImages as $title => $images)
                                
                                @php $image = $images->first(); @endphp
                                @if($firstImageId != $image->id && $displayCount < 3)
                                    <div class="col-6">
                                        <a href="#" class="gallery-card" data-gallery-type="{{ $title }}">
                                            <img src="{{ asset($image->website_image ?? '') }}" alt="{{ $title }}">
                                            
                                            @if($title)
                                                <span>{{ $title }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    @php $displayCount++; @endphp
                                @endif
                            @endforeach
                            
                            {{-- "See All" badge at the end --}}
                            <div class="col-6">
                                <a href="#" class="gallery-card" data-gallery-type="all">
                                    <img src="{{ asset($property->imagesWebsite->last()->website_image ?? '') }}" alt="See All">
                                    <span class="badge-bottom"><i class="icon-image me-2 lh-1"></i>See All ({{ $property->imagesWebsite->count() }})</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <p>No images available</p>
                @endif
            </div>
        </div>
    </section>
    <!--- For Desktop -->
    <section class="section section-checkin-info pt-0">
        <div class="container">
            <h3>Entire {{ isset($property->home_type) ? $property->home_type : '' }} in  {{ isset($property->location) ? $property->location : '' }}, {{ isset($property->state) ? $property->state : '' }} </h3>
            <p class="text-darkgray mb-0">
                {{ isset($property->guests_included) ? $property->guests_included : '' }} Guests &bull; 
                {{ isset($property->no_of_bedrooms) ? $property->no_of_bedrooms : '' }} Bedrooms &bull; 
                {{ isset($property->no_of_bathrooms) ? $property->no_of_bathrooms : '' }} Bathrooms  
                @if(!empty($property->property_size))
                &bull;{{ isset($property->property_size) ? $property->property_size : '' }}
                @endif
                
            </p>
            <ul class="list-unstyled mb-0">
                <li>
                    <div class="row gx-2 gx-md-3">
                        <div class="col-auto">
                            <i class="icon-timer"></i>
                        </div>
                        <div class="col">
                            <strong>
                            Check in at {{ date('g:iA', strtotime($property->checkin_time)) }}. 
                            Check out at {{ date('g:iA', strtotime($property->checkout_time)) }}.
                            </strong>
                            <p class="text-darkgray">Please contact us after your booking for any questions.</p>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="row gx-2 gx-md-3">
                        <div class="col-auto">
                            <i class="icon-self-check"></i>
                        </div>
                        <div class="col">
                            <strong>Self check-in</strong>
                            <p class="text-darkgray">Check yourself in using the lockbox.</p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </section>
    <section class="section section-about-property pt-0">
        <div class="container">
            <div class="row gx-equal gy-5">
                <div class="col-12 col-lg-6">
                    <div class="card-info card-about">
                        <h3 class="ci-title">About the listing</h3>  
                        <div class="content">
                        {!! isset($property->description) ? $property->description : '' !!}
                        </div>
                        <ul class="amenities-list list-unstyled m-0">
                            @if(!empty($property->websiteamenities) && $property->websiteamenities->isNotEmpty())
                            @foreach($property->websiteamenities as $amenitie)
                                <li>
                                    {{-- <i class="icon-smart-tv"></i> --}}
                                    <div class="amenities-small-icon"><img src="{{ isset($amenitie->amenities_image) ? asset('storage/amenities/' . $amenitie->amenities_image) : '' }}" alt=""></div>
                                    <span>{{ isset($amenitie->amenities_name) ? $amenitie->amenities_name : '' }}</span>
                                </li>       
                            @endforeach
                            @endif
                        </ul>
                        <div class="content">
                            <strong>Don’t see something you need?</strong>
                            <p>Please contact us and we will try our best to accommodate your request</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card-info card-about">
                        <h3 class="ci-title">Where you’ll be</h3>  
                        <div class="ci-map border border-light br-8 overflow-hidden">
                            <div class="map-card">
                                <h3>{{ isset($property->state) ? $property->state : '' }}, {{ isset($property->location) ? $property->location : '' }}</h3>
                                <p>{{ isset($property->home->map_text) ? $property->home->map_text : '' }}</p>
                            </div>
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            <div class="section-heading">
                <div class="row">
                    <div class="col">
                        <h3>Frequently Asked Questions</h3>
                    </div>
                </div>
            </div>  
            <div class="row">
                <div class="col-12">
                    <div class="swiper swiper-faqs overflow-visible">
                        <div class="swiper-wrapper">
                            @if(!empty($property->homefaqSection) && $property->homefaqSection->isNotEmpty())
                                @foreach($property->homefaqSection as $faq)
                                    <div class="swiper-slide">
                                        <div class="card-faq">
                                            <div class="card-title">{{ $faq->question ?? '' }}</div>
                                            <div class="card-text text-gray-500">
                                                <p>{{ $faq->answer ?? '' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section pt-0">
        <div class="container">
            <div class="section-heading">
                <div class="row">
                    <div class="col">
                        <h3>Important Information</h3>
                    </div>
                </div>
            </div>  
            <div class="row g-3 g-lg-equal">
                @foreach($importantInformation as $key => $info)
                    <div class="col-4">
                        <a href="javascript:void(0)" class="card-link" data-custom-fancy data-src="#{{ Str::slug($key, '-') }}">
                            <div class="card-icon">
                            <i class="{{ $iconMap[$key] ?? 'icon-default' }}"></i>
                            </div>
                            <div class="card-title">{{ $key }}</div>
                        </a>
                    </div>
                @endforeach
                <div class="col-12">
                    <p class="mb-2">Still have questions?</p>
                    <a href="/customer-support" class="icon-link icon-link-hover">CUSTOMER SUPPORT <i class="bi icon-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </section>
    @foreach($importantInformation as $key => $info)
        <div class="section-fancybox full-fanybox mxw-600" id="{{ Str::slug($key, '-') }}" style="display: none;">
            <div class="row">
                <div class="col-12">
                    <div class="fancy-heading">
                        <div class="row">
                            <div class="col">
                                <h3>{{ $key }}</h3>
                            </div>
                            <div class="col-auto">
                                <a href="javascript:void(0)" class="fancy-close" onclick="Fancybox.close()">
                                    <i class="icon-close"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <ul class="list-unstyled m-0">
                        @foreach($info as $item)
                            <li>
                                <div class="li-title">
                                {{--  {!! $item->icon_image !!} --}}
                                 
                                      @if($item->iconImage && !empty($item->iconImage->icons_image))
                                     <img src="{{ asset('storage/icons/' . $item->iconImage->icons_image) }}" style="width: 20px;" alt="icon">
                                     @endif
                                    
                                    <span>{{ $item->title }}</span>
                                </div>
                                <div class="fancy-content text-gray-500">
                                    <p>{{ $item->sub_title }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
</div>


<div class="section-fancybox full-fanybox mxw-600" id="share" style="display: none;">
    <div class="row">
        <div class="col-12">
            <div class="fancy-heading">
                <div class="row">
                    <div class="col">
                        <h3>Share this place</h3>
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" class="fancy-close" onclick="Fancybox.close()">
                            <i class="icon-close"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $encodedUrl = urlencode($currentUrl);
        @endphp
        <div class="col-12">
            <ul class="list-unstyled share-link-list m-0">
                <li>
                    <a href="javascript:void(0)"
                        class="share-item"
                        onclick="handleInstagramShare(this, '{{ $currentUrl }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M16.7764 0C20.7071 0.00197428 23.8926 3.18797 23.8945 7.11816V16.7754C23.8915 20.7058 20.7068 23.8918 16.7764 23.8955H7.11914C3.18847 23.8925 0.00264616 20.7059 0 16.7754V7.11816C0.00327951 3.18825 3.18903 0.0030411 7.11914 0H16.7764ZM7.11621 2.40332C4.51277 2.40445 2.40312 4.51525 2.40332 7.11816V16.7764C2.40332 19.3801 4.5149 21.4912 7.11914 21.4912H16.7764C19.3804 21.4905 21.4917 19.3794 21.4922 16.7754L21.4912 7.11816C21.4905 4.51437 19.3798 2.40399 16.7764 2.40332H7.11621ZM11.9482 5.76953C15.3565 5.76964 18.1258 8.54046 18.126 11.9473C18.126 15.3542 15.3553 18.1259 11.9482 18.126C8.54109 18.126 5.76953 15.3543 5.76953 11.9473C5.76972 8.5404 8.5399 5.76953 11.9482 5.76953ZM11.9463 8.17383C9.86197 8.17451 8.17298 9.86397 8.17285 11.9482C8.17329 14.0328 9.86363 15.7227 11.9482 15.7227C14.0327 15.7221 15.7216 14.0317 15.7217 11.9473C15.7212 9.86273 14.0309 8.17383 11.9463 8.17383ZM18.1396 4.33496C18.9571 4.33558 19.6195 4.99892 19.6191 5.81641C19.6187 6.63406 18.9544 7.29633 18.1367 7.2959C17.3201 7.29524 16.6586 6.63305 16.6582 5.81641C16.6583 4.99968 17.2121 4.33496 18.1396 4.33496Z" fill="black" />
                        </svg>
                        Instagram
                        <span class="status-text">Link copied</span>
                    </a>
                </li>
                <!-- <li>
                    <a href="javascript:void(0)"
                        class="share-item"
                        onclick="handleInstagramShare('{{ $currentUrl }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M16.7764 0C20.7071 0.00197428 23.8926 3.18797 23.8945 7.11816V16.7754C23.8915 20.7058 20.7068 23.8918 16.7764 23.8955H7.11914C3.18847 23.8925 0.00264616 20.7059 0 16.7754V7.11816C0.00327951 3.18825 3.18903 0.0030411 7.11914 0H16.7764ZM7.11621 2.40332C4.51277 2.40445 2.40312 4.51525 2.40332 7.11816V16.7764C2.40332 19.3801 4.5149 21.4912 7.11914 21.4912H16.7764C19.3804 21.4905 21.4917 19.3794 21.4922 16.7754L21.4912 7.11816C21.4905 4.51437 19.3798 2.40399 16.7764 2.40332H7.11621ZM11.9482 5.76953C15.3565 5.76964 18.1258 8.54046 18.126 11.9473C18.126 15.3542 15.3553 18.1259 11.9482 18.126C8.54109 18.126 5.76953 15.3543 5.76953 11.9473C5.76972 8.5404 8.5399 5.76953 11.9482 5.76953ZM11.9463 8.17383C9.86197 8.17451 8.17298 9.86397 8.17285 11.9482C8.17329 14.0328 9.86363 15.7227 11.9482 15.7227C14.0327 15.7221 15.7216 14.0317 15.7217 11.9473C15.7212 9.86273 14.0309 8.17383 11.9463 8.17383ZM18.1396 4.33496C18.9571 4.33558 19.6195 4.99892 19.6191 5.81641C19.6187 6.63406 18.9544 7.29633 18.1367 7.2959C17.3201 7.29524 16.6586 6.63305 16.6582 5.81641C16.6583 4.99968 17.2121 4.33496 18.1396 4.33496Z" fill="black" />
                        </svg>
                        Instagram
                    </a>
                </li> -->
                <li>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M24 24H19.2V15.6C19.2 13.296 18.183 12.012 16.362 12.012C14.541 12.012 13.2 13.35 13.2 15.6V24H8.4V8.4H13.2V10.155C13.2 10.155 14.706 7.512 18.099 7.512C21.492 7.512 24 9.582 24 13.869V24ZM2.931 5.904C1.311 5.904 0 4.584 0 2.952C0 1.32 1.311 0 2.931 0C4.551 0 5.859 1.323 5.859 2.952C5.859 4.584 4.548 5.904 2.931 5.904ZM0 24H6V8.4H0V24Z" fill="black" />
                        </svg>LinkedIn</a>
                </li>

                <li>
                    <a href="https://wa.me/?text={{$encodedUrl}}" data-action="share/whatsapp/share" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                        </svg>WhatsApp</a>
                </li>
                <li><a href="mailto:?body={{$encodedUrl}}&amp;subject=Title"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z" />
                        </svg>Email</a></li>
            </ul>
        </div>
    </div>
</div>


<!--- For mobile -->
<div class="mobile-search-wrapper container d-lg-none">
    <div class="card mobile-search d-lg-none">
        <div class="card-header">
            <a href="javascript:void(0)" class="m-search-close text-black"><i class="icon-close"></i><span class="fs-6">Search</span></a> 
        </div>
        <div class="card-body pt-0">
            <div class="accordion-wrap">
                <div class="accordion-box active">
                    <div class="accordion-title">
                        <div class="row align-items-center">
                            <div class="col"><h2>When?</h2></div>
                            <div class="col text-end checkIn_chechout_mobile_date ">
                                @if($bookingQuotationProperty->bookingQuotationDetail->checkin_date)
                                {{ \Carbon\Carbon::parse($bookingQuotationProperty->bookingQuotationDetail->checkin_date)->format('jS M') }} to {{ \Carbon\Carbon::parse($bookingQuotationProperty->bookingQuotationDetail->checkout_date)->format('jS M') }}
                                @else
                                Add dates
                                @endif
                                
                            </div>
                        </div>
                    </div>
                    <div class="accordion-data">
                        <input type="text" id="mobile-calender" style="display: none;" value="@if($bookingQuotationProperty->bookingQuotationDetail->checkin_date){{ date('Y-m-d', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkin_date)) . ' - ' . date('Y-m-d', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkout_date)) }}@endif">
                    </div>
                </div>
                <div class="accordion-box">
                    <div class="accordion-title">
                        <div class="row align-items-center">
                            <div class="col"><h2>Who?</h2></div>
                            <div class="col text-end search-field-value-detail add-guests-detail-value"> Add Guests</div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row align-items-center justify-content-between">
                {{-- <div class="col-auto">
                    <a href="" class="clear-mobile-search">Clear All</a>
                </div> --}}
                <div class="col-auto">
                    <button class="btn btn-primary search-next">Next <i class="icon-chevron-right"></i></button>
                    <button style="display:none;" class="btn btn-primary search-submit make-reservation"> Reserve <i class="bi icon-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mobile-reserve-wrap">
    <div class="container">
        <div class="row pb-2">   
            <div class="col">
                <h1 class="h3 mb-0">{{ isset($property->unit_name_website) ? $property->unit_name_website : '' }}</h1>
            </div>

            

            @if($property->tags->isNotEmpty())
            <div class="col-auto">
            @php
            $firstTag = $property->tags->first();
        @endphp
            <a href="#" class="card-tag single-tag">
                    <img src="{{ url('storage/tag/' . $firstTag->tags_image) }}" alt="{{ $firstTag->tags_name }}">
                <span>{{ $firstTag->tags_name ?? '' }}</span>
            </a>
        </div>
        @endif





        </div>
        <div class="mob-expand">
            <div class="tags mb-3">
                <div class="row g-2">
                    @if($property->tags->isNotEmpty())
                        @foreach($property->tags as $image)
                        <div class="col-auto">
                            <a href="#" class="card-tag">
                                    <img src="{{ isset($image->tags_image) && $image->tags_image ? url('storage/tag/'.$image->tags_image) : '' }}" alt="">
                                    <span>{{ $image->tags_name ?? '' }}</span>
                                </a>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="mob-booking-table mb-2 pb-3 border-bottom rounded-0">
                <table class="table mb-0 table-borderless">
                    <tbody>
                    
                        <tr class="first-tr">
                            <td>&#8377;<span class="PricePerNight">{{ number_format($property->per_night_price) }}</span> 
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
                            <td >Sub Total</td> 
                            <td align="right" >&#8377;{{ number_format($bookingQuotationProperty->total_amount) }}</td> 
                        </tr>
                        @if($property->total_pet_charge)
                            <tr class="second-tr">
                                <td> Pet Fees </td>
                                <td align="right">&#8377;<span class="totalExtraGuestCharge">{{ number_format($property->total_pet_charge) }}</span></td>
                            </tr>
                        @endif


                        @if($bookingQuotationProperty->total_pet_charge)
                            <tr class="second-tr">
                                <td> Pet Fees </td>
                                <td align="right">&#8377;<span class="totalExtraGuestCharge">{{ number_format($bookingQuotationProperty->total_pet_charge) }}</span></td>
                            </tr>
                        @endif



                        <!-- @if($property->addon_total_amount)
                        <tr class="second-tr">
                            <td> Cleaning Fees</td>
                            <td align="right">&#8377;<span class="totalExtraGuestCharge">{{ number_format($property->addon_total_amount - $property->total_pet_charge) }} </td>
                        </tr>
                        @endif -->


                        @if(!empty($bookingQuotationProperty))
                            @foreach($bookingQuotationProperty->additional_charges_detail as $charge)
                            <tr class="second-tr">
                                <td>{{ $charge['name'] }}</td>
                                <td align="right">₹ <span class="totalExtraGuestCharge">{{ number_format($charge['price']) }}</span></td>
                            </tr>
                            @endforeach
                        @endif


                       <!--  @if($property->adOnsDiscountAmount)
                        <tr>
                            <td class="text-link">Add on discount</td> 
                            <td align="right" class="text-link">-&#8377;{{ $property->adOnsDiscountAmount }}</td> 
                        </tr>
                        @endif -->

                        @if($bookingQuotationProperty->adOnsDiscountAmount)
                            <tr>
                                <td class="text-link">Add on discount</td>
                                <td align="right" class="text-link">-₹ {{ $bookingQuotationProperty->adOnsDiscountAmount }}</td>
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

                    </tbody>
                </table>
            </div>
            
        </div>  
        <div class="row pt-1">
            <div class="col">
                <div class="m-booking-info">
                    <strong>₹<span class="TotalAmount">{{ number_format($bookingQuotationProperty->payable_amount) }}</span></strong> incl. taxes <br>
                    <ul class="list-unstyled mb-0 bi-info search-link">
                        <li class="date-text">{{ date('jS M', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkin_date)) ?? 'Add dates' }} - {{ date('jS M', strtotime($bookingQuotationProperty->bookingQuotationDetail->checkout_date)) ?? 'Add dates' }}</li>
                        <li><span class="totalNight"></span> nights</li>
                    </ul>
                    {{-- <div class="promo-link"><a href="javascript:void(0)" data-src="#coupon" data-custom-fancy id="coupon-href">Have a promo code?</a></div> --}}
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('quotation-property-book', ['ptype' => $property->ptype, 'slug' => base64_encode($bookingQuotationProperty->id)]) }}" class="btn w-100 btn-primary icon-link icon-link-hover px-4 ">Reserve <i class="icon-chevron-right"></i></a>
            </div>
        </div> 
    </div>
</div>
<!--- For mobile --> 
<script>
    let minStay = 1;
    let maximum_number_of_guests = Number("{{ $property->maximum_number_of_guests }}");
    let allowed_no_of_children = Number("{{$property->allowed_no_of_children}}");
    let totalChildren = 0;
    let minStayArray =  JSON.parse('{!! json_encode($minStayArray) !!}');

    let adults = "@php if(isset($bookingQuotationProperty->bookingQuotationDetail->no_adults)){ echo $bookingQuotationProperty->bookingQuotationDetail->no_adults; }else{ echo 1;} @endphp";
    let children = "@php if(isset($bookingQuotationProperty->bookingQuotationDetail->no_children)){ echo $bookingQuotationProperty->bookingQuotationDetail->no_children; }else{ echo 0;} @endphp";
    let pets = "@php if(isset($pet)){ echo $pet; }else{ echo 0;} @endphp";
    let no_of_nights = Number("{{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights }}");
    let tax = '';
    let price_per_night = "";
    let base_price = "";
    let price = "";
    let ci_date = "{{ $bookingQuotationProperty->bookingQuotationDetail->checkin_date  }}";
    let co_date = "{{ $bookingQuotationProperty->bookingQuotationDetail->checkout_date  }}";
    let propertyId = "{{ $property->ru_property_id  }}";
    let tot_guest = "{{ ($property->bookingQuotationDetail->no_adults ?? 0) + ($property->bookingQuotationDetail->no_children ?? 0) }}";
    let tot_no_of_days = Number("{{ $bookingQuotationProperty->bookingQuotationDetail->no_of_nights }}");

    let propertyPrimaryId = "{{ $property->id  }}";
    let amountBeforeTax = parseInt(price_per_night)*parseInt(no_of_nights);
    let totalPayableAmount = 0;
    let discountAmount = 0;
    let discountCode = 0;

    let amountBeforeInitialTax = parseInt(price_per_night)*parseInt(no_of_nights);
    let totalPayableInitialAmount = 0;
    let taxAmount = 0;
    let initialTaxAmount = 0;
    let initialTax = 0;
    let initial_price_per_night = 0;
    let additionalCharges = [];
    let additionalChargesAmount = 0;
    let formatted_total_taxable_amount = '';
    let slug = "{{ $property->url_key }}";
    let couponCode = '';

    let property = "{{ $property->ru_property_id  }}";
    


    $(".toggle-date").on("click", function(e){
            e.stopPropagation();
            guestsBtn.hide();
            calendarBtn.toggle();
        })





    document.addEventListener("DOMContentLoaded", function () {
        let parentElClass = '.detail-page-search';    
        let inputCalendar = document.getElementById('detail-page-calendar');
        function resetDateInput(){
            // $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");  
            $(parentElClass).find(".js-checkin-text,.js-checkout-text-page-detail").removeClass('hasValue').text("Add dates");
            datepickerHero.clearDatepicker();
            datepickerHero.clear();
        }
        function updateMinStay(datepicker){
            const isSelecting = datepicker.start && !datepicker.end;               
            const allDays = datepicker.datepicker.getElementsByTagName("td");
            if(isSelecting){
                let getStartDate = fecha.format(datepicker.start, `YYYY-MM-DD`);
                let nights = minStayArray[getStartDate];
                for (let i = 0; i < allDays.length; i++) {
                    if ($(allDays[i]).hasClass("datepicker__month-day--first-day-selected")){
                    
                        for (let j = 1; j < nights; j++) {                                
                            $(allDays[i + j]).addClass("datepicker__month-day--invalid ss")
                        }
                    }
                }
            }
        }        
        window.datepickerHero = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,           
            // clearButton: true,
            // minNights: 4,
            selectForward: true,
            clearButton: true,
            showTopbar: true,
            topbarPosition: 'bottom',
            disabledDates: JSON.parse('{!! json_encode($propertyUnavailableDates) !!}'),
            onSelectRange: function() {
               let startDate = fecha.format(this.start, `Do MMM`);
               let endDate = fecha.format(this.end, `Do MMM`);
               $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
            //   $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);
               $(parentElClass).find(".js-checkout-text-page-detail").addClass('hasValue').html(endDate);
            } ,
            onDayClick: function() {  
                console.log(datepickerHero,"datepickerHero");
                updateMinStay(datepickerHero)              
                if(this.start){
                //   $(".js-checkout-text").text("Check out");
                   $(".c").text("Check out");
                   let startDate = fecha.format(this.start, `Do MMM`);
                   $(parentElClass).find(".js-checkin-text").addClass('hasValue').text(startDate).parent().removeClass('active');
                   $(parentElClass).find(".js-checkout-text-page-detail").parent().addClass('active');
                  
                }
                if(this.end){
                   let endDate = fecha.format(this.end, `Do MMM`);
                   $(parentElClass).find(".js-checkout-text-page-detail").addClass('hasValue').text(endDate); 
                }
                if(this.start && this.end){
                    let days = datepickerHero.getNights();
                    $(".date-text").text(datepickerHero.start+'-'+datepickerHero.end);
                    let getStartDate = fecha.format(datepickerHero.start, `YYYY-MM-DD`);
                    if(days < minStayArray[getStartDate]){
                        $(".bookNow").prop('disabled', true);
                    }
                    else{
                        $(".bookNow").prop('disabled', false);
                    }
                    const nightsText = days === 1 ? 'night' : 'nights';
                    $('.totalNight').text(days + ' ' + nightsText);
                    $('#total_night').val(days);
                    ci_date= fecha.format(this.start, `YYYY-MM-DD`);
                    co_date= fecha.format(this.end, `YYYY-MM-DD`);
                    tot_guest= $('#totalGuests').val();
                    tot_no_of_days= days;
                    $(parentElClass).find(".js-checkin-text,.js-checkout-text-page-detail").parent().removeClass('active');
                }
                if(!this.start && !this.end){
                   resetDateInput()
                }
            }         
        });
        $(document).on("click",".datepicker__month-button", function(){
            //console.log("clicked");
            setTimeout(() => {                
                updateMinStay(datepickerHero)
            }, 0);
        })
        $(document).on("click",'.datepicker__month-day--valid.datepicker__month-day--invalid', function(){
            let $timeStamp = $(this).attr('time');    
            let getDate = fecha.format(datepickerHero.start, `YYYY-MM-DD`);            
            //minStayArray[getStartDate];
            alert("Minimum stay can't be less than "+ minStayArray[getDate]+" Nights")
                   
        })

        $(".clear-dates").on("click",function(e){
           e.stopPropagation();
           resetDateInput();
        });


        $("#coupon_code").keyup(function(e){
            $('.coupon_error').text('').removeClass('text-danger');
            if(e.target.value.length <= 2){
                $('.apply_coupon').prop('disabled', true);
            }
            else{
                $('.apply_coupon').prop('disabled', false);
            }
            
        });
    });
    
    
    let url = "";


    $(document).on('click', '.make-reservation', function (e) {
        $.ajax({
            url: url,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
            data: {
                adults:adultsCount,
                children:childrenCount,
                pets:petsCount,
                all_total_guests:all_total_guests,
                no_of_nights:no_of_nights,
                tax:tax,
                price_per_night:price_per_night,
                base_price:base_price,
                price:price,
                checkin_date:ci_date,
                checkout_date:co_date,
                propertyId:propertyId,
                tot_guest:tot_guest,
                extra_guest_charges:$('#extra_guest_charges').val(),
                total_extra_guest_charge:$('#total_extra_guest_charge').val(),
                tot_no_of_days:tot_no_of_days,
                propertyPrimaryId:propertyPrimaryId,
                amountBeforeTax:amountBeforeTax,
                totalPayableAmount:totalPayableAmount,
                discountAmount:discountAmount,
                discountCode:discountCode,
                additionalCharges:additionalCharges,
                additionalChargesAmount:additionalChargesAmount,
                taxAmount:taxAmount
            },

            success: function(res) {
                if(res.status){
                    location.href = "{{ url('/') }}/booking-confirmation/"+slug;
                }else{
                    location.href = res.redirect;
                }
            },
            error: function(res) {
                $('.coupon_error').text(res.message).addClass('text-danger');
            }
        });
    })


    $(document).on('click', '.apply_coupon', function (e) {
        e.preventDefault();
        applyCouponCode($('#coupon_code').val(), $(this).text());
    });

    $(document).on('click', '.cus-link', function (e) {
        discountAmount = 0;
        discountCode = '';
        tax = initialTax;
        taxAmount = initialTaxAmount;
        totalPayableAmount = totalPayableInitialAmount;
        $('.tax').text(tax)
        $('.taxAmount').text(formatted_total_taxable_amount)
        $('.TotalAmount').text(totalPayableAmount)
        $(".promo-code-tr").remove();
        $('#coupon-href').removeClass('d-none');
        $('#coupon_code').val('');
        $('.apply_coupon').prop('disabled', true);
    });


    function applyCouponCode(code, text){
        $('.coupon_error').text('');
        $('.coupon_error').removeClass('text-danger');
        if(code !=''){
            if(text == 'Apply '){
                $.ajax({
                    url: "booking/apply/coupon/code",
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
                    data: {
                        coupon_code: code,
                        checkin_date:ci_date,
                        checkout_date:co_date,
                        propertyId:propertyId
                    },

                    success: function(res) {
                        discountCode = $('#coupon_code').val();
                        let totAmount = amountBeforeTax;
                        if(res.status){
                            if(res.discount_type =='percentage'){
                                discountAmount = Math.round(totAmount*(res.discount/100));
                            }
                            else{
                                discountAmount = res.discount;
                            }
                            let amountAfterDiscount = totAmount - discountAmount;
                            let tax = 12;
                            if(amountAfterDiscount > 7500){
                                tax = 18;
                            }
                            taxAmount = Math.round((amountAfterDiscount*tax)/100);
                            totalPayableAmount = amountAfterDiscount  + taxAmount;
                            $('.tax').text(tax)
                            $('.taxAmount').text(taxAmount)
                            $('.TotalAmount').text(totalPayableAmount)
                    
                            let discountTr = '<tr class="promo-code-tr"><td class="text-link">Promo Code '+discountCode+'</td><td align="right" class="text-link">-&#8377;'+discountAmount+'</td><td align="right"><a href="javascript:void(0);" class="cus-link"><i class="icon-minus"></i> Remove</a></td></tr>';
                            $(discountTr).insertAfter('.additional-tr');
                            $('#coupon-href').addClass('d-none');

                            Fancybox.close();
                        }
                        else{
                            $('.coupon_error').text(res.message).addClass('text-danger');
                        } 
                    },
                    error: function(res) {
                        $('.coupon_error').text(res.message).addClass('text-danger');
                    }
                });
            }  
        }
        else{
            $('.coupon_error').text('Enter Coupon Code').addClass('text-danger');
        }  
    }
   
   

let adultsCount = parseInt(adults);
let childrenCount = parseInt(children);
let petsCount = parseInt(pets);
let all_total_guests = 0;   

   
    function updateDetailCounter(element, increment) {
        const type = element.getAttribute('data-detail-type');
        const allow_guest = element.getAttribute('data-allow-detail-guest-count');
        const counterContainer = element.closest('.counter');
        const countDisplay = counterContainer.querySelector('.count-detail-val');
        const hiddenInput = counterContainer.querySelector('.count-detail-input');
        const minusBtn = counterContainer.querySelector('.c-minus');
        const plusBtn = counterContainer.querySelector('.c-plus');
       
        let count = parseInt(hiddenInput.value) || 0;
        count += increment;
        countDisplay.textContent = count;
        hiddenInput.value = count;
        if (type === 'Adults'){
            minusBtn.classList.toggle('disabled', count <= 1);
        }
        else{
            minusBtn.classList.toggle('disabled', count === 0);
        }

        updateGuestSummaryDetail(element, type, allow_guest, count);
    }

    function updateGuestSummaryDetail(element,type, allow_guest, count) {
        document.querySelectorAll('.counter-col-detail .count-detail-input').forEach(input => {
            if (type === 'Adults') {
                adultsCount =  count;
            }
            else if(type === 'Children') {
                childrenCount =  count;
            } 
            else if(type === 'Pets') {
                petsCount = count;
            }
        });
        
        const combinedGuests = tot_guest = adultsCount + childrenCount;
        let max_capacity = $('#max_capacity').val();
      
        if(combinedGuests == max_capacity ){   
            console.log(combinedGuests,"combinedGuests");       
            $(element).parents(".guests-counter").find(".c-plus").not('[data-detail-type="Pets"]').addClass("disabled");
        }else{
            $(element).parents(".guests-counter").find(".c-plus").not('[data-detail-type="Pets"]').removeClass("disabled");
        }

       $('#totalGuests').val(combinedGuests);
       
        const guestSummary = [];
        if (combinedGuests > 0) {
            const guestText = combinedGuests === 1 ? "Guest" : "Guests";
            guestSummary.push(`${combinedGuests} ${guestText}`);
        }
        if (petsCount > 0) {
            const petText = petsCount === 1 ? "Pet" : "Pets";
            guestSummary.push(`${petsCount} ${petText}`);
        }
        const displayText = all_total_guests = guestSummary.length > 0 ? guestSummary.join(', ') : 'Add guests';
        const guestSummaryElement = $('.search-field-value-detail.add-guests-detail-value');

        //console.log("displayText", displayText, guestSummaryElement)

        if (guestSummaryElement) {
            console.log("")
            guestSummaryElement.text(displayText);
        }
        else {
            console.error('Element not found: .search-field-value-detail.add-guests-detail-value');
        }
        
    }
    document.addEventListener('DOMContentLoaded', () => {
        updateGuestSummaryDetail();
    });
    
    
    
    
    
</script>

<style>
    .fancybox-wrapper .fancybox-caption{
        position: absolute;
        right: 65px;
        top: 65px;
        font-size: 0.875rem;
        padding: 6px 8px;
        line-height: 1;
        border-radius: 50px;
        font-weight: 700;
        background-color: #ffffff;
        z-index: 11;
    }
</style>
<script>
    let data_image = @json($property->imagesWebsite);

    let galleryImages = ({
        imageCategory,
        imageArray
    }) => {

        if (imageCategory === 'all') {
            return imageArray.map((v) => ({
                src: `${location.origin}/${v.filename}`
            }));
        }

        return imageArray.filter((v) => {
            return v.title === imageCategory
        }).map((v) => ({
            src: `${location.origin}/${v.filename}`
        }))
    }

    document.addEventListener("DOMContentLoaded", function() {
        const faqSwiper = new Swiper('.swiper-faqs', {
            spaceBetween: 30,
            grabCursor: true,
            freeMode: true,
            mousewheel: {
                enabled: true,
                forceToAxis: true
            },
            breakpoints: {

                0: {

                    slidesPerView: 1.2,

                },

                768: {

                    slidesPerView: 1.8,

                },

                992: {

                    slidesPerView: 2.4,

                },

                1200: {

                    slidesPerView: 2.3,

                },

                1350: {

                    slidesPerView: 2.5,

                },

                1700: {

                    slidesPerView: 3.5,

                }

            }

        })

        const mobileGallery = new Swiper('.swiper-gallery', {
            spaceBetween: 0,
            slidesPerView: 1,
            grabCursor: true,
            // freeMode: true,
            pagination: {
                el: ".section-mobile-gallery .swiper-pagination",
                dynamicBullets: true,
            },
        })

        $(document).on("click", '[data-gallery-type]', function(e) {
            e.preventDefault();
            let galType = $(this).attr('data-gallery-type');
            Fancybox.show(galleryImages({
                imageCategory: galType,
                imageArray: data_image
            }), {
                Thumbs: false,
                mainClass: "gallery-popup",
                Toolbar: {
                    display: {
                        left: ["infobar"],
                        middle: [],
                        right: ["close"],
                    },
                },
                Images: {
                    initialSize: "fit",
                },
                on: {
                    reveal: (fancybox, slide) => {
                        $(slide.contentEl).append(`${galType != 'all' ? `<div class="gallery-tag">${galType}</div>` : ''}`)
                    },
                },

            });

        })

    })
    
        
    
        
    
        let mapStyles = [
    
                {
    
                    "featureType": "administrative",
    
                    "elementType": "labels.text.fill",
    
                    "stylers": [
    
                        {
    
                            "color": "#444444"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "landscape",
    
                    "elementType": "all",
    
                    "stylers": [
    
                        {
    
                            "color": "#f2f2f2"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "poi",
    
                    "elementType": "all",
    
                    "stylers": [
    
                        {
    
                            "visibility": "off"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "road",
    
                    "elementType": "all",
    
                    "stylers": [
    
                        {
    
                            "saturation": -100
    
                        },
    
                        {
    
                            "lightness": "6"
    
                        },
    
                        {
    
                            "color": "#cce6e0"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "road",
    
                    "elementType": "labels",
    
                    "stylers": [
    
                        {
    
                            "color": "#716e6e"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "road",
    
                    "elementType": "labels.text.fill",
    
                    "stylers": [
    
                        {
    
                            "weight": "0.01"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "road",
    
                    "elementType": "labels.text.stroke",
    
                    "stylers": [
    
                        {
    
                            "weight": "0.01"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "road.highway",
    
                    "elementType": "all",
    
                    "stylers": [
    
                        {
    
                            "visibility": "simplified"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "road.arterial",
    
                    "elementType": "labels.icon",
    
                    "stylers": [
    
                        {
    
                            "visibility": "off"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "transit",
    
                    "elementType": "all",
    
                    "stylers": [
    
                        {
    
                            "visibility": "off"
    
                        }
    
                    ]
    
                },
    
                {
    
                    "featureType": "water",
    
                    "elementType": "all",
    
                    "stylers": [
    
                        {
    
                            "color": "#cddae5"
    
                        },
    
                        {
    
                            "visibility": "on"
    
                        }
    
                    ]
    
                }
    
            ]
    
        
    
        
    
        window.initMap = function(){
            const location = { lat: {{ $property->home->map_latitude ?? 0 }}, lng: {{ $property->home->map_longitude ?? 0 }}  }; 
            // Create the map, centered at the location
    
            const map = new google.maps.Map(document.getElementById("map"), {
    
                zoom: 18,
    
                center: location,
    
                styles: mapStyles,
    
                disableDefaultUI: true
    
            });
    
        }
    
    
        //---------------------------Mobile search code---------------------//
    
        document.addEventListener("DOMContentLoaded", function(){
            $(".mobile-search-placeholder,.search-link").on("click", function(){
                $('body').addClass('mobile-search-active');
            })
            $('.m-search-close').on("click", function(){
                $('body').removeClass('mobile-search-active');
            })
            function enableSearch(){
                if($('#mobile-calender').val()){
                    $('.search-next').hide();
                    $('.search-submit').show();
                    console.log("bif", $('#mobile-calender').val())
                }else{
                     console.log("b", $('#mobile-calender').val())
                    $('.search-next').show();
                    $('.search-submit').hide();
                }
            }
            new Swiper('.cities-carousel', {
                speed: 400,
                slidesPerView: 3.5,
                spaceBetween: 8,
            });
            function fixeCalendarWidth(){
                $(".mobile-search .datepicker__month-day").outerHeight($(".mobile-search .datepicker__month-day").outerWidth());
            }
            let currentIndex = 1; // Track the current index
            $(".search-next").click(function() {
                $(".mobile-search .accordion-box").removeClass("active"); 
                $(".mobile-search .accordion-box").eq(currentIndex).addClass("active");
                fixeCalendarWidth()
                enableSearch();
                currentIndex = (currentIndex + 1) % $(".mobile-search .accordion-box").length;
            });
            $(".mobile-search .accordion-title").on("click", function(){
                currentIndex = $(this).parent().index();
                $(".mobile-search .accordion-box").removeClass("active"); 
                $(".mobile-search .accordion-box").eq(currentIndex).addClass("active");
                console.log("dh",currentIndex)
                fixeCalendarWidth()
                enableSearch();
            })
            $('.m-search-input').on("click", function(){
                $('body').addClass('mobile-location-search-active');
            })
            $(document).on("click",'.location-search-list a', function(e){
                e.preventDefault();
                let locName = $(this).data('location-name');
                let locValue = $(this).data('location-value');
                $('.m-search-input span').text(locName);
                $('body').removeClass('mobile-location-search-active');
            })
            $('.close-mobile-location-search').on('click', function(){
                $('body').removeClass('mobile-location-search-active');
            })
            // Mobile Calendar
            let parentElClass = '.mobile-search';    
            let inputCalendar = document.getElementById('mobile-calender');
            function resetDateInput(){
              $(parentElClass).find(".m-date").text("Add dates");
              datepickerMobile.clearDatepicker();
              datepickerMobile.clear();
            }
            function updateMinStay(datepicker){
                const isSelecting = datepicker.start && !datepicker.end;               
                const allDays = datepicker.datepicker.getElementsByTagName("td");
                if(isSelecting){
                    let getStartDate = fecha.format(datepicker.start, `YYYY-MM-DD`);
                    let nights = minStayArray[getStartDate];
                    for (let i = 0; i < allDays.length; i++) {
                        if ($(allDays[i]).hasClass("datepicker__month-day--first-day-selected")){
                        
                            for (let j = 1; j < nights; j++) {                                
                                $(allDays[i + j]).addClass("datepicker__month-day--invalid ss")
                            }
                        }
                    }
                }
            }          
            
           
        })
    
    </script>
    <script>
    function handleInstagramShare(el, url) {
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

        if (isMobile) {
            // Open Instagram app
            window.location.href = 'instagram://app';

            // Fallback to web
            setTimeout(() => {
                window.location.href = 'https://www.instagram.com/';
            }, 1000);
        } else {
            // Desktop → copy link + show text
            navigator.clipboard.writeText(url).then(() => {
                el.classList.add('copied');

                setTimeout(() => {
                    el.classList.remove('copied');
                }, 1500);
            });
        }
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBadtfvHfxj3uAeNivR0Prec9tEWQUZoX0&callback=initMap"></script>
@endsection