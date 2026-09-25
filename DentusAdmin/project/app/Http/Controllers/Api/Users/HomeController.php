<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\ClinicDoctor;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Models\Banner;
use App\Models\SocialMedia;
use Illuminate\Support\Facades\Password;

class HomeController extends Controller
{
    public function OffersBanner(Request $request)
{
    $user = User::where('id', auth()->user()->id)->first();
    $clinic = Clinic::where('clinic_id', $user->clinic_id)->first();
    $doctor = '';
    $clinic_dr = ClinicDoctor::where('clinic_id', $clinic->clinic_id)->first();

    if (!empty($clinic_dr->doctor_id)) {
        $doctor = Doctor::where('doctor_id', $clinic_dr->doctor_id)->first();
        $doctor_profile = DoctorProfile::where('id', $doctor->doctor_id)->first();
    }

    $offers_banner = Banner::all();
    $social_media = SocialMedia::all();

    foreach ($offers_banner as $banner) {
        $banner->image = asset('project/public/banner/') . '/' . $banner->image;
    }
    foreach ($social_media as $media) {
        $media->video = asset('project/public/social_media/') . '/' . $media->video;
    }

    return response()->json([
        'user' => $user,
        'Offers' => $offers_banner,
        'SocialMedia' => $social_media,
        // 'clinic' => $clinic,
        'doctor' => $doctor,
        // 'doctor_profile' => $doctor_profile,
        'status' => true
    ], 200);
}

}
