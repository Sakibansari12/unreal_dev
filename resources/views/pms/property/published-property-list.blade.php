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
    <section>
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">All Published Units List</h1>
                    </div>
                    <div class="col-auto">
                        {{-- <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id'=>request('property_id'), 'pType'=>request('pType'), 'str'=>'new']) }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="icon-plus me-2"></i> <span>Add New</span>
                        </a> --}}
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
             
                
                
                
                <form method="GET" action="{{ route('pms.published.property.list',['pType' => 'unit']) }}">
                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <div class="data-info text-primary">
                                {{ $propertList->total() }} Result{{ $propertList->total() == 1 ? '' : 's' }} found
                            </div>
                        </div>
                          
                          <div class="col-md-4">
                            <select class="form-select form-control js-select2-search" name="property_name" id="propertyId">
                                <option value="" selected disabled>Search by property</option>
                                @foreach ($propertyDropdownList as $propertyDropdown)
                                    <option value="{{ $propertyDropdown->unit_name }}" 
                                        @selected(old('search', request('property_name')) == $propertyDropdown->unit_name)>
                                        {{ $propertyDropdown->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <select class="form-select form-control js-select2-search-property-manager" name="property_manager" id="propertymanagerId">
                                <option value="" selected disabled>Search by Property Manager</option>
                                @foreach (
                                    $propertyDropdownList
                                        ->unique('property_manager_name')
                                        as $propertyDropdownmanager
                                )
                                    @if(!empty($propertyDropdownmanager->property_manager_name))
                                        <option value="{{ $propertyDropdownmanager->property_manager_name }}"
                                            @selected(request('property_manager') == $propertyDropdownmanager->property_manager_name)>
                                            {{ $propertyDropdownmanager->property_manager_name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                    
                        <div class="col-md-auto text-md-end">
                            <div class="input-group input-group-sm">
                              
                                
                                <input type="hidden" name="pType" value="unit">
                                
                                
                                
                                <!--<input type="text" name="property_manager" class="form-control" placeholder="Search by Property Manager"-->
                                <!--    value="{{ request('property_manager') }}">-->
            
                                
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i>
                                </button>
                                <a href="{{ route('pms.published.property.list',['pType' => 'unit']) }}" class="btn btn-warning">
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
                                <th width="45px">
                                    <div class="ch-box">
                                        <input type="checkbox" id="checkAll" class="checkAll">
                                        <label for="checkAll"></label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>OTA ID</th>
                                @if($user->role == 'Super Admin')
                                <th>Featured Property</th>
                                @endif
                                <th>Only For Enquiry</th>
                                @if($user->role != 'Travel Agent')
                                <th>Status</th>
                                @endif
                                @if($user->role == 'Super Admin')
                                <th>Publish</th>
                                <th>RU (Active/Archive)</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($propertList->count() > 0)
                                @foreach($propertList as $key => $detail)
                                    <tr>
                                        <td>
                                            <input class="form-check-input check" id="c{{ $key }}" type="checkbox" value="{{ $detail->id }}">
                                            <label for="c{{ $key }}"></label>
                                        </td>
                                        <td>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="tblImg">
                                                        @php $firstImage = $detail->imagesDisplaypms; @endphp
                                                        <img src="{{ $firstImage->display_image }}" width="80" height="80" loading="lazy" alt="">
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    {{ $detail->unit_name }}
                                                    @if($user->role == 'Super Admin')
                                                         @if (!empty($detail->property_manager_name))
                                                            <a href="{{ route('pms.manager.property.list', ['name' => $detail->property_manager_name]) }}" 
                                                                style="display:block; font-size:13px; margin-top:5px; text-decoration:none;">
                                                                {{ $detail->property_manager_name }}
                                                            </a>
                                                         @endif 
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $detail->location }}, {{ $detail->state }}</td>
                                        <td>
                                            {{ $detail->ru_property_id }}
                                        </td>
                                        @if($user->role == 'Super Admin')
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_show_on_home" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->show_on_home ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        @endif
                                         <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_only_for_enquiry" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->only_for_enquiry ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                         @if($user->role != 'Travel Agent')
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_status" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->status ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                         @endif
                                        @if($user->role == 'Super Admin')
                                        <td>
                                            <ul class="actions">
                                                <li class="d-flex align-items-center">
                                                    @if($detail->is_published ==0)
                                                        <a class="btn btn-small w-50 btn-save btn-primary openpublishModal"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#publishModal"
                                                            data-id="{{ $detail->id }}">
                                                            Publish
                                                        </a>
                                                    @else
                                                        <span>Published</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                             @if($detail->is_published ==1)
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input item_is_unpublished" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->ru_status ? 'checked' : '' }}>
                                                </div>
                                             @endif
                                        </td>
                                        @endif
                                        <td>
                                            <ul class="actions">
                                                <li><a href="{{ route('pms.property.unit.or.multiunit.overview', ['id'=>$detail->id, 'type'=>'published', 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                                
                                                <li><button type="button" class="btn item_delete" data-value="{{ $detail->id }}" data-type="{{ $detail->pType }}"><i class="icon-delete"></i></button></li>
                                               
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="text-center">
                                    <td colspan="12">No Property Found!</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $propertList->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>

         <!-- pop modal for payment status change  -->
    <div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg bg-primary text-white">
                    <h5 class="modal-title" id="publishModalLabel">Publish Property: <span class="propertyName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Yaha tera form ya status change ka content aayega -->
                    <form id="PayemntRequestFormId">
                        <div class="row p-2 publishModalBody">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </section>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    let pType = "{{ request()->pType }}";
    document.addEventListener('DOMContentLoaded', function () {
        
        $('.js-select2-search').select2({
            placeholder: "Search by property",
            allowClear: true,
            minimumInputLength: 0,
        });
        
        $('.js-select2-search-property-manager').select2({
            placeholder: "Search by Property Manager",
            allowClear: true,
            minimumInputLength: 0,
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
            const type = $(this).data("type");

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
                        url: '{{ url("pms/property/unit-or-multiunit/delete") }}/' + id+'?pType='+pType,
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
                            url: '{{ url("pms/property/unit-or-multiunit/multidelete") }}?pType='+pType,
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
                url: '{{ url("pms/property/unit-or-multiunit/toggle-status") }}/' + id+'?pType='+pType,
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
        
        $('.item_is_unpublished').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/property/unitormultiunit/unpublish") }}/' + id+'?pType='+pType,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message, 'Success', { timeOut: 2000 });
                },
                error: function (xhr) {
            console.error("🔴 AJAX Error Response:", xhr.responseJSON);
            toastr.error(
                xhr.responseJSON?.message || 'Something went wrong!',
                'Error',
                { timeOut: 2000 }
            );
        }
                
                // error: function () {
                //     toastr.error('Something went wrong!', 'Error', { timeOut: 2000 });
                // }
            });
        });
        
        $('.btn_show_on_home').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/property/unit-or-multiunit/show-on-home") }}/' + id+'?pType='+pType,
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
        $('.btn_only_for_enquiry').on('click', function () {
            let id = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/property/unit-or-multiunit/only_for_enquiry") }}/' + id+'?pType='+pType,
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

        $('.openpublishModal').on('click', function () {
            let id = $(this).data("id");
            $.ajax({
                url: '{{ url("pms/property/unitormultiunit/preview") }}/' + id+'?pType='+pType,
                type: 'GET',
                success: function (response) {
                   $(".propertyName").text(response.detail.unit_name);
                   $(".publishModalBody").empty();
                   $(".publishModalBody").append(response.html);
                },
                error: function () {
                    toastr.error('Something went wrong!', 'Error', { timeOut: 2000 });
                }
            });
        });


        $(document).on('click', '.publish-property', function () {
            const $btn = $(this);
            const id = $btn.data("value");

            if (id) {
                const btnText = $btn.find('.btn-text');

                // Backup original text
                const originalText = btnText.text();

                // Disable button and add spinner
                $btn.prop('disabled', true);
                btnText.html(`Publishing... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>`);

                $.ajax({
                    url: '{{ route("pms.property.unit.or.multiunit.publish") }}/' + id + '?pType=' + pType,
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success').then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    },
                    complete: function () {
                        // Restore button
                        $btn.prop('disabled', false);
                        btnText.text(originalText);
                    }
                });
            } else {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        });



    });
</script>