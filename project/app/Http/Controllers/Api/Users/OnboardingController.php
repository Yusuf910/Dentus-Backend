<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\ClinicDoctor;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OnboardingController extends Controller
{

    public function test(Request $request)
    {
        echo "ccddcdc"; die;
    }

    // Create clinic with doctors
    public function createClinicWithDoctors(Request $request)
    {
        // echo "ccddcdc"; die;
        $clinic = Clinic::create($request->only('clinic_name', 'logo', 'address', 'contact_info', 'description', 'social_media_links', 'admin_doctor_id'));

        // Generate QR code for the clinic
        // $clinic_qr_code = QrCode::format('png')->size(200)->generate('Clinic ID: ' . $clinic->id);
        // $qr_code_path = 'uploads' . $clinic->id . '.png';
        // \Storage::put($qr_code_path, $clinic_qr_code);
        // $clinic->qr_code = $qr_code_path;
        // $clinic->save();
        try {
            $qrCode = QrCode::format('png')->size(200)->generate('Clinic ID: ' . $clinic->id);
            $qrCodePath = 'uploads/clinics_' . $clinic->id . '.png';
            \Storage::disk('public')->put($qrCodePath, $qrCode);

            $clinic->qr_code = $qrCodePath;
            $clinic->save();
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
        }


        // Assign doctors to the clinic
        if($request->has('doctors')) {
            foreach ($request->doctors as $doctorData) {
                $doctor = Doctor::create($doctorData);

                // Generate QR code for the doctor
                // $doctor_qr_code = QrCode::format('png')->size(200)->generate('Doctor ID: ' . $doctor->id);
                // $qr_code_path = 'qr_codes/doctor_' . $doctor->id . '.png';
                // \Storage::put($qr_code_path, $doctor_qr_code);
                // $doctor->qr_code = $qr_code_path;
                // $doctor->save();
                try {
                    $doctor_qr_code = QrCode::format('png')->size(200)->generate('Doctor ID: ' . $doctor->id);
                    $qr_code_path = 'uploads' . $doctor->id . '.png';
                    \Storage::put($qr_code_path, $doctor_qr_code);

                    $doctor->qr_code = $qr_code_path;
                    $doctor->save();
                }
                catch (\Exception $e) {
                    // Handle the exception, maybe log the error or return a message
                    \Log::error('QR Code generation failed: ' . $e->getMessage());
                }

                ClinicDoctor::create([
                    'clinic_id' => $clinic->id,
                    'doctor_id' => $doctor->id,
                    'role' => $doctorData['role']
                ]);
            }
        }

        return response()->json([
            'message' => 'Clinic and doctors created successfully',
            'clinic' => $clinic,
            'qr_code_url' => url('storage/' . $clinic->qr_code),
            // 'doctors' => $clinic->doctors
        ], 201);
    }

    // Onboard an individual doctor
    public function onboardDoctor(Request $request)
    {
        $doctor = Doctor::create($request->only('name', 'specialization', 'experience_years', 'languages_spoken', 'bio', 'work_location', 'consultation_timings'));

        DoctorProfile::create([
            'doctor_id' => $doctor->id,
            'user_id' => $request->user_id,
            'profile_picture' => $request->profile_picture,
            'bio' => $request->bio,
            'specialization' => $request->specialization,
            'experience_years' => $request->experience_years,
            'languages_spoken' => $request->languages_spoken,
            'practice_type' => $request->practice_type,
            'introduction' => $request->introduction,
            'education' => $request->education,
            'awards' => $request->awards,
            'medical_registration_doc' => $request->medical_registration_doc,
            'clinic_address' => $request->clinic_address,
            'establishment_timings' => $request->establishment_timings,
            'subscription_plan' => $request->subscription_plan,
            'is_main_doctor' => $request->is_main_doctor
        ]);

        try {
            $doctor_qr_code = QrCode::format('png')->size(200)->generate('Doctor ID: ' . $doctor->id);
            $qr_code_path = 'uploads' . $doctor->id . '.png';
            \Storage::put($qr_code_path, $doctor_qr_code);

            $doctor->qr_code = $qr_code_path;
            $doctor->save();
        }
        catch (\Exception $e) {
            // Handle the exception, maybe log the error or return a message
            \Log::error('QR Code generation failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Doctor onboarded successfully',
            'doctor' => $doctor,
            'profile' => $doctor->profile,
            'qr_code_url' => url('storage/' . $doctor->qr_code)
        ], 201);
    }
}
