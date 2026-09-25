<?php

namespace App\Listeners;

use App\Events\ReminderCallEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Generalsetting;
use App\Models\Booking;
use App\Models\AskAQuestion;
use App\Models\Astrologer;


class CallReminderCallApiListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReminderCallEvent $event): void
    {
        if ($event->condition == 'bookingstart') {
            $b = Booking::where("id",$event->bookingid)->first();
            if ($b) {
                $astrologernumber = $b->userdetails->mobile ?? '';
                $seconds = 15;
                $settings = Generalsetting::find(1);
                if ($b->type == 1) {
                    $mobile = $settings->video_reminder_number;
                }
                elseif ($b->type == 2) {
                    $mobile = $settings->audio_reminder_number; 
                }
                elseif ($b->type == 3) {
                    $mobile = $settings->chat_reminder_number;
                }
                // $input = array( "agent_number"=>$mobile,
                //                 "destination_number"=>$astrologernumber,
                //                 "call_timeout"=>$seconds,
                //                 "custom_identifier"=>0);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api-smartflo.tatateleservices.com/v1/click_to_call',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{"agent_number":"'.$mobile.'","destination_number":"'.$astrologernumber.'","call_timeout":'.$seconds.',"custom_identifier":"0"}',
                  CURLOPT_HTTPHEADER => array(
                    'accept: application/json',
                    'content-type: application/json',
                    'Authorization: Bearer '.$settings->tata_tele_token
                  ),
                ));

                $response = curl_exec($curl);
                
                curl_close($curl);
                // code...
            }
        }
        elseif ($event->condition == 'bookingadd') {
            $b = Booking::where("id",$event->bookingid)->whereIN('status',[0])->where('is_chat_or_video_start',0)->first();
            if ($b) {
                $astrologernumber = $b->astrologerdetails->mobile ?? '';
                $seconds = 15;
                $settings = Generalsetting::find(1);
                if ($b->type == 1) {
                    $mobile = $settings->video_reminder_number;
                }
                elseif ($b->type == 2) {
                    $mobile = $settings->audio_reminder_number; 
                }
                elseif ($b->type == 3) {
                    $mobile = $settings->chat_reminder_number;
                }
                $mobile = $settings->audio_reminder_number;
                // $input = array( "agent_number"=>$mobile,
                //                 "destination_number"=>$astrologernumber,
                //                 "call_timeout"=>$seconds,
                //                 "custom_identifier"=>0);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api-smartflo.tatateleservices.com/v1/click_to_call',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>'{"agent_number":"'.$mobile.'","destination_number":"'.$astrologernumber.'","call_timeout":'.$seconds.',"custom_identifier":"0"}',
                  CURLOPT_HTTPHEADER => array(
                    'accept: application/json',
                    'content-type: application/json',
                    'Authorization: Bearer '.$settings->tata_tele_token
                  ),
                ));

                $response = curl_exec($curl);
                
                curl_close($curl);
                // code...
            }
        }
        elseif ($event->condition == 'bookingaskaquestion') {
            $b = AskAQuestion::where('status',0)->where('assigned_id','<>',0)->where('id',$event->bookingid)->first();
            if ($b) {
                if ($b->assigned_id > 0) {
                    $astrologerdetails = Astrologer::where('id',$b->assigned_id)->first();
                    if ($astrologerdetails) {
                        $astrologernumber = $astrologerdetails->mobile ?? '';
                        $seconds = 15;
                        $settings = Generalsetting::find(1);
                        if ($b->type == 1) {
                            $mobile = $settings->video_reminder_number;
                        }
                        elseif ($b->type == 2) {
                            $mobile = $settings->audio_reminder_number; 
                        }
                        elseif ($b->type == 3) {
                            $mobile = $settings->chat_reminder_number;
                        }
                        $mobile = $settings->audio_reminder_number;
                        // $input = array( "agent_number"=>$mobile,
                        //                 "destination_number"=>$astrologernumber,
                        //                 "call_timeout"=>$seconds,
                        //                 "custom_identifier"=>0);
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => 'https://api-smartflo.tatateleservices.com/v1/click_to_call',
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => '',
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 0,
                          CURLOPT_FOLLOWLOCATION => true,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => 'POST',
                          CURLOPT_POSTFIELDS =>'{"agent_number":"'.$mobile.'","destination_number":"'.$astrologernumber.'","call_timeout":'.$seconds.',"custom_identifier":"0"}',
                          CURLOPT_HTTPHEADER => array(
                            'accept: application/json',
                            'content-type: application/json',
                            'Authorization: Bearer '.$settings->tata_tele_token
                          ),
                        ));

                        $response = curl_exec($curl);
                        
                        curl_close($curl);
                    }
                }
                // code...
            }
        }
        
        // return true;
        // $input = $event->booking->id;
        // if ($event->booking) {
        //     $a = new AdminNotification();
        //     $a->booking_id = $event->booking->id;
        //     $a->user_id = $event->booking->user_id;
        //     $a->assign_id = $event->booking->assigned_id;
        //     $a->type = 2;
        //     $a->title = 'Ask A Question Pending';
        //     $a->message = 'The question has been pending for 12 hours. Kindly check.#'.$event->booking->id;
        //     $a->status = 1;
        //     $a->save();
        // }
    }
}
