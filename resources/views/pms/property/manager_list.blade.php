@extends('pms.layouts.app')
@section('content')
    <section>
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Property Manager: {{ request('name') }}</h1>
                    </div>
                    <div class="col-auto">
                        <!--<a href="{{ route('pms.property.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">-->
                        <!--    <i class="icon-plus me-2"></i> <span>Add New</span>-->
                        <!--</a>-->
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
                <form method="GET" action="{{ route('pms.manager.property.list', ['name' => request('name')]) }}">
                    <div class="row align-items-center mb-3">

                        {{-- Left Side: Result Count --}}
                        <div class="col-md-6">
                            <div class="text-primary">
                                {{ $propertList->count() }} Results found
                            </div>
                        </div>

                        {{-- Right Side: Search Box + Buttons --}}
                        <div class="col-md-6 text-md-end">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Search by name"
                                    value="{{ request('search') }}">


                                <!--<input type="text" name="property_manager" class="form-control"-->
                                <!--    placeholder="Search by Property Manager" value="{{ request('property_manager') }}">-->


                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i>
                                </button>
                                <a href="{{ route('pms.manager.property.list', ['name' => request('name')]) }}" class="btn btn-warning">
                                    <span class="material-symbols-outlined">refresh</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="table-responsive data-table text-nowrap">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                {{-- <th width="45px">
                                    <div class="ch-box">
                                        <input type="checkbox" id="checkAll" class="checkAll">
                                        <label for="checkAll"></label>
                                    </div>
                                </th> --}}
                                <th>Name</th>
                                <th>Location</th>
                                <th>Unit</th>
                                <th>MultiUnit</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($propertList->count() > 0)
                                @foreach ($propertList as $key => $detail)
                                    <tr>
                                        {{-- <td>
                                            <input class="form-check-input check" id="c{{ $key }}" type="checkbox" value="{{ $detail->id }}">
                                            <label for="c{{ $key }}"></label>
                                        </td> --}}
                                        <td>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="tblImg">
                                                        <img src="{{ $detail->primary_image }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col">{{ $detail->home_name }}
                                                    

                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $detail->location }}, {{ $detail->state }}</td>
                                        <td class="text-start">
                                            <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id' => $detail->id, 'pType' => 'unit', 'str' => 'new']) }}"
                                                class="btn btn-dark btn-small rounded-pill me-2 py-1 px-2 text-center"
                                                title="Create New Unit"><span class="material-symbols-outlined">
                                                    add_circle
                                                </span></a>
                                            @if ($detail->units->count() > 0)
                                                <a href="{{ route('pms.property.unit.or.multiunit.list', ['property_id' => $detail->id, 'pType' => 'unit']) }}"
                                                    class="">{{ $detail->units->where('is_published', 1)->count() }}/{{ $detail->units->count() }} <span>Units</span></a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id' => $detail->id, 'pType' => 'multiunit', 'str' => 'new']) }}"
                                                class="btn btn-dark btn-small rounded-pill me-2 py-1 px-2 text-center"
                                                title="Create New Unit"><span class="material-symbols-outlined">
                                                    add_circle
                                                </span></a>
                                            @if ($detail->multiunits->count() > 0)
                                                <a href="{{ route('pms.property.unit.or.multiunit.list', ['property_id' => $detail->id, 'pType' => 'multiunit']) }}"
                                                    class="">{{ $detail->multiunits->count() }}
                                                    <span>Units</span></a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_status" type="checkbox" role="switch"
                                                    data-value="{{ $detail->id }}"
                                                    {{ $detail->status ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="actions">
                                                <li><a href="{{ route('pms.property.form', ['id' => $detail->id]) }}"
                                                        class="btn btn-link"><i class="icon-edit"></i></a></li>
                                                <li><button type="button" class="btn item_delete"
                                                        data-value="{{ $detail->id }}"><i
                                                            class="icon-delete"></i></button></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="6">No Property Found!</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Fancybox.bind('[data-fancybox]', {}); // auto binds all anchors with data-fancybox
        });
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
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('pms/property/delete') }}/' + id,
                            method: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire('Deleted!', response.message ||
                                        'The item has been deleted.', 'success');
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            });

            $(".deleteSelected").click(function() {
                let idArray = $('.check:checked').map(function() {
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
                                url: '{{ url('pms/property/multidelete') }}',
                                method: 'POST',
                                data: {
                                    ids: idArray
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    setTimeout(() => location.reload(), 1500);
                                },
                                error: function() {
                                    Swal.fire('Error', 'Something went wrong!',
                                        'error');
                                }
                            });
                        }
                    });
                }
            });

            $('.btn_status').on('click', function() {
                let id = $(this).data("value");
                $.ajax({
                    url: '{{ url('pms/property/toggle-status') }}/' + id,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success', {
                            timeOut: 2000
                        });
                    },
                    error: function() {
                        toastr.error('Something went wrong!', 'Error', {
                            timeOut: 2000
                        });
                    }
                });
            });
        });
    </script>
@endsection