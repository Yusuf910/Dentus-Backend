<?php

namespace App\Traits;

use DB;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* New aliases. */
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
use File;
use App\Models\Booking;

trait SdSendSms {

	public function smssend($variables='',$mobile='',$country_code='',$template_id='')
    {
    	if ($country_code == '+91' || $country_code == '91' || $country_code == '0') {
    		$recipients["mobiles"] = $mobile;
	    	if (!empty($variables)) {
	    		for ($i=0; $i < count($variables); $i++) { 
	    			$recipients['var'.$i+1] = $variables[$i];
	    			// array_push($recipients, $d);
	    		}
	    	}
	    	$m = array($recipients);
	    	$jso = array("template_id"=>$template_id,"short_url"=>"1","recipients"=>$m);
	    	$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://control.msg91.com/api/v5/flow/',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>json_encode($jso),
			  CURLOPT_HTTPHEADER => array(
			    'accept: application/json',
			    'authkey: 402948AxpaiZDoa8q64e304cdP1',
			    'content-type: application/json',
			    'Cookie: PHPSESSID=eiia02pokpap96aobbvpcm2pq0'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			// echo $response;
    	}
    	else {
    		$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/AC86d9a9cc6160a881f26ef4928df75f21/Messages.json',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => 'To=%20919538911266&From=%2015124026526&Body=Dear%20User%2C%0D%0A123456%20is%20your%20OTP%20to%20verify%20your%20mobile%20number%20on%20Astrolight.%0D%0ATeam%20Gold%20Tree%20Astrolight%20Pvt.%20Ltd.',
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/x-www-form-urlencoded',
			    'Authorization: Basic QUM4NmQ5YTljYzYxNjBhODgxZjI2ZWY0OTI4ZGY3NWYyMTpbQXV0aFRva2VuXQ=='
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			// echo $response;
    	}
        return true;
        //echo $response;
    }

    public function async_to_all($title='', $msg='',$id='',$for='')
    {
        $url = route('Asyncnotifytoall');
        $curl = curl_init();                
        $post['title'] = $title; 
        $post['msg'] = $msg; 
        $post['id'] = $id; 
        $post['for'] = $for; 
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_POST, TRUE);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $post); 

        curl_setopt($curl, CURLOPT_USERAGENT, 'api');

        curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

        $re = curl_exec($curl);   
        curl_close($curl);  
        return true;
    }

    public function sendMessageThroughFCM($registatoin_ids, $message,$type)
    {

        $settings = DB::table('generalsettings')
            ->select('firebase_key_for_user','firebase_key_for_astrologer'
                    )
            ->first();
        if ($type == 1) 
        { 
            $k = $settings->firebase_key_for_user;
        }
        elseif ($type == 2) 
        {
            $k = $settings->firebase_key_for_astrologer;
        }
        elseif ($type == 3) 
        {
            $k = $settings->firebase_key_for_user;
        }
        // elseif ($type == 2) 
        // {
        //     $k = $settings->firebase_key_for_driver;
        // }
     
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
            'notification' => $message
        );
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='.$k;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        //Setup curl, add headers and post parameters.
        
        $result = curl_exec($ch);
        $result .= "token:".json_encode($fields);
        $result .= "key:".$k;
        $result .= "usertype:".$type;  

        $file = time() . rand() . '_file.json';
        $destinationPath = "project/checkNOTIlogsfirebase/".date('Y-m-d').'/';
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, json_encode($result));        
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        //print_r($registatoin_ids);
    }

    public function startRecording($resourceId,$uid_agora)
    {
        $customerKey = env('AGORA_KEY');
        $customerSecret = env('AGORA_SECRET');
        $credentials = $customerKey . ":" . $customerSecret;
        $base64Credentials = base64_encode($credentials);
        // $resourceId = 'lSrHz-zfW_X-N_CYOC_aTNRuKpgleBRqAoGDHFgwpSLl4ecB2KXLpyr_4mnwCsvtTG4R5tVP1lgPMf_AVLcHiJ_gJtPAYe0c5DDabJP6CULduQQokDhGEk2DIkVYMAySgcSn3vMlX8qhenhUASEsXt8PobFx9Vt0NeGr6YUHc0nYYEgFF8WEc20m4LXRpThHfRWJ_eCNG_IMCAGlQBpAr__5wxf9ekDaBQvCVUJnC0C2rpzOF42RUUKr90IWqYoKueAkbuopxLlHVfN5sErMXw';
        $agora_APPID = env('AGORA_APP_ID');
        $bucket = env('AWS_BUCKET');
        $accessKey = env('AWS_ACCESS_KEY_ID');
        $secretKey = env('AWS_SECRET_ACCESS_KEY');
        $get_data = Booking::where('id',$uid_agora)->first();
        if ($get_data->sid == '') 
        {
            $cname = $get_data->bridge_id;
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.agora.io/v1/apps/".$agora_APPID."/cloud_recording/resourceid/".$resourceId."/mode/mix/start",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>"{\n\t\"cname\":\"$cname\",\n\t\"uid\":\"$uid_agora\",\n\t\"clientRequest\":{\n\t\t\"recordingConfig\":{\n\t\t\t\"channelType\":0,\n\t\t\t\"audioProfile\":2,\n\t\t\t\"videoStreamType\":0,\n\t\t\t\"transcodingConfig\":{\n\t\t\t\"width\":640,\n\t\t\t\"height\":480,\n\t\t\t\"fps\":30,\n\t\t\t\"bitrate\":750\n\t\t\t}\n\t\t},\n\t\t\"storageConfig\":{\n\t\t\t\"vendor\":1,\n\t\t\t\"region\":14,\n\t\t\t\"bucket\":\"$bucket\",\n\t\t\t\"accessKey\":\"$accessKey\",\n\t\t\t\"secretKey\":\"$secretKey\"\n\t\t}\t\n\t}\n} \n",
              CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic ".$base64Credentials
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $reso = json_decode($response);
            if(isset($reso->sid))
            {
                $get_data->resourceId = $resourceId;
                $get_data->sid = $reso->sid;
                $get_data->save();

            }
        }
        return true;
    }

    public function stopRecordingnew($id)
    {
        $get_data = Booking::where('id',$id)->first();
        if ($get_data->sid == '') 
        {
            $customerKey = env('AGORA_KEY');
            $customerSecret = env('AGORA_SECRET');
            $credentials = $customerKey . ":" . $customerSecret;
            $base64Credentials = base64_encode($credentials);
            $agora_APPID = env('AGORA_APP_ID');
            $cname = $get_data->bridge_id;
            $uid = $id;
            $resourceId = $get_data->resourceId;
            $sid = $get_data->sid;

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.agora.io/v1/apps/".$agora_APPID."/cloud_recording/resourceid/".$resourceId."/sid/".$sid."/mode/mix/stop",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>"{\n  \"cname\": \"$cname\",\n  \"uid\": \"$uid\",\n  \"clientRequest\":{\n  }\n}",
              CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json;charset=utf-8",
                "Authorization: Basic ".$base64Credentials
              ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $r = json_decode($response);
            if (isset($r->serverResponse))
            {
                $r1 =$r->serverResponse;        
                if (isset($r1->uploadingStatus)) {
                    
                    if ($r1->uploadingStatus == 'uploaded') 
                    {
                        $get_data->ivr_recording = env('AWS_URLPATH').$r1->fileList;
                        $get_data->save();   
                    }
                }
                
            }
            
        }
        return true;
    }

    public function async_notifytoall($title='', $body='', $user_type='', $image='',$type='',$for='')
    {
        $url = route('AsyncAllBackithreadNoti');
        $curl = curl_init();                
        $post['title'] = $title; 
        $post['body'] = $body; 
        $post['user_type'] = $user_type; 
        $post['image'] = $image; 
        $post['type'] = $type;
        $post['for'] = $for; 
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_POST, TRUE);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $post); 

        curl_setopt($curl, CURLOPT_USERAGENT, 'api');

        curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

        $re = curl_exec($curl);   
        curl_close($curl);  
        // return true;
    }
}