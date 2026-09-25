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

use DB;
use Hash;
use Carbon;
class DoctorDetailController extends Controller
{
    public function getProfile()
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
        $response = ['status' => true, 'user_profile' => $a];
        return response($response, 200);
    }

    public function updateUserProfile8(Request $request)
    {
        $a = UserEstablishmentClinic::where('user_id', auth()->user()->id)->where('status',1)->first();
        if ($a) {
            $a->clinic_name = $request->clinic_name;
            $a->clinic_description = $request->clinic_description;
            $a->latitude = $request->latitude;
            $a->longitude = $request->longitude;
            $a->address = $request->address;
            $a->state = $request->state;
            $a->city = $request->city;
            $a->pincode = $request->pincode;
            $a->save();
        }
        else {
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
            $a->status = 1;
            $a->save();
        }
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
            $a = UserEstablishmentHoliday::where('id', $key['id'])->where('status',1)->first();
            if ($a) {
                $a->date = $key['date'];
                $a->event_name = $key['event_name'];
                $a->status = 1;
                $a->save();
            }
            else {
                $a = new UserEstablishmentHoliday();
                $a->establishment_clinics_id = $request->id;
                $a->date = $key['date'];
                $a->event_name = $key['event_name'];
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


}
