<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EcomCancelReason;
use App\Models\EcomCancelRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use App\Models\Generalsetting;
use App\Models\OrderAddress;
use App\Models\OrderStatusRecord;
use App\Models\Book;
use App\Models\Uniform;
use App\Models\UniformSize;
use App\Models\Notebook;
use App\Models\NotebookPack;
use App\Models\AstrologerTransaction;
use Illuminate\Http\Request;
use Auth;
use DB;
use File;
use PDF;
use Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{


    function index(Request $request)
    {

        if ($request->exp == 'export') {
            $dataexport[] = array(
                'order_no',
                // 'Refrence',
                // 'Invoice URL',
                'UserName',
                'Email',
                'Mobile',
                // 'Usertype',
                ' Address',
                // 'Shiiping Address',
                'Payment Mode',
                'Currency',
                'Total Qty',
                'Sub Total',
                'Coupon No.',
                'Coupon Discount',
                'Tax Amount',
                'Delivery Amount',
                'Net Amount',
                'Order Date and Time',
                'Order Status',
                'Order Accepted Datetime',
                'Order Dispatched Datetime',
                'Order Delivered Datetime',
                'Order Cancelled Datetime',
                'Tracking Id',
                'Tracking Link',
                // 'Product Type',
                'Product Name',
                'Product Variant',
                'Unit Price',
                'Quantity',
                'Product Net Amount',
                'Tax Rate',
                'Tax Type',
                'Tax Amount',
                'Total'
            );
            $i = 1;
            $queryqb = Order::query();
            $queryqb->select('orders.*');
            $queryqb->orderBy('orders.updated_at', 'DESC');
            $queryqb->whereNOTIN('orders.id', [0]);
            if (!is_null($request->order_no)) {
                $queryqb->whereRaw("(('orders.order_no' like '%" . $request->order_no . "%') OR ('orders.refnofull' like '%" . $request->order_no . "%') OR ('orders.total_mrp' like '%" . $request->order_no . "%'))");
            }
            if (!is_null($request->status)) {
                $queryqb->where("delivery_status", $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('orders.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('orders.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('orders.created_at', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                $user =  DB::table('users')->where('id', $a->user_id)->first();
                $address1 = OrderAddress::where('order_id', $a->id)->where('address_type', 1)->first();
                $address2 = OrderAddress::where('order_id', $a->id)->where('address_type', 1)->first();
                $refrence = '';
                $invoice = '';
                $usertype = '';
                $billingaddress = '';
                $shiipingaddress = '';
                if ($user->user_type == 1) {
                    $usertype = 'Student';
                } elseif ($user->user_type == 2) {
                    $usertype = 'Teacher';
                } elseif ($user->user_type == 3) {
                    $usertype = 'Sales User';
                }
                // if (!is_null($a->invoice)) {
                $refrence = $a->refnofull;
                $invoice = $a->refnofull;
                // }
                if ($address1) {
                    $billingaddress = ucfirst($address1->name) . ', ' . $address1->address . ', ' . $address1->locality . ', ' . $address1->city . ', ' . $address1->pincode . ', ' . $address1->state . ', ' . $address1->country . ', ' . $address1->type . ', ' . $address1->mobile;
                }
                if ($address2) {
                    $shiipingaddress = ucfirst($address2->name) . ', ' . $address2->address . ', ' . $address2->locality . ', ' . $address2->city . ', ' . $address2->pincode . ', ' . $address2->state . ', ' . $address2->country . ', ' . $address2->type . ', ' . $address2->mobile;
                }
                $status = '';
                if ($a->delivery_status == 0) {
                    $status = 'Pending';
                } elseif ($a->delivery_status == 1) {
                    $status = 'Accepted';
                } elseif ($a->delivery_status == 2) {
                    $status = 'Dispatched';
                } elseif ($a->delivery_status == 3) {
                    $status = 'Delivered';
                } elseif ($a->delivery_status == 4) {
                    $status = 'Cancelled';
                }

                $order_accepted_date = '';
                if (!is_null($a->order_accepted_date)) {
                    $order_accepted_date = $a->order_accepted_date;
                }
                $order_dispatch_date = '';
                if (!is_null($a->order_on_the_way_date)) {
                    $order_dispatch_date = $a->order_on_the_way_date;
                }
                $order_delivered_date = '';
                if (!is_null($a->order_delivered_date)) {
                    $order_delivered_date = $a->order_delivered_date;
                }
                $order_cancelled_date  = '';
                if (!is_null($a->order_cancelled_date)) {
                    $order_cancelled_date  = $a->order_cancelled_date;
                }
                $dataexport[] = [
                    $a->order_no,
                    // $refrence,
                    // $invoice,
                    $user->name,
                    $user->email,
                    $user->mobile,
                    // $usertype,
                    $billingaddress,
                    // $shiipingaddress,
                    $a->payment_mode,
                    $a->currency,
                    $a->total_qty,
                    $a->total_mrp,
                    $a->coupon_no,
                    $a->coupon_discount,
                    $a->tax_amt,
                    $a->delivery_price,
                    $a->net_amount,
                    date('d-M-Y h:ia', strtotime($a->created_at)),
                    $status,
                    $order_accepted_date,
                    $order_dispatch_date,
                    $order_delivered_date,
                    $order_cancelled_date,
                    $a->tracking_id,
                    $a->tracking_link,
                ];

                $oop = OrderProduct::where('order_id', $a->id)->orderBy('id', 'ASC')->get();;
                if ($oop->count() > 0) {
                    foreach ($oop as $us) {
                        $taxrate1 = '0%';
                        $taxrate2 = '0%';
                        $taxtype1 = 'None';
                        $taxtype2 = 'None';
                        if ($address2->state == 'Uttar Pradesh' || $address2->state == 'Uttar Pradesh') {
                            $txrate = round($us->tax_percentage / 2);
                            $taxrate1 = $txrate . '%';
                            $taxrate2 = $txrate . '%';
                            $taxtype1 = 'CGST';
                            $taxtype2 = 'SGST';
                        } else {
                            $taxrate1 = $us->tax_percentage . '%';
                            $taxrate2 = '';
                            $taxtype1 = 'IGST';
                            $taxtype2 = '';
                        }
                        $prodtype = '';
                        if ($us->type == 1) {
                            $prodtype = 'Book';
                            if ($us->book_online_support) {
                                $variant = 'Ebook';
                            } else {
                                $variant = 'Physical Book';
                            }
                        } elseif ($us->type == 2) {
                            $prodtype = 'Uniform';
                            $variant = $us->variant;
                        } elseif ($us->type == 3) {
                            $prodtype = 'Notebook';
                            $variant = $us->variant;
                        } elseif ($us->type == 4) {
                            $prodtype = 'Course';
                        }

                        $name5 = json_decode($us->product_name, true);
                        $a_name5 = $name5[1] ?? 'Astro N';
                        // echo  $a_name5 ; 



                        $dataexport[] = [
                            $a->order_no,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            // $prodtype,
                            $a_name5,
                            // $variant,
                            number_format((float)$us->price, 2, '.', ''),
                            $us->qty,
                            number_format((float)($us->total_price - $us->total_tax), 2, '.', ''),
                            $taxrate1 . ',' . $taxrate2,
                            $taxtype1 . ',' . $taxtype2,
                            $us->total_tax,
                            $us->total_price
                        ];
                    }
                }
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Orders" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = "Orders";
        $addbutton = "";
        $queryqb = Order::query();
        $queryqb->join('order_addresses', 'orders.id', '=', 'order_addresses.order_id');
        if (!is_null($request->status)) {
            $queryqb->join('order_products', 'orders.id', '=', 'order_products.order_id');
        }
        $queryqb->select('orders.*');
        $queryqb->orderBy('orders.id', 'DESC');
        $queryqb->whereNotIn('orders.id', [0]);

        if (!is_null($request->order_no)) {
            $orderNo = $request->order_no;
            $queryqb->where(function ($query) use ($orderNo) {
                $query->where('orders.order_no', 'like', "%$orderNo%")
                    ->orWhere('orders.refnofull', 'like', "%$orderNo%")
                    //   ->orWhere('order_addresses.mobile', 'like', "%$orderNo%")
                    ->orWhere('orders.net_amount', 'like', "%$orderNo%");
            });
        }
        // print_r($request->status); die;
        if (!is_null($request->order_mobile)) {
            $queryqb->where('order_addresses.mobile', $request->order_mobile);
        }

        if (!is_null($request->status)) {
            $queryqb->where('order_products.delivery_status', $request->status);
        }

        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('orders.created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('orders.created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('orders.created_at', $request->end_date);
            }
        }

        $orders = $queryqb->paginate(10);
        // $orders = $queryqb->toSql();
        // print_r( $orders); die;


        if (!is_null($request->order_no)) {
            $orders->appends(['order_no' => $request->get('order_no')]);
        }
        if (!is_null($request->start_date)) {
            $orders->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $orders->appends(['end_date' => $request->get('end_date')]);
        }
        if (!is_null($request->status)) {
            $orders->appends(['status' => $request->get('status')]);
        }
        $exporturl = 'admin.astro-product.orders_list.index';
        return view('admin.ecom_order.index', compact('orders', 'title', 'addbutton', 'exporturl'));
    }
    function orders_vendor(Request $request)
    {

        if ($request->exp == 'export') {
            $dataexport[] = array(
                'order_no',
                'Refrence',
                'Invoice URL',
                'UserName',
                'Email',
                'Mobile',
                'Usertype',
                'Billing Address',
                'Shiiping Address',
                'Payment Mode',
                'Currency',
                'Total Qty',
                'Sub Total',
                'Coupon No.',
                'Coupon Discount',
                'Tax Amount',
                'Delivery Amount',
                'Net Amount',
                'Order Date and Time',
                'Order Status',
                'Order Accepted Datetime',
                'Order Dispatched Datetime',
                'Order Delivered Datetime',
                'Order Cancelled Datetime',
                'Tracking Id',
                'Tracking Link',
                'Product Type',
                'Product Name',
                'Product Variant',
                'Unit Price',
                'Quantity',
                'Product Net Amount',
                'Tax Rate',
                'Tax Type',
                'Tax Amount',
                'Total'
            );
            $i = 1;
            $queryqb = Order::query();
            $queryqb->select('orders.*');
            $queryqb->orderBy('orders.updated_at', 'DESC');
            $queryqb->whereNOTIN('orders.id', [0]);
            if (!is_null($request->order_no)) {
                $queryqb->whereRaw("(('order_no' like '%" . $request->order_no . "%') OR ('refnofull' like '%" . $request->order_no . "%') OR ('total_mrp' like '%" . $request->order_no . "%'))");
            }
            if (!is_null($request->status)) {
                $queryqb->where("delivery_status", $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('orders.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('orders.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('orders.created_at', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                $user =  DB::table('users')->where('id', $a->user_id)->first();
                $address1 = OrderAddress::where('order_id', $a->id)->where('address_type', 1)->first();
                $address2 = OrderAddress::where('order_id', $a->id)->where('address_type', 1)->first();
                $refrence = '';
                $invoice = '';
                $usertype = '';
                $billingaddress = '';
                $shiipingaddress = '';
                if ($user->user_type == 1) {
                    $usertype = 'Student';
                } elseif ($user->user_type == 2) {
                    $usertype = 'Teacher';
                } elseif ($user->user_type == 3) {
                    $usertype = 'Sales User';
                }
                // if (!is_null($a->invoice)) {
                $refrence = $a->refnofull;
                $invoice = $a->refnofull;
                // }
                if ($address1) {
                    $billingaddress = ucfirst($address1->name) . ', ' . $address1->address . ', ' . $address1->locality . ', ' . $address1->city . ', ' . $address1->pincode . ', ' . $address1->state . ', ' . $address1->country . ', ' . $address1->type . ', ' . $address1->mobile;
                }
                if ($address2) {
                    $shiipingaddress = ucfirst($address2->name) . ', ' . $address2->address . ', ' . $address2->locality . ', ' . $address2->city . ', ' . $address2->pincode . ', ' . $address2->state . ', ' . $address2->country . ', ' . $address2->type . ', ' . $address2->mobile;
                }
                $status = '';
                if ($a->delivery_status == 0) {
                    $status = 'Pending';
                } elseif ($a->delivery_status == 1) {
                    $status = 'Accepted';
                } elseif ($a->delivery_status == 2) {
                    $status = 'Dispatched';
                } elseif ($a->delivery_status == 3) {
                    $status = 'Delivered';
                } elseif ($a->delivery_status == 4) {
                    $status = 'Cancelled';
                }

                $order_accepted_date = '';
                if (!is_null($a->order_accepted_date)) {
                    $order_accepted_date = $a->order_accepted_date;
                }
                $order_dispatch_date = '';
                if (!is_null($a->order_on_the_way_date)) {
                    $order_dispatch_date = $a->order_on_the_way_date;
                }
                $order_delivered_date = '';
                if (!is_null($a->order_delivered_date)) {
                    $order_delivered_date = $a->order_delivered_date;
                }
                $order_cancelled_date  = '';
                if (!is_null($a->order_cancelled_date)) {
                    $order_cancelled_date  = $a->order_cancelled_date;
                }
                $dataexport[] = [
                    $a->order_no,
                    $refrence,
                    $invoice,
                    $user->name,
                    $user->email,
                    $user->mobile,
                    $usertype,
                    $billingaddress,
                    $shiipingaddress,
                    $a->payment_mode,
                    $a->currency,
                    $a->total_qty,
                    $a->total_mrp,
                    $a->coupon_no,
                    $a->coupon_discount,
                    $a->tax_amt,
                    $a->delivery_price,
                    $a->net_amount,
                    date('d-M-Y h:ia', strtotime($a->created_at)),
                    $status,
                    $order_accepted_date,
                    $order_dispatch_date,
                    $order_delivered_date,
                    $order_cancelled_date,
                    $a->tracking_id,
                    $a->tracking_link,
                ];

                $oop = OrderProduct::where('order_id', $a->id)->orderBy('id', 'ASC')->get();;
                if ($oop->count() > 0) {
                    foreach ($oop as $us) {
                        $taxrate1 = '0%';
                        $taxrate2 = '0%';
                        $taxtype1 = 'None';
                        $taxtype2 = 'None';
                        if ($address2->state == 'Uttar Pradesh' || $address2->state == 'Uttar Pradesh') {
                            $txrate = round($us->tax_percentage / 2);
                            $taxrate1 = $txrate . '%';
                            $taxrate2 = $txrate . '%';
                            $taxtype1 = 'CGST';
                            $taxtype2 = 'SGST';
                        } else {
                            $taxrate1 = $us->tax_percentage . '%';
                            $taxrate2 = '';
                            $taxtype1 = 'IGST';
                            $taxtype2 = '';
                        }
                        $prodtype = '';
                        if ($us->type == 1) {
                            $prodtype = 'Book';
                            if ($us->book_online_support) {
                                $variant = 'Ebook';
                            } else {
                                $variant = 'Physical Book';
                            }
                        } elseif ($us->type == 2) {
                            $prodtype = 'Uniform';
                            $variant = $us->variant;
                        } elseif ($us->type == 3) {
                            $prodtype = 'Notebook';
                            $variant = $us->variant;
                        } elseif ($us->type == 4) {
                            $prodtype = 'Course';
                        }

                        $dataexport[] = [
                            $a->order_no,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $prodtype,
                            $us->product_name,
                            $variant,
                            number_format((float)$us->price, 2, '.', ''),
                            $us->qty,
                            number_format((float)($us->total_price - $us->total_tax), 2, '.', ''),
                            $taxrate1 . ',' . $taxrate2,
                            $taxtype1 . ',' . $taxtype2,
                            $us->total_tax,
                            $us->total_price
                        ];
                    }
                }
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Orders" . date('Y-m-d-h:i:s') . '.csv');
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
        $login_id =  Auth::user()->id;
        // print_r( $login_id); die;
        $title = "Orders";
        $addbutton = "";
        $queryqb = OrderProduct::query();
        $queryqb->select('order_products.*');
        $queryqb->orderBy('order_products.updated_at', 'DESC');
        $queryqb->orderBy('order_products.updated_at', 'DESC');
        $queryqb->whereNOTIN('order_products.id', [0]);
        $queryqb->where('order_products.vendor_id', $login_id);
        if (!is_null($request->order_no)) {
            $queryqb->whereRaw("(('order_no' like '%" . $request->order_no . "%') OR ('refnofull' like '%" . $request->order_no . "%') OR ('total_mrp' like '%" . $request->order_no . "%'))");
        }
        if (!is_null($request->status)) {
            $queryqb->where("delivery_status", $request->status);
        }
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('orders.created_at', [$request->start_date, $request->end_date]);
            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('orders.created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('orders.created_at', $request->end_date);
            }
        }
        $orders = $queryqb->paginate(10);
        if (!is_null($request->order_no)) {
            $orders->appends(['order_no' => $request->get('order_no')]);
        }
        if (!is_null($request->start_date)) {
            $orders->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $orders->appends(['end_date' => $request->get('end_date')]);
        }
        if (!is_null($request->status)) {
            $orders->appends(['status' => $request->get('status')]);
        }
        $exporturl = 'admin.manageorder.orders.index';
        return view('admin.ecom_order.index_vendor', compact('orders', 'title', 'addbutton', 'exporturl'));
    }


    public function show($id)
    {
        $user = Order::find($id);
        return view('admin.ecom_order.show', compact('user'));
    }
    public function order_view_vendor($id)
    {
        $user = OrderProduct::find($id);
        return view('admin.ecom_order.show_vendor', compact('user'));
    }

    public function st_update(Request $request, $id)
    {
        $st = $request->status;
        $product_id = $request->product_id;
        // dd($st);
        $user = OrderProduct::where('id', $product_id)->first();
        $order_data = Order::where('id', $user->order_id)->first();
        $user_data =  DB::table('users')->where('id', $user->user_id)->first();


        $user->cancel_remark = $request->cancel_remark;
        if ($st == 1) {
            if ($user->order_accepted_date == null) {
                $user->order_accepted_date = date('Y-m-d h:i:s');
            }

            $body = "Order Accept ";
            $title = "Order Accept";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        } else if ($st == 2) {
            if ($user->order_on_the_way_date == null) {
                $user->order_on_the_way_date = date('Y-m-d h:i:s');
            }
            $user->tracking_id = $request->tracking_id;
            $user->status = 2;
            $user->tracking_link = $request->tracking_link;
            $user->order_arriving_by = date('Y-m-d h:i:s', strtotime($request->arriving_by));


            $body = "Order Dispatched ";
            $title = "Order Dispatched";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        } else if ($st == 3) {

            if (!empty($user->astrologer_id)) {

                $astrologer_data =  DB::table('astrologers')->where('id', $user->astrologer_id)->first();

                $commision_percent = $astrologer_data->astroshop_percentage;
                $pgc_gsttax = $astrologer_data->gst_perct;
                $tds = $astrologer_data->tds_perct;
                $astrologer_commission = 0;
                $tds_amount = 0;
                $gst_amount = 0;
                $admin_commision = 0;
                if ($commision_percent > 0) {
                    $astrologer_commission = $user->price  * ($commision_percent / 100);
                    // $gst_amount = $astrologer_commission * ($pgc_gsttax/100);
                    // $astrologer_commission -= $gst_amount;
                    $tds_amount = $astrologer_commission * ($tds / 100);
                    $astrologer_commission -= $tds_amount;
                    $astrologer_commission = number_format((float)$astrologer_commission, 2, '.', '');
                    $tds_amount = number_format((float)$tds_amount, 2, '.', '');
                    // $gst_amount = number_format((double)$gst_amount, 2, '.', '');
                    $admin_commision = number_format((float)($user->price - $astrologer_commission), 2, '.', '');
                }



                $t = new AstrologerTransaction();
                $t->user_id = $user->astrologer_id;
                $t->txn_name = 'Eshop Booking OrderID -' . $order_data->order_no;
                $t->booking_id  = $user->id;
                $t->payment_mode = 'online';
                $t->booking_txn_id = time();
                $t->txn_for = 'Eshop Booking';
                $t->type = 'credit';
                $t->price = $user->price;
                $t->tax_price = 0;
                $t->tds_price = $tds_amount;
                $t->amount = $astrologer_commission;
                $t->status = 1;
                $t->txn_type = 5;
                $t->currency = 'INR';
                $t->save();
            }

            $user->status = 3;
            if ($user->order_delivered_date == null) {
                $user->order_delivered_date = date('Y-m-d h:i:s');
            }
            $body = "Order Delivered ";
            $title = "Order Delivered";
            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        } else if ($st == 4) {
            $user->cancel_reason = $request->cancel_reason;
            $user->cancel_remark = $request->cancel_remark;

            $user->status = 4;


            if ($user->order_cancelled_date == null) {
                $user->order_cancelled_date = date('Y-m-d h:i:s');
            }


            $body = "Order cancel ";
            $title = "Order cancel";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        }
        $user->status = $st;
        $user->delivery_status = $st;
        $user->update();

        $order_data->updated_at = date('Y-m-d h:i:s');
        $order_data->update();
        $record = new OrderStatusRecord();
        $record->user_id = auth()->user()->id;
        $record->order_product_id = $product_id;
        $record->order_id = $id;
        $record->status = $st;
        $record->tracking_id = $request->tracking_id ?? '';
        $record->tracking_link = $request->tracking_link ?? '';
        $record->cancel_reason = $request->cancel_reason ?? '';
        $record->cancel_remark = $request->cancel_remark ?? '';
        $record->save();
        //dd($user->delivery_status);
        return redirect()->back()->with('success', 'status updated successfully');
    }

    public function st_update_old(Request $request, $id)
    {
        $st = $request->status;
        //dd($st);
        $user = Order::find($id);

        $user_data =  DB::table('user')->where('id', $user->user_id)->first();


        $user->cancel_remark = $request->cancel_remark;
        if ($st == 1) {
            if ($user->order_accepted_date == null) {
                $user->order_accepted_date = date('Y-m-d h:i:s');
            }

            $body = "Order Accept ";
            $title = "Order Accept";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        } else if ($st == 2) {
            if ($user->order_on_the_way_date == null) {
                $user->order_on_the_way_date = date('Y-m-d h:i:s');
            }
            $user->tracking_id = $request->tracking_id;
            $user->status = 2;
            $user->tracking_link = $request->tracking_link;
            $user->order_arriving_by = date('Y-m-d h:i:s', strtotime($request->arriving_by));


            $body = "Order Dispatched ";
            $title = "Order Dispatched";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        } else if ($st == 3) {

            $user->status = 3;


            if ($user->order_delivered_date == null) {
                $user->order_delivered_date = date('Y-m-d h:i:s');
            }


            $body = "Order Delivered ";
            $title = "Order Delivered";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        } else if ($st == 4) {
            $user->cancel_reason = $request->cancel_reason;
            $user->cancel_remark = $request->cancel_remark;

            $user->status = 4;


            if ($user->order_cancelled_date == null) {
                $user->order_cancelled_date = date('Y-m-d h:i:s');
            }


            $body = "Order cancel ";
            $title = "Order cancel";

            // print_r($body); die;
            $details =  array(
                'body' => $body,
                'data' => $body,
                'title' => $title,
                'sound' => 'default',
                "icon" => "ic_launcher"
            );

            $this->yt_fcm_push_notification($user_data->device_token, $user_data->user_type, $details);
        }
        $user->delivery_status = $st;
        $user->update();
        $record = new OrderStatusRecord();
        $record->user_id = auth()->user()->id;
        $record->order_id = $id;
        $record->status = $st;
        $record->tracking_id = $request->tracking_id ?? '';
        $record->tracking_link = $request->tracking_link ?? '';
        $record->cancel_reason = $request->cancel_reason ?? '';
        $record->cancel_remark = $request->cancel_remark ?? '';
        $record->save();
        //dd($user->delivery_status);
        return redirect()->back()->with('success', 'status updated successfully');
    }




    public function yt_fcm_push_notification($registatoin_ids, $user_type, $message)
    {
        //    print_r($registatoin_ids);die();
        $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
        $API_SERVER_KEY = 'AAAAHf1GcUI:APA91bEFIczCEhmE16y0GQ7s8qi-9bzVOLadZxtRZWYYsUwPgmZS-SqG6ds55VVMvlYQdAejDaZtrYvtZ33XAyyqOMtHDpI5oLFBV-DoGQA8togEzRAoKNhUlhn2sPOFC5usIl1GICiy';

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





    function order_cancel()
    {

        $title = "Order Cancel Request";
        $addbutton = "";
        $cancel = EcomCancelRequest::orderBy('created_at', 'desc')->paginate();
        return view('admin.ecom_order.cancel_request', compact('cancel', 'title', 'addbutton'));
    }

    public function cancel_status($id, $no)
    {
        //$st=$request->status;
        //$date = date('Y-m-d h:i:s A');
        // dd($date);

        $cancel_st = EcomCancelRequest::find($id);
        if ($cancel_st->status == 0) {
            $cancel_st->status = $no;
            $cancel_st->update();
        }
        if ($no == 1) {
            $user = Order::find($cancel_st->order_id);
            $user->delivery_status = 4;
            $user->order_cancelled_date = date('Y-m-d h:i:s');
            $user->update();
        }
        //dd($user->delivery_status);
        return redirect()->route('admin.ecom.orders_cancel.index')->with('success', 'status updated successfully');
    }

    public function cancel_reasons()
    {

        $title = "Order Cancel Reason";
        $addbutton = "add cancel reason";
        $cancel = EcomCancelReason::paginate();
        return view('admin.ecom_order.cancel_reason', compact('cancel', 'title', 'addbutton'));
    }
    public function reason_create()
    {

        $title = "Add Cancel Reason";
        $listurl = route('admin.ecom.orders_cancel_reason.index');
        $listname = "Cancel reason List";
        return view('admin.ecom_order.reason_create', compact('listurl', 'title', 'listname'));
    }
    public function reason_save(Request $request)
    {

        $request->validate([
            'reason' => ['required']
        ]);

        $reason = new EcomCancelReason();
        $reason->status = $request->status;
        $reason->reason = $request->reason;


        $reason->save();
        return redirect()->route('admin.ecom.orders_cancel_reason.index')
            ->with('success', 'Cancel Reason added successfully');
    }

    function reason_edit($id)
    {
        $title = "Edit Cancel Reson";
        $listurl = route('admin.ecom.orders_cancel_reason.index');
        $listname = "Cancel reason List";
        $reason = EcomCancelReason::find($id);
        return view('admin.ecom_order.reason_edit', compact('title', 'listurl', 'listname', 'reason'));
    }

    public function reason_update(Request $request, $id)
    {

        $request->validate([
            'reason' => ['required']
        ]);

        $reason = EcomCancelReason::find($id);
        $reason->status = $request->status;
        $reason->reason = $request->reason;


        $reason->update();
        return redirect()->route('admin.ecom.orders_cancel_reason.index')
            ->with('success', 'Cancel Reason updated successfully');
    }

    public function reason_delete($id)
    {
        $reason = EcomCancelReason::destroy($id);
        // $reason->destroy();
        return redirect()->route('admin.ecom.orders_cancel_reason.index')
            ->with('success', 'Cancel Reason deleted successfully');
    }

    public function createInvoicedyn($id)
    {
        $order = Order::find($id);
        if ($order) {
            // if (!is_null($order->invoice)) {
            //     $path= $order->invoice;
            //     $certificateid = Str::random(10);
            //     $name = $certificateid.'.pdf';
            //     return view('print.questiongenraterpdfdownload', compact('path','name'));
            // }
            // else {
            $userdetails = User::find($order->user_id);
            $settings = Generalsetting::find(1);
            if (date('m') <= 6) { //Upto June 2014-2015
                $financial_year = (date('y') - 1) . '-' . date('y');
            } else { //After June 2015-2016
                $financial_year = date('y') . '-' . (date('y') + 1);
            }
            $refnofull = Str::random(10);
            if (!is_null($order->refnofull)) {
                $refnofull =  $order->refnofull;
            } else {
                if ($order->delivery_status == 3 || $order->delivery_status == 2 || $order->delivery_status == 1) {
                    $financial_year = $settings->ecomm_invoice_financial_year;
                    $refnumber = 1;
                    $refno = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second;
                    $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                    $getlastorder = Order::where('refno', $refno)->orderBy('refnumber', 'DESC')->first();
                    if ($getlastorder) {
                        $refnumber = $getlastorder->refnumber + 1;
                        $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                    }

                    $order->refno = $refno;
                    $order->refnumber = $refnumber;
                    $order->refnofull = $refnofull;
                    $order->save();
                }
            }
            $order = Order::find($id);
            if ($order->delivery_status == 2 || $order->delivery_status == 3) {
                if (!is_null($order->invoice)) {
                } elseif ($order->invoice != '') {
                } else {
                    $newString = str_replace('/', '-', $order->refnofull);
                    $certificateid = Str::random(10);
                    $pdfname = $newString . '.pdf';
                    $fileName = public_path('Ecommerceinvoices/') . $pdfname;
                    $pdf = PDF::loadView('emails.ecommerceorderinvoicedyn', compact('order', 'settings')); //->save($fileName);
                    // $path = Storage::disk('s3')->put('Ecommerceinvoices/'.$pdfname, $pdf->output());
                    // $content = 'https://gbp-meritbox.s3.ap-south-1.amazonaws.com/Ecommerceinvoices/'.$pdfname;
                    $order->invoice = "";
                    $order->save();
                }
            }
            echo $view  = view('emails.ecommerceorderinvoicedyn', compact('order', 'settings'))->render();
            // }
        }
    }

    public function import_ecommerceprices()
    {
        $title = 'Upload Bulk Prices';
        $listname = 'Bulk Prices';
        $listurl = route('admin.ecom.import_ecommerceprices');
        $samplebutton = 'Sample File';
        $sampleurl = asset('project/public/temp_files/BULKUPLOADNEW.csv');
        return view('admin.bulkprice.import', compact('title', 'listurl', 'listname', 'samplebutton', 'sampleurl'));
    }

    public function store_ecommerceprices(Request $request)
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
                if ($line[1] == 'book' || $line[1] == 'course') {
                    $check = Book::whereNOTIN('status', [2])->where('id', $line[0])->first();
                    if ($check) {
                        $b = Book::find($check->id);
                        if ($line[3] != '') {
                            $b->price = $line[3];
                        }
                        if ($line[4] != '') {
                            $b->discounted_price = $line[4];
                        }
                        if ($line[5] != '') {
                            $b->onlineprice = $line[5];
                        }
                        if ($line[6] != '') {
                            $b->discountonlineprice = $line[6];
                        }
                        $b->save();
                    } else {
                        $log .= '<br>Row No.' . $i . ' Not Found Product ' . $line[0];
                    }
                } elseif ($line[1] == 'uniform') {
                    $check = Uniform::whereNOTIN('status', [2])->where('id', $line[0])->first();
                    if ($check) {
                        $b = UniformSize::where('uniform_id', $check->id)->where('name', $line[2])->first();
                        if ($b) {
                            if ($line[3] != '') {
                                UniformSize::where('uniform_id', $check->id)->where('name', $line[2])
                                    ->update([
                                        'selling_price' => $line[3]
                                    ]);
                            }
                            if ($line[4] != '') {
                                UniformSize::where('uniform_id', $check->id)->where('name', $line[2])
                                    ->update([
                                        'price' => $line[4]
                                    ]);
                            }
                        } else {
                            $log .= '<br>Row No.' . $i . ' Not Found Product ' . $line[0];
                        }
                    } else {
                        $log .= '<br>Row No.' . $i . ' Not Found Product ' . $line[0];
                    }
                } elseif ($line[1] == 'notebook') {
                    $check = Notebook::whereNOTIN('status', [2])->where('id', $line[0])->first();
                    if ($check) {
                        $b = NotebookPack::where('notebook_id', $check->id)->where('name', $line[2])->first();
                        if ($b) {
                            if ($line[3] != '') {
                                NotebookPack::where('notebook_id', $check->id)->where('name', $line[2])
                                    ->update([
                                        'selling_price' => $line[3]
                                    ]);
                            }
                            if ($line[4] != '') {
                                NotebookPack::where('notebook_id', $check->id)->where('name', $line[2])
                                    ->update([
                                        'price' => $line[4]
                                    ]);
                            }
                        } else {
                            $log .= '<br>Row No.' . $i . ' Not Found Product ' . $line[0];
                        }
                    } else {
                        $log .= '<br>Row No.' . $i . ' Not Found Product ' . $line[0];
                    }
                }
            }
            $i++;
        }
        $successfailure = 'failure';
        $message = 'Some rows not uploaded ' . $log;
        if ($log == '') {
            $successfailure = 'success';
            $message = 'prices impoted successfully';
        }
        return redirect()->route('admin.ecom.import_ecommerceprices')
            ->with($successfailure, $message);
    }

    public function st_updateorder_address(Request $request, $id)
    {
        $record = OrderAddress::where('id', $id)->first();
        if ($record) {
            $record->name = $request->name ?? '';
            $record->mobile = $request->mobile ?? '';
            $record->address = $request->address ?? '';
            $record->type = $request->type ?? '';
            $record->pincode = $request->pincode ?? '';
            $record->country = $request->country ?? '';
            $record->state = $request->state ?? '';
            $record->city = $request->city ?? '';
            $record->save();
        }
        return redirect()->back()->with('success', 'updated successfully');
    }



    public function order_invoice_upload(Request $request, $id)
    {
        $record = Order::where('id', $id)->first();
        if ($record) {

            if (request()->hasFile('image')) {
                $sFile = request()->file('image');
                $fileNameWithTheExtension = $sFile->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = $sFile->getClientOriginalExtension();
                $image_name = $fileName . '_' . time() . '.' . $extension;
                $filePath = $sFile->move('project/public/invoice/', $image_name);
            }


            $record->invoice = $image_name ?? '';
            $record->save();
        }
        return redirect()->back()->with('success', 'updated successfully');
    }
}
