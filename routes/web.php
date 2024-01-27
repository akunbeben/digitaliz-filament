<?php

use App\Livewire\Pages\Tickets;
use App\Livewire\Pages\ViewTicket;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/', Tickets::class)->name('dashboard');
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__ . '/auth.php';

Route::get('{ticket}', ViewTicket::class)
    ->middleware(['auth'])
    ->name('tickets.show');
