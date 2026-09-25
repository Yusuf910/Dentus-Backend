<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Invitation;
use App\Models\UserInformation;
use App\Models\UserEstablishmentClinic;
use App\Models\UserEstablishmentGallery;
use App\Models\UserEstablishmentHoliday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DB;
use HasApiTokens;

class DoctorController extends Controller
{


    public function inviteDoctor(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users|unique:Invitation',
                    'mobile' => 'required|string|max:15|unique:users|unique:Invitation',
                ]);

        if ($validator->fails()) {
                    $errors = implode(' ', $validator->errors()->all());
                    return response()->json([
                        'status' => false,
                        'msg' => $errors,
                    ], 422);
                }

        // Check if the subscription allows for more doctors
        $doctor = auth()->user();
        $subscription = UserSubscription::where('user_id', $doctor->id)->first();
        $subscription_name = DB::table('subscriptions')->where('id', $subscription->subscription_id)->first();

        $invitedDoctorsCount = User::where('id', $doctor->id)->count();
        if ($subscription_name->name == 'Free Trail' && $invitedDoctorsCount >= 2) {
            return response()->json(['message' => 'Subscription limit reached for adding more doctors'], 400);
        }
        $token = Str::random(32);
           // Create the invitation
        $invitation = Invitation::create([
            'doctor_id' => auth()->user()->id, // main doctor ID
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'token' => $token,
        ]);

        // Prepare invitation data for email
        $invitationData = $invitation->toArray();
        $invitationData['invitation_id'] = $invitation->id; // Add the invitation ID

        // Send the invitation via email
        // Mail::send('mail', $invitationData, function ($message) use ($request) {
        //     $message->from('cotalajbh2@gmail.com', 'Dentus');
        //     $message->replyTo('cotalajbh2@gmail.com', 'Dentus');
        //     $message->to($request->email);
        //     $message->subject('Dentus | Accept Invitation');
        // });

        return response()->json(['status' => true,'message' => 'Invitation sent successfully.']);
    }


    public function resendInvitation()
    {

        $user_profile = User::where('id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays','activeSubscription.premiumAddons'])->first();
        $getchild = array();
        if ($user_profile->parent_id == 0) {
            $getchild = $this->getchild($user_profile->id);
        }
        $user_information = $user_profile->UserInformationDetails;
        if ($user_information == null) {
            $a = new UserInformation();
            $a->user_id = auth()->user()->id;
            $a->save();
        }
        $user_information = $user_profile->UserInformationDetails;
        $user_clinic = $user_profile->UserClinicDetails;
        $step1Fields = ['name', 'last_name', 'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

        $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
        $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
        $step4Fields = ['about'];
        $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
        $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
        $step7Fields = ['doctor_availability'];
        $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
        $step9Fields = ['theme'];
        $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

        $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
        $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
        $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
        $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
        $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
        $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
        $step8Completion = (count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
        $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
        $addonQuantity = 0;
        if ($user_profile->activeSubscription) {
            if ($user_profile->activeSubscription->premiumAddons->isNotEmpty()) {
                foreach ($user_profile->activeSubscription->premiumAddons as $addon) {
                    $addonQuantity = $addon->quantity;
                }
            }
        }
        $canaddchild = 0;
        if ($addonQuantity > 0) {
           $canaddchild = $addonQuantity-count($getchild);
        }
        $completionByStep = [
            'step1' => $step1Completion,
            'step2' => $step2Completion,
            'step3' => $step3Completion,
            'step4' => $step4Completion,
            'step5' => $step5Completion,
            'step6' => $step6Completion,
            'step7' => $step7Completion,
            'step8' => $step8Completion,
            'step9' => $step9Completion,

        ];
        $totalCompletion = array_sum($completionByStep);
        $totalSteps = count($completionByStep);
        $finalCompletionPercentage = $totalCompletion / $totalSteps;

        // Check if the final completion percentage is 100%
        if (round($finalCompletionPercentage, 2) < 100.00) {
            // dd('nbd');
            // Mail::to($user_profile->email)->send(new \App\Mail\InvitationEmail($user_profile));
            $emailData = [
                'name' => $user_profile->name,
                'last_name' => $user_profile->last_name,
                'email' => $user_profile->email,
                // Add other necessary fields from $user_profile as needed
            ];
        Mail::send('mail_new', $emailData, function ($message) use ($user_profile) {
            $message->from('cotalajbh2@gmail.com', 'Dentus');
            $message->replyTo('cotalajbh2@gmail.com', 'Dentus');
            $message->to($user_profile->email);
            $message->subject('Dentus | Profile Status');
        });
            return response()->json(['message' => 'Mail has been se sent'], 201);
        }else{
        // $checkchild = UserPremiumAddon::where('user_id',auth()->user()->id)->where('premium_feature_id',1)->first();
        $response = [
            'status' => true,
            'user_profile' => $user_profile,
            'completion_percentage' => $completionByStep,
            'final_completion_percentage' =>round($finalCompletionPercentage, 2),
            'path' => asset('content/doctor'),
            'getchild'=>$getchild,
            'canaddchild'=>$canaddchild

        ];
        return response($response, 200);
    }

    }

    public function listInvitedDoctors()
    {
        $user_profile = User::where('id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays','activeSubscription.premiumAddons'])->first();
        $getchild = array();
        if ($user_profile->parent_id == 0) {
            $getchild = $this->getchild($user_profile->id);
        }
        $user_information = $user_profile->UserInformationDetails;
        if ($user_information == null) {
            $a = new UserInformation();
            $a->user_id = auth()->user()->id;
            $a->save();
        }
        $user_information = $user_profile->UserInformationDetails;
        $user_clinic = $user_profile->UserClinicDetails;
        $step1Fields = ['name', 'last_name', 'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

        $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
        $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
        $step4Fields = ['about'];
        $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
        $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
        $step7Fields = ['doctor_availability'];
        $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
        $step9Fields = ['theme'];
        $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

        $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
        $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
        $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
        $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
        $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
        $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
        $step8Completion = (count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
        $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
        $addonQuantity = 0;
        if ($user_profile->activeSubscription) {
            if ($user_profile->activeSubscription->premiumAddons->isNotEmpty()) {
                foreach ($user_profile->activeSubscription->premiumAddons as $addon) {
                    $addonQuantity = $addon->quantity;
                }
            }
        }
        $canaddchild = 0;
        if ($addonQuantity > 0) {
           $canaddchild = $addonQuantity-count($getchild);
        }
        $completionByStep = [
            'step1' => $step1Completion,
            'step2' => $step2Completion,
            'step3' => $step3Completion,
            'step4' => $step4Completion,
            'step5' => $step5Completion,
            'step6' => $step6Completion,
            'step7' => $step7Completion,
            'step8' => $step8Completion,
            'step9' => $step9Completion,

        ];
        $totalCompletion = array_sum($completionByStep);
        $totalSteps = count($completionByStep);
        $finalCompletionPercentage = $totalCompletion / $totalSteps;

        // $checkchild = UserPremiumAddon::where('user_id',auth()->user()->id)->where('premium_feature_id',1)->first();
        $response = [
            'status' => true,
            'user_profile' => $user_profile,
            'completion_percentage' => $completionByStep,
            'final_completion_percentage' =>round($finalCompletionPercentage, 2),
            'path' => asset('content/doctor'),
            'getchild'=>$getchild,
            'canaddchild'=>$canaddchild
        ];

        return response($response, 200);
    }


    public function getchild($id)
    {
        $child = array();
        $main = User::where('parent_id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->whereNOTIN('status',[2])->get();
        if ($main->count() > 0) {
            foreach ($main as $key) {
                $user_profile = $key;
                $user_information = $user_profile->UserInformationDetails;
                if ($user_information == null) {
                    $a = new UserInformation();
                    $a->user_id = auth()->user()->id;
                    $a->save();
                }
                $user_information = $user_profile->UserInformationDetails;
                $user_clinic = $user_profile->UserClinicDetails;
                $step1Fields = ['name', 'last_name', 'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

                $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
                $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
                $step4Fields = ['about'];
                $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
                $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
                $step7Fields = ['doctor_availability'];
                $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
                $step9Fields = ['theme'];
                $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

                $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
                $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
                $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
                $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
                $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
                $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
                $step8Completion = (count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
                $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
                $completionByStep = [
                    'step1' => $step1Completion,
                    'step2' => $step2Completion,
                    'step3' => $step3Completion,
                    'step4' => $step4Completion,
                    'step5' => $step5Completion,
                    'step6' => $step6Completion,
                    'step7' => $step7Completion,
                    'step8' => $step8Completion,
                    'step9' => $step9Completion,
                ];
                $totalCompletion = array_sum($completionByStep);
                $totalSteps = count($completionByStep);
                $finalCompletionPercentage = $totalCompletion / $totalSteps;

                $child[] = array('user_profile' => $user_profile,
                    'completion_percentage' => $completionByStep,
                    'final_completion_percentage' =>round($finalCompletionPercentage, 2),);
            }
        }
        return $child;
    }




    // public function inviteDoctor(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users|unique:Invitation',
    //         'mobile' => 'required|string|max:15|unique:users|unique:Invitation',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = implode(' ', $validator->errors()->all());
    //         return response()->json([
    //             'status' => false,
    //             'msg' => $errors,
    //         ], 422);
    //     }

    //     // Generate a token for invitation
    //     $token = Str::random(32);

    //     // Create the invitation
    //     $invitation = Invitation::create([
    //         'doctor_id' => auth()->user()->id, // main doctor ID
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //         'token' => $token,
    //     ]);

    //     // Prepare invitation data for email
    //     $invitationData = $invitation->toArray();
    //     $invitationData['invitation_id'] = $invitation->id; // Add the invitation ID

    //     // Send the invitation via email
    //     Mail::send('mail', $invitationData, function ($message) use ($request) {
    //         $message->from('cotalajbh2@gmail.com', 'Dentus');
    //         $message->replyTo('cotalajbh2@gmail.com', 'Dentus');
    //         $message->to($request->email);
    //         $message->subject('Dentus | Accept Invitation');
    //     });

    //     return response()->json(['status' => true,'message' => 'Invitation sent successfully.']);
    // }


    public function getDoctorByToken(Request $request)
    {
        // dd(auth()->user()->id);
        $doctor = User::where('id', auth()->user()->id)->where('parent_id',0)->first();

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        $subscription = UserSubscription::where('user_id', $doctor->id)->first();
        $subscription_name = DB::table('subscriptions')->where('id', $subscription->subscription_id)->first();

        return response()->json([
            'is_main_doctor' => $doctor,
            'subscription_status' => $subscription_name->name ?? 'none',
            'profile_status' => $doctor->status,
        ]);
    }


    public function assistantStatus()
    {
        $doctor = auth()->user();

        $assistantsCount = Invitation::where('doctor_id', $doctor->id)->count();

        return response()->json(['assistants_added' => $assistantsCount > 0]);
    }


    public function checkSubscription()
    {
        $doctor = auth()->user();
        $subscription = UserSubscription::where('user_id', $doctor->id)->first();

        if (!$subscription || $subscription->end_date < now()) {
            // Disable features
            return response()->json(['message' => 'Subscription expired'], 403);
        }

        return response()->json(['message' => 'Subscription active']);
    }


    public function getDoctorReviews($id)
    {
        $doctor = DB::table('reviews')->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        return response()->json($doctor);
    }



    public function signupDoctor(Request $request)
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

        $inv = DB::table('invitation')->where('email',$request->email)->where('mobile',$request->mobile)->where('status',1)->first();
        if($inv){
            $flag=1;
        }else{
            $flag=0;
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

        $response = ['status' => true, 'msg' => 'OTP Send successfully.','flag' => $flag];
        return response($response, 200);
    }

    public function newSignupdr(Request $request)
    {
        $input = $request->all();
        return $this->register($input, 1, 'app');
    }

    public function register($data, $user_type, $login_type, $created_by = null)
    {
        $validator = Validator::make($data, [
            // 'country_code' => 'required|string',
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
            'password' => 'required|min:1|max:100',
            'device_id' => 'required',
            'device_token' => 'required',
            'device_type' => 'required',
            'name' => 'required'
        ]);

        $data['ip_address'] = request()->ip();
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        $inv = DB::table('Invitation')->where('email',$data['email'])->where('mobile',$data['mobile'])->where('status',1)->first();
        // dd($inv->doctor_id);die();
        $data['email'] = trim($data['email']);
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 3;
        $data['parent_id'] = $inv->doctor_id;
        $user = User::create($data);
        $token = $user->createToken('MyApp')->accessToken;
        $response = ['status' => true, 'token' => $token, 'user_detail' => $user, 'signup_skip' => false];
        return response($response, 200);
    }


    public function acceptInvitation($id)
    {
        $invitation = Invitation::find($id);

        if (!$invitation) {
            return response()->json(['message' => 'Invitation not found.'], 404);
        }

        // Check if the invitation has expired (15 minutes)
        $expiresAt = $invitation->created_at->addMinutes(15);
        if (now()->isAfter($expiresAt)) {
            return response()->json(['message' => 'This invitation link has expired.'], 410); // 410 Gone
        }

        // Update the status to 1
        $invitation->status = 1; // Assuming 'status' is a field in your Invitations table
        $invitation->save();
        return view('invitation');
        // return response()->json(['message' => 'Invitation accepted successfully.']);
    }



}
