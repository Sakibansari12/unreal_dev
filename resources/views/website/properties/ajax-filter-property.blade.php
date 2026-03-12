@if(!empty($properties) && $properties->count() > 0)
    <div class="properties-listing">
        <div class="my-4">
          <h4 class="text-center other_regions">{{ $similar_text ?? '' }}</h4>
        </div>
        
        <div class="row g-4" id="propertyListContainer">
            @foreach($properties as $property)
                <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 property-card"> <!-- Added property-card class here -->
                    <a href="{{ route('property-detail', ['ptype' => strtolower($property->ptype),'slug' => $property->url_key,
                            'location_name' => request()->input('location_name'),
                            'filter_type' => request()->input('filter_type'),
                            'location' => request()->input('location'),
                            'check_in' => request()->input('check_in') ?? '', 
                            'check_out' => request()->input('check_out') ?? '', 
                            'adults' => request()->input('adults'), 
                            'children' => request()->input('children'), 
                            'total_guests' => request()->input('total_guests'),
                            'type' => request()->input('type'),
                            'city_id' => $city_id ?? '',
                            'guestCount' => $guestCount ?? '',
                            'next_available' => $next_available ?? '',
                            ]) }}" target="_blank" class="card card-properties">
                        <div class="card-img-top">
                            <div class="swiper-outer">
                                <div class="swiper swiper-property-image">
                                    <div class="swiper-wrapper">
                                        
                                             @php
                                                $images = $property->imagesWebsite->sortByDesc(function ($image) {
                                                    return $image->tbl_ru_image_type_id == 1 ? 1 : 0;
                                                });
                                            @endphp
                                            
                                            @if ($images->isNotEmpty())
                                                @foreach ($images as $image)
                                                    <div class="swiper-slide">
                                                        <div class="imgBox">
                                                            <img loading="lazy" src="{{ asset($image->website_image) }}" alt="">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="swiper-slide">
                                                    <div class="imgBox">
                                                        <img loading="lazy" src="{{ asset('assets/website/images/no-image.png') }}" alt="">
                                                    </div>
                                                </div>
                                            @endif

                                            
                                        <!--@if ($property->imagesWebsite->where('type', 'image')->isNotEmpty())-->
                                        <!--    @foreach ($property->imagesWebsite->where('type', 'image') as $image)-->
                                        <!--        <div class="swiper-slide">-->
                                        <!--            <div class="imgBox">-->
                                        <!--                <img loading="lazy" src="{{ asset($image->website_image) }}" alt="">-->
                                        <!--            </div>-->
                                        <!--        </div>-->
                                        <!--    @endforeach-->
                                        <!--@else-->
                                        <!--    <div class="swiper-slide">-->
                                        <!--        <div class="imgBox">-->
                                        <!--            <img loading="lazy" src="{{ asset('assets/website/images/no-image.png') }}" alt="">-->
                                        <!--        </div>-->
                                        <!--    </div>-->
                                        <!--@endif-->
                                        
                                    </div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-button-next"></div>        
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                            @php
                                $averageRating = $property->homeReviews->avg('rating');
                            @endphp

                            @if ($averageRating)
                                @php
                                    $roundedRating = floor((float) $averageRating);
                                @endphp

                                <div class="rating">
                                    {{ number_format($averageRating, 1) }}
                                    <i class="icon-star text-primary"></i>
                                </div>
                            @endif
                            <div class="overlayInfo">
                                Upto {{ $property->maximum_number_of_guests == 1 ? $property->maximum_number_of_guests . ' Guest' : $property->maximum_number_of_guests . ' Guests' }} + {{ $property->no_of_bedrooms == 1 ? $property->no_of_bedrooms . ' Room' : $property->no_of_bedrooms . ' Rooms' }} + {{ $property->no_of_bathrooms == 1 ? $property->no_of_bathrooms . ' Bathroom' : $property->no_of_bathrooms . ' Bathrooms' }}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="card-title h4 mb-0">{{ $property->unit_name_website ?? '' }}</div>
                                <div class="card-text">{{ $property->locationData->location_name ?? '' }}, {{ $property->state }}</div>
                                <div class="price">from &#8377;{{ formatIN($property->per_night_price) }} per night</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @if ($propertyCount < $totalStays)
    <div class="properties-loader text-center mt-5">
        <a href="#/" class="btn btn-plus" id="loadMoreButton"><div class="icon"><span class="icon-plus"></span></div> View all properties</a>
    </div>
    @endif
@else
 <div class="properties-listing">
    <div class="row g-4" id="propertyListContainer">
        <div class="col-12">
            <div class="card-body text-center">
                No Property Found!
            </div>     
        </div>   
    </div>   
</div>   
@endif