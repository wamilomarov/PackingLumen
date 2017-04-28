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

$app->post('/login', 'UserController@login');

$app->post('/logout', 'UserController@logout');

$app->post('/registerUser', 'UserController@register');

$app->post('/updateUser', 'UserController@update');

$app->get('/deleteUser/{id}', 'UserController@delete');

$app->post('/searchUser', 'UserController@search');

$app->get('/getUser/{id}', ['middleware' => 'auth', 'uses' => 'UserController@info']);


$app->post('/registerCompany', 'CompanyController@create');

$app->post('/updateCompany', 'CompanyController@update');

$app->get('/deleteCompany/{id}', 'CompanyController@delete');

$app->post('/searchCompany', 'CompanyController@search');

$app->get('/getCompany/{id}', 'CompanyController@info');

$app->get('/getCompanies', ['middleware' => 'auth', 'uses' => 'CompanyController@getCompaniesList']);


$app->post('/registerWorker', 'WorkerController@register');

$app->post('/updateWorker', 'WorkerController@update');

$app->get('/deleteWorker/{id}', 'WorkerController@delete');

$app->post('/searchWorker', 'WorkerController@search');

$app->get('/getWorker/{id}', 'WorkerController@info');


$app->post('/addMachine', 'MachineController@add');

$app->post('/updateMachine', 'MachineController@update');

$app->get('/deleteMachine/{id}', 'MachineController@delete');

$app->get('/getMachine/{id}', 'MachineController@info');


$app->post('/addNote', 'CompanyNoteController@add');

$app->post('/updateNote', 'CompanyNoteController@update');

$app->get('/deleteNote/{id}', 'CompanyNoteController@delete');

$app->get('/getNote/{id}', 'CompanyNoteController@info');


$app->post('/addMeeting', 'MeetingController@add');

$app->post('/updateMeeting', 'MeetingController@update');

$app->get('/deleteMeeting/{id}', 'MeetingController@delete');

$app->get('/getMeeting/{id}', 'MeetingController@info');

$app->get('/getReminders/{id}', 'MeetingController@getReminders');




$app->post('/info', ['middleware' => 'auth', 'uses' => 'Controller@info']);
