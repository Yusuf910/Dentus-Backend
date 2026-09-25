<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Validator;
use App\Models\UserInformation;
use App\Models\UserEstablishmentClinic;
use App\Models\UserEstablishmentGallery;
use App\Models\UserEstablishmentHoliday;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
use App\Models\DoctorClinicSlot;
use App\Models\MasterSpecialsation;
use App\Models\MasterLangauage;
use App\Models\MasterServices;
use DB;
use Hash;
use Carbon;
class DoctorDetailController extends Controller
{


public function getProfile_old()
{
    // print_r('test');
    // die();
    $user_profile = User::where('id', auth()->user()->id)
        ->with([
            'UserInformationDetails',
            'UserClinicDetails',
            'UserClinicDetails.galleries',
            'UserClinicDetails.holidays',
            'activeSubscription.premiumAddons'
        ])
        ->first();

    $getchild = [];
    if ($user_profile->parent_id == 0) {
        $getchild = $this->getchild($user_profile->id);
    }

    $check = UserInformation::where('user_id', auth()->user()->id)->first();
    if (!$check) {
        $a = new UserInformation();
        $a->user_id = auth()->user()->id;
        $a->save();
        $user_information = $a;
    } else {
        $user_information = $check;
    }

    $user_clinic = $user_profile->UserClinicDetails;

    $step1Fields = ['name', 'last_name', 'mobile', 'email', 'gender', 'date_of_birth', 'image'];
    $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
    $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
    $step4Fields = ['about'];
    $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
    $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
    $step7Fields = ['doctor_availability'];
    $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address', 'state', 'city', 'pincode', 'timings'];
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

    // Fetching names based on IDs for Step 2 fields
    $languageNames = [];
    $specialisationNames = [];
    $serviceNames = [];

    if (!empty($user_information->language_known)) {
        $languageIds = is_array($user_information->language_known)
            ? $user_information->language_known
            : json_decode($user_information->language_known, true);

        $languageNames = MasterLangauage::whereIn('id', $languageIds ?? [])->pluck('name')->toArray();
    }

    if (!empty($user_information->specialisations)) {
        $specialisationIds = is_array($user_information->specialisations)
            ? $user_information->specialisations
            : json_decode($user_information->specialisations, true);

        $specialisationNames = MasterSpecialsation::whereIn('id', $specialisationIds ?? [])->pluck('name')->toArray();
    }

    if (!empty($user_information->services)) {
        $serviceIds = is_array($user_information->services)
            ? $user_information->services
            : json_decode($user_information->services, true);

        $serviceNames = MasterServices::whereIn('id', $serviceIds ?? [])->pluck('name')->toArray();
    }

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
        $canaddchild = $addonQuantity - count($getchild);
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

    $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
        ->where('status', 1)
        ->first();

    $finalFeatures = [];
    $specialFeatureIds = [12, 13, 14, 15, 16, 17, 18];
    if ($alreadySubscribed) {
        $subscription = Subscription::with(['features' => function ($q) {
            $q->orderBy('position', 'ASC');
        }])->find($alreadySubscribed->subscription_id);

        $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
            ->where('subscription_id', $alreadySubscribed->id)
            ->get()
            ->keyBy('premium_feature_id');

        foreach ($subscription->features as $feature) {
            $pivotId = $feature->pivot->id;
            $addon = $premiumAddons->get($pivotId);
            $isSpecial = in_array($feature->id, $specialFeatureIds);

            $quantity = 0;
            $price = 0;
            $unlimited = 0;

            if ($alreadySubscribed->subscription_id == 9) {
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

    $user_profile->approved = $user_profile->status;

    return response([
        'status' => true,
        'user_profile' => $user_profile,
        'completion_percentage' => $completionByStep,
        'final_completion_percentage' => round($finalCompletionPercentage, 2),
        'path' => asset('content/doctor'),
        'getchild' => $getchild,
        'canaddchild' => $canaddchild,
        'qrcodepath' => asset('content/doctor/qrcode'),
        'subscription' => $alreadySubscribed,
        'finalFeatures' => $finalFeatures,
        'names' => [
            'languages' => $languageNames,
            'specialisations' => $specialisationNames,
            'services' => $serviceNames,
        ]
    ], 200);
}

    public function getProfile()
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
        $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion'/*, 'year_of_experience'*/];
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

        // print_r( $user_information->language_known); die;
        // 1|2|7|13
        $languageIds = array_filter(explode('|', $user_information->language_known)); // filters out empty values

        $languageNames = MasterLangauage::whereIn('id', $languageIds)
            ->select('id', 'name')
            ->get();
        
        $user_profile->language_data = $languageNames;
        $speciali = array_filter(explode('|', $user_information->specialisations)); // filters out empty values
        $specialiNames = MasterSpecialsation::whereIn('id', $speciali)
            ->select('id', 'name')
            ->get();
        $user_profile->specialisations_data = $specialiNames;
        $ser = array_filter(explode('|', $user_information->services)); // filters out empty values
        $services_name = MasterServices::whereIn('id', $ser)
            ->select('id', 'name')
            ->get();
        $user_profile->services_data = $services_name;

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
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();
        $finalFeatures = [];
        $specialFeatureIds = [12, 13, 14, 15, 16, 17, 18];
        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features' => function ($q) {
                $q->orderBy('position', 'ASC');
            }])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');
            foreach ($subscription->features as $feature) {
                $pivotId = $feature->pivot->id;

                $addon = $premiumAddons->get($pivotId);
                $isSpecial = in_array($feature->id, $specialFeatureIds);

                $quantity = 0;
                $price = 0;
                $unlimited = 0;
                if ($alreadySubscribed->subscription_id == 9) {
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
        $user_profile->approved = $user_profile->status;
        $is_shown = true;
        $response = [
            'status' => true,
            'user_profile' => $user_profile,
            'completion_percentage' => $completionByStep,
            'final_completion_percentage' =>round($finalCompletionPercentage, 2),
            'path' => asset('content/doctor'),
            'getchild'=>$getchild,
            'canaddchild'=>$canaddchild,
            'qrcodepath' => asset('content/doctor/qrcode'),
            'subscription'=>$alreadySubscribed,
            'finalFeatures'=>$finalFeatures,
            'clinicpath'=>asset('content/doctor/clinic'),
            "is_shown"=>$is_shown
        ];

        return response($response, 200);
    }

    public function getchild($id)
    {
        $child = array();
        $main = User::where('parent_id', auth()->user()->id)->with(['UserInformationDetails'])->whereNOTIN('status',[2])->get();
        // $main = User::where('parent_id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->whereNOTIN('status',[2])->get();
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
                // $user_clinic = $user_profile->UserClinicDetails;
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
                // $step8Completion = (count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
                $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
                $completionByStep = [
                    'step1' => $step1Completion,
                    'step2' => $step2Completion,
                    'step3' => $step3Completion,
                    'step4' => $step4Completion,
                    'step5' => $step5Completion,
                    'step6' => $step6Completion,
                    'step7' => $step7Completion,
                    // 'step8' => $step8Completion,
                    'step8' => $step9Completion,
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

    public function updateUserProfile(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('id','!=',auth()->user()->id)->whereNOTIN('status', [2]);
                }),
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('id','!=',auth()->user()->id)->whereNOTIN('status', [2]);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $image_name = auth()->user()->image;
        if (request('image')) 
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand().'_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/doctor', $image_name);
            
        }
        $update = array("name"=>$request->name,
                        "last_name"=>$request->last_name,
                        "email"=>$request->email,
                        "gender"=>$request->gender,
                        "date_of_birth"=>$request->date_of_birth,
                        "image"=>$image_name);
        User::whereId(auth()->user()->id)->update($update);
        $user_profile = User::where('id', auth()->user()->id)->get();
        $response = ['status' => true, 'user_profile' => $user_profile ,'path'=>asset('content/doctor/')];   
        return response($response, 200);
    }

    public function updateUserProfile2(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->experience = $request->experience;
        $a->language_known = $request->language_known;
        $a->specialisations = $request->specialisations;
        $a->services = $request->services;
        $a->hospital_worked_in = $request->hospital_worked_in;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function updateUserProfile3(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->about = $request->about;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function updateUserProfile4(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->degree = $request->degree;
        $a->college_institute = $request->college_institute;
        $a->year_of_completion = $request->year_of_completion;
        $a->year_of_experience = $request->year_of_experience;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function updateUserProfile5(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->award_college_institute = $request->award_college_institute;
        $a->award_year = $request->award_year;
        $a->awards_name = $request->awards_name;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }
    public function updateUserProfile6(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->registration_number = $request->registration_number;
        $a->registration_council = $request->registration_council;
        $a->registration_year = $request->registration_year;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function updateUserProfile7(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->doctor_availability = json_encode($request->doctor_availability);
        $a->save();
        $existing = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('doctor_availability', '')->orderBy('id', 'desc')->where('status',1)->get();

        if ($existing->count()) {
            foreach ($existing as $key) {
                $key->doctor_availability = json_encode($request->doctor_availability);
                $key->save();
            }
        }
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function clinicdetail(Request $request) {
        $a = UserEstablishmentClinic::where('id', $request->id)
        ->with(['galleries','holidays'])->first();

        $response = ['status' => true, 'user_profile' => $a ,'path' => asset('content/doctor'),'gallerypath' => asset('content/doctor/gallery'),
        'clinicpath'=>asset('content/doctor/clinic')];   
        return response($response, 200);
    }

    public function updateUserProfile8(Request $request)
    {
        $image_name = 'default';
        $a = UserEstablishmentClinic::where('user_id', auth()->user()->id)->where('status',1)->first();
        if ($a) {
            $image_name = $a->image;
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_clinic_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/doctor/clinic', $image_name);
                
            }
            $a->clinic_name = $request->clinic_name;
            $a->clinic_description = $request->clinic_description;
            $a->latitude = $request->latitude;
            $a->longitude = $request->longitude;
            $a->address = $request->address;
            $a->state = $request->state;
            $a->city = $request->city;
            $a->pincode = $request->pincode;
            $a->image = $image_name;
            $a->register_number = $request->register_number;
            $a->save();
        }
        else {
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_clinic_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/doctor/clinic', $image_name);
                
            }
            
            $a = new UserEstablishmentClinic();
            $a->user_id = auth()->user()->id;
            $a->clinic_name = $request->clinic_name;
            $a->clinic_description = $request->clinic_description;
            $a->latitude = $request->latitude;
            $a->longitude = $request->longitude;
            $a->address = $request->address;
            $a->state = $request->state;
            $a->city = $request->city;
            $a->pincode = $request->pincode;
            $a->image = $image_name;
            $a->register_number = $request->register_number;
            $a->status = 1;
            $a->save();
        }
        $existing = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $a->id)->orderBy('id', 'desc')->where('status',1)->first();

        if ($existing) {
            $existing->status = 0;
            $existing->save();
        }
        $ab = UserInformation::where('user_id', auth()->user()->id)->first();
        $new = new DoctorClinicSlot();
        $new->doctor_id = auth()->user()->id;
        $new->clinic_id = $a->id;
        $new->doctor_availability = $ab->doctor_availability ?? '';
        $new->status = 1;
        $new->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function updateUserProfile9(Request $request)
    {
        $a = UserEstablishmentClinic::where('id', $request->id)->first();
        $a->timings = json_encode($request->timings);
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function addGallery(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20000',
        ]);

        $uploadedImages = [];

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $filePath = $image->move('content/doctor/gallery', $imageName);

                $gallery = new UserEstablishmentGallery();
                $gallery->establishment_clinics_id = $request->id;
                $gallery->name = $imageName;
                $gallery->status = 1;
                $gallery->save();
                $uploadedImages[] = $gallery;
            }
        }
        $f = UserEstablishmentClinic::where('id',$request->id)->first();
        if ($f) {
            $f->status = 1;
            $f->save();
        }
        return response()->json(['success' => 'Images uploaded successfully',"path"=>asset('content/doctor/gallery/'), 'data' => $uploadedImages], 200);
    }

    public function deleteImages(Request $request)
    {
        $directory = 'content/doctor/gallery';
        $image = UserEstablishmentGallery::where('id', $request->ids)->first();
        if ($image) {
            $filePath = $directory . '/' . $image->name;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $image->delete();
        }

        return response()->json(['success' => 'Images deleted successfully']);
    }

    public function updateUserProfile12(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->theme = $request->theme;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function updateUserProfile13(Request $request)
    {
        foreach ($request->holiday as $key) {
            $a = UserEstablishmentHoliday::where('date', $key['date'])->where('establishment_clinics_id', $request->id)->where('status',1)->first();
            if ($a) {
                $a->date = $key['date'];
                $a->event_name = $key['event_name'];
                $a->end_date = $key['end_date'];
                $a->status = 1;
                $a->save();
            }
            else {
                $a = new UserEstablishmentHoliday();
                $a->establishment_clinics_id = $request->id;
                $a->date = $key['date'];
                $a->event_name = $key['event_name'];
                $a->end_date = $key['end_date'];
                $a->status = 1;
                $a->save();
            }
        }
        $a = UserEstablishmentHoliday::where('establishment_clinics_id', $request->id)->where('status',1)->orderBy('date','ASC')->get();
        $response = ['status' => true, 'holiday' => $a];   
        return response($response, 200); 
        // $a = UserEstablishmentHoliday::where('id', $request->id)->first();
        // $a->timings = json_encode($request->timings);
        // $a->save();
        // $response = ['status' => true, 'user_profile' => $a];   
        // return response($response, 200);
    }
    


    public function AddClinicEstablishmentAddress(Request $request)
    {
        $image_name = 'default';
        if (request('image')) 
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand().'_clinic_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/doctor/clinic', $image_name);
            
        }
        
        $a = new UserEstablishmentClinic();
        $a->user_id = auth()->user()->id;
        $a->clinic_name = $request->clinic_name;
        $a->clinic_description = $request->clinic_description;
        $a->latitude = $request->latitude;
        $a->longitude = $request->longitude;
        $a->address = $request->address;
        $a->state = $request->state;
        $a->city = $request->city;
        $a->pincode = $request->pincode;
        $a->image = $image_name;
        $a->register_number = $request->register_number;
        $a->status = 2;
        $a->save();
        $existing = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $a->id)->orderBy('id', 'desc')->where('status',1)->first();

        if ($existing) {
            $existing->status = 0;
            $existing->save();
        }
        $ab = UserInformation::where('user_id', auth()->user()->id)->first();
        $new = new DoctorClinicSlot();
        $new->doctor_id = auth()->user()->id;
        $new->clinic_id = $a->id;
        $new->doctor_availability = $ab->doctor_availability ?? '';
        $new->status = 1;
        $new->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }


    public function UpdateClinicEstablishmentAddress(Request $request)
    {
        $image_name = 'default';
        $a = UserEstablishmentClinic::where('id',$request->id)->where('status',1)->first();
        if ($a) {
            $image_name = $a->image;
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_clinic_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/doctor/clinic', $image_name);
                
            }
            $a->clinic_name = $request->clinic_name;
            $a->clinic_description = $request->clinic_description;
            $a->latitude = $request->latitude;
            $a->longitude = $request->longitude;
            $a->address = $request->address;
            $a->state = $request->state;
            $a->city = $request->city;
            $a->pincode = $request->pincode;
            $a->image = $image_name;
            $a->register_number = $request->register_number;
            $a->save();
        }
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }

    public function deleteClinic(Request $request)
    {
        $a = UserEstablishmentClinic::where('id',$request->id)->where('status',1)->first();
        if ($a) {
            $a->status = 2;
            $a->save();
        }
        $response = ['status' => true, 'msg' => 'Delete successfully'];   
        return response($response, 200);
    }

    public function AddClinicTimings(Request $request)
    {
        $a = UserEstablishmentClinic::where('id', $request->id)->first();
        $a->timings = json_encode($request->timings);
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }


    public function AddClinicHolidays(Request $request)
    {
        foreach ($request->holiday as $key) {
            
                $a = new UserEstablishmentHoliday();
                $a->establishment_clinics_id = $request->id;
                $a->date = $key['date'];
                $a->event_name = $key['event_name'];
                $a->end_date = $key['end_date'] ?? '';
                $a->status = 1;
                $a->save();
          
        }
        $a = UserEstablishmentHoliday::where('establishment_clinics_id', $request->id)->where('status',1)->orderBy('date','ASC')->get();
        $response = ['status' => true, 'holiday' => $a];   
        return response($response, 200); 
    }


    public function UpdateClinicHolidays(Request $request)
    {
        foreach ($request->holiday as $key) {
            $a = UserEstablishmentHoliday::where('id', $key['id'])->where('status',1)->first();
            if ($a) {
                $a->date = $key['date'];
                $a->event_name = $key['event_name'];
                $a->end_date = $key['end_date'] ?? '';
                $a->status = 1;
                $a->save();
            }
        }
        $a = UserEstablishmentHoliday::where('establishment_clinics_id', $request->id)->where('status',1)->orderBy('date','ASC')->get();
        $response = ['status' => true, 'holiday' => $a];   
        return response($response, 200); 
    }

    
    public function AddClinicGallery(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20000',
        ]);

        $uploadedImages = [];

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $filePath = $image->move('content/doctor/gallery', $imageName);

                $gallery = new UserEstablishmentGallery();
                $gallery->establishment_clinics_id = $request->id;
                $gallery->name = $imageName;
                $gallery->status = 1;
                $gallery->save();
                $uploadedImages[] = $gallery;
            }
        }

        $a = UserEstablishmentClinic::where('id', $request->id)->first();
        $a->status = 1;
        $a->save();

        return response()->json(['success' => 'Images uploaded successfully',"path"=>asset('content/doctor/gallery/'), 'data' => $uploadedImages], 200);
    }



    public function clinic_list(Request $request) {
        // Retrieve all clinic records based on the provided user ID
        if (auth()->user()->parent_id == 0) {
            $id = auth()->user()->id;
            
        } else $id = auth()->user()->parent_id;

        // dd($id);
        $clinics = UserEstablishmentClinic::where('user_id', $id)->whereIN('status',[0,1])->get();
        $totalquantity = $this->getquantity(13);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($clinics);
        }
        // Check if clinics exist
        if ($clinics->isEmpty()) {
            $response = ['status' => false, 'msg' => 'Sorry! No Clinic records found.','canaddchild'=>$canaddchild];
            return response($response, 422);
        }
    
        // Initialize an array to store clinic data with their holidays and galleries
        $clinicData = [];
    
        // Iterate over each clinic record
        foreach ($clinics as $clinic) {
            // Get associated holidays and gallery for each clinic
            $ClinicHoliday = UserEstablishmentHoliday::where('establishment_clinics_id', $clinic->id)->get();
            $Clinicgallery = UserEstablishmentGallery::where('establishment_clinics_id', $clinic->id)->get();
            
            // Set the image URL for the clinic
            $clinic->image = asset('content/doctor/clinic') . '/' . $clinic->image;
    
            // Iterate over the gallery collection and set the image URL for each gallery item
            foreach ($Clinicgallery as $gallery) {
                $gallery->name = asset('content/doctor/gallery') . '/' . $gallery->name;
            }
    
            // Add ClinicHoliday and Clinicgallery to the clinic data
            $clinic->ClinicHoliday = $ClinicHoliday;
            $clinic->Clinicgallery = $Clinicgallery;
    
            // Push the clinic data with holidays and gallery into the response array
            $clinicData[] = $clinic;
        }
        $totalquantity = $this->getquantity(13);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($clinics);
        }
    
        // Return the response with clinic data, holidays, and gallery attached to each clinic
        $response = [
            'status' => true,
            'msg' => 'Clinic List',
            'data' => $clinicData,
            'canaddchild'=>$canaddchild
        ];
        return response($response, 200);
    }

    public function updatePoints(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        $a->loyalty_points_base_100 = $request->loyalty_points_base_100;
        $a->referrer_a_friend_points = $request->referrer_a_friend_points;
        $a->referred_after_booking_points = $request->referred_after_booking_points;
        $a->redemption_points = $request->redemption_points;
        $a->save();
        $response = ['status' => true, 'user_profile' => $a];   
        return response($response, 200);
    }
    
    public function myconfig()
    {
        $check = UserInformation::where('user_id',auth()->user()->id)->first();
        $response = [
            'status' => true,
            'user_profile' => $check,
            
        ];

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
