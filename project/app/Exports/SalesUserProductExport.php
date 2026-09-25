<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
use App\Models\SalesUserShare;
class SalesUserProductExport implements FromCollection, WithHeadings
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
        $queryqb = SalesUserShare::query();
        $queryqb->select('sales_user_shares.*');
        $queryqb->orderBy('sales_user_shares.updated_at','DESC');
        $queryqb->whereNOTIN('sales_user_shares.id',[0]);
        $queryqb->join('users', 'users.id', '=', 'sales_user_shares.sales_user_id');
        $queryqb->join('book_vouchers', 'book_vouchers.id', '=', 'sales_user_shares.shareid');
        if ($this->condition == 1) {
            $queryqb->where('book_vouchers.expiry_in_days', '=', '365');
        }
        if ($this->condition == 2) {
            $queryqb->where('book_vouchers.expiry_in_days', '=', '30');
        }
        if(!is_null($this->request->name)) {
            $queryqb->whereRaw("((users.username like '%" . $this->request->name . "%' ) OR (users.name like '%" . $this->request->name . "%' ) OR (users.email like '%" . $this->request->name . "%' ) OR (users.mobile like '%" . $this->request->name . "%' ) OR (book_vouchers.voucher like '%" . $this->request->name . "%' ))");
        }
        if(!is_null($this->request->start_date) || !is_null($this->request->end_date)) {
            if (!is_null($this->request->start_date) && !is_null($this->request->end_date)) {
                $queryqb->whereBetween('sales_user_shares.created_at', [$this->request->start_date, $this->request->end_date]);
            }
            elseif (!is_null($this->request->start_date)) {
                $queryqb->whereDate('sales_user_shares.created_at', $this->request->start_date);

            }
            elseif (!is_null($this->request->end_date)) {
                $queryqb->whereDate('sales_user_shares.created_at', $this->request->end_date);
            }
            
        }

        $arrays = $queryqb->get();
        // dd($arrays->count());
        $output = [];

        foreach ($arrays as $a)
        {
            $user =  DB::table('users')->where('id',$a->sales_user_id)->first();
            $bookid = DB::table('book_vouchers')->where('id',$a->shareid)->first();
            
          if ($bookid) {
            $b_detail =DB::table('books')->where('id',$bookid->book_id)->first();
            if ($b_detail) {
                $voucher =DB::table('book_vouchers')->where('id',$a->shareid ?? '')->first();
                $output[] = [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->mobile,
                    $user->username,
                    $b_detail->name,
                    $b_detail->board->name ?? '',
                    $b_detail->getclass->name ?? '',
                    $b_detail->series->name ?? '',
                    $voucher->voucher,
                    $voucher->expiry_in_days ,
                    date('d-M-Y h:ia', strtotime($a->created_at))

                  ];
            }
          }
          else {
            echo "<br>".$a->id;
          }
        }
        // dd(/1);
        return collect($output);
    }

    public function headings(): array
    {
        return [
            'UserID',
            'Name',
            'Email',
            'Mobile',
            'Username',
            'Book Name',
            'Board',
            'Class',
            'Series',
            'Voucher Code',
            'Voucher Expiry Days',
            'Shared Date',
        ];
    }
    
}
