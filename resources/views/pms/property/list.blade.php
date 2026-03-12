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
                        <h1 class="fs-5 mb-0">All properties</h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.property.form') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="icon-plus me-2"></i> <span>Add New</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
                <form method="GET" action="{{ route('pms.property.list') }}">
                    <div class="row align-items-center mb-3">

                        {{-- Left Side: Result Count --}}
                        
                        <div class="col-md">
                            <div class="text-primary">
                                {{ $propertList->total() }} Result{{ $propertList->total() == 1 ? '' : 's' }} found
                            </div>
                        </div>
                        
                        
                          <div class="col-md-4">
                            <select class="form-select form-control js-select2-search" name="search" id="propertyId">
                                <option value="" selected disabled>Search by property</option>
                                @foreach ($propertyDropdownList as $propertyDropdown)
                                    <option value="{{ $propertyDropdown->home_name }}" 
                                        @selected(old('search', request('search')) == $propertyDropdown->home_name)>
                                        {{ $propertyDropdown->home_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- <div class="col-md-4">
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
                        </div> -->

                        {{-- Right Side: Search Box + Buttons --}}
                        <div class="col-md-auto text-md-end">
                            <div class="input-group input-group-sm">
                               <!-- <input type="text" name="search" class="form-control" placeholder="Search by name, location, state"
                                    value="{{ request('search') }}"> -->
                                
                                
                                <!--<input type="text" name="property_manager" class="form-control" placeholder="Search by Property Manager"-->
                                <!--    value="{{ request('property_manager') }}">-->
            
                                
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i>
                                </button>
                                <a href="{{ route('pms.property.list') }}" class="btn btn-warning">
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
                                <th>Area</th>
                                <th>Unit</th>
                                <th>MultiUnit</th>
                                <!--<th>Status</th>-->
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($propertList->count() > 0)
                                @foreach($propertList as $key => $detail)
                                    <tr>
                                        {{-- <td>
                                            <input class="form-check-input check" id="c{{ $key }}" type="checkbox" value="{{ $detail->id }}">
                                            <label for="c{{ $key }}"></label>
                                        </td> --}}
                                        <td>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="tblImg">
                                                        <img src="{{ asset('assets/pms/images/noimage.jpg') }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col">{{ $detail->home_name }}
                                                
                                             
                                                    
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
                                        <td>{{ $detail->area }}</td>
                                        <td class="text-start">
                                            <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id'=>$detail->id, 'pType'=>'unit', 'str'=>'new']) }}" class="btn btn-dark btn-small rounded-pill me-2 py-1 px-2 text-center" title="Create New Unit"><span class="material-symbols-outlined">
                                                add_circle
                                                </span></a>
                                            @if($detail->units->count() > 0)
                                                <a href="{{ route('pms.property.unit.or.multiunit.list', ['property_id'=>$detail->id, 'pType'=>'unit']) }}" class="">{{ $detail->units->where('is_published', 1)->count() }}/{{ $detail->units->count() }} <span>Units</span></a>
                                            @endif
                                        </td>
                                        
                                       

                                        
                                        <td>
                                            <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id'=>$detail->id, 'pType'=>'multiunit', 'str'=>'new']) }}" class="btn btn-dark btn-small rounded-pill me-2 py-1 px-2 text-center" title="Create New Unit"><span class="material-symbols-outlined">
                                                add_circle
                                                </span></a>
                                            @if($detail->multiunits->count() > 0)
                                                <a href="{{ route('pms.property.unit.or.multiunit.list', ['property_id'=>$detail->id, 'pType'=>'multiunit']) }}" class="">{{ $detail->multiunits->count() }} <span>Units</span></a>
                                            @endif
                                        </td>
                                        <!--<td>-->
                                        <!--    <div class="form-check form-switch">-->
                                        <!--        <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $detail->id }}" {{ $detail->status ? 'checked' : '' }}>-->
                                        <!--    </div>-->
                                        <!--</td>-->
                                        <td>
                                            <ul class="actions">
                                                <li><a href="{{ route('pms.property.form', ['id'=>$detail->id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                                <li><button type="button" class="btn item_delete" data-value="{{ $detail->id }}"><i class="icon-delete"></i></button></li>
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
                <div class="mt-3">
                    {{ $propertList->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
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
        
        
    });
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
                        url: '{{ url("pms/property/delete") }}/' + id,
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        success: function (response) {
                            if(response.status){
                                Swal.fire('Deleted!', response.message || 'The item has been deleted.', 'success');
                                setTimeout(() => location.reload(), 1500);
                            }
                            else{
                                Swal.fire('Error', response.message, 'error');
                            }
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
                            url: '{{ url("pms/property/multidelete") }}',
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
                url: '{{ url("pms/property/toggle-status") }}/' + id,
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