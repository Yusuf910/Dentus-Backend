<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\TreatmentDoctor;
use App\Models\UserModels\Booking;
use App\Models\User;
use App\Models\UserEstablishmentClinic;
use App\Models\UserInformation;
use App\Models\Prescription;
use App\Models\Drug;
use App\Models\Generalsetting;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\Member;
use App\Models\UserModels\MedicalRecord;
use App\Models\MasterSpecialsation;
use App\Models\UserModels\Rating;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Models\UserModels\UserPayment;
use App\Traits\SdSendSms;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
use App\Models\DoctorClinicSlot;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use Carbon\Carbon;
use App\Models\UserEstablishmentHoliday;
use File;
use Illuminate\Support\Facades\Http;
class BookingController extends Controller
{
    
    use SdSendSms;
    public function DoctorListToken(Request $request) {
        // Get authenticated user's ID
        $userId = auth()->user()->id;
    
        // Query users where the ID matches the authenticated user's ID
        $doctorsById = User::where('id', $userId)->get();
    
        // Query users where the parent_id matches the authenticated user's ID
        $doctorsByParentId = User::where('parent_id', $userId)->get();
    
        // Merge both result sets
        $doctors = $doctorsById->merge($doctorsByParentId);
    
        // Check if there are any doctors found
        if (!$doctors->isEmpty()) {
            // Return success response with the list of doctors
            $response = ['status' => true, 'msg' => 'Doctor List', 'data' => $doctors];
            return response($response, 200);
        } else {
            // Return error response if no doctors were found
            $response = ['status' => false, 'msg' => 'Sorry! No Doctor found.'];
            return response($response, 422);
        }
    }
    


// public function BookingPendingList(Request $request)
// {
//     $userId = auth()->user();

//     // Fetch the user(s) where either the `id` or `parent_id` matches the $userId
//     if ($userId->parent_id == 0) {
//         $users = User::where(function ($query) use ($userId) {
//             $query->where('id', $userId->id)
//                 ->orWhere('parent_id', $userId->id);
//         })
//         ->get(); // This will return a collection of users
//     } elseif ($userId->parent_id > 0) {
//         $users = User::where('id', $userId->id)->first(); // This will return a single user
//     }

//     // Fetch appointments for the users
//     // If multiple users are returned, use `whereIn()`, otherwise, use `where()`
//     if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
//         // Multiple users (using whereIn)
//         $appointments = Booking::whereIn('doctor_id', $users->pluck('id')) // Get appointments for all matching doctors
//             ->where('status', 0)
//             ->orderBy('schedule_date', 'desc')
//             ->get();
//     } else {
//         // Single user (using where)
//         $appointments = Booking::where('doctor_id', $users->id)
//             ->where('status', 0)
//             ->orderBy('schedule_date', 'desc')
//             ->get();
//     }

//     // Map appointments and include doctor and treatment details
//     $appointmentsWithDoctor = $appointments->map(function ($appointment) {
//         // Get the doctor associated with this appointment
//         $doctor = User::find($appointment->doctor_id);

//         if ($doctor) {
//             $doctor->image = asset('content/doctor/' . $doctor->image);
//             // Get the doctor's specialisation IDs from UserInformation
//             $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            
//             if ($doctorprofile && $doctorprofile->specialisations) {
//                 $specialisationIds = explode('|', $doctorprofile->specialisations);
//                 $specialisationNames = \DB::table('master_specialsations')
//                     ->whereIn('id', $specialisationIds)
//                     ->pluck('name')
//                     ->toArray();
                
//                 // Adding doctor and specialisation names to the appointment
//                 $appointment->doctor = $doctor;
//                 $appointment->doctorprofile = $specialisationNames;
//             } else {
//                 // If no specialisation exists
//                 $appointment->doctor = $doctor;
//                 $appointment->doctorprofile = [];
//             }

//             // Now fetch the treatment name and add it directly to the appointment object
//             $treatment = DB::table('treatments')
//                 ->where('id', $appointment->treatment_id)
//                 ->first(['treatment_name']);
            
//             // Directly setting treatment_name next to treatment_id in the appointment
//             if ($treatment) {
//                 $appointment->treatment_name = $treatment->treatment_name;
//             } else {
//                 // If no treatment found, set treatment_name to null
//                 $appointment->treatment_name = null;
//             }
//         }

//         return $appointment;
//     });

//     // Return the appointments with doctor and treatment details inside the appointments object
//     return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
// }
   





   public function rejectBooking(Request $request)
        {
            $validated = $request->validate([
                'booking_id' => 'required'
            ]);

            $a = Booking::find($request->booking_id);
            $a->cancel_by = $request->cancel_by ?? 'doctor';
            $a->cancel_date = date('Y-m-d h:ia');
            $a->status = 3;
            $a->save();
            if ($a->loyality_points > 0) {
                $get = UserPayment::where('booking_id',$a->id)->where('type',2)->first();
                if ($get) {
                    $u = UserProfile::where('id',$a->user_id)->first();
                    $update_wallet =  $u->wallet+$get->amount;
                    $new = new UserPayment();
                    $new->user_id = $u->id;
                    $new->type = 2;
                    $new->booking_id = $a->id;
                    $new->action = 'credit';
                    $new->amount = $get->amount;
                    $new->old_balance = $u->wallet;
                    $new->payment_status = 'completed';
                    $new->payment_method = 'online';
                    $new->new_balance = $update_wallet;
                    $new->status = 1;
                    $new->trxn_id =  time().rand();
                    $new->save();
                    $u->wallet = $update_wallet;
                    $u->save();
                }
            }
            $booking = $a;
            if(!empty($booking->payid)) {
                $scheduleDate = $a->schedule_date;
                $scheduleTime = $a->schedule_time;
                $scheduledDateTimeStr = $scheduleDate . ' ' . $scheduleTime;
                $scheduledDateTime = strtotime($scheduledDateTimeStr);
                $now = time();
                $diffInSeconds = $scheduledDateTime - $now;
                $hoursUntilBooking = $diffInSeconds / 3600;
                if ($hoursUntilBooking > 24) { 
                    $key_id = env('RAZORPAY_KEYID_TEST');
                    $key_secret = env('RAZORPAY_SECRET_TEST');

                    $originalPayId = $booking->payid;
                    $paymentId = null;

                    if (str_starts_with($originalPayId, 'pay_')) {
                        $paymentId = $originalPayId;
                    } elseif (str_starts_with($originalPayId, 'order_')) {
                        $orderResponse = Http::withBasicAuth($key_id, $key_secret)
                            ->get("https://api.razorpay.com/v1/orders/{$originalPayId}/payments");

                        if ($orderResponse->successful() && isset($orderResponse['items'][0]['id'])) {
                            $paymentId = $orderResponse['items'][0]['id'];
                        }
                    }
                    $refundUrl = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";
                    if ($booking->is_paid == 2) {
                       $amountInPaise = (int) ($booking->partial_amount * 100);
                    } else
                    $amountInPaise = (int) ($booking->total_amount * 100);
                    $refundPayload = [];

                    if ($amountInPaise > 0) {
                        $refundPayload['amount'] = $amountInPaise;
                    }
                    try {
                        $refundResponse = empty($refundPayload)
                            ? Http::withBasicAuth($key_id, $key_secret)->post($refundUrl)
                            : Http::withBasicAuth($key_id, $key_secret)->post($refundUrl, $refundPayload);

                        if ($refundResponse->successful()) {
                            $a->refund_details = $refundResponse->json();
                            $a->refund_status = 1;
                            $a->save();
                        } else {
                            $a->refund_details = $refundResponse->json();
                            $a->refund_status = 2;
                            $a->save();
                        }
                    } catch (\Exception $e) {
                        $a->refund_details = $e;
                        $a->refund_status = 3;
                        $a->save();
                    }
                }
            }
            return response()->json(['status' => true,'message' => 'Booking Cancel successfully', 'reason' => $a], 201);
        }


        public function AcceptBooking(Request $request)
        {
            $validated = $request->validate([
                'booking_id' => 'required'
            ]);

            $a = Booking::find($request->booking_id);
            $a->status = 4;
            $a->save();

            return response()->json(['status' => true,'message' => 'Booking Accept successfully', 'reason' => $a], 201);
        }





        public function BookingPendingDateFilter_old(Request $request)
        {
            $userId = auth()->user();
        
            // Fetch the user(s) where either the id or parent_id matches the $userId
            if ($userId->parent_id == 0) {
                $users = User::where(function ($query) use ($userId) {
                    $query->where('id', $userId->id)
                        ->orWhere('parent_id', $userId->id);
                })
                ->get(); // This will return a collection of users
            } elseif ($userId->parent_id > 0) {
                $users = User::where('id', $userId->id)->first(); // This will return a single user
            }
        
            // Check if dentist date is provided in the request
            $dentistdate = $request->date;
            $dentistId = $request->dentist_id;
        
            // Ensure the date format is normalized (assuming format 'YYYY-MM-DD')
            if ($dentistdate) {
                // Convert to the same format as the schedule_date column (YYYY-MM-DD)
                $dentistdate = \Carbon\Carbon::parse($dentistdate)->format('Y-m-d');
            }
        
            // Fetch appointments for the users
            if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
                // Multiple users (using whereIn)
                $query = Booking::whereIn('doctor_id', $users->pluck('id'))
                                ->where('status', 4);
        
                // Add condition for dentist_id if it's provided
                if ($dentistId) {
                    $query->where('doctor_id', $dentistId);
                }
        
                if ($dentistdate != 0) {
                    // If dentist_date is provided, filter appointments by schedule_date
                    $query->whereDate('schedule_date', $dentistdate); // Use whereDate to ignore time
                }
        
                $appointments = $query->orderBy('schedule_date', 'desc')->get();
            } else {
                // Single user (using where)
                $query = Booking::where('doctor_id', $users->id)
                                ->where('status', 4);
        
                // Add condition for dentist_id if it's provided
                if ($dentistId) {
                    $query->where('doctor_id', $dentistId);
                }
        
                if ($dentistdate != 0) {
                    // If dentist_date is provided, filter appointments by schedule_date
                    $query->whereDate('schedule_date', $dentistdate); // Use whereDate to ignore time
                }
        
                $appointments = $query->orderBy('schedule_date', 'desc')->get();
            }
        
            // Map appointments and include doctor, treatment, clinic, and user profile details
            $appointmentsWithDoctor = $appointments->map(function ($appointment) {
                // Get the doctor associated with this appointment
                $doctor = User::find($appointment->doctor_id);
        
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
        
                    // Get the doctor's specialisation IDs from UserInformation
                    $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        
                    if ($doctorprofile && $doctorprofile->specialisations) {
                        $specialisationIds = explode('|', $doctorprofile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
        
                        // Adding doctor and specialisation names to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // If no specialisation exists
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = [];
                    }
        
                    // Fetch the treatment name and add it directly to the appointment object
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'average_duration'])
                        ->first(); // Using first() to get a single result
        
                    // Add treatment details to appointment
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        // If no treatment found, set both to null
                        $appointment->treatment_name = null;
                        $appointment->average_duration = null;
                    }
        
                    // Fetch the clinic information
                    $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
                    if ($clinic) {
                        $appointment->clinic = $clinic; // Add clinic details
        
                        // Fetch the UserProfile details associated with the clinic
                        $userProfile = UserProfile::where('id', $appointment->user_id)->first();
                        if ($userProfile) {
                            $appointment->user = $userProfile; // Move UserProfile outside the clinic object
                        } else {
                            $appointment->user = null; // If no UserProfile found, set to null
                        }
                    } else {
                        $appointment->clinic = null; // If no clinic found, return null
                    }
                }
                $startflag = 0;
                if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                    $currenttime = time();
                    $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                    if ($currenttime >= $starttime) {
                        $startflag = 1;
                    }
                }
                $appointment->startflag = $startflag;
                return $appointment;
            });
        
            // Return the appointments with doctor, treatment, clinic, and user profile details
            return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
        }

        public function BookingPendingDateFilter11(Request $request)
        {
            $userId = auth()->user();
        
            // Fetch the user(s) where either the id or parent_id matches the $userId
            if ($userId->parent_id == 0) {
                $users = User::where(function ($query) use ($userId) {
                    $query->where('id', $userId->id)
                        ->orWhere('parent_id', $userId->id);
                })
                ->get(); // This will return a collection of users
            } elseif ($userId->parent_id > 0) {
                $users = User::where('id', $userId->id)->first(); // This will return a single user
            }
        
            // Check if dentist date is provided in the request
            $dentistdate = $request->date;
            $dentistId = $request->dentist_id;
        
            // Ensure the date format is normalized (assuming format 'YYYY-MM-DD')
            if ($dentistdate) {
                // Convert to the same format as the schedule_date column (YYYY-MM-DD)
                $dentistdate = \Carbon\Carbon::parse($dentistdate)->format('Y-m-d');
            }
        
            // Fetch appointments for the users
            if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
                // Multiple users (using whereIn)
                $query = Booking::whereIn('doctor_id', $users->pluck('id'))
                                ->where('status', 4);
        
                // Add condition for dentist_id if it's provided
                if ($dentistId) {
                    $query->where('doctor_id', $dentistId);
                }
        
                if ($dentistdate != 0) {
                    // If dentist_date is provided, filter appointments by schedule_date
                    $query->whereDate('schedule_date', $dentistdate); // Use whereDate to ignore time
                }
        
                $appointments = $query->orderBy('schedule_date', 'desc')->get();
            } else {
                // Single user (using where)
                $query = Booking::where('doctor_id', $users->id)
                                ->where('status', 4);
        
                // Add condition for dentist_id if it's provided
                if ($dentistId) {
                    $query->where('doctor_id', $dentistId);
                }
        
                if ($dentistdate != 0) {
                    // If dentist_date is provided, filter appointments by schedule_date
                    $query->whereDate('schedule_date', $dentistdate); // Use whereDate to ignore time
                }
        
                $appointments = $query->orderBy('schedule_date', 'desc')->get();
            }
        
            // Map appointments and include doctor, treatment, clinic, and user profile details
            $appointmentsWithDoctor = $appointments->map(function ($appointment) {
                // Get the doctor associated with this appointment
                $doctor = User::find($appointment->doctor_id);
        
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
        
                    // Get the doctor's specialisation IDs from UserInformation
                    $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        
                    if ($doctorprofile && $doctorprofile->specialisations) {
                        $specialisationIds = explode('|', $doctorprofile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
        
                        // Adding doctor and specialisation names to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // If no specialisation exists
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = [];
                    }
        
                    // Fetch the treatment name and add it directly to the appointment object
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'average_duration'])
                        ->first(); // Using first() to get a single result
        
                    // Add treatment details to appointment
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        // If no treatment found, set both to null
                        $appointment->treatment_name = null;
                        $appointment->average_duration = null;
                    }
        
                    // Fetch the clinic information
                    $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
                    if ($clinic) {
                        $appointment->clinic = $clinic; // Add clinic details
        
                        // Fetch the UserProfile details associated with the clinic
                        $userProfile = UserProfile::where('id', $appointment->user_id)->first();
                        if ($userProfile) {
                            $appointment->user = $userProfile; // Move UserProfile outside the clinic object
                        } else {
                            $appointment->user = null; // If no UserProfile found, set to null
                        }
                    } else {
                        $appointment->clinic = null; // If no clinic found, return null
                    }
        
                    // Fetch member details if user_id and member_id are different
                    if ($appointment->user_id != $appointment->member_id) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            $appointment->member = $member; // Add member details to the appointment

                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        // if (!empty($member->image)) {
                        //     $member->image = asset('project/public/member_images/') . '/' . $member->image;
                        // } else {
                          
                        // }
                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->member_id)->where('member_type',$appointment->member_type)->where('type',"xray")->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->member_id)->where('member_type',$appointment->member_type)->where('type',"lab")->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->member_id)->where('member_type',$appointment->member_type)->where('type',"prescription")->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('member_type',$appointment->member_type)->where('type',"xray")->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('member_type',$appointment->member_type)->where('type',"lab")->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('member_type',$appointment->member_type)->where('type',"prescription")->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });

                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null

                    }
                }
        
                $startflag = 0;
                if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                    $currenttime = time();
                    $starttime = strtotime(date('Y-m-d H:i:s', strtotime($appointment->schedule_date . ' ' . $appointment->schedule_time)));
                    if ($currenttime >= $starttime) {
                        $startflag = 1;
                    }
                }
                $appointment->startflag = $startflag;
                return $appointment;
            });
        
            // Return the appointments with doctor, treatment, clinic, and user profile details
            return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
        }

        public function BookingPendingDateFilter(Request $request)
        {
            $userId = auth()->user();
        
            // Fetch the user(s) where either the id or parent_id matches the $userId
            if ($userId->parent_id == 0) {
                $users = User::where(function ($query) use ($userId) {
                    $query->where('id', $userId->id)
                        ->orWhere('parent_id', $userId->id);
                })
                ->get(); // This will return a collection of users
            } elseif ($userId->parent_id > 0) {
                $users = User::where('id', $userId->id)->first(); // This will return a single user
            }
        
            // Check if dentist date is provided in the request
            $dentistdate = $request->date;
            $dentistId = $request->dentist_id;
        
            // Ensure the date format is normalized (assuming format 'YYYY-MM-DD')
            if ($dentistdate) {
                // Convert to the same format as the schedule_date column (YYYY-MM-DD)
                $dentistdate = \Carbon\Carbon::parse($dentistdate)->format('Y-m-d');
            }
        
            // Fetch appointments for the users
            if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
                // Multiple users (using whereIn)
                $query = Booking::whereIn('doctor_id', $users->pluck('id'))
                                ->where('status', 4);
        
                // Add condition for dentist_id if it's provided
                if ($dentistId) {
                    $query->where('doctor_id', $dentistId);
                }
        
                if ($dentistdate != 0) {
                    // If dentist_date is provided, filter appointments by schedule_date
                    $query->whereDate('schedule_date', $dentistdate); // Use whereDate to ignore time
                }
        
                $appointments = $query->orderBy('schedule_date', 'desc')->get();
            } else {
                // Single user (using where)
                $query = Booking::where('doctor_id', $users->id)
                                ->where('status', 4);
        
                // Add condition for dentist_id if it's provided
                if ($dentistId) {
                    $query->where('doctor_id', $dentistId);
                }
        
                if ($dentistdate != 0) {
                    // If dentist_date is provided, filter appointments by schedule_date
                    $query->whereDate('schedule_date', $dentistdate); // Use whereDate to ignore time
                }
        
                $appointments = $query->orderBy('schedule_date', 'desc')->get();
            }
        
            // Map appointments and include doctor, treatment, clinic, and user profile details
            $appointmentsWithDoctor = $appointments->map(function ($appointment) {
                // Get the doctor associated with this appointment
                $doctor = User::find($appointment->doctor_id);
        
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
        
                    // Get the doctor's specialisation IDs from UserInformation
                    $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        
                    if ($doctorprofile && $doctorprofile->specialisations) {
                        $specialisationIds = explode('|', $doctorprofile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
        
                        // Adding doctor and specialisation names to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // If no specialisation exists
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = [];
                    }
        
                    // Fetch the treatment name and add it directly to the appointment object
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'average_duration'])
                        ->first(); // Using first() to get a single result
        
                    // Add treatment details to appointment
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        // If no treatment found, set both to null
                        $appointment->treatment_name = null;
                        $appointment->average_duration = null;
                    }
        
                    // Fetch the clinic information
                    $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
                    if ($clinic) {
                        $appointment->clinic = $clinic; // Add clinic details
        
                        // Fetch the UserProfile details associated with the clinic
                        $userProfile = UserProfile::where('id', $appointment->user_id)->first();
                        if ($userProfile) {
                            // Add full URL for the user's image
                            $userProfile->image = asset('content/user/' . $userProfile->image);
                            $appointment->user = $userProfile; // Move UserProfile outside the clinic object
                        } else {
                            $appointment->user = null; // If no UserProfile found, set to null
                        }
                    } else {
                        $appointment->clinic = null; // If no clinic found, return null
                    }
        
                    // Fetch member details if user_id and member_id are different
                    if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                    
                        // Map the medical records images to URLs (make sure it's an array)
                        // $userMedical_xray->each(function ($record) {
                        //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        // });
                        // $userMedical_lab->each(function ($record) {
                        //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        // });
                        // $userMedical_prescription->each(function ($record) {
                        //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        // });
                        $setImagePath = function ($record) {
                            if ($record->image) {
                                $record->image = asset('storage/prescriptions/' . $record->image);
                            } else {
                                $record->image = asset('project/storage/app/public/prescriptions/' . $record->pdf);
                            }
                        };

                        $userMedical_xray->each($setImagePath);
                        $userMedical_lab->each($setImagePath);
                        $userMedical_prescription->each($setImagePath);
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', 0)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', 0)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', 0)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        // $userMedical_xray->each(function ($record) {
                        //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        // });
                        // $userMedical_lab->each(function ($record) {
                        //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        // });
                        // $userMedical_prescription->each(function ($record) {
                        //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        // });
                        $setImagePath = function ($record) {
                            if ($record->image) {
                                $record->image = asset('storage/prescriptions/' . $record->image);
                            } else {
                                $record->image = asset('project/storage/app/public/prescriptions/' . $record->pdf);
                            }
                        };

                        $userMedical_xray->each($setImagePath);
                        $userMedical_lab->each($setImagePath);
                        $userMedical_prescription->each($setImagePath);
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }
                }
        
                $startflag = 0;
                if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                    $currenttime = time();
                    $starttime = strtotime(date('Y-m-d H:i:s', strtotime($appointment->schedule_date . ' ' . $appointment->schedule_time)));
                    if ($currenttime >= $starttime) {
                        $startflag = 1;
                    }
                }
                $appointment->startflag = $startflag;
                return $appointment;
            });
        
            // Return the appointments with doctor, treatment, clinic, and user profile details
            return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
        }

        public function BookingPendingList(Request $request)
        {
            $userId = auth()->user();
    
            // Fetch the user(s) where either the `id` or `parent_id` matches the $userId
            if ($userId->parent_id == 0) {
                $users = User::where(function ($query) use ($userId) {
                    $query->where('id', $userId->id)
                        ->orWhere('parent_id', $userId->id);
                })
                ->get(); // This will return a collection of users
            } elseif ($userId->parent_id > 0) {
                $users = User::where('id', $userId->id)->first(); // This will return a single user
            }
    
            // Check if dentist_id is provided in the request
            $dentistId = $request->dentist_id; // Default to 0 if not provided
    
            // Fetch appointments for the users
            if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
                // Multiple users (using whereIn)
                if ($dentistId == 0) {
                    // If dentist_id is 0, return appointments for all users
                    $appointments = Booking::whereIn('doctor_id', $users->pluck('id'))
                        ->whereIn('status', [0])
                        ->orderBy('schedule_date', 'desc')
                        ->get();
                } else {
                    // If dentist_id is provided, filter appointments by dentist_id
                    $appointments = Booking::whereIn('doctor_id', $users->pluck('id'))
                        ->where('doctor_id', $dentistId)
                        ->whereIn('status', [0])
                        ->orderBy('schedule_date', 'desc')
                        ->get();
                }
            } else {
                // Single user (using where)
                if ($dentistId == 0) {
                    // If dentist_id is 0, return appointments for the single user
                    $appointments = Booking::where('doctor_id', $users->id)
                        ->whereIn('status', [0])
                        ->orderBy('schedule_date', 'desc')
                        ->get();
                } else {
                    // If dentist_id is provided, filter appointments by dentist_id
                    $appointments = Booking::where('doctor_id', $users->id)
                        ->where('doctor_id', $dentistId)
                        ->whereIn('status', [0])
                        ->orderBy('schedule_date', 'desc')
                        ->get();
                }
            }
    
            // Map appointments and include doctor and treatment details
            $appointmentsWithDoctor = $appointments->map(function ($appointment) use ($userId) {
                // Fetch all medical records for the user associated with this appointment
                // $userMedical_xray = MedicalRecord::where('member_id', $appointment->member_id)->where('member_type',$appointment->member_type)->where('type',"xray")->get(); // Get all records
                // $userMedical_lab = MedicalRecord::where('member_id', $appointment->member_id)->where('member_type',$appointment->member_type)->where('type',"lab")->get(); // Get all records
                // $userMedical_prescription = MedicalRecord::where('member_id', $appointment->member_id)->where('member_type',$appointment->member_type)->where('type',"prescription")->get(); // Get all records
            
                // // Map the medical records images to URLs (make sure it's an array)
                // $userMedical_xray->each(function ($record) {
                //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                // });
                // $userMedical_lab->each(function ($record) {
                //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                // });
                // $userMedical_prescription->each(function ($record) {
                //     $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                // });


                  if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            if (!empty($record->image)) {
                                $record->image = asset('project/public/medical_records/' . $record->image);
                            } elseif (!empty($record->pdf)) {
                                $record->image = asset('project/storage/app/public/prescriptions/' . $record->pdf);
                            } else {
                                $record->image = null;
                            }
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {

                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            if (!empty($record->image)) {
                                $record->image = asset('project/public/medical_records/' . $record->image);
                            } elseif (!empty($record->pdf)) {
                                $record->image = asset('project/storage/app/public/prescriptions/' . $record->pdf);
                            } else {
                                $record->image = null;
                            }
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }
            
                // Fetch user profile for the current appointment user
                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('content/user/' . $userProfile->image);
                }
            
                // Fetch the doctor details associated with this appointment
                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
            
                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
            
                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }
            
                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();
            
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }
    
    
                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;


                  

                }
            
                return $appointment;
            });
            
            // Return the appointments with doctor and treatment details
            return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);    
    
        }


        public function BookingAcceptList(Request $request)
        {
            $userId = auth()->user();
            
            // Fetch the user(s) where either the id or parent_id matches the $userId
            if ($userId->parent_id == 0) {
                $users = User::where(function ($query) use ($userId) {
                    $query->where('id', $userId->id)
                        ->orWhere('parent_id', $userId->id);
                })
                ->get(); // This will return a collection of users
            } elseif ($userId->parent_id > 0) {
                $users = User::where('id', $userId->id)->first(); // This will return a single user
            } else $users = User::where('id', $userId)->first();
            
            $dentistId = $request->dentist_id;
            if ($dentistId == 0) {
                $query = Booking::whereIN('doctor_id', $users->pluck('id'))
                                ->where('status', 4);
            } else {
                $query = Booking::where('doctor_id', $dentistId)
                                ->where('status', 4);
            }
            
            // Add condition for dentist_id if it's provided
            if ($dentistId) {
                $query->where('doctor_id', $dentistId);
            }
    
            $appointments = $query->orderBy('schedule_date', 'desc')
            ->orderByRaw("STR_TO_DATE(schedule_time, '%h:%i %p') desc")->get();
        
            // Map appointments and include doctor, treatment, clinic, and user profile details
            $appointmentsWithDoctor = $appointments->map(function ($appointment) {
                // Get the doctor associated with this appointment
                $doctor = User::find($appointment->doctor_id);
        
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
        
                    // Get the doctor's specialisation IDs from UserInformation
                    $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        
                    if ($doctorprofile && $doctorprofile->specialisations) {
                        $specialisationIds = explode('|', $doctorprofile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
        
                        // Adding doctor and specialisation names to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // If no specialisation exists
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = [];
                    }
        
                    // Fetch the treatment name and add it directly to the appointment object
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'average_duration'])
                        ->first(); // Using first() to get a single result
        
                    // Add treatment details to appointment
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        // If no treatment found, set both to null
                        $appointment->treatment_name = null;
                        $appointment->average_duration = null;
                    }
        
                    // Fetch the clinic information
                    $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
                    if ($clinic) {
                        $appointment->clinic = $clinic; // Add clinic details
        
                        // Fetch the UserProfile details associated with the clinic
                        $userProfile = UserProfile::where('id', $appointment->user_id)->first();
                        if ($userProfile) {
                            $appointment->user = $userProfile; // Move UserProfile outside the clinic object
                        } else {
                            $appointment->user = null; // If no UserProfile found, set to null
                        }
                    } else {
                        $appointment->clinic = null; // If no clinic found, return null
                    }

                    if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', 0)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', 0)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', 0)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }
                }
                $startflag = 0;
                if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                    $currenttime = time();
                    $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                    if ($currenttime >= $starttime) {
                        $startflag = 1;
                    }
                }
                $appointment->startflag = $startflag;
                return $appointment;
            });
        
            // Return the appointments with doctor, treatment, clinic, and user profile details
            return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
        }
        
        
        
        
        


    public function DoctorCancelReason(Request $request)
    {
        $a = Booking::find($request->booking_id);
        $a->cancel_by = $request->cancel_by ?? 'doctor';
        $a->cancel_other = $request->cancel_other;
        $a->cancel_date = date('Y-m-d h:ia');
        $a->status = 2;
        $a->save();
        if ($a->loyality_points > 0) {
            $get = UserPayment::where('booking_id',$a->id)->where('type',2)->first();
            if ($get) {
                $u = UserProfile::where('id',$a->user_id)->first();
                $update_wallet =  $u->wallet+$get->amount;
                $new = new UserPayment();
                $new->user_id = $u->id;
                $new->type = 2;
                $new->booking_id = $a->id;
                $new->action = 'credit';
                $new->amount = $get->amount;
                $new->old_balance = $u->wallet;
                $new->payment_status = 'completed';
                $new->payment_method = 'online';
                $new->new_balance = $update_wallet;
                $new->status = 1;
                $new->trxn_id =  time().rand();
                $new->save();
                $u->wallet = $update_wallet;
                $u->save();
            }
        }
        $booking = $a;
        if(!empty($booking->payid)) {
            $scheduleDate = $a->schedule_date;
            $scheduleTime = $a->schedule_time;
            $scheduledDateTimeStr = $scheduleDate . ' ' . $scheduleTime;
            $scheduledDateTime = strtotime($scheduledDateTimeStr);
            $now = time();
            $diffInSeconds = $scheduledDateTime - $now;
            $hoursUntilBooking = $diffInSeconds / 3600;
            if ($hoursUntilBooking > 24) { 
                $key_id = env('RAZORPAY_KEYID_TEST');
                $key_secret = env('RAZORPAY_SECRET_TEST');

                $originalPayId = $booking->payid;
                $paymentId = null;

                if (str_starts_with($originalPayId, 'pay_')) {
                    $paymentId = $originalPayId;
                } elseif (str_starts_with($originalPayId, 'order_')) {
                    $orderResponse = Http::withBasicAuth($key_id, $key_secret)
                        ->get("https://api.razorpay.com/v1/orders/{$originalPayId}/payments");

                    if ($orderResponse->successful() && isset($orderResponse['items'][0]['id'])) {
                        $paymentId = $orderResponse['items'][0]['id'];
                    }
                }
                $refundUrl = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";
                if ($booking->is_paid == 2) {
                   $amountInPaise = (int) ($booking->partial_amount * 100);
                } else
                $amountInPaise = (int) ($booking->total_amount * 100);
                $refundPayload = [];

                if ($amountInPaise > 0) {
                    $refundPayload['amount'] = $amountInPaise;
                }
                try {
                    $refundResponse = empty($refundPayload)
                        ? Http::withBasicAuth($key_id, $key_secret)->post($refundUrl)
                        : Http::withBasicAuth($key_id, $key_secret)->post($refundUrl, $refundPayload);

                    if ($refundResponse->successful()) {
                        $a->refund_details = $refundResponse->json();
                        $a->refund_status = 1;
                        $a->save();
                    } else {
                        $a->refund_details = $refundResponse->json();
                        $a->refund_status = 2;
                        $a->save();
                    }
                } catch (\Exception $e) {
                    $a->refund_details = $e;
                    $a->refund_status = 3;
                    $a->save();
                }
            }
        }
        return response()->json(['status' => true,'message' => 'Booking Cancel successfully', 'reason' => $a], 201);
    }



    public function DoctorRescheduleBooking(Request $request)
        {
            $validated = $request->validate([
                'clinic_id' => 'required',
                'schedule_date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required'
            ]);

            $a = Booking::find($request->booking_id);
            $a->clinic_id = $request->clinic_id;
            $a->schedule_date = $request->schedule_date;
            $a->schedule_time = $request->start_time;
            $a->start_time = $request->start_time;
            $a->end_time = $request->end_time;
            $a->save();

            return response()->json(['status' => true,'message' => 'Booking Reschedule successfully', 'rescheduleBooking' => $a], 201);
        }


        public function cancel_list(Request $request) {
            // echo "ccddcdc"; die;
            $l = Cancel::where('status',1)->orderBy('id','ASC')->get();
            if(!empty($l)) {
                $response = ['status' => true, 'msg' => 'cancel List', 'data' => $l];
                return response($response, 200);
            } else {
                $response = ['status' => false, 'msg' => 'Sorry ! No cancel found.'];
                return response($response, 422);
            }
        }


        public function DoctorclinicList(Request $request)
        {
            // Get the input from the request
            $input = $request->all();
            
            // Validate the doctor_id
            $validator = Validator::make($input, [
                'doctor_id' => 'required',
            ]);
            
            // If validation fails, return the error message
            if ($validator->fails()) {
                return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
            }
            $canaddchild = 0;
            $dcotordetail = User::where('id',$request->doctor_id)->first();
            // If doctor_id is 0, decrypt the clinic ID from a predefined string
            if ($request->doctor_id == 0) {
                // Decrypt the clinic ID
                $decrypted = Crypt::decryptString('eyJpdiI6Ijh2Qy9mUUhlY3lrcmpuazZNa3VkeFE9PSIsInZhbHVlIjoiVEMya3lUWk1xWEpDT2l2MXJpOGhIQT09IiwibWFjIjoiNjI3MzQ3NDMyYjgyZDA4MDM2OTE5YTEzNThmN2M3ZTgzMGQ4NTEzMDYzMDIwNjhkODU3OTdmZmI4YmFjMGVhNyIsInRhZyI6IiJ9');
                $clinicId = unserialize($decrypted);
        
                // Fetch clinics by the decrypted clinic ID
                $result = UserEstablishmentClinic::where('user_id', $clinicId)->where('status',1)
                    ->orderBy('id', 'ASC')
                    ->get()
                    ->map(function ($item) {
                        $item->image = asset('content/doctor/clinic/' . $item->image);
                        return $item;
                    });
            } else {
                // If doctor_id is not 0, fetch clinics for the provided doctor_id
                if ($dcotordetail->parent_id > 0) {
                    $result = UserEstablishmentClinic::where([
                        ['user_id', $dcotordetail->parent_id],
                        ['status', 1]  // Ensure that the clinic is active
                    ])
                        ->orderBy('id', 'ASC')
                        ->get()
                        ->map(function ($item) {
                            $item->image = asset('content/doctor/clinic/' . $item->image);
                            return $item;
                        });
                } else {
                    $result = UserEstablishmentClinic::where([
                        ['user_id', $request->doctor_id],
                        ['status', 1]  // Ensure that the clinic is active
                    ])
                        ->orderBy('id', 'ASC')
                        ->get()
                        ->map(function ($item) {
                            $item->image = asset('content/doctor/clinic/' . $item->image);
                            return $item;
                        });
                }
                
                $totalquantity = $this->getquantity(13,$request->doctor_id);
                if ($totalquantity > 0) {
                   $canaddchild = $totalquantity-count($result);
                }
            }
        
            // Check if no results were found
            if ($result->isEmpty()) {
                return response(['status' => false, 'msg' => 'Sorry! Not found.','canaddchild'=>$canaddchild], 200);
            }
        
            // Return the list of clinics
            return response(['status' => true, 'msg' => 'List', 'data' => $result], 200);
        }



    public function doctorappointmenttimeslotold(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'doctor_id' => 'required',
            'clinic_id' => 'required',
            'total_minutes' => 'required',
            'apointment_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        $response = ['status' => false, 'message' => 'no slots found!'];
        
        if ($request->doctor_id < 0) {
            $key1 = UserInformation::where('user_id', $request->doctor_id)
                ->first();
            if ($key1) {
                $dateslots = array();
                $slot = array(); 
                $apointment_date = $request->apointment_date;

                $today_date = strtotime(date('Y-m-d'));
                $given_date = strtotime($apointment_date);
                $id = $key1->user_id;
                if ($given_date >= $today_date) {
                    $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                    // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                    $already_in_time_check = array();
                    // if (count($check_bt_) > 0) {
                    //     foreach ($check_bt_ as $keybt) {
                    //         $time_bt = $keybt->schedule_time;
                    //         $minutes = $keybt->total_minutes;
                            
                    //         $one_Cal_time = strtotime($time_bt);
                    //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));
                            
                    //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                    //         array_push($already_in_time_check, $array_inn_ol);
                    //     }
                    // }
                    $working_time = json_decode($key1->doctor_availability, true);
                    if (!empty($working_time)) {
                        $working_time = json_decode($key1->doctor_availability, true);
                        $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                        if (!empty($schedule)) {
                            if (isset($schedule['morning'])) {
                                $fistslots = explode(' - ', $schedule['morning'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        }  else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                            if (isset($schedule['evening'])) {
                                $fistslots = explode(' - ', $schedule['evening'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        } else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($slot)) {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
                } else {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
                }
                if (!empty($dateslots)) {
                    $response = ['status' => true, 'dateslots' => $dateslots];
                }
            }
        } else {
            $key1 = DoctorClinicSlot::where('doctor_id', $request->doctor_id)->where('clinic_id', $request->clinic_id)->where('status',1)
                ->first();
            if (!$key1) {
                $check = UserInformation::where('user_id',$request->doctor_id)->first();
                $new = new DoctorClinicSlot();
                $new->doctor_id = $request->doctor_id;
                $new->clinic_id = $request->clinic_id;
                $new->doctor_availability = $check->doctor_availability ?? '';
                $new->status = 1;
                $new->save();
                $key1 = DoctorClinicSlot::where('doctor_id', $request->doctor_id)->where('clinic_id', $request->clinic_id)->where('status',1)
                ->first();
            }
            // $key1 = UserEstablishmentClinic::where('id', $request->clinic_id)
            //     ->first();
            if ($key1) {
                $dateslots = array();
                $slot = array(); 
                $apointment_date = $request->apointment_date;

                $today_date = strtotime(date('Y-m-d'));
                $given_date = strtotime($apointment_date);
                $id = $key1->user_id;
                if ($given_date >= $today_date) {
                    $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                    // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                    $already_in_time_check = array();
                    // if (count($check_bt_) > 0) {
                    //     foreach ($check_bt_ as $keybt) {
                    //         $time_bt = $keybt->schedule_time;
                    //         $minutes = $keybt->total_minutes;
                            
                    //         $one_Cal_time = strtotime($time_bt);
                    //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));
                            
                    //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                    //         array_push($already_in_time_check, $array_inn_ol);
                    //     }
                    // }
                    $working_time = json_decode($key1->timings, true);
                    if (!empty($working_time)) {
                        $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                        if (!empty($schedule)) {
                            if (isset($schedule['morning'])) {
                                $fistslots = explode(' - ', $schedule['morning'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    if (strpos($fistslots[0], ':a') !== false) {
                                        $fistslots[0] = str_replace(':a', ' a', $fistslots[0]);
                                    } elseif (strpos($fistslots[0], ':p') !== false) {
                                        $fistslots[0] = str_replace(':p', ' p', $fistslots[0]);
                                    }

                                    if (strpos($fistslots[1], ':a') !== false) {
                                        $fistslots[1] = str_replace(':a', ' a', $fistslots[1]);
                                    } elseif (strpos($fistslots[1], ':p') !== false) {
                                        $fistslots[1] = str_replace(':p', ' p', $fistslots[1]);
                                    }
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        }  else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                            if (isset($schedule['evening'])) {
                                $fistslots = explode(' - ', $schedule['evening'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    if (strpos($fistslots[0], ':a') !== false) {
                                        $fistslots[0] = str_replace(':a', ' a', $fistslots[0]);
                                    } elseif (strpos($fistslots[0], ':p') !== false) {
                                        $fistslots[0] = str_replace(':p', ' p', $fistslots[0]);
                                    }

                                    if (strpos($fistslots[1], ':a') !== false) {
                                        $fistslots[1] = str_replace(':a', ' a', $fistslots[1]);
                                    } elseif (strpos($fistslots[1], ':p') !== false) {
                                        $fistslots[1] = str_replace(':p', ' p', $fistslots[1]);
                                    }

                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        } else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($slot)) {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
                } else {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
                }
                if (!empty($dateslots)) {
                    $response = ['status' => true, 'dateslots' => $dateslots];
                }
            }
        }
        
        return response($response, 200);
    }

    public function doctorappointmenttimeslot(Request $request)
    {

        $input = $request->all();
        // $file = time() . rand() . '_file.json';
        // $destinationPath = "project/checkNOTIlogs/";
        // if (!is_dir($destinationPath)) {
        //     mkdir($destinationPath, 0777, true);
        // }
        // File::put($destinationPath . $file, json_encode($input));
        $validator = Validator::make($input, [
            'doctor_id' => 'required',
            'clinic_id' => 'required',
            'total_minutes' => 'required',
            'apointment_date' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        $response = ['status' => false, 'message' => 'no slots found!'];
        
        if ($request->doctor_id < 0) {
            $key1 = UserInformation::where('user_id', $request->doctor_id)
                ->first();
            if ($key1) {
                $dateslots = array();
                $slot = array(); 
                $apointment_date = $request->apointment_date;

                $today_date = strtotime(date('Y-m-d'));
                $given_date = strtotime($apointment_date);
                $id = $key1->user_id;
                if ($given_date >= $today_date) {
                    $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                    // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                    $already_in_time_check = array();
                    // if (count($check_bt_) > 0) {
                    //     foreach ($check_bt_ as $keybt) {
                    //         $time_bt = $keybt->schedule_time;
                    //         $minutes = $keybt->total_minutes;
                            
                    //         $one_Cal_time = strtotime($time_bt);
                    //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));
                            
                    //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                    //         array_push($already_in_time_check, $array_inn_ol);
                    //     }
                    // }
                    $working_time = json_decode($key1->doctor_availability, true);
                    if (!empty($working_time)) {
                        $working_time = json_decode($key1->doctor_availability, true);
                        $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                        if (!empty($schedule)) {
                            if (isset($schedule['morning'])) {
                                $fistslots = explode(' - ', $schedule['morning'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $slotEndTime = strtotime($apointment_date . ' ' . $end);
                                        $maxSlotEnd = strtotime($apointment_date . ' ' . $fistslots[1]);
                                        if ($slotEndTime > $maxSlotEnd) {
                                            continue;
                                        }
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        }  else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                            if (isset($schedule['evening'])) {
                                $fistslots = explode(' - ', $schedule['evening'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $slotEndTime = strtotime($apointment_date . ' ' . $end);
                                        $maxSlotEnd = strtotime($apointment_date . ' ' . $fistslots[1]);
                                        if ($slotEndTime > $maxSlotEnd) {

                                            continue;
                                        }
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        } else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $check = Booking::whereIN('status',['0','1','2','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($slot)) {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
                } else {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
                }
                if (!empty($dateslots)) {
                    $response = ['status' => true, 'dateslots' => $dateslots];
                }
            }
        } else {
            $key1 = DoctorClinicSlot::where('doctor_id', $request->doctor_id)->where('clinic_id', $request->clinic_id)->where('status',1)
                ->first();
            if (!$key1) {
                $check = UserInformation::where('user_id',$request->doctor_id)->first();
                $new = new DoctorClinicSlot();
                $new->doctor_id = $request->doctor_id;
                $new->clinic_id = $request->clinic_id;
                $new->doctor_availability = $check->doctor_availability ?? '';
                $new->status = 1;
                $new->save();
                $key1 = DoctorClinicSlot::where('doctor_id', $request->doctor_id)->where('clinic_id', $request->clinic_id)->where('status',1)
                ->first();
            }
            // $key1 = UserEstablishmentClinic::where('id', $request->clinic_id)
            //     ->first();
            if ($key1) {
                $dateslots = array();
                $slot = array(); 
                $apointment_date = $request->apointment_date;

                $today_date = strtotime(date('Y-m-d'));
                $given_date = strtotime($apointment_date);
                $id = $key1->user_id;
                $checkholiday = UserEstablishmentHoliday::/*where('date',$apointment_date)*/whereDate('date', '<=', $apointment_date)->whereDate('end_date', '>=', $apointment_date)->where('establishment_clinics_id',$request->clinic_id)->where('status',1)->first();
                if (!$checkholiday) {
                    if ($given_date >= $today_date) {
                        $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                        // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                        $already_in_time_check = array();
                        // if (count($check_bt_) > 0) {
                        //     foreach ($check_bt_ as $keybt) {
                        //         $time_bt = $keybt->schedule_time;
                        //         $minutes = $keybt->total_minutes;
                                
                        //         $one_Cal_time = strtotime($time_bt);
                        //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));
                                
                        //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                        //         array_push($already_in_time_check, $array_inn_ol);
                        //     }
                        // }
                        $working_time = json_decode($key1->doctor_availability, true);
                        if (!empty($working_time)) {
                            $working_time = json_decode($key1->doctor_availability, true);
                            $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                            if (!empty($schedule)) {
                                if (isset($schedule['morning'])) {
                                    $fistslots = explode(' - ', $schedule['morning'][0] ?? '');
                                    if (isset($fistslots[0]) && isset($fistslots[1])) {
                                        $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                        $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                        $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                        foreach ($period as $dt) {
                                            $start = $dt->format('h:ia');
                                            $end = $dt->add($interval)->format('h:ia');
                                            $slotEndTime = strtotime($apointment_date . ' ' . $end);
                                            $maxSlotEnd = strtotime($apointment_date . ' ' . $fistslots[1]);
                                            if ($slotEndTime > $maxSlotEnd) {
                                                continue;
                                            }
                                            $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                            
                                            if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                                $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                                $slotEnd = Carbon::parse($end)->format('H:i'); 
                                                $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                    ->where('doctor_id', $request->doctor_id)
                                                    ->where('schedule_date', $apointment_date)
                                                    ->where(function ($q) use ($slotStart, $slotEnd) {
                                                        $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                          ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                    })
                                                    ->first();
                                                $is_book = $check ? 1 : 0;
                                                array_push($slot, [
                                                    "start_time" => $time_derive,
                                                    "end_time" => $end,
                                                    "is_book" => $is_book,
                                                    "timepass"=>0
                                                ]);
                                            }  else {
                                                $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                                $slotEnd = Carbon::parse($end)->format('H:i'); 
                                                $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                    ->where('schedule_date', $apointment_date)
                                                    ->where(function ($q) use ($slotStart, $slotEnd) {
                                                        $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                          ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                    })
                                                    ->first();
                                                $is_book = $check ? 1 : 0;
                                                array_push($slot, [
                                                    "start_time" => $time_derive,
                                                    "end_time" => $end,
                                                    "is_book" => $is_book,
                                                    "timepass"=>1
                                                ]);
                                            }
                                        }
                                    }
                                }
                                if (isset($schedule['evening'])) {
                                    $fistslots = explode(' - ', $schedule['evening'][0] ?? '');
                                    if (isset($fistslots[0]) && isset($fistslots[1])) {
                                        $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                        $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                        $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                        foreach ($period as $dt) {
                                            $start = $dt->format('h:ia');
                                            $end = $dt->add($interval)->format('h:ia');
                                            $slotEndTime = strtotime($apointment_date . ' ' . $end);
                                            $maxSlotEnd = strtotime($apointment_date . ' ' . $fistslots[1]);
                                            if ($slotEndTime > $maxSlotEnd) {
                                                continue;
                                            }
                                            $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                            if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                                $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                                $slotEnd = Carbon::parse($end)->format('H:i'); 
                                                $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                    ->where('doctor_id', $request->doctor_id)
                                                    ->where('schedule_date', $apointment_date)
                                                    ->where(function ($q) use ($slotStart, $slotEnd) {
                                                        $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                          ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                    })
                                                    ->first();
                                                $is_book = $check ? 1 : 0;
                                                array_push($slot, [
                                                    "start_time" => $time_derive,
                                                    "end_time" => $end,
                                                    "is_book" => $is_book,
                                                    "timepass"=>0
                                                ]);
                                            } else {
                                                $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                                $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                                $slotEnd = Carbon::parse($end)->format('H:i'); 
                                                $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                     ->where('doctor_id', $request->doctor_id)
                                                    ->where('schedule_date', $apointment_date)
                                                    ->where(function ($q) use ($slotStart, $slotEnd) {
                                                        $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                          ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                    })
                                                    ->first();
                                                $is_book = $check ? 1 : 0;
                                                array_push($slot, [
                                                    "start_time" => $time_derive,
                                                    "end_time" => $end,
                                                    "is_book" => $is_book,
                                                    "timepass"=>1
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (!empty($slot)) {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
                } else {
                    array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
                }
                if (!empty($dateslots)) {
                    $response = ['status' => true, 'dateslots' => $dateslots];
                }
            }
        }
        
        return response($response, 200);
    }


    public function addPrescription(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'booking_id' => 'required|string',
            'notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'advice' => 'nullable|string',
            'prescription' => 'nullable|array',
        ]);

        // Create the booking and save it to the database
        $booking = Prescription::create($request->all());
        $book = Booking::find($booking->booking_id);
        $doctor = User::find($book->doctor_id);
        $clinic = UserEstablishmentClinic::where('id', $book->clinic_id)->first();

        $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        $specialisationIds = explode('|', $doctorprofile->specialisations);
            $specialisationNames = \DB::table('master_specialsations')
                ->whereIn('id', $specialisationIds)
                ->pluck('name')
                ->toArray();
        $firstSpecialisation = $specialisationNames[0] ?? '';
        $user = UserProfile::find($book->user_id);
        $name = $user->name.' '.$user->last_name;
        $gender = $user->gender;
        if ($book->member_id > 0) {
            $name = ($book->memberdetail->name ?? $user->name) . ' ' . ($book->memberdetail->last_name ?? $user->last_name);
            $gender = $book->memberdetail->gender ?? $user->gender;
        }
        $data = [
            'dr_name' => $doctor->name,
            'dr_email' => $doctor->email,
            'dr_mobile' => $doctor->mobile,
            'user_name' => $name,
            'user_gender' => $gender,
            'dr_spec' => $firstSpecialisation,//implode(', ', $specialisationNames), 
            'clinic_address' => $clinic->address,
            'clinic_register' => $clinic->register_number,
            'booking_id' => $booking->booking_id,
            'booking_created' => $booking->created_at->format('d M Y'),
            'notes' => $booking->notes,
            'diagnosis' => $booking->diagnosis,
            'advice' => $booking->advice,
            'prescription' => $booking->prescription
        ];

        // Load the view and generate the PDF
        $pdf_prs = Pdf::loadView('prescription', $data);

        // Save the PDF to a specific path (e.g., storage/app/public/prescriptions/)
        $pdfFileName_prs = 'prescriptions_' . $booking->booking_id . '.pdf';
        $pdfPath_prs = storage_path('app/public/prescriptions/' . $pdfFileName_prs);

        // Save the PDF file to storage
        $pdf_prs->save($pdfPath_prs);

        // Generate the full URL for the saved PDF file
        $PrsUrl = url('storage/prescriptions/' . $pdfFileName_prs);
        $booking->pdf = $pdfFileName_prs;
        $booking->save();
        $booking->pdf=url('project/storage/app/public/prescriptions/prescriptions_' . $booking->booking_id . '.pdf');
        $booking->image=url('project/storage/app/public/prescriptions/prescriptions_' . $booking->booking_id . '.pdf');
        // Return the booking information and the full PDF URL
        return response()->json([
            'status' => true,
            'prescription_data' => $booking,
            'pdf_url' => url('project/storage/app/public/prescriptions/prescriptions_' . $booking->booking_id . '.pdf')
        ], 201);
    }


    public function updatePrescription(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'advice' => 'nullable|string',
            'prescription' => 'nullable|array',
        ]);

        // Find the existing prescription by booking_id
        $booking = Prescription::where('id', $request->prescription_id)->first();

        $book = Booking::find($booking->booking_id);
            $doctor = User::find($book->doctor_id);
            $clinic = UserEstablishmentClinic::where('id', $book->clinic_id)->first();

            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
            $firstSpecialisation = $specialisationNames[0] ?? '';
            $user = UserProfile::find($book->user_id);

        // If prescription not found, return an error response
        if (!$booking) {
            return response()->json([
                'message' => 'Prescription not found for the given booking_id'
            ], 404);
        }

        // Update the prescription fields with the new data
        $booking->update($request->only(['notes', 'diagnosis', 'advice', 'prescription']));

        // Prepare data for PDF generation
        $data = [
            'dr_name' => $doctor->name,
            'dr_email' => $doctor->email,
            'dr_mobile' => $doctor->mobile,
            'user_name' => $user->name,
            'user_gender' => $user->gender,
            'dr_spec' => $firstSpecialisation,//implode(', ', $specialisationNames), 
            'clinic_address' => $clinic->address,
            'clinic_register' => $clinic->register_number,
            'booking_id' => $booking->booking_id,
            'booking_created' => $booking->created_at,
            'notes' => $booking->notes,
            'diagnosis' => $booking->diagnosis,
            'advice' => $booking->advice,
            'prescription' => $booking->prescription
        ];

        // Load the view and generate the updated PDF
        $pdf = Pdf::loadView('prescription', $data);

        // Save the updated PDF to the same path (or a new path if needed)
        $pdfFileName = 'prescriptions_' . $booking->booking_id . '.pdf';
        $pdfPath = storage_path('app/public/prescriptions/' . $pdfFileName);

        // Save the PDF file to storage
        $pdf->save($pdfPath);

        // Generate the full URL for the saved PDF file
        $pdfUrl = url('storage/prescriptions/' . $pdfFileName);

        // Return the updated booking information and the new PDF URL
        return response()->json([
            'status' => true,
            'prescription_data' => $booking,
            'pdf_url' => url('project/storage/app/public/prescriptions/prescriptions_' . $booking->booking_id . '.pdf')
        ], 200);
    }


    public function prescriptionSend(Request $request) 
    {
        $validated = $request->validate([
            'prescription_id' => 'required'
        ]);

        $a = Prescription::find($request->prescription_id);
        if (!$a) {
            return response()->json(['status' => false, 'message' => 'Prescription not found'], 404);
        }
        
        
        $a->status = 1;
        $a->save();
        $b = Booking::where('id', $a->booking_id)
                    ->first();

        if ($b) {
            $member_type = 'self';
            if ($b->member_type == 'relation') {
                $member_type = 'other';
            }
            $ab = new MedicalRecord();
            $ab->user_id = $b->user_id;
            $ab->member_id = $b->member_id;
            $ab->member_type = $member_type;
            $ab->type = 'prescription';
            $ab->status = 1;
            $ab->notes = $a->notes;
            $ab->diagnosis = $a->diagnosis;
            $ab->advice = $a->advice;
            $ab->prescription = json_encode($a->prescription);
            $ab->pdf = $a->pdf;
            $ab->save();
            $b->status = 1;
            $b->is_paid = 1;
            $b->save();
            if($b->doctordetail->UserInformationDetails->loyalty_points == 1 || $b->doctordetail->UserInformationDetails->loyalty_points == '1') {
                $generalSetting = Generalsetting::select('loyality_program_points_credit')->where('id', 1)->first();
                if ($generalSetting->loyality_program_points_credit > 0) {
                    // $user_data = UserProfile::where("id",$b->user_id)->first();
                    // $user_wallet = $user_data->wallet;
                    // $update_wallet =  $user_wallet+$generalSetting->loyality_program_points_credit;
                    // $new = new UserPayment();
                    // $new->user_id = auth()->user()->id;
                    // $new->booking_id = $b->id;
                    // $new->type = 2;
                    // $new->action = 'credit';
                    // $new->amount = $generalSetting->loyality_program_points_credit;
                    // $new->old_balance = $user_wallet;
                    // $new->payment_status = 'completed';
                    // $new->payment_method = 'online';
                    // $new->new_balance = $update_wallet;
                    // $new->status = 1;
                    // $new->trxn_id =  time().rand();
                    // $new->save();
                    // // dd($new);
                    // $user_data->wallet = $update_wallet;
                    // $user_data->save();
                }
            }
            $bdate = date("l, F j, Y g:i A", strtotime($b->schedule_date.' '.$b->schedule_time));
            $msgarray = array("title"=>"📌Prescription Added",
              "msg"=>"📩 Your appointment with Dr. ".$b->doctordetail->name." at ".$b->clinicdetail->clinic_name." scheduled for ".$bdate.' prescription has been added. We hope you had a great experience!',

             "msg2"=>"You have a new appointment with ".$b->userdetail->name." at ".$b->clinicdetail->clinic_name." on ".$bdate." has been successfully completed."
            );
            $this->async_to_all($msgarray,'',$b->id,'sendprescription');
            // return response()->json(['status' => false, 'message' => 'Booking not found or consultation type mismatch'], 404);
        }

        return response()->json(['status' => true, 'message' => 'Prescription sent successfully', 'data' => $a], 201);
    }


    public function drugName(Request $request) {
        $l = Drug::where('status',1)->orderBy('id','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'Drug List', 'data' => $l];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No drug found.'];
            return response($response, 422);
        }
    }


    public function BookingPatientHistory(Request $request) {
        $userId = auth()->user();
        
        // Fetch the user(s) where either the `id` or `parent_id` matches the $userId
        if ($userId->parent_id == 0) {
            $users = User::where(function ($query) use ($userId) {
                $query->where('id', $userId->id)
                      ->orWhere('parent_id', $userId->id);
            })
            ->get(); // This will return a collection of users
        } elseif ($userId->parent_id > 0) {
            $users = User::where('id', $userId->id)->first(); // This will return a single user
        }
        
        // If $users is a collection, use pluck() to get all doctor ids
        if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
            $doctorIds = $users->pluck('id'); // Get an array of ids
            $appointments = Booking::whereIn('doctor_id', $doctorIds)  // Use whereIn to match any doctor_id in the collection
                ->where('status', 1)  // Only fetch appointments with status = 1
                ->orderBy('schedule_date', 'desc')
                ->get();
        } else {
            // If $users is a single user instance, use its id directly
            $appointments = Booking::where('doctor_id', $users->id)  // Use where for a single user id
                ->where('status', 1)  // Only fetch appointments with status = 1
                ->orderBy('schedule_date', 'desc')
                ->get();
        }
    
        // Get unique user_ids from the appointments collection
        $uniqueUserIds = $appointments->pluck('user_id')->unique(); // Get unique user_ids
        
        // Fetch user profiles for unique user_ids
        $userProfiles = UserProfile::whereIn('id', $uniqueUserIds)->orderBy('id', 'desc')->get();
    
        // Check if there are any users found
        if (($users instanceof \Illuminate\Database\Eloquent\Collection && !$users->isEmpty()) || ($users instanceof \App\Models\User && $users)) {
            // Return success response with the list of users and user profiles
            $response = ['status' => true, 'msg' => 'User List', 'user_profiles' => $userProfiles];
            return response($response, 200);
            // $response = ['status' => true, 'msg' => 'User List', 'data' => $users, 'bookings' => $appointments, 'user_profiles' => $userProfiles];
        } else {
            // Return error response if no users or bookings were found
            $response = ['status' => false, 'msg' => 'Sorry! No Doctor found.'];
            return response($response, 422);
        }
    }
    
    
    public function HistoryDetailPatient(Request $request)
    {
        // Get the appointments for the authenticated user
        $bookings = Booking::where('user_id', $request->user_id)
            ->whereIn('status', [1,2])
            ->orderBy('schedule_date', 'desc')
            ->get();
        
        // Loop through each booking and fetch the doctor information and their specialisations
        foreach ($bookings as $booking) {
            // Find the doctor associated with the booking
            $doctor = User::find($booking->doctor_id);
            
            if ($doctor) {
                // Add the doctor's image URL (assuming the image is stored in 'content/doctor/')
                $doctor->image = asset('content/doctor/' . $doctor->image);
    
                // Fetch the doctor's profile information, including specialisations
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                // If specialisations exist, fetch the corresponding names
                if ($doctorprofile && $doctorprofile->specialisations) {
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name')
                        ->toArray();
                    
                    // Add specialisations to doctor profile
                    $doctor->specialisations = $specialisationNames;
                } else {
                    // No specialisations found, assign an empty array
                    $doctor->specialisations = [];
                }
            }
            
            // Add the doctor and the user's information to the booking
            $booking->doctor = $doctor;
        }
        
        // Return the appointments with the doctor information and specialisations included
        return response()->json(['status' => true, 'bookings' => $bookings], 200);
    }
    
    
    public function DoctorHistoryDetails(Request $request)
    {
        // Get the appointment for the given booking_id and status 1 (active appointments)
        $appointments = Booking::where('id', $request->booking_id)
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->first();  // This returns a single appointment or null
    
        // Check if appointment is found
        if (!$appointments) {
            // Return response if no appointment is found
            return response()->json(['status' => false, 'msg' => 'No appointment found'], 404);
        }


        // Explicitly get the doctor, user, and clinic details
        $doctor = User::find($appointments->doctor_id);
        $user = UserProfile::find($appointments->user_id);
        $clinic = UserEstablishmentClinic::where('id', $appointments->clinic_id)->first();

        // Manually assign the related data to the appointment model
        // $appointments->doctor = $doctor;
        // $appointments->user = $user;
        // $appointments->clinic = $clinic;
    
        // Generate the PDF
        // $pdf = PDF::loadView('invoice', ['appointment' => $appointments]);
        $pdf = PDF::loadView('invoice', [
            'appointment' => $appointments,
            'doctor' => $doctor,
            'user' => $user,
            'clinic' => $clinic,
        ]);
    
        // Save the PDF to a specific path (e.g., storage/app/public/invoices/)
        $pdfPath = storage_path('app/public/invoices/invoice_' . $appointments->id . '.pdf');
        $pdf->save($pdfPath);  // Save PDF file to storage
    
        // Generate the public URL for the saved PDF file
        $pdfUrl = url('storage/invoices/invoice_' . $appointments->id . '.pdf');
    
        // Retrieve medical records based on member_id and member_type from the appointment
        $userMedical_xray = MedicalRecord::where('member_id', $appointments->member_id)
            ->where('member_type', $appointments->member_type)
            ->where('type', 'xray')
            ->get();
    
        $userMedical_lab = MedicalRecord::where('member_id', $appointments->member_id)
            ->where('member_type', $appointments->member_type)
            ->where('type', 'lab')
            ->get();
    
        $userMedical_prescription = MedicalRecord::where('member_id', $appointments->member_id)
            ->where('member_type', $appointments->member_type)
            ->where('type', 'prescription')
            ->get();
    
        // Map the medical records images to URLs (ensure the path is correct)
        $userMedical_xray->each(function ($record) {
            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
        });
    
        $userMedical_lab->each(function ($record) {
            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
        });
    
        $userMedical_prescription->each(function ($record) {
            if (!empty($record->image)) {
                $record->image = asset('project/public/medical_records/' . $record->image);
            } elseif (!empty($record->pdf)) {
                $record->image = asset('project/storage/app/public/prescriptions/' . $record->pdf);
            } else {
                $record->image = null; // or set a default image path if needed
            }
        });
    
        // Return the response with the appointments and medical records
        return response()->json([
            'status' => true,
            'appointments' => $appointments,
            'pdf_url' => url('project/storage/app/public/invoices/invoice_' . $appointments->id . '.pdf'),
            'xray_records' => $userMedical_xray,
            'lab_records' => $userMedical_lab,
            'prescription_records' => $userMedical_prescription
        ], 200);
    }
    

    public function AppointmentNewUser(Request $request) {

        $validated = $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
        ]);
        $l = UserProfile::where('mobile', $request->mobile)
                ->orWhere('email', $request->email)
                ->first();

        if (!empty($l)) {
            $response = ['status' => false, 'msg' => 'Already User'];
            return response($response, 200);
        }
        if (auth()->user()->parent_id > 0) {
            $link_id = auth()->user()->parent_id;
        } else $link_id = auth()->user()->id;
        $a = new UserProfile();
        $a->link_id = $link_id;
        $a->name = $request->name;
        $a->last_name = $request->last_name;
        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->status = 1;
        $a->save();

        return response()->json(['status' => true,'message' => 'User created successfully', 'user' => $a], 201);
    }


    public function AppointmentAlreadyUserOtpSend(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }

        $otp = $input['otp']; //mt_rand(1000, 9999);
        $user_message = "One Time Password " . $otp . " to verify your Mobile on";
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

        $response = ['status' => true, 'msg' => 'OTP Send successfully.'];
        return response($response, 200);
    }


    public function AppointmentAlreadyUserOtpVerify(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->first(), 'status' => false], 422);
        }
    
        // Check if the user exists
        $user = UserProfile::where('id', $request->user_id)->first();
    
        if (!$user) {
            // If the user is not found, return response for new user
            return response(['status' => true, 'newuser' => 1, 'msg' => 'New user']);
        }
    
        // If user exists, try to decrypt the clinic_id and fetch clinic details
        try {
            // Decrypt the clinic_id (ensure it exists in the user profile)
            if($user->clinic_id){
            $decrypted = Crypt::decryptString($user->clinic_id);
            $clinicId = unserialize($decrypted);
            }else{
                $clinicId = $user->link_id;
            }
            // Check if clinicId is valid
            if (empty($clinicId)) {
                return response(['msg' => 'Invalid clinic ID.', 'status' => false], 400);
            }
    
            // Fetch clinic details
            $clinic = UserEstablishmentClinic::where('user_id', $clinicId)->first();
    
            if (!$clinic) {
                return response(['msg' => 'Clinic not found for the user.', 'status' => false], 404);
            }
    
            // Fetch the doctor details associated with the clinic
            $doctor = User::where('id', auth()->user()->id)
                      ->orWhere('parent_id', auth()->user()->id)
                      ->get();
    
            // Generate the clinic logo URL
            $clinic->logo = asset('project/public/clinics/') . '/' . $clinic->logo;
    
            // Create an access token for the UserProfile model
            $token = $user->createToken('MyApp')->accessToken;
    
            // Fetch the main user details using member_id from the request
            $main_user = UserProfile::where('id', $user->id)
                ->where('status', 1)
                ->first(); // Use first() to get the first matching record
    
            // If no main user is found, return an error
            if (!$main_user) {
                return response(['status' => false, 'msg' => 'Main user not found.'], 404);
            }
    
            // Fetch the list of members related to the given user_id
            $members = Member::where('user_id', $user->id)
                ->where('status', 1)
                ->orderBy('id', 'ASC')
                ->get();
    
            // Modify the image URL for each member in the list
            $members->each(function ($member) {
                if ($member->image) {
                    $member->image = asset('project/public/member_images/') . '/' . $member->image;
                } else {
                    $member->image = asset('project/public/member_images/default.jpg'); // Default image if no image exists
                }
            });
    
            // Merge main user data with the members list, adding the main user as the first member
            $mergedMembers = collect([$main_user])->merge($members); // Merge main user into members list
    
            return response([
                'msg' => 'User already exists',
                'token' => $token,
                'user_detail' => $user,
                'clinic_detail' => $clinic,
                'doctor' => $doctor,
                'member' => $mergedMembers, // Return all members, including the main user
                'status' => true
            ]);
    
        } catch (\Exception $e) {
            // If there is any error during decryption or fetching clinic info, handle it
            return response(['msg' => 'An error occurred: ' . $e->getMessage(), 'status' => false], 500);
        }
    }
    

    public function member_list_dr(Request $request) {
        // Validate the member_id in the request
        if (!$request->has('member_id')) {
            return response(['status' => false, 'msg' => 'Member ID is required.'], 400);
        }
    
        // Fetch the main user details using member_id from the request
        $main_user = UserProfile::where('id', $request->member_id)
                                ->where('status', 1)
                                ->first(); // Use first() to get the first matching record
    
        // If no main user is found, return an error
        if (!$main_user) {
            return response(['status' => false, 'msg' => 'Main user not found.'], 404);
        }
    
        // Fetch the list of members related to the given user_id
        $members = Member::where('user_id', $request->member_id)
                         ->where('status', 1)
                         ->orderBy('id', 'ASC')
                         ->get();
    
        // Modify the image URL for each member in the list
        $members->each(function ($member) {
            if ($member->image) {
                $member->image = asset('project/public/member_images/') . '/' . $member->image;
            } else {
                $member->image = asset('project/public/member_images/default.jpg'); // Default image if no image exists
            }
        });
    
        // Prepare a new "self" member entry for the main user
        $main_user_member = [
            'id' => $main_user->id,
            'user_id' => $main_user->user_id,
            'name' => $main_user->name,
            'last_name' => $main_user->last_name,
            'mobile' => $main_user->mobile,
            'dob' => $main_user->date_of_birth,
            'relation' => 'self', // Main user is always 'self'
            'gender' => $main_user->gender,
            'image' => asset('content/user/' . ($main_user->image ?: 'default.jpg')), // Updated image path for the main user
            'status' => $main_user->status,
            'created_at' => $main_user->created_at,
            'updated_at' => $main_user->updated_at
        ];
    
        // If there are members, prepend the main user as the first member
        if (!$members->isEmpty()) {
            $members->prepend((object) $main_user_member);
        } else {
            // If no members are found, only return the main user as the member
            $members = collect([(object) $main_user_member]);
        }
    
        // Prepare the final response
        $response = [
            'status' => true,
            'msg' => 'Member List',
            'data' => [
                'members' => $members // Include members with main user at the beginning
            ]
        ];
    
        return response($response, 200);
    }


    public function doctor_treatment_list(Request $request)
    {
        // Get the logged-in user profile
        $user = UserProfile::where('id', $request->user_id)->first();
    
        // Decrypt the clinic ID
        if($user->clinic_id){
            $decrypted = Crypt::decryptString($user->clinic_id);
            $clinicId = unserialize($decrypted);
        }else{
            $clinicId = $user->link_id;
        }

        // Get the clinic information
        $clinic = UserEstablishmentClinic::where('user_id', $clinicId)->where('status',1)->first();
    
        // Get the doctor associated with the clinic
        $doctor = User::where('id', $clinic->user_id)->first();
    
        // Get all active treatments for this doctor
        $treatment_list = Treatment::where('doctor_id', $doctor->id)->where('status', 1)->get();
    
        // Check if treatment list is not empty
        if (!empty($treatment_list)) {
            foreach ($treatment_list as $key) {
                // Get the doctors associated with the treatment
                $treatmentDoctor = TreatmentDoctor::where('treatment_id', $key->id)->with('doctor')->get();
    
                foreach ($treatmentDoctor as $doctorDetail) {
                    // Fetch the doctor's image from the doctor profile
                    $doctorDetail->doctor->image = asset('content/doctor/') . '/' . $doctorDetail->doctor->image;
    
                    // Now, fetch additional information from the UserInformation table for the doctor
                    $userInformation = UserInformation::where('user_id', $doctorDetail->doctor->id)->first();
    
                    if ($userInformation) {
                        // Add the user's information (e.g. specializations) to the doctor's details
                        $doctorDetail->doctor->doctor_info = [
                            // 'experience' => $userInformation->experience,
                            'specialisations' => $this->getSpecializationsFromIds($userInformation->specialisations),
                            // Add other fields from the UserInformation table as needed
                        ];
                    }
                }
    
                // Add the list of doctors to the treatment
                $key->doctorlist = $treatmentDoctor;
            }
    
            // Prepare the response with the treatment list and clinic details
            $response = [
                'status' => true,
                'msg' => 'Treatment List',
                'data' => $treatment_list,
                'clinic' => $clinic,
            ];
    
            return response($response, 200);
        } else {
            // If no treatments found
            $response = ['status' => false, 'msg' => 'Sorry! No treatments found.'];
            return response($response, 200);
        }
    }
    
    /**
     * Helper function to get specializations based on IDs.
     */
    private function getSpecializationsFromIds($specializations)
    {
        // If there are no specializations, return an empty array
        if (empty($specializations)) {
            return [];
        }
    
        // Split the specializations by '|' to get an array of IDs
        $specializationIds = explode('|', $specializations);
    
        // Query the master_specializations table to get the specialization details
        $specializations = MasterSpecialsation::whereIn('id', $specializationIds)->get();
    
        // Format the results as an array of specialization names
        $specializationsArray = $specializations->pluck('name')->toArray();
    
        return $specializationsArray;
    }


  
    public function AppointmentBookingDoctor(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required',
            'user_id' => 'required',
            'consultation_type' => 'required',
            'appointment_type' => 'required',
            'treatment_id' => 'required',
            'member_id' => 'required',
            'member_type' => 'required',
            'symptoms' => 'required',
            'clinic_id' => 'required',
            'schedule_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'payment_mode' => 'required',
            'subtotal' => 'required',
            'gst' => 'required',
            // 'notes' => 'required'
        ]);
        $doctor_id = 0;
        if ($request->doctor_id == 0) {
            $anyother = $this->assigndocauto($request->schedule_date,$request->start_time);
            if ($anyother > 0) {
                $doctor_id = $anyother;
            }
        } else $doctor_id = $validated['doctor_id'];
        if ($doctor_id > 0) {
            $status = 0;
            // $gettreatmen = Treatment::where('id',$request->treatment_id)->first();
            // if ($gettreatmen) {
            //     if ($gettreatmen->call_before_confirmation == 0) {
            //         $status = 4;
            //     }
            // }
            $status = 4;
            $member_id = $validated['member_id'];
            if ($request->member_type == 'self') {
                if ($validated['member_id'] == $validated['user_id']) {
                    $member_id = 0;
                }
            }
            
            $appointment = Booking::create([
                'doctor_id' => $doctor_id,
                'user_id' => $validated['user_id'],
                'consultation_type' => $validated['consultation_type'],
                'appointment_type' => $validated['appointment_type'],
                'treatment_id' => $validated['treatment_id'],
                'member_id' => $member_id,
                'member_type' => $validated['member_type'],
                'symptoms' => $validated['symptoms'],
                'clinic_id' => $validated['clinic_id'],
                'schedule_date' => $validated['schedule_date'],
                'schedule_time' => $validated['start_time'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'payment_mode' => $validated['payment_mode'],
                'subtotal' => $validated['subtotal'],
                'gst' => $validated['gst'],
                'notes' => $request->notes,
                'status' => $status,
                'is_paid'=>1
            ]);
            $bdate = date("l, F j, Y g:i A", strtotime($appointment->schedule_date.' '.$appointment->schedule_time));
            $msgarray = array("title"=>"📌New Appointment Scheduled",
                              "msg"=>"Your appointment with Dr. ".$appointment->doctordetail->name." at ".$appointment->clinicdetail->clinic_name." has been successfully scheduled for ".$bdate.'.',

                             "msg2"=>"You have a new appointment with ".$appointment->userdetail->name." at ".$appointment->clinicdetail->clinic_name." on ".$bdate."."
                       );
            $this->async_to_all($msgarray,'',$appointment->id,'bookingadd');
            $intructions = "A payment instruction is the instance of a payment method with the details necessary to perform payment actions. ";
            return response()->json([ 'status' => true,'message' => 'Appointment booked successfully', 'appointment' => $appointment, 'intructions' => $intructions], 201);
        } else {
            return response()->json([ 'status' => false,'message' => 'No doctor available'], 201);
        }
        
    }


    public function assigndocauto($schedule_date='',$start_time='')
    {
        $ret = 0;
        $decrypted = Crypt::decryptString(auth()->user()->clinic_id);
        $doctor_id = unserialize($decrypted);
        if ($doctor_id) {
            $checkanybooking = Booking::where('doctor_id',$doctor_id)->where('schedule_date',$schedule_date)->where('start_time',$start_time)->whereIn('status',[0,1,4])->first();
            if ($checkanybooking) {
                $getchild = User::where('parent_id',$doctor_id)->where('status',1)->where('approved',1)->get();
                if ($getchild->count() > 0) {
                    foreach ($getchild as $key) {
                        $checkanybooking = Booking::where('doctor_id',$key->id)->where('schedule_date',$schedule_date)->where('start_time',$start_time)->whereIn('status',[0,1,4])->first();
                        if ($checkanybooking) {
                            // code...
                        } else {
                           $ret = $key->id; 
                           break;
                        }
                    }
                }
            } else {
                $ret = $doctor_id; 
            }
        }
        return $ret; 
    }


    public function DoctorUserCheck(Request $request)
    {
        // Optional: Basic validation (can be moved to a FormRequest for cleaner code)
        $request->validate([
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
        ]);
    
        $l = UserProfile::where(function($query) use ($request) {
                $query->where('mobile', $request->mobile)
                ->orWhere('email', $request->email);
            })
            ->where('status', 1)
            ->first();
    
        if (!$l) {
            return response()->json(['status' => false, 'msg' => 'User not found'], 200);
        }
        
        $lis = Booking::whereIN('doctor_id', [auth()->user()->id,auth()->user()->parent_id])
            ->where('user_id', $l->id)
            ->first();
    
        if ($lis) {
            return response()->json(['status' => true, 'User' => $l], 200);
        } else {
            return response()->json(['status' => false, 'msg' => 'This number is not registered with you'], 200);
        }
    }
    



    public function PatientPayment(Request $request)
    {
        $validated = $request->validate([
            'partial_payment' => 'required',
            'partial_percentage' => 'required',
            'loyalty_points' => 'required',
        ]);
        // dd(auth()->user()->id);
        $a = UserInformation::where('user_id',auth()->user()->id)->first();
        $a->partial_payment = $request->partial_payment;
        $a->partial_percentage = $request->partial_percentage;
        $a->loyalty_points = $request->loyalty_points;
        $a->save();

        return response()->json(['status' => true,'message' => 'PatientPayment update successfully', 'patient' => $a], 201);
    }


    public function PatientPaymentList(Request $request)
    {

        $a = UserInformation::where('user_id',auth()->user()->id)->select('partial_payment','partial_percentage','loyalty_points')->first();

        return response()->json(['status' => true,'message' => 'PatientPayment List', 'patient' => $a], 201);
    }


    public function DoctorSettingData(Request $request)
    {

        $a = Generalsetting::select('about_us','privacy_policy','terms_and_condition','contact_us')->first();

        return response()->json(['status' => true, 'data' => $a], 201);
    }


    public function DoctorFeedback(Request $request) {
        // Get all bookings for the authenticated doctor
        $l = Booking::where('doctor_id', auth()->user()->id)->get();
    
        // Initialize an array to store all ratings
        $allRatings = [];
    
        // Loop through each booking
        foreach ($l as $data) {
            // Get ratings for each booking (if any)
            $rat = Rating::where('booking_id', $data->id)->get();
    
            // Merge the ratings into the $allRatings array
            $allRatings = array_merge($allRatings, $rat->toArray());
        }
    
        // Return the ratings as a response
        return response()->json(['status' => true, 'rating' => $allRatings], 200);
    }

    public function getquantity($for,$id)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', $id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', $id)
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

