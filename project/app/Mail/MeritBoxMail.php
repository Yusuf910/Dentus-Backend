<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MeritBoxMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->details['fil_package_deatils'] == '') {
            $BODY = $this->details['body'];
            return $this->subject($this->details['subject'])
                        ->view('emails.mainmailerbodyforcommon',compact('BODY'));
        }
        elseif ($this->details['bcc_email'] == '') {
            $BODY = $this->details['body'];
            return $this->subject($this->details['subject'])
                        ->bcc($this->details['bcc_email'])
                        ->view('emails.mainmailerbodyforcommon',compact('BODY'));
        }
        else {
            $BODY = $this->details['body'];
            return $this->subject($this->details['subject'])
                        ->view('emails.mainmailerbodyforcommon',compact('BODY'))
                        ->attach($this->details['fil_package_deatils']);
        }
        // ->cc($moreUsers)
    
        
    }
}
