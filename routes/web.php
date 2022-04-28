<?php

use Illuminate\Support\Facades\Route;


Route::middleware('auth:web')->group( function () {

    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', 'DashboardController@show')
        ->name('dashboard');

    Route::resource('url-tracking', TrackingController::class, [
        'names' => [
            'index' => 'list-url-tracking',
            'store' => 'add-url-tracking',
            'destroy' => 'delete-url-tracking'
        ]
    ]);

});

Route::get('url-check', 'TrackingController@checkUrls');

require __DIR__.'/auth.php';
