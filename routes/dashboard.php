<?php

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\Auth\AuthController;
use App\Http\Controllers\Dashboard\Auth\ForgotController;
use App\Http\Controllers\Dashboard\Auth\ResetPasswordController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\ContactsController;
use App\Http\Controllers\Dashboard\CountriesController;
use App\Http\Controllers\Dashboard\CouponsController;
use App\Http\Controllers\Dashboard\OrdersController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\ProductsController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;






Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/dashboard',
    'as' => 'dashboard.',
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });


    ############################### Auth Routes ############################################
    Route::get('login',       [AuthController::class, 'login'])->name('login');
    Route::post('login/post', [AuthController::class, 'loginPost'])->name('login.post');
    Route::post('logout',     [AuthController::class, 'logout'])->name('logout');

    ############################### Forgot Password Routes ############################################
    Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
        Route::get('email',          [ForgotController::class, 'showEmailForm'])->name('email');
        Route::post('email',         [ForgotController::class, 'sendOTP'])->name('sendOTP');
        Route::get('verify/{email}', [ForgotController::class, 'showOtpForm'])->name('showOtpForm');
        Route::post('verify',        [ForgotController::class, 'verifyOtp'])->name('verifyOtp');

        ############################### Reset Password Routes ############################################
        Route::get('reset/{email}',  [ResetPasswordController::class, 'showResetForm'])->name('resetForm');
        Route::post('reset',         [ResetPasswordController::class, 'resetPassword'])->name('reset');
    });

    ############################### Admin Routes ############################################
    Route::group(['middleware' => 'auth:admin'], function () {

        ############################### Auth Routes ############################################
        Route::get('home', [AuthController::class, 'home'])->name('home');
        Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
        Route::get('security', [ProfileController::class, 'security'])->name('security');
        Route::post('profile/update', [ProfileController::class, 'profileUpdate'])->name('profile.update');
        Route::post('profile/update/password', [ProfileController::class, 'profileUpdatePassword'])->name('profile.update.password');

        ############################### Role Routes ############################################
        Route::resource('roles', RoleController::class)->middleware('can:roles');
        ############################### End Role Routes ############################################

        ############################### Admin Routes ############################################
        Route::resource('admins',         AdminController::class)->middleware('can:admins');
        Route::get('admins/{id}/status', [AdminController::class, 'changeStatus'])->middleware('can:admins')->name('admin.changeStatus');
        ############################### End Amin Routes ############################################

        ############################### Users Routes ############################################
        Route::get('users',                  [UserController::class, 'index'])->middleware('can:users')->name('users.index');
        Route::get('user/profile/{id}',      [UserController::class, 'userProfile'])->middleware('can:users')->name('user.profile');
        ############################### End Users Routes #########################################


        ############################### COUNTRIES Routes ############################################
        Route::get('countries',     [CountriesController::class, 'index'])->middleware('can:countries')->name('countries');
        ############################### COUNTRIES Routes ############################################

        ############################### CATEGORIES Routes ############################################
        Route::get('categories',    [CategoriesController::class, 'index'])->middleware('can:categories')->name('categories');
        ############################### End CATEGORIES Routes ############################################

        ############################### PRODUCTS Routes ############################################
        Route::get('products',      [ProductsController::class, 'index'])->middleware('can:products')->name('products');
        ############################### End PRODUCTS Routes ############################################

        ############################### ORDERS Routes ############################################
        Route::get('orders',        [OrdersController::class, 'index'])->middleware('can:orders')->name('orders');
        Route::get('orders/{order}', [OrdersController::class, 'show'])->middleware('can:orders')->name('orders.show');
        Route::get('orders/{order}/print', [OrdersController::class, 'print'])->middleware('can:orders')->name('orders.print');
        Route::post('orders/{order}/status', [OrdersController::class, 'updateStatus'])->middleware('can:orders_change_status')->name('orders.status.update');
        ############################### End ORDERS Routes ############################################

        ############################### COUPONS Routes ############################################
        Route::get('coupons',       [CouponsController::class, 'index'])->middleware('can:coupons')->name('coupons');
        ############################### End COUPONS Routes ############################################

        ############################### CONTACTS Routes ############################################
        Route::get('contacts',      [ContactsController::class, 'index'])->middleware('can:contacts')->name('contacts');
        ############################### End CONTACTS Routes ############################################





        ############################### settings Routes ############################################
        Route::get('banners',             [SettingsController::class, 'banners'])->middleware('can:settings')->name('banners');
        Route::get('settings',            [SettingsController::class, 'genralSetting'])->middleware('can:settings')->name('settings');
        Route::get('abouts',              [SettingsController::class, 'aboutSetting'])->middleware('can:settings')->name('about.setting');
        Route::get('faqs',                [SettingsController::class, 'faqs'])->middleware('can:settings')->name('faqs.setting');
        Route::get('privacy',             [SettingsController::class, 'privacy'])->middleware('can:settings')->name('privacy.setting');
        Route::get('terms',               [SettingsController::class, 'terms'])->middleware('can:settings')->name('terms.setting');
        ############################### End settings Routes ############################################

    });
});
