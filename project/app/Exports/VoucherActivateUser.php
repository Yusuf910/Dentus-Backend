<?php

namespace App\Exports;

use App\Models\SalesUserShare;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class VoucherActivateUser implements FromCollection, WithHeadings
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
        $queryqb->where('sales_user_shares.sales_user_id', '=', $this->condition);
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
                $totalused = 0;
                $counter = DB::table('bookings')
                                     ->where('voucher_code_id', $a->shareid)->where('booking_type','bookscan')
                                     ->count();
                if ($counter > 0) {
                    $totalused = $counter;
                }
                $output[] = [
                    'Voucher Shared',
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
                    date('d-M-Y h:ia', strtotime($a->created_at)),
                    $totalused
                  ];

                $usage =DB::table('bookings')->where('voucher_code_id', $a->shareid)->where('booking_type','bookscan')->get();
                if ($usage->count() > 0) {
                    foreach ($usage as $us) {
                         $user2 =  DB::table('users')->where('id',$us->user_id)->first();
                        $output[] = [
                            $voucher->voucher,
                            $user2->id,
                            $user2->name,
                            $user2->email,
                            $user2->mobile,
                            $user2->username,
                            '',
                            '',
                            '',
                            '',
                            '',
                            $voucher->expiry_in_days ,
                            date('d-M-Y h:ia', strtotime($us->created_at)),
                            0
                          ];
                    }
                }

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
            'Roots',
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
            'Total Voucher Activated',
        ];
    }
}
