<?php

namespace App\Http\Controllers\Api\Users;

use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\UserModels\UserProfile;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\Generalsetting;
use App\Models\UserNotification;
use App\Models\UserSubscription;
use App\Models\GuestUserLoginHistory;
use App\Models\GuestUser;
use App\Models\UserEstablishmentClinic;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\ClinicDoctor;
use App\Traits\SdSendSms;
use Carbon\Carbon;
use File;
// use Faker\Generator;
// use Illuminate\Container\Container;
class UserController extends Controller
{
    // use SdSendSms;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyRegisterOtpSD(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'otp' => 'required|string',
            // 'mobile' => [
            //     'required',
            //     Rule::unique('users')->where(function ($query) {
            //         return $query->whereNOTIN('status', [2]);
            //     }),
            // ],
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $otp = $input['otp']; //mt_rand(1000, 9999);
        $user_message = "One Time Password " . $otp . " to verify your Mobile on";
        // $phone = $input['country_code'] . $input['mobile'];
        // $this->twofactorsms($otp,$phone);
        if ($request->email) {
            // $data = [
            // 'to' => $request->email,
            // 'subject' => "Pegasus Verification Code",
            // 'name' => $request->username,
            // 'generalsettings'=>Generalsetting::find(1),
            // 'otp'=>$otp
            // ];
            // $view  = view('emails.otpapp',compact('data'))->render();
            // $subject = "Dentus Verification Code";
            // $mail = $this->check_curl_for_sendinblue($request->email,$request->username,$subject,$view,'otp');
        }

        $response = ['status' => true, 'msg' => 'OTP Send successfully.'];
        return response($response, 200);
    }


    public function verifyUserOtp(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->first(), 'status' => false], 422);
        }

        // Check if the user exists
        $user = UserProfile::where('mobile', $request->mobile)->first();
        if(!empty($user->clinic_id)){
            $clinic = UserEstablishmentClinic::where('id', $user->clinic_id)->first();
            }

        if ($user) {
            // User exists
            $doctor = User::where('id',$clinic->user_id)->first();
            $token = $user->createToken('MyApp')->accessToken;
            $clinic->logo =  asset('project/public/clinics/').'/'.$clinic->logo ;
            return response(['msg' => 'User already exists','token' => $token, 'user_detail' => $user,'clini_detail' => $clinic,'doctor' => $doctor,'newuser' => 0, 'status' => true]);
        } else {
            // User does not exist
            return response(['status' => true, 'newuser' => 1, 'msg' => 'New user']);
        }
    }

    public function newSignupUser(Request $request)
    {
        $input = $request->all();
        return $this->register($input, 1, 'app');
    }

    public function register($data, $user_type, $login_type, $created_by = null)
    {
        $validator = Validator::make($data, [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNotIn('status', [2]);
                }),
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNotIn('status', [2]);
                }),
            ],
            'device_id' => 'required',
            'device_token' => 'required',
            'device_type' => 'required',
            'name' => 'required|string',
            'last_name' => 'required|string',
            // 'referral_code' => 'required|string',
            'clinic_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->first(), 'status' => false], 422);
        }


        $data['ip_address'] = request()->ip();
        $data['name'] = trim($data['name']);
        $data['last_name'] = trim($data['last_name']);
        $data['email'] = trim($data['email']);
        $data['mobile'] = trim($data['mobile']);
        $data['referral_code'] = trim($data['referral_code']);
        $data['clinic_id'] = trim($data['clinic_id']);
        $data['status'] = 1;


        $user = UserProfile::create($data);
        $clinic = UserEstablishmentClinic::where('id', $user->clinic_id)->first();
        $token = $user->createToken('MyApp')->accessToken;

        return response(['status' => true, 'token' => $token, 'user_detail' => $user,'clini_detail' => $clinic, 'signup_skip' => false], 200);
    }





}
