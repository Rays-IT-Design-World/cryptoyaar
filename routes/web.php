<?php

use App\Http\Controllers\Admin\AdminConroller;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MlmMemberController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SupersubCategory;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [HomeController::class, 'index']);
Route::post('/register', [HomeController::class, 'register'])->name('user.register');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact-us');
Route::get('/about', [HomeController::class, 'about'])->name('about-us');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy-policy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms-condition');
Route::get('/test', [HomeController::class, 'testing'])->name('testing');


Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/videodetails/{id}', [HomeController::class, 'videodetails'])->name('video-details');
Route::post('/contact-enquiry', [HomeController::class, 'enquirystore'])
    ->name('contact.enquiry.store');

Route::get('/captcha', [AuthController::class, 'captchagenerate'])
    ->name('captcha.generate');

Route::get('login', [AuthController::class, 'loginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');

Route::prefix('admin')->group(function () {

    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [AdminConroller::class, 'index'])->name('admin.dashboard');
        Route::get('creatorlist', [AdminConroller::class, 'creatorReq'])->name('admin.creatorlist');
        Route::put('/creator-status/{id}', [AdminConroller::class, 'updateStatus'])
            ->name('creator.status.update');
        Route::get('/users', [AdminConroller::class, 'userList'])->name('userlist');
        Route::get('/my-network', [AdminConroller::class, 'myNetwork'])->name('my.network');
        Route::post('/refund/{id}', [AdminConroller::class, 'refund'])->name('user.refund');
        Route::get('/users/create', [AdminConroller::class, 'usercreate'])->name('user.add');
        Route::post('/users/store', [AdminConroller::class, 'userStore'])->name('user.store');
        Route::post('/users/store-referral', [AdminConroller::class, 'userStoreReferral'])->name('user.store-referral');
        Route::post('/users/storeplan', [AdminConroller::class, 'userStoreplan'])->name('user.storeplan');
        Route::get('/users/{id}/edit', [AdminConroller::class, 'editUser'])->name('user.edit');
        Route::put('/users/{id}/update', [AdminConroller::class, 'updateUser'])->name('user.update');
        Route::delete('/users/{id}/destroy', [AdminConroller::class, 'destroyUser'])->name('user.destroy');
        Route::get('/enquiry', [AdminConroller::class, 'enquiryList'])->name('enquirylist');
        Route::get('/event/interested/', [AdminConroller::class, 'interested'])->name('event.interested');
        Route::get('/creator/list/', [AdminConroller::class, 'creatorList'])->name('creator.list');
        Route::delete('enquiry/{id}/destroy', [AdminConroller::class, 'enquirydestroy'])->name('enquiry.destroy');
        Route::get('/plans/purchase', [AdminConroller::class, 'planPurchase'])->name('plan.purchase');
        Route::get('/subscription/revenue', [AdminConroller::class, 'revenue'])->name('subscription.revenue');
        Route::delete('revenue/{id}/destroy', [AdminConroller::class, 'destroyrevenue'])->name('revenue.destroy');

        Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');
        // Role
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles/store', [RoleController::class, 'store'])->name('roles.store');

        Route::get('roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('roles/{id}/update', [RoleController::class, 'update'])->name('roles.update');

        Route::get('roles/{role}/permissions', [RoleController::class, 'permissionForm'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [RoleController::class, 'savePermissions'])->name('roles.permissions.save');

        // Permission
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::post('permissions/{permission}/toggle', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle');


        

        //member
        Route::get('members', [MemberController::class, 'index'])->name('members.index');
        Route::get('members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('members/store', [MemberController::class, 'store'])->name('members.store');

        Route::get('members/{id}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::post('members/{member}/update', [MemberController::class, 'update'])->name('members.update');
        Route::get('members/{member}/destroy', [MemberController::class, 'destroy'])->name('members.destroy');

        // Plans 
        Route::get('Plans', [PlanController::class, 'create'])->name('plan.create');
        Route::post('/plans', [PlanController::class, 'store'])
            ->name('plans.store');
        Route::get('plan/view', [PlanController::class, 'index'])->name('plan.index');
        Route::get('plans/{id}/edit', [PlanController::class, 'edit'])->name('plan.edit');

        Route::put('plans/{plan}/update', [PlanController::class, 'update'])->name('plan.update');
        Route::delete('plans/{id}', [PlanController::class, 'destroy'])
            ->name('plan.destroy');

        // Video's
        Route::get('/videos', [VideoController::class, 'index'])->name('admin.videos.index');
        Route::get('/videos/create', [VideoController::class, 'create'])->name('admin.videos.create');
        Route::post('/videos/store', [VideoController::class, 'store'])->name('admin.videos.store');
        Route::get('/videos/{id}/edit', [VideoController::class, 'edit'])->name('admin.videos.edit');
        Route::put('/videos/{id}/update', [VideoController::class, 'update'])->name('admin.videos.update');
        Route::delete('/videos/{id}/delete', [VideoController::class, 'destroy'])->name('admin.videos.destroy');

        Route::get('/get-subcategories/{id}', [VideoController::class, 'getSubCategories']);
        Route::get('/get-super-subcategories/{id}', [VideoController::class, 'getSuperSubCategories']);

        // likes
        Route::post('/videos/{videoId}/like', [LikeController::class, 'toggle'])
            ->name('admin.video.like');

        Route::get('/videos/like-test', [AdminConroller::class, 'likeTest'])
            ->name('videos.like.test');

        //  comment 
        Route::post('/comments/store', [CommentController::class, 'store'])
            ->name('admin.comments.store');

        Route::get('/videos/{videoId}/comments', [CommentController::class, 'index'])
            ->name('admin.comments.index');

        //Category
        Route::get('category', [CategoryController::class, 'index'])->name('category.index');
        Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');

        Route::get('category/{slug}/edit', [CategoryController::class, 'edit'])->name('category.edit');
        Route::post('category/{id}/update', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('category/{id}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');


        //subCategory
        Route::get('subcategory', [SubCategoryController::class, 'index'])->name('subcategory.index');
        Route::get('subcategory/create', [SubCategoryController::class, 'create'])->name('subcategory.create');
        Route::post('subcategory/store', [SubCategoryController::class, 'store'])->name('subcategory.store');


        Route::get('subcategory/{slug}/edit', [SubCategoryController::class, 'edit'])->name('subcategory.edit');
        Route::post('subcategory/{id}/update', [SubCategoryController::class, 'update'])->name('subcategory.update');
        Route::delete('subcategory/{id}/destroy', [SubCategoryController::class, 'destroy'])->name('subcategory.destroy');

        //supersubCategory
        Route::get('supersubcategory', [SupersubCategory::class, 'index'])->name('supersubcategory.index');
        Route::get('supersubcategory/create', [SupersubCategory::class, 'create'])->name('supersubcategory.create');
        Route::post('supersubcategory/store', [SupersubCategory::class, 'store'])->name('supersubcategory.store');

        Route::get('supersubcategory/{slug}/edit', [SupersubCategory::class, 'edit'])->name('supersubcategory.edit');
        Route::post('supersubcategory/{id}/update', [SupersubCategory::class, 'update'])->name('supersubcategory.update');
        Route::delete('supersubcategory/{id}/destroy', [SupersubCategory::class, 'destroy'])->name('supersubcategory.destroy');


        Route::get('subcategory/{slug}/edit', [SubCategoryController::class, 'edit'])->name('subcategory.edit');
        Route::post('subcategory/{id}/update', [SubCategoryController::class, 'update'])->name('subcategory.update');
        Route::delete('subcategory/{id}/destroy', [SubCategoryController::class, 'destroy'])->name('subcategory.destroy');

        //supersubCategory
        Route::get('supersubcategory', [SupersubCategory::class, 'index'])->name('supersubcategory.index');
        Route::get('supersubcategory/create', [SupersubCategory::class, 'create'])->name('supersubcategory.create');
        Route::post('supersubcategory/store', [SupersubCategory::class, 'store'])->name('supersubcategory.store');

        Route::get('supersubcategory/{slug}/edit', [SupersubCategory::class, 'edit'])->name('supersubcategory.edit');
        Route::post('supersubcategory/{id}/update', [SupersubCategory::class, 'update'])->name('supersubcategory.update');

        //events

        Route::get('events', [EventController::class, 'index'])->name('event.index');
        Route::get('events/create', [EventController::class, 'create'])->name('event.create');
        Route::post('events/store', [EventController::class, 'store'])->name('event.store');
        Route::get('events/edit/{id}', [EventController::class, 'edit'])->name('event.edit');
        Route::post('events/update/{id}', [EventController::class, 'update'])->name('event.update');
        Route::delete('events/{id}/destroy', [EventController::class, 'destroy'])->name('events.destroy');

        Route::get('faq', [FaqController::class, 'index'])->name('faq.index');
        Route::get('faq/create', [FaqController::class, 'create'])->name('faq.create');
        Route::post('faq/store', [FaqController::class, 'store'])->name('faq.store');
        Route::get('faq/edit/{id}', [FaqController::class, 'edit'])->name('faq.edit');
        Route::post('faq/update/{id}', [FaqController::class, 'update'])->name('faq.update');
        Route::delete('faq/delete/{id}', [FaqController::class, 'destroy'])->name('faq.destroy');
    });
});
