@extends('website.layouts.app')
@section('content')
<style>
    .fancybox-wrapper .fancybox-caption {
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
    .bi-info::before {
        content: " " !important;
    }
</style>

<div class="page-wrapper">
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
                                                <i class="icon-arrow-left"></i>
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
                                    <a href="javascript:void(0)" class="card-tag">
                                        <img src="{{ isset($image->tags_image) && $image->tags_image ? url('storage/tag/' . $image->tags_image) : '' }}" alt="">
                                        <span>{{ $image->tags_name ?? '' }}</span>
                                    </a>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="bh-price fw-bold">
                            &#8377;<span class="PricePerNight"></span> <small class="fw-normal">per night</small>
                        </div>
                        <div class="main-search-outer detail-page-search ms-0">
                            <div class="main-search">
                                <div class="row g-0 align-items-center">
                                    <div class="col-8 field-col d-flex position-relative">
                                        <button class="btn text-start btn-checkin search-field">
                                            <strong>Check in</strong>
                                            <div class="search-field-value js-checkin-text">{{ date('jS M', strtotime($property->date_from)) ?? 'Add dates' }}</div>
                                            <span class="clear-dates">&times;</span>
                                        </button>
                                        <button class="btn text-start btn-checkout search-field">
                                            <strong>Check out</strong>
                                            <div class="search-field-value js-checkout-text-page-detail">{{ date('jS M', strtotime($property->date_to)) ?? 'Add dates' }} </div>
                                            <span class="clear-dates">&times;</span>
                                        </button>
                                        <!--fill calendra-->
                                        <div class="custom-dropdown calendar-dropdown">
                                            <input id="detail-page-calendar" type="text" style="display:none;" value="@if($property->date_from){{ date('Y-m-d', strtotime($property->date_from)) . ' - ' . date('Y-m-d', strtotime($property->date_to)) }}@endif" />
                                        </div>
                                    </div>
                                    <div class="col field-col position-relative">
                                        <button class="btn text-start search-field search-field-guest">
                                            <strong>Guests</strong>
                                            <div class="search-field-value-detail add-guests-detail-value" style="color: #8b8b8b; font-size: 1rem;  line-height: 1; padding-top: 2px;">Add guests</div>
                                        </button>
                                        <div class="custom-dropdown guests-counter guests-dropdown">
                                            <input type="hidden" name="capacity" id="capacity" value="{{ $property->guests_included ?? '' }}">
                                            <input type="hidden" name="max_capacity" id="max_capacity" value="{{ $property->maximum_number_of_guests ?? '' }}">
                                            <input type="hidden" name="extra_guest_charges" id="extra_guest_charges" value="">
                                            <input type="hidden" name="total_extra_guest_charge" id="total_extra_guest_charge" value="">

                                            <input type="hidden" name="total_pet_charge" id="total_pet_charge" value="">
                                            <input type="hidden" name="per_pet_charge" id="per_pet_charge" value="">


                                            <input type="hidden" name="totalGuests" id="totalGuests">
                                            <ul class="list-unstyled m-0">

                                                @if (!empty($guest_data) && $guest_data->isNotEmpty())
                                                @foreach ($guest_data as $guest)
                                                <li>
                                                    <div class="row flex-nowrap align-items-center">
                                                        <div class="col">
                                                            <div class="guests-title">
                                                                <strong>{{ isset($guest->title) ? $guest->title : '' }}</strong>
                                                                <small>{{ isset($guest->name) ? $guest->name : '' }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="counter">
                                                                <!--<a href="javascript:void(0)" class="btn counter-col counter-col-detail c-minus {{ ($guest->type == 'Adults' && $guest->count == 1) || ($guest->type != 'Adults' && $guest->count == 0) ? 'disabled' : '' }}" data-detail-type="{{ $guest->type }}" data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, -1)">-->
                                                                <!--    <span class="icon-minus"></span>-->
                                                                <!--</a>-->
                                                                <!--<div class="counter-col counter-col-detail">-->
                                                                <!--    <input type="hidden" class="count-input count-detail-input" name="{{ strtolower($guest->title) }}_count" value="{{ isset($guest->count) ? $guest->count : 0 }}">-->
                                                                <!--    <strong class="count-val count-detail-val">{{ isset($guest->count) ? $guest->count : 0 }}</strong>-->
                                                                <!--</div>-->
                                                                <!--<a href="javascript:void(0)" class="btn counter-col -->
                                                                <!--counter-col-detail c-plus" data-detail-type="{{ $guest->type }}"-->
                                                                <!--data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, 1)" >-->
                                                                <!--    <span class="icon-plus"></span>-->
                                                                <!--</a>-->


                                                                @php
                                                                $guestCount = 0;
                                                                if($guest->type == 'Adults' && $adult){
                                                                $guestCount = $adult;
                                                                }
                                                                elseif ($guest->type == 'Children' && $child){
                                                                $guestCount = $child;
                                                                }
                                                                elseif($guest->type == 'Pets' && $pet) {
                                                                $guestCount = $pet;
                                                                }
                                                                else{
                                                                $guestCount = $guest->count ?? 0;
                                                                }
                                                                $totalguests_data = $adult + $child;
                                                                @endphp
                                                                <a href="javascript:void(0)" class="btn counter-col counter-col-detail c-minus {{ ($guest->type == 'Adults' && $guestCount == 1) || ($guest->type != 'Adults' && $guestCount == 0) ? 'disabled' : '' }}" data-detail-type="{{ $guest->type }}" data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, -1)">
                                                                    <span class="icon-minus"></span>
                                                                </a>
                                                                <div class="counter-col counter-col-detail">
                                                                    <input type="hidden" class="count-input count-detail-input" name="{{ strtolower($guest->title) }}_count" value="{{ $guestCount }}">
                                                                    <strong class="count-val count-detail-val">{{ $guestCount }}</strong>
                                                                </div>
                                                                <a href="javascript:void(0)" class="btn counter-col 
                                                                            counter-col-detail c-plus {{ $totalguests_data == $property->maximum_number_of_guests && $guest->type != 'Pets' ? 'disabled' : '' }}" data-detail-type="{{ $guest->type }}"
                                                                    data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, 1)">
                                                                    <span class="icon-plus"></span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endforeach
                                                @endif
                                            </ul>
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
                                    <td>&#8377;<span class="PricePerNight"></span>
                                        x <span class="totalNight"></span></td>
                                    <td align="right">&#8377;<span class="PriceWithPerNight"></span></td>
                                </tr>

                                <tr class="second-tr">
                                    <td> Extra charge (<span class="extraGuestCharge"></span>
                                        x <span class="totalNight"></span> )</td>
                                    <td align="right">&#8377;<span class="totalExtraGuestCharge"></span></td>
                                </tr>

                                <tr class="total-pet-charge">
                                    <td> Pet Fees (&#8377;<span class="PerPetCharge"></span>
                                        x <span class="totalpet"></span>)</td>
                                    <td align="right">&#8377;<span class="totalPetCharge"></span></td>
                                </tr>

                                <tr>
                                    <td>Govt. Taxes (<span class="tax">{{ $tax ?? '' }}</span>%)</td>
                                    <td align="right">&#8377;<span class="taxAmount"></span></td>
                                </tr>
                            </table>
                        </div>
                        <table class="table mb-0 table-borderless">
                            <tr class="fw-bold">
                                <td>Total incl. taxes</td>
                                <td align="right">
                                <td align="right">&#8377;<span class="TotalAmount"></span></td>
                            </tr>
                        </table>
                        <div class="promo-link text-center">
                            <a href="javascript:void(0)" data-src="#coupon" data-custom-fancy id="coupon-href">Have a promo code?</a>
                        </div>
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
                                                    <input type="hidden" name="discount_amount" id="discount_amount" />
                                                    <input type="hidden" name="discount" id="discount" />
                                                    <input type="text" class="form-control text-uppercase" name="coupon_code" id="coupon_code">
                                                </div>
                                                <span class="error justify-content-center coupon_error "></span>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group cs-input bottom-toolbar">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto d-md-none">
                                                            <a href="javascript:void(0)" class="clear-btn">Clear</a>
                                                        </div>
                                                        <div class="col text-end">
                                                            <button type="submit" class="btn btn-primary icon-link icon-link-hover w-md-100 apply_coupon">Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button>
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
                        <span class="text-danger serverErr"></span>
                        <!-- <div class="reserve-button">
                                <a href="javascript:void(0)" class="btn w-100 btn-primary icon-link icon-link-hover make-reservation">Make a Reservation <i class="bi icon-chevron-right"></i></a>  
                            </div> -->

                        <form action="{{ route('property-book') }}" method="GET">
                            <div class="reserve-button">
                                <button type="submit" class="btn py-3 fw-bold w-100 btn-primary cbooking ">Make a Reservation</button>
                            </div>
                        </form>



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
                            <img src="{{ asset($image->medium_image ?? '') }}" alt="{{ $image->title }}">
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
                        <img src="{{ asset($property->imagesWebsite->first()->medium_image ?? '') }}" alt="Living Room">
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
                                <img src="{{ asset($image->medium_image ?? '') }}" alt="{{ $title }}">

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
                            <img src="{{ asset($property->imagesWebsite->last()->medium_image ?? '') }}" alt="See All">
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
        <h3>Entire {{ isset($property->home_type) ? $property->home_type : '' }} in {{ isset($property->locationData->location_name) ? $property->locationData->location_name : '' }}, {{ isset($property->state) ? $property->state : '' }} </h3>
        <p class="text-darkgray mb-0">
            {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }} &bull;
            {{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Bedroom' : $property->no_of_bedrooms . ' Bedrooms' }} &bull;
            {{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}
            @if(!empty($property->property_size))
            &bull; {{ $property->property_size ?? '' }} {{ $property->area_unit ?? '' }}
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
                        @if(!empty($property->websiteamenities) && $property->websiteamenities->count() > 8)
                        <li class="justify-content-end w-auto flex-grow-1"><button class="btn py-0 btn-link see-less-all">SEE ALL</button></li>
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
                            <h3>
                                {{ isset($property->state) ? $property->state : '' }}, {{ isset($property->locationData->location_name) ? $property->locationData->location_name : '' }}
                            </h3>
                            <p>{{ isset($property->home->map_text) ? $property->home->map_text : '' }}</p>
                        </div>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@if(!empty($property->homefaqSection) && $property->homefaqSection->isNotEmpty())
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
@endif
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
<!--- For mobile -->
<div class="mobile-search-wrapper container d-lg-none">
    @include('website.search.mobile-detail-calendar')
</div>
<div class="mobile-reserve-wrap">
    <div class="container">
        <div class="row pb-2">
            <div class="col">
                <h1 class="h3 mb-0">{{ isset($property->unit_name_website) ? $property->unit_name_website : '' }}</h1>
            </div>
            @if($property->tags->isNotEmpty())
            <div class="col-auto">
                @php $firstTag = $property->tags->first(); @endphp
                <a href="{{ route('property-list', ['tags[]' => $firstTag->id, 'tagName' => $firstTag->tags_name, 'type' => 'tag']) }}" class="card-tag single-tag">
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
                        <a href="{{ route('property-list', ['tags[]' => $firstTag->id, 'tagName' => $firstTag->tags_name, 'type' => 'tag']) }}" class="card-tag">
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
                            <td>₹<span class="PricePerNight"></span> x <span class="totalNight"></span></td>
                            <td align="right">₹<span class="PriceWithPerNight"></span></td>
                        </tr>
                        <tr class="second-tr">
                            <td> Extra charge (<span class="extraGuestCharge"></span>
                                x <span class="totalNight"></span> )</td>
                            <td align="right">&#8377;<span class="totalExtraGuestCharge"></span></td>
                        </tr>
                        <tr class="total-pet-charge">
                            <td> Pet Fees (&#8377;<span class="PerPetCharge"></span>
                                x <span class="totalpet"></span>)</td>
                            <td align="right">&#8377;<span class="totalPetCharge"></span></td>
                        </tr>
                        <tr>
                            <td>Govt. Taxes (<span class="tax">{{ $tax ?? '' }}</span>%)</td>
                            <td align="right">₹<span class="taxAmount"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row pt-1">
            <div class="col">
                <div class="m-booking-info">
                    <strong>₹<span class="TotalAmount"></span></strong> incl. taxes <br>
                    <ul class="list-unstyled mb-0 bi-info icon-remove search-link">
                        <li class="date-text checkIn_chechout_mobile_display">{{ date('jS M', strtotime($property->date_from)) ?? 'Add dates' }} - {{ date('jS M', strtotime($property->date_to)) ?? 'Add dates' }}</li>
                        <li><span class="totalNight"></span> nights</li>
                    </ul>
                    <div class="promo-link"><a href="javascript:void(0)" data-src="#coupon" data-custom-fancy id="coupon-href">Have a promo code?</a></div>

                </div>
            </div>
            <span class="text-danger serverErr"></span>
            <!-- <div class="col-auto">
                <a href="javascript:void(0)" class="btn w-100 btn-primary icon-link icon-link-hover px-4 make-reservation">Reserve <i class="icon-chevron-right"></i></a>
            </div> -->
            <div class="col-auto">
                <form action="{{ route('property-book') }}" method="GET">
                    <div class="reserve-button">
                        <button type="submit" class="btn  w-100 btn-primary icon-link icon-link-hover px-4 make-reservation">Reserve <i class="icon-chevron-right"></i></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!--- For mobile -->
<script defer>
    let minStay = 1;
    let maximum_number_of_guests = Number("{{ $property->maximum_number_of_guests }}");
    let allowed_no_of_children = Number("{{$property->allowed_no_of_children}}");
    let totalChildren = 0;
    let minStayArray = JSON.parse('{!! json_encode($minStayArray) !!}');

    let adults = "@php if(isset($adult)){ echo $adult; }else{ echo 1;} @endphp";
    let children = "@php if(isset($child)){ echo $child; }else{ echo 0;} @endphp";
    let pets = "@php if(isset($pet)){ echo $pet; }else{ echo 0;} @endphp";
    let no_of_nights = "@php echo $property->no_of_nights; @endphp";
    let tax = "@php if($bookingDetail){echo $bookingDetail['tax']; } @endphp";
    let price_per_night = "";
    let base_price = "";
    let price = "";
    let ci_date = "@php echo $property->date_from; @endphp";
    let co_date = "@php echo $property->date_to; @endphp";
    let propertyId = "{{ $property->ru_property_id  }}";
    let ptype = "@php echo $property->ptype; @endphp";
    let tot_guest = "@php echo $totGuest; @endphp";
    let tot_no_of_days = "@php echo $property->no_of_nights; @endphp";

    let propertyPrimaryId = "{{ $property->id  }}";
    let amountBeforeTax = parseInt(price_per_night) * parseInt(no_of_nights);
    let totalPayableAmount = 0;
    let discountAmount = 0;
    let discountCode = 0;

    /* header */
    let city_slug_name_p = "{{ $city_slug_name  }}";
    let checkin_date_P = "{{ $checkin_date  }}";
    let checkout_date_p = "{{ $checkout_date  }}";
    let city_id_p = "{{ $city_id  }}";
    let tot_guest_p = "{{ $tot_guest  }}";
    let adultsCount_p = "{{ $adult  }}";
    let childrenCount_p = "{{ $child  }}";
    let petsCount_p = "{{ $petsCount  }}";
    let all_total_guests_p = "{{ $all_total_guests  }}";
    /* end */

    let amountBeforeInitialTax = parseInt(price_per_night) * parseInt(no_of_nights);
    let totalPayableInitialAmount = 0;
    let taxAmount = 0;
    let initialTaxAmount = 0;
    let initialTax = 0;
    let initial_price_per_night = 0;
    let additionalCharges = [];
    let additionalChargesAmount = 0;
    let formatted_total_taxable_amount = '';
    let slug = "{{ $property->url_key }}";
    let couponCode = "@php if($bookingDetail){echo $bookingDetail['discountCode']; } @endphp"

    let property = "{{ $property->ru_property_id  }}";
    let petCharge = 0;
    let extraGuest = 0;

    document.addEventListener("DOMContentLoaded", function() {
        let parentElClass = '.detail-page-search';
        let inputCalendar = document.getElementById('detail-page-calendar');

        function resetDateInput() {
            // $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");  
            $(parentElClass).find(".js-checkin-text,.js-checkout-text-page-detail").removeClass('hasValue').text("Add dates");
            datepickerHero.clearDatepicker();
            datepickerHero.clear();
        }

        function updateMinStay(datepicker) {
            const isSelecting = datepicker.start && !datepicker.end;
            const allDays = datepicker.datepicker.getElementsByTagName("td");
            if (isSelecting) {
                let getStartDate = fecha.format(datepicker.start, `YYYY-MM-DD`);
                let nights = minStayArray[getStartDate];
                for (let i = 0; i < allDays.length; i++) {
                    if ($(allDays[i]).hasClass("datepicker__month-day--first-day-selected")) {

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
            //showTopbar: false,
            selectForward: true,
            clearButton: true,
            showTopbar: true,
            enableCheckout: true,
            topbarPosition: 'bottom',
            disabledDates: JSON.parse('{!! json_encode($propertyUnavailableDates) !!}'),
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                //   $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);
                $(parentElClass).find(".js-checkout-text-page-detail").addClass('hasValue').html(endDate);
            },
            onDayClick: function() {
                updateMinStay(datepickerHero)
                if (this.start) {
                    $(".js-checkout-text-page-detail").text("Check out");
                    $(".c").text("Check out");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(parentElClass).find(".js-checkin-text").addClass('hasValue').text(startDate).parent().removeClass('active');
                    $(parentElClass).find(".js-checkout-text-page-detail").parent().addClass('active');

                }
                if (this.end) {
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(parentElClass).find(".js-checkout-text-page-detail").addClass('hasValue').text(endDate);
                }
                if (this.start && this.end) {
                    let days = datepickerHero.getNights();

                    $(".date-text").text(datepickerHero.start + '-' + datepickerHero.end);
                    let getStartDate = fecha.format(datepickerHero.start, `YYYY-MM-DD`);
                    if (days < minStayArray[getStartDate]) {
                        $(".bookNow").prop('disabled', true);
                    } else {
                        $(".bookNow").prop('disabled', false);
                    }
                    const nightsText = days === 1 ? 'night' : 'nights';
                    $('.totalNight').text(days + ' ' + nightsText);
                    $('#total_night').val(days);
                    ci_date = fecha.format(this.start, `YYYY-MM-DD`);
                    co_date = fecha.format(this.end, `YYYY-MM-DD`);
                    // tot_guest= $('#total_guest').val();
                    tot_guest = $('#totalGuests').val();
                    tot_no_of_days = days;

                    $(".promo-code-tr").remove();
                    $('#coupon-href').removeClass('d-none');
                    $('#coupon_code').val('');

                    getPropertyPrice();
                    $(parentElClass).find(".js-checkin-text,.js-checkout-text-page-detail").parent().removeClass('active');
                }
                if (!this.start && !this.end) {
                    resetDateInput()
                }
            }
        });
        $(document).on("click", ".datepicker__month-button", function() {
            //console.log("clicked");
            setTimeout(() => {
                updateMinStay(datepickerHero)
            }, 0);
        })
        $(document).on("click", '.datepicker__month-day--valid.datepicker__month-day--invalid', function() {
            let $timeStamp = $(this).attr('time');
            let getDate = fecha.format(datepickerHero.start, `YYYY-MM-DD`);
            //minStayArray[getStartDate];
            alert("Minimum stay can't be less than " + minStayArray[getDate] + " Nights")

        })

        $(".clear-dates").on("click", function(e) {
            e.stopPropagation();
            resetDateInput();
        });


        $("#coupon_code").keyup(function(e) {
            $('.coupon_error').text('').removeClass('text-danger');
            if (e.target.value.length <= 2) {
                $('.apply_coupon').prop('disabled', true);
            } else {
                $('.apply_coupon').prop('disabled', false);
            }

        });
    });

    function getPropertyPrice() {
        $.ajax({
            url: "/get/ajax/property/price",
            type: 'get',
            headers: {
                'X-CSRF-TOKEN': $("input[name=_token]").val()
            },
            data: {
                adults: adultsCount,
                children: childrenCount,
                pets: petsCount,
                all_total_guests: all_total_guests,


                checkin_date: ci_date,
                checkout_date: co_date,
                propertyId: propertyId,
                ptype: ptype,
                tot_guest: tot_guest,
                tot_no_of_days: tot_no_of_days
            },
            success: function(res) {
                $('.totalPrice').text(res.data.formatted_base_price)
                $('.PricePerNight').text(res.data.price_per_night_num_formatted)
                $('.PriceWithPerNight').text(res.data.total_price_multiple)
                $('.tax').text(res.data.tax)
                $('.taxAmount').text(res.data.formatted_total_taxable_amount)
                $('.TotalAmount').text(res.data.num_formatted_tot_price)
                $('.totalNight').text(tot_no_of_days)

                $('.totalExtraGuestCharge').text(res.data.total_extra_guest_charge)
                $('.second-tr').hide();
                if (res.data && res.data.total_extra_guest_charge !== 0) {
                    $('.second-tr').show();
                }

                $('.total-pet-charge').hide();
                if (res.data && res.data.total_pet_charge !== 0) {

                    $('.total-pet-charge').show();
                    $('.totalPetCharge').text(res.data.total_pet_charge)
                    $('.PerPetCharge').text(res.data.per_pet_charge)
                    $('.totalpet').text(res.data.total_pet_count)
                    $('#total_pet_charge').val(res.data.total_pet_charge)
                    $('#per_pet_charge').val(res.data.per_pet_charge)
                }


                if (res.data.total_pet_charge) {
                    petCharge = res.data.total_pet_charge;
                }



                $('.extraGuestCharge').text(res.data.extra_guest_charge)
                $('#extra_guest_charges').val(res.data.extra_guest_charge)
                $('#total_extra_guest_charge').val(res.data.total_extra_guest_charge)
                extraGuest = res.data.extraGuest;
                $('.extraGuestCharge').text(res.data.extra_guest_charge * res.data.extra_no_of_guest)

                amountBeforeTax = res.data.amountBeforeTax + res.data.total_additional_charges;
                tax = initialTax = res.data.tax;
                tax_amount = initialTaxAmount = taxAmount = res.data.tax_amount;


                totalPayableAmount = totalPayableInitialAmount = amountBeforeTax + Math.round(tax_amount) + Math.round(petCharge);


                price_per_night = initial_price_per_night = res.data.per_night_price;
                formatted_total_taxable_amount = res.data.formatted_total_taxable_amount;
                additionalCharges = res.data.additionalCharges;
                additionalChargesAmount = res.data.total_additional_charges;
                let additionalChargesTr = '';
                if (additionalCharges && additionalCharges.length > 0) {
                    additionalCharges.forEach(item => {
                        if (item.type_option == 'Per_Stay') {
                            additionalChargesTr = additionalChargesTr + '<tr class ="additional-tr"><td>' + item.name + '</td><td align="right">&#8377;<span>' + item.price + '</span></td></tr>';

                        } else {
                            additionalChargesTr = additionalChargesTr + '<tr class ="additional-tr"><td>' + item.name + '</td><td align="right">&#8377;<span>' + item.price * tot_no_of_days + '</span></td></tr>';
                        }

                    });
                }
                $(".additional-tr").remove();
                $(additionalChargesTr).insertAfter('.first-tr');
                if (couponCode != '') {
                    console.log(couponCode)
                    applyCouponCode(couponCode, 'Apply ');
                }
                let website_markup_price = res.data.website_markup_price;
                let formData = {
                    ptype: ptype,
                    adults: adultsCount,
                    children: childrenCount,
                    pets: petsCount,
                    checkin: ci_date,
                    checkout: co_date,
                    //  propertyId: propertyId,
                    total_guests: tot_guest,
                    tot_no_of_days: tot_no_of_days,
                    slug: slug,
                    per_pet_charge: $('#per_pet_charge').val(),
                    total_pet_charge: $('#total_pet_charge').val(),
                    extraGuest: extraGuest,
                    //    website_markup_price: website_markup_price,

                };
                $('.dynamic-hidden-input').remove();
                console.log(formData);
                $.each(formData, function(key, value) {
                    let input = $('<input>').attr({
                        type: 'hidden',
                        name: key,
                        value: value
                    }).addClass('dynamic-hidden-input');
                    $('form').append(input);
                });

            },
            error: function(res) {}
        });
    }

    let url = '';
    $(document).on('click', '.make-reservation', function(e) {
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $("input[name=_token]").val()
            },
            data: {
                // adults:adults,
                // children:children,
                // pets:pets,

                adults: adultsCount,
                children: childrenCount,
                pets: petsCount,
                petCharge: petCharge,
                no_of_nights: no_of_nights,
                all_total_guests: all_total_guests,
                tax: tax,
                price_per_night: price_per_night,
                base_price: base_price,
                price: price,
                checkin_date: ci_date,
                checkout_date: co_date,
                propertyId: propertyId,
                tot_guest: tot_guest_p,
                city_slug_name: city_slug_name_p,
                slug: slug,
                city_id: city_id_p,
                type_booking: 'bookingPage',


                extra_guest_charges: $('#extra_guest_charges').val(),
                total_extra_guest_charge: $('#total_extra_guest_charge').val(),


                tot_no_of_days: tot_no_of_days,
                propertyPrimaryId: propertyPrimaryId,
                amountBeforeTax: amountBeforeTax,
                totalPayableAmount: totalPayableAmount,
                discountAmount: discountAmount,
                discountCode: discountCode,
                additionalCharges: additionalCharges,
                additionalChargesAmount: additionalChargesAmount,
                taxAmount: taxAmount,


                per_pet_charge: $('#per_pet_charge').val(),
                total_pet_charge: $('#total_pet_charge').val(),
            },

            success: function(res) {

                if (res.type != 'unavailable') {
                    if (res.status) {
                        const searchDataHe = {
                            city_slug_name: city_slug_name_p,
                            slug: slug,
                            checkin_date: checkin_date_P,
                            checkout_date: checkout_date_p,
                            city_id: city_id_p,
                            tot_guest: tot_guest_p,
                            adultsCount: adultsCount_p,
                            childrenCount: childrenCount_p,
                            petsCount: petsCount_p,
                            all_total_guests: all_total_guests_p,
                        };

                        location.href = "{{ url('/') }}/booking-confirmation/?" + $.param(searchDataHe);
                    } else {
                        //console.log(res,"res");
                        location.href = res.redirect;
                    }
                } else {
                    $('.serverErr').text(res.message);
                }
            },
            error: function(res) {
                $('.coupon_error').text(res.message).addClass('text-danger');
            }
        });
    })


    $(document).on('click', '.apply_coupon', function(e) {
        e.preventDefault();
        applyCouponCode($('#coupon_code').val(), $(this).text());
    });

    $(document).on('click', '.cus-link', function(e) {
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


    function applyCouponCode(code, text) {
        $('.coupon_error').text('');
        $('.coupon_error').removeClass('text-danger');
        if (code != '') {
            if (text == 'Apply ') {
                $.ajax({
                    url: "/booking/apply/coupon/code",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $("input[name=_token]").val()
                    },
                    data: {
                        coupon_code: code,
                        checkin_date: ci_date,
                        checkout_date: co_date,
                        propertyId: propertyPrimaryId,
                        amountBeforeTax: amountBeforeTax
                    },

                    success: function(res) {
                        discountCode = $('#coupon_code').val();
                        let totAmount = amountBeforeTax + petCharge;
                        if (res.status) {
                            if (res.discount_type == 'percentage') {
                                discountAmount = Math.round(totAmount * (res.discount / 100));
                            } else {
                                discountAmount = res.discount;
                            }
                            let amountAfterDiscount = totAmount - discountAmount;
                            let tax = 5;
                            if (amountAfterDiscount > 7500) {
                                tax = 18;
                            }
                            taxAmount = Math.round((amountAfterDiscount * tax) / 100);
                            totalPayableAmount = amountAfterDiscount + taxAmount;
                            $('.tax').text(tax)
                            $('.taxAmount').text(taxAmount)
                            $('.TotalAmount').text(totalPayableAmount)

                            // let discountTr = '<tr class="promo-code-tr"><td class="text-link">Promo Code ' + discountCode + '</td><td align="right" class="text-link">-&#8377;' + discountAmount + '</td><td align="right"><a href="javascript:void(0)" class="cus-link"><i class="icon-minus"></i> Remove</a></td></tr>';
                            let discountTr = '<tr class="promo-code-tr"><td class="text-link">Promo Code (' + discountCode + ')</td><td align="right" style="width:100px;" class="text-link">-&#8377;' + discountAmount + ' <a href="javascript:void(0);" class="cus-link text-danger" style="font-size:13px;">&#128465</a></td></tr>';

                            $(discountTr).insertAfter('.total-pet-charge');
                            $('#coupon-href').addClass('d-none');

                            Fancybox.close();


                            let formData = {
                                tax: tax,
                                num_formatted_tot_price: totalPayableAmount,
                                formatted_total_taxable_amount: taxAmount,
                                discountCode: discountCode,
                                discountAmount: discountAmount,
                            };
                            $.each(formData, function(key, value) {
                                let existingInput = $('form').find('input[name="' + key + '"]');
                                if (existingInput.length > 0) {
                                    existingInput.val(value);
                                } else {
                                    let input = $('<input>').attr({
                                        type: 'hidden',
                                        name: key,
                                        value: value
                                    }).addClass('dynamic-hidden-input');
                                    $('form').append(input);
                                }
                            });






                        } else {
                            $('.coupon_error').text(res.message).addClass('text-danger');
                        }
                    },
                    error: function(res) {
                        $('.coupon_error').text(res.message).addClass('text-danger');
                    }
                });
            }
        } else {
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
        if (type === 'Adults') {
            minusBtn.classList.toggle('disabled', count <= 1);
        } else {
            minusBtn.classList.toggle('disabled', count === 0);
        }

        updateGuestSummaryDetail(element, type, allow_guest, count);
    }

    function updateGuestSummaryDetail(element, type, allow_guest, count) {
        document.querySelectorAll('.counter-col-detail .count-detail-input').forEach(input => {
            if (type === 'Adults') {
                adultsCount = count;
            } else if (type === 'Children') {
                childrenCount = count;
            } else if (type === 'Pets') {
                petsCount = count;
            }
        });
        const combinedGuests = tot_guest = adultsCount + childrenCount

        let max_capacity = $('#max_capacity').val();

        if (combinedGuests == max_capacity) {
            $(element).parents(".guests-counter").find(".c-plus").not('[data-detail-type="Pets"]').addClass("disabled");
        } else {
            $(element).parents(".guests-counter").find(".c-plus").not('[data-detail-type="Pets"]').removeClass("disabled");
        }

        $('#totalGuests').val(combinedGuests);

        getPropertyPrice();
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

        // console.log("displayText", displayText, guestSummaryElement)

        if (guestSummaryElement) {
            console.log("")
            guestSummaryElement.text(displayText);
        } else {
            console.error('Element not found: .search-field-value-detail.add-guests-detail-value');
        }
        $(".promo-code-tr").remove();
        $('#coupon-href').removeClass('d-none');
        $('#coupon_code').val('');
        getPropertyPrice();
    }
    document.addEventListener('DOMContentLoaded', () => {
        updateGuestSummaryDetail();
    });
</script>
<script defer>
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

                    "color": "#fefefe"

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
            zoom: 16,
            center: location,
            styles: mapStyles,
            disableDefaultUI: true
        });


        const circle = new google.maps.Circle({
            strokeColor: "#3BB6B1",
            // strokeOpacity: 0.8,
            strokeWeight: 1,
            fillColor: "#3BB6B1",
            fillOpacity: 0.2,
            map: map,
            center: location,
            radius: 100,
        });

        // Add an icon in the center
        const iconMarker = new google.maps.Marker({
            position: location,
            map: map,
            icon: {
            url: "{{ asset('assets/website/images/map.svg') }}",
            scaledSize: new google.maps.Size(24, 24),
            anchor: new google.maps.Point(12, 18)
            },
        });






    }

    //---------------------------Mobile search code---------------------//

    document.addEventListener("DOMContentLoaded", function() {
        $(document).on("click", ".see-less-all", function() {
            $(this).parents(".amenities-list").toggleClass("active-less-all");
            if ($(this).parents(".amenities-list").hasClass("active-less-all")) {
                $(this).text("SEE LESS");
            } else {
                $(this).text("SEE ALL");
            }
        })
        $(".mobile-search-placeholder,.search-link").on("click", function() {
            $('body').addClass('mobile-search-active');
        })
        $('.m-search-close').on("click", function() {
            $('body').removeClass('mobile-search-active');
        })

        function enableSearch() {
            if ($('#mobile-calender').val()) {
                $('.search-next').hide();
                $('.search-submit').show();
                console.log("bif", $('#mobile-calender').val())
            } else {
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

        function fixeCalendarWidth() {
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
        $(".mobile-search .accordion-title").on("click", function() {
            currentIndex = $(this).parent().index();
            $(".mobile-search .accordion-box").removeClass("active");
            $(".mobile-search .accordion-box").eq(currentIndex).addClass("active");
            console.log("dh", currentIndex)
            fixeCalendarWidth()
            enableSearch();
        })
        $('.m-search-input').on("click", function() {
            $('body').addClass('mobile-location-search-active');
        })
        $(document).on("click", '.location-search-list a', function(e) {
            e.preventDefault();
            let locName = $(this).data('location-name');
            let locValue = $(this).data('location-value');
            $('.m-search-input span').text(locName);
            $('body').removeClass('mobile-location-search-active');
        })
        $('.close-mobile-location-search').on('click', function() {
            $('body').removeClass('mobile-location-search-active');
        })
        // Mobile Calendar
        let parentElClass = '.mobile-search';
        let inputCalendar = document.getElementById('mobile-calender');

        function resetDateInput() {
            $(parentElClass).find(".m-date").text("Add dates");
            datepickerMobile.clearDatepicker();
            datepickerMobile.clear();
        }

        function updateMinStay(datepicker) {
            const isSelecting = datepicker.start && !datepicker.end;
            const allDays = datepicker.datepicker.getElementsByTagName("td");
            if (isSelecting) {
                let getStartDate = fecha.format(datepicker.start, `YYYY-MM-DD`);
                let nights = minStayArray[getStartDate];
                for (let i = 0; i < allDays.length; i++) {
                    if ($(allDays[i]).hasClass("datepicker__month-day--first-day-selected")) {

                        for (let j = 1; j < nights; j++) {
                            $(allDays[i + j]).addClass("datepicker__month-day--invalid ss")
                        }
                    }
                }
            }
        }
        window.datepickerMobile = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,
            // clearButton: true,
            // minNights: 4,
            // showTopbar: false,
            selectForward: true,
            clearButton: true,
            showTopbar: true,
            topbarPosition: 'bottom',
            disabledDates: JSON.parse('{!! json_encode($propertyUnavailableDates) !!}'),
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".m-date").text(`${startDate}-${endDate}`);
                enableSearch();
            },
            onDayClick: function() {
                updateMinStay(datepickerMobile)
                if (this.start) {
                    //$(".btn-end-date span").text("Departure");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(parentElClass).find(".m-date").text(`${startDate}`);
                }
                if (this.end) {
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(parentElClass).find(".m-date").text(`-${endDate}`);
                }
                if (this.start && this.end) {
                    let startDateMobile = fecha.format(this.start, 'Do MMM');
                    let endDateMobile = fecha.format(this.end, 'Do MMM');
                    const checkIn_chechout_mobile_date = $('.checkIn_chechout_mobile_date');
                    checkIn_chechout_mobile_date.text(`${startDateMobile} to ${endDateMobile}`);
                    $('.checkIn_chechout_mobile_display').text(`${startDateMobile} - ${endDateMobile}`);
                    let days = datepickerMobile.getNights();
                    $(parentElClass).find(".m-date").text(`Add dates`);

                    const nightsText = days === 1 ? 'night' : 'nights';

                    $('.totalNight').text(days + ' ' + nightsText);
                    $('#total_night').val(days);
                    ci_date = fecha.format(this.start, `YYYY-MM-DD`);
                    co_date = fecha.format(this.end, `YYYY-MM-DD`);
                    //tot_guest= $('#total_guest').val();
                    tot_guest = $('#totalGuests').val();
                    tot_no_of_days = days;
                    getPropertyPrice();
                }
                if (!this.start && !this.end) {
                    resetDateInput()
                }
            }
        });
        $(".clear-dates").on("click", function(e) {
            e.stopPropagation();
            resetDateInput();
        });
        fixeCalendarWidth()
        $(window).on("resize", function() {
            fixeCalendarWidth()
        })
    })
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.clear-btn').addEventListener('click', function () {
            document.getElementById('coupon_code').value = '';
            document.getElementById('discount_amount').value = '';
            document.getElementById('discount').value = '';
            document.querySelector('.coupon_error').innerHTML = '';
        });
    });
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBadtfvHfxj3uAeNivR0Prec9tEWQUZoX0&callback=initMap"></script>
@endsection