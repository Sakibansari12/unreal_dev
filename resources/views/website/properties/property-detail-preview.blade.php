@extends('website.layouts.app')
@section('content')

<style>
    
    .section-all-reviews .heading-reviews {
    position: relative;
    }
    .bi-info::before{
    display: none;
   }
</style>
<div class="breadcrum">
    <div class="container-fluid">
        <ul>
            <li><a href="{{ route('index') }}">Home</a></li>
            <li>{{ $property->unit_name_website ?? '' }}</li>
        </ul>
    </div>
</div>
<div class="page-wrapper">
    
   <section class="section-property-gallery d-none d-md-block pt-4 py-3 pb-4">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <div class="position-relative"> 
                            <a href="#" class="gallery-card gallery-icons" data-gallery-type="all">
                                <!--@php $firstImage = $property->imagesWebsite->first(); @endphp-->
                                <!--@if($firstImage)-->
                                <!--    <img src="{{ asset($firstImage->medium_image ?? 'assets/website/images/no-image.png') }}" alt="">-->
                                <!--@else-->
                                <!--    <img src="{{ asset('assets/website/images/no-image.png') }}" alt="">-->
                                <!--@endif-->
                                
                                
                                @php
                                    // Sort images so that tbl_ru_image_type_id = 1 comes first
                                    $sortedImages = $property->imagesWebsite->sortByDesc(function ($img) {
                                        return $img->tbl_ru_image_type_id == 1 ? 1 : 0;
                                    });
                                
                                    $firstImage = $sortedImages->first();
                                @endphp
                                
                                @if($firstImage)
                                    <img src="{{ asset($firstImage->medium_image ?? 'assets/website/images/no-image.png') }}" alt="">
                                @else
                                    <img src="{{ asset('assets/website/images/no-image.png') }}" alt="">
                                @endif

                                
                                <div class="photos-div">
                                    <i class="icon-thumbnails" style="margin-right:5px;"></i> <span class="text">All Photos</span>
                                </div>
                            
                            </a>
                            <div class="gallery-icons d-flex gap-4 my-2 align-items-baseline position-absolute top-0 left-0">
                            @if(!empty($property->propertyVideo) && !empty($property->propertyVideo->filename))
                                <a href="{{ asset($property->propertyVideo->filename) }}" data-fancybox class="btn">
                                    <div class="icon-video">
                                        <span class="icon-play"></span>
                                    </div>
                                </a>
                            @endif
                            
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="row g-4">
                    @php
                        $displayedImageIds = []; 
                        $firstImageId = $firstImage ? $firstImage->id : null;
                        $lastImage = $property->imagesWebsite->last();
                        $lastImageId = $lastImage ? $lastImage->id : null;
                        $displayCount = 0; 
                    @endphp
                        @foreach($property->imagesWebsite as $image)
                        @if($image->id != $firstImageId && $image->id != $lastImageId && $displayCount < 4)
                            <div class="col-6">
                                <a href="#" class="gallery-card" data-gallery-type="all">
                                    <img src="{{ asset($image->medium_image ?? 'assets/images/noimage-property.jpg') }}"  alt="Property Image">
                                </a>
                            </div>
                            @php
                                $displayedImageIds[] = $image->id;
                                $displayCount++;
                            @endphp
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
 <!--- For Mobile -->
    <section class="section swiper-property-image section-mobile-gallery pt-0 pb-4 mt-n2 d-md-none">
        <div class="container-fluid px-0">
            <div class="gallery-icons d-flex gap-4 my-2 align-items-baseline">
                @if(!empty($property->propertyVideo) && !empty($property->propertyVideo->filename))
                <a href="{{ asset($property->propertyVideo->filename) }}" data-fancybox class="btn">
                    <div class="icon-video">
                        <span class="icon-play"></span>
                    </div>
                </a>
                @else
                <a></a>
                @endif
                <a href="javascript:void(0)" data-gallery-type="all">
                    <div class="photos-div">
                        <i class="icon-thumbnails" style="margin-right:5px;"></i> <span class="text">All Photos</span>
                    </div>
                </a>
            </div>
            <div class="swiper swiper-gallery">
                <div class="swiper-wrapper">
                    @if ($property->imagesWebsite->where('type', 'image')->isNotEmpty())
                    @foreach($property->imagesWebsite as $image)
                    <div class="swiper-slide">
                        <a href="javascript:void(0)" data-gallery-type="all">
                            <img src="{{ asset($image->medium_image ?? 'assets/website/images/no-image.png') }}" alt="">
                        </a>
                    </div>
                    @endforeach
                    @else
                    <div class="swiper-slide">
                        <a href="javascript:void(0)" data-gallery-type="all">
                            <img  src="{{ asset('assets/website/images/no-image.png') }}"  alt="">
                        </a>
                    </div>
                   @endif
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    <!--- For Mobile -->

    <section class="section section-about-property pt-0 section-property-info">
        <div class="container-fluid">
            <div class="row gx-5 gy-4">
                <div class="col-12 col-xl-8">
                    <h1 class="fs-1 mb-3">{{ $property->unit_name_website ?? '' }}</h1>

                    <div class="row align-items-baseline g-2 gx-4 property-details">
                        <div class="col-auto col-md-2">
                            <span class="property-address">{{ $property->locationData->location_name ?? '' }}, {{ $property->state ?? ''}}</span>
                        </div>
                        <div class="col-auto col-md-10">
                            <ul class="nav property-short-info">
                                <li>Upto {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }}</li>
                                <li>{{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Bedroom' : $property->no_of_bedrooms . ' Bedrooms' }}</li>
                                <li>{{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}</li>
                                @if($property->property_size)
                                <li>{{ $property->property_size ?? '' }} {{ $property->area_unit ?? '' }}</li>
                                @endif
                            </ul> 
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4 property-info-deatils">
                    <ul class="list-unstyled row g-2 share-links justify-content-lg-end justify-content-md-start mb-0">
                        @if ($property && $property->homeReviews->isNotEmpty()) 
                            @php
                                $totalRating = 0;
                                $totalReviews = count($property->homeReviews);
                            @endphp

                            @foreach ($property->homeReviews as $reviews)
                                @php $totalRating += $reviews->rating; @endphp
                            @endforeach

                            @if($totalReviews > 0)
                                <li class="col-auto gallery-card btn property-rating" style="cursor: unset;">
                                    <span>{{ number_format($totalRating / $totalReviews, 1) }} <span class="icon-star"></span></span> 
                                </li>
                            @endif
                        <li class="col-auto align-self-center">
                            <a href="#review" class="gallery-card property-reviews">
                                {{ count($property->homeReviews)}} {{ $property->homeReviews->count() == 1 ? 'Review' : 'Reviews' }}
                            </a>
                        </li>
                        @endif
                        
                        <!--<li class="col-auto">-->
                        <!--    <a href="{{ route('download.brochure', ['id' => $property->id, 'pType' => $property->pType]) }}" class="gallery-card btn btn-primary text-white">-->
                        <!--        <span class="icon-pdf" style="margin-right:5px;"></span>Brochure-->
                        <!--        <span id="spinner_brochure" class="spinner-border spinner-border-sm" style="color: #fff; display: none;" role="status" aria-hidden="true"></span>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <li class="col-auto">
                            <a href="{{ route('download.brochure', ['id' => $property->id, 'pType' => $property->pType]) }}"
                               class="gallery-card btn btn-primary text-white"
                               id="downloadBrochureBtn">
                                <span class="icon-pdf" style="margin-right:5px;"></span>Brochure&nbsp;&nbsp;
                                <span id="spinner_brochure" class="spinner-border spinner-border-sm" style="color: #fff; display: none;" role="status" aria-hidden="true"></span>
                            </a>
                        </li>


                        
                        <li class="col-auto">
                            <a href="#" data-src="#share" data-custom-fancy data-close-button="false" class="property-short-info btn btn-outline-primary text-dark">
                                <span class="icon-share" style="margin-right:5px;"></span> <span class="d-none d-md-inline-block">Share</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-xl-8 page-detail-column">                
                    <div class="detailContainer">
                        
                        @if(!empty($property->description))
                        <div class="group">
                            <h3 class="ci-title text-dark">About this place</h3>  
                            <div class="content js-description-read-smore" data-read-smore-chars="300">
                                <p class="short-description-content">                             
                                </p>
                                <p class="full-description-content d-none">                             
                                </p>
                                <div class="mt-3">
                                    <a href="javascript:void(0);" id="description-text" class="btn btn-primary review-btn text-white view-more-btn-description py-2" style="display: none; border-radius:20px;font-size: 14px;">View More</a>
                                </div>
                                <div id="description-data" style="display:none;">
                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($property && $property->websiteamenities->isNotEmpty())  
                        <div class="group">
                            <h3 class="ci-title text-dark">Amenities:</h3>  
                            <div class="content">
                                <ul class="amenities-list list-unstyled m-0">
                                    @foreach ($property->websiteamenities as $amenity)
                                    <li>
                                        <div class="amenities-small-icon">
                                            <img src="{{ asset('storage/amenities/' . $amenity->amenities_image) }}" alt="">
                                        </div>
                                        <span>{{ $amenity->amenities_name ?? '' }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        <div class="group">
                            <h3 class="ci-title text-dark">Property Location:</h3>  
                            <div class="card p-2" style="border-radius:10px;">
                                <!--<div id="map" style="border-radius:10px;"></div>-->
                                <iframe
                                width="100%"
                                height="300"
                                style="border:0;"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ $property->home->map_latitude }},{{ $property->home->map_longitude }}&z=18&output=embed">
                            </iframe>
                            </div>
                        </div>

                        @if ($property && $property->homeReviews->isNotEmpty()) 
                        <section class="group review-section" id="review">
                            <div class="content mb-3">
                                <div class="row align-items-center mb-4">
                                <div class="col">
                                    <h3 class="ci-title text-dark mb-0">Reviews</h3>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-primary review-btn text-white" data-fancybox data-src="#all-reviews">Read all <span class="icon-arrow-up-right1"></span></button>
                                </div>
                                </div>

                                <!-- Swiper -->
                                <div class="swiper user-reviews">
                                    <div class="swiper-wrapper">
                                        <!-- Slide 1 -->
                                        @foreach ($property->homeReviews as $reviews)
                                        <div class="swiper-slide review-slides">
                                            <div class="card review-card h-100">
                                                <div class="card-body review-content">
                                                    <p>{{$reviews->comment ?? ''}}</p>
                                                </div>
                                                <div class="card-footer review-footer">
                                                    <div class="row p-2">
                                                        <div class="col-3">
                                                            <div class="profile-img small-round-img">
                                                                {{collect(explode(' ', $reviews->guest_name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('')}}
                                                            </div>
                                                        </div>
                                                        <div class="col-9">
                                                        <div class="user-info d-flex flex-column">
                                                                <span class="text-dark">{{ strtoupper($reviews->guest_name) ?? ''}}</span>
                                                                <span class="rating">{{ number_format($reviews->rating,1) ?? ''}} <i class="icon-star"></i></span>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <!-- Controls -->
                                    <div class="row gx-3 align-items-center mt-4 pt-3 controls">
                                        <div class="col-auto">
                                            <div class="pagination">
                                                <div class="swiper-pagination"></div>
                                            </div>
                                        </div>
                                        <div class="col"><div class="line"></div></div>
                                        <div class="col-auto position-relative">
                                            <button class="btn p-0 swiper-outer-prev"><span class="icon-arrow-left"></span></button>
                                        </div>
                                        <div class="col-auto position-relative">
                                            <button class="btn p-0 swiper-outer-next"><span class="icon-arrow-right"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        @endif

                        <section class="section section-important-points mt-4">
                            <div class="container">
                                <h3>Important Information</h3>
                                <div class="row g-3">
                                @if(!empty($property->website_property_rules))
                                    <div class="col-12 col-xl-6">
                                        <div class="info-card p-3 card h-100">
                                            <div class="head-sec d-flex justify-content-between align-items-center">
                                            <h4 class="text-primary fw-bold mb-0">House Rules</h4>
                                            <a  data-fancybox data-src="#house-rules-fancy" href="javascript:void(0)"  class="btn btn-primary show-more-btn text-white">Show more <span class="icon-arrow-up-right1"></span>
                                            </a>
                                            </div>

                                            <p>{!! Str::limit($property->website_property_rules, 380) ?? '' !!}</p>

                                            <div style="display:none;" id="house-rules-fancy">
                                                <h4 class="text-primary fw-bold">House Rules</h4>
                                                <p>{!! $property->website_property_rules ?? '' !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($property->website_property_rules))
                                    <div class="col-12 col-xl-6">
                                        <div class="info-card p-3 card h-100">
                                            <div class="head-sec d-flex justify-content-between align-items-center">
                                            <h4 class="text-primary fw-bold mb-0">Cancellation Policy</h4>
                                            <a href="#"  data-fancybox data-src="#cancellation-policy-fancy" href="javascript:;"   class="btn btn-primary show-more-btn text-white">Show more <span class="icon-arrow-up-right1"></span>
                                            </a>
                                            </div>

                                            <p>{!! Str::limit($property->cancellation_policy, 380) ?? '' !!}</p>

                                            <div style="display:none;" id="cancellation-policy-fancy">
                                                <h4 class="text-primary fw-bold">Cancellation Policy</h4>
                                                <p>{!! $property->cancellation_policy !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="col-12 col-xl-4">

                    <div class="main-search-outer detail-page-search ms-0 property-page-checkout pb-0">
                        <span role="button" class="d-xl-none close-btn lh-1 fs-1 fw-light ps-3"><i class="icon-clear"></i></span>
                        <div class="main-search p-0">
                            <div class="row g-3 align-items-center">
                                <div class="col-12">
                                    <div class="bh-price fw-bold prices">
                                        <strong>&#8377; <span class="PricePerNight"></span></strong> <small class="fw-normal">per night</small>
                                    </div>
                                </div>

                                <div class="col-12 field-col d-flex position-relative date-search">
                                    <div class="text-start btn-checkin search-field form-control In-date">
                                    <small class="text-dark">Check In</small>
                                    <div class="search-field-value js-checkin-text">{{ date('jS M', strtotime($property->date_from)) ?? 'Select date' }}</div>
                                    </div>
                                    <div class="text-start btn-checkout search-field form-control">
                                        <small class="text-dark">Check out</small>
                                        <div class="search-field-value js-checkout-text">{{ date('jS M', strtotime($property->date_to)) ?? 'Select date' }}</div>
                                    </div>
                                    <div class="custom-dropdown calendar-dropdown">
                                        <input id="detail-page-calendar" type="text" style="display:none;" value="@if($property->date_from){{ date('Y-m-d', strtotime($property->date_from)) . ' - ' . date('Y-m-d', strtotime($property->date_to)) }}@endif"/>
                                    </div>
                                </div>
                                <div class="col-12 field-col position-relative add-guests" data-scope="header">
                                    <div class="text-start search-field search-field-guest">
                                        <small class="text-dark">Guests</small>
                                        <div class="search-field-value" total-guests-detail data-search-id="guests">1 Guest</div>
                                    </div>   
                                    <div class="custom-dropdown guests-counter guests-dropdown">
                                        <ul class="list-unstyled m-0">
                                            <li>
                                                <div class="row flex-nowrap align-items-center">
                                                    <div class="col">
                                                        <div class="guests-title">
                                                            <strong>Adults</strong>
                                                            <small>Ages 10+</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="counter">
                                                            <a href="javascript:void(0)" class="btn counter-col c-minus c-minus-detail disabled" data-type-detail="adults" data-minus-detail>
                                                                <span class="icon-minus"></span>
                                                            </a>
                                                            <div class="counter-col">
                                                                <input type="hidden" class="adults-count adultsCountDetail" name="" value="{{ $adult ?? 1 }}" data-min="{{ $adult ?? 1 }}" data-max="{{ $property->maximum_number_of_guests ?? '0' }}" data-adults-value>
                                                                <strong class="count-val count-val-detail">{{ $adult ?? 1 }}</strong>
                                                            </div>
                                                            <a href="javascript:void(0)" class="btn counter-col c-plus c-plus-detail" data-type-detail="adults" data-plus-detail>
                                                                <span class="icon-plus"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="row flex-nowrap align-items-center">
                                                    <div class="col">
                                                        <div class="guests-title">
                                                            <strong>Children</strong>
                                                            <small>Ages 0 -10</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="counter">
                                                            <a href="javascript:void(0)" class="btn counter-col disabled c-minus c-minus-detail" data-type-detail="children" data-minus-detail>
                                                                <span class="icon-minus"></span>
                                                            </a>
                                                            <div class="counter-col">
                                                                <input type="hidden" class="children-count childrenCountDetail" name="" value="{{ $child ?? 0 }}" data-children-value>
                                                                <strong class="count-val count-val-detail">{{ $child ?? 0 }}</strong>
                                                            </div>
                                                            <a href="javascript:void(0)" class="btn counter-col c-plus c-plus-detail" data-type-detail="children" data-plus-detail>
                                                                <span class="icon-plus"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>                        
                                        </ul>
                                    </div>
                                </div>    
                                
                                <div class="col-12">
                                    <button class="btn w-100 offers-btn" style="padding:5px 18px;" data-fancybox data-src="#offer-fancy" data-src="#coupon" data-custom-fancy id="coupon-href">Check offers and discounts</button>
                                </div>

                                <div class="col-12 col-xl-6" id="offer-fancy" style="display:none;">
                                    
                                    <form action="#">
                                        <div class="row gy-4">
                                            <div class="col">
                                                <h3 class="text-primary fw-bold">Apply Offer Code</h3>
                                            </div>
                                            <div class="col-auto close-icon">
                                                <a href="javascript:void(0)" class="fancy-close text-decoration-none" onclick="Fancybox.close()">
                                                    <span style="font-size: 24px; color: #cf9f62">&#10006;</span>
                                                </a>
                                            </div>
                                            
                                            @if($queryDiscountCoupon->count())
                                                <div class="col-12 mb-3">
                                                    <ul class="list-group">
                                                        @foreach($queryDiscountCoupon->sortByDesc('stay_date_from') as $coupon)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center position-relative">
                                                                <div>
                                                                    <div class="text-uppercase ">{{ $coupon->title }}</div>
                                                                    @if($coupon->stay_date_from || $coupon->stay_date_to)
                                                                        <small class="text-muted">
                                                                            Valid From: {{ \Carbon\Carbon::parse($coupon->stay_date_from)->format('d M Y') ?? 'N/A' }}
                                                                            To: {{ \Carbon\Carbon::parse($coupon->stay_date_to)->format('d M Y') ?? 'N/A' }}
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                                <div class="position-relative">
                                                                    <span class="text-uppercase">
                                                                        <span class="fw-bold bg-primary text-white p-1 rounded copy-coupon-btn" 
                                                                            data-code="{{ $coupon->code }}"
                                                                            style="cursor: pointer;">
                                                                            {{ $coupon->code }}
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="" class="mb-2">Enter your Offer Code</label>
                                                    <input type="hidden" name="discount_amount" id="discount_amount"/>
                                                    <input type="hidden" name="discount" id="discount"/>
                                                    <input type="text" class="form-control text-uppercase" name="coupon_code" id="coupon_code">
                                                </div>
                                                <span class="error justify-content-center coupon_error"></span>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group cs-input bottom-toolbar">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto d-md-none">
                                                            <!--<a href="javascript:void(0)" class="clear-btn">Clear</a>-->
                                                        </div>
                                                        <div class="col text-end">
                                                            <!--<button type="submit" class="btn btn-primary icon-link icon-link-hover w-md-100 apply_coupon" disabled>Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button> -->
                                                            <button type="submit" class="btn btn-primary w-md-100 apply_coupon rounded" disabled>Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                

                                <div class="col-12">
                                    <div class="table-subtotal">
                                        <table class="table table-sm mb-0 table-borderless">
                                            <tr class="fw-bold first-tr">
                                                <td>Subtotal - <span class="totalNightclass"></span> </td>
                                                <td align="right">&#8377;<span class="PriceWithPerNight"></span></td>
                                            </tr>
                                            <tr class="second-tr">
                                                <td> Extra Guest Charge (<span class="extraGuestCharge"></span> x <span class="totalNight"></span>)</td>
                                                <td align="right">&#8377; <span class="totalExtraGuestCharge"></span></td>
                                            </tr>
                                            <tr>
                                                <td>Taxes (<span class="tax">{{ $tax ?? '' }}</span>%)</td>
                                                <td align="right">&#8377; <span class="taxAmount"></span></td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Total incl. taxes</td>
                                                <td align="right">&#8377;<span class="TotalAmount"></span></td>
                                            </tr>
                                        <tr class="fw-bold totalRow">
                                            <td>Booking Amount Payable - 50%</td>
                                            <td align="right">&#8377;<span class="bookingPayableAmountText"></span></td>
                                        </tr>
                                        <tr class="fw-bold totalRow">
                                            <td>Balance upon Checkin</td>
                                            <td align="right">&#8377;<span class="balanceUponCheckinText"></span></td>
                                        </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <!--<form action="{{ route('property-book') }}" method="GET">  -->
                                    <!--    <button type="submit" class="btn fw-bold w-100 btn-primary cbooking rounded">Book Now</button>-->
                                    <!--</form>-->
                                    <!--<form action="{{ route('property-book') }}" method="GET">  -->
                                    <!--    @if($property->only_for_enquiry ==0)-->
                                    <!--       <button type="submit" class="btn py-3 fw-bold w-100 btn-primary cbooking rounded">Book Now</button>-->
                                    <!--    @else-->
                                    <!--        <button role="button" data-fancybox data-src="#enquiry" class="btn py-3 fw-bold w-100 btn-primary rounded">Enquire Now</button>-->
                                    <!--    @endif-->
                                    <!--</form>-->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- */row /* -->
        </div>
    </section>

    <section class="section section-why pb-0">
        <div class="container">
            <div class="section-heading">
                <div class="row gy-3">
                    <div class="col-12 col-sm text-center">
                        <h2>India's Trusted <span class="text-primary">Luxury Stays</span></h2>
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-center">
                <div class="col-12 col-lg order-2 order-lg-1">
                    <div class="fl flp position-relative">
                        <div class="whyBorder"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                        <img 
                                            src="{{ $servicesData->customer_service_icon ? asset('storage/' . $servicesData->customer_service_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{ $servicesData->customer_service_title ?? '' }}</h3>
                                        <p>{!! $servicesData->customer_service_short_description ?? '' !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                        <img 
                                            src="{{ $servicesData->privacy_flexibility_icon ? asset('storage/' . $servicesData->privacy_flexibility_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$servicesData->privacy_flexibility_title ?? ''}}</h3>
                                        <p>{!! $servicesData->privacy_flexibility_short_description ?? '' !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto order-1 order-lg-2">
                    <div class="roundLogo">
                        <img src="{{ asset('storage/' . $servicesData->image) }}" alt="" class="img-fluid">
                    </div>
                </div>
                <div class="col-12 col-lg order-3 order-lg-3">
                    <div class="fr frp position-relative">
                        <div class="whyBorder"></div>
                        <div class="row">
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                        <img 
                                            src="{{ $servicesData->professionally_managed_icon ? asset('storage/' . $servicesData->professionally_managed_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$servicesData->professionally_managed_title ?? ''}}</h3>
                                        <p>{!! $servicesData->professionally_managed_description ?? '' !!}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="iconBox">
                                    <div class="icon">
                                       <img 
                                            src="{{ $servicesData->best_feature_icon ? asset('storage/' . $servicesData->best_feature_icon) : '' }}"
                                            alt="Properties Icon" width="100">
                                    </div>
                                    <div class="content">
                                        <h3>{{$servicesData->best_feature_title ?? ''}}</h3>
                                        <p>{!! $servicesData->best_feature_description ?? '' !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- All reviews  -->
@if ($property && $property->homeReviews->isNotEmpty()) 
<div class="container section-all-reviews" id="all-reviews" style="display:none;">
    <div class="row">
        <div class="col-12 heading-reviews">
            <!-- <div class="heading-reviews"> -->
            <h3>Reviews</h3>
            <!-- </div> -->
        </div>
        <div class="feedback-section mt-3">
            <div class="row g-3">
                @foreach ($property->homeReviews as $reviews)
                <div class="col-12">
                    <div class="row">
                        <div class="review-card py-2 px-2">
                            <div class="row">
                                <div class="col-12 col-xl">
                                    <div class="col-12 col-xl-12 d-flex gap-3">
                                        <div class="profile-img small-round-img">
                                            {{collect(explode(' ', $reviews->guest_name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('')}}
                                        </div>
                                        <div class="about-info d-flex flex-column">
                                            <h6 class="text-dark mb-0">{{ strtoupper($reviews->guest_name) ?? ''}}</h6>
                                            <span class="rating">{{ number_format($reviews->rating,1) ?? ''}} <i class="icon-star"></i></span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-xl-12 mt-1">
                                        <div class="feedback-content">
                                            <p>{{$reviews->comment ?? ''}}</p>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 col-xl-3">
                                    @if($reviews->file_type == 'video')
                                    <a href="{{ $reviews->media_url ?? ''}}" data-fancybox class="review-video">
                                        <img src="{{ $reviews->thumbnail_url ?? ''}}" alt="" class="img-fluid rounded">
                                    </a>
                                    @else
                                    <a href="{{ $reviews->media_url  ?? ''}}" target="_blank">
                                        <img src="{{ $reviews->media_url  ?? ''}}" alt="" class="img-fluid rounded">
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
<!-- All reviews  -->


<div class="mobile-reserve-wrap d-xl-none">
    <div class="container">  
        <div class="row">
            <div class="col">
                <div class="m-booking-info">
                    <strong>₹<span class="PricePerNight"></span></strong> per night + taxes <br>
                    <ul class="list-unstyled mb-0 bi-info book-link">
                        <li class="date-text checkIn_chechout_mobile_display">{{ date('jS M', strtotime($property->date_from)) ?? '' }} - {{ date('jS M', strtotime($property->date_to)) ?? '' }}</li>
                        <li>
                            <span class="totalNightclass"></span>                            
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152L0 424c0 48.6 39.4 88 88 88l272 0c48.6 0 88-39.4 88-88l0-112c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 112c0 22.1-17.9 40-40 40L88 464c-22.1 0-40-17.9-40-40l0-272c0-22.1 17.9-40 40-40l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24L88 64z"/></svg>
                        </li>
                    </ul>                    
                </div>
            </div>
            <div class="col-auto">
                <!--<a href="javascript:void(0)" class="btn w-100 btn-primary px-3 make-reservation">Book Now</a>-->
                <!--<form action="{{ route('property-book') }}" method="GET">  -->
                <!--    <button type="submit" class="btn py-3 fw-bold w-100 btn-primary cbooking rounded">Book Now</button>-->
                <!--</form>-->
                
                <!--<form action="{{ route('property-book') }}" method="GET">  -->
                <!--    @if($property->only_for_enquiry ==0)-->
                <!--       <button type="submit" class="btn py-3 fw-bold w-100 btn-primary cbooking rounded">Book Now</button>-->
                <!--    @else-->
                <!--        <button role="button" data-fancybox data-src="#enquiry" class="btn py-3 fw-bold w-100 btn-primary rounded">Enquire Now</button>-->
                <!--    @endif-->
                <!--</form>-->
                
            </div>
        </div> 
    </div>
</div>


<div class="section-fancybox full-fanybox" id="share" style="display: none;max-width:480px;">
    <div class="row">
        <div class="col-12">
            <div class="fancy-heading">
                <div class="row">
                    <div class="col">
                        <h3>Share this place</h3>
                    </div>
                    <div class="col-auto close-icon">
                        <a href="javascript:void(0)" class="fancy-close text-decoration-none" onclick="Fancybox.close()">
                            <span style="font-size: 24px; color: #000">&#10006;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12">
            @php
                $currentUrl = url()->full(); // gets the full current URL
            @endphp                    
            <ul class="list-unstyled share-link-list m-0">
                <li>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $currentUrl ?? ''}}" target="_blank">
                    <svg style="width: 20px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/></svg>Facebook</a>
                </li>
                <li>
                    <a href="https://twitter.com/intent/tweet?url={{ $currentUrl ?? ''}}" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>Twitter X</a>
                </li>
                <li>
                    <a href="https://wa.me/?text={{ $currentUrl ?? ''}}" data-action="share/whatsapp/share" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>WhatsApp</a>
                </li>
                <li><a href="mailto:?body={{ $currentUrl ?? ''}}"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M64 112c-8.8 0-16 7.2-16 16l0 22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1l0-22.1c0-8.8-7.2-16-16-16L64 112zM48 212.2L48 384c0 8.8 7.2 16 16 16l384 0c8.8 0 16-7.2 16-16l0-171.8L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64l384 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64L0 128z"/></svg>Email</a></li>
            </ul>                
        </div>
    </div>
</div>

<div class="col-12 col-xl-4" id="enquiry" style="display:none;">
        <form method="post" id="enquiry_form">

            <input type ="hidden" name="checkInDate" id="enquiryCheckinDate" value="{{ $property->date_from }}">
            <input type ="hidden" name="checkOutDate" id="enquiryCheckOutDate" value="{{$property->date_from}}">
            <input type ="hidden" name="property_name" value="{{$property->unit_name_website}}">
            <input type ="hidden" name="pType" value="{{$property->pType}}">
            <input type ="hidden" name="pId" value="{{$property->id}}">
            <input type ="hidden" name="guest" id="enq_guest" value="{{$totGuest}}">
            <input type ="hidden" name="total_price_multiple" id="total_price_multiple" value="">


            <h2>{{ $property->unit_name_website ?? '' }} <span class="text-primary">{{ $property->locationData->location_name ?? '' }}, {{ $property->state ?? ''}}</span></h2>
            
           <div class="row g-4">
            <div class="col-12">
                <label class="mb-1">Your Name <sup class="text-danger">*</sup></label>
                <input type="text" class="form-control" name="name" id="name">
            </div>

            <div class="col-12">
                <label for="">Phone Number <sup>*</sup></label>
                <div class="row g-2 form-group d-flex">
                    @php
                        $countries = DB::table('countries')->get();
                    @endphp
                    <div class="col-auto">
                        <select name="country_code" id="country_code" class="form-select country_code" class="form-select">
                            @foreach ($countries as $country)
                                <option data-countryCode="{{$country->phonecode}}" value="{{ $country->phonecode }}"
                                    {{ $country->phonecode == 91 ? 'selected' : '' }}>
                                    {{ $country->iso }} (+{{ $country->phonecode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Phone Number" name="phone_number" id="phone_number" maxlength="12" minlength="6">
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label for="">Email Address <sup>*</sup></label>
                <input type="text" class="form-control" name="email" id="email">
            </div>

            <div class="col-12">
                <label for="">Message <sup>*</sup></label>
                <textarea name="message" id="message" cols="" rows="3" class="form-control" ></textarea>
            </div>

            {{-- <div class="col-12">
                <a href="" class="btn btn-primary rounded w-100 text-white fw-bold">Send Enquiry</a>
            </div> --}}
            <div class="col-12">
                <div class="form-group">
                    <button id="submitEnquiry" class="btn btn-primary rounded-pill fw-bold w-100 submit_enquiry" >Submit Enquiry

                    &nbsp;&nbsp; <span id="spinner_new_form" class="spinner-border spinner-border-sm " style="color: #fff; display: none;" role="status"  aria-hidden="true" ></span>
                </button>
                </div>
            </div>
            <div class="col-12">
                <div id="successMessage" class="alert alert-success d-none" role="alert">
                    Your enquiry has been submitted successfully!
                </div>
            </div>


           </div>
        </form>
    </div>
<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBadtfvHfxj3uAeNivR0Prec9tEWQUZoX0&loading=async&callback=initMap">
</script>

<script>
    let mapStyles = [
        {
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [
                {
                    "color": "#000"
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "all",
            "stylers": [
                {
                    "color": "#F2ECE3"
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
                    "color": "#c79f62"
                }
            ]
        },
        {
            "featureType": "road",
            "elementType": "labels",
            "stylers": [
                {
                    "color": "#000"
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
                    "color": ""
                },
                {
                    "visibility": "on"
                }
            ]
        }
    ]
    
    window.initMap = function(){
        const location = { lat: {{$property->home->map_latitude ?? '0' }}, lng: {{$property->home->map_longitude ?? '0' }} }; 
        // Create the map, centered at the location
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 16,
            center: location,
            styles: mapStyles,
            disableDefaultUI: true
        });
        // Add an icon in the center
        const iconMarker = new google.maps.Marker({
            position: location,
            map: map,
            icon: {
            url: "{{ asset('assets/website/images/map-logo.png')}}",
            scaledSize: new google.maps.Size(46, 46),
            anchor: new google.maps.Point(12, 20)
            },
        });

    }
</script>
@php
    $sortedImagesNew = $property->imagesWebsite
        ->where('type', 'image')
        ->sortByDesc(fn($img) => $img->tbl_ru_image_type_id == 1 ? 1 : 0)
        ->values(); // reset keys
@endphp
<script>
    let baseUrl = "{{ URL('/') }}";
        @if($property && $property->imagesWebsite)
            let images = @json($sortedImagesNew);
        @else
            let images = [];
        @endif
        console.log(images,"images");
        let sliders = Object.entries(images);
        let slideData = []
        if (sliders.length != 0) {
            sliders.forEach(item => {
                slideData.push({
                    src: baseUrl + '/' + item[1].filename
                })
            });
        }
        let galleryImages = {
            all: slideData
        }

    document.addEventListener("DOMContentLoaded", function(){
        Fancybox.bind('[data-fancybox]', {});  

        Fancybox.bind("[data-custom-fancy]", {
            hideScrollbar: true,
            closeButton: false,
        })
        
        const mobileGallery = new Swiper('.swiper-gallery', {
            spaceBetween: 0,
            slidesPerView: 1,
            grabCursor: true,          
            pagination: {
                el: ".section-mobile-gallery .swiper-pagination",
                dynamicBullets: true,
            },
        })

        var swiper = new Swiper(".user-reviews", {
                spaceBetween: 10,
                slidesPerView: 1,
                slidesPerGroup: 1,
                grabCursor: true,
                pagination: {
                el: ".review-section .controls .swiper-pagination",
                type: "fraction",
                formatFractionCurrent: (n) => (n < 10 ? "0" + n : n),
                formatFractionTotal: (n) => (n < 10 ? "0" + n : n),
                },
                navigation: {
                nextEl: ".review-section .controls .swiper-outer-next",
                prevEl: ".review-section .controls .swiper-outer-prev",
                },
                breakpoints: {
                768: {
                    slidesPerView: 2,
                    slidesPerGroup: 2,
                },
                1024: {
                    slidesPerView: 3,
                    slidesPerGroup: 3,
                }
                }
            });



        $(".book-link").on("click", function(){
            $("body").addClass("detail-booking-active");
        })


        $(".detail-page-search .close-btn").on("click", function(){
            $("body").removeClass("detail-booking-active");
        })
        $(document).on("click",'[data-gallery-type]', function(e){
        e.preventDefault();
        let galType = $(this).attr('data-gallery-type');
        let index = $(this).parents('.section-property-gallery').find('[data-gallery-type]').index(this);
        Fancybox.show(galleryImages[galType],{
            Thumbs: false,
            startIndex: galleryImages[galType][index] ? index : 0,
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
                
                },
            },
        
        });
    })

    })
</script>

<script>
    let minStay = 1;
    let minStayArray =  JSON.parse('{!! json_encode($minStayArray) !!}');
    let maximum_number_of_guests = Number("{{ $property->maximum_number_of_guests }}");
    document.addEventListener("DOMContentLoaded", function () {
        let parentElClass = '.detail-page-search';    
        let inputCalendar = document.getElementById('detail-page-calendar');
        
        let minStayArray = JSON.parse('{!! json_encode($minStayArray) !!}');
        let previouslyBookedCheckoutDates = JSON.parse('{!! json_encode($previouslyBookedCheckoutDates) !!}');
        let disabledDates = JSON.parse('{!! json_encode($propertyUnavailableDates) !!}');

        disabledDates = disabledDates.filter(date => !previouslyBookedCheckoutDates.includes(date));

        function resetDateInput(){
            $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
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
            clearButton: true,
            showTopbar: true,
            selectForward: true,
            enableCheckout: true,
            disabledDates: disabledDates,
            topbarPosition: 'bottom',
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);   
            },
            onDayClick: function() {    
                updateMinStay(datepickerHero);
                if(this.start){
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(parentElClass).find(".js-checkin-text").addClass('hasValue').text(startDate).parent().removeClass('active');
                    $(parentElClass).find(".js-checkout-text").parent().addClass('active');
                }
                if(this.end){
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(parentElClass).find(".js-checkout-text").addClass('hasValue').text(endDate); 
                }
                if(this.start && this.end){
                    $('.booknow').removeAttr('disabled');
                    let startDateMobile = fecha.format(this.start, 'Do MMM');
                    let endDateMobile = fecha.format(this.end, 'Do MMM');
                    $('.checkIn_chechout_mobile_display').text(`${startDateMobile} - ${endDateMobile}`);

                    ci_date = fecha.format(this.start, `YYYY-MM-DD`);
                    co_date = fecha.format(this.end, `YYYY-MM-DD`);
                    tot_no_of_days = datepickerHero.getNights();

                    $(".promo-code-tr").remove();
                    $('#coupon-href').removeClass('d-none');
                    $('#coupon_code').val('');
                    getPropertyPrice();
                    $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
                }
                if(!this.start && !this.end){
                    resetDateInput();
                }
            }
        });

        $(datepickerHero.datepicker).find(".datepicker__clear-button").text("Clear Dates");
        $(datepickerHero.datepicker).find(".datepicker__buttons").append('<button type="button" class="btn btn-link close-datepicker">Close</button>');

        $(document).on("click",".close-datepicker", function(){
            $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
        });

        $(document).on("click",".datepicker__month-button", function(){
            setTimeout(() => { updateMinStay(datepickerHero); }, 0);
        });

        $(document).on("click",'.datepicker__month-day--valid.datepicker__month-day--invalid', function(){
            let getDate = fecha.format(datepickerHero.start, `YYYY-MM-DD`);
            alert("Minimum stay can't be less than " + minStayArray[getDate] + " Nights");
        });

        $(".clear-dates").on("click", function(e){
            e.stopPropagation();
            resetDateInput();
        });

        let mm = gsap.matchMedia();

        mm.add(
        "(min-width: 1300px)", function () {
            ScrollTrigger.create({
                trigger: ".detail-page-search",
                start:()=>`top 120px`, 
                pin: true,     
                // markers: true,
                end: "bottom bottom",
                endTrigger: ".page-detail-column"
            });
        });


        $('#seeDetailsBtn').click(function(e) {
            e.preventDefault(); 
            
            var table = $('#checkoutTable');

            
            if (table.is(':visible')) {
                
                table.fadeOut(300); 
                $(this).text('See Details'); 
            } else {
            
                table.fadeIn(300); 
                $(this).text('Hide Details');
            }
        });
    });
</script>


{{-- custom code --}}
<script>
    let adults = "@php if(isset($adult)){ echo $adult; }else{ echo 1;} @endphp";
    let children = "@php if(isset($child)){ echo $child; }else{ echo 0;} @endphp";
    let tot_guest = "@php echo $totGuest; @endphp";
    let tot_no_of_days = "@php echo $property->no_of_nights; @endphp";
    let ptype = "@php echo $property->ptype; @endphp";
    let slug = "@php echo $property->url_key; @endphp";
    let ci_date = "@php echo $property->date_from; @endphp";
    let co_date = "@php echo $property->date_to; @endphp";
    let propertyId = "{{ $property->ru_property_id  }}";
    let property_id = "{{ $property->id  }}";
    let couponCode = 0;
    // let maximum_number_of_guests = "{{ $property->maximum_number_of_guests  }}";
    let adultsCount = parseInt(adults);
    let childrenCount = parseInt(children);
    let noOfBedRooms = "{{$property->no_of_bedrooms}}";
    let extraGuest =0;

    initializeGuestCounterDetail();
    $(document).on('click', '[data-type-detail]', function () {
        let dataTypeDetail = $(this).data('type-detail');
        counterdetail($(this), dataTypeDetail);
    });

    function initializeGuestCounterDetail() {
        let adultsCountDetail = parseInt($('.adultsCountDetail').val()) || 1; 
        let childrenCountDetail = parseInt($('.childrenCountDetail').val()) || 0;

        $('.adultsCountDetail').val(adultsCountDetail);
        $('.childrenCountDetail').val(childrenCountDetail);

        $('.adultsCountDetail').parent().find('.count-val-detail').text(adultsCountDetail);
        $('.childrenCountDetail').parent().find('.count-val-detail').text(childrenCountDetail);

        // Enable/disable minus buttons
        adultsCountDetail > 1 
            ? $('[data-type-detail="adults"][data-minus-detail]').removeClass('disabled') 
            : $('[data-type-detail="adults"][data-minus-detail]').addClass('disabled');

        // Check if maximum guests reached
        updateTotalGuestsDetail();
    }

    function counterdetail(el, dataTypeDetail) {
        let inputDetailEl = el.parent().find(`input[data-${dataTypeDetail}-value]`);
        let minusEl = el.parent().find('[data-minus-detail]');
        let plusEl = el.parent().find('[data-plus-detail]');

        let inputValue = parseInt(inputDetailEl.val()) || 0;
        if (el.hasClass('c-plus-detail')) {
        // if (dataTypeDetail === 'children' && inputValue >= 2) return; 
            inputValue++;
        } else {
            inputValue--;
        }
        inputValue = Math.max(0, inputValue); 
        if (dataTypeDetail === 'adults' && inputValue < 1) {
            inputValue = 1; 
        }
        inputDetailEl.val(inputValue);
        el.parent().find('.count-val-detail').text(inputValue);
        if (dataTypeDetail === 'adults') {
            inputValue > 1 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
        } else {
            inputValue > 0 ? minusEl.removeClass('disabled') : minusEl.addClass('disabled');
        }
        adultsCount = parseInt($('.adultsCountDetail').val()) || 0;
        childrenCount = parseInt($('.childrenCountDetail').val()) || 0;
        // let totalGuest = tot_guest = adultsCount + childrenCount;
        let totalGuest = tot_guest = adultsCount;

        // if (totalGuest >= maximum_number_of_guests) {
        //     $('[data-plus-detail]').each(function () {
        //         console.log(inputValue,"inputValue");
        //         $(this).addClass('disabled');
        //     });
        // } else {
        //     $('[data-plus-detail]').each(function () {
        //         $(this).removeClass('disabled');
        //     });
        // }

        if (totalGuest >= maximum_number_of_guests) {
            $('[data-plus-detail][data-type-detail="adults"]').addClass('disabled');
        }
        else {
            $('[data-plus-detail][data-type-detail="adults"]').removeClass('disabled');
        }
        
        if(dataTypeDetail == 'children'){
            if(childrenCount >=4){
                $('[data-plus-detail][data-type-detail="children"]').addClass('disabled');
            }
            else{
                $('[data-plus-detail][data-type-detail="children"]').removeClass('disabled');
            }
        }
        updateTotalGuestsDetail();
    }

    function updateTotalGuestsDetail() {
        adultsCount = parseInt($('.adultsCountDetail').val()) || 0;
        childrenCount = parseInt($('.childrenCountDetail').val()) || 0;
         let totalGuest = adultsCount + childrenCount;
        //let totalGuest = adultsCount;
        const totalGuestInputText = totalGuest === 0 
            ? 'Add guests' 
            : totalGuest > 1 
            ? `${totalGuest} Guests` 
            : `${totalGuest} Guest`;

        $('[total-guests-detail]').text(totalGuestInputText);

        // if (totalGuest >= maximum_number_of_guests) {
        //     $('[data-plus-detail]').each(function () {
        //         $(this).addClass('disabled');
        //     });
        // } else {
        //     $('[data-plus-detail]').each(function () {
        //         $(this).removeClass('disabled');
        //     });
        // }

        if (adultsCount >= maximum_number_of_guests) {
            $('[data-plus-detail][data-type-detail="adults"]').addClass('disabled');
        } else {
            $('[data-plus-detail][data-type-detail="adults"]').removeClass('disabled');
        }


        $(".promo-code-tr").remove();
        $('#coupon-href').removeClass('d-none');
        $('#coupon_code').val('');
        getPropertyPrice();
        
    }

    // getPropertyPrice();
    function getPropertyPrice(){
        $.ajax({
            url: "/get/ajax/property/price",
            type: 'get',
            headers: {'X-CSRF-TOKEN': $("input[name=_token]").val()},
            data: {
                ptype:ptype,
                adults:adultsCount,
                children:childrenCount,
                checkin_date: ci_date,
                checkout_date: co_date,
                propertyId: propertyId,
                tot_guest: tot_guest,
                tot_no_of_days: tot_no_of_days
            },
            success: function(res) {
                //$('.totalPrice').text(res.data.formatted_base_price)
                $('.PricePerNight').text(res.data.price_per_night_num_formatted)
                $('.PriceWithPerNight').text(res.data.total_price_multiple)
                $('.tax').text(res.data.tax)
                $('.taxAmount').text(res.data.formatted_total_taxable_amount)
                $('.TotalAmount').text(res.data.num_formatted_tot_price)
                
                let tot_no_of_night = parseInt(tot_no_of_days); // Convert string to number
                let nightText = tot_no_of_night + ' ' + (tot_no_of_night === 1 ? 'night' : 'nights');
                $('.totalNightclass').text(nightText);
                
                
                
                $('.totalNight').text(tot_no_of_days)
                $('.totalExtraGuestCharge').text(res.data.total_extra_guest_charge)
                $('.second-tr').hide();
                if (res.data && res.data.total_extra_guest_charge !== 0) {
                    $('.second-tr').show();
                }
                
                
                $('#extraGuest').val(res.data.extra_no_of_guest)
                $('#extraGuestm').val(res.data.extra_no_of_guest)
                 extraGuest = res.data.extraGuest;
                $('.extraGuestCharge').text(res.data.extra_guest_charge*res.data.extra_no_of_guest)
                // console.log(extraGuest);
                amountBeforeTax = res.data.amountBeforeTax + res.data.total_additional_charges;
                tax = initialTax =  res.data.tax;
                tax_amount = initialTaxAmount =  taxAmount =  res.data.tax_amount;
                totalPayableAmount = totalPayableInitialAmount =  amountBeforeTax + Math.round(tax_amount) ;
                price_per_night = initial_price_per_night =  res.data.per_night_price;
                formatted_total_taxable_amount = res.data.formatted_total_taxable_amount;
                additionalCharges = res.data.additionalCharges;
                additionalChargesAmount = res.data.total_additional_charges;
                let additionalChargesTr = '';
                if(additionalCharges && additionalCharges.length > 0){
                    additionalCharges.forEach(item => {
                        if(item.type_option =='Per_Stay'){
                            additionalChargesTr = additionalChargesTr+'<tr class ="additional-tr"><td>'+item.name+'</td><td align="right">&#8377;<span>'+item.price+'</span></td></tr>';

                        }
                        else{
                            additionalChargesTr = additionalChargesTr+'<tr class ="additional-tr"><td>'+item.name+'</td><td align="right">&#8377;<span>'+item.price*tot_no_of_days+'</span></td></tr>';
                        }
                        
                    });
                }
                $(".additional-tr").remove();
                $(additionalChargesTr).insertAfter('.first-tr');
                if(couponCode !=''){
                    console.log(couponCode,"couponCode");
                    applyCouponCode(couponCode, 'Apply ');
                }

                booking_amount_payable = res.data.booking_amount_payable;
                balance_upon_checkin = res.data.balance_upon_checkin;

                $('.bookingPayableAmountText').text(booking_amount_payable);
                $('.balanceUponCheckinText').text(balance_upon_checkin);
                let website_markup_price = res.data.website_markup_price;
                let formData = {
                    ptype:ptype,
                    adults:adultsCount,
                    children:childrenCount,
                    checkin: ci_date,
                    checkout: co_date,
                  //  propertyId: propertyId,
                    total_guests: tot_guest,
                    tot_no_of_days: tot_no_of_days,
                    slug: slug,
                    extraGuest:extraGuest,
                    website_markup_price: website_markup_price,
                   
                };
                $('.dynamic-hidden-input').remove();
                $.each(formData, function(key, value) {
                    let input = $('<input>').attr({
                        type: 'hidden',
                        name: key,
                        value: value
                    }).addClass('dynamic-hidden-input');
                    $('form').append(input);
                });
            },
            error: function(res) {
            }
        });
    }
</script>

<script>
    // code coupon start
        
    $("#coupon_code").keyup(function(e){
        $('.coupon_error').text('').removeClass('text-danger');
        if(e.target.value.length <= 2){
            $('.apply_coupon').prop('disabled', true);
        }
        else{
            $('.apply_coupon').prop('disabled', false);
        }
        
    });

    $(document).on('click', '.apply_coupon', function (e) {
        e.preventDefault();
        applyCouponCode($('#coupon_code').val(), $(this).text());
    });

    $(document).on('click', '.cus-link', function(e) {
        discountAmount = 0;
        discountCode = '';
        tax = initialTax;
        taxAmount = initialTaxAmount;
        totalPayableAmount = totalPayableInitialAmount;
        let totalPayableAmountHalf = totalPayableAmount/2
        $('.tax').text(tax)
        $('.taxAmount').text(formatted_total_taxable_amount)
        $('.TotalAmount').text(totalPayableAmount)
         
        $('.bookingPayableAmountText').text(totalPayableAmountHalf);
        $('.balanceUponCheckinText').text(totalPayableAmountHalf);

        $(".promo-code-tr").remove();
        $('#coupon-href').removeClass('d-none');
        $('#coupon_code').val('');
        $('input[name="discountAmount"]').val('');
        $('input[name="discountCode"]').val('');
        $('.apply_coupon').prop('disabled', true);
    });

    function applyCouponCode(code, text){
      //  alert(3443);
        $('.coupon_error').text('');
        $('.coupon_error').removeClass('text-danger');
        if(code !=''){
            if(text == 'Apply '){
                $.ajax({
                    url: "/booking/apply/coupon/code",
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: {
                        coupon_code: code,
                        checkin_date:ci_date,
                        checkout_date:co_date,
                        propertyId:property_id,
                        amountBeforeTax:amountBeforeTax
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
                            
                            let ta = amountAfterDiscount/tot_no_of_days
                            
                            let tax = 12;
                            if(ta > 7500){
                                tax = 18;
                            }
                            taxAmount = Math.round((amountAfterDiscount*tax)/100);
                          //  totalPayableAmount = amountAfterDiscount  + taxAmount;
                            let formattedTotalAmount = amountAfterDiscount  + taxAmount;
                            totalPayableAmount = formattedTotalAmount.toLocaleString();
                            let bookingAmount  = formattedTotalAmount/2;
                            console.log(totalPayableAmount,"totalPayableAmount");
                            console.log(formattedTotalAmount,"formattedTotalAmount");

                            $('.bookingPayableAmountText').text(bookingAmount);
                            $('.balanceUponCheckinText').text(bookingAmount);


                            $('.tax').text(tax)
                            $('.taxAmount').text(taxAmount)
                            $('.TotalAmount').text(totalPayableAmount)
                    
                            let discountTr = '<tr class="promo-code-tr"><td class="text-link">Offer Code ('+discountCode+')</td><td align="right" style="width:100px;" class="text-link">-&#8377;'+discountAmount+' <a href="javascript:void(0);" class="cus-link text-danger" style="font-size:13px;">&#128465</a></td></tr>';
                            $(discountTr).insertAfter('.second-tr');
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
        
    // code coupon end
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        $(document).on('click', '.copy-coupon-btn', function () {
            let code = $(this).data('code');
            let $btn = $(this);
            let $tooltip = $btn.find('.copied-tooltip');

            // Input me set karo
            $('#coupon_code').val(code);
            $('.apply_coupon').prop('disabled', false);
            // Copy to clipboard
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(() => {
                    showCopiedTooltip($tooltip);
                }).catch((err) => {
                    console.error('Clipboard API failed: ', err);
                });
            } else {
                fallbackCopy(code);
                showCopiedTooltip($tooltip);
            }

            function fallbackCopy(text) {
                let tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(text).select();
                try {
                    document.execCommand('copy');
                } catch (err) {
                    console.error('Fallback copy failed: ', err);
                }
                tempInput.remove();
            }

            function showCopiedTooltip($tooltip) {
                $tooltip.stop(true, true).fadeIn(200);
                setTimeout(() => {
                    $tooltip.fadeOut(200);
                }, 1200);
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#description-text').click(function(e) {
            e.preventDefault(); 
            var content = $('#description-data');
            if (content.is(':visible')) {
                
                content.fadeOut(300); 
                $(this).text('View More'); 
            } else {
            
                content.fadeIn(300); 
                $(this).text('View Less');
            }
        }); 
        
        
        // new way to manage full description
        const contentDiv = document.querySelector(".js-description-read-smore");
        const shortContentDiv = contentDiv.querySelector(".short-description-content");
        const fullContentDiv = contentDiv.querySelector(".full-description-content");
        const viewMoreBtn = document.querySelector(".view-more-btn-description");

    
        const fullContent = `{!! addslashes($property->description) !!}`;
        const charLimit = parseInt(contentDiv.getAttribute("data-read-smore-chars")) || 250;

   
        function truncateHTML(html, maxLength) {
            let div = document.createElement("div");
            div.innerHTML = html;
            let text = "";
            let truncatedHTML = "";
            let totalChars = 0;

            function recursiveTruncate(node) {
                if (totalChars >= maxLength) return;

                if (node.nodeType === Node.TEXT_NODE) {
                    let remaining = maxLength - totalChars;
                    if (node.nodeValue.length > remaining) {
                        truncatedHTML += node.nodeValue.substring(0, remaining) + "...";
                        totalChars += remaining;
                    } else {
                        truncatedHTML += node.nodeValue;
                        totalChars += node.nodeValue.length;
                    }
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    let openTag = `<${node.tagName.toLowerCase()}${Array.from(node.attributes).map(attr => ` ${attr.name}="${attr.value}"`).join("")}>`;
                    truncatedHTML += openTag;
                    node.childNodes.forEach(child => recursiveTruncate(child));
                    truncatedHTML += `</${node.tagName.toLowerCase()}>`;
                }
            }

            div.childNodes.forEach(node => recursiveTruncate(node));
            return truncatedHTML;
        }

        // Truncate content
        let truncatedContent = truncateHTML(fullContent, charLimit);

        // Show truncated or full content
        if (truncatedContent.length < fullContent.length) {
            shortContentDiv.innerHTML = truncatedContent;
            fullContentDiv.innerHTML = fullContent; 
            viewMoreBtn.style.display = "inline-block";
        } else {
            shortContentDiv.innerHTML = fullContent; 
            viewMoreBtn.style.display = "none"; 
        }

    
        let isFullContentVisible = false;
        viewMoreBtn.addEventListener("click", function () {
            if (isFullContentVisible) {
                
                shortContentDiv.innerHTML = truncatedContent;
                viewMoreBtn.textContent = "View More"; 
            } else {
            
                shortContentDiv.innerHTML = fullContent;
                viewMoreBtn.textContent = "View Less"; 
            }
            isFullContentVisible = !isFullContentVisible;
        });

        viewMoreBtn.addEventListener("click", function () {
            document.querySelector(".popup-content").innerHTML = fullContent;
        });
        
        
        $('#submitEnquiry').on('click', function(e) {
            e.preventDefault();
            var button = document.getElementById('submitEnquiry');
            var spinner = document.getElementById('spinner_new_form');
            spinner.style.display = 'inline-block';
            button.disabled = true;
            let formdata = $('#enquiry_form').serialize();
            $.ajax({
                url: "{{ route('enquire') }}",
                type: "POST",
                data: formdata,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response['status'] == true) {
                        spinner.style.display = 'none';
                        button.disabled = false;

                        const successMessage = $('#successMessage');
                        successMessage.removeClass('d-none').text('Thank You for your query. We will get back to you soon.!');


                        setTimeout(() => {
                            Fancybox.close();
                            successMessage.addClass('d-none');
                        }, 5000); // Hide after 5 seconds


                        $('#enquiry_form')[0].reset();


                    }
                    else {
                        var errors = response['errors'];
                        if (errors) {
                            $.each(errors, function(key, value) {
                                console.log(value[0], "value[0]");
                                var elementId = key.replace(/\./g, '_');
                                console.log(elementId, "key");
                                var inputElement = $('#' + elementId);
                                console.log(inputElement, "inputElement");
                                inputElement.addClass('border-danger');
                                // inputElement.next('p').addClass('text-danger').html(value[0]);
                            });
                        }
                        spinner.style.display = 'none';
                        button.disabled = false;
                    }
                },
            });
        });





   function toggleButtonState() {
        var phoneNumber = $('#phone_number').val();
        phoneNumber = phoneNumber.replace(/[^0-9]/g, '').slice(0, 12);
        if(phoneNumber[0] == '0'){
            $('#phone_number').addClass('border-danger');
            $('#phone_number').siblings('p').addClass('text-danger').html("Phone number can't start with 0.");
            
        }else{
            $('#phone_number').siblings('p').addClass('text-danger').html("");
            $('#phone_number').removeClass('border-danger').html('');
        }
        $('#phone_number').val(phoneNumber);
    }
    $('#phone_number').on('input', function() {
        toggleButtonState();
    }); 
        
    });
</script>
<script>
    document.getElementById('downloadBrochureBtn').addEventListener('click', function (e) {
        e.preventDefault();

        const btn = this;
        const spinner = document.getElementById('spinner_brochure');

        // Disable button + show spinner
        btn.classList.add('disabled');
        spinner.style.display = 'inline-block';

        fetch(btn.href)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Download failed');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'property-brochure.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                alert('Failed to download brochure.');
            })
            .finally(() => {
                // Always hide spinner and re-enable button
                spinner.style.display = 'none';
                btn.classList.remove('disabled');
            });
    });
</script>


@endsection