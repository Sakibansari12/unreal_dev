@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $id?'Update':'Add' }} {{ ucfirst(request()->pType) }} (Property: {{ $parentHome->unit_name }})</h1>
                </div>
                <div class="col-auto">
                     @php
                        $route = request()->type === 'published'
                            ? route('pms.published.property.list', ['pType' => 'unit'])
                            : route('pms.property.unit.or.multiunit.list', [
                                'property_id' => request()->property_id,
                                'pType' => request()->pType,
                            ]);
                    @endphp
                    
                    <a href="{{ $route }}"  class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="content-box p-3">
            <div class="row g-5">
                <style>
                    .ulTab{list-style-type:none;margin:0;padding:0;overflow-x:auto}@media (max-width: 991.98px){.ulTab{display:-webkit-box;display:-ms-flexbox;display:flex}}.ulTab li{margin:10px 5px}.ulTab li button,.ulTab li a{border:0px;padding:10px 15px;background-color:#fff;width:100%;border-radius:6px!important;text-align:left;border:1px solid #0E0E0E;display:block;text-decoration:none}.ulTab li button.active,.ulTab li a.active{border:0px;padding:10px 15px;color:#fff;background-color:#0e0e0e}.ulTab li button[disabled],.ulTab li a[disabled]{opacity:1;color:#000}@media (max-width: 991.98px){.ulTab li button,.ulTab li a{white-space:nowrap}}
                    .ulTab {
                        display: block !important;
                    }
                </style>
                @include('pms.property.unit-or-multiunit-menu-segments')
                {{-- amenities section --}}
                <div class="col-12 col-lg-9">
                    <form action="{{ route('pms.property.unit.or.multiunit.amenities.save') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        @foreach($amenitiesList as $value)
                            <h4 class="bg-dark px-3 py-2 text-white mb-3 rounded-2">
                                {{ $value['type'] != '' ? $value['type'] : 'General' }}
                            </h4>
                            <div class="row g-2 pb-2">
                                @foreach($value['ammenites'] as $amenity)
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <div class="form-field mb-2">
                                            <div class="form-check form-check-lg w-auto h-auto">
                                                <input
                                                    type="checkbox"
                                                    name="selectedAmenities[]"
                                                    class="form-check-input"
                                                    value="{{ $amenity['amenities_id'] }}"
                                                    {{ $amenity['is_checked'] == true ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label">{{ $amenity['amenities_name'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <div class="btn-wrap pt-2">
                            <!-- <button type="submit" class="btn btn-primary px-5">SUBMIT</button> -->
                             <button type="submit" id="submitBtn" class="btn btn-primary">
                                    <span id="btnText">SUBMIT</span>
                                    <span id="btnLoader"
                                        class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"
                                        aria-hidden="true"></span>
                                </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection