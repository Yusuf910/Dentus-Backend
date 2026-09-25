<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MasterController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\DoctorDetailController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorHistoryController;
use App\Http\Controllers\Api\Users\UserController;
use App\Http\Controllers\Api\Users\ClinicController;
use App\Http\Controllers\Api\Users\HomeController;
use App\Http\Controllers\Api\Users\AppointmentUserController;
use App\Http\Controllers\Api\TreatmentController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\Users\UserBookingController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\RazorpayController;
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


Route::get('/test', [QuestionGenerator::class, 'test']);
Route::get('/smstests', [BaseController::class, 'smstests']);

Route::post('Asyncnotifytoall', [BaseController::class,'asyncnotifytoall'])->name('Asyncnotifytoall');
Route::post('BackithreadNotifire', [BaseController::class,'pushNotification'])->name('BackithreadNotifire');
Route::get('reminder1hour', [BaseController::class,'reminder1hour'])->name('reminder1hour');
Route::get('bookingcheck', [BaseController::class,'bookingcheck'])->name('bookingcheck');
Route::get('bookingcheck', [BaseController::class,'bookingcheck'])->name('bookingcheck');
Route::post('payment-event' ,[RazorpayController::class,'razorPaywebhook']);
Route::post('order-events' ,[RazorpayController::class,'razorPaywebhook']);
Route::post('refund-events' ,[RazorpayController::class,'handle']);



Route::group(['middleware' => ['json.response']], function () {

    //User
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

    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('reset-password', [ForgotPasswordController::class, 'reset']);

    
    //master
    Route::get('masterLangSpeciaService', [MasterController::class,'masterLangSpeciaService']);
    Route::get('masterDegreeCollege', [MasterController::class,'masterDegreeCollege']);
    Route::get('/package/parentlist', [SubscriptionController::class, 'parentlist'])->name('subscribelist');

    Route::post('package/package-details', [SubscriptionController::class, 'PackageDetail'])->name('package-details');
    Route::get('state', [MasterController::class,'state']);
    Route::get('city', [MasterController::class,'city']);
    Route::get('mastertheme', [MasterController::class,'mastertheme']);
    Route::get('mastercouncil', [MasterController::class,'mastercouncil']);
    Route::get('mastertax', [MasterController::class,'mastertax']);

    //ysf
    Route::post('signup/doctor', [DoctorController::class, 'signupDoctor'])->name('signup.doctor');
    Route::post('verify/doctor', [DoctorController::class, 'newSignupdr'])->name('verify.doctor');

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
    
    Route::get('invoiceuser/{id?}', [HomeController::class,'invoiceuser'])->name('invoiceuser');
    Route::get('invoiceuserrefund/{id?}', [HomeController::class,'invoiceuserrefund'])->name('invoiceuserrefund');
    Route::get('prescriptionview/{id?}', [DoctorHistoryController::class,'prescriptionview'])->name('prescriptionview');
    Route::get('masterpoints', [MasterController::class,'masterpoints']);
        Route::post('/user/setting/data',  [UserController::class, 'UserSettingData']);

    Route::middleware(['auth:astrologer-api','checkstatus'])->group(function () {

        //USers Api Ysf
        Route::post('flash/screen', [ClinicController::class, 'FlashScreen'])->name('flash.screen');
        Route::post('home-screen', [HomeController::class, 'OffersBanner'])->name('home-screen');

        Route::post('doctor-tax', [AppointmentUserController::class, 'doctor_tax'])->name('doctor-tax');
        Route::get('doctor-tax-list', [AppointmentUserController::class, 'getDoctorTax'])->name('doctor-tax-list');

        Route::post('/member-store', [UserController::class, 'member_store']);
        Route::post('/member-update', [UserController::class, 'member_update']);
        Route::post('/member-destroy', [UserController::class, 'destroy_member']);
        Route::post('/member-list', [UserController::class, 'member_list']);
        Route::post('/appointment-treatment-list', [AppointmentUserController::class, 'appointment_treatment_list']);
        Route::post('/book-doctor', [AppointmentUserController::class, 'book']);
        Route::post('/appointments/history', [AppointmentUserController::class, 'getHistory']);
        Route::post('/appointments/history/all', [AppointmentUserController::class, 'getHistoryall']);

        Route::post('/appointments/history/details', [AppointmentUserController::class, 'getHistoryDetails']);
        Route::post('/appointments/history/completed', [AppointmentUserController::class, 'getHistoryCompleted']);
        Route::post('/appointments/history/completed/details', [AppointmentUserController::class, 'getHistoryCompletedDetails']);
        Route::post('/appointments/history/cancel', [AppointmentUserController::class, 'getHistoryCancel']);
        Route::post('/appointments/history/cancel/details', [AppointmentUserController::class, 'getHistoryCancelDetails']);
        Route::post('/appointments/rating', [AppointmentUserController::class, 'rating']);
        Route::post('/appointments/cancel/reason', [AppointmentUserController::class, 'getCancelReason']);
        Route::post('/appointments/reschedule/booking', [AppointmentUserController::class, 'rescheduleBooking']);
        Route::post('/appointments/cancel/list', [AppointmentUserController::class, 'cancel_list']);

        Route::post('/appointment-datefilter-user', [AppointmentUserController::class, 'BookingDateFilterUser']);
        Route::post('/appointment-timeslot', [AppointmentUserController::class, 'appointmenttimeslot']);
        Route::post('/appointment-clicnic-list', [AppointmentUserController::class, 'clinicList']);
        Route::post('/user-update', [UserController::class, 'user_update']);
        Route::post('/user-list', [UserController::class, 'user_list']);
        Route::post('/medical-records', [UserController::class, 'medical_records']);
        Route::post('/editmedical-records', [UserController::class, 'editmedical_records']);
        Route::post('/deletemedical-records', [UserController::class, 'deletemedical_records']);

        Route::post('/medical-list', [UserController::class, 'medical_list']);
        Route::post('/booking/home', [UserBookingController::class, 'home']);
        Route::post('/booking/joinBooking', [UserBookingController::class, 'joinBooking']);
        Route::post('/pack/treatment/list', [AppointmentUserController::class, 'PackTreatmentList']);
        Route::post('/treatment/buynow/', [AppointmentUserController::class, 'TreatmentBuyNow']);
        Route::post('/treatment/package/active', [AppointmentUserController::class, 'PackTreatmentActive']);
        Route::post('/home/screen/data', [HomeController::class, 'HomeScreen']);
        Route::post('/home/screen/search', [HomeController::class, 'searchDoc']);

        Route::post('/home/screen/doctor/detail', [HomeController::class, 'HomeScreenDoctorDetail']);
        Route::post('/home/screen/clinic/detail', [HomeController::class, 'HomeScreenClinicDetail']);
        Route::post('/home/screen/social/detail', [HomeController::class, 'HomeScreenSocialDetail']);
        Route::post('/blogslist', [HomeController::class, 'blogslist']);
        Route::post('/blogsview', [HomeController::class, 'blogsview']);

        Route::post('list-notification',[HomeController::class, 'listNotification'])->name('list-notification');
        Route::post('/home/invoicelist', [HomeController::class, 'invoicelist']);
        Route::post('/loyalitypoints', [HomeController::class, 'loyalitypoints']);
        Route::post('/coupancodelist', [AppointmentUserController::class, 'coupancodelist']);
        Route::get('settingsConfig', [HomeController::class,'settingsConfig'])->name('settingsConfig');

    });

    Route::middleware(['auth:api,guestuser-api','checkstatus'])->group(function () { 
        //ysf
        //dentus team verify
        Route::post('doctors/invite', [DoctorController::class, 'inviteDoctor'])->name('doctors.invite');
        Route::post('doctors/getDoctor', [DoctorController::class, 'getDoctorByToken'])->name('doctors.getDoctor');
        Route::post('doctors/assistant', [DoctorController::class, 'assistantStatus'])->name('doctors.assistant');
        Route::post('doctors/checkSubscription', [DoctorController::class, 'checkSubscription'])->name('doctors.checkSubscription');
        Route::get('/doctors/reviews/{id}', [DoctorController::class, 'getDoctorReviews']);
        Route::post('/doctor/resendInvitation', [DoctorController::class, 'resendInvitation']);
        Route::post('/doctor/singlelist', [DoctorController::class, 'doctorsinglelist']);
        Route::get('/doctor/listInvitedDoctors', [DoctorController::class, 'listInvitedDoctors']);
        Route::post('/appointments', [AppointmentController::class, 'bookAppointment']);
        Route::post('/appointments/{appointment}/payment', [AppointmentController::class, 'makePayment']);
        Route::get('/appointments/{appointment}/payment-summary', [AppointmentController::class, 'paymentSummary']);
        Route::post('/seasonal/offerbanner', [HomeController::class, 'SeasonalOffer']);
        Route::post('/seasonal/offerbanner/List', [HomeController::class, 'SeasonalOfferList']);
        Route::post('/add/blog/', [MasterController::class, 'addBlog']);
        Route::any('/update/updateblog', [MasterController::class, 'editBlog']);
        Route::post('/delete/deleteblog', [MasterController::class, 'deleteBlog']);


        Route::post('/blog/list/', [MasterController::class, 'BlogList']);
        Route::post('/blog/single/details', [MasterController::class, 'BlogSingleList']);
        Route::post('/seasonal/promotions', [MasterController::class, 'SeasonalPromotions']);
        Route::post('/seasonal/promotionsadmin', [MasterController::class, 'promotionsadmin']);
        Route::post('update/seasonal/promotions', [MasterController::class, 'UpdateSeasonalPromotions']);
        Route::post('/seasonal/promotions/list', [MasterController::class, 'SeasonalPromotionsList']);
        Route::post('/seasonal/promotions/delete', [MasterController::class, 'SeasonalPromotionsdelete']);
        Route::post('/offer/type', [MasterController::class, 'OfferType']);
        Route::post('/member-list-dr', [BookingController::class, 'member_list_dr']);
        Route::post('/doctor-treatment-list', [BookingController::class, 'doctor_treatment_list']);
        

        //doctor
        Route::post('/doctor/list/token', [BookingController::class, 'DoctorListToken']);
        Route::post('/booking/pending/list', [BookingController::class, 'BookingPendingList']);
        Route::post('/booking/accept/list', [BookingController::class, 'BookingAcceptList']);
        Route::post('/booking/reject', [BookingController::class, 'rejectBooking']);
        Route::post('/booking/accept', [BookingController::class, 'AcceptBooking']);
        Route::post('/booking/date/filter', [BookingController::class, 'BookingPendingDateFilter']);
        Route::post('/doctor/cancel/reason', [BookingController::class, 'DoctorCancelReason']);
        Route::post('/doctor/reschedule/booking', [BookingController::class, 'DoctorRescheduleBooking']);
        Route::post('/doctor-clicnic-list', [BookingController::class, 'DoctorclinicList']);
        Route::post('/doctor-appointment-timeslot', [BookingController::class, 'doctorappointmenttimeslot']);
        Route::post('/bookings/prescription',  [BookingController::class, 'addPrescription']);
        Route::post('/bookings/update/prescription',  [BookingController::class, 'updatePrescription']);
        Route::post('/bookings/send/prescription',  [BookingController::class, 'prescriptionSend']);
        Route::post('/bookings/drug/name',  [BookingController::class, 'drugName']);
        Route::post('/bookings/patient/history',  [BookingController::class, 'BookingPatientHistory']);
        Route::post('/bookings/history/detail/patient',  [BookingController::class, 'HistoryDetailPatient']);
        Route::post('/bookings/history/detail/doctor',  [BookingController::class, 'DoctorHistoryDetails']);
        Route::post('/appointment/new/user',  [BookingController::class, 'AppointmentNewUser']);
        Route::post('/appointment/already/UserOtp/send',  [BookingController::class, 'AppointmentAlreadyUserOtpSend']);
        Route::post('/appointment/already/verify/userotp',  [BookingController::class, 'AppointmentAlreadyUserOtpVerify']);
        Route::post('/appointment/booking/doctor',  [BookingController::class, 'AppointmentBookingDoctor']);
        Route::post('/doctor/user/check',  [BookingController::class, 'DoctorUserCheck']);
        Route::post('/patient/payment',  [BookingController::class, 'PatientPayment']);
        Route::post('/patient/payment/list',  [BookingController::class, 'PatientPaymentList']);
        Route::post('/doctor/feedback',  [BookingController::class, 'DoctorFeedback']);
        Route::post('/doctor/setting/data',  [BookingController::class, 'DoctorSettingData']);

        Route::post('/treatments', [TreatmentController::class, 'store']);
        Route::post('/treatments/show', [TreatmentController::class, 'show']);
        Route::post('/treatments/update', [TreatmentController::class, 'update']);
        Route::post('/treatments/delete/{id}', [TreatmentController::class, 'destroy']);
        //dr list
        Route::post('doctor-list', [TreatmentController::class, 'doctor_list'])->name('doctor-list');

        Route::post('/packages', [PackageController::class, 'store']);
        Route::get('/packages/index', [PackageController::class, 'index']); // List all packages
        Route::get('/packages/show/{id}', [PackageController::class, 'show']); // View single package
        Route::put('/packages/update/{id}', [PackageController::class, 'update']);
        Route::delete('/packages/delete/{id}', [PackageController::class, 'destroy']);
        
        Route::post('/package/subscribe', [SubscriptionController::class, 'chooseSubscription']);
        Route::post('/package/updatepackage', [SubscriptionController::class, 'updatepackage'])->name('updatepackage');
        Route::get('/doctor/get-profile', [DoctorDetailController::class, 'getProfile']);
        Route::get('/doctor/myconfig', [DoctorDetailController::class, 'myconfig']);
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
        Route::post('doctor/update-points', [DoctorDetailController::class, 'updatePoints'])->name('update-points');

        Route::post('doctor/add-clinic-establishmentAddress',[DoctorDetailController::class, 'AddClinicEstablishmentAddress']);
        Route::post('doctor/delete-clinic',[DoctorDetailController::class, 'deleteClinic']);
        Route::post('doctor/clinicdetail',[DoctorDetailController::class, 'clinicdetail']);
        Route::post('doctor/update-clinic-establishmentAddress',[DoctorDetailController::class, 'UpdateClinicEstablishmentAddress']);
        Route::post('doctor/add-clinic-timings',[DoctorDetailController::class, 'AddClinicTimings']);
        Route::post('doctor/add-clinic-holidays',[DoctorDetailController::class, 'AddClinicHolidays']);
        Route::post('doctor/update-clinic-holidays',[DoctorDetailController::class, 'UpdateClinicHolidays']);
        Route::post('doctor/add-clinic-gallery',[DoctorDetailController::class, 'AddClinicGallery']);
        Route::post('doctor/single-all-clinic-details',[DoctorDetailController::class, 'clinic_list']);
        Route::get('/package/upgradeparentlist', [SubscriptionController::class, 'upgradeparentlist'])->name('upgradeparentlist');
        Route::post('/package/upgradeSubscription', [SubscriptionController::class, 'upgradeSubscription'])->name('upgradeSubscription');

        Route::post('/doctor/home', [DoctorController::class, 'home']);
        Route::post('/doctor/bookingStart', [DoctorController::class, 'bookingStart']);
        Route::post('/doctor/bookingEnd', [DoctorController::class, 'bookingEnd']);
        Route::post('/doctor/selfpackage', [SubscriptionController::class, 'selfpackage']);
        Route::post('/doctor/selfpackageactiveinactive', [SubscriptionController::class, 'selfpackageactiveinactive']);
        Route::post('/doctor/selfpackage_create', [SubscriptionController::class, 'selfpackage_create']);
        Route::post('/doctor/selfpackage_update', [SubscriptionController::class, 'selfpackage_update']);
        Route::post('/selfpackage_delete1', [SubscriptionController::class, 'selfpackage_delete']);

        Route::post('/doctor/feedback-history', [DoctorHistoryController::class, 'getDoctorRatings']);
        Route::post('/doctor/earning-stats', [DoctorHistoryController::class, 'getDoctorEarningStats']);
        Route::post('/doctor/earning-history', [DoctorHistoryController::class, 'getDoctorEarningHistory']);
        Route::post('/doctor/earning-history-graph', [DoctorHistoryController::class, 'getDoctorEarningHistoryGraph']);
        Route::post('/doctor/list-of-register-patient', [DoctorHistoryController::class, 'listofegisterpatient']);    
        Route::post('/doctor/register-patient-bookingshistory', [DoctorHistoryController::class, 'bookinghistory']);  
        Route::post('/doctor/register-patient-precriptionhistory', [DoctorHistoryController::class, 'precriptionhistory']);
        Route::post('/doctor/register-patient-loyalitypointshistory', [DoctorHistoryController::class, 'loyalitypoints']);
        Route::post('/doctor/register-patient-acitvepackagehistory', [DoctorHistoryController::class, 'PackTreatmentActive']);
        Route::post('/doctor/register-patient-medicalrecords', [DoctorHistoryController::class, 'medicalrecords']);

        Route::post('/doctor/list-notification',[DoctorHistoryController::class, 'listNotification']);
        Route::post('/doctor/total-earning',[DoctorHistoryController::class, 'earningList']);
        Route::post('/doctor/conditional-appointment-list',[DoctorHistoryController::class, 'bookingList']);
        Route::post('/doctor/loyalitypoints', [DoctorHistoryController::class, 'loyalitypointsindiv']);
        Route::post('/doctor/modify-loyalitypoints', [DoctorHistoryController::class, 'modifyLoyalitypointsindiv']);
        Route::post('/doctor/best-customer', [DoctorHistoryController::class, 'bestCustomer']);
        Route::post('/doctor/my-doc-performed', [DoctorHistoryController::class, 'mydocperformace']);
        Route::post('/doctor/my-doc-performed-detail', [DoctorHistoryController::class, 'mydocperformacedetails']);
        Route::post('/doctor/my-doc-patient-plan-performance', [DoctorHistoryController::class, 'myplanperformance']);
        Route::post('/doctor/list-clinictime',[DoctorHistoryController::class, 'listtime']);
        Route::post('/doctor/add-clinictime',[DoctorHistoryController::class, 'addtime']);
        Route::post('/doctor/update-clinictime',[DoctorHistoryController::class, 'updatetime']);




    });


});