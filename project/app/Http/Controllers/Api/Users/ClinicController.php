<?php
namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DoctorProfile;
use App\Models\User;
use App\Models\UserInformation;
use App\Models\UserModels\UserProfile;
use App\Models\UserEstablishmentClinic;
use App\Models\MasterSpecialsation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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

        $clinic = Clinic::where('clinic_id',$request->clinic_id)->where('status',1)->first();

        return response()->json([
            'message' => 'Clinic found',
            'clinic' => $clinic,
        ], 200);
    }

    public function ClinicDetail(Request $request)
    {
        // Decrypt the clinic_id
        $decrypted = Crypt::decryptString($request->clinic_id);

        // Unserialize the decrypted value to get the integer (e.g., 15)
        $clinicId = unserialize($decrypted);
        // dd($clinicId);
        // Check if unserialization was successful (should be an integer)
        if (!is_int($clinicId)) {
            return response()->json([
                'message' => 'Invalid clinic ID format.',
                'status' => false
            ], 400);
        }

        // Fetch the clinic information using the decrypted clinic_id
        $clinic = UserEstablishmentClinic::where('user_id', $clinicId)->where('status',1)->first();

        if (!$clinic) {
            return response()->json([
                'message' => 'Clinic not found.',
                'status' => false
            ], 404);
        }

        // Fetch the doctor's information related to the clinic
        $clinic_dr = User::where('id', $clinic->user_id)->first();
        $doctor = UserInformation::where('user_id', $clinic_dr->id)->first();

        // Generate the asset URLs for the clinic logo and doctor profile image
        $clinic->logo = asset('project/public/clinics/') . '/' . $clinic->logo;
        $clinic_dr->image = asset('content/doctor/') . '/' . $clinic_dr->image;

        // Check if doctor profile has 'specialisations' field
        if ($doctor && $doctor->specialisations) {
            // Split the specialisations string into an array of IDs
            $specialisationIds = explode('|', $doctor->specialisations);

            // Fetch the matching specialisations from the MasterSpecialisations table
            $specialisations = MasterSpecialsation::whereIn('id', $specialisationIds)->get();

            // Format the specialisations data to return as an array of names
            $doctor->specialisations = $specialisations->map(function ($specialisation) {
                return $specialisation->name; // Assuming the 'name' column exists in the MasterSpecialisation table
            })->toArray();
        } else {
            // If no specialisations are found, set it to an empty array
            $doctor->specialisations = [];
        }
        $clinics = UserEstablishmentClinic::where('user_id', $clinic->user_id)->where('status',1)->with('holidays','galleries')->get();
        $clinics->each(function ($clinics) {
            $clinics->logo = asset('content/doctor/clinic') . '/' . $clinics->logo;
            $clinics->image = asset('content/doctor/clinic') . '/' . $clinics->image;
        });
        // Return the response as JSON with the clinic and doctor details
        return response()->json([
            'message' => 'Clinic found',
            'clinic' => $clinic,
            'doctor' => $clinic_dr,
            'doctor_profile' => $doctor,
            'status' => true,
            'clinics'=>$clinics
        ], 200);
    }

    


    public function FlashScreen(Request $request)
    {

        // dd(Auth()->user());

        $user = UserProfile::where('id', auth()->user()->id)->first();
        if ((int)$user->clinic_id) {
            $clinicId = $user->link_id;
        }
        else {
            $decrypted = Crypt::decryptString($user->clinic_id);
            $clinicId = unserialize($decrypted);
        }

        $clinic = UserEstablishmentClinic::where('user_id',$clinicId)->first();
        $doctor = User::where('id',$clinic->user_id)->first();
        $doctor_profile = UserInformation::where('user_id',$doctor->id)->first();

       $user->image =  asset('project/public/member_images/').'/'.$user->image ;
       $clinic->logo =  asset('content/doctor/clinic/').'/'.$clinic->image ;
       $doctor->image =  asset('content/doctor/').'/'.$doctor->image ;
        return response()->json([
            'message' => 'Clinic found',
            'user' => $user,
            'clinic' => $clinic,
            'doctor' => $doctor,
            'doctor_profile' => $doctor_profile,
            'status' => true
        ], 200);
    }
}
