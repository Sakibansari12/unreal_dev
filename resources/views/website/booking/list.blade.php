@extends('website.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            
        <h2 class="text-center">Manage Your Booking</h2>
        </div>
    </div>
</div>
<section class="section manage-section">
    <div class="container-fluid">
        <div class="row g-4">

    <div class="property-card mb-4 p-3 p-md-4 rounded bg-white">
        <div class="row g-4 align-items-center">

            <!-- Image -->
            <div class="col-12 col-md-3">
                <div class="property-image overflow-hidden rounded">
                    <img src="{{ $booking->property_details->imagesWebsite->first()->website_image ?? '' }}" 
                         alt="{{ $booking->property_details->unit_name_website ?? 'Villa Image' }}" 
                         class="img-fluid w-100 h-100 object-fit-cover">
                </div>
            </div>

            <!-- Content -->
            <div class="col-12 col-md-8">
                <div class="property-details">
                    <!-- Title -->
                    <h3 class="mb-1 fw-semibold">{{ $booking->property_details->unit_name_website ?? 'Villa Title' }}</h3>
                    <p class="text-muted mb-3">
                        {{ $booking->property_details->locationData->location_name ?? '' }}, {{ $booking->property_details->state ?? '' }}
                       
                    </p>

                    <!-- Info Cards -->
                    <div class="row g-2 text-center info-cards">
                        <div class="col-6 col-sm-4 col-lg">
                            <div class="info-icon-card">
                                <i class="bi bi-calendar-check-fill"></i>
                                <p class="label">Check In</p>
                                <p class="value">{{ \Carbon\Carbon::parse($booking->checkin_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg">
                            <div class="info-icon-card">
                                <i class="bi bi-calendar-x-fill"></i>
                                <p class="label">Check Out</p>
                                <p class="value">{{ \Carbon\Carbon::parse($booking->checkout_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg">
                            <div class="info-icon-card">
                                <i class="bi bi-moon-stars-fill"></i>
                                <p class="label">Nights</p>
                                <p class="value">{{ $booking->no_of_nights ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-auto">
                            <div class="info-icon-card">
                                <i class="bi bi-people-fill"></i>
                                <p class="label">Guests</p>
                                <p class="value">{{ $booking->no_of_adult ?? '' }} Adults, {{ $booking->no_of_children ?? '' }} Children</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg">
                            <div class="info-icon-card">
                                <i class="bi bi-currency-rupee"></i>
                                <p class="label">Total</p>
                                <p class="value text-success">₹{{ number_format($booking->payable_amount ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $checkinDate = \Carbon\Carbon::parse($booking->checkin_date);
                $isFuture = $checkinDate->gt(\Carbon\Carbon::today());
            @endphp

            @if($isFuture)
                <div class="col-12 col-md-1 text-start">
                    <button type="button" class="btn btn-small custom-btn-cancel btn-save btn-danger item_cancel" @if(isset($expired) && $expired) disabled @endif data-value="{{ $booking->id }}">
                        Cancel
                    </button>
                </div>
            @endif


        </div>
    </div>

        </div>
    </div>
</section>


@if(session('expired'))
    <script>
        setTimeout(() => {
            alert("Your OTP session has expired. Please try again.");
             // ya koi aur page jaha tu bhejna chahta hai
        }, 500);
    </script>
@endif
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(isset($expired) && $expired)
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Session Expired',
            text: 'Your session has expired. Please try again.',
        }).then(() => {
            window.location.href = "{{ route('index') }}"; 
        });
    </script>
@endif

<script>
        $(document).on('click', '.item_cancel', function () {
            const id = $(this).data("value");

            Swal.fire({
                title: 'Are you sure?',
                text: 'you want to cancel this booking?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pms/bookingproperty/cancellation") }}/' + id,
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire('Cancelled!', 'The Booking has been Cancelled.', 'success');
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function () {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection