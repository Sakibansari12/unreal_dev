@extends('pms.layouts.app')
@section('content')
    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Booking Enquiry List</h1>
                    </div>
                    <div class="col-auto">

                    </div>
                </div>
            </div>
            <div class="content-box p-3">
                <form method="GET" action="{{ route('pms.booking.bookingEnquiry') }}" class="searchBox mobSearch p-2">
                    <div class="row gy-3 gx-2 align-items-end">
                        <div class="col-12 col-md-4 col-lg-3 col-xxl">
                            <div class="form-group mb-0">
                                <input type="date" class="form-control flatpickr" name="checkInDate"
                                    placeholder="Arrival" id="checkInDate"
                                    value="{{ request('checkInDate') }}" />
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3 col-xxl">
                            <div class="form-group mb-0">
                                  <input type="date" class="form-control flatpickr" name="checkOutDate"
                                    placeholder="Departure" id="checkOutDate"
                                    value="{{ request('checkOutDate') }}" />
                            </div>
                        </div>
                        <div class="col-12 col-md-auto">
                            <div class="btn-group gap-2 mb-0">
                                <button type="submit" class="btn btn-small btn-primary btn-icon">
                                    <i class="icon-search"></i>
                                </button>
                                <a href="{{ route('pms.booking.bookingEnquiry') }}" class="btn btn-warning btn-small">
                                    <span class="material-symbols-outlined">refresh</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col mt-2">
                        <div class="data-info text-primary">
                            {{ $list->total() }} Results found
                        </div>
                    </div>
                </form>
                <div class="tableWrapper mt-2">
                    <div class="outer-wrapper">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-list mb-0 mw-lg">
                                    <thead>
                                        <tr>
                                            <th width="10px">
                                                <div class="ch-box">
                                                    <input type="checkbox" id="checkAll" class="checkAll">
                                                    <label for="checkAll"></label>
                                                </div>
                                            </th>
                                            <th>Property Name</th>
                                            <th>Location</th>
                                            <th>Enquiry Created At</th>
                                            <th>Booking Detail</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile No.</th>
                                            <th>Message</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($list->count() > 0)
                                            @foreach ($list as $key => $item)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input check" id="c{{ $key }}"
                                                            type="checkbox" value="{{ $item->id }}">
                                                        <label for="c{{ $key }}"></label>
                                                    </td>
                                                    <td>
                                                        {{ $item->property_name ?? '' }}
                                                    </td>
                                                    <td>
                                                        {{ $item->propertyLocation->location_name ?? '' }}
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime($item->created_at)) ?? '' }}</td>
                                                    <td nowrap >
                                                        <i class="bi bi-calendar2-check"></i> Arrival:
                                                        {{ \Carbon\Carbon::parse($item->checkin_date)->format('d F Y') ?? '' }}
                                                        | Departure:
                                                        {{ \Carbon\Carbon::parse($item->checkout_date)->format('d F Y') ?? '' }}<br>
                                                        <i class="bi bi-moon"></i> No of
                                                        Nights:{{ $item->no_of_night ?? '' }}<br>
                                                        <i class="bi bi-people"></i> No of
                                                        Guests:{{ $item->no_of_guest ?? '' }}
                                                    </td>
                                                    <td>{{ $item->name ?? '' }}</td>
                                                    <td>{{ $item->email ?? '' }}</td>
                                                    <td>{{ $item->phone_no ?? '' }}</td>
                                                    <td>{{ $item->enquiry_message ?? '' }}</td>
                                                    <td>
                                                        <ul class="actions">
                                                            <li><button type="button" class="btn item_delete"
                                                                    data-value="{{ $item->id }}"><i
                                                                        class="icon-delete"></i></button></li>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="text-center">
                                                <td colspan="9">No Enquiry Found!</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="delete-all-action pt-3">
                    <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE
                        SELECTED</button>
                </div>
                <div class="mt-3 p-2">
                    {{ $list->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInDate = flatpickr('#checkInDate', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: "d/m/Y",
                // minDate: "today",
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
        document.addEventListener('DOMContentLoaded', function() {
            $(".checkAll").on('change', function() {
                $('.check').prop('checked', this.checked);
                $('.deleteSelected').prop('disabled', !this.checked);
            });
            $(document).on('change', '.check', function() {
                const anyChecked = $('.check:checked').length > 0;
                $('.deleteSelected').prop('disabled', !anyChecked);
                $('.checkAll').prop('checked', $('.check').length === $('.check:checked').length);
            });
            $(document).on('click', '.item_delete', function() {
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
                            url: '{{ url('pms/booking/enquiry/delete') }}/' + id,
                            method: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message ||
                                    'The item has been deleted.', 'success');
                                setTimeout(() => location.reload(), 1500);
                            },
                            error: function() {
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            });
            $(".deleteSelected").click(function() {
                let idArray = $('.check:checked').map(function() {
                    return this.value;
                }).get();
                if (idArray.length > 0) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Selected records will be deleted!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete selected!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ url('pms/booking/enquiry/multidelete') }}',
                                method: 'POST',
                                data: {
                                    ids: idArray
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    setTimeout(() => location.reload(), 1500);
                                },
                                error: function() {
                                    Swal.fire('Error', 'Something went wrong!',
                                        'error');
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkIn = document.querySelector('[name="checkInDate"]');
            const checkOut = document.querySelector('[name="checkOutDate"]');
            const searchBtn = document.querySelector('button[type="submit"]');
        
            function toggleSearchButton() {
                const checkInVal = checkIn.value.trim();
                const checkOutVal = checkOut.value.trim();
                if ((checkInVal && !checkOutVal) || (!checkInVal && checkOutVal)) {
                    searchBtn.disabled = true;
                } else {
                    searchBtn.disabled = false;
                }
            }
        
            checkIn.addEventListener('input', toggleSearchButton);
            checkOut.addEventListener('input', toggleSearchButton);
            
            toggleSearchButton(); 
        });
        </script>
        
@endsection