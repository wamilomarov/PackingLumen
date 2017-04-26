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

$app->get('/getCompanies', 'CompanyController@getCompaniesList');


$app->post('/registerWorker', 'CompanyController@registerWorker');

$app->post('/updateWorker', 'CompanyController@updateWorker');

$app->get('/deleteWorker/{id}', 'CompanyController@deleteWorker');

$app->post('/searchWorker', 'CompanyController@searchWorker');

$app->get('/getWorker/{id}', 'CompanyController@infoWorker');


$app->post('/addMachine', 'CompanyController@addMachine');

$app->post('/updateMachine', 'CompanyController@updateMachine');

$app->get('/deleteMachine/{id}', 'CompanyController@deleteMachine');

$app->get('/getMachine/{id}', 'CompanyController@infoMachine');


$app->post('/addNote', 'UserController@addNote');

$app->post('/updateNote', 'UserController@updateMachine');

$app->get('/deleteNote/{id}', 'UserController@deleteMachine');

$app->get('/getNote/{id}', 'UserController@infoMachine');


$app->post('/addMeeting', 'UserController@addMeeting');

$app->post('/updateMeeting', 'UserController@updateMeeting');

$app->get('/deleteMeeting/{id}', 'UserController@deleteMeeting');

$app->get('/getMeeting/{id}', 'UserController@infoMeeting');




$app->post('/info', ['middleware' => 'auth', 'uses' => 'Controller@info']);
