<?php
namespace App\Http\Controllers\Api\Users;

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
class UserBookingController extends Controller
{
    public function home(Request $request)
    {
        $response = ['status' => false,'msg'=>'Not found'];  
        $user_id =  auth()->user()->id;
        $qt = Booking::where('user_id',$user_id)->where('status',6)->orderBy('id','DESC')->first();
        if(!empty($qt)) {
            $remaintime = 0;
            switch ($qt->status) {
                case 1:
                    $qt->booking_status = "Completed";
                    break;
                case 2:
                    $qt->booking_status = "Cancel";
                    break;
                case 3:
                    $qt->booking_status = "Reject";
                    break;
                case 4:
                    $qt->booking_status = "Accept";
                    break;
                case 6:
                    $qt->booking_status = "Ongoing";

                    // Calculate remaining time
                        $booking_date_time = strtotime($qt->schedule_date . ' ' . $qt->start_time);
                        $date = new \DateTime( date('Y-m-d H:i:s'));
                        $endTime = date('Y-m-d H:i:s',strtotime($qt->schedule_date . ' ' . $qt->end_time));
                        $date2 = new \DateTime( $endTime );
                        $diffInSeconds = $date2->getTimestamp() - $date->getTimestamp();
                        if ($diffInSeconds > 0) {
                            $remaintime = $diffInSeconds;
                        }
                    break;
                default:
                    $qt->booking_status = "Unknown"; // Handle any unexpected statuses
                    break;
            } 
            $qt->remaintime = $remaintime;
            $qt->clinic = UserEstablishmentClinic::find($qt->clinic_id);
            
            $doctor = User::find($qt->doctor_id);
            if ($doctor) {
                $doctor->image = asset('content/doctor/' . $doctor->image);
                $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                
                if ($doctorprofile && $doctorprofile->specialisations) {
                    $specialisationIds = explode('|', $doctorprofile->specialisations);
    
                    $specialisationNames = \DB::table('master_specialsations')
                        ->whereIn('id', $specialisationIds)
                        ->pluck('name')
                        ->toArray();
    
                    $doctor->doctorprofile = $specialisationNames;
                }
            }
            $qt->doctor = $doctor;
            $response = ['status' => true, 'msg' => 'Bookind Details', 'data' => $qt];   
            return response($response, 200);
        }
        return response($response, 200);
    }

    public function bookingStart(Request $request)
    {
        $response = ['status' => false,'msg'=>'Not found'];  
        $booking_id = $request->booking_id;
        $settings = Generalsetting::find(1);
        $b = Booking::where("id",$booking_id)->whereIN('status',[4])->where('is_chat_or_video_start',0)->first();
        if ($b) {
            $response = ['status' => true];
            $checkanyotherstart = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->first();
            $checkanyotherstart2 = Booking::where("user_id",$b->user_id)->whereIN('status',[0])->whereIN('is_chat_or_video_start',[1])->first();
            $checkanyotherstart4 = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->whereIN('is_chat_or_video_start',[1,2])->first();
            $current_time = Carbon::now()->toDateTimeString();
            $checkanybookingschedule = Booking::where('user_id',$b->user_id)->whereIN('status',[0,6])->where('id','<>',$b->id)->where('consultation_type','Online Consultation')->whereRaw('? BETWEEN start_time AND end_time', [$current_time])->first();
            if ($checkanyotherstart || $checkanyotherstart2 || $checkanyotherstart4 ) {
                $response = ['status' => false,'msg'=>"User busy on another consultation"];  
            }
            elseif ($checkanybookingschedule) {
                $response = ['status' => false,'msg'=>"User busy on another consultation"];  
            }
            else {
                $ongoingbooking = 0;
                $goaheadwallethave = 0;  
                $checkanybookingongoing =  Booking::where("user_id",$b->user_id)->whereIN('status',[6])->first();
                if ($checkanybookingongoing) {
                    $ongoingbooking = 1;
                    $response = ['status' => false,'msg'=>"User busy on another consultation"];  
                }
                $checkanybookingschedulecanstart = Booking::where('user_id', $b->user_id)
                    ->where('id', $b->id)
                    ->where('consultation_type', 'Online Consultation')
                    ->whereRaw('? BETWEEN CONCAT(schedule_date, " ", start_time) AND CONCAT(schedule_date, " ", end_time)', [$current_time])
                    ->toSql();

                if ($checkanybookingschedulecanstart) {
                    if ($ongoingbooking == 0) {
                        $checkanyotherstart = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->first();
                        $checkanyotherstart2 = Booking::where("user_id",$b->user_id)->whereIN('status',[0])->whereIN('is_chat_or_video_start',[1])->first();
                        $checkanyotherstart4 = Booking::where("user_id",$b->user_id)->whereIN('status',[6])->whereIN('is_chat_or_video_start',[1,2])->first();
                        
                        if ($checkanyotherstart || $checkanyotherstart2 || $checkanyotherstart4) {
                            $response = ['status' => false,'msg'=>"User busy another consultation"];  
                        }
                        else {
                            $b->status = 6;
                            $b->is_chat_or_video_start = 1;
                            $b->save();
                            if ($b->status == 6) {
                                // if ($b->astrologerdetails) {
                                //     $name = json_decode($b->astrologerdetails->screen_name,true);
                                //     $nname = $name[1] ?? 'Astro N';

                                //     if ($b->type == 1) {
                                //        $call_name = "Video";
                                //     }
                                //     else{
                                //         $call_name = "Chat";
                                //     }

                                //     //  User::where('id',$b->user_id)->first();

                                //     $user_data = User::find($b->user_id);
                                //     $msg = "Astrologer has started a ".$call_name." consultation with you, kindly accept.";
                                //     $title = "Astrologer has started a ".$call_name." consultation with you, kindly accept.";
                                //     $details =  array(
                                //         'body' => $msg,
                                //         'data' => $msg,
                                //         'title' => $title,
                                //         'sound' => 'default',
                                //         "icon" => "ic_launcher"
                                //     );
                                //     $this->yt_fcm_push_notification($user_data->device_token, 2, $details);
                                //     $nn = new UserNotification();
                                //     $nn->user_id = $user_data->id;
                                //     $nn->notification_type =1;
                                //     $nn->title = $title;
                                //     $nn->notification = $msg;
                                //     $nn->prod_id = 0;
                                //     $nn->image = "";
                                //     $nn->status = 1;
                                //     $nn->user_type = 1;
                                //     $nn->save();
                                //     event(new ReminderCallEvent($b->id,'bookingstart'));

                                // }
                            }
                        }
                    }
                }
                else {
                    $response = ['status' => false,'msg'=>"Your slot booking time is ".date('d/m/Y h:ia',strtotime($b->start_time)).' IST']; 
                }
                
            }
        }
        return response($response, 200);
    }

    public function joinBooking(Request $request)
    {
        $response = ['status' => false];  
        $booking_id = $request->booking_id;
        $b = Booking::where("id",$booking_id)/*->whereIN('status',[0,1,7])*/->first();
        if ($b) {
            $response = ['status' => true];  
            if ($b->status == 6) {
                $b->is_chat_or_video_start = 2;
                $b->save();
            }
        }
        return response($response, 200);
    }


}
