@extends('pms.layouts.app')
@section('content')
<style>
    .ui-state-highlight {
    background-color: #e9e9e9;  // Placeholder background for drag
    height: 50px;  // Adjust height according to your needs
    border: 1px dashed #bbb;  // Dashed border for visual cue
}
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Leads</h1>
                </div>
                
                 <!--import start -->
                
                <div class="col-auto">
                      <button type="button" class="btn btn-info d-flex btn-small rounded-2 btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        Import CSV
                    </button>
                </div>
                <!--import end -->
                
                <div class="col-auto">
                    <a href="{{ route('pms.lead.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="icon-plus me-2"></i> <span>Add Lead</span>
                    </a>
                </div>
               
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.lead.list') }}">
                
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Search by name." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>
                            <a href="{{ route('pms.lead.list') }}" class="btn btn-warning">
                                <span class="material-symbols-outlined">refresh</span>
                            </a>

                                <a href="{{ route('pms.lead.exportToExcel', [
									'search' => request('search'),
									'searchDateRange' => request('searchDateRange'),
									'searchDateCheckIn' => request('searchDateCheckIn'),
									'stage' => request('stage'),
                                    'page' => request('page'),
								]) }}"
                                    class="btn btn-small btn-export btn-secondary">
                                    <span class="material-symbols-outlined">
                                        file_export
                                    </span>
                                </a>

                        </div>
                    </div>
                    <div class="col-md-auto">
                        <select name="stage" class="form-select form-select-sm">
                            <option value="">Select Stage</option>
                            <option value="Cold" {{ request('stage') == 'Cold' ? 'selected' : '' }}>Cold</option>
                            <option value="Warm" {{ request('stage') == 'Warm' ? 'selected' : '' }}>Warm</option>
                            <option value="Hot" {{ request('stage') == 'Hot' ? 'selected' : '' }}>Hot</option>
                            <option value="Dropped" {{ request('stage') == 'Dropped' ? 'selected' : '' }}>Dropped</option>
                            <option value="Booked" {{ request('stage') == 'Booked' ? 'selected' : '' }}>Booked</option>
                        </select>
                    </div>
                    <!-- Date Range -->
                    <!--<div class="col-6 col-md-auto">-->
                    <!--    <div class="form-group mb-0">-->
                    <!--        <input type="date" class="form-control flatpickr" id="checkOutDate" name="checkOutDate"-->
                    <!--            placeholder="Check out" value="{{ request('checkOutDate') }}" />-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!-- <div class="col-6 col-md-auto">-->
                    <!--    <div class="form-group mb-0">-->
                    <!--        <input type="date" class="form-control flatpickr" name="checkInDate"-->
                    <!--            placeholder="Check in" id="checkInDate" value="{{ request('checkInDate') }}" />-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <div class="col-6 col-md">
                        <div class="form-group mb-0">
                            <input type="text" name="searchDateCheckIn" class="form-control flatpickr"
                                placeholder="Check in" id="searchDateCheckIn"
                                value="{{ request('searchDateCheckIn') }}" />
                        </div>
                    </div>


                    
                    
                    <div class="col-6 col-md">
                        <div class="form-group mb-0">
                            <input type="text" name="searchDateRange" class="form-control flatpickr"
                                placeholder="From-To" id="searchDateRange"
                                value="{{ request('searchDateRange') }}" />
                        </div>
                    </div>
                    
                    <div class="col-auto">
                        <div class="data-info text-primary">
                            {{ $items->total() }} Result{{ $items->total() == 1 ? '' : 's' }} found
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive data-table text-nowrap">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="45px">
                                <div class="ch-box">
                                    <input type="checkbox" id="checkAll" class="checkAll">
                                    <label for="checkAll"></label>
                                </div>
                            </th>
                           
                            <th>Date</th>
                            <th>Added By</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Check in</th>
                            <th>Check out</th>
                            <th>Stage</th>
                            <th>Booking ID</th>
                            <th>Source</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @forelse($items as $key => $value)
                        <tr data-id="{{ $value->id }}">
                            <td>
                                <input class="form-check-input check" id="c{{ $key }}" type="checkbox" value="{{ $value->id }}">
                                <label for="c{{ $key }}"></label>
                            </td>
                           <td>{{ \Carbon\Carbon::parse($value->date)->format('d M, Y') }}</td>
                            <td>
                                
                                @php
                                    $rolePrefix = [
                                        'Super Admin'      => '',
                                        'Admin'            => 'Admin-',
                                        'Owners'           => 'Owners-',
                                        'Property Manager' => 'PM-',
                                    ];
                                
                                    $role = $value->user->role ?? null;
                                    $prefix = $role ? ($rolePrefix[$role] ?? '') : '';
                                    $userName = $value->user->name ?? '';
                                @endphp
                                

                                {{ $prefix }}{{ $value->user->name ?? '' }}
                            </td>
                            <td>{{$value->name ?? ''}}</td>
                            <td>{{$value->email ?? ''}}</td>
                            <td>{{$value->mobile ?? ''}}</td>
                            <td>
                                @if($value->checkin_date)
                                {{ \Carbon\Carbon::parse($value->checkin_date)->format('d M, Y') }}
                                @endif
                                </td>
                            <td>
                                @if($value->checkout_date)
                                {{ \Carbon\Carbon::parse($value->checkout_date)->format('d M, Y') }}
                                @endif
                                </td>
                            <td>{{$value->stage ?? ''}}</td>
                            <td>{{$value->booking_id ?? ''}}</td>
                            <td>{{$value->source ?? ''}}</td>
                            <td>
                                @if(!empty($value->note))
                                    <button type="button" 
                                        class="btn btn-sm btn-outline-primary view-note-btn" 
                                        data-note="{{ $value->note }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#noteModal">
                                        View
                                    </button>
                                @endif
                            </td>

                            <td>
                                <ul class="actions">
                                    <li><a href="{{ route('pms.lead.form', ['id'=>$value->id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                    <li><button type="button" class="btn item_delete" data-value="{{ $value->id }}"><i class="icon-delete"></i></button></li>
                                </ul>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="13">No Record Found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="delete-all-action pt-3">
                <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE SELECTED</button>
            </div>

            <div class="mt-3">
                {{ $items->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="importForm" method="POST" action="{{ route('pms.lead.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Leads</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Upload CSV File</label>
                        <input type="file" name="file" id="file" class="form-control" required accept=".csv">
                    </div>
                    <div id="importErrors" class="alert alert-danger mt-2" style="display:none;"></div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    
                        <a href="{{asset('assets/pms/lead_excel/leads-import.csv')}}" class="btn btn-warning text-white py-2">
                            Download Sample
                        </a>
                    
                    <div>
                        
                    <button id="importBtn" type="button" class="btn btn-primary py-2">Upload</button>
                    <button type="button" class="btn btn-secondary py-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Note View Modal -->
<div class="modal fade" id="noteViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lead Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="noteContent" class="mb-0"></p>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        document.addEventListener('DOMContentLoaded', function () {
             const datePicker = flatpickr('#searchDateCheckIn', {
            mode: 'range',
            dateFormat: 'd/m/Y',
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    // fetchAllData();
                }
            }
        });
             
        });
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const datePicker = flatpickr('#searchDateRange', {
            mode: 'range',
            dateFormat: 'd/m/Y',
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    // fetchAllData();
                }
            }
        });


        $(".checkAll").on('change', function () {
            $('.check').prop('checked', this.checked);
            $('.deleteSelected').prop('disabled', !this.checked);
        });

        $(document).on('change', '.check', function () {
            const anyChecked = $('.check:checked').length > 0;
            $('.deleteSelected').prop('disabled', !anyChecked);
            $('.checkAll').prop('checked', $('.check').length === $('.check:checked').length);
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
                        url: '{{ url("pms/lead/delete") }}/' + id,
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

        $(".deleteSelected").click(function () {
            let idArray = $('.check:checked').map(function () {
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
                            url: '{{ url("pms/lead/multidelete") }}',
                            method: 'POST',
                            data: { ids: idArray },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            },
                            error: function () {
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            }
        });

        $('.btn_status').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/lead/toggle-status") }}/' + id,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message, 'Success', { timeOut: 2000 });
                },
                error: function () {
                    toastr.error('Something went wrong!', 'Error', { timeOut: 2000 });
                }
            });
        });



    /* $('.importBtn').on('click', function(e) {
        e.preventDefault();  
        let formdata = $('#importForm').serialize();
        $.ajax({
            url: $('#importForm').attr('action'),
            type: "POST",  
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  
            },
            success: function(response) {
                if (response['status'] == true) {
                  
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error); 
            }
        });
    }); */
   $(document).ready(function() {
    // Prevent default form submission
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
    });

    $('#importBtn').on('click', function(e) {
        e.preventDefault(); // Prevent any default behavior

        // Reset previous errors
        $('#importErrors').html('').hide();

        let form = $('#importForm')[0];
        let formData = new FormData(form);

        $.ajax({
            url: '{{ route("pms.lead.import") }}', // Use Laravel route directly
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status) {
                    $('#importModal').modal('hide');
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload page to show updated leads
                    });
                }
            },
            error: function(xhr) {
                let response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorHtml = '<ul>';
                    response.errors.forEach(err => {
                        errorHtml += `<li>${err}</li>`;
                    });
                    errorHtml += '</ul>';
                    $('#importErrors').html(errorHtml).show();
                } else {
                    $('#importErrors').html('<p>An unexpected error occurred. Please try again.</p>').show();
                }
            }
        });
    });
});

$(document).on('click', '.view-note-btn', function() {
    let noteText = $(this).data('note') || 'No note available';
    $('#noteContent').text(noteText);
    $('#noteViewModal').modal('show');
});

    });
</script>

@endsection