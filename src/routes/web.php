<?php

use Illuminate\Support\Facades\Route;
use Plugins\ExamplePlugin\Http\Controllers\ExampleRecordController;

Route::middleware(['auth', 'set.locale'])->group(function () {
    Route::get('/example-plugin/records', [ExampleRecordController::class, 'index'])->name('example-plugin.examples.index');
    Route::get('/example-plugin/records/{record}', [ExampleRecordController::class, 'show'])->name('example-plugin.examples.show');
});
