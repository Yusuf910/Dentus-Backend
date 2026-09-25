<?php

namespace App\Exports;

use App\Models\CallHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
class CallHistoryExport implements FromCollection, WithHeadings
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
        $queryUser = CallHistory::query();
        $queryUser->orderBy('added_on','DESC');
        if(!is_null($this->request->name)) {
            // $queryUser->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
        }
        if(!is_null($this->request->start_date) || !is_null($this->request->end_date)) {
            if (!is_null($this->request->start_date) && !is_null($this->request->end_date)) {
                $queryUser->whereBetween('added_on', [$this->request->start_date, $this->request->end_date]);
            }
            elseif (!is_null($this->request->start_date)) {
                $queryUser->whereDate('added_on', $this->request->start_date);

            }
            elseif (!is_null($this->request->end_date)) {
                $queryUser->whereDate('added_on', $this->request->end_date);
            }
            
        }

        $arrays = $queryUser->get();
        // dd($arrays->count());
        $output = [];

        foreach ($arrays as $a)
        {
            $output[] = [
                
                   // $a->id,
                   // $a->astrologer->name ?? '',
                   // $a->user->name ?? '',
                   // $a->sid,
                   // $a->start_time,
                   // $a->end_time,
//$a->call_status,
                   // $a->currency,
                   // $a->call_price,
                   // $a->duration,
                   // $a->total_call_price,
                   // $a->added_on,
            ];
        }
        // dd(/1);
        return collect($output);
    }

    public function headings(): array
    {
        return [
           "Id","Astrologer","UserName","Sid","Start Time","End Time","Call Status","Currency","Call Price","Duration","Total Call Price","Added on"
          // "Txn Name","Name","Email","Phone","Gender","Wallet","Booking Txn Id","Payment Mode","Txn For","Type","Old Wallet","Txn Amount","Update Wallet","Created At"
        ];
    }
}
