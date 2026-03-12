@php
    $guest_data = getGuestData();
    $location_data = getLocationData();
@endphp
<div class="main-search-outer py-5 hero-search">
    <div class="main-search">
        <div class="row g-0 align-items-center">
            <div class="col-3 field-col position-relative">
                <label class="search-field search-field-location" for="loc">
                  <strong>Where</strong>
                  <div class="search-data">
                        <input id="loc"  class="form-control" placeholder="Search for a city...">
                        <div class="swiper cities-placeholder">
                            <div class="swiper-wrapper">
                                @if (!empty($location_data) && $location_data->isNotEmpty())
                                @foreach ($location_data as $location)
                                    <div class="swiper-slide swiper-slide-img">{{ isset($location->location_name) ? $location->location_name : ''}}</div>
                                @endforeach
                                @endif
                            </div> 
                        </div>
                  </div>
                </label>
                <div class="custom-dropdown custom-dropdown-loc">
                    <h2>Search by City</h2>
                    <div  class="row g-0">
                        <div id="city-results" class="row g-0">
                        @if (!empty($location_data) && $location_data->isNotEmpty())
                                @foreach ($location_data as $location)
                        <div class="col-4">
                            <div class="loc-city">
                                <div class="loc-img">
                                    <a href="javascript:void(0)"  class="city-image" 
                                    data-city="{{ isset($location->location_name) ? $location->location_name : ''}}" data-cityid="{{ isset($location->id) ? $location->id : ''}}">
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
            </div>
            <div class="col field-col d-flex position-relative">
                <input type="hidden" name="start_date" id="start_date" class="start_date">
                <input type="hidden" name="end_date" id="end_date" class="end_date">
                <button class="btn text-start btn-checkin search-field" id="checkIn">
                    <strong>Check in</strong>
                    <div class="search-field-value js-checkin-text">Add dates</div>
                    <span class="clear-dates">&times;</span>
                </button>
                
                <button class="btn text-start btn-checkout search-field">
                    <strong>Check out</strong>
                    <div class="search-field-value js-checkout-text">Add dates</div>
                    <span class="clear-dates">&times;</span>
                </button>
                
                <div class="custom-dropdown calendar-dropdown">
                     <input id="hero-calendar" type="text" style="display:none;" />
                </div>
            </div>
            <div class="col field-col position-relative">
                <button class="btn text-start guests-tab search-field search-field-guest">
                    <strong>Who</strong>
                    <div class="search-field-value add-guests-value" data-total-guest>Add guests</div>
                </button>
                <a href="#" id="search-link">   
                    <button class="btn btn-search btn-search-first">
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
                                                <a href="javascript:void(0)" class="btn counter-col c-minus {{ ($guest->type == 'Adults' && $guest->count == 1) || ($guest->type != 'Adults' && $guest->count == 0) ? 'disabled' : '' }}" data-type="{{ $guest->type }}" data-allow-guest-count="{{ $guest->allow_guest_count }}" onclick="updateCounter(this, -1)">
                                                    <span class="icon-minus"></span>
                                                </a>
                                                <div class="counter-col">
                                                    <input type="hidden" class="count-input" name="{{ strtolower($guest->title) }}_count" value="{{ isset($guest->count) ? $guest->count : 0 }}">
                                                    <strong class="count-val">{{ isset($guest->count) ? $guest->count : 0 }}</strong>
                                                </div>
                                                <a href="javascript:void(0)" class="btn counter-col c-plus" data-type="{{ $guest->type }}" data-allow-guest-count="{{ $guest->allow_guest_count }}" onclick="updateCounter(this, 1)">
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

<script defer>
    document.addEventListener("DOMContentLoaded", function(){
        let LocationData = @json($location_data);
        $('#loc').on('keyup', function() {
            let query = $(this).val().toLowerCase();  
            $('#city-results').empty();  
            const filteredData = LocationData.filter(location => {
                return location.location_name.toLowerCase().includes(query);
            });
            if (filteredData.length > 0) {
                $.each(filteredData, function(index, city) {
                    let cityHtml = `
                        <div class="col-4">
                            <div class="loc-city">
                                <div class="loc-img">
                                    <a href="javascript:void(0)" class="city-image" data-city="${city.location_name}" data-cityid="${city.id}">
                                        <img src="${city.image ? '{{ url('storage/location') }}/' + city.image : '{{ asset('assets/website/images/noimage.jpg') }}'}" alt="${city.location_name}">
                                    </a> 
                                </div>
                                <h3>${city.location_name}</h3>
                            </div>
                        </div>
                    `;
                    $('#city-results').append(cityHtml); 
                });
            } else {
                $('#city-results').append('<div class="col-12">No cities found.</div>');
            }
        });
    });
</script>
<script defer>
let checkInDate = $('.start_date').val(); 
let checkOutDate = $('.end_date').val();
let check_check_in_date, check_check_out_date;
     document.addEventListener("DOMContentLoaded", function () {
     
        let parentElClass = '.hero-search';    
        let inputCalendar = document.getElementById('hero-calendar');
        function resetDateInput(){
          $(parentElClass).find(".js-checkin-text,.js-checkout-text").removeClass('hasValue').text("Add dates");
          datepickerHero.clearDatepicker();
          datepickerHero.clear();
          const data = {
            check_in: '',
            checkout_date: '',
        };      
       // setSessionVariable(data);
        }        
        window.datepickerHero = new HotelDatepicker(inputCalendar, {
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
                //calendarBtn.toggle();             
            } ,
            onDayClick: function() {                
                if(this.start){
                    //$(".btn-end-date span").text("Departure");
                    $(".js-checkout-text").text("Check out");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(parentElClass).find(".js-checkin-text").addClass('hasValue').text(startDate).parent().removeClass('active');
                    $(parentElClass).find(".js-checkout-text").parent().addClass('active');
                    check_check_in_date = fecha.format(this.start, `Do MMM`);
                }
                if(this.end){
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(parentElClass).find(".js-checkout-text").addClass('hasValue').text(endDate);
                    check_check_out_date = fecha.format(this.end, `Do MMM`);
                }
                if(this.start && this.end){
                    $(parentElClass).find(".js-checkin-text,.js-checkout-text").parent().removeClass('active');
                    const data = {
                        check_in: fecha.format(this.start, `Do MMM YYYY`),
                        checkout_date: fecha.format(this.end, `Do MMM YYYY`),
                    };
                    checkInDate = fecha.format(this.start, `Do MMM YYYY`);
                    checkOutDate = fecha.format(this.end, `Do MMM YYYY`)
                  //  setSessionVariable(data);
                }
                if(!this.start && !this.end){
                    resetDateInput()
                }
            }         
        });
        $(datepickerHero.datepicker).find(".datepicker__clear-button").text("Clear Dates")
        $(datepickerHero.datepicker).find(".datepicker__buttons").append('<button type="button" class="btn btn-link close-datepicker">Close</button>');

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
let adults_count = 0;
let children_count = 0;
let pets_count = 0;  
let selectedCityId = null;
let cityName = '';
let all_total_guests = 0;
 $(document).on('click', '.city-image', function() {
    const locationEl = $('#loc')
    cityName = $(this).data('city');
    selectedCityId = $(this).data('cityid');
    locationEl.val(cityName);
    if(locationEl){
        $('.swiper-slide-img').empty();
        locationEl.css('opacity', "1");
        $('#checkIn').trigger('click');
    }
        const data = {
            location: cityName,
            city_id: selectedCityId,
        };
      //  setSessionVariable(data);
}); 
$(document).on('click', '.btn-search-first', function(e) {
    e.preventDefault();
    const total_guests  = $('.add-guests-value').text();
    let guestsParams = '';
    $('.count-input').each(function() {
        const guestType = $(this).attr('name'); 
        const countValue = $(this).val(); 
        guestsParams += `&${encodeURIComponent(guestType)}=${encodeURIComponent(countValue)}`;
    });
    if(cityName ==''){
        alert('Please Select Destination');
        return false;
    }
    else{
        const searchData = {
            location: cityName,
            check_in: checkInDate,
            check_out: checkOutDate,
            city_id: selectedCityId,
            tot_guest: total_guests,
            adults: adults_count,
            children: children_count,
            petsCount: pets_count,
            all_total_guests: all_total_guests,
        }; 
        let search = true;
        if(City_Sec_Id || check_check_in_date && check_check_out_date==undefined){
            search = false;
            alert('Please select checkout date')
            return false
        }
        if(search){
            dynamicAjaxRequest(searchData);
        }
    }
});
function updateCounter(element, increment) {
    const type = element.getAttribute('data-type');
    const allow_guest = element.getAttribute('data-allow-guest-count');
    const counterContainer = element.closest('.counter');
    const countDisplay = counterContainer.querySelector('.count-val');
    const hiddenInput = counterContainer.querySelector('.count-input');
    const minusBtn = counterContainer.querySelector('.c-minus');
    const plusBtn = counterContainer.querySelector('.c-plus');
    let count = parseInt(hiddenInput.value) || 0;
    count += increment;
    if (type === 'Adults') {
        count = Math.max(1, count); 
    }
    else {
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
      //  count = Math.max(0, count); 
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
    updateGuestSummary(type, allow_guest);
}
function updateGuestSummary(type, allow_guest) {
    let adultsCount = 0;
    let childrenCount = 0;
    let petsCount = 0;
    
    document.querySelectorAll('.counter-col .count-input').forEach(input => {
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
    adults_count = adultsCount;
    pets_count = petsCount; 
    children_count = childrenCount;
    const combinedGuests  = adultsCount + childrenCount
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
    const guestSummaryElement = document.querySelector('.search-field-value.add-guests-value');
    if (guestSummaryElement) {
        guestSummaryElement.textContent = displayText;
    } else {
        console.error('Element not found: .search-field-value.add-guests-value');
    }
    const data = {
            all_total_guests: all_total_guests,
            adults: adults_count,
            children: children_count,
            petsCount: pets_count
        };
        //setSessionVariable(data);
}
document.addEventListener('DOMContentLoaded', () => {
    updateGuestSummary();
});
/* filter data  */
/* function setSessionVariable(data) {
        //alert(232);
        $.ajax({
            url: "location-property", 
            method: 'GET', 
            data: data,
            success: function(response) {
            },
            error: function(xhr) {
                alert('An error occurred while processing your request.');
            }
        });
    } */
/* function dynamicAjaxRequest(data) {

    $.ajax({
        url: "location-property", 
        method: 'GET', 
        data: data,
        success: function(response) {
            var redirectUrl = "/property-list/" + data.city_slug_name;  // Dynamically build the URL
            window.location.href = redirectUrl;
        },
        error: function(xhr) {
            alert('An error occurred while processing your request.');
        }
    });
} */

function dynamicAjaxRequest(data) {
    const searchURL = "{{ route('property-list') }}?" + $.param(data);
    window.location.href = searchURL; // Redirect to the search results page with query parameters
}
</script>
