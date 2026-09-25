<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class BookingExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;
    private $collection;
    protected $request;

    public function __construct($request,$condition='')
    {
        $this->request = $request;
        $this->condition = $condition;
    }

    public function collection()
    {

        // print($this->request); die;
        $queryUser = Booking::query();
        if ($this->condition == 2) {
            $queryUser->where('status',2);
            
        }
        elseif ($this->condition == 3) {
            $queryUser->whereIN('refund_request_raised',[1,2]);
            
        }
        elseif ($this->condition == 4) {
            $queryUser->whereIN('status',[3,4]);
            
        }
        elseif ($this->condition == 5) {
            $queryUser->whereIN('status',[0]);
            
        }
        $queryUser->orderBy('id','DESC');
        if(!is_null($this->request->name)) {
            $queryUser->whereRaw("(type  like '%" . $this->request->name . "%' )");
            //$queryUser->appends(['name' => $request->name]);
        }
       // if(!is_null($this->request->name)) {
        //    $queryUser->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
        //}
        if(!is_null($this->request->mobile)) {
            $queryUser->whereRaw("(user_phone like '%" . $this->request->mobile . "%' )");
        }
        if(!is_null($this->request->orderid)) {
            //  $queryUser->whereRaw("(id like '%" . $request->orderid . "%' )");
            $queryUser->where('id',$this->request->orderid);
          }
        if(!is_null($this->request->start_date) || !is_null($this->request->end_date)) {
            // print_r($this->request->start_date); die;
            if (!is_null($this->request->start_date) && !is_null($this->request->end_date)) {
                $queryUser->whereBetween('created_at', [$this->request->start_date.' 00:00:00', $this->request->end_date.' 23:59:59']);
            }
            elseif (!is_null($this->request->start_date)) {
                $queryUser->whereDate('created_at', $this->request->start_date);

            }
            elseif (!is_null($this->request->end_date)) {
                $queryUser->whereDate('created_at', $this->request->end_date);
            }
            
        }

        $arrays = $queryUser->get();
        // dd($arrays->count());
        $output = [];

        foreach ($arrays as $a)
        {
            $created_at = date("d-m-Y", strtotime($a->created_at));
            $dob = date("d/m/Y", strtotime($a->user_dob));
            $tob =  date('h:ia', strtotime($a->user_tob));
            $schedule_date_time   = date("d-m-Y h:i:s A", strtotime($a->schedule_date_time ));
            $end_time   = date("d-m-Y h:i:s A", strtotime($a->end_time ));
            $start = strtotime($a->schedule_date_time);
            $end = strtotime($a->end_time);
            $diff = abs((($d=$end-$start) <0 ? 0 : $d )/60);
            $mail_deff =  round($diff);
            $sss =  ($d=$end-$start) <0 ? 0 : $d;
            switch ($a->status) {
                    case '1':
                        $st = "Confirmed";
                       
                        break;
                    case '2':
                        $st = "Completed";
                           

                        break;
                    case '3':
                        $st = "Canceled";
                           
                        break;
                    case '4':
                        $st = "refunded";
                           
                        break;
                           case '6':
                        $st = "Ongoing";
                           
                        break;

                    default:
                        $st = "PENDING";
                           

                        break;
            }

            switch ($a->type) {
                    case '1':
                        $bt = "Video";
                        break;
                    case '2':
                        $bt = "Audio";
                        break;
                    case '3':
                        $bt = "Chat";
                        break;
                   
                    default:
                        $bt = "Live";

                        break;
            }
            $output[] = [
                    $a->id,
                    $bt,
                    $st,
                    $a->astrologer->name,
                    $a->user_name,
                    $a->user_email,
                    $a->user_phone,
                    $a->user_gender,
                    $dob,
                    $tob,
                    $a->user_pob,
                    $a->message,
                    $a->payable_amount,
                    $schedule_date_time,
                    $end_time,
                    $mail_deff,
                    $a->sss,
                    $a->ivr_recording,
                    $a->gst_astro,
                    $a->tds_astro,
                    $a->total_astro_comission,
                    $created_at,
            ];
        }
        // dd(/1);
        return collect($output);
    }

    public function headings(): array
    {
        return [
            "OrderId","Booking Type","Booking Status","Astrologer","UserName","Email","Mobile","Gender","Dob","Time of birth","Place of birth","Message","Amount","Start Time","End Time ","Total Minutes","Total Seconds","Recording","PG astro","TDS Astro","Total Astro Comission","Added on"
        ];
    }
}
