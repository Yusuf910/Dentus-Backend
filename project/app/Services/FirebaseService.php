<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;

class FirebaseService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }


    public function sendNotification($deviceToken, $title, $body, $imageUrl = null, $priority = 'high')
    {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        if ($imageUrl) {
            $notification['image'] = $imageUrl;
        }

        $dataPayload = [
            'title' => $title,
            'body' => $body,
        ];

        

            $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($dataPayload)
            ->withAndroidConfig([
                'priority' => $priority,
            ])
            ->withApnsConfig([
                'headers' => [
                    'apns-priority' => $priority === 'high' ? '10' : '5',
                ],
            ]);

        try {
            // \Log::info('Attempting to send Firebase notification', [
            //     'deviceToken' => $deviceToken,
            //     'title' => $title,
            //     'body' => $body,
            //     'imageUrl' => $imageUrl,
            //     'dataPayload' => $dataPayload
            // ]);

            $this->messaging->send($message);

            // \Log::info('Firebase notification sent successfully', [
            //     'deviceToken' => $deviceToken,
            //     'title' => $title,
            //     'body' => $body,
            //     'imageUrl' => $imageUrl,
            //     'dataPayload' => $dataPayload  // Log the custom data payload
            // ]);

            return true;
        } catch (\Exception $e) {
            // // Log the error
            // \Log::error('Error sending Firebase notification: ' . $e->getMessage());
            return false;
        }
    }

    // public function sendNotification($deviceToken, $title, $body, $imageUrl = null)
    // {
    //     // Create the basic notification array
    //     $notification = [
    //         'title' => $title,
    //         'body' => $body,
    //     ];
    
    //     // Add the image to the notification if an image URL is provided
    //     if ($imageUrl) {
    //         $notification['image'] = $imageUrl;
    //     }
    
    //     // Build the message with the token and the notification data
    //     $message = CloudMessage::withTarget('token', $deviceToken)
    //         ->withNotification($notification);
    
    //     // try {
    //     //     // Send the message using Firebase Messaging
    //     //     $this->messaging->send($message);
    //     //     return true;
    //     // } catch (\Exception $e) {
    //     //     // Log any errors and return false if the message could not be sent
    //     //     \Log::error('Error sending Firebase notification: ' . $e->getMessage());
    //     //     return false;
    //     // }


    //     try {
    //         // Log the sending attempt
    //         \Log::info('Attempting to send Firebase notification', [
    //             'deviceToken' => $deviceToken,
    //             'title' => $title,
    //             'body' => $body,
    //             'imageUrl' => $imageUrl
    //         ]);
    
    //         // Send the message using Firebase Messaging
    //         $this->messaging->send($message);
    
    //         // Log if the message is sent successfully
    //         \Log::info('Firebase notification sent successfully', [
    //             'deviceToken' => $deviceToken,
    //             'title' => $title,
    //             'body' => $body,
    //             'imageUrl' => $imageUrl
    //         ]);
    
    //         return true;
    //     } catch (\Exception $e) {
    //         // Log the error
    //         \Log::error('Error sending Firebase notification: ' . $e->getMessage());
    
    //         return false;
    //     }

        
    // }
}

?>
