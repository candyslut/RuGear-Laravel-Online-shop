<?php

use App\Http\Controllers\{
    AddressController,
};
use Illuminate\Support\Facades\Route;

Route::post('/address-suggest', [AddressController::class, 'suggest']);