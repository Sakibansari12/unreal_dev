<style>
    .modal-backdrop {
        --bs-backdrop-zindex: 9999
    }

    .modal {
        --bs-modal-zindex: 10000;
        --bs-modal-padding: 2rem;
        --bs-modal-width: 400px
    }

    .modal .modal-dialog {
        -webkit-transform: scale(0)!important;
        -ms-transform: scale(0)!important;
        transform: scale(0)!important;
        -webkit-transition: .3s ease-in-out;
        transition: .3s ease-in-out
    }

    .modal.show .modal-dialog {
        -webkit-transform: scale(1)!important;
        -ms-transform: scale(1)!important;
        transform: scale(1)!important
    }

    .modal .modal-content {
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center
    }

    .modal .modal-type {
        width: 80px;
        height: 80px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        font-size: 45px;
        color: #fff;
        border-radius: 50%;
        background-color: #12274a;
        margin: -40px auto 0
    }

    .modal .modal-type i {
        line-height: 0
    }

    .form-box{
        padding: 20px 0px;
    }

    .modal .modal-body {
        padding-top: 1.5rem
    }

    .modal .title {
        font-weight: 700
    }

    .modal .btn-save {
        min-width: 100px;
        border-width: 1px;
        border-radius: 5px
    }

    .modal .btn-save.close-btn:hover {
        background-color: #eeedfc!important;
        color: #12274a
    }

    .border-secondary-2 {
        border-color: #12274a!important
    }

    .label-radio h3,.label-check h3,.label-radio .h3,.label-check .h3 {
        font-size: 12px;
        font-weight: 500;
        margin: 0;
        color: #000
    }

    @media (min-width: 576px) {
        .label-radio h3,.label-check h3,.label-radio .h3,.label-check .h3 {
            font-size:13px
        }
    }

    .label-radio label,.label-check label {
        border: 1px solid #C7C7C7;
        border-radius: 4px;
        color: #13367d;
        font-size: 13px;
        padding: 7px 7px 7px 35px;
        position: relative;
        cursor: pointer;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        min-height: 40px;
        margin: 0
    }

    @media (min-width: 576px) {
        .label-radio label,.label-check label {
            padding:11px 11px 11px 50px;
            min-height: 55px
        }
    }

    .label-radio label>*,.label-check label>* {
        margin: 0;
        -webkit-box-flex: 1;
        -ms-flex: 1 1 100%;
        flex: 1 1 100%
    }

    .label-radio label p,.label-check label p {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap
    }

    .label-radio label p .bi,.label-check label p .bi {
        font-size: 16px;
        line-height: 1;
        margin-right: 2px
    }

    @media (max-width: 575.98px) {
        .label-radio label p .bi,.label-check label p .bi {
            display:none
        }
    }

    .label-radio label p strong,.label-check label p strong {
        font-size: 11px;
        padding-right: 5px;
        color: #12274a
    }

    @media (max-width: 575.98px) {
        .label-radio label p strong,.label-check label p strong {
            display:block;
            padding-right: 0;
            -webkit-box-flex: 0;
            -ms-flex: 0 0 100%;
            flex: 0 0 100%
        }
    }

    .label-radio label:before,.label-check label:before {
        content: "";
        font-family: bootstrap-icons;
        width: 22px;
        height: 22px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        left: 7px;
        top: 50%;
        font-size: 20px;
        margin-top: -11px;
        border-radius: 50%;
        position: absolute;
        margin-right: 10px;
        background-color: #c7c7c7
    }

    @media (min-width: 576px) {
        .label-radio label:before,.label-check label:before {
            width:28px;
            height: 28px;
            left: 11px;
            font-size: 26px;
            margin-top: -14px
        }
    }

    .label-radio input[type=radio],.label-check input[type=radio],.label-radio input[type=checkbox],.label-check input[type=checkbox],.label-radio input[type=check],.label-check input[type=check] {
        display: none
    }

    .label-radio input[type=radio]:checked+label,.label-check input[type=radio]:checked+label,.label-radio input[type=checkbox]:checked+label,.label-check input[type=checkbox]:checked+label,.label-radio input[type=check]:checked+label,.label-check input[type=check]:checked+label {
        background: #12274a;
        color: #fff;
        background-color: #12274a
    }

    .label-radio input[type=radio]:checked+label h3,.label-check input[type=radio]:checked+label h3,.label-radio input[type=radio]:checked+label .h3,.label-check input[type=radio]:checked+label .h3,.label-radio input[type=checkbox]:checked+label h3,.label-check input[type=checkbox]:checked+label h3,.label-radio input[type=checkbox]:checked+label .h3,.label-check input[type=checkbox]:checked+label .h3,.label-radio input[type=check]:checked+label h3,.label-check input[type=check]:checked+label h3,.label-radio input[type=check]:checked+label .h3,.label-check input[type=check]:checked+label .h3 {
        color: inherit
    }

    .label-radio input[type=radio]:checked+label p strong,.label-check input[type=radio]:checked+label p strong,.label-radio input[type=checkbox]:checked+label p strong,.label-check input[type=checkbox]:checked+label p strong,.label-radio input[type=check]:checked+label p strong,.label-check input[type=check]:checked+label p strong {
        color: #fff
    }

    .label-radio input[type=radio]:checked+label:before,.label-check input[type=radio]:checked+label:before,.label-radio input[type=checkbox]:checked+label:before,.label-check input[type=checkbox]:checked+label:before,.label-radio input[type=check]:checked+label:before,.label-check input[type=check]:checked+label:before {
        content: "\F26E";
    background-color: #fff;
    font-family: "bootstrap-icons";
    color: #12274a;
    }
</style>
<style>
    .ulTab{list-style-type:none;margin:0;padding:0;overflow-x:auto}@media (max-width: 991.98px){.ulTab{display:-webkit-box;display:-ms-flexbox;display:flex}}.ulTab li{margin:10px 5px}.ulTab li button,.ulTab li a{border:0px;padding:10px 15px;background-color:#fff;width:100%;border-radius:6px!important;text-align:left;border:1px solid #0E0E0E;display:block;text-decoration:none}.ulTab li button.active,.ulTab li a.active{border:0px;padding:10px 15px;color:#fff;background-color:#0e0e0e}.ulTab li button[disabled],.ulTab li a[disabled]{opacity:1;color:#000}@media (max-width: 991.98px){.ulTab li button,.ulTab li a{white-space:nowrap}}
    .ulTab {
        display: block !important; /* Ensure the menu is always visible */
    }
    .datepicker__month-button{
        text-indent: 0px !important;
    }

    .datepicker__month-button .datepicker__month-button--prev{
    display: none;
    }
    .datepicker__month-button:after {
        background-repeat: no-repeat;
        background-position: center;
        float: left;
        text-indent: 0;
        content: "";
        width: 15px;
        height: 15px;
    }
    .datepicker__month-button--next:after{
        background-image: url(./right.svg);
        background-size: cover;
    }
    .datepicker__month-button--prev:after{
        background-image: url(./left.svg);
        background-size: cover;
    }
    .close-datepicker{
        border-radius: 2px;
        background-color: #15274C;
        border: none;
        -webkit-box-shadow: none;
        box-shadow: none;
        font-size: 10px;
        color: #fff;
        margin-top: 2px;
        margin-left: 8px;
        padding: 6px 13px;
        text-decoration: none;
        text-shadow: none;
        text-transform: uppercase;
    }
    .close-datepicker:hover{
        background-color: #002164;
        color: #fff;
    }
</style>
<div class="radio-tabs pb-0">
    <div class="row g-2">
        <div class="col-auto">
            <div class="label-radio">
                <input type="radio" class="active" name="priceChangeOrBooking" id="priceChange" onclick="openForm('priceChange')">
                <label for="priceChange">Price Change</label>
            </div>
        </div>
        @if($req['dateFrom'] < $req['dateDate'])
            <div class="col-auto">
                <div class="label-radio">
                    <input type="radio" name="priceChangeOrBooking" id="newBooking" onclick="openForm('newBooking')">
                    <label for="newBooking">New Booking</label>
                </div>
            </div>
            <div class="col-auto">
                <div class="label-radio">
                    <input type="radio" name="priceChangeOrBooking" id="blockProperty" onclick="openForm('blockProperty')">
                    <label for="blockProperty">Block</label>
                </div>
            </div>
        @endif
    </div>
</div>

    <div class="modal-body form-box">
        <h4 class="text-primary fs-6 mb-0">{{  $req['propertyName'] }}</h4> Date: From {{date("F d, Y", strtotime($req['dateFrom']))}} to {{date("F d, Y", strtotime($req['dateDate']))}}
        <br>
        <br>
        {{-- Price or Avaliability Change Form --}}
        <form action="#" id="calendarModalForm" novalidate>
            <input type="hidden" name="date_from" value="{{ $req['dateFrom'] }}">
            <input type="hidden" name="date_to" value="{{ $req['dateDate'] }}">
            <input type="hidden" name="propertyId" value="{{ $req['propertyId'] }}">
            <input type="hidden" name="pType" value="{{ $req['pType'] }}">

            <div class="row">
                <div class="priceChange resetcls">
                    <div class="col-12 col-lg-6">
                        <div class="form-field">
                            <label for="">Price per Night<sup class="text-danger">*</sup></label>
                            <input type="number" class="form-control" name="pricePerNight" value="{{ $req['price']  }}">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-field">
                            <label for="">Minimum No. of Nights</label>
                            <input type="number" class="form-control" name="minNights" value="">
                        </div>
                    </div>
                </div>
                <div class="blockProperty d-none resetcls">
                    <div class="col-12 col-lg-12 ">
                        <div class="form-field">
                            <label for="">Reason</label>
                            <textarea name="reason"  class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary"  onclick="calendarModalFormSubmit()"><span>Save</span></button>
            </div>
        </form>

        {{-- Booking Form --}}
        <form action="#" id="calendarModalFormBooking"  novalidate>
            <input type="hidden" name="date_from" value="{{ $req['dateFrom'] }}">
            <input type="hidden" name="date_to" value="{{ $req['dateDate'] }}">
            <input type="hidden" name="propertyId" value="{{ $req['propertyId'] }}">
            <input type="hidden" name="pType" value="{{ $req['pType'] }}">
            <input type="hidden" name="no_adults" value="1">
            <div class="row" id="bookingDetail">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary other-submit"  onclick="calendarModalFormSubmit()"><span>Save</span></button>

                <button type="button" class="btn btn-primary booking-submit d-none"  onclick="submitBookingForm()"><span>Save</span></button>
            </div>
        </form>
    </div>



