<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('create/user', [UserController::class, 'create'] );
Route::get('/users', [UserController::class, 'getList'] );

