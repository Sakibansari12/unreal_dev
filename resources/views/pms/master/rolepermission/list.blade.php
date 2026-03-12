@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Role/Permission List</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.rolepermission.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="icon-plus me-2"></i> <span>Add New</span>
                    </a>
                </div>
            </div>
        </div> 
        <div class="content-box p-3">
            <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                <div class="col-sm-6 col-md-5 col-lg-4">
                    <div class="position-relative">
                        <form method="GET" action="{{ route('pms.rolepermission.list') }}">
                            @csrf
                             <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                                <div class="col">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search_role" class="form-control" placeholder="Search by role name." value="{{ request('search_role') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-search"></i>
                                        </button>
                                        <a href="{{ route('pms.rolepermission.list') }}" class="btn btn-warning">
                                            <span class="material-symbols-outlined">refresh</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive data-table text-nowrap">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formattedData as $data)
                            <tr>
                                <td>{{ $data['role_name'] ?? '' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $data['role_id'] }}" {{ $data['role_status'] ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <ul class="actions">

                                        <li><a href="{{ route('pms.rolepermission.form', $data['role_id']) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>

                                        <li><button type="button" class="btn item_delete" data-value="{{ $data['role_id']}}"><i class="icon-delete"></i></button></li>
                                    </ul>
                                </td>
                            </tr>
                        @empty
                            <tr class="text-center">
                                <td colspan="6">No role permission found!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $results->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {

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
                        url: '{{ url("pms/rolepermission/delete") }}/' + id,
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

        $('.btn_status').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/rolepermission/toggle-status") }}/' + id,
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