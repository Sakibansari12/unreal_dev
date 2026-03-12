@extends('pms.layouts.app')
@section('content')
<style>
    .ui-state-highlight {
        background-color: #e9e9e9;
        height: 50px;
        border: 1px dashed #bbb;
    }
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Channel List</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.channel.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="icon-plus me-2"></i> <span>Add New</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
              <form method="GET" action="{{ route('pms.channel.list') }}">
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md-5 col-lg-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Search by Name" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>
                            <a href="{{ route('pms.channel.list') }}" class="btn btn-warning">
                                <span class="material-symbols-outlined">refresh</span>
                            </a>
                        </div>
                    </div>
                    <div class="col">
                        <div class="data-info text-primary">
                            {{ $specialInvitations->total() }} Result{{ $specialInvitations->total() == 1 ? '' : 's' }} found
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
                            <th>Image</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="">
                        @forelse($specialInvitations as $key => $value)
                        <tr data-id="{{ $value->id }}">
                            <td>
                                <input class="form-check-input check" id="c{{ $key }}" type="checkbox" value="{{ $value->id }}">
                                <label for="c{{ $key }}"></label>
                            </td>
                            <td>
                                @php
                                $imagePath = public_path('storage/channel/' . ($value->image ?? ''));
                                @endphp
                                @if(!empty($value->review_image) && file_exists($imagePath))
                                <img src="{{ asset('storage/channel/' . $value->review_image) }}" alt="Icon" style="width: 50px;" width="100px">
                                @else
                                <svg width="70" height="70" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="4" fill="#f0f0f0"/>
                                    <path d="M4 17L8 13L11 16L16 11L20 15V19H4V17Z" fill="#ccc"/>
                                    <circle cx="8" cy="8" r="2" fill="#ccc"/>
                                </svg>
                                @endif
                            </td>
                            <td>{{ $value->review_name }}</td>
                            
                            <!--<td>{!! Str::limit($value->description, 100) !!}</td>-->
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $value->id }}" {{ $value->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <ul class="actions">
                                    <li><a href="{{ route('pms.channel.form', ['id' => $value->id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                    <li><button type="button" class="btn item_delete" data-value="{{ $value->id }}"><i class="icon-delete"></i></button></li>
                                </ul>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="9">No Record Found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="delete-all-action pt-3">
                <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE SELECTED</button>
            </div>

            <div class="mt-3">
                {{ $specialInvitations->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>

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
                    url: '{{ url("pms/channel/delete") }}/' + id,
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function () {
                        Swal.fire('Deleted!', 'The channel has been deleted.', 'success');
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
                text: 'Selected channel will be deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete selected!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pms/channel/multidelete") }}',
                        method: 'POST',
                        data: { ids: idArray },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function () {
                            Swal.fire('Deleted!', 'Selected channel have been deleted.', 'success');
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
        let status = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: '{{ url("pms/channel/toggle-status") }}/' + id,
            type: 'PUT',
            data: { status: status },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function () {
                toastr.success('Status updated successfully.', 'Success', { timeOut: 2000 });
            },
            error: function () {
                toastr.error('Something went wrong!', 'Error', { timeOut: 2000 });
            }
        });
    });

    
});
</script>
@endsection