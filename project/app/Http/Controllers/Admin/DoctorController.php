<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserModels\Booking;
use App\Models\UserInformation;
use App\Models\UserModels\UserProfile;
use App\Models\Prescription;
use App\Models\UserEstablishmentClinic;
use App\Models\UserModels\Member;
use App\Models\UserModels\MedicalRecord;
use App\Models\Treatment;
use App\Models\Generalsetting;
use App\Models\SeasonalOffers;

use Illuminate\Support\Str;
use DB;
use Illuminate\Validation\Rule;
use Hash;
use Carbon\Carbon;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use App\Traits\SdSendSms;
use App\Models\SocialConnectView;
use App\Models\MasterSpecialsation;
use Illuminate\Support\Facades\Mail;
use App\Models\Invitation;
use App\Models\MyNotification;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;

use App\Models\UserModels\UserSubscriptionsPack;
use App\Models\UserModels\UserPremiumAddonsPack;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
use App\Models\DoctorClinicSlot;
use App\Models\UserModels\UserPayment;
use App\Models\UserEstablishmentHoliday;
use App\Models\TreatmentDoctor;
class DoctorController extends Controller
{
    use SdSendSms;
    public function mystaff(Request $request, $what = 1)
    {

        if (isset($_GET['export_file'])) {

            // print_r($what); die;
            // $queryqb->where('astrologers.status',$what);


            $data[] = array(
                "ID",
                "Name",
                "Slug",
                "Screen Name",
                "Email",
                "Country Code",
                "Mobile Number",
                "Alternate Number",
                "Gender",
                "About",
                "Image",
                "Experience",
                "AreaOfExpertise",
                "Language",
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


            $queryqb = Astrologer::query();
            $queryqb->select('astrologers.*');
            $queryqb->orderBy('astrologers.updated_at', 'DESC');
            // $queryqb->whereNOTIN('astrologers.id',[0,1]);
            $queryqb->where('astrologers.status', $what);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {

                    // $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);


                    $queryqb->whereBetween('astrologers.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('astrologers.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('astrologers.created_at', $request->end_date);
                }
            }

            $fetch = $queryqb->get();


            // $fetch = DB::table('astrologers')->where('status', '!=',$what)->paginate(500000); //status is not equal to  2
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
                $nm2 = json_decode($user->screen_name, true);
                $abt = json_decode($user->about, true);
                $aoe = '';
                if ($user->getastroservice()) {
                    $asaoe = array();
                    foreach ($user->getastroservice() as $key) {
                        $service = AreaOfExpertise::where('id', $key->areaofexpertise_id)->where('status', 1)->first();
                        if ($service) {
                            $ser1 = json_decode($service->name, true);
                            $ser1name = $ser1[1] ?? 'NA';
                            if ($ser1[1]) {
                                $ser1name = $ser1[1] ?? 'NA';
                            }
                            array_push($asaoe, $ser1name);
                        }
                    }
                    if (!empty($asaoe)) {
                        $aoe = implode('|', $asaoe);
                    }
                }
                $lngg = '';
                if ($user->getlangauge()) {
                    $langauge = array();

                    foreach ($user->getlangauge() as $key) {
                        $lang = MasterLangauage::where('id', $key->language_id)->where('status', 1)->first();
                        if ($lang) {
                            array_push($langauge, $lang->name);
                        }
                    }
                    if (!empty($langauge)) {
                        $lngg = implode('|', $langauge);
                    }
                }
                $data[] = array(
                    "ID" => $i,
                    "name" => $nm[1] ?? '',
                    "slug" => $user->slug,
                    "screen_name" => $nm2[1] ?? '',
                    "email" => $user->email,
                    "country_code" => $user->country_code,
                    "mobile" => $user->mobile,
                    "alternate_mobile" => $user->alternate_mobile,
                    "gender" => $user->gender,
                    "about" => $user->abt[1] ?? '',
                    "image" => $user->image,
                    "experience" => $user->experience,
                    "aoe" => $aoe,
                    "lngg" => $lngg,
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
            $fetch = DB::table('astrologers')->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('astrologers')->where('status', 1)->paginate(500000);
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
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
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
                        ->whereDate('start_time', $request->s_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->s_date)
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
                        ->whereDate('start_time', $request->e_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->e_date)
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


        if (isset($_GET['export_payout_new'])) {

            $data[] = array(

                "Id",
                "Name",
                "Paid Booking Count",
                "Paid Earnings [Minus PG]",
                "Free Minutes",    "Free Earnings",
                "AAQ Cout",
                "AAQ Earnings [Minus PG]",
                "Gift Count",
                "Gift Earnings [Minus PG]",
                "Incentive Earnings",
                "Selling Count",
                "Selling Earnings",
                "Total PG [Paid PG + AAQ PG + Gift PG]",
                "Total TDS [All earnings * TDS 10%]",
                "Total Earnings [All earnings w/o TDS, value needed by Govt]",
                "Total Bank Credit [All earnings - TDS]",
                "PAN",
                "Account Name",
                "Account Number",
                "Account Type",
                "IFSC Code"

            );
            $fetch = DB::table('astrologers')->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('astrologers')->whereIN('id', [265])->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('astrologers')->where('status', 1)->paginate(500000);
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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('amount');
                        // ->toSql();
                    // print_r( $astrologer_comission_amount); die;

                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');
                        // $astrologer_comission_amount_tds = 0;


                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;




                    $total_tds_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', 'credit')
                        // ->where('txn_type', '=', 4)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');


                    $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_price');




                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');


                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('gst_astro');


                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('gst_astro');


                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();

                    // Free Minutes


                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        ->where('status', '2')
                        ->where('free', '1')
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('astrologer_comission_amount');

                    $freeearning_tds =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_astro');



                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('astrologer_comission_amount');

                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');



                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');


                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('amount');

                        
                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');

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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('amount');


                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');


                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;

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
                        ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');


                        $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');
    

                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');



                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('gst_astro');


                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('gst_astro');




                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->s_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->s_date)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereDate('created_at', $request->s_date)
                        ->count();

                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 2)
                        ->where('free', '1')
                        ->whereDate('start_time', $request->s_date)
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->s_date)
                        ->sum('astrologer_comission_amount');

                        $freeearning_tds =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->s_date)
                        ->sum('tds_astro');


                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('astrologer_comission_amount');


                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');



                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');



                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->s_date)
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('amount');

                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');


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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('amount');


                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');


                    // $astrologer_incentive_amount = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "credit")
                    //     ->where('is_incentive', '=', "1")
                    //     ->whereDate('created_at', $request->e_date)
                    //     ->sum('amount');

                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;



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
                        ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                        $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');



                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('gst_astro');

                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('gst_astro');




                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->e_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->e_date)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereDate('created_at', $request->e_date)
                        ->count();

                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 2)
                        ->where('free', '1')
                        ->whereDate('start_time', $request->e_date)
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->e_date)
                        ->sum('astrologer_comission_amount');

                        $freeearning_tds =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->e_date)
                        ->sum('tds_astro');


                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('astrologer_comission_amount');

                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');



                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');

                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->e_date)
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('amount');

                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');


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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->sum('amount');

                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->sum('tds_price');



                    // $astrologer_incentive_amount = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "credit")
                    //     ->where('is_incentive', '=', "1")
                    //     ->sum('amount');


                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;


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
                        ->where('type', '=', "credit")
                        // ->where('type', '=', 0)
                        ->sum('tds_price');

                        
                        $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)

                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)

                        ->sum('tds_astro');

                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->sum('tax_price');


                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->sum('gst_astro');

                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->sum('gst_astro');





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

                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 2)
                        ->where('free', '1')
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)

                        ->sum('astrologer_comission_amount');


                        $freeearning_tds =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->sum('tds_astro');


                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->sum('astrologer_comission_amount');

                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->sum('tds_astro');




                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');


                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->sum('amount');


                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->sum('tds_price');

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

                $total_free_earning =     $freeearning + $freeearning_tds ;

               

                $total_earnings_all =   $sellingearning_tds_price + $sellingearning +  $astrologer_incentive_amount + $giftsmoney + $giftsmoney_tds_astro + $aaqmoney + $aaqmoney_tds + $astrologer_comission_amount + $astrologer_comission_amount_tds;
                $total_tds_all =   $total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit ;
                // print_r($total_earnings_all); die;
                // print_r($total_tds_astro + $total_gift_tds_astro ); die;
                $total_bank_credit =  $total_earnings_all -  $total_tds_all;
                 
                $nm = json_decode($user->name, true);
                $data[] = array(
                    "ID" => $i,
                    "name" => $nm[1] ?? '',
                    "total_paid_complete_booking" => $total_paid_complete_booking,
                    "astrologer_comission_amount" =>  sprintf( '%0.2f',($astrologer_comission_amount + $astrologer_comission_amount_tds -  $total_free_earning)),
                    "total_free_minutes" =>  $total_free_minutes,
                    "free_earnings" =>   sprintf( '%0.2f',$total_free_earning),
                    "total_ask_booking" => $total_ask_booking,
                    "aaq_earnings" => sprintf( '%0.2f',($aaqmoney + $aaqmoney_tds)),
                    "gift_count" =>  $gifts,
                    "gift_earnings" =>   sprintf( '%0.2f',($giftsmoney + $giftsmoney_tds_astro)),
                    "incentive_earnings " => sprintf('%0.2f', $astrologer_incentive_amount),
                    "selling_count" => $sellingcount,
                    "selling_earnings" =>  sprintf( '%0.2f',($sellingearning + $sellingearning_tds_price)),
                    "total_pg" =>  sprintf('%0.2f', ($total_gst_astro + $total_gift_gst_astro)),
                    "total_tds" => sprintf('%0.2f', ($total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit)),
                    "total_earnings" =>     sprintf('%0.2f', $total_earnings_all), 
                    "total_bank_credit" =>  sprintf('%0.2f', $total_bank_credit), 

                    // "total_booking" =>  $total_book,
                    // "booking_amount" => $booking_amount,
                    // "admin_earnings" => sprintf('%0.2f', $booking_amount - $astrologer_comission_amount),

                    // "astrologer_incentive_amount" => sprintf('%0.2f', $astrologer_incentive_amount),
                    // // "Astrologer_pending_payout" => sprintf('%0.2f', $total_debit_payouts),
                    // "Astrologer_pending_payout" => sprintf('%0.2f', $total_credit_payouts - $total_debit_payouts),
                    // "total_tds_astro" => sprintf('%0.2f', $total_tds_astro),
                    // "total_gst_astro" => sprintf('%0.2f', $total_gst_astro),

                    // "total_free_booking" => $total_free_booking,

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
                'Specialization',
                'Added Date',
            );
            $i = 1;
            $queryqb = User::query();
            $queryqb->select('users.*');
            $queryqb->orderBy('users.updated_at', 'DESC');
            $queryqb->where('parent_id',auth()->user()->id);
            $queryqb->whereNOTIN('users.status', [2]);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

                   
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('users.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('users.created_at', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                $doctor_info = $a->UserInformationDetails;

                $specialisation_ids = $doctor_info && $doctor_info->specialisations 
                    ? explode('|', $doctor_info->specialisations) 
                    : [];

                $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();

                $specialisations_data = $specialisations->map(function ($specialisation) {
                    return $specialisation->name;
                })->toArray();
                $dataexport[] = [
                    $a->id,
                    $a->name,
                    $a->email,
                    $a->mobile,
                    implode(', ', $specialisations_data),
                    date('d-M-Y h:ia', strtotime($a->created_at))
                ];
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"List" . date('Y-m-d-h:i:s') . '.csv');
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
            $queryqb = Astrologer::query();
            $queryqb->select('astrologers.*');
            $queryqb->orderBy('astrologers.updated_at', 'DESC');
            // $queryqb->whereNOTIN('astrologers.id',[0]);
            $queryqb->where('astrologers.ultra_premium', 1);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('astrologers.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

                    // $queryqb->whereBetween('astrologers.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('astrologers.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('astrologers.created_at', $request->end_date);
                }
            }

            $data = $queryqb->paginate(100);
        } else {
            // $queryqb = User::query();
            // $queryqb->select('users.*');
            // $queryqb->orderBy('users.updated_at', 'DESC');
            // $queryqb->where('parent_id',auth()->user()->id);
            // $queryqb->whereNOTIN('users.status', [2]);
            // if (!is_null($request->name)) {
            //     $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            // }
            // if (!is_null($request->start_date) || !is_null($request->end_date)) {
            //     if (!is_null($request->start_date) && !is_null($request->end_date)) {
            //         $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

                   
            //     } elseif (!is_null($request->start_date)) {
            //         $queryqb->whereDate('users.created_at', $request->start_date);
            //     } elseif (!is_null($request->end_date)) {
            //         $queryqb->whereDate('users.created_at', $request->end_date);
            //     }
            // }

            // $data = $queryqb->paginate(1000);
        }

        // if (!is_null($request->name)) {
        //     $data->appends(['name' => $request->get('name')]);
        // }
        // if (!is_null($request->start_date)) {
        //     $data->appends(['start_date' => $request->get('start_date')]);
        // }
        // if (!is_null($request->end_date)) {
        //     $data->appends(['end_date' => $request->get('end_date')]);
        // }
        // $data->appends(request()->except('page'));
        $user_profile = User::where('id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays','activeSubscription.premiumAddons'])->first();
        $data = array();
        if ($user_profile->parent_id == 0) {
            $data = $this->getchild($user_profile->id);
        }
        $exporturl = 'admin.user.mystaff';
        $addbutton = 'Add Doctor';
        $importbutton = 'Upload Excel';
        $title = 'Doctor List';
        $listurl = route('admin.user.mystaff');
        $addurl = route('admin.user.create_mystaff', $what);
        $importurl = route('admin.user.import_mystaff');
        $editurl = 'admin.user.edit_mystaff';
        $loginurl = 'admin.user.login_mystaff';
        $destroyurl = route('admin.user.destroy_mystaff');
        $addurl1 = route('admin.user.store_mystaff');
        $viewurl = 'admin.user.view_mystaff';

        return view('doctor.mystaff.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'exporturl', 'what', 'viewurl', 'loginurl'));
    }

    

    public function mystaffdetail($id, $what = '')
    {
        $title = 'View Staf Detail';
        $listname = 'Detail';
        $listurl = route('admin.user.mystaff', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = User::where('id', $id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->with(['ratings.userProfile' => function ($query) {
            $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
        }])->withCount([
            'ratings',
            'bookings as active_bookings_count' => function ($query) {
                $query->where('status', 1);
            }
        ])
        ->withAvg('ratings', 'rating')->whereNOTIN('status',[2])->first();
        if ($a) {
            $user_profile = $a;
                $check = UserInformation::where('user_id',auth()->user()->id)->first();
                if (!$check) {
                    $a = new UserInformation();
                    $a->user_id = auth()->user()->id;
                    $a->save();
                    $user_information = $a;
                } else $user_information = $check;
            $user_clinic = $user_profile->UserClinicDetails;
            $step1Fields = ['name',  'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

            $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
            $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'experience'];
            $step4Fields = ['about'];
            $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
            $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
            $step7Fields = ['doctor_availability'];
            $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
            $step9Fields = ['theme'];
            $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;
            $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;

            $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
            $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
            $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
            $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
            $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
            $step8Completion = 100;//(count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
            $step9Completion = 100;//(count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
            // dd($user_information);
            // dd($step9Completion);
            $completionByStep = [
                'step1' => $step1Completion,
                'step2' => $step2Completion,
                'step3' => $step3Completion,
                'step4' => $step4Completion,
                'step5' => $step5Completion,
                'step6' => $step6Completion,
                'step7' => $step7Completion,
                'step8' => $step8Completion,
                'step9' => $step9Completion,
            ];
            $totalCompletion = array_sum($completionByStep);
            $totalSteps = count($completionByStep);
            $finalCompletionPercentage = $totalCompletion / $totalSteps;
            $getclininc = collect();
            $r = DoctorClinicSlot::where('doctor_id', $id)->orderBy('id', 'desc')->where('status',1)->get();
            if ($r->count() == 0) {
                $id1 = auth()->user()->id;
                if (auth()->user()->parent_id > 0) {
                    $id1 = auth()->user()->parent_id;
                }
                $getclininc = UserEstablishmentClinic::where('user_id', $id1)->where('status',1)->get();

            }
            return view('doctor.mystaff.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','finalCompletionPercentage','r','getclininc'));
        } else {
            return redirect()->route('admin.user.mystaff', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function myprofiledetail( $id)
    {
        $what = 1;
        $title = 'View Staf Detail';
        $listname = 'Detail';
        $listurl = route('admin.user.mystaff', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = User::where('id', $id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->with(['ratings.userProfile' => function ($query) {
            $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
        }])->withCount([
            'ratings',
            'bookings as active_bookings_count' => function ($query) {
                $query->where('status', 1);
            }
        ])
        ->withAvg('ratings', 'rating')->whereNOTIN('status',[2])->first();
        if ($a) {
            $user_profile = $a;
                $check = UserInformation::where('user_id',auth()->user()->id)->first();
                if (!$check) {
                    $a = new UserInformation();
                    $a->user_id = auth()->user()->id;
                    $a->save();
                    $user_information = $a;
                } else $user_information = $check;
            $user_clinic = $user_profile->UserClinicDetails;
            $step1Fields = ['name',  'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

            $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
            $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
            $step4Fields = ['about'];
            $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
            $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
            $step7Fields = ['doctor_availability'];
            $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
            $step9Fields = ['theme'];
            $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

            $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
            $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
            $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
            $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
            $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
            $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
            $step8Completion = 100;//(count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
            $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
            // dd($step2Completion);
            $completionByStep = [
                'step1' => $step1Completion,
                'step2' => $step2Completion,
                'step3' => $step3Completion,
                'step4' => $step4Completion,
                'step5' => $step5Completion,
                'step6' => $step6Completion,
                'step7' => $step7Completion,
                'step8' => $step8Completion,
                'step9' => $step9Completion,
            ];
            $totalCompletion = array_sum($completionByStep);
            $totalSteps = count($completionByStep);
            $finalCompletionPercentage = $totalCompletion / $totalSteps;
            return view('doctor.mystaff.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','finalCompletionPercentage'));
        } else {
            return redirect()->route('admin.user.mystaff', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function myqr()
    {
        return view('doctor.mystaff.myqr');
    }

    public function feedback(Request $request)
    {
        if ($request->u1) {
            $doctorId = $request->u1;
        } else
        $doctorId = auth()->user()->id;
        $doctor = User::where('id', $doctorId)
            ->with([
                'ratings.userProfile' => function ($query) {
                    $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
                }
            ])
            ->withCount([
                'ratings',
                'bookings as active_bookings_count' => function ($query) {
                    $query->where('status', 1);
                }
            ])
            ->withAvg('ratings', 'rating')
            ->first();
        $ratings = $doctor->ratings()
        ->selectRaw('rating, COUNT(*) as count')
        ->groupBy('rating')
        ->pluck('count', 'rating')
        ->toArray();

        $starCounts = [
            '5_star' => $ratings[5] ?? 0,
            '4_star' => $ratings[4] ?? 0,
            '3_star' => $ratings[3] ?? 0,
            '2_star' => $ratings[2] ?? 0,
            '1_star' => $ratings[1] ?? 0,
        ];

        $totalReviews = array_sum($starCounts);

        $ratingsList = $doctor->ratings()
            ->with('userProfile:id,name,image')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                $rating->human_date = Carbon::parse($rating->created_at)->diffForHumans();
                return $rating;
            });
        $u = User::where('parent_id', auth()->user()->id)->where('status',1)->orderBy('name', 'DESC')->get();    
        return view('doctor.mystaff.feedback',compact('totalReviews','starCounts','ratingsList','doctor','u'));
    }

    public function sendreminder($team)
    {
        $user_profile = Invitation::where('id', $team)->first();
        if ($user_profile) {
                $invitationData = $user_profile->toArray();
                $s = Generalsetting::find(1);
                $doctor = User::where('id',$user_profile->doctor_id)->first();
                Mail::send('emails.mail_inv', compact('invitationData','s','doctor'), function ($message) use ($invitationData) {
                    $message->from('patriciadentist1@gmail.com', 'Dentus');
                    $message->replyTo('patriciadentist1@gmail.com', 'Dentus');
                    $message->to($invitationData['email']);
                    $message->subject('Dentus | Accept Invitation');
                });
        }
        return redirect()->back()
            ->with('success', 'Reminder Sent successfully!');
        

    }

    public function seasonaloffer(Request $request) {
        $blogs = SeasonalOffers::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
              ->where('created_by', 1);
        })
        ->where('status',1)
        ->orderBy('start_date')
        ->get();

        
        $blogs->each(function ($blog) {
            $treatment = DB::table('treatments')
                ->where('id', $blog->treat_package_id)
                ->first(['treatment_name']);
    
            if ($treatment) {
                $blog->treatment_name = $treatment->treatment_name;
            } else {
                $blog->treatment_name = null;
            }
    
            $blog->image = asset('content/SeasonalOffers') . '/' . $blog->image;
        });
        $destroyurl = route('admin.myuser.destroy_seasonaloffer');
        $addurl1 = route('admin.myuser.store_seasonaloffer');
        $t = Treatment::whereNOTIN('status', [2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();
        $importurl = 'admin.myuser.update_seasonaloffer';
        return view('doctor.seasonaloffer.index',compact('blogs','destroyurl','addurl1','t','importurl'));
    }

    public function store_seasonaloffer (Request $request)
    {
        
        
        $totalquantity = $this->getquantity2(17);
        $blogs = SeasonalOffers::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
              ->where('created_by', 1);
        })
        ->where('status',1)
        ->orderBy('start_date')
        ->get();
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($blogs);
        }
        elseif ($totalquantity < 0) {
           $canaddchild = 1;
        }
        if ($canaddchild > 0) {
            if ($request->offer_type == 'custom') {
                $offer_type = $request->custom_offer_type;
                $image_name = '';
                if (request('image')) 
                {
                    $fileNameWithTheExtension = request('image')->getClientOriginalName();
                    $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                    $extension = request('image')->getClientOriginalExtension();
                    $image_name = rand().'_blog_' . time() . '.' . $extension;
                    $filePath = request('image')->move('content/SeasonalOffers', $image_name);
                }
            } else {
                $offer = SeasonalOffers::find($request->offer_type);
                $offer_type = $offer->offer_type;
                $image_name = $offer->image;
            }
            $a = new SeasonalOffers();
            $a->user_id = auth()->user()->id;
            $a->created_by = 1;
            $a->offer_type = $offer_type;
            $a->treat_package_id = $request->treat_package_id;
            $a->discount = $request->discount;
            $a->start_date = $request->start_date;
            $a->end_date = $request->end_date;
            $a->image =  $image_name;
            $a->status = 1;
            $a->save();
            return redirect()->back()
                ->with('success', 'Created successfully');
        }
        else {
            return redirect()->back()
                ->with('failure', 'You cannot add more!');
        }
    }

    public function update_seasonaloffer (Request $request, $id)
    {
        $a = SeasonalOffers::find($id);
        $image_name = $a->image;
        if (request('image')) 
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand().'_blog_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/SeasonalOffers', $image_name);
        }
        $a->offer_type = $request->offer_type;

        $a->treat_package_id = $request->treat_package_id;
        $a->discount = $request->discount;
        $a->start_date = $request->start_date;
        $a->end_date = $request->end_date;
        $a->image =  $image_name;
        $a->save();
        
        return redirect()->back()
            ->with('success', 'Updated successfully');
    }

    public function destroy_seasonaloffer(Request $request)
    {
        $input = $request->all();
        $a = SeasonalOffers::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }

    public function aboutupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->about = $request->about;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function professionalupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->specialisations = implode('|', $request->specialisations);
            $user_information->services = implode('|', $request->services);
            $user_information->hospital_worked_in = $request->hospital_worked_in;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function educationupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->degree = $request->degree;
            $user_information->college_institute = $request->college_institute;
            $user_information->year_of_completion = $request->year_of_completion;
            $user_information->year_of_experience = $request->year_of_experience;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function awardupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->award_college_institute = $request->award_college_institute;
            $user_information->award_year = $request->award_year;
            $user_information->awards_name = $request->awards_name;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function counsilupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->registration_number = $request->registration_number;
            $user_information->registration_council = $request->registration_council;
            $user_information->registration_year = $request->registration_year;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function timingupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $formattedSchedule = [];
            $scheduleData = $request->input('schedule');
            foreach ($scheduleData as $day => $times) {
                $morning_start = isset($times['morning_start']) ? Carbon::parse($times['morning_start'])->format('h:i A') : null;
                $morning_end = isset($times['morning_end']) ? Carbon::parse($times['morning_end'])->format('h:i A') : null;
                $evening_start = isset($times['evening_start']) ? Carbon::parse($times['evening_start'])->format('h:i A') : null;
                $evening_end = isset($times['evening_end']) ? Carbon::parse($times['evening_end'])->format('h:i A') : null;

                $formattedSchedule[$day] = [
                    "morning" => $morning_start && $morning_end ? ["{$morning_start} - {$morning_end}"] : [],
                    "evening" => $evening_start && $evening_end ? ["{$evening_start} - {$evening_end}"] : [],
                ];
            }
            $finalJson = json_encode(["schedule" => $formattedSchedule], JSON_PRETTY_PRINT);
            $user_information->doctor_availability = $finalJson;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function timingupdate2($id, Request $request)
    {
        $a = DoctorClinicSlot::where('id', $id)->first();
        if ($a) {
            $formattedSchedule = [];
            $scheduleData = $request->input('schedule');
            foreach ($scheduleData as $day => $times) {
                $morning_start = isset($times['morning_start']) ? Carbon::parse($times['morning_start'])->format('h:i A') : null;
                $morning_end = isset($times['morning_end']) ? Carbon::parse($times['morning_end'])->format('h:i A') : null;
                $evening_start = isset($times['evening_start']) ? Carbon::parse($times['evening_start'])->format('h:i A') : null;
                $evening_end = isset($times['evening_end']) ? Carbon::parse($times['evening_end'])->format('h:i A') : null;

                $formattedSchedule[$day] = [
                    "morning" => $morning_start && $morning_end ? ["{$morning_start} - {$morning_end}"] : [],
                    "evening" => $evening_start && $evening_end ? ["{$evening_start} - {$evening_end}"] : [],
                ];
            }
            $finalJson = json_encode(["schedule" => $formattedSchedule], JSON_PRETTY_PRINT);
            $a->doctor_availability = $finalJson;
            $a->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function timingupdate3($id, Request $request)
    {
        $a = new DoctorClinicSlot();
        $formattedSchedule = [];
        $scheduleData = $request->input('schedule');
        foreach ($scheduleData as $day => $times) {
            $morning_start = isset($times['morning_start']) ? Carbon::parse($times['morning_start'])->format('h:i A') : null;
            $morning_end = isset($times['morning_end']) ? Carbon::parse($times['morning_end'])->format('h:i A') : null;
            $evening_start = isset($times['evening_start']) ? Carbon::parse($times['evening_start'])->format('h:i A') : null;
            $evening_end = isset($times['evening_end']) ? Carbon::parse($times['evening_end'])->format('h:i A') : null;

            $formattedSchedule[$day] = [
                "morning" => $morning_start && $morning_end ? ["{$morning_start} - {$morning_end}"] : [],
                "evening" => $evening_start && $evening_end ? ["{$evening_start} - {$evening_end}"] : [],
            ];
        }
        $a->doctor_id = auth()->user()->id;
        $a->clinic_id = $id;
        $a->status = 1;
        $finalJson = json_encode(["schedule" => $formattedSchedule], JSON_PRETTY_PRINT);
        $a->doctor_availability = $finalJson;
        $a->save();
        return redirect()->back()
                ->with('success', 'Update successfully!!');
    }

    public function profileupdate(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        $a = User::where('id', $id)->first();
        if ($a) {
            $image_name = $a->image;
            if (request('image')) {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand() . '_profile_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/doctor', $image_name);
            }


            $a->name = $request->name;
            $a->email = $request->email;
            $a->mobile = $request->mobile;
            $a->image = $image_name;
            $a->gender = $request->gender;
            $a->date_of_birth = $request->date_of_birth;
            $a->save();
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->language_known = implode('|', $request->language_known);
            $user_information->experience = $request->experience;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        }
        else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function addmystaffmain ($what = '')
    {
        $title = 'Add Staff';
        $listname = 'Staff';
        $listurl = route('admin.user.mystaff');
        $addurl = route('admin.user.store_staff', $what);
        return view('doctor.mystaff.create', compact('title', 'listurl', 'listname', 'addurl', 'what'));
    }

    public function addmystaff ($what = '')
    {
        $title = 'Add Staff';
        $listname = 'Staff';
        $listurl = route('admin.user.mystaff');
        $addurl = route('admin.user.store_staff', $what);
        return view('doctor.mystaff.createinvite', compact('title', 'listurl', 'listname', 'addurl', 'what'));
    }

    public function store_staff(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query)  {
                    $query->whereNOTIN('status', [2]);
                    // $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query)  {
                    $query->whereNOTIN('status', [2]);
                    // $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);
        $doctor = auth()->user();
        $getchild = array();
        if ($doctor->parent_id == 0) {
            $getchild = $this->getchild($doctor->id);
        }
        $invitation = $this->getchildinvitation($doctor->id);
        $totalquantity = $this->getquantity(12);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($getchild);
        }
        if ($canaddchild > 0) {
            // code...
            $image_name = 'default.png';
            $a = new User();
            
            if (request('image')) {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand() . '_profile_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/doctor', $image_name);
            }

            $a->parent_id = auth()->user()->id;
            $a->name = $request->name.' '.$request->lname;
            $a->password = Hash::make($request->password);

            $a->email = $request->email;
            $a->mobile = $request->mobile;
            $a->image = $image_name;
            $a->gender = $request->gender;
            $a->date_of_birth = $request->date_of_birth;
            $a->save();
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $ab = new UserInformation();
                $ab->user_id = $a->id;
                $ab->save();
                $user_information = $ab;
            } else $user_information = $check;
            $user_information->language_known = $request->language_known ? implode('|', $request->language_known) : '';
            $user_information->save();
            return redirect()->route('admin.user.mystaffdetail', $a->id)
            ->with('success', 'Created successfully');
        } else return redirect()->back()
            ->with('failure', 'You cannot add more limit is crossed!');
        
        
    }

    public function store_staffinvite(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users|unique:invitations',
            'mobile' => 'required|string|max:15|unique:users|unique:invitations',
        ]);

        $doctor = auth()->user();
        $getchild = array();
        if ($doctor->parent_id == 0) {
            $getchild = $this->getchild($doctor->id);
        }
        $invitation = $this->getchildinvitation($doctor->id);
        $addonQuantity =  $this->getquantity(12);
        
        $canaddchild = 0;
        if ($addonQuantity > 0) {
           $canaddchild = $addonQuantity-(count($getchild)/*+$invitation*/);
        }
        if ($canaddchild > 0) {
            $token = Str::random(32);
            $invitation = Invitation::create([
                'doctor_id' => auth()->user()->id,
                'user_id' => 0,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'token' => $token,
            ]);

            $invitationData = $invitation->toArray();
            $invitationData['invitation_id'] = $invitation->id;
            $s = Generalsetting::find(1);
            $doctor = User::where('id',$invitation->doctor_id)->first();
            Mail::send('emails.mail_inv', compact('invitationData','s','doctor'), function ($message) use ($invitationData) {
                $message->from('patriciadentist1@gmail.com', 'Dentus');
                $message->replyTo('patriciadentist1@gmail.com', 'Dentus');
                $message->to($invitationData['email']);
                $message->subject('Dentus | Accept Invitation');
            });
            return redirect()->route('admin.user.mystaff')
            ->with('success', 'Sent successfully');
        } else
        return redirect()->back()
            ->with('failure', 'You cannot add other users!');
    }

    public function getchild($id)
    {
        $child = array();
        $main = User::where('parent_id', auth()->user()->id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->whereNOTIN('status',[2])->get();
        if ($main->count() > 0) {
            foreach ($main as $key) {
                $user_profile = $key;
                $check = UserInformation::where('user_id',auth()->user()->id)->first();
                if (!$check) {
                    $a = new UserInformation();
                    $a->user_id = auth()->user()->id;
                    $a->save();
                    $user_information = $a;
                } else $user_information = $check;
                $user_clinic = $user_profile->UserClinicDetails;
                $step1Fields = ['name',  'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

                    $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
                    $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'experience'];
                    $step4Fields = ['about'];
                    $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
                    $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
                    $step7Fields = ['doctor_availability'];
                    $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
                    $step9Fields = ['theme'];
                $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

                $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
                $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
                $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
                $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
                $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
                $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
                $step8Completion = (count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
                $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
                $step8Completion = 100;//(count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
                $step9Completion = 100;//(count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
                $completionByStep = [
                    'step1' => $step1Completion,
                    'step2' => $step2Completion,
                    'step3' => $step3Completion,
                    'step4' => $step4Completion,
                    'step5' => $step5Completion,
                    'step6' => $step6Completion,
                    'step7' => $step7Completion,
                    'step8' => $step8Completion,
                    'step9' => $step9Completion,
                ];
                $totalCompletion = array_sum($completionByStep);
                $totalSteps = count($completionByStep);
                $finalCompletionPercentage = $totalCompletion / $totalSteps;
        
                $child[] = array('user_profile' => $user_profile,
                                 'is_invite'=>0,
                                 'final_completion_percentage' =>round($finalCompletionPercentage, 2),);
            }
        }
        $invitationuser = Invitation::where('doctor_id',auth()->user()->id)->where('user_id',0)->where('status',0)->get();
        if ($invitationuser->count() > 0) {
            foreach ($invitationuser as $key) {
                $child[] = array('user_profile' => $key,
                                'is_invite'=>1,
                                 'final_completion_percentage' =>0);
            }
        }
        return $child;
    }

    public function getchildinvitation($id)
    {
        $child = array();
        $main = Invitation::where('doctor_id', $id)->whereNOTIN('status',[2])->get();
        return $main->count();
    }


    public function myuserlist(Request $request, $what = 1)
    {

        if (isset($_GET['export_file'])) {

            // print_r($what); die;
            // $queryqb->where('astrologers.status',$what);


            $data[] = array(
                "ID",
                "Name",
                "Slug",
                "Screen Name",
                "Email",
                "Country Code",
                "Mobile Number",
                "Alternate Number",
                "Gender",
                "About",
                "Image",
                "Experience",
                "AreaOfExpertise",
                "Language",
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


            $queryqb = Astrologer::query();
            $queryqb->select('astrologers.*');
            $queryqb->orderBy('astrologers.updated_at', 'DESC');
            // $queryqb->whereNOTIN('astrologers.id',[0,1]);
            $queryqb->where('astrologers.status', $what);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {

                    // $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);


                    $queryqb->whereBetween('astrologers.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('astrologers.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('astrologers.created_at', $request->end_date);
                }
            }

            $fetch = $queryqb->get();


            // $fetch = DB::table('astrologers')->where('status', '!=',$what)->paginate(500000); //status is not equal to  2
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
                $nm2 = json_decode($user->screen_name, true);
                $abt = json_decode($user->about, true);
                $aoe = '';
                if ($user->getastroservice()) {
                    $asaoe = array();
                    foreach ($user->getastroservice() as $key) {
                        $service = AreaOfExpertise::where('id', $key->areaofexpertise_id)->where('status', 1)->first();
                        if ($service) {
                            $ser1 = json_decode($service->name, true);
                            $ser1name = $ser1[1] ?? 'NA';
                            if ($ser1[1]) {
                                $ser1name = $ser1[1] ?? 'NA';
                            }
                            array_push($asaoe, $ser1name);
                        }
                    }
                    if (!empty($asaoe)) {
                        $aoe = implode('|', $asaoe);
                    }
                }
                $lngg = '';
                if ($user->getlangauge()) {
                    $langauge = array();

                    foreach ($user->getlangauge() as $key) {
                        $lang = MasterLangauage::where('id', $key->language_id)->where('status', 1)->first();
                        if ($lang) {
                            array_push($langauge, $lang->name);
                        }
                    }
                    if (!empty($langauge)) {
                        $lngg = implode('|', $langauge);
                    }
                }
                $data[] = array(
                    "ID" => $i,
                    "name" => $nm[1] ?? '',
                    "slug" => $user->slug,
                    "screen_name" => $nm2[1] ?? '',
                    "email" => $user->email,
                    "country_code" => $user->country_code,
                    "mobile" => $user->mobile,
                    "alternate_mobile" => $user->alternate_mobile,
                    "gender" => $user->gender,
                    "about" => $user->abt[1] ?? '',
                    "image" => $user->image,
                    "experience" => $user->experience,
                    "aoe" => $aoe,
                    "lngg" => $lngg,
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
            $fetch = DB::table('astrologers')->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('astrologers')->where('status', 1)->paginate(500000);
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
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
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
                        ->whereDate('start_time', $request->s_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->s_date)
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
                        ->whereDate('start_time', $request->e_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->e_date)
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


        if (isset($_GET['export_payout_new'])) {

            $data[] = array(

                "Id",
                "Name",
                "Paid Booking Count",
                "Paid Earnings [Minus PG]",
                "Free Minutes",    "Free Earnings",
                "AAQ Cout",
                "AAQ Earnings [Minus PG]",
                "Gift Count",
                "Gift Earnings [Minus PG]",
                "Incentive Earnings",
                "Selling Count",
                "Selling Earnings",
                "Total PG [Paid PG + AAQ PG + Gift PG]",
                "Total TDS [All earnings * TDS 10%]",
                "Total Earnings [All earnings w/o TDS, value needed by Govt]",
                "Total Bank Credit [All earnings - TDS]",
                "PAN",
                "Account Name",
                "Account Number",
                "Account Type",
                "IFSC Code"

            );
            $fetch = DB::table('astrologers')->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('astrologers')->whereIN('id', [265])->whereNOTIN('status', [3, 2])->paginate(500000);
            // $fetch = DB::table('astrologers')->where('status', 1)->paginate(500000);
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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('amount');
                        // ->toSql();
                    // print_r( $astrologer_comission_amount); die;

                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');
                        // $astrologer_comission_amount_tds = 0;


                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;




                    $total_tds_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', 'credit')
                        // ->where('txn_type', '=', 4)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');


                    $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_price');




                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');


                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('gst_astro');


                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('gst_astro');


                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();

                    // Free Minutes


                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        ->where('status', '2')
                        ->where('free', '1')
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('astrologer_comission_amount');

                    $freeearning_tds =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_astro');



                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('astrologer_comission_amount');

                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');



                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_astro');


                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('amount');

                        
                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                        ->sum('tds_price');

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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('amount');


                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');


                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;

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
                        ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');


                        $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');
    

                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');



                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('gst_astro');


                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('gst_astro');




                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->s_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->s_date)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereDate('created_at', $request->s_date)
                        ->count();

                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 2)
                        ->where('free', '1')
                        ->whereDate('start_time', $request->s_date)
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->s_date)
                        ->sum('astrologer_comission_amount');

                        $freeearning_tds =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->s_date)
                        ->sum('tds_astro');


                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('astrologer_comission_amount');


                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');



                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_astro');



                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->s_date)
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('amount');

                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->s_date)
                        ->sum('tds_price');


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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('amount');


                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');


                    // $astrologer_incentive_amount = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "credit")
                    //     ->where('is_incentive', '=', "1")
                    //     ->whereDate('created_at', $request->e_date)
                    //     ->sum('amount');

                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;



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
                        ->where('type ', '=', "credit")
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                        $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');



                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('gst_astro');

                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('gst_astro');




                    $total_free_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 1)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->e_date)
                        ->count();


                    $total_paid_complete_booking = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('free', '=', 0)
                        ->where('status', '=', 2)
                        ->whereDate('start_time', $request->e_date)
                        ->count();


                    $total_ask_booking = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 1)
                        ->whereDate('created_at', $request->e_date)
                        ->count();

                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 2)
                        ->where('free', '1')
                        ->whereDate('start_time', $request->e_date)
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->e_date)
                        ->sum('astrologer_comission_amount');

                        $freeearning_tds =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('start_time', $request->e_date)
                        ->sum('tds_astro');


                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('astrologer_comission_amount');

                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');



                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');

                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->e_date)
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('amount');

                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');


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
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->sum('amount');

                    $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('txn_type', '=', "1")
                        ->where('is_incentive', '=', "0")
                        ->sum('tds_price');



                    // $astrologer_incentive_amount = DB::table('astrologer_transactions')
                    //     ->where('user_id', '=', $user->id)
                    //     ->where('type', '=', "credit")
                    //     ->where('is_incentive', '=', "1")
                    //     ->sum('amount');


                    $incentive_earning_credit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "credit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('price');


                    $incentive_earning_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('price');

                    $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;


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
                        ->where('type', '=', "credit")
                        // ->where('type', '=', 0)
                        ->sum('tds_price');

                        
                        $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->where('type', '=', "debit")
                        ->where('is_incentive', '=', "1")
                        ->where('txn_type', 4)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('tds_price');

                    $total_gift_tds_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)

                        ->sum('tds_astro');

                    $total_ask_tds_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)

                        ->sum('tds_astro');

                    $total_gst_astro = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        // ->where('status', '=', 2)
                        // ->where('type', '=', 0)
                        ->sum('tax_price');


                    $total_gift_gst_astro = DB::table('send_gifts')
                        ->where('astrologer_id', '=', $user->id)
                        ->sum('gst_astro');

                    $total_ask_gst_astro = DB::table('ask_a_questions')
                        ->where('astrologer_id', '=', $user->id)
                        ->sum('gst_astro');





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

                    $total_free_minutes = DB::table('bookings')
                        ->where('astrologer_id', '=', $user->id)
                        ->where('status', '=', 2)
                        ->where('free', '1')
                        ->sum('total_minutes');

                    $freeearning =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)

                        ->sum('astrologer_comission_amount');


                        $freeearning_tds =  DB::table('bookings')
                        ->where('status', '2')
                        ->where('free', '1')
                        ->where('astrologer_id',  $user->id)
                        ->sum('tds_astro');


                    $aaqmoney = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->sum('astrologer_comission_amount');

                        $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                        ->where('status', '1')
                        ->where('astrologer_id', $user->id)
                        ->sum('tds_astro');




                    $gifts = DB::table('send_gifts')
                        ->where('astrologer_id', $user->id)
                        ->count();

                    $giftsmoney = DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        ->sum('astrologer_comission_amount');

                        $giftsmoney_tds_astro= DB::table('send_gifts')
                        ->where('astrologer_id',  $user->id)
                        // ->whereDate('created_at', $request->e_date)
                        ->sum('tds_astro');


                    $sellingcount = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->count();


                    $sellingearning = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->sum('amount');


                        $sellingearning_tds_price = DB::table('astrologer_transactions')
                        ->where('user_id', $user->id)
                        ->where('txn_type', 5)
                        ->sum('tds_price');

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

                $total_free_earning =     $freeearning + $freeearning_tds ;

               

                $total_earnings_all =   $sellingearning_tds_price + $sellingearning +  $astrologer_incentive_amount + $giftsmoney + $giftsmoney_tds_astro + $aaqmoney + $aaqmoney_tds + $astrologer_comission_amount + $astrologer_comission_amount_tds;
                $total_tds_all =   $total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit ;
                // print_r($total_earnings_all); die;
                // print_r($total_tds_astro + $total_gift_tds_astro ); die;
                $total_bank_credit =  $total_earnings_all -  $total_tds_all;
                 
                $nm = json_decode($user->name, true);
                $data[] = array(
                    "ID" => $i,
                    "name" => $nm[1] ?? '',
                    "total_paid_complete_booking" => $total_paid_complete_booking,
                    "astrologer_comission_amount" =>  sprintf( '%0.2f',($astrologer_comission_amount + $astrologer_comission_amount_tds -  $total_free_earning)),
                    "total_free_minutes" =>  $total_free_minutes,
                    "free_earnings" =>   sprintf( '%0.2f',$total_free_earning),
                    "total_ask_booking" => $total_ask_booking,
                    "aaq_earnings" => sprintf( '%0.2f',($aaqmoney + $aaqmoney_tds)),
                    "gift_count" =>  $gifts,
                    "gift_earnings" =>   sprintf( '%0.2f',($giftsmoney + $giftsmoney_tds_astro)),
                    "incentive_earnings " => sprintf('%0.2f', $astrologer_incentive_amount),
                    "selling_count" => $sellingcount,
                    "selling_earnings" =>  sprintf( '%0.2f',($sellingearning + $sellingearning_tds_price)),
                    "total_pg" =>  sprintf('%0.2f', ($total_gst_astro + $total_gift_gst_astro)),
                    "total_tds" => sprintf('%0.2f', ($total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit)),
                    "total_earnings" =>     sprintf('%0.2f', $total_earnings_all), 
                    "total_bank_credit" =>  sprintf('%0.2f', $total_bank_credit), 

                    // "total_booking" =>  $total_book,
                    // "booking_amount" => $booking_amount,
                    // "admin_earnings" => sprintf('%0.2f', $booking_amount - $astrologer_comission_amount),

                    // "astrologer_incentive_amount" => sprintf('%0.2f', $astrologer_incentive_amount),
                    // // "Astrologer_pending_payout" => sprintf('%0.2f', $total_debit_payouts),
                    // "Astrologer_pending_payout" => sprintf('%0.2f', $total_credit_payouts - $total_debit_payouts),
                    // "total_tds_astro" => sprintf('%0.2f', $total_tds_astro),
                    // "total_gst_astro" => sprintf('%0.2f', $total_gst_astro),

                    // "total_free_booking" => $total_free_booking,

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
                'Name',
                'Email',
                'Mobile',
                'Added Date',
            );
            $i = 1;
            $queryqb = UserProfile::query();
            $queryqb->orderBy('users.updated_at', 'DESC');
            $queryqb->where('link_id',auth()->user()->id);
            $queryqb->whereNOTIN('users.status', [2]);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

                   
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('users.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('users.created_at', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                $dataexport[] = [
                    $a->name,
                    $a->email,
                    $a->mobile,
                    date('d-M-Y h:ia', strtotime($a->created_at))

                ];
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"User" . date('Y-m-d-h:i:s') . '.csv');
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
        
        $queryqb = UserProfile::query();
        $queryqb->orderBy('users.updated_at', 'DESC');
        $queryqb->where('link_id',auth()->user()->id);
        $queryqb->whereNOTIN('users.status', [2]);
        if (!is_null($request->name)) {
            $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
        }
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

               
            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('users.created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('users.created_at', $request->end_date);
            }
        }

        $data = $queryqb->paginate(50);
        

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
        $exporturl = 'admin.user.myuserlist';
        $addbutton = 'Add Doctor';
        $importbutton = 'Upload Excel';
        $title = 'User List';
        $listurl = route('admin.myuser.myuserlist');
        $addurl = route('admin.myuser.create_myuser', $what);
        $importurl = route('admin.user.import_mystaff');
        $editurl = 'admin.user.edit_mystaff';
        $loginurl = 'admin.user.login_mystaff';
        $destroyurl = route('admin.user.destroy_mystaff');
        $addurl1 = route('admin.user.store_mystaff');
        $viewurl = 'admin.user.view_mystaff';

        return view('doctor.user.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'exporturl', 'what', 'viewurl', 'loginurl'));
    }

    public function myuserlistnew(Request $request, $what = 1)
    {
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            $doctorIds = [$authUser->id];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
            $doctorIds[] = $authUser->id;
        }
        $userid = array();
        $memberid = array();
        $userlist = [];
        $a = Booking::with(['userdetail', 'memberdetail'])
            ->whereIn('doctor_id', $doctorIds)
            ->get();
        if ($a) {
            foreach ($a as $key) {
                if (($key->user_id == $key->member_id) || ($key->member_id == 0)) {
                    if (!in_array($key->user_id, $userid)) {
                        array_push($userid, $key->user_id);
                        $userlist[] = [
                            'is_member' => false,
                            'user_details' => $key->userdetail,
                            'member_details' => null, 
                        ];
                    }
                } else {
                    if (!in_array($key->member_id, $memberid)) {
                        array_push($memberid, $key->member_id);
                        $userlist[] = [
                            'is_member' => true,
                            'user_details' => $key->userdetail,
                            'member_details' => $key->memberdetail, 
                        ];
                    }
                }
            }
        }
        
        $exporturl = 'admin.user.myuserlist';
        $addbutton = 'Add Doctor';
        $importbutton = 'Upload Excel';
        $title = 'User List';
        $listurl = route('admin.myuser.myuserlist');
        $addurl = route('admin.myuser.create_myuser', $what);
        $importurl = route('admin.user.import_mystaff');
        $editurl = 'admin.user.edit_mystaff';
        $loginurl = 'admin.user.login_mystaff';
        $destroyurl = route('admin.user.destroy_mystaff');
        $addurl1 = route('admin.user.store_mystaff');
        $viewurl = 'admin.user.view_mystaff';

        return view('doctor.user.myuserlistnew', compact('userlist', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'exporturl', 'what', 'viewurl', 'loginurl'));
    }

    public function create_myuser  ($what = '')
    {
        $title = 'Add Paitent';
        $listname = 'Paitent';
        $listurl = route('admin.myuser.myuserlist');
        $addurl = route('admin.myuser.store_patient', $what);
        return view('doctor.user.create', compact('title', 'listurl', 'listname', 'addurl', 'what'));
    }

    public function store_patient(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('mysql2')
                        ->table('users')
                        ->where('email', $value)
                        ->whereNotIn('status', [2])
                        ->exists();

                    if ($exists) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'mobile' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('mysql2')
                        ->table('users')
                        ->where('mobile', $value)
                        ->whereNotIn('status', [2])
                        ->exists();

                    if ($exists) {
                        $fail('The mobile has already been taken.');
                    }
                },
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        $image_name = 'default.png';
        $a = new UserProfile();
        
        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/user', $image_name);
        }

        $a->link_id = auth()->user()->id;
        $a->name = $request->name;
        $a->last_name = $request->lname;
        $a->password = Hash::make(12345);

        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->image = $image_name;
        $a->gender = $request->gender;
        $a->date_of_birth = $request->date_of_birth;
        $a->save();
        return redirect()->route('admin.myuser.myuserlist', $a->id)
            ->with('success', 'Created successfully');
    }

    public function paitentprofile ($id, $what = '')
    {
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = UserProfile::where('id', $id)->whereNOTIN('status',[2])->first();
        if ($a) {
            $user_profile = $a;
            return view('doctor.user.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function paitentprofilenew ($id, $what = '')
    {
        $credits = [];
        $debits = [];
        if ($id == 1) {
            $a = Member::where('id', $what)->whereNOTIN('status',[2])->first();
        } else  
        {
            $a = UserProfile::where('id', $what)
                ->with([
                    'userPayments' => function ($query) {
                        $query->orderBy('created_at', 'desc')
                            ->select('id', 'user_id', 'amount', 'action', 'invoice_no', 'trxn_id', 'created_at','type');
                    }
                ])
                ->first();
            $credits = $a->userPayments->where('action', 'credit')->wherein('type',[2,3])->values();
            $debits = $a->userPayments->where('action', 'debit')->where('type',2)->values();   
        }
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        
        if ($a) {
            if ($id == 1) {
                $appointments = Booking::where('member_id', $what)
                    ->orderBy('schedule_date', 'desc')
                    ->with(['prescriptions'])
                    ->get();
                $lmr = MedicalRecord::where('member_id', $what)->where('member_type','other')->where('status',1)
                          ->get();    
            } else {
                $appointments = Booking::where('user_id', $what)
                ->orderBy('schedule_date', 'desc')
                ->with(['prescriptions'])
                ->get();
                $lmr = MedicalRecord::where('user_id', $what)->where('member_type','self')->where('status',1)
                          ->get();
            }
            $appointmentsWithDoctor = $appointments->map(function ($appointment) {
                $doctor = User::find($appointment->doctor_id);
               
                if ($doctor) {

                    $doctor->image = asset('content/doctor/' . $doctor->image);
                    $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    
                    if ($doctorprofile && $doctorprofile->specialisations) {
                        $specialisationIds = explode('|', $doctorprofile->specialisations);
        
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
        
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        $appointment->doctor = $doctor;
                        $appointment->doctorprofile = [];
                    }

                }

                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->first(['treatment_name']); // Fetch treatment name based on the treatment_id
            
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                    } else {
                        $appointment->treatment_name = null;
                    }
                    $appointment->invoiceurl = route('invoiceuser',$appointment->id);
                    $validPrescriptions = $appointment->prescriptions->filter(function ($prescription) {
                        return $prescription->status === 1;
                    })->map(function ($prescription) use ($appointment) {
                        $prescription->viewurl = route('prescriptionview', $prescription->id);
                        return $prescription;
                    })->values();
                    $appointment->prescriptionlist = $validPrescriptions;
                    unset($appointment->prescriptions);
                    $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)/*->where('member_type',$appointment->member_type)*/->where('type',"xray")->get();
                    $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)/*->where('member_type',$appointment->member_type)*/->where('type',"lab")->get();
                    $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)/*->where('member_type',$appointment->member_type)*/->where('type',"prescription")->get(); $userMedical_xray->each(function ($record) {
                        $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                    });
                    $userMedical_lab->each(function ($record) {
                        $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                    });
                    $userMedical_prescription->each(function ($record) {
                        $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                    });
                    $appointment->userMedical_xray = $userMedical_xray;
                    $appointment->userMedical_lab = $userMedical_lab;
                    $appointment->userMedical_prescription = $userMedical_prescription;
                return $appointment;
            });

            $prescritptionDetails = $appointments->filter(function ($appointment) {
                $validPrescriptions = $appointment->prescriptions->filter(function ($prescription) {
                    return $prescription->status === 1;
                });

                return $validPrescriptions->isNotEmpty();
            })->map(function ($appointment) {
                $validPrescriptions = $appointment->prescriptions->filter(function ($prescription) {
                    return $prescription->status === 1;
                })->map(function ($prescription) use ($appointment) {
                    $prescription->viewurl = route('prescriptionview', $prescription->id);
                    return $prescription;
                })->values();

                $appointment->invoiceurl = route('invoiceuser', $appointment->id);

                $appointment->prescriptionlist = $validPrescriptions;
                unset($appointment->prescriptions);
                return $appointment;
            });
            if ($lmr->isEmpty()) {
            } else {
                $lmr->each(function ($medicalRecord) {
                    if ($medicalRecord->image) {
                        $medicalRecord->image = asset('project/public/medical_records/') . '/' . $medicalRecord->image;
                    } else {
                        $medicalRecord->image = asset('project/public/medical_records/default.jpg');
                    }
                });

                foreach ($lmr as $medicalRecord) {
                    if ($medicalRecord->member_type == 'self') {
                        $user = UserProfile::where('id', $medicalRecord->user_id)->first();
                    } elseif ($medicalRecord->member_type == 'relation' || $medicalRecord->member_type == 'other') {
                        $user = Member::where('id', $medicalRecord->user_id)->first();
                    }
                    $medicalRecord->user = $user;
                }
            }
            $user_profile = $a;
            return view('doctor.user.paitentprofilenew', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','appointmentsWithDoctor','prescritptionDetails','lmr','credits','debits','id'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function prescriptionView  ($id, $what = '')
    {
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = Prescription::where('id', $id)->first();
        if ($a) {
            $book = Booking::find($a->booking_id);
            $doctor = User::find($book->doctor_id);
            $clinic = UserEstablishmentClinic::where('id', $book->clinic_id)->first();

            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
            $firstSpecialisation = $specialisationNames[0] ?? '';
            $user = UserProfile::find($book->user_id);
            if (!$book) {
                return redirect()->back()
                ->with('failure', 'Not found any data');
            }
            $data = [
                'dr_name' => $doctor->name,
                'dr_email' => $doctor->email,
                'dr_mobile' => $doctor->mobile,
                'user_name' => $user->name,
                'user_gender' => $user->gender,
                'dr_spec' => $firstSpecialisation,//implode(', ', $specialisationNames), 
                'clinic_address' => $clinic->address,
                'clinic_register' => $clinic->register_number,
                'booking_id' => $a->booking_id,
                'booking_created' => $a->created_at,
                'notes' => $a->notes,
                'diagnosis' => $a->diagnosis,
                'advice' => $a->advice,
                'prescription' => $a->prescription
            ];
           return view('doctor.user.prescription', [
                    'dr_name' => $doctor->name,
                    'dr_email' => $doctor->email,
                    'dr_mobile' => $doctor->mobile,
                    'user_name' => $user->name,
                    'user_gender' => $user->gender,
                    'dr_spec' => $firstSpecialisation, // or implode(', ', $specialisationNames)
                    'clinic_address' => $clinic->address,
                    'clinic_register' => $clinic->register_number,
                    'booking_id' => $a->booking_id,
                    'booking_created' => $a->created_at,
                    'notes' => $a->notes,
                    'diagnosis' => $a->diagnosis,
                    'advice' => $a->advice,
                    'prescription' => $a->prescription
                ]);

        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function dwnprescriptionView  ($id, $what = '')
    {
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = Prescription::where('id', $id)->first();
        if ($a) {
            $book = Booking::find($a->booking_id);
            $doctor = User::find($book->doctor_id);
            $clinic = UserEstablishmentClinic::where('id', $book->clinic_id)->first();

            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
            $firstSpecialisation = $specialisationNames[0] ?? '';
            $user = UserProfile::find($book->user_id);
            if (!$book) {
                return redirect()->back()
                ->with('failure', 'Not found any data');
            }
            $data = [
                'dr_name' => $doctor->name,
                'dr_email' => $doctor->email,
                'dr_mobile' => $doctor->mobile,
                'user_name' => $user->name,
                'user_gender' => $user->gender,
                'dr_spec' => $firstSpecialisation,//implode(', ', $specialisationNames), 
                'clinic_address' => $clinic->address,
                'clinic_register' => $clinic->register_number,
                'booking_id' => $a->booking_id,
                'booking_created' => $a->created_at,
                'notes' => $a->notes,
                'diagnosis' => $a->diagnosis,
                'advice' => $a->advice,
                'prescription' => $a->prescription
            ];
           return view('doctor.user.dwnprescriptionView', [
                    'dr_name' => $doctor->name,
                    'dr_email' => $doctor->email,
                    'dr_mobile' => $doctor->mobile,
                    'user_name' => $user->name,
                    'user_gender' => $user->gender,
                    'dr_spec' => $firstSpecialisation, // or implode(', ', $specialisationNames)
                    'clinic_address' => $clinic->address,
                    'clinic_register' => $clinic->register_number,
                    'booking_id' => $a->booking_id,
                    'booking_created' => $a->created_at,
                    'notes' => $a->notes,
                    'diagnosis' => $a->diagnosis,
                    'advice' => $a->advice,
                    'prescription' => $a->prescription
                ]);

        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    

    public function mycalender (Request $request,$id, $what = '')
    {
        $title = 'View Calender';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $userId = auth()->user();
        if ($userId->parent_id == 0) {
            $users = User::where(function ($query) use ($userId) {
                $query->where('id', $userId->id)
                    ->orWhere('parent_id', $userId->id);
            })
            ->get();
        } elseif ($userId->parent_id > 0) {
            $users = User::where('id', $userId->id)->get();
        }
        $bookings = Booking::whereIn('doctor_id', $users->pluck('id'))->whereIn('status', [4])
                ->orderBy('schedule_date', 'desc')
                ->get();
        if ($request->c1 && $request->u1) {
           $bookings = Booking::where('doctor_id', $request->u1)->where('clinic_id', $request->c1)->whereIn('status', [4])
                ->orderBy('schedule_date', 'desc')
                ->get();
        }
        if ($request->c1) {
            $bookings = Booking::where('clinic_id', $request->c1)->whereIn('status', [4])
                ->orderBy('schedule_date', 'desc')
                ->get();
        }
        if ($request->u1) {
            $bookings = Booking::where('doctor_id', $request->u1)->whereIn('status', [4])
                ->orderBy('schedule_date', 'desc')
                ->get();
        }
        

        $events = [];
        foreach ($bookings as $booking) {
            $start_time = date('H:i:s', strtotime($booking->start_time));
            $end_time = date('H:i:s', strtotime($booking->end_time));
            $a = $booking;
            // if member_id null or empty or 0 then show user name else show member name
            if (!$a->member_id || $a->member_id == 0) {
                $name = $booking->userdetail->name;
            } else {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $name = $member->name;
                 } else {
                     $name = $booking->userdetail->name;
                 }
            }
            // $name = $booking->userdetail->name;
            if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $name = $member->name;
                 }
            }
            $colors = ['#F67F52', '#52C1F6', '#7EF652', '#F652A0', '#A552F6', '#52F6C6', '#F6E252'];
            $events[] = [
                'title' => 'Booking with ' . $name,
                'start' => $booking->schedule_date . 'T' . $start_time,
                'end' => $booking->schedule_date . 'T' . $end_time, 
                'url' => route('admin.appoint.myappointmentdetails', ['id' => $booking->id]),
                'color' => $this->randomColor()
            ];
        }
        // dd($events);
        $c = UserEstablishmentClinic::where('user_id', auth()->user()->id)->whereNOTIN('status',[2])->get();
        $u = User::where('parent_id', auth()->user()->id)->where('status',1)->orderBy('name', 'DESC')->get();
        return view('doctor.appointments.calender', compact('title', 'listurl', 'listname', 'editurl', 'what','events','c','u'));
    }

    function randomColor() {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public function mystats ($id, $what = '')
    {
        $title = 'View Stats';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $doctorId = auth()->user()->id;

        $totalBookings = Booking::where('doctor_id', $doctorId)->count();

        $rebookCount = Booking::select('user_id')
            ->where('doctor_id', $doctorId)
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $upcomingBookings = Booking::where('doctor_id', $doctorId)
            ->whereIn('status', [0, 4])
            ->count();

        $pendingBookings = Booking::where('doctor_id', $doctorId)
            ->where('status', 0)
            ->count();
        $totalAppDownloads = UserProfile::where('link_id', $doctorId)->count();

        $totalSocialConnectViews = SocialConnectView::where('blog_by_id', $doctorId)->count();
        $totalEarnings = Booking::where('doctor_id', $doctorId)
            ->where('status', 1)
            ->sum('total_amount');
        
        return view('doctor.appointments.mystats', compact('title', 'listurl', 'listname', 'editurl', 'what','totalBookings'
                            ,'rebookCount'
                            ,'upcomingBookings'
                            ,'pendingBookings'
                            ,'totalAppDownloads'
                            ,'totalSocialConnectViews'
                            ,'totalEarnings'));
    }

    public function myappointment (Request $request,$id, $what = '')
    {
        $userId = auth()->user();
        if ($userId->parent_id == 0) {
            $users = User::where(function ($query) use ($userId) {
                $query->where('id', $userId->id)
                    ->orWhere('parent_id', $userId->id);
            })
            ->get();
        } elseif ($userId->parent_id > 0) {
            $users = User::where('id', $userId->id)->get();
        }
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Booking for',
                'Email',
                'Mobile',
                'Type',
                'Symptoms'
                ,'Treatment'
                ,'Date & time'
                ,'Status'
                ,'Payment Status',
                'Subtotal',
                'gst',
                'partial amount',
                'total amount',
                'Created at',
            );
            $i = 1;
            $queryqb = Booking::query();
            $queryqb->orderBy('schedule_date', 'desc');
            $queryqb->whereIn('doctor_id', $users->pluck('id'));
            if (!is_null($request->name)) {
                $queryqb->whereHas('userdetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('mobile', 'LIKE', '%' . $request->name . '%');
                })->orWhereHas('doctordetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%');
                });
            }
            if (!is_null($request->status)) {
                $queryqb->where('status', $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('schedule_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('schedule_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('schedule_date', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }

                 

                 } else {
                     $a->member = null;

                     
                 }
                 $userProfile = UserProfile::find($a->user_id);
                 $doctor = User::find($a->doctor_id);
                 if ($doctor) {
                     $a->doctor = $doctor;
                     $a->user = $userProfile;
                     $treatment = DB::table('treatments')
                         ->where('id', $a->treatment_id)
                         ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                         ->first();

                     if ($treatment) {
                         $a->treatment_name = $treatment->treatment_name;
                         $a->call_before_confirmation = $treatment->call_before_confirmation;
                         $a->average_duration = $treatment->average_duration;
                     } else {
                         $a->treatment_name = null;
                         $a->call_before_confirmation = null;
                         $a->average_duration = null;
                     }
                 }
                 $status = '';
                 if($a->status == 0) {
                    $status = 'Pending';
                 }
                 if($a->status == 1) {
                    $status = 'Completed';
                 }
                 if($a->status == 2) {
                    $status = 'Canceled';
                 }
                 if($a->status == 3) {
                    $status = 'Rejected';
                 }
                 if($a->status == 4) {
                    $status = 'Accepted';
                 }
                 
                 if($a->status == 5) {
                    $status = 'Payment Failed';
                 }
                 if($a->status == 6) {
                    $status = 'Refunded';
                 }
                 $ps = '';
                if($a->is_paid==0) {
                    $ps = 'Not Paid';
                }
                if($a->is_paid==1) {
                    $ps = 'Fully Paid';

                }
                if($a->is_paid==2) {
                    $ps = 'Partially Paid';

                }
                if ($a->member) {
                    $dataexport[] = [
                        $a->member->name.' '.$a->member->last_name,
                        'Member',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                } else {
                    $dataexport[] = [
                        $a->user->name.' '.$a->user->last_name,
                        'Self',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];    
                }
                
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Appointment" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = 'View Appointments';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        
        if ($request->doctor_id) {
            $data = Booking::where('doctor_id', $request->doctor_id)
                        ->with(['userdetail', 'doctordetail',])
                        ->when(!is_null($request->name), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                                $q->orwhere('last_name', 'LIKE', '%' . $request->name . '%');
                            })->orWhereHas('doctordetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            });
                        })
                        ->when(!is_null($request->email), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('email', 'LIKE', '%' . $request->email . '%');
                            });
                        })
                        ->when(!is_null($request->mobile), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('mobile', 'LIKE', '%' . $request->mobile . '%');
                            });
                        })
                        ->when(!is_null($request->status), function ($query) use ($request) {
                            $query->where('status', $request->status);
                        })
                        ->when(!is_null($request->start_date) && !is_null($request->end_date), function ($query) use ($request) {
                            $query->whereBetween('schedule_date', [$request->start_date, $request->end_date]);
                        })
                        ->orderBy('schedule_date', 'desc')
                        ->paginate(50);
        } else {
            $data = Booking::whereIn('doctor_id', $users->pluck('id'))
                        ->with(['userdetail', 'doctordetail',])
                        ->when(!is_null($request->name), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                                $q->orwhere('last_name', 'LIKE', '%' . $request->name . '%');
                            })->orWhereHas('doctordetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            });
                        })
                        ->when(!is_null($request->email), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('email', 'LIKE', '%' . $request->email . '%');
                            });
                        })
                        ->when(!is_null($request->mobile), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('mobile', 'LIKE', '%' . $request->mobile . '%');
                            });
                        })
                        ->when(!is_null($request->status), function ($query) use ($request) {
                            $query->where('status', $request->status);
                        })
                        ->when(!is_null($request->start_date) && !is_null($request->end_date), function ($query) use ($request) {
                            $query->whereBetween('schedule_date', [$request->start_date, $request->end_date]);
                        })
                        ->orderBy('schedule_date', 'desc')
                        ->paginate(50);
        }
        if ($data) {
            $appointmentsWithDoctor = $data->map(function ($appointment) use ($userId) {
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }
            
                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('content/user/' . $userProfile->image);
                }
            
                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
            
                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
            
                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }
            
                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();
            
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }
    
    
                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;
                }
                return $appointment;
            });
            $u = User::where('parent_id', auth()->user()->id)->where('status',1)->orderBy('name', 'DESC')->get();
            return view('doctor.appointments.list', compact('appointmentsWithDoctor', 'title', 'listurl', 'listname', 'editurl', 'what','data','u'))
            ->with('i', ($request->input('page', 1) - 1) * 5);;
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function myappointmentpending (Request $request,$id, $what = '')
    {
        $userId = auth()->user();
        if ($userId->parent_id == 0) {
            $users = User::where(function ($query) use ($userId) {
                $query->where('id', $userId->id)
                    ->orWhere('parent_id', $userId->id);
            })
            ->get();
        } elseif ($userId->parent_id > 0) {
            $users = User::where('id', $userId->id)->get();
        }
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Booking for',
                'Email',
                'Mobile',
                'Type',
                'Symptoms'
                ,'Treatment'
                ,'Date & time'
                ,'Status'
                ,'Payment Status',
                'Subtotal',
                'gst',
                'partial amount',
                'total amount',
                'Created at',
            );
            $i = 1;
            $queryqb = Booking::query();
            $queryqb->orderBy('schedule_date', 'desc');
            $queryqb->whereIn('doctor_id', $users->pluck('id'));
            if (!is_null($request->name)) {
                $queryqb->whereHas('userdetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('mobile', 'LIKE', '%' . $request->name . '%');
                })->orWhereHas('doctordetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%');
                });
            }
            if (!is_null($request->status)) {
                $queryqb->where('status', $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('schedule_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('schedule_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('schedule_date', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }

                 

                 } else {
                     $a->member = null;

                     
                 }
                 $userProfile = UserProfile::find($a->user_id);
                 $doctor = User::find($a->doctor_id);
                 if ($doctor) {
                     $a->doctor = $doctor;
                     $a->user = $userProfile;
                     $treatment = DB::table('treatments')
                         ->where('id', $a->treatment_id)
                         ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                         ->first();

                     if ($treatment) {
                         $a->treatment_name = $treatment->treatment_name;
                         $a->call_before_confirmation = $treatment->call_before_confirmation;
                         $a->average_duration = $treatment->average_duration;
                     } else {
                         $a->treatment_name = null;
                         $a->call_before_confirmation = null;
                         $a->average_duration = null;
                     }
                 }
                 $status = '';
                 if($a->status == 0) {
                    $status = 'Pending';
                 }
                 if($a->status == 1) {
                    $status = 'Completed';
                 }
                 if($a->status == 2) {
                    $status = 'Canceled';
                 }
                 if($a->status == 3) {
                    $status = 'Rejected';
                 }
                 if($a->status == 4) {
                    $status = 'Accepted';
                 }
                 
                 if($a->status == 5) {
                    $status = 'Payment Failed';
                 }
                 if($a->status == 6) {
                    $status = 'Refunded';
                 }
                 $ps = '';
                if($a->is_paid==0) {
                    $ps = 'Not Paid';
                }
                if($a->is_paid==1) {
                    $ps = 'Fully Paid';

                }
                if($a->is_paid==2) {
                    $ps = 'Partially Paid';

                }
                if ($a->member) {
                    $dataexport[] = [
                        $a->member->name.' '.$a->member->last_name,
                        'Member',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                } else {
                    $dataexport[] = [
                        $a->user->name.' '.$a->user->last_name,
                        'Self',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];    
                }
                
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Appointment" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = 'View Appointments';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        
        $data = Booking::whereIn('doctor_id', $users->pluck('id'))->where('status',0)
                        ->with(['userdetail', 'doctordetail',])
                        ->when(!is_null($request->name), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                                $q->orwhere('last_name', 'LIKE', '%' . $request->name . '%');
                            })->orWhereHas('doctordetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            });
                        })
                        ->when(!is_null($request->email), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('email', 'LIKE', '%' . $request->email . '%');
                            });
                        })
                        ->when(!is_null($request->mobile), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('mobile', 'LIKE', '%' . $request->mobile . '%');
                            });
                        })
                        ->when(!is_null($request->doctor_id), function ($query) use ($request) {
                            $query->where('doctor_id', $request->doctor_id);
                        })
                        ->when(!is_null($request->start_date) && !is_null($request->end_date), function ($query) use ($request) {
                            $query->whereBetween('schedule_date', [$request->start_date, $request->end_date]);
                        })
                        ->orderBy('schedule_date', 'desc')
                        ->paginate(50);
        if ($data) {
            $appointmentsWithDoctor = $data->map(function ($appointment) use ($userId) {
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }
            
                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('content/user/' . $userProfile->image);
                }
            
                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
            
                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
            
                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }
            
                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();
            
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }
    
    
                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;
                }
                return $appointment;
            });
            $u = User::where('parent_id', auth()->user()->id)->where('status',1)->orderBy('name', 'DESC')->get();
            return view('doctor.appointments.myappointmentpending', compact('appointmentsWithDoctor', 'title', 'listurl', 'listname', 'editurl', 'what','data','u'))
            ->with('i', ($request->input('page', 1) - 1) * 5);;
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function myappointmentdetails($id, $what = '')
    {
        $title = 'View Appointment Detail';
        $listname = 'Detail';
        $listurl = route('admin.appoint.myappointment', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = Booking::where('id', $id)->first();
        if ($a) {
            $c = UserEstablishmentClinic::where('user_id', auth()->user()->id)->whereNOTIN('status',[2])->get();
            return view('doctor.appointments.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','c'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    // markaspaid
    public function markaspaid($id)
    {
        $a = Booking::where('id', $id)->first();
        if ($a) {
            $a->is_paid = 1;
            $a->save();
            return redirect()->back()
                ->with('success', 'Marked as paid successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }


    public function updatemyappointment($id, $what = '')
    {
        $a = Booking::where('id', $id)->first();
        if ($a) {
            $a->status = $what;
            $a->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function updatemyappointmentslot($id, Request $request)
    {
        $a = Booking::where('id', $id)->first();
        if ($a) {
            list($start_time, $end_time) = explode(" - ", $request->start_time);
            $a->schedule_date = $request->schedule_date;
            $a->clinic_id = $request->clinic_id;
            $a->schedule_time = $start_time;
            $a->start_time = $start_time;
            $a->end_time = $end_time;
            $a->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function updatemyappointmentcancel($id, Request $request)
    {
        $a = Booking::where('id', $id)->first();
        if ($a) {
            $re = $request->cancel_other2;
            if ($request->cancel_other2 == 'Other') {
                $re = $request->cancel_otherl;
            }
            $a->cancel_by = auth()->user()->id;
            $a->cancel_other = $re;
            $a->status = 2;
            $a->save();
            if ($a->loyality_points > 0) {
                $get = UserPayment::where('booking_id',$a->id)->where('type',2)->first();
                if ($get) {
                    $u = UserProfile::where('id',$a->user_id)->first();
                    $update_wallet =  $u->wallet+$get->amount;
                    $new = new UserPayment();
                    $new->user_id = $u->id;
                    $new->type = 2;
                    $new->booking_id = $a->id;
                    $new->action = 'credit';
                    $new->amount = $get->amount;
                    $new->old_balance = $u->wallet;
                    $new->payment_status = 'completed';
                    $new->payment_method = 'online';
                    $new->new_balance = $update_wallet;
                    $new->status = 1;
                    $new->trxn_id =  time().rand();
                    $new->save();
                    $u->wallet = $update_wallet;
                    $u->save();
                }
            }
            return redirect()->back()
                ->with('success', 'Cancel successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function addappointment($what = '')
    {
        $userId = auth()->user();
        if ($userId->parent_id == 0) {
            $mainid = $userId->id;
        } elseif ($userId->parent_id > 0) {
            $mainid = $userId->parent_id;
        }
        $title = 'Add Appointment';
        $listname = 'Appointment';
        $listurl = route('admin.appoint.myappointment');
        $a = UserProfile::where('status', 1)->where('link_id',$mainid)->orderBy('name', 'ASC')->get();
        $t = Treatment::whereNOTIN('status', [2])->where('doctor_id',$mainid)->orderBy('updated_at', 'ASC')->get();
        $c = UserEstablishmentClinic::where('user_id', $mainid)->whereNOTIN('status',[2])->get();
        $addurl = route('admin.appoint.store_appointment', $what);
        $u = User::where('parent_id', auth()->user()->id)->where('status',1)->orderBy('name', 'DESC')->get();

        return view('doctor.appointments.create', compact('title', 'listurl', 'listname', 'addurl', 'what','a','c','t','u'));
    }

    public function getDoctorsByTreatment($id)
    {
        $treatmentDoctor = TreatmentDoctor::where('treatment_id', $id)
            ->with('doctor')
            ->get();

        $doctors = [];

        foreach ($treatmentDoctor as $doctorDetail) {

            $doctor = $doctorDetail->doctor;

            if ($doctor) {

                // doctor image
                $doctor->image = asset('content/doctor/') . '/' . $doctor->image;

                // get extra info
                $userInformation = UserInformation::where('user_id', $doctor->id)->first();

                $specialisations = [];

                if ($userInformation) {
                    $specialisations = $this->getSpecializationsFromIds($userInformation->specialisations);
                }

                $doctors[] = [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'image' => $doctor->image,
                    'specialisations' => $specialisations
                ];
            }
        }

        return response()->json($doctors);
    }

    private function getSpecializationsFromIds($specializations)
    {
        // If there are no specializations, return an empty array
        if (empty($specializations)) {
            return [];
        }
    
        // Split the specializations by '|' to get an array of IDs
        $specializationIds = explode('|', $specializations);
    
        // Query the master_specializations table to get the specialization details
        $specializations = MasterSpecialsation::whereIn('id', $specializationIds)->get();
    
        // Format the results as an array of specialization names
        $specializationsArray = $specializations->pluck('name')->toArray();
    
        return $specializationsArray;
    }

    public function getslotsold(Request $request)
    {
        if ($request->doctor_id == '' || $request->treatment == '') {
            return response(['status' => false], 200);
        }
        $key1 = UserInformation::where('user_id', auth()->user()->id)
                ->first();
        if ($request->doctor_id) {
            $key1 = UserInformation::where('user_id', $request->doctor_id)
                ->first();
        }
        if ($key1) {
            $treament = Treatment::where('id', $request->treatment)->first();
            $request->total_minutes = $treament->average_duration ?? 60;
            $dateslots = array();
            $slot = array(); 
            $apointment_date = $request->dateselect;

            $today_date = strtotime(date('Y-m-d'));
            $given_date = strtotime($apointment_date);
            $id = $key1->user_id;
            if ($given_date >= $today_date) {
                $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                $already_in_time_check = array();
                // if (count($check_bt_) > 0) {
                //     foreach ($check_bt_ as $keybt) {
                //         $time_bt = $keybt->schedule_time;
                //         $minutes = $keybt->total_minutes;
                        
                //         $one_Cal_time = strtotime($time_bt);
                //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));
                        
                //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                //         array_push($already_in_time_check, $array_inn_ol);
                //     }
                // }
                $working_time = json_decode($key1->doctor_availability, true);
                if (!empty($working_time)) {
                    $working_time = json_decode($key1->doctor_availability, true);
                    $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                    if (!empty($schedule)) {
                        if (isset($schedule['morning'])) {
                            $fistslots = explode(' - ', $schedule['morning'][0]);
                            if (isset($fistslots[0]) && isset($fistslots[1])) {
                                $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                foreach ($period as $dt) {
                                    $start = $dt->format('h:ia');
                                    $end = $dt->add($interval)->format('h:ia');
                                    $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                    if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                        $slotEnd = Carbon::parse($end)->format('H:i'); 
                                        $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                            ->where('schedule_date', $apointment_date)
                                            ->where(function ($q) use ($slotStart, $slotEnd) {
                                                $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                  ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                            })
                                            ->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>0
                                        ]);
                                    }  else {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                        $slotEnd = Carbon::parse($end)->format('H:i'); 
                                        $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                            ->where('schedule_date', $apointment_date)
                                            ->where(function ($q) use ($slotStart, $slotEnd) {
                                                $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                  ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                            })
                                            ->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>1
                                        ]);
                                    }
                                }
                            }
                        }
                        if (isset($schedule['evening'])) {
                            $fistslots = explode(' - ', $schedule['evening'][0]);
                            if (isset($fistslots[0]) && isset($fistslots[1])) {
                                $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                foreach ($period as $dt) {
                                    $start = $dt->format('h:ia');
                                    $end = $dt->add($interval)->format('h:ia');
                                    $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                    if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                        $slotEnd = Carbon::parse($end)->format('H:i'); 
                                        $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                            ->where('schedule_date', $apointment_date)
                                            ->where(function ($q) use ($slotStart, $slotEnd) {
                                                $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                  ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                            })
                                            ->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>0
                                        ]);
                                    } else {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                        $slotEnd = Carbon::parse($end)->format('H:i'); 
                                        $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                            ->where('schedule_date', $apointment_date)
                                            ->where(function ($q) use ($slotStart, $slotEnd) {
                                                $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                  ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                            })
                                            ->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>1
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($slot)) {
                array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
            } else {
                array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
            }
            if (!empty($dateslots)) {
                $response = ['status' => true, 'dateslots' => $dateslots];
            }
        }
        return response($response, 200);
    }

    public function getslots(Request $request)
    {
        if ($request->doctor_id == '' || $request->treatment == '') {
            return response(['status' => false], 200);
        }
        $key1 = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $request->clinic_id)->where('status',1)
                ->first();

        if ($request->doctor_id) {
            $key1 = DoctorClinicSlot::where('doctor_id', $request->doctor_id)->where('clinic_id', $request->clinic_id)->where('status',1)
                ->first();
        }
        if (!$key1) {
            $check = UserInformation::where('user_id',$request->doctor_id)->first();
            $new = new DoctorClinicSlot();
            $new->doctor_id = $request->doctor_id;
            $new->clinic_id = $request->clinic_id;
            $new->doctor_availability = $check->doctor_availability ?? '';
            $new->status = 1;
            $new->save();
            $key1 = DoctorClinicSlot::where('doctor_id', $request->doctor_id)->where('clinic_id', $request->clinic_id)->where('status',1)
            ->first();
        }
        if ($key1) {
            $treament = Treatment::where('id', $request->treatment)->first();
            $request->total_minutes = $treament->average_duration ?? 60;
            $dateslots = array();
            $slot = array(); 
            $apointment_date = $request->dateselect;

            $today_date = strtotime(date('Y-m-d'));
            $given_date = strtotime($apointment_date);
            $id = $key1->user_id;
            $checkholiday = UserEstablishmentHoliday::/*where('date',$apointment_date)*/whereDate('date', '<=', $apointment_date)->whereDate('end_date', '>=', $apointment_date)->where('establishment_clinics_id',$request->clinic_id)->where('status',1)->first();
            if (!$checkholiday) {
                if ($given_date >= $today_date) {
                    $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                    // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                    $already_in_time_check = array();
                    // if (count($check_bt_) > 0) {
                    //     foreach ($check_bt_ as $keybt) {
                    //         $time_bt = $keybt->schedule_time;
                    //         $minutes = $keybt->total_minutes;
                            
                    //         $one_Cal_time = strtotime($time_bt);
                    //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));
                            
                    //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                    //         array_push($already_in_time_check, $array_inn_ol);
                    //     }
                    // }
                    $working_time = json_decode($key1->doctor_availability, true);
                    if (!empty($working_time)) {
                        $working_time = json_decode($key1->doctor_availability, true);
                        $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                        if (!empty($schedule)) {
                            if (isset($schedule['morning'])) {
                                $fistslots = explode(' - ', $schedule['morning'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $slotEndTime = strtotime($apointment_date . ' ' . $end);
                                            $maxSlotEnd = strtotime($apointment_date . ' ' . $fistslots[1]);
                                        if ($slotEndTime > $maxSlotEnd) {
                                            continue;
                                        }
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                            $slotEnd = Carbon::parse($end)->format('H:i'); 
                                            $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                ->where('schedule_date', $apointment_date)
                                                ->where(function ($q) use ($slotStart, $slotEnd) {
                                                    $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                      ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                })
                                                ->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        }  else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                            $slotEnd = Carbon::parse($end)->format('H:i'); 
                                            $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                ->where('schedule_date', $apointment_date)
                                                ->where(function ($q) use ($slotStart, $slotEnd) {
                                                    $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                      ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                })
                                                ->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                            if (isset($schedule['evening'])) {
                                $fistslots = explode(' - ', $schedule['evening'][0]);
                                if (isset($fistslots[0]) && isset($fistslots[1])) {
                                    $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                    $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                    $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                    foreach ($period as $dt) {
                                        $start = $dt->format('h:ia');
                                        $end = $dt->add($interval)->format('h:ia');
                                        $slotEndTime = strtotime($apointment_date . ' ' . $end);
                                        $maxSlotEnd = strtotime($apointment_date . ' ' . $fistslots[1]);
                                        if ($slotEndTime > $maxSlotEnd) {
                                            continue;
                                        }
                                        $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                        if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                            $slotEnd = Carbon::parse($end)->format('H:i'); 
                                            $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                ->where('schedule_date', $apointment_date)
                                                ->where(function ($q) use ($slotStart, $slotEnd) {
                                                    $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                      ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                })
                                                ->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>0
                                            ]);
                                        } else {
                                            $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                            $slotStart = Carbon::parse($time_derive)->format('H:i'); // e.g., 10:00
                                            $slotEnd = Carbon::parse($end)->format('H:i'); 
                                            $check = Booking::whereIn('status', ['0', '1', '4', '6', '7'])
                                                ->where('schedule_date', $apointment_date)
                                                ->where(function ($q) use ($slotStart, $slotEnd) {
                                                    $q->whereRaw('STR_TO_DATE(schedule_time, "%h:%i%p") < ?', [$slotEnd])
                                                      ->whereRaw('STR_TO_DATE(end_time, "%h:%i%p") > ?', [$slotStart]);
                                                })
                                                ->first();
                                            $is_book = $check ? 1 : 0;
                                            array_push($slot, [
                                                "start_time" => $time_derive,
                                                "end_time" => $end,
                                                "is_book" => $is_book,
                                                "timepass"=>1
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($slot)) {
                array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
            } else {
                array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
            }
            if (!empty($dateslots)) {
                $response = ['status' => true, 'dateslots' => $dateslots];
            }
        }
        return response($response, 200);
    }

    public function store_appointment(Request $request)
    {
        if (!$request->all()) {
            return redirect()->route('admin.home');
        }
        if (session()->has('appointment_submitted')) {
            dd(session()->has('appointment_submitted'));
            return redirect()->route('admin.appoint.myappointments')
                ->with('info', 'You have already submitted this appointment.');
        }
        if ($request->user_type == 'existing_user') {
            $userid = $request->selected_user;
        } else {
            $this->validate($request, [
                'name' => 'required',
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('email', $value)
                            ->whereNotIn('status', [2])
                            ->exists();
                        if ($exists) {
                            $fail('The email has already been taken.');
                        }
                    },
                ],
                'mobile' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('mobile', $value)
                            ->whereNotIn('status', [2])
                            ->exists();
                        if ($exists) {
                            $fail('The mobile has already been taken.');
                        }
                    },
                ],
            ]);

            $userid = null; // No need to save user yet
        }

        list($start_time, $end_time) = explode(" - ", $request->start_time);
        $treatment = Treatment::where('id', $request->treatment_id)->first();
        $subtotal = $treatment->treatment_fees ?? 0;
        $gst = $subtotal * 0.18; // 18% tax
        $total_amount = $subtotal + $gst;
        // Store all data in session
        session([
            'appointment_data' => [
                'user_id' => $userid,
                'user_type' => $request->user_type,
                'name' => $request->name ?? null,
                'lname' => $request->lname ?? null,
                'email' => $request->email ?? null,
                'mobile' => $request->mobile ?? null,
                'consultation_type' => $request->consultation_type,
                'appointment_type' => $request->appointment_type,
                'doctor_id' => $request->doctor_id,
                'clinic_id' => $request->clinic_id,
                'member_id' => $request->member_id ?? $userid,
                'treatment_id' => $request->treatment_id,
                'schedule_date' => $request->schedule_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'symptoms' => $request->symptoms,
                'clinic_id' => $request->clinic_id,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'total_amount' => $total_amount,
                'gst' => $gst,
                'is_paid' => $request->is_paid ?? 0
            ]
        ]);
        $appointmentData = session('appointment_data');
        return view('doctor.appointments.preview', compact('appointmentData'));
    } 

    public function appointmentconfirm(Request $request)
    {
        $appointmentData = session('appointment_data');

        if (!$appointmentData) {
            return redirect()->route('home')->with('error', 'No appointment data found.');
        }
        if ($appointmentData['user_type'] == 'existing_user') {
            $userid = $appointmentData['user_id'];
        } else {
            $this->validate(new Request($appointmentData), [
                'name' => 'required',
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('email', $value)
                            ->whereNotIn('status', [2])
                            ->exists();
                        if ($exists) {
                            $fail('The email has already been taken.');
                        }
                    },
                ],
                'mobile' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('mobile', $value)
                            ->whereNotIn('status', [2])
                            ->exists();
                        if ($exists) {
                            $fail('The mobile has already been taken.');
                        }
                    },
                ],
            ]);

            $a = new UserProfile();
            $a->link_id = auth()->user()->id;
            $a->name = $appointmentData['name'];
            $a->last_name = $appointmentData['lname'];
            $a->password = Hash::make(12345);
            $a->email = $appointmentData['email'];
            $a->mobile = $appointmentData['mobile'];
            $a->gender = $appointmentData['gender'] ?? '';
            $a->date_of_birth = $appointmentData['date_of_birth'] ?? '';
            $a->save();

            $userid = $a->id;
        }

        $doctor_id = $appointmentData['doctor_id'];
        $treatment = Treatment::where('id', $appointmentData['treatment_id'])->first();
        $subtotal = $treatment->treatment_fees ?? 0;
        $gst = $subtotal * 0.18; // 18% tax
        $total_amount = $subtotal + $gst;
        $member_id = $appointmentData['member_id'];
        $member_type = 'self';
        if ($appointmentData['member_id'] == $userid) {
            $member_type = 'relation';
        }
        $appointment = Booking::create([
            'doctor_id' => $doctor_id,
            'user_id' => $userid,
            'consultation_type' => $appointmentData['consultation_type'],
            'appointment_type' => $appointmentData['appointment_type'],
            'treatment_id' => $appointmentData['treatment_id'],
            'member_id' => $member_id,
            'member_type' => 'self',
            'symptoms' => $appointmentData['symptoms'],
            'clinic_id' => $appointmentData['clinic_id'],
            'schedule_date' => $appointmentData['schedule_date'],
            'schedule_time' => $appointmentData['start_time'],
            'start_time' => $appointmentData['start_time'],
            'end_time' => $appointmentData['end_time'],
            'payment_mode' => 1,
            'subtotal' => $subtotal,
            'total_amount' => $total_amount ?? 0,
            'gst' => $gst,
            'notes' => $appointmentData['notes'],
            'status' => 4,
            'is_paid' => $appointmentData['is_paid'] ?? 0
        ]);

        session()->forget('appointment_data');

        $bdate = date("l, F j, Y g:i A", strtotime($appointment->schedule_date . ' ' . $appointment->schedule_time));
        $msgarray = [
            "title" => "📌New Appointment Scheduled",
            "msg" => "Your appointment with Dr. " . $appointment->doctordetail->name . " at " . $appointment->clinicdetail->clinic_name . " has been successfully scheduled for " . $bdate . '.',
            "msg2" => "You have a new appointment with " . $appointment->userdetail->name . " at " . $appointment->clinicdetail->clinic_name . " on " . $bdate . "."
        ];

        $this->async_to_all($msgarray, '', $appointment->id, 'bookingadd');
        session()->flash('appointment_submitted', true);
        return redirect()->route('admin.appoint.myappointmentdetails', $appointment->id)
            ->with('success', 'Created successfully');
    }
    public function store_appointmentfinal(Request $request)
    {
        if ($request->user_type == 'existing_user') {
            $userid = $request->selected_user;
        } else {

            $this->validate($request, [
                'name' => 'required',
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('email', $value)
                            ->whereNotIn('status', [2])
                            ->exists();

                        if ($exists) {
                            $fail('The email has already been taken.');
                        }
                    },
                ],
                'mobile' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('mobile', $value)
                            ->whereNotIn('status', [2])
                            ->exists();

                        if ($exists) {
                            $fail('The mobile has already been taken.');
                        }
                    },
                ],
                // 'gender' => 'required',
                // 'date_of_birth' => 'required',
            ]);
            $image_name = 'default.png';
            $a = new UserProfile();
            $a->link_id = auth()->user()->id;
            $a->name = $request->name;
            $a->last_name = $request->lname;
            $a->password = Hash::make(12345);

            $a->email = $request->email;
            $a->mobile = $request->mobile;
            $a->gender = $request->gender ?? '';
            $a->date_of_birth = $request->date_of_birth ?? '';
            $a->save();
            $userid = $a->id;
        }
        $doctor_id = auth()->user()->id;
        list($start_time, $end_time) = explode(" - ", $request->start_time);
        $treatment = Treatment::where('id',$request->treatment_id)->first();
        $appointment = Booking::create([
            'doctor_id' => $doctor_id,
            'user_id' => $userid,
            'consultation_type' => $request->consultation_type,
            'appointment_type' => 'New Appointment  ',
            'treatment_id' => $request->treatment_id,
            'member_id' => $userid,
            'member_type' => 'self',
            'symptoms' => $request->symptoms,
            'clinic_id' => $request->clinic_id,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $start_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'payment_mode' => 'online',
            'subtotal' => 0,
            'total_amount' => $treatment->treatment_fees ?? 0,
            'gst' => 0,
            'notes' => $request->notes,
            'status' => 4,
            'is_paid'=> $request->is_paid ?? 0
        ]);
        $bdate = date("l, F j, Y g:i A", strtotime($appointment->schedule_date.' '.$appointment->schedule_time));
        $msgarray = array("title"=>"📌New Appointment Scheduled",
                          "msg"=>"Your appointment with Dr. ".$appointment->doctordetail->name." at ".$appointment->clinicdetail->clinic_name." has been successfully scheduled for ".$bdate.'.',

                         "msg2"=>"You have a new appointment with ".$appointment->userdetail->name." at ".$appointment->clinicdetail->clinic_name." on ".$bdate."."
                   );
        $this->async_to_all($msgarray,'',$appointment->id,'bookingadd');
        return redirect()->route('admin.appoint.myappointmentdetails',$appointment->id)
            ->with('success', 'Created successfully');
    }

    public function earninghistory (Request $request)
    {
        $doctorId = auth()->user()->id;

        $query = Booking::where('doctor_id', $doctorId)
            ->where('status', 1)
            ->with(['userDetail' => function ($query) {
                $query->select('id', 'name');
            }])
            ->orderBy('created_at', 'desc');

        if ($request->condition === 'last10') {
            $query->limit(10);
        } elseif ($request->condition === 'lastWeek') {
            $query->whereBetween('created_at', [now()->subWeek(), now()]);
        } elseif ($request->condition === 'lastMonth') {
            $query->whereBetween('created_at', [now()->subMonth(), now()]);
        } elseif ($request->condition === 'custom' && $request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $bookings = $query->get();

        $cumulativeTotal = $bookings->sum('total_amount');

        $response = [
            'status'=>true,
            'total' => $cumulativeTotal,
            'data' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'user_name' => $booking->userDetail->name ?? 'N/A',
                    'total_amount' => $booking->total_amount,
                    'created_at' => $booking->created_at,
                    'created_attime' => $booking->created_at->diffForHumans(),
                    'session_no'=>$booking->session_no,
                ];
            }),
        ];
        $title = 'Earning History';
        return view('doctor.stats.earninghistory', compact('response','title'));
    }

    public function loyalitypoints (Request $request)
    {
        $doctorId = auth()->user()->id;
        
        $appointments = Booking::where('doctor_id', $doctorId)->with('loyalitydetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->get();
        $appointmentsWithLoyality = $appointments->filter(function ($appointment) {
            return $appointment->loyalitydetail !== null;
        });
        $title = 'Patient’s Loyalty Points';
        return view('doctor.stats.loyalitypoints', compact('appointmentsWithLoyality','title'));
    }

    public function bestpatient (Request $request)
    {
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            $doctorIds = [$authUser->id];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
            $doctorIds[] = $authUser->id;
        }
        if ($request->date_filter) {
            $dateLimit = now()->subDays($request->date_filter);
            $bestCustomers = Booking::where('status', 1)->whereIn('doctor_id',$doctorIds)
            ->where('updated_at', '>=', $dateLimit)
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'))->with('userdetail')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->get();
        } else 
        $bestCustomers = Booking::where('status', 1)
            ->whereIn('doctor_id',$doctorIds)
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'))->with('userdetail')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->get();
        $title = 'Best Patients';
        return view('doctor.stats.bestpatient', compact('bestCustomers','title'));
    }
    
    public function docactivity (Request $request)
    {
        $response = [
            'status' => true,
            'data' => [],
            'path'=>asset('content/doctor/')
        ];
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            $doctorIds = [$authUser->id];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
            $doctorIds[] = $authUser->id;
        }
        if (!empty($doctorIds)) {
            array_push($doctorIds,$authUser->id);
            $dateFilter = $request->input('date_filter2');
            $startDate = '';
            $endDate = '';
            if ($request->condition == 'lastWeek') {
                $startDate = now()->subWeek()->startOfWeek();
                $endDate = now()->subWeek()->endOfWeek();
            } elseif ($request->condition == 'lastMonth') {
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();

            } elseif ($request->start_date) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
            }
            
            $query = Booking::whereIn('doctor_id', $doctorIds)
                ->where('status', 1)
                ->selectRaw('doctor_id, COUNT(id) as total_bookings, SUM(total_amount) as total_revenue')->with('doctordetail')
                ->groupBy('doctor_id');
            if ($startDate && $endDate) {
                $query->whereBetween('schedule_date', [$startDate, $endDate]);
            }
            if (in_array($dateFilter, ['30', '60', '90', '365'])) {
                $query->where('schedule_date', '>=', now()->subDays($dateFilter));
            }
            $performance = $query->get();
            $response = [
                'status' => true,
                'data' => $performance,
                'path'=>asset('content/doctor/')
            ];
        }
        $title = 'Doctors Activity';
        return view('doctor.stats.docactivity', compact('response','title'));
    }

    public function plansold (Request $request)
    {
        $response = [
            'status' => true,
            'data' => []
        ];

        $doctorId = auth()->user()->id;

        $treatments = PackTreatment::where('user_id', $doctorId)
            ->where('user_type', 2)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        

        $dateFilter = $request->input('date_filter');

        $query = UserSubscriptionsPack::whereIn('pack_id', $treatments)
            ->selectRaw('pack_id, COUNT(user_subscriptions_pack.id) as total_sales, SUM(user_payments.amount) as total_revenue')
            ->join('user_payments', 'user_payments.id', '=', 'user_subscriptions_pack.payment_id')
            ->groupBy('pack_id');

        if (in_array($dateFilter, ['30', '60', '90', '365'])) {
            $query->where('user_subscriptions_pack.updated_at', '>=', now()->subDays($dateFilter));
        }

        $packagePerformance = $query->get();

        $packageIds = $packagePerformance->pluck('pack_id')->toArray();
        $packages = PackTreatment::whereIn('id', $packageIds)->get()->keyBy('id');

        $totalSales = 0;
        $totalRevenue = 0;

        foreach ($packagePerformance as $data) {
            if (isset($packages[$data->pack_id])) {
                $totalSales += $data->total_sales;
                $totalRevenue += $data->total_revenue;

                $response['data'][] = [
                    'pack_id'      => $data->pack_id,
                    'pack_name'    => $packages[$data->pack_id]->name,
                    'total_sales'  => $data->total_sales,
                    'total_revenue' => $data->total_revenue
                ];
            }
        }
        $title = 'Patient Plans Sold';
        return view('doctor.stats.plansold', compact('response','title'));
    }

    public function treatmentperfom (Request $request,$doctorId = 0)
    {   
        if ($doctorId == 0) {
            $doctorId = auth()->user()->id;
        }
        $response = [
            'status' => true,
            'data' => []
        ];

        

        $startDate = '';
        $endDate = '';

        if ($request->condition == 'lastWeek') {
            $startDate = now()->subWeek()->startOfWeek();
            $endDate = now()->subWeek()->endOfWeek();
        } elseif ($request->condition == 'lastMonth') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
        } elseif ($request->start_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        } 

        $query = Booking::where('doctor_id', $doctorId)
            ->where('status', 1)
            ->selectRaw('treatment_id, COUNT(id) as total_bookings')
            ->groupBy('treatment_id');

        if ($startDate && $endDate) {
            $query->whereBetween('schedule_date', [$startDate, $endDate]);
        }

        $treatmentBookings = $query->get();

        $treatmentIds = $treatmentBookings->pluck('treatment_id')->toArray();
        $treatments = Treatment::whereIn('id', $treatmentIds)->get()->keyBy('id');

        foreach ($treatmentBookings as $booking) {
            if (isset($treatments[$booking->treatment_id])) {
                $response['data'][] = [
                    'treatment_id'   => $booking->treatment_id,
                    'treatment_name' => $treatments[$booking->treatment_id]->treatment_name,
                    'total_bookings' => $booking->total_bookings
                ];
            }
        }
        $title = 'Treatments Performed';
        return view('doctor.stats.treatmentperfom', compact('response','title','doctorId'));
    }

    public function mynoti (Request $request)
    {
        $list = MyNotification::limit(333)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->whereNOTIN('status',[2])->get();
        $title = 'Notification';
        return view('doctor.stats.mynoti', compact('title','list'));
    }

    public function vgraph (Request $request,$doctorType = 'All')
    {
        if ($request->date_filter) {
           $doctorType = $request->date_filter;
        }
        $currentYear = Carbon::now()->year;
        $parentDoctorId = auth()->user()->id;
        if ($doctorType === 'All') {
            $doctorIds = User::where('parent_id', $parentDoctorId)->orWhere('id', $parentDoctorId)->pluck('id');
        } else {
            $doctorIds = User::where('id', $doctorType)->pluck('id');
        }

        $totalAppointments = Booking::whereIn('doctor_id', $doctorIds)->count();
        $onlineAppointments = Booking::whereIn('doctor_id', $doctorIds)
            ->whereIN('consultation_type', ['Online Consultation','online'])
            ->count();
        $inPersonAppointments = Booking::whereIn('doctor_id', $doctorIds)
            ->where('consultation_type', 'In-Clinic Consultation')
            ->count();

        $previousOnlineCount = Booking::whereIn('doctor_id', $doctorIds)
            ->where('consultation_type', ['Online Consultation','online'])
            ->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()])
            ->count();

        $previousInPersonCount = Booking::whereIn('doctor_id', $doctorIds)
            ->where('consultation_type', 'In-Clinic Consultation')
            ->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()])
            ->count();

        $onlineSignal = $onlineAppointments > $previousOnlineCount ? 1 : 0;
        $inPersonSignal = $inPersonAppointments > $previousInPersonCount ? 1 : 0;

        $monthlyData = Booking::selectRaw("
                MONTH(created_at) as month,
                SUM(CASE WHEN consultation_type IN ('Online Consultation', 'online') THEN 1 ELSE 0 END) as online,
                SUM(CASE WHEN consultation_type = 'In-Clinic Consultation' THEN 1 ELSE 0 END) as in_clinic
            ")
            ->whereIn('doctor_id', $doctorIds)
            ->whereYear('created_at', $currentYear) 
            ->groupBy('month')
            ->get();

        $graphDataset = array_fill(1, 12, ['online' => 0, 'in_clinic' => 0]);

        foreach ($monthlyData as $data) {
            $graphDataset[$data->month] = [
                'online' => $data->online,
                'in_clinic' => $data->in_clinic,
            ];
        }

        $res = [
            'statu'=>true,
            'total' => $totalAppointments,
            'online' => [
                'count' => $onlineAppointments,
                'signal' => $onlineSignal,
            ],
            'in_person' => [
                'count' => $inPersonAppointments,
                'signal' => $inPersonSignal,
            ],
            'graph' => $graphDataset,
        ];
        $title = 'Graph';
        $u = User::where('parent_id', auth()->user()->id)->where('status',1)->orderBy('name', 'DESC')->get();
        return view('doctor.stats.vgraph', compact('res','title','doctorType','u'));
    }

    public function memberlist(Request $request)
    {
        $userId = $request->input('user_id');
        $familyMembers = Member::where('user_id', $userId)->get(['id', 'name','last_name']);
        return response()->json($familyMembers);
    }

    public function myappointmentcond(Request $request, $condition = '')
    {
        $userId = auth()->user();
        $doctorId = auth()->user()->id;
        if ($userId->parent_id == 0) {
            $users = User::where(function ($query) use ($userId) {
                $query->where('id', $userId->id)
                    ->orWhere('parent_id', $userId->id);
            })
            ->get();
        } elseif ($userId->parent_id > 0) {
            $users = User::where('id', $userId->id)->get();
        }
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Booking for',
                'Email',
                'Mobile',
                'Type',
                'Symptoms'
                ,'Treatment'
                ,'Date & time'
                ,'Status'
                ,'Payment Status',
                'Subtotal',
                'gst',
                'partial amount',
                'total amount',
                'Created at',
            );
            $i = 1;
            $queryqb = Booking::query();
            $queryqb->orderBy('schedule_date', 'desc');
            $queryqb->whereIn('doctor_id', $users->pluck('id'));
            if (!is_null($request->name)) {
                $queryqb->whereHas('userdetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('mobile', 'LIKE', '%' . $request->name . '%');
                })->orWhereHas('doctordetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%');
                });
            }
            if (!is_null($request->status)) {
                $queryqb->where('status', $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('schedule_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('schedule_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('schedule_date', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }

                 

                 } else {
                     $a->member = null;

                     
                 }
                 $userProfile = UserProfile::find($a->user_id);
                 $doctor = User::find($a->doctor_id);
                 if ($doctor) {
                     $a->doctor = $doctor;
                     $a->user = $userProfile;
                     $treatment = DB::table('treatments')
                         ->where('id', $a->treatment_id)
                         ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                         ->first();

                     if ($treatment) {
                         $a->treatment_name = $treatment->treatment_name;
                         $a->call_before_confirmation = $treatment->call_before_confirmation;
                         $a->average_duration = $treatment->average_duration;
                     } else {
                         $a->treatment_name = null;
                         $a->call_before_confirmation = null;
                         $a->average_duration = null;
                     }
                 }
                 $status = '';
                 if($a->status == 0) {
                    $status = 'Pending';
                 }
                 if($a->status == 1) {
                    $status = 'Completed';
                 }
                 if($a->status == 2) {
                    $status = 'Canceled';
                 }
                 if($a->status == 3) {
                    $status = 'Rejected';
                 }
                 if($a->status == 4) {
                    $status = 'Accepted';
                 }
                 
                 if($a->status == 5) {
                    $status = 'Payment Failed';
                 }
                 if($a->status == 6) {
                    $status = 'Refunded';
                 }
                 $ps = '';
                if($a->is_paid==0) {
                    $ps = 'Not Paid';
                }
                if($a->is_paid==1) {
                    $ps = 'Fully Paid';

                }
                if($a->is_paid==2) {
                    $ps = 'Partially Paid';

                }
                if ($a->member) {
                    $dataexport[] = [
                        $a->member->name.' '.$a->member->last_name,
                        'Member',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                } else {
                    $dataexport[] = [
                        $a->user->name.' '.$a->user->last_name,
                        'Self',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];    
                }
                
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Appointment" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = ucfirst($condition) .' Appointments';
        $listname = 'Detail';
        $listurl = route('admin.home');
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $dateFilter = $request->date_filter ?? 365;
        $dateLimit = '';
        if ($dateFilter) {
            $dateLimit = now()->subDays($dateFilter);
        }
        
        $dateFilter = $request->date_filter ?? 0;
        if ($dateFilter) {
            $dateLimit = now()->subDays($dateFilter);
        }
        
        if ($condition == 'earning') {
            if ($dateFilter > 0) {
                $data = Booking::where('doctor_id', $doctorId)
                    ->orderBy('schedule_date', 'desc')
                    ->where('status', 1)
                    ->where('schedule_date', '>=', $dateLimit)
                    ->get();    
            } else
            $data = Booking::where('doctor_id', $doctorId)
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->get();
        }
        if ($condition == 'allbooking') {
            if ($dateFilter > 0) {
                $data = Booking::where('doctor_id', $doctorId)
                    ->orderBy('schedule_date', 'desc')
                    ->where('schedule_date', '>=', $dateLimit)
                    ->get();  
            } else
            $data = Booking::where('doctor_id', $doctorId)
            ->orderBy('schedule_date', 'desc')
            ->get();
        }
        if ($condition == 'rebooking') {
            $subquery = Booking::selectRaw('MAX(id) as id')
                ->where('doctor_id', $doctorId)
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1');

            if ($dateFilter > 0) {
                $data = Booking::whereIn('id', $subquery)
                ->where('schedule_date', '>=', $dateLimit)
                ->orderBy('schedule_date', 'desc')
                ->get();
            } else
            $data = Booking::whereIn('id', $subquery)
                ->orderBy('schedule_date', 'desc')
                ->get();
            
        }
        if ($condition == 'upcomingbooking') {
            if ($dateFilter > 0) {
                $data = Booking::where('doctor_id', $doctorId)
                ->whereIn('status', [0, 4])
                ->where('schedule_date', '>=', $dateLimit)
                ->orderBy('schedule_date', 'desc')
                ->get();
            } else
            $data = Booking::where('doctor_id', $doctorId)
            ->whereIn('status', [0, 4])
            ->orderBy('schedule_date', 'desc')
            ->get();

        }
        if ($condition == 'pendingbooking') {
            if ($dateFilter > 0) {
                $data = Booking::where('doctor_id', $doctorId)
                    ->where('status', 0)
                    ->where('schedule_date', '>=', $dateLimit)
                    ->orderBy('schedule_date', 'desc')
                    ->get();
            } else
            $data = Booking::where('doctor_id', $doctorId)
            ->where('status', 0)
            ->orderBy('schedule_date', 'desc')
            ->get();
        }


        
        if ($data) {
            $appointmentsWithDoctor = $data->map(function ($appointment) use ($userId) {
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records
                    
                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }
            
                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('content/user/' . $userProfile->image);
                }
            
                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);
            
                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();
            
                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }
            
                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();
            
                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }
    
    
                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;
                }
                return $appointment;
            });
            return view('doctor.appointments.myappointmentcond', compact('appointmentsWithDoctor', 'title', 'listurl', 'listname', 'editurl', 'data','condition'))
            ->with('i', ($request->input('page', 1) - 1) * 5);;
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function getquantity($for)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

            foreach ($subscription->features as $feature) {
                if ($feature->id == $for) {
                    $pivotId = $feature->pivot->id;
                    $addon = $premiumAddons->get($pivotId);

                    if ($alreadySubscribed->subscription_id == 9) {
                        if ($addon) {
                            $feature12Quantity = $addon->quantity;
                        } else {
                            $feature12Quantity = $feature->pivot->basic_quantity ?? 0;
                        }
                    } else {
                        $feature12Quantity = $feature->pivot->quantity ?? 0;
                    }

                    break;
                }
            }
        }
        return $feature12Quantity;
    }

    public function getquantity2($for)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

            foreach ($subscription->features as $feature) {
                if ($feature->id == $for) {
                    $pivotId = $feature->pivot->id;
                    $addon = $premiumAddons->get($pivotId);

                    if ($alreadySubscribed->subscription_id == 9) {
                        if ($addon) {
                            if ($addon->unlimited == 1) {
                              $feature12Quantity = -1;  
                            } else
                            $feature12Quantity = $addon->quantity;
                        } else {
                            $feature12Quantity = $feature->pivot->basic_quantity ?? 0;
                        }
                    } else {
                        $feature12Quantity = $feature->pivot->quantity ?? 0;
                    }

                    break;
                }
            }
        }
        return $feature12Quantity;
    }

    




    
}
