
<aside class="col-12 col-xl-auto align-self-start">
    <div class="card filter-card" id="sidebar">
        <div class="card-header py-3"><i class="icon-filter"></i>Filters <span role="button" class="d-lg-none ms-auto close-btn lh-1 fs-3 fw-light ps-3">&times;</span></div>
        @if (!request('collection'))
          @if($locationName)
           <div class="card-header bg-primary text-white py-2">{{ $locationName ?? '' }}</div>
        @endif
        @endif
        <div class="card-body p-0">
            <div class="nano filter-content-wrap">
                <div class="nano-content">
                    

                    <div class="filter-box fb-price">
                        <h3>Price Range</h3>
                        <div class="price-range">              
                            <div class="range-outer mt-3">
                                <div id="range-slider"></div>
                            </div>
                    
                            <div class="row gx-2 mt-4">
                                <div class="col">
                                    <ul class="nav">
                                        <li>Min. &#8377;</li>
                                        <li class="pi-min-val"><span>1000</span></li>
                                    </ul>
                                </div>
                                <div class="col">
                                    <ul class="nav">
                                        <li>Max. &#8377;</li>
                                        <li class="pi-max-val"><span>150000</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="min_price" id="min_price" value="{{ request('min_price') ?? 1000 }}">
                    <input type="hidden" name="max_price" id="max_price" value="{{ request('max_price') ?? 150000 }}">
                                        
                    @php
                        $bedroomCount = request()->input('bedrooms', 1); // default 0
                    @endphp

                    <div class="filter-box fb-rooms">
                        <h3>Rooms</h3>
                        <div class="row gx-2 align-items-center">
                            <div class="col">
                                <h4><small>Number of Bedrooms</small></h4>
                            </div>
                            <div class="col-auto">
                                <ul class="list-unstyled mb-0 d-flex align-items-center counter-box">
                                    <li>
                                        <button class="btn rooms-decrement" type="button">-</button>
                                    </li>
                                    <li class="rc-value" id="bedroomCountDisplay">{{ $bedroomCount }}</li>
                                    <li>
                                        <button class="btn rooms-increment" type="button">+</button>
                                    </li>
                                </ul>
                                <!-- hidden input for form submission or URL param -->
                                <input type="hidden" id="bedroomsInput" name="bedrooms" value="{{ $bedroomCount }}">
                            </div>
                        </div>
                    </div>

                    <!--<div class="filter-box fb-nav">-->
                    <!--    <h3>Property Type</h3>-->
                    <!--    <ul class="nav-list list-unstyled mb-0">-->
                    <!--        <li>-->
                    <!--            <div class="ch-box">-->
                    <!--                <input type="radio" id="homeType1" name="property_type" value="All Homes"-->
                    <!--                    @if(!request()->has('property_type') || request()->input('property_type') == 'All Homes') checked @endif />-->
                    <!--                <label for="homeType1">All Homes</label>-->

                    <!--            </div>-->
                    <!--        </li>-->
                    <!--        @foreach(App\Models\TblHomeType::where('status', 1)->get() as $homeTypeKey => $homeTypeDetail)-->
                    <!--            <li>-->
                    <!--                <div class="ch-box">-->
                    <!--                    <input type="radio" id="homeType{{ $homeTypeKey+2 }}" name="property_type" -->
                    <!--                            value="{{ $homeTypeDetail->name }}"-->
                    <!--                            @if(request()->input('property_type') == $homeTypeDetail->name) checked @endif>-->
                    <!--                    <label for="homeType{{ $homeTypeKey+2 }}">{{ $homeTypeDetail->name }}</label>-->
                    <!--                </div>-->
                    <!--            </li>-->
                    <!--        @endforeach-->
                    <!--    </ul>-->
                    <!--</div>-->
                    
                        <div class="filter-box fb-nav">
                            <h3>Property Type</h3>
                            <ul class="nav-list list-unstyled mb-0">
                                <li>
                                    <div class="ch-box">
                                        <input type="checkbox" id="homeType1" class="all-homes" name="property_type[]" value="All Homes"
                                            @php
                                                $selected = request()->input('property_type');
                                                $checkAll = empty($selected) || (is_array($selected) && in_array('All Homes', $selected));
                                            @endphp
                                            {{ $checkAll ? 'checked' : '' }} />
                                        <label for="homeType1">All Homes</label>
                                    </div>
                                </li>
                                @foreach(App\Models\TblHomeType::where('status', 1)->get() as $homeTypeKey => $homeTypeDetail)
                                    <li>
                                        <div class="ch-box">
                                            <input type="checkbox" 
                                                class="home-type"
                                                id="homeType{{ $homeTypeKey+2 }}" 
                                                name="property_type[]" 
                                                value="{{ $homeTypeDetail->name }}"
                                                @if(is_array(request()->input('property_type')) && in_array($homeTypeDetail->name, request()->input('property_type')))
                                                    checked
                                                @endif>
                                            <label for="homeType{{ $homeTypeKey+2 }}">{{ $homeTypeDetail->name }}</label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    
                    
                    <div class="filter-box fb-nav">
                        <h3>Location</h3>
                        <ul class="nav-list list-unstyled mb-0">
                            @foreach(App\Models\TblLocation::where('status', 1)->get() as $locationKey => $locationDetail)
                            <li>
                                <div class="ch-box">
                                    <input type="checkbox" id="locationId{{ $locationKey+1 }}" name="location[]" 
                                        value="{{ $locationDetail->slug_name }}"
                                        @if(in_array($locationDetail->slug_name, explode(',', request()->input('location'))) ) checked @endif>
                                    <label for="locationId{{ $locationKey+1 }}">{{ $locationDetail->location_name }}</label>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>


                    <div class="filter-box fb-nav">
                        <h3>Top Filters</h3>
                        <ul class="nav-list list-unstyled mb-0">
                            @php
                            $selectedAmenities = request()->input('amenities', []); // null ki jagah empty array default
                        @endphp
                        
                        @foreach(App\Models\TblAmenities::where('status', 1)->get() as $amenityKey => $amenityDetail)
                            <li>
                                <div class="ch-box">
                                    <input 
                                        type="checkbox" 
                                        id="amenity{{ $amenityKey + 1 }}" 
                                        name="amenities[]" 
                                        value="{{ $amenityDetail->id }}"
                                        {{ in_array($amenityDetail->id, $selectedAmenities) ? 'checked' : '' }}
                                    >
                                    <label for="amenity{{ $amenityKey + 1 }}">{{ $amenityDetail->amenities_name }}</label>
                                </div>
                            </li>
                        @endforeach
                              
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer p-0 d-xl-none">
            <div class="row g-0">
                <div class="col">
                    <a href="{{ route('property-list', ['type' => 'all-property']) }}" class="btn rounded-0 w-100 btn-light" style="border:0!important;">
                        CLEAR
                    </a>
                </div>
                <div class="col">
                    <button class="btn close-btn rounded-0 w-100 btn-primary">
                        DONE
                    </button>
                </div>
            </div>
        </div>
    </div>
</aside>