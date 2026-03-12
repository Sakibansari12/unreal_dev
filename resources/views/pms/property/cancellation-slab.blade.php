
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
                    .is-invalid {
                        border-color: #dc3545 !important;
                    }
                    .table-list td {
                        vertical-align: middle;
                    }
                    .actions {
                        list-style: none;
                        padding: 0;
                        display: flex;
                        gap: 10px;
                    }
                    .mw-lg {
                        max-width: 800px;
                    }
                </style>
                @include('pms.property.unit-or-multiunit-menu-segments')
                {{-- cancellation slab section --}}
                <div class="col-12 col-lg-9">
                    <form method="POST" action="{{ route('pms.property.unit.or.multiunit.cancellationslab.save') }}" id="cancellation-slab-form">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-list mb-0 mw-lg" id="slab-table">
                                    <thead>
                                        <tr>
                                            <th class="fw-semibold">From</th>
                                            <th class="fw-semibold">To</th>
                                            <th class="fw-semibold" width="10%">Slab(%)</th>
                                            <th style="width:90px;" class="text-center fw-semibold">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="slab-rows">
                                        @php
                                            $slabs = old('slab', $cancellation_slab ?? [['slab_from' => '', 'slab_to' => '', 'slab' => 0]]);
                                        @endphp
                                        @if (count($slabs) > 0)
                                            @foreach ($slabs as $index => $slab)
                                                <tr class="slab-row">
                                                    <td>
                                                        <div class="form-field mb-0">
                                                            <input
                                                                type="number"
                                                                class="form-control slab-from"
                                                                name="slab[{{$index}}][slab_from]"
                                                                value="{{ old('slab.'.$index.'.slab_from', $slab['slab_from']) }}"
                                                                {{ $index > 0 ? 'readonly' : '' }}
                                                            >
                                                            @error('slab.'.$index.'.slab_from')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-field mb-0">
                                                            <input
                                                                type="number"
                                                                class="form-control slab-to"
                                                                name="slab[{{$index}}][slab_to]"
                                                                value="{{ old('slab.'.$index.'.slab_to', $slab['slab_to']) }}"
                                                            >
                                                            @error('slab.'.$index.'.slab_to')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-field mb-0">
                                                            <input
                                                                type="number"
                                                                class="form-control slab"
                                                                name="slab[{{$index}}][slab]"
                                                                value="{{ old('slab.'.$index.'.slab', $slab['slab']) }}"
                                                            >
                                                            @error('slab.'.$index.'.slab')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <ul class="actions justify-content-center mb-0 mw-0">
                                                            @if ($index > 0)
                                                                <li>
                                                                    <button type="button" class="btn p-1 fs-5 text-black remove-row">
                                                                        <i class="icon-bi bi-trash"></i>
                                                                    </button>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="slab-row">
                                                <td>
                                                    <div class="form-field mb-0">
                                                        <input
                                                            type="number"
                                                            class="form-control slab-from"
                                                            name="slab[0][slab_from]"
                                                            value="{{ old('slab.0.slab_from', '') }}"
                                                        >
                                                        @error('slab.0.slab_from')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-field mb-0">
                                                        <input
                                                            type="number"
                                                            class="form-control slab-to"
                                                            name="slab[0][slab_to]"
                                                            value="{{ old('slab.0.slab_to', '') }}"
                                                        >
                                                        @error('slab.0.slab_to')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-field mb-0">
                                                        <input
                                                            type="number"
                                                            class="form-control slab"
                                                            name="slab[0][slab]"
                                                            value="{{ old('slab.0.slab', 0) }}"
                                                        >
                                                        @error('slab.0.slab')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    <ul class="actions justify-content-center mb-0 mw-0">
                                                        <!-- No remove button for the first row -->
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-footer pt-3">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <button
                                            type="button"
                                            class="btn btn-small btn-save btn-secondary add-row"
                                            id="add-row-btn"
                                            disabled
                                        >
                                            <i class="bi bi-plus-lg me-2"></i>Add More
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-footer pt-4">
                            <div class="row align-items-center">
                                <div class="col-12">
                                     <button class="btn btn-save btn-primary" type="submit" id="submit-btn">
                                       SUBMIT
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('cancellation-slab-form');
    const tableBody = document.getElementById('slab-rows');
    const addRowBtn = document.getElementById('add-row-btn');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitSpinner = document.getElementById('submit-spinner');

    const numericRegex = /^[0-9]+$/;

    // Validation function for a single row
    function validateRow(row, index) {
        const fromInput = row.querySelector('.slab-from');
        const toInput = row.querySelector('.slab-to');
        const slabInput = row.querySelector('.slab');

        let isValid = true;

        // Validate slab_from
        if (!fromInput.value.trim() || !numericRegex.test(fromInput.value.trim())) {
            fromInput.classList.add('is-invalid');
            isValid = false;
        } else {
            fromInput.classList.remove('is-invalid');
        }

        // Validate slab_to
        if (!toInput.value.trim() || !numericRegex.test(toInput.value.trim())) {
            toInput.classList.add('is-invalid');
            isValid = false;
        } else if (parseInt(toInput.value) <= parseInt(fromInput.value)) {
            toInput.classList.add('is-invalid');
            isValid = false;
        } else {
            toInput.classList.remove('is-invalid');
        }

        // Validate slab
        if (!slabInput.value.trim() || !numericRegex.test(slabInput.value.trim())) {
            slabInput.classList.add('is-invalid');
            isValid = false;
        } else {
            slabInput.classList.remove('is-invalid');
        }

        return isValid;
    }

    // Update Add More button state
    function updateAddButton() {
        const rows = tableBody.querySelectorAll('.slab-row');
        const lastRow = rows[rows.length - 1];
        const lastToInput = lastRow.querySelector('.slab-to');
        const lastFromInput = lastRow.querySelector('.slab-from');
        addRowBtn.disabled = !lastToInput.value.trim() || !numericRegex.test(lastToInput.value.trim()) || parseInt(lastToInput.value) <= parseInt(lastFromInput.value);
    }

    // Validate all rows
    function validateAllRows() {
        const rows = tableBody.querySelectorAll('.slab-row');
        let isValid = true;
        rows.forEach((row, index) => {
            if (!validateRow(row, index)) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Initial button state (removed validateAllRows to prevent red borders on first load)
    updateAddButton();

    // Real-time validation
    tableBody.addEventListener('input', function (e) {
        const row = e.target.closest('.slab-row');
        if (row) {
            const index = Array.from(tableBody.querySelectorAll('.slab-row')).indexOf(row);
            validateRow(row, index);
            updateAddButton();
        }
    });

    // Add new row
    addRowBtn.addEventListener('click', function () {
        const rows = tableBody.querySelectorAll('.slab-row');
        const lastRow = rows[rows.length - 1];
        const lastToValue = lastRow.querySelector('.slab-to').value;
        const newIndex = rows.length;

        const newRow = document.createElement('tr');
        newRow.classList.add('slab-row');
        newRow.innerHTML = `
            <td>
                <div class="form-field mb-0">
                    <input
                        type="number"
                        class="form-control slab-from"
                        name="slab[${newIndex}][slab_from]"
                        value="${parseInt(lastToValue) + 1}"
                        readonly
                    >
                </div>
            </td>
            <td>
                <div class="form-field mb-0">
                    <input
                        type="number"
                        class="form-control slab-to"
                        name="slab[${newIndex}][slab_to]"
                        value=""
                    >
                </div>
            </td>
            <td>
                <div class="form-field mb-0">
                    <input
                        type="number"
                        class="form-control slab"
                        name="slab[${newIndex}][slab]"
                        value="0"
                    >
                </div>
            </td>
            <td>
                <ul class="actions justify-content-center mb-0 mw-0">
                    <li>
                        <button type="button" class="btn p-1 fs-5 text-black remove-row">
                            <i class="icon-bi bi-trash"></i>
                        </button>
                    </li>
                </ul>
            </td>
        `;
        tableBody.appendChild(newRow);
        updateAddButton();
    });

    // Remove row
    tableBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('.slab-row');
            const rows = tableBody.querySelectorAll('.slab-row');
            if (rows.length > 1) {
                row.remove();
                updateAddButton();
            }
        }
    });

    // Form submission
    form.addEventListener('submit', function (e) {
        if (!validateAllRows()) {
            e.preventDefault();
            return;
        }
        submitText.style.display = 'none';
        submitSpinner.style.display = 'inline-block';
    });
});
</script>
@endsection