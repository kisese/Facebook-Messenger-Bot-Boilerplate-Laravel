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

use App\Quote;

Route::get('/', function () {
    return view('welcome');
});

//route for verification
Route::get("/quote", "MainController@receive")->middleware("verify");

//where Facebook sends messages to. No need to attach the middleware to this because the verification is via GET
Route::post("/quote", "MainController@receive");

Route::get('/preview', function (){
    $quote_data = Quote::all()->random(1)[0]->toArray();
    //clear any past solutions left in the cache
    return $quote_data;
});
