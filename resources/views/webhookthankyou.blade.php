@extends('website.layouts.app')
@section('content')
<style>
    .booking-information .booking-amount .input-group .btn.btn-danger{
        background-color: #dc3545 !important
    }
    .booking-details{
        color: #726659;
    }
    .booking-details > h3{
        color: #000;
        margin-bottom: 2rem;
    }

    .bd-box > *{
       display: block;
    }
    @media(min-width: 992px){
        .booking-details .row > div +  div{
            border-left: 1px dashed #ccc;
        }
    }
</style>
<section class="section section-top section-booking pb-5">
    <div class="container" style="max-width: 900px;">
        <div class="title text-center ">
            <h3 class="fs-4 fw-normal">Thank You!</h3><br>
            <h4>Your booking is confirmed. Your booking id is <strong>{{ $data->booking_id }}.</strong></h4>
        </div>

        <div class="booking-details mt-4">
            <h3>Booking Information</h3>
            <div class="booking-information p-4 mb-5  rounded-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-4">
                        <a href="{{ route('index') }}" class="swiper m-0 card-slider rounded-3">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <span>
                                        @php $firstImage = $data->property->images->first(); @endphp
                                        @if($firstImage)
                                            <img src="{{ asset($firstImage->medium_image ?? 'assets/images/noimage-property.jpg') }}" width="230" alt=""style="width: 100%; max-width: 230px; background: #dddddd; font-family: Arial, sans-serif; font-size: 14px; line-height: 15px; color: #000000;">
                                        @else
                                            <img src="{{ asset('assets/images/noimage-property.jpg') }}" width="230" alt="">
                                        @endif
                                    </span>
                                </div>
                                </div>
                            <div class="cs-next"><i class="bi bi-chevron-right"></i></div>
                            <div class="cs-prev"><i class="bi bi-chevron-left"></i></div>
                            <div class="swiper-pagination"></div>
                        </a>
                    </div>
                    <div class="col-12 col-lg border-0">
                        <div class="booking-info-text p-3 p-lg-0">
                            

                            @if(!empty($data->home->unit_name_website))
                                <div class="property-name mb-3">
                                    <h3 class="h3 fw-bold mb-2">{{ $data->home->unit_name_website }}</h3>
                                    <p>
                                        @if(!empty($data->home->locationData->location_name))
                                            {{ $data->home->locationData->location_name }},
                                        @else
                                            Location Not Available,
                                        @endif
                                        @if(!empty($data->home->state))
                                            {{ $data->home->state }}
                                        @else
                                            State Not Available
                                        @endif
                                    </p>
                                </div>
                            @endif


                            <ul class="list-unstyled small mb-0">
                                <li class="mb-3">
                                    <div class="row gy-3 gx-5">
                                        <div class="col-6 col-md-auto">
                                            Arrival<br> <strong>{{$data->checkin_date}}</strong>
                                        </div>
                                        <div class="col-6 col-md-auto">
                                            Departure<br><strong>{{$data->checkout_date}}</strong>
                                        </div>
                                        <div class="col-6 col-md-auto">
                                         No. of Nights<br><strong>{{ $data->no_of_nights }}</strong>
                                        </div>
                                        <div class="col-6 col-md-auto">
                                            No. of Guests<br><strong>{{$data->tot_guests}}</strong>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Payment Details</h3>
            <div class="booking-information p-4 mb-5 rounded-4">
                <div class="row gx-5 gy-3 ">
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Payment ID:</small>
                            <strong>{{ $payload['razorpay_invoice_id']}}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Date:</small>
                            <strong>{{ date('d M Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Payment Method:</small>
                            <strong>Razorpay</strong>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="bd-box">
                            <small>Paid Amount:</small>
                            <strong>INR {{ number_format($paymentRequestInfo->amount) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
