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
                    <h1 class="fs-5 mb-0">Landing Page List</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.landing.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="icon-plus me-2"></i> <span>Add New</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.landing.list') }}">
                
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md-5 col-lg-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Search by Landing Page." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>
                            <a href="{{ route('pms.landing.list') }}" class="btn btn-warning">
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
                            <th >
                                <div class="ch-box">
                                    <input type="checkbox" id="checkAll" class="checkAll">
                                    <label for="checkAll"></label>
                                </div>
                            </th>
                            <th>Banner Image</th>
                            <th>Title</th>
                            <th>Landing page url</th>
                            <th>Status</th>
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
                            <td>
                                @php
                                $imagePath = public_path('storage/landing/' . ($value->image ?? ''));
                                @endphp
                                @if(!empty($value->image) && file_exists($imagePath))
                                <img src="{{ asset('storage/landing/' . $value->image) }}" style="width: 50px;" alt="landing Image" width="100px">
                                @else
                                <svg width="70" height="70" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="24" height="24" rx="4" fill="#f0f0f0"/>
                                    <path d="M4 17L8 13L11 16L16 11L20 15V19H4V17Z" fill="#ccc"/>
                                    <circle cx="8" cy="8" r="2" fill="#ccc"/>
                                </svg>
                                @endif
                            </td>
                            <td>{{$value->title}}</td>
                           <td>
                                    <div class="position-relative">
                                        <button
                                            class="btn btn-small btn-secondary"
                                            onclick="copyToClipboard(this)"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-text="{{ url('landing/page/' . ($value->slug ?? 'No link available')) }}"
                                            title="Copy">
                                            <i class="bi bi-copy me-1"></i> Copy
                                        </button>
                                    </div>
                                </td>
                            
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $value->id }}" {{ $value->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <ul class="actions">
                                    <li><a href="{{ route('pms.landing.form', ['id'=>$value->id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                    <li><button type="button" class="btn item_delete" data-value="{{ $value->id }}"><i class="icon-delete"></i></button></li>
                                </ul>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="5">No Record Found!</td>
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
    function copyToClipboard(button) {
        const textToCopy = button.getAttribute('data-text');
        navigator.clipboard.writeText(textToCopy).then(() => {
            const tooltip = bootstrap.Tooltip.getInstance(button);
            if (tooltip) {
                tooltip.setContent({ '.tooltip-inner': 'Copied!' });
                tooltip.show();

                setTimeout(() => {
                    tooltip.setContent({ '.tooltip-inner': 'Copy' });
                    tooltip.hide();
                }, 1000);
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function () {

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
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
                        url: '{{ url("pms/landing/delete") }}/' + id,
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
                            url: '{{ url("pms/landing/multidelete") }}',
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
                url: '{{ url("pms/landing/toggle-status") }}/' + id,
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

        $("#sortable").sortable({
            items: "tr",  // Specify the item to be draggable (rows in your case)
            handle: ".cursor-grab",  // This makes the drag work only when clicking on the "drag_indicator" icon
            placeholder: "ui-state-highlight",  // Placeholder for the dragged item
            update: function (event, ui) {
                let positionArray = [];
                $("#sortable tr").each(function () {
                    positionArray.push($(this).data("id"));
                });

                $.ajax({
                    url: '{{ route("pms.landing.position") }}',
                    type: 'POST',
                    data: { position: positionArray },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function (response) {
                        toastr.success(response.message);
                    },
                    error: function (xhr) {
                        toastr.error('Something went wrong: ' + (xhr.responseJSON?.message || 'Error'));
                    }
                });
            }
        });

        $('.btn_show_home').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/landing/show_home/toggle-status") }}/' + id,
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