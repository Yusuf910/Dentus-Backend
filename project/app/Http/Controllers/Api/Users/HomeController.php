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
use App\Models\SocialConnectView;
use App\Models\UserModels\Booking;
use App\Models\UserInformation;
use App\Models\Treatment;
use App\Models\TreatmentDoctor;
use App\Models\UserModels\UserProfile;
use App\Models\UserEstablishmentClinic;
use App\Models\MasterSpecialsation;
use App\Models\MasterServices;
use App\Models\MasterLangauage;
use App\Models\Blog;
use App\Models\SeasonalOffers;
use App\Models\UserModels\UserNotification;
use App\Models\Generalsetting;
use App\Models\Faq;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use DB;
class HomeController extends Controller
{

    public function HomeScreen(Request $request)
    {
        $user = UserProfile::where('id', auth()->user()->id)->first();
        
        // Decrypt the clinic ID
        if ($user->clinic_id) {
            if ((int)$user->clinic_id) {
                $clinicId = $user->link_id;
            }
            else {
                $decrypted = Crypt::decryptString($user->clinic_id);
                $clinicId = unserialize($decrypted);
            }
        } else {
            $clinicId = $user->link_id;
        }
        
        // Get the doctor(s) associated with the clinic
        $doctors = User::where('id', $clinicId)
                        ->orWhere('parent_id', $clinicId)->with('UserInformationDetails')
                        ->get();
        $doctors->each(function ($doctors) {
            $doctors->image = asset('content/doctor') . '/' . $doctors->image;
            $specialisation_ids = explode('|', $doctors->UserInformationDetails->specialisations ?? '');
    
            $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
        
            $doctors->specialisations_data = $specialisations->map(function ($specialisation) {
                return [
                    'id' => $specialisation->id,
                    'name' => $specialisation->name,
                ];
            });
        
            // $service_ids = explode('|', $doctor_info->services);
        
            // $services = MasterServices::whereIn('id', $service_ids)->get();
        
            // $services_data = $services->map(function ($service) {
            //     return [
            //         'id' => $service->id,
            //         'name' => $service->name,
            //     ];
            // });
        
            // $language_ids = explode('|', $doctor_info->language_known);
        
            // $languages = MasterLangauage::whereIn('id', $language_ids)->get();
        
            // $languages_data = $languages->map(function ($language) {
            //     return [
            //         'id' => $language->id,
            //         'name' => $language->name, 
            //     ];
            // });
        });
        // $clinic = UserEstablishmentClinic::where('user_id', $doctors->id)->get();

        // Initialize an empty array for blogs
        $blogs = collect(); // Initialize as an empty collection
        
        // Loop through each doctor to get their associated blogs
        foreach ($doctors as $doctor) {
            $doctorBlogs = Blog::where('user_id', $doctor->id)->where('status', 1)->orderBy('updated_at','desc')->get();
            $blogs = $blogs->merge($doctorBlogs); // Merge using collection's merge method
        }
        
        // Modify blog images
        $blogs->each(function ($blog) {
            $blog->image = asset('content/blogs') . '/' . $blog->image;
        });
    
        // Initialize an empty array for seasonal offers
        $seasonal = collect(); // Initialize as an empty collection
        
        $today = Carbon::today()->toDateString();
        // foreach ($doctors as $doctor_sea) {
            $doctorseasonal = SeasonalOffers::where(function ($query) {
                            $query->where('user_id', auth()->user()->link_id)
                              ->where('created_by', 1);
                        })
                        ->where('status',1)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today) 
                        ->orderBy('discount','DESC')
                        ->get();
            $seasonal = $seasonal->merge($doctorseasonal); // Merge using collection's merge method
        // }
        
        // Modify seasonal offer images
        $seasonal->each(function ($offer) {
            $treatment = DB::table('treatments')
                ->where('id', $offer->treat_package_id)
                ->first(['treatment_name']);
    
            if ($treatment) {
                $offer->treatment_name = $treatment->treatment_name;
            } else {
                $offer->treatment_name = null;
            }
            $offer->image = asset('content/SeasonalOffers') . '/' . $offer->image;
        });
        
        $upcomingbooking = Booking::where('user_id', auth()->user()->id)->with('doctordetail','clinicdetail','doctor')
            ->where('schedule_date', '>=', now()->format('Y-m-d'))
            ->whereIN('status', [0,4])
            ->orderBy('schedule_date', 'asc')
            ->orderByRaw("STR_TO_DATE(schedule_time, '%h:%i %p') asc")
            ->limit(1)
            ->get()

            ->map(function ($appointment) {
                if ($appointment->doctordetail) {
                    $doctor = $appointment->doctordetail;

                    $doctor->imagen = asset('content/doctor/' . $doctor->image);
                    $appointment->doctor->imagen = asset('content/doctor/' . $doctor->image);
                    $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorprofile && $doctorprofile->specialisations) {
                        $specialisationIds = explode('|', $doctorprofile->specialisations);

                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name') // Only fetch the names
                            ->toArray();

                        $appointment->doctor = $doctor;

                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        $appointment->doctorprofile = [];
                    }
                } else {
                    $appointment->doctor = null;
                    $appointment->doctorprofile = [];
                }

                if ($appointment->clinicdetail) {
                    $appointment->clinic = $appointment->clinicdetail;
                } else {
                    $appointment->clinic = null;
                }

                return $appointment;
            });
        
        $response = [
            'status' => true,
            'msg' => 'Home Screen List',
            'doctor' => $doctors,
            'social' => $blogs,
            'seasonal' => $seasonal,
            'upcomingbooking'=>$upcomingbooking
        ];
    
        return response($response, 200);
    }


    


    public function HomeScreenDoctorDetail(Request $request)
    {
        $doctor = User::where('id', $request->doctor_id)->with(['ratings.userProfile' => function ($query) {
            $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
        }])->withCount([
            'ratings',
            'bookings as active_bookings_count' => function ($query) {
                $query->where('status', 1);
            }
        ])
        ->withAvg('ratings', 'rating')->first();
        $doctor->active_bookings_count = UserProfile::where('link_id',$request->doctor_id)->count();
        if (!$doctor) {
            return response([
                'status' => false,
                'msg' => 'Doctor not found.',
            ], 404);
        }
        $doctor->image = asset('content/doctor') . '/' . $doctor->image;
        $clinics = UserEstablishmentClinic::where('user_id', $doctor->id)->where('status',1)->get();
        $clinics->each(function ($clinics) {
            $clinics->logo = asset('content/doctor/clinic') . '/' . $clinics->logo;
            $clinics->image = asset('content/doctor/clinic') . '/' . $clinics->image;
        });
    
        $doctor_info = UserInformation::where('user_id', $request->doctor_id)
                                      ->select('about', 'specialisations', 'degree', 'college_institute', 'year_of_completion', 
                                               'year_of_experience', 'awards_name', 'award_college_institute', 'award_year', 'services', 'language_known','experience')
                                      ->first();
        $doctor_info->year_of_experience = $doctor_info->experience;
        if (!$doctor_info) {
            return response([
                'status' => false,
                'msg' => 'Doctor information not found.',
            ], 404);
        }
    
        $specialisation_ids = explode('|', $doctor_info->specialisations);
    
        $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
    
        $specialisations_data = $specialisations->map(function ($specialisation) {
            return [
                'id' => $specialisation->id,
                'name' => $specialisation->name,
            ];
        });
    
        $service_ids = explode('|', $doctor_info->services);
    
        $services = MasterServices::whereIn('id', $service_ids)->get();
    
        $services_data = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
            ];
        });
    
        $language_ids = explode('|', $doctor_info->language_known);
    
        $languages = MasterLangauage::whereIn('id', $language_ids)->get();
    
        $languages_data = $languages->map(function ($language) {
            return [
                'id' => $language->id,
                'name' => $language->name, 
            ];
        });
    
        
    
        
        $response = [
            'status' => true,
            'msg' => 'Doctor details',
            'doctor' => $doctor,
            'doctor_info' => $doctor_info,
            'specialisations' => $specialisations_data,
            'services' => $services_data,
            'languages' => $languages_data,
            
        ];
    
        return response($response, 200);
    }


    public function HomeScreenClinicDetail(Request $request)
    {
        $doctor = User::where('id', $request->doctor_id)->first();
    
        if (!$doctor) {
            return response([
                'status' => false,
                'msg' => 'Doctor not found.',
            ], 404);
        }
    
        $clinics = UserEstablishmentClinic::where('user_id', $doctor->id)->where('status',1)->with('holidays','galleries')->get();
        $clinics->each(function ($clinics) {
            $clinics->logo = asset('content/doctor/clinic') . '/' . $clinics->logo;
            $clinics->image = asset('content/doctor/clinic') . '/' . $clinics->image;
        });
    
     
        $response = [
            'status' => true,
            'msg' => 'Clinic details',
            'clinics' => $clinics,
            'galleypath'=>asset('content/doctor/gallery').'/'
        ];
    
        return response($response, 200);
    }



    public function HomeScreenSocialDetail(Request $request)
    {
        // Get the doctor associated with the provided doctor_id
        $doctor = User::where('id', $request->doctor_id)->first();
    
        // Check if the doctor exists
        if (!$doctor) {
            return response([
                'status' => false,
                'msg' => 'Doctor not found.',
            ], 404);
        }
    
        $blogs = Blog::where('user_id', $doctor->id)->where('type',$request->type)->get();
        $blogs->each(function ($blog) {
            $blog->image = asset('content/blogs') . '/' . $blog->image; // Prepend the full URL for images
        });
    
     
        $response = [
            'status' => true,
            'msg' => 'Social details',
            'blogs' => $blogs,
        ];
    
        return response($response, 200);
    }
    
    
    

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

    public function blogslist(Request $request)
    {
        $user = UserProfile::where('id', auth()->user()->id)->first();
        if ($user->clinic_id) {
            if ((int)$user->clinic_id) {
                $clinicId = $user->link_id;
            }
            else {
                $decrypted = Crypt::decryptString($user->clinic_id);
                $clinicId = unserialize($decrypted);
            }
        } else {
            $clinicId = $user->link_id;
        }
        $blogs = collect();
        if ($request->doctor_id) {
            $doctorBlogs = Blog::where('user_id', $request->doctor_id)->where('type', $request->type)->orderBy('created_at','ASC')->get();
            $blogs = $blogs->merge($doctorBlogs);
        }
        else {
            $doctors = User::where('id', $clinicId)
                            ->orWhere('parent_id', $clinicId)
                            ->get();
            foreach ($doctors as $doctor) {
                $doctorBlogs = Blog::where('user_id', $doctor->id)->where('type', $request->type)->orderBy('created_at','ASC')->get();
                $blogs = $blogs->merge($doctorBlogs);
            }
        }
        $blogs->each(function ($blog) {
            $blog->image = asset('content/blogs') . '/' . $blog->image;
        });
        $response = [
            'status' => true,
            'msg' => 'Social details',
            'blogs' => $blogs,
        ];
        return response($response, 200);
    }

    public function blogsview(Request $request)
    {
        $blog = Blog::where('id', $request->blog_id)->first();
        $a = new SocialConnectView();
        $a->user_id = auth()->user()->id;
        $a->blog_by_id = $blog->user_id;
        $a->blog_id = $request->blog_id;
        $a->save();
        $response = [
            'status' => true,
            'msg' => 'Social details',
            'blogs' => $a,
        ];
        return response($response, 200);
    }

    public function listNotification(Request $request)
    {
        UserNotification::where('user_id', auth()->user()->id)->where('read', 0)
       ->update([
           'read' => 1
        ]);
        $list = UserNotification::limit(37)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->whereNOTIN('status',[2])->get();
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

    public function settingsConfig(Request $request)
    {
        $check = UserInformation::where('user_id',auth()->user()->link_id)->first();
        $referrer_a_friend_points = $check->referrer_a_friend_points ?? 0;
        $referred_after_booking_points = $check->referred_after_booking_points ?? 0;
        $redemption_points = $check->redemption_points ?? 0;
        $replacements = [
            '{{referrer_a_friend_points}}' => $referrer_a_friend_points, 
            '{{referred_after_booking_points}}' => $referred_after_booking_points, 
            '{{redemption_points}}' => $redemption_points//
        ];
        $generalSetting = Generalsetting::select(
            'loyality_program_about',
            'loyality_program_points',
            'loyality_program_become_member',
            'loyality_program_steps_to_earn',
            'loyality_program_steps_to_redeem',
            'loyality_faq'
        )->where('id', 1)->first();
        $generalSetting->loyality_program_about = strtr($generalSetting->loyality_program_about, $replacements);
        
        if ($generalSetting && $generalSetting->loyality_faq) {
            $faqIds = explode(',', $generalSetting->loyality_faq);
            $faqs = Faq::whereIn('id', $faqIds)->get();
        } else {
             $faqs = collect();
        }
        $generalSetting->loyality_program_steps_to_earn = explode('|', $generalSetting->loyality_program_steps_to_earn);
        $generalSetting->loyality_program_steps_to_redeem = explode('|', $generalSetting->loyality_program_steps_to_redeem);
        $response = ['status' => true, 'Generalsetting' => $generalSetting,'Faqs' => $faqs,'referrer_a_friend_points'=>$referrer_a_friend_points
    ,'referred_after_booking_points'=>$referred_after_booking_points
    ,'redemption_points'=>$redemption_points];
        return response($response, 200);
    }

    public function invoiceuser(Request $request,$id)
    {
        $a = Booking::where('id',$id)->first();
        $treatment = DB::table('treatments')
                    ->where('id', $a->treatment_id)
                    ->first(['treatment_name']);
        
        if ($treatment) {
            $a->treatment_name = $treatment->treatment_name;
        } else {
            $a->treatment_name = null;
        }
        return view('pdf.invoice',compact('a'));
    }

    public function invoiceuserrefund(Request $request,$id)
    {
        $a = Booking::where('id',$id)->first();
        $treatment = DB::table('treatments')
                    ->where('id', $a->treatment_id)
                    ->first(['treatment_name']);
        
        if ($treatment) {
            $a->treatment_name = $treatment->treatment_name;
        } else {
            $a->treatment_name = null;
        }
        return view('pdf.invoicerefund',compact('a'));
    }

    public function loyalitypoints()
    {
        $user = UserProfile::where('id', auth()->user()->id)
            ->with([
                'userPayments' => function ($query) {
                    $query->orderBy('created_at', 'desc')
                        ->select('id', 'user_id', 'amount', 'action', 'invoice_no', 'trxn_id', 'created_at','type');
                }
            ])
            ->first();
        $credits = $user->userPayments->where('action', 'credit')->whereIN('type',[2,3])->values();
        $debits = $user->userPayments->where('action', 'debit')->where('type',2)->values();  
        $generalSetting = Generalsetting::select('loyality_program_points_amount')->where('id', 1)->first();
        $r = User::where('id',auth()->user()->link_id)->first();
        if ($r->UserInformationDetails) {
            $generalSetting->loyality_program_points_amount = $r->UserInformationDetails->redemption_points ?? 0;
        }
        $response = [
            'status' => true,
            'user' => $user->only(['id', 'wallet']),
            'member'=>'Ellite Member',
            'image'=>asset('content/user').'/'.$user->image,
            'credits' => $credits,
            'debits' => $debits,
            'settings'=>$generalSetting
            ];
        return response()->json($response, 200);
    }

    public function searchDoc(Request $request)
    {
        $user = UserProfile::where('id', auth()->user()->id)->first();
        
        if ($user->clinic_id) {
            if ((int)$user->clinic_id) {
                $clinicId = $user->link_id;
            }
            else {
                $decrypted = Crypt::decryptString($user->clinic_id);
                $clinicId = unserialize($decrypted);
            }
        } else {
            $clinicId = $user->link_id;
        }
        $searchString = $request->input('searchstring');    
        $doctors = User::where('id', $clinicId)
                        ->orWhere('parent_id', $clinicId)->with('UserInformationDetails')
                        ->get();
        $doctors = User::where(function ($query) use ($clinicId) {
                        $query->where('id', $clinicId)
                              ->orWhere('parent_id', $clinicId);
                    })
                    ->when($searchString, function ($query, $searchString) {
                        // Apply search condition to specific columns
                        $query->where('name', 'like', '%' . $searchString . '%')
                              ->orWhere('email', 'like', '%' . $searchString . '%');
                              // ->orWhereHas('UserInformationDetails', function ($query) use ($searchString) {
                              //     $query->where('address', 'like', '%' . $searchString . '%');
                              // });
                    })
                    ->with('UserInformationDetails')
                    ->get();
        $specialisationIds = $doctors->pluck('UserInformationDetails.specialisations')
            ->filter()
            ->flatMap(function ($item) {
                return explode('|', $item);
            })
            ->unique()
            ->all();

        $specialisations = MasterSpecialsation::whereIn('id', $specialisationIds)
            ->get()
            ->keyBy('id'); // Use keyBy for faster lookup

        // Transform doctors with specialisations and image
        $doctors = $doctors->map(function ($doctor) use ($specialisations) {
            $specialisationIds = explode('|', $doctor->UserInformationDetails->specialisations ?? '');
            $doctor->image = asset('content/doctor') . '/' . $doctor->image;
            $doctor->specialisations_data = collect($specialisationIds)
                ->map(function ($id) use ($specialisations) {
                    return $specialisations->get($id);
                })
                ->filter()
                ->map(function ($specialisation) {
                    return [
                        'id' => $specialisation->id,
                        'name' => $specialisation->name,
                    ];
                });

            return $doctor;
        });            
        return response()->json(array("status"=>true,"list"=>$doctors));
    }
}
