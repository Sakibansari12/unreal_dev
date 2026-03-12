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
                        <div class="col text-end checkIn_chechout_mobile_date">
                            @if($property->date_from)
                               {{ \Carbon\Carbon::parse($property->date_from)->format('jS M') }} to {{ \Carbon\Carbon::parse($property->date_to)->format('jS M') }}
                            @else
                               Add dates
                            @endif
                        </div>
                    </div>
                </div>
                <div class="accordion-data">
                    <input type="text" id="mobile-calender" style="display: none;" value="@if($property->date_from){{ date('Y-m-d', strtotime($property->date_from)) . ' - ' . date('Y-m-d', strtotime($property->date_to)) }}@endif">
                </div>
            </div>
            <div class="accordion-box">
                <div class="accordion-title">
                    <div class="row align-items-center">
                        <div class="col"><h2>Who?</h2></div>
                        <div class="col text-end search-field-value-detail add-guests-detail-value"> Add Guests</div>
                    </div>
                </div>
                <div class="accordion-data">
                    <div class="guests-counter">
                        <ul class="list-unstyled m-0">
                            @if(!empty($guest_data) && $guest_data->isNotEmpty())
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
                                                    <!--<a href="javascript:void(0)" class="btn counter-col c-minus  {{ ($guest->type == 'Adults' && $guest->count == 1) || ($guest->type != 'Adults' && $guest->count == 0) ? 'disabled' : '' }}" data-detail-type="{{ $guest->type }}" data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, -1)">-->
                                                    <!--    <span class="icon-minus"></span>-->
                                                    <!--</a>-->
                                                    <!--<div class="counter-col ">-->
                                                    <!--    <input type="hidden" class="count-input count-detail-input" name="{{ strtolower($guest->title) }}_count" value="{{ isset($guest->count) ? $guest->count : 0 }}">-->
                                                    <!--    <strong class="count-val count-detail-val">{{ isset($guest->count) ? $guest->count : 0 }}</strong>-->
                                                    <!--</div>-->
                                                    <!--<a href="javascript:void(0)" class="btn counter-col c-plus" data-detail-type="{{ $guest->type }}"-->
                                                    <!--data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, 1)" >-->
                                                    <!--    <span class="icon-plus"></span>-->
                                                    <!--</a>-->
                                                    
                                                    @php

                                                    $guestCount = 0;

                                                       if ($guest->type == 'Adults' && $adult) {

                                                           $guestCount = $adult;

                                                       } elseif ($guest->type == 'Children' && $child) {

                                                           $guestCount = $child;

                                                       } elseif ($guest->type == 'Pets' && $pet) {

                                                           $guestCount = $pet;

                                                       } else {

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

                                                    data-allow-detail-guest-count="{{ $guest->allow_guest_count }}" onclick="updateDetailCounter(this, 1)" >

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
    <div class="card-footer">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <a href="" class="clear-mobile-search">Clear All</a>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary search-next">Next <i class="icon-chevron-right"></i></button>
                <!-- <button style="display:none;" class="btn btn-primary search-submit make-reservation"> Reserve <i class="bi icon-chevron-right"></i></button> -->
                 <form action="{{ route('property-book') }}" method="GET">
                        <button style="display:none;" type="submit" class="btn btn-primary search-submit make-reservation">Reserve <i class="icon-chevron-right"></i></button>
                </form>
            </div>

             



        </div>
    </div>
</div>
<!--<div class="mobile-location-search d-md-none">-->
<!--    <div class="form-group mb-3">-->
<!--        <button class="btn close-mobile-location-search"><i class="icon-arrow-left"></i></button>-->
<!--        <input type="text" class="form-control box-shadow-1">-->
<!--        <button class="btn clear-loc-input"><i class="icon-close"></i></button>-->
<!--    </div>-->
<!--    <ul class="location-search-list">-->
<!--        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Bengaluru, Karnataka">Bengaluru, Karnataka</a></li>-->
<!--        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Mangalore, Karnataka">Mangalore, Karnataka</a></li>-->
<!--        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Bengaluru, Karnataka">Bengaluru, Karnataka</a></li>-->
<!--        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Mangalore, Karnataka">Mangalore, Karnataka</a></li>-->
<!--    </ul>-->
<!--</div>-->
