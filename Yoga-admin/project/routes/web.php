<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('')->group(function () {

    // Home page route: /site
    // Route::get('/', [WebsiteController::class, 'home'])->name('website.index');


    // Route::resource('stores', StoreController::class);


});


Auth::routes();




Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware(['guest:admin', 'PreventBackHistory'])->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('admin.login');
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/check', [AdminController::class, 'check'])->name('check');
        Route::get('login/locked', [LoginController::class, 'locked'])->middleware('auth')->name('locked');
        Route::post('login/locked', [LoginController::class, 'unlock'])->name('unlock');
    });

    Route::middleware(['auth:admin', 'PreventBackHistory'])->group(function () {
        Route::get('/home', [DashboardController::class, 'index'])->name('home');
        // User List
        Route::any('/user_list', [DashboardController::class, 'user_list'])->name('user_list');
        Route::get('user-details/{id}', [DashboardController::class, 'user_details'])->name('user_details');
        Route::get('/analytics/overview', [DashboardController::class, 'analytics2'])
            ->name('analytics.overview');

        Route::any('/create_user_list', [DashboardController::class, 'create_user_list'])->name('create_user_list');
        Route::post('/store_user_list', [DashboardController::class, 'store_user_list'])->name('store_user_list');
        Route::get('/edit_user_list/{id}', [DashboardController::class, 'edit_user_list'])->name('edit_user_list');
        Route::patch('/update_user_list/{id}', [DashboardController::class, 'update_user_list'])->name('update_user_list');
        Route::post('/delete_user_list', [DashboardController::class, 'delete_user_list'])->name('delete_user_list');
        // Coach List
        Route::any('/coach_list', [DashboardController::class, 'coach_list'])->name('coach_list');
        Route::get('/create_coach_list', [DashboardController::class, 'create_coach_list'])->name('create_coach_list');
        Route::post('/store_coach_list', [DashboardController::class, 'store_coach_list'])->name('store_coach_list');
        Route::get('/edit_coach_list/{id}', [DashboardController::class, 'edit_coach_list'])->name('edit_coach_list');
        Route::patch('/update_coach_list/{id}', [DashboardController::class, 'update_coach_list'])->name('update_coach_list');
        Route::post('/delete_coach_list', [DashboardController::class, 'delete_coach_list'])->name('delete_coach_list');

        // Yoga Categories
        Route::any('yoga_categories', [DashboardController::class, 'yoga_categories'])->name('yoga_categories');
        Route::get('/create_yoga_categories', [DashboardController::class, 'create_yoga_categories'])->name('create_yoga_categories');
        Route::post('/store_yoga_categories', [DashboardController::class, 'store_yoga_categories'])->name('store_yoga_categories');
        Route::get('/edit_yoga_categories/{id}', [DashboardController::class, 'edit_yoga_categories'])->name('edit_yoga_categories');
        Route::patch('/update_yoga_categories/{id}', [DashboardController::class, 'update_yoga_categories'])->name('update_yoga_categories');
        Route::post('/destroy_yoga_categories', [DashboardController::class, 'destroy_yoga_categories'])->name('destroy_yoga_categories');

        // Yoga Poses
        Route::any('yoga_poses', [DashboardController::class, 'yoga_poses'])->name('yoga_poses');
        Route::post('/toggle-routine-status', [DashboardController::class, 'toggleStatus'])->name('toggle_routine_status');

        Route::get('/create_yoga_poses', [DashboardController::class, 'create_yoga_poses'])->name('create_yoga_poses');
        Route::post('/store_yoga_poses', [DashboardController::class, 'store_yoga_poses'])->name('store_yoga_poses');
        Route::get('/edit_yoga_poses/{id}', [DashboardController::class, 'edit_yoga_poses'])->name('edit_yoga_poses');
        Route::patch('/update_yoga_poses/{id}', [DashboardController::class, 'update_yoga_poses'])->name('update_yoga_poses');
        Route::post('/destroy_yoga_poses', [DashboardController::class, 'destroy_yoga_poses'])->name('destroy_yoga_poses');

        // FAQ
        Route::any('faq', [DashboardController::class, 'faq'])->name('faq');
        Route::get('/create_faq', [DashboardController::class, 'create_faq'])->name('create_faq');
        Route::post('/store_faq', [DashboardController::class, 'store_faq'])->name('store_faq');
        Route::get('/edit_faq/{id}', [DashboardController::class, 'edit_faq'])->name('edit_faq');
        Route::patch('/update_faq/{id}', [DashboardController::class, 'update_faq'])->name('update_faq');
        Route::post('/destroy_faq', [DashboardController::class, 'destroy_faq'])->name('destroy_faq');


        // Package Users
        Route::any('package_users', [DashboardController::class, 'package_users'])->name('package_users');
        Route::get('/create_package_users', [DashboardController::class, 'create_package_users'])->name('create_package_users');
        Route::post('/store_package_users', [DashboardController::class, 'store_package_users'])->name('store_package_users');
        Route::get('/edit_package_users/{id}', [DashboardController::class, 'edit_package_users'])->name('edit_package_users');
        Route::patch('/update_package_users/{id}', [DashboardController::class, 'update_package_users'])->name('update_package_users');
        Route::post('/destroy_package_users', [DashboardController::class, 'destroy_package_users'])->name('destroy_package_users');

        // Yoga Pose Levels
        Route::any('yoga_pose_levels', [DashboardController::class, 'yoga_pose_levels'])->name('yoga_pose_levels');
        Route::get('/create_yoga_pose_levels', [DashboardController::class, 'create_yoga_pose_levels'])->name('create_yoga_pose_levels');
        Route::post('/store_yoga_pose_levels', [DashboardController::class, 'store_yoga_pose_levels'])->name('store_yoga_pose_levels');
        Route::get('/edit_yoga_pose_levels/{id}', [DashboardController::class, 'edit_yoga_pose_levels'])->name('edit_yoga_pose_levels');
        Route::patch('/update_yoga_pose_levels/{id}', [DashboardController::class, 'update_yoga_pose_levels'])->name('update_yoga_pose_levels');
        Route::post('/destroy_yoga_pose_levels', [DashboardController::class, 'destroy_yoga_pose_levels'])->name('destroy_yoga_pose_levels');

        //Practice Routines
        Route::any('practice_routines', [DashboardController::class, 'practice_routines'])->name('practice_routines');
        Route::get('practice-routine/{id}', [DashboardController::class, 'practice_routine_details'])->name('practice_routine_details');

        Route::get('/create_practice_routines', [DashboardController::class, 'create_practice_routines'])->name('create_practice_routines');
        Route::post('/store_practice_routines', [DashboardController::class, 'store_practice_routines'])->name('store_practice_routines');
        Route::get('/edit_practice_routines/{id}', [DashboardController::class, 'edit_practice_routines'])->name('edit_practice_routines');
        Route::patch('/update_practice_routines/{id}', [DashboardController::class, 'update_practice_routines'])->name('update_practice_routines');
        Route::post('/destroy_practice_routines', [DashboardController::class, 'destroy_practice_routines'])->name('destroy_practice_routines');

        Route::get('settings2', [SettingsController::class, 'settings2'])->name('settings2');
        Route::patch('settings2update', [SettingsController::class, 'settings2update'])->name('settings2update');

        Route::any('purchase_history', [DashboardController::class, 'purchase_history'])->name('purchase_history');
        Route::any('practice_yoga_coach_file', [DashboardController::class, 'practice_yoga_coach_file'])->name('practice_yoga_coach_file');
        Route::post('destroy_practice_yoga_coach_file', [DashboardController::class, 'destroy_practice_yoga_coach_file'])->name('destroy_practice_yoga_coach_file');
        Route::post('/delete_coachfile', [DashboardController::class, 'delete_coachfile'])->name('delete_coachfile');
        Route::any('video_zip_files', [DashboardController::class, 'video_zip_files'])->name('video_zip_files');
        Route::post('/delete_video_zip_files', [DashboardController::class, 'delete_video_zip_files'])->name('delete_video_zip_files');
        Route::post('destroy_video_zip_files', [DashboardController::class, 'destroy_video_zip_files'])->name('destroy_video_zip_files');
        Route::get('import_country',[SettingsController::class,'import_country'])->name('import_country');
        Route::post('store_import_country',[SettingsController::class,'store_import_country'])->name('store_import_country');
        Route::get('import_country2',[SettingsController::class,'import_country2'])->name('import_country2');
        Route::post('store_import_country2',[SettingsController::class,'store_import_country2'])->name('store_import_country2');
        Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/stafflist', [StaffController::class, 'index'])->name('stafflist')->middleware('role:staff');

        Route::patch('adminupdate/{id}', [StaffController::class, 'adminupdate'])->name('adminupdate');

        Route::get('/listrequest', [StaffController::class, 'index'])->name('listrequest');
        Route::resource('roles', RolesController::class)->middleware('role:roles');
        Route::resource('permissions', PermissionController::class);
        Route::resource('admins', StaffController::class)->middleware('role:staff');
        Route::any('adminedit/{id}', [StaffController::class, 'adminedit'])->name('admins.adminedit');
    });
});
