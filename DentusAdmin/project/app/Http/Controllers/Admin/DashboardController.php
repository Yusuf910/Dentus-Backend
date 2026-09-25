<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\Booking;
use App\Models\Astrologer;
use App\Models\Blog;
use App\Models\Video;
use App\Models\AstrologerTransaction;
use App\Models\Transactions;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use DB;
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    // public function __construct()
    // {
    //     $this->middleware('auth:web');
    // }

    public function index()
    {

        $todayDate = date("Y-m-d");

        $user = UserProfile::whereNOTIN('status',[2])->orderBy('id','DESC')->count();
        $booking = Booking::whereNOTIN('status',[2,3])->orderBy('id','DESC')->count();
        $doctor = User::whereNOTIN('status',[2,3])->orderBy('id','DESC')->count();
        $today_user_cnt = User::whereDate('created_at', $todayDate)->count();
        // return view('admin.dashboard');
        return view('admin.dashboard',compact(
            'user',
            'today_user_cnt',
            'booking',
            'doctor'
        ));

    }




    public function export_online_astrologer(Request $request)
    {
        if (isset($_GET['onlineExport'])) {
            $data[] = array(
                "ID",
                "Name",
                "Email",
                "Country Code",
                "Mobile Number",
                "Alternate Number",
            );
            $counts = Astrologer::where('status',1)->where('online_status',1)->orderBy('id','DESC')->get();
            //

            foreach ($counts as $count) {
                if($count->name){
                    $nm = json_decode($count->name,true);
                }




                $data[] = array(
                    "id" => $count->id,
                    "name" => $nm[1] ?? '',
                    "email" => $count->email,
                    "country_code" => $count->country_code,
                    "mobile" => $count->mobile,
                    "alternate_mobile" => $count->alternate_mobile,

                );

            }




            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"ManageUser" . $string_file .".csv");
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



    public function export_offline_astrologer(Request $request)
    {
        if (isset($_GET['offlineExport'])) {
            $data[] = array(
                "ID",
                "Name",
                "Email",
                "Country Code",
                "Mobile Number",
                "Alternate Number",
            );
            $counts = Astrologer::where('status',1)->where('online_status',0)->orderBy('id','DESC')->get();
            //

            foreach ($counts as $count) {
                if($count->name){
                    $nm = json_decode($count->name,true);
                }




                $data[] = array(
                    "id" => $count->id,
                    "name" => $nm[1] ?? '',
                    "email" => $count->email,
                    "country_code" => $count->country_code,
                    "mobile" => $count->mobile,
                    "alternate_mobile" => $count->alternate_mobile,

                );

            }




            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"ManageUser" . $string_file .".csv");
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

// Call

    public function export_free_call(Request $request)
    {
        if (isset($_GET['start_date'])) {
            $data[] = array(
                "Date",
                "Hour",
                "Total Count",
                "Completed",
                "Cancelled",
                "Pending",
            );
            $date1 =   DB::table('bookings')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            // ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->where('free', 1)
            ->where('type', 2)
            ->whereBetween('created_at', [$_GET['start_date'].' 00:00:00', $_GET['start_date'].' 23:59:59'])
            ->groupBy('hour')
            ->get();

            $i = 1;
            foreach ($date1 as $user) {

                $today_date =  date('d M y', strtotime($_GET['start_date']));
                if($user->hour == 0){
                    $hours = "12:00 AM - 01:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 1){
                    $hours = "01:00 AM - 02:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 2){
                    $hours = "02:00 AM - 03:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 3){
                    $hours = "03:00 AM - 04:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 4){
                    $hours = "04:00 AM - 05:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 5){
                    $hours = "05:00 AM - 06:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 6){
                    $hours = "06:00 AM - 07:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 7){
                    $hours = "07:00 AM - 08:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 8){
                    $hours = "08:00 AM - 09:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 9){
                    $hours = "09:00 AM - 10:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 10){
                    $hours = "10:00 AM - 11:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 11){
                    $hours = "11:00 AM - 12:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 12){
                    $hours = "12:00 PM - 13:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 13){
                    $hours = "13:00 PM - 14:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 14){
                    $hours = "14:00 PM - 15:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 15){
                    $hours = "15:00 PM - 16:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 16){
                    $hours = "16:00 PM - 17:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 17){
                    $hours = "17:00 PM - 18:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 18){
                    $hours = "18:00 PM - 19:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 19){
                    $hours = "19:00 PM - 20:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 20){
                    $hours = "20:00 PM - 21:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 21){
                    $hours = "21:00 PM - 22:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 22){
                    $hours = "22:00 PM - 23:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 23){
                    $hours = "23:00 PM - 24:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }

                $data[] = array(
                    "today_date" => $today_date,
                    "hour" => $hours,
                    "count" => $user->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,
                );
                $i++;
            }
            $today_date = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"FreeCall" . $today_date . ".csv");
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




    public function export_date_free_call(Request $request)
    {
        if (isset($_GET['all_date'])) {
            $data[] = array(
                "Date",
                "Count",
                "Completed",
                "Cancelled",
                "Pending",
            );


            $counts = DB::table('bookings')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('free', 1)
            ->where('type', 2)
            // ->where('status', 2)
            ->groupBy('date')
            ->get();


            $i = 1;

            foreach ($counts as $count) {

                $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])

                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();



                $data[] = array(
                    "date" => $count->date,
                    "count" => $count->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,

                );
                $i++;

            }



            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"allFreecall" . $string_file . ".csv");
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







    public function export_normal_call(Request $request)
    {
        if (isset($_GET['start_date'])) {
            $data[] = array(
                "Date",
                "Hour",
                "Total Count",
                "Completed",
                "Cancelled",
                "Pending",
            );
            $date1 =   DB::table('bookings')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            // ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->where('free', 0)
            ->where('mode', 5)
            ->where('type', 2)
            ->whereBetween('created_at', [$_GET['start_date'].' 00:00:00', $_GET['start_date'].' 23:59:59'])
            ->groupBy('hour')
            ->get();

            $i = 1;
            foreach ($date1 as $user) {

                $today_date =  date('d M y', strtotime($_GET['start_date']));
                if($user->hour == 0){
                    $hours = "12:00 AM - 01:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 1){
                    $hours = "01:00 AM - 02:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 2){
                    $hours = "02:00 AM - 03:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 3){
                    $hours = "03:00 AM - 04:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 4){
                    $hours = "04:00 AM - 05:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 5){
                    $hours = "05:00 AM - 06:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 6){
                    $hours = "06:00 AM - 07:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 7){
                    $hours = "07:00 AM - 08:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 8){
                    $hours = "08:00 AM - 09:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 9){
                    $hours = "09:00 AM - 10:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 10){
                    $hours = "10:00 AM - 11:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 11){
                    $hours = "11:00 AM - 12:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 12){
                    $hours = "12:00 PM - 13:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 13){
                    $hours = "13:00 PM - 14:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 14){
                    $hours = "14:00 PM - 15:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 15){
                    $hours = "15:00 PM - 16:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 16){
                    $hours = "16:00 PM - 17:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 17){
                    $hours = "17:00 PM - 18:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 18){
                    $hours = "18:00 PM - 19:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 19){
                    $hours = "19:00 PM - 20:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 20){
                    $hours = "20:00 PM - 21:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 21){
                    $hours = "21:00 PM - 22:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 22){
                    $hours = "22:00 PM - 23:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 23){
                    $hours = "23:00 PM - 24:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }

                $data[] = array(
                    "today_date" => $today_date,
                    "hour" => $hours,
                    "count" => $user->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,
                );
                $i++;
            }
            $today_date = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"NormalCall" . $today_date . ".csv");
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




    public function export_date_normal_call(Request $request)
    {
        if (isset($_GET['all_date'])) {
            $data[] = array(
                "Date",
                "Count",
                "Completed",
                "Cancelled",
                "Pending",
            );


            $counts = DB::table('bookings')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('free', 0)
            ->where('mode', 5)
            ->where('type', 2)
            // ->where('status', 2)
            ->groupBy('date')
            ->get();


            $i = 1;

            foreach ($counts as $count) {

                $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 2)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])

                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 3)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 5)
                    ->where('status', 0)
                    ->where('type', 2)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();



                $data[] = array(
                    "date" => $count->date,
                    "count" => $count->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,

                );
                $i++;

            }



            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"allNormalcall" . $string_file . ".csv");
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

    // end call
    // chat


     public function export_free_chat(Request $request)
    {
        if (isset($_GET['start_date'])) {
            $data[] = array(
                "Date",
                "Hour",
                "Total Count",
                "Completed",
                "Cancelled",
                "Pending",
            );
            $date1 =   DB::table('bookings')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            // ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->where('free', 1)
            ->where('type', 3)
            ->whereBetween('created_at', [$_GET['start_date'].' 00:00:00', $_GET['start_date'].' 23:59:59'])
            ->groupBy('hour')
            ->get();

            $i = 1;
            foreach ($date1 as $user) {

                $today_date =  date('d M y', strtotime($_GET['start_date']));
                if($user->hour == 0){
                    $hours = "12:00 AM - 01:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 1){
                    $hours = "01:00 AM - 02:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 2){
                    $hours = "02:00 AM - 03:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 3){
                    $hours = "03:00 AM - 04:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 4){
                    $hours = "04:00 AM - 05:00 AM";

                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 5){
                    $hours = "05:00 AM - 06:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 6){
                    $hours = "06:00 AM - 07:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 7){
                    $hours = "07:00 AM - 08:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 8){
                    $hours = "08:00 AM - 09:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 9){
                    $hours = "09:00 AM - 10:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 10){
                    $hours = "10:00 AM - 11:00 AM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 11){
                    $hours = "11:00 AM - 12:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 12){
                    $hours = "12:00 PM - 13:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 13){
                    $hours = "13:00 PM - 14:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 14){
                    $hours = "14:00 PM - 15:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 15){
                    $hours = "15:00 PM - 16:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 16){
                    $hours = "16:00 PM - 17:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 17){
                    $hours = "17:00 PM - 18:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 18){
                    $hours = "18:00 PM - 19:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 19){
                    $hours = "19:00 PM - 20:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 20){
                    $hours = "20:00 PM - 21:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 21){
                    $hours = "21:00 PM - 22:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 22){
                    $hours = "22:00 PM - 23:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 23){
                    $hours = "23:00 PM - 24:00 PM";
                    $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }

                $data[] = array(
                    "today_date" => $today_date,
                    "hour" => $hours,
                    "count" => $user->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"FreeChat" . $today_date . ".csv");
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




    public function export_date_free_chat(Request $request)
    {
        if (isset($_GET['all_date'])) {
            $data[] = array(
                "Date",
                "Count",
                "Completed",
                "Cancelled",
                "Pending",
            );


            $counts = DB::table('bookings')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('free', 1)
            ->where('type', 3)
            // ->where('status', 2)
            ->groupBy('date')
            ->get();


            $i = 1;

            foreach ($counts as $count) {

                $date_Completed =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])

                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
                    ->where('free', 1)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();



                $data[] = array(
                    "date" => $count->date,
                    "count" => $count->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,

                );
                $i++;

            }



            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"allFreechat" . $string_file . ".csv");
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







    public function export_normal_chat(Request $request)
    {
        if (isset($_GET['start_date'])) {
            $data[] = array(
                "Date",
                "Hour",
                "Total Count",
                "Completed",
                "Cancelled",
                "Pending",
            );
            $date1 =   DB::table('bookings')
            ->select(DB::raw('count(*) as count, HOUR(created_at) as hour'))
            // ->whereDate('created_at', '=', Carbon::now()->toDateString())
            ->where('free', 0)
            ->where('mode', 4)
            ->where('type', 3)
            ->whereBetween('created_at', [$_GET['start_date'].' 00:00:00', $_GET['start_date'].' 23:59:59'])
            ->groupBy('hour')
            ->get();

            $i = 1;
            foreach ($date1 as $user) {

                $today_date =  date('d M y', strtotime($_GET['start_date']));
                if($user->hour == 0){
                    $hours = "12:00 AM - 01:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
              ->where('mode', 4)
              ->where('status', 2)
              ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
              ->where('mode', 4)
              ->where('status', 2)
              ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 01:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 1){
                    $hours = "01:00 AM - 02:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
              ->where('mode', 4)
              ->where('status', 2)
              ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
              ->where('mode', 4)
              ->where('status', 2)
              ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
              ->where('mode', 4)
              ->where('status', 2)
              ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 01:00:00', $_GET['start_date'].' 02:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 2){
                    $hours = "02:00 AM - 03:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
              ->where('mode', 4)
              ->where('status', 2)
              ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
           ->where('mode', 4)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 02:00:00', $_GET['start_date'].' 03:00:00'])
                    // ->groupBy('hour')
                    ->count();

                }
                elseif($user->hour == 3){
                    $hours = "03:00 AM - 04:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 03:00:00', $_GET['start_date'].' 04:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 4){
                    $hours = "04:00 AM - 05:00 AM";

                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 04:00:00', $_GET['start_date'].' 05:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 5){
                    $hours = "05:00 AM - 06:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 05:00:00', $_GET['start_date'].' 06:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 6){
                    $hours = "06:00 AM - 07:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 06:00:00', $_GET['start_date'].' 07:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 7){
                    $hours = "07:00 AM - 08:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 07:00:00', $_GET['start_date'].' 08:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 8){
                    $hours = "08:00 AM - 09:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 08:00:00', $_GET['start_date'].' 09:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 9){
                    $hours = "09:00 AM - 10:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 09:00:00', $_GET['start_date'].' 10:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 10){
                    $hours = "10:00 AM - 11:00 AM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 10:00:00', $_GET['start_date'].' 11:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 11){
                    $hours = "11:00 AM - 12:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                 ->whereBetween('created_at', [$_GET['start_date'].' 11:00:00', $_GET['start_date'].' 12:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 12){
                    $hours = "12:00 PM - 13:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 12:00:00', $_GET['start_date'].' 13:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 13){
                    $hours = "13:00 PM - 14:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 13:00:00', $_GET['start_date'].' 14:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 14){
                    $hours = "14:00 PM - 15:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 14:00:00', $_GET['start_date'].' 15:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 15){
                    $hours = "15:00 PM - 16:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 15:00:00', $_GET['start_date'].' 16:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 16){
                    $hours = "16:00 PM - 17:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 16:00:00', $_GET['start_date'].' 17:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 17){
                    $hours = "17:00 PM - 18:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 17:00:00', $_GET['start_date'].' 18:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 18){
                    $hours = "18:00 PM - 19:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                      ->whereBetween('created_at', [$_GET['start_date'].' 18:00:00', $_GET['start_date'].' 19:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 19){
                    $hours = "19:00 PM - 20:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 19:00:00', $_GET['start_date'].' 20:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 20){
                    $hours = "20:00 PM - 21:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 20:00:00', $_GET['start_date'].' 21:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 21){
                    $hours = "21:00 PM - 22:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                  ->whereBetween('created_at', [$_GET['start_date'].' 21:00:00', $_GET['start_date'].' 22:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 22){
                    $hours = "22:00 PM - 23:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                   ->whereBetween('created_at', [$_GET['start_date'].' 22:00:00', $_GET['start_date'].' 23:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }
                elseif($user->hour == 23){
                    $hours = "23:00 PM - 24:00 PM";
                    $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                     ->where('type', 3)
                    ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                     ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                     ->where('type', 3)
                     ->whereBetween('created_at', [$_GET['start_date'].' 23:00:00', $_GET['start_date'].' 24:00:00'])
                    // ->groupBy('hour')
                    ->count();
                }

                $data[] = array(
                    "today_date" => $today_date,
                    "hour" => $hours,
                    "count" => $user->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Normalchat" . $today_date . ".csv");
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




    public function export_date_normal_chat(Request $request)
    {
        if (isset($_GET['all_date'])) {
            $data[] = array(
                "Date",
                "Count",
                "Completed",
                "Cancelled",
                "Pending",
            );


            $counts = DB::table('bookings')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('free', 0)
            ->where('mode', 4)
            ->where('type', 3)
            // ->where('status', 2)
            ->groupBy('date')
            ->get();


            $i = 1;

            foreach ($counts as $count) {

                $date_Completed =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 2)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])

                    // ->groupBy('hour')
                    ->count();

                    $date_Cancelled =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 3)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();

                    $date_Pending =   DB::table('bookings')
              ->where('free', 0)
            ->where('mode', 4)
                    ->where('status', 0)
                    ->where('type', 3)
                    ->whereBetween('created_at', [$count->date.' 00:00:00', $count->date.' 23:59:59'])
                    // ->groupBy('hour')
                    ->count();



                $data[] = array(
                    "date" => $count->date,
                    "count" => $count->count,
                    "date_Completed" => $date_Completed,
                    "date_Cancelled" => $date_Cancelled,
                    "date_Pending" => $date_Pending,

                );
                $i++;

            }



            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"allNormalchat" . $string_file . ".csv");
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
            ->whereBetween('created_at', [$_GET['start_date'].' 00:00:00', $_GET['start_date'].' 23:59:59'])
            ->groupBy('hour')
            ->get();

            $i = 1;
            foreach ($date1 as $user) {

                $today_date =  date('d M y', strtotime($_GET['start_date']));
                if($user->hour == 0){
                    $hours = "12:00 AM - 01:00 AM";
                }
                elseif($user->hour == 1){
                    $hours = "01:00 AM - 02:00 AM";
                }
                elseif($user->hour == 2){
                    $hours = "02:00 AM - 03:00 AM";
                }
                elseif($user->hour == 3){
                    $hours = "03:00 AM - 04:00 AM";
                }
                elseif($user->hour == 4){
                    $hours = "04:00 AM - 05:00 AM";
                }
                elseif($user->hour == 5){
                    $hours = "05:00 AM - 06:00 AM";
                }
                elseif($user->hour == 6){
                    $hours = "06:00 AM - 07:00 AM";
                }
                elseif($user->hour == 7){
                    $hours = "07:00 AM - 08:00 AM";
                }
                elseif($user->hour == 8){
                    $hours = "08:00 AM - 09:00 AM";
                }
                elseif($user->hour == 9){
                    $hours = "09:00 AM - 10:00 AM";
                }
                elseif($user->hour == 10){
                    $hours = "10:00 AM - 11:00 AM";
                }
                elseif($user->hour == 11){
                    $hours = "11:00 AM - 12:00 PM";
                }
                elseif($user->hour == 12){
                    $hours = "12:00 PM - 13:00 PM";
                }
                elseif($user->hour == 13){
                    $hours = "13:00 PM - 14:00 PM";
                }
                elseif($user->hour == 14){
                    $hours = "14:00 PM - 15:00 PM";
                }
                elseif($user->hour == 15){
                    $hours = "15:00 PM - 16:00 PM";
                }
                elseif($user->hour == 16){
                    $hours = "16:00 PM - 17:00 PM";
                }
                elseif($user->hour == 17){
                    $hours = "17:00 PM - 18:00 PM";
                }
                elseif($user->hour == 18){
                    $hours = "18:00 PM - 19:00 PM";
                }
                elseif($user->hour == 19){
                    $hours = "19:00 PM - 20:00 PM";
                }
                elseif($user->hour == 20){
                    $hours = "20:00 PM - 21:00 PM";
                }
                elseif($user->hour == 21){
                    $hours = "21:00 PM - 22:00 PM";
                }
                elseif($user->hour == 22){
                    $hours = "22:00 PM - 23:00 PM";
                }
                elseif($user->hour == 23){
                    $hours = "23:00 PM - 24:00 PM";
                }

                $data[] = array(
                    "today_date" => $today_date,
                    "hour" => $hours,
                    "count" => $user->count,
                );
                $i++;
            }
            $today_date = date("d-m-Y h:i:s A");

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







}
