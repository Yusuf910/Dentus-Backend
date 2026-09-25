<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use App\Models\Education;
use App\Models\MedicalRegistration;
use App\Models\EstablishmentTimings;
use App\Models\DoctorAvailability;
use App\Models\EstablishmentAddress;
use App\Models\EstablishmentGallery;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Save Personal Information
    public function savePersonalInfo(Request $request)
    {
        // dd(Auth()->user());
        $user = User::where('id', auth()->user()->id)->first();
        // echo "$user"; die;
        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->first_name = $request->input('first_name');
        $profile->last_name = $request->input('last_name');
        $profile->email = $request->input('email');
        $profile->mobile_no = $request->input('mobile_no');
        $profile->gender = $request->input('gender');
        $profile->date_of_birth = $request->input('date_of_birth');
        $profile->status = 1;
        $profile->save();

        return response()->json(['message' => 'Personal Info saved successfully'], 200);
    }

            public function saveProfessionalInfo(Request $request)
        {
            $user = User::where('id', auth()->user()->id)->first();
            $profile = Profile::find($user->id);
            $profile->experience = $request->input('experience');
            $profile->languages_known = json_encode($request->input('languages_known')); // Save as JSON
            $profile->specialization = json_encode($request->input('specialization'));
            $profile->service = json_encode($request->input('service'));
            $profile->hospital_worked_in = $request->input('hospital_worked_in');
            $profile->save();

            return response()->json(['message' => 'Professional Info saved successfully'], 200);
        }


             public function saveBiography(Request $request)
            {
                $user = User::where('id', auth()->user()->id)->first();
                $profile = Profile::find($user->id);
                $profile->biography = $request->input('biography');
                $profile->save();

                return response()->json(['message' => 'Biography saved successfully'], 200);
            }


            public function saveEducation(Request $request)
            {
                $user = User::where('id', auth()->user()->id)->first();
                $education = new Education();
                $education->user_id = $request->input('user_id');
                $education->degree = $request->input('degree');
                $education->college_institute = $request->input('college_institute');
                $education->year_of_completion = $request->input('year_of_completion');
                $education->years_of_experience = $request->input('years_of_experience');
                $education->status = 1;
                $education->save();

                return response()->json(['message' => 'Education details saved successfully'], 200);
            }


            public function saveMedicalRegistration(Request $request)
            {
                $user = User::where('id', auth()->user()->id)->first();
                $registration = new MedicalRegistration();
                $registration->user_id = $user->id;
                $registration->registration_number = $request->input('registration_number');
                $registration->registration_council = $request->input('registration_council');
                $registration->registration_year = $request->input('registration_year');
                $registration->status = 1;
                $registration->save();

                return response()->json(['message' => 'Medical registration saved successfully'], 200);
            }


            public function saveEstablishmentTimings(Request $request)
            {
                $user = User::where('id', auth()->user()->id)->first();
                $timings = new EstablishmentTimings();
                $timings->user_id = $user->id;
                $timings->days = json_encode($request->input('days')); // Save as JSON
                $timings->morning_from = $request->input('morning_from');
                $timings->morning_to = $request->input('morning_to');
                $timings->evening_from = $request->input('evening_from');
                $timings->evening_to = $request->input('evening_to');
                $timings->status = 1;
                $timings->save();

                return response()->json(['message' => 'Establishment timings saved successfully'], 200);
            }


            public function saveDoctorAvailability(Request $request)
            {
                $user = User::where('id', auth()->user()->id)->first();
                $availability = new DoctorAvailability();
                $availability->user_id = $user->id;
                $availability->days = json_encode($request->input('days'));
                $availability->morning_from = $request->input('morning_from');
                $availability->morning_to = $request->input('morning_to');
                $availability->evening_from = $request->input('evening_from');
                $availability->evening_to = $request->input('evening_to');
                $availability->status = 1;
                $availability->save();

                return response()->json(['message' => 'Doctor availability saved successfully'], 200);
            }


            public function saveEstablishmentAddress(Request $request)
            {
                $request->validate([
                    'clinic_name' => 'required|string|max:255',
                    'clinic_description' => 'required|string',
                    'address' => 'required|string',
                    'state' => 'required|string',
                    'city' => 'required|string',
                    'pincode' => 'required|digits:6',
                    'latitude' => 'required|numeric',  // Add validation for latitude
                    'longitude' => 'required|numeric', // Add validation for longitude
                ]);

                $user = User::where('id', auth()->user()->id)->first();
                $address = new EstablishmentAddress();
                $address->user_id = $user->id;
                $address->clinic_name = $request->input('clinic_name');
                $address->clinic_description = $request->input('clinic_description');
                $address->address = $request->input('address');
                $address->state = $request->input('state');
                $address->city = $request->input('city');
                $address->pincode = $request->input('pincode');
                $address->latitude = $request->input('latitude');  
                $address->longitude = $request->input('longitude'); 
                $address->status = 1;
                $address->save();

                return response()->json(['message' => 'Establishment address saved successfully'], 200);
            }


            public function uploadEstablishmentGallery(Request $request)
            {
               
                $request->validate([
                    'images.*' => 'required|image|max:1024',
                ], [
                    'images.*.max' => 'Each image must be smaller than 1MB.',
                    'images.*.image' => 'The file must be an image.',
                    'images.*.required' => 'You must upload at least one image.',
                ]);

                if ($request->hasFile('images') && is_array($request->file('images'))) {

                    if (count($request->file('images')) > 6) {
                        return back()->withErrors(['images' => 'You can only upload a maximum of 6 images.']);
                    }
                } else {
                    return back()->withErrors(['images' => 'You must upload at least one image.']);
                }

                $imagePaths = [];

                foreach ($request->file('images') as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $fileNameWithTheExtension = $image->getClientOriginalName();
                        $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                        $extension = $image->getClientOriginalExtension();
                        $image_name = $fileName . '_' . time() . '.' . $extension;

                        $image->move(public_path('gallery'), $image_name);
                        $imagePaths[] = url('gallery/' . $image_name);
                    }
                }
                $user = User::where('id', auth()->user()->id)->first();
                foreach ($imagePaths as $image_name) {
                    $gallery = new EstablishmentGallery();
                    $gallery->user_id = $user->id;
                    $gallery->image = basename($image_name); 
                    $gallery->status = 1;
                    $gallery->save();
                }

                return response()->json(['message' => 'Photos uploaded successfully', 'paths' => $imagePaths], 200);
            }







}
