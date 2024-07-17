<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/users', [UserController::class, 'index']);
// });

Route::get('/', function(){
    return response()->json([
        'status' => false,
        'message' => 'Akses tidak diizinkan'
    ], 401);
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'index'])->middleware(['auth:sanctum', 'role']);
Route::post('/user', [UserController::class, 'store'])->middleware(['auth:sanctum', 'role']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::put('/user/{id}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'role']);
Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware(['auth:sanctum', 'role']);

Route::get('/usersData', [UserController::class, 'usersData'])->middleware(['auth:sanctum', 'role']);

Route::get('/books', [BookController::class, 'index']);
Route::post('/book', [BookController::class, 'store'])->middleware(['auth:sanctum', 'role']);
Route::get('/book/{id}', [BookController::class, 'show']);
Route::patch('/book/{id}', [BookController::class, 'update'])->middleware(['auth:sanctum', 'role']);
Route::delete('/book/{id}', [BookController::class, 'destroy'])->middleware(['auth:sanctum', 'role']);

Route::get('/booksData', [BookController::class, 'booksData'])->middleware(['auth:sanctum', 'role']);

Route::get('/home',[BookController::class, 'home']);

Route::get('/transactions', [TransactionController::class, 'index'])->middleware(['auth:sanctum', 'role']);
Route::post('/transaction', [TransactionController::class, 'store'])->middleware(['auth:sanctum', 'role']);
Route::get('/transaction/{id}', [TransactionController::class, 'show'])->middleware(['auth:sanctum', 'role']);
Route::patch('/transaction/{id}', [TransactionController::class, 'update'])->middleware(['auth:sanctum', 'role']);
Route::delete('/transaction/{id}', [TransactionController::class, 'destroy'])->middleware(['auth:sanctum', 'role']);

Route::get('/marks/{id}', [MarkController::class, 'index']);
// Route::post('/addMark', [MarkController::class, 'addMark']);
Route::post('/toggleMark', [MarkController::class, 'toggleMark']);
Route::get('/checkMark', [MarkController::class, 'checkMark']);


Route::get('/transactiondata', [TransactionController::class, 'dashboard']);
Route::get('/myBook/{id}', [TransactionController::class, 'myBook']);