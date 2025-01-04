<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GenericCrudController;


// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// Users routes
Route::get('/users', [UserController::class, 'index']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Generic CRUD routes

Route::prefix('/generic-crud')->group(function () {
    Route::get('/', [GenericCrudController::class, 'index']); // Fetch all records
    Route::post('/', [GenericCrudController::class, 'store']); // Create a record
    Route::get('/{id}', [GenericCrudController::class, 'show']); // Fetch a single record
    Route::put('/{id}', [GenericCrudController::class, 'update']); // Update a record
    Route::delete('/{id}', [GenericCrudController::class, 'destroy']); // Soft delete a record
    Route::patch('/restore/{id}', [GenericCrudController::class, 'restore']); // Restore a soft-deleted record
});
