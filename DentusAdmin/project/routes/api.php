<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotifyController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Users\UserController;
use App\Http\Controllers\Api\ClinicController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\DoctorDetailController;
use App\Http\Controllers\Api\AppointmentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('test', [OnboardingController::class,'test'])->name('test');
Route::get('accept-invitation/{id}', [DoctorController::class, 'acceptInvitation'])->name('accept.invitation');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
// Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('forgot-password');
Route::post('verify/forgot_password', [AuthController::class,'sendOtpForForgotPassword'])->name('verify/forgot_password');
Route::post('verify/reset_password', [AuthController::class,'resetUserPassword'])->name('verify/reset_password');
Route::post('/onboard/clinic', [OnboardingController::class, 'createClinicWithDoctors'])->name('onboard/clinic');
Route::post('/onboard/doctor', [OnboardingController::class, 'onboardDoctor'])->name('onboard/doctor');
Route::post('reset_check',[AuthController::class,'reset_check'])->name('reset_check');
Route::post('/password/verify-otp', [AuthController::class, 'forgotverifyOtp'])->name('password/verify-otp');
Route::post('password-reset',[AuthController::class,'resetPassword'])->name('password-reset');

Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [ForgotPasswordController::class, 'reset']);


Route::get('/subscribelist', [SubscriptionController::class, 'subscribelist'])->name('subscribelist');


Route::group(['middleware' => ['json.response']], function () {
    // Route::middleware(['auth:api','checkstatus'])->get('/user', function (Request $request) {
    //     return $request->user();
    // });
    //User sd
    Route::post('verify/check-username', [AuthController::class,'checkUsername'])->name('register.check-username');
    Route::post('verify/check-email', [AuthController::class,'checkEmail'])->name('register.check-email');
    Route::post('verify/check-mobile', [AuthController::class,'checkMobile'])->name('register.check-mobile');

    Route::post('verify/otp_signup', [AuthController::class,'verifyRegisterOtpSD'])->name('register.verifyotp');
    Route::post('verify/forgot_password', [AuthController::class,'sendOtpForForgotPassword']);
    Route::post('verify/reset_password', [AuthController::class,'resetUserPassword']);
    Route::post('user/send-login-otp', [AuthController::class,'sendOtp']);
    Route::post('user/otp-login', [AuthController::class,'otpLogin']);
    Route::post('verify/signupuser', [AuthController::class,'newSignupUser'])->name('register.api');
    Route::post('verify/signinuser', [AuthController::class,'loginUser'])->name('login.api');
    //master
    Route::get('masterLangSpeciaService', [MasterController::class,'masterLangSpeciaService']);
    Route::get('masterDegreeCollege', [MasterController::class,'masterDegreeCollege']);
    Route::get('/package/parentlist', [SubscriptionController::class, 'parentlist'])->name('subscribelist');
    Route::post('package/package-details', [SubscriptionController::class, 'PackageDetail'])->name('package-details');
    Route::get('state', [MasterController::class,'state']);
    Route::get('city', [MasterController::class,'city']);
    Route::get('mastertheme', [MasterController::class,'mastertheme']);
    Route::get('mastercouncil', [MasterController::class,'mastercouncil']);

    //users api
    Route::post('verify/otp_signup/user', [UserController::class,'verifyRegisterOtpSD'])->name('register.verifyotp');
    Route::post('verify/otp_user/user', [UserController::class,'verifyUserOtp'])->name('register.otp_user');
    Route::post('verify/signupuser/user', [UserController::class,'newSignupUser'])->name('register.api');

    // Clinic QR Code Generation
    Route::post('clinic/generate-qr', [ClinicController::class, 'generateQrCode']);

    // QR Code Scanning
    Route::post('clinic/scan-qr', [ClinicController::class, 'scanQrCode'])->name('clinic.scan-qr');
    Route::post('clinic/detail', [ClinicController::class, 'ClinicDetail'])->name('clinic.detail');
    //USer End Api


    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('signup/doctor', [DoctorController::class, 'signupDoctor'])->name('signup.doctor');
    Route::post('verify/doctor', [DoctorController::class, 'newSignupdr'])->name('verify.doctor');

    Route::middleware(['auth:api','checkstatus'])->group(function () {
        //dentus team verify
        Route::post('doctors/invite', [DoctorController::class, 'inviteDoctor'])->name('doctors.invite');
        Route::post('doctors/getDoctor', [DoctorController::class, 'getDoctorByToken'])->name('doctors.getDoctor');
        Route::post('doctors/assistant', [DoctorController::class, 'assistantStatus'])->name('doctors.assistant');
        Route::post('doctors/checkSubscription', [DoctorController::class, 'checkSubscription'])->name('doctors.checkSubscription');
        Route::get('/doctors/reviews/{id}', [DoctorController::class, 'getDoctorReviews']);
        Route::get('/doctor/resendInvitation', [DoctorController::class, 'resendInvitation']);
        Route::get('/doctor/listInvitedDoctors', [DoctorController::class, 'listInvitedDoctors']);
        Route::post('/appointments', [AppointmentController::class, 'bookAppointment']);
        Route::post('/appointments/{appointment}/payment', [AppointmentController::class, 'makePayment']);
        Route::get('/appointments/{appointment}/payment-summary', [AppointmentController::class, 'paymentSummary']);
        Route::post('/seasonal/offerbanner', [HomeController::class, 'SeasonalOffer']);
        Route::post('/seasonal/offerbanner/List', [HomeController::class, 'SeasonalOfferList']);


        Route::post('/treatments', [TreatmentController::class, 'store']);
        Route::get('/treatments/show/{id}', [TreatmentController::class, 'show']);
        Route::post('/treatments/update/{id}', [TreatmentController::class, 'update']);
        Route::delete('/treatments/delete/{id}', [TreatmentController::class, 'destroy']);

        Route::post('/packages', [PackageController::class, 'store']);
        Route::get('/packages/index', [PackageController::class, 'index']); // List all packages
        Route::get('/packages/show/{id}', [PackageController::class, 'show']); // View single package
        Route::put('/packages/update/{id}', [PackageController::class, 'update']);
        Route::delete('/packages/delete/{id}', [PackageController::class, 'destroy']);


        Route::post('/subscribe', [SubscriptionController::class, 'chooseSubscription']);
        Route::post('/save-personal-info', [ProfileController::class, 'savePersonalInfo'])->name('save-personal-info');
        Route::post('/save-professional-info', [ProfileController::class, 'saveProfessionalInfo'])->name('save-professional-info');
        Route::post('/save-biography', [ProfileController::class, 'saveBiography'])->name('save-biography');
        Route::post('/save-education', [ProfileController::class, 'saveEducation'])->name('save-education');
        Route::post('/save-medical-registration', [ProfileController::class, 'saveMedicalRegistration'])->name('save-medical-registration');
        Route::post('/save-establishment-timings', [ProfileController::class, 'saveEstablishmentTimings'])->name('save-establishment-timings');
        Route::post('/save-doctor-availability', [ProfileController::class, 'saveDoctorAvailability'])->name('save-doctor-availability');
        Route::post('/save-establishment-address', [ProfileController::class, 'saveEstablishmentAddress'])->name('save-establishment-address');
        Route::post('/upload-establishment-photo', [ProfileController::class, 'uploadEstablishmentGallery'])->name('upload-establishment-photo');

        //sd
        Route::post('/package/subscribe', [SubscriptionController::class, 'chooseSubscription']);
        Route::get('/doctor/get-profile', [DoctorDetailController::class, 'getProfile']);
        Route::post('doctor/update-profile',[DoctorDetailController::class, 'updateUserProfile']);
        Route::post('doctor/update-profile2',[DoctorDetailController::class, 'updateUserProfile2']);
        Route::post('doctor/update-profile3',[DoctorDetailController::class, 'updateUserProfile3']);
        Route::post('doctor/update-profile4',[DoctorDetailController::class, 'updateUserProfile4']);
        Route::post('doctor/update-profile5',[DoctorDetailController::class, 'updateUserProfile5']);
        Route::post('doctor/update-profile6',[DoctorDetailController::class, 'updateUserProfile6']);
        Route::post('doctor/update-profile7',[DoctorDetailController::class, 'updateUserProfile7']);
        Route::post('doctor/update-profile8',[DoctorDetailController::class, 'updateUserProfile8']);
        Route::post('doctor/update-profile9',[DoctorDetailController::class, 'updateUserProfile9']);
        Route::post('doctor/add-gallery',[DoctorDetailController::class, 'addGallery']);
        Route::post('doctor/delete-gallery',[DoctorDetailController::class, 'deleteImages']);
        Route::post('doctor/update-profile12',[DoctorDetailController::class, 'updateUserProfile12']);
        Route::post('doctor/update-profile13',[DoctorDetailController::class, 'updateUserProfile13']);
    });


    //USers Api
    Route::post('flash/screen', [ClinicController::class, 'FlashScreen'])->name('flash.screen');
    Route::post('home-screen', [HomeController::class, 'OffersBanner'])->name('home-screen');
});
