@extends('pms.layouts.app')
@section('content')
    <style>
        .pdf-btn {
            padding: 10px 25px;
            border-radius: 15px;
            text-decoration: none;
        }
        .ui-sortable-helper {
         cursor: pointer;
        }
    </style>
    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Team List</h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.team.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="icon-plus me-2"></i> <span>Add New</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="content-box p-3">
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
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @forelse($items as $key => $value)
                                <tr data-id="{{ $value->id }}">
                                    <td>
                                        <input class="form-check-input check" id="c{{ $key }}" type="checkbox"
                                            value="{{ $value->id }}">
                                        <label for="c{{ $key }}"></label>
                                    </td>
                                    <td>
                                        <img src="{{ $value->image ? asset('storage/' . $value->image) : asset('images/placeholder.png') }}"
                                            width="100px" alt="{{ $value->name }}">
                                    </td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->designation }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input btn_status" type="checkbox" role="switch"
                                                data-value="{{ $value->id }}" {{ $value->status ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <ul class="actions">
                                            <li>
                                                <a href="{{ route('pms.team.form', ['id' => $value->id]) }}"
                                                    class="btn btn-link">
                                                    <i class="icon-edit"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" class="btn item_delete"
                                                    data-value="{{ $value->id }}">
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

                <div class="delete-all-action pt-3">
                    <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE
                        SELECTED</button>
                </div>

                <div class="mt-3">
                    {{ $items->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="_token"]').getAttribute('content');

            // Check All functionality
            $('.checkAll').on('change', function() {
                $('.check').prop('checked', this.checked);
                $('.deleteSelected').prop('disabled', !this.checked);
            });

            $(document).on('change', '.check', function() {
                const anyChecked = $('.check:checked').length > 0;
                $('.deleteSelected').prop('disabled', !anyChecked);
                $('.checkAll').prop('checked', $('.check').length === $('.check:checked').length);
            });

            // Single Delete
            $(document).on('click', '.item_delete', function() {
                const id = $(this).data('value');

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
                            url: `{{ url('pms/team/delete') }}/${id}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message ||
                                    'The item has been deleted.', 'success');
                                setTimeout(() => location.reload(), 1500);
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Failed to delete the item.', 'error');
                            }
                        });
                    }
                });
            });

            // Multiple Delete
            $('.deleteSelected').on('click', function() {
                const idArray = $('.check:checked').map(function() {
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
                        confirmButtonText: 'Yes, delete selected!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ url('pms/team/multidelete') }}',
                                method: 'POST',
                                data: {
                                    ids: idArray
                                },
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                success: function(response) {
                                    Swal.fire('Deleted!', response.message ||
                                        'Selected items deleted.', 'success');
                                    setTimeout(() => location.reload(), 1500);
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', xhr.responseJSON?.message ||
                                        'Failed to delete selected items.', 'error');
                                }
                            });
                        }
                    });
                }
            });

            // Toggle Status
            $('.btn_status').on('click', function() {
                const id = $(this).data('value');
                $.ajax({
                    url: `{{ url('pms/team/toggle-status') }}/${id}`,
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        toastr.success(response.message || 'Status updated successfully.',
                            'Success', {
                                timeOut: 2000
                            });
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to update status.',
                            'Error', {
                                timeOut: 2000
                            });
                    }
                });
            });

            // Sortable Functionality
            $('#sortable').sortable({
                items: 'tr',
                placeholder: 'ui-state-highlight',
                update: function() {
                    const positionArray = $('#sortable tr').map(function() {
                        return $(this).data('id');
                    }).get();

                    $.ajax({
                        url: '{{ route('pms.team.position') }}', // Corrected route
                        method: 'POST',
                        data: {
                            position: positionArray
                        },
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            toastr.success(response.message ||
                                'Positions updated successfully.');
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.message ||
                                'Failed to update positions.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
