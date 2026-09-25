<?php

namespace App\Listeners;

use App\Events\AskAQuestionNotAttain;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use File;
use App\Models\AdminNotification;
class NotifyAdminAskAQuestionNotCompleted
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
    public function handle(AskAQuestionNotAttain $event): void
    {
        $input = $event->booking->id;
        if ($event->booking) {
            $a = new AdminNotification();
            $a->booking_id = $event->booking->id;
            $a->user_id = $event->booking->user_id;
            $a->assign_id = $event->booking->assigned_id;
            $a->hours = $event->booking->hour;
            $a->type = 2;
            $a->title = 'Ask A Question Pending';
            $a->message = 'The question has been pending for '.$a->hours.' hours. Kindly check.#'.$event->booking->id;
            $a->status = 1;
            $a->save();
        }
        
    }
}
