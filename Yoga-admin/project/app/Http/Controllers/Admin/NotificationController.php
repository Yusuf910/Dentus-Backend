<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\User;
use App\Models\Generalsetting;
use App\Models\Setting;
use App\Models\UserNotification;
use App\Models\AstrologerNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Astrologer;

use App\Traits\SdSendSms;
use Storage;
class NotificationController extends Controller
{
    use SdSendSms;
    // private $http2_server = 'https://api.push.apple.com:443';
    // private $app_bundle_id = "com.meritbox";
    // private $apple_cert = "certificates.pem";
    public function send_notification_to_user(Request $request)
    {
// print_r("Dd"); die;

        if ($request->ajax()) {
            $name = $request->name;
            $template['search'] = User::where('user_type', 1)->where('status', 1)->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', '%'.$name.'%');
                $query->orWhere('mobile', 'LIKE','%'.$name.'%');
            })
                ->get();
            $output = "";

            if (count($template['search']) > 0) {
                $output = '<table class="table table-striped" style="display:block;postion:relative;z-index:1">';
                foreach ($template['search'] as $row) {
                    $output .= '<tr><td class="" id="name_id" hidden>' . $row->id . '</td>
                        <td class="" id="name_val">' . $row->name . '</td>
                        <td class="" id="mob" hidden>' . $row->mobile . '</td>
                        <td >' . " ( " . $row->mobile . " )" . '</td>' . '</tr>';
                }

                $output .= '</table>';
            } else {
                $output .= '<li class="list-group-item bg-secondary">No Data Found</li>';
            }
            return $output;
        }

        // 


        // $template['users'] = User::customerUser()->orderBy('name','Asc')->get();
        $template['page_title'] = 'Send Notifications to User';
        // $template['user_type'] = 2;
        return view('admin.notification.user', $template);
    }

    public function smt_notification(Request $request)
    {
        if (!empty($_POST["user_type"])) {
            $user_type = $_POST["user_type"];
            $title = $_POST["title"];
            $body = $_POST["body"];
            $notification_type = $_POST["notification_type"];

            $image = null;
            if ($request->hasFile("image")) {
                // $sfile = $request->file("image");
                $imagename = time() . '.' . $request->image->extension();
                // $sfile->move('../admin/uploads/notification/', $imageName);
                $path = Storage::disk('s3')->putFileAs('/notification/'.date('Y-m-d'),request('image'),$imagename);
                $content = Storage::disk('s3')->url($path);
                $image = $content;
            }
            $this->async_notifytoall($title, $body, $user_type, $image,$notification_type,'toall');
            $message = "";
            if ($user_type == 1) {
                $message = "Notification send to  All User";
            } else if ($user_type == 2) {
                $message = "Notification send to All Astrologer";
            } else if ($user_type == 3) {
                $message = "Notification send to All Sales Teams";
            }
            
            return redirect()->back()->with(['success' => $message]);
        } 
        else {
            $user_id = $_POST["id"];
            $title = $_POST["title"];
            $body = $_POST["body"];
            $image = null;
            $notification_type = $_POST["notification_type"];
            if ($request->hasFile("image")) {
                $sfile = $request->file("image");
                $imageName = time() . '.' . $request->image->extension();
                $sfile->move('../admin/uploads/notification/', $imageName);
                $image = $imageName;
            }
            //$image =$url ();
            $user = User::whereId($user_id)->first();
            $this->yt_notification_single($title, $body, $user_id, $image,'1',$notification_type);
            $message = "";
            return redirect()->back()->with(['success' => $message]);
        }
    }


    // public function async_yt_notification($title='', $msg='',$user_type='',$image='',$notification_type='',$for='')
    // {
    //     $url = route('Asyncnotifytoall_admin');
    //     $curl = curl_init();                
    //     $post['title'] = $title; 
    //     $post['msg'] = $msg; 
    //     $post['user_type'] = $user_type; 
    //     $post['notification_type'] = $notification_type; 
    //     $post['for'] = $for; 
    //     $post['image'] = $image; 
    //     curl_setopt($curl, CURLOPT_URL, $url);
    //     curl_setopt ($curl, CURLOPT_POST, TRUE);
    //     curl_setopt ($curl, CURLOPT_POSTFIELDS, $post); 

    //     curl_setopt($curl, CURLOPT_USERAGENT, 'api');

    //     curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
    //     curl_setopt($curl, CURLOPT_HEADER, 0);
    //     curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
    //     curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
    //     curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    //     curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 800); 

    //     curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
        

    //     $re = curl_exec($curl);   
    //     // print_r($re); die;
    //     curl_close($curl);  
    //     return true;
    // }


    public function asyncnotifytoall_admin(Request $request)
    {
        
     print_r("dsvvvvvvvvvvvv"); die;

    }


    public function yt_notification_single($title, $body, $user_id, $image,$for,$notification_type)
    { 
    //    print_r($notification_type);die();
        if ($for == 1) {
            $user_tokens = User::where('id', $user_id)->where('device_token', '!=', '')->select(['device_token', 'id', 'user_type', 'device_type'])->get()->toArray();
           // print_r($user_tokens);die();
            if (!empty($user_tokens)) {
                $regIdChunk1 = array_chunk($user_tokens, 1000);
                foreach ($regIdChunk1 as $list) {


                    foreach ($list as $RegId1) {
                        if ($RegId1['device_type'] == "Android") {
                            $details =  array(
                                'body' => $body,
                                'data' => $body,
                                'title' => $title,
                                'sound' => 'default',
                                // 'priority'=>'high',
                                "icon" => "ic_launcher"
                            );
                            if ($image) {
                                $details['image'] = asset('../admin/uploads/notification/' . $image);
                            } else {
                                $image = 'default.png';
                            }
                        } elseif ($RegId1['device_type'] == "ios") {
                            $details = array(
                                'aps' => [
                                    'alert' => $body,
                                    'badge' => 0,
                                    'content-available' => 1,
                                    'mutable-content' => 1,
                                    'category' => "notification",
                                    'sound' => 'default'
                                ],

                                'sd_type' => 1
                            );
                        }

                        $a = new UserNotification();

                        $a->user_id=$RegId1["id"];
                       
                        $a->user_type= 1;
                        $a->notification= $body;
                        $a->title= $title;
                        $a->image=$image;
                        $a->notification_type= $notification_type;
                        // $a->type='admin';
                        $a->status='1';
                        // $a->added_on= date('Y-m-d H:i:s');
                        $a->save();
                      //  print_r( $a->id);die();
                      //  print_r($a->notification);die();
                        if ($RegId1["device_type"] == "Android") {
                            $this->yt_fcm_push_notification($RegId1["device_token"], $RegId1["user_type"], $details);
                        } elseif ($RegId1["device_type"] == "ios") {
                            $this->sendNotification($details, $RegId1["device_token"]);
                        }
                    }
                }
            }
        }
        else {
            $user_tokens = Astrologer::where('id', $user_id)->where('device_token', '!=', '')->select(['device_token', 'id', 'device_type'])->get()->toArray();
            // dd($user_tokens);
            if (!empty($user_tokens)) {
                $regIdChunk1 = array_chunk($user_tokens, 1000);
                foreach ($regIdChunk1 as $list) {


                    foreach ($list as $RegId1) {
                        $details =  array(
                            'body' => $body,
                            'data' => $body,
                            'title' => $title,
                            'sound' => 'default',
                            "icon" => "ic_launcher"
                        );
                        if ($image) {
                            $details['image'] = asset('../admin/uploads/notification/' . $image);
                        } else {
                            $image = 'default.png';
                        }
                        $a = new UserNotification();
                        $a->user_id=$RegId1["id"];
                        $a->notification= $body;
                        $a->title= $title;
                        $a->image= $image;
                        $a->notification_type= $notification_type;
                        $a->user_type= 2;
                        $a->status='1';

                        // $a->notification_type= 1;
                        // $a->type='admin';
                        // $a->added_on= date('Y-m-d H:i:s');
                        $a->save();
                    //    print_r($RegId1["device_token"]);die();
                        // if ($RegId1["device_type"] == "Android") {
                            $this->yt_fcm_push_notification($RegId1["device_token"], 2, $details);
                        // } elseif ($RegId1["device_type"] == "ios") {
                        //     $this->sendNotification($details, $RegId1["device_token"]);
                        // }
                    }
                }
            }
        }
    }

    public function yt_notification($title, $body, $user_type, $image)
    {
        $user_tokens = User::where('user_type', $user_type)->where('device_token', '!=', '')->select(['device_token', 'id', 'device_type'])->get()->toArray();
        $details = array();
        $regIdChunk1 = array_chunk($user_tokens, 1000);
        foreach ($regIdChunk1 as $list) {
            foreach ($list as $RegId1) {
                if ($RegId1['device_type'] == "Android") {
                    $details =  array(
                        'body' => $body,
                        'data' => $body,
                        'title' => $title,
                        'sound' => 'default',
                        "icon" => "ic_launcher"
                    );
                    if ($image) {
                        $details['image'] = asset('../project/public/notification/' . $image);
                    } else {
                        $image = 'default.png';
                    }
                } elseif ($RegId1['device_type'] == "ios") {
                    $details = array(
                        'aps' => [
                            'alert' => $body,
                            'badge' => 0,
                            'content-available' => 1,
                            'mutable-content' => 1,
                            'category' => "notification",
                            'sound' => 'default'
                        ],

                        'sd_type' => 1
                    );
                }
                UserNotification::create(array(
                    "user_id" => $RegId1["id"],
                    'notification' => $body,
                    'title' => $title,
                    'image' => $image,
                    'notification_type' => 'normal',
                    'status' => 1
                ));
                if ($RegId1["device_type"] == "Android") {
                    $this->yt_fcm_push_notification($RegId1["device_token"], $user_type, $details);
                } elseif ($RegId1["device_type"] == "ios") {
                    $this->sendNotification($details, $RegId1["device_token"]);
                }
            }
        }
    }
    function sendNotification($message, $token)
    {
        $http2ch = curl_init();
        curl_setopt($http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        $url = "{$this->http2_server}/3/device/{$token}";

        //        Log::debug($token);
        // certificate
        $cert = storage_path($this->apple_cert);

        // headers
        $headers = array(
            "apns-topic: {$this->app_bundle_id}",
            "User-Agent: My Sender"
        );

        // other curl options
        curl_setopt_array($http2ch, array(
            CURLOPT_URL => $url,
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => json_encode($message),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLCERT => $cert,
            CURLOPT_HEADER => 1
        ));

        // go...
        $result = curl_exec($http2ch);
        if ($result === FALSE) {
            throw new \Exception("Curl failed: " .  curl_error($http2ch));
        }

        // get response
        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);

        return $result;
    }


    public function yt_fcm_push_notification($registatoin_ids, $user_type, $message)
    {
    //    print_r($message);die();
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

        if ($user_type == 2 ) {
            $API_SERVER_KEY = 'AAAAStPR1HQ:APA91bFFAPbjmDwbpv2vyDqdGL8hC_ghSkDHFHv30cfLWvVoPnDmV5W-Gkel5uCVUH6fid5RdwJFSeGwRSRFckTK9qpZLMcMgujW_gaBoKZIa60zDPhSM-g3EJ1ofLwZS_YkWkUzWd8b';
        }
        else{
            $API_SERVER_KEY = 'AAAAStPR1HQ:APA91bFFAPbjmDwbpv2vyDqdGL8hC_ghSkDHFHv30cfLWvVoPnDmV5W-Gkel5uCVUH6fid5RdwJFSeGwRSRFckTK9qpZLMcMgujW_gaBoKZIa60zDPhSM-g3EJ1ofLwZS_YkWkUzWd8b';
        }
     

        // if (Setting::count() > 0) {
        //     $setting = Setting::first();
        // } else {
        //     $setting = new Setting();
        // }

        // if ($user_type == 1) {
        //     $API_SERVER_KEY = $setting->firebase_key;
        // } else if ($user_type == 2) {
        //     $API_SERVER_KEY = $setting->astrologer_firebase;
        // } else if ($user_type == 3) {
        //     $API_SERVER_KEY = $setting->firebase_key;
        // }
        if (!is_array($registatoin_ids)) {
            $device_tokens = [$registatoin_ids];
        } else {
            $device_tokens = $registatoin_ids;
        }
       // dd($API_SERVER_KEY);
        $fields = array(
            'registration_ids' => $device_tokens,
            'data' => $message,
            'notification' => $message,
            // 'notification' => array(
            //     'title' => 'This is title',
            //     'body' => 'This is body'
            // ),
            // 'priority' => 'high'
            // 'sound'=>'default'
        );
       // dd($fields);
        $headers = array(
            'Authorization:key=' . $API_SERVER_KEY,
            'Content-Type:application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //    dd($result);
        //    exit;
        return $result;
    }


    public function send_notification_to_astrologer(Request $request)
    {
        if ($request->ajax()) {
            $name = $request->name;
            $template['search'] = Astrologer::where('status',1)->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', '%'.$name . '%');
                $query->orWhere('mobile', 'LIKE', '%'.$name . '%');
            })
                ->get();
            $output = "";

            if (count($template['search']) > 0) {
                $output = '<table class="table table-striped" style="display:block;postion:relative;z-index:1">';
                foreach ($template['search'] as $row) {

                    $name5 = json_decode($row->name,true);
                    $a_name5 = $name5[1] ?? 'Astro N';
                    $output .= '<tr><td class="" id="name_id" hidden>' . $row->id . '</td>
                        <td class="" id="name_val">' . $a_name5 . '</td>
                        <td class="" id="mob" hidden>' . $row->mobile . '</td>
                        <td >' . " ( " . $row->mobile . " )" . '</td>' . '</tr>';
                }

                $output .= '</table>';
            } else {
                $output .= '<li class="list-group-item bg-secondary">No Data Found</li>';
            }
            return $output;
        }
        // $template['users'] = User::customerUser()->orderBy('name','Asc')->get();
        $template['page_title'] = 'Send Notifications to Astrologer';
        // $template['user_type'] = 2;
        return view('admin.notification.astrologer', $template);
    }

    public function send_notification_to_astrologer_new(Request $request)
    {
        if ($request->ajax()) {
            $name = $request->name;
            $template['search'] = Astrologer::where('status',1)->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', '%'.$name . '%');
                $query->orWhere('mobile', 'LIKE', '%'.$name . '%');
            })
                ->get();
            $output = "";

            if (count($template['search']) > 0) {
                $output = '<table class="table table-striped" style="display:block;postion:relative;z-index:1">';
                foreach ($template['search'] as $row) {

                    $name5 = json_decode($row->name,true);
                    $a_name5 = $name5[1] ?? 'Astro N';
                    $output .= '<tr><td class="" id="astroname_id" hidden>' . $row->id . '</td>
                        <td class="" id="astroname_val">' . $a_name5 . '</td>
                        <td class="" id="astromob" hidden>' . $row->mobile . '</td>
                        <td >' . " ( " . $row->mobile . " )" . '</td>' . '</tr>';
                }

                $output .= '</table>';
            } else {
                $output .= '<li class="list-group-item bg-secondary">No Data Found</li>';
            }
            return $output;
        }
        // $template['users'] = User::customerUser()->orderBy('name','Asc')->get();
        $template['page_title'] = 'Send Notifications to Astrologer';
        // $template['user_type'] = 2;
        return view('admin.notification.astrologer', $template);
    }
    
    public function smt_notification_astrologer(Request $request)
    {

        // print_r("Dsdsdsd");die();

        if (!empty($_POST["user_type"])) {
            $user_type = $_POST["user_type"];
            $title = $_POST["title"];
            $body = $_POST["body"];
            // print_r("Dsdsdsd");die();
            $image = null;
            if ($request->hasFile("image")) {
                $sfile = $request->file("image");
                $imageName = time() . '.' . $request->image->extension();
                $sfile->move('../admin/uploads/notification/', $imageName);
                $image = $imageName;
            }
            // print_r("Ddd"); die;

            $this->async_yt_notification($title, $body, 2, $image,'toall');
            $message = "";
            $message = "Notification send to All Astrologer";
            
            return redirect()->back()->with(['success' => $message]);
        } else {
            // print_r($_POST["notification_type"]);die();
            $user_id = $_POST["id"];
            $title = $_POST["title"];
            $body = $_POST["body"];
            $notification_type = $_POST["notification_type"];
            $image = null;
            if ($request->hasFile("image")) {
                $sfile = $request->file("image");
                $imageName = time() . '.' . $request->image->extension();
                $sfile->move('../admin/uploads/notification/', $imageName);
                $image = $imageName;
            }
            //$image =$url ();
           // $user = Astrologer::whereId($user_id)->first();
            $this->yt_notification_single($title, $body, $user_id, $image,'2',$notification_type);
            $message = "";
            $message = "Notification send to Astrologer";
            return redirect()->back()->with(['success' => $message]);
        }
    }
    

    public function send_notification_to_all()
    {
        return view('admin.notification.all');
    }

    
    
}
