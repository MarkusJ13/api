<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/product', function () {
    return view('index');
});

$app->post('api/addquestion', 'QuestionController@addQuestion');
$app->post('api/getquestions', 'QuestionController@getQuestions');
$app->post('api/bookmark', 'QuestionController@bookmarkQuestion');
$app->post('api/view', 'QuestionController@viewQuestion');

$app->post('api/upvotequestion', 'QuestionController@upvoteQuestion');
$app->post('api/upvoteanswer', 'QuestionController@upvoteAnswer');
$app->post('api/answerquestion', 'QuestionController@answerQuestion');
$app->post('api/getanswers', 'QuestionController@getAnswers');

$app->post('api/delquestion', 'QuestionController@delQuestion');
$app->post('api/delanswer', 'QuestionController@delAnswer');

// APIs for notification
$app->get('api/notifications', 'QuestionController@seeNotification');

$app->post('api/saveaudio', 'FilehandleController@saveAudio');
$app->post('api/savetranscript', 'EnglishController@saveTranscript');
