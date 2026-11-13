<?php

use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', [EventController::class, 'show'])->name('event.show');

Route::post('/attendees', [AttendeeController::class, 'store'])
	->middleware('throttle:20,1')
	->name('attendees.store');

Route::put('/attendees/{attendee}', [AttendeeController::class, 'update'])
	->middleware('throttle:30,1')
	->name('attendees.update');

Route::middleware('guest')->group(function () {
	Route::get('/login', [SessionController::class, 'create'])->name('login');
	Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
	Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
	Route::delete('/attendees/{attendee}', [AttendeeController::class, 'destroy'])->name('attendees.destroy');
	Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
});
