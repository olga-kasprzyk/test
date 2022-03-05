<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{status}', [TicketController::class, 'index']);
Route::patch('/tickets/{ticket}', [TicketController::class, 'update']);

Route::get('/users/{email}/tickets', [TicketController::class, 'getTicketsByEmail']);
Route::get('/stats', [TicketController::class, 'getStats']);
