<?php

use App\Livewire\Auth\ForgetPage;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrdersDetailPage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductdetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class);  
Route::get('categories', CategoriesPage::class);
Route::get('products', ProductsPage::class);
Route::get('cart', CartPage::class);
Route::get('/products/{product}', ProductdetailPage::class);
Route::get('/checkout', CheckoutPage::class);
Route::get('/myorderspage', MyOrdersPage::class);
Route::get('/myorders/{order}', MyOrdersDetailPage::class);
Route::get('/login', Login::class);
Route::get('register',RegisterPage::class);
Route::get('forgot', ForgetPage::class);
Route::get('resetpassword', ResetPasswordPage::class);
Route::get('cancel', CancelPage::class);
Route::get('/success', SuccessPage::class);