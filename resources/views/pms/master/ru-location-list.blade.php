@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
       <div class="title">
          <div class="row align-items-center">
              <div class="col">
                 <h1 class="fs-5 mb-0">Location List (OTA)</h1>
              </div>
              <div class="col-auto">
                <!-- Add button placeholder -->
              </div>
          </div>
       </div>

        <div class="content-box p-3">
            <form method="GET" action="{{ url('pms/ru-location/list') }}">
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md-5 col-lg-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search_location" class="form-control" placeholder="Search by location." value="{{ request('search_location') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>  <!-- Using an icon here -->
                            </button>
                            <a href="{{ route('pms.ru_location.list') }}" class="btn btn-warning">
                                
                                <span class="material-symbols-outlined">refresh</span>
                            </a>
                        </div>
                    </div>
                    <div class="col">
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
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $value)
                        <tr>
                            <td>
                                <input class="form-check-input check" id="c{{ $key }}" type="checkbox" value="{{ $value->id }}">
                                <label for="c{{ $key }}"></label>
                            </td>	
                            <td>{{ $value->name }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $value->id }}" {{ $value->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>                
                                <ul class="actions">          
                                    <li><button type="button" class="btn item_delete" data-value="{{ $value->id }}"><i class="icon-delete"></i></button></li>     
                                </ul>                           
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
            <div class="delete-all-action pt-3">
                <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE SELECTED</button>
            </div>
            <div class="mt-3">
                {{ $items->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function(){

        // Toggle all checkboxes
        $(".checkAll").on('change', function () {
            $('.check').prop('checked', this.checked);
            $('.deleteSelected').prop('disabled', !this.checked);
        });

        // Enable/disable Delete Selected button based on checkbox state
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
	                    url: '{{ url("pms/ru-location/delete") }}/' + id, // adjust route if needed
	                    method: "DELETE",
	                    headers: {
		                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		                },
	                    success: function (response) {
	                        Swal.fire({
	                            title: 'Deleted!',
	                            text: response.message || 'The item has been deleted.',
	                            icon: 'success',
	                            timer: 2000,
	                            toast: true,
	                            position: 'top-right',
	                            showConfirmButton: false
	                        }).then(() => {
	                            location.reload();
	                        });
	                    },
	                    error: function (xhr) {
	                        Swal.fire({
	                            title: 'Error',
	                            text: 'Something went wrong!',
	                            icon: 'error'
	                        });
	                    }
	                });
	            }
	        });
	    });

     	$(".deleteSelected").click(function(){
		    let idArray = [];

		    $('.check:checked').each(function(){
		        idArray.push($(this).val());
		    });

		    if (idArray.length === 0) {
		        $('.deleteSelected').prop('disabled', true);
		    } else {
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
		                    url: '{{ url("pms/ru-location/multidelete") }}',
		                    method: 'POST',
		                    data: {
		                        ids: idArray
		                    },
		                    headers: {
		                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		                    },
		                    success: function(response) {
		                        Swal.fire({
		                            title: 'Deleted!',
		                            text: response.message || 'Selected records have been deleted.',
		                            icon: 'success',
		                            timer: 2000,
		                            toast: true,
		                            position: 'top-right',
		                            showConfirmButton: false
		                        }).then(() => {
		                            location.reload();
		                        });
		                    },
		                    error: function(xhr, status, error) {
		                        Swal.fire({
		                            title: 'Error',
		                            text: 'Something went wrong!',
		                            icon: 'error'
		                        });
		                    }
		                });
		            }
		        });
		    }
		});

        // Status toggle via AJAX
        $('.btn_status').on('click', function(){
            let id  = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/ru-location/toggle-status") }}/' + id,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000,
                        positionClass: 'toast-top-right'
                    });
                },
                error: function () {
                    toastr.error('Something went wrong!', 'Error', {
                        timeOut: 2000,
                        positionClass: 'toast-top-right'
                    });
                }
            });
        });

    });
</script>
@endsection
