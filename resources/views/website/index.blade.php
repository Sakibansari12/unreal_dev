
@extends('website.layouts.app')
@section('content')
    <style>
    .pagination {
  -webkit-transform:translate3d(0,0,0);
}
.section-bg img{
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
    <section class="section flex-wrap section-hero section-bg">
        @foreach($banner_data as $banner)
            @php $heading = $banner->heading; $subtitle = $banner->subtitle; @endphp
        <div class="section-bg">
            <img src="{{ asset($banner->image) }}" class="d-none d-md-block" alt="unreal estate banner">
            <img src="{{ asset($banner->mobile_image) }}" class="d-md-none" alt="unreal estate mobile banner">
        </div>
        @endforeach
        <!-- <div class=" flex-grow-1 ">
        </div> -->
        <div class="mobile-search-wrapper container d-lg-none">
            @include('website.search.mobile-search')
        </div>
        <div class="container">
            @include('website.search.search')
        </div>
        <div class="container hero-content-container homeHContent">
            <div class="row">
                <div class="col-12">
                    <div class="hero-card">
                        <h2 class="fw-normal"><?php echo $heading; ?></h2>
                        <h1><?php echo $subtitle; ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section section-filters" id="scroll-fixed">
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
    {{-- Location --}}
    <section class="section section-swiper firstSection">
        <div class="container">
            <div class="section-heading">
                @php
                    $tags_data = getTags();
                @endphp
                <div class="row">
                    <div class="col">
                        <h2>{{ $tags_data->tag_title ?? '' }}</h2>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="{{ route('property-list', ['type' => 'all-property']) }}" class="icon-link icon-link-hover">See All <i class="bi bi-chevron-right"></i></a>
                    </div>
                    <div class="col-12">
                        <p>{{ $tags_data->tab_sub_title ?? '' }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="swiper-main-outer">
                        <div class="swiper swiper-property overflow-visible">
                            <div class="swiper-wrapper">
                                @if(!empty($onTopPropertyArray))
                                @foreach($onTopPropertyArray->take(5) as $property)
                                @php $priceAndAvaliability = getPriceAndAvalibility($property->ru_property_id, $property->pType);   @endphp
                                    <div class="swiper-slide">
                                        <!--<p>let it down</p>-->
                                        <div class="card card-property card-location">
                                                <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}" target="_blank" class="card-img-top">
                                                <div class="swiper-outer">
                                                    <div class="swiper swiper-property-image">
                                                        <div class="swiper-wrapper">
                                                        @if($property->images->isNotEmpty())
                                                            @foreach($property->images->take(3) as $media)
                                                                <div class="swiper-slide">
                                                                    <img  loading="lazy" src="{{ $media->website_image ?? asset('storage/home/images/no-image.png') }}" class="w-100" alt="Image Title Goes Here">
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="swiper-slide">
                                                                <img  loading="lazy" src="{{ asset('storage/home/images/no-image.png') }}" class="w-100" alt="No Image Available">
                                                            </div>
                                                        @endif
                                                        </div>
                                                        <div class="swiper-button-next"></div>
                                                        <div class="swiper-button-prev"></div>
                                                        <div class="swiper-pagination"></div>
                                                    </div>
                                                </div>
                                                <!-- <div class="card-tag">{{ isset($property->location) ? $property->location : '' }}</div> -->
                                            </a>
                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <div class="col-12 align-self-center">
                                                        <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}" target="_blank" class="card-title h4 mb-0">{{ isset($property->unit_name_website) ? $property->unit_name_website : '' }}</a>
                                                    </div>
                                                   
                                                    <div class="col">
                                                        <div class="card-text">{{ isset($property->location) ? $property->location : '' }}, {{ isset($property->state) ? $property->state : '' }}

                                                        </div>
                                                    </div>
                                                    @if(!empty($property->tags[0]['tags_name']))
                                                        <div class="col-auto">
                                                                <a href="{{ route('property-list', ['tags[]' => $property->tags[0]['id'], 'tagName' => $property->tags[0]['tags_name'], 'type' => 'tag']) }}" class="card-tag">
                                                                    <img loading="lazy"  src="{{ optional($property)->tags[0]['tags_image'] ? asset('storage/tag/' . $property->tags[0]['tags_image']) : '' }}">
                                                                    <span>{{$property->tags[0]['tags_name'] ?? ''}}</span>
                                                                    {{-- @php
                                                                        $tag = $tag_data->where('tag_show_on_page', 0)->first();
                                                                    @endphp
                                                                    
                                                                    <span>{{ optional($tag_data->where('tag_show_on_page', 0)->last())->tags_name ?? '' }}</span> --}}
                                                                </a>
                                                           
                                                        </div>
                                                     @endif    
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <div class="card-text mt-0">from &#8377;{{ number_format($priceAndAvaliability['per_night_price'] ?? '' ) }} / night</div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}" target="_blank" class="icon-link icon-link-hover"><span>VIEW</span> <i class="bi bi-chevron-right"></i></a>
                                                   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @endif
                            </div>
                            <div class="swiper-main-pagination swiper-pagination bottom-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Location Based Property start--}}

    @foreach($locations as $location)
        @if($location->properties->count() > 0)
            <section class="section section-swiper section-swiper-small {{ $loop->index != 0 ? 'section-swiper-small' : '' }} {{ $loop->last ? 'pb-lg-4' : '' }}">
                <div class="container">
                    <div class="section-heading">
                        <div class="row">
                            <div class="col">
                                <h2>{{ isset($location->title) ? $location->title : '' }}</h2>
                            </div>
                            <div class="col-auto align-self-center">
                                <a href=" {{ route('property-list', ['location' => $location->location_name, 'locationName' => $location->location_name  ]) }}" class="icon-link icon-link-hover">See All <i class="bi bi-chevron-right"></i></a>
                            </div>      
                            <div class="col-12">
                                <p>{{ isset($location->sub_title) ? $location->sub_title : '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-12">
                            <div class="swiper-main-outer">
                                <div class="swiper swiper-property overflow-visible">
                                    <div class="swiper-wrapper">
                                    @foreach($location->properties->take(5)->orderBy('position') as $property)
                                        @php $priceAndAvaliability = getPriceAndAvalibility($property->ru_property_id, $property->pType);   @endphp

                            
                                        <div class="swiper-slide">
                                            <div class="card card-property">
                                                <div class="card-img-top">
                                                    <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}" target="_blank" class="swiper-outer">
                                                        <div class="swiper swiper-property-image">
                                                            <div class="swiper-wrapper">
                                                                @if($property->imagesWebsite->isNotEmpty())
                                                                        @foreach($property->imagesWebsite->take(3) as $media)
                                                                            <div class="swiper-slide">
                                                                                <img  loading="lazy" src="{{ $media->website_image ?? asset('storage/home/images/no-image.png') }}" class="w-100" alt="Image Title Goes Here">
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <!-- Display this only if no images are available -->
                                                                        <div class="swiper-slide">
                                                                            <img  loading="lazy" src="{{ asset('storage/home/images/no-image.png') }}" class="w-100" alt="No Image Available">
                                                                        </div>
                                                                    @endif
                                                            </div>
                                                            <div class="swiper-button-next"></div>
                                                            <div class="swiper-button-prev"></div>
                                                            <div class="swiper-pagination"></div>
                                                        </div>
                                                    </a>

                                                    <!-- @php
                                                        $firstTag = $property->tags->first();
                                                    @endphp
                                                    @if(!empty($firstTag))
                                                    <a href="{{ route('property-list', ['tags[]' => $firstTag->id, 'tagName' => $firstTag->tags_name, 'type' => 'tag']) }}" class="card-tag">
                                                        
                                                    
                                                        <img  loading="lazy" src="{{ isset($firstTag->tags_image) ? asset('storage/tag/' . $firstTag->tags_image) : '' }}" alt="">
                                                        {{ $firstTag->tags_name ?? '' }}
                                                    </a>
                                                    @endif -->
                                                    
                                                </div>
                                             
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col align-self-center">
                                                            <a href="{{ route('property-detail', ['ptype' => $property->ptype, 'slug' => $property->url_key]) }}" target="_blank" class="card-title h4 mb-0">{{ isset($property->unit_name_website) ? $property->unit_name_website : '' }}</a>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="card-text">
                                                                <ul class="property-info">
                                                                    <li>
                                                                        {{ $priceAndAvaliability['no_of_nights'] }}
                                                                        @if($priceAndAvaliability['no_of_nights'] > 1)
                                                                        nights
                                                                        @else
                                                                            night
                                                                        @endif
                                                                    </li>

                                                                    <li>
                                                                        @if(isset($priceAndAvaliability['next_available_date_from'], $priceAndAvaliability['next_available_date_to']))
                                                                            {{ \Carbon\Carbon::parse($priceAndAvaliability['next_available_date_from'])->format('d') . '-' . 
                                                                            \Carbon\Carbon::parse($priceAndAvaliability['next_available_date_to'])->format('d M') }}
                                                                        @endif
                                                                    </li>
                                                                </ul> 
                                                            </div>
                                                            <div class="card-text fw-normal">&#8377;{{ number_format($priceAndAvaliability['per_night_price']) }} total before taxes</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-main-pagination swiper-pagination bottom-pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach
    {{-- Location Based Property end--}}
    <script defer>
        document.addEventListener('DOMContentLoaded', () => {
            const filterSwiper = new Swiper('.swiper-filter', {
                slidesPerView: "auto",
                freeMode: true,
                watchSlidesProgress: true,
            })
            
            
            let PropertySwiperElement = document.querySelectorAll('.swiper-property');
            let propertySwiperImageElement = document.querySelectorAll('.swiper-property-image');


            PropertySwiperElement.forEach((item, idx) => {
                const PropertySwiper = new Swiper(item, {
                    spaceBetween: 30,
                    grabCursor: true,
                    freeMode: true,
                    mousewheel: {
                        enabled: true,
                        forceToAxis: true
                    },
                    virtual: {
                        enabled: true,
                        addSlidesAfter: 3,
                        addSlidesBefore: 3
                    },
                    pagination: {
                        el: $(item).find('.swiper-main-pagination').get(0),
                        dynamicBullets: true,
                        clickable: true
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1.2,
                            pagination: false
                        },
                        768: {
                            slidesPerView: 1.8,
                            pagination: false
                        },
                        992: {
                            slidesPerView: 2.4, 
                            //pagination: true,
                        },
                        1200: {
                            slidesPerView: 2.3,
                        },
                        1350: {
                            slidesPerView: 3.2,
                        },
                        1700: {
                            slidesPerView: 3.5,
                        }
                    }
                })


                PropertySwiper.on("slideChangeTransitionEnd", function(){
                   console.log("update")
                })



            })            
            
           
            propertySwiperImageElement.forEach((item, idx) => {
                const PropertyImageSwiper = new Swiper(item, {
                    slidesPerView: 1,
                    allowTouchMove: false,
                    nested: true,
                    virtual: {
                        enabled: true,
                        addSlidesAfter: 3,
                        addSlidesBefore: 3
                    },
                    pagination: {
                        el: $(item).find('.swiper-pagination').get(0),
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