<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class UserExport implements FromCollection, WithHeadings
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
        $queryUser = User::query();
        $queryUser->where('user_type',1);
        $queryUser->whereNOTIN('status',[2]);
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
            if($a->status == 1){
                $st =  "Active";}
                elseif($a->status == 0){
                $st = "Inactive";}
            $output[] = [
                    $a->id,
                    $a->name,
                    $a->country,
                    $a->gender,
                    $a->dob,
                    $a->email,
                    $a->phone,
                    $a->wallet ?? '',
                    asset('admin/uploads/user/').'/'.$a->image,
                    $a->birth_time ?? '',
                    $a->place_of_birth,
                    $a->marital_status ,
                    $a->address ,
                    $a->city ,
                    $a->zip ,
                    $a->auth ,
                    $a->device_type ,
                    $a->loginTime ,
                    $a->model_name ,
                    date('d-M-Y h:ia', strtotime($a->created_at)),
                    date('d-M-Y h:ia', strtotime($a->created_at)),
                    $st

                  ];
        }
        // dd(/1);
        return collect($output);
    }

    public function headings(): array
    {
        return [
            "UserID","User Name","Country","Gender","DOB","Email","Phone Number","Wallet Amount","image","birth_time",
                "place_of_birth","marital_status","address","city","zip","auth",
                "device_type","Last Login Time","model_name","Added_On","First time Registration Date","Status"
        ];
    }
}
