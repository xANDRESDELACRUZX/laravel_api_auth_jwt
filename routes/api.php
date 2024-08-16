<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


//users como end point norrmal
Route::get('/user/list', [UserController::class, 'getList'] );
Route::get('/user/{id}', [UserController::class, 'getuserById'] );
Route::post('/user/create', [UserController::class, 'create'] );
Route::post('/user/update/{id}', [UserController::class, 'editUser'] );
Route::post('/user/status/{id}', [UserController::class, 'statedUser'] );

//user como archivos fuera del controler como base logic
Route::match(['get', 'post'], 'userRequest', [UserController::class, 'userRequest']);



