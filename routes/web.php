<?php

use App\Livewire\Web\Pages\About;
use App\Livewire\Web\Pages\Cart;
use App\Livewire\Web\Pages\Checkout;
use App\Livewire\Web\Pages\Contact;
use App\Livewire\Web\Pages\ForgotPassword;
use App\Livewire\Web\Pages\Home;
use App\Livewire\Web\Pages\ProductDescription;
use App\Livewire\Web\Pages\Profile;
use App\Livewire\Web\Pages\Signin;
use App\Livewire\Web\Pages\Signup;
use App\Livewire\Web\Pages\Thankyou;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
