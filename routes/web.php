<?php

use App\Http\Controllers\ProductionStateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductionStateController::class, 'index']);