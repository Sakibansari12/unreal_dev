@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Guest Database</h1>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.guestdatabase.list') }}" id="searchForm">
                @csrf
                <div class="row mb-3 g-3 align-items-center">
                    <div class="col-sm-6 col-md-3 col-lg">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search_name" value="{{old('search_name', request('search_name') ?? '')}}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg">
                        <div class="input-group input-group-sm">
                            <input type="text" name="property_name" value="{{old('property_name', request('property_name') ?? '')}}" class="form-control" placeholder="Property Name">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg">
                        <div class="input-group input-group-sm">
                            <input type="email" name="search_email" class="form-control" value="{{old('search_email', request('search_email') ?? '')}}" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search_mobile"  maxlength="13" value="{{old('search_mobile', request('search_mobile') ?? '')}}" oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')" class="form-control" placeholder="Mobile">
                        </div>
                    </div>   
                    <div class="col-sm-6 col-md-3 col-lg-auto">
                        <div class="input-group input-group-sm d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>

                            <a href="{{ route('pms.guestdatabase.list') }}" class="btn btn-warning">
                                <span class="material-symbols-outlined">refresh</span>
                            </a>

                            <button type="button" class="btn btn-secondary" id="export">
                                <span class="material-symbols-outlined">file_export</span>
                            </button>
                        </div>
                    </div> 
                    <div class="col-12">
                        <div class="data-info text-primary">
                            {{ $items->total() }} Results found
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive data-table text-nowrap">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Property Name</th>
                            <th>Location</th>
                            <th>Email</th>
                            <th>Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            
                            <td>
                                @if(optional($value->propertyBooking)->pType === 'unit')
                                    {{ optional(optional($value->propertyBooking)->homeUnit)->unit_name ?? '' }}
                                @elseif(optional($value->propertyBooking)->pType === 'multiunit')
                                    {{ optional(optional($value->propertyBooking)->homeMultiUnit)->unit_name ?? '' }}
                                @endif
                            </td>

                            <td>
                                @if($value->propertyBooking?->pType === 'unit')
                                    {{ $value->propertyBooking?->homeUnit?->locationData?->location_name ?? '' }}
                                @elseif($value->propertyBooking?->pType === 'multiunit')
                                    {{ $value->propertyBooking?->homeMultiUnit?->locationData?->location_name ?? '' }}
                                @endif
                            </td>
                            
                            <td>{{ $value->email }}</td>
                            <td>{{ $value->mobile_no }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="5">No Record Found!</td>
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
<script>
    document.getElementById('export').addEventListener('click', function () {
        const form = document.getElementById('searchForm');
        const action = "{{route('pms.guestdatabase.exportToExcel')}}";
        const params = new URLSearchParams(new FormData(form)).toString();
        window.location.href = `${action}?${params}`;
    });
</script>
@endsection