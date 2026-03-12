@extends('pms.layouts.app')
@section('content')
<style>
    /* Icon actions */
.btn-action {
    border: none;
    background: transparent;
    padding: 4px;
    color: #000;
}

.btn-action:hover {
    color: #0d6efd;
}

/* Divider */
.action-divider {
    width: 1px;
    height: 18px;
    background: #ddd;
    margin: 0 6px;
}

/* Pill buttons */
.btn-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 12px;
    border-radius: 20px;
    background: #eaf6fb;
    color: #0369a1;
    border: 1px solid #cde9f3;
}

.btn-pill:hover {
    background: #d8eff8;
}

/* Secondary pill */
.btn-pill-secondary {
    background: #eefaf3;
    color: #166534;
    border-color: #d1fae5;
}

</style>

    <section>
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Property: {{ $parentHome->unit_name }} </h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.property.unit.or.multiunit.overview', ['property_id'=>request('property_id'), 'pType'=>request('pType'), 'str'=>'new']) }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="icon-plus me-2"></i> <span>Add New</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
                <form method="GET" action="{{ route('pms.property.unit.or.multiunit.list',['property_id'=>request()->property_id,'pType'=>request()->pType]) }}">
                    <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                        <div class="col-sm-6 col-md-5 col-lg-4">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search_name" class="form-control" placeholder="Search by name." value="{{ request('search_name') }}">
                                <input type="hidden" name="property_id" value="{{request()->property_id}}">
                                <input type="hidden" name="pType" value="{{request()->pType}}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-search"></i>
                                </button>
                                <a href="{{ route('pms.property.unit.or.multiunit.list',['property_id'=>request()->property_id,'pType'=>request()->pType]) }}" class="btn btn-warning">
                                    <span class="material-symbols-outlined">refresh</span>
                                </a>
                            </div>
                        </div>
                        <div class="col">
                            <div class="data-info text-primary">
                                {{ $propertList->total() }} Result{{ $propertList->total() == 1 ? '' : 's' }} found
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
                                <th>Area</th>
                                <th>OTA ID</th>
                                @if($user->role == 'Super Admin')
                                <th>Show On Home</th>
                                @endif
                                <!-- <th>Only For Enquiry</th> -->
                                @if($user->role != 'Travel Agent')
                                <th>Status</th>
                                @endif
                                @if($user->role == 'Super Admin')
                                <th>Full Publish</th>
                                <!-- <th>Website Publish</th> -->
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
                                                        
                                                        <img src="{{ $detail->primaryImageRelation 
                                                                    ? url($detail->primaryImageRelation->filename) 
                                                                    : url('/assets/pms/images/noimage.jpg') }}" alt="">
                                                    </div>
                                                </div>
                                                <div class="col">{{ $detail->unit_name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $detail->location }}, {{ $detail->state }}</td>
                                        <td>{{ $detail->area }}</td>
                                        <td>
                                            @if($detail->is_published == 1)
                                            {{ $detail->ru_property_id }}
                                            @endif
                                        </td>
                                        @if($user->role == 'Super Admin')
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_show_on_home" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->show_on_home ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        @endif
                                         <!-- <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_only_for_enquiry" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->only_for_enquiry ? 'checked' : '' }}>
                                            </div>
                                        </td> -->
                                         @if($user->role != 'Travel Agent')
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input btn_status" type="checkbox" role="switch" data-type="{{ $detail->pType }}" data-value="{{ $detail->id }}" {{ $detail->status ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                         @endif
                                        @if($user->role == 'Super Admin')
                                        <td>
                                            @if($detail->is_published ==0)
                                                <a class="btn btn-small btn-save btn-primary openpublishModal py-2 px-2" style="font-size: 13px;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#publishModal"
                                                    data-id="{{ $detail->id }}">
                                                    Publish
                                                </a>
                                            @else
                                                <span>Published</span>
                                            @endif
                                        </td>
                                        @endif
                                        
                                        <!-- @if($user->role == 'Super Admin')
                                        <td>
                                            <ul class="actions">
                                                <li class="d-flex align-items-center">
                                                        @if($detail->website_is_published ==0 && $detail->is_published == 0)
                                                        <a class="btn btn-save btn-primary openWebsitepublishModal py-2 px-2" style="font-size: 13px; "
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#websitepublishModal"
                                                            data-id="{{ $detail->id }}">
                                                           Website Publish
                                                        </a>
                                                    @else
                                                        <span>Website Published</span>
                                                    @endif
                                                    
                                                </li>
                                            </ul>
                                        </td>
                                        @endif -->
                                        
                                        
                                        
                                        <td>
                                            <ul class="actions">
                                                <li><a href="{{ route('pms.property.unit.or.multiunit.overview', ['id'=>$detail->id, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="btn btn-link"><i class="icon-edit"></i></a></li>
                                                <li><button type="button" class="btn item_delete" data-value="{{ $detail->id }}" data-type="{{ $detail->pType }}"><i class="icon-delete"></i></button></li>

                                                 

                                                @if($detail->is_published && isPriceLabEnable())
                                                    <!-- Divider -->
                                                    <span class="action-divider"></span>
                                        
                                                    <div class="d-flex flex-column gap-1">
                                                          <!--Get Prices -->
                                                        @if(!$detail->price_lab_sync_date_time)   
                                                            <button type="button btn-primary"
                                                                    class="btn-pill btn-pill-secondary pPriceLab"
                                                                    data-id="{{ $detail->id }}">
                                                                <span class="btn-text">
                                                                   
                                                                    Publish On Pricelab 
                                                                </span>
                                                                <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                                                            </button> 
                                                        @endif    
                                                    @if($detail->price_lab_sync_date_time)   
                                                        
                                                        
                                                        <button type="button"
                                                                class="btn-pill getPrices"
                                                                data-id="{{ $detail->ru_property_id }}">
                                                            <span class="btn-text">
                                                               
                                                                Get PriceLab Prices 
                                                            </span>
                                                            <span class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                                                        </button>
                                                    @endif   
                                                    </div>
                                                @endif




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
    
     <div class="modal fade" id="websitepublishModal" tabindex="-1" aria-labelledby="websitepublishModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg bg-primary text-white">
                    <h5 class="modal-title" id="websitepublishModalLabel">Website Publish Property: <span class="websitepropertyName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Yaha tera form ya status change ka content aayega -->
                    <form id="PayemntRequestFormId">
                        <div class="row p-2 websitepublishModalBody">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    </section>
@endsection
<script>
    let pType = "{{ request()->pType }}";
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

       
       $('.openWebsitepublishModal').on('click', function () {
            let id = $(this).data("id");
            $.ajax({
                url: '{{ url("pms/property/unitormultiunit/website/preview") }}/' + id+'?pType='+pType,
                type: 'GET',
                success: function (response) {
                   $(".websitepropertyName").text(response.detail.unit_name);
                   $(".websitepublishModalBody").empty();
                   $(".websitepublishModalBody").append(response.html);
                },
                error: function () {
                    toastr.error('Something went wrong!', 'Error', { timeOut: 2000 });
                }
            });
        });
       


       $(document).on('click', '.website-publish-property', function () {
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
                    url: '{{ route("pms.property.unit.or.multiunit.website.publish") }}/' + id + '?pType=' + pType,
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.pPriceLab').on('click', function () {
        const btn = $(this);
        const id = btn.data('id');
    
        const text = btn.find('.btn-text');
        const spinner = btn.find('.spinner-border');
    
        btn.prop('disabled', true);
        text.addClass('d-none');
        spinner.removeClass('d-none');
    
        $.ajax({
            url: "{{route('pms.property.unit.or.multiunit.publish.pricelab')}}/"+id,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
    
                
    
                // ✅ Success case
                Swal.fire({
                    title: 'Success',
                    text: 'Property published on Pricelab successfully',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                })
            },
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Server error',
                    'error'
                );
            },
            complete: function () {
                btn.prop('disabled', false);
                text.removeClass('d-none');
                spinner.addClass('d-none');
            }
        });
    });
    $('.getPrices').on('click', function () {
        const btn = $(this);
        const id = btn.data('id');
    
        const text = btn.find('.btn-text');
        const spinner = btn.find('.spinner-border');
    
        btn.prop('disabled', true);
        text.addClass('d-none');
        spinner.removeClass('d-none');
    
        $.ajax({
            url: `/api/property/${id}/get-prices`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
    
                // ✅ IF API says false
                if (response.status === false) {
                    Swal.fire(
                        'Error',
                        response.message || 'Something went wrong!',
                        'error'
                    );
                    return;
                }
    
                // ✅ Success case
                Swal.fire('Success', 'Prices fetched successfully', 'success');
            },
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Server error',
                    'error'
                );
            },
            complete: function () {
                btn.prop('disabled', false);
                text.removeClass('d-none');
                spinner.addClass('d-none');
            }
        });
    });



    
    $('.checkPricelabs').on('click', function () {
        const btn = $(this);
        const id = btn.data('id');
    
        const text = btn.find('.btn-text');
        const spinner = btn.find('.spinner-border');
    
        btn.prop('disabled', true);
        text.addClass('d-none');
        spinner.removeClass('d-none');
    
        $.ajax({
            url: `/api/property/${id}/pricelabs-status`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (response) {
    
                if (response.status === false) {
                    Swal.fire(
                        'Error',
                        response.message || 'Pricelabs not connected!',
                        'error'
                    );
                    return;
                }
    
                Swal.fire('Success', 'Status fetched successfully', 'success');
            },
            error: function () {
                Swal.fire('Error', 'Request failed', 'error');
            },
            complete: function () {
                btn.prop('disabled', false);
                text.removeClass('d-none');
                spinner.addClass('d-none');
            }
        });
    });



});

</script>