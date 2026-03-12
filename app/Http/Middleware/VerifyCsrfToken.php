<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'pms/calendar/modal', 'pms/calendar/form/submit', 'pms/calendar/unblock/dates', 'pms/calendar/block/dates', 'pms/calendar/booking/form', 'pms/calendar/modal/booking/edit', 'pms/calendar/ajax/get/booking/price'
    ];
}
