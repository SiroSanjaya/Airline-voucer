<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Airline Voucher API. Use /api/check or /api/generate.']);
});
