<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('welcome');
});



// task route group list
Route::prefix('tasks')->name('task.')->group(function () {
    Route::get('/', [TaskController::class, 'getTaskList'])->name('taskList');         // GET /tasks
    Route::post('/', [TaskController::class, 'createTask'])->name('createTask');       // POST /tasks
    Route::delete('{id}', [TaskController::class, 'deleteTask'])->name('deleteTask');  // DELETE /tasks/{id}
    Route::get('{id}', [TaskController::class, 'showSingleTask'])->name('showSingleTask'); // GET /tasks/{id}
});


// user plan group list
Route::prefix('user')->name('user.')->group(function () {
    Route::get('plan', [UserController::class, 'plan'])->name('plan'); //GET  /user/plan
    Route::post('/', [UserController::class, 'addNewUser'])->name('addNewUser'); //POST  /user/plan
});

// payment route group list
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/initialize', [TransactionController::class, 'initializePayment'])->name('initializePayment'); // POST /payment/initialize 

    //callback hook for payment service gateway
    Route::post('/verify', [TransactionController::class, 'verifyPayment'])->name('verifyPayment'); // POST /payment/verify 
});
