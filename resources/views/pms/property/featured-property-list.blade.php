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
                        <h1 class="fs-5 mb-0">All Featured Units</h1>
                    </div>
                    <div class="col-auto">
                        {{-- <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id'=>request('property_id'), 'pType'=>request('pType'), 'str'=>'new']) }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="icon-plus me-2"></i> <span>Add New</span>
                        </a> --}}
                    </div>
                </div>
            </div>
            <div class="content-box p-3">
                <form method="GET" action="{{ route('pms.featured.property.list', ['pType' => 'unit']) }}">
                    <div class="row align-items-center mb-3">  
                        <div class="col">
                            <div class="data-info text-primary">
                                {{ $propertList->total() }} Result{{ $propertList->total() == 1 ? '' : 's' }} found
                            </div>
                        </div>
                        
                       <div class="col-md">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input order-type-radio" type="radio" name="order_type" id="randomOrder" value="random"
                                    {{ $orderType == 'random' ? 'checked' : '' }}>
                                <label class="form-check-label" for="randomOrder">Random Order</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input order-type-radio" type="radio" name="order_type" id="fixedOrder" value="fixed"
                                    {{ $orderType == 'fixed' ? 'checked' : '' }}>
                                <label class="form-check-label" for="fixedOrder">Fixed Order</label>
                            </div>
                        </div>

                        
                        
                          <div class="col-md-4">
                            <select class="form-select form-control js-select2-search" name="property_name" id="propertyId">
                                <option value="" selected disabled>Search by Unit</option>
                                @foreach ($propertyDropdownList as $propertyDropdown)
                                    <option value="{{ $propertyDropdown->unit_name }}" 
                                        @selected(old('search', request('property_name')) == $propertyDropdown->unit_name)>
                                        {{ $propertyDropdown->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
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
                                <a href="{{ route('pms.featured.property.list', ['pType' => 'unit']) }}" class="btn btn-warning">
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
                               
                                <th>Name</th>
                                <th>Location</th>
                                <th>OTA ID</th>
                                <th>Position</th>
                                @if($user->role == 'Super Admin')
                                <th>Featured Property</th>
                                @endif
                                
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @if($propertList->count() > 0)
                                @foreach($propertList as $key => $detail)
                                    <tr data-id="{{ $detail->id }}">
                                        
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
                                                            <a  
                                                                style="display:block; font-size:13px; margin-top:5px; text-decoration:none; color: #C79F62">
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
                                        <td class="text-muted">
                                            <span class="material-symbols-outlined cursor-grab" style="cursor: pointer;">drag_indicator</span>
                                        </td>
                                        @if($user->role == 'Super Admin')
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_show_on_home" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->show_on_home ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        @endif
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
    <div class="modal fade" id="publishModal" tabindex="-1" aria-labelledby="publishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg bg-primary text-white">
                    <h5 class="modal-title" id="publishModalLabel">Publish Property: <span class="propertyName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="PayemntRequestFormId">
                        <div class="row p-2 publishModalBody">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    let pType = "{{ request()->pType }}";
    document.addEventListener('DOMContentLoaded', function () {
        
        $('.js-select2-search').select2({
            placeholder: "Search by Unit",
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
    });
    document.addEventListener('DOMContentLoaded', function () {
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
                    url: '{{ route("pms.featured.position") }}',
                    type: 'POST',
                    data: { position: positionArray },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    },
                    success: function (response) {
                        console.log(response,"response");
                        toastr.success(response.message);
                    },
                    error: function (xhr) {
                        console.log(xhr,"dd");
                        toastr.error('Something went wrong: ' + (xhr.responseJSON?.message || 'Error'));
                    }
                });
            }
        });
        
        $('.order-type-radio').on('change', function() {
            let orderType = $(this).val();

            $.ajax({
                url: '{{ route("pms.featured.order-type") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                data: { order_type: orderType },
                success: function(response) {
                    toastr.success(response.message, 'Success');
                },
                error: function(xhr) {
                    toastr.error('Something went wrong: ' + (xhr.responseJSON?.message || 'Error'));
                }
            });
        });
        
    });
    
</script>
@endsection