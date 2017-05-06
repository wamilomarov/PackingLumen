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


$app->group(['middleware' => 'auth'], function () use ($app) {


    $app->post('/logout', 'UserController@logout');

    $app->get('/getUserStatuses', [ 'middleware' => 'admin', 'uses' => 'UserController@getUserStatuses']);

    $app->post('/registerUser', 'UserController@register');

    $app->post('/updateUser', [ 'middleware' => 'admin', 'uses' => 'UserController@update']);

    $app->get('/deleteUser/{id}', [ 'middleware' => 'admin', 'uses' => 'UserController@delete']);

    $app->post('/searchUser', [ 'middleware' => 'admin', 'uses' => 'UserController@search']);

    $app->get('/getUser/{id}', [ 'middleware' => 'admin', 'uses' => 'UserController@info']);

    $app->get('/getUsers', [ 'middleware' => 'admin', 'uses' => 'UserController@getUsersList']);


    $app->post('/registerCompany', 'CompanyController@create');

    $app->post('/updateCompany', 'CompanyController@update');

    $app->get('/deleteCompany/{id}', 'CompanyController@delete');

    $app->get('/searchCompany', 'CompanyController@search');

    $app->get('/getCompany/{id}', 'CompanyController@info');

    $app->get('/getCompanies', 'CompanyController@getCompaniesList');


    $app->post('/registerWorker', 'WorkerController@register');

    $app->post('/updateWorker', 'WorkerController@update');

    $app->get('/deleteWorker/{id}', 'WorkerController@delete');

    $app->get('/searchWorker', 'WorkerController@search');

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

    $app->get('/getReminders', 'MeetingController@getReminders');

    $app->get('/getMeetings', 'MeetingController@getMeetings');

    $app->post('/addBrand', [ 'middleware' => 'admin', 'uses' => 'MachineController@addBrand']);

    $app->get('/getBrands', 'MachineController@getBrands');

    $app->get('/deleteBrand/{id}', 'MachineController@deleteBrand');

    $app->get('/searchBrandName', 'MachineController@searchBrandName');

    $app->get('/searchWorkerName', 'WorkerController@searchWorkerName');

});

$app->get('/password/{pass}', 'UserController@pass');

