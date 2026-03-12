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
                    /* Твои стили + доп для textarea */
                    .ulTab { list-style-type: none; margin: 0; padding: 0; overflow-x: auto; }
                    @media (max-width: 991.98px) { .ulTab { display: flex; } }
                    .ulTab li { margin: 10px 5px; }
                    .ulTab li button, .ulTab li a {
                        border: 1px solid #0E0E0E; padding: 10px 15px; background-color: #fff;
                        border-radius: 6px !important; text-decoration: none; display: block;
                    }
                    .ulTab li button.active, .ulTab li a.active {
                        background-color: #0e0e0e; color: #fff; border: 0;
                    }
                    .is-invalid { border: 2px solid red !important; }

                    /* Стили для textarea */
                    textarea.form-control {
                        min-height: 100px;
                        resize: vertical;
                    }
                </style>

                @include('pms.property.unit-or-multiunit-menu-segments')

                <div class="col-12 col-lg-9">
                    <form action="{{ route('pms.property.unit.or.multiunit.websitefaq.save') }}" method="POST" id="chargesForm">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $detail->home_id }}">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="pType" value="{{ request()->pType }}">

                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table class="table table-list mb-0 mw-lg" id="chargesTable">
                                    <thead>
                                        <tr>
                                            <th class="fw-semibold">Question</th>
                                            <th class="fw-semibold">Answer</th>
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
                                                    <textarea name="charges[{{ $index }}][question]" 
                                                              class="form-control @error('charges.' . $index . '.question') is-invalid @enderror" 
                                                              rows="4">{{ old('charges.' . $index . '.question', $charge['question'] ?? '') }}</textarea>
                                                    
                                                </td>
                                                <td>
                                                    <textarea name="charges[{{ $index }}][answer]" 
                                                              class="form-control @error('charges.' . $index . '.answer') is-invalid @enderror" 
                                                              rows="6">{{ old('charges.' . $index . '.answer', $charge['answer'] ?? '') }}</textarea>
                                                    
                                                </td>
                                                <td class="text-center align-middle">
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
                                <div class="row">
                                    <div class="col-12">
                                        <!-- <button type="submit" class="btn btn-save btn-primary">
                                            <span>SUBMIT</span>
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = {{ count(old('charges', $charges ?? [])) }};

    const tableBody = document.querySelector('#chargesTable tbody');
    const addRowBtn = document.getElementById('addRowBtn');

    addRowBtn.addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <textarea name="charges[${rowIndex}][question]" class="form-control" rows="4"></textarea>
            </td>
            <td>
                <textarea name="charges[${rowIndex}][answer]" class="form-control" rows="6"></textarea>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-small remove-row">
                    Remove
                </button>
            </td>
        `;
        tableBody.appendChild(newRow);
        rowIndex++;
    });

    tableBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            if (tableBody.rows.length === 1) {
                alert('At least one FAQ is required.');
                return;
            }
            e.target.closest('tr').remove();
        }
    });
});
</script>