<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Payment;
use App\Models\UserModels\UserProfile;
use Illuminate\Http\Request;


class AppointmentController extends Controller
{

    public function bookAppointment(Request $request)
{

    // If new user, create the user entry
    if ($request->user_type == 1) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|max:15|unique:users',
        ]);

        // Register the new user
        $user = UserProfile::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);
        $userId = $user->id; // Save the newly created user ID

        $appointment = Appointment::create([
            'user_id' => $userId,
            'doctor_id' => auth()->user()->id,
            'clinic_id' => $request->clinic_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 1,
        ]);

    } else {
        // Validate common fields
        $request->validate([
            'type_of_consultation' => 'required|string',
            'type_of_appointment' => 'required|string',
            'type_of_treatment' => 'required|string',
            'describe_symptoms' => 'required|string',
            'family_member' => 'nullable|string',
            'doctor' => 'required|nullable',
            'notes' => 'nullable|string',
        ]);
        // For existing users, use the provided user_id
        $userId = $request->user_id;
        // Create the appointment
        $appointment = Appointment::create([
            'user_id' => $userId,
            'doctor_id' => auth()->user()->id,
            'mobile' => $request->mobile,
            'type_of_consultation' => $request->type_of_consultation,
            'type_of_appointment' => $request->type_of_appointment,
            'type_of_treatment' => $request->type_of_treatment,
            'describe_symptoms' => $request->describe_symptoms,
            'family_member' => $request->family_member,
            'doctor' => $request->doctor,
            'notes' => $request->notes,
            'clinic_id' => $request->clinic_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 1,
        ]);
    }

    return response()->json(['message' => 'Appointment booked successfully', 'appointment' => $appointment], 201);
}

    // public function bookAppointment(Request $request)
    // {
    //     // If new user, first create the user entry
    //     if ($request->user_type == 1) {
    //         $request->validate([
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|email|unique:users',
    //             'mobile' => 'required|string|max:15|unique:users',
    //         ]);


    //         // Register the new user
    //         $user = User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'mobile' => $request->mobile,
    //         ]);


    //         return response()->json(['message' => 'User Created successfully', 'user' => $user], 201);
    //     } else {
    //         // For existing users
    //         // $user_id = auth()->user()->id;
    //         $appointment = Appointment::create([
    //             'user_id' => $request->user_id,
    //             'doctor_id' => auth()->user()->id,
    //             'mobile' => $request->mobile,
    //             'type_of_consultation' => $request->type_of_consultation,
    //             'type_of_appointment' => $request->type_of_appointment,
    //             'type_of_treatment' => $request->type_of_treatment,
    //             'desccribe_symtoms' => $request->desccribe_symtoms,
    //             'family_member' => $request->family_member,
    //             'doctor' => $request->doctor,
    //             'notes' => $request->notes,
    //         ]);
    //         return response()->json(['message' => 'Appointment booked successfully', 'appointment' => $appointment], 201);
    //     }




    //     if (!empty($appointment)) {
    //         $appointment = Appointment::find($appointment->id);
    //         $request->validate([
    //             'clinic_id' => 'required|date',
    //             'date' => 'required',
    //             'time' => 'required|string',
    //         ]);

    //         $appointment->update([
    //             'clinic_id' => $request->clinic_id,
    //             'date' => $request->date,
    //             'time' => $request->time,
    //         ]);
    //         return response()->json(['message' => 'Appointment booked successfully', 'appointment' => $appointment], 201);
    //     } else{
    //         $appointment = Appointment::create([
    //             'user_id' => $request->user_id,
    //             'doctor_id' => auth()->user()->id,
    //             'clinic_id' => $request->clinic_id,
    //             'date' => $request->date,
    //             'time' => $request->time,
    //         ]);
    //         return response()->json(['message' => 'Appointment booked successfully', 'appointment' => $appointment], 201);
    //     }


    // }


    public function paymentSummary($appointmentId)
    {
        $appointment = Appointment::with('user')->findOrFail($appointmentId);


        $summary = [
            'doctor_name' => $appointment->user->name,
            'type_of_treatment' => $appointment->type_of_treatment,
            'date' => $appointment->date,
            'time' => $appointment->time,
            'tax_amount' => 100, // Dummy value for tax calculation
            'amount' => 1100, // Dummy value, base_fee + tax
        ];


        return response()->json(['payment_summary' => $summary], 200);
    }


    public function makePayment(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);


        $request->validate([
            'amount' => 'required|numeric|min:0',
            // 'payment_method' => 'required|string|in:card,cash,upi',
        ]);


        // Payment processing logic here...


        $payment = Payment::create([
            'user_id' => auth()->user()->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'tax_amount' => 100, // Dummy value for tax calculation
            'payment_status' => 'pending', // Assuming successful payment
        ]);


        $appointment->update(['payment_status' => 'pending']);


        return response()->json(['message' => 'Payment successful', 'payment' => $payment], 200);
    }


}
