@extends('pms.layouts.app')
@section('content')

<style>
    .drag-el {
        cursor: move;
    }
</style>
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $id?'Update':'Add' }} {{ ucfirst(request()->pType) }} (Property: {{ $parentHome->unit_name ?? '' }})</h1>
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

                    <a href="{{ $route }}" class="btn d-flex btn-small rounded-2 btn-secondary">
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
                        overflow-x: auto
                    }

                    @media (max-width: 991.98px) {
                        .ulTab {
                            display: -webkit-box;
                            display: -ms-flexbox;
                            display: flex
                        }
                    }

                    .ulTab li {
                        margin: 10px 5px
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
                        text-decoration: none
                    }

                    .ulTab li button.active,
                    .ulTab li a.active {
                        border: 0px;
                        padding: 10px 15px;
                        color: #fff;
                        background-color: #0e0e0e
                    }

                    .ulTab li button[disabled],
                    .ulTab li a[disabled] {
                        opacity: 1;
                        color: #000
                    }

                    @media (max-width: 991.98px) {

                        .ulTab li button,
                        .ulTab li a {
                            white-space: nowrap
                        }
                    }

                    .ulTab {
                        display: block !important;
                    }
                </style>

                @include('pms.property.unit-or-multiunit-menu-segments')
                <div class="col-12 col-lg-9">
                    <form method="POST" action="{{ route('pms.property.unit.or.multiunit.website.amenities.save') }}">
                        @csrf

                        <input type="hidden" name="pType" value="{{ request()->pType }}">
                        <input type="hidden" name="home_id" value="{{ request()->id }}">

                        @if(count($allData))
                        <div class="row g-3 sortable-container">
                            @php $amenityIndex = 0; @endphp
                            @foreach($allData as $index => $item)
                            <div class="col-12 col-md-6 col-lg-6 col-xxl-3 drag-el">
                                <div class="form-group">
                                    <div class="form-check form-check-lg form-check-box {{ $item['isChecked'] ? 'border-primary' : '' }}">
                                        <div class="row gx-0 gy-3 align-items-center">
                                            <div class="col-auto">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    name="amenities[{{ $amenityIndex }}][isChecked]"
                                                    id="amenity_{{ $item['id'] }}"
                                                    value="1"
                                                    {{ $item['isChecked'] ? 'checked' : '' }}>
                                            </div>
                                            <div class="col">
                                                <img
                                                    src="{{ asset('storage/amenities/' . $item['amenities_image']) }}"
                                                    alt="{{ $item['amenities_name'] }}"
                                                    height="20">&nbsp;
                                                <label for="amenity_{{ $item['id'] }}">{{ $item['amenities_name'] }}</label>
                                            </div>

                                            <!-- HIDDEN FIELDS -->
                                            <input type="hidden" name="amenities[{{ $amenityIndex }}][amenities_id]" value="{{ $item['id'] }}">
                                            <input type="hidden" name="amenities[{{ $amenityIndex }}][amenities_name]" value="{{ $item['amenities_name'] }}">
                                            <input type="hidden" class="position-input" name="amenities[{{ $amenityIndex }}][position]" value="{{ $index }}">

                                            <div class="col-12 d-none">
                                                <input
                                                    type="number"
                                                    name="amenities[{{ $amenityIndex }}][amenities_number]"
                                                    class="form-control"
                                                    value="{{ $item['number'] ?? '' }}"
                                                    {{ !$item['isChecked'] ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php $amenityIndex++; @endphp
                            @endforeach

                        </div>

                        <div class="row pt-3">
                            <div class="col-12">
                                <!-- <button type="submit" class="btn btn-primary">
                                    SUBMIT
                                </button> -->
                                <button type="submit" id="submitBtn" class="btn btn-primary">
                                    <span id="btnText">SUBMIT</span>
                                    <span id="btnLoader"
                                        class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"
                                        aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        @else
                        <div class="text-center">
                            <h6 class="fw-bold">
                                No record found

                            </h6>
                        </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.sortable-container');

        if (container) {
            new Sortable(container, {
                animation: 150,
                onEnd: function() {
                    document.querySelectorAll('.drag-el').forEach((el, index) => {
                        const input = el.querySelector('.position-input');
                        if (input) input.value = index;
                    });
                }
            });
        }
    });
</script>

@endsection