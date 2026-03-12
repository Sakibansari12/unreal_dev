@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail) ? 'Edit' : 'Add' }} Property
                        @if(isset($detail) && !empty($detail->property_manager_name))
                        (Property Manager: {{ $detail->property_manager_name }})
                        @endif
                    </h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.property.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('pms.property.save') }}" method="POST">
            @csrf
            @if(isset($detail))
            <input type="hidden" name="id" value="{{ $detail->id }}">
            @endif

            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        {{-- Property Name --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="home_name">Property Name<sup>*</sup></label>
                                <input type="text" class="form-control @error('home_name') is-invalid @enderror"
                                    name="home_name"
                                    value="{{ old('home_name', $detail->home_name ?? '') }}">

                            </div>
                        </div>

                        {{-- Property Type --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="home_type_id">Property Type<sup>*</sup></label>
                                <select name="home_type_id" class="form-select @error('home_type_id') is-invalid @enderror">
                                    <option value="">Select Property Type</option>
                                    @foreach($homeTypes as $homeType)
                                    <option value="{{ $homeType->id }}"
                                        {{ old('home_type_id', $detail->home_type_id ?? '') == $homeType->id ? 'selected' : '' }}>
                                        {{ $homeType->name }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        {{-- State --}}
                        <div class="col-md-4">
                            <div class="form-field">
                                <label for="state_id">State<sup>*</sup></label>
                                <select name="state_id" id="state_id" class="form-select @error('state_id') is-invalid @enderror">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('state_id', $detail->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="col-md-4">
                            <div class="form-field">
                                <label for="location_id">Location<sup>*</sup></label>
                                <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror">
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}"
                                        {{ old('location_id', $detail->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                        {{ $location->location_name }}
                                    </option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        {{-- Area --}}
                        <div class="col-4">
                            <div class="form-field">
                                <label>Area<sup>*</sup></label>
                                <select name="area_id" id="area_id"
                                    class="form-control @error('area_id') is-invalid @enderror">
                                    <option value="">Select Area</option>

                                    {{-- Edit case --}}
                                    @if(!empty($areas))
                                    @foreach($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ old('area_id', $detail->area_id ?? '') == $area->id ? 'selected' : '' }}>
                                        {{ $area->area_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- Postal Code --}}
                        <div class="col-md-4">
                            <div class="form-field">
                                <label for="postal_code">Postal Code<sup>*</sup></label>
                                <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                    value="{{ old('postal_code', $detail->postal_code ?? '') }}">

                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-field">
                                <label for="latitude">Latitude<sup>*</sup></label>
                                <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $detail->map_latitude ?? '') }}">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-field">
                                <label for="longitude">Longitude<sup>*</sup></label>
                                <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $detail->map_longitude ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-field">
                                <label for="map_blurb">Map Blurb<sup>*</sup></label>
                                <input type="text" name="map_blurb" class="form-control @error('map_blurb') is-invalid @enderror" value="{{ old('map_blurb', $detail->map_text ?? '') }}">
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="address">Address<sup>*</sup></label>
                                <textarea name="address" rows="5" class="form-control @error('address') is-invalid @enderror">{{ old('address', $detail->address ?? '') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label for="description">Description<sup>*</sup></label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $detail->description ?? '') }}</textarea>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-wrap pt-2">
                <button type="submit" class="btn btn-primary px-5">SUBMIT</button>
            </div>
        </form>
    </div>
</section>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    const stateSelect    = document.getElementById('state_id');
    const locationSelect = document.getElementById('location_id');
    const areaSelect     = document.getElementById('area_id');
   const selectedLocationId = "{{ old('location_id', $detail->location_id ?? '') }}";
   const selectedAreaId = "{{ old('area_id', $detail->area_id ?? '') }}";
    /* -------------------------
       FETCH LOCATIONS
    -------------------------- */
    function fetchLocations(stateId) {
        const basePath = "{{ url('/pms/property/get/locations/by/state') }}";

        fetch(`${basePath}/${stateId}`)
            .then(res => res.json())
            .then(data => {
                locationSelect.innerHTML = '<option value="">Select Location</option>';

                data.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc.id;
                    opt.textContent = loc.location_name;

                    if (selectedLocationId && selectedLocationId == loc.id) {
                        opt.selected = true;
                    }

                    locationSelect.appendChild(opt);
                });

                // 🔥 IMPORTANT: location set hone ke baad area load karo
                if (locationSelect.value) {
                    fetchAreas(locationSelect.value);
                }
            });
    }

    /* -------------------------
       FETCH AREAS
    -------------------------- */
    function fetchAreas(locationId) {
        areaSelect.innerHTML = '<option value="">Loading...</option>';

        fetch("{{ url('pms/get-areas') }}/" + locationId)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Select Area</option>';

                data.forEach(area => {
                    let selected = '';
                    if (selectedAreaId && selectedAreaId == area.id) {
                        selected = 'selected';
                    }
                    options += `<option value="${area.id}" ${selected}>${area.area_name}</option>`;
                });

                areaSelect.innerHTML = options;
            });
    }

    /* -------------------------
       EVENTS
    -------------------------- */
    stateSelect.addEventListener('change', function () {
        locationSelect.innerHTML = '<option value="">Select Location</option>';
        areaSelect.innerHTML = '<option value="">Select Area</option>';

        if (this.value) {
            fetchLocations(this.value);
        }
    });

    locationSelect.addEventListener('change', function () {
        areaSelect.innerHTML = '<option value="">Select Area</option>';

        if (this.value) {
            fetchAreas(this.value);
        }
    });

    /* -------------------------
       PAGE LOAD (VALIDATION / EDIT)
    -------------------------- */
    if (stateSelect.value) {
        fetchLocations(stateSelect.value);
    }

});
</script>

