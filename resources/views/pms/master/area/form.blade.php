@extends('pms.layouts.app')
@section('content')
<style>
    .upload-wrapper {
        border: 2px dashed #C7C7C7;
        border-radius: 5px;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 15px;
        user-select: none;
        background-color: #fff;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
    }

    .upload-wrapper input[type=file] {
        position: absolute;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .upload-wrapper.is-invalid {
        border-color: #dc3545;
    }

    .upload-info {
        z-index: 1;
        pointer-events: none;
    }

    /* Image preview CSS without cropping: */
    .imagePreview {
        position: relative;
        width: 100%;
        height: 300px;
        overflow: hidden;
    }

    .imagePreview img {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        object-fit: contain;
        transform: translate(-50%, -50%);
    }

    .remove-preview {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 18px;
        line-height: 30px;
        text-align: center;
        cursor: pointer;
        z-index: 3;
    }
</style>
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">@if($detail) Update @else Add @endif Area</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.area.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to list</span>
                    </a>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('pms.area.save') }}" id="submittags" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $detail->id ?? '' }}">
            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label>State<sup>*</sup></label>
                                <select name="state_id" id="state_id"
                                    class="form-control @error('state_id') is-invalid @enderror">
                                    <option value="">Select State</option>
                                    @foreach($states as $value)
                                    <option value="{{ $value->id }}"
                                        {{ old('state_id', $detail->state_id ?? '') == $value->id ? 'selected' : '' }}>
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-field">
                                <label>Location<sup>*</sup></label>
                                <select name="location_id" id="location_id"
                                    class="form-control @error('location_id') is-invalid @enderror">
                                    <option value="">Select Location</option>

                                    {{-- Edit case ke liye --}}
                                    @if(!empty($locations))
                                    @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ old('location_id', $detail->location_id ?? '') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->location_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="form-field">
                                <label for="state">Area Name<sup>*</sup></label>
                                <input type="text" name="area_name" id="area_name" value="{{ old('area_name', $detail->area_name ?? '') }}" class="form-control @error('area_name') border-danger @enderror">
                            </div>
                        </div>
                    </div>


                </div>

            </div>

            <div class="btn-wrap pt-2">
                <button class="btn btn-primary px-5" id="submitButton">@if($detail) UPDATE @else SUBMIT @endif</button>
            </div>

        </form>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const stateSelect = document.getElementById('state_id');
        const locationSelect = document.getElementById('location_id');

        stateSelect.addEventListener('change', function() {
            let stateId = this.value;
            locationSelect.innerHTML = '<option value="">Loading...</option>';

            if (!stateId) {
                locationSelect.innerHTML = '<option value="">Select Location</option>';
                return;
            }

            fetch("{{ url('pms/get-locations') }}/" + stateId)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Select Location</option>';
                    data.forEach(item => {
                        options += `<option value="${item.id}">${item.location_name}</option>`;
                    });
                    locationSelect.innerHTML = options;
                });
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const stateSelect = document.getElementById('state_id');
        const locationSelect = document.getElementById('location_id');

        function loadLocations(stateId, selectedLocation = null) {
            if (!stateId) {
                locationSelect.innerHTML = '<option value="">Select Location</option>';
                return;
            }

            fetch("{{ url('pms/get-locations') }}/" + stateId)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">Select Location</option>';
                    data.forEach(item => {
                        let selected = selectedLocation == item.id ? 'selected' : '';
                        options += `<option value="${item.id}" ${selected}>${item.location_name}</option>`;
                    });
                    locationSelect.innerHTML = options;
                });
        }

        // 🔹 On change
        stateSelect.addEventListener('change', function() {
            loadLocations(this.value, null);
        });

        // 🔹 On page reload after validation error
        @if(old('state_id'))
        loadLocations(
            "{{ old('state_id') }}",
            "{{ old('location_id') }}"
        );
        @endif

    });
</script>


@endsection