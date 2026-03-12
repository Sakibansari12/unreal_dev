@extends('website.layouts.app')
@section('content')
@if($type_show_heading != 'location')
<section class="section section-filters propertiesPage" id="scroll-fixed">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="innerFilterWrap">
                    <div class="swiper swiper-filter overflow-visible">
                        <div class="swiper-wrapper">
                            @if($tag_data->isNotEmpty())
                            @foreach($tag_data as $tag)
                            <div class="swiper-slide">
                                <div class="card card-filter">
                                    <a href="{{ route('property-list', ['tags[]' => $tag->id, 'tagName' => $tag->tags_name, 'type' => 'tag']) }}" class="card-body">
                                        <div class="card-icon">
                                            <img loading="lazy" src="{{ isset($tag->tags_image) ? asset('storage/tag/' . $tag->tags_image) : '' }}" alt="{{ $tag->tags_name ?? '' }}">
                                        </div>
                                        <div class="card-text">{{ isset($tag->tags_name) ? $tag->tags_name : '' }}</div>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<div class="page-wrapper">


    <section class="section section-pb-40">
        <div class="container">
            @if(request('type') === 'all-property')
            <div class="section-heading">
                @php
                $tags_data = getTags();
                @endphp
                <div class="row">
                    <div class="col">
                        <div class="row gx-3 align-items-baseline">
                            <div class="col-12 col-lg-auto">
                                <div class="back-nav">
                                    <a href="/" class="icon-link icon-link-hover back-link h2">
                                        <i class="icon-arrow-left"></i>
                                        <span class="d-lg-none">Back</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-lg" data-pin-disabled>
                                <h2>{{ $tags_data->tag_title ?? '' }}</h2>
                                <p>{{ $tags_data->tab_sub_title ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(request('type') === 'tag')
            <div class="section-heading">
                @php
                $tag_data = \App\Models\TblTag::where('tags_name', request('tagName'))->first();
                @endphp
                <div class="row">
                    <div class="col">
                        <div class="row gx-3 align-items-baseline">
                            <div class="col-12 col-lg-auto">
                                <div class="back-nav">
                                    <a href="/" class="icon-link icon-link-hover back-link h2">
                                        <i class="icon-arrow-left"></i>
                                        <span class="d-lg-none">Back</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-lg" data-pin-disabled>
                                <h2>{{ $tag_data->tag_title ?? '' }}</h2>
                                <p>{{ $tag_data->tab_sub_title ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

          



            @if($type_show_heading == 'location')
            <div class="section-heading">
                <div class="row">
                    <div class="col">
                        <div class="row gx-3 align-items-baseline">
                            <div class="col-12 col-lg-auto">
                                <div class="back-nav">
                                    <a href="/" class="icon-link icon-link-hover back-link h2">
                                        <i class="icon-arrow-left"></i>
                                        <span class="d-lg-none">Back</span>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-lg" data-pin-disabled>
                                <h2>{{$loc_data->title ?? ''}}</h2>
                                <p>{{$loc_data->sub_title ?? ''}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(!empty($areas))
            <div class="tag-filter section section-pb-40 pt-0">
                <div class="swiper swiper-tag-filter overflow-visible">
                    <div class="swiper-wrapper">
                        @foreach($areas as $area)
                        <div class="swiper-slide tag-item-area">
                            <a href="javascript:void(0)" class="tag-item area-filter-alender
                                    
                                    @if(request()->has('areas') && in_array($area->area_name, explode(',', request()->input('areas')))) active @endif
                                    
                                    " data-area-id="{{ $area->area_name }}">
                                {{ $area->area_name ?? '' }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endif


            <div class="row g-equal">
          
                @if(!empty($properties) && $properties->count() > 0)
                @foreach($properties as $property)
                <div class="col-12 col-md-6 col-lg-4 col-xxl-3">
                    <div class="card card-property">
                        <div class="card-img-top">
                            <a href="
                                        
                                          {{ route('property-detail', ['ptype' => strtolower($property->ptype),'slug' => $property->url_key,
                            'location_name' => request()->input('location_name'),
                            'filter_type' => request()->input('filter_type'),
                            'location' => request()->input('location'),
                            'check_in' => request()->input('check_in') ?? '', 
                            'check_out' => request()->input('check_out') ?? '', 
                            'adults' => request()->input('adults'), 
                            'children' => request()->input('children'), 
                            'total_guests' => request()->input('total_guests'),
                            'tot_guest' => request()->input('tot_guest'),
                            'petsCount' => request()->input('petsCount'),
                            'all_total_guests' => request()->input('all_total_guests'),
                            'type' => request()->input('type'),
                            'city_id' => $city_id ?? '',
                            'guestCount' => $guestCount ?? '',
                            'next_available' => $next_available ?? '',
                            ]) }}
                                        
                                        
                                        " target="_blank" class="swiper-outer">
                                <div class="swiper swiper-property-image">
                                    <div class="swiper-wrapper">
                                        @if($property->imagesWebsite->isNotEmpty())
                                        @foreach($property->imagesWebsite as $media)
                                        <div class="swiper-slide">
                                            <img loading="lazy" src="{{ isset($media->website_image) ? asset($media->website_image) : '' }}" class="w-100" alt="">
                                        </div>
                                        @endforeach
                                        @else
                                        <div class="swiper-slide">
                                            <img loading="lazy" src="{{ asset('storage/home/images/no-image.png') }}" class="w-100" alt="No Image Available">
                                        </div>
                                        @endif
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </a>
                            <div class="card-tag">{{ isset($property->location) ? $property->location : '' }}</div>
                            <!--<a href="javascript:void(0)" class="card-tag"><i class="icon-beach-access"></i>{{ isset($property->location_name) ? $property->location_name : '' }}</a>-->
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col align-self-center">
                                    <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}" target="_blank" class="card-title h4 mb-0">{{ isset($property->unit_name_website) ? $property->unit_name_website : '' }}</a>
                                </div>
                                <div class="col-12">
                                    <div class="card-text">
                                        <ul class="property-info">
                                            <li>{{ isset($property->no_of_nights) ? $property->no_of_nights . ' ' . ($property->no_of_nights == 1 ? 'night' : 'nights') : '' }}</li>
                                            <li>{{ isset($property->date_from, $property->date_to) ? \Carbon\Carbon::parse($property->date_from)->format('d') . '-' . \Carbon\Carbon::parse($property->date_to)->format('d M') : '' }}</li>
                                        </ul>
                                    </div>
                                    <div class="card-text fw-normal">&#8377;{{ number_format($property->per_night_price * $property->no_of_nights) }} total before taxes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
                @else
                <h4 class="d-lg">No Properties Found.</h4>
                @endif
            </div>

            @if($properties->lastPage() > 1)
            <div class="section-small border-bottom border-light">
                <div class="row">
                    <div class="col-12">
                        <div class="pagination-list">
                            <ul>
                               

 @if($properties->lastPage() > 1)
                                 {{-- Previous --}}
                    <li class="{{ $properties->onFirstPage() ? 'disabled' : '' }}">
                        <a href="{{ $properties->previousPageUrl() ?? '#' }}">
                            <i class="icon-chevron-left"></i>
                        </a>
                    </li>

                    {{-- Page Numbers --}}
                    @foreach($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                        <li class="{{ $page == $properties->currentPage() ? 'active' : '' }}">
                            <a href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Next --}}
                    <li class="{{ !$properties->hasMorePages() ? 'disabled' : '' }}">
                        <a href="{{ $properties->hasMorePages() ? $properties->nextPageUrl() : '#' }}">
                            <i class="icon-chevron-right"></i>
                        </a>
                    </li>
@endif


                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filterSwiper = new Swiper('.swiper-filter', {
            slidesPerView: "auto",
            freeMode: true,
            watchSlidesProgress: true,
        })

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
        });
        
        const tagFilterSwiper = new Swiper('.swiper-tag-filter', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            freeMode: true,
            //spaceBetween: 20,
        })
    })
</script>
@endsection