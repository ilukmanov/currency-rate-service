<?php

use Webman\Route;

Route::get('/api/currency-rate', [app\controller\CurrencyController::class, 'getCurrencyRate']);