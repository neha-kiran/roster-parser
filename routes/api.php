<?php
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/events', [EventController::class, 'index']); // Get all events
Route::get('/events/date-range', [EventController::class, 'getByDateRange']); // Events between dates
Route::get('/events/flights/next-week', [EventController::class, 'getFlightsForNextWeek']); // Flights for the next week
Route::get('/events/standby/next-week', [EventController::class, 'getStandbyForNextWeek']); // Standby for the next week
Route::get('/events/location', [EventController::class, 'getFlightsByLocation']); // Flights by location
Route::post('/events/upload', [EventController::class, 'upload']); // Upload roster file
