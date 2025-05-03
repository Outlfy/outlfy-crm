<?php

use Illuminate\Support\Facades\Route;
use Webkul\Outlfy\Http\Controllers\OutlfyController;

Route::prefix('outlfy')->group(function () {
    Route::get('', [OutlfyController::class, 'index'])->name('admin.outlfy.index');
});
