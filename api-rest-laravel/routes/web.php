<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Loading classes
use App\Http\Middleware\ApiAuthMiddleware;

// Testing routes
Route::get('/', function () {
    return view('welcome');
});

/*
Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = "<h2>HOLA</h2>";
    $texto .= 'Nombre: '.$nombre;
    return $texto;
});
*/

Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = "<h2>HOLA</h2>";
    $texto .= 'Nombre: '.$nombre;
    return view('pruebas', array(
        'texto' => $texto
    ));
});

Route::get('/animales', 'PruebasController@index');

Route::get('/test-orm', 'PruebasController@testORM');


// API routes

    // Common HTTP Methods
    /*
     * GET: get data or means
     * POST: save data or means, or make logic from a form
     * PUT: update data or means
     * DELETE: delete data or means 
     */

    // API test routes
    // Route::get('/user/tests', 'UserController@pruebas');
    // Route::get('/account/tests', 'AccountController@pruebas');
    // Route::get('/transaction/tests', 'TransactionController@pruebas');
    
    // UserController routes
    Route::post('/api/register', 'UserController@register');
    Route::post('/api/login', 'UserController@login');
    Route::put('/api/user/update', 'UserController@update');
    Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
    Route::get('/api/user/detail/{id}', 'UserController@detail');
    
    // AccountController routes
    Route::resource('/api/account', 'AccountController');
    Route::get('/api/account/user/{user_id}', 'AccountController@getAccountByUser');
    Route::put('/api/account/deposit/{account_id}', 'AccountController@deposit');
    Route::put('/api/account/extract/{account_id}', 'AccountController@extract');
    
    // TransactionController routes
    Route::get('/api/transaction/account/{account_id}', 'TransactionController@getTransactionsByAccount');
    Route::get('/api/convert', 'TransactionController@convert');