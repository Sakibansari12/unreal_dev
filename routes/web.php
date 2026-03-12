<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website as Website;
use App\Http\Controllers\RuBookingController as RuBookingController;
use App\Http\Controllers\RuLiveNotificationWebhookController as RuLiveNotificationWebhookController;
use App\Http\Controllers\TestingController;

Route::get('/property-types', [TestingController::class, 'fetchRuPropertyTypes']);

Route::get('/', [Website\HomeController::class, 'index'])->name('index');
Route::get('/property-list', [Website\HomeController::class, 'propertyList'])->name('property-list');
Route::get('/landing/page/{slug}', [Website\HomeController::class, 'landing'])->name('landing.page');
Route::post('/landingenquire/submit', [Website\HomeController::class, 'landingenquireSave'])->name('landingenquire');
Route::get('/download-brochure/{id}/{pType}', [Website\HomeController::class, 'downloadBrochure'])->name('download.brochure');
Route::post('/enquire', [Website\HomeController::class, 'enquireSave'])->name('enquire');


Route::any('/razorpay/webhook/callback', [RazorpayController::class, 'handleWebhookCallBack'])->name('handle.razorpay.callback');


Route::get('blog', [Website\HomeController::class, 'blogs'])->name('blog');
Route::get('blog/detail/{slug}', [Website\HomeController::class, 'blogDetail'])->name('blog.detail');
Route::get('faq', [Website\HomeController::class, 'faq'])->name('faq');


Route::get('refund/policy', [Website\HomeController::class, 'refundPolicy'])->name('refund.policy');


Route::get('cookie-policy', [Website\AboutusController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('terms/condition', [Website\AboutusController::class, 'termsCondition'])->name('terms.condition');
Route::get('privacy/policy', [Website\AboutusController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('careers', [Website\AboutusController::class, 'careers'])->name('careers');
Route::get('/corporate-bookings', [Website\AboutusController::class, 'corporateBookings'])->name('corporate-bookings');
Route::get('/cancellation-policy', [Website\AboutusController::class, 'cancellation_refund'])->name('cancellation_refund');
Route::get('/customer-support', [Website\AboutusController::class, 'customerSupport'])->name('customer-support');
Route::get('/host-with-unreal-estate', [Website\AboutusController::class, 'hostwithnook'])->name('hostwithnook');


Route::get('location-property', [Website\HomeController::class, 'locationProperty'])->name('location-property');







Route::get('aboutus', [Website\AboutusController::class, 'aboutUs'])->name('about.us');
Route::post('/subscribe-store', [Website\AboutusController::class, 'SubscribeStore'])->name('subscribe-store');




Route::get('/listproperty', [Website\ListpropertyController::class, 'listProperty'])->name('list.property');
Route::post('/list-your-property/form/submit', [Website\ListpropertyController::class, 'store'])->name('list.property.submit');
Route::get('captcha', [Website\ListpropertyController::class, 'refreshCaptcha'])->name('refresh.captcha');

Route::get('/property-detail/{ptype}/{slug}', [Website\HomeController::class, 'propertyDetail'])->name('property-detail');



Route::get('/get/ajax/property/price', [Website\HomeController::class, 'PropertyPriceFilter']);

//--------Rental United-------------------------------------//
Route::get('set/booking/webhook', [RuBookingController::class, 'setBookingHandlerAPi']);
Route::get('set/live/notification/url', [RuLiveNotificationWebhookController::class, 'setLiveNotificationWebhook']);
Route::get('contactus', [Website\ContactusController::class, 'contactus'])->name('contactus.form');
Route::post('contactus/submit', [Website\ContactusController::class, 'contactussubmit'])->name('contactus.submit');
Route::post('contact/submit', [Website\ContactusController::class, 'contactSubmit'])->name('contact.submit');
Route::get('offers', [Website\HomeController::class, 'coupen'])->name('coupen');

Route::get('/quotation/detail/{id}', [Website\BookingQuotationController::class, 'bookingQuotationDetail'])->name('booking.quotation.detail');
Route::get('/quotation-property-detail/{ptype}/{slug}', [Website\BookingQuotationController::class, 'quotationPropertyDetail'])->name('quotation-property-detail');
Route::get('/quotation-property-book/{ptype}/{slug}', [Website\BookingQuotationController::class, 'bookProperty'])->name('quotation-property-book');

Route::get('/property-book', [Website\WebBookingController::class, 'bookProperty'])->name('property-book');
Route::post('property-booking-payment', [Website\WebBookingController::class, 'savePropertyBookingWebsite'])->name('property-booking-payment');
Route::get('/razorpay-payment-success', [Website\WebBookingController::class, 'handlePaymentBookingWebsite'])->name('property-booking-update');
Route::get('thankyou', [Website\WebBookingController::class, 'thankYouPage'])->name('payment.thankYou');
Route::get('payment-failure', [Website\WebBookingController::class, 'PaymentFaild'])->name('payment-failure');
Route::post('/booking/apply/coupon/code', [Website\WebBookingController::class, 'applyBookingCouponCode'])->name('booking.apply.coupon.code');

Route::get('/booking/list', [Website\WebBookingController::class, 'manageBookingList'])->name('manage.booking');
Route::post('/send-otp', [Website\WebBookingController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [Website\WebBookingController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/check-booking', [Website\WebBookingController::class, 'checkBooking'])->name('check.booking');

Route::get('/DayOfArrivalEmailCommandJob', function () {
    Artisan::call("DayOfArrivalEmailCommandJob");
    /// dd('success.');
});
Route::get('/DayOfDepartureEmailCommandJob', function () {
    Artisan::call("DayOfDepartureEmailCommandJob");
    //dd('success.');
});

Route::get('/create-invoice', function () {
    Artisan::call("CreateInvoiceEmailCommandJob");
    dd('Invoice generated successfully.');
});

Route::get('/get/missing/bookings', function () {
    Artisan::call("job:getRuBookings");
    /// dd('success.');
});
Route::get('/clear', function () {
    Artisan::call("optimize:clear");
    Artisan::call("migrate");
    dd('success., migrations done, caches cleared' );
});



Route::get('/login', [Website\FrontLoginController::class, 'index'])->name('customer.login');
Route::post('/userlogin', [Website\FrontLoginController::class, 'userlogin']);

Route::get('/signup', [Website\FrontLoginController::class, 'signup'])->name('customer.signup');
Route::post('/registerform', [Website\FrontLoginController::class, 'register']);

Route::get('/forgot-password', [Website\FrontLoginController::class, 'forgotpassword'])->name('forgotpassword');
Route::post('/submit-forgot-password', [Website\FrontLoginController::class, 'submitforgotpassword']);

Route::post('/logout', [Website\FrontLoginController::class, 'logout'])->name('logout');


Route::get('auth/redirect/google', [Website\GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/callback/google', [Website\GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');


// Route::post('/logout', [FrontLoginController::class, 'logout'])->name('logout');
Route::get('/about-us', [IndexController::class, 'aboutus'])->name('about_us');
//Route::get('/faqs', [IndexController::class, 'faqs'])->name('faqs');
//Route::get('/cancellation-policy', [IndexController::class, 'cancellation_refund'])->name('cancellation_refund');
//Route::get('/contact-us', [IndexController::class, 'contactus'])->name('contact_us');
Route::get('all-property', [IndexController::class, 'AllPropertyData'])->name('all-property');
//Route::post('/subscribe-store', [IndexController::class, 'SubscribeStore'])->name('subscribe-store');
Route::get('tag-property-list/{tag_name}', [IndexController::class, 'TagPropertyList'])->name('tag-property-list');
Route::get('see-all-property', [IndexController::class, 'seeAllPropertyData'])->name('see-all-property');
Route::get('location-all-property', [IndexController::class, 'locationAllProperty'])->name('location-all-property');
//Route::get('property-detail/{slug}', [IndexController::class, 'propertyDetail'])->name('property-detail');
Route::get('/my-account', [Website\FrontLoginController::class, 'myaccount'])->name('myaccount');
Route::post('/profilesubmit', [Website\FrontLoginController::class, 'profilesubmit'])->name('profilesubmit');
Route::get('/my-bookings', [Website\FrontLoginController::class, 'mybooking'])->name('mybooking');
Route::get('/change-password', [Website\FrontLoginController::class, 'changepassword'])->name('changepassword');
Route::post('/submitpassword', [Website\FrontLoginController::class, 'submitpassword'])->name('submitpassword');
Route::get('/forgot-password', [Website\FrontLoginController::class, 'forgotpassword'])->name('forgotpassword');
Route::post('/submitforgotpassword', [Website\FrontLoginController::class, 'submitforgotpassword'])->name('submitforgotpassword');
Route::get('/get-city/{id}', [Website\FrontLoginController::class, 'GetCity']);

Route::get('auth/redirect/google', [Website\GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/callback/google', [Website\GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

