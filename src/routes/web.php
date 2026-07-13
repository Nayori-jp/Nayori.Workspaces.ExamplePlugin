<?php

use Illuminate\Support\Facades\Route;
use Plugins\ExamplePlugin\Http\Controllers\ExampleRecordController;

Route::middleware(['auth', 'set.locale'])->group(function () {
    Route::get('/example-plugin/records', [ExampleRecordController::class, 'index'])->name('example-plugin.examples.index');
    Route::get('/example-plugin/records/create', [ExampleRecordController::class, 'create'])->name('example-plugin.examples.create');
    Route::post('/example-plugin/records', [ExampleRecordController::class, 'store'])->name('example-plugin.examples.store');
    Route::get('/example-plugin/records/{record}', [ExampleRecordController::class, 'show'])->name('example-plugin.examples.show');
    Route::get('/example-plugin/records/{record}/edit', [ExampleRecordController::class, 'edit'])->name('example-plugin.examples.edit');
    Route::put('/example-plugin/records/{record}', [ExampleRecordController::class, 'update'])->name('example-plugin.examples.update');
});
