<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\User;
use App\Models\Generalsetting;
use App\Models\Setting;
use App\Models\UserNotification;
use App\Models\Counsellor;
use Illuminate\Support\Facades\Log;
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
        if ($request->ajax()) {
            $name = $request->name;
            $template['search'] = User::where('user_type', 1)->where('status', 1)->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', $name . '%');
                $query->orWhere('mobile', 'LIKE', $name . '%');
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
        $template['page_title'] = 'Send Notifications to User';
        return view('admin.notification.user', $template);
    }

    public function smt_notification(Request $request)
    {
        if (!empty($_POST["user_type"])) {
            $title = $_POST["title"];
            $body = $_POST["body"];

            $image = null;
            if ($request->hasFile("image")) {
                // $sfile = $request->file("image");
                // $imageName = time() . '.' . $request->image->extension();
                // $sfile->move('admin/uploads/notification/', $imageName);
                // $image = $imageName;
                $path = Storage::disk('s3')->put('notification', request('image'));
                $image = Storage::disk('s3')->url($path);
            }



            $this->async_yt_notification($title, $body,$_POST["user_type"], $image,'toall');
            $message = "";
            // if ($user_type == 1) {
            //     $message = "Notification send to  All User";
            // } else if ($user_type == 2) {
            //     $message = "Notification send to All Astrologer";
            // } else if ($user_type == 3) {
            //     $message = "Notification send to All Sales Teams";
            // }
            
            return redirect()->back()->with('success','notification send successfully');
        } else {
            $user_id = $_POST["id"];
            $title = $_POST["title"];
            $body = $_POST["body"];
            $image = null;
            if ($request->hasFile("image")) {
                // $sfile = $request->file("image");
                // $imageName = time() . '.' . $request->image->extension();
                // $sfile->move('project/public/notification/', $imageName);
                $path = Storage::disk('s3')->put('notification', request('image'));
                $image = Storage::disk('s3')->url($path);
                // $image = $imageName;
            }
            $user = User::whereId($user_id)->first();
            $this->yt_notification_single($title, $body, $user_id, $image,'1');
            $message = "";
            return redirect()->back()->with('success','notification send successfully');
        }
    }

    public function yt_notification_single($title, $body, $user_id, $image,$for)
    {
        if ($for == 1) {
            $user_tokens = User::where('id', $user_id)->where('device_token', '!=', '')->select(['device_token', 'id', 'user_type', 'device_type'])->get()->toArray();
            if (!empty($user_tokens)) {
                $regIdChunk1 = array_chunk($user_tokens, 1000);
                foreach ($regIdChunk1 as $list) {
                    foreach ($list as $RegId1) {
                        if ($RegId1['device_type'] == "ios") {
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
                        else {
                            $details =  array(
                                'body' => $body,
                                'data' => $body,
                                'title' => $title,
                                'sound' => 'default',
                                "icon" => "ic_launcher"
                            );
                            if ($image) {
                                $details['image'] = $image;
                            } else {
                                $image = 'default.png';
                            }
                        }

                        $a = new UserNotification();
                        $a->user_id=$RegId1["id"];
                        $a->user_type= 1;
                        $a->notification= $body;
                        $a->title= $title;
                        $a->image=$image;
                        $a->notification_type='admin';
                        $a->status='1';
                        $a->save();
                        if ($RegId1["device_type"] == "ios") {
                            $this->sendNotification($details, $RegId1["device_token"]);
                        }
                        else {
                            $d = $this->yt_fcm_push_notification($RegId1["device_token"], $RegId1["user_type"], $details);
                        } 
                    }
                }
            }
        }
        else {
            $user_tokens = Counsellor::where('id', $user_id)->where('device_token', '!=', '')->select(['device_token', 'id', 'device_type'])->get()->toArray();
            if (!empty($user_tokens)) {
                $regIdChunk1 = array_chunk($user_tokens, 1000);
                foreach ($regIdChunk1 as $list) {


                    foreach ($list as $RegId1) {
                        if ($RegId1['device_type'] == "ios") {
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
                        else {
                            $details =  array(
                                'body' => $body,
                                'data' => $body,
                                'title' => $title,
                                'sound' => 'default',
                                "icon" => "ic_launcher"
                            );
                            if ($image) {
                                $details['image'] = $image;
                            } else {
                                $image = 'default.png';
                            }
                        }

                        $a = new UserNotification();
                        $a->user_id=$RegId1["id"];
                        $a->user_type= 2;
                        $a->notification= $body;
                        $a->title= $title;
                        $a->image=$image;
                        $a->notification_type='admin';
                        $a->status='1';
                        $a->save();
                        if ($RegId1["device_type"] == "ios") {
                            $this->sendNotification($details, $RegId1["device_token"]);
                        }
                        else {
                            $this->yt_fcm_push_notification($RegId1["device_token"], 2, $details);
                        }
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
                if ($RegId1['device_type'] == "android") {
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
                if ($RegId1["device_type"] == "android") {
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
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

        if (Setting::count() > 0) {
            $setting = Generalsetting::first();
        } else {
            $setting = new Generalsetting();
        }

        if ($user_type == 1) {
            $API_SERVER_KEY = $setting->firebase_key_for_user;
        } else if ($user_type == 2) {
            $API_SERVER_KEY = '';
        } else if ($user_type == 3) {
            $API_SERVER_KEY = '';
        }
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
            'priority' => 'high'
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
        // dd($result);
        //    exit;
        return $result;
    }


    public function send_notification_to_councellor(Request $request)
    {
        if ($request->ajax()) {
            $name = $request->name;
            $template['search'] = Counsellor::where('status',1)->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', $name . '%');
                $query->orWhere('mobile', 'LIKE', $name . '%');
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
        // $template['users'] = User::customerUser()->orderBy('name','Asc')->get();
        $template['page_title'] = 'Send Notifications to Counsellor';
        // $template['user_type'] = 2;
        return view('admin.notification.counsellor', $template);
    }
    
    public function smt_notification_councellor(Request $request)
    {
        if (!empty($_POST["user_type"])) {
            $user_type = $_POST["user_type"];
            $title = $_POST["title"];
            $body = $_POST["body"];

            $image = null;
            if ($request->hasFile("image")) {
                // $sfile = $request->file("image");
                // $imageName = time() . '.' . $request->image->extension();
                // $sfile->move('../admin/uploads/notification/', $imageName);
                // $image = $imageName;
                $path = Storage::disk('s3')->put('notification', request('image'));
                $image = Storage::disk('s3')->url($path);
            }



            $this->async_yt_notification($title, $body, 2, $image,'toall');
            $message = "";
            $message = "Notification send to All Councellor";
            
            return redirect()->back()->with('success','notification send successfully');
        } else {
            $user_id = $_POST["id"];
            $title = $_POST["title"];
            $body = $_POST["body"];
            $image = null;
            if ($request->hasFile("image")) {
                // $sfile = $request->file("image");
                // $imageName = time() . '.' . $request->image->extension();
                // $sfile->move('../admin/uploads/notification/', $imageName);
                // $image = $imageName;
                $path = Storage::disk('s3')->put('notification', request('image'));
                $image = Storage::disk('s3')->url($path);
            }
            //$image =$url ();
            $user = Counsellor::whereId($user_id)->first();
            $this->yt_notification_single($title, $body, $user_id, $image,'2');
            $message = "";
            $message = "Notification send to Councellor";
            return redirect()->back()->with('success','notification send successfully');
        }
    }

    public function send_notification_to_all()
    {
        return view('admin.notification.all');
    }

    
    
}
