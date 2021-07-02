<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;

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

Route::get('/encryptString', function () {
    $encrypted = Crypt::encryptString('Hello DevDojo,2017-08-01');
    print_r($encrypted);
    $decrypt = Crypt::decryptString($encrypted);
    print_r($decrypt);
});

Route::get('/getInfofromEncrypted', function () {
    $string = "eyJpdiI6InlaMGpPQ1ZlTFdHa1N2SXVQWkR3Vnc9PSIsInZhbHVlIjoiMktTL1FveEwydkJOVHJ1amRPdkh0YVh3OEthcURMUFBRQ1pmcDQ1MnA1UT0iLCJtYWMiOiIxOTQ2ZTIyZDg2ODYwNGUxNjAzMDNjOWY4MjBlMDE2MDFkM2JiNzRiNDY0OTMxOWVhZDcxMTBlNTQzYmIzYWQxIn0=";
    $decrypt = Crypt::decryptString($string);

    $explodedArray = explode(",", $decrypt);
    dd($explodedArray);
});
