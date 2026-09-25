<?php

namespace App\Http\Controllers\Api;

use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\Generalsetting;
use App\Models\UserNotification;
use App\Models\UserSubscription;
use App\Models\GuestUserLoginHistory;
use App\Models\GuestUser;
use App\Models\Invitation;
use App\Traits\SdSendSms;
use Carbon\Carbon;
use File;
// use Faker\Generator;
// use Illuminate\Container\Container;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
class AuthController extends Controller
{
    use SdSendSms;
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
            'name' => 'required|string',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNOTIN('status', [2]);
                }),
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNOTIN('status', [2]);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $otp = $input['otp']; //mt_rand(1000, 9999);
        $user_message = "One Time Password " . $otp . " to verify your Mobile on";
        // $phone = $input['country_code'] . $input['mobile'];
        // $this->twofactorsms($otp,$phone);
        $msg = 'Dear User,Your login OTP for using Dentist platform is '.$otp.'. Please do not share this code with anyone. TBASPL';
        $this->otpmsg_sd($input['mobile'],$msg,'1705174825127580471','','582582'); 
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
            'password' => 'required|min:1|max:100',
            // 'device_id' => 'required',
            // 'device_token' => 'required',
            // 'device_type' => 'required',
            'name' => 'required'
        ]);

        $data['ip_address'] = request()->ip();
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        $parent_id = 0;
        $checkinvitation = Invitation::where('email',$data['email'])->where('mobile',$data['mobile'])->where('status',0)->first();
        if ($checkinvitation) {
            $parent_id = $checkinvitation->doctor_id;
        }
        $data['email'] = trim($data['email']);
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 3;
        $data['parent_id'] = $parent_id;
        $user = User::create($data);
        $token = $user->createToken('MyApp')->accessToken;
        $user->qrcode = $this->QrcodeCreation($user->id);
        $user->save();
        if ($checkinvitation) {
            $checkinvitation->user_id = $user->id;
            $checkinvitation->save();
        }
        $u = User::find($user->id);
        $response = ['status' => true, 'token' => $token, 'user_detail' => $u, 'signup_skip' => false,"checkinvitation"=>$checkinvitation];
        return response($response, 200);
    }


    // public function register($data, $user_type, $login_type, $created_by = null)
    // {
    //     $validator = Validator::make($data, [
    //         // 'country_code' => 'required|string',
    //         'email' => [
    //             'required',
    //             Rule::unique('users')->where(function ($query) {
    //                 return $query->whereNOTIN('status', [2]);
    //             }),
    //         ],
    //         'mobile' => [
    //             'required',
    //             Rule::unique('users')->where(function ($query) {
    //                 return $query->whereNOTIN('status', [2]);
    //             }),
    //         ],
    //         'password' => 'required|min:1|max:100',
    //         'device_id' => 'required',
    //         'device_token' => 'required',
    //         'device_type' => 'required',
    //         'name' => 'required'
    //     ]);

    //     $data['ip_address'] = request()->ip();
    //     if ($validator->fails()) {
    //         return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
    //     }
    //     $data['email'] = trim($data['email']);
    //     $data['password'] = Hash::make($data['password']);
    //     $data['status'] = 3;
    //     $user = User::create($data);
    //     $token = $user->createToken('MyApp')->accessToken;
    //     $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false];
    //     return response($response, 200);
    // }

    public function createReferCode($username, $user_id)
    {
        $user_name = explode(" ", strtolower($username));
        $refer_code = $user_name[0] . $user_id;
        User::whereId($user_id)->update(['referral_code' => $refer_code,'user_login'=>1]);
        return $refer_code;
    }

    public function createuserName($username, $user_id)
    {
        $user_name = explode(" ", strtolower($username));
        $refer_code = $user_name[0] . $user_id;
        User::whereId($user_id)->update(['username' => $refer_code,"step_one"=>1]);
        return $refer_code;
    }

    public function createuserName2($username, $user_id)
    {
        $user_name = explode(" ", strtolower($username));
        $refer_code = $user_name[0] . $user_id;
        return $refer_code;
    }


    public function loginUser(Request $request)
    {
        $input = $request->all();
        return $this->login($input, 1, 'app');
    }

    public function loginTeacher(Request $request)
    {
        $input = $request->all();
        return $this->login($input, 2, 'app');
    }

    public function logout(Request $request)
    {
        User::whereId(auth()->user()->id)->update(['user_login'=>0]);
        $token = $request->user()->token();
        $token->revoke();
        $response = ['status' => true, 'message' => 'You have been succesfully logged out!'];
        return response($response, 200);
    }


    public function login($data, $user_type)
    {
        $validator = Validator::make($data, [
            'emailmobile' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        if (strpos($data['emailmobile'], '@') !== false) {
            $user = User::where('email', $data['emailmobile'])->whereIN('status',[1,3])->first();
        } else {
            $user = User::where('mobile', $data['emailmobile'])->whereIN('status',[1,3])->first();
        }

        $validator = Validator::make($data, [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        
        if ($user) {
            if ($user->password == '') {
                return response(['msg' => 'Password is wrong.', 'status' => false], 200);
            }
            return $this->checkLoginCred($data['password'], $user->password, $user, $data);
        } else {
            $response = ['status' => false, 'msg' => 'User not exist Please Signup for registration!.'];
            return response($response, 422);
        }
    }

    function sendOtp(Request $request) {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'country_code' => 'required|string',
            'mobile' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        $country_code = '+91';
        $check = User::where('mobile',$request->mobile)->where('status',1)->first();
        if($check) {
            if ($check->country_code) {
                if ($check->country_code != $request->country_code) {
                    return response(['msg' =>'User not found', 'status' => false]);
                }
                else {
                    $country_code = $request->country_code;
                }
            }
            // where('country_code', $request->country_code)->
        }
        else {
            return response(['msg' =>'User not found', 'status' => false]);
        }
        $otp = $request->otp;
        $user_message = "One Time Password " . $otp . " to verify your Mobile on Goyal's Online Support";
        $phone = $country_code . $request->mobile;
        $msg = 'Dear User,Your login OTP for using Dentist platform is '.$otp.'. Please do not share this code with anyone. TBASPL';
        $this->otpmsg_sd($request->mobile,$msg,'1705174825127580471','','582582'); 
        $this->twofactorsms($otp,$phone);
        return response()->json(['status' => true, 'msg' => 'otp send']);
        
    } 

    function otpLogin(Request $request) {
        $data = $request->all();
        $validator = Validator::make($data, [
            'country_code' => 'required|string',
            'mobile' => 'required|string',
            // 'device_id' => 'required',
            // 'device_token' => 'required',
            // 'device_type' => 'required',
            // 'loginTime' => 'required',
            // 'model_name' => 'required',
            // 'carrier_name' => 'required',
            // 'device_country' => 'required',
            // 'device_memory' => 'required',
            // 'have_notch' => 'required',
            // 'manufacture' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        if(User::where('country_code', $request->country_code)->where('mobile',$request->mobile)->count() == 0) {
            return response(['msg' =>'User not found', 'status' => false]);
        }

        $user = User::where('country_code', $request->country_code)->where('mobile',$request->mobile)->first();

        $user->update([
            'device_id' => $data['device_id'] ?? '',
            'device_token' => $data['device_token'] ?? '',
            'device_type' => $data['device_type'] ?? '',
            'loginTime' => $data['loginTime'] ?? '',
            'model_name' => $data['model_name'] ?? '',
            'carrier_name' => $data['carrier_name'] ?? '',
            'device_country' => $data['device_country'] ?? '',
            'device_memory' => $data['device_memory'] ?? '',
            'have_notch' => $data['have_notch'] ?? '',
            'manufacture' => $data['manufacture'] ?? '',
            'ip_address' => request()->ip(),
            'user_login' => 1,
        ]);
        $token = $user->createToken('MyApp')->accessToken;
        $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false];
        return response($response, 200);
    }

    public function checkLoginCred($input_password, $saved_password, $user, $data)
    {
        if (Hash::check($input_password, $saved_password)) {
            $token = $user->createToken('MyApp')->accessToken;
            if (isset($data['device_id'])) {
                User::whereId($user->id)->update([
                    'device_id' => $data['device_id'],
                    'device_token' => $data['device_token'],
                    'device_type' => $data['device_type'],
                    'ip_address' => request()->ip()
                ]);
            }
            $checksubscription = UserSubscription::where('user_id',$user->id)->where('status',1)->count();
            $user->approved = $user->status;
            $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false,'checksubscription'=>$checksubscription];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Oops ! Your credentials are wrong.'];
            return response($response, 422);
        }
    }

    public function sendOtpForForgotPassword(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'emailmobile' => 'required|string',
            'otp' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
    
        if (strpos($input['emailmobile'], '@') !== false) {
            $user = User::where('email', $input['emailmobile'])->whereIn('status', [1, 3])->first();
        } else {
            $user = User::where('mobile', $input['emailmobile'])->whereIn('status', [1, 3])->first();
        }
    
        if ($user) {
            $msg = 'Dear User,Your login OTP for using Dentist platform is '.$input['otp'].'. Please do not share this code with anyone. TBASPL';
            $this->otpmsg_sd($user->mobile,$msg,'1705174825127580471','','582582'); 
            if ($user->email) {
                $data = [
                    'to' => $user->email,
                    'subject' => "Dentus Verification Code",
                    'name' => $user->name,
                    'generalsettings' => Generalsetting::find(1),
                    'otp' => $input['otp']
                ];
    
                // Send the email using Mail::send
                Mail::send('dentusotp', compact('data'), function ($message) use ($data) {
                    $message->to($data['to'])
                            ->subject($data['subject']);
                });
    
                $response = ['status' => true, 'msg' => 'Forgot password verification OTP sent to your email. Please verify to continue.', 'user_id' => $user->id];
            }
        } else {
            $response = ['status' => false, 'msg' => 'Sorry, the number is not registered with us. Please correct the number.'];
        }
    
        return response($response, 200);
    }
    

    public function resetUserPassword(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'emailmobileusername' => 'required|string',
            'user_id' => 'required|integer',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $password = Hash::make($input['password']);
        User::where('id', $input['user_id'])->update(['password' => $password]);
        $response = ['status' => true, 'msg' => 'Password updated successfully. Please login with your new password.'];
        return response($response, 200);
    }

    public function social_connect_user(Request $request)
    {
        $input = $request->all();
        return $this->social_connectlogin($input, 1);
    }

    public function social_connect_teacher(Request $request)
    {
        $input = $request->all();
        return $this->social_connectlogin($input, 2);
    }

    public function social_connectlogin($data, $user_type)
    {

        //login
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string',
            'signupby' => 'required',
            'device_id' => 'required',
            'device_token' => 'required',
            'device_type' => 'required',
            'loginTime' => 'required',
            'model_name' => 'required',
            'carrier_name' => 'required',
            'device_country' => 'required',
            'device_memory' => 'required',
            'have_notch' => 'required',
            'manufacture' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        if (strpos($data['email'], '@') !== false) {
            $user = User::where('email', $data['email'])->where([/*['user_type','=', $user_type],*/['status', '<>', 2]])->first();
        } else {
            $user = User::where('mobile', $data['email'])->where([/*['user_type','=', $user_type],*/['status', '<>', 2]])->first();
        }



        if ($user) {
            if ($user->status == 0) {
                return response()->json(['status' => false, 'msg' => 'Your account is inactive. Please contact our customer support for more information.'], 422);
            } else {
                $token = $user->createToken('MyApp')->accessToken;
                User::whereId($user->id)->update([
                    'device_id' => $data['device_id'],
                    'device_token' => $data['device_token'],
                    'device_type' => $data['device_type'],
                    'loginTime' => $data['loginTime'],
                    'model_name' => $data['model_name'],
                    'carrier_name' => $data['carrier_name'],
                    'device_country' => $data['device_country'],
                    'device_memory' => $data['device_memory'],
                    'have_notch' => $data['have_notch'],
                    'manufacture' => $data['manufacture'],
                    'ip_address' => request()->ip(),
                    'user_login' => 1,
                ]);
                $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false];
                return response($response, 200);
            }
        } else {
            // $validator = Validator::make($data, [
            //     'name' => 'required|string|max:255',
            //     'email' => [
            //         'required',
            //         Rule::unique('users')->where(function ($query) {
            //             return $query->whereNOTIN('status', [2]);
            //         }),
            //     ],
            // ]);

            // $data['user_type'] = $user_type;

            // if ($validator->fails()) {
            //     return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
            // }
            // $data['password'] = Hash::make('GBP@Pass');
            // $data['signupby'] = $data['signupby'];
            // $data['ip_address'] = request()->ip();
            // $user = User::create($data);
            // $token = $user->createToken('MyApp')->accessToken;
            // $user_name = explode(" ", strtolower($user->name));
            // $username = $user_name[0] . $user->id;
            // User::whereId($user->id)->update(['username' => $username, 'status' => 1]);
            // $created_refer_code = $this->createReferCode($user->name, $user->id);
            $response = ['status' => false, 'signup_skip' => false];
            return response($response, 200);
        }
    }

    public function social_connect_apple(Request $request)
    {
        $input = $request->all();
        return $this->social_connectloginapple($input, 3);
    }

    public function social_connectloginapple($data, $user_type)
    {

        //login
        $validator = Validator::make($data, [
            'email' => 'required|string',
            'login_type' => 'required|string',
            'devicetoken' => 'required|string',
            'apple_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $user = User::where('apple_token', $data['apple_token'])->where('user_type', $user_type)->first();

        if (isset($user->register_verification) && (!$user->register_verification)) {

            return response()->json(['status' => false, 'msg' => 'Your account is not verified. Please contact our customer support for more information.'], 422);
        }

        if (isset($user->register_verification) && (!$user->status)) {
            return response()->json(['status' => false, 'msg' => 'Your account is inactive. Please contact our customer support for more information.'], 422);
        }

        if ($user) {
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false];
            return response($response, 200);
        } else {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
            ]);

            $data['user_type'] = $data['user_type'];

            if ($validator->fails()) {
                return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
            }
            $data['password'] = Hash::make('Humsafar@Fuel');
            $data['login_type'] = $data['login_type'];
            $data['created_by'] = 0;
            $otp = mt_rand(1000, 9999);
            $data['register_otp'] = $otp;
            $data['device_type'] = $data['device_type'] ?? null;
            $device_tok = null;
            if (isset($data['devicetoken']) && null !=  $data['devicetoken'] && $data['devicetoken'] != 'fsadfasf') {
                $device_tok = $data['devicetoken'];
            }
            $data['devicetoken'] = $device_tok;
            $data['refer_by'] = $data['refer_by'] ?? null;


            $data['register_verification'] = true;
            $user = User::create($data);
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;

            if ($user_type === 3) {
                $settings = DB::table('settings')
                    ->select('adminnumber')
                    ->first();
                $admin['phone'] = $settings->adminnumber;
                $admin['msg'] = 'Customer Sign Up Alert ! Name : ' . str_replace(' ', '%20', $data['name']);
                $admin['temp_id'] = '1707161773076198662';
                // SendSms::dispatch($admin);
                $otpmsg_sms = $this->otpmsg_sd($admin['phone'], $admin['msg'], $admin['temp_id']);
                $created_refer_code = $this->createReferCode($user->name, $user->id);
            }

            $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false];
            return response($response, 200);
        }
    }

    public function listNotification(Request $request)
    {
        UserNotification::where('user_id', auth()->user()->id)->where('read', 0)
       ->update([
           'read' => 1
        ]);
        $list = UserNotification::limit(37)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->whereNOTIN('status',[2])->get();
        $mainlist = array();
        foreach ($list as $key) {
            $created_at = '';
            $updated_at = '';

            if ($key->created_at) {
                $created_at = Carbon::parse($key->created_at)->format('d/m/Y H:i a');
            }
            if ($key->updated_at) {
                $updated_at = Carbon::parse($key->updated_at)->format('d/m/Y H:i a');
            }
            $mainlist[] = array("id"=> $key->id,
                                "user_id"=> $key->user_id,
                                "prod_id"=> $key->prod_id,
                                "title"=> $key->title,
                                "notification"=> $key->notification,
                                "notification_type"=> $key->notification_type,
                                "image"=> $key->image,
                                "status"=> $key->status,
                                "read"=> $key->read,
                                "created_at"=> $created_at,
                                "updated_at"=> $updated_at,);
        }
        $response = ['status' => true, 'list' => $mainlist,"path"=>asset('project/public/notification')];
        return response($response, 200);
    }

    public function testnotific(Request $request)
    {
        $input = $request->all();
        $file = time() . rand() . '_file.json';
        $destinationPath = "project/checkNOTIlogs/";
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, json_encode($input));
        $usertype = $input['usertype'];
        $image = 'default.png';
        $message = $input['message'];
        $title = $input['title'];
        $order_id = $input['order_id'];
        $for_ = $input['for_'];
        // $image = '';


        $devicetoken = User::where('id', '=', $input['id'])->where('device_token', '!=', '')->first();
        if ($devicetoken) {
            if ($devicetoken->device_type == 'android') {
                $notification_type1 = "text";
                $respJson1 = '{"notification_type":"' . $notification_type1 . '","title":"' . $title . '","msg":"' . $message . '","image":"' . $image . '","type":"no"}';
                if ($usertype == 2 && $order_id > 0) {
                    $message2 = array(
                        'body' => $message,
                        'title' => $title,
                        'image' => $image,
                        'sound' => 'Default',
                        'type' => 'normal',
                        'data' => array(
                            'body' => $message,
                            'title' => $title,
                            'image' => $image,
                            'sound' => 'Default',
                            'type' => 'normal',
                            'icon' => 'ic_notification',
                            'color' => '#18d821',
                            'sound' => 'default',
                            'priority' => 'high',
                            'activityType' => $for_
                        )
                    );
                } else {
                    $message2 = array(
                        'body' => $message,
                        'title' => $title,
                        'image' => $image,
                        'sound' => 'Default',
                        'type' => 'normal',
                        'activityType' => $for_
                    );
                }

                $a = $this->sendMessageThroughFCM([$devicetoken->device_token], $message2, $usertype);
                $nn = new UserNotification();
                $nn->user_id = $input['id'];
                $nn->notification_type = $for_;
                $nn->title = $title;
                $nn->notification = $message;
                $nn->prod_id = $order_id;
                $nn->image = $image;
                $nn->save();
            } else {
                // $notification_type1 = "text";
                // $respJson1 = '{"notification_type":"'.$notification_type1.'","title":"'.$title.'","msg":"'.$message.'","type":"no"}';
                // $message2 = array(
                //         'body' => $message,
                //         'title' => $title,
                //         'sound' => 'Default'
                //     );
                // $a = $this->send_ios_to_user($device_token->devicetoken,$message,$usertype);
                $nn = new UserNotification();
                $nn->user_id = $input['id'];
                $nn->notification_type = $for_;
                $nn->title = $title;
                $nn->notification = $message;
                $nn->prod_id = $order_id;
                $nn->image = $image;
                $nn->save();
            }
        }
    }

    public function checkUsername(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'username' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNOTIN('status', [2]);
                }),
            ],
        ]);
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $response = ['status' => true, 'msg' => 'username can take'];
        return response($response, 200);
    }

    public function checkEmail(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNOTIN('status', [2]);
                }),
            ],
        ]);
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $response = ['status' => true, 'msg' => 'email can take'];
        return response($response, 200);
    }

    public function checkMobile(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->whereNOTIN('status', [2]);
                }),
            ],
        ]);
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $response = ['status' => true, 'msg' => 'mobile can take'];
        return response($response, 200);
    }

    public function otpcheck($value='')
    {
        $otp = mt_rand(1000, 9999);
        $user_message = "One Time Password " . $otp . " to verify your Mobile on Goyal's Online Support";
        $phone = '918290838118';
        $msg = $user_message;
        $temp_id = '1707161761166396747';
        $entity_id = '1701159793007694875';
        $otpmsg = $this->otpmsg_sd($phone, $msg, $temp_id, $entity_id);
    }

    public function BackithreadNotiwebinarapi(Request $request)
    {
        $input = $request->all();
        $file = 'webinar'.time() . rand() . '_file.json';
        $destinationPath = "project/checkNOTIlogs/";
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, json_encode($input));
        $getuser = WebinarEnrollment::where('webinar_id',3)->where('status',1)->with('userdetails')->get();
        if ($getuser->count() > 0) {
            foreach ($getuser as $key) {
                $subject = $key->webind->name.' completed!';
                $data = [
                    'to' => $key->userdetails->email,
                    'subject' => "Webinar completed!",
                    'name' => $key->userdetails->name,
                    'generalsettings'=>Generalsetting::find(1),
                    'webinarid'=>$key->webinar_id,
                    'datetime' =>date('d F Y',strtotime($key->webind->datetime))
                ];
                $view  = view('emails.webinarcertificate',compact('data'))->render();
                // $fileName = public_path('webinar/certificate/').$pdfname;
                if (is_null($key->certificate)) {
                    $pdfname = Str::random(10).'.pdf';
                    $pdf = PDF::loadView('emails.webinarcertificate', compact('data'));//->save($fileName);
                    $path = Storage::disk('s3')->put('webinarcertificate/'.$pdfname, $pdf->output());
                    $content = 'https://gbp-Pegasus.s3.ap-south-1.amazonaws.com/webinarcertificate/'.$pdfname;
                    $update = WebinarEnrollment::find($key->id);
                    $update->certificate = $content;
                    $update->save();
                }
                else {
                    $content = $key->certificate;
                }
                
                // $this->check_curl($key->userdetails->email,$key->userdetails->name,$subject,$view,'webinar completed');
                $mail = $this->check_curl_for_sendinblue($key->userdetails->email,$key->userdetails->name,$subject,$view,'webinar completed','sachinappslure@gmail.com','',$content);
                
            }
        }
    }

    public function deleteAccount(Request $request)
    {
         $update = array("status"=>2,
                        );
        User::whereId(auth()->user()->id)->update($update);
        $token = $request->user()->token();
        $token->revoke();
        $response = ['status' => true, 'message' => 'Account deleted succesfully!'];
        return response($response, 200);
    }


    public function QrcodeCreation($id)
    {
        $encryptedString = Crypt::encrypt($id);
        $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($encryptedString)
                ->size(300)
                ->margin(10)
                ->backgroundColor(new Color(255, 255, 255))
                ->foregroundColor(new Color(0, 0, 255))
                ->logoPath('content/Artboard.png')
                ->logoResizeToWidth(50)
                ->build();
        $name = rand().'_qr_' . time() .'.png';
        $qrCode->saveToFile('content/doctor/qrcode/' . $name);
        return $name;
    }
    
}
