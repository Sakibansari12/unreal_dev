@extends('pms.layouts.app')
@section('content')
<style>
    .ui-state-highlight {
    background-color: #e9e9e9;  // Placeholder background for drag
    height: 50px;  // Adjust height according to your needs
    border: 1px dashed #bbb;  // Dashed border for visual cue
}

        .select2-container {
            width: 100% !important;
            
        }
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
          height: 38px;
        }
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Quotation List</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.quotation.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="icon-plus me-2"></i> <span>Add New</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.quotation.list') }}">
                <div class="row gy-3 gx-2">
	                <!-- Guest Name -->
	                <div class="col-12 col-md-3 col-lg-3 col-xxl">
	                    <div class="input-group input-icon">
	                        {{-- <i class="bi bi-search absIcon"></i> --}}
	                        <input
	                            type="text"
	                            class="form-control form-control"
	                            placeholder="Guest Name"
	                            name="guest_name"
                                value="{{ request('guest_name') }}"
	                        />
	                    </div>
	                </div>

	                <!-- Property Name -->
                    <div class="col-12 col-md-3 col-lg-3 col-xxl">
                        <div class="form-group mb-0">
                            <select class="form-select form-control js-select2-search" name="property_name">
                                <option value="" disabled {{ request('property_name') ? '' : 'selected' }}>Property Name</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}" {{ request('property_name') == $property->id ? 'selected' : '' }}>
                                        {{ $property->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Check-in Date -->
                    <div class="col-12 col-md-4 col-lg-3 col-xxl">
                        <div class="form-group mb-0">
                            <input type="date" class="form-control flatpickr" name="checkin_date"
                                placeholder="Arrival" id="checkInDate" value="{{ request('checkin_date') ?? '' }}" />
                        </div>
                    </div>

                    <!-- Check-out Date -->
                    <div class="col-12 col-md-4 col-lg-3 col-xxl">
                        <div class="form-group mb-0">
                            <input type="date" class="form-control flatpickr" id="checkOutDate" name="checkout_date"
                                placeholder="Departure" value="{{ request('checkout_date') ?? '' }}" />
                        </div>
                    </div>

	                
	                <!-- Buttons -->
	                <div class="col-12 col-md-2 ">
                        <div class="btn-group gap-2">
                            <button type="submit" class="btn btn-small btn-primary btn-icon">
                                <i class="icon-search"></i>
                            </button>
                             <a href="{{ route('pms.quotation.list') }}" class="btn btn-warning btn-small">
                                <span class="material-symbols-outlined">refresh</span>
                            </a>
                        </div>
                    </div>
	            </div>
            </form>

            <div class="table-responsive data-table text-nowrap mt-3">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="border-end-0">Reference Details</th>
                            <th>Created by</th>
                            <th>Arrival - Departure</th>
                            <th>No. of nights</th>
                            <th>Guests</th>
                            <th>Validity</th>
                            <th>Url</th>
                            <th>Email sent</th>
                            <th>Booking status</th>
                            <th width="125">Action</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $value)
                            <tr>
                                <td>
                                    <p class="fw-bold m-0">{{ $value->first_name }} {{ $value->last_name }}</p>
                                    <small>{{ $value->country_code ?? '' }}{{ $value->mobile_number ?? '' }} | {{ $value->email ?? '' }}</small>
                                </td>
                
                                <td>
                                    Admin<br>
                                    {{ $value->quotation_date }}
                                </td>
                
                                <td>{{ $value->checkin_date }} - {{ $value->checkout_date }}</td>
                
                                <td>{{ $value->no_of_nights }}</td>
                
                                <td>{{ $value->no_adults + $value->no_children }}</td>
                
                                <td>
                                    {{ $value->validity }} {{ $value->validity == 1 ? 'Hour' : 'Hours' }}
                                    <br>
                                    @if($value->is_link_expired)
                                        <span class="text-danger">Expired</span>
                                    @endif
                                </td>
                
                                <td>
                                    <div class="position-relative">
                                        <button
                                            class="btn btn-small btn-secondary"
                                            onclick="copyToClipboard(this)"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-text="{{ $value->url ?? 'No link available' }}"
                                            title="Copy">
                                            <i class="bi bi-copy me-1"></i> Copy
                                        </button>
                                    </div>
                                </td>
                
                                <td>
                                    {{ $value->is_email_sent == 0 ? 'No' : 'Yes' }}
                                </td>
                
                                <td>{{ $value->booking_status }}</td>
                
                                <td>
                                    <ul class="actions mb-0 mw-0 d-flex gap-1">
                                        @if(!$value->is_link_expired && $value->booking_status == 'Not Booked')
                                            <li>
                                                <a href="{{ route('pms.quotation.form', ['id' => $value->id]) }}" class="btn ps-0 p-1 fs-5 text-black">
                                                    <i class="icon-edit"></i>
                                                </a>
                                            </li>
                                        @endif
                                        <li><button type="button" class="btn item_delete" data-value="{{ $value->id }}"><i class="icon-delete"></i></button></li>
                                    </ul>
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="10">No Record Found!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
            </div>
            <div class="mt-3">
                {{ $items->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.js-select2-search').select2({
            placeholder: "Property Name",
            allowClear: true,
            minimumInputLength: 0,
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkInDate = flatpickr('#checkInDate', {
            dateFormat: 'Y-m-d',
            altInput: true, 
            altFormat: "d/m/Y",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr) {
                    checkOutDate.set('minDate', dateStr);
                } else {
                    checkOutDate.set('minDate', null);
                }
            }
        });

        const checkOutDate = flatpickr('#checkOutDate', {
            dateFormat: "Y-m-d",
            altInput: true, 
            altFormat: "d/m/Y",
        });

    });
</script>
<script>
    function copyToClipboard(button) {
        const textToCopy = button.getAttribute('data-text');
        navigator.clipboard.writeText(textToCopy).then(() => {
            const tooltip = bootstrap.Tooltip.getInstance(button);
            if (tooltip) {
                tooltip.setContent({ '.tooltip-inner': 'Copied!' });
                tooltip.show();

                setTimeout(() => {
                    tooltip.setContent({ '.tooltip-inner': 'Copy' });
                    tooltip.hide();
                }, 1000);
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
        $(document).on('click', '.item_delete', function () {
            const id = $(this).data("value");

            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pms/quotation/delete") }}/' + id,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire('Deleted!', response.message || 'The item has been deleted.', 'success');
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function () {
                            Swal.fire('Error', 'Something went wrong!', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection