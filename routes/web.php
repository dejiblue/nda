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

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/', 'HomeController@index')->name('home');

//A form for customers to optin with their details
Route::get('/customers',  'CustomersController@index')->name('customers.index');
Route::post('/customers/contact/save', 'CustomersController@optin')->name('customers.contact.save');

// Ideally this routes will be protected by a middleware to ensure only logged in staffs can gain access
Route::get('/staff/import',  'StaffsController@index')->name('staff.import.index');
Route::post('/staff/import', 'StaffsController@parseImport')->name('staff.import.contacts');
