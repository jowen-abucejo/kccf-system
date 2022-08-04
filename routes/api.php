<?php

use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\StudentTypeController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(["auth:api"])
    ->prefix(env("API_VERSION"))
    ->group(function () {
        Route::controller(LevelController::class)->prefix("levels")->group(function () {
            Route::get("", 'index');
        });

        Route::controller(ProgramController::class)->prefix("programs")->group(function () {
            Route::get("", 'index');
        });

        Route::controller(SchoolSettingController::class)->prefix("school-settings")->group(function () {
            Route::get("", 'index');
            Route::post("school-setting", 'store');
        });

        Route::controller(StudentController::class)->prefix("students")->group(function () {
            Route::get("", 'index');
        });

        Route::controller(StudentRegistrationController::class)->prefix("student-registrations")->group(function () {
            Route::get("", 'index');
            Route::post("registration", 'store');
            Route::put("registration/{studentRegistration}", 'update');
        });

        Route::controller(StudentTypeController::class)->prefix("student-types")->group(function () {
            Route::get("", 'index');
        });

        Route::controller(TermController::class)->prefix("terms")->group(function () {
            Route::get("", 'index');
        });


        Route::controller(UserController::class)->prefix("users")->group(function () {
            Route::post("user/logout", 'login');
            Route::post("validate-email", 'validateEmail');
        });
    });

Route::post("users/user/login", [UserController::class, "login"]); //issuing access token
