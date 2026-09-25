<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingAddress;
use App\Models\BirthdayCalendar;
use App\Models\DiscountCoupon;
use App\Models\Faq;
use App\Models\ProductSizes;
use App\Models\ProductColors;
use App\Models\Categories;

use App\Models\MasterUser;


use App\Models\Transactions;

use App\Models\AstrologerLeaveRequest;

use App\Models\HomeService;
use App\Models\TicketList;

use App\Models\AdminNotification;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Practiceyogacategories;
use PhpOffice\PhpSpreadsheet\IOFactory;
use MongoDB\BSON\ObjectId;
use App\Models\PracticeYogaCoachFile;
class MasterController extends Controller
{


    public function manage_user(Request $request)
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "Name",
                "Email",
                "Mobile",
                "Company",
                "Personal history and influences",
                "unique value",
                "Target Audience",
                "Value Proposition",
                "Audience Engagement",
                "Impact and Legacy",
                "Status",
                "Registered On"
            );


            $queryUser = User::query();
            $queryUser->where('user_type', 1);
            $queryUser->whereNOTIN('status', [2]);
            // $queryUser->orderBy('id', 'DESC');
            if (!is_null($request->name)) {
                $queryUser->whereRaw("(name like '%" . $request->name . "%' )");
                //$queryUser->appends(['name' => $request->name]);
            }

            if (!is_null($request->email)) {
                $queryUser->whereRaw("(email like '%" . $request->email . "%' )");
            }

            if (!is_null($request->mobile)) {
                $queryUser->whereRaw("(mobile like '%" . $request->mobile . "%' )");
            }
            if (!is_null($request->status)) {
                $queryUser->whereRaw("(status like '%" . $request->status . "%' )");
            }

            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryUser->whereDate('created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryUser->whereDate('created_at', $request->end_date);
                }
            }
            $fetch = $queryUser->get();

            $i = 1;
            foreach ($fetch as $a) {
                $st = "";
                if ($a->status == 1) {
                    $st = "Active";
                }else {
                    $st = "Inactive";
                }


                $data[] = array(
                    $a->name,
                    $a->mobile,
                    $a->email,
                    $a->company,
                    $a->about,
                    $a->unique_value,
                    $a->target_audience,
                    $a->mission_statement,
                    $a->key_achievement,
                    $a->personal_brand_voice,
                    $st,
                    \Carbon\Carbon::createFromTimestampMs($a->createdAt ?? 0)->toDateTimeString()
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"User" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        // $data = User::whereNOTIN('status', [2])->get();
        $addbutton = 'Add User';
        $importbutton = 'Upload Excel';
        $title = 'User List';
        

        if ($request->exp == 'export') {
            return Excel::download(new UserExport($request, $full = 0), 'UserList' . date('Y-m-d-h:i:s') . '.xlsx');
        }
        $page_limit = 20;
        $queryUser = User::query();
        $queryUser->where('user_type', 1);
        $queryUser->whereNOTIN('status', [2]);
        // $queryUser->orderBy('id', 'DESC');
        if (!is_null($request->name)) {
            $queryUser->where('name', 'like', '%' . $request->name . '%');
        }

        if (!is_null($request->email)) {
            $queryUser->where('email', 'like', '%' . $request->email . '%');
        }

        if (!is_null($request->mobile)) {
            $queryUser->where('mobile', 'like', '%' . $request->mobile . '%');
        }

        if (!is_null($request->status)) {
            $queryUser->where('status', intval($request->status)); // Convert status to integer if needed
        }

        // Handle Date Range Filtering in MongoDB
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser->whereBetween('createdAt', [
                    new UTCDateTime(strtotime($request->start_date . ' 00:00:00') * 1000),
                    new UTCDateTime(strtotime($request->end_date . ' 23:59:59') * 1000)
                ]);
            } elseif (!is_null($request->start_date)) {
                $queryUser->where('createdAt', '>=', new UTCDateTime(strtotime($request->start_date . ' 00:00:00') * 1000));
            } elseif (!is_null($request->end_date)) {
                $queryUser->where('createdAt', '<=', new UTCDateTime(strtotime($request->end_date . ' 23:59:59') * 1000));
            }
        }

        //Fetch list of results

        $data = $queryUser->paginate($page_limit);
        if (!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if (!is_null($request->email)) {
            $data->appends(['email' => $request->get('email')]);
        }
        if (!is_null($request->mobile)) {
            $data->appends(['mobile' => $request->get('mobile')]);
        }
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        $exporturl = 'admin.master.manage_user';

        dd($data);
    }
    public function users_detail($id)
    {
        $a = User::find($id);
        // $booking = $a->id;
        $birthdayCalendar = BirthdayCalendar::where('user_id', $id)->get();
        $billingAddress = BillingAddress::where('user_id', $id)->get();
        // $ref_data = ReferCodeHistory::where('refer_by_uid', $booking)->get();
        // $ref_count = ReferCodeHistory::where('refer_by_uid', $booking)->count();

        // $m_count = UserMember::where('user_id', $booking)->get();
        // $member_count = UserMember::where('user_id', $booking)->count();
        return view('admin.manage_user.user-details', compact('a', 'birthdayCalendar', 'billingAddress'));
    }









    public function yt_fcm_push_notification($registatoin_ids, $user_type, $message)
    {
        //    print_r($registatoin_ids);die();
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
        $API_SERVER_KEY = 'AAAA2-R5o5Q:APA91bH72dCtucGtvWNfKPXzUY3Eh2XUHOgwya0UDZkpRgwfRPyHtICAMOwlC8du1fcs4IwLhUspcUk8iEE01ELZ7ieqNJj3XGWBCFO_uzOHQy6sP49mqjSBD80V0QEq8iwalADzUlhI';

        // if (Setting::count() > 0) {
        //     $setting = Setting::first();
        // } else {
        //     $setting = new Setting();
        // }

        // if ($user_type == 1) {
        //     $API_SERVER_KEY = $setting->firebase_key;
        // } else if ($user_type == 2) {
        //     $API_SERVER_KEY = $setting->astrologer_firebase;
        // } else if ($user_type == 3) {
        //     $API_SERVER_KEY = $setting->firebase_key;
        // }
        if (!is_array($registatoin_ids)) {
            $device_tokens = [$registatoin_ids];
        } else {
            $device_tokens = $registatoin_ids;
        }
        // dd($API_SERVER_KEY);
        $fields = array(
            'registration_ids' => $device_tokens,
            'data' => $message,
            'notification' => $message,
            // 'notification' => array(
            //     'title' => 'This is title',
            //     'body' => 'This is body'
            // ),
            // 'priority' => ''
            // 'sound'=>'default'
        );
        // dd($fields);
        $headers = array(
            'Authorization:key=' . $API_SERVER_KEY,
            'Content-Type:application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        //    dd($result);
        //    exit;
        return $result;
    }







    public function export_date_user(Request $request)
    {
        if (isset($_GET['all_date'])) {
            $data[] = array(
                "Date",
                "Count",
            );


            $counts = DB::table('users')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->get();


            $i = 1;

            foreach ($counts as $count) {

                $data[] = array(
                    "date" => $count->date,
                    "count" => $count->count,

                );
                $i++;
            }



            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"ManageUser" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }
    }

  


    //end chat



    public function export_user(Request $request)
    {
        if (isset($_GET['start_date'])) {
            $data[] = array(
                "Date",
                "Hour",
                "Count",
            );
            $date1 =   DB::table('users')
                ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
                // ->whereDate('created_at', '=', Carbon::now()->toDateString())
                ->whereBetween('created_at', [$_GET['start_date'] . ' 00:00:00', $_GET['start_date'] . ' 23:59:59'])
                ->groupBy('hour')
                ->get();

            $i = 1;
            foreach ($date1 as $user) {

                $today_date =  date('d M y', strtotime($_GET['start_date']));
                if ($user->hour == 0) {
                    $hours = "12:00 AM - 01:00 AM";
                } elseif ($user->hour == 1) {
                    $hours = "01:00 AM - 02:00 AM";
                } elseif ($user->hour == 2) {
                    $hours = "02:00 AM - 03:00 AM";
                } elseif ($user->hour == 3) {
                    $hours = "03:00 AM - 04:00 AM";
                } elseif ($user->hour == 4) {
                    $hours = "04:00 AM - 05:00 AM";
                } elseif ($user->hour == 5) {
                    $hours = "05:00 AM - 06:00 AM";
                } elseif ($user->hour == 6) {
                    $hours = "06:00 AM - 07:00 AM";
                } elseif ($user->hour == 7) {
                    $hours = "07:00 AM - 08:00 AM";
                } elseif ($user->hour == 8) {
                    $hours = "08:00 AM - 09:00 AM";
                } elseif ($user->hour == 9) {
                    $hours = "09:00 AM - 10:00 AM";
                } elseif ($user->hour == 10) {
                    $hours = "10:00 AM - 11:00 AM";
                } elseif ($user->hour == 11) {
                    $hours = "11:00 AM - 12:00 PM";
                } elseif ($user->hour == 12) {
                    $hours = "12:00 PM - 13:00 PM";
                } elseif ($user->hour == 13) {
                    $hours = "13:00 PM - 14:00 PM";
                } elseif ($user->hour == 14) {
                    $hours = "14:00 PM - 15:00 PM";
                } elseif ($user->hour == 15) {
                    $hours = "15:00 PM - 16:00 PM";
                } elseif ($user->hour == 16) {
                    $hours = "16:00 PM - 17:00 PM";
                } elseif ($user->hour == 17) {
                    $hours = "17:00 PM - 18:00 PM";
                } elseif ($user->hour == 18) {
                    $hours = "18:00 PM - 19:00 PM";
                } elseif ($user->hour == 19) {
                    $hours = "19:00 PM - 20:00 PM";
                } elseif ($user->hour == 20) {
                    $hours = "20:00 PM - 21:00 PM";
                } elseif ($user->hour == 21) {
                    $hours = "21:00 PM - 22:00 PM";
                } elseif ($user->hour == 22) {
                    $hours = "22:00 PM - 23:00 PM";
                } elseif ($user->hour == 23) {
                    $hours = "23:00 PM - 24:00 PM";
                }

                $data[] = array(
                    "today_date" => $today_date,
                    "hour" => $hours,
                    "count" => $user->count,
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"ManageUser" . $today_date . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }
    }


  

    

    public function create_user()
    {
        $title = 'Add User';
        $listname = 'User';
        $listurl = route('admin.master.manage_user');
        $addurl = route('admin.master.store_user');
        return view('admin.manage_user.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_user(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
        ]);
        $a = new User();
        $a->name = $request->name;
        $a->email = $request->email;
        $a->gender = $request->gender;
        $a->dob = $request->dob;
        $a->country_code = "+91";
        $a->mobile = $request->mobile;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.manage_user')
            ->with('success', 'User created successfully !');
    }

    public function edit_user($id)
    {
        $title = 'Edit User';
        $listname = 'User';
        $listurl = route('admin.master.manage_user');
        $editurl = 'admin.master.update_user';
        $a = User::find($id);
        if ($a) {
            return view('admin.manage_user.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.manage_user')
                ->with('failure', 'Not found any data');
        }
    }



    public function update_user(Request $request, $id)
    {
        $this->validate($request, [
            // 'name' => 'required',
            'status' => 'required',
        ]);

        $a = User::find($id);
        $a->name = $request->name;
        $a->email = $request->email;
        $a->gender = $request->gender;
        $a->dob = $request->dob;
        $a->mobile = $request->mobile;
        $a->status = $request->status;
        $a->save();
        // $tkt_data = new TicketList();
        // $tkt_data->admin_id = auth()->user()->id;
        // $tkt_data->user_id = $id;
        // $tkt_data->type = 5;
        // $tkt_data->ticket_id = $request->ticket_id;
        // $tkt_data->ticket_comment = $request->ticket_comment;
        // $tkt_data->save();
        return redirect()->route('admin.master.manage_user')
            ->with('success', 'User updated successfully !');
    }

    public function destroy_user(Request $request)
    {
        $input = $request->all();
        $a = User::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.manage_user')
            ->with('success', 'User deleted successfully !');
    }

  

    //masteruser
    public function masteruser()
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Name Details",
                "Image",
                "Position",
                "Status",
                "Created At"
            );
            $fetch = MasterUser::where('status', '!=', 2)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->status == 1) {
                    $st = "Active";
                } else if ($user->status == 2) {
                    $st = "Deleted";
                } else {
                    $st = "Inactive";
                }
                // $im = <img height="100" width="100" src="{{ asset('content/master-user') }}/{{$a->image}}" alt="">
                // $lang = App\Models\AppLanguage::get();
                // $name = json_decode($user->name,true);

                $data[] = array(
                    "ID" => $i,
                    "name" => $user->name,
                    "image" => $user->image,
                    "position" => $user->position,
                    "status" => $st,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }

            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"MasterTax" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        $data = MasterUser::whereNOTIN('status', [2])->orderBy('name', 'ASC')->get();
        $addbutton = 'Add Testimonial User';
        $importbutton = 'Upload Excel';
        $title = 'Testimonial User List';
        $listurl = route('admin.master.masteruser');
        $addurl = route('admin.master.create_masteruser');
        $importurl = route('admin.master.import_masteruser');
        $editurl = 'admin.master.edit_masteruser';
        $destroyurl = route('admin.master.destroy_masteruser');
        $addurl1 = route('admin.master.store_masteruser');
        return view('admin.masteruser.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_masteruser()
    {
        $title = 'Add Testimonial User';
        $listname = 'Testimonial User';
        $listurl = route('admin.master.masteruser');
        $addurl = route('admin.master.store_masteruser');
        return view('admin.masteruser.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_masteruser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);
        // $image = 'default.png';
        // if (request('image'))
        // {
        //     $fileNameWithTheExtension = request('image')->getClientOriginalName();
        //     $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
        //     $extension = request('image')->getClientOriginalExtension();
        //     $image_name = rand().'_' . time() . '.' . $extension;
        //     // $d = request()->image->storeAs('master-user', $image_name,'contentfolder');
        //     $filePath = request('image')->move('content/master-user', $image);
        //     $image = $image_name;
        // }
        $image = 'default.png';
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/master-user', $image);
        }


        $a = new MasterUser();
        $a->name = json_encode($request->name);
        $a->position = $request->position;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'Testimonial user created successfully');
    }


    public function edit_masteruser($id)
    {
        $title = 'Edit Testimonial User';
        $listname = 'Testimonial User';
        $listurl = route('admin.master.masteruser');
        $editurl = 'admin.master.update_masteruser';
        $a = MasterUser::find($id);
        if ($a) {
            return view('admin.masteruser.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.masteruser')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_masteruser(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);

        $a = MasterUser::find($id);
        $image = $a->image;
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/master-user', $image);


            // $fileNameWithTheExtension = request('image')->getClientOriginalName();
            // $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            // $extension = request('image')->getClientOriginalExtension();
            // $image_name = rand().'_' . time() . '.' . $extension;
            // $d = request()->image->storeAs('master-user', $image_name,'contentfolder');
            // $image = $image_name;
        }

        // print_r( $image); die;

        $a->name = json_encode($request->name);
        $a->position = $request->position;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'Testimonial user updated successfully');
    }

    public function destroy_masteruser(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('masteruser_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.masteruser')
        //                 ->with('failure','You can not delete masteruser due to linking with book');
        // }
        $a = MasterUser::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'Testimonial user deleted successfully');
    }

    public function import_masteruser()
    {
        $title = 'Upload Testimonial user File';
        $listname = 'Testimonial user';
        $listurl = route('admin.master.masteruser');
        $samplebutton = 'Sample File';
        $addurl = route('admin.master.store_import_masteruser');
        $sampleurl = asset('project/public/temp_files/masteruserImportFileSample.csv');
        return view('admin.masteruser.import', compact('title', 'listurl', 'listname', 'samplebutton', 'sampleurl', 'addurl'));
    }

    public function store_import_masteruser(Request $request)
    {
        $this->validate($request, [
            'importfile' => 'required|mimes:csv,txt',
        ]);
        $log = "";
        $filename = '';
        if ($file = $request->file('importfile')) {
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('/temp_files/'), $filename);
        }
        $datas = "";
        $file = fopen(public_path('/temp_files/' . $filename), "r");
        $i = 0;
        while (($line = fgetcsv($file)) !== FALSE) {
            if ($i > 0) {
                $a = new MasterUser();
                $a->name_en = $line[0];
                $a->name_hi = $line[1];
                $a->name_ta = $line[2];
                $a->name_ml = $line[3];
                $a->name_te = $line[4];
                $a->name_kn = $line[5];
                $a->position = $line[6];
                $a->status = $line[7];
                $a->save();
            }
            $i++;
        }
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'master user impoted successfully ' . $log);
    }

   



    //end mall

    public function categories()
    {
        $data = Categories::whereNOTIN('status', [2])->orderBy('id', 'ASC')->paginate(20);
       
        $addbutton = 'Add Categories';
        $importbutton = 'Upload Excel';
        $title = 'Categories List';
        $listurl = route('admin.master.categories');
        $addurl = route('admin.master.create_categories');
        $addurl1 = route('admin.master.store_categories');
        $importurl = route('admin.master.import_categories');
        $editurl = 'admin.master.edit_categories';
        $destroyurl = route('admin.master.destroy_categories');
        return view('admin.categories.index', compact('data','addurl1', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl'));
    }

    public function create_categories()
    {
        $title = 'Add Categories';
        $listname = 'Categories List';
        $listurl = route('admin.master.categories');
        $addurl = route('admin.master.store_categories');
        return view('admin.categories.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_categories(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);

        $a = new Categories();


        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = 'city' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/categories/', $image_name);
        }

        $a->name = $request->name;
        $a->image = $image_name;
        $a->status = $request->flag;
        $a->colors = json_encode($request->colors);
        $a->sizes = json_encode($request->sizes);
        $a->save();


      
        
        return redirect()->route('admin.master.categories')
            ->with('success', 'Categories created successfully !');
    }


    public function edit_categories($id)
    {
        $title = 'Edit categories';
        $listname = 'categories List';
        $listurl = route('admin.master.categories');
        $editurl = 'admin.master.update_categories';
  
        $a = Categories::find($id);
        if ($a) {
            return view('admin.categories.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.categories')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_categories(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);
        $a = Categories::find($id);

        $image_name = $a->image;
        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = 'rand()' . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/categories/', $image_name);
        }



        $a->name = is_null($request->name) ? $a->name : $request->name;
        $a->image = $image_name ;
        $a->status = is_null($request->flag) ? $a->flag : $request->flag;

        if ($request->has('colors')) {
            $a->colors = json_encode($request->input('colors'));
        }
        if ($request->has('sizes')) {
            $a->sizes = json_encode($request->input('sizes'));
        }

        
        $a->save();

        return redirect()->route('admin.master.categories')
            ->with('update', 'Categories updated successfully !');
    }

    public function destroy_categories(Request $request)
    {
        $input = $request->all();
        $a = Categories::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.categories')
            ->with('delete', 'Categories deleted successfully !');
    }

    public function add()
    {
        $categories = [
            'Standing',
            'Seated',
            'Supine',
            'Prone',
            'Arm & Leg Support',
            'Arm Balance & Inversion'
        ];

        foreach ($categories as $name) {
            Practiceyogacategories::firstOrCreate(['name' => $name,'status'=>1,'image'=>'https://mycotalastorage.s3.ca-central-1.amazonaws.com/yoga/category/ff59cb75f07f2ec8333cc36bff841c4e.png']);
        }

    }



    public function importFromExcel(Request $request)
    {
        $filePath = public_path('yoga_poses_full_expanded.xlsx');
        $todopath = public_path('todo.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $imagePrefix = "http://13.201.150.234/yoga_admin/content/posesfile/";

        $todoSpreadsheet = IOFactory::load($todopath);
        $todoSheet = $todoSpreadsheet->getActiveSheet();
        $todoRows = $todoSheet->toArray(null, true, true, true);
        $todoMap = [];
        foreach ($todoRows as $index => $row) {
            if ($index == 1) continue; // skip header
            $idPose = trim($row['A'] ?? '');
            $step = trim($row['B'] ?? '');
            if ($idPose && $step) {
                $todoMap[$idPose][] = [
                    'points' => $step,
                    '_id' => (string)new ObjectId(),
                ];
            }
        }
        $categoryMap = [
            'Standing' => '68e399e27a3d4562990b3092',
            'Seated' => '68e399e27a3d4562990b3093',
            'Supine' => '68e399e27a3d4562990b3094',
            'Prone' => '68e399e27a3d4562990b3095',
            'Arm & Leg Support' => '68e399e27a3d4562990b3096',
            'Arm Balance & Inversion' => '68e399e27a3d4562990b3097',
        ];
        foreach ($rows as $index => $row) {
            if ($index == 1) continue; // skip header row
            $name = trim($row['B'] ?? ''); // File_Name / name
            $typeTracking = trim($row['C'] ?? null);
            $angle = trim($row['D'] ?? null);
            $symetric = trim($row['E'] ?? null);
            $imageFile = trim($row['H'] ?? null); // image filename column if exists
            $categoryName = trim($row['G'] ?? null);
            if (empty($name)) continue;

            $imagePath = $imageFile ? $imagePrefix . $imageFile : $imagePrefix . 'default.jpg';

            $toDo = $todoMap[$row['A']] ?? [];
            $categoryId = isset($categoryMap[$categoryName]) ? new ObjectId($categoryMap[$categoryName]) : null;
            
            $pose = new PracticeYogaCoachFile();
            $pose->name = $name;
            $pose->Type_of_tracking = $typeTracking;
            $pose->Angle = $angle;
            $pose->symetric = $symetric;
            $pose->image = $imagePath;
            $pose->categoryId = $categoryId;
            $pose->status = 1;
            $pose->to_do = $toDo;
            $pose->created_at = now();
            $pose->updated_at = now();
            dd($pose);
            $pose->save();
        }

        return back()->with('success', 'All yoga poses imported successfully!');
    }


    // end cat



}
