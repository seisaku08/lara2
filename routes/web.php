<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpreadsheetController;
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
// Auth::routes(['verify' => true]);

//ログイン不要なページ
Route::get('ordermailsample', 'OrdermailController@view');
Route::get('shippingmailsample', 'ShippingmailController@view');
Route::get('ninedaymailsample', 'NineDayMailController@view');
Route::get('/helo', 'HeloController@view');
Route::get('/xlsdl',[SpreadsheetController::class, 'index']);
Route::get('/ajax','AjaxController@view');
Route::get('/ajax/show_all','AjaxController@showall');
Route::post('/download', [SpreadsheetController::class, 'download']);
Route::post('/invoicedl', [SpreadsheetController::class, 'invoice']);

//ログイン要・メール認証不要なページ
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
Route::match(['get','post'],'/ajaxpctool','AjaxpctoolController@view');
Route::get('/ajaxpctool/show_all','AjaxpctoolController@show_all');
Route::post('/ajaxpctool/checkday','AjaxpctoolController@checkday');
    Route::get('/dashboard', ['uses'=>'DashboardController@index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'pageback'])->name('profile.pageback');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// //ログイン要・メール認証要なページ
Route::middleware('verified')->group(function () {

    Route::match(['get','post'],'/pctool', 'pctoolController@view')->name('pctool');
    Route::match(['get','post'],'/sendto', 'SendtoController@view')->name('sendto');
    Route::get('/pctool.retry', 'pctoolController@retry')->name('pctool.retry');
    Route::get('/pctool/detail/{id}', 'pctoolController@detail');
    Route::post('/confirm', 'ConfirmController@post')->name('confirm');
    Route::post('/finish', 'FinishController@finish')->name('finish');

    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::post('/cart', 'CartController@view')->name('cart');
    Route::post('/addCart', 'CartController@addCart')->name('addCart');
    Route::post('/delCart', 'CartController@delCart')->name('delCart');
    
    Route::match(['get','post'],'/order', 'OrderController@list')->name('order.list');
    Route::get('/order/detail/{id}', 'OrderController@detail')->name('order.detail');
    Route::get('/order/changetome/{id}', 'OrderController@changetome')->name('order.changetome');
    Route::get('/order/changetojohn/{id}', 'OrderController@changetojohn')->name('order.changetojohn');
    Route::get('/order/edit/{id}', 'OrderController@edit')->name('order.edit');
    Route::get('/order/edit/del/{id}', 'OrderController@delpc')->name('order.delpc');
    Route::get('/order/edit/add/{id}', 'OrderController@addpc')->name('order.addpc');
    Route::post('/order/edit/addprocess/{id}', 'OrderController@addprocess')->name('order.addprocess');
    Route::post('/order/edit/delprocess/{id}', 'OrderController@delprocess')->name('order.delprocess');
    Route::put('/order/update/{id}', 'OrderController@update')->name('order.update');
    Route::delete('/order/destroy/{id}', 'OrderController@destroy')->name('order.destroy');
    
});

    // //ログイン要・ロール設定"daioh"
    // Route::group(['middleware' => ['auth', 'can:daioh']], function () {
        Route::get('/maintenance', 'MaintenanceController@index')->name('maintenance');
        Route::post('/maintenance/selorder', 'MaintenanceController@selorder')->name('selorder');
        Route::post('/maintenance/selpc', 'MaintenanceController@selpc')->name('selpc');

        Route::get('/shipping', 'ShippingController@index')->name('shipping');
        Route::match(['get','post'],'/shipping/order', 'ShippingController@order')->name('shipping.order');
        Route::match(['get','post'],'/shipping/invoice', 'ShippingController@invoice')->name('shipping.invoice');
        Route::match(['get','post'],'/shipping/return', 'ShippingController@return')->name('shipping.return');
        Route::match(['get','post'],'/shipping/back', 'ShippingController@back')->name('shipping.back');

    // });

Route::get('/adminlte', function () {
    return view('adminlte');
})->middleware(['auth', 'verified'])->name('adminlte');
require __DIR__.'/auth.php';
