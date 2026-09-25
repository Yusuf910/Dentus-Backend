<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\ReportManagementController;



Route::get("/",[FrontendController::class,'index'])->name("webfront.index");
Route::prefix('doctor-dashboard')->name('admin.')->group(function(){


    Route::middleware(['guest:admin','PreventBackHistory'])->group(function(){
          Route::get('/', [LoginController::class,'showLoginForm'])->name('admin.login');
          Route::get('/login',[LoginController::class,'showLoginForm'])->name('login');
          Route::get('/forget',[LoginController::class,'forget'])->name('forget');
          Route::post('/check',[AdminController::class,'check'])->name('check');
          Route::get('login/locked', [LoginController::class,'locked'])->middleware('auth')->name('locked');
          Route::post('login/locked', [LoginController::class,'unlock'])->name('unlock');
          Route::post('/forgetcheck',[AdminController::class,'forgetcheck'])->name('forgetcheck');
          Route::get("/otpforforget/{token?}",[AdminController::class,'otpforforget'])->name("otpforforget");
          Route::get('/otpverifyforget',[AdminController::class,'otpverifyforget'])->name('otpverifyforget');
          Route::get("/resendotpforforget/{token?}",[AdminController::class,'resendotpforforget'])->name("resendotpforforget");
          Route::post('/changepassword',[AdminController::class,'changepassword'])->name('changepassword');
          Route::get('/signup',[LoginController::class,'signup'])->name('signup');
          Route::post('/check2',[AdminController::class,'check2'])->name('check2');
          Route::get("/otpforsignup/{token?}",[AdminController::class,'otpforsignup'])->name("otpforsignup");
          Route::get('/otpverifysignup',[AdminController::class,'otpverifysignup'])->name('otpverifysignup');
          Route::get("/resendotpforsignup/{token?}",[AdminController::class,'resendotpforsignup'])->name("resendotpforsignup");
    });

    Route::middleware(['auth:admin','PreventBackHistory'])->group(function(){
        Route::get('/home',[DashboardController::class,'index'])->name('home');
        Route::get('/logout',[AdminController::class,'logout'])->name('logout');
        Route::get('/stafflist',[StaffController::class,'index'])->name('stafflist');
        Route::get('/loginhistory/{id}',[StaffController::class,'loginhistory'])->name('loginhistory');
        Route::get('/listrequest',[StaffController::class,'index'])->name('listrequest');
        Route::get('/listallchartorderelaetd',[ChartController::class,'index'])->name('listallchartorderelaetd');
        Route::resource('admins', StaffController::class);
        Route::get('superadmin-password-change',[StaffController::class,'superpasswordchange'])->name('superadmin-password-change');
        Route::patch('superadmin2update',[StaffController::class,'superadmin2update'])->name('superadmin2update');
        Route::resource('roles', RolesController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('settings', SettingsController::class)->middleware('role:settingsn');
        Route::get('settings2',[SettingsController::class,'settings2'])->name('settings2');
        Route::post('settings2update',[SettingsController::class,'settings2update'])->name('settings2update');
        Route::get('settings3',[SettingsController::class,'settings3'])->name('settings3');
        Route::get('settings4/{type?}',[SettingsController::class,'settings4'])->name('settings4');
        Route::post('settings3update',[SettingsController::class,'settings3update'])->name('settings3update');
        Route::get('settings4invoice',[SettingsController::class,'settings4invoice'])->name('settings4invoice')->middleware('role:setting-for-invoice');
        Route::patch('settings4invoiceupdate',[SettingsController::class,'settings4invoiceupdate'])->name('settings4invoiceupdate');
        Route::get('settingsaboutus',[SettingsController::class,'settingsaboutus'])->name('settingsaboutus')->middleware('role:settingsaboutus');
        Route::patch('settingsaboutusupdate',[SettingsController::class,'settingsaboutusupdate'])->name('settingsaboutusupdate');
        

        
        //User Management
        Route::prefix('User-Management')->name('user.')->group(function () {
            Route::get('myqr',[DoctorController::class,'myqr'])->name('myqr');
            Route::get('my-staff',[DoctorController::class,'mystaff'])->name('mystaff');
            Route::get('addmy-staff',[DoctorController::class,'addmystaff'])->name('addmystaff');
            Route::post('store_staff',[DoctorController::class,'store_staff'])->name('store_staff');
            Route::post('store_staffinvite',[DoctorController::class,'store_staffinvite'])->name('store_staffinvite');
            Route::get('my-staff-detail/{id}',[DoctorController::class,'mystaffdetail'])->name('mystaffdetail');
            Route::get('sendreminder/{id}',[DoctorController::class,'sendreminder'])->name('sendreminder');
            Route::post('create_mystaff',[DoctorController::class,'create_mystaff'])->name('create_mystaff');
            Route::post('import_mystaff',[DoctorController::class,'import_mystaff'])->name('import_mystaff');
            Route::post('destroy_mystaff',[DoctorController::class,'destroy_mystaff'])->name('destroy_mystaff');
            Route::post('store_mystaff',[DoctorController::class,'store_mystaff'])->name('store_mystaff');
            Route::post('aboutupdate/{id}',[DoctorController::class,'aboutupdate'])->name('aboutupdate');
            Route::post('professionalupdate/{id}',[DoctorController::class,'professionalupdate'])->name('professionalupdate');
            Route::post('educationupdate/{id}',[DoctorController::class,'educationupdate'])->name('educationupdate');
            Route::post('awardupdate/{id}',[DoctorController::class,'awardupdate'])->name('awardupdate');
            Route::post('counsilupdate/{id}',[DoctorController::class,'counsilupdate'])->name('counsilupdate');
            Route::post('timingupdate/{id}',[DoctorController::class,'timingupdate'])->name('timingupdate');
            Route::post('timingupdate2/{id}',[DoctorController::class,'timingupdate2'])->name('timingupdate2');
            Route::post('timingupdate3/{id}',[DoctorController::class,'timingupdate3'])->name('timingupdate3');
            Route::post('profileupdate/{id}',[DoctorController::class,'profileupdate'])->name('profileupdate');
            Route::get('my-profile-detail/{id?}',[DoctorController::class,'myprofiledetail'])->name('myprofiledetail');

		});
		Route::prefix('My-User-Management')->name('myuser.')->group(function () {
            
            Route::get('my-user',[DoctorController::class,'myuserlistnew'])->name('myuserlist');
            Route::get('create_myuser',[DoctorController::class,'create_myuser'])->name('create_myuser');
            Route::post('store_patient',[DoctorController::class,'store_patient'])->name('store_patient');
            Route::get('patients-profile/{id?}/{mainid?}',[DoctorController::class,'paitentprofilenew'])->name('paitentprofile');
            Route::get('prescription-view/{id}',[DoctorController::class,'prescriptionView'])->name('prescriptionView');
            Route::get('dwnprescriptionView/{id}',[DoctorController::class,'dwnprescriptionView'])->name('dwnprescriptionView');
            Route::get('my-feedback',[DoctorController::class,'feedback'])->name('feedback');
            Route::get('my-seasonaloffer',[DoctorController::class,'seasonaloffer'])->name('seasonaloffer');
            Route::post('store_seasonaloffer',[DoctorController::class,'store_seasonaloffer'])->name('store_seasonaloffer');
            Route::patch('update_seasonaloffer/{id?}', [DoctorController::class, 'update_seasonaloffer'])->name('update_seasonaloffer');
            Route::post('destroy_seasonaloffer', [DoctorController::class, 'destroy_seasonaloffer'])->name('destroy_seasonaloffer');


		});
		Route::prefix('appointments')->name('appoint.')->group(function () {
            
            Route::get('my-calender',[DoctorController::class,'mycalender'])->name('mycalender');
            Route::get('my-appointment',[DoctorController::class,'myappointment'])->name('myappointment');
            Route::get('my-appointmentpending',[DoctorController::class,'myappointmentpending'])->name('myappointmentpending');
            Route::get('add-appointment',[DoctorController::class,'addappointment'])->name('addappointment');
            Route::get('/get-doctors-by-treatment/{id}', [DoctorController::class, 'getDoctorsByTreatment']);
            Route::any('store_appointment',[DoctorController::class,'store_appointment'])->name('store_appointment');
            Route::get('appointmentconfirm',[DoctorController::class,'appointmentconfirm'])->name('appointmentconfirm');
            Route::get('store_appointmentfinal',[DoctorController::class,'store_appointmentfinal'])->name('store_appointmentfinal');
            Route::post('get-timeslot',[DoctorController::class,'getslots'])->name('get-timeslot');
            Route::get('my-appointmentdetails/{id}',[DoctorController::class,'myappointmentdetails'])->name('myappointmentdetails');
            Route::get('updatemy-appointment/{id}/{status}',[DoctorController::class,'updatemyappointment'])->name('updatemyappointment');
            Route::post('updatemy-appointmentslot/{id}',[DoctorController::class,'updatemyappointmentslot'])->name('updatemyappointmentslot');
            Route::post('updatemy-appointmentcancel/{id}',[DoctorController::class,'updatemyappointmentcancel'])->name('updatemyappointmentcancel');
            Route::get('markaspaid/{id}',[DoctorController::class,'markaspaid'])->name('markaspaid');

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

            Route::prefix('Notification')->name('notification.')->group(function () {
                  //notification
                  Route::any('send_notification_to_user', [NotificationController::class, 'send_notification_to_user'])->name('send_notification_to_user');
      
                  Route::get('notification_user', [NotificationController::class, 'notification_user'])->name('notification_user');
                  Route::post('smt_notification', [NotificationController::class, 'smt_notification'])->name('smt_notification');
                  Route::post('smt_notification_astrologer', [NotificationController::class, 'smt_notification_astrologer'])->name('smt_notification_astrologer');
                  Route::post('curl_main_resource', [NotificationController::class, 'curl_main_resource'])->name('curl_main_resource');
                  Route::any('send_notification_to_astrologer', [NotificationController::class, 'send_notification_to_astrologer'])->name('send_notification_to_astrologer')->middleware('role:astrologer-notification');
                  Route::any('send_notification_to_astrologer_new', [NotificationController::class, 'send_notification_to_astrologer_new'])->name('send_notification_to_astrologer_new')->middleware('role:astrologer-notification');
                  Route::get('send_notification_to_all', [NotificationController::class, 'send_notification_to_all'])->name('send_notification_to_all');
                  Route::post('Asyncnotifytoall_admin', [NotificationController::class, 'asyncnotifytoall_admin'])->name('Asyncnotifytoall_admin');
              });

		Route::prefix('master')->name('master.')->group(function () {
            
            Route::get('treatment', [MasterController::class, 'treatment'])->name('treatment');
            Route::get('create_treatment', [MasterController::class, 'create_treatment'])->name('create_treatment');
            Route::post('store_treatment', [MasterController::class, 'store_treatment'])->name('store_treatment');
            Route::get('edit_treatment/{id}', [MasterController::class, 'edit_treatment'])->name('edit_treatment');
            Route::patch('update_treatment/{id?}', [MasterController::class, 'update_treatment'])->name('update_treatment');
            Route::get('import_treatment', [MasterController::class, 'import_treatment'])->name('import_treatment');
            Route::post('store_import_treatment', [MasterController::class, 'store_import_treatment'])->name('store_import_treatment');
            Route::post('destroy_treatment', [MasterController::class, 'destroy_treatment'])->name('destroy_treatment');

            Route::get('packtreatment', [MasterController::class, 'packtreatment'])->name('packtreatment');
            Route::get('create_packtreatment', [MasterController::class, 'create_packtreatment'])->name('create_packtreatment');
            Route::post('store_packtreatment', [MasterController::class, 'store_packtreatment'])->name('store_packtreatment');
            Route::get('edit_packtreatment/{id}', [MasterController::class, 'edit_packtreatment'])->name('edit_packtreatment');
            Route::patch('update_packtreatment/{id?}', [MasterController::class, 'update_packtreatment'])->name('update_packtreatment');
            Route::get('import_packtreatment', [MasterController::class, 'import_packtreatment'])->name('import_packtreatment');
            Route::post('store_import_packtreatment', [MasterController::class, 'store_import_packtreatment'])->name('store_import_packtreatment');
            Route::post('destroy_packtreatment', [MasterController::class, 'destroy_packtreatment'])->name('destroy_packtreatment');

            Route::get('packtreatment', [MasterController::class, 'packtreatment'])->name('packtreatment');
            Route::get('create_packtreatment', [MasterController::class, 'create_packtreatment'])->name('create_packtreatment');
            Route::post('store_packtreatment', [MasterController::class, 'store_packtreatment'])->name('store_packtreatment');
            Route::get('edit_packtreatment/{id}', [MasterController::class, 'edit_packtreatment'])->name('edit_packtreatment');
            Route::patch('update_packtreatment/{id?}', [MasterController::class, 'update_packtreatment'])->name('update_packtreatment');
            Route::get('import_packtreatment', [MasterController::class, 'import_packtreatment'])->name('import_packtreatment');
            Route::post('store_import_packtreatment', [MasterController::class, 'store_import_packtreatment'])->name('store_import_packtreatment');
            Route::post('destroy_packtreatment', [MasterController::class, 'destroy_packtreatment'])->name('destroy_packtreatment');

            Route::get('clinic', [MasterController::class, 'clinic'])->name('clinic');
            Route::get('create_clinic', [MasterController::class, 'create_clinic'])->name('create_clinic');
            Route::post('store_clinic', [MasterController::class, 'store_clinic'])->name('store_clinic');
            Route::get('edit_clinic/{id}', [MasterController::class, 'edit_clinic'])->name('edit_clinic');
            Route::patch('update_clinic/{id?}', [MasterController::class, 'update_clinic'])->name('update_clinic');
            Route::get('import_clinic', [MasterController::class, 'import_clinic'])->name('import_clinic');
            Route::post('store_import_clinic', [MasterController::class, 'store_import_clinic'])->name('store_import_clinic');
            Route::post('destroy_clinic', [MasterController::class, 'destroy_clinic'])->name('destroy_clinic');
            Route::get('clinicimages/{id}', [MasterController::class, 'clinicimages'])->name('clinicimages');
            Route::post('addimages_clinic/{id}', [MasterController::class, 'addimages_clinic'])->name('addimages_clinic');
            Route::post('destroy_clinicimages', [MasterController::class, 'destroy_clinicimages'])->name('destroy_clinicimages');
            Route::get('clinictiming/{id}', [MasterController::class, 'clinictiming'])->name('clinictiming');
            Route::post('destroy_clinictiming', [MasterController::class, 'destroy_clinictiming'])->name('destroy_clinictiming');
            Route::post('timingupdateclinictiming/{id}',[MasterController::class,'timingupdateclinictiming'])->name('timingupdateclinictiming');
            Route::get('clinicholiday/{id}', [MasterController::class, 'clinicholiday'])->name('clinicholiday');
            Route::post('destroy_holiday', [MasterController::class, 'destroy_holiday'])->name('destroy_holiday');
            Route::post('addimages_holiday/{id}', [MasterController::class, 'addimages_holiday'])->name('addimages_holiday');
            
            Route::get('blog', [MasterController::class, 'blog'])->name('blog');
            Route::get('create_blog', [MasterController::class, 'create_blog'])->name('create_blog');
            Route::post('store_blog', [MasterController::class, 'store_blog'])->name('store_blog');
            Route::get('edit_blog/{id}', [MasterController::class, 'edit_blog'])->name('edit_blog');
            Route::patch('update_blog/{id?}', [MasterController::class, 'update_blog'])->name('update_blog');
            Route::get('import_blog', [MasterController::class, 'import_blog'])->name('import_blog');
            Route::post('store_import_blog', [MasterController::class, 'store_import_blog'])->name('store_import_blog');
            Route::post('destroy_blog', [MasterController::class, 'destroy_blog'])->name('destroy_blog');
            Route::get('/load_city2/{state_id}/', [MasterController::class,'load_city2'])->name('load_city2');

            Route::get('doctimeslots/{id}', [MasterController::class, 'doctimeslots'])->name('doctimeslots');
            Route::post('timingupdateclininc/{id}',[MasterController::class,'timingupdateclininc'])->name('timingupdateclininc'); 
		});

    });

});
