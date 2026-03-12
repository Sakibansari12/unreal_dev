@extends('pms.layouts.app')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp

<style>
    .btn {
            padding: 9px 23px;
        }
</style>

    <section class="section">
        <div class="container-fluid dashboard-wrap">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Sale Report</h1>
                    </div>
                </div>
            </div>
            <div class="content-box p-3">
                <div class="page-title">
                    <div class="row gy-3 align-items-center justify-content-end">


                        <form method="GET" action="{{ route('pms.report.sale-report') }}" id="sale-report-form">
                            
                            <div class="row gy-3 gx-2">
                                <!-- Property Name -->
                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <select name="searchPropertyId" class="form-control" id="searchPropertyId" v-model="searchPropertyId">
                                            <option value="" selected disabled>Property Name</option>
                                            <option value="All" {{ request('searchPropertyId') == 'All' ? 'selected' : '' }}>All</option>
                                            @foreach ($propertyList as $property)
                                                <option value="{{ $property->id }}"
                                                        data-ptype="{{ $property->pType }}"
                                                        {{ request('searchPropertyId') == $property->id && request('pType') == $property->pType ? 'selected' : '' }}>
                                                    {{ $property->unit_name }}
                                                </option>
                                            @endforeach

                                        </select>
                                        <input type="hidden" name="pType" id="pTypeInput"
                                                        value="{{ request('pType') }}">
                                    </div>
                                </div>
                                <!-- Search Type -->
                                <div class="col-12 col-md-4 col-lg-3 col-xl-2 col-xxl">
                                    <div class="form-group mb-0">
                                        <select name="searchtype" class="form-control" v-model="searchtype">
                                            <option value="" selected disabled>Type</option>
                                            <option value="checkin" {{ request('searchtype') == 'checkin' ? 'selected' : '' }}>Check In</option>
                                            <option value="BookingDate" {{ request('searchtype') == 'BookingDate' ? 'selected' : '' }}>Booking Date</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Date Range -->
                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <input type="text" name="searchDateRange" class="form-control flatpickr"
                                            placeholder="From-To" id="searchDateRange"
                                            value="{{ request('searchDateRange') }}" />
                                    </div>
                                </div>
                                <!-- Channel -->
                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <select name="searchChannel" class="form-control" v-model="searchChannel">
                                            @if(Auth::guard('admin')->user()->role_id !=8)
                                                <option value="" selected disabled>Channel</option>
                                                <option value="All" {{ request('searchChannel') == 'All' ? 'selected' : '' }}>All</option>
                                                <option value="Agoda" {{ request('searchChannel') == 'Agoda' ? 'selected' : '' }}>Agoda</option>
                                                <option value="Airbnb" {{ request('searchChannel') == 'Airbnb' ? 'selected' : '' }}>Airbnb</option>
                                                <option value="Booking.com" {{ request('searchChannel') == 'Booking.com' ? 'selected' : '' }}>Booking.com</option>
                                                <option value="MakeMyTrip" {{ request('searchChannel') == 'MakeMyTrip' ? 'selected' : '' }}>MakeMyTrip</option>
                                                <option value="Offline" {{ request('searchChannel') == 'Offline' ? 'selected' : '' }}>Offline</option>
                                                <option value="Website" {{ request('searchChannel') == 'Website' ? 'selected' : '' }}>Website</option>
                                                <option value="Quotation" {{ request('searchChannel') == 'Quotation' ? 'selected' : '' }}>Quotation</option>
                                                <option value="Travel Agent" {{ request('searchChannel') == 'Travel Agent' ? 'selected' : '' }}>Travel Agent</option>
                                            @else
                                                <option value="Travel Agent" {{ request('searchChannel') == 'Travel Agent' ? 'selected' : '' }}>Travel Agent</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <!-- Payment Status -->
                                <div class="col-12 col-md-4 col-lg">
                                    <div class="form-group mb-0">
                                        <select name="searchPaymentStatus" class="form-control"
                                            v-model="searchPaymentStatus">
                                            <option value="" selected disabled>Booking Status</option>
                                             <option value="All" {{ request('searchPaymentStatus') == 'All' ? 'selected' : '' }}>All</option>
                                            <option value="Requested" {{ request('searchPaymentStatus') == 'Requested' ? 'selected' : '' }}>Requested</option>
                                            <option value="Confirmed" {{ request('searchPaymentStatus') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="Not Confirmed" {{ request('searchPaymentStatus') == 'Not Confirmed' ? 'selected' : '' }}>Not Confirmed</option>
                                            <option value="Canceled" {{ request('searchPaymentStatus') == 'Canceled' ? 'selected' : '' }}>Canceled</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Buttons -->
                                <div class="col-12 col-md-auto">
                                    <div class="btn-group gap-1">
                                        <button type="submit" class="btn btn-primary btn-icon rounded" id="search-btn">
                                            <span class="icon-search text-decoration-none"></span>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-clear btn-warning rounded"
                                            onclick="clearFilter()">
                                            <span class="bi bi-arrow-clockwise"></span>
                                        </button>
                                        {{-- <button type="button" class="btn btn-export btn-secondary"
                                            onclick="exportSaleReport()">
                                            <i class="bi bi-file-earmark-excel me-2 fs-6"></i>Export
                                        </button> --}}

                                <a href="{{ route('pms.report.sale-report.export', [
									'searchPaymentStatus' => request('searchPaymentStatus'),
									'pType' => request('pType'),
									'searchPropertyId' => request('searchPropertyId'),
                                    'searchChannel' => request('searchChannel'),
									'searchDateRange' => request('searchDateRange'),
									'searchtype' => request('searchtype'),
								]) }}"
                                    class="btn btn-small btn-export btn-secondary rounded">
                                    <span class="material-symbols-outlined">
                                        file_export
                                    </span>
                                </a>
                                        
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <section class="section mt-2">
                    @if ($isLoading)
                        <div class="d-flex justify-content-center py-5">
                            <div class="spinner-border" role="status"></div>
                        </div>
                    @else
                        @if (count($list) > 0)
                            <div class="outer-wrapper">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table class="table table-list-2 align-middle mb-0 mw-lg">
                                            <thead>
                                                <tr>
                                                    <!--<th>Booking ID</th>-->
                                                    <!--<th>Property Name</th>-->
                                                    <!--<th>Location</th>-->
                                                    <!--<th>Guest Name</th>-->
                                                    <!--<th>Booking Detail</th>-->
                                                    <!--<th>Channel</th>-->
                                                    <!--<th>Price</th>-->
                                                    <!--<th>Paid</th>-->
                                                    <!--<th>Tax</th>-->
                                                    <!--<th>Tax Amount</th>-->
                                                    <!--<th>Booking Status</th>-->
                                                    <th>Booking ID</th>
                                                    <th>Property Name</th>
                                                    <th>Location</th>
                                                    <th>Guest Name</th>
                                                    <th>Booking Detail</th>
                                                    <th>Channel</th>
                                                    
                                                    <th>Base Price</th>
                                                    <th>Markup Price</th>
                                                    <th>Tax</th>
                                                    <th>Tax Amount</th>
                                                    <th>Payable</th>
                                                    <th>Paid</th>
                                                    
                                                    <th>Booking Status</th>
                                                    
                                                    
                                                    
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($list as $item)
                                                    <tr>
                                                        <td>{{ $item['booking_id'] }}</td>
                                                        <td>{{ $item['home_name'] }}</td>
                                                        <td>{{ $item['location'] }}</td>
                                                        <td>{{ $item['guest_name'] }}</td>
                                                        <td>
                                                            Check-in: {{ $item['checkin_date'] }} | Check-out:
                                                            {{ $item['checkout_date'] }} <br>
                                                            Nights: {{ $item['no_of_nights'] }} | Guests:
                                                            {{ $item['no_of_adult'] }} {{ $item['no_of_adult'] == 1 ? 'Adult' : 'Adults' }}, {{ $item['no_of_children'] }}
                                                            Children
                                                        </td>
                                                        <td>{{ $item['channel'] }}</td>
                                                        
                                                        
                                                        <!--<td>{{ $item['payable_amount'] }}</td>-->
                                                        <!--<td>{{ $item['paid_amount'] }}</td>-->
                                                        <!--<td>{{ $item['tax'] }}</td>-->
                                                        <!--<td>{{ $item['tax_amount'] }}</td>-->
                                                        
                                                        
                                                        
                                                        <td>{{ $item['base_price'] }}</td>
                                                        <td>{{ $item['website_markup_price'] }}</td>
                                                        <td>{{ $item['tax'] }}</td>
                                                        <td>{{ $item['tax_amount'] }}</td>
                                                        
                                                        <td>{{ $item['payable_amount'] }}</td>
                                                        <td>{{ $item['paid_amount'] }}</td>
                                                        <td>{{ $item['property_booking_status'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="message text-center">
                                <h6 class="fw-bold">No Record Found</h6>
                            </div>
                        @endif
                    @endif
                </section>
                @if ($pagination->isNotEmpty())
                    <div class="mt-3 p-2">
                    {{ $pagination->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    </section>

    <!-- Include Flatpickr for Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr
       /*  flatpickr("#searchDateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            enableTime: false,
            autoApply: true,
        }); */

        const datePicker = flatpickr('#searchDateRange', {
            mode: 'range',
            dateFormat: 'd/m/Y',
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    // fetchAllData();
                }
            }
        });


//         document.getElementById('sale-report-form').addEventListener('submit', function (e) {
//     const searchtype = document.querySelector('[name="searchtype"]').value;
//     const dateRange = document.getElementById('searchDateRange').value.trim();

//     if (searchtype && !dateRange) {
//         e.preventDefault();
//         document.getElementById('searchDateRange').classList.add('is-invalid');
        
//     } else {
//         document.getElementById('searchDateRange').classList.remove('is-invalid');
//     }
// });

document.getElementById('sale-report-form').addEventListener('submit', function (e) {
    const searchtype = document.querySelector('[name="searchtype"]').value.trim();
    const dateRange = document.getElementById('searchDateRange').value.trim();

    const searchtypeField = document.querySelector('[name="searchtype"]');
    const dateRangeField = document.getElementById('searchDateRange');

    // Case 1: One is filled and the other is empty — show error
    if ((searchtype && !dateRange) || (!searchtype && dateRange)) {
        e.preventDefault();

        // Mark invalid fields
        if (!searchtype) searchtypeField.classList.add('is-invalid');
        else searchtypeField.classList.remove('is-invalid');

        if (!dateRange) dateRangeField.classList.add('is-invalid');
        else dateRangeField.classList.remove('is-invalid');

        // Optional: alert user
        // alert('Please select both Type and Date Range.');

        return false;
    }

    // Case 2: Both filled or both empty — allow submit
    searchtypeField.classList.remove('is-invalid');
    dateRangeField.classList.remove('is-invalid');
});



        // Clear Filter
        function clearFilter() {
            document.getElementById('sale-report-form').reset();
            window.location.href = "{{ route('pms.report.sale-report') }}";
        }

        // Export Sale Report
        function exportSaleReport() {
            let formData = new FormData(document.getElementById('sale-report-form'));
            fetch("{{ route('pms.report.sale-report.export') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'sale-report.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                })
                .catch(error => console.error('Error exporting report:', error));
        }

document.addEventListener('DOMContentLoaded', function() {
            const searchPropertyId = document.getElementById('searchPropertyId');
            const pTypeInput = document.getElementById('pTypeInput');
            if (searchPropertyId) {
                searchPropertyId.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const pType = selectedOption.getAttribute('data-ptype') || '';
                    console.log("Selected pType:", pType);
                    if (pTypeInput) {
                        pTypeInput.value = pType;
                    }
                });
            }
        });


    </script>
    <style scoped>
        .toggleTbl th,
        .toggleTbl td {
            background: #F1F2E3 !important;
        }

        table {
            font-size: 12px !important;
        }
    </style>
@endsection