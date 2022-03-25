<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\AssignmentController;

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

//Login
Route::post('login',[UserController::class, 'authenticate']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('Authenticate',[UserController::class, 'getAuthenticatedUser']);
});
//User
Route::post('user',[UserController::class, 'register']);
Route::get('validar',[UserController::class, 'validation']);
Route::put('user/update/{uuid}',[UserController::class, 'update']);
Route::get('user/listSA',[UserController::class, 'listSA']);
Route::get('user/listA',[UserController::class, 'listA']);
Route::get('user/edit/{uuid}',[UserController::class, 'edit']);
Route::delete('user/delete/{uuid}',[UserController::class, 'delete']);
//Roles
Route::get('rol',[RolController::class, 'list']);
//Teachers
Route::post('teacher',[TeacherController::class, 'register']);
Route::put('teacher/update/{uuid}',[TeacherController::class, 'update']);
Route::post('teacher/upload',[TeacherController::class, 'upload']);
Route::get('teacher/upload/{name}',[TeacherController::class, 'return_image']);
Route::get('teacher/list',[TeacherController::class, 'list']);
Route::get('teacher/edit/{uuid}',[TeacherController::class, 'edit']);
Route::delete('teacher/delete/{uuid}',[TeacherController::class, 'delete']);
//Students
Route::post('student',[StudentController::class, 'register']);
Route::put('student/update/{uuid}',[StudentController::class, 'update']);
Route::post('student/upload',[StudentController::class, 'upload']);
Route::get('student/upload/{name}',[StudentController::class, 'return_image']);
Route::get('student/list',[StudentController::class, 'list']);
Route::get('student/edit/{uuid}',[StudentController::class, 'edit']);
Route::delete('student/delete/{uuid}',[StudentController::class, 'delete']);
//Practice
Route::post('practice',[PracticeController::class, 'register']);
Route::put('practice/update/{uuid}',[PracticeController::class, 'update']);
Route::get('practice/list',[PracticeController::class, 'list']);
Route::get('practice/edit/{uuid}',[PracticeController::class, 'edit']);
Route::delete('practice/delete/{uuid}',[PracticeController::class, 'delete']);
//Activity
Route::post('activity',[ActivityController::class, 'register']);
Route::put('activity/update/{uuid}',[ActivityController::class, 'update']);
Route::get('activity/list',[ActivityController::class, 'list']);
Route::get('activity/edit/{uuid}',[ActivityController::class, 'edit']);
Route::delete('activity/delete/{uuid}',[ActivityController::class, 'delete']);
//Grade
Route::post('grade',[GradeController::class, 'register']);
Route::put('grade/update/{uuid}',[GradeController::class, 'update']);
Route::get('grade/list',[GradeController::class, 'list']);
Route::get('grade/edit/{uuid}',[GradeController::class, 'edit']);
Route::delete('grade/delete/{uuid}',[GradeController::class, 'delete']);
//Period
Route::post('period',[PeriodController::class, 'register']);
Route::put('period/update/{uuid}',[PeriodController::class, 'update']);
Route::get('period/list',[PeriodController::class, 'list']);
Route::get('period/edit/{uuid}',[PeriodController::class, 'edit']);
Route::delete('period/delete/{uuid}',[PeriodController::class, 'delete']);
//Assignment
Route::post('assignment',[AssignmentController::class, 'register']);
Route::put('assignment/update/{uuid}',[AssignmentController::class, 'update']);
Route::get('assignment/list',[AssignmentController::class, 'list']);
Route::get('assignment/edit/{uuid}',[AssignmentController::class, 'edit']);
Route::delete('assignment/delete/{uuid}',[AssignmentController::class, 'delete']);


