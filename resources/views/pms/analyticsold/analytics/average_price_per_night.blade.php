@extends('pms.layouts.app')
@section('content')
    <style>
        .btn {
            padding: 9px 23px;
        }

       /*  .table-wrap {
            min-height: 300px;
        } */
    </style>



    <section class="section">
        <div class="container-fluid">
            <div class="col-12">
                <div class="row gy-3">
                    <div class="col align-self-end">
                        <h1 class="h2 mb-0">Average Price Per Night</h1>
                    </div>
                </div>
            </div>

            <div class="content-box p-3">
                <div class="page-title">
                    <div class="col-12">
                        <div class="search-filter ">
                            <form method="GET" action="{{ route('pms.analytics.average_price_per_night') }}">
                                <div class="row gy-3 gx-2 justify-content-xl-end">
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
                                            <div class="col-12 col-md-auto">
                                                <div class="btn-group gap-1">
                                                    <button type="submit" class="btn btn-primary btn-icon">
                                                        <span class="bi bi-search"></span>
                                                    </button>
                                                    <a href="{{ route('pms.analytics.average_price_per_night') }}"
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
                            @if (is_array($data) ? count($data) > 0 : $data->isNotEmpty())
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table class="table table-list-2 align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="bg-primary text-white">Property Name</th>
                                                    <th class="bg-primary text-white text-end">Average Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $item)
                                                    <tr>
                                                        <td nowrap>{{ $item->property_name }}</td>
                                                        <td class="text-end">
                                                            <b
                                                                class="text-black">₹{{ number_format($item->average_price_per_night) }}</b>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="py-5 px-3">
                                    No Data Found...!
                                </div>
                            @endif
                        </div>
                    </section>
                    @if (!$isListLoading && $pagination->isNotEmpty())
                        <div class="row justify-content-end align-items-center">
                            <div class="col">
                                <nav aria-label="Page navigation example">

                                    {{ $pagination->links('pagination::bootstrap-4') }}
                                </nav>
                            </div>

                            <div class="col-auto">
                                <h4 class="text-primary m-0">
                                    <span class="text-dark">Average Price:</span>
                                    <b>₹{{ number_format($total_average_price, 2) }}</b>
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
        document.addEventListener('DOMContentLoaded', function() {
            const propertySelect = document.getElementById('propertySelect');
            const pTypeInput = document.getElementById('pTypeInput');
            if (propertySelect) {
                propertySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const pType = selectedOption.getAttribute('data-ptype') || '';
                    if (pTypeInput) {
                        pTypeInput.value = pType;
                    }
                });
            }

            const daysSelect = document.getElementById('daysSelect');
            if (daysSelect) {
                daysSelect.addEventListener('change', function() {
                    const selectedDays = this.value;
                    const baseUrl = window.location.origin + window.location.pathname;
                    window.location.href = baseUrl + '?days=' + encodeURIComponent(selectedDays);
                });
            }
        });
    </script>
@endsection
