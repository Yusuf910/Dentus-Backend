<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Clinic;
use App\Models\ClinicDoctor;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClinicController extends Controller
{
    public function generateQrCode(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:clinics,clinic_id',
        ]);

        $clinicId = $request->clinic_id;
       $clinicUrl = route('clinic.scan-qr', ['clinic_id' => $clinicId]);
        $clinic = Clinic::where('clinic_id',$request->clinic_id)->first();

        $qrCode = QrCode::create($clinic)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $qrCodeFilePath = 'qr_codes/clinic_' . $clinicId . '.png';
        Storage::disk('public')->put($qrCodeFilePath, $result->getString());

        // Save the QR code file path to the clinic's record
        Clinic::where('clinic_id', $clinicId)->update(['qr_code_path' => $qrCodeFilePath]);

        return response()->json([
            'message' => 'QR Code Generated',
            'qr_code_path' => Storage::url($qrCodeFilePath),
        ], 200);
    }

    public function scanQrCode(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:clinics,clinic_id',
        ]);

        $clinic = Clinic::where('clinic_id',$request->clinic_id)->first();

        return response()->json([
            'message' => 'Clinic found',
            'clinic' => $clinic,
        ], 200);
    }

    public function ClinicDetail(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:clinics,clinic_id',
        ]);

        $clinic = Clinic::where('clinic_id', $request->clinic_id)->first();
        $clinic_dr = ClinicDoctor::where('clinic_id', $request->clinic_id)->first();
        $doctor = Doctor::where('doctor_id', $clinic_dr->doctor_id)->first();
        $doctor_profile = DoctorProfile::where('id', $doctor->doctor_id)->first();

        $clinic->logo =  asset('project/public/clinics/').'/'.$clinic->logo ;
        $doctor_profile->profile_picture =  asset('project/public/doctor_profile/').'/'.$doctor_profile->profile_picture ;
        return response()->json([
            'message' => 'Clinic found',
            'clinic' => $clinic,
            // 'clinic_image_path' => asset('clinics/' . $clinic->image), // Assuming the clinic has an 'image' attribute
            'doctor' => $doctor,
            'doctor_profile' => $doctor_profile,
            // 'doctor_profile_image_path' => asset('doctor_profile/' . $doctor_profile->image), // Assuming the profile has an 'image' attribute
            'status' => true
        ], 200);
    }

    public function FlashScreen(Request $request)
    {

        // dd(Auth()->user());
        $request->validate([
            // 'clinic_id' => 'required|exists:clinics,clinic_id',
        ]);
        $user = User::where('id', auth()->user()->id)->first();
        $clinic = Clinic::where('clinic_id',$user->clinic_id)->first();
        $clinic_dr = ClinicDoctor::where('clinic_id',$clinic->clinic_id)->first();
        $doctor = Doctor::where('doctor_id',$clinic_dr->doctor_id)->first();
        $doctor_profile = DoctorProfile::where('id',$doctor->doctor_id)->first();

       $clinic->logo =  asset('project/public/clinics/').'/'.$clinic->logo ;
       $doctor_profile->profile_picture =  asset('project/public/doctor_profile/').'/'.$doctor_profile->profile_picture ;
        return response()->json([
            'message' => 'Clinic found',
            'user' => $user,
            'clinic' => $clinic,
            'doctor' => $doctor,
            'doctor_profile' => $doctor_profile,
            // 'doctor_profile_image_path' => asset('doctor_profile/' . $doctor_profile->image),
            'status' => true
            // 'path'=>asset('clinics/')
        ], 200);
    }
}
