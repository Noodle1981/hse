<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AngularController;

//Route::get('/', function () {
//    return view('welcome');
//});

//Route::get('login', function () {
//    return "Login required";
//});
Route::any('/', [AngularController::class, 'index']);
Route::any('/{any}', [AngularController::class, 'panel'])->where('any', '^(?!api).*$');