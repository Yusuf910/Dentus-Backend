<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use DB;
use HasApiTokens;

class AuthController extends Controller
{

     // Handle registration
     public function register(Request $request)
     {
         // Validate input data
         $validator = Validator::make($request->all(), [
             'first_name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             'mobile' => 'required|string|max:15|unique:users',
             'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
         ]);

         if ($validator->fails()) {
            $errors = implode(' ', $validator->errors()->all()); // Join all errors into a single string
            $response = [
                'status' => false,
                'msg' => $errors, // Send all errors in a single line
            ];
            return response()->json($response, 422);
        }

         // Generate static OTP (222222)
         $otp = '222222';

         // Store user details temporarily (could also use session if needed)
         $tempUser = [
             'first_name' => $request->first_name,
             'email' => $request->email,
             'mobile' => $request->mobile,
             'password' => $request->password, // Store hashed password for later insertion
             'otp' => $otp, // OTP
         ];

         // Return success response with OTP for simulation
         return response()->json([
             'status' => 'success',
             'message' => 'OTP sent to your phone. Verify OTP to complete registration.',
             'data' => [
                 'temp_user' => $tempUser,
                 'otp' => $otp // Send OTP for now since no SMS gateway
             ]
         ], 201);
     }

    // Handle registration
    // public function register(Request $request)
    // {
    //     // Validate input data
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'mobile_number' => 'required|string|max:15|unique:users',
    //         'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    //     }

    //     // Generate static OTP (222222)
    //     $otp = '222222';

    //     // Create the user with OTP
    //     $user = User::create([
    //         'first_name' => $request->first_name,
    //         'email' => $request->email,
    //         'mobile_number' => $request->mobile_number,
    //         'password' => $request->password,
    //         'otp' => $otp, // Assign the OTP
    //     ]);

    //     // Return success response with OTP for simulation
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'User registered successfully! Verify using OTP.',
    //         'data' => [
    //             'user_id' => $user->id,
    //             'otp' => $otp // Send the OTP in response for now (since no SMS gateway)
    //         ]
    //     ], 201);
    // }


        public function verifyOtp(Request $request)
        {
            // Validate OTP input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile' => 'required|string|max:15|unique:users',
                'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
                'otp' => 'required|string',
            ]);

            if ($validator->fails()) {
                $errors = implode(' ', $validator->errors()->all()); // Join all errors into a single string
                $response = [
                    'status' => false,
                    'msg' => $errors, // Send all errors in a single line
                ];
                return response()->json($response, 422);
            }

            // Verify the OTP
            if ($request->otp === '222222') {
                // If OTP is valid, create the user in the database

                $user = User::create([
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'password' => bcrypt($request->password),
                    'is_verified' => true, // Mark user as verified
                    'status' => 1
                ]);
                // $token = $user->createToken('MyApp')->accessToken;
                return response()->json([
                    'status' => 'success',
                    'message' => 'User verified and created successfully!',
                    'data' => $user,
                    // 'token' => $token
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP!'
            ], 401);
        }


    // Handle OTP verification
    //     public function verifyOtp(Request $request)
    // {
    //     // Validate OTP input
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required|exists:users,user_id',
    //         'otp' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    //     }

    //     // Find the user by ID using Eloquent
    //     $user = User::where('user_id', $request->user_id)->first();

    //     // Check if user exists
    //     if (!$user) {
    //         return response()->json(['status' => 'error', 'message' => 'User not found!'], 404);
    //     }

    //     // Check if the provided OTP matches the user's OTP
    //     if ($user->otp == $request->otp) {
    //         $user->is_verified = true;
    //         $user->otp = null; // Clear OTP after verification
    //         $user->save(); // Save changes using Eloquent

    //         return response()->json(['status' => 'success', 'message' => 'User verified successfully!']);
    //     }

    //     return response()->json(['status' => 'error', 'message' => 'Invalid OTP!'], 401);
    // }

    // public function password_reset()
    // {
    //   return view('admin.password_reset');
    // }

    function reset_check(Request $request){

        if(!empty($request->email))
        {
          $checkphone = User::where('email',$request->email)->first();
         if($checkphone){
          $password = $checkphone->remember_password;
          $otp = 222222;//rand(1000,9999);
        //   $checkphone->otp = $otp;
        //   $checkphone->save();
          $data = ['name' => $checkphone->name,  'data' => $otp];
          Mail::send('mail', $data, function ($message) use ($checkphone) {
              $message->from('cotalajbh2@gmail.com', 'Dentus');
              $message->replyTo('cotalajbh2@gmail.com', 'Dentus');
              $message->returnPath('cotalajbh2@gmail.com', 'Dentus');
              $message->to($checkphone->email);
              $message->subject('Dentus | Reset your password');
          });
          return response()->json(['status' => 'success', 'message' => 'Password reset link sent!']);
        }
        else
        {
            return response()->json(['status' => 'error', 'message' => 'Unable to send reset link']);
        }
        }
   }

         // Step 2: Verify OTP
    public function forgotverifyOtp(Request $request)
    {
        // Validate the email and OTP
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => $validator->errors()->first(),
            ], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the OTP is correct
        if ($user->otp == $request->otp) {
            // Clear the OTP once verified
            $user->otp = null;
            $user->save();

            return response()->json([
                'status' => true,
                'msg' => 'OTP verified successfully.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Invalid OTP.',
            ], 422);
        }
    }

    // Step 3: Reset Password
    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|string',
            'user_id' => 'required|integer',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            // 'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }

        $password = Hash::make($input['password']);
        User::where('user_id', $input['user_id'])->update(['password' => $password ]);
        $response = ['status' => true, 'msg' => 'Password updated successfully. Please login with your new password.'];
        return response($response, 200);
    }


    public function sendOtpForForgotPassword(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|string',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        if (strpos($input['email'], '@') !== false)
        {
            $user = User::where('email', $input['email'])->where('status','=', 1)->first();

        }
        else
        {
            $user = User::where('mobile', $input['email'])->where('status','=', 1)->first();
        }
        if($user){
            $otp = 222222;//$input['otp'];
            $user_message = "One Time Password ".$otp." to verify your Mobile No.";
            $phone = $user->country_code.$user->mobile;
            $msg = $user_message;
            $temp_id = '1707161761166396747';
            $entity_id = '1701159793007694875';
            // $otpmsg = $this->otpmsg_sd($phone,$msg,$temp_id,$entity_id);
            $response = ['status' => true, 'otp' => $otp ,'msg' => 'Forgot password verification otp send to your mobile. Please verify to continue.','user_id'=>$user->user_id];
        } else {
            $response = ['status' => false, 'msg' => 'Sorry number not registered with us. Please correct the number.'];
        }
        return response($response, 200);
    }

    public function resetUserPassword(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|string',
            'user_id' => 'required|integer',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            // 'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }

        $password = Hash::make($input['password']);
        User::where('user_id', $input['user_id'])->update(['password' => $password ]);
        $response = ['status' => true, 'msg' => 'Password updated successfully. Please login with your new password.'];
        return response($response, 200);
    }

}
