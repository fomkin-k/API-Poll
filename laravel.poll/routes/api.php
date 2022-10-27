<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Действия, доступные в режиме администратора, для них требуется авторизация
Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::resource('/polls','App\Http\Controllers\PollsController');

    Route::resource('/questions','App\Http\Controllers\QuestionsController');
    Route::get('/get_questions_by_poll_id/{id}','App\Http\Controllers\QuestionsController@get_questions_by_poll_id');

    Route::resource('/answers','App\Http\Controllers\AnswerController');
    Route::get('/get_answers_by_question_id/{id}','App\Http\Controllers\AnswerController@get_answers_by_question_id');
    Route::get('/answers_of_users','App\Http\Controllers\AnswerController@answers_of_users');

    Route::put('/logout','App\Http\Controllers\UsersController@logout');
});

//Действия, доступные всем пользователям
Route::post('/login','App\Http\Controllers\UsersController@login');
Route::get('/get_active_polls','App\Http\Controllers\PollsController@get_active_polls');
Route::get('/take_poll/{id}','App\Http\Controllers\PollsController@take_poll');
Route::post('/answer_question','App\Http\Controllers\AnswerController@answer_question');
Route::post('/complete_poll','App\Http\Controllers\PollsController@complete_poll');
Route::get('/get_completed_polls/{user_id}','App\Http\Controllers\PollsController@get_completed_polls');
Route::get('/get_completed_polls/{user_id}/{poll_id}','App\Http\Controllers\PollsController@get_completed_poll');

//Регистрация необходимая для создания пользователей в БД
Route::post('/register','App\Http\Controllers\UsersController@register');