<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\UserNotification;

use App\Models\UserModels\Booking;
use App\Models\Generalsetting;
use DB;
use PDF;
use File;
use App\Traits\SdSendSms;
use Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;
use App\Services\FirebaseService;
class BaseController extends Controller
{
    use SdSendSms;


    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function asyncnotifytoall(Request $request)
    {
        // $input = $request->all();
        // $file = time() . rand() . '_file.json';
        // $destinationPath = "project/checkNOTIlogs/";
        // if (!is_dir($destinationPath)) {
        //     mkdir($destinationPath, 0777, true);
        // }
        // File::put($destinationPath . $file, json_encode($input));
       //  $path = Storage::disk('s3')->put('global_files', request('uploadfile'));
       // $content = Storage::disk('s3')->url($path);
       // dd($content);
        if ($request->image) 
        {
            $iamgetosave = $request->image;
            $image = asset('project/public/salespromos/' . $request->image);
        } 
        else 
        {
            $iamgetosave = 'default.png';
            $image = 'default.png';
        }
        $user_type = 3;
        if ($request->for == 'bookingadd') {
            $b = Booking::where('id',$request->id)->first();
            if ($b) {
                $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$b->user_id,$request->id,'bookingadd');        
            } 
        }
        if ($request->for == 'bookingstart') {
            $b = Booking::where('id',$request->id)->first();
            if ($b) {
                $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$b->user_id,$request->id,'bookingstart');        
            } 
        }
        if ($request->for == 'bookingend') {
            $b = Booking::where('id',$request->id)->first();
            if ($b) {
                $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$b->user_id,$request->id,'bookingend');        
            } 
        }

        if ($request->for == 'bookingreminder') {
            $b = Booking::where('id',$request->id)->first();
            if ($b) {
                $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$b->user_id,$request->id,'bookingreminder');        
            } 
        }
        if ($request->for == 'bookingcancelauto') {
            $b = Booking::where('id',$request->id)->first();
            if ($b) {
                $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$b->user_id,$request->id,'bookingcancelauto');        
            } 
        }
        if ($request->for == 'refercodebenefit') {
            $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$request->msg,$request->id,'refercodebenefit'); 
            $this->backgroundprocessnotification(1,$request->title["msg2"],$request->title['title'],$request->id,$request->id,'refercodebenefit'); 
        }
        if ($request->for == 'refercodebenefit2') {
            $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$request->msg,$request->id,'refercodebenefit');
        }
        if ($request->for == 'sendprescription') {
            $b = Booking::where('id',$request->id)->first();
            if ($b) {
                $this->backgroundprocessnotification(1,$request->title["msg"],$request->title['title'],$b->user_id,$request->id,'sendprescription');        
            } 
        }
        
    }
    
    public function backgroundprocessnotification_admin($usertype,$message,$title,$userid,$order_id,$for,$image)
    {
        $mailerurl = route('BackithreadNotifire_admin');
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $mailerurl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "usertype":"'.$usertype.'",
            "message":"'.$message.'",
            "title":"'.$title.'",
            "id":"'.$userid.'",
            "order_id":"'.$order_id.'",
            "for_":"'.$for.'",
            "image":"'.$image.'"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return true;
    }
    
    public function pushNotification_admin(Request $request)
    {
        $input = $request->all();
        // $file = time() . rand() . '_file.json';
        // $destinationPath = "project/checkNOTIlogs/".date('Y-m-d').'/';
        // if (!is_dir($destinationPath)) {
        //     mkdir($destinationPath, 0777, true);
        // }
        // File::put($destinationPath . $file, json_encode($input));
        $usertype = $input['usertype'];
        // $image = 'default.png';
        $message = $input['message'];
        $title = $input['title'];
        $order_id = $input['order_id'];
        $for_ = $input['for_'];
        $image = $input['image'];
        $devicetoken = User::where('id', '=', 0)->where('device_token', '!=', '')->first();
        if ($usertype == 1) {
            $devicetoken = User::where('id', '=', $input['id'])->where('device_token', '!=', '')->first();
        }
        else {
            $devicetoken = Astrologer::where('id', '=', $input['id'])->where('device_token', '!=', '')->first();
        }
        if ($devicetoken) {
            if ($devicetoken->device_type == 'ios') {
                // $notification_type1 = "text";
                // $respJson1 = '{"notification_type":"'.$notification_type1.'","title":"'.$title.'","msg":"'.$message.'","type":"no"}';
                // $message2 = array(
                //         'body' => $message,
                //         'title' => $title,
                //         'sound' => 'Default'
                //     );
                // $a = $this->send_ios_to_user($device_token->devicetoken,$message,$usertype);
                $nn = new UserNotification();
                $nn->user_id = $input['id'];
                $nn->notification_type = $for_;
                $nn->title = $title;
                $nn->notification = $message;
                $nn->prod_id = $order_id;
                $nn->image = $image;
                $nn->status = 1;
                $nn->user_type = $usertype;
                $nn->save();
            }
            else {
                $notification_type1 = "text";
                $respJson1 = '{"notification_type":"' . $notification_type1 . '","title":"' . $title . '","msg":"' . $message . '","image":"' . $image . '","type":"no"}';
                if ($usertype == 2 && $order_id > 0) {
                    $message2 = array(
                        'body' => $message,
                        'title' => $title,
                        'image' => asset('../admin/uploads/notification/' . $image),
                        'sound' => 'Default',
                        'type' => 'normal',
                        'data' => array(
                            'body' => $message,
                            'title' => $title,
                            'image' => asset('../admin/uploads/notification/' . $image),
                            'sound' => 'Default',
                            'type' => 'normal',
                            'icon' => 'ic_notification',
                            'color' => '#18d821',
                            'sound' => 'default',
                            'priority' => 'high',
                            'activityType' => $for_
                        )
                    );
                } else {
                    $message2 = array(
                        'body' => $message,
                        'title' => $title,
                        'image' => asset('../admin/uploads/notification/' . $image),
                        'sound' => 'Default',
                        'type' => 'normal',
                        'activityType' => $for_
                    );
                }

                // $a = $this->sendMessageThroughFCM([$devicetoken->device_token], $message2, $usertype);

                $this->send($devicetoken->device_token,$title, $message, $image);

                $nn = new UserNotification();
                $nn->user_id = $input['id'];
                $nn->notification_type = $for_;
                $nn->title = $title;
                $nn->notification = $message;
                $nn->prod_id = $order_id;
                $nn->image = $image;
                // $nn->image = $image;
                $nn->status = 1;
                $nn->user_type = $usertype;
                $nn->save();
            }
        }
    }

    public function backgroundprocessnotification($usertype,$message,$title,$userid,$order_id,$for)
    {
        $postData = [
            'usertype' => $usertype,
            'message'  => $message,
            'title'    => $title,
            'id'       => $userid,
            'order_id' => $order_id,
            'for_'     => $for
        ];
        $jsonData = json_encode($postData);
        $mailerurl = route('BackithreadNotifire');
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $mailerurl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>$jsonData,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        echo $response = curl_exec($curl);
        curl_close($curl);
        return true;
    }

    public function pushNotification(Request $request)
    {
        $input = $request->all();
        
        // $file = time() . rand() . '_file.json';
        // $destinationPath = "project/checkNOTIlogs/".date('Y-m-d').'/';
        // if (!is_dir($destinationPath)) {
        //     mkdir($destinationPath, 0777, true);
        // }
        // File::put($destinationPath . $file, json_encode($input));
        $usertype = $input['usertype'];
        $image = 'default.png';
        $message = $input['message'];
        $title = $input['title'];
        $order_id = $input['order_id'];
        $for_ = $input['for_'];
        if ($usertype == 1) {
            $devicetoken = UserProfile::where('id', '=', $input['id'])->where('device_token', '!=', '')->first();
        }
        else {
            $devicetoken = User::where('id', '=', $input['id'])->where('device_token', '!=', '')->first();
        }
        if ($devicetoken) {
            if ($devicetoken->device_type == 'ios') {
                $this->send($devicetoken->device_token,$title, $message, $image);
                $nn = new UserNotification();
                $nn->user_id = $input['id'];
                $nn->notification_type = $for_;
                $nn->title = $title;
                $nn->notification = $message;
                $nn->prod_id = $order_id;
                $nn->image = $image;
                $nn->status = 1;
                $nn->user_type = $usertype;
                $nn->save();
            }
            else {
                $notification_type1 = "text";
                $respJson1 = '{"notification_type":"' . $notification_type1 . '","title":"' . $title . '","msg":"' . $message . '","image":"' . $image . '","type":"no"}';
                if ($usertype == 2 && $order_id > 0) {
                    $message2 = array(
                        'body' => $message,
                        'title' => $title,
                        'image' => $image,
                        'sound' => 'Default',
                        'type' => 'normal',
                        'data' => array(
                            'body' => $message,
                            'title' => $title,
                            'image' => $image,
                            'sound' => 'Default',
                            'type' => 'normal',
                            'icon' => 'ic_notification',
                            'color' => '#18d821',
                            'sound' => 'default',
                            'priority' => 'high',
                            'activityType' => $for_
                        )
                    );
                } else {
                    $message2 = array(
                        'body' => $message,
                        'title' => $title,
                        'image' => $image,
                        'sound' => 'Default',
                        'type' => 'normal',
                        'activityType' => $for_
                    );
                }

                // $a = $this->sendMessageThroughFCM([$devicetoken->device_token], $message2, $usertype);
                $this->send($devicetoken->device_token,$title, $message, $image);
                // ss
                $nn = new UserNotification();
                $nn->user_id = $input['id'];
                $nn->notification_type = $for_;
                $nn->title = $title;
                $nn->notification = $message;
                $nn->prod_id = $order_id;
                $nn->image = $image;
                $nn->status = 1;
                $nn->user_type = $usertype;
                $nn->save();
            }
        }
    }

    public function send($device_token,$title, $body, $image)
    {

          $deviceToken = $device_token;
          $imageUrl = $image;
          $result = $this->firebaseService->sendNotification($deviceToken, $title, $body, $imageUrl);
          if ($result) {
              return response()->json(['success' => 'Notification sent successfully.']);
          } else {
              return response()->json(['error' => 'Failed to send notification.'], 500);
          }
    }

    public function reminder1hour(Request $request)
    {
        $bookings = Booking::
            whereIN('status', [0,4])
            ->where('reminder1hour',0)
            ->whereDate('schedule_date', date('Y-m-d'))
            ->get();
        $currentTimestamp = strtotime(now());
        if ($bookings->count()) {
            foreach ($bookings as $b) {
                $bookingTimestamp = strtotime($b->schedule_date . ' ' . $b->schedule_time);
                $oneHourBefore = strtotime("-1 hours", strtotime($b->schedule_date.' '.$b->schedule_time));
                if ($currentTimestamp >= $oneHourBefore && $currentTimestamp < $bookingTimestamp) {
                    $bdate = date("l, F j, Y g:i A", strtotime($b->schedule_date.' '.$b->schedule_time));
                    $msgarray = array("title"=>"📌Appointment Reminder",
                      "msg"=>"📩 Reminder: Your appointment with".$b->doctordetail->name." at ".$b->clinicdetail->clinic_name." is scheduled for ".$bdate.' Please be on time.',

                     "msg2"=>"Reminder: You have an upcoming appointment with ".$b->userdetail->name." at ".$b->clinicdetail->clinic_name." on ".$bdate."."
                    );
                    $this->async_to_all($msgarray,'',$b->id,'bookingreminder');
                    $b->reminder1hour = 1;
                    $b->save();
                }
            }
        }


        

    }

    public function bookingcheck(Request $request)
    {
        for ($i = 0; $i < 29; ++$i) {
            $bookings = Booking::
                whereIN('status', [0,4])
                ->whereDate('schedule_date', date('Y-m-d'))
                ->get();
            if ($bookings->count()) {
                echo date('Y-m-d H:i:s');
                foreach ($bookings as $b) {
                    $scheduledDateTime = strtotime($b->schedule_date . ' ' . $b->schedule_time);
                    $currentTime = time();
                    if ($currentTime > $scheduledDateTime) {
                        $scheduledDateTime = strtotime($b->schedule_date . ' ' . $b->schedule_time);
                        $currentTime = time();  
                        if (isset($b->doctordetail->name) && isset($b->clinicdetail->clinic_name)) {
                            $bdate = date("l, F j, Y g:i A", strtotime($b->schedule_date.' '.$b->schedule_time));
                            $msgarray = array(
                                "title" => "📌 Appointment Completed",
                                "msg" => "📩 Reminder: Your appointment with Dr. " . $b->doctordetail->name ?? '-' . 
                                         " at " . $b->clinicdetail->clinic_name . 
                                         " scheduled for " . $bdate . " has been Completed due to the time limit being exceeded.",

                                "msg2" => "Reminder: You had an appointment with " . $b->userdetail->name . 
                                          " at " . $b->clinicdetail->clinic_name . 
                                          " on " . $bdate . "."
                            );

                            $this->async_to_all($msgarray, '', $b->id, 'bookingcancelauto');
                        }
                        
                        $b->status = 1;
                        $b->save();
                    }
                }
            }
            sleep(1);
        }
    }

    public function smstests(Request $request)
    {
        $msg = 'Dear User,Your login OTP for using Dentist platform is 582582. Please do not share this code with anyone. TBASPL';
        $this->otpmsg_sd('8290838118',$msg,'1705174825127580471','','582582');        
    }
}
