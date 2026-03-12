
@php

    $guest_data = getGuestData();

    $location_data = getLocationData();

@endphp
<div class="mobile-search-placeholder py-2 form-control box-shadow-1">
    <div class="row gx-3 align-items-center">
        <div class="col-auto"><i class="icon-search"></i></div>
        <div class="col">
            <strong class="hide-show">Goa?</strong>
            <small>Check in - Check out &bull; No. of Guests</small>
        </div>
    </div>
</div>


<div class="card mobile-search d-lg-none">
    <div class="card-header">
        <a href="javascript:void(0)" class="m-search-close text-black"><i class="icon-close"></i><span class="fs-6">Search</span></a> 
    </div>
    <div class="card-body pt-0">
        <div class="accordion-wrap">
            <div class="accordion-box active">
                <div class="accordion-title">
                    <div class="row align-items-center">
                        <div class="col"><h2>Where to?</h2></div>
                        <div class="col text-end" id="swiper-slide-mobile-sec">{{-- Bengaluru, Karnataka --}}</div>
                    </div>
                </div>
                <div class="accordion-data">
                    <div class="m-search-input form-control box-shadow-1 ">
                        <i class="icon-search"></i> <span>Try Goa</span>
                    </div>
                    <div class="or-cities">or pick one from below</div>
                    <div class="swiper cities-carousel">
                        <div class="swiper-wrapper">
                            <input type="hidden" name="location_mobile_id" id="location_mobile_id" >
                            <input type="hidden" name="location_mobile_name" id="location_mobile_name">
                           
                            
                            @if (!empty($location_data) && $location_data->isNotEmpty())
                            @foreach ($location_data as $location)

                                <div class="swiper-slide">
                                    <div class="loc-city city-image-mobile-in"
                                    
                                            data-citynamemobile="{{ isset($location->location_name) ? $location->location_name : ''}}" 
                                            data-cityidmobile="{{ isset($location->id) ? $location->id : ''}}">
                                      
                                            <div class="loc-img">
                                            <img src="{{ isset($location->image) && $location->image ? url('storage/location/' . $location->image) : asset('assets/website/images/noimage.jpg') }}" alt="">
                                        </div>
                                        <h3>{{ isset($location->location_name) ? $location->location_name : ''}}</h3>
                                    
                                    </div>
                                </div>
                            @endforeach
                            @endif
                            <div class="swiper-slide d-flex">
                                <a href="{{ route('property-list', ['type' => 'all-property']) }}" class="icon-link icon-link-hover">See All <i class="bi icon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

          

            <div class="accordion-box">
                <div class="accordion-title">
                    <div class="row align-items-center" id="checkIn_Mobile_Sec">
                        <div class="col" ><h2>When?</h2></div>
                        <div class="col text-end checkIn_chechout_date">Add dates</div>
                    </div>
                </div>
                <div class="accordion-data">
                    <input type="text" id="mobile-calender" style="display: none;">
                </div>
            </div>

            <div class="accordion-box">
                <div class="accordion-title">
                    <div class="row align-items-center">
                        <div class="col"><h2>Who?</h2></div>
                        <div class="col text-end search-field-value-mobile add-guests-value-mobile" data-total-guest>Add guests</div>
                    </div>
                </div>
                <div class="accordion-data">
                    <div class="guests-counter">
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

                                            
                                            <a href="javascript:void(0)" class="btn counter-col counter-col-mobile c-minus {{ ($guest->type == 'Adults' && $guest->count == 1) || ($guest->type != 'Adults' && $guest->count == 0) ? 'disabled' : '' }}" data-type="{{ $guest->type }}" data-allow-guest-count="{{ $guest->allow_guest_count }}" onclick="updateCounterMobile(this, -1)">
                                                    <span class="icon-minus"></span>
                                                </a>

                                            <div class="counter-col counter-col-mobile">
                                                <input type="hidden" class="count-input-mobile adults-count" name="{{ strtolower($guest->title) }}_count" value="{{ isset($guest->count) ? $guest->count : 0 }}">
                                                <strong class="count-val">{{ isset($guest->count) ? $guest->count : 0 }}</strong>
                                            </div>



                                           

                                            <a href="javascript:void(0)" class="btn counter-col counter-col-mobile c-plus" data-type="{{ $guest->type }}" data-allow-guest-count="{{ $guest->allow_guest_count }}" onclick="updateCounterMobile(this, 1)">
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

                <button style="display:none;" class="btn btn-primary search-submit"><i class="icon-search"></i> Search</button>
            </div>
        </div>
    </div>
</div>


<div class="mobile-location-search d-md-none">
    <div class="form-group mb-3">
        <button class="btn close-mobile-location-search"><i class="icon-arrow-left"></i></button>
        <input type="text" class="form-control box-shadow-1" id="location-mobile-second">
        <button class="btn clear-loc-input"><i class="icon-close"></i></button>
    </div>
    <ul class="location-search-list" id="city-results-mobile">
        @if (!empty($location_data) && $location_data->isNotEmpty())
            @foreach ($location_data as $location)
              <li><a href="javascript:void(0)" data-location-value="" 
                data-citynamemobile="{{ isset($location->location_name) ? $location->location_name : ''}}" 
                data-location-id="{{ isset($location->id) ? $location->id : ''}}"
                data-location-name="{{ isset($location->location_name) ? $location->location_name : ''}}">
                {{ isset($location->location_name) ? $location->location_name : ''}}</a></li>
            @endforeach
        @endif
    </ul>    
    {{-- <ul class="location-search-list">
        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Bengaluru, Karnataka">Bengaluru, Karnataka</a></li>
        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Mangalore, Karnataka">Mangalore, Karnataka</a></li>
        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Bengaluru, Karnataka">Bengaluru, Karnataka</a></li>
        <li><a href="javascript:void(0)" data-location-value="" data-location-name="Mangalore, Karnataka">Mangalore, Karnataka</a></li>
    </ul> --}}
</div>

<script defer>
     let checkin_date_mobile = '';
     let checkout_date_mobile = '';

     let City_mobile_Id = $('#location_mobile_id').val();
     let city_name_mobile = $('#location_mobile_name').val();

    document.addEventListener("DOMContentLoaded", function(){
        $(".mobile-search-placeholder,.search-link").on("click", function(){
            $('body').addClass('mobile-search-active');
        })
        $('.m-search-close').on("click", function(){
            $('body').removeClass('mobile-search-active');
        })


        // function enableSearch(){
            
        //     if($('#mobile-calender').val()){
        //         $('.search-next').hide();
        //         $('.search-submit').show();
        //        // console.log("bif", $('#mobile-calender').val())
        //     }else{
        //          //console.log("b", $('#mobile-calender').val())
        //         $('.search-next').show();
        //         $('.search-submit').hide();
        //     }
        // }

        new Swiper('.cities-carousel', {
            speed: 400,
            slidesPerView: 3.5,
            spaceBetween: 8,
        });

        function fixeCalendarWidth(){
            $(".mobile-search .datepicker__month-day").outerHeight($(".mobile-search .datepicker__month-day").outerWidth());
        }


        let currentIndex = 1; // Track the current index

        $(".search-next").on('click',function() {
             City_mobile_Id = $('#location_mobile_id').val();
             city_name_mobile = $('#location_mobile_name').val();

            if(City_mobile_Id == ''){
            alert('Please Select Destination');
            return false;
        }


            $(".mobile-search .accordion-box").removeClass("active"); 
            $(".mobile-search .accordion-box").eq(currentIndex).addClass("active");
            fixeCalendarWidth()

            //enableSearch();
            currentIndex = (currentIndex + 1) % $(".mobile-search .accordion-box").length;
        });


        $(".mobile-search .accordion-title").on("click", function(){
            currentIndex = $(this).parent().index();
            $(".mobile-search .accordion-box").removeClass("active"); 
            $(".mobile-search .accordion-box").eq(currentIndex).addClass("active");
            console.log("dh",currentIndex)
            fixeCalendarWidth()
            //enableSearch();
        })



        $('.m-search-input').on("click", function(){
            $('body').addClass('mobile-location-search-active');
        })

        $(document).on("click",'.location-search-list a', function(e){
            e.preventDefault();
            let locName = $(this).data('location-name');
            let locId = $(this).data('location-id');
            let locValue = $(this).data('location-value');
            $('#location_mobile_id').val(locId);
            $('#location_mobile_name').val(locName);
            locationEl = $('#swiper-slide-mobile-sec');
            locationEl.text(locName);

            $('.m-search-input span').text(locName);
            $('body').removeClass('mobile-location-search-active');
        })

        $('.close-mobile-location-search').on('click', function(){
            $('body').removeClass('mobile-location-search-active');
        })




        // Mobile Calendar
        let parentElClass = '.mobile-search';    
        let inputCalendar = document.getElementById('mobile-calender');
        function resetDateInput(){
          $(parentElClass).find(".m-date").text("Add dates");
          datepickerMobile.clear();
        }        
       
        window.datepickerMobile = new HotelDatepicker(inputCalendar, {
            inline: true,
            moveBothMonths: true,           
            // clearButton: true,
            // minNights: 4,
            showTopbar: false,
            onSelectRange: function() {
                let startDate = fecha.format(this.start, `Do MMM`);
                let endDate = fecha.format(this.end, `Do MMM`);
                $(parentElClass).find(".m-date").text(`${startDate}-${endDate}`);
               // enableSearch();  
            } ,
            onDayClick: function() {      
                //enableSearch();            
                if(this.start){
                    //$(".btn-end-date span").text("Departure");
                    let startDate = fecha.format(this.start, `Do MMM`);
                    $(parentElClass).find(".m-date").text(`${startDate}`);
                }
                if(this.end){
                    let endDate = fecha.format(this.end, `Do MMM`);
                    $(parentElClass).find(".m-date").text(`-${endDate}`);
                }


                
                if(this.start && this.end){
                    checkin_date_mobile = fecha.format(this.start, `Do MMM YYYY`);
                    checkout_date_mobile = fecha.format(this.end, `Do MMM YYYY`);

                    let startDate = fecha.format(this.start, 'Do MMM');
                    let endDate = fecha.format(this.end, 'Do MMM');
                    const checkIn_chechout = $('.checkIn_chechout_date');
                    checkIn_chechout.text(`${startDate} to ${endDate}`);
                    



                    const data = {
                    check_in: checkin_date_mobile,
                    check_out: checkout_date_mobile,
                    };
                    
                    setSessionVariableMobile(data);
                    $(parentElClass).find(".m-date").text(`Add dates`);
                }
                if(!this.start && !this.end){
                    resetDateInput()
                }
            }         
        });

        $(".clear-dates").on("click",function(e){
            e.stopPropagation();
            resetDateInput();
        });

        fixeCalendarWidth()

        $(window).on("resize", function(){
            fixeCalendarWidth()
        })

    })




$(document).on('click', '.city-image-mobile-in', function() {
     locationEl = $('#swiper-slide-mobile-sec');
     city_name_mobile = $(this).data('citynamemobile');
     City_mobile_Id = $(this).data('cityidmobile');

       $('#location_mobile_id').val(City_mobile_Id);
       $('#location_mobile_name').val(city_name_mobile);
    locationEl.text(city_name_mobile);
    /* if (locationEl) {
        $('#checkIn_Mobile_Sec').trigger('click');
    } */
    const data = {
          location: city_name_mobile,
          city_id: City_mobile_Id,
        };
    setSessionVariableMobile(data);

});

$(document).ready(function() {

  let LocationData = @json($location_data);
  let filteredData = [];
    $('#location-mobile-second').on('keyup', function() {
        let query = $(this).val().toLowerCase();  
        $('#city-results-mobile').empty();  

        filteredData = LocationData.filter(location => {
            if(location.location_name.toLowerCase().includes(query)){
                return   {location_name:location.location_name,id:location.id}
            }
        });
        if (filteredData.length > 0) {
            $.each(filteredData, function(index, city) {
              
                let cityHtml = `
                    <li><a class="setCity" href="javascript:void(0)" data-location-id="${city.id}" data-location-name="${city.location_name}">${city.location_name}</a></li>
                `;
                $('#city-results-mobile').append(cityHtml); 
            });
        } else {
            $('#city-results-mobile').append('<ul class="location-search-list">No cities found.</div>');
        }
    });
});


$(".setCity").on("click", function(){
    $('#location_mobile_id').val($(this).attr('data-location-id'));
    $('#location_mobile_name').val($(this).attr('data-location-name'));
})


function setSessionVariableMobile(data) {
    $.ajax({
        url: "/location-property",  
        method: 'GET',
        data: data,
        success: function(response) {
        },
        error: function(xhr) {
            alert('An error occurred while processing your request.');
        }
    });
}

function updateCounterMobile(element, increment) {
const type = element.getAttribute('data-type');
const allow_guest = element.getAttribute('data-allow-guest-count');
const counterContainer = element.closest('.counter');
const countDisplay = counterContainer.querySelector('.count-val');
const hiddenInput = counterContainer.querySelector('.count-input-mobile');
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
updateGuestSummaryMobile(type, allow_guest);

}


function updateGuestSummaryMobile(type, allow_guest) {
let adultsCount = 0;
let childrenCount = 0;
let petsCount = 0;
let all_total_guests = 0;
document.querySelectorAll('.counter-col-mobile .count-input-mobile').forEach(input => {
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
const guestSummaryElement = document.querySelector('.search-field-value-mobile.add-guests-value-mobile');

if (guestSummaryElement) {

    guestSummaryElement.textContent = displayText;

} else {

    console.error('Element not found: .add-guests-value-mobile.add-guests-value-mobile');

}

const data = {
        all_total_guests: all_total_guests,
        adults: adults_count,
        children: children_count,
        petsCount: pets_count
    };
    setSessionVariableMobile(data);
}

document.addEventListener('DOMContentLoaded', () => {
    updateGuestSummaryMobile();
});
$(document).ready(function() {
   // alert(545);
    let currentIndex = 0;
    const totalSteps = 2;
    
    $(document).on('click', '.search-next', function(e) {
        e.preventDefault(); 
     City_mobile_Id = $('#location_mobile_id').val();
     city_name_mobile = $('#location_mobile_name').val();

        
        if (typeof city_name_mobile !== 'undefined' && city_name_mobile) {
            currentIndex++;
            if (currentIndex === totalSteps) {
                $('.search-next').hide();
                $('.search-submit').show();
            }
        } else {
            $('.search-next').show();
            $('.search-submit').hide();
        }
    });
});




$(document).on('click', '.search-submit', function(e) {
e.preventDefault();
if(City_mobile_Id ==''){
    alert('Please Select Destination');
    return false;
}
const total_guests  = $('.add-guests-value').text();
let guestsParams = '';
$('.count-input').each(function() {
    const guestType = $(this).attr('name'); 
    const countValue = $(this).val(); 
    guestsParams += `&${encodeURIComponent(guestType)}=${encodeURIComponent(countValue)}`;
});
 const data = {
        location: city_name_mobile,
        check_in: checkin_date_mobile,
        check_out: checkout_date_mobile,
        city_id: City_mobile_Id,
        tot_guest: total_guests,
        adults: adults_count,
        children: children_count,
        petsCount: pets_count
}; 
if (City_mobile_Id || checkin_date_mobile || checkout_date_mobile) {
    dynamicAjaxRequestMobile(data);
}
});

// function dynamicAjaxRequestMobile(data) {
//     console.log(data.city_slug_name,"data.city_slug_name");
// $.ajax({
//     url: "location-property", 
//     method: 'GET', 
//     data: data,
//     success: function(response) {
//       var redirectUrl = "/property-list/" + data.city_slug_name;  
//       window.location.href = redirectUrl;
//     },
//     error: function(xhr) {
//         alert('An error occurred while processing your request.');
//     }
// });
// }
function dynamicAjaxRequestMobile(data) {
    const searchURL = "{{ route('property-list') }}?" + $.param(data);
    window.location.href = searchURL; // Redirect to the search results page with query parameters
}

</script>