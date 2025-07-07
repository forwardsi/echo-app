<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NfcController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\BartenderController;

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

Route::get('/test', function () {
    return 'Laravel routing works!!!!!!';
});
//SESSION ROUTES
//Route::get('/', [SessionController::class, 'checkSession']);//reading data from the URL parameters

//NFC ROUTES
Route::get('/getnfcdata', [NfcController::class, 'getNfcData']);//reading data from the URL parameters

/* ROUTES FOR CLIENTS */
Route::get('/client', [ClientController::class, 'index']);
Route::post('/client', [ClientController::class, 'store']);

/* ROUTES FOR USERS */
//Route::get('/', [UserController::class, 'index']);
Route::get('/viewOne', [UserController::class, 'returnViewOne']);
Route::post('/store', [UserController::class, 'store']);
Route::get('/question', [UserController::class, 'index_question']);

//api controller
Route::post('/generateReview', [ApiController::class, 'generateReview']);
Route::get('/googleAccounts', [ApiController::class, 'gettGoogleAccount']);
Route::get('/checkReview', [ApiController::class, 'fullGoogleReviewCheck']);
Route::get('/scanedReview', function () {
    return view('scanedReview');
})->name('scanedReview');

//extra
Route::get('/tryAgain/{currentProgress}', [ViewController::class, 'returnView']);



Route::get('/update-freebie-status', [ApiController::class, 'updateFreebieStatus']);

//manual check reviews
Route::get('/bartender/reviews/{hotelId}', [BartenderController::class, 'showReviewsForHotel']) ->name('bartender.reviews');
Route::post('/bartender/reviews/mark-as-posted', [BartenderController::class, 'markAsPosted']);
Route::post('/bartender/reviews/show-qr-code', [BartenderController::class, 'showQrCode']);
Route::get('bartender/manual-check', [BartenderController::class, 'manualCheck'])->name('manualCheck');



//extra question route
Route::get('/questionViewAgain', [ApiController::class, 'showQuestionareSecondTime']);

Route::get('/success', [ApiController::class, 'successPage'])->name('success.page');

