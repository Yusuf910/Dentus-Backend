<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Invitation;
use App\Models\UserInformation;
use App\Models\UserEstablishmentClinic;
use App\Models\UserEstablishmentGallery;
use App\Models\UserEstablishmentHoliday;
use App\Models\MasterLangauage;
use App\Models\MasterSpecialsation;
use App\Models\MasterServices;
use App\Models\Subscription;
use App\Models\MasterDegree;
use App\Models\Generalsetting;
use App\Models\UserModels\UserProfile;
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
use App\Models\UserModels\Booking;
use Carbon\Carbon;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;
use App\Models\UserModels\UserPayment;
use App\Traits\SdSendSms;
use App\Models\UserPremiumAddon;
class DoctorController extends Controller
{

    use SdSendSms;

    public function inviteDoctor(Request $request)
    {
        $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users|unique:invitations',
                    'mobile' => 'required|string|max:15|unique:users|unique:invitations',
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
        $getchild = array();
        if ($doctor->parent_id == 0) {
            $getchild = $this->getchild($doctor->id);
        }
        $invitation = $this->getchildinvitation($doctor->id);
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', $doctor->id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', $doctor->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

            foreach ($subscription->features as $feature) {
                if ($feature->id == 12) {
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

                    break; // no need to continue loop
                }
            }
        }
        // $addonQuantity = 0;
        // if ($doctor->activeSubscription) {
        //     if ($doctor->activeSubscription->premiumAddons->isNotEmpty()) {
        //         foreach ($doctor->activeSubscription->premiumAddons as $addon) {
        //             $addonQuantity += $addon->quantity;
        //         }
        //     }
        // }
        $canaddchild = 0;
        if ($feature12Quantity  > 0) {
           $canaddchild = $feature12Quantity -(count($getchild)/*+$invitation*/);
        }
        if ($canaddchild > 0) {
            $token = Str::random(32);
            $invitation = Invitation::create([
                'doctor_id' => auth()->user()->id,
                'user_id' => 0,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'token' => $token,
            ]);

            $invitationData = $invitation->toArray();
            $invitationData['invitation_id'] = $invitation->id;
            $s = Generalsetting::find(1);
            $doctor = User::where('id',$invitation->doctor_id)->first();
            Mail::send('emails.mail_inv', compact('invitationData','s','doctor'), function ($message) use ($invitationData) {
                $message->from('patriciadentist1@gmail.com', 'Dentus');
                $message->replyTo('patriciadentist1@gmail.com', 'Dentus');
                $message->to($invitationData['email']);
                $message->subject('Dentus | Accept Invitation');
            });

            return response()->json(['status' => true,'msg' => 'Invitation sent successfully.']);
        } else return response()->json(['status' => false,'msg' => 'You cannot add other users!']);
        
    }


    public function resendInvitation(Request $request)
    {
        $team = $request->doctor_tm;
        $user_profile = Invitation::where('id', $team)->first();
        $response = ['status' => true,'message' => 'Mail has been se sent'];
        if ($user_profile) {
                $invitationData = $user_profile->toArray();
                $s = Generalsetting::find(1);
                $doctor = User::where('id',$user_profile->doctor_id)->first();
                Mail::send('emails.mail_inv', compact('invitationData','s','doctor'), function ($message) use ($invitationData) {
                    $message->from('patriciadentist1@gmail.com', 'Dentus');
                    $message->replyTo('patriciadentist1@gmail.com', 'Dentus');
                    $message->to($invitationData['email']);
                    $message->subject('Dentus | Accept Invitation');
                });
        }
        return response($response, 200);
        

    }

    public function listInvitedDoctors(Request $request)
    {
        $user_profile = User::where('id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays','activeSubscription.premiumAddons'])->first();
        $getchild = array();
        if ($user_profile->parent_id == 0) {
            $getchild = $this->getchild($user_profile->id);
        }
        $check = UserInformation::where('user_id',auth()->user()->id)->first();
        if (!$check) {
            $a = new UserInformation();
            $a->user_id = auth()->user()->id;
            $a->save();
            $user_information = $a;
        } else $user_information = $check;
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
        $totalquantity = $this->getquantity(12);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($getchild);
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
            'final_completion_percentage' =>round($finalCompletionPercentage, 2),
            'path' => asset('content/doctor'),
            'qrcodepath' => asset('content/doctor/qrcode'),
            'getchild'=>$getchild,
            'canaddchild'=>$canaddchild
        ];

        return response($response, 200);
    }



    // public function getchild($id)
    // {
    //     $child = array();
    //     $main = Invitation::where('doctor_id', auth()->user()->id)->whereNOTIN('status',[2])->get();
    //     // $main = User::where('parent_id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->whereNOTIN('status',[2])->get();
    //     if ($main->count() > 0) {
    //         foreach ($main as $key) {
    //             $user_profile = $key;
    //             $user_information = $user_profile->UserInformationDetails;
    //             // if ($user_information == null) {
    //             //     $a = new UserInformation();
    //             //     $a->user_id = auth()->user()->id;
    //             //     $a->save();
    //             // }
    //             $user_information = $user_profile->UserInformationDetails;
    //             $user_clinic = $user_profile->UserClinicDetails;
    //             $step1Fields = ['name', 'last_name', 'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

    //             $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
    //             $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
    //             $step4Fields = ['about'];
    //             $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
    //             $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
    //             $step7Fields = ['doctor_availability'];
    //             $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
    //             $step9Fields = ['theme'];
    //             $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

    //             $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
    //             $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
    //             $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
    //             $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
    //             $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
    //             $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
    //             $step8Completion = (count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
    //             $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
    //             $completionByStep = [
    //                 'step1' => $step1Completion,
    //                 'step2' => $step2Completion,
    //                 'step3' => $step3Completion,
    //                 'step4' => $step4Completion,
    //                 'step5' => $step5Completion,
    //                 'step6' => $step6Completion,
    //                 'step7' => $step7Completion,
    //                 'step8' => $step8Completion,
    //                 'step9' => $step9Completion,
    //             ];
    //             $totalCompletion = array_sum($completionByStep);
    //             $totalSteps = count($completionByStep);
    //             $finalCompletionPercentage = $totalCompletion / $totalSteps;
        
    //             $child[] = array('user_profile' => $user_profile,
    //                 'completion_percentage' => $completionByStep,
    //                 'final_completion_percentage' =>round($finalCompletionPercentage, 2),);
    //         }
    //     }
    //     return $child;
    // }
    public function getchild($id)
    {
        $child = array();
        $main = User::where('parent_id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->whereNOTIN('status',[2])->get();
        if ($main->count() > 0) {
            foreach ($main as $key) {
                $user_profile = $key;
                $check = UserInformation::where('user_id',auth()->user()->id)->first();
                if (!$check) {
                    $a = new UserInformation();
                    $a->user_id = auth()->user()->id;
                    $a->save();
                    $user_information = $a;
                } else $user_information = $check;
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
                                 'is_invite'=>0,
                                 'final_completion_percentage' =>round($finalCompletionPercentage, 2),);
            }
        }
        $invitationuser = Invitation::where('doctor_id',auth()->user()->id)->where('user_id',0)->where('status',0)->get();
        if ($invitationuser->count() > 0) {
            foreach ($invitationuser as $key) {
                $child[] = array('user_profile' => $key,
                                'is_invite'=>1,
                                 'final_completion_percentage' =>0);
            }
        }
        return $child;
    }

    public function getchildinvitation($id)
    {
        $child = array();
        $main = Invitation::where('doctor_id', $id)->whereNOTIN('status',[2])->get();
        return $main->count();
    }



    public function doctorsinglelist(Request $request)
    {
        $user_profile = User::where('id', $request->doctor_id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays','activeSubscription.premiumAddons'])->first();
        $check = UserInformation::where('user_id',$user_profile->id)->first();
        if (!$check) {
            $a = new UserInformation();
            $a->user_id = auth()->user()->id;
            $a->save();
            $user_information = $a;
        } else $user_information = $check;
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
        $languages = $user_profile->UserInformationDetails?->getLanguageNames() ?? [];
        $specialisations = $user_profile->UserInformationDetails?->getSpecialisationNames() ?? [];
        $services = $user_profile->UserInformationDetails?->getServiceNames() ?? [];
        $response = [
            'status' => true,
            'user_profile' => $user_profile,
            'completion_percentage' => $completionByStep,
            'final_completion_percentage' =>round($finalCompletionPercentage, 2),
            'path' => asset('content/doctor'),
            'qrcodepath'=>asset('content/doctor/qrcode/'),
            'languages_known' => $languages,
            'specialisations' => $specialisations,
            'services' => $services,
        ];

        return response($response, 200);
    }

// public function doctorsinglelist(Request $request)
// {
//     $team = $request->doctor_id;
//     $user_profile = User::where('id', $team)->with(['UserInformationDetails'])->first();
    
//     if (!$user_profile) {
//         return response()->json(['status' => false, 'message' => 'User not found'], 404);
//     }

//     $user_information = $user_profile->UserInformationDetails;

//     // Check if UserInformation exists; if not, create a new one
//     if ($user_information == null) {
//         $user_information = new UserInformation();
//         $user_information->user_id = $user_profile->id; // Use the correct user ID
//         $user_information->save();
//     }

//     // Fetch language, specialisation, and service names
//     $user_information->language_names = $this->getNamesFromIds($user_information->language_known, MasterLangauage::class);
//     $user_information->specialisation_names = $this->getNamesFromIds($user_information->specialisations, MasterSpecialsation::class);
//     $user_information->service_names = $this->getNamesFromIds($user_information->services, MasterServices::class);

//     // $user_information->degree = $user_information->degree->toArray();
//     // Create a new array for additional information
    

//     $response = [
//         'status' => true,
//         'user_profile' => $user_profile,
//         'user_information' => $user_information,
//         'path' => asset('content/doctor'),
//     ];

//     return response($response, 200);
// }

// private function getNamesFromIds($idsString, $model)
// {
//     $ids = explode('|', $idsString);
//     $names = $model::whereIn('id', $ids)->pluck('name', 'id')->toArray();

//     return array_map(function ($id) use ($names) {
//         return $names[$id] ?? null; // Return null if the name is not found
//     }, $ids);
// }



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
        $subscription12 = UserSubscription::where('user_id', $doctor->id)->where('status', 1)->orderBy('id', 'desc')->first();
        // $subs_pack = DB::table('subscriptions')->where('id',$subscription12->subscription_id)->first();

        // $subscription = Subscription::with('features')->find($subscription12->subscription_id);

        // if (!$subscription) {
        //     return response()->json(['status' => false, 'message' => 'Subscription not found'], 404);
        // }
        $finalFeatures = [];
        $specialFeatureIds = [12, 13, 14, 15, 16, 17, 18];
        if ($subscription12) {
            $subscription = Subscription::with(['features' => function ($q) {
                $q->orderBy('position', 'ASC');
            }])->find($subscription12->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $subscription12->id)
                ->get()
                ->keyBy('premium_feature_id');
            foreach ($subscription->features as $feature) {
                $pivotId = $feature->pivot->id;

                $addon = $premiumAddons->get($pivotId);
                $isSpecial = in_array($feature->id, $specialFeatureIds);

                $quantity = 0;
                $price = 0;
                $unlimited = 0;
                if ($subscription12->subscription_id == 9) {
                    if ($addon) {
                        $quantity = $addon->quantity;
                        $price = $addon->price;
                        $unlimited = $addon->unlimited;

                        if (in_array($feature->id, [15, 16, 17])) {
                            $unlimited = 1;
                        }
                    } elseif ($isSpecial) {
                        $quantity = $feature->pivot->basic_quantity ?? 0;
                        $price = 0;
                        $unlimited = 0;
                    } else {
                        $quantity = $feature->pivot->quantity ?? 0;
                        $price = $feature->pivot->price ?? 0;
                        $unlimited = $feature->pivot->unlimited ?? 0;
                    }
                } else {
                    $quantity = $feature->pivot->quantity ?? 0;
                    $price = $feature->pivot->price ?? 0;
                    $unlimited = $feature->pivot->unlimited ?? 0;
                }
                

                $finalFeatures[] = [
                    'id' => $feature->id,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'position' => $feature->position,
                    'is_premium' => $addon ? 1 : 0,
                    'purchased' => $addon ? 1 : 0,
                    'quantity' => $quantity,
                    'price' => $price,
                    'unlimited' => $unlimited,
                    'addon' => $addon ?? null
                ];
            }
        }

        return response()->json([
            'status' => true,
            'subscription_date' => $subscription12,
            'subscription' => $subscription,
            'features' => $subscription->features,
            'finalFeatures'=>$finalFeatures
        ]);

        // if (!$subscription || $subscription->end_date < now()) {
        //     // Disable features
        //     return response()->json(['message' => 'Subscription expired'], 403);
        // }

        // return response()->json(['message' => 'Subscription active',]);
        // return response()->json(['status' => true,'subscription'=>$subscription,'subscription_pack'=>$subs_pack]);
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
            // 'device_id' => 'required',
            // 'device_token' => 'required',
            // 'device_type' => 'required',
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

    public function home(Request $request)
    {
        $response = ['status' => false,'msg'=>'Not found'];  
        $user_id =  auth()->user()->id;
        $qt = Booking::where('doctor_id',$user_id)->where('status',6)->orderBy('id','DESC')->first();
        if(!empty($qt)) {
            $remaintime = 0;
            switch ($qt->status) {
                case 1:
                    $qt->booking_status = "Completed";
                    break;
                case 2:
                    $qt->booking_status = "Cancel";
                    break;
                case 3:
                    $qt->booking_status = "Reject";
                    break;
                case 4:
                    $qt->booking_status = "Accept";
                    break;
                case 6:
                    $qt->booking_status = "Ongoing";

                    // Calculate remaining time
                        $booking_date_time = strtotime($qt->schedule_date . ' ' . $qt->start_time);
                        $date = new \DateTime( date('Y-m-d H:i:s'));
                        $endTime = date('Y-m-d H:i:s',strtotime($qt->schedule_date . ' ' . $qt->end_time));
                        $date2 = new \DateTime( $endTime );
                        $diffInSeconds = $date2->getTimestamp() - $date->getTimestamp();
                        if ($diffInSeconds > 0) {
                            $remaintime = $diffInSeconds;
                        }
                    break;
                default:
                    $qt->booking_status = "Unknown"; // Handle any unexpected statuses
                    break;
            } 
            $qt->remaintime = $remaintime;
            $qt->clinic = UserEstablishmentClinic::find($qt->clinic_id);
            $qt->user = UserProfile::find($qt->user_id);
            $response = ['status' => true, 'msg' => 'Bookind Details', 'data' => $qt];   
            return response($response, 200);
        }
        return response($response, 200);
    }

    public function bookingStart(Request $request)
    {
        $response = ['status' => false,'msg'=>'Not found'];  
        $booking_id = $request->booking_id;
        $settings = Generalsetting::find(1);
        $b = Booking::where("id",$booking_id)->whereIN('status',[4])->where('is_chat_or_video_start',0)->first();
        if ($b) {
            $response = ['status' => true];
            $checkanyotherstart = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->first();
            $checkanyotherstart2 = Booking::where("user_id",$b->user_id)->whereIN('status',[0])->whereIN('is_chat_or_video_start',[1])->first();
            $checkanyotherstart4 = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->whereIN('is_chat_or_video_start',[1,2])->first();
            $current_time = Carbon::now()->toDateTimeString();
            $checkanybookingschedule = Booking::where('user_id',$b->user_id)->whereIN('status',[0,6])->where('id','<>',$b->id)->where('consultation_type','Online Consultation')->whereRaw('? BETWEEN start_time AND end_time', [$current_time])->first();
            if ($checkanyotherstart || $checkanyotherstart2 || $checkanyotherstart4 ) {
                $response = ['status' => false,'msg'=>"User busy on another consultation"];  
            }
            elseif ($checkanybookingschedule) {
                $response = ['status' => false,'msg'=>"User busy on another consultation"];  
            }
            else {
                $ongoingbooking = 0;
                $goaheadwallethave = 0;  
                $checkanybookingongoing =  Booking::where("user_id",$b->user_id)->whereIN('status',[6])->first();
                if ($checkanybookingongoing) {
                    $ongoingbooking = 1;
                    $response = ['status' => false,'msg'=>"User busy on another consultation"];  
                }
                $checkanybookingschedulecanstart = Booking::where('user_id', $b->user_id)
                    ->where('id', $b->id)
                    ->where('consultation_type', 'Online Consultation')
                    ->whereRaw('? BETWEEN CONCAT(schedule_date, " ", start_time) AND CONCAT(schedule_date, " ", end_time)', [$current_time])
                    ->toSql();

                if ($checkanybookingschedulecanstart) {
                    if ($ongoingbooking == 0) {
                        $checkanyotherstart = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->first();
                        $checkanyotherstart2 = Booking::where("user_id",$b->user_id)->whereIN('status',[0])->whereIN('is_chat_or_video_start',[1])->first();
                        $checkanyotherstart4 = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->whereIN('is_chat_or_video_start',[1,2])->first();
                        
                        if ($checkanyotherstart || $checkanyotherstart2 || $checkanyotherstart4) {
                            $response = ['status' => false,'msg'=>"User busy another consultation"];  
                        }
                        else {
                            $b->status = 6;
                            $b->is_chat_or_video_start = 1;
                            $b->save();
                            if ($b->status == 6) {
                                $bdate = date("l, F j, Y g:i A", strtotime($b->schedule_date.' '.$b->schedule_time));
                                $msgarray = array("title"=>"📌Appointment Started",
                                  "msg"=>"📩 Your appointment with Dr. ".$b->doctordetail->name." at ".$b->clinicdetail->clinic_name." scheduled for ".$bdate.' has started. Please join the consultation..',

                                 "msg2"=>"You have a new appointment with ".$b->userdetail->name." at ".$b->clinicdetail->clinic_name." on ".$bdate." has started. Please proceed."
                                );
                                $this->async_to_all($msgarray,'',$b->id,'bookingstart');
                            
                            }
                        }
                    }
                }
                else {
                    $response = ['status' => false,'msg'=>"Your slot booking time is ".date('d/m/Y h:ia',strtotime($b->start_time)).' IST']; 
                }
                
            }
        }
        return response($response, 200);
    }

    public function bookingEnd(Request $request)
    {
        $response = ['status' => false];  
        $booking_id = $request->booking_id;
        $b = Booking::where("id",$booking_id)/*->whereIN('status',[0,1,7])*/->first();
        if ($b) {
            $response = ['status' => true];  
            if ($b->status == 6) {
                $b->status = 1;
                $b->is_paid = 1;
                $b->complete_date = date('Y-m-d H:i:s');
                $b->ended_by = 'doctor';
                $b->save();
                if($b->doctordetail->UserInformationDetails->loyalty_points == 1 || $b->doctordetail->UserInformationDetails->loyalty_points == '1') {
                    // $generalSetting = Generalsetting::select('loyality_program_points_credit')->where('id', 1)->first();
                    // if ($generalSetting->loyality_program_points_credit > 0) {
                    //     $user_data = UserProfile::where("id",$b->user_id)->first();
                    //     $user_wallet = $user_data->wallet;
                    //     $update_wallet =  $user_wallet+$generalSetting->loyality_program_points_credit;
                    //     $new = new UserPayment();
                    //     $new->user_id = auth()->user()->id;
                    //     $new->booking_id = $b->id;
                    //     $new->type = 2;
                    //     $new->action = 'credit';
                    //     $new->amount = $generalSetting->loyality_program_points_credit;
                    //     $new->old_balance = $user_wallet;
                    //     $new->payment_status = 'completed';
                    //     $new->payment_method = 'online';
                    //     $new->new_balance = $update_wallet;
                    //     $new->status = 1;
                    //     $new->trxn_id =  time().rand();
                    //     $new->save();
                    //     $user_data->wallet = $update_wallet;
                    //     $user_data->save();
                    // }
                    
                }
                $bdate = date("l, F j, Y g:i A", strtotime($b->schedule_date.' '.$b->schedule_time));
                $msgarray = array("title"=>"📌Appointment Completed",
                  "msg"=>"📩 Your appointment with Dr. ".$b->doctordetail->name." at ".$b->clinicdetail->clinic_name." scheduled for ".$bdate.' has been completed. We hope you had a great experience!',

                 "msg2"=>"You have a new appointment with ".$b->userdetail->name." at ".$b->clinicdetail->clinic_name." on ".$bdate." has been successfully completed."
                );
                $this->async_to_all($msgarray,'',$b->id,'bookingend');
            }
        }
        return response($response, 200);
    }

    public function getquantity($for)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
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
