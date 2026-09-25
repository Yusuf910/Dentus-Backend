<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Invitation;
use App\Models\UserInformation;
use App\Models\UserEstablishmentClinic;
use App\Models\UserEstablishmentGallery;
use App\Models\UserEstablishmentHoliday;
use App\Models\MasterLangauage;
use App\Models\MasterSpecialsation;
use App\Models\MasterServices;
use App\Models\Subscription;
use App\Models\MasterDegree;
use App\Models\Generalsetting;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DB;
use HasApiTokens;
use App\Models\UserModels\Booking;
use Carbon\Carbon;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;
use App\Models\UserModels\UserPayment;
use App\Models\SocialConnectView;
use App\Models\Prescription;

use App\Models\UserModels\UserSubscriptionsPack;
use App\Models\UserModels\UserPremiumAddonsPack;
use App\Models\MyNotification;
use App\Models\UserModels\MedicalRecord;
use App\Models\Treatment;
use App\Models\DoctorClinicSlot;
use App\Models\UserModels\Member;
class DoctorHistoryController extends Controller
{
    public function getDoctorRatings(Request $request) {
        $doctorId = auth()->user()->id;

        if (!$doctorId) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor ID is required.',
            ], 400);
        }

        $doctor = User::where('id', $doctorId)
            ->with([
                'ratings.userProfile' => function ($query) {
                    $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
                }
            ])
            ->withCount([
                'ratings',
                'bookings as active_bookings_count' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->withAvg('ratings', 'rating')
            ->first();

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found.',
            ], 404);
        }

        $ratings = $doctor->ratings()
        ->selectRaw('rating, COUNT(*) as count')
        ->groupBy('rating')
        ->pluck('count', 'rating')
        ->toArray();

        $starCounts = [
            '5_star' => $ratings[5] ?? 0,
            '4_star' => $ratings[4] ?? 0,
            '3_star' => $ratings[3] ?? 0,
            '2_star' => $ratings[2] ?? 0,
            '1_star' => $ratings[1] ?? 0,
        ];

        $totalReviews = array_sum($starCounts);

        $ratingsList = $doctor->ratings()
        ->with('userProfile:id,name,image')
        ->orderBy('created_at', 'desc')
        ->get();

        $ratingsList = $doctor->ratings()
            ->with('userProfile:id,name,image')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                $rating->human_date = Carbon::parse($rating->created_at)->diffForHumans();
                return $rating;
            });


        return response()->json([
            'status' => true,
            'total_reviews' => $totalReviews,
            'rating_counts' => $starCounts,
            'ratings_list' => $ratingsList,
            'path'=>asset('content/user/').'/'
        ]);
    }

    public function getDoctorEarningStats(Request $request) {
        $doctorId = auth()->user()->id;

        if (!$doctorId) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor ID is required.',
            ], 400);
        }

        $totalBookings = Booking::where('doctor_id', $doctorId)->count();

        $rebookCount = Booking::select('user_id')
            ->where('doctor_id', $doctorId)
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $upcomingBookings = Booking::where('doctor_id', $doctorId)
            ->whereIn('status', [0, 4])
            ->count();

        $pendingBookings = Booking::where('doctor_id', $doctorId)
            ->where('status', 0)
            ->count();

        $totalAppDownloads = UserProfile::where('link_id', $doctorId)->count();

        $totalSocialConnectViews = SocialConnectView::where('blog_by_id', $doctorId)->count();
        $totalEarnings = Booking::where('doctor_id', $doctorId)
            ->where('status', 1)
            ->sum('total_amount');

        $treatments = PackTreatment::where('user_id', $doctorId)
            ->where('user_type', 2)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        $packTotal = UserSubscriptionsPack::whereIn('pack_id', $treatments)
            ->where('type', '1')
            ->join('user_payments', 'user_payments.id', '=', 'user_subscriptions_pack.payment_id')
            ->sum('user_payments.amount');
        $grandTotal = $totalEarnings + $packTotal;
        return response()->json([
            'status' => true,
            'total_bookings' => $totalBookings,
            'rebook_count' => $rebookCount,
            'upcoming_bookings' => $upcomingBookings,
            'pending_bookings' => $pendingBookings,
            'total_app_downloads' => $totalAppDownloads,
            'total_social_connect_views' => $totalSocialConnectViews,
            'referfriend' => 0,
            'total_earning'=>round($grandTotal)
        ]);
    }

    public function getDoctorEarningHistory(Request $request)
    {

        $doctorId = auth()->user()->id;

        $query = Booking::where('doctor_id', $doctorId)
            ->where('status', 1)
            ->with(['userDetail' => function ($query) {
                $query->select('id', 'name');
            }])
            ->orderBy('created_at', 'desc');

        if ($request->condition === 'last10') {
            $query->limit(10);
        } elseif ($request->condition === 'lastWeek') {
            $query->whereBetween('created_at', [now()->subWeek(), now()]);
        } elseif ($request->condition === 'lastMonth') {
            $query->whereBetween('created_at', [now()->subMonth(), now()]);
        } elseif ($request->condition === 'custom' && $request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $bookings = $query->get();

        $cumulativeTotal = $bookings->sum('total_amount');
        $treatments = PackTreatment::where('user_id', $doctorId)
            ->where('user_type', 2)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        $salesQuery = UserSubscriptionsPack::whereIn('pack_id', $treatments)->where('type','1')
            ->join('user_payments', 'user_payments.id', '=', 'user_subscriptions_pack.payment_id');
            
        if ($request->condition === 'last10') {
            $salesQuery->limit(10);
        } elseif ($request->condition === 'lastWeek') {
            $salesQuery->whereBetween('created_at', [now()->subWeek(), now()]);
        } elseif ($request->condition === 'lastMonth') {
            $salesQuery->whereBetween('created_at', [now()->subMonth(), now()]);
        } elseif ($request->condition === 'custom' && $request->has(['start_date', 'end_date'])) {
            $salesQuery->whereBetween('created_at', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }
        

        $packSales = $salesQuery->get();
        $bookingFormatted = $bookings->map(function ($booking) {
            return [
                'type' => 'booking',
                'id' => $booking->id,
                'user_name' => $booking->userDetail->name ?? 'N/A',
                'total_amount' => $booking->total_amount,
                'created_at' => $booking->created_at,
                'created_attime' => $booking->created_at->diffForHumans(),
                'session_no' => $booking->session_no,
            ];
        });

        $packFormatted = $packSales->map(function ($pack) {
            return [
                'type' => 'pack_sale',
                'id' => $pack->id,
                'user_name' => $pack->userdetail->name ?? 'N/A',
                'total_amount' => $pack->amount,
                'created_at' => $pack->created_at,
                'created_attime' => Carbon::parse($pack->created_at)->diffForHumans(),
                'session_no' => null, // not applicable
            ];
        });
        $merged = $bookingFormatted->merge($packFormatted)->sortByDesc('created_at')->values();
        if ($request->condition === 'last10') {
            $merged = $merged->take(10);
        }
        $total = round($merged->sum('total_amount'));//$bookingFormatted->sum('total_amount') + $packFormatted->sum('total_amount');
        $response = [
            'status'=>true,
            'total' => $total,
            'data' => $merged/*$bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'user_name' => $booking->userDetail->name ?? 'N/A',
                    'total_amount' => $booking->total_amount,
                    'created_at' => $booking->created_at,
                    'created_attime' => $booking->created_at->diffForHumans(),
                    'session_no'=>$booking->session_no,
                ];
            }),*/
        ];

        return response()->json($response, 200);
    }

    public function getDoctorEarningHistoryGraph(Request $request)
    {
        $request->validate([
            'doctor_type' => 'required|string',
        ]);
        $currentYear = Carbon::now()->year;
        $doctorType = $request->doctor_type;
        $parentDoctorId = auth()->user()->id;

        if ($doctorType === 'All') {
            $doctorIds = User::where('parent_id', $parentDoctorId)->orWhere('id', $parentDoctorId)->pluck('id');
        } else {
            $doctorIds = User::where('id', $doctorType)->pluck('id');
        }

        $totalAppointments = Booking::whereIn('doctor_id', $doctorIds)->count();
        $onlineAppointments = Booking::whereIn('doctor_id', $doctorIds)
            ->whereIN('consultation_type', ['Online Consultation','online'])
            ->count();
        $inPersonAppointments = Booking::whereIn('doctor_id', $doctorIds)
            ->where('consultation_type', 'In-Clinic Consultation')
            ->count();

        $previousOnlineCount = Booking::whereIn('doctor_id', $doctorIds)
            ->where('consultation_type', ['Online Consultation','online'])
            ->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()])
            ->count();

        $previousInPersonCount = Booking::whereIn('doctor_id', $doctorIds)
            ->where('consultation_type', 'In-Clinic Consultation')
            ->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()])
            ->count();

        $onlineSignal = $onlineAppointments > $previousOnlineCount ? 1 : 0;
        $inPersonSignal = $inPersonAppointments > $previousInPersonCount ? 1 : 0;

        $monthlyData = Booking::selectRaw("
                MONTH(created_at) as month,
                SUM(CASE WHEN consultation_type IN ('Online Consultation', 'online') THEN 1 ELSE 0 END) as online,
                SUM(CASE WHEN consultation_type = 'In-Clinic Consultation' THEN 1 ELSE 0 END) as in_clinic
            ")
            ->whereIn('doctor_id', $doctorIds)
            ->whereYear('created_at', $currentYear) 
            ->groupBy('month')
            ->get();

        $graphDataset = array_fill(1, 12, ['online' => 0, 'in_clinic' => 0]);

        foreach ($monthlyData as $data) {
            $graphDataset[$data->month] = [
                'online' => $data->online,
                'in_clinic' => $data->in_clinic,
            ];
        }

        return response()->json([
            'statu'=>true,
            'total' => $totalAppointments,
            'online' => [
                'count' => $onlineAppointments,
                'signal' => $onlineSignal,
            ],
            'in_person' => [
                'count' => $inPersonAppointments,
                'signal' => $inPersonSignal,
            ],
            'graph' => $graphDataset,
        ]);
    }

    public function listofegisterpatient(Request $request)
    {
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            $doctorIds = [$authUser->id];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
            $doctorIds[] = $authUser->id;
        }
        $userid = array();
        $memberid = array();
        $userlist = [];
        $a = Booking::with(['userdetail', 'memberdetail'])
            ->whereIn('doctor_id', $doctorIds)
            ->get();
        if ($a) {
            foreach ($a as $key) {
                if ($key->user_id == $key->member_id || ($key->member_id == 0)) {
                    if (!in_array($key->user_id, $userid)) {
                        array_push($userid, $key->user_id);
                        $userlist[] = [
                            'is_member' => false,
                            'user_details' => $key->userdetail,
                            'member_details' => null, 
                        ];
                    }
                } else {
                    if (!in_array($key->member_id, $memberid)) {
                        array_push($memberid, $key->member_id);
                        $userlist[] = [
                            'is_member' => true,
                            'user_details' => $key->userdetail,
                            'member_details' => $key->memberdetail, 
                        ];
                    }
                }
            }
        }



        return response()->json([
            'status'=>true,
            'userlist' => $userlist,
            'path'=> asset('content/user').'/',
            'member'=>asset('project/public/member_images').'/'
        ]);
    }

    public function bookinghistory(Request $request)
    {
        if ($request->type == 'member') {
            $appointments = Booking::where('member_id', $request->id)
            ->orderBy('schedule_date', 'desc')
            ->with(['prescriptions'])
            ->get();
        } else
        $appointments = Booking::where('user_id', $request->id)
            ->orderBy('schedule_date', 'desc')
            ->with(['prescriptions'])
            ->get();
    
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            $doctor = User::find($appointment->doctor_id);
           
            if ($doctor) {

                $doctor->image = asset('content/doctor/' . $doctor->image);
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                if ($doctorprofile && $doctorprofile->specialisations) {
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
    
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name')
                        ->toArray();
    
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames;
                } else {
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }

            }

                $treatment = DB::table('treatments')
                    ->where('id', $appointment->treatment_id)
                    ->first(['treatment_name']); // Fetch treatment name based on the treatment_id
        
                if ($treatment) {
                    $appointment->treatment_name = $treatment->treatment_name;
                } else {
                    $appointment->treatment_name = null;
                }
                $appointment->invoiceurl = route('invoiceuser',$appointment->id);
                $validPrescriptions = $appointment->prescriptions->filter(function ($prescription) {
                    return $prescription->status === 1;
                })->map(function ($prescription) use ($appointment) {
                    $prescription->viewurl = route('prescriptionview', $prescription->id);
                    return $prescription;
                })->values();
                $appointment->prescriptionlist = $validPrescriptions;
                unset($appointment->prescriptions);
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id != $appointment->member_id && $appointment->member_type == 'self') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) 
                {
                    $member = Member::find($appointment->member_id);
                    if ($member) {
                        $member->image = asset('project/public/member_images/' . $member->image);
                        $appointment->member = $member; // Add member details to the appointment
                    } else {
                        $appointment->member = null; // If no member found, set to null
                    }

                    $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                    $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                    $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); 
                    
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
                    $appointment->userMedical_xray = $userMedical_xray;
                    $appointment->userMedical_lab = $userMedical_lab;
                    $appointment->userMedical_prescription = $userMedical_prescription;
                } else {
                        $appointment->member = null;

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get();
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get();
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get();
                        
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
                        $appointment->userMedical_xray = $userMedical_xray;
                        $appointment->userMedical_lab = $userMedical_lab;
                        $appointment->userMedical_prescription = $userMedical_prescription;
                }
                return $appointment;
        });
    
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor], 200);
    }

    public function precriptionhistory(Request $request)
    {
        if ($request->type == 'member') {
            $appointments = Booking::where('member_id', $request->id)
            ->orderBy('schedule_date', 'desc')
            ->with(['prescriptions'])
            ->get();
        } else
        $appointments = Booking::where('user_id', $request->id)
            ->orderBy('schedule_date', 'desc')
            ->get();

        $appointmentsWithDetails = $appointments->filter(function ($appointment) {
            $validPrescriptions = $appointment->prescriptions->filter(function ($prescription) {
                return $prescription->status === 1;
            });

            return $validPrescriptions->isNotEmpty();
        })->map(function ($appointment) {
            $validPrescriptions = $appointment->prescriptions->filter(function ($prescription) {
                return $prescription->status === 1;
            })->map(function ($prescription) use ($appointment) {
                $prescription->viewurl = route('prescriptionview', $prescription->id);
                return $prescription;
            })->values();

            $appointment->invoiceurl = route('invoiceuser', $appointment->id);

            $appointment->prescriptionlist = $validPrescriptions;
            unset($appointment->prescriptions);
            return $appointment;
        });

        return response()->json(['status' => true, 'appointments' => $appointmentsWithDetails->values()], 200);
    }

    public function prescriptionview(Request $request,$id)
    {
        $booking = Prescription::find($id);
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
        return view('pdf.precription',compact('data'));
    }

    public function loyalitypoints(Request $request)
    {
        $user = UserProfile::where('id', $request->id)
            ->with([
                'userPayments' => function ($query) {
                    $query->orderBy('created_at', 'desc')
                        ->select('id', 'user_id', 'amount', 'action', 'invoice_no', 'trxn_id', 'created_at','type');
                }
            ])
            ->first();
        $credits = $user->userPayments->where('action', 'credit')->whereIN('type',[2,3])->values();
        $debits = $user->userPayments->where('action', 'debit')->where('type',2)->values();    
        $response = [
            'status' => true,
            'user' => $user->only(['id', 'wallet']),
            'member'=>'Ellite Member',
            'image'=>asset('content/user').'/'.$user->image,
            'credits' => $credits,
            'debits' => $debits,
            ];
        return response()->json($response, 200);
    }

    public function PackTreatmentActive_old(Request $request) 
    {
        $l = UserSubscriptionsPack::where('status', 1)
            ->where('user_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$l) {
            $response = ['status' => false, 'msg' => 'Sorry! No active pack found.'];
            return response($response, 422);
        }
    
        $payment = UserPayment::where('id', $l->payment_id)
            ->select('amount', 'tax_amount', 'payment_status', 'payment_method')
            ->first();
    
        $user_prem = UserPremiumAddonsPack::where('subscription_id', $l->id)
            ->select('pack_feature_id', 'treatment_id', 'quantity')
            ->get();
        $quantity = UserPremiumAddonsPack::where('subscription_id', $l->id)
            ->select('quantity')
            ->sum('quantity');
        $used = Booking::where('plan_id', $l->subscription_id)
                    ->whereIn('status', [0, 1, 4])
                    ->count();
        $userProfile = UserProfile::where('id', $l->user_id)->first();
        $userName = $userProfile ? $userProfile->name : 'Unknown';
    
        $userPack = PackTreatment::where('id', $l->pack_id)->first();
        
        $userPackName = $userPack ? $userPack->name : 'Unknown';
        $userPackType = $userPack ? $userPack->type : 'Unknown';
        
        $l->username = $userName;
        $l->packagename = $userPackName;
        $l->type = $userPackType; 
    
        $response = [
            'status' => true,
            'msg' => 'Active Pack found',
            'data' => [
                'subscription' => $l,
                'payment' => $payment,
                'premium_addons' => $user_prem,
                'quantity'=>$quantity,
                'used'=>$used
            ],
        ];
    
        return response($response, 200);
    }


    public function PackTreatmentActive(Request $request) {
        $list = UserSubscriptionsPack::where('status', 1)
        ->where('user_id', $request->id)
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


    public function listNotification(Request $request)
    {
        MyNotification::where('user_id', auth()->user()->id)->where('read', 0)
       ->update([
           'read' => 1
        ]);
        $list = MyNotification::limit(37)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->whereNOTIN('status',[2])->get();
        $mainlist = array();
        foreach ($list as $key) {
            $created_at = '';
            $updated_at = '';

            if ($key->created_at) {
                $created_at = Carbon::parse($key->created_at)->format('d/m/Y H:i a');
            }
            if ($key->updated_at) {
                $updated_at = Carbon::parse($key->updated_at)->format('d/m/Y H:i a');
            }
            $mainlist[] = array("id"=> $key->id,
                                "user_id"=> $key->user_id,
                                "prod_id"=> $key->prod_id,
                                "title"=> $key->title,
                                "notification"=> $key->notification,
                                "notification_type"=> $key->notification_type,
                                "image"=> $key->image,
                                "status"=> $key->status,
                                "read"=> $key->read,
                                "created_at"=> $created_at,
                                "updated_at"=> $updated_at,);
        }
        $response = ['status' => true, 'list' => $mainlist,"path"=>asset('project/public/notification')];
        return response($response, 200);
    }

    public function bookingList(Request $request) {
        $doctorId = auth()->user()->id;
        $appointments = Booking::where('id', 0)
            ->orderBy('schedule_date', 'desc')
            ->get();
        $dateFilter = $request->date_filter ?? 0;
        if ($dateFilter) {
            $dateLimit = now()->subDays($dateFilter);
        }
        if (!$doctorId) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor ID is required.',
            ], 400);
        }
        if ($request->condition == 'earning') {
            if ($dateFilter > 0) {
                $appointments = Booking::where('doctor_id', $doctorId)
                    ->orderBy('schedule_date', 'desc')
                    ->where('status', 1)
                    ->where('schedule_date', '>=', $dateLimit)
                    ->get();    
            } else
            $appointments = Booking::where('doctor_id', $doctorId)
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->get();
        }
        if ($request->condition == 'allbooking') {
            if ($dateFilter > 0) {
                $appointments = Booking::where('doctor_id', $doctorId)
                    ->orderBy('schedule_date', 'desc')
                    ->where('schedule_date', '>=', $dateLimit)
                    ->get();  
            } else
            $appointments = Booking::where('doctor_id', $doctorId)
            ->orderBy('schedule_date', 'desc')
            ->get();
        }
        if ($request->condition == 'rebooking') {
            $subquery = Booking::selectRaw('MAX(id) as id')
                ->where('doctor_id', $doctorId)
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1');

            if ($dateFilter > 0) {
                $appointments = Booking::whereIn('id', $subquery)
                ->where('schedule_date', '>=', $dateLimit)
                ->orderBy('schedule_date', 'desc')
                ->get();
            } else
            $appointments = Booking::whereIn('id', $subquery)
                ->orderBy('schedule_date', 'desc')
                ->get();
        }
        if ($request->condition == 'upcomingbooking') {
            if ($dateFilter > 0) {
                $appointments = Booking::where('doctor_id', $doctorId)
                ->whereIn('status', [0, 4])
                ->where('schedule_date', '>=', $dateLimit)
                ->orderBy('schedule_date', 'desc')
                ->get();
            } else
            $appointments = Booking::where('doctor_id', $doctorId)
            ->whereIn('status', [0, 4])
            ->orderBy('schedule_date', 'desc')
            ->get();
        }
        if ($request->condition == 'pendingbooking') {
            if ($dateFilter > 0) {
                $appointments = Booking::where('doctor_id', $doctorId)
                    ->where('status', 0)
                    ->where('schedule_date', '>=', $dateLimit)
                    ->orderBy('schedule_date', 'desc')
                    ->get();
            } else
            $appointments = Booking::where('doctor_id', $doctorId)
            ->where('status', 0)
            ->orderBy('schedule_date', 'desc')
            ->get();
        }
        $appointmentsWithDoctor = $appointments->map(function ($appointment) {
            $doctor = User::find($appointment->doctor_id);
           
            if ($doctor) {

                $doctor->image = asset('content/doctor/' . $doctor->image);
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                if ($doctorprofile && $doctorprofile->specialisations) {
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
    
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name')
                        ->toArray();
    
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = $specialisationNames;
                } else {
                    $appointment->doctor = $doctor;
                    $appointment->doctorprofile = [];
                }

            }
            if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) 
            {
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
            $treatment = DB::table('treatments')
                ->where('id', $appointment->treatment_id)
                ->first(['treatment_name']);
    
            if ($treatment) {
                $appointment->treatment_name = $treatment->treatment_name;
            } else {
                $appointment->treatment_name = null;
            }
            $appointment->invoiceurl = route('invoiceuser',$appointment->id);
            $appointment->userdetail;
            return $appointment;
        });
    
        return response()->json(['status' => true, 'appointments' => $appointmentsWithDoctor,'path'=>asset('content/user'.'/')], 200);
    }

    public function loyalitypointsindiv(Request $request) {
        $doctorId = auth()->user()->id;
        
        $appointments = Booking::where('doctor_id', $doctorId)->with('loyalitydetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->get();
        $appointmentsWithLoyality = $appointments->filter(function ($appointment) {
            return $appointment->loyalitydetail !== null;
        });
        return response()->json(['status' => true, 'list' => $appointmentsWithLoyality->values(),'path'=>asset('content/user'.'/')], 200);
    }

    public function modifyLoyalitypointsindiv(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required',
            'booking_id' => 'required',
            'points' => 'required|numeric',
        ]);
        $pointsToUpdate = $request->points;
        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $booking = Booking::with(['loyalitydetail', 'userdetail'])->find($request->booking_id);
        if (!$booking) {
            return response()->json(['status' => false, 'message' => 'Booking not found.'], 404);
        }
        $loyalityHistory = UserPayment::find($request->id);
        $user = $booking->userdetail;
        $currentWalletBalance = $user->wallet;
        if ($currentWalletBalance < $pointsToUpdate) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient wallet balance. Points may already have been used.'
            ], 400);
        }
        $newWalletBalance = $currentWalletBalance - $loyalityHistory->amount + $pointsToUpdate;
        $user->wallet = $newWalletBalance;
        $user->save();

        $loyalityHistory->amount = $pointsToUpdate;
        $loyalityHistory->old_balance = $currentWalletBalance;
        $loyalityHistory->new_balance = $newWalletBalance;
        $loyalityHistory->save();
        $history = json_decode($loyalityHistory->history, true) ?? [];
        $history[] = [
            'previous_balance' => $currentWalletBalance,
            'updated_balance' => $newWalletBalance,
            'change' => $pointsToUpdate - $loyalityHistory->points,
            'action' => "Updated via booking ID $request->booking_id",
            'timestamp' => now()->toDateTimeString(),
            'updated_by'=>auth()->user()->id
        ];
        $loyalityHistory->history = json_encode($history);
        $loyalityHistory->save();
        return response()->json([
            'status' => true,
            'message' => 'Loyalty points updated successfully.',
            'wallet_balance' => $newWalletBalance,
            'history' => $history,
        ], 200);
    }

    public function medicalrecords(Request $request) {
        if ($request->type == 'member') {
            $l = MedicalRecord::where('member_id', $request->id)->where('member_type','other')->where('status',1)
                          ->get();
        } else
        $l = MedicalRecord::where('user_id', $request->id)->where('member_type','self')->where('status',1)
                          ->get();
        
        $user = null;

        if ($l->isEmpty()) {
            $response = ['status' => false, 'msg' => 'Sorry! No medical records found.'];
            return response($response, 422);
        }

        $l->each(function ($medicalRecord) {
            if (!empty($medicalRecord->image)) {
                $medicalRecord->image = asset('project/public/medical_records/' . $medicalRecord->image);
            } elseif (!empty($medicalRecord->pdf)) {
                $medicalRecord->image = asset('project/storage/app/public/prescriptions/' . $medicalRecord->pdf);
            } else {
                $medicalRecord->image = asset('project/public/medical_records/default.jpg');
            }
            // if ($medicalRecord->image) {
            //     $medicalRecord->image = asset('project/public/medical_records/') . '/' . $medicalRecord->image;
            // } else {
            //     $medicalRecord->image = asset('project/public/medical_records/default.jpg');
            // }
        });

        foreach ($l as $medicalRecord) {
            if ($medicalRecord->member_type == 'self') {
                $user = UserProfile::where('id', $medicalRecord->user_id)->first();
            } elseif ($medicalRecord->member_type == 'relation') {
                $user = Member::where('id', $medicalRecord->user_id)->first();
            }

            $medicalRecord->user = $user;
        }

        $response = [
            'status' => true,
            'msg' => 'Medical List',
            'data' => $l,
            'pdfurl'=>asset('/storage/app/public/prescriptions/').'/'
        ];

        return response($response, 200);
    }

    public function bestCustomer(Request $request)
    {
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            $doctorIds = [$authUser->id];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
            $doctorIds[] = $authUser->id;
        }
        if ($request->date_filter) {
            $dateLimit = now()->subDays($request->date_filter);
            $bestCustomers = Booking::where('status', 1)->whereIn('doctor_id',$doctorIds)
            ->where('updated_at', '>=', $dateLimit)
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'))->with('userdetail')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->get();
        } else 
        $bestCustomers = Booking::where('status', 1)
            ->whereIn('doctor_id',$doctorIds)
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'))->with('userdetail')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bestCustomers,
            'path'=> asset('content/user').'/',
            'member'=>asset('project/public/member_images').'/'
        ]);
    }

    public function mydocperformace(Request $request)
    {
        $response = [
            'status' => true,
            'data' => [],
            'path'=>asset('content/doctor/')
        ];
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            // $doctorIds = [];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
        }
        if (!empty($doctorIds)) {
            array_push($doctorIds,$authUser->id);
            $startDate = '';
            $endDate = '';
            $dateFilter = $request->input('date_filter2');
            if ($request->date_filter == 'last_week') {
                $startDate = now()->subWeek()->startOfWeek();
                $endDate = now()->subWeek()->endOfWeek();
            } elseif ($request->date_filter == 'last_month') {
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
            } elseif ($request->start_date) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
            }
            $query = Booking::whereIn('doctor_id', $doctorIds)
                ->where('status', 1)
                ->selectRaw('doctor_id, COUNT(id) as total_bookings, SUM(total_amount) as total_revenue')->with('doctordetail')
                ->groupBy('doctor_id');
            if ($startDate && $endDate) {
                $query->whereBetween('schedule_date', [$startDate, $endDate]);
            }

            if (in_array($dateFilter, ['30', '60', '90', '365'])) {
                $query->where('schedule_date', '>=', now()->subDays($dateFilter));
            }

            $performance = $query->get();
            $response = [
                'status' => true,
                'data' => $performance,
                'path'=>asset('content/doctor/')
            ];
        }
        return response($response, 200);
    }

    public function mydocperformacedetails(Request $request)
    {
        $response = [
            'status' => true,
            'data' => []
        ];

        if (!$request->has('doctor_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Doctor ID is required.'
            ], 400);
        }

        $doctorId = $request->doctor_id;

        $startDate = '';
        $endDate = '';
        $dateFilter = $request->input('date_filter2');
        if ($request->date_filter == 'last_week') {
            $startDate = now()->subWeek()->startOfWeek();
            $endDate = now()->subWeek()->endOfWeek();
        } elseif ($request->date_filter == 'last_month') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
        } elseif ($request->start_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        } 

        $query = Booking::where('doctor_id', $doctorId)
            ->where('status', 1)
            ->selectRaw('treatment_id, COUNT(id) as total_bookings')
            ->groupBy('treatment_id');
        if (in_array($dateFilter, ['30', '60', '90', '365'])) {
            $query->where('updated_at', '>=', now()->subDays($dateFilter));
        }
        if ($startDate && $endDate) {
            $query->whereBetween('schedule_date', [$startDate, $endDate]);
        }

        $treatmentBookings = $query->get();

        $treatmentIds = $treatmentBookings->pluck('treatment_id')->toArray();
        $treatments = Treatment::whereIn('id', $treatmentIds)->get()->keyBy('id');

        foreach ($treatmentBookings as $booking) {
            if (isset($treatments[$booking->treatment_id])) {
                $response['data'][] = [
                    'treatment_id'   => $booking->treatment_id,
                    'treatment_name' => $treatments[$booking->treatment_id]->treatment_name,
                    'total_bookings' => $booking->total_bookings
                ];
            }
        }

        return response()->json($response);
    }

    public function myplanperformance(Request $request)
    {
        $response = [
            'status' => true,
            'data' => []
        ];

        $doctorId = auth()->user()->id;

        $treatments = PackTreatment::where('user_id', $doctorId)
            ->where('user_type', 2)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        if (empty($treatments)) {
            return response()->json([
                'status' => false,
                'message' => 'No active packages found.'
            ], 404);
        }

        $dateFilter = $request->input('date_filter');

        $query = UserSubscriptionsPack::whereIn('pack_id', $treatments)
            ->selectRaw('pack_id, COUNT(user_subscriptions_pack.id) as total_sales, SUM(user_payments.amount) as total_revenue')
            ->join('user_payments', 'user_payments.id', '=', 'user_subscriptions_pack.payment_id')
            ->groupBy('pack_id');

        if (in_array($dateFilter, ['30', '60', '90', '365'])) {
            $query->where('user_subscriptions_pack.updated_at', '>=', now()->subDays($dateFilter));
        }

        $packagePerformance = $query->get();

        $packageIds = $packagePerformance->pluck('pack_id')->toArray();
        $packages = PackTreatment::whereIn('id', $packageIds)->get()->keyBy('id');

        $totalSales = 0;
        $totalRevenue = 0;

        foreach ($packagePerformance as $data) {
            if (isset($packages[$data->pack_id])) {
                $totalSales += $data->total_sales;
                $totalRevenue += $data->total_revenue;

                $response['data'][] = [
                    'pack_id'      => $data->pack_id,
                    'pack_name'    => $packages[$data->pack_id]->name,
                    'total_sales'  => $data->total_sales,
                    'total_revenue' => $data->total_revenue
                ];
            }
        }

        // Add total summary
        // $response['summary'] = [
        //     'total_packages_sold' => $totalSales,
        //     'total_revenue'        => $totalRevenue
        // ];

        return response()->json($response);
    }

    public function listtime(Request $request)
    {
        $list = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $request->clinic_id)->orderBy('id', 'desc')->where('status',1)->first();
        if ($list) {
            $response = ['status' => true, 'list' => $list];

        } else $response = ['status' => false, 'list' => $list];
         
        return response($response, 200);
    }

    public function addtime(Request $request)
    {
        $existing = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $request->clinic_id)->orderBy('id', 'desc')->where('status',1)->first();

        if ($existing) {
            $existing->status = 0;
            $existing->save();
        }

        $new = new DoctorClinicSlot();
        $new->doctor_id = auth()->user()->id;
        $new->clinic_id = $request->clinic_id;
        $new->doctor_availability = json_encode($request->timings);
        $new->status = 1;
        $new->save();

        // Step 4: Return the new record
        $response = [
            'status' => true,
            'time' => $new
        ];
        
        return response($response, 200);
    }

    public function updatetime(Request $request)
    {
        $existing = DoctorClinicSlot::where('id', $request->id)->first();
        $response = [
                'status' => false,
                
            ];
        if ($existing) {
            $existing->doctor_availability = $request->timings;
            $existing->save();

            // Step 4: Return the new record
            $response = [
                'status' => true,
                'time' => $existing
            ];
        }

        
        
        return response($response, 200);
    }



}
