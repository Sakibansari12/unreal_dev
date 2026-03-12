@extends('pms.layouts.app')
@section('content') <!-- Assuming you have a layout file -->
<style>
    .btn{
            padding: 9px 23px;
        }
</style>

    <section class="section">
        <div class="container-fluid">


            <div class="col-12">
                <div class="row gy-3">
                    <div class="col align-self-end">
                        <h1 class="h2 mb-0">Bookings Created</h1>
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
                <div class="page-title">

                    <div class="col-12">
                        <div class="search-filter">
                            <form method="GET" action="{{ route('pms.analytics.bookings_created') }}">
                                <div class="row gy-3 gx-2">
                                    <div class="col-auto position-relative">
                                        <div class="row justify-content-xl-end gy-3 gx-2">
                                            <div class="col-12 col-lg col-xl-auto">
                                                <div class="form-group mb-0">
                                                    <select class="form-control" name="property_id" id="propertySelect"
                                                        style="min-width:250px;">
                                                        <option value=""
                                                            {{ !request('property_id') ? 'selected' : '' }} disabled>
                                                            Property Name</option>
                                                        @foreach ($properties as $property)
                                                            <option value="{{ $property->id }}"
                                                                data-ptype="{{ $property->pType }}"
                                                                {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                                                {{ $property->property_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <!-- Hidden input to carry selected pType -->
                                                    <input type="hidden" name="pType" id="pTypeInput"
                                                        value="{{ request('pType') }}">

                                                </div>
                                            </div>

                                            <div class="col-6 col-lg col-xl-auto">
                                                <div class="form-group mb-0">
                                                    <select class="form-control" name="location_id">
                                                        <option value=""
                                                            {{ !request('location_id') ? 'selected' : '' }} disabled>
                                                            Location</option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->location_id }}"
                                                                {{ request('location_id') == $location->location_id ? 'selected' : '' }}>
                                                                {{ $location->location_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6 col-lg col-xl-auto">
                                                <div class="form-group mb-0">
                                                    <select class="form-control" name="channel">
                                                        <option value="" {{ !request('channel') ? 'selected' : '' }}
                                                            disabled>Channel</option>
                                                        @foreach ($channels as $channel)
                                                            <option value="{{ $channel }}"
                                                                {{ request('channel') == $channel ? 'selected' : '' }}>
                                                                {{ $channel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- <div class="col-12 col-lg col-xl-auto">
                                                <div class="row gy-3 gx-2">
                                                    <div class="col">
                                                        <input  class="form-control flatpickr" name="date_range"
                                                                 placeholder="From - To" id="dateRangePicker"
                                                                  value="{{ request('date_range') }}"
                                                                 />
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <div class="col-12 col-md-auto">
                                                <div class="btn-group gap-1">
                                                    <button type="submit" class="btn btn-primary btn-icon">
                                                        <span class="bi bi-search"></span>
                                                    </button>
                                                    <a href="{{ route('pms.analytics.bookings_created', ['days' => request('days')]) }}"
                                                        class="btn btn-icon btn-clear btn-warning">
                                                        <span class="bi bi-arrow-clockwise"></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto ms-xxl-auto">
                                        <select class="form-control" name="days" onchange="this.form.submit()" id="daysSelect">
                                            <option value="" disabled {{ !request('days') ? 'selected' : '' }}>Select
                                                days</option>
                                            <option value="7_next" {{ request('days') == '7_next' ? 'selected' : '' }}>Next
                                                7 days</option>
                                            <option value="7" {{ request('days') == '7' ? 'selected' : '' }}>Last 7
                                                days</option>
                                            <option value="30" {{ request('days') == '30' ? 'selected' : '' }}>Last 30
                                                days</option>
                                            <option value="60" {{ request('days') == '60' ? 'selected' : '' }}>Last 60
                                                days</option>
                                            <option value="90" {{ request('days') == '90' ? 'selected' : '' }}>Last 90
                                                days</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <section class="section mt-3">
                        <div class="outer-wrapper">
                           
                            @if (is_array($dataList) ? count($dataList) > 0 : $dataList->isNotEmpty())
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table class="table table-list-2 align-middle mb-0">
                                            <thead>
                                                 <tr>
                                                    <th class="bg-primary text-white">Location</th>
                                                    <th class="bg-primary text-white">Property Name</th>
                                                    <th class="bg-primary text-white">Channel</th>
                                                    <th class="bg-primary text-white text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataList as $data)
                                                    <tr>
                                                        <td nowrap>{{ $data->location_name }}</td>
                                                        <td nowrap>{{ $data->property_name }}</td>
                                                        <td>{{ $data->channel }}</td>


                                                        <td class="text-end">
                                                            <b
                                                                class="text-black">₹{{ number_format($data->total_amount, 2) }}</b>
                                                        </td>

                                                        
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex justify-content-center align-items-center">
                                    <strong>No Data Found...!</strong>
                                </div>
                            @endif
                        </div>
                    </section>

                    @if (!$isListLoading && $dataList->isNotEmpty())
                        <div class="row justify-content-end align-items-center">
                            <div class="col">
                                <nav aria-label="Page navigation example">

                                    {{ $dataList->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>

                            <div class="col-auto">
                                <h4 class="text-primary m-0">
                                    <span class="text-dark">Grand Total:</span>
                                    <b>₹{{ number_format($grand_total, 2) }}</b>
                                </h4>
                            </div>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('dateRangePicker');

    flatpickr(input, {
        mode: "range",
        dateFormat: "d/m/Y",
        defaultDate: getDefaultDates(input.value),
    });

    function getDefaultDates(value) {
        if (!value || !value.includes(' to ')) return null;
        const [start, end] = value.split(' to ');
        return [start.trim(), end.trim()];
    }
});
        document.addEventListener('DOMContentLoaded', function() {
            const propertySelect = document.getElementById('propertySelect');
            const pTypeInput = document.getElementById('pTypeInput');
            if (propertySelect) {
                propertySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const pType = selectedOption.getAttribute('data-ptype') || '';
                    console.log("Selected pType:", pType);
                    if (pTypeInput) {
                        pTypeInput.value = pType;
                    }
                });
            }
        });

        document.getElementById('daysSelect').addEventListener('change', function() {
    const selectedDays = this.value;

    // Build base URL without query params
    const baseUrl = window.location.origin + window.location.pathname;

    // Redirect with only ?days=selectedDays param
    window.location.href = baseUrl + '?days=' + encodeURIComponent(selectedDays);
});
    </script>
@endsection