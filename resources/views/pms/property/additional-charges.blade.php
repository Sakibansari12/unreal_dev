@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $id ? 'Update' : 'Add' }} {{ ucfirst(request()->pType) }} (Property: {{ $parentHome->unit_name }})</h1>
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

                    @media (max-width: 991.98px) {

                        .ulTab li button,
                        .ulTab li a {
                            white-space: nowrap;
                        }
                    }

                    .ulTab {
                        display: block !important;
                    }

                    .is-invalid {
                        border: 2px solid red !important;
                    }
                </style>
                @include('pms.property.unit-or-multiunit-menu-segments')
                <div class="col-12 col-lg-9">
                    <form action="{{ route('pms.property.unit.or.multiunit.additionalcharges.save') }}" method="POST" id="chargesForm">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-list mb-0 mw-lg" id="chargesTable">
                                    <thead>
                                        <tr>
                                            <th class="fw-semibold">Name</th>
                                            <th class="fw-semibold">Price</th>
                                            <th class="fw-semibold">Type</th>
                                            <th class="fw-semibold" width="10%">Display on Website</th>
                                            <th class="text-secondary text-center fw-semibold" style="width: 90px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $chargesData = old('charges', $charges ?? []);
                                        @endphp
                                        @foreach($chargesData as $index => $charge)
                                        <tr>
                                            <td>
                                                <input type="text" name="charges[{{ $index }}][name]" class="form-control @error('charges.' . $index . '.name') is-invalid @enderror" value="{{ old('charges.' . $index . '.name', $charge['name'] ?? '') }}">
                                            </td>
                                            <td>
                                                <input type="number" name="charges[{{ $index }}][price]" class="form-control @error('charges.' . $index . '.price') is-invalid @enderror" value="{{ old('charges.' . $index . '.price', $charge['price'] ?? '') }}">
                                            </td>
                                            <td>
                                                <select name="charges[{{ $index }}][type]" class="form-control mw-150">
                                                    <option disabled value="">Type</option>
                                                    <option value="Per_Night" {{ old('charges.' . $index . '.type', $charge['type'] ?? '') == 'Per_Night' ? 'selected' : '' }}>Per Night</option>
                                                    <option value="Per_Stay" {{ old('charges.' . $index . '.type', $charge['type'] ?? '') == 'Per_Stay' ? 'selected' : '' }}>Per Stay</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="charges[{{ $index }}][display]" class="form-check-input" {{ old('charges.' . $index . '.display', $charge['display'] ?? '') == 1 ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-small remove-row">
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-footer pt-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-save btn-secondary btn-small" id="addRowBtn">
                                            <i class="bi bi-plus-lg me-2"></i>Add More
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-footer pt-4">
                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-save btn-primary">
                                            <span>SUBMIT</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowIndex = {{count(old('charges', $charges ?? []))}};
        const tableBody = document.querySelector('#chargesTable tbody');
        const addRowBtn = document.getElementById('addRowBtn');

        addRowBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
            <td><input type="text" name="charges[${rowIndex}][name]" class="form-control"></td>
            <td><input type="number" name="charges[${rowIndex}][price]" class="form-control"></td>
            <td>
                <select name="charges[${rowIndex}][type]" class="form-control mw-150">
                    <option disabled selected value="">Type</option>
                    <option value="Per_Night">Per Night</option>
                    <option value="Per_Stay">Per Stay</option>
                </select>
            </td>
            <td class="text-center">
                <input type="checkbox" name="charges[${rowIndex}][display]" class="form-check-input">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-small remove-row">Remove</button>
            </td>
        `;
            tableBody.appendChild(newRow);
            rowIndex++;
        });

        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (tableBody.rows.length === 1) {
                    alert('At least one charge row is required.');
                    return;
                }
                e.target.closest('tr').remove();
            }
        });
    });
</script>