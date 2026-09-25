<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\MasterTwoController;
use App\Http\Controllers\Admin\AstrologerController;
use App\Http\Controllers\Admin\AstroShopProductController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrdersManagementController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Admin\TreatmentController;
use App\Http\Controllers\Admin\PatientController;
// use App\Http\Controllers\Api\DoctorController;


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

// Route::get('/', [UserController::class,'hi'])->name("webfront.index");
// Route::get('home', [HomeController::class, 'index'])->name('webfront.index');
Route::get('accept-invitation/{id}', [DoctorController::class, 'acceptInvitation'])->name('accept.invitation');
Auth::routes();

Route::prefix('user')->name('user.')->group(function () {

    Route::middleware(['guest:web', 'PreventBackHistory'])->group(function () {
        Route::view('/login', 'admin.login')->name('login');
        Route::view('/register', 'admin.register')->name('register');
        Route::post('/create', [UserController::class, 'create'])->name('create');
        Route::get('/signup_otp_page', [UserController::class, 'signup_otp_page'])->name('signup_otp_page');
        Route::post('/signup_otpmatch', [UserController::class, 'signup_otpmatch'])->name('signup_otpmatch');
        Route::post('/check', [UserController::class, 'check'])->name('check');
    });



    Route::middleware(['auth:astrologer-api', 'checkstatus'])->group(function () {

        //USers Api Ysf
        Route::post('flash/screen', [ClinicController::class, 'FlashScreen'])->name('flash.screen');
        Route::post('home-screen', [HomeController::class, 'OffersBanner'])->name('home-screen');
    });
    // Route::middleware(['auth:web','PreventBackHistory'])->group(function(){
    //     // Route::get('/home',[DashboardController::class,'index'])->name('home');
    //       Route::view('/home','admin.dashboard')->name('home');
    //       Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    //       Route::get('/add-new',[UserController::class,'add'])->name('add');


    //       //patient
    //     Route::get('patient',[PatientController::class,'patient'])->name('patient');

    //     //Treatment
    //     Route::get('treatment',[TreatmentController::class,'treatment'])->name('treatment');
    //     Route::get('create_treatment',[DoctorController::class,'create_treatment'])->name('create_treatment');
    //     Route::post('store_treatment',[DoctorController::class,'store_treatment'])->name('store_treatment');
    //     Route::get('edit_treatment/{id}',[DoctorController::class,'edit_treatment'])->name('edit_treatment');
    //     Route::patch('update_treatment/{id}',[DoctorController::class,'update_treatment'])->name('update_treatment');
    //     Route::post('destroy_treatment',[DoctorController::class,'destroy_treatment'])->name('destroy_treatment');

    //     //Doctor
    //     Route::get('doctor_team',[DoctorController::class,'doctor_team'])->name('doctor_team');
    //     Route::post('add_team',[DoctorController::class,'add_team'])->name('add_team');
    //     Route::get('doctor_list',[DoctorController::class,'doctor_list'])->name('doctor_list');
    //     Route::get('doctor_detail/{id}',[DoctorController::class,'doctor_detail'])->name('doctor_detail');
    //     Route::get('edit_doctor_detail/{id}',[DoctorController::class,'edit_doctor_detail'])->name('edit_doctor_detail');
    //     Route::post('update_doctor_detail/{id}',[DoctorController::class,'update_doctor_detail'])->name('update_doctor_detail');

    //     Route::get('about_dr/{id}',[DoctorController::class,'about_dr'])->name('about_dr');
    //     Route::get('about_create/{id}',[DoctorController::class,'about_create'])->name('about_create');
    //     Route::post('about_dr_add',[DoctorController::class,'about_dr_add'])->name('about_dr_add');
    //     Route::post('update_education/{id}',[DoctorController::class,'update_education'])->name('update_education');


    //     Route::get('create_education/{id}',[DoctorController::class,'create_education'])->name('create_education');
    //     Route::post('education_store',[DoctorController::class,'education_store'])->name('education_store');
    //     Route::get('education_edit/{id}',[DoctorController::class,'education_edit'])->name('education_edit');
    //     Route::post('update_about_dr/{id}',[DoctorController::class,'update_about_dr'])->name('update_about_dr');




    //     Route::get('add_professional/{id}',[DoctorController::class,'add_professional'])->name('add_professional');
    //     Route::post('add_professional_store',[DoctorController::class,'add_professional_store'])->name('add_professional_store');
    //     Route::get('professional_edit/{id}',[DoctorController::class,'professional_edit'])->name('professional_edit');
    //     Route::post('update_professional_dr/{id}',[DoctorController::class,'update_professional_dr'])->name('update_professional_dr');

    //     Route::get('award_create/{id}',[DoctorController::class,'award_create'])->name('award_create');
    //     Route::post('award_store',[DoctorController::class,'award_store'])->name('award_store');
    //     Route::get('award/{id}',[DoctorController::class,'award_edit'])->name('award');
    //     Route::post('update_award_dr/{id}',[DoctorController::class,'update_award_dr'])->name('update_award_dr');

    //     Route::get('medical_regis_create/{id}',[DoctorController::class,'medical_regis_create'])->name('medical_regis_create');
    //     Route::post('medical_regis_store',[DoctorController::class,'medical_regis_store'])->name('medical_regis_store');
    //     Route::get('medical_regis_edit/{id}',[DoctorController::class,'medical_regis_edit'])->name('medical_regis_edit');
    //     Route::post('update_medical_regis/{id}',[DoctorController::class,'update_medical_regis'])->name('update_medical_regis');


    //     Route::get('timing_dr_create/{id}',[DoctorController::class,'timing_dr_create'])->name('timing_dr_create');
    //     Route::post('timing_dr_store',[DoctorController::class,'timing_dr_store'])->name('timing_dr_store');
    //     Route::get('timing_dr_edit/{id}',[DoctorController::class,'timing_dr_edit'])->name('timing_dr_edit');
    //     Route::post('update_timing_dr/{id}',[DoctorController::class,'update_timing_dr'])->name('update_timing_dr');

    //     Route::group(['middleware' => ['role:doctor']], function () {

    //         Route::get('create_doctor',[DoctorController::class,'create_doctor'])->name('create_doctor');
    //         Route::post('store_testimonial',[DoctorController::class,'store_testimonial'])->name('store_testimonial');
    //         Route::get('edit_doctor/{id}',[DoctorController::class,'edit_doctor'])->name('edit_doctor');
    //         Route::patch('update_doctor/{id}',[DoctorController::class,'update_doctor'])->name('update_doctor');
    //         Route::get('import_doctor',[DoctorController::class,'import_doctor'])->name('import_doctor');
    //         Route::post('store_doctor',[DoctorController::class,'store_doctor'])->name('store_doctor');
    //         Route::post('destroy_doctor',[DoctorController::class,'destroy_doctor'])->name('destroy_doctor');
    //     });
    // });

});

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
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');



        //Doctor
        Route::get('doctor_team', [DoctorController::class, 'doctor_team'])->name('doctor_team');
        Route::post('add_team', [DoctorController::class, 'add_team'])->name('add_team');
        Route::get('doctor_list/{what?}', [DoctorController::class, 'doctor_list'])->name('doctor_list');
        Route::get('doctor_detail/{id}', [DoctorController::class, 'doctor_detail'])->name('doctor_detail');
        Route::get('edit_doctor_detail/{id}', [DoctorController::class, 'edit_doctor_detail'])->name('edit_doctor_detail');
        Route::post('update_doctor_detail/{id}', [DoctorController::class, 'update_doctor_detail'])->name('update_doctor_detail');
        Route::get('doctor_toggle_approve/{id}', [DoctorController::class, 'toggleApprove'])->name('doctor_toggle_approve');
        Route::get('doctor_toggle_reject/{id}', [DoctorController::class, 'toggleReject'])->name('doctor_toggle_reject');

        // Route::get('doctor_toggle_approve/{id}',[DoctorController::class,'toggleApprove'])->name('doctor_toggle_approve');

        Route::get('about_dr/{id}', [DoctorController::class, 'about_dr'])->name('about_dr');
        Route::get('about_create/{id}', [DoctorController::class, 'about_create'])->name('about_create');
        Route::post('about_dr_add', [DoctorController::class, 'about_dr_add'])->name('about_dr_add');
        Route::post('update_education/{id}', [DoctorController::class, 'update_education'])->name('update_education');


        Route::get('create_education/{id}', [DoctorController::class, 'create_education'])->name('create_education');
        Route::post('education_store', [DoctorController::class, 'education_store'])->name('education_store');
        Route::get('education_edit/{id}', [DoctorController::class, 'education_edit'])->name('education_edit');
        Route::post('update_about_dr/{id}', [DoctorController::class, 'update_about_dr'])->name('update_about_dr');




        Route::get('add_professional/{id}', [DoctorController::class, 'add_professional'])->name('add_professional');
        Route::post('add_professional_store', [DoctorController::class, 'add_professional_store'])->name('add_professional_store');
        Route::get('professional_edit/{id}', [DoctorController::class, 'professional_edit'])->name('professional_edit');
        Route::post('update_professional_dr/{id}', [DoctorController::class, 'update_professional_dr'])->name('update_professional_dr');

        Route::get('award_create/{id}', [DoctorController::class, 'award_create'])->name('award_create');
        Route::post('award_store', [DoctorController::class, 'award_store'])->name('award_store');
        Route::get('award/{id}', [DoctorController::class, 'award_edit'])->name('award');
        Route::post('update_award_dr/{id}', [DoctorController::class, 'update_award_dr'])->name('update_award_dr');

        Route::get('medical_regis_create/{id}', [DoctorController::class, 'medical_regis_create'])->name('medical_regis_create');
        Route::post('medical_regis_store', [DoctorController::class, 'medical_regis_store'])->name('medical_regis_store');
        Route::get('medical_regis_edit/{id}', [DoctorController::class, 'medical_regis_edit'])->name('medical_regis_edit');
        Route::post('update_medical_regis/{id}', [DoctorController::class, 'update_medical_regis'])->name('update_medical_regis');


        Route::get('timing_dr_create/{id}', [DoctorController::class, 'timing_dr_create'])->name('timing_dr_create');
        Route::post('timing_dr_store', [DoctorController::class, 'timing_dr_store'])->name('timing_dr_store');
        Route::get('timing_dr_edit/{id}', [DoctorController::class, 'timing_dr_edit'])->name('timing_dr_edit');
        Route::post('update_timing_dr/{id}', [DoctorController::class, 'update_timing_dr'])->name('update_timing_dr');


        Route::group(['middleware' => ['role:doctor']], function () {

            Route::get('create_doctor', [DoctorController::class, 'create_doctor'])->name('create_doctor');
            Route::post('store_testimonial', [DoctorController::class, 'store_testimonial'])->name('store_testimonial');
            Route::get('edit_doctor/{id}', [DoctorController::class, 'edit_doctor'])->name('edit_doctor');
            Route::patch('update_doctor/{id}', [DoctorController::class, 'update_doctor'])->name('update_doctor');
            Route::get('import_doctor', [DoctorController::class, 'import_doctor'])->name('import_doctor');
            Route::post('store_doctor', [DoctorController::class, 'store_doctor'])->name('store_doctor');
            Route::post('destroy_doctor', [DoctorController::class, 'destroy_doctor'])->name('destroy_doctor');
        });



        Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/stafflist', [StaffController::class, 'index'])->name('stafflist')->middleware('role:staff');

        Route::patch('adminupdate/{id}', [StaffController::class, 'adminupdate'])->name('adminupdate');



        Route::get('/listrequest', [StaffController::class, 'index'])->name('listrequest');
        Route::resource('roles', RolesController::class)->middleware('role:roles');
        Route::resource('permissions', PermissionController::class);
        Route::resource('admins', StaffController::class)->middleware('role:staff');
        Route::any('adminedit/{id}', [StaffController::class, 'adminedit'])->name('admins.adminedit');

        // Settings
        Route::group(['middleware' => ['role:settings']], function () {
            Route::resource('settings', SettingsController::class);
            Route::get('settings2', [SettingsController::class, 'settings2'])->name('settings2');
            Route::patch('settings2update', [SettingsController::class, 'settings2update'])->name('settings2update');
            Route::get('settings3', [SettingsController::class, 'settings3'])->name('settings3');
            Route::patch('settings3update', [SettingsController::class, 'settings3update'])->name('settings3update');
            // Route::get('settings4', [SettingsController::class, 'settings4'])->name('settings4')->middleware('role:call-config-setting');
            // Route::patch('settings4update', [SettingsController::class, 'settings4update'])->name('settings4update');
        });

        Route::prefix('Notification')->name('notification.')->group(function () {
            //notification
            Route::any('send_notification_to_user', [NotificationController::class, 'send_notification_to_user'])->name('send_notification_to_user')->middleware('role:user-notification');

            Route::get('notification_user', [NotificationController::class, 'notification_user'])->name('notification_user');
            Route::post('smt_notification', [NotificationController::class, 'smt_notification'])->name('smt_notification');
            Route::post('smt_notification_astrologer', [NotificationController::class, 'smt_notification_astrologer'])->name('smt_notification_astrologer');
            Route::post('curl_main_resource', [NotificationController::class, 'curl_main_resource'])->name('curl_main_resource');
            Route::any('send_notification_to_astrologer', [NotificationController::class, 'send_notification_to_astrologer'])->name('send_notification_to_astrologer')->middleware('role:astrologer-notification');
            Route::any('send_notification_to_astrologer_new', [NotificationController::class, 'send_notification_to_astrologer_new'])->name('send_notification_to_astrologer_new')->middleware('role:astrologer-notification');
            Route::get('send_notification_to_all', [NotificationController::class, 'send_notification_to_all'])->name('send_notification_to_all')->middleware('role:all-user-notification');
            Route::post('Asyncnotifytoall_admin', [NotificationController::class, 'asyncnotifytoall_admin'])->name('Asyncnotifytoall_admin');
        });

        //User Management
        Route::prefix('User-Management')->name('user.')->group(function () {

            Route::get('my-staff/{what?}', [DoctorController::class, 'mystaff'])->name('mystaff');
            Route::get('addmy-staff', [DoctorController::class, 'addmystaff'])->name('addmystaff');
            Route::post('store_staff', [DoctorController::class, 'store_staff'])->name('store_staff');
            Route::get('my-staff-detail/{id}', [DoctorController::class, 'mystaffdetail'])->name('mystaffdetail');
            Route::post('create_mystaff', [DoctorController::class, 'create_mystaff'])->name('create_mystaff');
            Route::post('import_mystaff', [DoctorController::class, 'import_mystaff'])->name('import_mystaff');
            Route::post('destroy_mystaff', [DoctorController::class, 'destroy_mystaff'])->name('destroy_mystaff');
            Route::post('store_mystaff', [DoctorController::class, 'store_mystaff'])->name('store_mystaff');
            Route::post('aboutupdate/{id}', [DoctorController::class, 'aboutupdate'])->name('aboutupdate');
            Route::post('professionalupdate/{id}', [DoctorController::class, 'professionalupdate'])->name('professionalupdate');
            Route::post('educationupdate/{id}', [DoctorController::class, 'educationupdate'])->name('educationupdate');
            Route::post('awardupdate/{id}', [DoctorController::class, 'awardupdate'])->name('awardupdate');
            Route::post('counsilupdate/{id}', [DoctorController::class, 'counsilupdate'])->name('counsilupdate');
            Route::post('timingupdate/{id}', [DoctorController::class, 'timingupdate'])->name('timingupdate');
            Route::post('profileupdate/{id}', [DoctorController::class, 'profileupdate'])->name('profileupdate');
            Route::post('update_status', [DoctorController::class, 'update_status'])->name('update_status');
            Route::get('my-profile-detail/{id?}',[DoctorController::class,'myprofiledetail'])->name('myprofiledetail');

            Route::get('staff_list/{id}', [DoctorController::class, 'staff_list'])->name('staff_list');
            Route::get('add_staff_list/{id}', [DoctorController::class, 'add_staff_list'])->name('add_staff_list');
            Route::post('store_staff_list', [DoctorController::class, 'store_staff_list'])->name('store_staff_list');
            Route::get('edit_staff_list/{id}', [DoctorController::class, 'edit_staff_list'])->name('edit_staff_list');
            Route::patch('update_staff_list/{id}', [DoctorController::class, 'update_staff_list'])->name('update_staff_list');
            Route::post('destroy_staff_list', [DoctorController::class, 'destroy_staff_list'])->name('destroy_staff_list');
        });

        Route::prefix('My-User-Management')->name('myuser.')->group(function () {

            Route::get('my-user', [DoctorController::class, 'myuserlist'])->name('myuserlist');
            Route::get('create_myuser', [DoctorController::class, 'create_myuser'])->name('create_myuser');
            Route::post('store_patient', [DoctorController::class, 'store_patient'])->name('store_patient');
            Route::get('edit_patient/{id}',[DoctorController::class,'edit_patient'])->name('edit_patient');
            Route::patch('update_patient/{id}',[DoctorController::class,'update_patient'])->name('update_patient');
            Route::get('patients-profile/{id}', [DoctorController::class, 'paitentprofile'])->name('paitentprofile');
            Route::get('prescription-view/{id}', [DoctorController::class, 'prescriptionView'])->name('prescriptionView');
            Route::get('dwnprescriptionView/{id}', [DoctorController::class, 'dwnprescriptionView'])->name('dwnprescriptionView');
        });



        Route::prefix('Package-Management')->name('package.')->group(function () {

            Route::get('packagelist',[DoctorController::class,'packagelist'])->name('packagelist');
            Route::get('create_package',[DoctorController::class,'create_package'])->name('create_package');
            Route::post('store_package',[DoctorController::class,'store_package'])->name('store_package');
            Route::get('edit_package/{id}',[DoctorController::class,'edit_package'])->name('edit_package');
            Route::patch('update_package/{id}',[DoctorController::class,'update_package'])->name('update_package');
            Route::post('destroy_package',[DoctorController::class,'destroy_package'])->name('destroy_package');
		});

        Route::prefix('user-subscription')->name('subscription.')->group(function () {

            Route::get('subscriptionlist',[DoctorController::class,'subscriptionlist'])->name('subscriptionlist');
            Route::get('create_subscription',[DoctorController::class,'create_subscription'])->name('create_subscription');
            Route::post('store_subscription',[DoctorController::class,'store_subscription'])->name('store_subscription');
            Route::get('edit_subscription/{id}',[DoctorController::class,'edit_subscription'])->name('edit_subscription');
            Route::any('update_subscription/{id}',[DoctorController::class,'update_subscription'])->name('update_subscription');
            Route::post('destroy_subscription',[DoctorController::class,'destroy_subscription'])->name('destroy_subscription');
            Route::post('subscription_update_status', [DoctorController::class, 'subscription_update_status'])->name('subscription_update_status');
            Route::get('subscription/detail/{id}', [DoctorController::class, 'packageDetail'])->name('maindetails');
		});
        

        Route::prefix('appointments')->name('appoint.')->group(function () {

            Route::get('my-calender', [DoctorController::class, 'mycalender'])->name('mycalender');
            Route::get('my-appointment', [DoctorController::class, 'myappointment'])->name('myappointment');
            Route::get('cancel-appointment', [DoctorController::class, 'cancelmyappointment'])->name('cancelmyappointment');

            Route::get('add-appointment', [DoctorController::class, 'addappointment'])->name('addappointment');
            Route::post('store_appointment', [DoctorController::class, 'store_appointment'])->name('store_appointment');
            Route::post('get-timeslot', [DoctorController::class, 'getslots'])->name('get-timeslot');
            Route::get('my-appointmentdetails/{id}', [DoctorController::class, 'myappointmentdetails'])->name('myappointmentdetails');
            Route::get('updatemy-appointment/{id}/{status}', [DoctorController::class, 'updatemyappointment'])->name('updatemyappointment');
            Route::get('/complete_appoint/{id}', [DoctorController::class, 'complete_appoint'])->name('complete_appoint');
            Route::get('/cancel_appoint/{id}', [DoctorController::class, 'cancel_appoint'])->name('cancel_appoint');

            Route::get('my-stats',[DoctorController::class,'mystats'])->name('mystats');
            Route::get('my-earninghistory',[DoctorController::class,'earninghistory'])->name('earninghistory');
            Route::get('my-loyalitypoints',[DoctorController::class,'loyalitypoints'])->name('loyalitypoints');
            Route::get('my-bestpatient',[DoctorController::class,'bestpatient'])->name('bestpatient');
            Route::get('my-docactivity',[DoctorController::class,'docactivity'])->name('docactivity');
            Route::get('my-treatmentperfom/{id?}',[DoctorController::class,'treatmentperfom'])->name('treatmentperfom');
            Route::get('my-notification',[DoctorController::class,'mynoti'])->name('mynoti');
            Route::get('my-graph/{id?}',[DoctorController::class,'vgraph'])->name('vgraph');
            Route::get('my-memberlist/{user_id?}',[DoctorController::class,'memberlist'])->name('memberlist');
            Route::get('my-plansold',[DoctorController::class,'plansold'])->name('plansold');
            Route::get('my-myappointmentcond/{cond?}',[DoctorController::class,'myappointmentcond'])->name('myappointmentcond');
        });

        // Masters
        Route::prefix('Main-Masters')->name('master.')->group(function () {
            Route::group(['middleware' => ['role:faq-s']], function () {
                Route::get('faq', [MasterController::class, 'faq'])->name('faq');
                Route::get('create_faq', [MasterController::class, 'create_faq'])->name('create_faq');
                Route::post('store_faq', [MasterController::class, 'store_faq'])->name('store_faq');
                Route::get('edit_faq/{id}', [MasterController::class, 'edit_faq'])->name('edit_faq');
                Route::patch('update_faq/{id}', [MasterController::class, 'update_faq'])->name('update_faq');
                Route::get('import_faq', [MasterController::class, 'import_faq'])->name('import_faq');
                Route::post('store_import_faq', [MasterController::class, 'store_import_faq'])->name('store_import_faq');
                Route::post('destroy_faq', [MasterController::class, 'destroy_faq'])->name('destroy_faq');
            });

            //blogs
            Route::group(['middleware' => ['role:blog-management']], function () {
                Route::get('blog', [MasterController::class, 'blog'])->name('blog');
                Route::get('create_blog', [MasterController::class, 'create_blog'])->name('create_blog');
                Route::post('store_blog', [MasterController::class, 'store_blog'])->name('store_blog');
                Route::get('edit_blog/{id}', [MasterController::class, 'edit_blog'])->name('edit_blog');
                Route::patch('update_blog/{id}', [MasterController::class, 'update_blog'])->name('update_blog');
                Route::get('import_blog', [MasterController::class, 'import_blog'])->name('import_blog');
                Route::post('store_import_blog', [MasterController::class, 'store_import_blog'])->name('store_import_blog');
                Route::post('destroy_blog', [MasterController::class, 'destroy_blog'])->name('destroy_blog');
            });

            //seasonal_offer
            Route::group(['middleware' => ['role:seasonal-offer']], function () {
                Route::get('seasonal_offer',[MasterController::class,'seasonal_offer'])->name('seasonal_offer');
                Route::get('create_seasonal_offer',[MasterController::class,'create_seasonal_offer'])->name('create_seasonal_offer');
                Route::post('store_seasonal_offer',[MasterController::class,'store_seasonal_offer'])->name('store_seasonal_offer');
                Route::get('edit_seasonal_offer/{id}',[MasterController::class,'edit_seasonal_offer'])->name('edit_seasonal_offer');
                Route::patch('update_seasonal_offer/{id}',[MasterController::class,'update_seasonal_offer'])->name('update_seasonal_offer');
                Route::get('import_seasonal_offer',[MasterController::class,'import_seasonal_offer'])->name('import_seasonal_offer');
                Route::post('store_import_seasonal_offer',[MasterController::class,'store_import_seasonal_offer'])->name('store_import_seasonal_offer');
                Route::post('destroy_seasonal_offer',[MasterController::class,'destroy_seasonal_offer'])->name('destroy_seasonal_offer');
            });

            //master_college_institutes
            Route::group(['middleware' => ['role:college-institutes']], function () {
                Route::get('master_college_institutes', [MasterController::class, 'master_college_institutes'])->name('master_college_institutes');
                Route::get('create_master_college_institutes', [MasterController::class, 'create_master_college_institutes'])->name('create_master_college_institutes');
                Route::post('store_master_college_institutes', [MasterController::class, 'store_master_college_institutes'])->name('store_master_college_institutes');
                Route::get('edit_master_college_institutes/{id}', [MasterController::class, 'edit_master_college_institutes'])->name('edit_master_college_institutes');
                Route::patch('update_master_college_institutes/{id}', [MasterController::class, 'update_master_college_institutes'])->name('update_master_college_institutes');
                Route::post('destroy_master_college_institutes', [MasterController::class, 'destroy_master_college_institutes'])->name('destroy_master_college_institutes');
            });


            //master_degrees
            Route::group(['middleware' => ['role:master-degrees']], function () {
                Route::get('master_degrees', [MasterController::class, 'master_degrees'])->name('master_degrees');
                Route::get('create_master_degrees', [MasterController::class, 'create_master_degrees'])->name('create_master_degrees');
                Route::post('store_master_degrees', [MasterController::class, 'store_master_degrees'])->name('store_master_degrees');
                Route::get('edit_master_degrees/{id}', [MasterController::class, 'edit_master_degrees'])->name('edit_master_degrees');
                Route::patch('update_master_degrees/{id}', [MasterController::class, 'update_master_degrees'])->name('update_master_degrees');
                Route::post('destroy_master_degrees', [MasterController::class, 'destroy_master_degrees'])->name('destroy_master_degrees');
            });


            //master_registraion_councils
            Route::group(['middleware' => ['role:registraion-councils']], function () {
                Route::get('master_registraion_councils', [MasterController::class, 'master_registraion_councils'])->name('master_registraion_councils');
                Route::get('create_master_registraion_councils', [MasterController::class, 'create_master_registraion_councils'])->name('create_master_registraion_councils');
                Route::post('store_master_registraion_councils', [MasterController::class, 'store_master_registraion_councils'])->name('store_master_registraion_councils');
                Route::get('edit_master_registraion_councils/{id}', [MasterController::class, 'edit_master_registraion_councils'])->name('edit_master_registraion_councils');
                Route::patch('update_master_registraion_councils/{id}', [MasterController::class, 'update_master_registraion_councils'])->name('update_master_registraion_councils');
                Route::post('destroy_master_registraion_councils', [MasterController::class, 'destroy_master_registraion_councils'])->name('destroy_master_registraion_councils');
            });


            //master_services
            Route::group(['middleware' => ['role:master-services']], function () {
                Route::get('master_services', [MasterController::class, 'master_services'])->name('master_services');
                Route::get('create_master_services', [MasterController::class, 'create_master_services'])->name('create_master_services');
                Route::post('store_master_services', [MasterController::class, 'store_master_services'])->name('store_master_services');
                Route::get('edit_master_services/{id}', [MasterController::class, 'edit_master_services'])->name('edit_master_services');
                Route::patch('update_master_services/{id}', [MasterController::class, 'update_master_services'])->name('update_master_services');
                Route::post('destroy_master_services', [MasterController::class, 'destroy_master_services'])->name('destroy_master_services');
            });

            //master_specialsations
            Route::group(['middleware' => ['role:master-services']], function () {
                Route::get('master_specialsations', [MasterController::class, 'master_specialsations'])->name('master_specialsations');
                Route::get('create_master_specialsations', [MasterController::class, 'create_master_specialsations'])->name('create_master_specialsations');
                Route::post('store_master_specialsations', [MasterController::class, 'store_master_specialsations'])->name('store_master_specialsations');
                Route::get('edit_master_specialsations/{id}', [MasterController::class, 'edit_master_specialsations'])->name('edit_master_specialsations');
                Route::patch('update_master_specialsations/{id}', [MasterController::class, 'update_master_specialsations'])->name('update_master_specialsations');
                Route::post('destroy_master_specialsations', [MasterController::class, 'destroy_master_specialsations'])->name('destroy_master_specialsations');
            });



            Route::get('notification_data', [MasterController::class, 'notification_data'])->name('notification_data');
            Route::group(['middleware' => ['role:manage-user']], function () {
                Route::get('manage_user', [MasterController::class, 'manage_user'])->name('manage_user')->middleware('role:admin-notification');

                Route::any('token_user', [MasterController::class, 'token_user'])->name('token_user');


                Route::get('create_user', [MasterController::class, 'create_user'])->name('create_user')->middleware('role:add-user');
                Route::post('store_user', [MasterController::class, 'store_user'])->name('store_user')->middleware('role:add-user');
                Route::get('edit_user/{id}', [MasterController::class, 'edit_user'])->name('edit_user')->middleware('role:edit-user');
                Route::get('recover_user/{id}', [MasterController::class, 'recover_user'])->name('recover_user')->middleware('role:edit-user');
                Route::patch('update_user/{id}', [MasterController::class, 'update_user'])->name('update_user')->middleware('role:edit-user');
                Route::get('import_applanguage', [MasterController::class, 'import_applanguage'])->name('import_applanguage');
                Route::post('store_import_applanguage', [MasterController::class, 'store_import_applanguage'])->name('store_import_applanguage');
                Route::post('destroy_user', [MasterController::class, 'destroy_user'])->name('destroy_user');
                Route::post('add_wallet', [MasterController::class, 'add_wallet'])->name('add_wallet')->middleware('role:add-wallet-user');
                Route::post('deduct_wallet', [MasterController::class, 'deduct_wallet'])->name('deduct_wallet')->middleware('role:nill-wallet-user');
                Route::get('users_detail/{id}', [MasterController::class, 'users_detail'])->name('users_detail');
            });

            Route::group(['middleware' => ['role:banners-management']], function () {
                Route::get('banner', [MasterController::class, 'banner'])->name('banner');
                Route::get('create_banner', [MasterController::class, 'create_banner'])->name('create_banner');
                Route::post('store_banner', [MasterController::class, 'store_banner'])->name('store_banner');
                Route::get('edit_banner/{id}', [MasterController::class, 'edit_banner'])->name('edit_banner');
                Route::patch('update_banner/{id}', [MasterController::class, 'update_banner'])->name('update_banner');
                Route::get('import_banner', [MasterController::class, 'import_banner'])->name('import_banner');
                Route::post('store_import_banner', [MasterController::class, 'store_import_banner'])->name('store_import_banner');
                Route::post('destroy_banner', [MasterController::class, 'destroy_banner'])->name('destroy_banner');
            });

            Route::get('tickethistory', [MasterController::class, 'tickethistory'])->name('tickethistory')->middleware('role:ticket-history');

            Route::group(['middleware' => ['role:app-language']], function () {
                Route::get('applanguage', [MasterController::class, 'applanguage'])->name('applanguage');
                Route::get('create_applanguage', [MasterController::class, 'create_applanguage'])->name('create_applanguage');
                Route::post('store_applanguage', [MasterController::class, 'store_applanguage'])->name('store_applanguage');
                Route::get('edit_applanguage/{id}', [MasterController::class, 'edit_applanguage'])->name('edit_applanguage');
                Route::patch('update_applanguage/{id}', [MasterController::class, 'update_applanguage'])->name('update_applanguage');
                Route::get('import_applanguage', [MasterController::class, 'import_applanguage'])->name('import_applanguage');
                Route::post('store_import_applanguage', [MasterController::class, 'store_import_applanguage'])->name('store_import_applanguage');
                Route::post('destroy_applanguage', [MasterController::class, 'destroy_applanguage'])->name('destroy_applanguage');
            });

            Route::group(['middleware' => ['role:master-tax']], function () {
                Route::get('mastertax', [MasterController::class, 'mastertax'])->name('mastertax');
                Route::get('create_mastertax', [MasterController::class, 'create_mastertax'])->name('create_mastertax');
                Route::post('store_mastertax', [MasterController::class, 'store_mastertax'])->name('store_mastertax');
                Route::get('edit_mastertax/{id}', [MasterController::class, 'edit_mastertax'])->name('edit_mastertax');
                Route::patch('update_mastertax/{id}', [MasterController::class, 'update_mastertax'])->name('update_mastertax');
                Route::get('import_mastertax', [MasterController::class, 'import_mastertax'])->name('import_mastertax');
                Route::post('store_import_mastertax', [MasterController::class, 'store_import_mastertax'])->name('store_import_mastertax');
                Route::post('destroy_mastertax', [MasterController::class, 'destroy_mastertax'])->name('destroy_mastertax');
            });


            Route::group(['middleware' => ['role:master-templates']], function () {
                Route::get('master_templates', [MasterController::class, 'master_templates'])->name('master_templates');
                Route::get('create_master_templates', [MasterController::class, 'create_master_templates'])->name('create_master_templates');
                Route::post('store_master_templates', [MasterController::class, 'store_master_templates'])->name('store_master_templates');
                Route::get('edit_master_templates/{id}', [MasterController::class, 'edit_master_templates'])->name('edit_master_templates');
                Route::patch('update_master_templates/{id}', [MasterController::class, 'update_master_templates'])->name('update_master_templates');
                Route::get('import_master_templates', [MasterController::class, 'import_master_templates'])->name('import_master_templates');
                Route::post('store_import_master_templates', [MasterController::class, 'store_import_master_templates'])->name('store_import_master_templates');
                Route::post('destroy_master_templates', [MasterController::class, 'destroy_master_templates'])->name('destroy_master_templates');
            });


            Route::group(['middleware' => ['role:master-drugs']], function () {
                Route::get('master_drugs', [MasterController::class, 'master_drugs'])->name('master_drugs');
                Route::get('create_master_drugs', [MasterController::class, 'create_master_drugs'])->name('create_master_drugs');
                Route::post('store_master_drugs', [MasterController::class, 'store_master_drugs'])->name('store_master_drugs');
                Route::get('edit_master_drugs/{id}', [MasterController::class, 'edit_master_drugs'])->name('edit_master_drugs');
                Route::patch('update_master_drugs/{id}', [MasterController::class, 'update_master_drugs'])->name('update_master_drugs');
                Route::get('import_master_drugs', [MasterController::class, 'import_master_drugs'])->name('import_master_drugs');
                Route::post('store_import_master_drugs', [MasterController::class, 'store_import_master_drugs'])->name('store_import_master_drugs');
                Route::post('destroy_master_drugs', [MasterController::class, 'destroy_master_drugs'])->name('destroy_master_drugs');
            });



            //masteruser
            Route::group(['middleware' => ['role:testimonial-user']], function () {
                Route::get('masteruser', [MasterController::class, 'masteruser'])->name('masteruser');
                Route::get('create_masteruser', [MasterController::class, 'create_masteruser'])->name('create_masteruser');
                Route::post('store_masteruser', [MasterController::class, 'store_masteruser'])->name('store_masteruser');
                Route::get('edit_masteruser/{id}', [MasterController::class, 'edit_masteruser'])->name('edit_masteruser');
                Route::patch('update_masteruser/{id}', [MasterController::class, 'update_masteruser'])->name('update_masteruser');
                Route::get('import_masteruser', [MasterController::class, 'import_masteruser'])->name('import_masteruser');
                Route::post('store_import_masteruser', [MasterController::class, 'store_import_masteruser'])->name('store_import_masteruser');
                Route::post('destroy_masteruser', [MasterController::class, 'destroy_masteruser'])->name('destroy_masteruser');
            });


            Route::group(['middleware' => ['role:master-language']], function () {
                Route::get('masterlanguage', [MasterController::class, 'masterlanguage'])->name('masterlanguage');
                Route::get('create_masterlanguage', [MasterController::class, 'create_masterlanguage'])->name('create_masterlanguage');
                Route::post('store_masterlanguage', [MasterController::class, 'store_masterlanguage'])->name('store_masterlanguage');
                Route::get('edit_masterlanguage/{id}', [MasterController::class, 'edit_masterlanguage'])->name('edit_masterlanguage');
                Route::patch('update_masterlanguage/{id}', [MasterController::class, 'update_masterlanguage'])->name('update_masterlanguage');
                Route::get('import_masterlanguage', [MasterController::class, 'import_masterlanguage'])->name('import_masterlanguage');
                Route::post('store_import_masterlanguage', [MasterController::class, 'store_import_masterlanguage'])->name('store_import_masterlanguage');
                Route::post('destroy_masterlanguage', [MasterController::class, 'destroy_masterlanguage'])->name('destroy_masterlanguage');
            });



            //area_of_expertise
            Route::group(['middleware' => ['role:astro-shop-categories']], function () {
                Route::get('astro_shop_categories', [MasterController::class, 'astro_shop_categories'])->name('astro_shop_categories');
                Route::get('create_astro_shop_categories', [MasterController::class, 'create_astro_shop_categories'])->name('create_astro_shop_categories');
                Route::post('store_astro_shop_categories', [MasterController::class, 'store_astro_shop_categories'])->name('store_astro_shop_categories');
                Route::get('edit_astro_shop_categories/{id}', [MasterController::class, 'edit_astro_shop_categories'])->name('edit_astro_shop_categories');
                Route::patch('update_astro_shop_categories/{id}', [MasterController::class, 'update_astro_shop_categories'])->name('update_astro_shop_categories');
                Route::get('import_astro_shop_categories', [MasterController::class, 'import_astro_shop_categories'])->name('import_astro_shop_categories');
                Route::post('store_import_astro_shop_categories', [MasterController::class, 'store_import_astro_shop_categories'])->name('store_import_astro_shop_categories');
                Route::post('destroy_astro_shop_categories', [MasterController::class, 'destroy_astro_shop_categories'])->name('destroy_astro_shop_categories');
            });

            Route::group(['middleware' => ['role:master-mastergifts']], function () {
                Route::get('mastergifts', [MasterController::class, 'mastergifts'])->name('mastergifts');
                Route::get('create_mastergifts', [MasterController::class, 'create_mastergifts'])->name('create_mastergifts');
                Route::post('store_mastergifts', [MasterController::class, 'store_mastergifts'])->name('store_mastergifts');
                Route::get('edit_mastergifts/{id}', [MasterController::class, 'edit_mastergifts'])->name('edit_mastergifts');
                Route::patch('update_mastergifts/{id}', [MasterController::class, 'update_mastergifts'])->name('update_mastergifts');
                Route::get('import_mastergifts', [MasterController::class, 'import_mastergifts'])->name('import_mastergifts');
                Route::post('store_import_mastergifts', [MasterController::class, 'store_import_mastergifts'])->name('store_import_mastergifts');
                Route::post('destroy_mastergifts', [MasterController::class, 'destroy_mastergifts'])->name('destroy_mastergifts');
            });


            //area_of_expertise
            Route::group(['middleware' => ['role:areas-of-expertise']], function () {
                Route::get('area_of_expertise', [MasterController::class, 'area_of_expertise'])->name('area_of_expertise');
                Route::get('create_area_of_expertise', [MasterController::class, 'create_area_of_expertise'])->name('create_area_of_expertise');
                Route::post('store_area_of_expertise', [MasterController::class, 'store_area_of_expertise'])->name('store_area_of_expertise');
                Route::get('edit_area_of_expertise/{id}', [MasterController::class, 'edit_area_of_expertise'])->name('edit_area_of_expertise');
                Route::patch('update_area_of_expertise/{id}', [MasterController::class, 'update_area_of_expertise'])->name('update_area_of_expertise');
                Route::get('import_area_of_expertise', [MasterController::class, 'import_area_of_expertise'])->name('import_area_of_expertise');
                Route::post('store_import_area_of_expertise', [MasterController::class, 'store_import_area_of_expertise'])->name('store_import_area_of_expertise');
                Route::post('destroy_area_of_expertise', [MasterController::class, 'destroy_area_of_expertise'])->name('destroy_area_of_expertise');
            });


            //coupans
            Route::group(['middleware' => ['role:coupon-management']], function () {
                Route::get('coupans', [MasterTwoController::class, 'coupans'])->name('coupans');
                Route::get('create_coupans', [MasterTwoController::class, 'create_coupans'])->name('create_coupans');
                Route::post('store_coupans', [MasterTwoController::class, 'store_coupans'])->name('store_coupans');
                Route::get('edit_coupans/{id}', [MasterTwoController::class, 'edit_coupans'])->name('edit_coupans');
                Route::patch('update_coupans/{id}', [MasterTwoController::class, 'update_coupans'])->name('update_coupans');
                Route::get('import_coupans', [MasterTwoController::class, 'import_coupans'])->name('import_coupans');
                Route::post('store_import_coupans', [MasterTwoController::class, 'store_import_coupans'])->name('store_import_coupans');
                Route::post('destroy_coupans', [MasterTwoController::class, 'destroy_coupans'])->name('destroy_coupans');
            });

            //walletplan
            Route::group(['middleware' => ['role:wallet-recharge-plans']], function () {
                Route::get('walletplan', [MasterTwoController::class, 'walletplan'])->name('walletplan');
                Route::get('create_walletplan', [MasterTwoController::class, 'create_walletplan'])->name('create_walletplan');
                Route::post('store_walletplan', [MasterTwoController::class, 'store_walletplan'])->name('store_walletplan');
                Route::get('edit_walletplan/{id}', [MasterTwoController::class, 'edit_walletplan'])->name('edit_walletplan');
                Route::patch('update_walletplan/{id}', [MasterTwoController::class, 'update_walletplan'])->name('update_walletplan');
                Route::get('import_walletplan', [MasterTwoController::class, 'import_walletplan'])->name('import_walletplan');
                Route::post('store_import_walletplan', [MasterTwoController::class, 'store_import_walletplan'])->name('store_import_walletplan');
                Route::post('destroy_walletplan', [MasterTwoController::class, 'destroy_walletplan'])->name('destroy_walletplan');
            });

            //mantras
            Route::group(['middleware' => ['role:mantra']], function () {
                Route::get('mantras', [MasterTwoController::class, 'mantras'])->name('mantras');
                Route::get('create_mantras', [MasterTwoController::class, 'create_mantras'])->name('create_mantras');
                Route::post('store_mantras', [MasterTwoController::class, 'store_mantras'])->name('store_mantras');
                Route::get('edit_mantras/{id}', [MasterTwoController::class, 'edit_mantras'])->name('edit_mantras');
                Route::patch('update_mantras/{id}', [MasterTwoController::class, 'update_mantras'])->name('update_mantras');
                Route::get('import_mantras', [MasterTwoController::class, 'import_mantras'])->name('import_mantras');
                Route::post('store_import_mantras', [MasterTwoController::class, 'store_import_mantras'])->name('store_import_mantras');
                Route::post('destroy_mantras', [MasterTwoController::class, 'destroy_mantras'])->name('destroy_mantras');
            });


            //rudrakshi
            Route::group(['middleware' => ['role:rudrakshi']], function () {
                Route::get('rudrakshi', [MasterTwoController::class, 'rudrakshi'])->name('rudrakshi');
                Route::get('create_rudrakshi', [MasterTwoController::class, 'create_rudrakshi'])->name('create_rudrakshi');
                Route::post('store_rudrakshi', [MasterTwoController::class, 'store_rudrakshi'])->name('store_rudrakshi');
                Route::get('edit_rudrakshi/{id}', [MasterTwoController::class, 'edit_rudrakshi'])->name('edit_rudrakshi');
                Route::patch('update_rudrakshi/{id}', [MasterTwoController::class, 'update_rudrakshi'])->name('update_rudrakshi');
                Route::get('import_rudrakshi', [MasterTwoController::class, 'import_rudrakshi'])->name('import_rudrakshi');
                Route::post('store_import_rudrakshi', [MasterTwoController::class, 'store_import_rudrakshi'])->name('store_import_rudrakshi');
                Route::post('destroy_rudrakshi', [MasterTwoController::class, 'destroy_rudrakshi'])->name('destroy_rudrakshi');
            });


            //precious_stone
            Route::group(['middleware' => ['role:precious-stone']], function () {
                Route::get('precious_stone', [MasterTwoController::class, 'precious_stone'])->name('precious_stone');
                Route::get('create_precious_stone', [MasterTwoController::class, 'create_precious_stone'])->name('create_precious_stone');
                Route::post('store_precious_stone', [MasterTwoController::class, 'store_precious_stone'])->name('store_precious_stone');
                Route::get('edit_precious_stone/{id}', [MasterTwoController::class, 'edit_precious_stone'])->name('edit_precious_stone');
                Route::patch('update_precious_stone/{id}', [MasterTwoController::class, 'update_precious_stone'])->name('update_precious_stone');
                Route::get('import_precious_stone', [MasterTwoController::class, 'import_precious_stone'])->name('import_precious_stone');
                Route::post('store_import_precious_stone', [MasterTwoController::class, 'store_import_precious_stone'])->name('store_import_precious_stone');
                Route::post('destroy_precious_stone', [MasterTwoController::class, 'destroy_precious_stone'])->name('destroy_precious_stone');
            });


            //semi_precious_stone
            Route::group(['middleware' => ['role:semi-precious-stone']], function () {
                Route::get('semi_precious_stone', [MasterTwoController::class, 'semi_precious_stone'])->name('semi_precious_stone');
                Route::get('create_semi_precious_stone', [MasterTwoController::class, 'create_semi_precious_stone'])->name('create_semi_precious_stone');
                Route::post('store_semi_precious_stone', [MasterTwoController::class, 'store_semi_precious_stone'])->name('store_semi_precious_stone');
                Route::get('edit_semi_precious_stone/{id}', [MasterTwoController::class, 'edit_semi_precious_stone'])->name('edit_semi_precious_stone');
                Route::patch('update_semi_precious_stone/{id}', [MasterTwoController::class, 'update_semi_precious_stone'])->name('update_semi_precious_stone');
                Route::get('import_semi_precious_stone', [MasterTwoController::class, 'import_semi_precious_stone'])->name('import_semi_precious_stone');
                Route::post('store_import_semi_precious_stone', [MasterTwoController::class, 'store_import_semi_precious_stone'])->name('store_import_semi_precious_stone');
                Route::post('destroy_semi_precious_stone', [MasterTwoController::class, 'destroy_semi_precious_stone'])->name('destroy_semi_precious_stone');
            });


            //threadcolor
            Route::group(['middleware' => ['role:thread-colour']], function () {
                Route::get('threadcolor', [MasterTwoController::class, 'threadcolor'])->name('threadcolor');
                Route::get('create_threadcolor', [MasterTwoController::class, 'create_threadcolor'])->name('create_threadcolor');
                Route::post('store_threadcolor', [MasterTwoController::class, 'store_threadcolor'])->name('store_threadcolor');
                Route::get('edit_threadcolor/{id}', [MasterTwoController::class, 'edit_threadcolor'])->name('edit_threadcolor');
                Route::patch('update_threadcolor/{id}', [MasterTwoController::class, 'update_threadcolor'])->name('update_threadcolor');
                Route::get('import_threadcolor', [MasterTwoController::class, 'import_threadcolor'])->name('import_threadcolor');
                Route::post('store_import_threadcolor', [MasterTwoController::class, 'store_import_threadcolor'])->name('store_import_threadcolor');
                Route::post('destroy_threadcolor', [MasterTwoController::class, 'destroy_threadcolor'])->name('destroy_threadcolor');
            });

            //masterdonation
            Route::group(['middleware' => ['role:donation']], function () {
                Route::get('masterdonation', [MasterTwoController::class, 'masterdonation'])->name('masterdonation');
                Route::get('create_masterdonation', [MasterTwoController::class, 'create_masterdonation'])->name('create_masterdonation');
                Route::post('store_masterdonation', [MasterTwoController::class, 'store_masterdonation'])->name('store_masterdonation');
                Route::get('edit_masterdonation/{id}', [MasterTwoController::class, 'edit_masterdonation'])->name('edit_masterdonation');
                Route::patch('update_masterdonation/{id}', [MasterTwoController::class, 'update_masterdonation'])->name('update_masterdonation');
                Route::get('import_masterdonation', [MasterTwoController::class, 'import_masterdonation'])->name('import_masterdonation');
                Route::post('store_import_masterdonation', [MasterTwoController::class, 'store_import_masterdonation'])->name('store_import_masterdonation');
                Route::post('destroy_masterdonation', [MasterTwoController::class, 'destroy_masterdonation'])->name('destroy_masterdonation');
            });
        });


        // Astrologer
        Route::prefix('Astrologer')->name('astrologer-manage.')->group(function () {
            Route::group(['middleware' => ['role:astrologer-management']], function () {

                Route::get('payout/{condi?}/{id}', [AstrologerController::class, 'payout'])->name('payout')->middleware('role:astrologer-time-manage');
                Route::get('booking_payout_done/{condi?}/{id}/{start_date}/{end_date}', [AstrologerController::class, 'booking_payout_done'])->name('booking_payout_done')->middleware('role:astrologer-time-manage');
                Route::post('payout_astrolger', [AstrologerController::class, 'payout_astrolger'])->name('payout_astrolger')->middleware('role:astrologer-time-manage');



                Route::get('import_astrologer', [AstrologerController::class, 'import_astrologer'])->name('import_astrologer')->middleware('role:import-astrologer');
                Route::post('store_import_astrologer', [AstrologerController::class, 'store_import_astrologer'])->name('store_import_astrologer');


                Route::get('astrologer/{what?}', [AstrologerController::class, 'astrologer'])->name('astrologer');
                Route::get('view_astrologer/{id}/{what?}', [AstrologerController::class, 'view_astrologer'])->name('view_astrologer')->middleware('role:astrologer-profile-view');
                Route::get('create_astrologer/{what?}', [AstrologerController::class, 'create_astrologer'])->name('create_astrologer')->middleware('role:add-astrologer');
                Route::post('store_astrologer', [AstrologerController::class, 'store_astrologer'])->name('store_astrologer')->middleware('role:add-astrologer');
                Route::get('edit_astrologer/{id}/{what?}', [AstrologerController::class, 'edit_astrologer'])->name('edit_astrologer')->middleware('role:edit-astrologer');
                Route::patch('update_astrologer/{id}', [AstrologerController::class, 'update_astrologer'])->name('update_astrologer')->middleware('role:edit-astrologer');
                Route::get('import_applanguage', [AstrologerController::class, 'import_applanguage'])->name('import_applanguage');
                Route::post('store_import_applanguage', [AstrologerController::class, 'store_import_applanguage'])->name('store_import_applanguage');
                Route::post('destroy_astrologer', [AstrologerController::class, 'destroy_astrologer'])->name('destroy_astrologer')->middleware('role:destroy-astrologer');
                Route::post('destroy_astrologer_file', [AstrologerController::class, 'destroy_astrologer_file'])->name('destroy_astrologer_file')->middleware('role:destroy-astrologer');
                Route::post('add-files', [AstrologerController::class, 'addFiles'])->name('add-files')->middleware('role:edit-astrologer');;
                Route::get('edit_astrologer_service/{id}/{what?}', [AstrologerController::class, 'edit_astrologer_service'])->name('edit_astrologer_service')->middleware('role:edit-astrologer');
                Route::patch('update_astrologer_service/{id}', [AstrologerController::class, 'update_astrologer_service'])->name('update_astrologer_service')->middleware('role:edit-astrologer')->middleware('role:edit-astrologer');
                Route::get('login_astrologer/{id}/{what?}', [AstrologerController::class, 'login_astrologer'])->name('login_astrologer')->middleware('role:astrologer-login-history');
                Route::group(['middleware' => ['role:incentive-astrologer']], function () {
                    Route::get('index_incentive_astrologer', [AstrologerController::class, 'index_incentive_astrologer'])->name('index_incentive_astrologer');
                    Route::get('create_incentive_astrologer', [AstrologerController::class, 'create_incentive_astrologer'])->name('create_incentive_astrologer');
                    Route::post('store_incentive_astrologer', [AstrologerController::class, 'store_incentive_astrologer'])->name('store_incentive_astrologer');
                    Route::post('approve_incentive_astrologer', [AstrologerController::class, 'approve_incentive_astrologer'])->name('approve_incentive_astrologer');
                    Route::post('reject_incentive_astrologer', [AstrologerController::class, 'reject_incentive_astrologer'])->name('reject_incentive_astrologer');
                });


                Route::group(['middleware' => ['role:reviews-astrologers']], function () {
                    Route::get('astrologers-reviews', [AstrologerController::class, 'show_reviews'])->name('astrologers-reviews');
                    Route::post('create_reviews', [AstrologerController::class, 'create_reviews'])->name('create_reviews');
                    Route::get('edit_reviews//{id}', [AstrologerController::class, 'edit_reviews'])->name('edit_reviews');
                    Route::patch('update_reviews/{id}', [AstrologerController::class, 'update_reviews'])->name('update_reviews');
                    Route::post('destroy_reviews', [AstrologerController::class, 'destroy_reviews'])->name('destroy_reviews');
                });
                Route::post('approveurl', [AstrologerController::class, 'approveurl'])->name('approveurl');
                Route::post('hide_url', [AstrologerController::class, 'hide_url'])->name('hide_url');




                Route::get('astrologertimemanage/{condi?}/{id}', [AstrologerController::class, 'astrologertimemanage'])->name('astrologertimemanage')->middleware('role:astrologer-time-manage');
                Route::get('create_custome_time/{condi?}/{id}', [AstrologerController::class, 'create_custome_time'])->name('create_custome_time')->middleware('role:astrologer-time-manage');
                Route::post('store_custome_time/{cond?}', [AstrologerController::class, 'store_custome_time'])->name('store_custome_time')->middleware('role:astrologer-time-manage');
                Route::get('delete_timemanage/{condi?}/{id}/{mainid}', [AstrologerController::class, 'delete_timemanage'])->name('delete_timemanage')->middleware('role:astrologer-time-manage');


                // astrologer area of expertise
                Route::get('astrologeraoe/{id}', [AstrologerController::class, 'astrologeraoe'])->name('astrologeraoe');
                Route::get('create_astrologeraoe/{id}', [AstrologerController::class, 'create_astrologeraoe'])->name('create_astrologeraoe');
                Route::post('store_astrologeraoe', [AstrologerController::class, 'store_astrologeraoe'])->name('store_astrologeraoe');
                Route::get('edit_astrologeraoe/{id}', [AstrologerController::class, 'edit_astrologeraoe'])->name('edit_astrologeraoe');
                Route::patch('update_astrologeraoe/{id}', [AstrologerController::class, 'update_astrologeraoe'])->name('update_astrologeraoe');
                Route::get('import_astrologeraoe', [AstrologerController::class, 'import_astrologeraoe'])->name('import_astrologeraoe');
                Route::post('store_import_astrologeraoe', [AstrologerController::class, 'store_import_astrologeraoe'])->name('store_import_astrologeraoe');
                Route::post('destroy_astrologeraoe', [AstrologerController::class, 'destroy_astrologeraoe'])->name('destroy_astrologeraoe');

                // astrologer language
                Route::get('astrologerlanguage/{id}', [AstrologerController::class, 'astrologerlanguage'])->name('astrologerlanguage');
                Route::get('create_astrologerlanguage/{id}', [AstrologerController::class, 'create_astrologerlanguage'])->name('create_astrologerlanguage');
                Route::post('store_astrologerlanguage', [AstrologerController::class, 'store_astrologerlanguage'])->name('store_astrologerlanguage');
                Route::get('edit_astrologerlanguage/{id}', [AstrologerController::class, 'edit_astrologerlanguage'])->name('edit_astrologerlanguage');
                Route::patch('update_astrologerlanguage/{id}', [AstrologerController::class, 'update_astrologerlanguage'])->name('update_astrologerlanguage');
                Route::get('import_astrologerlanguage', [AstrologerController::class, 'import_astrologerlanguage'])->name('import_astrologerlanguage');
                Route::post('store_import_astrologerlanguage', [AstrologerController::class, 'store_import_astrologerlanguage'])->name('store_import_astrologerlanguage');
                Route::post('destroy_astrologerlanguage', [AstrologerController::class, 'destroy_astrologerlanguage'])->name('destroy_astrologerlanguage');
            });
        });

        Route::prefix('Transactions')->name('order.')->group(function () {
            Route::group(['middleware' => ['role:transaction-history']], function () {
                // Route::post('transactionhistory',[AdminUserController::class,'export']);
                Route::get('transactionhistory', [OrdersManagementController::class, 'transactionhistory'])->name('transactionhistory');
            });
        });

        Route::prefix('Orders')->name('order.')->group(function () {
            Route::group(['middleware' => ['role:order-management']], function () {
                Route::get('consultation/{cond?}', [OrdersManagementController::class, 'consultation'])->name('consultation');
                Route::get('updateOrder/{id}/{what}', [OrdersManagementController::class, 'updateOrder'])->name('updateOrder');
                Route::get('refundRequestSolved/{id}', [OrdersManagementController::class, 'refundRequestSolved'])->name('refundRequestSolved');
                Route::get('callhistory', [OrdersManagementController::class, 'callhistory'])->name('callhistory');
                Route::get('chatlog/{id}', [OrdersManagementController::class, 'chatlog'])->name('chatlog');
                Route::get('export_data', [OrdersManagementController::class, 'export_data'])->name('export_data');
            });
        });

        Route::prefix('AstroProduct')->name('astro-product.')->group(function () {
            Route::group(['middleware' => ['role:astroshop-product']], function () {
                Route::get('index_product', [AstroShopProductController::class, 'index_product'])->name('index_product');
                Route::get('create_shop_product', [AstroShopProductController::class, 'create_product'])->name('create_shop_product');
                Route::post('store_shop_product', [AstroShopProductController::class, 'store_product'])->name('store_shop_product');
                Route::get('edit_shop_product/{id}', [AstroShopProductController::class, 'edit_product'])->name('edit_shop_product');
                Route::patch('update_shop_product/{id}', [AstroShopProductController::class, 'update_product'])->name('update_shop_product');
                Route::post('destroy_shop_product', [AstroShopProductController::class, 'destroy_shop_product'])->name('destroy_shop_product');
                Route::get('import_astro_product', [AstroShopProductController::class, 'import_faq'])->name('import_astro_product');
                Route::post('store_import_faq', [MasterController::class, 'store_import_faq'])->name('store_import_faq');
            });
        });
    });
});
