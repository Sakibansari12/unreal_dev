@extends('pms.layouts.app')
@section('content')
    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Area List</h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.area.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="icon-plus me-2"></i> <span>Add New</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
                <form method="GET" action="{{ route('pms.area.list') }}">
                    <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                        <div class="col-sm-6 col-md-5 col-lg-4">
                            <div class="input-group input-group-sm">                    
                                <input type="text" name="search_name" class="form-control" placeholder="Search by name." value="{{ request('search_name') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i>
                                </button>
                                <a href="{{ route('pms.area.list') }}" class="btn btn-warning">
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
                                <th>State</th>
                                <th>Location</th>
                                <th>Area Name</th>
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
                               
                                <td>{{ $value->state_name }}</td>
                                <td>{{ $value->location_name }}</td>
                                <td>{{ $value->area_name }}</td>
                               
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $value->id }}" {{ $value->status ? 'checked' : '' }}>
                                    </div>
                                </td> 
                                <td>
                                    <ul class="actions">
                                        <li><a href="{{ route('pms.area.form', ['id'=>$value->id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                        <li><button type="button" class="btn item_delete" data-value="{{ $value->id }}"><i class="icon-delete"></i></button></li>
                                    </ul>
                                </td>
                            </tr> 
                            @empty
                            <tr>
                                <td class="text-center" colspan="7">No Record Found!</td>
                            </tr>
                            @endforelse       
                        </tbody>
                    </table>
                </div>
                <div class="data-table-footer">
                    <div class="row gy-4 align-items-center">
                        <div class="col">
                            <button type="button" class="btn text-nowrap btn-small btn-danger py-2 deleteSelected" disabled>Delete Selected</button>
                        </div>
                        <div class="col-auto">
                            {{ $items->links('pagination::bootstrap-5') }}
                        </div>
                    </div> 
                </div>   

            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                            url: '{{ url("pms/area/delete") }}/' + id,
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
                                url: '{{ url("pms/area/multidelete") }}',
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
                    url: '{{ url("pms/area/toggle-status") }}/' + id,
                    type: 'post',
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
            $('.btn_show_home').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/area/show_home/toggle-status") }}/' + id,
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
        });
    </script>
@endsection