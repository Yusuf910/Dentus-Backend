<?php

namespace App\Exports;

use App\Models\Astrologer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class AstrologerExport implements FromCollection, WithHeadings
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
        $queryUser = Astrologer::query();
        $queryUser->orderBy('id','DESC');
        if(!is_null($this->request->name)) {
            $queryUser->whereRaw("(name like '%" . $this->request->name . "%' )");
        }
        if(!is_null($this->request->email)) {
            $queryUser->whereRaw("(email like '%" . $this->request->email . "%' )");
        }
        if(!is_null($this->request->mobile)) {
            $queryUser->whereRaw("(phone like '%" . $this->request->mobile . "%' )");
        }
        if(!is_null($this->request->status)) {
            $queryUser->whereRaw("(status like '%" . $this->request->status . "%' )");
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
            $st = '';
            $is_premium = '';
            if($a->approved == 1){
                $temp1 = "approved";
            }elseif($a->approved == 0){
                $temp1 =   "Deactivated";
            }
            if($a->online_status==0){
                $temp = "offline";
            }elseif($a->online_status==1){
                $temp = "online";
            }
            switch ($a->status) {
                case '1':
                    $st = "COMPLETE";
                   
                    break;
                
                default:
                    $st = "PENDING";
                     $fl = '';    

                    break;
            }

             switch ($a->is_premium) {
                case '1':
                    $is_premium = "Yes";
                    break;
           
                default:
                    $is_premium = "No";

                    break;
            }
            $output[] = [
                    $a->id,
                    $a->name,
                    $a->email,
                    $a->city,
                    $a->country,
                    $a->phone,
                    $a->gender,
                    $temp1,
                    $a->price_per_mint_chat,
                     $a->price_per_mint_video,
                     $a->price_per_mint_audio,
                     $a->price_per_mint_chat_usd,
                     $a->price_per_mint_video_usd,
                     $a->price_per_mint_audio_usd,
                     $a->added_on ?? '',
                    $a->loginTime,
                    $temp,
                   $a->experience,
                   $a->share_percentage,
                    $a->languages ?? '',
                   // $a->price_per_mint_chat,
                   // $a->price_per_mint_video,
                   // $a->price_per_mint_audio,
                    $is_premium,
                    $a->aadhar_number,
                   $a->pan_number,
                    $a->bankName,
                   $a->bank_account_no,
                   $a->ifsc_code,
                    $st,
                    $a->expertise,
                    $a->bio ?? '',
                    asset('admin/uploads/astrologer/').'/'.$a->image,
                   $a->added_on ,
                  // date('d-M-Y h:ia', strtotime($a->updated_at)),
            ];
        }
        // dd(/1);
        return collect($output);
    }

    public function headings(): array
    {
        return [
        //    "AstrologerId"," Astrologer Name","Email","City","Country","Mobile","Gender","Approval Status","Per Minute Chat Charge in INR","Per Minute Video Charge in INR","Per Minute Audio Charge in INR","Per Minute Chat Charge in USD","Per Minute Video Charge in USD","Per Minute Audio Charge in USD","Registration Date","Approved Date","Last Login","Live Status","Astrologer Experience","Share Percentage","Languages","Per Minute Chat Charge","Per Minute Video Charge","Per Minute Audio Charge","In House Astrologers","Is Premium Astrologer","Aadhar Number","Pan Number","Bank Name","Bank Account Number","IFSC Code","Status","Area of Expertise","Biography","Image","Added On"
        "AstrologerId"," Astrologer Name","Email","City","Country","Phone Number","Gender","Approval Status","Rate Card Per Minute Chat Charge in INR","Rate Card Per Minute Video Charge in INR", "Rate Card Per Minute Audio Charge in INR"," Rate Card Per Minute Chat Charge in USD"," Rate Card Per Minute Video Charge in USD"," Rate Card Per Minute Audio Charge in USD","Registration Date","Last Login","Live Status","Astrologer Experience","Share Percentage","Languages","Is Premium Astrologer","Aadhar Number","Pan Number","Bank Name","Bank Account Number","IFSC Code","Status","Area of Expertise","Biography","Image","Added On","Approved Date"
       
    ];
    }
}
