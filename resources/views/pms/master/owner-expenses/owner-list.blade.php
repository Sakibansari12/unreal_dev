@extends('pms.layouts.app')
@section('content')
<style>
    .ui-state-highlight {
    background-color: #e9e9e9;  // Placeholder background for drag
    height: 50px;  // Adjust height according to your needs
    border: 1px dashed #bbb;  // Dashed border for visual cue
}

        .select2-container {
            width: 100% !important;
            margin: 10px 0;
        }
        .select2-container .select2-selection--single {
            height: 38px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
          height: 38px;
        }



</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Owner List</h1>
                </div>
                
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.owner.list') }}">
                
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
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
                            <th>Owner Name</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @forelse($items as $key => $value)
                        <tr data-id="{{ $value->id }}">
                            @php
                                $routeParams = [
                                    'property_id'   => $value->id,
                                    'pType' => $value->pType,
                                ];

                                if (!empty($value->ownerData?->id)) {
                                    $routeParams['owner_id'] = $value->ownerData->id;
                                }
                            @endphp

                            <td>
                                @if(!empty($value->ownerData?->name))
                                    <a href="{{ route('pms.owner-revenue.list', $routeParams) }}" class="text-decoration-none">
                                        {{ $value->ownerData->name }}
                                    </a>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('pms.owner-revenue.list', $routeParams) }}" class="text-decoration-none">
                                    {{ $value->unit_name ?? '' }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="8">No Record Found!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                 {{ $items->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.js-select2-search').select2({
            placeholder: "Search for a unit",
            allowClear: true,
            minimumInputLength: 0,
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

$('#searchUnitId').on('change', function () {
    var selectedOption = $(this).find('option:selected');
    var pType = selectedOption.data('ptype');
    $('#pType').val(pType);
});


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
                        url: '{{ url("pms/owner-expenses/delete") }}/' + id,
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
                            url: '{{ url("pms/owner-expenses/multidelete") }}',
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
                url: '{{ url("pms/owner-expenses/toggle-status") }}/' + id,
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
                    url: '{{ route("pms.collection.position") }}',
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
                url: '{{ url("pms/collection/show_home/toggle-status") }}/' + id,
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