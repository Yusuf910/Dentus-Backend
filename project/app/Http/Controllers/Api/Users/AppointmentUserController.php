<?php

namespace App\Http\Controllers\Api\Users;

use Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\Rating;
use App\Models\UserModels\Booking;
use App\Models\UserModels\Member;
use App\Models\UserModels\Cancel;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;
use App\Models\UserModels\UserPayment;
use App\Models\UserModels\UserSubscriptionsPack;
use App\Models\UserModels\UserPremiumAddonsPack;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Models\UserEstablishmentClinic;
use App\Models\UserInformation;
use App\Models\Treatment;
use App\Models\TreatmentDoctor;
use App\Models\MasterSpecialsation;
use App\Models\Prescription;
use Illuminate\Support\Facades\Crypt;
use App\Traits\SdSendSms;
use Carbon\Carbon;
use File;
// use Barryvdh\DomPDF\Facade as PDF;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;
// use Faker\Generator;
// use Illuminate\Container\Container;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use App\Models\SeasonalOffers;
use App\Models\UserModels\ReferCodeHistory;
use App\Models\DoctorClinicSlot;
use App\Models\UserEstablishmentHoliday;
use Illuminate\Support\Facades\Http;

class AppointmentUserController extends Controller
{
    use SdSendSms;
    // public function appointment_treatment_list(Request $request)
    // {
    //     // dd(Auth()->user());

    //     $user = UserProfile::where('id', auth()->user()->id)->first();
    //     $decrypted = Crypt::decryptString($user->clinic_id);
    //     $clinicId = unserialize($decrypted);
    //     $clinic = UserEstablishmentClinic::where('user_id',$clinicId)->first();
    //     $doctor = User::where('id',$clinic->user_id)->first();
    //     // $doctor_profile = UserInformation::where('user_id',$doctor->id)->first();
    //     $treatment_list = Treatment::where('doctor_id',$doctor->id)->where('status',1)->get();
    //     if(!empty($treatment_list)) {
    //         foreach ($treatment_list as $key) {
    //             $treamentdoctor = TreatmentDoctor::where('treatment_id',$key->id)->with('doctor')->get();
    //             $key->doctorlist = $treamentdoctor;
    //             // dd($key);
    //         }
    //         $response = ['status' => true, 'msg' => 'l List', 'data' => $treatment_list,'clinic' => $clinic,];
    //         return response($response, 200);
    //     } else {
    //         $response = ['status' => false, 'msg' => 'Sorry ! No  found.'];
    //         return response($response, 200);
    //     }

    // //    $clinic->logo =  asset('project/public/clinics/').'/'.$clinic->logo ;
    // //    $doctor->image =  asset('project/public/doctor_profile/').'/'.$doctor->image ;
    // //     return response()->json([
    // //         'message' => 'Clinic found',
    // //         'user' => $user,
    // //         'clinic' => $clinic,
    // //         'treatment_list' => $treatment_list,
    // //         // 'doctor' => $doctor,
    // //         // 'doctor_profile' => $doctor_profile,
    // //         'status' => true
    // //     ], 200);
    // }

     public function doctor_tax(Request $request)
    {
        try {
            // Get authenticated user
             $user = User::where('id', auth()->user()->id)->first();
            
            // Validate request
            // $validator = Validator::make($request->all(), [
            //     'tax_id' => 'required|string|max:255|unique:users,tax_id,' . $user->id,
            // ]);
            
            // if ($validator->fails()) {
            //     return response()->json([
            //         'status' => false,
            //         'msg' => 'Validation failed',
            //         'errors' => $validator->errors()
            //     ], 422);
            // }
            
            // Update only tax_id
            $user->tax_id = $request->tax_id;
            $user->save();
            
            return response()->json([
                'status' => true,
                'msg' => 'Tax ID updated successfully',
                'data' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'tax_id' => $user->tax_id
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }



     public function getDoctorTax(Request $request)
    {
        try {
            $user = auth()->user();
            
            return response()->json([
                'status' => true,
                'data' => [
                    'tax_id' => $user->tax_id
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Something went wrong'
            ], 500);
        }
    }

    public function appointment_treatment_list(Request $request)
    {
        $user = UserProfile::where('id', auth()->user()->id)->first();
        if ((int)$user->clinic_id) {
            $clinicId = $user->link_id;
        }
        else {
            $decrypted = Crypt::decryptString($user->clinic_id);
            $clinicId = unserialize($decrypted);
        }
        $clinic = UserEstablishmentClinic::where('user_id', $clinicId)->where('status',1)->first();
        $doctor = User::where('id', $clinic->user_id)->first();
        $treatment_list = Treatment::where('doctor_id', $doctor->id)->where('status', 1)->get();
        if (!empty($treatment_list)) {
            foreach ($treatment_list as $key) {
                $treatmentDoctor = TreatmentDoctor::where('treatment_id', $key->id)->with('doctor')->get();
    
                foreach ($treatmentDoctor as $index => $doctorDetail) {
                    $doctorDetail->doctor->image = asset('content/doctor/') . '/' . $doctorDetail->doctor->image;
                    $doctorDetail->doctor->UserInformationDetails;
                    // $checktimeslot = $this->checktimeslot($doctorDetail->doctor->id);
                    // if ($checktimeslot == '') {
                    //     unset($treatmentDoctor[$index]);
                    //     continue;
                    // } else {
                        $userInformation = UserInformation::where('user_id', $doctorDetail->doctor->id)->first();
                        $doctorDetail->iscomeinpackage = 0;
                        $doctorDetail->pack_id = 0;
                        $bookingCountmm = 0;
                        $matchingRow = UserPremiumAddonsPack::join('user_subscriptions_pack', 'user_premium_addons_pack.subscription_id', '=', 'user_subscriptions_pack.id')
                                            ->where('user_premium_addons_pack.treatment_id', $key->id)
                                            ->where('user_subscriptions_pack.user_id', auth()->user()->id)
                                            ->where('user_subscriptions_pack.status', 1)
                                            ->select('user_premium_addons_pack.*')
                                            ->orderBy('user_subscriptions_pack.end_date', 'asc')
                                            ->get();
                        if ($matchingRow->count() > 0) {
                            $mmquantity = 0;
                            $mmsubscription_id = 0;
                            $i = 0;
                            foreach ($matchingRow as $mm) {
                                $mmquantity += $mm->quantity;
                                if ($i == 0) {
                                    $mmsubscription_id = $mm->subscription_id;
                                }
                                $bookingCount = Booking::where('treatment_id', $key->id)
                                ->where('plan_id', $mm->subscription_id)
                                ->whereIn('status', [0, 1, 4])
                                ->count();
                                $bookingCountmm += $bookingCount;
                                $i++;
                            }
                            $key->iscomeinpackage = $mmquantity;
                            $key->pack_id = $mmsubscription_id;
                            
                        }
                        $key->bookingCount = $bookingCountmm;

                        if ($userInformation) {
                            $doctorDetail->doctor->doctor_info = [
                                'specialisations' => $this->getSpecializationsFromIds($userInformation->specialisations),
                            ];
                        }
                    // }
                }
    
                // $treatmentDoctor = array_values($treatmentDoctor);

                $key->doctorlist = array_values($treatmentDoctor->toArray());//$treatmentDoctor;
            }
    
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
    

    public function book(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required',
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
            'gst' => 'required'
        ]);
        // dd(auth()->user()->id);
        $doctor_id = 0;
        if ($request->doctor_id == 0) {
            $anyother = $this->assigndocauto($request->schedule_date,$request->start_time);
            if ($anyother > 0) {
                $doctor_id = $anyother;
            }
        } else $doctor_id = $validated['doctor_id'];
        if ($doctor_id > 0) {
            $user_data = UserProfile::where("id",Auth()->user()->id)->first();
            $user_wallet = $wallet_balace  = $user_data->wallet;
            $cango = 1;
            if ($request->loyality_points > 0) {
                $mainpoints = $user_wallet;
                $r = User::where('id',auth()->user()->link_id)->first();
                if ($r->UserInformationDetails) {
                    $mainpoints = $user_wallet;// * $r->UserInformationDetails->redemption_points ?? 0;
                }
                if ( $mainpoints >= $request->loyality_points ) {
                    $cango = 1;
                } else {
                    $cango = 0;
                    $response = ['status' => false, 'msg' => "You have insufficient loyality balance."];
                }
            }
            $status = 0;
            $gettreatmen = Treatment::where('id',$request->treatment_id)->first();
            if ($gettreatmen) {
                if ($gettreatmen->call_before_confirmation == 0) {
                    $status = 4;
                }
            }
            if ($cango == 1) {
                $member_id = $validated['member_id'];
                if ($request->member_type == 'self') {
                    if ($validated['member_id'] == $request->user_id) {
                        $member_id = 0;
                    }
                }
                $is_paid = 1;
                if ($request->partial_amount > 0) {
                    $is_paid = 2;
                }
                if ($request->plan_id > 0) {
                    $is_paid = 1;
                }


                 // ✅ Razorpay Capture Logic (if payid exists)
            if (!empty($request->payid)) {
                $key_id = 'rzp_live_RCGVCm3xkWYVRs';
                $key_secret = '73XN1JZWDZSEf69PcIzHz2Fd';

                // Fetch payment details
                $paymentInfo = Http::withBasicAuth($key_id, $key_secret)
                    ->get("https://api.razorpay.com/v1/payments/{$request->payid}");

                if (!$paymentInfo->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid Razorpay Payment ID',
                        'razorpay_response' => $paymentInfo->json()
                    ], 400);
                }

                if ($paymentInfo['status'] === 'authorized') {
                    $captureAmount = $paymentInfo['amount'];

                    $captureResponse = Http::withBasicAuth($key_id, $key_secret)
                        ->post("https://api.razorpay.com/v1/payments/{$request->payid}/capture", [
                            'amount' => $captureAmount
                        ]);

                    if (!$captureResponse->successful()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Payment capture failed',
                            'razorpay_response' => $captureResponse->json()
                        ], 400);
                    }
                }
            }
            
                $appointment = Booking::create([
                    'doctor_id' => $doctor_id,
                    'payid' =>$request->payid,
                    'user_id' => auth()->user()->id,
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
                    'status' => $status,
                    'plan_id'=>$request->plan_id,
                    'loyality_points'=>$request->loyality_points,
                    'partial_amount'=>$request->partial_amount,
                    'total_amount'=>$request->total_amount,
                    'is_paid'=>$is_paid,
                    'coupan_code_id' => $request->coupan_code_id ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,


                ]);
                if ( $wallet_balace >= $request->loyality_points ) {
                    $update_wallet =  $user_wallet-$request->loyality_points;
                    $new = new UserPayment();
                    $new->user_id = auth()->user()->id;
                    $new->type = 2;
                    $new->booking_id = $appointment->id;
                    $new->action = 'debit';
                    $new->amount = $request->loyality_points;
                    $new->old_balance = $user_wallet;
                    $new->payment_status = 'completed';
                    $new->payment_method = 'online';
                    $new->new_balance = $update_wallet;
                    $new->status = 1;
                    $new->trxn_id =  time().rand();
                    $new->save();
                    auth()->user()->wallet = $update_wallet;
                    auth()->user()->save();
                }
                $bdate = date("l, F j, Y g:i A", strtotime($appointment->schedule_date.' '.$appointment->schedule_time));
                if ($status > 0) {
                    $msgarray = array("title"=>"📌New Appointment Scheduled",
                                  "msg"=>"Your appointment with Dr. ".$appointment->doctordetail->name." at ".$appointment->clinicdetail->clinic_name." has been successfully scheduled for ".$bdate.'.',

                                 "msg2"=>"You have a new appointment with ".$appointment->userdetail->name." at ".$appointment->clinicdetail->clinic_name." on ".$bdate."."
                           );
                } else {
                    $msgarray = array("title"=>"📌New Appointment Scheduled",
                                  "msg"=>"Thanks for you appointment request 🙏🏻 It will be confirmed anytime soon. Once confirmed it will be displayed in your calendar.",

                                 "msg2"=>"You have a new appointment with ".$appointment->userdetail->name." at ".$appointment->clinicdetail->clinic_name." on ".$bdate."."
                           );
                }
                
                $this->async_to_all($msgarray,'',$appointment->id,'bookingadd');
                $intructions = "A payment instruction is the instance of a payment method with the details necessary to perform payment actions. ";
                $countbooking = Booking::where("user_id",auth()->user()->id)->count();
                if($countbooking == 1) {
                    $this->referBenefitadd(auth()->user()->id,auth()->user()->link_id);
                }
                return response()->json([ 'status' => true,'message' => 'Appointment booked successfully', 'appointment' => $appointment, 'intructions' => $intructions], 201);
            }
            else return response()->json($response);
        } else {
            return response()->json([ 'status' => false,'message' => 'No doctor available'], 201);
        }
        
    }

    public function assigndocauto($schedule_date='',$start_time='')
    {
        $ret = 0;
        if ($user->link_id > 0) {
            $doctor_id = $user->link_id;
        }
        else if ((int)$user->clinic_id) {
            $doctor_id = $user->link_id;
        }
        else {
            $decrypted = Crypt::decryptString($user->clinic_id);
            $doctor_id = unserialize($decrypted);
        }
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
    
    public function appointmenttimeslotold(Request $request)
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
        
        if ($request->doctor_id > 0) {
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
            $key1 = UserEstablishmentClinic::where('id', $request->clinic_id)
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

    public function appointmenttimeslot(Request $request)
    {
        $input = $request->all();
        $file = time() . rand() . '_file.json';
        $destinationPath = "project/checkNOTIlogs/";
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, json_encode($input));
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
        
        if ($request->doctor_id > 0) {
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
        } else {
            $key1 = UserEstablishmentClinic::where('id', $request->clinic_id)
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

    public function clinicList(Request $request)
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

        // If doctor_id is 0, decrypt the clinic ID from a predefined string
        // if ($request->doctor_id == 0) {
            // Decrypt the clinic ID
            $decrypted = Crypt::decryptString('eyJpdiI6Ijh2Qy9mUUhlY3lrcmpuazZNa3VkeFE9PSIsInZhbHVlIjoiVEMya3lUWk1xWEpDT2l2MXJpOGhIQT09IiwibWFjIjoiNjI3MzQ3NDMyYjgyZDA4MDM2OTE5YTEzNThmN2M3ZTgzMGQ4NTEzMDYzMDIwNjhkODU3OTdmZmI4YmFjMGVhNyIsInRhZyI6IiJ9');
            $clinicId = unserialize($decrypted);

            // Fetch clinics by the decrypted clinic ID
            $result = UserEstablishmentClinic::where('user_id', auth()->user()->link_id)->where('status',1)
                ->orderBy('id', 'ASC')
                ->get()
                ->map(function ($item) {
                    $item->image = asset('content/doctor/clinic/' . $item->image);
                    return $item;
                });
        // } else {
            // If doctor_id is not 0, fetch clinics for the provided doctor_id
        //     $result = UserEstablishmentClinic::where([
        //         ['user_id', $request->doctor_id],
        //         ['status', 1]  // Ensure that the clinic is active
        //     ])
        //         ->orderBy('id', 'ASC')
        //         ->get()
        //         ->map(function ($item) {
        //             $item->image = asset('content/doctor/clinic/' . $item->image);
        //             return $item;
        //         });
        // }

        // Check if no results were found
        if ($result->isEmpty()) {
            return response(['status' => false, 'msg' => 'Sorry! Not found.'], 200);
        }

        // Return the list of clinics
        return response(['status' => true, 'msg' => 'List', 'data' => $result], 200);
    }


    // Get History of Appointments
    public function getHistory(Request $request)
    {
        // Get the appointments for the authenticated user
        $appointments = Booking::where('user_id', auth()->user()->id)->with('memberdetail')
            ->whereIN('status', [0,4])
            ->where(function($query) {
                $query->whereDate('schedule_date', Carbon::today()) 
                      ->orWhere('schedule_date', '>', Carbon::today());
            })
            ->orderBy('schedule_date', 'asc')
            ->orderByRaw("STR_TO_DATE(schedule_time, '%h:%i %p') asc")
            ->get();
    
        // Retrieve the doctor and treatment information for each appointment
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            // Get the doctor associated with this appointment
            $doctor = User::find($appointment->doctor_id);
            
            if ($doctor) {
                $doctor->image = asset('content/doctor/' . $doctor->image);
                // Get the doctor's specialisation IDs from UserInformation
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                // If specialisations exist, fetch the corresponding names
                if ($doctorprofile && $doctorprofile->specialisations) {
                    // Split the specialisations string by "|" to get an array of IDs
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
        
                    // Query the master_specialisations table for the names of these specialisations
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name') // Only fetch the names
                        ->toArray();
        
                    // Add the doctor and the specialisation names to the appointment
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames; // Set specialisations after doctor
                } else {
                    // If no specialisations, set doctorprofile to an empty array or null
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }
            }
    
            // Now, we want to fetch the treatment name based on treatment_id
            $treatment = DB::table('treatments')
                ->where('id', $appointment->treatment_id)
                ->first(['treatment_name']); // Fetch treatment name based on the treatment_id
    
            // If treatment exists, add treatment name to the appointment
            if ($treatment) {
                $appointment->treatment_name = $treatment->treatment_name;
            } else {
                // If no treatment is found, you can set a default value or null
                $appointment->treatment_name = null;
            }
    
            return $appointment;
        });
    
        // Return the appointments with the doctor and treatment information included
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor,"path"=>asset('content/user/')], 200);
    }
    




    public function getHistoryDetails(Request $request)
    {
        // Get the appointments for the authenticated user
        $appointments = Booking::where('id', $request->booking_id)->with('memberdetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->get();
        
        // Retrieve the doctor and clinic for each appointment
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            // Get the doctor associated with this appointment
            $doctor = User::find($appointment->doctor_id);
            
            // Get the clinic associated with this appointment (use first() to get the clinic data)
            $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first(); // use first() to get the first matching clinic

            if ($doctor) {
                $doctor->image = asset('content/doctor/' . $doctor->image);
                // Get the doctor's specialisation IDs from UserInformation
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                // If specialisations exist, fetch the corresponding names
                if ($doctorprofile && $doctorprofile->specialisations) {
                    // Split the specialisations string by "|" to get an array of IDs
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
        
                    // Query the master_specialisations table for the names of these specialisations
                    $specialisationNames = \DB::table('master_specialsations') // Fixed the typo: "master_specialsations" => "master_specialisations"
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name') // Only fetch the names
                        ->toArray();
        
                    // Add the doctor, clinic, and specialisation names to the appointment
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames; // Set specialisations after doctor
                } else {
                    // If no specialisations, set doctorprofile to an empty array or null
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }
            }

            // Now, we want to fetch the full treatment details based on treatment_id
            $treatment = DB::table('treatments')
                ->where('id', $appointment->treatment_id)
                ->first();  // Fetch the full treatment details
        
            // If treatment exists, add the full treatment details as a new object
            if ($treatment) {
                // Assign the full treatment object to the `treatment_details` property
                $appointment->treatment_details = $treatment;
                
                // Optionally, keep the current treatment name and duration as well (they will remain the same)
                $appointment->treatment_name = $treatment->treatment_name;
                $appointment->average_duration = $treatment->average_duration;
            } else {
                // If no treatment is found, set treatment_details to null and other values to null
                $appointment->treatment_details = null;
                $appointment->treatment_name = null;
                $appointment->average_duration = null;
            }
        
            // Add the clinic data to the appointment (if clinic exists)
            $appointment->clinic = $clinic ? $clinic : null;
        
            return $appointment;
        });
        
        // Return the appointments with the doctor, clinic, and treatment information included
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
    }

    
    

    // Get History of Appointments
    public function getHistoryCompleted(Request $request)
    {
        // Get the appointments for the authenticated user
        $appointments = Booking::where('user_id', auth()->user()->id)->with('memberdetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->orderByRaw("STR_TO_DATE(schedule_time, '%h:%i %p') desc")
            ->where('status',1)
            ->get();
    
        // Retrieve the doctor for each appointment
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            // Get the doctor associated with this appointment
            $doctor = User::find($appointment->doctor_id);
           
            if ($doctor) {

                $doctor->image = asset('content/doctor/' . $doctor->image);
                // Get the doctor's specialisation IDs from UserInformation
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                // If specialisations exist, fetch the corresponding names
                if ($doctorprofile && $doctorprofile->specialisations) {
                    // Split the specialisations string by "|" to get an array of IDs
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
    
                    // Query the master_specialisations table for the names of these specialisations
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name') // Only fetch the names
                        ->toArray();
    
                    // Add the doctor and the specialisation names to the appointment
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames; // Set specialisations after doctor
                } else {
                    // If no specialisations, set doctorprofile to an empty array or null
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }

            }

                // Now, we want to fetch the treatment name based on treatment_id
                $treatment = DB::table('treatments')
                    ->where('id', $appointment->treatment_id)
                    ->first(['treatment_name']); // Fetch treatment name based on the treatment_id
        
                // If treatment exists, add treatment name to the appointment
                if ($treatment) {
                    $appointment->treatment_name = $treatment->treatment_name;
                } else {
                    // If no treatment is found, you can set a default value or null
                    $appointment->treatment_name = null;
                }
                $appointment->invoiceurl = route('invoiceuser',$appointment->id);
            return $appointment;
        });
    
        // Return the appointments with the doctor information included
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
    }



//     public function getHistoryCompletedDetails(Request $request)
// {
//     // Get the appointment for the authenticated user
//     $appointment = Booking::where('id', $request->booking_id)
//         ->orderBy('schedule_date', 'desc')
//         ->where('status', 1)
//         ->first();

//     if (!$appointment) {
//         return response()->json(['status' => false, 'message' => 'Appointment not found'], 404);
//     }

//     // Get the doctor associated with this appointment
//     $doctor = User::find($appointment->doctor_id);
    
//     // Get the clinic associated with this appointment
//     $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
    
//     if ($doctor) {
//         // Get the doctor's specialisation IDs from UserInformation
//         $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        
//         // If specialisations exist, fetch the corresponding names
//         if ($doctorprofile && $doctorprofile->specialisations) {
//             $specialisationIds = explode('|', $doctorprofile->specialisations);
//             $specialisationNames = \DB::table('master_specialsations')
//                 ->whereIn('id', $specialisationIds)
//                 ->pluck('name')
//                 ->toArray();
            
//             // Set the doctor and specialisations
//             $appointment->doctor = $doctor;
//             $appointment->doctorprofile = $specialisationNames;
//         } else {
//             $appointment->doctor = $doctor;
//             $appointment->doctorprofile = [];
//         }
//     }

//     // Add the clinic data to the appointment
//     $appointment->clinic = $clinic ? $clinic : null;

//     // Return the appointment with doctor and clinic information
//     return response()->json(['status' => true, 'appointment' => $appointment], 200);
// }




    public function getHistoryCompletedDetails(Request $request)
    {
        // Get the appointment for the authenticated user
        $appointment = Booking::where('id', $request->booking_id)->with('memberdetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->first();

        if (!$appointment) {
            return response()->json(['status' => false, 'message' => 'Appointment not found'], 404);
        }

        // Get the doctor and user associated with this appointment
        $rating = Rating::where('booking_id', $appointment->id)->select('rating', 'review')->first();
        $doctor = User::find($appointment->doctor_id);
        $user = UserProfile::find($appointment->user_id);
        
        // Get the clinic associated with this appointment
        $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
        
        if ($doctor) {

            $doctor->image = asset('content/doctor/' . $doctor->image);
            // Get the doctor's specialisation IDs from UserInformation
            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            
            // If specialisations exist, fetch the corresponding names
            if ($doctorprofile && $doctorprofile->specialisations) {
                $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
                
                // Set the doctor and specialisations
                $appointment->rating = $rating;
                $appointment->doctor = $doctor;
                $appointment->user = $user;
                $appointment->doctorprofile = $specialisationNames;
            } else {
                $appointment->rating = $rating;
                $appointment->doctor = $doctor;
                $appointment->user = $user;
                $appointment->doctorprofile = [];
            }

        }


                // Now, we want to fetch the treatment name based on treatment_id
                $treatment = DB::table('treatments')
                ->where('id', $appointment->treatment_id)
                ->first(['treatment_name']); // Fetch treatment name based on the treatment_id

                // If treatment exists, add treatment name to the appointment
                if ($treatment) {
                    $appointment->treatment_name = $treatment->treatment_name;
                } else {
                    // If no treatment is found, you can set a default value or null
                    $appointment->treatment_name = null;
                }

        // Add the clinic data to the appointment
        $appointment->clinic = $clinic ? $clinic : null;

        // Generate the PDF
        $pdf = PDF::loadView('invoice', ['appointment' => $appointment]);

        // Save the PDF to a specific path (e.g., storage/app/public/invoices/)
        $pdfPath = storage_path('app/public/invoices/invoice_' . $appointment->id . '.pdf');
        $pdf->save($pdfPath);  // Save PDF file to storage

        // Generate the public URL for the saved PDF file
        $pdfUrl = url('storage/invoices/invoice_' . $appointment->id . '.pdf');


        //prescription pdf start
            $booking = Prescription::where('booking_id', $appointment->id)->first();
            if($booking){
            // $book = Booking::find($booking->booking_id);
            $doctor_dtl = User::find($appointment->doctor_id);
            $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();

            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
            $firstSpecialisation = $specialisationNames[0] ?? '';
            $user_dtl = UserProfile::find($appointment->user_id);
            $name = $user_dtl->name.' '.$user_dtl->last_name;
            $gender = $user_dtl->gender;
            if ($appointment->member_id > 0) {
                $name = ($appointment->memberdetail->name ?? $user_dtl->name) . ' ' . ($appointment->memberdetail->last_name ?? $user_dtl->last_name);
                $gender = $appointment->memberdetail->gender ?? $user_dtl->gender;
            }
            // Prepare data for PDF generation
            $data = [
                'dr_name' => $doctor_dtl->name,
                'dr_email' => $doctor_dtl->email,
                'dr_mobile' => $doctor_dtl->mobile,
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
            $pdfFileName_prs = 'prescriptions_' . $appointment->id . '.pdf';
            $pdfPath_prs = storage_path('app/public/prescriptions/' . $pdfFileName_prs);

            // Save the PDF file to storage
            $pdf_prs->save($pdfPath_prs);

            // Generate the full URL for the saved PDF file
            $PrsUrl = url('storage/prescriptions/' . $pdfFileName_prs);

            return response()->json([
                'status' => true,
                'appointments' => [$appointment], // Wrap the appointment in an array
                'pdf_url' => url('project/storage/app/public/invoices/invoice_' . $appointment->id . '.pdf'),
                'prescription_url' => url('project/storage/app/public/prescriptions/prescriptions_' . $appointment->id . '.pdf')
            ]);
        }
        else{
        return response()->json([
            'status' => true,
            'appointments' => [$appointment], // Wrap the appointment in an array
            'pdf_url' => url('project/storage/app/public/invoices/invoice_' . $appointment->id . '.pdf'),
            'prescription_url' => Null
        ]);
        }
    }



    
    public function rating(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required',
            // 'review' => 'required'
        ]);
        $a = Rating::where('booking_id', $request->booking_id)->where('status',1)->first();
        $booking = Booking::where('id', $request->booking_id)->first();
        if ($a) {
            $a->user_id = auth()->user()->id;
            $a->booking_id = $request->booking_id;
            $a->doctor_id = $booking->doctor_id;
            $a->rating = $request->rating;
            $a->review = $request->review;
            $a->status = 1;
            $a->save();
        }
        else{
            $a = new Rating();
            $a->user_id = auth()->user()->id;
            $a->booking_id = $request->booking_id;
            $a->doctor_id = $booking->doctor_id;
            $a->rating = $request->rating;
            $a->review = $request->review;
            $a->status = 1;
            $a->save();
        }
        return response()->json(['status' => true,'message' => 'Rating added successfully', 'member' => $a], 201);
    }



    // Get History of Appointments
    public function getHistoryCancel(Request $request)
    {
        // Get the appointments for the authenticated user
        $appointments = Booking::where('user_id', auth()->user()->id)->with('memberdetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->orderByRaw("STR_TO_DATE(schedule_time, '%h:%i %p') desc")
            ->where('status',2)
            ->get();
    
        // Retrieve the doctor for each appointment
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            // Get the doctor associated with this appointment
            $doctor = User::find($appointment->doctor_id);
            
            if ($doctor) {

                $doctor->image = asset('content/doctor/' . $doctor->image);
                // Get the doctor's specialisation IDs from UserInformation
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                // If specialisations exist, fetch the corresponding names
                if ($doctorprofile && $doctorprofile->specialisations) {
                    // Split the specialisations string by "|" to get an array of IDs
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
    
                    // Query the master_specialisations table for the names of these specialisations
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name') // Only fetch the names
                        ->toArray();
    
                    // Add the doctor and the specialisation names to the appointment
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames; // Set specialisations after doctor
                } else {
                    // If no specialisations, set doctorprofile to an empty array or null
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }

            }


                // Now, we want to fetch the treatment name based on treatment_id
                $treatment = DB::table('treatments')
                    ->where('id', $appointment->treatment_id)
                    ->first(['treatment_name']); // Fetch treatment name based on the treatment_id
        
                // If treatment exists, add treatment name to the appointment
                if ($treatment) {
                    $appointment->treatment_name = $treatment->treatment_name;
                } else {
                    // If no treatment is found, you can set a default value or null
                    $appointment->treatment_name = null;
                }
    
            return $appointment;
        });
    
        // Return the appointments with the doctor information included
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
    }



    public function getHistoryCancelDetails(Request $request)
{
    // Get the appointment for the authenticated user
    $appointment = Booking::where('id', $request->booking_id)->with('memberdetail','userdetail')
        ->orderBy('schedule_date', 'desc')
        ->where('status', 2)
        ->first();

    if (!$appointment) {
        return response()->json(['status' => false, 'message' => 'Appointment not found'], 404);
    }

    // Get the doctor associated with this appointment
    $doctor = User::find($appointment->doctor_id);
    
    // Get the clinic associated with this appointment
    $clinic = UserEstablishmentClinic::where('id', $appointment->clinic_id)->first();
    
    if ($doctor) {

        $doctor->image = asset('content/doctor/' . $doctor->image);
        // Get the doctor's specialisation IDs from UserInformation
        $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
        
        // If specialisations exist, fetch the corresponding names
        if ($doctorprofile && $doctorprofile->specialisations) {
            $specialisationIds = explode('|', $doctorprofile->specialisations);
            $specialisationNames = \DB::table('master_specialsations')
                ->whereIn('id', $specialisationIds)
                ->pluck('name')
                ->toArray();
            
            // Set the doctor and specialisations
            $appointment->doctor = $doctor;
            $appointment->doctorprofile = $specialisationNames;
        } else {
            $appointment->doctor = $doctor;
            $appointment->doctorprofile = [];
        }


       
    }


        // Now, we want to fetch the treatment name based on treatment_id
        $treatment = DB::table('treatments')
        ->where('id', $appointment->treatment_id)
        ->first(['treatment_name']); // Fetch treatment name based on the treatment_id

        // If treatment exists, add treatment name to the appointment
        if ($treatment) {
            $appointment->treatment_name = $treatment->treatment_name;
        } else {
            // If no treatment is found, you can set a default value or null
            $appointment->treatment_name = null;
        }

    // Add the clinic data to the appointment
    $appointment->clinic = $clinic ? $clinic : null;

    // Return the appointment with doctor and clinic information
    return response()->json(['status' => true, 'appointments' => [$appointment]], 200);
}


//shubham
        public function getCancelReason_old(Request $request)
        {
            $validated = $request->validate([
                'cancel_by' => 'required'
            ]);

            $a = Booking::find($request->booking_id);
            $a->cancel_by = $request->cancel_by;
            $a->cancel_other = $request->cancel_other;
            $a->status = 2;
            $a->save();

            return response()->json(['status' => true,'message' => 'Booking Cancel successfully', 'reason' => $a], 201);
        }


        public function getCancelReason11111(Request $request)
    {
        $validated = $request->validate([
            'cancel_by' => 'required',
            // 'booking_id' => 'required|exists:bookings,id',
        ]);

        // Fetch booking
        $booking = Booking::find($request->booking_id);

        if (!$booking) {
            return response()->json(['status' => false, 'message' => 'Booking not found'], 404);
        }

        // Razorpay credentials
        $key_id = 'rzp_live_RCGVCm3xkWYVRs';
        $key_secret = '73XN1JZWDZSEf69PcIzHz2Fd';
        $payment_id = $booking->payid; // Razorpay payment ID stored in DB
        // print_r($payment_id); die;

        // Refund URL
        $url = "https://api.razorpay.com/v1/payments/{$payment_id}/refund";

        // Refund amount in paise (optional: full refund if not passed)
        $refundData = [];
        if ($booking->total_amount) {
            $refundData['amount'] = (int)($booking->total_amount * 100);
        }
        // print_r($refundData); die;
        try {
            // Call Razorpay API
            $response = Http::withBasicAuth($key_id, $key_secret)
                            ->post($url, $refundData);

            if ($response->successful()) {
                // Mark booking as cancelled
                $booking->cancel_by = $request->cancel_by;
                $booking->cancel_other = $request->cancel_other ?? null;
                $booking->status = 2;
                $booking->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Booking cancelled and refund successful.',
                    'refund_details' => $response->json(),
                    'booking' => $booking,
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Refund failed.',
                    'error' => $response->json(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }




    public function getCancelReason(Request $request)
    {
        // Validate input
        $request->validate([
            'cancel_by' => 'required|string',
            // 'booking_id' => 'required|exists:bookings,id',
        ]);

        // Fetch booking
        $booking = Booking::find($request->booking_id);
        if (!$booking) {
            return response()->json(['status' => false, 'message' => 'Booking not found'], 404);
        }
        $a = Booking::find($request->booking_id);
        $a->cancel_by = $request->cancel_by;
        $a->cancel_other = $request->cancel_other;
        $a->cancel_date = date('Y-m-d h:ia');
        $a->status = 2;
        $a->save();
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


        // Reschedule Booking



    public function rescheduleBooking(Request $request)
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


        public function BookingDateFilterUser(Request $request)
        {
            $userId = auth()->user();
            $user = UserProfile::where('id', $userId->id)->first();
            
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found'], 404);
            }
        
            // Hardcoded dentist date for 2024-12-18
            $dentistdate = $request->dentistdate;
            
            // Query bookings with status 0 or 4 for the specific date
            $query = Booking::whereIn('status', [4])->where('user_id',$userId->id)
                            ->whereDate('schedule_date', $dentistdate)->with('memberdetail','userdetail');
            
            // Get the appointments ordered by schedule date in descending order
            $appointments = $query->orderBy('schedule_date', 'asc')
            ->orderByRaw("STR_TO_DATE(schedule_time, '%h:%i %p') asc")->get();
            
            // Check if there are any appointments for the given date
            if ($appointments->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No appointments found for the given date'], 404);
            }
        
            // Map the appointments to include additional details
            $appointmentsWithDoctor = $appointments->map(function ($appointment) {
                $doctor = User::find($appointment->doctor_id);
                $clinic = UserEstablishmentClinic::find($appointment->clinic_id);
                
                $appointmentDetails = [
                    'id' => $appointment->id,
                    'schedule_date' => $appointment->schedule_date,
                    'schedule_time' => $appointment->schedule_time,
                    'consultation_type' => $appointment->consultation_type,
                    'appointment_type' => $appointment->appointment_type,
                    'symptoms' => $appointment->symptoms,
                    'subtotal' => $appointment->subtotal,
                    'gst' => $appointment->gst,
                    'status' => $appointment->status,
                    'payment_mode' => $appointment->payment_mode,
                    'startflag' => 0,
                    'memberdetail' => $appointment->memberdetail,
                    'userdetail' => $appointment->userdetail,
                ];
        
                // Add doctor details if available
                if ($doctor) {
                    $appointmentDetails['doctor'] = [
                        'name' => $doctor->name,
                        'image' => asset('content/doctor/' . $doctor->image),
                        'specialisations' => [],
                    ];
        
                    // Fetch doctor specialisations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
                        $appointmentDetails['doctor']['specialisations'] = $specialisationNames;
                    }
                }
        
                // Add treatment details if available
                $treatment = DB::table('treatments')->where('id', $appointment->treatment_id)->select(['treatment_name', 'average_duration'])->first();
                if ($treatment) {
                    $appointmentDetails['treatment'] = [
                        'name' => $treatment->treatment_name,
                        'average_duration' => $treatment->average_duration,
                    ];
                }
        
                // Add clinic details if available
                if ($clinic) {
                    $appointmentDetails['clinic'] = [
                        'name' => $clinic->clinic_name,
                        'address' => $clinic->address,
                        'image' => asset('content/clinic/' . $clinic->image),
                    ];
                }
        
                // Add start flag logic
                $currenttime = time();
                $starttime = strtotime(date('Y-m-d H:i:s', strtotime($appointment->schedule_date . ' ' . $appointment->schedule_time)));
                if ($currenttime >= $starttime) {
                    $appointmentDetails['startflag'] = 1;
                }
        
                return $appointmentDetails;
            });
        
            // Return the response with appointments
            return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
        }
        
        
        
        
        public function PackTreatmentList(Request $request) {
            $user_id = auth()->user()->id;
            
            $checkany = UserSubscriptionsPack::where('user_id',$user_id)->where('status', 1)->get();
            if ($checkany->count() > 0) {
                foreach ($checkany as $k) {
                    $bookingCountmm = 0;
                    $totalprovided = 0;
                    $matchingRow = UserPremiumAddonsPack::where('user_id',$user_id)->where('subscription_id',$k->id)->where('pack_id',$k->pack_id)->get();
                    if ($matchingRow->count() > 0) {
                        $mmquantity = 0;
                        $mmsubscription_id = 0;
                        $i = 0;
                        foreach ($matchingRow as $mm) {
                            $totalprovided += $mm->quantity;
                            if ($i == 0) {
                                $mmsubscription_id = $mm->subscription_id;
                            }
                            $bookingCount = Booking::where('treatment_id', $mm->treatment_id)
                            ->where('plan_id', $mm->subscription_id)
                            ->whereIn('status', [0, 1, 4])
                            ->count();
                            $bookingCountmm += $bookingCount;
                            $i++;
                        }
                    }
                    $fi = $totalprovided - $bookingCountmm;
                    if ($fi == 0) {
                        $k->status = 0;
                        $k->save();
                    }
                }
                
            }
            $l = PackTreatment::where('status', 1)->where('user_id', auth()->user()->link_id)->whereNotIn('id', function ($query) use ($user_id) {
                                    $query->select('pack_id')
                                          ->from('user_subscriptions_pack')
                                          ->where('user_id',$user_id)
                                          ->where('status', 1);
                                })
                                ->orderBy('id', 'ASC')->get();
        
            if ($l->isEmpty()) {
                $response = ['status' => false, 'msg' => 'Sorry ! No pack found.'];
                return response($response, 422);
            }
        
            foreach ($l as $packTreatment) {
                $packTreatment->packFeature = PackFeature::where('status', 1)
                    ->where('pack_id', $packTreatment->id)
                    ->orderBy('id', 'ASC')
                    ->get();
            }
        
            // Return the response with the pack treatment list and associated features
            $response = ['status' => true, 'msg' => 'Pack List', 'data' => $l];
            return response($response, 200);
        }
        

        

        public function TreatmentBuyNow(Request $request) {
            // Validate the request
            $validated = $request->validate([
                'amount' => 'required',
                'pack_id' => 'required',
            ]);
            $packTreatment = PackTreatment::where('status', 1)
                ->where('id', $request->pack_id)
                ->firstOrFail();
        
            $startDate = Carbon::today();
            $expiryDate = null;
        
            if ($packTreatment->type == 'Month') {
                $expiryDate = $startDate->copy()->addMonths($packTreatment->validity);
            } elseif ($packTreatment->type == 'Year') {
                $expiryDate = $startDate->copy()->addYears($packTreatment->validity);
            }
        
            $expiryDateFormatted = $expiryDate->format('Y-m-d');
        
            // Create a subscription record
            $subscription = new UserSubscriptionsPack();
            $subscription->user_id = auth()->user()->id;
            $subscription->pack_id = $request->pack_id;
            $subscription->start_date = $startDate;
            $subscription->end_date = $expiryDateFormatted;
            $subscription->payment_id = 0;
            $subscription->status = 1;
            $subscription->save();
            
            $payment = new UserPayment();
            $payment->user_id = auth()->user()->id;
            $payment->amount = $request->amount;
            $payment->payid = $request->payid ?? '';
            $payment->tax_amount = $request->tax_amount ?? 0;
            $payment->save();
            $subscription->payment_id = $payment->id;
            $subscription->save();
            $packFeatures = PackFeature::where('status', 1)
                ->where('pack_id', $packTreatment->id)
                ->orderBy('id', 'ASC')
                ->get();
        
            // Create premium add-ons for each pack feature
            foreach ($packFeatures as $feature) {
                $premiumAddon = new UserPremiumAddonsPack();
                $premiumAddon->user_id = auth()->user()->id;
                $premiumAddon->subscription_id = $subscription->id;
                $premiumAddon->pack_id = $request->pack_id;
                $premiumAddon->pack_feature_id = $feature->id;
                $premiumAddon->treatment_id = $feature->treatment_id;
                $premiumAddon->quantity = $feature->quantity;
                $premiumAddon->payment_id = $payment->id;
                $premiumAddon->save();
            }
            
            if ($request->payid) {
                $key_id = env('RAZORPAY_KEYID_TEST');
                $key_secret = env('RAZORPAY_SECRET_TEST');

                // Fetch payment details
                $paymentInfo = Http::withBasicAuth($key_id, $key_secret)
                    ->get("https://api.razorpay.com/v1/payments/{$request->payid}");

                if (!$paymentInfo->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid Razorpay Payment ID',
                        'razorpay_response' => $paymentInfo->json()
                    ], 400);
                }

                if ($paymentInfo['status'] === 'authorized') {
                    $captureAmount = $paymentInfo['amount'];

                    $captureResponse = Http::withBasicAuth($key_id, $key_secret)
                        ->post("https://api.razorpay.com/v1/payments/{$request->payid}/capture", [
                            'amount' => $captureAmount
                        ]);

                    if (!$captureResponse->successful()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Payment capture failed',
                            'razorpay_response' => $captureResponse->json()
                        ], 400);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Treatment BuyNow successfully',
                'treatment' => $payment
            ], 201);
        }
        
        

        public function PackTreatmentActive(Request $request) {
            $list = UserSubscriptionsPack::where('status', 1)
                ->where('user_id', auth()->user()->id)
                ->orderBy('end_date', 'asc')
                ->get();
            
            if (!$list) {
                $response = ['status' => false, 'msg' => 'Sorry! No active pack found.'];
                return response($response, 422);
            }
            if ($list->count() > 0) {
                foreach ($list as $l) {
                    $l->payment = UserPayment::where('id', $l->payment_id)
                        ->select('amount', 'tax_amount', 'payment_status', 'payment_method')
                        ->first();
                
                    $l->user_prem = UserPremiumAddonsPack::where('subscription_id', $l->id)->with('featurelist')
                        ->select('pack_feature_id', 'treatment_id', 'quantity') ->withCount([
                            'bookings as used_quantity' => function ($query) use ($l) {
                                $query->where('plan_id', $l->id)
                                      ->whereColumn('treatment_id', 'user_premium_addons_pack.treatment_id')
                                      ->whereIn('status', [0, 1, 4]);
                            }
                        ])
                        ->get();
                    $l->quantity = UserPremiumAddonsPack::where('subscription_id', $l->id)
                        ->select('quantity')
                        ->sum('quantity');

                    $l->used = Booking::where('plan_id', $l->id)
                                ->whereIn('status', [0, 1, 4])
                                ->count();
                    $userProfile = UserProfile::where('id', $l->user_id)->first();
                    $userName = $userProfile ? $userProfile->name : 'Unknown';
                
                    $userPack = PackTreatment::where('id', $l->pack_id)->first();
                    
                    $userPackName = $userPack ? $userPack->name : 'Unknown';
                    $userPackType = $userPack ? $userPack->type : 'Unknown';
                    
                    $l->user_id = $userName;
                    $l->pack_id = $userPackName;
                    $l->type = $userPackType; 
                }
            }
            
        
            $response = [
                'status' => true,
                'msg' => 'Active Pack found',
                'data' => [
                    'subscription' => $list,
                ],
            ];
        
            return response($response, 200);
        }

    public function getHistoryall(Request $request)
    {
        // Get the appointments for the authenticated user
        $appointments = Booking::where('user_id', auth()->user()->id)
            ->orderBy('schedule_date', 'desc')
            // ->where('status',1)
            ->get();
    
        // Retrieve the doctor for each appointment
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            // Get the doctor associated with this appointment
            $doctor = User::find($appointment->doctor_id);
           
            if ($doctor) {

                $doctor->image = asset('content/doctor/' . $doctor->image);
                // Get the doctor's specialisation IDs from UserInformation
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                // If specialisations exist, fetch the corresponding names
                if ($doctorprofile && $doctorprofile->specialisations) {
                    // Split the specialisations string by "|" to get an array of IDs
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
    
                    // Query the master_specialisations table for the names of these specialisations
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name') // Only fetch the names
                        ->toArray();
    
                    // Add the doctor and the specialisation names to the appointment
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames; // Set specialisations after doctor
                } else {
                    // If no specialisations, set doctorprofile to an empty array or null
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }

            }

                // Now, we want to fetch the treatment name based on treatment_id
                $treatment = DB::table('treatments')
                    ->where('id', $appointment->treatment_id)
                    ->first(['treatment_name']); // Fetch treatment name based on the treatment_id
        
                // If treatment exists, add treatment name to the appointment
                if ($treatment) {
                    $appointment->treatment_name = $treatment->treatment_name;
                } else {
                    // If no treatment is found, you can set a default value or null
                    $appointment->treatment_name = null;
                }
                if ($appointment->status == 2 || $appointment->status == 3 ) {
                    $appointment->invoiceurl = '';
                } else $appointment->invoiceurl = $appointment->status == 2 ? '' : route('invoiceuser',$appointment->id);
                
                $appointment->refundinvoiceurl = $appointment->status == 2 ? route('invoiceuserrefund',$appointment->id) : null;
            return $appointment;
        });
    
        // Return the appointments with the doctor information included
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
    }
        
    public function checktimeslot($id)
    {
        $response = '';
        $key1 = UserInformation::where('user_id', $id)
            ->first();
        if ($key1) {
            $dateslots = array();
            $slot = array(); 
            $apointment_date = date('Y-m-d');
            $total_minutes = 60;
            $today_date = strtotime(date('Y-m-d'));
            $given_date = strtotime($apointment_date);
            $id = $key1->user_id;
            if ($given_date >= $today_date) {
                $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                $already_in_time_check = array();
                $working_time = json_decode($key1->doctor_availability, true);
                if (!empty($working_time)) {
                    $working_time = json_decode($key1->doctor_availability, true);
                    $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                    if (!empty($schedule)) {
                        if (isset($schedule['morning'])) {
                            $fistslots = explode(' - ', $schedule['morning'][0]);
                            if (isset($fistslots[0]) && isset($fistslots[1])) {
                                $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                $interval = new DateInterval('PT'.$total_minutes.'M');
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
                                $interval = new DateInterval('PT'.$total_minutes.'M');
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
                if ($id == 34) {
                    dd($slot);
                }
                $response=1;
            }
        }
        return $response;
    }

    public function coupancodelist(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $blogs = SeasonalOffers::where(function ($query) {
            $query->where('user_id', auth()->user()->link_id)
              ->where('created_by', 1);
        })
        ->where('status',1)
        ->where('treat_package_id',$request->treatment_id)
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today) 
        ->orderBy('discount','DESC')
        ->get();

        
        $blogs->each(function ($blog) {
            $treatment = DB::table('treatments')
                ->where('id', $blog->treat_package_id)
                ->first(['treatment_name']);
    
            if ($treatment) {
                $blog->treatment_name = $treatment->treatment_name;
            } else {
                $blog->treatment_name = null;
            }
    
            $blog->image = asset('content/SeasonalOffers') . '/' . $blog->image;
        });
    
        return response()->json(['status' => true, 'seasonal' => $blogs], 200);
    }

    public function referBenefitadd($user_id,$link_id)
    {
        $getrefree = ReferCodeHistory::where('refer_to_uid',$user_id)->where('refer_to_uid_wallet_add_flag',1)->where('refer_by_uid_wallet_add_flag',0)->first();
        if ($getrefree) {
            $user = UserProfile::where('id',$user_id)->where('status',1)->first();
            if ($user) {
                $points = 0;
                $getrefer = User::where('id',$link_id)->where('status',1)->first();

                if ($getrefer) {
                    if ($getrefer->UserInformationDetails->referred_after_booking_points > 0) {
                        $points = $getrefer->UserInformationDetails->referred_after_booking_points;
                    }
                }

                if ($points > 0) {
                    $old_points = $user->wallet;
                    $new_points = $old_points+$points;
                    $new = new UserPayment();
                    $new->user_id = $user->id;
                    $new->type = 3;
                    $new->booking_id = $getrefree->id;
                    $new->action = 'credit';
                    $new->amount = $points;
                    $new->old_balance = $old_points;
                    $new->payment_status = 'completed';
                    $new->payment_method = 'online';
                    $new->new_balance = $new_points;
                    $new->status = 1;
                    $new->trxn_id =  time().rand();
                    $new->save();
                    if ($new) {
                        $body = "Hi ".$user->name.",Great news! your referral benefit added. As a reward, you’ve earned '.$points.' loyalty points in your points!Keep sharing your referral code to earn even more rewards!";
                        $user->wallet = $new_points;
                        $user->save();
                        $getrefree->refer_by_uid_wallet_add_flag = 1;
                        $getrefree->save();
                        $msgarray = array("title"=>"🎉 You've Earned Loyalty Points from a Referral!",
                                          "msg"=>$body,

                                         "msg2"=>$body
                                   );
                        $this->async_to_all($msgarray,$user->id,$user->id,'refercodebenefit2');
                    }
                }
            }
        }
        return true;
    }

        
        
}
