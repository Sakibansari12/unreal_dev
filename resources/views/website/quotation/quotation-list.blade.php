@extends('website.layouts.app')
@section('content')
<style>
    .strike {
        position: relative;
        display: inline-block;
    }

    .strike:after {
        position: absolute;
        content: '';
        width: 100%;
        height: 1px;
        background: #ff0000;
        top: 50%;
        left: 0px;
    }
</style>
<div class="page-wrapper cms-pages">
    <section class="section section-pb-40">
        <div class="container">
            <div class="section-heading">
            </div>
            <div class="row g-equal">
                @if(!empty($quotationProperty) && $quotationProperty->count() > 0)
                @foreach($quotationProperty as $property)

                <div class="col-12 col-md-6 col-lg-4 col-xxl-3">
                    <div class="card card-property">
                        <div class="card-img-top">
                            <a
                                href="{{ route('quotation-property-detail', ['ptype' => $property->property_details->ptype, 'slug' => base64_encode($property->id)]) }}"
                                class="swiper-outer">
                                <div class="swiper swiper-property-image">
                                    <div class="swiper-wrapper">
                                        @if ($property->property_details->imagesWebsite->where('type', 'image')->isNotEmpty())
                                        @foreach ($property->property_details->imagesWebsite->where('type', 'image') as $image)

                                        <div class="swiper-slide">

                                            <img loading="lazy" src="{{ asset($image->website_image) }}" alt="{{ $property->property_details->unit_name_website ?? 'Property Image' }}">

                                        </div>
                                        @endforeach
                                        @else
                                        <div class="swiper-slide">
                                            <img loading="lazy" src="{{ asset('assets/images/noimage-property.jpg') }}" alt="No Image Available">

                                        </div>
                                        @endif
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </a>
                            <div class="card-tag">{{ $property->property_details->locationData->location_name ?? '' }}</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col align-self-center">
                                    <a href="{{ route('quotation-property-detail', ['ptype' => $property->property_details->ptype, 'slug' => base64_encode($property->id)]) }}" class="card-title h4 mb-0">{{ isset($property->property_details->unit_name_website) ? $property->property_details->unit_name_website : '' }}</a>
                                </div>
                                <div class="col-12">
                                    <div class="card-text">
                                        <ul class="property-info">
                                            <li>{{ isset($quotationDetail->no_of_nights) ? $quotationDetail->no_of_nights . ' ' . ($quotationDetail->no_of_nights == 1 ? 'night' : 'nights') : '' }}</li>
                                            <li>{{ isset($quotationDetail->checkin_date, $quotationDetail->checkout_date) ? \Carbon\Carbon::parse($quotationDetail->checkin_date)->format('d') . '-' . \Carbon\Carbon::parse($quotationDetail->checkout_date)->format('d M') : '' }}</li>
                                        </ul>
                                    </div>
                                    <!--<div class="card-text fw-normal">INR {{ number_format($property['per_night_price'] * $quotationDetail->no_of_nights) }} total before taxes</div>-->
                                    <div class="pr-price mb-4"><small>
                                            @if($property->payable_amount>0)
                                            @if(round($property->discounted_per_night_price) < round($property->current_per_night_price))
                                                <span class="strike">INR {{ number_format($property->current_per_night_price) }} /night</span>
                                                @endif
                                                <span class="text-primary">INR {{ number_format($property->discounted_per_night_price) }} /night</span> total before taxes <span class="p-room"></span>
                                                @else
                                                @if(round($property->discounted_per_night_price) < round($property->current_per_night_price))
                                                    <span class="strike">INR {{ number_format($property->current_per_night_price) }} /night</span>
                                                    @endif
                                                    <span class="text-primary">INR 0 /night</span> total before taxes
                                                    @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-12 col-md-6 col-lg-4 col-xxl-3">
                    <div class="card card-property">
                        <div class="card-img-top">
                            No Property Found!
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let propertySwiperImageElement = document.querySelectorAll('.swiper-property-image')
        propertySwiperImageElement.forEach((item, idx) => {
            const PropertyImageSwiper = new Swiper(item, {
                slidesPerView: 1,
                pagination: {
                    el: ".swiper-property-image .swiper-pagination",
                    dynamicBullets: true,
                    //clickable: true
                },
                navigation: {
                    nextEl: $(item).find('.swiper-button-next').get(0),
                    prevEl: $(item).find('.swiper-button-prev').get(0)
                },
            })
        })
    })
</script>
@endsection