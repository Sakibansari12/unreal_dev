<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PMS as PMS;
use App\Http\Controllers\RazorpayController;

Route::any('/razorpay/webhook/callback', [RazorpayController::class, 'handleWebhookCallBack'])->name('handle.razorpay.callback');

Route::prefix('pms')->group(function () {
    Route::get('/login', [PMS\Auth\LoginController::class,'showLoginForm'])->name('pms.form');
    Route::post('/login', [PMS\Auth\LoginController::class,'login'])->name('pms.login');

    Route::middleware(['auth:admin', \App\Http\Middleware\SessionTimeout::class])->group(function () {

        Route::get('/dashboard', [PMS\DashboardController::class,'dashboard'])->name('pms.dashboard');
        Route::get('/truncate', [PMS\DashboardController::class,'truncateTable'])->name('pms.truncate');
        Route::get('/logout', [PMS\Auth\LoginController::class,'logout'])->name('pms.logout');
        Route::get('/changepassword', [PMS\Auth\LoginController::class,'changepassword'])->name('pms.changepassword.form');
        Route::post('/changepassword', [PMS\Auth\LoginController::class,'changepasswordsubmit'])->name('pms.changepasswordsubmit.submit');

        /*User*/
        Route::group(['middleware' => ['auth', 'role:1,7']], function () {
            Route::get('user/list', [PMS\User\UserController::class, 'index'])->name('pms.user.list');
            Route::get('user/form/{id?}', [PMS\User\UserController::class, 'form'])->middleware('check.user')->name('pms.user.form');
            Route::get('get/property', [PMS\User\UserController::class, 'getProperties'])->name('pms.get.property');
            Route::post('user/save', [PMS\User\UserController::class, 'save'])->name('pms.user.save');
            Route::put('user/toggle-status/{id}', [PMS\User\UserController::class, 'toggleStatus'])->name('pms.user.toggle-status');
            Route::delete('user/delete/{id}', [PMS\User\UserController::class, 'delete'])->name('pms.user.delete');
            Route::post('user/multidelete', [PMS\User\UserController::class, 'multiDelete'])->name('pms.user.multidelete');
            Route::get('user/get-cities/{state_id}', [PMS\User\UserController::class, 'getCities'])->name('pms.get.cities');
        });


        // calendar
        Route::group(['middleware' => ['auth', 'role:1,2,5,7,6']], function () {
            Route::get('/calendar', [PMS\CalendarController::class,'index'])->name('pms.calendar');
            Route::post('/calendar/modal', [PMS\CalendarController::class,'calendarModal'])->name('pms.calendar.modal');
            Route::post('/calendar/form/submit', [PMS\CalendarController::class,'calendarModalFormSubmit'])->name('pms.calendar.form.submit');
            Route::post('/calendar/unblock/dates', [PMS\CalendarController::class,'calendarUnblockDates'])->name('pms.calendar.unblock.dates');
            Route::get('/calendar/filter', [PMS\CalendarController::class,'index'])->name('calendar.filter');
            Route::post('/calendar/booking/form', [PMS\CalendarController::class,'calendarBookingForm'])->name('pms.calendar.booking.form');
            Route::get('/calendar/cancel/booking/{id?}', [PMS\CalendarController::class,'cancelBooking'])->name('pms.calendar.cancel.booking');
            Route::post('/calendar/modal/booking/edit', [PMS\CalendarController::class,'calendarModalBookingEdit'])->name('pms.calendar.modal.booking.edit');
            Route::post('/calendar/ajax/get/booking/price', [PMS\CalendarController::class,'calendarAjaxGetBookingPrice'])->name('pms.calendar.ajax.get.booking.price');
        });

        // property
        Route::group(['middleware' => ['auth', 'role:1,2,7,8']], function () {
            Route::get('/property/list', [PMS\Property\PropertyController::class,'list'])->name('pms.property.list');
            
            Route::get('property/form/{id?}', [PMS\Property\PropertyController::class, 'form'])->middleware('check.property.ownership')->name('pms.property.form');
            
            Route::post('property/save', [PMS\Property\PropertyController::class, 'save'])->name('pms.property.save');
            Route::put('property/toggle-status/{id}', [PMS\Property\PropertyController::class, 'toggleStatus'])->name('pms.property.toggle-status');
            Route::delete('property/delete/{id}', [PMS\Property\PropertyController::class, 'delete'])->name('pms.property.delete');
            Route::post('property/multidelete', [PMS\Property\PropertyController::class, 'multiDelete'])->name('pms.property.multidelete');
            Route::get('property/get/locations/by/state/{state_id?}', [PMS\Property\PropertyController::class, 'getLocationByState'])->name('pms.property.get.location.by.state');
            // unit or multi unit
            Route::get('property/unit/or/multiunit/list', [PMS\Property\UnitOrMultiUnitController::class,'list'])->name('pms.property.unit.or.multiunit.list');
            Route::post('property/unit-or-multiunit/save', [PMS\Property\UnitOrMultiUnitController::class, 'save'])->name('pms.property.unit.or.multiunit.save');
            Route::put('property/unit-or-multiunit/toggle-status/{id}', [PMS\Property\UnitOrMultiUnitController::class, 'toggleStatus'])->name('pms.property.unit.or.multiunit.toggle-status');
            Route::delete('property/unit-or-multiunit/delete/{id}', [PMS\Property\UnitOrMultiUnitController::class, 'delete'])->name('pms.property.unit.or.multiunit.delete');
            Route::post('property/unit-or-multiunit/multidelete', [PMS\Property\UnitOrMultiUnitController::class, 'multiDelete'])->name('pms.property.unit.or.multiunit.multidelete');
            Route::get('property/unitormultiunit/overview/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'overview'])->name('pms.property.unit.or.multiunit.overview');
            Route::post('property/unitormultiunit/overview/save', [PMS\Property\UnitOrMultiUnitController::class, 'overviewSave'])->name('pms.property.unit.or.multiunit.overview.save');
            Route::get('property/unitormultiunit/amenities/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'amenities'])->name('pms.property.unit.or.multiunit.amenities');
            Route::post('property/unitormultiunit/amenities/save', [PMS\Property\UnitOrMultiUnitController::class, 'amenitiesSave'])->name('pms.property.unit.or.multiunit.amenities.save');
            Route::get('property/unitormultiunit/additionalcharges/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'additionalCharges'])->name('pms.property.unit.or.multiunit.additionalcharges');
            Route::post('property/unitormultiunit/additionalcharges/save', [PMS\Property\UnitOrMultiUnitController::class, 'saveAdditionalCharge'])->name('pms.property.unit.or.multiunit.additionalcharges.save');
            Route::get('property/unitormultiunit/gallery/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'gallery'])->name('pms.property.unit.or.multiunit.gallery');
            Route::post('property/unitormultiunit/gallery/save', [PMS\Property\UnitOrMultiUnitController::class, 'gallerySave'])->name('pms.property.unit.or.multiunit.gallery.save');
            Route::get('property/unitormultiunit/commas/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'commas'])->name('pms.property.unit.or.multiunit.commas');
            Route::post('property/unitormultiunit/commas/save', [PMS\Property\UnitOrMultiUnitController::class, 'commasSave'])->name('pms.property.unit.or.multiunit.commas.save');
            Route::get('property/unitormultiunit/cancellationslab/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'cancellationSlab'])->name('pms.property.unit.or.multiunit.cancellationslab');
            Route::post('property/unitormultiunit/cancellationslab/save', [PMS\Property\UnitOrMultiUnitController::class, 'cancellationSlabSave'])->name('pms.property.unit.or.multiunit.cancellationslab.save');
            Route::get('property/unitormultiunit/floor/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'floor'])->name('pms.property.unit.or.multiunit.floor');
            Route::post('property/unitormultiunit/floor/save', [PMS\Property\UnitOrMultiUnitController::class, 'floorSave'])->name('pms.property.unit.or.multiunit.floor.save');
            Route::put('property/unit-or-multiunit/show-on-home/{id}', [PMS\Property\UnitOrMultiUnitController::class, 'showOnHome'])->name('pms.property.unit.or.multiunit.show-on-home');
            Route::get('property/unitormultiunit/preview/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'preview'])->name('pms.property.unit.or.multiunit.preview');
            Route::post('property/unitormultiunit/publish/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'publish'])->name('pms.property.unit.or.multiunit.publish');
            
            Route::get('property/unitormultiunit/video/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'galleryVideo'])->name('pms.property.unit.or.multiunit.video');
            Route::post('property/unitormultiunit/video/save', [PMS\Property\UnitOrMultiUnitController::class, 'gallerySaveVideo'])->name('pms.property.unit.or.multiunit.video.save');

            
            Route::get('property/unitormultiunit/websiteamenities/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'websiteAmenities'])->name('pms.property.unit.or.multiunit.websiteamenities');
            Route::post('property/unitormultiunit/website/amenities/save', [PMS\Property\UnitOrMultiUnitController::class, 'websiteamenitiesSave'])->name('pms.property.unit.or.multiunit.website.amenities.save');


            
            
            Route::get('property/unitormultiunit/review/{id?}', [PMS\Property\UnitOrMultiUnitController::class, 'review'])->name('pms.property.unit.or.multiunit.review');
            Route::post('property/unitormultiunit/review/save', [PMS\Property\UnitOrMultiUnitController::class, 'reviewSave'])->name('pms.property.unit.or.multiunit.review.save');
             Route::delete('property/review/delete/{id}', [PMS\Property\UnitOrMultiUnitController::class, 'deleteReview'])->name('pms.property.review.delete');
             Route::post('property/review/delete-multiple', [PMS\Property\UnitOrMultiUnitController::class, 'deleteMultipleReviews'])->name('pms.property.review.delete.multiple');
             Route::put('property/review/show_home/toggle-status/{id}', [PMS\Property\UnitOrMultiUnitController::class, 'ShowHometoggleStatus'])->name('pms.property.review.show_home.toggle-status');
       
        });

        Route::group(['middleware' => ['auth', 'role:1,2,4,5,6,7,8']], function () {

            // Show Menu > Bookings/Enquiry (parent menu)
            Route::get('booking/list', [PMS\Booking\BookingController::class, 'index'])->name('pms.booking.list');

            // Show submenu > New Booking (only for 1,2,5,6,7)
            Route::group(['middleware' => ['role:1,2,5,6,7,8']], function () {
                Route::get('booking/by/location', [PMS\Booking\BookingByLocationController::class, 'byLocation'])->name('pms.booking.bylocation');
            });

            // Show submenu > Booking Enquiry (only for 1,2,5,7)
            Route::group(['middleware' => ['role:1,2,5,7,8']], function () {
                Route::get('booking/enquiry', [PMS\Booking\BookingController::class, 'bookingEnquiry'])->name('pms.booking.bookingEnquiry');
                Route::delete('booking/enquiry/delete/{id}', [PMS\Booking\BookingController::class, 'delete'])->name('pms.bookingEnquiry.delete');
                Route::post('booking/enquiry/multidelete', [PMS\Booking\BookingController::class, 'multiDelete'])->name('pms.bookingEnquiry.multidelete');
            });

            // Other Booking Routes (accessible to all 1,2,4,5,6,7)
            Route::get('booking/by/property', [PMS\Booking\BookingController::class, 'byProperty'])->name('pms.booking.byproperty');
            Route::post('booking/property/by/location/id', [PMS\Booking\BookingController::class, 'propertyListByLocationId'])->name('pms.booking.propertyListByLocationId');
            Route::get('booking/property/unavaliabile/dates', [PMS\Booking\BookingController::class, 'getPropertyBookingUnavailableDates'])->name('pms.booking.propertyBookingUnavailableDates');
            Route::post('booking/property/search', [PMS\Booking\BookingController::class, 'searchProperties'])->name('pms.booking.searchProperties');

           // Payment Request (same role access as All Bookings: 1,2,4,5,6,7)
            Route::get('booking/show/payment/request/form/{property_booking_id}', [PMS\Booking\PaymentRequestController::class, 'showPaymentRequestForm'])->middleware('check.paymentRequests')->name('pms.booking.showPaymentRequestForm');
            Route::get('booking/show/payment/request/edit/form/{property_booking_id}/{payment_request_id}', [PMS\Booking\PaymentRequestController::class, 'showPaymentRequestEditForm'])->middleware('check.paymentRequests')->name('pms.booking.showPaymentRequestEditForm');
            Route::post('booking/payment/request/save', [PMS\Booking\PaymentRequestController::class, 'paymentRequestSave'])->name('pms.booking.paymentRequestSave');
            Route::get('booking/paymentrequest', [PMS\Booking\PaymentRequestController::class, 'paymentrequest'])->name('pms.booking.paymentRequest');
            Route::post('booking/paymentrequestupdate', [PMS\Booking\PaymentRequestController::class, 'paymentRequestUpdate'])->name('pms.booking.paymentRequestUpdate');

            // Booking Export, Cancel, Delete (all same roles)
            Route::get('bookingproperty/database/export', [PMS\Booking\BookingController::class, 'bookingExport'])->name('pms.bookingproperty.exportToExcel');
            Route::delete('bookingproperty/delete/{id}', [PMS\Booking\BookingController::class, 'deleteBooking'])->name('pms.bookingproperty.delete');
            Route::get('bookingproperty/cancellation/{id}', [PMS\Booking\BookingController::class, 'cancelBooking'])->name('pms.bookingproperty.cancellation');

            // Guest Check-in (same role: 1,2,4,5,6,7)
            Route::get('property/booking/{id}/guest-ids', [PMS\Booking\GuestCheckinController::class, 'showGuestIdForm'])->name('property.booking.guest-ids');
            Route::post('property/booking/ids', [PMS\Booking\GuestCheckinController::class, 'savePropertyBookingIds'])->name('property.booking.ids.save');
            Route::post('/guest/otp/send', [PMS\Booking\GuestCheckinController::class, 'SendOtp'])->name('send.guest.otp');
            Route::post('/guest/otp/verify', [PMS\Booking\GuestCheckinController::class, 'otpVerify'])->name('guest.verify.otp');

            // API-based Booking by Location (no restriction beyond above)
            Route::get('booking/by/locationfetch', [PMS\Booking\BookingByLocationController::class, 'locationfetch'])->name('pms.booking.locationfetch');
            Route::post('booking/by/location/propertylist', [PMS\Booking\BookingByLocationController::class, 'propertyList'])->name('pms.booking.bylocationPropertyList');
            Route::post('booking/save', [PMS\Booking\BookingByLocationController::class, 'savePropertyBooking'])->name('pms.booking.save');
            Route::post('property/booking/save', [PMS\Booking\BookingController::class, 'savePropertyBooking'])->name('pms.property.booking.save');
            Route::post('bookingbyproperty', [PMS\Booking\BookingController::class, 'bookingByProperty'])->name('pms.bookingbyproperty');
        });


        /*guestdatabase*/
        Route::group(['middleware' => ['auth', 'role:1,2,7']], function () {
            Route::get('guest/list', [PMS\GuestDatabase\GuestDatabaseController::class, 'index'])->name('pms.guestdatabase.list');
            Route::get('guest/database/export',[PMS\GuestDatabase\GuestDatabaseController::class,'exportToExcel'])->name('pms.guestdatabase.exportToExcel');
        });

        /*quotation*/
        Route::group(['middleware' => ['auth', 'role:1,2,5,7']], function () {
            Route::get('quotation/list', [PMS\Quotation\QuotationController::class, 'index'])->name('pms.quotation.list');
            Route::delete('quotation/delete/{id}', [PMS\Quotation\QuotationController::class, 'delete'])->name('pms.quotation.delete');
            Route::get('quotation/form/{id?}', [PMS\Quotation\QuotationController::class, 'quotationCreate'])->middleware('check.quotation')->name('pms.quotation.form');
            Route::post('/property/search/ajax', [PMS\Quotation\QuotationController::class, 'ajaxSearch'])->name('property.search.ajax');
            Route::post('/quotation/save', [PMS\Quotation\QuotationController::class, 'saveBookingQuation'])->name('quotation.form.save');
        });

        /*Master*/
        Route::group(['middleware' => ['auth', 'role:1,2','check.parent']], function () {
            /*RuAmenitie*/
            Route::get('ru-amenities/list', [PMS\Master\RuAmenitieController::class, 'index'])->name('pms.ru_amenities.list');
            Route::put('ru-amenities/toggle-status/{id}', [PMS\Master\RuAmenitieController::class, 'toggleStatus'])->name('pms.ru_amenities.toggle-status');

            /*RuLocation*/
            Route::get('ru-location/list', [PMS\Master\RuLocationController::class, 'index'])->name('pms.ru_location.list');
            Route::put('ru-location/toggle-status/{id}', [PMS\Master\RuLocationController::class, 'toggleStatus'])->name('pms.ru_location.toggle-status');
            Route::delete('ru-location/delete/{id}', [PMS\Master\RuLocationController::class, 'delete'])->name('pms.ru_location.delete');
            Route::post('ru-location/multidelete', [PMS\Master\RuLocationController::class, 'multiDelete'])->name('pms.ru_location.multidelete');

            /*Setting*/
                /*gst setting*/
                Route::get('gst/setting', [PMS\Master\SettingController::class, 'setGST'])->name('pms.gst_setting.list');
                Route::put('gst/setting/toggle-status/{id}', [PMS\Master\SettingController::class, 'toggleStatus'])->name('pms.gst_setting.toggle-status');

                /*Website Markup*/
                Route::get('website/markup', [PMS\Master\SettingController::class, 'websiteMarkup'])->name('pms.website.markup.list');
                Route::post('website/markup/saves/{id?}', [PMS\Master\SettingController::class, 'WebsiteMarkupSave'])->name('pms.website.markup.save');

            /*Role*/
            Route::get('role/list', [PMS\Master\RoleController::class, 'index'])->name('pms.role.list');
            Route::get('role/form/{id?}', [PMS\Master\RoleController::class, 'form'])->name('pms.role.form');
            Route::post('role/save', [PMS\Master\RoleController::class, 'save'])->name('pms.role.save');
            Route::put('role/toggle-status/{id}', [PMS\Master\RoleController::class, 'toggleStatus'])->name('pms.role.toggle-status');
            Route::delete('role/delete/{id}', [PMS\Master\RoleController::class, 'delete'])->name('pms.role.delete');
            Route::post('role/multidelete', [PMS\Master\RoleController::class, 'multiDelete'])->name('pms.role.multidelete');

            /*Company*/
            Route::get('company/list', [PMS\Master\CompanyController::class, 'index'])->name('pms.company.list');
            Route::get('company/form/{id?}', [PMS\Master\CompanyController::class, 'form'])->name('pms.company.form');
            Route::post('company/save', [PMS\Master\CompanyController::class, 'save'])->name('pms.company.save');
            Route::put('company/toggle-status/{id}', [PMS\Master\CompanyController::class, 'toggleStatus'])->name('pms.company.toggle-status');
            Route::delete('company/delete/{id}', [PMS\Master\CompanyController::class, 'delete'])->name('pms.company.delete');
            Route::post('company/multidelete', [PMS\Master\CompanyController::class, 'multiDelete'])->name('pms.company.multidelete');

            /*Gst*/
            Route::get('gst/view', [PMS\Master\GstController::class, 'view'])->name('pms.gst.view');
            Route::get('gst/form', [PMS\Master\GstController::class, 'form'])->name('pms.gst.form');
            Route::post('gst/save', [PMS\Master\GstController::class, 'save'])->name('pms.gst.save');

            /*HomeType*/
            Route::get('hometype/list', [PMS\Master\HomeTypeController::class, 'index'])->name('pms.hometype.list');
            Route::get('hometype/form/{id?}', [PMS\Master\HomeTypeController::class, 'form'])->name('pms.hometype.form');
            Route::post('hometype/save', [PMS\Master\HomeTypeController::class, 'save'])->name('pms.hometype.save');
            Route::put('hometype/toggle-status/{id}', [PMS\Master\HomeTypeController::class, 'toggleStatus'])->name('pms.hometype.toggle-status');
            Route::delete('hometype/delete/{id}', [PMS\Master\HomeTypeController::class, 'delete'])->name('pms.hometype.delete');
            Route::post('hometype/multidelete', [PMS\Master\HomeTypeController::class, 'multiDelete'])->name('pms.hometype.multidelete');

            /*Location*/
            Route::get('location/list', [PMS\Master\LocationController::class, 'index'])->name('pms.location.list');
            Route::get('location/form/{id?}', [PMS\Master\LocationController::class, 'form'])->name('pms.location.form');
            Route::post('location/save', [PMS\Master\LocationController::class, 'save'])->name('pms.location.save');
            Route::put('location/toggle-status/{id}', [PMS\Master\LocationController::class, 'toggleStatus'])->name('pms.location.toggle-status');
            Route::put('location/show_home/toggle-status/{id}', [PMS\Master\LocationController::class, 'ShowHometoggleStatus'])->name('pms.location.show_home.toggle-status');
            Route::delete('location/delete/{id}', [PMS\Master\LocationController::class, 'delete'])->name('pms.location.delete');
            Route::post('location/multidelete', [PMS\Master\LocationController::class, 'multiDelete'])->name('pms.location.multidelete');
            
             /*Coupon*/
            Route::get('coupon/list', [PMS\Coupons\CouponController::class, 'index'])->name('pms.coupon.list');
            Route::get('coupon/form/{id?}', [PMS\Coupons\CouponController::class, 'form'])->name('pms.coupon.form');
            Route::post('coupon/save', [PMS\Coupons\CouponController::class, 'save'])->name('pms.coupon.save');

            Route::post('coupon/get-properties', [PMS\Coupons\CouponController::class, 'getPropertiesByHomeType'])->name('pms.coupon.get-properties');

            Route::put('coupon/toggle-status/{id}', [PMS\Coupons\CouponController::class, 'toggleStatus'])->name('pms.coupon.toggle-status');
            Route::delete('coupon/delete/{id}', [PMS\Coupons\CouponController::class, 'delete'])->name('pms.coupon.delete');
            Route::post('coupon/multidelete', [PMS\Coupons\CouponController::class, 'multiDelete'])->name('pms.coupon.multidelete');
            
            
            /*PrivacyPolicy*/
            Route::get('privacy/policy/form', [PMS\Master\PrivacyPolicyController::class, 'form'])->name('pms.privacy.policy.form');
            Route::post('privacy/policy/save', [PMS\Master\PrivacyPolicyController::class, 'save'])->name('pms.privacy.policy.save');
            
             /*RefundPolicy*/
            Route::get('refund/policy/form', [PMS\Master\CancellationRefundConroller::class, 'form'])->name('pms.refund.policy.form');
            Route::post('refund/policy/save', [PMS\Master\CancellationRefundConroller::class, 'save'])->name('pms.refund.policy.save');
            
             /*aboutus*/
            Route::get('aboutus/form', [PMS\Master\AboutusConroller::class, 'form'])->name('pms.aboutus.form');
            Route::post('aboutus/save', [PMS\Master\AboutusConroller::class, 'save'])->name('pms.aboutus.save');
             /*Service*/
            Route::get('service/form', [PMS\Master\ServiceController::class, 'form'])->name('pms.service.form');
            Route::post('service/save', [PMS\Master\ServiceController::class, 'save'])->name('pms.service.save');
            /*Team*/
             Route::get('team/list', [PMS\Master\TeamConroller::class, 'index'])->name('pms.team.list');
            Route::get('team/form/{id?}', [PMS\Master\TeamConroller::class, 'form'])->name('pms.team.form');
            Route::post('team/save', [PMS\Master\TeamConroller::class, 'save'])->name('pms.team.save');
            Route::put('team/toggle-status/{id}', [PMS\Master\TeamConroller::class, 'toggleStatus'])->name('pms.team.toggle-status');
            Route::delete('team/delete/{id}', [PMS\Master\TeamConroller::class, 'delete'])->name('pms.team.delete');
            Route::post('team/multidelete', [PMS\Master\TeamConroller::class, 'multiDelete'])->name('pms.team.multidelete');
            Route::post('team/position', [PMS\Master\TeamConroller::class, 'savePosition'])->name('pms.team.position');


            /*TermCondition*/
            Route::get('term/condition/form', [PMS\Master\TermConditionController::class, 'form'])->name('pms.term.condition.form');
            Route::post('term/condition/save', [PMS\Master\TermConditionController::class, 'save'])->name('pms.term.condition.save');

            /*FaqCategory*/
            Route::get('faqcategory/list', [PMS\Master\FaqCategoryController::class, 'index'])->name('pms.faqcategory.list');
            Route::get('faqcategory/form/{id?}', [PMS\Master\FaqCategoryController::class, 'form'])->name('pms.faqcategory.form');
            Route::post('faqcategory/save', [PMS\Master\FaqCategoryController::class, 'save'])->name('pms.faqcategory.save');
            Route::put('faqcategory/toggle-status/{id}', [PMS\Master\FaqCategoryController::class, 'toggleStatus'])->name('pms.faqcategory.toggle-status');
            Route::delete('faqcategory/delete/{id}', [PMS\Master\FaqCategoryController::class, 'delete'])->name('pms.faqcategory.delete');
            Route::post('faqcategory/multidelete', [PMS\Master\FaqCategoryController::class, 'multiDelete'])->name('pms.faqcategory.multidelete');

            /*Faq*/
            Route::get('faq/list', [PMS\Master\FaqController::class, 'index'])->name('pms.faq.list');
            Route::get('faq/form/{id?}', [PMS\Master\FaqController::class, 'form'])->name('pms.faq.form');
            Route::post('faq/save', [PMS\Master\FaqController::class, 'save'])->name('pms.faq.save');
            Route::put('faq/toggle-status/{id}', [PMS\Master\FaqController::class, 'toggleStatus'])->name('pms.faq.toggle-status');
            Route::delete('faq/delete/{id}', [PMS\Master\FaqController::class, 'delete'])->name('pms.faq.delete');
            Route::post('faq/multidelete', [PMS\Master\FaqController::class, 'multiDelete'])->name('pms.faq.multidelete');

            /*Amenities*/
            Route::get('amenities/list', [PMS\Master\AmenitiesController::class, 'index'])->name('pms.amenities.list');
            Route::get('amenities/form/{id?}', [PMS\Master\AmenitiesController::class, 'form'])->name('pms.amenities.form');
            Route::post('amenities/save', [PMS\Master\AmenitiesController::class, 'save'])->name('pms.amenities.save');
            Route::put('amenities/toggle-status/{id}', [PMS\Master\AmenitiesController::class, 'toggleStatus'])->name('pms.amenities.toggle-status');
            Route::delete('amenities/delete/{id}', [PMS\Master\AmenitiesController::class, 'delete'])->name('pms.amenities.delete');
            Route::post('amenities/multidelete', [PMS\Master\AmenitiesController::class, 'multiDelete'])->name('pms.amenities.multidelete');

            /*BannerSlide*/
            Route::get('bannerslide/list', [PMS\Master\BannerSlideController::class, 'index'])->name('pms.bannerslide.list');
            Route::get('bannerslide/form/{id?}', [PMS\Master\BannerSlideController::class, 'form'])->name('pms.bannerslide.form');
            Route::post('bannerslide/save', [PMS\Master\BannerSlideController::class, 'save'])->name('pms.bannerslide.save');
            Route::put('bannerslide/toggle-status/{id}', [PMS\Master\BannerSlideController::class, 'toggleStatus'])->name('pms.bannerslide.toggle-status');
            Route::delete('bannerslide/delete/{id}', [PMS\Master\BannerSlideController::class, 'delete'])->name('pms.bannerslide.delete');
            Route::post('bannerslide/multidelete', [PMS\Master\BannerSlideController::class, 'multiDelete'])->name('pms.bannerslide.multidelete');
            Route::post('bannerslide/position', [PMS\Master\BannerSlideController::class, 'savePosition'])->name('pms.bannerslide.position');
            
            /*collection*/
            Route::get('collection/list', [PMS\Master\CollectionController::class, 'index'])->name('pms.collection.list');
            Route::get('collection/form/{id?}', [PMS\Master\CollectionController::class, 'form'])->name('pms.collection.form');
            Route::post('collection/save', [PMS\Master\CollectionController::class, 'save'])->name('pms.collection.save');
            Route::put('collection/toggle-status/{id}', [PMS\Master\CollectionController::class, 'toggleStatus'])->name('pms.collection.toggle-status');
            Route::delete('collection/delete/{id}', [PMS\Master\CollectionController::class, 'delete'])->name('pms.collection.delete');
            Route::post('collection/multidelete', [PMS\Master\CollectionController::class, 'multiDelete'])->name('pms.collection.multidelete');
            Route::post('collection/position', [PMS\Master\CollectionController::class, 'savePosition'])->name('pms.collection.position');
            Route::put('collection/show_home/toggle-status/{id}', [PMS\Master\CollectionController::class, 'ShowHomecollectionStatus'])->name('pms.collection.show_home.toggle-status');
            
            /*landing*/
            Route::get('landing/list', [PMS\Master\LandingController::class, 'index'])->name('pms.landing.list');
            Route::get('landing/form/{id?}', [PMS\Master\LandingController::class, 'form'])->name('pms.landing.form');
            Route::post('landing/save', [PMS\Master\LandingController::class, 'save'])->name('pms.landing.save');
            Route::put('landing/toggle-status/{id}', [PMS\Master\LandingController::class, 'toggleStatus'])->name('pms.landing.toggle-status');
            Route::delete('landing/delete/{id}', [PMS\Master\LandingController::class, 'delete'])->name('pms.landing.delete');
            Route::post('landing/multidelete', [PMS\Master\LandingController::class, 'multiDelete'])->name('pms.landing.multidelete');
            Route::post('landing/position', [PMS\Master\LandingController::class, 'savePosition'])->name('pms.landing.position');
            
            
             /*Blogs*/
            Route::get('blog/list', [PMS\Master\BlogController::class, 'index'])->name('pms.blog.list');
            Route::get('blog/form/{id?}', [PMS\Master\BlogController::class, 'form'])->name('pms.blog.form');
            Route::post('blog/save', [PMS\Master\BlogController::class, 'save'])->name('pms.blog.save');
            Route::put('blog/toggle-status/{id}', [PMS\Master\BlogController::class, 'toggleStatus'])->name('pms.blog.toggle-status');
            Route::delete('blog/delete/{id}', [PMS\Master\BlogController::class, 'delete'])->name('pms.blog.delete');
            Route::post('blog/multidelete', [PMS\Master\BlogController::class, 'multiDelete'])->name('pms.blog.multidelete');
            Route::post('blog/position', [PMS\Master\BlogController::class, 'savePosition'])->name('pms.blog.position');
            Route::put('blog/show_home/toggle-status/{id}', [PMS\Master\BlogController::class, 'ShowHomeblogStatus'])->name('pms.blog.show_home.toggle-status');
            
             /*Special Offers*/
            Route::get('specialoffer/list', [PMS\Master\SpecialOfferController::class, 'index'])->name('pms.specialoffer.list');
            Route::get('specialoffer/form/{id?}', [PMS\Master\SpecialOfferController::class, 'form'])->name('pms.specialoffer.form');
            Route::post('specialoffer/save', [PMS\Master\SpecialOfferController::class, 'save'])->name('pms.specialoffer.save');
            Route::put('specialoffer/toggle-status/{id}', [PMS\Master\SpecialOfferController::class, 'toggleStatus'])->name('pms.specialoffer.toggle-status');
            Route::delete('specialoffer/delete/{id}', [PMS\Master\SpecialOfferController::class, 'delete'])->name('pms.specialoffer.delete');
            Route::post('specialoffer/multidelete', [PMS\Master\SpecialOfferController::class, 'multiDelete'])->name('pms.specialoffer.multidelete');
            Route::post('specialoffer/position', [PMS\Master\SpecialOfferController::class, 'savePosition'])->name('pms.specialoffer.position');
            Route::put('specialoffer/show_home/toggle-status/{id}', [PMS\Master\SpecialOfferController::class, 'ShowHomeblogStatus'])->name('pms.specialoffer.show_home.toggle-status');

            
            /*RolePermisson*/
            Route::get('rolepermission/list', [PMS\Master\RolePermissonController::class, 'index'])->name('pms.rolepermission.list');
            Route::get('rolepermission/form/{id?}', [PMS\Master\RolePermissonController::class, 'form'])->name('pms.rolepermission.form');
            Route::post('rolepermission/save', [PMS\Master\RolePermissonController::class, 'save'])->name('pms.rolepermission.save');
            Route::put('rolepermission/toggle-status/{id}', [PMS\Master\RolePermissonController::class, 'toggleStatus'])->name('pms.rolepermission.toggle-status');
            Route::delete('rolepermission/delete/{id}', [PMS\Master\RolePermissonController::class, 'delete'])->name('pms.rolepermission.delete');
            Route::post('rolepermission/multidelete', [PMS\Master\RolePermissonController::class, 'multiDelete'])->name('pms.rolepermission.multidelete');
            
            /*Agreement*/
            Route::get('agreement/list', [PMS\Master\AgreementController::class, 'index'])->name('pms.agreement.list');
            Route::get('agreement/form/{id?}', [PMS\Master\AgreementController::class, 'form'])->name('pms.agreement.form');
            Route::post('agreement/save', [PMS\Master\AgreementController::class, 'save'])->name('pms.agreement.save');
            Route::put('agreement/toggle-status/{id}', [PMS\Master\AgreementController::class, 'toggleStatus'])->name('pms.agreement.toggle-status');
            Route::delete('agreement/delete/{id}', [PMS\Master\AgreementController::class, 'delete'])->name('pms.agreement.delete');
            Route::post('agreement/multidelete', [PMS\Master\AgreementController::class, 'multiDelete'])->name('pms.agreement.multidelete');
             Route::get('/channel/manager', [PMS\Property\ExternalChannelController::class,'index'])->name('pms.extrnal.channel');

            
        /*End Master*/
        });

        /*Channel*/
       
        Route::get('/404', function () {
            return view('pms.layouts.404');
        })->name('pms.404');
        
    });


});