<?php

namespace App\Exports;

use App\Models\LoginActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;
class UserAppUsage implements FromCollection, WithHeadings
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
        $queryqb = LoginActivity::query();
        $queryqb->select('login_activities.*');
        $queryqb->orderBy('login_activities.updated_at','DESC');
        $queryqb->whereNOTIN('login_activities.id',[0]);
        $queryqb->join('users', 'users.id', '=', 'login_activities.user_id');
        $queryqb->where('users.user_type', '=', $this->condition);
        if(!is_null($this->request->name)) {
            $queryqb->whereRaw("((users.username like '%" . $this->request->name . "%' ) OR (users.name like '%" . $this->request->name . "%' ) OR (users.email like '%" . $this->request->name . "%' ) OR (users.mobile like '%" . $this->request->name . "%' ) OR (login_activities.ip_address like '%" . $this->request->name . "%' ))");
        }
        if(!is_null($this->request->start_date) || !is_null($this->request->end_date)) {
            if (!is_null($this->request->start_date) && !is_null($this->request->end_date)) {
                $queryqb->whereBetween('login_activities.created_at', [$this->request->start_date, $request->end_date]);
            }
            elseif (!is_null($this->request->start_date)) {
                $queryqb->whereDate('login_activities.created_at', $this->request->start_date);

            }
            elseif (!is_null($this->request->end_date)) {
                $queryqb->whereDate('login_activities.created_at', $this->request->end_date);
            }
            
        }

        $arrays = $queryqb->get();
        // dd($arrays->count());
        $output = [];

        foreach ($arrays as $a)
        {
            $user =  DB::table('users')->where('id',$a->user_id)->first();
            if ($user) {
                $output[] = [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->mobile,
                    $user->username,
                    $a->user_agent,
                    $a->ip_address ?? '',
                    date('d-M-Y h:i:s', strtotime($a->created_at)),
                    date('d-M-Y h:i:s', strtotime($a->updated_at))

                  ];
            }
        }
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
            'User Agent',
            'IP Address',
            'Created At',
            'Updated At',
        ];
    }
}
