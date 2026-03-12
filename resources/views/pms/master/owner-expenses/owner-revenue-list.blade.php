@extends('pms.layouts.app')
@section('content')
<style>
    .select2-container {
        width: 100% !important;
        margin: 10px 0;
    }
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
</style>

<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $owner_new_name ?? '' }}
                        @if($property_new_name && $owner_new_name)
                        ({{ $property_new_name ?? '' }})
                        @endif
                        @if($property_new_name && empty($owner_new_name))
                        {{ $property_new_name ?? '' }}
                        @endif
                    </h1>
                </div>
                <div class="col-auto">
                        <a href="{{ route('pms.owner.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                        </a>
                    </div>
            </div>
        </div>
        <div class="content-box p-3">
            <form method="GET" action="{{ route('pms.owner-revenue.list') }}">
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <input type="hidden" name="property_id" value="{{ request('property_id') }}">
                    <input type="hidden" name="pType" value="{{ request('pType') }}">
                    <input type="hidden" name="owner_id" value="{{ request('owner_id') }}">
                    <div class="col-sm-6 col-md-5 col-lg-auto">
                        <div class="input-group input-group-sm">
                           <!--  <input type="text" name="search" class="form-control" placeholder="Search by expenses name." value="{{ request('search') }}"> -->
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>
                            </button>
                            <!-- <a href="{{ route('pms.owner-revenue.list') }}" class="btn btn-warning">
                                <span class="material-symbols-outlined">refresh</span>
                            </a> -->
                             <a href="{{ route('pms.owner-revenue.export',  [
                                    'property_id' => request('property_id'),
                                    'pType' => request('pType'),
                                    'owner_id' => request('owner_id'),
                                ]) }}"
                                    class="btn btn-small btn-export btn-secondary">
                                    <span class="material-symbols-outlined">
                                        file_export
                                    </span>
                                </a>
                           
                        </div>
                    </div>

                   <div class="col-6 col-md-4 col-lg-2">
                        <select class="form-select" name="year" id="year">
                            @foreach([2025, 2026] as $y)
                                <option value="{{ $y }}" {{ request('year', 2025) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <div class="data-info text-primary">
                           
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive data-table text-nowrap">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th></th>
                        @foreach($months as $month)
                            <th>{{ $month->format('M Y') }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <!-- <tr>
                            <th>Owner Name</th>
                            @foreach($months as $month)
                                @php $key = $month->format('F'); @endphp
                                <td>{{ isset($item['months'][$key]) ? $item['owner_name'] : '' }}</td>
                            @endforeach
                        </tr> -->

                       <!--  <tr>
                            <th>Unit</th>
                            @foreach($months as $month)
                                @php $key = $month->format('F'); @endphp
                                <td>{{ isset($item['months'][$key]) ? $item['unit_name'] : '' }}</td>
                            @endforeach
                        </tr> -->
                        <tr>
                            <th>No. of Bookings</th>
                            @foreach($months as $month)
                                @php $key = $month->format('F'); @endphp
                                <td>{{ $item['months'][$key]['bookings'] ?? '' }}</td>
                            @endforeach
                        </tr>
                    <tr>
                        <th>Amount</th>
                        @foreach($months as $month)
                            @php $key = $month->format('F'); @endphp
                            <!--<td>{{ (!empty($item['months'][$key]['amount']) && $item['months'][$key]['amount'] != 0) ? $item['months'][$key]['amount'] : '' }}</td>-->
                            
                            
                            <td>
                                {{ 
                                    (!empty($item['months'][$key]['amount']) && $item['months'][$key]['amount'] != 0)
                                        ? preg_replace('/(\d+?)(?=(\d\d)+(\d)(?!\d))/', '$1,', (int)$item['months'][$key]['amount'])
                                        : '' 
                                }}
                            </td>

                            
                        @endforeach
                    </tr>

                        <tr>
                            <th>Expenses</th>
                            @foreach($months as $month)
                                @php $key = $month->format('F'); @endphp
                                <!--<td>{{ (!empty($item['months'][$key]['expenses']) && $item['months'][$key]['expenses'] != 0) ? $item['months'][$key]['expenses'] : '' }}</td>-->
                                <td>
                                    {{ 
                                        (!empty($item['months'][$key]['expenses']) && $item['months'][$key]['expenses'] != 0)
                                            ? preg_replace('/(\d+?)(?=(\d\d)+(\d)(?!\d))/', '$1,', (int)$item['months'][$key]['expenses'])
                                            : '' 
                                    }}
                                </td>

                            @endforeach
                        </tr>

                        <tr>
                            <th>Total</th>
                            @foreach($months as $month)
                                @php $key = $month->format('F'); @endphp
                                <!--<td><b>{{ (!empty($item['months'][$key]['total']) && $item['months'][$key]['total'] != 0) ? $item['months'][$key]['total'] : '' }}</b></td>-->
                                <td>
                                    <b>
                                        {{
                                            (!empty($item['months'][$key]['total']) && $item['months'][$key]['total'] != 0)
                                                ? preg_replace('/(\d+?)(?=(\d\d)+(\d)(?!\d))/', '$1,', (int)$item['months'][$key]['total'])
                                                : ''
                                        }}
                                    </b>
                                </td>

                            @endforeach
                        </tr>

                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted">No Record Found!</td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>
            <!-- <div class="delete-all-action pt-3">
                <button type="button" class="btn btn-sm rounded-2 btn-secondary deleteSelected" disabled>DELETE SELECTED</button>
            </div> -->
            <!-- <div class="mt-3">
                
            </div> -->
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(function () {
    // Select2 init
    $('.js-select2-search, .js-select2-search-unit').select2({
        allowClear: true,
        minimumInputLength: 0,
    });

    // 🔹 Set pType helper
    function setPType(source) {
        let pType = source.find(':selected').data('ptype') || '';
        $("#pType").val(pType);
    }

    // 🔹 On Expenses change
    $('#searchOwnerExpensesId').on('change', function () {
        let expenseId = $(this).val();
        let pType = $(this).find(':selected').data('ptype') || '';
        $("#pType").val(pType);

        if (expenseId) {
            loadUnits(expenseId, pType, null);
        } else {
            $('#searchUnitId').empty().append('<option value="">Select unit</option>');
        }
    });

    // 🔹 Load units by expense
    function loadUnits(expenseId, pType, selectedUnitId) {
        $.ajax({
            url: "{{ route('get.units.by.owner.expense') }}",
            type: "GET",
            data: { expense_id: expenseId, pType: pType },
            success: function (res) {
                $('#searchUnitId').empty().append('<option value="">Select unit</option>');

                $.each(res, function (key, unit) {
                    $('#searchUnitId').append(
                        '<option value="' + unit.id + '" data-ptype="' + unit.pType + '">' + unit.unit_name + '</option>'
                    );
                });

                if (selectedUnitId) {
                    $('#searchUnitId').val(selectedUnitId).trigger('change');
                }
            }
        });
    }

    // 🔹 On Unit change (agar direct unit select kare)
    $('#searchUnitId').on('change', function () {
        setPType($(this));
    });

    // 🔹 On page load: prefill if already selected
    let initialExpenseId = "{{ request('searchOwnerExpensesId') }}";
    let initialPType = "{{ request('pType') }}";
    let initialUnitId = "{{ request('searchUnitId') }}";

    if (initialExpenseId) {
        $('#searchOwnerExpensesId').val(initialExpenseId).trigger('change');
        $('#pType').val(initialPType);
        loadUnits(initialExpenseId, initialPType, initialUnitId);
    } else if (initialUnitId) {
        $('#searchUnitId').val(initialUnitId).trigger('change');
        setPType($('#searchUnitId'));
    }
});


</script>
@endsection