@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Icons List</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.icons.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="icon-plus me-2"></i> <span>Add New</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.icons.list') }}">
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md-5 col-lg-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search_name" class="form-control" placeholder="Search by name." value="{{ request('search_name') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>
                            <a href="{{ route('pms.icons.list') }}" class="btn btn-warning">
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
                            <th>Icon</th>
                            <th>Name</th>
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

                            <td>
                                @php
                                $imagePath = public_path('storage/icons/' . ($value->icons_image ?? ''));
                                @endphp

                                @if(!empty($value->icons_image) && file_exists($imagePath))
                                <img src="{{ asset('storage/icons/' . $value->icons_image) }}" style="width: 50px;" alt="icon">
                                @else
                                {!! $value->icons_code !!}
                                @endif
                            </td>

                            <td>{{ $value->icons_name }}</td>

                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input btn_status"
                                        type="checkbox"
                                        role="switch"
                                        data-value="{{ $value->id }}"
                                        {{ $value->status ? 'checked' : '' }}>
                                </div>
                            </td>

                            <td>
                                <ul class="actions">
                                    <li>
                                        <a href="{{ route('pms.icons.form', ['id'=>$value->id]) }}" class="btn btn-link">
                                            <i class="icon-edit"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="btn item_delete" data-value="{{ $value->id }}">
                                            <i class="icon-delete"></i>
                                        </button>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="6">No Record Found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="data-table-footer">
                <div class="row gy-4 align-items-center">
                    <div class="col">
                        <button type="button" class="btn text-nowrap btn-small btn-danger py-2 deleteSelected" disabled>
                            Delete Selected
                        </button>
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
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pms/icons/delete") }}/' + id,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        }
                    });
                }
            });
        });

        $(".deleteSelected").click(function() {

            let idArray = $('.check:checked').map(function() {
                return this.value;
            }).get();

            if (idArray.length === 0) {
                toastr.warning('Please select at least one icon.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'Selected icons will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pms/icons/multidelete") }}',
                        method: 'POST',
                        data: {
                            ids: idArray
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        },
                        error: function() {
                            toastr.error('Something went wrong.');
                        }
                    });
                } else {
                    toastr.info('Deletion cancelled.');
                }
            });
        });

        $('.btn_status').on('click', function() {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/icons/toggle-status") }}/' + id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                }
            });
        });
    });
</script>
@endsection