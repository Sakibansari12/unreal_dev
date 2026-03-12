@extends('pms.layouts.app')
@section('content')
<section class="section">
   <div class="container-fluid">
	    <div class="title">
	    	<div class="row gx-2 align-items-center">
	    		<div class="col">
	                    <h1 class="fs-5 mb-0">Booking List</h1>
	            </div>
	            <div class="col-auto">
	                <a href="{{ route('pms.booking.bylocation') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
	                    <i class="icon-plus me-2"></i> <span>Create New Booking</span>
	                </a>
	            </div>
	        </div>
	    </div>
	    <!-- Search Box -->
	    <div class="content-box pt-0">
	        <form method='GET' action="{{route('pms.booking.list')}}" class="searchBox mobSearch p-2">
	        	@csrf
	            <div class="row gy-3 gx-2">
	                <!-- Booking ID -->
	                <div class="col-12 col-md-4 col-lg-3 col-xxl">
	                    <div class="input-group input-icon">
	         
	                        <input
	                            type="text"
	                            class="form-control form-control"
	                            placeholder="Booking ID"
	                            name="searchBookingId"
	                            value="{{$requestParams['searchBookingId'] ?? ''}}"
	                        />
	                    </div>
	                </div>

	                <!-- Property Name -->
	                <div class="col-12 col-md-4 col-lg-3 col-xxl">
	                    <div class="form-group mb-0">
	                        <select class="form-select form-control" name="searchPropertyId">
                                <option value="" selected disabled>Property Name</option>
                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}" 
                                        @selected(old('searchPropertyId', $requestParams['searchPropertyId'] ?? '') == $property->id)>
                                        {{ $property->home_name }}
                                    </option>
                                @endforeach
                            </select>
	                    </div>
	                </div>

	                <!-- Check-in Date -->
	                <div class="col-12 col-md-4 col-lg-3 col-xxl">
	                    <div class="form-group mb-0">
	                        <input
	                            type="date"
	                            class="form-control flatpickr"
	                            name="checkInDate"
	                            id="checkInDate"
	                            placeholder="Check-in Date"
	                            value="{{$requestParams['checkInDate'] ?? ''}}"
	                        />
	                    </div>
	                </div>

	                <!-- Check-out Date -->
	                <div class="col-12 col-md-4 col-lg-3 col-xxl">
	                    <div class="form-group mb-0">
	                        <input
	                            type="date"
	                            class="form-control flatpickr"
	                            name="checkOutDate"
	                            id="checkOutDate"
	                            placeholder="Check-out Date"
	                            value="{{$requestParams['checkOutDate'] ?? ''}}"
	                        />
	                    </div>
	                </div>

	                <!-- Payment Status -->
	               <div class="col-12 col-md-4 col-lg-3 col-xxl">
					    <div class="form-group mb-0">
					        <select
					            class="form-select form-control"
					            name="searchPaymentStatus">
					            <option value="" disabled {{ ($requestParams['searchPaymentStatus'] ?? '') == '' ? 'selected' : '' }}>Payment Status</option>
					            <option value="Paid" {{ ($requestParams['searchPaymentStatus'] ?? '') == 'Paid' ? 'selected' : '' }}>Paid</option>
					            <option value="Pending" {{ ($requestParams['searchPaymentStatus'] ?? '') == 'Pending' ? 'selected' : '' }}>Pending</option>
					        </select>
					    </div>
					</div>

	                <!-- Booking Status -->
	                @php
					    $bookingStatus = $requestParams['searchBookingStatus'] ?? '';
					    $channel = $requestParams['searchChannel'] ?? '';
					@endphp

					<!-- Booking Status -->
					<div class="col-12 col-md-4 col-lg-3 col-xxl">
					    <div class="form-group mb-0">
					        <select
					            class="form-select form-control"
					            name="searchBookingStatus">
					            <option value="" disabled {{ $bookingStatus == '' ? 'selected' : '' }}>Booking Status</option>
					            <option value="Requested" {{ $bookingStatus == 'Requested' ? 'selected' : '' }}>Requested</option>
					            <option value="Confirmed" {{ $bookingStatus == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
					            <option value="Not Confirmed" {{ $bookingStatus == 'Not Confirmed' ? 'selected' : '' }}>Not Confirmed</option>
					            <option value="Canceled" {{ $bookingStatus == 'Canceled' ? 'selected' : '' }}>Canceled</option>
					        </select>
					    </div>
					</div>

					<!-- Channel -->
					<div class="col-12 col-md-4 col-lg-3 col-xxl">
					    <div class="form-group mb-0">
					        <select
					            class="form-select form-control"
					            name="searchChannel">
					            <option value="" disabled {{ $channel == '' ? 'selected' : '' }}>Channel</option>
					            <option value="Airbnb" {{ $channel == 'Airbnb' ? 'selected' : '' }}>Airbnb</option>
					            <option value="Booking.com" {{ $channel == 'Booking.com' ? 'selected' : '' }}>Booking.com</option>
					            <option value="MakeMyTrip" {{ $channel == 'MakeMyTrip' ? 'selected' : '' }}>MakeMyTrip</option>
					            <option value="Offline" {{ $channel == 'Offline' ? 'selected' : '' }}>Offline</option>
					            <option value="Website" {{ $channel == 'Website' ? 'selected' : '' }}>Website</option>
					        </select>
					    </div>
					</div>


	                <!-- Buttons -->
	                <div class="col-12 col-md-auto">
                        <div class="btn-group gap-2">
                            <button type="submit" class="btn btn-small btn-primary btn-icon">
                                <i class="icon-search"></i>
                            </button>
                             <a href="{{ route('pms.booking.list') }}" class="btn btn-warning btn-small">
                                <span class="material-symbols-outlined">refresh</span>
                            </a>
                            <a href="#" class="btn btn-small btn-export btn-secondary ">
                                <span class="material-symbols-outlined">
                                file_export
                                </span>
                            </a>
                        </div>
                    </div>
	            </div>
	            <div class="col mt-2">
                    <div class="data-info text-primary">
                        {{ $bookings->total() }} Results found
                    </div>
                </div>
	        </form>

		    <!-- Booking List -->
		    <div class="tableWrapper mt-2">
		    	<div class="outer-wrapper">
		    		<div class="table-wrap">
	                    <div class="table-responsive">
							<table class="table table-list mb-0 mw-lg">
					            <thead>
					                <tr>
					                    <th width="10px"></th>
					                    <th>Booking ID</th>
					                    <th>Guest Detail</th>
					                    <th>Booking Detail</th>
					                    <th>Property Name</th>
					                    <th>Channel</th>
					                    <th>Total</th>
					                    <th>Paid</th>
					                    <th>Pending</th>
					                    <th>Booking Status</th>
					                    <th>Action</th>
					                </tr>
					            </thead>
					            <tbody>
					                @forelse($bookings as $booking)
				                    <tr>
			                    	  	<!-- Your original booking row content -->
									    <td>
									    	@if($booking->paymentRequests->isNotEmpty())
									        <a href="javascript:void(0)" class="bookingDetail fw-bold" onclick="bookinToggle(this)">
									            <span class="material-symbols-outlined down-arrow">keyboard_arrow_down</span>
									            <span class="material-symbols-outlined up-arrow" style="display:none">keyboard_arrow_up</span>
									        </a>
									        @endif
									    </td>
				                        <td>{{ $booking->booking_id }}</td>
				                        <td>{{json_decode( $booking->customer_detail)->first_name }} {{ json_decode( $booking->customer_detail)->last_name }}</td>
				                        <td>
				                            Arrival: {{ $booking->checkin_date }} | Departure: {{ $booking->checkout_date }} <br>
				                            Nights: {{ $booking->no_of_nights }} | Guests: {{ $booking->no_of_adult }} Adults, {{ $booking->no_of_children }} Children
				                        </td>
				                        <td>{{ $booking->home_name }}</td>
				                        <td>{{ $booking->channel }}</td>
				                        <td>₹{{ number_format($booking->payable_amount, 2) }}</td>
				                        <td>₹{{ number_format($booking->paid_amount, 2) }}</td>
				                        <td>₹{{ number_format($booking->payable_amount - $booking->paid_amount, 2) }}</td>
				                        <td>{{ $booking->property_booking_status }}</td>
				                        <td>
				                            @if(in_array($role, ['Admin', 'Reservations', 'Finance']))
				                                <ul class="action-btn-group mb-0 mw-0 d-block list-unstyled">
				                                    <!-- Payment Request Button -->
				                                    @if($booking->payable_amount > $booking->paid_amount && in_array($role, ['Admin', 'Finance']))
				                                        <li class="pt-1">
				                                            <a href="{{route('pms.booking.showPaymentRequestForm',['property_booking_id'=>$booking->id])}}" class="btn btn-small btn-save btn-warning w-100">
				                                                Payment Request
				                                            </a>
				                                            <br>
				                                        </li>
				                                    @endif

				                                    <!-- Guest ID Button -->
				                                    @if(($booking->property_booking_status == 'Confirmed' || $booking->property_booking_status == 'Confirmed') && in_array($role, ['Admin', 'Finance']) && !$booking->invoice)
				                                        <li class="pt-1">
				                                            <a href="{{route('property.booking.guest-ids',['id'=>$booking->id])}}" class="btn btn-small w-100 btn-save btn-info">
				                                                Guest ID
				                                            </a>
				                                        </li>
				                                    @endif

				                                    <!-- Download Invoice Button -->
				                                    @if($booking->invoice)
				                                        <li class="pt-1">
				                                            <a href="#" class="btn btn-small btn-save btn-info" target="_blank">
				                                                Download Invoice
				                                            </a>
				                                        </li>
				                                    @endif

				                                    <!-- Delete Button -->
				                                    @if(in_array($role, ['Admin', 'Finance']))
				                                        <li class="pt-1">
				                                            <button wire:click.prevent="deleteBooking({{ $booking->id }})" class="btn p-1 fs-5 text-black">
				                                                <i class="icon-bi bi-trash"></i>
				                                            </button>
				                                        </li>
				                                    @endif
				                                </ul>
				                            @else
				                                <ul class="action-btn-group mb-0 mw-0">
				                                    <!-- Guest ID Button for Non-RU Bookings -->
				                                    @if(in_array($role, ['Admin', 'Finance']) && !$booking->invoice)
				                                        <li>
				                                            <a href="#" class="btn btn-small btn-save btn-info">
				                                                Guest ID
				                                            </a>
				                                        </li>
				                                    @endif
				                                </ul>
				                            @endif
				                        </td>
				                    </tr>
				                    <!-- More static rows can be added similarly -->
			                        <tr class="toggle-row" style="display:none">
									    <td colspan="11">
								            <div class="table-responsive">
		                						<table class="table table-list mb-0 mw-lg">
								                    <thead>
								                        <tr>
								                            <th>Payment Req ID</th>
								                            <th>Person Detail</th>
								                            <th>Amount</th>
								                            <th>Payment Mode</th>
								                            <th>Status</th>
								                            <th>Action</th>
								                        </tr>
								                    </thead>
								                    <tbody>
								                    	@foreach($booking->paymentRequests as $paymentRequest)
								                        <tr>
								                            <td>{{$paymentRequest->booking_request_id}}</td>
								                            <td>{{$paymentRequest->name}} ({{$paymentRequest->email}})</td>
								                            <td>₹{{$paymentRequest->amount}} </td>
								                            <td>{{$paymentRequest->payment_mode}} </td>
								                            <td>{{$paymentRequest->booking_request_status}} </td>
								                            <td>
								                            	@if($paymentRequest->booking_request_status != 'Payment Received')
								                                <ul class="action-btn-group mb-0 mw-0 d-block list-unstyled">
																    <li class="pt-1">
																        <a  class="btn btn-small w-50 btn-save btn-primary openStatusModal" data-bs-toggle="modal" data-bs-target="#statusModal" data-id="{{$paymentRequest->id}}">
																            Change Status
																        </a>
																    </li>
																    <li class="pt-1">
																        <a href="{{route('pms.booking.showPaymentRequestEditForm',['property_booking_id'=>$booking->id,'payment_request_id'=>$paymentRequest->id])}}" class="btn btn-small w-50 btn-save btn-info ">Edit
																        </a>
																    </li>
																</ul>
															    @endif
								                            </td>
								                        </tr>
								                        @endforeach
								                    </tbody>
								                </table>
								            </div>
									    </td>
									</tr>
					                @empty
					                <tr>
					                	<td class="text-center" colspan="11">No record found.</td>
					                </tr>
					                @endforelse
					            </tbody>
					        </table>
					    </div>
					</div>
				</div>
		    </div>

		    <!-- Pagination -->
		    <div class="mt-3 p-2">
		        {{ $bookings->links('vendor.pagination.bootstrap-5') }}
		    </div>
		</div>
	</div>
</section>

<!-- pop modal for payment status change  -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg bg-primary text-white">
        <h5 class="modal-title" id="statusModalLabel">Update Payment Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Yaha tera form ya status change ka content aayega -->
        <form id="PayemntRequestFormId">
	        <div class="row p-2 statusModalBody">
		        <div class="row p-2">
		        	<div class="col-12">
		        		<span id="request_id_and_amount"></span>
		        		<span id="hiddenInput"></span>
						<label for="booking_request_status">Choose Status<sup class="text-danger">*</sup></label>
				        <select class="form-select" name="booking_request_status">
				            <option value="">Choose Status</option>
				            <option value="Payment Received">Payment Received</option>
				        </select>
		        	</div>
		        	<div class="col-12 mt-2">
		        		<label for="note">Note/Bank Detail<sup class="text-danger">*</sup></label>
		        		<textarea name="note" class="form-control h-auto" rows="6"></textarea>
		        	</div>
		        	<div class="col-12 mt-3">
			        	<button type="button" class="btn btn-primary" id="submitPaymentStatus">SUBMIT</button>
			        </div>
		        </div>
	        </div>
	    </form>
      </div>
    </div>
  </div>
</div>

<style>
	.toggle-row {
	    background-color: #f8f9fa;
	}
	.toggle-row .table {
	    margin-bottom: 0;
	}
	.toggle-row .table thead th {
	    background-color: #e9ecef;
	    font-size: 0.85rem;
	}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkInDate = flatpickr('#checkInDate', {
            dateFormat: 'Y-m-d',
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    checkOutDate.set('minDate', dateStr);
                } else {
                    checkOutDate.set('minDate', null);
                }
            }
        });

        const checkOutDate = flatpickr('#checkOutDate', {
            dateFormat: 'Y-m-d'
        });

    });
</script>
<script>
	function bookinToggle(element) {
	    const row = element.closest('tr');
	    const nextRow = row.nextElementSibling;
	    
	    if (!nextRow || !nextRow.classList.contains('toggle-row')) return;
	    
	    // Toggle visibility
	    const isHidden = nextRow.style.display === 'none';
	    nextRow.style.display = isHidden ? 'table-row' : 'none';
	    
	    // Toggle arrow icons
	    element.querySelector('.down-arrow').style.display = isHidden ? 'none' : 'inline';
	    element.querySelector('.up-arrow').style.display = isHidden ? 'inline' : 'none';
	}
	document.addEventListener('DOMContentLoaded', function () {
	    $('.openStatusModal').on('click', function () {
	        let paymentRequestId = $(this).data('id');

	        // Optional: show loader
	        $('#statusModalBody').html('Loading...');

	        // AJAX call to get modal content
	        $.ajax({
	            url: '{{ route("pms.booking.paymentRequest") }}',
	            type: 'GET',
	            data: { id: paymentRequestId },
	            success: function (response) {
	            	$('#request_id_and_amount').empty().html(`
					  <p>
					    <strong>Payment Req ID - ${response.data.booking_request_id}</strong><br>
					    <strong>Amount - ₹${response.data.amount}</strong>
					  </p>
					`);

					$('#hiddenInput').empty().html(`
					  <input type="hidden" name="payment_request_id" id="payment_request_id" value="${response.data.id}">
					  <input type="hidden" name="payment_request_amount" id="payment_request_amount" value="${response.data.amount}">
					  <input type="hidden" name="payment_booking_request_id" id="payment_booking_request_id" value="${response.data.booking_request_id}">
					`);
	            },
	            error: function () {
	                $('#request_id_and_amount').html('<p class="text-danger">Failed to load data.</p>');
	            }
	        });
	    });
     	$('#submitPaymentStatus').on('click', function () {
	        let form = $('#PayemntRequestFormId');
	        let isValid = true;

	        // Clear previous errors
	        form.find('.is-invalid').removeClass('is-invalid');

	        // Validate required fields
	        const status = form.find('[name="booking_request_status"]');
	        const note = form.find('[name="note"]');

	        if (status.val().trim() === '') {
	            status.addClass('is-invalid');
	            isValid = false;
	        }

	        if (note.val().trim() === '') {
	            note.addClass('is-invalid');
	            isValid = false;
	        }

	        if (!isValid) return;

	        // Submit if valid
	        let formData = form.serialize();

	        $.ajax({
	            url: '{{ route("pms.booking.paymentRequestUpdate") }}',
	            type: 'POST',
	            data: formData,
             	headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
	            success: function (response) {
	                $('#statusModal').modal('hide');
	                toastr.success(response.message, 'Success', { timeOut: 2000 });
	                setTimeout(function() {
					    location.reload();
					}, 2000);
	            },
	            error: function (xhr) {
	                if (xhr.status === 422) {
	                    let errors = xhr.responseJSON.errors;

	                    // Show Laravel validation errors
	                    $.each(errors, function (field, messages) {
	                        const input = form.find(`[name="${field}"]`);
	                        input.addClass('is-invalid');

	                        // Add error message if not already there
	                        if (input.next('.invalid-feedback').length === 0) {
	                            input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
	                        }
	                    });
	                } else {
	                    alert('Something went wrong. Please try again.');
	                }
	            }
	        });
	    });
	});

</script>
@endsection