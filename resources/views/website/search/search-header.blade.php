@php
    $guest_data = getGuestData();
    $location_data = getLocationData();
@endphp
<div class="main-search-outer py-3 hero-search-detail">
    <div class="main-search">
        <div class="row g-0 align-items-center">
            <div class="col-3 field-col position-relative">
                <label class="search-field search-field-location" for="loc1" data-search-id="location">
                  <strong>Where</strong>
                  <div class="search-data">
                        <input id="location-second" class="form-control" value="{{ request('location') && empty(request('locationName')) ? request('location') : '' }}"  placeholder="Search for a city..."

                        style="{{ !empty(request('location')) && empty(request('locationName')) ? 'opacity: 1;' : '' }}"

                        >
                        <div class="swiper cities-placeholder" style="{{ !empty(request('location')) ? 'opacity: 0;' : '' }}">
                            <div class="swiper-wrapper">
                                @if (!empty($location_data) && $location_data->isNotEmpty())
                                    @foreach ($location_data as $location)
                                        <div class="swiper-slide-sec swiper-slide">
                                            @if (!empty(request('location')))
                                                {{ request('location') }}
                                            @else
                                                {{ isset($location->location_name) ? $location->location_name : ''}}

                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </div> 
                        </div>
                  </div>
                </label>
                <div class="custom-dropdown custom-dropdown-loc">
                    <h2>Search by City</h2>
                    <input type="hidden" name="location_id" id="location_id" value="{{ $city_id ?? '' }}">
                    <input type="hidden" name="location_new_name" id="location_new_name" value="{{ request('location') ?? '' }}">
                    <div class="row g-0">
                        <div id="city-results-sec" class="row g-0">
                        @if (!empty($location_data) && $location_data->isNotEmpty())
                                @foreach ($location_data as $location)
                                    <div class="col-4">
                                        <div class="loc-city">
                                            <div class="loc-img">
                                                <a href="javascript:void(0)"  class="city-image-sec" 
                                                data-citysecname="{{ isset($location->location_name) ? $location->location_name : ''}}" data-cityid="{{ isset($location->id) ? $location->id : ''}}">
                                            <img src="{{ isset($location->image) && $location->image ? url('storage/location/' . $location->image) : asset('assets/website/images/noimage.jpg') }}" alt="">
                                            </a> 
                                            </div>
                                            <h3>
                                                {{ isset($location->location_name) ? $location->location_name : ''}}
                                            </h3>
                                        </div>
                                    </div> 
                                @endforeach
                        @endif
                        </div>
                        <div class="col-4 d-flex align-items-center justify-content-center">
                            <a href="{{ route('property-list', ['type' => 'all-property']) }}" class="icon-link icon-link-hover">See All <i class="bi icon-chevron-right"></i></a>
                        </div>                        
                    </div>
                </div>
            </form>
            </div>
            <div class="col field-col d-flex position-relative">
                <input type="hidden" name="start_date" id="start_date"  class="start_date">
                <input type="hidden" name="end_date" id="end_date"  class="end_date">
                <button class="btn text-start btn-checkin search-field" id="checkIn_Sec" data-search-id="dates">
                    <strong>Check in</strong>
                    <div class="search-field-value search-field-value-sec js-checkin-text js-checkin-text-sec">
                      @if (!empty(request('check_in')))
                           {{ date("jS M", strtotime(request('check_in')))  }}
                      @else
                         Add dates
                      @endif
                        </div>
                    <span class="clear-dates clear-dates-sec">&times;</span>
                </button>
                
                <button class="btn text-start btn-checkout search-field" data-search-id="dates">
                    <strong>Check out</strong>
                    <div class="search-field-value search-field-value-sec js-checkout-text js-checkout-text-sec">
                        @if (!empty(request('check_out')))
                            {{ date("jS M", strtotime(request('check_out')))  }}
                        @else
                            Add dates
                        @endif
                        </div>
                    <span class="clear-dates clear-dates-sec">&times;</span>
                </button>
                
                <div class="custom-dropdown calendar-dropdown">
                     <!--<input id="hero-calendar-sec" type="text" style="display:none;" />-->
                     <input id="hero-calendar-sec" type="text" style="display:none;"  value="@if(!empty(request('check_in')) && !empty(request('check_out')) && !empty(request('location'))){!! date('Y-m-d', strtotime(request('check_in'))) . ' - ' . date('Y-m-d', strtotime(request('check_out'))) !!}@endif"/>
                </div>
            </div>
            <div class="col field-col position-relative">
                <button class="btn text-start guests-tab search-field search-field-guest" data-search-id="guests">
                    <strong>Who</strong>
                    <div class="search-field-value search-field-value-sec add-guests-value-sec" data-total-guest>Add guests</div>
                </button>
                <a href="#" id="search-link-sec">   
                    <button class="btn btn-search btn-search-sec">
                        <div class="btn btn-primary">
                            <i class="icon-search"></i><span>Search</span>
                        </div>
                    </button> 
                </a>     
                <div class="custom-dropdown guests-counter guests-dropdown">
                    <ul class="list-unstyled m-0">
                        <!-- Adults Counter -->
                        <!-- PHP Template Code -->
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
                                                   @php
                                                        $guestCount = 0;

                                                        if (request()->has('adults') && $guest->type == 'Adults') {
                                                            $guestCount = request('adults');

                                                        } elseif (request()->has('children') && $guest->type == 'Children') {
                                                            $guestCount = request('children');
                                                        } elseif (request()->has('petsCount') && $guest->type == 'Pets') {
                                                            $guestCount = request('petsCount');
                                                        } else {
                                                            $guestCount = $guest->count ?? 0;
                                                        }
                                                    @endphp


                                                
                                                    <a href="javascript:void(0)" 
                                                        class="btn counter-col counter-col-sec c-minus 
                                                            {{ ($guest->type == 'Adults' && $guestCount == 1) || ($guest->type != 'Adults' && $guestCount == 0) ? 'disabled' : '' }}" 
                                                        data-type="{{ $guest->type }}" 
                                                        data-allow-guest-count="{{ $guest->allow_guest_count }}" 
                                                        onclick="updateCounterSec(this, -1)">
                                                        <span class="icon-minus"></span>
                                                    </a>
                                                    <!--<a href="javascript:void(0)" class="btn counter-col counter-col-sec c-minus {{ ($guest->type == 'Adults' && $guest->count == 1) || ($guest->type != 'Adults' && $guest->count == 0) ? 'disabled' : '' }}" data-type="{{ $guest->type }}" data-allow-guest-count="{{ $guest->allow_guest_count }}" onclick="updateCounterSec(this, -1)">-->
                                                    <!--    <span class="icon-minus"></span>-->
                                                    <!--</a>-->
                                                    <div class="counter-col counter-col-sec">
                                                        <!--<input type="hidden" class="count-input-sec" name="{{ strtolower($guest->title) }}_count" value="{{ isset($guest->count) ? $guest->count : 0 }}">-->
                                                        <!--<strong class="count-val count-val-sec">{{ isset($guest->count) ? $guest->count : 0 }}</strong>-->
                                                    <input type="hidden" class="count-input-sec" name="{{ strtolower($guest->title) }}_count" value="{{ $guestCount }}">
                                                    <strong class="count-val count-val-sec">{{ $guestCount }}</strong> 
                                                    </div>
                                                    <a href="javascript:void(0)" class="btn counter-col counter-col-sec c-plus" data-type="{{ $guest->type }}" data-allow-guest-count="{{ $guest->allow_guest_count }}" onclick="updateCounterSec(this, 1)">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    let checkin_date = $('.start_date').val(); 
    let checkout_date = $('.end_date').val();
    let check_check_ind_date, check_check_outd_date;
    document.addEventListener("DOMContentLoaded", function () {
        let parentElClass = '.hero-search-detail';    
        let inputCalendar = document.getElementById('hero-calendar-sec');
        function resetDateInput(){
          $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
          datepickerHeader.clearDatepicker();
          datepickerHeader.clear();
        }    
        window.datepickerHeader = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,           
            // clearButton: true,
            // minNights: 4,
            clearButton: true,
            showTopbar: true,
            topbarPosition: 'bottom',
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".js-checkin-text").addClass('hasValue').html(startDate);
                $(parentElClass).find(".js-checkout-text").addClass('hasValue').html(endDate);   
               // $(parentElClass).find(".start_date").addClass('hasValue').val(startDate);
             //   $(parentElClass).find(".end_date").addClass('hasValue').val(endDate);   
                //calendarBtn.toggle();             
            } ,
            onDayClick: function() {                
                if(this.start){
                    $(".js-checkout-text").text("Check out");
                    //$(".btn-end-date span").text("Departure");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(parentElClass).find(".js-checkin-text").addClass('hasValue').text(startDate).parent().removeClass('active');
                    $(parentElClass).find(".js-checkout-text").parent().addClass('active');
                   // $(parentElClass).find(".start_date").addClass('hasValue').val(startDate).parent().removeClass('active');
                   // $(parentElClass).find(".end_date").parent().addClass('active');
                    //checkin_date  = fecha.format(this.start, `Do MMM YYYY`);
                    check_check_ind_date = fecha.format(this.start, `Do MMM`);
                }
                if(this.end){
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(parentElClass).find(".js-checkout-text").addClass('hasValue').text(endDate); 
                  //  $(parentElClass).find(".end_date").addClass('hasValue').text(endDate); 
                   // checkout_date  = fecha.format(this.end, `Do MMM YYYY`);
                   check_check_outd_date = fecha.format(this.end, `Do MMM`);
                }
                if(this.start && this.end){
                    const data = {
                        check_in: checkin_date,
                        check_out: checkout_date,
                    };
                    checkin_date = fecha.format(this.start, `Do MMM YYYY`);
                    checkout_date = fecha.format(this.end, `Do MMM YYYY`)
                   // setSessionVariableSec(data);
                    $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
                   // $(parentElClass).find(".start_date,.end_date").parent().removeClass('active');
                }
                if(!this.start && !this.end){
                    resetDateInput()
                }
            }         
        });
        
        $(datepickerHeader.datepicker).find(".datepicker__clear-button").text("Clear Dates")
        $(datepickerHeader.datepicker).find(".datepicker__buttons").append('<button type="button" class="btn btn-link close-datepicker">Close</button>');

        $(document).on("click",".close-datepicker", function(){
            $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
        })

        inputCalendar.addEventListener(
            "afterClear",
            function () {
                resetDateInput();
            },
            false
        );
        
        $(".clear-dates").on("click",function(e){
            e.stopPropagation();
            resetDateInput();
        });
    });
/* Location Second page */
let adults_count_sec = 0;
let children_count_sec = 0;
let pets_count_sec = 0;
let selectedAreasC = [];
//let City_Sec_Id = null;
//let city_name_sec = '';
let City_Sec_Id = $('#location_id').val();
let city_name_sec = $('#location_new_name').val();
 $(document).on('click', '.city-image-sec', function() {
    const locationEl = $('#location-second')
    city_name_sec = $(this).data('citysecname');
    console.log(city_name_sec,"city_name_sec");
    City_Sec_Id = $(this).data('cityid');
    locationEl.val(city_name_sec);
    if(locationEl){
        $('.swiper-slide-sec').empty();
        locationEl.css('opacity', "1");
        $('#checkIn_Sec').trigger('click');
    }
    // const data = {
    //         city_slug_name: city_name_sec,
    //         city_id: City_Sec_Id,
    //     };
    //     setSessionVariableSec(data);
}); 
$(document).on('click', '.btn-search-sec', function(e) {
    e.preventDefault();
    if(city_name_sec ==''){
        alert('Please Select Destination');
        return false;
    }
    else{
        console.log(check_check_ind_date, check_check_outd_date)
        const totalguests  = $('.add-guests-value-sec').text();
        const searchData = {
                location: city_name_sec,
                check_in: checkin_date,
                check_out: checkout_date,
                city_id: City_Sec_Id,
                // tot_guest: totalguests,
                // adultsCount: adults_count_sec,
                // childrenCount: children_count_sec,
                // petsCount: pets_count_sec
                all_total_guests: all_total_guests_sec,
                adults: adults_count,
                children: children_count,
                petsCount: pets_count,
        }; 
        let search = true;
        if( check_check_ind_date && check_check_outd_date==undefined){
            search = false;
            alert('Please select checkout date')
            return false
        }
        if(search){
            dynamicAjaxRequestSec(searchData);
        }
    }    
});
 function updateCounterSec(element, increment) {
    const type = element.getAttribute('data-type');
    const allow_guest = element.getAttribute('data-allow-guest-count');
    const counterContainer = element.closest('.counter');
    const countDisplay = counterContainer.querySelector('.count-val-sec');
    const hiddenInput = counterContainer.querySelector('.count-input-sec');
    const minusBtn = counterContainer.querySelector('.c-minus');
    const plusBtn = counterContainer.querySelector('.c-plus');
    let count = parseInt(hiddenInput.value) || 0;
    count += increment;
    if (type === 'Adults') {
        count = Math.max(1, count); 
    } else {
        if (type === 'Children') {
            const maxChildren = 2;
            if (count >= maxChildren) {
                plusBtn.classList.add('disabled');
                count = maxChildren;
            } else {
                plusBtn.classList.remove('disabled');
            }
            count = Math.max(0, count);
        } else {
            count = Math.max(0, count);
        }
    }
    countDisplay.textContent = count;
    hiddenInput.value = count;
    if (type === 'Adults') {
        minusBtn.classList.toggle('disabled', count <= 1); 
    } else {
        minusBtn.classList.toggle('disabled', count === 0); 
    }
    let listType;
    if(type == 'children'){
        listType = type;
    }else{
        listType = ''
    }
    updateGuestSummarySec(type, allow_guest);
    // const data = {
    //     all_total_guests: all_total_guests_sec,
    //     adultsCount: adults_count,
    //     childrenCount: children_count,
    //     petsCount: pets_count
    // };
    // setSessionVariableSec(data);
}
function updateGuestSummarySec(type, allow_guest) {
    let adultsCount = 0;
    let childrenCount = 0;
    let petsCount = 0;
    document.querySelectorAll('.counter-col-sec .count-input-sec').forEach(input => {
        const count = parseInt(input.value) || 0;
        let type = input.name.replace('_count', ''); 
        if (type === 'adults') {
            adultsCount = count;
        } else if (type === 'children') {
            childrenCount = count;
        } else if (type === 'pets') {
            petsCount = count;
        }
    });
    /* let combinedGuests;
    if(allow_guest == 1){
        const combinedGuests = adultsCount + childrenCount + petsCount;
    }else{
        const combinedGuests = adultsCount
    } */
    adults_count = adultsCount;
    pets_count = petsCount; 
    children_count = childrenCount;
    const combinedGuests = adultsCount + childrenCount
    const guestSummary = [];
    if (combinedGuests > 0) {
        const guestText = combinedGuests === 1 ? "Guest" : "Guests";
        guestSummary.push(`${combinedGuests} ${guestText}`);
    }
    if (petsCount > 0) {
    const petText = petsCount === 1 ? "Pet" : "Pets";
    guestSummary.push(`${petsCount} ${petText}`);
}
    const displayText = all_total_guests_sec = guestSummary.length > 0 ? guestSummary.join(', ') : 'Add guests';
    const guestSummaryElement = document.querySelector('.search-field-value-sec.add-guests-value-sec');
    if (guestSummaryElement) {
        guestSummaryElement.textContent = displayText;
    } else {
        console.error('Element not found: .search-field-value-sec.add-guests-value-sec');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    updateGuestSummarySec();
});
</script>
<script>
    $(document).ready(function() {
        let LocationData = @json($location_data);
        $('#location-second').on('keyup', function() {
            let query = $(this).val().toLowerCase();  
            $('#city-results-sec').empty();  
            const filteredData = LocationData.filter(location => {
                return location.location_name.toLowerCase().includes(query);
            });
            if (filteredData.length > 0) {
                $.each(filteredData, function(index, city) {
                    let cityHtml = `
                        <div class="col-4">
                            <div class="loc-city">
                                <div class="loc-img">
                                    <a href="javascript:void(0)" class="city-image-sec" 
                                         data-citysecname="${city.location_name}" data-cityid="${city.id}">
                                        <img src="${city.image ? '{{ url('storage/location') }}/' + city.image : '{{ asset('assets/website/images/noimage.jpg') }}'}" alt="${city.location_name}">
                                    </a> 
                                </div>
                                <h3>${city.location_name}</h3>
                            </div>
                        </div>
                    `;
                    $('#city-results-sec').append(cityHtml); 
                });
            } else {
                $('#city-results-sec').append('<div class="col-12">No cities found.</div>');
            }
        });
    });
    // function setSessionVariableSec(data) {
       
    //     $.ajax({
    //         url: "/location-property",  
    //         method: 'GET',
    //         data: data,
    //         success: function(response) {
               
    //         },
    //         error: function(xhr) {
    //             alert('An error occurred while processing your request.');
    //         }
    //     });
    // }
// function dynamicAjaxRequestSec(data) {
//     $.ajax({
//         url: "/location-property",  
//         method: 'GET',
//         data: data,
//         success: function(response) {
//             var redirectUrl = "/property-list/" + data.city_slug_name;
//             window.location.href = redirectUrl;
//         },
//         error: function(xhr) {
//             alert('An error occurred while processing your request.');
//         }
//     });
// }

function dynamicAjaxRequestSec(data) {
    const searchURL = "{{ route('property-list') }}?" + $.param(data);
    window.location.href = searchURL; // Redirect to the search results page with query parameters
}




//  $(document).on('click', '.area-filter-alender', function() {
//         const areaId = $(this).data('area-id');
//         console.log(areaId, 'areaId');
//         const index = selectedAreasC.indexOf(areaId);
//         if (index === -1) {
//             selectedAreasC.push(areaId);
//             $(this).addClass('active');
//         } else {
//             selectedAreasC.splice(index, 1);
//             $(this).removeClass('active');
//         }
            
// });





</script>

<script>
$(document).on('click', '.area-filter-alender', function () {
    let areaName = $(this).data('area-id');

    // Current URL params
    let url = new URL(window.location.href);
    let params = new URLSearchParams(url.search);

    // Get existing areas
    let areas = [];
    if (params.has('areas')) {
        areas = params.get('areas').split(',');
    }

    // Toggle logic
    if (areas.includes(areaName)) {
        // REMOVE area
        areas = areas.filter(a => a !== areaName);
    } else {
        // ADD area
        areas.push(areaName);
    }

    // Update URL
    if (areas.length > 0) {
        params.set('areas', areas.join(','));
    } else {
        params.delete('areas'); // remove param if empty
    }

    // Reset pagination when filter changes
    params.delete('page');

    // Redirect with all existing params
    window.location.href = url.pathname + '?' + params.toString();
});
</script>
