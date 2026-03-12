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
        background-color: rgba(0,0,0,0.5);
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
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Lead' : 'Add Lead' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.lead.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="form-box">
                <form action="{{ route('pms.lead.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="name">Name<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name', $detail->name ?? '') }}" 
                                    class="form-control @error('name') is-invalid @enderror"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="email">Email<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="email" 
                                    value="{{ old('email', $detail->email ?? '') }}" 
                                    class="form-control @error('email') is-invalid @enderror"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="mobile">Mobile<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="mobile" 
                                    value="{{ old('mobile', $detail->mobile ?? '') }}" 
                                    class="form-control @error('mobile') is-invalid @enderror"
                                >
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-field">
                                <label for="stage">Lead Stage<sup>*</sup></label>
                                <select name="stage" id="stage" class="form-control @error('stage') is-invalid @enderror">
                                    <option value="">Select Stage</option>
                                    <option value="Cold" {{ old('stage', $detail->stage ?? '') == 'Cold' ? 'selected' : '' }}>Cold</option>
                                    <option value="Warm" {{ old('stage', $detail->stage ?? '') == 'Warm' ? 'selected' : '' }}>Warm</option>
                                    <option value="Hot" {{ old('stage', $detail->stage ?? '') == 'Hot' ? 'selected' : '' }}>Hot</option>
                                    <option value="Dropped" {{ old('stage', $detail->stage ?? '') == 'Dropped' ? 'selected' : '' }}>Dropped</option>
                                    <option value="Booked" {{ old('stage', $detail->stage ?? '') == 'Booked' ? 'selected' : '' }}>Booked</option>
                                </select>
                                @error('stage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-field">
                                <label for="booking_id">Booking ID
                                    @if(old('stage', $detail->stage ?? '') == 'Booked')
                                    <sup>*</sup>
                                    @endif</label>
                                <input 
                                    type="text" 
                                    name="booking_id" 
                                    value="{{ old('booking_id', $detail->booking_id ?? '') }}" 
                                    class="form-control @error('booking_id') is-invalid @enderror"
                                >
                                @error('booking_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-field">
                                <label for="source">Source</label>
                                <input 
                                    type="text" 
                                    name="source" 
                                    value="{{ old('source', $detail->source ?? '') }}" 
                                    class="form-control @error('source') is-invalid @enderror"
                                >
                                @error('source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field mb-0">
                                <label for="checkin_date">Check in</label>
                                <input type="date" placeholder="Check in" id="checkin_date" name="checkin_date" value="{{ old('checkin_date', $detail ? date('Y-m-d', strtotime($detail->checkin_date)) : '') }}" min="{{ date('Y-m-d') }}" class="form-control flatpickr">
                                <div class="invalid-feedback" id="checkInDateError"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field mb-0">
                                <label for="checkout_date">Check out</label>
                                <input type="date" placeholder="Check out" id="checkout_date" name="checkout_date" value="{{ old('checkout_date', !empty($detail->checkout_date) ? date('Y-m-d', strtotime($detail->checkout_date)) : '') }}"
                                    min="{{ date('Y-m-d') }}" class="form-control flatpickr">
                                <div class="invalid-feedback" id="checkOutDateError"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-field">
                                <label for="note">Note</label>
                                <textarea name="note" id="note" cols="30" rows="4" class="form-control">{{ $detail->note ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    </div>
                    <div class="btn-wrap pt-2">
                        <button class="btn btn-primary px-5" type="submit">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {
        
        // Flatpickr for date inputs
        const checkInDateInput = document.getElementById("checkin_date");
        let fpcheckInDate = flatpickr(checkInDateInput, {
           // dateFormat: "d/m/Y",
          //  minDate: "today", // 
            dateFormat: "Y-m-d",
            altInput: true, 
            altFormat: "d/m/Y",
            minDate: "today",

           // clickOpens: false,
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length) {
                    // Use selectedDates[0] directly to calculate +1 day
                    let minCheckOutDate = new Date(selectedDates[0]);
                    minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
                    
                    fpcheckOutDate.set('minDate', minCheckOutDate);

                    if (checkOutDateInput.value && dayjs(checkOutDateInput.value, 'YYYY-MM-DD').isSameOrBefore(dayjs(selectedDates[0]))) {
                        checkOutDateInput.value = '';
                    }

                    console.log('Check-in Date Selected:', dateStr);
                    fpcheckOutDate.open(); // Auto-open checkout calendar
                }
            }

        });

        checkInDateInput.addEventListener("click", () => {
            if (fpcheckInDate.isOpen) {
                fpcheckInDate.close();
            } else {
                fpcheckInDate.open();
            }
        });

        const checkOutDateInput = document.getElementById("checkout_date");
        let fpcheckOutDate = flatpickr(checkOutDateInput, {
           // dateFormat: "d/m/Y",
           // clickOpens: false,
            dateFormat: "Y-m-d",
            altInput: true, 
            altFormat: "d/m/Y",
            onChange: function(selectedDates, dateStr) {
                console.log('Check-out Date Selected:', dateStr);
            }
        });

        checkOutDateInput.addEventListener("click", () => {
            if (fpcheckOutDate.isOpen) {
                fpcheckOutDate.close();
            } else {
                fpcheckOutDate.open();
            }
        });

        // Flatpickr for time inputs
        const checkInTimeInput = document.getElementById("checkInTime");
        let fpTimecheckInTime = flatpickr(checkInTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            clickOpens: false,
            onChange: function(selectedDates, timeStr) {
                console.log('Check-in Time Selected:', timeStr);
            }
        });

        checkInTimeInput.addEventListener("click", () => {
            if (fpTimecheckInTime.isOpen) {
                fpTimecheckInTime.close();
            } else {
                fpTimecheckInTime.open();
            }
        });

        const checkOutTimeInput = document.getElementById("checkOutTime");
        let fpTimecheckOutTime = flatpickr(checkOutTimeInput, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            clickOpens: false,
            onChange: function(selectedDates, timeStr) {
                console.log('Check-out Time Selected:', timeStr);
            }
        });

        checkOutTimeInput.addEventListener("click", () => {
            if (fpTimecheckOutTime.isOpen) {
                fpTimecheckOutTime.close();
            } else {
                fpTimecheckOutTime.open();
            }
        });
        
    });
</script>
@endsection