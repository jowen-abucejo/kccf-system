<?php

use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramLevelController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\StudentTypeController;
use App\Http\Controllers\SubjectController;
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
            Route::post("", 'store');
            Route::get("{program}", 'show');
            Route::put("{program}", 'update');
            Route::delete("{program}", 'destroy');
        });

        Route::controller(ProgramLevelController::class)->prefix("program-levels")->group(function () {
            Route::get("", 'index');
        });

        Route::controller(SchoolSettingController::class)->prefix("school-settings")->group(function () {
            Route::get("", 'index');
            Route::post("", 'store');
            Route::put('{schoolSetting}', 'update');
        });

        Route::controller(StudentController::class)->prefix("students")->group(function () {
            Route::get("", 'index');
            Route::delete("{student}", 'destroy');
        });

        Route::controller(StudentRegistrationController::class)->prefix("student-registrations")->group(function () {
            Route::get("", 'index');
            Route::post("", 'store');
            Route::put("{studentRegistration}", 'update');
        });

        Route::controller(StudentTypeController::class)->prefix("student-types")->group(function () {
            Route::get("", 'index');
        });

        Route::controller(SubjectController::class)->prefix("subjects")->group(function () {
            Route::get("", 'index');
            Route::post("", 'store');
            Route::put("{subject}", "update");
            Route::delete("{subject}", "destroy");
        });

        Route::controller(TermController::class)->prefix("terms")->group(function () {
            Route::get("", 'index');
        });


        Route::controller(UserController::class)->prefix("users")->group(function () {
            Route::post("logout", 'logout');
            Route::post("validate-email", 'validateEmail');
        });
    });

Route::post("users/login", [UserController::class, "login"]); //issuing access token
