<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterLangauage;
use App\Models\AreaOfExpertise;
use App\Models\Astrologer;
use App\Models\Serivcepartners;
use App\Models\AstrologerAreaofExpertise;
use App\Models\AstrologerLanguage;
use App\Models\AstrologerDocumentDetail;
use App\Models\AstrologerServiceFlag;
use App\Models\AstrologerGallery;
use App\Models\AstrologerPrice;
use App\Models\Astrologerpayouts;
use App\Models\AstrologerTransaction;
use App\Models\AstrologerRatingReviews;
use App\Models\AstrologerDatewiseTimes;
use App\Models\TicketList;
use App\Models\Store;
use App\Models\User;
use App\Models\BusinessEnrollment;
use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Support\Str;
use DB;
use Illuminate\Validation\Rule;
use Hash;

class PartnerController extends Controller
{

    public function store_list($id)
    {
       $title = 'Partner Store details';
         

            $stores = DB::table('stores')->where('id', $id)->first();
            // $products = DB::table('products')->where('store_id', $id)->get();
            $products = Product::where('store_id', $id)->whereNOTIN('status', [2])->orderBy('id', 'ASC')->paginate(10);
            return view('admin.partnerstore.product', compact( 'stores','title','products'));
        
    }


    public function show_product($id)
    {
        $product = Product::find($id);
        $product_image =   ProductImage::where('product_id', $id)->get();
        return view('admin.partnerstore.product_details', compact('product','product_image'));
    }



    public function updateStatusAjax(Request $request)
    {
        $record = User::find($request->id);
        // print_r($request->is_approved); die;
    
        if ($record) {
            // Toggle the is_approve status
            $record->is_approved = $request->is_approved;
            $record->save();

            $recordEnrollment = BusinessEnrollment::where('user_id', $record->id)->first();
            $recordEnrollment->is_approved = $request->is_approved;
            $recordEnrollment->save();


            if($request->is_approved == "1"){
                $this->send_Approval_sms($record->mobile);
            }
    
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }
    }


    public function storeupdateStatusAjax(Request $request)
    {
        $record = Store::find($request->id);
        // print_r($request->is_approved); die;
    
        if ($record) {
            // Toggle the is_approve status
            $record->is_approved = $request->is_approved;
            $record->save();

            if($request->is_approved == "1"){

                $store_user = User::find($record->user_id);

                $this->send_Approval_sms_store($store_user->mobile);
            }
    
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Record not found']);
        }
    }


    public function send_Approval_sms_store( $mobile='')
    { 
       
        $message = "Congrats! Your store is approved. You can now manage your listings. Thanks for choosing UO - Empower your business. UO Tech";
        $url = "https://api.pinnacle.in/index.php/sms/send/TECHUO/".urlencode($mobile)."/".urlencode($message)."/TXT?apikey=3224ea-b3dab1-b6829a-10065c-db82c6&dlttempid=1707173087363146160";
        // echo $url;die;
           
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            // CURLOPT_POSTFIELDS => "{\"OTP\": \"".$otp."\"}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            // curl_close($curl);
    
        //     if ($err) {
        //         return $err; 
        //     return false; 
        //     } else {
        //         return $response;
        //    return true;
        //     }
    }

    
    // Signup OTP
    public function send_Approval_sms( $mobile='')
    { 
       
        $message = "Congrats! Your business is approved. You can now manage stores and listings. Thanks for choosing UO - Empower your business. UO Tech";
        $url = "https://api.pinnacle.in/index.php/sms/send/TECHUO/".urlencode($mobile)."/".urlencode($message)."/TXT?apikey=3224ea-b3dab1-b6829a-10065c-db82c6&dlttempid=1707173087366210641";
        // echo $url;die;
           
            $curl = curl_init();
        
            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            // CURLOPT_POSTFIELDS => "{\"OTP\": \"".$otp."\"}",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
    
            // curl_close($curl);
    
        //     if ($err) {
        //         return $err; 
        //     return false; 
        //     } else {
        //         return $response;
        //    return true;
        //     }
    }




    public function partner(Request $request, $what = '')
    {
        // print_r($what); die;
        if (isset($_GET['export_file'])) {

        // 
            // $queryqb->where('astrologers.status',$what);


            $data[] = array(
                "ID",
                "Name",
                "Slug",
                // "Screen Name",
                "Email",
                "Country Code",
                "Mobile Number",
                "Alternate Number",
                "Gender",
                "About",
                "Image",
                "Experience",
                "In Homepage",
                "In House Astrologer",
                "Status",
                "Position",
                "Recommend Status",
                "Free Call",
                "Online Status",
                "Average Rating",
                "Average Price",
                "Is-premium",
                "Ultra Premium",
                "Ask a Question",
                "Website Link",
                "Wikipedia Link",
                "Fixed Percentage",
                "Fixed Commission",
                "Share Percentage",
                "GST Percentage",
                "TDS Percentage",
                "Consultation",
                "Age",
                "Date of Birth",
                "Address",
                "Total Consultation",
                "Created At"
            );
            // $queryqb->where('astrologers.status',$what)


            $queryqb = User::query();
            $queryqb->select('users.*');
            $queryqb->orderBy('users.updated_at', 'DESC');
            // $queryqb->whereNOTIN('users.id',[0,1]);
            $queryqb->where('users.status', $what);
            $queryqb->where('users.user_type', 2);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.screen_name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {

                    // $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);


                    $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('users.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('users.created_at', $request->end_date);
                }
            }

            $fetch = $queryqb->get();


            // $fetch = DB::table('users')->where('status', '!=',$what)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->status == 1) {
                    $st = "Active and Verified";
                } else if ($user->status == 2) {
                    $st = "Deleted";
                } else if ($user->status == 3) {
                    $st = "Unverified";
                } else {
                    $st = "Inactive";
                }

                $freecall = "";
                if ($user->free_call == 1) {
                    # code...
                    $freecall = "Yes";
                } else {
                    # code...
                    $freecall = "No";
                }

                $homepage = "";
                if ($user->in_homepage == 1) {
                    # code...
                    $homepage = "Yes";
                } else {
                    # code...
                    $homepage = "No";
                }

                $house = "";
                if ($user->in_house_astrologer == 1) {
                    # code...
                    $house = "Yes";
                } else {
                    # code...
                    $house = "No";
                }

                $recommend = "";
                if ($user->recommend_status == 1) {
                    # code...
                    $recommend = "Yes";
                } else {
                    # code...
                    $recommend = "No";
                }

                $online = "";
                if ($user->online_status == 1) {
                    # code...
                    $online = "Yes";
                } else {
                    # code...
                    $online = "No";
                }

                $premium = "";
                if ($user->is_premium == 1) {
                    # code...
                    $premium = "Yes";
                } else {
                    # code...
                    $premium = "No";
                }

                $nm = json_decode($user->name, true);
                // $nm2 = json_decode($user->screen_name, true);
                $abt = json_decode($user->about, true);
                $data[] = array(
                    "ID" => $i,
                    "name" => $nm[1] ?? '',
                    "slug" => $user->slug,
                    // "screen_name" => $nm2[1] ?? '',
                    "email" => $user->email,
                    "country_code" => $user->country_code,
                    "mobile" => $user->mobile,
                    "alternate_mobile" => $user->alternate_mobile,
                    "gender" => $user->gender,
                    "about" => $user->abt[1] ?? '',
                    "image" => $user->image,
                    "experience" => $user->experience,
                    "in_homepage" => $homepage,
                    "in_house_astrologer" => $house,
                    "status" => $st,
                    "position" => $user->position,
                    "recommend_status" => $recommend,
                    "free_call" => $freecall,
                    "online_status" => $online,
                    "average_rating" => $user->average_rating,
                    "average_price" => $user->average_price,
                    "is_premium" => $premium,
                    "ultra_premium" => $user->ultra_premium,
                    "can_askquestion" => $user->can_askquestion,
                    "website_link" => $user->website_link,
                    "wikipedia_link" => $user->wikipedia_link,
                    "is_fixed_percentage" => $user->is_fixed_percentage,
                    "fixed_commission " => $user->fixed_commission,
                    "share_percentage " => $user->share_percentage,
                    "gst_perct " => $user->gst_perct,
                    "tds_perct " => $user->tds_perct,
                    "consultation " => $user->consultation,
                    "age " => $user->age,
                    "dob " => $user->dob,
                    "address " => $user->address,
                    "total_consultation " => $user->total_consultation,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Astrologer" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }


        if (isset($_GET['export_payout'])) {

            $data[] = array(
                "Id",
                "Name",
                "Total Booking",
                "Total Booking Revenue",
                "Admin Earnings",
                "Astrologer Booking Earnings",
                "Astrologer Incentive Earnings",
                "Astrologer Pending Payout",
                "Total TDS",
                "Total PG",
                "Total Paid Complete Booking",
                "Total Free Complete Booking",
                "Total Ask a Question",
                "Pan Number",
                "Account Name",
                "Account Number ",
                "Account Type",
                "IFSC Code",
            );
            $fetch = DB::table('users')->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('users')->where('status', 1)->paginate(500000);
            $i = 1;
            foreach ($fetch as $user) {
                // if(isset($_GET['start_date']) && !empty($_GET['end_date'])){
                // $s_date = $_GET['start_date'];
                // $e_date = $_GET['end_date'];


                $bank_details =  DB::table('astrologer_document_details')->where('astrologer_id', $user->id)->first();
                if ($bank_details->bank_account_number ==  "SBI098989887") {
                    $pan_number =  "";
                    $account_name =  "";
                    $account_number =  "";
                    $account_type =  "";
                    $ifsc_code =  "";
                } else {
                    $pan_number =  $bank_details->pancard;
                    $account_name =  $bank_details->account_holder_name;
                    $account_number =   "'" . $bank_details->bank_account_number;
                    $account_type =  $bank_details->account_type;
                    $ifsc_code =  $bank_details->ifsc_code;
                }

                if (!is_null($request->s_date) && !is_null($request->e_date)) {

                    $total_book = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();

                    $booking_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('price');

                    $astrologer_comission_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('amount');

                    $astrologer_incentive_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('amount');



                    $total_tds_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');

                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tax_price');

                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();
                } elseif (!is_null($request->s_date)) {


                    $total_book = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->count();

                    $booking_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('price');

                    $astrologer_comission_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('amount');

                    $astrologer_incentive_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('amount');

                    // $total_credit_payouts = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "credit")
                    //     ->whereDate('created_at', $request->s_date)
                    //     ->sum('amount');

                    // $total_debit_payouts = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "debit")
                    //     ->whereDate('created_at', $request->s_date)
                    //     ->sum('amount');

                    $total_tds_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');

                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tax_price');

                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereDate('created_at', $request->s_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('created_at', $request->s_date)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereDate('created_at', $request->s_date)
                        ->count();
                } elseif (!is_null($request->e_date)) {

                    $total_book = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->count();

                    $booking_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('price');

                    $astrologer_comission_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('amount');

                    $astrologer_incentive_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('amount');

                    // $total_credit_payouts = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "credit")
                    //     ->whereDate('created_at', $request->e_date)
                    //     ->sum('amount');

                    // $total_debit_payouts = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "debit")
                    //     ->whereDate('created_at', $request->e_date)
                    //     ->sum('amount');

                    $total_tds_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tax_price');

                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereDate('created_at', $request->e_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('created_at', $request->e_date)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereDate('created_at', $request->e_date)
                        ->count();
                } else {

                    $total_book = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->count();

                    $booking_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->sum('price');

                    $astrologer_comission_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "0")
                        ->sum('amount');

                    $astrologer_incentive_amount = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->sum('amount');

                    $total_credit_payouts = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->sum('amount');

                    $total_debit_payouts = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->sum('amount');

                    $total_tds_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->sum('tds_price');

                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->sum('tax_price');

                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->count();
                }




                $total_credit_payouts = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('status', '=', 1)
                    // ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('amount');

                $total_debit_payouts = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('status', '=', 1)
                    // ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('amount');




                $nm = json_decode($user->name, true);
                $data[] = array(
                    "ID" => $i,
                    "name" => $nm[1] ?? '',
                    "total_booking" =>  $total_book,
                    "booking_amount" => $booking_amount,
                    "admin_earnings" => sprintf('%0.2f', $booking_amount - $astrologer_comission_amount),
                    "astrologer_comission_amount" => sprintf('%0.2f', $astrologer_comission_amount),
                    "astrologer_incentive_amount" => sprintf('%0.2f', $astrologer_incentive_amount),
                    // "Astrologer_pending_payout" => sprintf('%0.2f', $total_debit_payouts),
                    "Astrologer_pending_payout" => sprintf('%0.2f', $total_credit_payouts - $total_debit_payouts),
                    "total_tds_astro" => sprintf('%0.2f', $total_tds_astro),
                    "total_gst_astro" => sprintf('%0.2f', $total_gst_astro),
                    "total_paid_complete_booking" => $total_paid_complete_booking,
                    "total_free_booking" => $total_free_booking,
                    "total_ask_booking" => $total_ask_booking,
                    "pan_number" =>  $pan_number,
                    "account_name" => $account_name,
                    "account_number" => $account_number,
                    "account_type" => $account_type,
                    "ifsc_code" => $ifsc_code,
                    // "created_at" => date('d M y', strtotime($user->created_at)),
                );

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"AstrologerPayout" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        if ($request->exp == 'export') {
            $dataexport[] = array(
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
            );
            $i = 1;
            $queryqb = SalesUserShare::query();
            $queryqb->select('sales_user_shares.*');
            $queryqb->orderBy('sales_user_shares.updated_at', 'DESC');
            $queryqb->whereNOTIN('sales_user_shares.id', [0]);
            $queryqb->join('users', 'users.id', '=', 'sales_user_shares.sales_user_id');
            $queryqb->join('book_vouchers', 'book_vouchers.id', '=', 'sales_user_shares.shareid');
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.username like '%" . $request->name . "%' ) OR (users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ) OR (book_vouchers.voucher like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('sales_user_shares.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('sales_user_shares.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('sales_user_shares.created_at', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                $user =  DB::table('users')->where('id', $a->sales_user_id)->first();
                $bookid = DB::table('book_vouchers')->where('id', $a->shareid)->first();

                if ($bookid) {
                    $b_detail = DB::table('books')->where('id', $bookid->book_id)->first();
                    if ($b_detail) {
                        $voucher = DB::table('book_vouchers')->where('id', $a->shareid ?? '')->first();
                        $dataexport[] = [
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
                            $voucher->expiry_in_days,
                            date('d-M-Y h:ia', strtotime($a->created_at))

                        ];
                        $i++;
                    }
                }
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"SalesUserProduct" . date('Y-m-d-h:i:s') . '.csv');
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            foreach ($dataexport as $dataexport) {
                fputcsv($handle, $dataexport);
            }
            fclose($handle);
            exit;
            // return Excel::download(new SalesUserProductExport($request,$full=0), 'SalesUserProduct'.date('Y-m-d-h:i:s').'.xlsx');
        }
        if ($what == 4) {
            $queryqb = User::query();
            $queryqb->select('users.*');

            $queryqb->orderBy('users.updated_at', 'DESC');
            // $queryqb->whereNOTIN('users.id',[0]);
            $queryqb->where('users.ultra_premium', 1);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.screen_name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

                    // $queryqb->whereBetween('users.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('users.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('users.created_at', $request->end_date);
                }
            }

            $data = $queryqb->paginate(100);
        } else {
            $queryqb = User::query();
            $queryqb->select('users.*');
            $queryqb->orderBy('users.updated_at', 'DESC');
            $queryqb->where('users.user_type', 2);
            // $queryqb->whereNOTIN('users.id',[0,1]);
            // $queryqb->where('users.status', $what);
            if($what == 1){
                // $queryqb->where('users.status', $what);
                $queryqb->whereIN('users.status', [0,1]);
                $queryqb->where('users.is_approved', 1);
            }
            else{
                $queryqb->whereIN('users.status', [0,1]);
                $queryqb->where('users.is_approved', 0);
            }
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.screen_name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

                    // $queryqb->whereBetween('users.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('users.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('users.created_at', $request->end_date);
                }
            }

            $data = $queryqb->paginate(100);
        }

        // dd($data);
        if (!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }
        $data->appends(request()->except('page'));
        $exporturl = 'admin.partner-manage.partner';
        $addbutton = 'Add Partner';
        $importbutton = 'Upload Excel';
        $title = 'Partner List';
        $listurl = route('admin.partner-manage.partner');
        $addurl = route('admin.partner-manage.create_partner', $what);
        $importurl = route('admin.partner-manage.import_partner');
        $editurl = 'admin.partner-manage.edit_partner';
        $loginurl = 'admin.partner-manage.store_partner';
        $destroyurl = route('admin.partner-manage.destroy_partner');
        $addurl1 = route('admin.partner-manage.partner');
        $viewurl = 'admin.partner-manage.view_partner';

        return view('admin.partner.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'exporturl', 'what', 'viewurl', 'loginurl'));
    }

    public function store_partner($id, $what = '')
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Astrologer",
                "Ip Address",
                "Status",
                "Created At"
            );
            $fetch = DB::table('astrologer_login_histories')->where('user_id', $id)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->online_status == 1) {
                    $st = "Online";
                } else {
                    $st = "Offline";
                }
                $ad = DB::table('astrologers')->where('id', $user->user_id)->first();
                $name = json_decode($ad->name, true);
                $data[] = array(
                    "ID" => $i,
                    "name" => $name[1],
                    "email " => $user->ip_address,
                    "status" => $st,
                    "created_at" => $user->created_at,
                    // "created_at" => date('d M y', strtotime($user->created_at)),
                );

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Login Astrologer" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        $title = 'Partner Store details';
        $listname = 'Login details';
        $listurl = route('admin.partner-manage.partner', $what);
        $editurl = 'admin.partner-manage.edit_partner';
        // $a = User::find($id);
        $data = DB::table('stores')->where('user_id', $id)->get();
        if ($data) {
            return view('admin.partnerstore.index', compact('data', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->route('admin.partner-manage.partner', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function view_partner($id, $what = '')
    {
        $title = 'View partner';
        $listname = 'Partner';
        $listurl = route('admin.partner-manage.partner', $what);
        $editurl = 'admin.partner-manage.edit_partner';
        $a = User::find($id);
        $business_enrollment = BusinessEnrollment::where('user_id', $id)->first();
        if ($a) {
            return view('admin.partner.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','business_enrollment'));
        } else {
            return redirect()->route('admin.partner-manage.partner', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function create_astrologer($what = '')
    {
        $title = 'Add Astrologer';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $addurl = route('admin.astrologer-manage.store_astrologer', $what);
        return view('admin.astrologer.create', compact('title', 'listurl', 'listname', 'addurl', 'what'));
    }

    public function import_astrologer()
    {
        $title = 'Upload Astrologer File';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $samplebutton = 'Sample File';
        $addurl = route('admin.astrologer-manage.store_import_astrologer');
        $sampleurl = asset('project/public/temp_files/astrologerImportFileSample.csv');
        return view('admin.astrologer.import', compact('title', 'listurl', 'listname', 'samplebutton', 'sampleurl', 'addurl'));
    }

    public function store_import_astrologer_old(Request $request)
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
            // print_r($line); die;
            if ($i > 0) {

                $name[1] = $line[0];
                $name[2] = $line[0];
                $name[3] = $line[0];
                $name[4] = $line[0];
                $name[5] = $line[0];
                $name[6] = $line[0];


                $d[1] = $line[0];
                $d[2] = $line[1];
                $d[3] = $line[2];
                $d[4] = $line[3];
                $d[5] = $line[4];
                $d[6] = $line[5];




                $about[1] = $line[10];
                $about[2] = $line[11];
                $about[3] = $line[12];
                $about[4] = $line[13];
                $about[5] = $line[14];
                $about[6] = $line[15];



                // dd( $ddddd);
                $a = new astrologer();
                $a->name = json_encode($name, JSON_UNESCAPED_UNICODE);
                $a->slug = $line[0];
                $a->slug = $line[0];
                // $a->screen_name = json_encode($d, JSON_UNESCAPED_UNICODE);
                $a->mobile = $line[16];
                $a->experience = $line[7];

                $a->average_rating = $line[8];
                $a->image = $line[9];
                $a->status = 1;
                $a->about = json_encode($about, JSON_UNESCAPED_UNICODE);


                $a->save();


                $ddddd = trim($line[6]);

                $ff =  explode(",", $ddddd);

                if ($a) {
                    foreach ($ff as $areaa) {
                        // dd(trim($areaa));

                        $ss = trim($areaa);
                        $check = AreaOfExpertise::where('exp_name', $ss)->first();
                        if ($check) {
                            $l = new AstrologerAreaofExpertise();
                            $l->astrologer_id = $a->id;
                            $l->areaofexpertise_id = $check->id;
                            $l->status = 1;
                            $l->save();
                        }
                    }
                }
            }
            $i++;
        }
        return redirect()->route('admin.astrologer-manage.astrologer')
            ->with('success', 'master user impoted successfully ' . $log);
    }



    public function store_import_astrologer(Request $request)
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
            // print_r($line); die;
            if ($i > 0) {

                $name[1] = $line[0];
                $name[2] = $line[0];
                $name[3] = $line[0];
                $name[4] = $line[0];
                $name[5] = $line[0];
                $name[6] = $line[0];


                $screen_name[1] = $line[19];
                $screen_name[2] = $line[20];
                $screen_name[3] = $line[21];
                $screen_name[4] = $line[22];
                $screen_name[5] = $line[24];
                $screen_name[6] = $line[23];




                $about[1] = $line[25];
                $about[2] = $line[26];
                $about[3] = $line[27];
                $about[4] = $line[28];
                $about[5] = $line[30];
                $about[6] = $line[29];



                // dd( $ddddd);
                $a = new astrologer();
                $a->name = json_encode($name, JSON_UNESCAPED_UNICODE);
                $a->age = $line[1];
                $a->dob = $line[2];
                $a->gender = $line[3];
                $a->mobile = $line[4];
                $a->alternate_mobile = $line[5];
                $a->email = $line[6];
                $a->address = $line[7];
                $a->experience = $line[10];
                $a->total_consultation = $line[11];
                $a->average_rating = $line[14];

                $a->screen_name = json_encode($screen_name, JSON_UNESCAPED_UNICODE);

                $a->status = 0;
                $a->about = json_encode($about, JSON_UNESCAPED_UNICODE);


                $a->save();

                // AreaOfExpertise
                $area = trim($line[13]);
                $areaexpertise =  explode(",", $area);
                // astrologer_languages
                $languages = trim($line[12]);
                $a_languages =  explode(",", $languages);

                if ($a) {
                    // AstrologerDocumentDetai

                    $doc = new AstrologerDocumentDetail();
                    $doc->astrologer_id =    $a->id;
                    $doc->aadhar_number = $line[8];
                    $doc->pancard = $line[9];
                    $doc->bank_account_number  = $line[15];
                    $doc->bank_name  = $line[16];
                    $doc->ifsc_code  = $line[17];
                    $doc->account_holder_name  = $line[18];
                    $doc->save();


                    foreach ($a_languages as $lang) {
                        // dd(trim($areaa));
                        $lang_name = trim($lang);
                        $check_lang = MasterLangauage::where('name', $lang_name)->first();
                        if ($check_lang) {
                            $l = new AstrologerLanguage();
                            $l->astrologer_id = $a->id;
                            $l->language_id = $check_lang->id;
                            $l->status = 1;
                            $l->save();
                        }
                    }




                    foreach ($areaexpertise as $areaa) {
                        // dd(trim($areaa));
                        $ss = trim($areaa);
                        $check = AreaOfExpertise::where('exp_name', $ss)->first();
                        if ($check) {
                            $l = new AstrologerAreaofExpertise();
                            $l->astrologer_id = $a->id;
                            $l->areaofexpertise_id = $check->id;
                            $l->status = 1;
                            $l->save();
                        }
                    }
                }
            }
            $i++;
        }
        return redirect()->route('admin.astrologer-manage.astrologer')
            ->with('success', 'Astrologer impoted successfully ' . $log);
    }




    public function store_astrologer(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required',
            'email' => [
                'required',
                Rule::unique('astrologers')->where(function ($query) {
                    $query->whereNOTIN('status', [2]);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('astrologers')->where(function ($query) {
                    $query->whereNOTIN('status', [2]);
                })
            ],
            'gender' => 'required',
            'experience' => 'required',
            'average_rating' => 'required',
            'status' => 'required',
        ]);
        $image_name = 'default.png';
        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/astrologer/image', $image_name);
        }
        $a = new Astrologer();
        // dd($a); die;
        $a->name = json_encode($request->name);
        $a->slug = $request->slug;
        $a->email = $request->email;
        $a->country_code = $request->country_code;
        $a->mobile = $request->mobile;
        $a->alternate_mobile = $request->alternate_mobile;
        $a->password = Hash::make(123456);
        $a->gender = $request->gender;
        $a->age = $request->age;
        $a->dob = $request->dob;
        $a->address = $request->address;
        $a->total_consultation = $request->total_consultation;
        $a->about = json_encode($request->about);
        $a->image = $image_name;
        $a->experience = $request->experience;
        $a->tags = $request->tags;
        $a->in_homepage = $request->in_homepage;
        $a->in_house_astrologer = $request->in_house_astrologer;
        $a->status = $request->status;
        $a->position = $request->position;
        $a->recommend_status = $request->recommend_status;
        $a->free_call = $request->free_call ?? 0;
        $a->online_status = $request->online_status;
        $a->average_rating = $request->average_rating;
        // $a->average_price = $request->average_price;
        $a->is_premium = $request->is_premium;
        $a->website_link = $request->website_link;
        $a->wikipedia_link = $request->wikipedia_link;
        // $a->is_fixed_percentage = $request->is_fixed_percentage;
        // $a->fixed_commission = $request->fixed_commission;
        // $a->share_percentage = $request->share_percentage;
        // $a->gst_perct = $request->gst_perct;
        // $a->tds_perct = $request->tds_perct;
        $a->consultation = $request->consultation;
        if ($request->in_house_astrologer == 1) {
            $a->can_askquestion = 1;
        } else {
            $a->can_askquestion = 0;
        }
        $a->create_profile = 1;
        $a->save();

        // if($request->area != null){
        //     foreach ($request->area as $areaa) {
        //         $check = AstrologerAreaofExpertise::where('astrologer_id',$a->id)->where('areaofexpertise_id',$areaa)->first();
        //         if ($check) {
        //             $check->status = 1;
        //             $check->save();
        //         }
        //         else {
        //             $l = new AstrologerAreaofExpertise();
        //             $l->astrologer_id = $a->id;
        //             $l->areaofexpertise_id = $areaa;
        //             $l->status = 1;
        //             $l->save();
        //         }
        //     }
        // }
        // if($request->area != null){
        //     foreach ($request->lang as $l) {
        //         $check = AstrologerLanguage::where('astrologer_id', $a->id)->where('language_id',$l)->first();
        //         if ($check) {
        //             $check->status = 1;
        //             $check->save();
        //         }
        //         else {
        //             $ln = new AstrologerLanguage();
        //             $ln->astrologer_id = $a->id;
        //             $ln->language_id = $l;
        //             $ln->status = 1;
        //             $ln->save();
        //         }
        //     }
        // }
        $aadhaar_name = 'default.png';
        if (request('aadharcard_image')) {
            $fileNameWithTheExtension = request('aadharcard_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('aadharcard_image')->getClientOriginalExtension();
            $aadhaar_name = rand() . '_aadharcard_image_' . time() . '.' . $extension;
            $filePath = request('aadharcard_image')->move('content/astrologer/documents', $aadhaar_name);
        }



        $tag_image_name =  '';
        if (request('tag_image')) {
            $fileNameWithTheExtension = request('tag_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('tag_image')->getClientOriginalExtension();
            $tag_image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('tag_image')->move('content/astrologer/image', $tag_image_name);
        }



        $pan_name = 'default.png';
        if (request('pancard_image')) {
            $fileNameWithTheExtension = request('pancard_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('pancard_image')->getClientOriginalExtension();
            $pan_name = rand() . '_pancard_' . time() . '.' . $extension;
            $filePath = request('pancard_image')->move('content/astrologer/documents', $pan_name);
        }
        $qualification = 'default.png';
        if (request('qualification')) {
            $fileNameWithTheExtension = request('qualification')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('qualification')->getClientOriginalExtension();
            $qualification = rand() . '_qualification_' . time() . '.' . $extension;
            $filePath = request('qualification')->move('content/astrologer/documents', $qualification);
        }

        $c = new AstrologerDocumentDetail();
        $c->astrologer_id = $a->id;
        $c->pancard = $request->pancard ?? '';
        $c->aadhar_number = $request->aadhar_number ?? '';
        $c->aadharcard_image = $aadhaar_name ?? '';
        $c->pancard_image = $pan_name ?? '';
        $c->qualification = $qualification ?? '';
        $a->tag_image = $tag_image_name ?? '';
        $c->bank_account_number = $request->bank_account_number ?? '';
        $c->account_type = $request->account_type ?? '';
        $c->ifsc_code = $request->ifsc_code ?? '';
        $c->account_holder_name = $request->account_holder_name ?? '';
        $c->save();

        return redirect()->route('admin.astrologer-manage.astrologer', $request->what)
            ->with('success', 'Astrologer created successfully');
    }


    public function edit_astrologer($id, $what = '')
    {
        $title = 'Edit Astrologer';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.astrologer-manage.update_astrologer';
        $a = User::find($id);

        if (empty($a->name)) {
            //    echo "Sdsds"; die;
            $a->name = '{"1":"Astrologer","2":"Astrologer","3":"Astrologer","4":"Astrologer","5":"Astrologer","6":"Astrologer"}';
            $a->save();
        }


        if ($a) {
            return view('admin.astrologer.edit', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->route('admin.astrologer-manage.astrologer', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function update_astrologer(Request $request, $id)
    {
        // print_r("ddd"); die;
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required',
            'email' => [
                'required',
                Rule::unique('astrologers')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('astrologers')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'experience' => 'required',
            'average_rating' => 'required',
            // 'average_price' => 'required',
            // 'position' => 'required',
            'status' => 'required',
        ]);

        $a = User::find($id);
        $image_name = $a->image;
        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/astrologer/image', $image_name);
        }

        if ($request->tag_image_show == 1) {
            $tag_image_name = $a->tag_image;
            if (request('tag_image')) {
                $fileNameWithTheExtension = request('tag_image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('tag_image')->getClientOriginalExtension();
                $tag_image_name = rand() . '_profile_' . time() . '.' . $extension;
                $filePath = request('tag_image')->move('content/astrologer/image', $tag_image_name);
            }
        } else {
            $tag_image_name = "";
        }
        if ($request->status == 1) {
            $create_profile = 1;
        } else {
            $create_profile = 0;
        }

        $a->name = json_encode($request->name);
        $a->slug = $request->slug;
        $a->email = $request->email;
        $a->country_code = $request->country_code;
        $a->mobile = $request->mobile;
        $a->alternate_mobile = $request->alternate_mobile;
        $a->password = Hash::make(123456);
        $a->gender = $request->gender;
        $a->age = $request->age;
        $a->dob = $request->dob;
        $a->address = $request->address;
        $a->total_consultation = $request->total_consultation;
        // $a->free_call = $request->free_call;
        $a->about = json_encode($request->about);
        $a->image = $image_name;
        $a->tag_image = $tag_image_name;
        $a->experience = $request->experience;
        $a->tags = $request->tags;
        $a->in_homepage = $request->in_homepage;
        $a->in_house_astrologer = $request->in_house_astrologer;
        $a->status = $request->status;
        $a->create_profile = $create_profile;
        $a->position = $request->position;
        $a->recommend_status = $request->recommend_status;
        $a->free_call = $request->free_call ?? 0;
        $a->online_status = $request->online_status;
        $a->average_rating = $request->average_rating;
        // $a->average_price = $request->average_price;
        $a->is_premium = $request->is_premium;
        $a->website_link = $request->website_link;
        $a->wikipedia_link = $request->wikipedia_link;
        // $a->share_percentage = $request->share_percentage;
        // $a->gst_perct = $request->gst_perct;
        // $a->tds_perct = $request->tds_perct;
        $a->consultation = $request->consultation;
        // $a->is_fixed_percentage = $request->is_fixed_percentage;
        // $a->fixed_commission = $request->fixed_commission;
        if ($request->in_house_astrologer == 1) {
            $a->can_askquestion = 1;
        } else {
            $a->can_askquestion = 0;
        }
        $a->save();

        // AstrologerAreaofExpertise::where('astrologer_id', $a->id)->delete();

        // AstrologerAreaofExpertise::where('astrologer_id',$a->id)
        // ->update([
        //    'status' => 0
        // ]);

        // if($request->area != null){
        //     foreach ($request->area as $areaa) {
        //         $check = AstrologerAreaofExpertise::where('astrologer_id',$a->id)->where('areaofexpertise_id',$areaa)->first();
        //         if ($check) {
        //             $check->status = 1;
        //             $check->save();
        //         }
        //         else {
        //             $l = new AstrologerAreaofExpertise();
        //             $l->astrologer_id = $a->id;
        //             $l->areaofexpertise_id = $areaa;
        //             $l->status = 1;
        //             $l->save();
        //         }
        //     }
        // }
        // AstrologerLanguage::where('astrologer_id', $a->id)->delete();

        // AstrologerLanguage::where('astrologer_id',$a->id)
        // ->update([
        //    'status' => 0
        // ]);
        // if($request->lang != null){
        //     foreach ($request->lang as $llang) {
        //         $check = AstrologerLanguage::where('astrologer_id', $a->id)->where('language_id',$llang)->first();
        //         if ($check) {
        //             $check->status = 1;
        //             $check->save();
        //         }
        //         else {
        //             $ln = new AstrologerLanguage();
        //             $ln->astrologer_id = $a->id;
        //             $ln->language_id = $llang;
        //             $ln->status = 1;
        //             $ln->save();
        //         }
        //     }
        // }

        $c = AstrologerDocumentDetail::where('astrologer_id', $id)->first();
        if ($c) {
            $aadharcard_image = $c->aadharcard_image;
            $pancard_image = $c->pancard_image;
            $qualification = $c->qualification;
        } else {
            $c = new AstrologerDocumentDetail();
            $c->astrologer_id = $id;
            $aadharcard_image = 'default.png';
            $pancard_image = 'default.png';
            $qualification = 'default.png';
        }

        if (request('aadharcard_image')) {
            $fileNameWithTheExtension = request('aadharcard_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('aadharcard_image')->getClientOriginalExtension();
            $aadhaar_name = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('aadharcard_image')->move('content/astrologer/documents', $aadhaar_name);
            $aadharcard_image = $aadhaar_name;
        }


        if (request('pancard_image')) {
            $fileNameWithTheExtension = request('pancard_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('pancard_image')->getClientOriginalExtension();
            $pan_name = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('pancard_image')->move('content/astrologer/documents', $pan_name);
            $pancard_image = $pan_name;
        }

        if (request('qualification')) {
            $fileNameWithTheExtension = request('qualification')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('qualification')->getClientOriginalExtension();
            $qualification = rand() . '_qualification_' . time() . '.' . $extension;
            $filePath = request('qualification')->move('content/astrologer/documents', $qualification);
        }
        $c->pancard = $request->pancard;
        $c->aadhar_number = $request->aadhar_number;
        $c->aadharcard_image =  $aadharcard_image;
        $c->pancard_image =  $pancard_image;
        $c->qualification = $qualification;
        $c->bank_account_number = $request->bank_account_number;
        $c->account_type = $request->account_type;
        $c->ifsc_code = $request->ifsc_code;
        $c->account_holder_name = $request->account_holder_name;
        $c->save();

        // $tkt_data = TicketList::where('user_id',$id)->first();
        // if($tkt_data)
        // {
        // $tkt_data->admin_id = auth()->user()->id;
        // $tkt_data->user_id = $id;
        // $tkt_data->type = $request->type;
        // $tkt_data->ticket_id = $request->ticket_id;
        // $tkt_data->ticket_comment = $request->ticket_comment;
        // $tkt_data->save();
        // }
        // else{
        $tkt_data = new TicketList();
        $tkt_data->admin_id = auth()->user()->id;
        $tkt_data->user_id = $id;
        $tkt_data->type = $request->type;
        $tkt_data->ticket_id = $request->ticket_id;
        $tkt_data->ticket_comment = $request->ticket_comment;
        $tkt_data->save();
        // }


        return redirect()->route('admin.astrologer-manage.astrologer', $request->what)
            ->with('success', 'Astrologer updated successfully');
    }

    public function destroy_astrologer(Request $request)
    {
        $input = $request->all();
        // dd($input);die;
        $a = User::find($input['id']);
        $a->status = 2;
        $a->save();

        // $data = AstrologerAreaofExpertise::where('astrologer_id',$input['id'])->first();
        // // dd($data->id);die;
        // if($data){
        // $data->status = 2;
        // $data->save();
        // }

        // $b = AstrologerLanguage::where('astrologer_id',$input['id'])->first();
        // if($b){
        // $b->status = 2;
        // $b->save();
        // }

        // $c = AstrologerDocumentDetail::where('astrologer_id',$input['id'])->first();
        // if($c){
        // $c->status = 2;
        // $c->save();
        // }

        return redirect()->route('admin.astrologer-manage.astrologer')
            ->with('success', 'Astrologer deleted successfully');
    }

    public function destroy_astrologer_file(Request $request)
    {
        $input = $request->all();
        $a = AstrologerGallery::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }

    public function addFiles(Request $request)
    {
        if (request('gfile')) {
            for ($i = 0; $i < count($request->gfile); $i++) {
                $fileNameWithTheExtension = request('gfile')[$i]->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('gfile')[$i]->getClientOriginalExtension();
                $image_name = rand() . '_file_' . time() . '.' . $extension;
                // if ($request->type == 'image') {
                $filePath = request('gfile')[$i]->move('content/astrologer/gallery', $image_name);
                // }
                // else {
                //     $filePath = request('gfile')[$i]->move('content/astrologer/video', $image_name);
                // }
                $l = new AstrologerGallery();
                $l->astrologer_id = $request->astrologer_id;
                $l->type = $request->type;
                $l->name = $request->name ?? '';
                $l->video_title = $request->video_title;
                $l->video_image = $image_name;
                $l->position = $i + 1;
                $l->status = 1;
                $l->save();
            }
        }
        return redirect()->back()
            ->with('success', 'Add successfully');
    }

    public function edit_astrologer_service($id, $what = '')
    {
        $title = 'Edit Astrologer Service';
        $listname = 'Astrologer Service';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.astrologer-manage.update_astrologer_service';
        $a = User::find($id);
        if ($a) {
            $s = AstrologerServiceFlag::where('astrologer_id', $a->id)->first();
            if ($s) {
            } else {
                $nn = new AstrologerServiceFlag();
                $nn->astrologer_id = $a->id;
                $nn->chat_flag = 2;
                $nn->priority_chat = 2;
                $nn->free_chat = 2;
                $nn->call_flag = 2;
                $nn->priority_call = 2;
                $nn->free_call = 2;
                $nn->video_call_flag = 2;
                $nn->priority_call_flag = 2;
                $nn->free_video = 2;
                $nn->astrochart_flag = 2;
                $nn->ask_a_question = 2;
                $nn->can_take_schedule = 2;
                $nn->can_take_live_broadcast = 2;
                $nn->save();
                $s = $nn;
            }
            $p = AstrologerPrice::where('astrologer_id', $a->id)->first();
            if ($p) {
            } else {
                $pp = new AstrologerPrice();
                $pp->astrologer_id = $a->id;
                $pp->save();
                $p = $pp;
            }


            return view('admin.astrologer.edit_services', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what', 's', 'p', 'a'));
        } else {
            return redirect()->route('admin.astrologer-manage.astrologer', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function update_astrologer_service(Request $request, $id)
    {


        if ($request->take_free_call == 0) {
            $a_free_chat = 2;
            $a_free_call = 2;
            $a_free_video = 2;
        } else {
            $a_free_chat = $request->free_chat;
            $a_free_call = $request->free_call;
            $a_free_video = $request->free_video;
        }


        $a = AstrologerServiceFlag::where('astrologer_id', $request->astrologer_id)->first();
        $a->chat_flag = $request->chat_flag;
        $a->priority_chat = $request->priority_chat;
        $a->free_chat = $a_free_chat;
        $a->call_flag = $request->call_flag;
        $a->priority_call = $request->priority_call;
        $a->free_call = $a_free_call;
        $a->video_call_flag = $request->video_call_flag;
        $a->priority_call_flag = $request->priority_call_flag;
        $a->free_video = $a_free_video;
        $a->astrochart_flag = $request->astrochart_flag;
        $a->ask_a_question = $request->ask_a_question;
        $a->can_take_schedule = $request->can_take_schedule;
        $a->can_take_live_broadcast = $request->can_take_live_broadcast;
        $a->save();
        $p = AstrologerPrice::where('astrologer_id', $request->astrologer_id)->first();
        $p->normal_chat_price = $request->normal_chat_price;
        $p->normal_chat_price_usd = $request->normal_chat_price_usd;
        $p->priority_chat_price = $request->priority_chat_price;
        $p->priority_chat_price_usd = $request->priority_chat_price_usd;
        $p->scheduled_chat_price = $request->scheduled_chat_price;
        $p->scheduled_chat_price_usd = $request->scheduled_chat_price_usd;
        $p->chat_price_commision_type = $request->chat_price_commision_type;
        $p->chat_price_commision_value = $request->chat_price_commision_value;
        $p->normal_call_price = $request->normal_call_price;
        $p->normal_call_price_usd = $request->normal_call_price_usd;
        $p->priority_call_price = $request->priority_call_price;
        $p->priority_call_price_usd = $request->priority_call_price_usd;
        $p->scheduled_call_price = $request->scheduled_call_price;
        $p->scheduled_call_price_usd = $request->scheduled_call_price_usd;
        $p->call_price_commision_type = $request->call_price_commision_type;
        $p->call_price_commision_value = $request->call_price_commision_value;
        $p->normal_video_price = $request->normal_video_price;
        $p->normal_video_price_usd = $request->normal_video_price_usd;
        $p->priority_video_price = $request->priority_video_price;
        $p->priority_video_price_usd = $request->priority_video_price_usd;
        $p->scheduled_video_price = $request->scheduled_video_price;
        $p->scheduled_video_price_usd = $request->scheduled_video_price_usd;
        $p->video_price_commision_type = $request->video_price_commision_type;
        $p->video_price_commision_value = $request->video_price_commision_value;



        // $p->astrochart_price= $request->astrochart_price;
        // $p->astrochart_commision_type= $request->astrochart_commision_type;
        // $p->astrochart_commision_value= $request->astrochart_commision_value;
        // $p->ask_a_question_price= $request->ask_a_question_price;
        // $p->ask_a_question_commision_type= $request->ask_a_question_commision_type;
        // $p->ask_a_question_commision_value= $request->ask_a_question_commision_value;
        $p->save();

        // $sum = $request->normal_chat_price+$request->normal_chat_price_usd+$request->priority_chat_price+$request->priority_chat_price_usd+$request->scheduled_chat_price+$request->scheduled_chat_price_usd+$request->chat_price_commision_type+$request->chat_price_commision_value+$request->normal_call_price+$request->normal_call_price_usd+$request->priority_call_price+$request->priority_call_price_usd+$request->scheduled_call_price+$request->scheduled_call_price_usd+$request->call_price_commision_type+$request->call_price_commision_value+$request->normal_video_price+$request->normal_video_price_usd+$request->priority_video_price+$request->priority_video_price_usd+$request->scheduled_video_price+$request->scheduled_video_price_usd;
        // $frequency = 18;
        // $average = $sum/$frequency;

        $avg = User::where('id', $request->astrologer_id)->first();
        $avg->average_price = $request->normal_chat_price;
        $avg->free_call = $request->take_free_call;



        $avg->is_fixed_percentage = $request->is_fixed_percentage;
        $avg->fixed_commission = $request->fixed_commission;
        $avg->share_percentage = $request->share_percentage;
        $avg->gst_perct = $request->gst_perct;
        $avg->tds_perct = $request->tds_perct;


        $avg->save();
        // $avg = User::where('id',$request->astrologer_id)->first();
        // $avg->average_price = $average;
        // $avg->save();

        $tkt_data = new TicketList();
        $tkt_data->admin_id = auth()->user()->id;
        $tkt_data->user_id = $request->astrologer_id;
        $tkt_data->type = 2;
        $tkt_data->ticket_id = $request->ticket_id;
        $tkt_data->ticket_comment = $request->ticket_comment;
        $tkt_data->save();
        return redirect()->route("admin.astrologer-manage.view_astrologer", [$request->astrologer_id, $request->what])
            ->with('success', 'Update successfully');
    }



    public function payout_astrolger(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            // 'account_number'=>'required',
            'booking_txn_id' => 'required',
            'payment_mode' => 'required',
            'id' => 'required',
            'cond' => 'required',
            // 'trxn_date'=>'required',

        ]);


        $totalamount = AstrologerTransaction::where('user_id', $request->id)->where('txn_for', '!=', 'payout')->where('type', 'credit')->where('status', 1)->sum('amount');
        $payout = AstrologerTransaction::where('user_id', $request->id)->where('txn_for', 'payout')->where('type', 'debit')->where('status', 1)->sum('amount');
        $mainamount = number_format($totalamount, 2);


        // print_r($totalamount); 
        // print_r("ssasas"); 
        // print_r($payout); 
        // die;


        $remainbalance = $totalamount - $payout;
        // $remainbalance = $mainamount - $payout;



        if ($remainbalance > 0) {

            if ($remainbalance >= (float)$request->amount) {
                $t = new AstrologerTransaction();
                $t->user_id = $request->id;
                $t->txn_name = 'payout';
                $t->booking_id  = 0;
                $t->payment_mode = $request->payment_mode;
                $t->booking_txn_id = $request->booking_txn_id;
                $t->trxn_date = $request->trxn_date;
                $t->txn_for = 'payout';
                $t->type = 'debit';
                $t->price = $request->amount;
                $t->tax_price = 0;
                $t->amount = $request->amount;
                $t->status = 1;
                $t->currency = 'INR';
                // $t->added_by = auth()->user()->id;
                // $t->account_number = $request->account_number;
                $t->save();
                return redirect()->back()
                    ->with('success', 'Payout successfully added!!');
            } else return redirect()->back()
                ->with('failure', 'You cannot perform this action!');
        } else return redirect()->back()
            ->with('failure', 'You cannot perform this action!');
    }







    public function payout(Request $request, $condition, $id)
    {

        $title = ' Astrologer Gift Manage';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.update_astrologers';
        $astrologer_ide = $id;
        $data =  DB::table('astrologer_transactions')->where('user_id', $id)->get();

        // print_r($_GET); die;

        if (isset($_GET['start_date']) && !empty($_GET['end_date'])) {

            $s_date = $_GET['start_date'];
            $e_date = $_GET['end_date'];

            $booking_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                // ->where('is_incentive', '=', "0")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('price');

            // $booking_amount = DB::table('astrologer_transactions')
            // ->where('user_id', '=', $user->id)
            // ->where('type', '=', "credit")
            // ->where('is_incentive', '=', "0")
            // ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
            // ->sum('price');


            $astrologer_comission_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');


            $astrologer_incentive_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "1")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');


            $total_pending_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');



            $total_credit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');

            $total_debit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "debit")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');





            // $total_booking = DB::table('astrologer_transactions')
            // ->where('user_id', '=',$id)
            // ->where('type', '=', "credit")
            // ->where('is_incentive', '=', "0")
            // ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
            // ->count();

            $total_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 0)
                ->where('status', '=', 2)
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->count();





            $total_tds_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)

                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                // ->where('type', '=', 0)
                ->sum('tds_price');

            $total_gst_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)

                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                // ->where('type', '=', 0)
                ->sum('tax_price');



            $total_free_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 1)
                ->where('status', '=', 2)
                ->whereBetween('start_time', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->count();

            $total_ask_booking = DB::table('ask_a_questions')
                ->where('astrologer_id', '=', $id)
                ->where('status', '=', 1)
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->count();



            // astrologer_comission_amount




        } else {

            $booking_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->sum('price');

            $astrologer_comission_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->sum('amount');

            $astrologer_incentive_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "1")
                ->sum('amount');

            $total_pending_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->sum('amount');


            $total_credit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->sum('amount');

            $total_debit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "debit")
                ->sum('amount');




            $total_tds_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                // ->where('type ', '=', "credit")
                // ->where('type', '=', 0)
                ->sum('tds_price');

            $total_gst_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                // ->where('status', '=', 2)
                // ->where('type', '=', 0)
                ->sum('tax_price');


            // $total_booking = DB::table('astrologer_transactions')
            // ->where('user_id', '=',$id)
            // ->count();

            $total_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 0)
                ->where('status', '=', 2)
                // ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                ->count();



            $total_free_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 1)
                ->where('status', '=', 2)
                ->count();

            $total_ask_booking = DB::table('ask_a_questions')
                ->where('astrologer_id', '=', $id)
                ->where('status', '=', 1)
                ->count();
        }





        // $data = AstrologerCustomeTime::where('user_id',$id)->whereNOTIN('status',[2])->get();
        if ($data) {
            $ab = User::find($id);
            $cond = $condition;
            return view('admin.astrologer.astrologerpayout', compact(
                'data',
                'listurl',
                'title',
                'listname',
                'total_credit_payouts',
                'total_debit_payouts',
                'editurl',
                'cond',
                'ab',
                'booking_amount',
                'total_booking',
                'astrologer_comission_amount',
                'astrologer_incentive_amount',
                'total_pending_payouts',
                'astrologer_ide',
                'total_tds_astro',
                'total_gst_astro',
                'total_free_booking',
                'total_ask_booking'
            ));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }




    public function booking_payout_done($condition, $id, $start_date, $end_date)
    {

        // print_r($start_date);
        // echo"Ddd";
        // print_r($end_date);
        // die;
        $booking_amount = DB::table('astrologer_transactions')
            ->where('user_id', '=', $id)
            ->where('type', '=', "credit")
            ->whereBetween('created_at', [$start_date, $end_date])
            ->sum('amount');
        $total_pay =  floor($booking_amount);


        $sum_amount =   $total_pay;

        if ($sum_amount) {
            DB::table('astrologer_transactions')
                ->where('user_id', $id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->update(array('type' => 'debit'));


            $a = new Astrologerpayouts();
            $a->astrologer_id = $id;
            $a->amount = $sum_amount;
            $a->start_date = $start_date;
            $a->end_date = $end_date;
            $a->type = 1;
            $a->save();
            return redirect()->back()
                ->with('success', 'Position update successfully!');
        }
    }




    public function index_incentive_astrologer(Request $request)
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Astrolger Name",

                "Payment For",
                "Reason",
                "Type",
                "Currency",
                "TDS",
                "Price",

                "Created At"
            );

            // $queryqb = AstrologerTransaction::query();
            // $queryqb->select('astrologer_transactions.*');
            // $queryqb->orderBy('astrologer_transactions.updated_at','DESC');
            // // $queryqb->whereNOTIN('astrologers.id',[0]);
            // $queryqb->where('astrologer_transactions.status',1);
            // // if(!is_null($request->name)) {

            // //     $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            // // }
            // if(!is_null($request->start_date) || !is_null($request->end_date)) {
            //     if (!is_null($request->start_date) && !is_null($request->end_date)) {
            //         $queryqb->whereBetween('astrologers.created_at', [$request->start_date, $request->end_date]);
            //     }
            //     elseif (!is_null($request->start_date)) {
            //         $queryqb->whereDate('astrologers.created_at', $request->start_date);

            //     }
            //     elseif (!is_null($request->end_date)) {
            //         $queryqb->whereDate('astrologers.created_at', $request->end_date);
            //     }

            // }

            // $fetch = $queryqb->get();



            $queryqb = AstrologerTransaction::query();
            $queryqb->select('astrologer_transactions.*');
            $queryqb->orderBy('astrologer_transactions.updated_at', 'DESC');
            // $queryqb->whereNOTIN('astrologers.id',[0]);
            $queryqb->where('astrologer_transactions.is_incentive', 1);
            if (!is_null($request->name)) {

                // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first(); 
                $astroname = DB::table('astrologers')->whereRaw("(name  like '%" . $request->name . "%' )")->first();
                $queryqb->where('astrologer_transactions.user_id', $astroname->id);
                // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }

            if (!is_null($request->mobile)) {

                // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first(); 
                $astromobile = DB::table('astrologers')->where('mobile', $request->mobile)->first();
                $queryqb->where('astrologer_transactions.user_id', $astromobile->id);
                // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }

            // print_r()
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('astrologer_transactions.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('astrologer_transactions.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('astrologer_transactions.created_at', $request->end_date);
                }
            }

            $fetch = $queryqb->get();


            // dd( $fetch);
            // $fetch = DB::table('astrologer_transactions')->where('status', 1)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {

                $astroname = DB::table('astrologers')->where('id', $user->user_id)->first();


                if ($astroname) {
                    $name5 = json_decode($astroname->name, true);
                    $a_name5 = $name5[1] ?? 'Astro N';
                } else {
                    $a_name5 = "Astro N";
                }

                // echo   $a_name5."(".$astroname->mobile.")" ;
                $txn_type = "";
                if ($user->txn_type == 1) {
                    $txn_type =  "Booking";
                } elseif ($user->txn_type == 2) {
                    $txn_type =  "Free Call";
                } elseif ($user->txn_type == 3) {
                    $txn_type =  "Ask A question";
                } else {
                    $txn_type =  "Other";
                }

                $type = "";
                if ($user->type == "credit") {
                    $type =  "Add Money";
                } elseif ($user->type == 2) {
                    $type =  "Deduct Money";
                } else {
                    $type =  "Deduct Money";
                }






                $st = "";
                if ($user->status == 1) {
                    $st = "Active";
                } else if ($user->status == 2) {
                    $st = "Deleted";
                } else {
                    $st = "Inactive";
                }
                $data[] = array(
                    "ID" => $i,
                    "name" => $a_name5,
                    "txn_type" => $txn_type,
                    "txn_for" => $user->txn_for,
                    "type" => $type,
                    "currency" => $user->currency,
                    "tds_price" => $user->tds_price,
                    "price" => $user->price,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"AstrologerTransaction" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }
        $title = "Astrologer Incentives";
        $listurl = route('admin.astrologer-manage.index_incentive_astrologer');
        $addurl = route('admin.astrologer-manage.create_incentive_astrologer');
        $addurl1 = route('admin.astrologer-manage.store_incentive_astrologer');
        $destroyurl = route('admin.astrologer-manage.approve_incentive_astrologer');
        $destroyurl1 = route('admin.astrologer-manage.approve_incentive_astrologer');


        $queryqb = AstrologerTransaction::query();
        $queryqb->select('astrologer_transactions.*');
        $queryqb->orderBy('astrologer_transactions.updated_at', 'DESC');
        // $queryqb->whereNOTIN('astrologers.id',[0]);
        $queryqb->where('astrologer_transactions.is_incentive', 1);
        if (!is_null($request->name)) {

            // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first(); 
            $astroname = DB::table('astrologers')->whereRaw("(name  like '%" . $request->name . "%' )")->first();
            $queryqb->where('astrologer_transactions.user_id', $astroname->id);
            // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
        }

        if (!is_null($request->mobile)) {

            // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first(); 
            $astromobile = DB::table('astrologers')->where('mobile', $request->mobile)->first();
            $queryqb->where('astrologer_transactions.user_id', $astromobile->id);
            // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
        }


        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('astrologer_transactions.created_at', [$request->start_date, $request->end_date]);
            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('astrologer_transactions.created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('astrologer_transactions.created_at', $request->end_date);
            }
        }

        $data = $queryqb->get();



        // $data = DB::table('astrologer_transactions')->where('is_incentive', 1)->latest()->orderby('id', 'ASC')->get();
        // $data1 = Paginator::useBootstrap($data);
        // return response()->json($data);
        return view('admin.incentive.index', compact('title', 'listurl', 'addurl', 'addurl1', 'destroyurl', 'destroyurl1', 'data'));
        // dd($data);
    }
    public function create_incentive_astrologer()
    {
        $title = "Add Astrologer Incentives";
        $listname = "Incentives";
        $listurl = route('admin.astrologer-manage.index_incentive_astrologer');
        $addurl1 = route('admin.astrologer-manage.store_incentive_astrologer');
        $data = DB::table('astrologer_transactions')->where('status', 1)->orderby('id', 'ASC')->get();
        $astrologerdetail = User::all();
        return view('admin.incentive.create', compact('title', 'addurl1', 'data', 'listurl', 'listname', 'astrologerdetail'));
    }

    public function store_incentive_astrologer(Request $request)
    {
        // [ "required", "regex:/^(\d+|\d+(\.\d{1,2})?|(\.\d{1,2}))$/" ]
        $this->validate($request, [
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',        
            // 'status' => 'required',
        ]);




        $as_id =  $request->get('user_id');

        $astro_data  = User::where('id', $as_id)->first();

        $tds_amount1 = $request->price * ($astro_data->tds_perct / 100);


        $amount = $request->price - $tds_amount1;

        $data = AstrologerTransaction::create();
        $data->user_id = $request->get('user_id');
        $data->booking_id = 0;
        $data->payment_mode = "online";
        $data->booking_txn_id =  rand(1111111111, 9999999999);;
        $data->txn_name = $request->txn_for;
        $data->txn_for = $request->txn_for;
        $data->txn_type = $request->txn_type;
        $data->type = $request->type;
        $data->txn_mode = "";
        $data->currency = $request->currency;
        $data->price = $request->price;
        $data->tds_price = $tds_amount1;
        $data->amount = $amount;

        // $data->amount = $request->amount;
        $data->status = 1;
        $data->is_incentive = 1;
        // $data->is_incentive = $request->is_incentive;
        $data->save();
        return redirect()->route('admin.astrologer-manage.index_incentive_astrologer',)
            ->with('success', 'Astrologer Incentive Created Successfully');
    }





    public function show_reviews(Request $request)
    {
        


        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Type",
                "Astrologer Name",
                "Astrologer Mobile",
                "User id",
                "User mobile",
                "BookingID",
                "Rate",
                "Message",
                "Reply Message",
                "Register On",
                "Is show"
            );


            $queryUser = AstrologerRatingReviews::query();
            $queryUser->whereNOTIN('status', [2]);
            // $astrologer_data = User::query();
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryUser->whereDate('created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryUser->whereDate('created_at', $request->end_date);
                }
            }
            if (!is_null($request->name)) {
                $queryUser->whereRaw("(name like '%" . $request->name . "%' )");
                //$queryUser->appends(['name' => $request->name]);
            }
            if (!is_null($request->astrologer_id)) {
                // print_r($request->astrologer_id); die;
                $astro_data1 = DB::table('astrologers')
                    ->whereRaw("(name   like '%" . $request->astrologer_id . "%' )")
                    ->first();
                if ($astro_data1) {
                    $queryUser->where('astrologer_id', $astro_data1->id);
                } else {
                    $queryUser->where('astrologer_id', $request->astrologer_id);
                }
            }
    
    
            if (!is_null($request->user_id)) {
                $queryUser->where('user_id', $request->user_id);
                //$queryUser->appends(['name' => $request->name]);
            }
            if (!is_null($request->user_mobile)) {
    
                $user_data = DB::table('users')
                    ->where('mobile', '=', $request->user_mobile)
                    ->first();
    
                if ($user_data) {
                    $queryUser->where('user_id', $user_data->id);
                } else {
                    $queryUser->where('user_id', $request->mobile);
                }
            }
    
            if (!is_null($request->astrologer_number)) {
    
                $astro_data = DB::table('astrologers')
                    ->where('mobile', '=', $request->astrologer_number)
                    ->first();
    
                if ($astro_data) {
                    $queryUser->where('astrologer_id', $astro_data->id);
                } else {
                    $queryUser->where('astrologer_id', $request->astrologer_number);
                }
            }
    
    
    
    
    
    
            if (!is_null($request->email)) {
                $queryUser->whereRaw("(email like '%" . $request->email . "%' )");
            }
    
            if (!is_null($request->mobile)) {
                $queryUser->whereRaw("(phone like '%" . $request->mobile . "%' )");
            }
            if (!is_null($request->status)) {
                $queryUser->whereRaw("(status like '%" . $request->status . "%' )");
            }

            
            $fetch = $queryUser->get();


            // dd( $fetch);
            // $fetch = DB::table('astrologer_transactions')->where('status', 1)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $astr = User::find($user->astrologer_id);
                $nam = json_decode($astr->name,true);
                $a_name5 = $nam[1] ?? 'Astro N';
                $a_mobile = $astr->mobile ?? '';
              
                $userdata =  User::find($user->user_id);

                $type = "";
                if ($user->type == "1" ) {
                     $type =  "Puja";
                }    
               
                else{
                     $type =  "Astrologer";
                }



               


                $st = "";
                if ($user->is_show == 1) {
                    $st = "Show";
                } else {
                    $st = "Hide";
                }




                $data[] = array(
                    "ID" => $i,
                    "type" => $type,
                    "name" => $a_name5,
                    "a_mobile" => $a_mobile,
                    "userid" => $userdata->id,
                    "usermobile" => $userdata->mobile,
                    "booking_id " => $user->booking_id ,
                    "rating" => $user->rating,
                    "reviews" => $user->reviews,
                    "astrologer_review" => $user->astrologer_review,
                    "created_at" => $user->created_at,
                    "st" => $st,
                    
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"AstrologerRatingReviews" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }



        $page_limit = 20;
        $queryUser = AstrologerRatingReviews::query();
        $queryUser->whereNOTIN('status', [2]);
        // $astrologer_data = User::query();
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            } elseif (!is_null($request->start_date)) {
                $queryUser->whereDate('created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryUser->whereDate('created_at', $request->end_date);
            }
        }
        if (!is_null($request->name)) {
            $queryUser->whereRaw("(name like '%" . $request->name . "%' )");
            //$queryUser->appends(['name' => $request->name]);
        }
        if (!is_null($request->astrologer_id)) {
            // print_r($request->astrologer_id); die;
            $astro_data1 = DB::table('astrologers')
                ->whereRaw("(name   like '%" . $request->astrologer_id . "%' )")
                ->first();

            if ($astro_data1) {
                $queryUser->where('astrologer_id', $astro_data1->id);
            } else {
                $queryUser->where('astrologer_id', $request->astrologer_id);
            }

            //$queryUser->appends(['name' => $request->name]);
        }


        if (!is_null($request->user_id)) {
            $queryUser->where('user_id', $request->user_id);
            //$queryUser->appends(['name' => $request->name]);
        }
        if (!is_null($request->user_mobile)) {

            $user_data = DB::table('users')
                ->where('mobile', '=', $request->user_mobile)
                ->first();

            if ($user_data) {
                $queryUser->where('user_id', $user_data->id);
            } else {
                $queryUser->where('user_id', $request->mobile);
            }
        }

        if (!is_null($request->astrologer_number)) {

            $astro_data = DB::table('astrologers')
                ->where('mobile', '=', $request->astrologer_number)
                ->first();

            if ($astro_data) {
                $queryUser->where('astrologer_id', $astro_data->id);
            } else {
                $queryUser->where('astrologer_id', $request->astrologer_number);
            }
        }






        if (!is_null($request->email)) {
            $queryUser->whereRaw("(email like '%" . $request->email . "%' )");
        }

        if (!is_null($request->mobile)) {
            $queryUser->whereRaw("(phone like '%" . $request->mobile . "%' )");
        }
        if (!is_null($request->status)) {
            $queryUser->whereRaw("(status like '%" . $request->status . "%' )");
        }
        $queryUser->orderBy('id', 'DESC');
        //Fetch list of results

        $data = $queryUser->paginate($page_limit);
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        if (!is_null($request->astrologer_id)) {
            $data->appends(['astrologer_id' => $request->get('astrologer_id')]);
        }
        if (!is_null($request->user_id)) {
            $data->appends(['user_id' => $request->get('user_id')]);
        }
        if (!is_null($request->user_mobile)) {
            $data->appends(['user_mobile' => $request->get('user_mobile')]);
        }

        $title = 'Reviews List';
        $exporturl = 'admin.astrologers-reviews';
        $astrologer_list = User::where('status', 1)->get();
        $user_list = User::where('status', 1)->get();
        $addurl1 = route('admin.astrologer-manage.create_reviews');
        $editurl = 'admin.astrologer-manage.edit_reviews';
        $destroyurl = route('admin.astrologer-manage.destroy_reviews');
        $approve_url = route('admin.astrologer-manage.approveurl');
        $hide_url = route('admin.astrologer-manage.hide_url');
        return view('admin.astrologer.reviewslist', compact('title', 'data', 'exporturl', 'destroyurl', 'astrologer_list', 'addurl1', 'user_list', 'editurl', 'approve_url', 'hide_url'));
    }



    public function create_reviews(Request $request)
    {
        $input = $request->all();
        print_r($input);
        die;
        $this->validate($request, [
            // 'type_id' => 'required',
            // 'user_id' => 'required',
            'reviews' => 'required',
            'rating' => 'required',
            'status' => 'required',

        ]);

        $a = new AstrologerRatingReviews();
        $a->astrologer_id = $request->type_id;
        $a->reviews = $request->reviews;
        $a->rating = $request->rating;
        // $a->type = 2;
        $a->user_id = $request->id;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologers-reviews')
            ->with('success', 'Review created successfully');
    }


    public function edit_reviews($id)
    {
        $title = 'Edit Reviews';
        $listname = 'gifts';
        $listurl = "";
        $editurl = 'admin.astrologer-manage.update_reviews';
        $astrologer_list = User::where('status', 1)->get();
        $user_list = User::where('status', 1)->get();
        $a = AstrologerRatingReviews::find($id);
        if ($a) {
            return view('admin.astrologer.edit_review', compact('a', 'title', 'listurl', 'listname', 'editurl', 'astrologer_list', 'user_list'));
        } else {
            return redirect()->route('admin.astrologers-reviews')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_reviews(Request $request, $id)
    {
        $this->validate($request, [
            // 'type_id' => 'required',
            'user_id' => 'required',
            // 'message' => 'required',
            'rating' => 'required',
            'status' => 'required',
        ]);

        $a = AstrologerRatingReviews::find($id);

        // $a->type_id = $request->type_id;
        $a->user_id = $request->user_id;
        $a->reviews = $request->reviews;
        $a->astrologer_review = $request->astrologer_review;
        $a->status = $request->status;
        $a->rating = $request->rating;

        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologers-reviews')
            ->with('success', 'reviews updated successfully');
    }




    public function approveurl(Request $request)
    {

        // $a =  DB::table('astrologer_rating_reviews')->where('id',$request->id);
        // $a->is_show = 1;
        // $a->save();
        $input = $request->all();

        // print_r($input['id']); die;
        $a = AstrologerRatingReviews::find($input['id']);
        $a->is_show = 1;
        $a->save();
        return redirect()->back()
            ->with('success', 'Review approved successfully');
    }
    public function hide_url(Request $request)
    {
        $input = $request->all();
        $a = AstrologerRatingReviews::find($input['id']);
        $a->is_show = 0;
        $a->save();
        return redirect()->back()
            ->with('success', 'Review approved successfully');
    }

    public function destroy_reviews(Request $request)
    {
        $input = $request->all();
        $a = AstrologerRatingReviews::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Review deleted successfully');
    }




    public function astrologertimemanage($condition, $id)
    {
        $title = ' Astrologer Time Manage';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.update_astrologers';
        $data = AstrologerDatewiseTimes::where('user_id', $id)->whereNOTIN('status', [2])->get();
        if ($data) {
            $ab = User::find($id);
            $cond = $condition;
            return view('admin.astrologer.astrologertimemanage', compact('data', 'title', 'listurl', 'listname', 'editurl', 'cond', 'ab'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function create_custome_time($condition, $id)
    {
        $title = 'Add Astrologer Time';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $addurl = route('admin.astrologer-manage.store_custome_time', $condition);
        return view('admin.astrologer.create_custome_time', compact('title', 'listurl', 'listname', 'addurl', 'condition', 'id'));
    }

    public function store_custome_time($condition, Request $request)
    {
        $this->validate($request, [
            'datevalue' => 'required',
            'times' => 'required',
            'timesend' => 'required',
        ]);

        $period = new \DatePeriod(
            new \DateTime($request->datevalue),
            new \DateInterval('P1D'),
            new \DateTime($request->end_date)
        );
        $dates = array();
        foreach ($period as $key => $value) {
            array_push($dates, $value->format('Y-m-d'));
        }
        array_push($dates, $request->end_date);

        $check = AstrologerDatewiseTimes::where('user_id', $request->id)->where('date', $request->datevalue)->where('status', 1)->first();
        if ($check) {
            return redirect()->back()
                ->with('failure', 'Already Date time added ');
        }
        $timeslots = array();
        foreach ($request->times as $t => $values) {
            $start = date('h:ia', strtotime($values));
            $end = date('h:ia', strtotime($request->timesend[$t]));
            if ($start != null && $end != null) {
                $timeslots[] = array(
                    "start" => $start,
                    "end" => $end,
                );
            }
        }

        if (!empty($dates)) {
            foreach ($dates as $key2) {
                $a = DB::table('astrologer_datewise_times')->whereDate('date', '=', $key2)->where('user_id', $request->id)->where('status', 1)->first();
                if ($a) {
                    DB::table('astrologer_datewise_times')
                        ->where('id', $a->id)  // find your user by their email
                        ->limit(1)  // optional - to ensure only one record is updated.
                        ->update(array('json' => json_encode($request->timslots), 'updated_at' => date('Y-m-d H:i:s')));
                    $a = DB::table('astrologer_datewise_times')->whereDate('date', '=', $key2)->where('user_id', $request->astrologer_id)->where('status', 1)->first();
                    return redirect()->back()
                        ->with('failure', 'Already Date time added ');
                }

                $a = new AstrologerDatewiseTimes();
                $a->json = json_encode($timeslots);
                $a->user_id = $request->id;
                $a->date = $key2;
                $a->status = 1;
                $a->save();
            }
        }
        if ($a) {
            return redirect()->route('admin.astrologer-manage.astrologertimemanage', [$condition, $request->id])
                ->with('success', 'Astrologer created successfully');
        } else {
            return redirect()->back()
                ->with('failure', 'Something Error Happen Please try again!');
        }
    }



    public function delete_timemanage($cond, $id, $mainid)
    {
        $a = AstrologerDatewiseTimes::find($id);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Custom Time deleted successfully');
    }


    public function astrologeraoe($id)
    {
        $data = AstrologerAreaofExpertise::where('status', 1)->where('astrologer_id', $id)->get();
        $addbutton = 'Add Astrologer Area of expertise';
        $importbutton = 'Upload Excel';
        $title = 'Area of expertise';
        $listurl = route('admin.astrologer-manage.astrologeraoe', $id);
        $addurl = route('admin.astrologer-manage.create_astrologeraoe', $id);
        $importurl = route('admin.astrologer-manage.import_astrologeraoe');
        $editurl = 'admin.astrologer-manage.edit_astrologeraoe';
        $destroyurl = route('admin.astrologer-manage.destroy_astrologeraoe');
        $addurl1 = route('admin.astrologer-manage.store_astrologeraoe');
        return view('admin.astrologeraoe.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1', 'id'));
    }

    public function create_astrologeraoe($id)
    {
        $title = 'Add Area of expertise';
        $listname = 'Area of expertise';
        $listurl = route('admin.astrologer-manage.astrologeraoe', $id);
        $addurl = route('admin.astrologer-manage.store_astrologeraoe');
        return view('admin.astrologeraoe.create', compact('title', 'listurl', 'listname', 'addurl', 'id'));
    }

    public function store_astrologeraoe(Request $request)
    {
        $this->validate($request, [
            'astrologer_id' => 'required',
            'areaofexpertise_id' => 'required',
        ]);
        $a = new AstrologerAreaofExpertise();
        $a->astrologer_id = $request->astrologer_id;
        $a->areaofexpertise_id = $request->areaofexpertise_id;
        $a->status = 1;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologeraoe', $request->astrologer_id)
            ->with('success', 'Area of expertise created successfully');
    }


    public function destroy_astrologeraoe(Request $request)
    {
        $input = $request->all();
        $a = AstrologerAreaofExpertise::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologeraoe', $a->astrologer_id)
            ->with('success', 'Area of expertise  deleted successfully');
    }


    public function astrologerlanguage($id)
    {
        $data = AstrologerLanguage::where('status', 1)->where('astrologer_id', $id)->get();
        $addbutton = 'Add Astrologer Language';
        $importbutton = 'Upload Excel';
        $title = 'Language';
        $listurl = route('admin.astrologer-manage.astrologerlanguage', $id);
        $addurl = route('admin.astrologer-manage.create_astrologerlanguage', $id);
        $importurl = route('admin.astrologer-manage.import_astrologerlanguage');
        $editurl = 'admin.astrologer-manage.edit_astrologerlanguage';
        $destroyurl = route('admin.astrologer-manage.destroy_astrologerlanguage');
        $addurl1 = route('admin.astrologer-manage.store_astrologerlanguage');
        return view('admin.astrologerlanguage.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1', 'id'));
    }

    public function create_astrologerlanguage($id)
    {
        $title = 'Add Language';
        $listname = 'Language';
        $listurl = route('admin.astrologer-manage.astrologerlanguage', $id);
        $addurl = route('admin.astrologer-manage.store_astrologerlanguage');
        return view('admin.astrologerlanguage.create', compact('title', 'listurl', 'listname', 'addurl', 'id'));
    }

    public function store_astrologerlanguage(Request $request)
    {
        $this->validate($request, [
            'astrologer_id' => 'required',
            'language_id' => 'required',
        ]);
        $a = new AstrologerLanguage();
        $a->astrologer_id = $request->astrologer_id;
        $a->language_id = $request->language_id;
        $a->status = 1;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologerlanguage', $request->astrologer_id)
            ->with('success', 'Language created successfully');
    }


    public function destroy_astrologerlanguage(Request $request)
    {
        $input = $request->all();
        $a = AstrologerLanguage::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologerlanguage', $a->astrologer_id)
            ->with('success', 'Language  deleted successfully');
    }
}
