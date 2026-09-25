<?php

namespace App\Http\Controllers\Api\Users;

use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\Member;
use App\Models\UserModels\MedicalRecord;
use App\Models\UserModels\ReferCodeHistory;
use App\Models\UserModels\UserPayment;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\UserEstablishmentClinic;
use App\Models\Generalsetting;
use Illuminate\Support\Facades\Crypt;
use App\Traits\SdSendSms;
use Carbon\Carbon;
use File;
// use Faker\Generator;
// use Illuminate\Container\Container;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
class UserController extends Controller
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
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        if($input['mobile'] == '9896904632') {
            $otp = '111111';
        }
        else{
         $otp = $input['otp']; //mt_rand(1000, 9999);
        }

        //  $otp = $input['otp']; //mt_rand(1000, 9999);
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

        $response = ['status' => true, 'otp' => $otp, 'msg' => 'OTP Send successfully.'];
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
    
        if (!$user) {
            // If the user is not found, return response for new user
            return response(['status' => true, 'newuser' => 1, 'msg' => 'New user']);
        }
    
        // If user exists, try to decrypt the clinic_id and fetch clinic details
        try {
            // Decrypt the clinic_id (ensure it exists in the user profile)
            if ($user->clinic_id > 0 && $request->clinic != 1) {
                if ((int)$user->clinic_id) {
                    $clinicId = $user->link_id;
                }
                else {
                    $decrypted = Crypt::decryptString($user->clinic_id);
                    $clinicId = unserialize($decrypted);
                }

                // Check if clinicId is valid
                if (empty($clinicId)) {
                    return response(['message' => 'Invalid clinic ID.', 'status' => false], 400);
                }
        
                // Fetch clinic details
                $clinic = UserEstablishmentClinic::where('user_id', $clinicId)->first();
        
                if (!$clinic) {
                    return response(['message' => 'Clinic not found for the user.', 'status' => false], 404);
                }
        
                // Fetch the doctor details associated with the clinic
                $doctor = User::where('id', $clinic->user_id)->first();
            } else {

                $doctor = User::where('id', $user->link_id)->first();

                if (!$doctor) {
                    return response(['message' => 'Doctor not found for the user.', 'status' => false], 404);
                } else {

                    $clinic = UserEstablishmentClinic::where('user_id',$doctor->id)->where('status',1)->first();
                    if (!$clinic) {
                        return response(['message' => 'Clinic not found for the user.', 'status' => false], 404);
                    } else {
                        $user->clinic_id = $clinic->id;
                    }
                }
            }
            
    
            // Generate the clinic logo URL
            $clinic->logo = asset('project/public/clinics/') . '/' . $clinic->logo;
    
            // Create an access token for the UserProfile model
            $token = $user->createToken('MyApp')->accessToken; // Use UserProfile model here
            $user->device_id = $request->device_id ?? $user->device_id;
            $user->device_token = $request->device_token ?? $user->device_token;
            $user->device_type = $request->device_type ?? $user->device_type;
            $user->save();
            return response([
                'message' => 'User already exists',
                'token' => $token,
                'user_detail' => $user,
                'clinic_detail' => $clinic,
                'doctor' => $doctor,
                'newuser' => 0,
                'status' => true
            ]);
            
        } catch (\Exception $e) {
            // If there is any error during decryption or fetching clinic info, handle it
            return response(['message' => 'An error occurred: ' . $e->getMessage(), 'status' => false], 500);
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
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('mysql2')
                        ->table('users')
                        ->where('email', $value)
                        ->whereNotIn('status', [2])
                        ->exists();

                    if ($exists) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'mobile' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('mysql2')
                        ->table('users')
                        ->where('mobile', $value)
                        ->whereNotIn('status', [2])
                        ->exists();

                    if ($exists) {
                        $fail('The mobile has already been taken.');
                    }
                },
            ],
            // 'device_id' => 'required',
            // 'device_token' => 'required',
            // 'device_type' => 'required',
            'name' => 'required|string',
            'last_name' => 'required|string',
            // 'referral_code' => 'required|string',
            'clinic_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->first(), 'status' => false], 422);
        }
        $decrypted = Crypt::decryptString($data['clinic_id']);
        $clinicId = unserialize($decrypted);
        $points = 0;
        if ($data['referral_code']) {
            $checkref = UserProfile::where('status',1)->where('referral_code',$data['referral_code'])->first();
            if ($checkref) {
               $getrefer = User::where('id',$clinicId)->where('status',1)->first();
               if ($getrefer) {
                    if ($getrefer->UserInformationDetails->referrer_a_friend_points > 0) {
                        $points = $getrefer->UserInformationDetails->referrer_a_friend_points;
                   } else return response(['status' => false, 'msg'=>"refer code invalid!"], 200);
               } else return response(['status' => false, 'msg'=>"Referral System is not being fully set up yet.!"], 200);
            } else {
                return response(['status' => false, 'msg'=>"refer code invalid!"], 200);
            }
        }
        
        $data['ip_address'] = request()->ip();
        $data['name'] = trim($data['name']);
        $data['last_name'] = trim($data['last_name']);
        $data['email'] = trim($data['email']);
        $data['mobile'] = trim($data['mobile']);
        $data['referral_code'] = 'DENTUS' . strtoupper(substr(uniqid(), -8));
        $data['clinic_id'] = trim($data['clinic_id']);
        $data['status'] = 1;
        $data['link_id'] = $clinicId;
        

        $user = UserProfile::create($data);
        $clinic = UserEstablishmentClinic::where('id', $user->clinic_id)->first();
        if ($points > 0) {
            $a = new ReferCodeHistory();
            $a->refer_by_uid = $checkref->id;
            $a->refer_to_uid = $user->id;
            $a->code = $data['referral_code'];
            $a->total_points = $points;
            $a->status = 1;
            $a->save();
            $visibleDigits = substr($user->mobile, -4);
            $hiddenDigits = str_repeat("*", strlen($user->mobile) - 4);
            $visibleMobileNumber = $hiddenDigits . $visibleDigits;
            $body = "Referral points credited to your loyality points with phone number ".$visibleMobileNumber;
            $body = "Hi ".$checkref->name.",Great news! Someone used your referral code with phone number ".$visibleMobileNumber." and completed their signup. As a reward, you’ve earned '.$points.' loyalty points in your points!Keep sharing your referral code to earn even more rewards!";
            $body2 = "Hi ".$user->name.", Your referral code has been successfully applied! 🎉 You will receive your loyalty points after completing your first booking.Enjoy your booking experience and make the most of your rewards! Happy Booking! 🚀";
            $old_points = $checkref->wallet;
            $new_points = $old_points+$points;
            $new = new UserPayment();
            $new->user_id = $checkref->id;
            $new->type = 3;
            $new->booking_id = $a->id;
            $new->action = 'credit';
            $new->amount = $points;
            $new->old_balance = $old_points;
            $new->payment_status = 'completed';
            $new->payment_method = 'online';
            $new->new_balance = $new_points;
            $new->status = 1;
            $new->trxn_id =  time().rand();
            $new->save();
            if ($new) {
                $checkref->wallet = $new_points;
                $checkref->save();
                $a->refer_to_uid_wallet_add_flag = 1;
                $a->save();
                $msgarray = array("title"=>"🎉 You've Earned Loyalty Points from a Referral!",
                                  "msg"=>$body,

                                 "msg2"=>$body2
                           );
                $this->async_to_all($msgarray,$checkref->id,$user->id,'refercodebenefit');
            }

        }
        $token = $user->createToken('MyApp')->accessToken;
        //$created_refer_code = $this->createReferCode($user->id);
        return response(['status' => true, 'token' => $token, 'user_detail' => $user,'clini_detail' => $clinic, 'signup_skip' => false], 200);
    }

    public function createReferCode($user_id)
    {
        $refer_code = 'DENTUS' . strtoupper(substr(uniqid(), -8));
        UserProfile::whereId($user_id)->update(['referral_code' => $refer_code]);
        return $refer_code;
    }


    public function user_update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'date_of_birth' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);


         $a = UserProfile::find(auth()->user()->id);
         $image = $a->image;
         if (request('image'))
          {
 
             $fileNameWithTheExtension = request('image')->getClientOriginalName();
             $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
             $extension = request('image')->getClientOriginalExtension();
             $image = 'image_'.$fileName . '_' . time() . '.' . $extension;
             $filePath = request('image')->move('content/user/', $image);
          }
         $a->name = $request->name;
         $a->last_name = $request->last_name;
         $a->mobile = $request->mobile;
         $a->email = $request->email;
         $a->date_of_birth = $request->date_of_birth;
         $a->gender = $request->gender;
         $a->image = $image;
         $a->status = 1;
         $a->save();
        return response()->json(['status' => true,'message' => 'User updated successfully', 'user' => $a], 201);
    }
    

    public function user_list(Request $request) {
        // echo "ccddcdc"; die;
        $l = UserProfile::where('id',auth()->user()->id)->first();
        $l->image = asset('content/user/' . $l->image);
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'User List', 'data' => $l];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No user found.'];
            return response($response, 422);
        }
    }


    public function member_store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'dob' => 'required|date',
            'relation' => 'required|string',
            'gender' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // New validation for image
        ]);
        $members = Member::where('user_id', auth()->user()->id)
                         ->where('status', 1)
                         ->orderBy('id', 'ASC')
                         ->get();
        $totalquantity = $this->getquantity(18,auth()->user()->link_id);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($members);
        }
        if ($canaddchild > 0) {
            // code...
            $image = 'default.png';
            if (request('image'))
            {

                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image = 'image_'.$fileName . '_' . time() . '.' . $extension;
                $filePath = request('image')->move('project/public/member_images/', $image);
            }


            $a = new Member();
            $a->user_id = auth()->user()->id;
            $a->name = $request->name;
            $a->last_name = $request->last_name;
            $a->mobile = $request->mobile;
            $a->dob = $request->dob;
            $a->relation = $request->relation;
            $a->gender = $request->gender;
            $a->status = 1;
            $a->image = $image;
            $a->save();

            // $member = Member::create($validated);
            return response()->json(['status' => true,'message' => 'Member added successfully', 'member' => $a], 201);
        } else return response()->json(['status' => false,'msg' => 'You can not add further member limit exceed!'], 201);
        
    }

    public function member_update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'dob' => 'required',
            'relation' => 'required|string',
            'gender' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // New validation for image
        ]);


        $a = Member::find($request->member_id);
        $image = $a->image;
        if (request('image'))
         {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_'.$fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('project/public/member_images/', $image);
         }
         $a->user_id = auth()->user()->id;
         $a->name = $request->name;
         $a->last_name = $request->last_name;
         $a->mobile = $request->mobile;
         $a->dob = $request->dob;
         $a->relation = $request->relation;
         $a->gender = $request->gender;
         $a->status = 1;
         $a->image = $image;
         $a->save();
        return response()->json(['status' => true,'message' => 'Member updated successfully', 'member' => $a], 201);
    }


    public function destroy_member(Request $request)
    {
        // Find the treatment with the related doctors
        $member = Member::find($request->member_id);

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Delete the treatment and related records in treatment_doctors will be automatically removed
        $member->delete();

        return response()->json(['status' => true, 'message' => 'Member deleted successfully']);
    }


    public function member_list(Request $request) {
        // Validate the member_id in the request
        if (!$request->has('member_id')) {
            return response(['status' => false, 'msg' => 'Member ID is required.'], 400);
        }
    
        // Fetch the main user details using member_id from the request
        $main_user = UserProfile::where('id', $request->member_id)
                                ->where('status', 1)
                                ->first(); // Use first() to get the first matching record
    
        // If no main user is found, return an error
        if (!$main_user) {
            return response(['status' => false, 'msg' => 'Main user not found.'], 404);
        }
    
        // Fetch the list of members related to the given user_id
        $members = Member::where('user_id', $request->member_id)
                         ->where('status', 1)
                         ->orderBy('id', 'ASC')
                         ->get();
    
        // Modify the image URL for each member in the list
        $members->each(function ($member) {
            if ($member->image) {
                $member->image = asset('project/public/member_images/') . '/' . $member->image;
            } else {
                $member->image = asset('project/public/member_images/default.png'); // Default image if no image exists
            }
        });
        
        // Prepare a new "self" member entry for the main user
        $main_user_member = [
            'id' => $main_user->id,
            'user_id' => $main_user->user_id,
            'name' => $main_user->name,
            'last_name' => $main_user->last_name,
            'mobile' => $main_user->mobile,
            'dob' => $main_user->date_of_birth,
            'relation' => 'self', // Main user is always 'self'
            'gender' => $main_user->gender,
            'image' => asset('content/user/' . ($main_user->image ?: 'default.png')), // Updated image path for the main user
            'status' => $main_user->status,
            'created_at' => $main_user->created_at,
            'updated_at' => $main_user->updated_at
        ];
    
        // If there are members, prepend the main user as the first member
        if (!$members->isEmpty()) {
            $members->prepend((object) $main_user_member);
        } else {
            // If no members are found, only return the main user as the member
            $members = collect([(object) $main_user_member]);
        }
        $totalquantity = $this->getquantity(18,$main_user->link_id);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($members);
        }
        // Prepare the final response
        $response = [
            'status' => true,
            'msg' => 'Member List',
            'data' => [
                'members' => $members // Include members with main user at the beginning
            ],
            'canaddchild'=>$canaddchild
        ];
    
        return response($response, 200);
    }
    

    public function medical_records(Request $request)
    {
        // Validation for both image and pdf
        $validated = $request->validate([
            'member_type' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,pdf|max:2048', // Validation for both image and pdf
        ]);

        $fileName = 'default.png'; // Default file name if no file is uploaded

        // Check if a file has been uploaded
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); // Get file extension

            // Handling for image files (JPEG, PNG, JPG, GIF)
            if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif'])) {
                $fileNameWithTheExtension = $file->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $fileName = 'image_' . $fileName . '_' . time() . '.' . $extension;

                // Move the uploaded image file to the server
                $file->move('project/public/medical_records/', $fileName);
            }

            // Handling for PDF files
            elseif ($extension == 'pdf') {
                $fileNameWithTheExtension = $file->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $fileName = 'pdf_' . $fileName . '_' . time() . '.' . $extension;

                // Move the uploaded PDF file to the server
                $file->move('project/public/medical_records/', $fileName);
            }
        }

        // Create a new medical record entry
        $a = new MedicalRecord();
        $a->user_id = auth()->user()->id;
        $a->member_id = $request->member_id;
        $a->member_type = $request->member_type;
        $a->type = $request->type;
        $a->status = 1;
        $a->image = $fileName; // Store the file name (image or pdf)
        $a->save();

        // Return response with the saved medical record
        return response()->json(['status' => true, 'message' => 'Medical Record added successfully', 'medical' => $a], 201);
    }

    public function editmedical_records(Request $request)
    {
        $validated = $request->validate([
            'member_type' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);

        $fileName = 'default.png'; 

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif'])) {
                $fileNameWithTheExtension = $file->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $fileName = 'image_' . $fileName . '_' . time() . '.' . $extension;

                $file->move('project/public/medical_records/', $fileName);
            }

            elseif ($extension == 'pdf') {
                $fileNameWithTheExtension = $file->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $fileName = 'pdf_' . $fileName . '_' . time() . '.' . $extension;

                // Move the uploaded PDF file to the server
                $file->move('project/public/medical_records/', $fileName);
            }
        }

        // Create a new medical record entry
        $a = MedicalRecord::where('id',$request->id)->first();
        $a->member_id = $request->member_id;
        $a->member_type = $request->member_type;
        $a->type = $request->type;
        $a->status = 1;
        $a->image = $fileName;
        $a->save();
        return response()->json(['status' => true, 'message' => 'Medical Record added successfully', 'medical' => $a], 201);
    }

    public function deletemedical_records(Request $request)
    {
        $a = MedicalRecord::where('id',$request->id)->first();
        $a->status = 2;
        $a->save();
        return response()->json(['status' => true, 'message' => 'Medical Record deleted successfully', 'medical' => $a], 201);
    }

    

    public function medical_list(Request $request) {
        // Fetch the medical records for the authenticated user
        $l = MedicalRecord::where('user_id', auth()->user()->id)->where('status',1)
                          ->where('type', $request->type)->with('memberdetail')
                          ->get();
        
        // Initialize a user variable to store the user information
        $user = null;

        // Check if there are any medical records
        if ($l->isEmpty()) {
            $response = ['status' => false, 'msg' => 'Sorry! No medical records found.'];
            return response($response, 422);
        }

        // Iterate over each medical record and modify the image path
        $l->each(function ($medicalRecord) {
            if ($medicalRecord->image) {
                $medicalRecord->image = asset('project/public/medical_records/') . '/' . $medicalRecord->image;
            } else {
                // Handle case where no image exists (default image)
                $medicalRecord->image = asset('project/public/medical_records/default.jpg');
            }
            if ($medicalRecord->pdf) {
                $medicalRecord->pdf = asset('project/storage/app/public/prescriptions/') . '/' . $medicalRecord->pdf;
            } else {
                // // Handle case where no image exists (default image)
                // $medicalRecord->pdf = asset('storage/app/public/prescriptions/');
            }
        });

        // Based on member type, fetch the corresponding user information
        foreach ($l as $medicalRecord) {
            if ($medicalRecord->member_type == 'self') {
                $user = UserProfile::where('id', $medicalRecord->user_id)->first();
            } elseif ($medicalRecord->member_type == 'relation') {
                $user = Member::where('id', $medicalRecord->user_id)->first();
            }

            // Add user data to each medical record
            $medicalRecord->user = $user;  // Adding user information for each record
        }

        // Return the response with medical records and user data
        $response = [
            'status' => true,
            'msg' => 'Medical List',
            'data' => $l,
            'pdfurl'=>asset('/storage/app/public/prescriptions/').'/'
        ];

        return response($response, 200);
    }


    public function UserSettingData(Request $request)
    {

        $a = Generalsetting::select('about_us','privacy_policy','terms_and_condition','contact_us')->first();

        return response()->json(['status' => true, 'data' => $a], 201);
    }

    public function getquantity($for,$userid)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', $userid)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', $userid)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

            foreach ($subscription->features as $feature) {
                if ($feature->id == $for) {
                    $pivotId = $feature->pivot->id;
                    $addon = $premiumAddons->get($pivotId);

                    if ($alreadySubscribed->subscription_id == 9) {
                        if ($addon) {
                            $feature12Quantity = $addon->quantity;
                        } else {
                            $feature12Quantity = $feature->pivot->basic_quantity ?? 0;
                        }
                    } else {
                        $feature12Quantity = $feature->pivot->quantity ?? 0;
                    }

                    break;
                }
            }
        }
        return $feature12Quantity;
    }
    

}
