<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuBookingController;
use App\Http\Controllers\RuLiveNotificationWebhookController;
use App\Http\Controllers\Api\PriceLabLiveNotificationController;
use App\Http\Controllers\PMS\Property\UnitOrMultiUnitController;


Route::any('ru/webhook/get/live/notification/{hash?}', [RuLiveNotificationWebhookController::class, 'getLiveNotificationWebhook']);
Route::any('ru/webhook/get/bookings/{hash?}', [RuBookingController::class, 'getBookingFromRu']);


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::any('get/sync/url/data', [PriceLabLiveNotificationController::class, 'getSyncUrlData'])->name('pricelabs.sync');
Route::any('get/hook/url/data', [PriceLabLiveNotificationController::class, 'getHookUrlData'])->name('pricelabs.hook');
Route::any('get/calendar/trigger/url/data', [PriceLabLiveNotificationController::class, 'getCalendarTriggerUrlData'])->name('pricelabs.calendar.trigger');
Route::post('/property/{id}/get-prices',[UnitOrMultiUnitController::class, 'getPrices']);
Route::post('/property/{id}/pricelabs-status', [UnitOrMultiUnitController::class, 'checkPricelabsStatus']);
Route::get('pricelab/integration', [PriceLabLiveNotificationController::class, 'callPriceLabIntegrationApi']);