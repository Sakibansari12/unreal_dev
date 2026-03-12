@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $id?'Update':'Add' }} {{ ucfirst(request()->pType) }}(Property: {{ $parentHome->unit_name }})</h1>
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
                    .ulTab {
                        list-style-type: none;
                        margin: 0;
                        padding: 0;
                        overflow-x: auto;
                    }
                    @media (max-width: 991.98px) {
                        .ulTab {
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex;
                        }
                    }
                    .ulTab li {
                        margin: 10px 5px;
                    }
                    .ulTab li button,
                    .ulTab li a {
                        border: 0px;
                        padding: 10px 15px;
                        background-color: #fff;
                        width: 100%;
                        border-radius: 6px !important;
                        text-align: left;
                        border: 1px solid #0E0E0E;
                        display: block;
                        text-decoration: none;
                    }
                    .ulTab li button.active,
                    .ulTab li a.active {
                        border: 0px;
                        padding: 10px 15px;
                        color: #fff;
                        background-color: #0e0e0e;
                    }
                    .ulTab li button[disabled],
                    .ulTab li a[disabled] {
                        opacity: 1;
                        color: #000;
                    }
                    .table>:not(caption)>*>*{
                        border-color: transparent;
                    }
                    @media (max-width: 991.98px) {
                        .ulTab li button,
                        .ulTab li a {
                            white-space: nowrap;
                        }
                    }
                    .ulTab {
                        display: block !important;
                    }

                    /* Custom styling for the form */
                    .form-field {
                        margin-bottom: 1rem;
                    }
                    .form-field label {
                        font-weight: 500;
                        margin-bottom: 0.5rem;
                        display: block;
                    }
                    .form-control {
                        border: 1px solid #d1d3e2;
                        border-radius: 4px;
                        padding: 0.375rem 0.75rem;
                    }
                    .border-danger {
                        border-color: #dc3545 !important;
                    }
                    .text-danger {
                        color: #dc3545 !important;
                    }

                    /* Custom checkbox styling */
                    .checkbox-group {
                        border: 1px solid #d1d3e2;
                        border-radius: 4px;
                        padding: 10px;
                        background-color: #fff;
                        max-height: 150px;
                        overflow-y: auto;
                    }
                    .checkbox-group .form-check {
                        padding-left: 1.5rem;
                        margin-bottom: 0rem;
                        display: flex;
                        align-items: center
                    }
                    .checkbox-group .form-check-input {
                        margin-left: -1.5rem;
                        margin-top: 0.25rem;
                    }
                    .checkbox-group .form-check-label {
                        margin-left: 0.5rem;
                    }
                </style>
                @include('pms.property.unit-or-multiunit-menu-segments')
                <div class="col-12 col-lg-9">
                    <form method="POST" action="{{ route('pms.property.unit.or.multiunit.floor.save') }}" id="amenities-form">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="form-field col-lg-4 mb-3">
                            <label for="floor">OTA Floor<span class="text-danger">*</span></label>
                            <select name="floor" id="floor" class="form-control @error('floor') border-danger @enderror">
                                <option value="" >Select Floors</option>
                               
                                @foreach($ruFloorList as $obj)
                                    <option value="{{ $obj->floor_id }}" {{ old('floor', $floorSelectedDetail->floor_id ?? '') == $obj->floor_id ? 'selected' : '' }}>
                                        {{ $obj->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('floor')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-list mb-0 mw-lg">
                                    <thead>
                                        <tr>
                                            <th class="fw-semibold" colspan="2">OTA Room-Specific Amenities<span class="text-danger">*</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bedroomCount as $idx => $bedroom)
                                            <tr>
                                                <td>Bedroom {{ $idx + 1 }}</td>
                                                <td width="60%">
                                                    <div class="form-field mb-0">
                                                        <div class="checkbox-group">
                                                            @foreach($bedRoomAmenityList as $amenity)
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                           name="bedroomAmmenity[{{ $idx }}][]"
                                                                           value="{{ $amenity->amenities_id }}"
                                                                           id="bedroomAmenity_{{ $idx }}_{{ $amenity->amenities_id }}"
                                                                           class="form-check-input"
                                                                           {{ in_array($amenity->amenities_id, $bedroom['bedRoomSelectedAmenities']) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="bedroomAmenity_{{ $idx }}_{{ $amenity->amenities_id }}">
                                                                        {{ $amenity->amenities_name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @error('bedroomAmmenity.' . $idx)
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @foreach($bathroomCount as $idbx => $bathroom)
                                            <tr>
                                                <td>Bathroom {{ $idbx + 1 }}</td>
                                                <td width="60%">
                                                    <div class="form-field mb-0">
                                                        <div class="checkbox-group">
                                                            @foreach($bathRoomAmenityList as $amenity)
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                           name="bathroomAmmenity[{{ $idbx }}][]"
                                                                           value="{{ $amenity->amenities_id }}"
                                                                           id="bathroomAmenity_{{ $idbx }}_{{ $amenity->amenities_id }}"
                                                                           class="form-check-input"
                                                                           {{ in_array($amenity->amenities_id, $bathroom['bathRoomSelectedAmenities']) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="bathroomAmenity_{{ $idbx }}_{{ $amenity->amenities_id }}">
                                                                        {{ $amenity->amenities_name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @error('bathroomAmmenity.' . $idbx)
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td>WC</td>
                                            <td width="60%">
                                                <div class="form-field mb-0">
                                                    <div class="checkbox-group">
                                                        @foreach($wcAmmenityList as $amenity)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="wcAmmenities[]"
                                                                       value="{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       id="wcAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       class="form-check-input"
                                                                       {{ in_array($amenity['tbl_ru_specific_room_ammenity_id'], $wcAmmenityListModel) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="wcAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}">
                                                                    {{ $amenity['ru_room_ammenity_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('wcAmmenities')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Kitchen in the living / dining room</td>
                                            <td width="60%">
                                                <div class="form-field mb-0">
                                                    <div class="checkbox-group">
                                                        @foreach($kitchenAndLivingRoomAmmenities as $amenity)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="kitchenAndLivingRoomAmmenities[]"
                                                                       value="{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       id="kitchenLivingAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       class="form-check-input"
                                                                       {{ in_array($amenity['tbl_ru_specific_room_ammenity_id'], $kitchenAndLivingRoomAmmenitiesModel) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="kitchenLivingAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}">
                                                                    {{ $amenity['ru_room_ammenity_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('kitchenAndLivingRoomAmmenities')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Kitchen</td>
                                            <td width="60%">
                                                <div class="form-field mb-0">
                                                    <div class="checkbox-group">
                                                        @foreach($kitchenRoomAmmenities as $amenity)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="kitchenRoomAmmenities[]"
                                                                       value="{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       id="kitchenAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       class="form-check-input"
                                                                       {{ in_array($amenity['tbl_ru_specific_room_ammenity_id'], $kitchenRoomAmmenitiesModel) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="kitchenAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}">
                                                                    {{ $amenity['ru_room_ammenity_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('kitchenRoomAmmenities')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Living room</td>
                                            <td width="60%">
                                                <div class="form-field mb-0">
                                                    <div class="checkbox-group">
                                                        @foreach($livingRoomAmmenities as $amenity)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="livingRoomAmmenities[]"
                                                                       value="{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       id="livingRoomAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       class="form-check-input"
                                                                       {{ in_array($amenity['tbl_ru_specific_room_ammenity_id'], $livingRoomAmmenitiesModel) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="livingRoomAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}">
                                                                    {{ $amenity['ru_room_ammenity_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('livingRoomAmmenities')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Livingroom / Bedroom</td>
                                            <td width="60%">
                                                <div class="form-field mb-0">
                                                    <div class="checkbox-group">
                                                        @foreach($livingBedRoomAmmenities as $amenity)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="livingBedRoomAmmenities[]"
                                                                       value="{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       id="livingBedRoomAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       class="form-check-input"
                                                                       {{ in_array($amenity['tbl_ru_specific_room_ammenity_id'], $livingBedRoomAmmenitiesModel) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="livingBedRoomAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}">
                                                                    {{ $amenity['ru_room_ammenity_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('livingBedRoomAmmenities')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Bedroom/Living room with kitchen corner</td>
                                            <td width="60%">
                                                <div class="form-field mb-0">
                                                    <div class="checkbox-group">
                                                        @foreach($livingBedRoomKitchenAmmenities as $amenity)
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       name="livingBedRoomKitchenAmmenities[]"
                                                                       value="{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       id="livingBedRoomKitchenAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}"
                                                                       class="form-check-input"
                                                                       {{ in_array($amenity['tbl_ru_specific_room_ammenity_id'], $livingBedRoomKitchenAmmenitiesModel) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="livingBedRoomKitchenAmenity_{{ $amenity['tbl_ru_specific_room_ammenity_id'] }}">
                                                                    {{ $amenity['ru_room_ammenity_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @error('livingBedRoomKitchenAmmenities')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="table-footer pt-4">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <!-- <button type="submit" class="btn btn-save btn-primary">
                                        SUBMIT
                                    </button> -->
                                    <button type="submit" id="submitBtn" class="btn btn-save btn-primary mt-3">
                                        <span id="btnText">SUBMIT</span>
                                        <span id="btnLoader"
                                            class="spinner-border spinner-border-sm ms-2 d-none"
                                            role="status"
                                            aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection