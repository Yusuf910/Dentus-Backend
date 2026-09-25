<?php

namespace App\Traits;

use DB;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/* New aliases. */
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

trait SdSendSms {
    public function otpmsg_sd($mobile, $message, $templateID, $entityID,$otp) 
    {
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //   CURLOPT_URL => 'http://smsjust.com/sms/user/urlsms.php?apikey=1d9f31-cf0634-5a9d42-57576a-6d13d2&senderid=1705174825127580471&message=Dear%20User%2C%0AYour%20login%20OTP%20for%20using%20Dentist%20platform%20is%20123456.%20Please%20do%20not%20share%20this%20code%20with%20anyone.%20TBASPL&dest_mobileno=8290838118&msgtype=TXT&response=Y',
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => '',
        //   CURLOPT_MAXREDIRS => 10,
        //   CURLOPT_TIMEOUT => 0,
        //   CURLOPT_FOLLOWLOCATION => true,
        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //   CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;
        // $curl = curl_init();

        // $url = "http://smsjust.com/sms/user/urlsms.php?"
        //      . "apikey=1d9f31-cf0634-5a9d42-57576a-6d13d2"
        //      . "&senderid=" . urlencode($templateID)
        //      . "&message=" . urlencode($message)
        //      . "&dest_mobileno=" . urlencode($mobile)
        //      . "&msgtype=TXT"
        //      . "&response=Y";

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;
        // $otp = 123456;
        $url = "https://api.pinnacle.in/index.php/sms/send/TBASPL/".urlencode($mobile)."/".urlencode($message)."/TXT?apikey=1d9f31-cf0634-5a9d42-57576a-6d13d2&dlttempid=1707174892765639117";
        // echo $url;die;
           
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{\"OTP\": \"".$otp."\"}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            curl_close($curl);
            return true;
    }

    public function twofactorsms($otp,$mobile)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://2factor.in/API/V1/f35f4e01-d50e-11ed-addf-0200cd936042/SMS/'.$mobile.'/'.$otp.'+/GBP+OTP',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return true;
        //echo $response;
    }

    public function backgroundprocessnotification($usertype,$message,$title,$userid,$order_id,$for)
    {
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
          CURLOPT_POSTFIELDS =>'{
            "usertype":"'.$usertype.'",
            "message":"'.$message.'",
            "title":"'.$title.'",
            "id":"'.$userid.'",
            "order_id":"'.$order_id.'",
            "for_":"'.$for.'"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return true;
    }

    public function sendMessageThroughFCM($registatoin_ids, $message,$type)
    {
        $settings = DB::table('generalsettings')
            ->select('firebase_key_for_user',
                    'firebase_key_for_teacher','firebase_key_for_salesuser')
            ->first();
        if ($type == 1) 
        { 
            $k = $settings->firebase_key_for_user;
        }
        elseif ($type == 2) 
        {
            $k = $settings->firebase_key_for_teacher;
        }
        elseif ($type == 3) 
        {
            $k = $settings->firebase_key_for_salesuser;
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
        // print_r($result);     die;         
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        //print_r($registatoin_ids);
    }

    public function check_curl($email = '',$name = '',$subject = '',$view = '',$what='',$bcc_email = '' ,$bcc_line = '',$fil_package_deatils = '')
    {

        $url = route('BackithreadNoti');
        $curl = curl_init();                
        $post['email'] = $email; // our data todo in received
        $post['subject'] = $subject; // our data todo in received
        $post['message'] = $view; // our data todo in received
        $post['name'] = $name; // our data todo in received
        $post['view'] = $view;
        $post['what'] = $what;
        $post['bcc_email'] = $bcc_email;
        $post['bcc_line'] = $bcc_line;
        $post['fil_package_deatils'] = $fil_package_deatils;
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

    
    public function sendmail2($email,$name,$subject,$view,$otp)
    {
        $email = $email;//$_POST["email"];
        $subject = $subject;//$_POST["subject"];
        $message = $view;//$_POST["view"];
        $name = $name;//$_POST["name"];
        $bcc_email = '';//$_POST["bcc_email"];
        $bcc_line = '';//$_POST["bcc_line"];
        $fil_package_deatils = '';//$_POST["fil_package_deatils"];
        $google_email = '@gmail.com';
        $oauth2_clientId = '-.apps.googleusercontent.com';
        $oauth2_clientSecret = '--A0_';
        $oauth2_refreshToken = '1//-';

        $mail = new PHPMailer(TRUE);

        try {
           
           $mail->setFrom($google_email, 'Pegasus');
           $mail->addAddress($email, $name);
           $mail->WordWrap = 500;
           $mail->isHTML(true);
           $mail->Subject = $subject;
           $mail->Body = $message;
           $mail->isSMTP();
           $mail->Port = 587;
           $mail->SMTPAuth = TRUE;
           $mail->SMTPSecure = 'tls';
           
           /* Google's SMTP */
           $mail->Host = 'smtp.gmail.com';
           
           /* Set AuthType to XOAUTH2. */
           $mail->AuthType = 'XOAUTH2';
           
           /* Create a new OAuth2 provider instance. */
           $provider = new Google(
              [
                 'clientId' => $oauth2_clientId,
                 'clientSecret' => $oauth2_clientSecret,
              ]
           );
           
           /* Pass the OAuth provider instance to PHPMailer. */
           $mail->setOAuth(
              new OAuth(
                 [
                    'provider' => $provider,
                    'clientId' => $oauth2_clientId,
                    'clientSecret' => $oauth2_clientSecret,
                    'refreshToken' => $oauth2_refreshToken,
                    'userName' => $google_email,
                 ]
              )
           );
           
           /* Finally send the mail. */
           $mail->send();
           return true;
        }
        catch (Exception $e)
        {
             $e->errorMessage();
        }
        catch (\Exception $e)
        {
            $e->getMessage();
        }
        // $data = [
        //     'email_body' => $message
        // ];


        // $objDemo = new \stdClass();
        // $objDemo->to = $email;
        // $objDemo->from = 'noreply@critimedsconsult.com';
        // $objDemo->title = 'Critimedsconsult';
        // $objDemo->subject = $subject;

        // try{
        //     Mail::send('emails.mailbody',$data, function ($message) use ($objDemo) {
        //         $message->from($objDemo->from,$objDemo->title);
        //         $message->to($objDemo->to);
        //         $message->subject($objDemo->subject);
        //     });
           

        // }
        // catch (Exception $e)
        // {
        //     //echo $e->errorMessage();
        // }
        // catch (\Exception $e){
        //     // die($e->getMessage());
        // }
    }

    public function sendmail()
    {
        $email = $_POST["email"];
        $subject = $_POST["subject"];
        $message = $_POST["view"];
        $name = $_POST["name"];
        $bcc_email = $_POST["bcc_email"];
        $bcc_line = $_POST["bcc_line"];
        $fil_package_deatils = $_POST["fil_package_deatils"];
        $google_email = 'mail@.app';
        $oauth2_clientId = '-3p65cmbugh9i5kmkuj5jssjtgadn0s9g.apps.googleusercontent.com';
        $oauth2_clientSecret = '-AKnJkcvt4v22MG6VNxcsimBqKH82';
        $oauth2_refreshToken = '1//-L9IrPQLuCHQSS5P9eWdJWDa8oLLqJEuNvPk6oihoknZDYBaZr6spl6dtcVmkyV5WYnuD5xQ';

        $mail = new PHPMailer(TRUE);

        try {
           
           $mail->setFrom($google_email, 'Pegasus');
           $mail->addAddress($email, $name);
           $mail->WordWrap = 500;
           $mail->isHTML(true);
           $mail->Subject = $subject;
           $mail->Body = $message;
           $mail->isSMTP();
           $mail->Port = 587;
           $mail->SMTPAuth = TRUE;
           $mail->SMTPSecure = 'tls';
           
           /* Google's SMTP */
           $mail->Host = 'smtp.gmail.com';
           
           /* Set AuthType to XOAUTH2. */
           $mail->AuthType = 'XOAUTH2';
           
           /* Create a new OAuth2 provider instance. */
           $provider = new Google(
              [
                 'clientId' => $oauth2_clientId,
                 'clientSecret' => $oauth2_clientSecret,
              ]
           );
           
           /* Pass the OAuth provider instance to PHPMailer. */
           $mail->setOAuth(
              new OAuth(
                 [
                    'provider' => $provider,
                    'clientId' => $oauth2_clientId,
                    'clientSecret' => $oauth2_clientSecret,
                    'refreshToken' => $oauth2_refreshToken,
                    'userName' => $google_email,
                 ]
              )
           );
           
           /* Finally send the mail. */
           $mail->send();
           return true;
        }
        catch (Exception $e)
        {
             $e->errorMessage();
        }
        catch (\Exception $e)
        {
            $e->getMessage();
        }
        // $data = [
        //     'email_body' => $message
        // ];


        // $objDemo = new \stdClass();
        // $objDemo->to = $email;
        // $objDemo->from = 'noreply@critimedsconsult.com';
        // $objDemo->title = 'Critimedsconsult';
        // $objDemo->subject = $subject;

        // try{
        //     Mail::send('emails.mailbody',$data, function ($message) use ($objDemo) {
        //         $message->from($objDemo->from,$objDemo->title);
        //         $message->to($objDemo->to);
        //         $message->subject($objDemo->subject);
        //     });
           

        // }
        // catch (Exception $e)
        // {
        //     //echo $e->errorMessage();
        // }
        // catch (\Exception $e){
        //     // die($e->getMessage());
        // }
    }

    public function sendmailtest()
    {
        $email = '@gmail.com';
        $subject = 'sd';
        $message = 'sd';
        $name = 'sd';
        $bcc_email = $_POST["bcc_email"];
        $bcc_line = $_POST["bcc_line"];
        $fil_package_deatils = $_POST["fil_package_deatils"];
        $google_email = 'mail@.app';
        $oauth2_clientId = '-.apps.googleusercontent.com';
        $oauth2_clientSecret = '-';
        $oauth2_refreshToken = '1//-';

        $mail = new PHPMailer(TRUE);

        try {
           
           $mail->setFrom($google_email, 'Pegasus');
           $mail->addAddress($email, $name);
           $mail->WordWrap = 500;
           $mail->isHTML(true);
           $mail->Subject = $subject;
           $mail->Body = $message;
           $mail->isSMTP();
           $mail->Port = 587;
           $mail->SMTPAuth = TRUE;
           $mail->SMTPSecure = 'tls';
           
           /* Google's SMTP */
           $mail->Host = 'smtp.gmail.com';
           
           /* Set AuthType to XOAUTH2. */
           $mail->AuthType = 'XOAUTH2';
           
           /* Create a new OAuth2 provider instance. */
           $provider = new Google(
              [
                 'clientId' => $oauth2_clientId,
                 'clientSecret' => $oauth2_clientSecret,
              ]
           );
           
           /* Pass the OAuth provider instance to PHPMailer. */
           $mail->setOAuth(
              new OAuth(
                 [
                    'provider' => $provider,
                    'clientId' => $oauth2_clientId,
                    'clientSecret' => $oauth2_clientSecret,
                    'refreshToken' => $oauth2_refreshToken,
                    'userName' => $google_email,
                 ]
              )
           );
           
           /* Finally send the mail. */
           $mail->send();
           echo 1;
        }
        catch (Exception $e)
        {
            echo  $e->errorMessage();
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
        
    }

    public function check_curl_for_sendinblue($email = '',$name = '',$subject = '',$view = '',$what='',$bcc_email = '' ,$bcc_line = '',$fil_package_deatils = '')
    {

        $url = route('BackithreadNotisendinblue');
        $curl = curl_init();                
        $post['email'] = $email; // our data todo in received
        $post['subject'] = $subject; // our data todo in received
        $post['message'] = $view; // our data todo in received
        $post['name'] = $name; // our data todo in received
        $post['view'] = $view;
        $post['what'] = $what;
        $post['bcc_email'] = $bcc_email;
        $post['bcc_line'] = $bcc_line;
        $post['fil_package_deatils'] = $fil_package_deatils;
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

    public function sendinblue()
    {
        $email = $_POST["email"];
        $subject = $_POST["subject"];
        $message = $_POST["view"];
        $name = $_POST["name"];
        $bcc_email = $_POST["bcc_email"];
        $bcc_line = $_POST["bcc_line"];
        $fil_package_deatils = $_POST["fil_package_deatils"];
        try {
           $details = [
                'body' => $message,
                'subject' =>$subject,
                'fil_package_deatils'=>$fil_package_deatils,
                'bcc_email'=>$bcc_email
            ];
            \Mail::to($email)->send(new \App\Mail\PegasusMail($details));
           return true;
        }
        catch (Exception $e)
        {
             $e->errorMessage();
        }
        catch (\Exception $e)
        {
            $e->getMessage();
        }
        // $data = [
        //     'email_body' => $message
        // ];


        // $objDemo = new \stdClass();
        // $objDemo->to = $email;
        // $objDemo->from = 'noreply@critimedsconsult.com';
        // $objDemo->title = 'Critimedsconsult';
        // $objDemo->subject = $subject;

        // try{
        //     Mail::send('emails.mailbody',$data, function ($message) use ($objDemo) {
        //         $message->from($objDemo->from,$objDemo->title);
        //         $message->to($objDemo->to);
        //         $message->subject($objDemo->subject);
        //     });
           

        // }
        // catch (Exception $e)
        // {
        //     //echo $e->errorMessage();
        // }
        // catch (\Exception $e){
        //     // die($e->getMessage());
        // }
    }

    public function sendWebinarcerts($id)
    {
        $url = route('BackithreadNotiwebinar');
        $curl = curl_init();                
        $post['id'] = $id; // our data todo in received
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


    public function async_yt_notification($title='', $body='', $user_type='', $image='',$method='',$classw='',$month='')
    {
        $url = route('AsyncAllBackithreadNoti');
        $curl = curl_init();                
        $post['title'] = $title; 
        $post['body'] = $body; 
        $post['user_type'] = $user_type; 
        $post['image'] = $image; 
        $post['method'] = $method;
        $post['classw'] = $classw; 
        $post['month'] = $month;


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

    public function async_yt_promos($title='', $content='', $image='', $status='',$msg='')
    {
        $url = route('AsyncAllpromos');
        $curl = curl_init();                
        $post['title'] = $title; 
        $post['content'] = $content; 
        $post['image'] = $image; 
        $post['status'] = $status; 
        $post['msg'] = $msg;
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

    public function sendmailjettest()
    {
        $email = 'sachinappslure@gmail.com';
        $subject = 'sd';
        $message = 'sd';
        $name = 'sd';
        $bcc_email = $_POST["bcc_email"];
        $bcc_line = $_POST["bcc_line"];
        $fil_package_deatils = $_POST["fil_package_deatils"];
        $google_email = 'mail@';
        $oauth2_clientId = '-.apps.googleusercontent.com';
        $oauth2_clientSecret = 'GOCSPX-';
        $oauth2_refreshToken = '1//-';

        $mail = new PHPMailer(TRUE);

        try {
           
           $mail->setFrom($google_email, 'Pegasus');
           $mail->addAddress($email, $name);
           $mail->WordWrap = 500;
           $mail->isHTML(true);
           $mail->Subject = $subject;
           $mail->Body = $message;
           $mail->isSMTP();
           $mail->Port = 587;
           $mail->SMTPAuth = TRUE;
           $mail->SMTPSecure = 'tls';
           
           /* Google's SMTP */
           $mail->Host = 'smtp.gmail.com';
           
           /* Set AuthType to XOAUTH2. */
           $mail->AuthType = 'XOAUTH2';
           
           /* Create a new OAuth2 provider instance. */
           $provider = new Google(
              [
                 'clientId' => $oauth2_clientId,
                 'clientSecret' => $oauth2_clientSecret,
              ]
           );
           
           /* Pass the OAuth provider instance to PHPMailer. */
           $mail->setOAuth(
              new OAuth(
                 [
                    'provider' => $provider,
                    'clientId' => $oauth2_clientId,
                    'clientSecret' => $oauth2_clientSecret,
                    'refreshToken' => $oauth2_refreshToken,
                    'userName' => $google_email,
                 ]
              )
           );
           
           /* Finally send the mail. */
           $mail->send();
           echo 1;
        }
        catch (Exception $e)
        {
            echo  $e->errorMessage();
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
        
    }

    public function sendinmailjet($email,$name,$subject,$view,$bcc_email='',$bcc_line='',$fil_package_deatils='')
    {
        $email = $email;
        $subject = $subject;
        $message = $view;
        $name = $name;
        $bcc_email = $bcc_email;
        $bcc_line = $bcc_line;
        $fil_package_deatils = $fil_package_deatils;
        try {
           $details = [
                'body' => $message,
                'subject' =>$subject,
                'fil_package_deatils'=>$fil_package_deatils,
                'bcc_email'=>$bcc_email
            ];
            \Mail::to($email)->send(new \App\Mail\PegasusMail($details));
           return true;
        }
        catch (Exception $e)
        {
            $e->errorMessage();
        }
        catch (\Exception $e)
        {
            $e->getMessage();
        }
        // $data = [
        //     'email_body' => $message
        // ];


        // $objDemo = new \stdClass();
        // $objDemo->to = $email;
        // $objDemo->from = 'noreply@critimedsconsult.com';
        // $objDemo->title = 'Critimedsconsult';
        // $objDemo->subject = $subject;

        // try{
        //     Mail::send('emails.mailbody',$data, function ($message) use ($objDemo) {
        //         $message->from($objDemo->from,$objDemo->title);
        //         $message->to($objDemo->to);
        //         $message->subject($objDemo->subject);
        //     });
           

        // }
        // catch (Exception $e)
        // {
        //     //echo $e->errorMessage();
        // }
        // catch (\Exception $e){
        //     // die($e->getMessage());
        // }
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
        curl_setopt ($curl, CURLOPT_POSTFIELDS, http_build_query($post)); 

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
    
}