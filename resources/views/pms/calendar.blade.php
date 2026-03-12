@extends('pms.layouts.app')
@section('content')
<style>
    .end-date-start:after {
        content: "";
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        left: 0;
        background: black;
        clip-path: polygon(100% 0, 0 0, 0 100%);
    }
</style>
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Calendar</h1>
                </div>
                <div class="col-auto d-lg-none">
                    <button class="btn btn-small btn-primary btn-icon px-2"><span class="bi bi-search absIcon"></span></button>
                </div>
            </div>
        </div>
        <div class="content-box pt-0">
            <div class="p-2 pb-0">
                <div class="searchBox mobSearch">
                    <div class="row g-2">
                        <div class="col">
                            <form method="get" action="{{ route('pms.calendar') }}">
                                <div class="row g-2">
                                    <div class="col-12 col-lg">
                                        <div class="form-field mb-0">
                                            <div class="dropdown">
                                                <select name="location" class="form-select">
                                                    <option value="">-Location-</option>
                                                    @foreach($locations as $location)
                                                    <option value="{{ $location->id }}" @if(isset($req['location']) && $req['location']==$location->id) selected="selected" @endif>
                                                        {{ $location->location_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg">
                                        <div class="dropdown">
                                            <select name="property" class="form-select">
                                                <option value="">-Property-</option>
                                                @foreach($list as $property)
                                                <option value="{{ $property->id }}" @if(isset($req['property']) && $req['property']==$property->id) selected="selected" @endif>
                                                    {{ $property->home_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg">
                                        <input type="text" id="guests" name="guests" class="form-control"
                                            placeholder="No. of Guests"
                                            value="{{ isset($req['guests']) ? $req['guests'] : '' }}">
                                        <small class="text-danger d-none" id="guests-error"></small>
                                    </div>

                                    <div class="col-12 col-lg">
                                        <input type="text" id="bedrooms" name="bedrooms" class="form-control"
                                            placeholder="No. of Bedrooms"
                                            value="{{ isset($req['bedrooms']) ? $req['bedrooms'] : '' }}">
                                        <small class="text-danger d-none" id="bedrooms-error"></small>
                                    </div>


                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary btn-icon px-2"><span class="bi bi-search absIcon"></span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('pms.calendar') }}" class="btn btn-secondary btn-icon px-2"><span class="bi bi-arrow-clockwise absIcon"></span></a>
                        </div>
                        <div class="col-12 col-lg-auto">
                            <div class="form-field mb-0">
                                <div class="input-group">
                                    <span class="input-group-text py-1"><i class="bi bi-calendar4"></i></span>
                                    <div class="dropdown">
                                        <!--<select name="month_drop_down" class="form-control" onchange="getMonthWiseCalendarData(this.value)">-->
                                        <!--    @foreach(range(7, 12) as $month)-->
                                        <!--        @php $date = date('Y-m-01', strtotime(date('Y') . '-' . $month . '-01')); @endphp-->
                                        <!--        <option value="{{ $date }}" @if(isset($req['month']) && $req['month'] == $date) selected="selected" @endif>-->
                                        <!--            {{ date('F, Y', strtotime($date)) }}-->
                                        <!--        </option>-->
                                        <!--    @endforeach-->
                                        <!--</select>-->

                                        <select name="month_drop_down" class="form-control" onchange="getMonthWiseCalendarData(this.value)">
                                            @php
                                            // Start aur end month define karo
                                            // $startMonth = '2025-11-01';
                                            // $endMonth = '2026-04-01';
                                            $startMonth = date('Y-m-01');
                                            $endMonth = date('Y-m-01', strtotime('+6 months'));

                                            // Request se selected month lo (agar aaya ho)
                                            $selectedMonth = isset($req['month']) && $req['month'] != ''
                                            ? date('Y-m-01', strtotime($req['month']))
                                            : date('Y-m-01'); // agar nahi aaya to current month

                                            // Date range banate hain
                                            $start = new DateTime($startMonth);
                                            $end = new DateTime($endMonth);
                                            $interval = new DateInterval('P1M');
                                            $period = new DatePeriod($start, $interval, $end->modify('+1 month'));
                                            @endphp

                                            @foreach ($period as $monthDate)
                                            @php $value = $monthDate->format('Y-m-01'); @endphp
                                            <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                                                {{ $monthDate->format('F, Y') }}
                                            </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tableWrapper">
                <style>
                    .horizontal-calendar {
                        min-height: auto !important;
                        height: auto !important
                    }
                </style>
                <div class="table-wrap overflow-hidden">
                    <div class="horizontal-calendar" horizontal-calendar="" style="">
                        <table class="table table-bordered w-auto mw-xl">
                            <thead>
                                <tr class="border-none-td border-start border-end">
                                    <th class="c-property" style="min-width:140px;"> </th>
                                    @foreach($months as $index => $monthObj)
                                    <th colspan="{{ $colSpanArray[$index] }}" class="text-start text-black fw-bold">
                                        {{ $monthObj }}
                                    </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="c-property" style="min-width: 140px;">   </th>
                                    @foreach($displayDatesArray as $dateDisplayKey=>$dateDisplayValue)
                                    <th>{{ $dateDisplayValue }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($final_array as $propertyKey=> $property)
                                <tr>
                                    <td class="c-property fw-bold text-nowrap" data-property-name="{{ $property->home_name }}" data-property-id="{{ $property->id }}" data-min-nights="{{ $property->min_stay }}">{{ $property->home_name }}</td>
                                    @foreach($property->prices as $pricesKey=>$singlePrice)
                                    <td class="{{ $user->role != 'Travel Agent' ? 'day' : 'agent-day' }} {{ $singlePrice->class_name }}" data-customer-name="{{ $singlePrice->customer_name }}" data-date="{{ $singlePrice->price_date }}" data-default-date="{{ $singlePrice->price_date }}" data-price="{{ $singlePrice->price }}" data-property-id="{{ $property->id }}" data-property-name="{{ $property->home_name }}" data-location-id="{{ $property->location_id }}" data-type="{{ $singlePrice->pType }}" data-id="{{ $property->ru_property_id }}" data-min-stays="1" data-type="unit" onclick="selectTd(event, this)" data-blocked-from="{{ $singlePrice->date_from ?? '' }}" data-blocked-to="{{ $singlePrice->date_to ?? '' }}">
                                        <span class="d-price">{{ $singlePrice->price }}</span>
                                        <small class="small d-block min-nights"><i class="bi bi-moon-fill"></i> {{ $singlePrice->min_stay }}</small>

                                        @if(in_array('start-date', explode(' ', $singlePrice->class_name ?? '')))
                                        <div
                                            class="s-name {{ $singlePrice->booking_class ?? '' }}"
                                            data-customer-name="{{ $singlePrice->customer_name ?? '' }}"
                                            data-checkin-time="{{ $property->checkin_time ?? '' }}"
                                            data-checkout-time="{{ $property->checkout_time ?? '' }}"
                                            data-checkin-date="{{ !empty($singlePrice->checkinDate) ? date('Y-m-d', strtotime($singlePrice->checkinDate)) : '' }}"
                                            data-checkout-date="{{ !empty($singlePrice->checkoutDate) ? date('Y-m-d', strtotime($singlePrice->checkoutDate)) : '' }}"
                                            data-total-price="{{ $singlePrice->totalPrice ?? '' }}"
                                            data-total-guest="{{ $singlePrice->no_of_guest ?? '' }}"
                                            data-is-editable="{{ $singlePrice->is_booked ?? '' }}"
                                            data-booking-id="{{ $singlePrice->bookingId ?? '' }}"
                                            data-blocked-from="{{ $singlePrice->date_from ?? '' }}"
                                            data-blocked-to="{{ $singlePrice->date_to ?? '' }}"
                                            data-channel="{{ $singlePrice->channel ?? '' }}"
                                            data-type="{{ $singlePrice->pType }}"
                                            data-id="{{ $property->ru_property_id }}">
                                            <span>{{ $singlePrice->customer_name ?? '' }}</span>
                                        </div>
                                        @endif

                                        @if($loop->last)
                                        @if(in_array('end-date', explode(' ', $singlePrice->class_name ?? '')))
                                        <div
                                            class="s-name {{ $singlePrice->booking_class ?? '' }}"
                                            data-customer-name="{{ $singlePrice->customer_name ?? '' }}"
                                            data-checkin-time="{{ $property->checkin_time ?? '' }}"
                                            data-checkout-time="{{ $property->checkout_time ?? '' }}"
                                            data-checkin-date="{{ !empty($singlePrice->checkinDate) ? date('Y-m-d', strtotime($singlePrice->checkinDate)) : '' }}"
                                            data-checkout-date="{{ !empty($singlePrice->checkoutDate) ? date('Y-m-d', strtotime($singlePrice->checkoutDate)) : '' }}"
                                            data-total-price="{{ $singlePrice->totalPrice ?? '' }}"
                                            data-total-guest="{{ $singlePrice->no_of_guest ?? '' }}"
                                            data-is-editable="{{ $singlePrice->is_booked ?? '' }}"
                                            data-booking-id="{{ $singlePrice->bookingId ?? '' }}"
                                            data-blocked-from="{{ $singlePrice->date_from ?? '' }}"
                                            data-blocked-to="{{ $singlePrice->date_to ?? '' }}"
                                            data-channel="{{ $singlePrice->channel ?? '' }}"
                                            data-type="{{ $singlePrice->pType }}"
                                            data-id="{{ $property->ru_property_id }}">
                                            <span>{{ $singlePrice->customer_name ?? '' }}</span>
                                        </div>
                                        @endif

                                        @endif


                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @csrf
    </div>
    <div class="modal fade" id="selectionModal" tabindex="-1" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selected Dates</h5>
                    <button type="button" class="close btn-close" id="modalCloseBtn"></button>
                </div>
                <div class="modal-body radio-tabs" id="modalBody"></div>
            </div>
        </div>
    </div>
</section>
@endsection
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
    let toolTipActive = true;
    let toggleType = 'priceChange';
    document.addEventListener('DOMContentLoaded', function() {

        $(".horizontal-calendar").freezeTable({
            'shadow': true,
        });

        $(".start-date").each(function() {
            let $startWidth = $(this).outerWidth();
            let $endWidth = $(this).nextUntil(".end-date").next(".end-date").outerWidth() || $startWidth;
            $(this).nextUntil(".end-date").addClass("disabled-date");
            let $siblingsWidth = $(this).nextUntil(".end-date").map(function(i, v) {
                return $(this).outerWidth();
            }).get().reduce((partialSum, a) => partialSum + a, 0);
            $(this).find(".s-name").width(($siblingsWidth + $endWidth) - 30);
        });

        $('#blockProperty').click(function() {
            $('.price-block').hide();
            $('.avaliability-block').removeClass('d-none').show();
        });

        $('#priceChange, #newBooking').click(function() {
            $('.price-block').show();
            $('.avaliability-block').addClass('d-none').hide();
        });

        let selectedCells = new Set();
        let modalElement = document.getElementById('selectionModal');
        let modalInstance = new bootstrap.Modal(modalElement);
        let modalBody = document.getElementById('modalBody');
        let firstCell = null;
        let isSelecting = false;

        document.querySelectorAll('.day').forEach(td => {
            //if (td.dataset.blockedFrom == '') {
            td.addEventListener('click', function(event) {
                event.preventDefault();
                if (!isSelecting) {
                    // First click: start selection
                    isSelecting = true;
                    firstCell = td;
                    selectedCells.clear();
                    document.querySelectorAll('.day').forEach(cell => {
                        cell.classList.remove('selected-first');
                        cell.classList.remove('selected');
                        cell.classList.remove('selected-date');
                    });
                    toggleSelection(td);
                } else {
                    // Second click: check if same cell or finalize selection
                    isSelecting = false;
                    if (firstCell === td) {
                        // Same cell clicked: unselect and reset
                        selectedCells.clear();
                        td.classList.remove('selected-first');
                        td.classList.remove('selected');
                        td.classList.remove('selected-date');
                        $('.edit-icon').remove();
                        firstCell = null;
                    } else {
                        // Different cell: finalize range and open modal
                        selectRange(firstCell, td);
                        $('.edit-icon').remove();
                        let target = $(event.target).closest('.s-name');
                        let blockedFrom = target.data('blocked-from');
                        let blockedTo = target.data('blocked-to');
                        // if (blockedFrom == undefined && blockedTo == undefined && selectedCells.size > 1) {
                        //     updateModalContent();
                        //     modalInstance.show();
                        // }
                        if (blockedFrom == undefined && blockedTo == undefined && selectedCells.size > 1) {
                            updateModalContent();
                            modalInstance.show();
                        } else {
                            // Clear selection if only one cell or blocked
                            selectedCells.clear();
                            document.querySelectorAll('.day').forEach(cell => {
                                cell.classList.remove('selected-first');
                                cell.classList.remove('selected');
                                cell.classList.remove('selected-date');
                            });
                        }
                        firstCell = null;
                    }
                }
            });

            td.addEventListener('mouseenter', function() {
                if (isSelecting && firstCell) {
                    // Highlight range dynamically as mouse moves
                    highlightRange(firstCell, td);
                }
            });

            td.addEventListener('contextmenu', function(event) {
                event.preventDefault();
                // Clear any existing edit icons and selections
                $('.edit-icon').remove();
                selectedCells.clear();
                document.querySelectorAll('.day').forEach(cell => {
                    cell.classList.remove('selected-first');
                    cell.classList.remove('selected');
                    cell.classList.remove('selected-date');
                });

                // Show edit icon on the right-clicked cell
                let editIcon = document.createElement('div');
                editIcon.className = 'edit-icon';
                editIcon.style.position = 'absolute';
                editIcon.style.top = '0';
                editIcon.style.right = '0';
                editIcon.style.cursor = 'pointer';
                editIcon.style.backgroundColor = '#000';
                editIcon.style.color = '#fff';
                editIcon.style.padding = '2px 5px';
                editIcon.style.fontSize = '12px';
                editIcon.style.borderRadius = '0 0 0 5px';
                editIcon.innerHTML = '✏️ Edit';
                td.style.position = 'relative';
                td.appendChild(editIcon);

                // Add click event to edit icon
                editIcon.addEventListener('click', function() {
                    toggleSelection(td); // Select only the clicked cell
                    let target = $(td).find('.s-name');
                    let blockedFrom = target.data('blocked-from');
                    let blockedTo = target.data('blocked-to');
                    if (blockedFrom == undefined && blockedTo == undefined) {
                        updateModalContent();
                        modalInstance.show();
                    }
                    $('.edit-icon').remove();
                    isSelecting = false;
                    firstCell = null;
                });
            });
            //}
        });

        function toggleSelection(td) {
            if (selectedCells.has(td)) {
                selectedCells.delete(td);
                td.classList.remove('selected-first');
                td.classList.remove('selected');
                td.classList.remove('selected-date');
            } else {
                selectedCells.add(td);
                if (selectedCells.size === 1) {
                    td.classList.add('selected-first');
                } else {
                    td.classList.add('selected'); // Default for middle cells
                }
            }
        }
        // new function isBlocked
        function isBlocked(cell) {
            return (
                cell.classList.contains('bg-booked') ||
                cell.classList.contains('bg-blocked') ||
                cell.classList.contains('disabled') ||
                cell.classList.contains('disabled-date')
            );
        }
        // new function markCell
        function markCell(cell, index, startIndex, endIndex) {

            const fromIndex = Math.min(startIndex, endIndex);
            const toIndex = Math.max(startIndex, endIndex);

            if (index === fromIndex) {
                // always leftmost → upper triangle
                cell.classList.add('selected-first');
            } else if (index === toIndex) {
                // always rightmost → lower triangle
                cell.classList.add('selected-date');
            } else {
                cell.classList.add('selected');
            }
        }

        // function highlightRange(startCell, endCell) {
        //     // Clear previous highlights
        //     document.querySelectorAll('.day').forEach(cell => {
        //         cell.classList.remove('selected-first');
        //         cell.classList.remove('selected');
        //         cell.classList.remove('selected-date');
        //     });
        //     selectedCells.clear();

        //     let row = startCell.parentElement;
        //     let cells = Array.from(row.querySelectorAll('.day'));
        //     let startIndex = cells.indexOf(startCell);
        //     let endIndex = cells.indexOf(endCell);

        //     let fromIndex = Math.min(startIndex, endIndex);
        //     let toIndex = Math.max(startIndex, endIndex);


        //     for (let i = fromIndex; i <= toIndex; i++) {
        //         if (cells[i].dataset.blockedFrom == '' || cells[i].dataset.blockedFrom != '') {
        //             if (i === fromIndex) {
        //                 cells[i].classList.add('selected-first');
        //             } else if (i === toIndex) {
        //                 cells[i].classList.add('selected-date');
        //             } else {
        //                 cells[i].classList.add('selected');
        //             }
        //             selectedCells.add(cells[i]);
        //         }
        //     }
        // }
        function highlightRange(startCell, endCell) {

            document.querySelectorAll('.day').forEach(cell => {
                cell.classList.remove('selected-first', 'selected', 'selected-date');
            });
            selectedCells.clear();

            let row = startCell.parentElement;
            let cells = Array.from(row.querySelectorAll('.day'));

            let startIndex = cells.indexOf(startCell);
            let endIndex = cells.indexOf(endCell);

            let step = startIndex <= endIndex ? 1 : -1;

            for (let i = startIndex; i !== endIndex + step; i += step) {
                let cell = cells[i];

                if (isBlocked(cell) && cell !== startCell) {
                    endIndex = i;
                    markCell(cell, i, startIndex, endIndex);
                    selectedCells.add(cell);
                    break;
                }

                markCell(cell, i, startIndex, endIndex);
                selectedCells.add(cell);
            }
        }

        function selectRange(startCell, endCell) {
            // Finalize the selection (same as highlight but persists)
            highlightRange(startCell, endCell);
        }

        document.addEventListener('mouseup', function(event) {
            let target = $(event.target).closest('.s-name');
            let blockedFrom = target.data('blocked-from');
            let blockedTo = target.data('blocked-to');
            let customerName = target.data('customer-name');
            let propertyId = target.data('id');
            let pType = target.data('type');
            let channel = target.data('channel');
            let bookingId = target.data('booking-id');

            if (channel != '') {
                blockedFrom = target.data('checkin-date');
                blockedTo = target.data('checkout-date');

                if (blockedFrom != undefined || blockedTo != undefined) {
                    toolTipActive = !toolTipActive;
                    if (!toolTipActive) {
                        $('.custom-tooltip').remove();
                        let tooltipHTML = `
                            <div class="custom-tooltip position-absolute bg-white p-2 shadow rounded" style="z-index: 9999; width: 200px;">
                                <div><strong>${customerName}</strong></div>
                                <div class="d-flex justify-content-between">
                                    <div><small class="text-muted">From</small><br>${blockedFrom}</div>
                                    <div><small class="text-muted">To</small><br>${blockedTo}</div>
                                </div>
                                <div class="text-danger mt-2 d-flex justify-content-between align-items-center" style="cursor: pointer;">
                                    <a href="javascript:void(0);" class="text-decoration-none" data-blocked-from="${blockedFrom}" data-blocked-to="${blockedTo}" data-blocked-id="${bookingId}" id="cancelBooking">
                                    <i class="bi bi-ban me-1"></i> <span >Cancel</span>
                                    </a>

                                    <a href="javascript:void(0);" class="text-decoration-none" data-blocked-from="${blockedFrom}" data-blocked-to="${blockedTo}" data-blocked-id="${bookingId}" id="editBooking">
                                    <i class="bi bi-pencil me-1"></i> <span >Edit</span>
                                    </a>
                                </div>

                                <div class="tooltip-arrow" style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 10px solid white;"></div>
                            </div>
                        `;
                        let $cell = $(event.target).closest('.day');
                        let offset = $cell.offset();
                        $('body').append(tooltipHTML);

                        $('.custom-tooltip').css({
                            top: offset.top - 100,
                            left: offset.left + ($cell.outerWidth() / 2) - 100
                        });
                    } else {
                        $('.custom-tooltip').remove();
                    }
                }
            } else {
                if (blockedFrom != undefined || blockedTo != undefined) {
                    toolTipActive = !toolTipActive;
                    if (!toolTipActive) {
                        $('.custom-tooltip').remove();
                        let tooltipHTML = `
                            <div class="custom-tooltip position-absolute bg-white p-2 shadow rounded" style="z-index: 9999; width: 200px;">
                                <div><strong>${customerName}</strong></div>
                                <div class="d-flex justify-content-between">
                                    <div><small class="text-muted">From</small><br>${blockedFrom}</div>
                                    <div><small class="text-muted">To</small><br>${blockedTo}</div>
                                </div>
                                <div class="text-danger mt-2 d-flex align-items-center" style="cursor: pointer;">
                                    <i class="bi bi-ban me-1"></i> <span data-blocked-from="${blockedFrom}" data-blocked-to="${blockedTo}" data-blocked-id="${propertyId}" data-type="${pType}" id="unblockDates">Unblock</span>
                                </div>
                                <div class="tooltip-arrow" style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 10px solid white;"></div>
                            </div>
                        `;
                        let $cell = $(event.target).closest('.day');
                        let offset = $cell.offset();
                        $('body').append(tooltipHTML);

                        $('.custom-tooltip').css({
                            top: offset.top - 100,
                            left: offset.left + ($cell.outerWidth() / 2) - 100
                        });
                    } else {
                        $('.custom-tooltip').remove();
                    }
                }
            }
        });

        $(document).on('click', '#unblockDates', function() {
            let blockedFrom = $(this).data('blocked-from');
            let blockedTo = $(this).data('blocked-to');
            let propertyId = $(this).data('blocked-id');
            let pType = $(this).data('type');

            if (confirm("Are you sure you want to unblock these dates?")) {
                $.ajax({
                    url: "{{ route('pms.calendar.unblock.dates') }}",
                    type: "POST",
                    data: JSON.stringify({
                        'propertyId': propertyId,
                        'blockedFrom': blockedFrom,
                        'blockedTo': blockedTo,
                        'pType': pType
                    }),
                    contentType: "application/json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message, 'Success', {
                            timeOut: 1500
                        });
                        setTimeout(() => location.reload(), 1600);
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to unblock dates:", error);
                    }
                });
            }
        });

        $(document).on('click', '#cancelBooking', function() {
            let id = $(this).data('blocked-id');
            if (id != '') {
                if (confirm("Are you sure you want to cancel booking?")) {
                    $.ajax({
                        url: "{{ route('pms.calendar.cancel.booking') }}/" + id,
                        type: "GET",
                        success: function(response) {
                            toastr.success(response.message, 'Success', {
                                timeOut: 1500
                            });
                            setTimeout(() => location.reload(), 1600);
                        },
                        error: function(xhr, status, error) {
                            console.error("Failed to cancel booking:", error);
                        }
                    });
                }
            }
        });

        $(document).on('click', '#editBooking', function() {
            let id = $(this).data('blocked-id');
            let modalBody = document.querySelector('#modalBody');
            let modalElement = document.getElementById('selectionModal');

            if (!modalElement) {
                console.error("Modal element #selectionModal not found");
                toastr.error("Modal not found", "Error");
                return;
            }

            modalBody.innerHTML = "<p>Loading...</p>"; // Show loading state

            $.ajax({
                url: "{{ route('pms.calendar.modal.booking.edit') }}",
                type: "POST",
                data: JSON.stringify({
                    'bookingId': id,
                }),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(response) {
                    console.log("Edit Booking Response:", response);
                    modalBody.innerHTML = response.modalHtml;
                    let modalInstance = new bootstrap.Modal(modalElement);
                    modalInstance.show();
                    setTimeout(function() {
                        getBookingDetail();
                    }, 500);

                    $('.modal-title').text('Edit Booking');


                },
                error: function(xhr, status, error) {
                    console.error("Failed to load booking edit modal:", error);
                    modalBody.innerHTML = "<p>Error loading content. Please try again.</p>";
                    toastr.error("Failed to load booking details", "Error");
                }
            });
        });

        let formData = [];

        function updateModalContent() {
            let modalBody = document.querySelector('#modalBody');
            modalBody.innerHTML = "";
            let formData = [];
            selectedCells.forEach(td => {
                let date = td.dataset.date;
                let price = td.dataset.price;
                let propertyId = td.dataset.id ?? "N/A";
                let pType = td.dataset.type ?? "N/A";
                let propertyName = td.dataset.propertyName ?? "N/A";
                let minNights = td.dataset.minNights ?? "N/A";
                formData.push({
                    date: date,
                    price: price,
                    propertyId: propertyId,
                    propertyName: propertyName,
                    minNights: minNights,
                    pType: pType
                });
            });
            formData.sort((a, b) => new Date(a.date) - new Date(b.date));

            let firstElement = formData[0];
            let lastElement = formData[formData.length - 1];
            $.ajax({
                url: "{{ route('pms.calendar.modal') }}",
                type: "POST",
                data: JSON.stringify({
                    'propertyName': firstElement.propertyName,
                    'price': firstElement.price,
                    'dateFrom': firstElement.date,
                    'dateDate': lastElement.date,
                    'price': firstElement.price,
                    'minNights': firstElement.minNights,
                    'propertyId': firstElement.propertyId,
                    'pType': firstElement.pType,
                }),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                },
                success: function(response) {
                    modalBody.innerHTML += response.modalHtml;
                    $('#priceChange').click();
                },
                error: function(xhr, status, error) {
                    console.error("Failed to update modal content:", error);
                }
            });
        }

        function closeModal() {

            const modalElement = document.getElementById('selectionModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
            $('.custom-tooltip').remove();
            // modalInstance.hide();
            selectedCells.clear();
            document.querySelectorAll('.day').forEach(td => {
                td.classList.remove('selected-first');
                td.classList.remove('selected');
                td.classList.remove('selected-date');
            });
            $('.edit-icon').remove();
            isSelecting = false;
            firstCell = null;
        }
        document.getElementById('modalCloseBtn')?.addEventListener('click', closeModal);
        document.getElementById('modalCloseBtnFooter')?.addEventListener('click', closeModal);

    });

    function openForm(id) {
        toggleType = id;
        $('.resetcls').each(function(index, element) {
            $(this).addClass('d-none');
        });
        $('.' + id).removeClass('d-none');

        if (id == 'newBooking') {
            $('#calendarModalForm').addClass('d-none');
            $('#calendarModalFormBooking').removeClass('d-none');
            $('.booking-submit').removeClass('d-none');
            $('.other-submit').addClass('d-none');
            getBookingDetail();
        } else {
            $('#calendarModalForm').removeClass('d-none');
            $('#calendarModalFormBooking').addClass('d-none');
            $('.booking-submit').addClass('d-none');
            $('.other-submit').removeClass('d-none');
        }


        if (id == 'priceChange') {
            $('.modal-title').text('Price & Min stay update');
        } else if (id == 'newBooking') {
            $('.modal-title').text('Create New Booking');
        } else {
            $('.modal-title').text('Block Property');
        }
    }

    function getBookingDetail() {
        let formdata = {};
        $('#calendarModalFormBooking').serializeArray().forEach(input => {
            formdata[input.name] = input.value;
        });
        $.ajax({
            url: "{{ route('pms.calendar.booking.form') }}",
            type: "POST",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#bookingDetail').html(response.bookingFromHtml);
                console.log("Booking form loaded into #bookingDetail");

                // Initialize flatpickr after AJAX content is loaded
                setTimeout(() => {
                    const checkInDateInput = document.getElementById("checkInDate");
                    const checkOutDateInput = document.getElementById("checkOutDate");

                    if (!checkInDateInput || !checkOutDateInput) {
                        console.error("Check-in or check-out input not found in DOM");
                        return;
                    }

                    // Destroy any existing flatpickr instances to prevent duplicates
                    if (checkInDateInput._flatpickr) {
                        checkInDateInput._flatpickr.destroy();
                    }
                    if (checkOutDateInput._flatpickr) {
                        checkOutDateInput._flatpickr.destroy();
                    }

                    // Initialize flatpickr for check-in date
                    let fpcheckInDate = flatpickr(checkInDateInput, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: "d/m/Y",
                        minDate: "today",
                        onChange: function(selectedDates, dateStr) {
                            if (selectedDates.length) {
                                let minCheckOutDate = dayjs(dateStr).add(1, 'day').format('YYYY-MM-DD');
                                fpcheckOutDate.set('minDate', minCheckOutDate);
                                fpcheckOutDate.open();
                            }
                        }
                    });

                    let fpcheckOutDate = flatpickr(checkOutDateInput, {
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: "d/m/Y",
                        minDate: "today",
                        onChange: function(selectedDates, dateStr) {
                            let formdata = {};
                            $('#searchFormId').serializeArray().forEach(input => {
                                formdata[input.name] = input.value;
                            });

                            $.ajax({
                                url: "{{ route('pms.calendar.ajax.get.booking.price') }}",
                                type: "POST",
                                data: formdata,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    console.log('response', response)
                                    $('.updateBooking').empty();
                                    $('.updateBooking').html(response.bookingFromHtml);
                                }
                            })
                        }
                    });
                }, 100);
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);
            }
        });
    }

    function getAjaxPrice() {
        let formdata = {};
        $('#searchFormId').serializeArray().forEach(input => {
            formdata[input.name] = input.value;
        });

        $.ajax({
            url: "{{ route('pms.calendar.ajax.get.booking.price') }}",
            type: "POST",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('response', response)
                $('.updateBooking').empty();
                $('.updateBooking').html(response.bookingFromHtml);
            }
        })
    }

    function showLoader($btn) {
        $btn.prop('disabled', true);
        $btn.find('.btn-text').addClass('d-none');
        $btn.find('.btn-spinner').removeClass('d-none');
    }

    function hideLoader($btn) {
        $btn.prop('disabled', false);
        $btn.find('.btn-text').removeClass('d-none');
        $btn.find('.btn-spinner').addClass('d-none');
    }

    function calendarModalFormSubmit() {

        let isValid = true;
        let formdata = {};
        let $btn;

        if (toggleType === 'blockProperty') {

            const reason = $('textarea[name="reason"]').val().trim();
            $btn = $('.other-submit:visible');

            if (!reason) {
                toastr.error('Reason is required');
                isValid = false;
            }

            $('#calendarModalForm').serializeArray().forEach(input => {
                formdata[input.name] = input.value;
            });

        } else if (toggleType === 'priceChange') {

            const price = $('input[name="pricePerNight"]').val().trim();
            $btn = $('.other-submit:visible');

            if (!price) {
                toastr.error('Price per night is required');
                isValid = false;
            }

            $('#calendarModalForm').serializeArray().forEach(input => {
                formdata[input.name] = input.value;
            });

        } else {
            return;
        }

        if (!isValid) return;

        showLoader($btn);

        $.ajax({
            url: "{{ route('pms.calendar.form.submit') }}",
            type: "POST",
            data: {
                formdata: formdata,
                type: toggleType
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === true) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 1500,
                        closeButton: true,
                        onHidden: function() {
                            hideLoader($btn);
                            location.reload();
                        }
                    });

                } else {
                    toastr.error(response.message || 'Something went wrong');
                    hideLoader($btn);
                }
            },
            error: function() {
                toastr.error('Request failed');
                hideLoader($btn);
            }
        });
    }
</script>

<script>
    function submitBookingForm() {
        let isValid = true;
        if (!$('#email_address').val()) {
            showError('email_address', 'Please enter email address');
            isValid = false;
        } else if (!/^\S+@\S+\.\S+$/.test($('#email_address').val())) {
            showError('email_address', 'Please enter a valid email address');
            isValid = false;
        } else {
            clearError('email_address');
        }

        if (!$('#mobile_number').val()) {
            showError('mobile_number', 'Please enter mobile number');
            isValid = false;
        } else if (!/^\d+$/.test($('#mobile_number').val())) {
            showError('mobile_number', 'Please enter a valid mobile number');
            isValid = false;
        } else {
            clearError('mobile_number');
        }

        if (!$('#first_name').val()) {
            showError('first_name', 'Please enter first name');
            isValid = false;
        } else if (!/^[a-zA-Z]+$/.test($('#first_name').val())) {
            showError('first_name', 'First name must contain only letters');
            isValid = false;
        } else {
            clearError('first_name');
        }

        if (!$('#last_name').val()) {
            showError('last_name', 'Please enter last name');
            isValid = false;
        } else if (!/^[a-zA-Z]+$/.test($('#last_name').val())) {
            showError('last_name', 'Last name must contain only letters');
            isValid = false;
        } else {
            clearError('last_name');
        }

        if (!isValid) return;
        $('.booking-submit').html('Saving... <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>');
        let checkInDate = $('#checkInDate').val().trim();
        let checkOutDate = $('#checkOutDate').val().trim();
        let tax = $("#tax_percent").text();
        let gstText = $('#gstamount').text();
        let taxAmount = parseFloat(gstText.replace(/[^0-9]/g, '')) || 0;
        let taxable_amount = $('#taxable_amount').text();
        let totalAmount = parseFloat(taxable_amount.replace(/[^0-9]+/g, ''));
        let total_payable_amount = $('#total_payable_amount').text();
        let totalPayableAmount = parseFloat(total_payable_amount.replace(/[^0-9]/g, '')) || 0;
        let discountAmount = $('#discount_amount').val();
        let no_adults = $('select[name="no_adults"]').val();
        let no_children = $('select[name="no_children"]').val();
        let addOnsDiscountTotalAmount = $('#add_ons_discount_amount').text();
        let pType = $('#pTypeBooking').val().trim();


        isSubmitLoading = true;
        $('#bookingFormBtn').html('<span class="spinner-border spinner-border-sm" role="status"></span>');

        let formData = {
            locationId: $('#locationId').val(),
            propertyId: $('#propertyId').val(),
            checkInDate: checkInDate,
            checkOutDate: checkOutDate,
            noOfNights: noOfNights,
            tax: tax,
            taxAmount: taxAmount,
            netAmount: totalAmount,
            netPayableAmount: totalPayableAmount,
            discount_amount: discountAmount,
            additional_charges: @json($addons ?? []),
            type: 'Property',
            no_children: no_children,
            no_adult: no_adults,
            per_night_price: $('#per_night_price').val(),
            base_price: $('#base_price').val(),
            initial_price: "{{$price ?? 0}}",
            dont_block: $('#cbk-block').is(':checked') ? 1 : 0,
            is_invoice: 0,
            id: '',
            tot_additional_charge_amount: addOnsDiscountTotalAmount,
            extra_guest_charge: 0,
            totalTaxableAmount: totalAmount,
            booking_note: $('#booking_note').val(),
            checkin_time: $('#checkInTime').val() || '12:00',
            checkout_time: $('#checkOutTime').val() || '11:00',
            email_address: $('#email_address').val(),
            mobile_number: $('#mobile_number').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            pType: pType,
            country_code: $('#countryCode').val(),
            bookingId: $('#bookingId').val()
        };

        $.ajax({
            url: '{{ route("pms.calendar.property.booking.save") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            },
            success: function(res) {
                if (res.status) {
                    toastr.success(res.message, 'Success', {
                        timeOut: 1000
                    });
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function(error) {
                console.error('Booking submission error:', error.responseJSON?.message);
                isSubmitLoading = false;
                $('#bookingFormBtn').html('SUBMIT');
            }
        });
    }

    function showError(elementId, message) {
        $(`#${elementId}`).addClass('is-invalid');
    }

    function clearError(elementId) {
        $(`#${elementId}`).removeClass('is-invalid');
        $(`#${elementId}Error`).text('').hide();
    }

    function getMonthWiseCalendarData(val) {
        window.location.href = "{{ route('pms.calendar') }}" + "?month=" + encodeURIComponent(val);
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.querySelector('form');
        const guests = document.getElementById('guests');
        const bedrooms = document.getElementById('bedrooms');

        const guestsError = document.getElementById('guests-error');
        const bedroomsError = document.getElementById('bedrooms-error');

        // Sirf numbers type hone dena
        guests.addEventListener('input', onlyNumbers);
        bedrooms.addEventListener('input', onlyNumbers);

        form.addEventListener('submit', function(e) {
            let valid = true;

            // Reset errors
            guestsError.classList.add('d-none');
            bedroomsError.classList.add('d-none');

            // Guests validation (agar empty nahi hai)
            if (guests.value !== '' && !isValidNumber(guests.value)) {
                guestsError.textContent = 'Only number & greater than 0 allowed';
                guestsError.classList.remove('d-none');
                valid = false;
            }

            // Bedrooms validation (agar empty nahi hai)
            if (bedrooms.value !== '' && !isValidNumber(bedrooms.value)) {
                bedroomsError.textContent = 'Only number & greater than 0 allowed';
                bedroomsError.classList.remove('d-none');
                valid = false;
            }

            if (!valid) {
                e.preventDefault(); // form submit rok dega
            }
        });

        function onlyNumbers(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        }

        function isValidNumber(value) {
            return /^[1-9][0-9]*$/.test(value);
        }

    });
</script>