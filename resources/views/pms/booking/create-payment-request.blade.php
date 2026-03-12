@extends('pms.layouts.app')
@section('content')

<style type="text/css">
    table .form-control, table .form-select {
        height: 32px;
        font-size: 11px;
        background-color: rgba(255, 255, 255, 0.5);
    }
    .btn-small{
        height: 32px;
        max-height:32px;
    }
    .btn-small span{
        font-size: 16px !important;
    }
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Booking Payment Request (ID: {{ $bookingDetail->booking_id }})</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.booking.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i> Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="row">
                <div class="col-md-3">
                    <h3 class="text-primary m-0">{{ $homeName }}</h3>
                </div>
                <div class="col-md-6 text-md-center">
                    <i class="bi bi-calendar2-check"></i> Check-in: <b>{{ $bookingDetail->checkin_date }}</b> |
                    <i class="bi bi-calendar2-check"></i> Check-out: <b>{{ $bookingDetail->checkout_date }}</b>
                </div>
                <div class="col-md-3 text-md-end">
                   Total Amount: <b>₹{{ number_format_indian($netPayableAmount) }}</b>
                </div>
                <div class="col-12 pt-3">
                    <div class="bg-dark text-white py-2 px-3 text-center">
                        Total/Remaining: <b>₹{{ number_format_indian($netPayableAmount) }}</b> /
                        ₹<span id="remainingAmount">{{ number_format_indian($remainingAmount) }}</span>
                    </div>
                </div>
            </div>
            @php
                $countries = DB::table('countries')->get();
            @endphp
            @if($remainingAmount>0)
            <div class="border pb-2 mt-4">
                <div class="table-responsive">
                    <table class="table table-list mb-0 mw-lg" id="payment-request-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile No</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                        @foreach ($rows as $key => $row)
                                <tr>
                                    <td><input type="text" class="form-control name" value="{{ $row['name'] }}" {{ isset($row['saved']) && $row['saved'] ? 'disabled' : '' }}></td>
                                    <td><input type="text" class="form-control email" value="{{ $row['email'] }}" {{ isset($row['saved']) && $row['saved'] ? 'disabled' : '' }}></td>
                                    <td>
                                        <div class="row gx-2">
                                            <div class="col-auto">
                                                <select class="form-control form-select pe-2 country_code" name="country_code" {{ isset($row['saved']) && $row['saved'] ? 'disabled' : '' }}>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->phonecode }}"
                                                            {{ $country->phonecode == ($row['country_code'] ?? 91) ? 'selected' : '' }}>
                                                            {{ $country->iso }} (+{{ $country->phonecode }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col">
                                                <input type="text" class="form-control mobile_no" value="{{ $row['mobile_no'] }}" maxlength="13" oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')" {{ isset($row['saved']) && $row['saved'] ? 'disabled' : '' }}>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="text" class="form-control amount" value="{{ $row['amount'] }}" {{ isset($row['saved']) && $row['saved'] ? 'disabled' : '' }}>
                                    </div>
                                    </td>
                                    <td>
                                        <select class="form-control payment_mode" {{ isset($row['saved']) && $row['saved'] ? 'disabled' : '' }}>
                                            <option value="">Select</option>
                                            @foreach ($paymentModeList as $mode)
                                                <option value="{{ $mode }}" {{ $row['payment_mode'] == $mode ? 'selected' : '' }}>
                                                    {{ $mode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        @if (!isset($row['saved']))
                                            <button type="button" class="btn btn-success btn-small save-row">Save</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-warning btn-small mt-2 mx-2 text-white" id="add-row-btn" disabled>+ Add</button>
            </div>
            @endif
        </div>
    </div>
</section>
<script>
    let totalPayableAmount = '{{ $remainingAmount}}';
    let rowIndex = 1;

    function validateRow(row) {
        let valid = true;
        row.find('input, select').each(function () {
            if ($(this).val().trim() === '') {
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return valid;
    }

    function getRowData(row) {
        return {
            name: row.find('.name').val(),
            email: row.find('.email').val(),
            country_code: row.find('.country_code').val(),
            mobile_no: row.find('.mobile_no').val(),
            amount: row.find('.amount').val(),
            payment_mode: row.find('.payment_mode').val(),
            booking_id: "{{ $bookingDetail->id }}"
        };
    }

    function calculateRemainingAmount() {
        let total = parseFloat("{{ $remainingAmount }}");
        let used = 0;

        $('#payment-request-table tbody tr').each(function () {
            const amt = parseFloat($(this).find('.amount').val());
            if (!isNaN(amt)) {
                used += amt;
            }
        });

        let remaining = Math.max(0, total - used);

        // Update visible remaining amount
        $('#remainingAmount').text(remaining.toLocaleString('en-IN'));

        // Disable Add button if remaining is 0 or less
        if (remaining <= 0) {
            $('#add-row-btn').prop('disabled', true);
        } else {
            $('#add-row-btn').prop('disabled', false);
        }

        return remaining;
    }

    function createNewRow(index) {
        const paymentModes = @json($paymentModeList);
        let countries = @json($countries);
        const remainingAmt = calculateRemainingAmount();

        let modeOptions = '<option value="">Select</option>';
        paymentModes.forEach(mode => {
            modeOptions += `<option value="${mode}">${mode}</option>`;
        });
        let countryOptions = '';
        countries.forEach(country => {
            countryOptions += `<option value="${country.phonecode}" ${country.phonecode == 91 ? 'selected' : ''}>${country.iso} (+${country.phonecode})</option>`;
        });

        return `
        <tr data-index="${index}">
            <td><input type="text" class="form-control name"></td>
            <td><input type="text" class="form-control email"></td>
            <td>
                <div class="row gx-2">
                    <div class="col-auto">
                        <select class="form-control form-select pe-2 country_code" name="country_code">
                            ${countryOptions}
                        </select>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control mobile_no" maxlength="13" oninput="this.value = this.value.replace(/(?!^\+)\D/g, '')">
                    </div>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input type="text" class="form-control amount" value="${remainingAmt}">
                </div>
            </td>
            <td>
                <select class="form-control payment_mode">
                    ${modeOptions}
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-success btn-small save-row">Save</button>
                <button type="button" class="btn btn-danger btn-small remove-row ms-1"><span class="material-symbols-outlined">delete</span></button>
            </td>
        </tr>`;
    }

    // Real-time amount validation and remaining amount update
    $(document).on('input', '.amount', function () {
        let amount = parseInt($(this).val()) || 0;

        if (amount > parseInt(totalPayableAmount)) {
            $('.save-row').prop('disabled', true);
            $(this).addClass('is-invalid');
        } else {
            $('.save-row').prop('disabled', false);
            $(this).removeClass('is-invalid');
        }
        calculateRemainingAmount(); // Update remaining amount
    });

    $(document).on('click', '.save-row', function () {
        const row = $(this).closest('tr');
        if (!validateRow(row)) return;

        const amount = parseFloat(row.find('.amount').val());
        const remaining = calculateRemainingAmount() + (amount || 0); // Add back current row's amount
        if (amount > remaining) {
            row.find('.amount').addClass('is-invalid');
            toastr.error('Amount cannot exceed remaining: ₹' + remaining.toLocaleString('en-IN'), 'Error', { timeOut: 2000 });
            return;
        }

        const data = getRowData(row);
        const saveBtn = $(this);
        saveBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('pms.booking.paymentRequestSave') }}",
            method: "POST",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (res) {
                if (res.status) {
                    // Disable fields
                    row.find('input, select').prop('disabled', true);
                    saveBtn.hide();
                    $('#add-row-btn').prop('disabled', false);

                    // Remove remove-btn and add edit-btn
                    row.find('.remove-row').remove();
                    if (row.find('.edit-row').length === 0) {
                        row.find('td:last').append(`
                            <button type="button" class="btn btn-primary btn-small edit-row ms-1"><span class="material-symbols-outlined">edit</span></button>
                        `);
                    }

                    // Store inserted ID as a data attribute on <tr>
                    row.attr('data-id', res.inserted_id);

                    calculateRemainingAmount();
                    toastr.success(res.message, 'Success', { timeOut: 2000 });
                }
            },
            error: function (err) {
                console.error(err);
                alert('Validation or server error. Check your inputs.');
                saveBtn.prop('disabled', false).text('Save');
            }
        });
    });

    $(document).on('click', '.edit-row', function () {
        const row = $(this).closest('tr');
        row.find('input, select').prop('disabled', false);
        $(this).replaceWith(`<button type="button" class="btn btn-success btn-small update-row">Update</button>`);
    });

    $(document).on('click', '.update-row', function () {
        const row = $(this).closest('tr');
        if (!validateRow(row)) return;

        const amount = parseFloat(row.find('.amount').val());
        const remaining = calculateRemainingAmount() + (amount || 0); // Add back current row's amount
        if (amount > remaining) {
            row.find('.amount').addClass('is-invalid');
            toastr.error('Amount cannot exceed remaining: ₹' + remaining.toLocaleString('en-IN'), 'Error', { timeOut: 2000 });
            return;
        }

        const data = getRowData(row);
        data.id = row.attr('data-id');

        const updateBtn = $(this);
        updateBtn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: "{{ route('pms.booking.paymentRequestSave') }}",
            method: "POST",
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function (res) {
                if (res.status) {
                    row.find('input, select').prop('disabled', true);
                    updateBtn.replaceWith(`<button type="button" class="btn btn-primary btn-small edit-row ms-1"><span class="material-symbols-outlined">edit</span></button>`);
                    calculateRemainingAmount();
                    toastr.success(res.message, 'Success', { timeOut: 2000 });
                } else {
                    alert('Error: ' + ('Something went wrong.'));
                    updateBtn.prop('disabled', false).text('Update');
                }
            },
            error: function (err) {
                console.error(err);
                alert('Validation or server error.');
                updateBtn.prop('disabled', false).text('Update');
            }
        });
    });

    $('#add-row-btn').on('click', function () {
        $('#payment-request-table tbody').append(createNewRow(rowIndex));
        $(this).prop('disabled', true);
        rowIndex++;
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        $('#add-row-btn').prop('disabled', false);
        calculateRemainingAmount();
    });
</script>
@endsection