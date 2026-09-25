<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
class TransactionHistroyExport implements FromCollection, WithHeadings
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
        // print_r($this->request);
        $this->condition = $condition;
    }

    public function collection()
    {
        // print_r($this->request->start_date); die;
        // $this->condition
        $queryUser = Transaction::query();
        $queryUser->select('transactions.*');
        $queryUser->orderBy('transactions.id','DESC');
        $queryUser->whereNOTIN('transactions.id',[0]);
        // $queryUser->join('user', 'user.id', '=', 'transactions.user_id');
        if(!empty($_GET['name'])) {
            $queryUser->where('user_id',$_GET['name']);
           // $queryUser->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
        }

        if(!empty($_GET['user_mobile'])) {
            $queryUser->where('user_id',$_GET['user_mobile']);
           // $queryUser->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
        }


        if(!is_null($this->request->start_date) || !is_null($this->request->end_date)) {
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
            $output[] = [
                $a->txn_name,
                $a->user->name ?? '',
                $a->user->email ?? '',
                $a->user->phone ?? '',
                $a->user->gender ?? '',
                $a->user->wallet ?? '',
                $a->booking_txn_id ,
                $a->payment_mode ,
                $a->txn_for ,
                $a->type ,
                $a->old_wallet ,
                $a->txn_amount ,
                $a->message ,
                $a->update_wallet ,
                $a->created_at
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
           // "Id","Astrologer","UserName","Sid","Start Time","End Time","Call Status","Currency","Call Price","Duration","Total Call Price","Added on"
           "Txn Name","Name","Email","Phone","Gender","Wallet","Booking Txn Id","Payment Mode","Txn For","Type","Old Wallet","Txn Amount","Update Wallet","Message","Created At"
        ];
    }
}
