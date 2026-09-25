<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Astrologer;
use http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Exports\BookingExport;
use App\Models\CallHistory;
use App\Models\Transactions;
use App\Models\BookingFeedback;
use App\Models\BookingTry;
use App\Exports\CallHistoryExport;

use DB;
use App\Exports\TransactionHistroyExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\SdSendSms;
class OrdersManagementController extends Controller
{
    use SdSendSms;
    public function consultation($condition,Request $request)
    {
    //    print_r( $request);die();
        if ($request->exp == 'export') {
            // print_r($request->cond); die;
           
            $data[] = array(
                "OrderId","Mode","Booking Type","Type","Booking Status","Astrologer","UserName","Email","Mobile","Payable Amount",
                "PG Astro","TDS Astro ","Astrologer Comission Amount","Total Astro Comission","Astrologer Comission Perct","Price Per Mint","start_time","end_time","Added_on","Cancel_by","total_seconds","Booking Tries","Booking Tries Time","Cancelled time","Cancel Reason","Wallet Deduct Actual","Wallet Deduct Virtual"
            );
         
            $queryUser11 = Booking::query();
            if ($request->cond == 2) {
                $queryUser11->where('status',2);
                
            }
            elseif ($request->cond == 3) {
                $queryUser11->whereIN('refund_request_raised',[1,2]);
                
            }
            elseif ($request->cond == 4) {
                $queryUser11->whereIN('status',[3,4]);
                
            }
            elseif ($request->cond == 5) {
                $queryUser11->whereIN('status',[0]);
                
            }
          
            $queryUser11->orderBy('id','DESC');
    
            if(!is_null($request->name)) {
                $queryUser11->whereRaw("(type  like '%" . $request->name . "%' )");
                //$queryUser11->appends(['name' => $request->name]);
            }
    
            // if(!is_null($request->user_email)) {
            //     $queryUser11->whereRaw("(user_email like '%" . $request->user_email . "%' )");
            // }
    
            if(!is_null($request->mobile)) {

                $user_data = DB::table('users')
            ->where('mobile', '=',$request->mobile)
            ->first();

            if ( $user_data) {
                $queryUser11->where('user_id',$user_data->id);
            }
            else{
                $queryUser11->where('user_id',$request->mobile);
            }

                // $queryUser11->whereRaw("(user_phone like '%" . $request->mobile . "%' )");
            }

            if(!is_null($request->status)) {
                $queryUser11->whereRaw("(status like '%" . $request->status . "%' )");
            }
            if(!is_null($request->orderid)) {
              //  $queryUser11->whereRaw("(id like '%" . $request->orderid . "%' )");
              $queryUser11->where('id',$request->orderid);
            }
    
            if(!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryUser11->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                }
                elseif (!is_null($request->start_date)) {
                    $queryUser11->whereDate('created_at', $request->start_date);
    
                }
                elseif (!is_null($request->end_date)) {
                    $queryUser11->whereDate('created_at', $request->end_date);
                }
                
            }

            $data11 = $queryUser11->get();
            // print_r( $data11); die;
            $i = 1;
            foreach ($data11 as $user) {
                $mode = "";

                if($user->is_schedule == 1){
                    $chatfor = "Slot";
                 }
                 else{
                    $chatfor = "Free";
                 }



                if ($user->mode == 1) {
                    $mode = "Priority chat";
                } else if ($user->mode == 2) {
                    $mode = "Priority audio";
                } else if ($user->mode == 3) {
                    $mode = "Priority video";
                }  else if ($user->mode == 4) {
                    $mode = "Normal chat";
                }  else if ($user->mode == 5) {
                    $mode = "Normal audio";
                }  else if ($user->mode == 6) {
                    $mode = "Normal video";
                }  else if ($user->mode == 7) {
                    $mode = "$chatfor chat";
                }  else if ($user->mode == 8) {
                    $mode = "$chatfor audio";
                }  else if ($user->mode == 9) {
                    $mode = "$chatfor video";
                }  else if ($user->mode == 10) {
                    $mode = "Premium";
                } else if ($user->mode == 11) {
                    $mode = "Ultapremium";
                } 
                else if ($user->mode == 12) {
                    $mode = "Broadcast";
                } 
                else {
                    $mode = "-";
                }

                $type = "";
                if ($user->type == 1) {
                    $type = "Video";
                } else if ($user->type == 2) {
                    $type = "Audio";
                } else if ($user->type == 3) {
                    $type = "Chat";
                } else {
                    $type = "Chat";
                }


              
                $status = "";
                if ($user->status == 1) {
                    $status = "Confirmed";
                } else if ($user->status == 2) {
                    $status = "Completed";
                } else if ($user->status == 3) {
                    $status = "Canceled";
                }
                else if ($user->status == 6) {
                    $status = "Ongoing";
                } else {
                    $status = "Pending";
                }


                $free = "";
                if ($user->free == 1) {
                    $free = "Free";
                } 
                elseif ($user->is_schedule == 1 && $user->free == 0) {
                    $free = "Slot";
                }
                else {
                    $free = "Normal";
                }


                

                // if ($user->astrologerdetails->name) {
                //     // $name5 = json_decode($user->astrologerdetails->name, true);
                //     // if ($name5) {
                //     //     $a_name5 = $name5[1] ?? 'Astro N';
                //     // }
                //     // else{
                       
                //     // }
                //     $a_name5 =  'Astro N';
                    
                // }
                // else{
                    
                  
                // }

                $astrologer_data = DB::table('astrologers')
                ->where('id', '=',$user->astrologer_id)
                ->first();
                $name5 = json_decode($astrologer_data->name, true);
            


                if ($astrologer_data) {
                    $name5 = json_decode($astrologer_data->name, true);
                    if ($name5) {
                        $a_name5 = $name5[1] ?? 'Astro N';
                    }
                    else{
                        $a_name5 =  'Astro N';
                    }
                }
                else{
                    $a_name5 =  'Astro N';
                }


                // print_r( $name5[1]); die;

               
                // $a_name5 = 'Astro N';
                
                if (empty($user->start_time)) {
                   $start_time =  date('d-m-Y g:i a', strtotime($user->schedule_date_time));
                } else {
                    $start_time =  date('d-m-Y g:i a', strtotime($user->start_time));
                }


                if ($user->end_time) {
                    $end_time =  date('d-m-Y g:i a', strtotime($user->end_time));
                }
                else{
                    $end_time ="";
                }

                if ($user->status == 2) {
                    $total_seconds =   gmdate('i:s', $user->total_seconds); 

                } else  {
                    $total_seconds =   ""; 

                }
                if ($user->status == 3) {
                    if ($user->cancel_by == 1) {
                        $canecl_time =  date('d-m-Y g:i a', strtotime($user->updated_at));
                    }
                    else{
                        $canecl_time =  date('d-m-Y g:i a', strtotime($user->complete_date));
                    }
                   

                } else  {
                    $canecl_time = "";

                }


                $added_date = date('d-m-Y g:i a', strtotime($user->created_at));
                $cancel = "";
               if($user->status == 3)
               {
                    if ($user->cancel_by == 1) {
                        $cancel = "Admin";
                    } else if ($user->cancel_by == 3) {
                        $cancel = "Astrologer";
                    } 
                    else {
                        $cancel = "User";
                    }
               }

               $booking_tr = DB::table('booking_tries')
               ->where('booking_id', '=',$user->id)
               ->count();

               $booking_try_time = DB::table('booking_tries')
               ->where('booking_id', '=',$user->id)
               ->orderby('id', 'ASC')
               ->first();
                if ($booking_try_time) {
                    $booking_try_first_time = date('d-m-Y g:i a', strtotime($booking_try_time->created_at));
                }
                else{
                    $booking_try_first_time = "";
                }

              
                
                $data[] = array(
                    "ID" => $user->id,
                    "free" => $free,
                    "mode" => $mode,
                    "type" => $type,
                    "status" => $status,
                    "a_name5" => $a_name5,
                    "name" => $user->userdetails->name,
                    "email" => $user->userdetails->email,
                    "mobile" => $user->userdetails->mobile,
                   
                    "payable_amount" => $user->payable_amount,
                    "gst_astro" => $user->gst_astro,
                    "tds_astro" => $user->tds_astro,
                    "astrologer_comission_amount" => $user->astrologer_comission_amount,
                    "total_astro_comission" => $user->total_astro_comission,
                    "astrologer_comission_perct" => $user->astrologer_comission_perct,
                    "price_per_mint" => $user->price_per_mint,
                 
                 
                    "start_time" =>  $start_time,
                    "end_time" => $end_time ,
                    "added_on" => $added_date ,
                    "cancel_by" => $cancel,
                    "total_seconds" => $total_seconds ,
                    "booking_tries" => $booking_tr ,
                    "booking_try_first_time" => $booking_try_first_time ,
                    "canecl_time" => $canecl_time ,
                    "cancel_reason" => $user->cancel_reason,
                    "wallet_deduct_actual" => $user->wallet_deduct_actual,
                    "wallet_deduct_virtual" => $user->wallet_deduct_virtual,
                );

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Bookings" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            // print_r( $handle); die;
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        if ($request->exp == 'export_feedback') {
            // print_r($request->cond); die;
           
            $data[] = array(
                "OrderId","Mode","Booking Type","Type","Booking Status","Astrologer","UserName","Email","Mobile","Consultation Language",
                "Main Profile","Rashi","Nakshtra","Feedback On User","Applicable","Refund Amount","Enter Amount","Reason","Resolve","Created At"
            );
         
            $queryUser11 = Booking::query();
            if ($request->cond == 2) {
                $queryUser11->where('status',2);
                
            }
            elseif ($request->cond == 3) {
                $queryUser11->whereIN('refund_request_raised',[1,2]);
                
            }
            elseif ($request->cond == 4) {
                $queryUser11->whereIN('status',[3,4]);
                
            }
            elseif ($request->cond == 5) {
                $queryUser11->whereIN('status',[0]);
                
            }
          
            $queryUser11->orderBy('id','DESC');
    
            if(!is_null($request->name)) {
                $queryUser11->whereRaw("(type  like '%" . $request->name . "%' )");
                //$queryUser11->appends(['name' => $request->name]);
            }
    
            // if(!is_null($request->user_email)) {
            //     $queryUser11->whereRaw("(user_email like '%" . $request->user_email . "%' )");
            // }
    
            if(!is_null($request->mobile)) {

                $user_data = DB::table('users')
            ->where('mobile', '=',$request->mobile)
            ->first();

            if ( $user_data) {
                $queryUser11->where('user_id',$user_data->id);
            }
            else{
                $queryUser11->where('user_id',$request->mobile);
            }

                // $queryUser11->whereRaw("(user_phone like '%" . $request->mobile . "%' )");
            }

            if(!is_null($request->status)) {
                $queryUser11->whereRaw("(status like '%" . $request->status . "%' )");
            }
            if(!is_null($request->orderid)) {
              //  $queryUser11->whereRaw("(id like '%" . $request->orderid . "%' )");
              $queryUser11->where('id',$request->orderid);
            }
    
            if(!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryUser11->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
                }
                elseif (!is_null($request->start_date)) {
                    $queryUser11->whereDate('created_at', $request->start_date);
    
                }
                elseif (!is_null($request->end_date)) {
                    $queryUser11->whereDate('created_at', $request->end_date);
                }
                
            }

            $data11 = $queryUser11->get();
            // print_r( $data11); die;
            $i = 1;
            foreach ($data11 as $user) {
                $bkg = BookingFeedback::where('booking_id', $user->id)->first();
                if ($bkg) {
                        $mode = "";
                        if ($user->mode == 1) {
                            $mode = "Priority chat";
                        } else if ($user->mode == 2) {
                            $mode = "Priority audio";
                        } else if ($user->mode == 3) {
                            $mode = "Priority video";
                        }  else if ($user->mode == 4) {
                            $mode = "Normal chat";
                        }  else if ($user->mode == 5) {
                            $mode = "Normal audio";
                        }  else if ($user->mode == 6) {
                            $mode = "Normal video";
                        }  else if ($user->mode == 7) {
                            $mode = "Schedule chat chat";
                        }  else if ($user->mode == 8) {
                            $mode = "Schedule chat audio";
                        }  else if ($user->mode == 9) {
                            $mode = "Schedule chat video";
                        }  else if ($user->mode == 10) {
                            $mode = "Premium";
                        } 
                        else if ($user->mode == 11) {
                            $mode = "Ultapremium";
                        } 
                        else if ($user->mode == 12) {
                            $mode = "Broadcast";
                        } 
                        else {
                            $mode = "-";
                        }


                        $type = "";
                        if ($user->type == 1) {
                            $type = "Video";
                        } else if ($user->type == 2) {
                            $type = "Audio";
                        } else if ($user->type == 3) {
                            $type = "Chat";
                        } else {
                            $type = "Chat";
                        }


                        $status = "";
                        if ($user->status == 1) {
                            $status = "Confirmed";
                        } else if ($user->status == 2) {
                            $status = "Completed";
                        } else if ($user->status == 3) {
                            $status = "Canceled";
                        }
                        else if ($user->status == 6) {
                            $status = "Ongoing";
                        } else {
                            $status = "Pending";
                        }


                        $free = "";
                        if ($user->free == 1) {
                            $free = "Free";
                        } else {
                            $free = "Normal";
                        }
                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$user->astrologer_id)
                    ->first();
                    $name5 = json_decode($astrologer_data->name, true);
                    if ($astrologer_data) {
                        $name5 = json_decode($astrologer_data->name, true);
                        if ($name5) {
                            $a_name5 = $name5[1] ?? 'Astro N';
                        }
                        else{
                            $a_name5 =  'Astro N';
                        }
                    }
                    else{
                        $a_name5 =  'Astro N';
                    }
                    if (empty($user->start_time)) {
                       $start_time =  date('d-m-Y g:i a', strtotime($user->schedule_date_time));
                    } else {
                        $start_time =  date('d-m-Y g:i a', strtotime($user->start_time));
                    }
                    if ($user->end_time) {
                        $end_time =  date('d-m-Y g:i a', strtotime($user->end_time));
                    }
                    else{
                        $end_time ="";
                    }

                    if ($user->status == 2) {
                        $total_seconds =   gmdate('i:s', $user->total_seconds); 

                    } else  {
                        $total_seconds =   ""; 

                    }
                    if ($user->status == 3) {
                        $canecl_time =  date('d-m-Y g:i a', strtotime($user->complete_date));

                    } else  {
                        $canecl_time = "";

                    }
                    $added_date = date('d-m-Y g:i a', strtotime($user->created_at));
                    $cancel = "";
                    if($user->status == 3)
                    {
                        if ($user->cancel_by == 1) {
                            $cancel = "Admin";
                        } else if ($user->cancel_by == 3) {
                            $cancel = "Astrologer";
                        } 
                        else {
                            $cancel = "User";
                        }
                    }

                    $data[] = array(
                        "ID" => $user->id,
                        "free" => $free,
                        "mode" => $mode,
                        "type" => $type,
                        "status" => $status,
                        "a_name5" => $a_name5,
                        "name" => $user->userdetails->name,
                        "email" => $user->userdetails->email,
                        "mobile" => $user->userdetails->mobile,
                        $bkg->user_consultation_language,
                        $bkg->main_profile,
                        $bkg->rashi,
                        $bkg->nakshtra,
                        $bkg->feedback_on_user,
                        $bkg->applicable,
                        $bkg->refund_amount,
                        $bkg->enter_amount,
                        $bkg->reason,
                        $bkg->resolve,
                        $bkg->created_at,
                    );

                    $i++;
                }
            }
            $string_file = date("d-m-Y h:i:s A");
            
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"BookingsFeedback" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            // print_r( $handle); die;
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }






        $page_limit = 20;
        $queryUser = Booking::query();
        if ($condition == 2) {
            $queryUser->where('status',2);
            
        }
        elseif ($condition == 3) {
            $queryUser->whereIN('refund_request_raised',[1,2]);
            
        }
        elseif ($condition == 4) {
            $queryUser->whereIN('status',[3,4]);
            
        }
        elseif ($condition == 5) {
            $queryUser->whereIN('status',[0]);
            
        }
        // elseif ($condition == 0) {
        //     $queryUser->where('status',0);
            
        // }
        // else
        // {
        //     $queryUser->where('status',1);
            
        // }
        $queryUser->orderBy('id','DESC');
      
        // print_r( $request->user_email);die();


        if(!is_null($request->name)) {
            $queryUser->whereRaw("(type  like '%" . $request->name . "%' )");
            //$queryUser->appends(['name' => $request->name]);
        }

        // if(!is_null($request->user_email)) {
        //     $queryUser->whereRaw("(user_email like '%" . $request->user_email . "%' )");
        // }

        if(!is_null($request->mobile)) {
            $user_data = DB::table('users')
            ->where('mobile', '=',$request->mobile)
            ->first();

            if ( $user_data) {
                $queryUser->where('user_id',$user_data->id);
            }
            else{
                $queryUser->where('user_id',$request->mobile);
            }
        }


        if(!is_null($request->astrologer_mobile)) {
            $astrologers_data = DB::table('astrologers')
            ->where('mobile', '=',$request->astrologer_mobile)
            ->first();

            if ( $astrologers_data) {
                $queryUser->where('astrologer_id',$astrologers_data->id);
            }
            else{
                $queryUser->where('astrologer_id',$request->astrologer_mobile);
            }
        }


        if(!is_null($request->status)) {
            $queryUser->whereRaw("(status like '%" . $request->status . "%' )");
        }
        if(!is_null($request->orderid)) {
          //  $queryUser->whereRaw("(id like '%" . $request->orderid . "%' )");
          $queryUser->where('id',$request->orderid);
        }

        if(!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
            }
            elseif (!is_null($request->start_date)) {
                $queryUser->whereDate('created_at', $request->start_date);

            }
            elseif (!is_null($request->end_date)) {
                $queryUser->whereDate('created_at', $request->end_date);
            }
            
        }

        //Fetch list of results

        $data = $queryUser->paginate($page_limit);


        // print_r($data); die;

        if(!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if(!is_null($request->astrologer_mobile)) {
            $data->appends(['astrologer_mobile' => $request->get('astrologer_mobile')]);
        }
        if(!is_null($request->mobile)) {
            $data->appends(['mobile' => $request->get('mobile')]);
        }
        if(!is_null($request->id)) {
            $data->appends(['id' => $request->get('id')]);
        }
        if(!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if(!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        $title = 'Booking List';
        $exporturl = 'admin.order.consultation';
        $cond = $condition;
        return view('admin.order.consultation',compact('title','data','exporturl','cond'));
    }

    public function updateOrder($id,$what)
    {
        $a = Booking::find($id);
        $a->status = $what;
        $a->cancel_by = 1;
        $a->save();
        if ($what == 3 && $a->is_schedule == 1) {
            $this->checkrefund($id);
        }
        return redirect()->back()
                        ->with('success','Status update successfully');
    }


    public function reset_call($id)
    {
        $a = Booking::find($id);
        $a->call_initaite   = 0;
        $a->save();
        return redirect()->back()
                        ->with('success','Reset Call update successfully');
    }



    public function refundRequestSolved($id)
    {
        $a = Booking::find($id);
        $a->refund_request_raised = 2;
        $a->save();
        return redirect()->back()
                        ->with('success','Status update successfully');
    }


    public function chatlog($id)
    {
        $title = 'Call History List';
        $id = $id;
        $cond = 1;
        $data = 1;
        $a = Booking::find($id);
        $user_name = $a->user_name;
        $user_id = $a->user_id;
        $assign_id = $a->astrologer_id;
        $user_data = User::find($user_id);
        $assign_data = Astrologer::find($assign_id);
        $astrologer_name = $assign_data->name;
        $user_image = $user_data->image;

        // dd($astrologer_name);
        return view('admin.order.chatlog',compact('title','data','id','cond','astrologer_name','user_image','user_name','assign_id'));
    }




    public function callhistory(Request $request)
    {
        if ($request->exp == 'export') {
            return Excel::download(new CallHistoryExport($request,1), 'callhistoryList'.date('Y-m-d-h:i:s').'.xlsx');
        }
        $page_limit = 20;
        // $queryUser = CallHistory::query();
        // $queryUser->orderBy('added_on','DESC');
        // if(!is_null($request->name)) {
        //     // $queryUser->whereRaw("(user_phone like '%" . $request->name . "%' ) OR (user_name like '%" . $request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $request->name . "%' )");
        //     //$queryUser->appends(['name' => $request->name]);
        // }

        // if(!is_null($request->start_date) || !is_null($request->end_date)) {
        //     if (!is_null($request->start_date) && !is_null($request->end_date)) {
        //         $queryUser->whereBetween('added_on', [$request->start_date, $request->end_date]);
        //     }
        //     elseif (!is_null($request->start_date)) {
        //         $queryUser->whereDate('added_on', $request->start_date);

        //     }
        //     elseif (!is_null($request->end_date)) {
        //         $queryUser->whereDate('added_on', $request->end_date);
        //     }
            
        // }

        $page_limit = 20;
        $queryUser = Booking::query();
        
        $queryUser->whereIN('status',[2]);
        $queryUser->whereIN('type',[2]);
       
        $queryUser->orderBy('id','DESC');
       

        //Fetch list of results

        $data = $queryUser->paginate($page_limit);



        //Fetch list of results

        // $data = $queryUser->paginate($page_limit);
        if(!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if(!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if(!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        $title = 'Call History List';
        $exporturl = 'admin.order.callhistory';
        $cond = 1;
        return view('admin.order.callhistory',compact('title','data','exporturl','cond'));
    }





    
    public function transactionhistory(Request $request)
    {
       // print_r($request->name);die();
        $userdata = User::get();
        if ($request->exp == 'export') {




           
            $data[] = array(
                "id", "Txn Name","Name","Phone","Wallet","Booking Txn Id","Payment Mode","Status","Txn For","Type","Old Wallet","Txn Amount","Update Wallet","Payment","Created At"
            );

            $queryUser11 = Transactions::query();
            $queryUser11->select('transactions.*');
            $queryUser11->orderBy('transactions.id','DESC');
            $queryUser11->whereNOTIN('transactions.id',[0]);
            // $queryUser11->join('user', 'user.id', '=', 'transactions.user_id');
            if(!empty($_GET['name'])) {
                $queryUser11->where('user_id',$_GET['name']);
               // $queryUser11->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
            }
    
            if(!empty($_GET['user_mobile'])) {

                $user_data = DB::table('users')
                ->where('mobile', '=',$request->user_mobile)
                ->first();
    
                if ( $user_data) {
                    $queryUser11->where('user_id',$user_data->id);
                }
                else{
                    $queryUser11->where('user_id',$request->user_mobile);
                }


                // $queryUser11->where('user_id',$_GET['user_mobile']);
               // $queryUser11->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
            }
    
    
            if(!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryUser11->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
    
                 
                }
                elseif (!is_null($request->start_date)) {
                    $queryUser11->whereDate('created_at', $request->start_date);
    
                }
                elseif (!is_null($request->end_date)) {
                    $queryUser11->whereDate('created_at', $request->end_date);
                }
                
            }

         
           
            $data11 = $queryUser11->get();
         
            // print_r( $data11); die;
            $i = 1;
            foreach ($data11 as $a) {
            

                if ($a->status == 1) {
                    $status = "Success";
                }
                else {
                    $status = "Failed";
                }


                
                $data[] = array(
                    "ID" => $a->id,
                    "txn_name" => $a->txn_name,
                    "name" => $a->user->name ?? '',
                    // "email" => $a->user->email ?? '',
                    "phone" => $a->user->mobile ?? '',
                    // "gender" => $a->user->gender ?? '',
                    "wallet" => $a->user->wallet ?? '',
                    "booking_txn_id" => $a->booking_txn_id ,
                    "payment_mode" => $a->payment_mode ,
                    "status" => $status ,
                    "txn_for" => $a->txn_for ,
                    "type" => $a->type ,
                    "old_wallet" => $a->old_wallet ,
                    "txn_amount" => $a->txn_amount ,
                  
                    "update_wallet" => $a->update_wallet ,
                    "payment " => $a->payment ,


                    "created_at" => $a->created_at
                   
                );

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"transactionhistory" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            // print_r( $handle); die;
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }


      
        $page_limit = 20;
        $queryUser = Transactions::query();
        $queryUser->select('transactions.*');
        $queryUser->orderBy('transactions.id','DESC');
        $queryUser->whereNOTIN('transactions.id',[0]);
        // $queryUser->join('user', 'user.id', '=', 'transactions.user_id');
        if(!is_null($request->name)) {
            $queryUser->where('user_id',$request->name);
           // $queryUser->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
        }
        if(!is_null($request->user_mobile)) {


            $user_data = DB::table('users')
            ->where('mobile', '=',$request->user_mobile)
            ->first();

            if ( $user_data) {
                $queryUser->where('user_id',$user_data->id);
            }
            else{
                $queryUser->where('user_id',$request->user_mobile);
            }
           
           // $queryUser->whereRaw("(user_phone like '%" . $this->request->name . "%' ) OR (user_name like '%" . $this->request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $this->request->name . "%' )");
        }
       // $queryUser = Transaction::query();
       // $queryUser->orderBy('created_at','DESC');
       // if(!is_null($request->name)) {
        //     $queryUser->whereRaw("(user_phone like '%" . $request->name . "%' ) OR (user_name like '%" . $request->name . "%' ) OR (user_email like '%" . $request->name . "%' ) OR (id like '%" . $request->name . "%' )");
        //    $queryUser->appends(['name' => $request->name]);
        //}

        if(!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser->whereBetween('created_at', [$request->start_date.' 00:00:00', $request->end_date.' 23:59:59']);
            }
            elseif (!is_null($request->start_date)) {
                $queryUser->whereDate('created_at', $request->start_date);

            }
            elseif (!is_null($request->end_date)) {
                $queryUser->whereDate('created_at', $request->end_date);
            }
            
        }

        //Fetch list of results

        $data = $queryUser->paginate($page_limit);
        if(!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if(!is_null($request->user_mobile)) {
            $data->appends(['user_mobile' => $request->get('user_mobile')]);
        }
        if(!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if(!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        $title = 'Transaction History List';
        $exporturl = 'admin.order.transactionhistory';
        $cond = 1;
        return view('admin.order.transactionhistory',compact('title','data','exporturl','cond','userdata'));
    }





    
    public function export_data(Request $request)
    {

        $title = 'Earning All Astrolger';
        $exporturl = 'admin.order.export_data';
        $dd = 1;
        $userdata = Astrologer::get();
        $input = $request->all();
  
        if ($request->exp == 'export') {
            $data[] = array("SNO","Astrologer ID","Astrolger Name","Astrolger Real Name","Country Code","Mobile","Email","City","Address","Country","Approve",
            "Created At","Total Booking","Astrologer Comission Perct","Booking Amount","Admin Earnings","Astrologer Comission Amount","Total Pay Payouts","Total Pending Payouts","Total Tds Astro","Total PG Astro");
            // print_r($_GET); die;
            if(isset($_GET['start_date']) && !empty($_GET['end_date']) && empty($_GET['astrologer_name'])){
                // print_r("1"); die;
                $s_date = $_GET['start_date'];
                $e_date = $_GET['end_date'];
                $acounts  = DB::table('bookings')
                ->groupBy('assign_id')
                ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                ->where('status', '=', 2)
                ->get();
            }
            elseif (!empty($_GET['start_date']) && !empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {
                // print_r("2"); die;
                $s_date = $_GET['start_date'];
                $e_date = $_GET['end_date'];
                $astrologer_name = $_GET['astrologer_name'];
                $acounts  = DB::table('bookings')
                ->groupBy('assign_id')
                ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                ->where('status', '=', 2)
                ->where('assign_id', '=',  $astrologer_name )
                ->get();
            }

            elseif (empty($_GET['start_date']) && empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {
                // print_r("3"); die;
                $astrologer_name = $_GET['astrologer_name'];
                $acounts  = DB::table('bookings')
                ->groupBy('assign_id')
                ->where('status', '=', 2)
                ->where('assign_id', '=',  $astrologer_name )
                ->get();
            }
            else{
                // print_r("eee"); die;
                $acounts  = DB::table('bookings')
                ->groupBy('assign_id')
                ->where('status', '=', 2)
                ->get();
            }

              $i = 1;
              foreach ($acounts as $item) {
                if(!empty($_GET['start_date']) && !empty($_GET['end_date']) && empty($_GET['astrologer_name'])){
                    $s_date = $_GET['start_date'];
                    $e_date = $_GET['end_date'];
                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$item->assign_id)
                    ->first();

                    $booking_amount = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('payable_amount');
        
                    $astrologer_comission_amount = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 0)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_booking = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->count();

                    $total_pay_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');

                    $total_tds_astro = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');

    
    
                }
                elseif (!empty($_GET['start_date']) && !empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {

                    $s_date = $_GET['start_date'];
                    $e_date = $_GET['end_date'];
                    $astrologer_name = $_GET['astrologer_name'];

                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$astrologer_name)
                    ->first();
                    
                    $booking_amount = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('payable_amount');
        
                    $astrologer_comission_amount = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 0)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_booking = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->count();


                    $total_pay_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');

                    $total_tds_astro = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');

    
                  
                }

                elseif (empty($_GET['start_date']) && empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {

                //  print_r("ddd"); die;
                    $astrologer_name = $_GET['astrologer_name'];

                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$astrologer_name)
                    ->first();
                    
                    $booking_amount = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->sum('payable_amount');
        
                    $astrologer_comission_amount = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 0)
                    ->sum('astrologer_comission_amount');
        
                    $total_booking = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->count();


                    $total_pay_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');

                    $total_tds_astro = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('bookings')
                    ->where('assign_id', '=',$astrologer_name)
                    ->where('status', '=', 2)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');
                  
                }


                else{
    
                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$item->assign_id)
                    ->first();
    
                    $booking_amount = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->sum('payable_amount');
        
                    $astrologer_comission_amount = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 0)
                    ->sum('astrologer_comission_amount');

                    $total_pay_payouts = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');
        
        
                    $total_tds_astro = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->where('status', '=', 2)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');
        
                    $total_booking = DB::table('bookings')
                    ->where('assign_id', '=',$item->assign_id)
                    ->count();
        
    
                }
    

                  $data[] = array(
                    "sno"=>$i,
                 "id"=>$astrologer_data->id,
                 "name"=>$astrologer_data->name,
                 "real_name"=>$astrologer_data->real_name,
                 "country_code"=>$astrologer_data->country_code,
                 "phone"=>$astrologer_data->phone,
                 "email"=>$astrologer_data->email,
                 "city"=>$astrologer_data->city,
                 "address"=>$astrologer_data->address,
                 "country"=>$astrologer_data->country,
                 "is_approval"=>$astrologer_data->is_approval,
                 "created_at"=>date('d M y g:i A',strtotime($astrologer_data->created_at)),
                 "total_booking"=>$total_booking,
                 "astrologer_comission_perct"=>$item->astrologer_comission_perct,
                 "booking_amount"=>$booking_amount,
                 "admin_earnings"=>sprintf('%0.2f', $booking_amount - $astrologer_comission_amount) ,
                 
                 "astrologer_comission_amount"=>sprintf('%0.2f',$astrologer_comission_amount),
                 "total_pay_payouts"=>$total_pay_payouts,
                 "total_pending_payouts"=>$total_pending_payouts,
                 "total_tds_astro"=>$total_tds_astro,
                 "total_gst_astro"=>$total_gst_astro,

                  );
                  $i++;
              }

                  $string_file = date("d-m-Y h:i:s A");

              header("Content-type: application/csv");
              header("Content-Disposition: attachment; filename=\"Booking_Earning " . $string_file . ".csv");
              header("Pragma: no-cache");
              header("Expires: 0");
              $handle = fopen('php://output', 'w');
              foreach ($data as $data) {
                  fputcsv($handle, $data);
              }
              fclose($handle);
              exit;
        }



        if ($request->exp_gift == 'export_gift') {
            $data[] = array("SNO","Astrologer ID","Astrolger Name","Astrolger Real Name","Country Code","Mobile","Email","City","Address","Country","Approve",
            "Created At","Total Gift","Astrologer Comission Perct","Admin Earnings","Gift Amount","Astrologer Comission Amount","Total Pay Payouts","Total Pending Payouts","Total Tds Astro","Total PG Astro");
            // print_r($_GET); die;
            if(!empty($_GET['start_date']) && !empty($_GET['end_date']) && empty($_GET['astrologer_name']) ){
                $s_date = $_GET['start_date'];
                $e_date = $_GET['end_date'];

                
                $acounts_gift  = DB::table('send_gifts')
                ->groupBy('astrologer_id')
                ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                ->get();
            }
            elseif (!empty($_GET['start_date']) && !empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {
                $s_date = $_GET['start_date'];
                $e_date = $_GET['end_date'];
                $astrologer_name = $_GET['astrologer_name'];

              


                $acounts_gift  = DB::table('send_gifts')
                ->groupBy('astrologer_id')
                ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                // ->where('status', '=', 2)
                ->where('astrologer_id', '=',  $astrologer_name )
                ->get();
            }

            elseif (empty($_GET['start_date']) && empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {
                $astrologer_name = $_GET['astrologer_name'];

              
                $acounts_gift  = DB::table('send_gifts')
                ->groupBy('astrologer_id')
                ->where('astrologer_id', '=',  $astrologer_name )
                ->get();
            }
            else{

                
                $acounts_gift  = DB::table('send_gifts')
                ->groupBy('astrologer_id')
                ->get();


            }

              $i = 1;
              foreach ($acounts_gift as $item) {
                if(!empty($_GET['start_date']) && !empty($_GET['end_date']) && empty($_GET['astrologer_name']) ){
                    $s_date = $_GET['start_date'];
                    $e_date = $_GET['end_date'];


                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$item->astrologer_id)
                    ->first();

             



                    $booking_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('price');
        
                    $astrologer_comission_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->where('is_comission_paid', '=', 0)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_booking = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->count();

                    $total_pay_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');

                    $total_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');

    
    
                }
                elseif (!empty($_GET['start_date']) && !empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {

                    $s_date = $_GET['start_date'];
                    $e_date = $_GET['end_date'];
                    $astrologer_name = $_GET['astrologer_name'];

                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$astrologer_name)
                    ->first();
                    
                    $booking_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('price');
        
                    $astrologer_comission_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                  
                    ->where('is_comission_paid', '=', 0)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->sum('astrologer_comission_amount');
        
                    $total_booking = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    ->count();


                    $total_pay_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');

                    $total_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');

    
                  
                }

                elseif (empty($_GET['start_date']) && empty($_GET['end_date']) && !empty($_GET['astrologer_name'])) {

                //  print_r("ddd"); die;
                    $astrologer_name = $_GET['astrologer_name'];

                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$astrologer_name)
                    ->first();
                    
                    $booking_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->sum('price');
        
                    $astrologer_comission_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->where('is_comission_paid', '=', 0)
                    ->sum('astrologer_comission_amount');
        
                    $total_booking = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->count();


                    $total_pay_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');

                    $total_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$astrologer_name)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');
                  
                }


                else{
    
                    $astrologer_data = DB::table('astrologers')
                    ->where('id', '=',$item->astrologer_id)
                    ->first();
    
                    $booking_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->sum('price');
        
                    $astrologer_comission_amount = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->sum('astrologer_comission_amount');
        
                    $total_pending_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->where('is_comission_paid', '=', 0)
                    ->sum('astrologer_comission_amount');

                    $total_pay_payouts = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->where('is_comission_paid', '=', 1)
                    ->sum('astrologer_comission_amount');
        
        
                    $total_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('tds_astro');
        
                    $total_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    // ->where('is_comission_paid', '=', 0)
                    ->sum('gst_astro');
        
                    $total_booking = DB::table('send_gifts')
                    ->where('astrologer_id', '=',$item->astrologer_id)
                    ->count();
        
    
                }
    
               

                  $data[] = array(
                    "sno"=>$i,
                 "id"=>$astrologer_data->id,
                 "name"=>$astrologer_data->name,
                 "real_name"=>$astrologer_data->real_name,
                 "country_code"=>$astrologer_data->country_code,
                 "phone"=>$astrologer_data->phone,
                 "email"=>$astrologer_data->email,
                 "city"=>$astrologer_data->city,
                 "address"=>$astrologer_data->address,
                 "country"=>$astrologer_data->country,
                 "is_approval"=>$astrologer_data->is_approval,
                 "created_at"=>date('d M y g:i A',strtotime($astrologer_data->created_at)),
                 "total_booking"=>$total_booking,
                 "astrologer_comission_perct"=>$item->astrologer_comission_perct,
                 "admin_earnings"=>sprintf('%0.2f', $booking_amount - $astrologer_comission_amount) ,
                 "booking_amount"=>$booking_amount,
                 "astrologer_comission_amount"=>$astrologer_comission_amount,
                 "total_pay_payouts"=>$total_pay_payouts,
                 "total_pending_payouts"=>$total_pending_payouts,
                 "total_tds_astro"=>$total_tds_astro,
                 "total_gst_astro"=>$total_gst_astro,

                  );
                  $i++;
              }

                  $string_file = date("d-m-Y h:i:s A");

              header("Content-type: application/csv");
              header("Content-Disposition: attachment; filename=\"Gift_Earning" . $string_file . ".csv");
              header("Pragma: no-cache");
              header("Expires: 0");
              $handle = fopen('php://output', 'w');
              foreach ($data as $data) {
                  fputcsv($handle, $data);
              }
              fclose($handle);
              exit;
        }



        return view('admin.order.export_data',compact('title','exporturl','userdata'));

        
    }

    public function checkrefund($id)
    {
        $booking = Booking::where('id', $id)
            ->where('is_schedule', 1)->where('refund_flag', 0)
            ->first();
        if ($booking) {
            $checktry = BookingTry::where('booking_id',$booking->id)->first();
            $interval1 = 0; 
            $name = json_decode($booking->astrologerdetails->screen_name,true);
            $pname = $name[1] ?? 'Astro N';
            if ($checktry) {
                $interval1 = 2.5; 
                $title = 'Slot booking cancelled';
                $msg = 'You have missed slot booking with Pandit '.$pname.', you incur cancellation charges as per our terms & conditions. Rest of the amount will be credited to your wallet';
            }
            else {
                $interval1 = 100; 
                $title = 'Slot booking cancelled';
                $msg = 'Due to emergency Pandit '.$pname.' has not attended your slot booking. Kindly book with another available Pandit. Sorry for the inconvenience caused. Booking amount credited to wallet.';
            }
            if ($interval1 > 0) {
                $bookingm = Booking::where('id',$booking->id)->first();
                $refund = $this->refundprocess($bookingm,$interval1,$title,$msg);
            }
        }
    }

    public function refundprocess($b,$percent,$title='',$msg='')
    {
        $refund = 0;
        $wallet_deduct_actual = $b->wallet_deduct_actual;
        $wallet_deduct_virtual = $b->wallet_deduct_virtual;
        $totalAmount = $wallet_deduct_actual+$wallet_deduct_virtual;
        if ($percent == 100) {
            $cancellationFee  = 0;
        }
        else {
            $cancellationFee  = $totalAmount * ($percent/100);
        }
        $refundAmount = $totalAmount - $cancellationFee;
        $totalpricerefund = $refundAmount;
        $get_user_detail = User::where('id',$b->user_id)->first();
        $user_actual_wallet = $get_user_detail->wallet;
        $user_virtual_wallet = $get_user_detail->virtual_wallet;
        $udpate_user_actual_wallet = 0;
        $udpate_user_virtual_wallet = 0;
        $deduct_actual_wallet = 0;
        $deduct_virtual_wallet = 0;
        if ($wallet_deduct_actual == 0 && $wallet_deduct_virtual > 0) {
            $udpate_user_virtual_wallet = $user_virtual_wallet + $refundAmount;
            $udpate_user_actual_wallet = $user_actual_wallet;
        }
        elseif ($wallet_deduct_actual > 0 && $wallet_deduct_virtual > 0) {
            $refundAmount = $wallet_deduct_actual - $cancellationFee;
            $udpate_user_actual_wallet = $user_actual_wallet + $refundAmount;
            $udpate_user_virtual_wallet = $user_virtual_wallet + $wallet_deduct_virtual;
        }
        elseif ($wallet_deduct_actual > 0 && $wallet_deduct_virtual == 0) {
            $refundAmount = $wallet_deduct_actual - $cancellationFee;
            $udpate_user_actual_wallet = $user_actual_wallet + $refundAmount;
            $udpate_user_virtual_wallet = $user_virtual_wallet;
        }
        // echo '<br>user_actual_wallet:'.$user_actual_wallet;
        // echo '<br>user_virtual_wallet:'.$user_virtual_wallet;
        // echo '<br>udpate_user_actual_wallet:'.$udpate_user_actual_wallet;
        // echo '<br>udpate_user_virtual_wallet:'.$udpate_user_virtual_wallet;
        $mode = 'Booking';
        if ($b->type == 1) {
            $mode = 'Video';
        }
        elseif ($b->type == 2) {
            $mode = 'Audio';
        }
        elseif ($b->type == 3) {
            $mode = 'Chat';
        }
        $old_wallet =  $user_actual_wallet+$user_virtual_wallet;
        $update_wallet = $udpate_user_actual_wallet;
        if ($update_wallet < 0) 
        {
            $update_wallet = 0;
        }
        $virtualwallet = $udpate_user_virtual_wallet;
        if ($virtualwallet < 0) 
        {
            $virtualwallet = 0;
        }
        $checkwallethaveanyadd = Transactions::where("user_id",$b->user_id)->where("booking_id",$b->id)->where("type","credit")->where('txn_mode','refund')->first();
        if ($checkwallethaveanyadd) 
        {
            
        }
        else
        {
            $t = new Transactions();
            $t->user_id=$get_user_detail->id;
            $t->name=$get_user_detail->name;
            $t->type="credit";
            $t->txn_name="Amount refund for Slot ".$mode." Service";
            $t->booking_id=$b->id;
            $t->booking_txn_id='RFND'.rand().time();
            $t->payment_mode="wallet";
            $t->txn_for="booking";
            $t->old_wallet=$old_wallet;
            $t->txn_amount=$totalpricerefund;
            $t->update_wallet=$update_wallet+$virtualwallet;
            $t->actual_amount = $deduct_actual_wallet;
            $t->virtual_amount = $deduct_virtual_wallet;
            $t->old_wallet_actual = $user_actual_wallet;
            $t->update_wallet_actual = $udpate_user_actual_wallet;
            $t->old_virtual_actual = $user_virtual_wallet;
            $t->update_virtual_actual = $udpate_user_virtual_wallet;
            $t->status=1;
            $t->txn_mode='refund';
            $t->bank_name='';
            $t->bank_txn_id='';
            $t->ifsc='';
            $t->account='';
            $t->save();
            if ($t) {
                $get_user_detail->wallet = $update_wallet;
                $get_user_detail->virtual_wallet = $virtualwallet;
                $get_user_detail->save();
                $b->refund_flag = 1;
                $b->cancel_reason = 'admin';
                $b->complete_date = date('Y-m-d H:i:s');
                $b->cancel_by_id = 0;
                $b->cancel_by = 'admin';
                $b->refund_cancelation_percent = $percent;
                $b->save();
                $refund = $percent;
                $this->async_to_all($title,$msg,$b->id,'bookingrefunded');
                $title1 = 'Booking cancelled';
                $msg1 = 'Your slot booking was auto cancelled.';
                $this->async_to_all($title1,$msg1,$b->id,'bookingrefundedastrologer');
            }
        }
        return $refund;
    }




    


}
