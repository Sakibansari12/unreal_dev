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
                    <h1 class="fs-5 mb-0">Property Billing Report</h1>
                </div>
                <!--import start -->
                <!--import end -->
                
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.report.property-billing') }}">
                
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md">
                        <div class="input-group input-group-sm">
                             <!--<input type="text" name="search" class="form-control" placeholder="Search by name." value="{{ request('search') }}">-->
                              <select name="month_drop_down" class="form-control">
                                @php 
                                    // Current month ka 1st date
                                    $currentMonth = date('Y-m-01');
                                    
                                    // Pichle 3 mahine ka start (3 months before current)
                                    $startMonth = date('Y-m-01', strtotime('-3 months'));
                                    
                                    // Default selected month (from request or current)
                                    $selectedMonth = request('month_drop_down') 
                                        ? date('Y-m-01', strtotime(request('month_drop_down'))) 
                                        : $currentMonth;
                            
                                    // Date range create karte hain
                                    $start = new DateTime($startMonth);
                                    $end   = new DateTime($currentMonth);
                                    $interval = new DateInterval('P1M');
                                    $period   = new DatePeriod($start, $interval, $end->modify('+1 month'));
                                @endphp
                            
                                @foreach ($period as $monthDate)
                                    @php $value = $monthDate->format('Y-m-01'); @endphp
                                    <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                                        {{ $monthDate->format('F, Y') }}
                                    </option>
                                @endforeach
                            </select>


                             
                             
                             
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>
                            <a href="{{ route('pms.report.property-billing') }}" class="btn btn-warning">
                                <span class="material-symbols-outlined">refresh</span>
                            </a>

                               

                        </div>
                    </div>
                    <div class="col-md">
                        <!-- <select name="stage" class="form-select form-select-sm">-->
                        <!--    <option value="">Select Stage</option>-->
                        <!--    <option value="Cold" {{ request('stage') == 'Cold' ? 'selected' : '' }}>Cold</option>-->
                        <!--    <option value="Warm" {{ request('stage') == 'Warm' ? 'selected' : '' }}>Warm</option>-->
                        <!--    <option value="Hot" {{ request('stage') == 'Hot' ? 'selected' : '' }}>Hot</option>-->
                        <!--    <option value="Dropped" {{ request('stage') == 'Dropped' ? 'selected' : '' }}>Dropped</option>-->
                        <!--    <option value="Booked" {{ request('stage') == 'Booked' ? 'selected' : '' }}>Booked</option>-->
                        <!--</select> -->
                        
                        
                        
                        
                        
                        
                    </div>
                    <!-- Date Range -->
                    <div class="col-6 col-md">
                        <div class="form-group mb-0">
                            <!-- <input type="text" name="searchDateRange" class="form-control flatpickr"
                                placeholder="From-To" id="searchDateRange"
                                value="{{ request('searchDateRange') }}" /> -->
                        </div>
                    </div>
                    
                    <div class="col-auto">
                        <div class="data-info text-primary">
                            
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive data-table text-nowrap">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Property Name</th>
                            <th>Published Date</th>
                            <th>Days Published</th>
                            <!--<th>Last RU (Active/Archive) Date</th>-->
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @forelse($items as $key => $value)
                            

                            <tr data-id="{{ $value->id }}">
                                <td>{{ $value->unit_name ?? '' }}</td>

                                <td>
                                    @if($value->is_published_date)
                                        {{ \Carbon\Carbon::parse($value->is_published_date)->format('d M, Y') }}
                                    @endif
                                </td>
                                
                                <td>
                                    {{ $value->days_published_in_month ?? '' }} days
                                </td>
                                <!--<td>-->
                                <!--    @if($value->is_unpublished_date)-->
                                <!--        {{ \Carbon\Carbon::parse($value->is_unpublished_date)->format('d M, Y') }}-->
                                <!--    @endif-->
                                <!--</td>-->
                                
                                
                                <td>
                                    <button style="padding: 4px 12px; border: none; border-radius: 4px; color: #fff; font-size: 13px; 
                                                   background-color: {{ $value->ru_status == 1 ? '#28a745' : '#C79F62' }};
                                                   cursor: default;">
                                        {{ $value->ru_status == 1 ? 'Active' : 'Archive' }}
                                    </button>
                                </td>





                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="4">No Record Found!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- <div class="delete-all-action pt-3">
                <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE SELECTED</button>
            </div> -->

            <div class="mt-3">
                
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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


    });
</script>

@endsection