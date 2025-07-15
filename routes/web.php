<?php

use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeLogController;

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
Route::get('/timelog', [TimeLogController::class, 'index'])->name('timelog.index');
Route::post('/timelog/store', [TimeLogController::class, 'store'])->name('timelog.store');
Route::get('/timelog/list', [TimeLogController::class, 'logList'])->name('timelog.list');
Route::get('/timelog/data', [TimeLogController::class, 'logData'])->name('timelog.data');
Route::get('/timelog/edit/{date}', [TimeLogController::class, 'editLog'])->name('timelog.edit');
Route::post('/timelog/update/{date}', [TimeLogController::class, 'updateLog'])->name('timelog.update');
Route::delete('/timelog/delete/{date}', [TimeLogController::class, 'deleteLog'])->name('timelog.delete');
Route::get('/check-leave-date/{date}', [TimeLogController::class, 'checkLeaveDate']);


Route::get('leave', [LeaveController::class, 'index'])->name('leave.index');
Route::post('leave/store', [LeaveController::class, 'store'])->name('leave.store');
Route::get('/leave/list', [LeaveController::class, 'leaveList'])->name('leave.list');
Route::get('/leave/data', [LeaveController::class, 'leaveData'])->name('leave.data');
Route::delete('/leave/delete/{id}', [LeaveController::class, 'deleteLeave'])->name('leave.delete');

});
