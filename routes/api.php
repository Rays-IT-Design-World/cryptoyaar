<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankDetailController;
use App\Http\Controllers\Api\CreatorController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\API\ReferralController;
use App\Http\Controllers\API\RefundController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // after login 
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/whatsapp-updates', [ProfileController::class, 'whatsappUpdates']);
    Route::get('/expert-categories', [HomeController::class, 'expertCategories']);
    Route::post('/user-favourite', [HomeController::class, 'saveUserFavourite']);
    Route::post('/skip-category', [HomeController::class, 'skipCategory']);
    Route::post('/update-favourite-categories', [HomeController::class, 'updateUserFavourite']);
    Route::get('/get-favourite-categories', [HomeController::class, 'getUserFavourite']);
    Route::get('/direct-member', [HomeController::class, 'directMembers']);
    Route::get('/team-member-lists', [HomeController::class, 'teamMembers']);
    Route::get('/earning-list', [HomeController::class, 'earnings']);

    // category subcategory supersub category list 
    Route::get('/categoryList', [HomeController::class, 'getCategories']);
    Route::get('/subCategoryList/{category_id}/', [HomeController::class, 'subcategories']);
    Route::get('/supCategoryList/{category_id}/', [HomeController::class, 'Supsubcategories']);
    
    //  bank details add/update  list
    Route::post('/bank-details', [BankDetailController::class, 'store']);
    Route::get('/bank-details', [BankDetailController::class, 'show']);

    // become a creator
    Route::post('/become-creator', [CreatorController::class, 'submit']);
    Route::post('/create/video', [CreatorController::class, 'add_video']);
    Route::get('/creator-request-status', [CreatorController::class, 'status']);
    Route::get('/video/list', [CreatorController::class, 'myVideos']);
    Route::get('/videoViewsList', [CreatorController::class, 'viewsList']);
    
   


    //   plans plan purchase  faq
    Route::get('/plan-list', [HomeController::class, 'planfetch']);
    // Route::post('/create-order', [PaymentController::class, 'createOrder']);
    // Route::post('/verify-payment', [PaymentController::class, 'verifyPayment']);
    Route::post('/plan/purchase', [PlanController::class, 'purchase']);
    Route::get('/payment_history', [BankDetailController::class, 'purchaseHistory']);
    Route::get('/faqs', [HomeController::class, 'faqList']);

    // video  show in home
    Route::get('/video_list', [HomeController::class, 'Videos']);

    //Meetup 
    Route::get('/events', [HomeController::class, 'event']);
    Route::post('/event/interested', [HomeController::class, 'eventInterested']);
    

    // Refer & Earn
    Route::post('/user/referral', [ReferralController::class, 'referralUser']);
    Route::post('/refund', [RefundController::class, 'refund']);
    Route::get('/my-network', [ReferralController::class, 'myNetwork']);
});

Route::post('/send-otp',[AuthController::class,'sendOtp']);
Route::post('/verify-otp',[AuthController::class,'verifyOtp']);
    Route::post('/watch_time', [HomeController::class, 'storeWatchTime'])->name('store-watch-time');



