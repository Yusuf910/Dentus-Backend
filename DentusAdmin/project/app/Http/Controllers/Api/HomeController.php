<?php

namespace App\Http\Controllers\Api;

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
    public function SeasonalOffer(Request $request)
    {

        $request->validate([
            'offer_type' => 'required',
            'treatment_packages' => 'required',
            'discount' => 'required',
            'validity' => 'required',
        ]);
        $image = $request->image;
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            $fileNameWithTheExtension = $image->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $image_name = $fileName . '_' . time() . '.' . $extension;

            $image->move(public_path('gallery'), $image_name);
            $imagePaths[] = url('gallery/' . $image_name);
        }

        $offer_banner = Banner::create([
            'user_id' => auth()->user()->id,
            'offer_type' => $request->offer_type,
            'treatment_packages' => $request->treatment_packages,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'image' => basename($image_name),
        ]);


        return response()->json(['message' => 'Seasonal Promotional Offers', 'Offer' => $offer_banner], 200);
    }


    public function SeasonalOfferList(Request $request)
    {
        // Fetch offers for the authenticated user
        $offers = Banner::where('user_id', auth()->user()->id)->get();

        // Map over the offers to modify the image property
        $offers = $offers->map(function ($offer) {
            $offer->image = asset('project/public/gallery/' . $offer->image);
            return $offer;
        });

        return response()->json([
            'message' => 'SeasonalOfferList found',
            'offers' => $offers, // Renamed to `offers` for clarity
            'status' => true
        ], 200);
    }

}
