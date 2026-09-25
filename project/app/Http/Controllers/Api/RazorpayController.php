<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\TempTransaction;
use App\Models\User;
use App\Models\WalletPlan;
use App\Models\Transactions;
use App\Models\Coupan;
use App\Models\Booking;
use App\Models\WalletAddRequest;
use App\Models\ReferCodeHistory;
use App\Models\Generalsetting;
use App\Models\Order;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Redirect;
use File;
use App\Traits\SdSendSms;

class RazorpayController extends Controller
{
    use SdSendSms;

    public function handle(Request $request)
    {
        $secret = config('services.razorpay.webhook_secret');
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent(); // RAW body

        $expected = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected, $signature)) {
            Log::warning('Invalid Razorpay signature', compact('signature'));
            return response('Invalid signature', 400);
        }

        $eventId = $request->header('X-Razorpay-Event-Id');
        if (Cache::has($eventId)) {
            return response('Duplicate event', 200);
        }
        Cache::put($eventId, true, now()->addHours(24));

        $event = $request->input('event');
        $data = $request->input('payload');
        // Handle specific events safely:
        switch ($event) {
            case 'payment.captured':
                // process payment...
                break;
            // other events...
            default:
                Log::info('Unhandled Razorpay event', ['event'=>$event]);
        }

        return response('OK', 200);
    }
    
    public function razorPaywebhook(Request $request)
    {
        echo bin2hex(random_bytes(32)); // Generates a 64-character hex string

        // print_r("sdsd"); die;
        $input = $request->all();
        $file = time() . rand() . '_file.json';
        $destinationPath = "project/razorPaywebhook/".date('Y-m-d').'/';
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, json_encode($input));

        $payload = $request->payload;
        
        if (isset($payload['payment'])) {
            $payment = $payload['payment'];
            $entity = $payment['entity'];
            $description1 = $entity['description'];
            $txnId = $entity['id'];
            $description1array = explode("|", $description1);

            if (in_array("buy", $description1array)) {
                if ($entity['status']) {
                    $user_id = $description1array[0];
                    $user_address = $description1array[2];
                    if ($entity['status'] == 'authorized') {

                        $checkalready = Order::where('payment_id', $txnId)->first();
                        if ($checkalready) {
                            // code...
                        } else {
                            $settings = Generalsetting::find(1);
                            if (date('m') <= 4) {
                                $financial_year = (date('y') - 1) . '-' . date('y');
                            } else {
                                $financial_year = date('y') . '-' . (date('y') + 1);
                            }
                            $address = UserAddress::where('id', $user_address)->first();
                            $userid = $user_id;
                            $cart = BuyNowCarts::where('user_id', $userid)->get();
                            if ($cart->count() > 0) {
                                $userdetails = User::find($userid);
                                $refnumber = 1;
                                $refno = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second;
                                $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                                $getlastorder = Order::where('refno', $refno)->orderBy('refnumber', 'DESC')->first();
                                if ($getlastorder) {
                                    $refnumber = $getlastorder->refnumber + 1;
                                    $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                                }
                                $discounamount = 0;
                                if ($request->discount_amount > 0) {
                                    $totalitem = count($request->cart_id);
                                    if ($totalitem > 0) {
                                        $discounamount = $request->discount_amount / $totalitem;
                                    }
                                }
                                $arr = array();
                                $subTotal = 0;
                                $total = 0;
                                $total_tax_price = 0;
                                $tQty = 0;
                                $couponDiscount = $request->discount_amount ?? 0;
                                $couponname = $request->coupan_code ?? 0;
                                $coup = Coupan::where('code', $couponname)->first();
                                $couponid = 0;
                                $couponid = '';
                                if ($coup) {
                                    $couponid = $coup->id;
                                }
                                $order_prefix = 'ASTR';
                                $ordersequence = 1;
                                $getlastorder = Order::whereNOTIN('id', [0])->orderBy('order_sequence_number', 'DESC')->first();
                                if ($getlastorder) {
                                    $ordersequence = $getlastorder->order_sequence_number + 1;
                                }
                                $order_id = $order_prefix . $ordersequence;
                                foreach ($cart as $key) {
                                    $tQty += $key->qty;




                                    if ($key->type == 1) {
                                        $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                    }
                                    if ($product) {
                                        $product_baseprice = $product->price;
                                        $product_discountprice = $product->discounted_price;
                                        $product_tax = $product->master_tax;
                                        $tax_percentage = 0;
                                        if ($product_tax > 0) {
                                            $tax = MasterTax::find($product_tax);
                                            if ($tax) {
                                                $tax_percentage = $tax->tax_percentage;
                                            }
                                        }

                                        $product_bookmark = Bookmark::where('user_id', $userid)->where('type', "astroproduct")->where('product_id', '=', $key->product_id)->first();
                                        if ($product_bookmark) {
                                            $bdte = Bookmark::where('id', $product_bookmark->id)->delete();
                                        }



                                        $finalprice = 0;
                                        $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $price = $finalprice;
                                        if ($product_discountprice > 0) {
                                            $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                            $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                            $price = $finadiscountprice;
                                            $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                        }
                                        $total += $price * $key->qty;
                                        $total_tax_price += $taxprice * $key->qty;
                                        $finalstock = $product->stock - $key->qty;
                                        if ($key->type == 1) {
                                            $finalstock = $product->stock - $key->qty;
                                            $updatestock = array(
                                                "stock" => $finalstock
                                            );
                                            AstroShopProduct::where("id", $product->id)->update($updatestock);
                                        }
                                    }
                                }
                                $tax = number_format((float)$total_tax_price, 2, '.', '');
                                $netTotal1 = $total - $couponDiscount;
                                // $netTotal1 = $total+$request->delivery_price - $couponDiscount;
                                $netTotal = number_format((float)$netTotal1, 2, '.', '');
                                $order_sequence_number = 0;
                                $se1 = explode('ASTR', $order_id);
                                if (isset($se1[1])) {
                                    $order_sequence_number = $se1[1];
                                }


                                $deliveryprice = 0;
                                // $carts_count =  BuyNowCarts::where('user_id', $userid)->count();

                                if ($tQty <= 3) {
                                    $deliveryprice = 200;
                                } elseif ($tQty < 6) {
                                    $deliveryprice = 300;
                                } elseif ($tQty >= 6) {
                                    $deliveryprice = 500;
                                }



                                $order = new Order();
                                $order->user_id = $user_id;
                                // $order->order_no = $order_id;
                                $order->tracking_id = '';
                                $order->status = 0; //payment failed
                                $order->payment_mode = "online";
                                $order->currency = "inr";
                                $order->total_qty = $tQty;
                                // $order->total_mrp = $total;
                                $order->tax_amt = $tax;
                                $order->payment_id = $txnId;
                                // $order->coupon_no = $couponname;
                                // $order->coupan_code_id = $couponid;
                                // $order->coupon_discount = $couponDiscount;
                                // $order->net_amount = $netTotal;
                                $order->delivery_price = $deliveryprice;
                                // $order->delivery_price = $request->delivery_price;
                                $order->bank_ref_no = $request->bank_ref_no;
                                $order->total_mrp = $total;
                                // $order->total_mrp = $total;
                                // $order->tax_amt = $request->tax_amount;
                                // $order->tax_amt = $tax;
                                $order->coupon_no = $couponname;
                                $order->coupan_code_id = $couponid;
                                $order->coupon_discount = $couponDiscount;
                                // $order->net_amount = $netTotal;
                                $order->net_amount = $total + $deliveryprice;
                                $order->decryptValues = '';
                                $order->order_sequence_number = $order_sequence_number;
                                $order->save();
                                // print_r( $order ); die;
                                if ($order) {
                                    if ($address) {
                                        // $country = Country::where('id', $address->country_id)->first();
                                        $state = State::where('id', $address->state_id)->first();
                                        $city = City::where('id', $address->city_id)->first();
                                        $shipping_addresses = new OrderAddress();
                                        $shipping_addresses->address_type = 1;
                                        $shipping_addresses->user_id = $userid;
                                        $shipping_addresses->name = $address->name;
                                        $shipping_addresses->mobile = $address->mobile;
                                        $shipping_addresses->alternate_number = $address->alternate_number;
                                        $shipping_addresses->address = $address->address;
                                        $shipping_addresses->locality = $address->locality;
                                        $shipping_addresses->country = "india";
                                        $shipping_addresses->city = $city->name;
                                        $shipping_addresses->state = $state->name;
                                        $shipping_addresses->pincode = $address->pincode;
                                        $shipping_addresses->type = $address->type;
                                        $shipping_addresses->order_id = $order->id;
                                        $shipping_addresses->save();
                                    }
                                    foreach ($cart as $key) {
                                        $tQty += $key->qty;
                                        $totalp = 0;
                                        $totalptax = 0;
                                        $tax_id = 0;
                                        $tax_name = '';
                                        $tax_per_quanity = 0;
                                        $tax_percentage = 0;
                                        if ($key->type == 1) {
                                            $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                        }
                                        if ($product) {
                                            $product_baseprice = $product->price;
                                            $product_discountprice = $product->discounted_price;
                                            $product_tax = $product->master_tax;
                                            if ($entity['currency'] !== 'INR') {
                                                $product_baseprice = $product->international_price;
                                                $product_discountprice = $product->international_discount_price;
                                                $product_tax = $product->international_master_tax;
                                            }

                                            $tax_percentage = 0;
                                            if ($product_tax > 0) {
                                                $tax = MasterTax::find($product_tax);
                                                if ($tax) {
                                                    $tax_percentage = $tax->tax_percentage;
                                                    $tax_id = $tax->id;
                                                    $tax_name = $tax->name;
                                                }
                                            }
                                            $finalprice = 0;
                                            $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                            $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                            $price = $finalprice;

                                            if ($product_discountprice > 0) {
                                                $product_baseprice = $product_discountprice;
                                                $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                                $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                                $price = $finadiscountprice;
                                                $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                            }
                                            $totalp = $price * $key->qty;
                                            $totalptax = $taxprice * $key->qty;
                                            $tax_per_quanity = $taxprice;
                                            $order_products = new OrderProduct();
                                            $order_products->type = $key->type;
                                            $order_products->user_id = $userid;
                                            $order_products->product_id = $product->id;
                                            $order_products->product_name = $product->name;
                                            $order_products->variant = $key->variant;
                                            $order_products->qty = $key->qty;
                                            $order_products->astrologer_id = $key->astrologer_id;
                                            $order_products->price = $product_baseprice;
                                            $order_products->total_price = $totalp;
                                            $order_products->total_tax = $totalptax;
                                            $order_products->tax_id = $tax_id;
                                            $order_products->tax_percentage = $tax_percentage;
                                            $order_products->tax_name = $tax_name;
                                            $order_products->tax_per_quanity = $tax_per_quanity;
                                            $order_products->currency = $request->currency;
                                            $order_products->order_id = $order->id;
                                            $order_products->save();
                                            $cartdelete = BuyNowCarts::where('user_id', $userid)->delete();
                                        }
                                    }

                                    $this->capturepayment($entity['id'],$entity['amount']);

                                    $response = ['status' => true, 'msg' => 'Order Placed Succefully!.', 'order' => $order];
                                }
                            }
                        }
                    } else {

                        $checkalready = Order::where('payment_id', $txnId)->first();
                        if ($checkalready) {
                            // code...
                        } else {
                            $settings = Generalsetting::find(1);
                            if (date('m') <= 4) {
                                $financial_year = (date('y') - 1) . '-' . date('y');
                            } else {
                                $financial_year = date('y') . '-' . (date('y') + 1);
                            }
                            $address = UserAddress::where('id', $user_address)->first();
                            $userid = $user_id;
                            $cart = BuyNowCarts::where('user_id', $userid)->get();
                            if ($cart->count() > 0) {
                                $userdetails = User::find($userid);
                                $refnumber = 1;
                                $refno = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second;
                                $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                                $getlastorder = Order::where('refno', $refno)->orderBy('refnumber', 'DESC')->first();
                                if ($getlastorder) {
                                    $refnumber = $getlastorder->refnumber + 1;
                                    $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                                }
                                $discounamount = 0;
                                if ($request->discount_amount > 0) {
                                    $totalitem = count($request->cart_id);
                                    if ($totalitem > 0) {
                                        $discounamount = $request->discount_amount / $totalitem;
                                    }
                                }
                                $arr = array();
                                $subTotal = 0;
                                $total = 0;
                                $total_tax_price = 0;
                                $tQty = 0;
                                $couponDiscount = $request->discount_amount ?? 0;
                                $couponname = $request->coupan_code ?? 0;
                                $coup = Coupan::where('code', $couponname)->first();
                                $couponid = 0;
                                $couponid = '';
                                if ($coup) {
                                    $couponid = $coup->id;
                                }
                                $order_prefix = 'ASTR';
                                $ordersequence = 1;
                                $getlastorder = Order::whereNOTIN('id', [0])->orderBy('order_sequence_number', 'DESC')->first();
                                if ($getlastorder) {
                                    $ordersequence = $getlastorder->order_sequence_number + 1;
                                }
                                $order_id = $order_prefix . $ordersequence;
                                foreach ($cart as $key) {
                                    $tQty += $key->qty;




                                    if ($key->type == 1) {
                                        $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                    }
                                    if ($product) {
                                        $product_baseprice = $product->price;
                                        $product_discountprice = $product->discounted_price;
                                        $product_tax = $product->master_tax;
                                        $tax_percentage = 0;
                                        if ($product_tax > 0) {
                                            $tax = MasterTax::find($product_tax);
                                            if ($tax) {
                                                $tax_percentage = $tax->tax_percentage;
                                            }
                                        }

                                        $product_bookmark = Bookmark::where('user_id', $userid)->where('type', "astroproduct")->where('product_id', '=', $key->product_id)->first();
                                        if ($product_bookmark) {
                                            $bdte = Bookmark::where('id', $product_bookmark->id)->delete();
                                        }



                                        $finalprice = 0;
                                        $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $price = $finalprice;
                                        if ($product_discountprice > 0) {
                                            $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                            $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                            $price = $finadiscountprice;
                                            $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                        }
                                        $total += $price * $key->qty;
                                        $total_tax_price += $taxprice * $key->qty;
                                        $finalstock = $product->stock - $key->qty;
                                        if ($key->type == 1) {
                                            $finalstock = $product->stock - $key->qty;
                                            $updatestock = array(
                                                "stock" => $finalstock
                                            );
                                            AstroShopProduct::where("id", $product->id)->update($updatestock);
                                        }
                                    }
                                }
                                $tax = number_format((float)$total_tax_price, 2, '.', '');
                                $netTotal1 = $total - $couponDiscount;
                                // $netTotal1 = $total+$request->delivery_price - $couponDiscount;
                                $netTotal = number_format((float)$netTotal1, 2, '.', '');
                                $order_sequence_number = 0;
                                $se1 = explode('ASTR', $order_id);
                                if (isset($se1[1])) {
                                    $order_sequence_number = $se1[1];
                                }


                                $deliveryprice = 0;
                                // $carts_count =  BuyNowCarts::where('user_id', $userid)->count();

                                if ($tQty <= 3) {
                                    $deliveryprice = 200;
                                } elseif ($tQty < 6) {
                                    $deliveryprice = 300;
                                } elseif ($tQty >= 6) {
                                    $deliveryprice = 500;
                                }



                                $order = new Order();
                                $order->user_id = $user_id;
                                // $order->order_no = $order_id;
                                $order->tracking_id = '';
                                $order->status = 5; //payment failed
                                $order->delivery_status = 5; //payment failed
                                $order->payment_mode = "online";
                                $order->currency = "inr";
                                $order->total_qty = $tQty;
                                // $order->total_mrp = $total;
                                $order->tax_amt = $tax;
                                $order->payment_id = $txnId;
                                // $order->coupon_no = $couponname;
                                // $order->coupan_code_id = $couponid;
                                // $order->coupon_discount = $couponDiscount;
                                // $order->net_amount = $netTotal;
                                $order->delivery_price = $deliveryprice;
                                // $order->delivery_price = $request->delivery_price;
                                $order->bank_ref_no = $request->bank_ref_no;
                                $order->total_mrp = $total;
                                // $order->total_mrp = $total;
                                // $order->tax_amt = $request->tax_amount;
                                // $order->tax_amt = $tax;
                                $order->coupon_no = $couponname;
                                $order->coupan_code_id = $couponid;
                                $order->coupon_discount = $couponDiscount;
                                // $order->net_amount = $netTotal;
                                $order->net_amount = $total + $deliveryprice;
                                $order->decryptValues = '';
                                $order->order_sequence_number = $order_sequence_number;
                                $order->save();
                                // print_r( $order ); die;
                                if ($order) {
                                    if ($address) {
                                        // $country = Country::where('id', $address->country_id)->first();
                                        $state = State::where('id', $address->state_id)->first();
                                        $city = City::where('id', $address->city_id)->first();
                                        $shipping_addresses = new OrderAddress();
                                        $shipping_addresses->address_type = 1;
                                        $shipping_addresses->user_id = $userid;
                                        $shipping_addresses->name = $address->name;
                                        $shipping_addresses->mobile = $address->mobile;
                                        $shipping_addresses->alternate_number = $address->alternate_number;
                                        $shipping_addresses->address = $address->address;
                                        $shipping_addresses->locality = $address->locality;
                                        $shipping_addresses->country = "india";
                                        $shipping_addresses->city = $city->name;
                                        $shipping_addresses->state = $state->name;
                                        $shipping_addresses->pincode = $address->pincode;
                                        $shipping_addresses->type = $address->type;
                                        $shipping_addresses->order_id = $order->id;
                                        $shipping_addresses->save();
                                    }
                                    foreach ($cart as $key) {
                                        $tQty += $key->qty;
                                        $totalp = 0;
                                        $totalptax = 0;
                                        $tax_id = 0;
                                        $tax_name = '';
                                        $tax_per_quanity = 0;
                                        $tax_percentage = 0;
                                        if ($key->type == 1) {
                                            $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                        }
                                        if ($product) {
                                            $product_baseprice = $product->price;
                                            $product_discountprice = $product->discounted_price;
                                            $product_tax = $product->master_tax;
                                            if ($entity['currency'] !== 'INR') {
                                                $product_baseprice = $product->international_price;
                                                $product_discountprice = $product->international_discount_price;
                                                $product_tax = $product->international_master_tax;
                                            }

                                            $tax_percentage = 0;
                                            if ($product_tax > 0) {
                                                $tax = MasterTax::find($product_tax);
                                                if ($tax) {
                                                    $tax_percentage = $tax->tax_percentage;
                                                    $tax_id = $tax->id;
                                                    $tax_name = $tax->name;
                                                }
                                            }
                                            $finalprice = 0;
                                            $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                            $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                            $price = $finalprice;

                                            if ($product_discountprice > 0) {
                                                $product_baseprice = $product_discountprice;
                                                $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                                $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                                $price = $finadiscountprice;
                                                $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                            }
                                            $totalp = $price * $key->qty;
                                            $totalptax = $taxprice * $key->qty;
                                            $tax_per_quanity = $taxprice;
                                            $order_products = new OrderProduct();
                                            $order_products->type = $key->type;
                                            $order_products->user_id = $userid;
                                            $order_products->product_id = $product->id;
                                            $order_products->product_name = $product->name;
                                            $order_products->variant = $key->variant;
                                            $order_products->qty = $key->qty;
                                            $order_products->astrologer_id = $key->astrologer_id;
                                            $order_products->price = $product_baseprice;
                                            $order_products->total_price = $totalp;
                                            $order_products->total_tax = $totalptax;
                                            $order_products->tax_id = $tax_id;
                                            $order_products->tax_percentage = $tax_percentage;
                                            $order_products->tax_name = $tax_name;
                                            $order_products->tax_per_quanity = $tax_per_quanity;
                                            $order_products->currency = $request->currency;
                                            $order_products->order_id = $order->id;
                                            $order_products->status = 5;
                                            $order_products->delivery_status = 5;
                                            $order_products->save();
                                            // $cartdelete = BuyNowCarts::where('user_id', $userid)->delete();
                                        }
                                    }
                                    $response = ['status' => true, 'msg' => 'Order Placed Succefully!.', 'order' => $order];
                                }
                            }
                        }
                    }
                }
            } 
            elseif(in_array("cart", $description1array)) {
                if ($entity['status']) {
                    $user_id = $description1array[0];
                    $user_address = $description1array[2];
                    if ($entity['status'] == 'authorized') {
                        

                        $settings = Generalsetting::find(1);
                        if (date('m') <= 4) {
                            $financial_year = (date('y') - 1) . '-' . date('y');
                        } else {
                            $financial_year = date('y') . '-' . (date('y') + 1);
                        }
                        $address = UserAddress::where('id', $user_address)->first();
                        $userid = $user_id;
                        $cart = EcomCart::where('user_id', $user_id)->get();

                        $deliveryprice = 0;
                        // $carts_count =  EcomCart::where('user_id', $user_id)->count();

                        // if ($carts_count <= 3 ) {
                        //     $deliveryprice = 200;
                        // }
                        // elseif ($carts_count < 6) {
                        //     $deliveryprice = 300;
                        // }
                        // elseif ($carts_count >= 6) {
                        //     $deliveryprice = 500;
                        // }



                        // print_r($cart->count()); die;
                        if ($cart->count() > 0) {
                            $userdetails = User::find($userid);
                            $refnumber = 1;
                            $refno = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second;
                            $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                            $getlastorder = Order::where('refno', $refno)->orderBy('refnumber', 'DESC')->first();
                            if ($getlastorder) {
                                $refnumber = $getlastorder->refnumber + 1;
                                $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                            }
                            $discounamount = 0;
                            if ($request->discount_amount > 0) {
                                $totalitem = count($request->cart_id);
                                if ($totalitem > 0) {
                                    $discounamount = $request->discount_amount / $totalitem;
                                }
                            }
                            $arr = array();
                            $subTotal = 0;
                            $total = 0;
                            $total_tax_price = 0;
                            $tQty = 0;
                            $couponDiscount = $request->discount_amount ?? 0;
                            $couponname = $request->coupan_code ?? 0;
                            $coup = Coupan::where('code', $couponname)->first();
                            $couponid = 0;
                            $couponid = '';
                            if ($coup) {
                                // $coup = Coupan::where('id',$couponid)->first();
                                // if ($coup) {
                                $couponid = $coup->id;
                                // }
                            }
                            $order_prefix = 'ASTR';
                            $ordersequence = 1;
                            $getlastorder = Order::whereNOTIN('id', [0])->orderBy('order_sequence_number', 'DESC')->first();
                            if ($getlastorder) {
                                $ordersequence = $getlastorder->order_sequence_number + 1;
                            }
                            $order_id = $order_prefix . $ordersequence;
                            foreach ($cart as $key) {
                                $tQty += $key->qty;


                                if ($tQty <= 3) {
                                    $deliveryprice = 200;
                                } elseif ($tQty < 6) {
                                    $deliveryprice = 300;
                                } elseif ($tQty >= 6) {
                                    $deliveryprice = 500;
                                }

                                if ($key->type == 1) {
                                    $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                }
                                if ($product) {


                                    $product_bookmark = Bookmark::where('user_id', $userid)->where('type', "astroproduct")->where('product_id', '=', $key->product_id)->first();
                                    if ($product_bookmark) {
                                        $bdte = Bookmark::where('id', $product_bookmark->id)->delete();
                                    }

                                    $product_baseprice = $product->price;

                                    // print_r( $product_baseprice); die;
                                    $product_discountprice = $product->discounted_price;
                                    $product_tax = $product->master_tax;
                                    // if ($request->currency !== 'INR') {
                                    //     $product_baseprice = $product->international_price;
                                    //     $product_discountprice = $product->international_discount_price;
                                    //     $product_tax = $product->international_master_tax;
                                    // }
                                    $tax_percentage = 0;
                                    if ($product_tax > 0) {
                                        $tax = MasterTax::find($product_tax);
                                        if ($tax) {
                                            $tax_percentage = $tax->tax_percentage;
                                        }
                                    }
                                    $finalprice = 0;

                                    // print_r( $product_baseprice); die;

                                    $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                    $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                    $price = $finalprice;



                                    if ($product_discountprice > 0) {
                                        $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                        $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                        $price = $finadiscountprice;
                                        $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                    }
                                    $total += $price * $key->qty;
                                    $total_tax_price += $taxprice * $key->qty;
                                    $finalstock = $product->stock - $key->qty;
                                    if ($key->type == 1) {
                                        $finalstock = $product->stock - $key->qty;
                                        $updatestock = array(
                                            "stock" => $finalstock
                                        );
                                        AstroShopProduct::where("id", $product->id)->update($updatestock);
                                    }
                                }
                            }
                            $tax = number_format((float)$total_tax_price, 2, '.', '');
                            $netTotal1 = $total - $couponDiscount;
                            // $netTotal1 = $total+$request->delivery_price - $couponDiscount;
                            $netTotal = number_format((float)$netTotal1, 2, '.', '');
                            $order_sequence_number = 0;
                            $se1 = explode('ASTR', $order_id);
                            if (isset($se1[1])) {
                                $order_sequence_number = $se1[1];
                            }
                            // print_r( $total); die;
                            $order = new Order();
                            $order->user_id = $user_id;
                            // $order->order_no = $order_id;
                            $order->tracking_id = '';
                            $order->status = 0; //payment failed
                            $order->payment_mode = "online";
                            $order->currency = "inr";
                            $order->total_qty = $tQty;
                            $order->total_mrp = $total;
                            // $order->total_mrp = $total;
                            $order->tax_amt = $tax;
                            $order->payment_id = $txnId;
                            $order->coupon_no = $couponname;
                            $order->coupan_code_id = $couponid;
                            $order->coupon_discount = $couponDiscount;
                            // $order->net_amount = $netTotal;
                            $order->net_amount = $total + $deliveryprice;
                            $order->delivery_price = $deliveryprice;
                            // $order->delivery_price = $request->delivery_price;
                            $order->bank_ref_no = $request->bank_ref_no;
                            $order->decryptValues = '';
                            $order->order_sequence_number = $order_sequence_number;
                            $order->save();
                            if ($order) {
                                if ($address) {
                                    // $country = Country::where('id', $address->country_id)->first();
                                    $state = State::where('id', $address->state_id)->first();
                                    $city = City::where('id', $address->city_id)->first();
                                    $shipping_addresses = new OrderAddress();
                                    $shipping_addresses->address_type = 1;
                                    $shipping_addresses->user_id = $userid;
                                    $shipping_addresses->name = $address->name;
                                    $shipping_addresses->mobile = $address->mobile;
                                    $shipping_addresses->alternate_number = $address->alternate_number;
                                    $shipping_addresses->address = $address->address;
                                    $shipping_addresses->locality = $address->locality;
                                    $shipping_addresses->country = "india";
                                    $shipping_addresses->city = $city->name;
                                    $shipping_addresses->state = $state->name;
                                    $shipping_addresses->pincode = $address->pincode;
                                    $shipping_addresses->type = $address->type;
                                    $shipping_addresses->order_id = $order->id;
                                    $shipping_addresses->save();
                                }
                                foreach ($cart as $key) {
                                    $tQty += $key->qty;
                                    $totalp = 0;
                                    $totalptax = 0;
                                    $tax_id = 0;
                                    $tax_name = '';
                                    $tax_per_quanity = 0;
                                    $tax_percentage = 0;
                                    if ($key->type == 1) {
                                        $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                    }
                                    if ($product) {
                                        $product_baseprice = $product->price;
                                        $product_discountprice = $product->discounted_price;
                                        $product_tax = $product->master_tax;
                                        // if ($request->currency !== 'INR') {
                                        //     $product_baseprice = $product->international_price;
                                        //     $product_discountprice = $product->international_discount_price;
                                        //     $product_tax = $product->international_master_tax;
                                        // }
                                        $tax_percentage = 0;
                                        if ($product_tax > 0) {
                                            $tax = MasterTax::find($product_tax);
                                            if ($tax) {
                                                $tax_percentage = $tax->tax_percentage;
                                                $tax_id = $tax->id;
                                                $tax_name = $tax->name;
                                            }
                                        }
                                        $finalprice = 0;
                                        $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $price = $finalprice;
                                        if ($product_discountprice > 0) {
                                            $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                            $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                            $price = $finadiscountprice;
                                            $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                        }
                                        $totalp = $price * $key->qty;
                                        $totalptax = $taxprice * $key->qty;
                                        $tax_per_quanity = $taxprice;
                                        $order_products = new OrderProduct();
                                        $order_products->type = $key->type;
                                        $order_products->user_id = $userid;
                                        $order_products->product_id = $product->id;
                                        $order_products->product_name = $product->name;
                                        $order_products->variant = $key->variant;
                                        $order_products->qty = $key->qty;
                                        $order_products->astrologer_id = $key->astrologer_id;
                                        $order_products->price = $product_discountprice;
                                        $order_products->total_price = $totalp;
                                        $order_products->total_tax = $totalptax;
                                        $order_products->tax_id = $tax_id;
                                        $order_products->tax_percentage = $tax_percentage;
                                        $order_products->tax_name = $tax_name;
                                        $order_products->tax_per_quanity = $tax_per_quanity;
                                        $order_products->currency = $request->currency;
                                        $order_products->order_id = $order->id;
                                        $order_products->save();

                                        $cartdelete = EcomCart::where('user_id', $userid)->delete();
                                    }
                                }

                                $this->capturepayment($entity['id'],$entity['amount']);
                                
                                $response = ['status' => true, 'msg' => 'Order Placed Succefully!.', 'order' => $order];
                            }
                        }
                    } else {
                        $settings = Generalsetting::find(1);
                        if (date('m') <= 4) {
                            $financial_year = (date('y') - 1) . '-' . date('y');
                        } else {
                            $financial_year = date('y') . '-' . (date('y') + 1);
                        }
                        $address = UserAddress::where('id', $user_address)->first();
                        $userid = $user_id;
                        $cart = EcomCart::where('user_id', $user_id)->get();

                        $deliveryprice = 0;

                        if ($cart->count() > 0) {
                            $userdetails = User::find($userid);
                            $refnumber = 1;
                            $refno = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second;
                            $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                            $getlastorder = Order::where('refno', $refno)->orderBy('refnumber', 'DESC')->first();
                            if ($getlastorder) {
                                $refnumber = $getlastorder->refnumber + 1;
                                $refnofull = $settings->ecomm_invoice_ref_first . '/' . $financial_year . '/' . $settings->ecomm_invoice_ref_second . '/' . $refnumber;
                            }
                            $discounamount = 0;
                            if ($request->discount_amount > 0) {
                                $totalitem = count($request->cart_id);
                                if ($totalitem > 0) {
                                    $discounamount = $request->discount_amount / $totalitem;
                                }
                            }
                            $arr = array();
                            $subTotal = 0;
                            $total = 0;
                            $total_tax_price = 0;
                            $tQty = 0;
                            $couponDiscount = $request->discount_amount ?? 0;
                            $couponname = $request->coupan_code ?? 0;
                            $coup = Coupan::where('code', $couponname)->first();
                            $couponid = 0;
                            $couponid = '';
                            if ($coup) {
                                $couponid = $coup->id;
                            }
                            $order_prefix = 'ASTR';
                            $ordersequence = 1;
                            $getlastorder = Order::whereNOTIN('id', [0])->orderBy('order_sequence_number', 'DESC')->first();
                            if ($getlastorder) {
                                $ordersequence = $getlastorder->order_sequence_number + 1;
                            }
                            $order_id = $order_prefix . $ordersequence;
                            foreach ($cart as $key) {
                                $tQty += $key->qty;


                                if ($tQty <= 3) {
                                    $deliveryprice = 200;
                                } elseif ($tQty < 6) {
                                    $deliveryprice = 300;
                                } elseif ($tQty >= 6) {
                                    $deliveryprice = 500;
                                }

                                if ($key->type == 1) {
                                    $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                }
                                if ($product) {


                                    $product_bookmark = Bookmark::where('user_id', $userid)->where('type', "astroproduct")->where('product_id', '=', $key->product_id)->first();
                                    if ($product_bookmark) {
                                        $bdte = Bookmark::where('id', $product_bookmark->id)->delete();
                                    }

                                    $product_baseprice = $product->price;
                                    $product_discountprice = $product->discounted_price;
                                    $product_tax = $product->master_tax;
                                    $tax_percentage = 0;
                                    if ($product_tax > 0) {
                                        $tax = MasterTax::find($product_tax);
                                        if ($tax) {
                                            $tax_percentage = $tax->tax_percentage;
                                        }
                                    }
                                    $finalprice = 0;
                                    $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                    $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                    $price = $finalprice;
                                    if ($product_discountprice > 0) {
                                        $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                        $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                        $price = $finadiscountprice;
                                        $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                    }
                                    $total += $price * $key->qty;
                                    $total_tax_price += $taxprice * $key->qty;
                                    $finalstock = $product->stock - $key->qty;
                                    if ($key->type == 1) {
                                        $finalstock = $product->stock - $key->qty;
                                        $updatestock = array(
                                            "stock" => $finalstock
                                        );
                                        AstroShopProduct::where("id", $product->id)->update($updatestock);
                                    }
                                }
                            }
                            $tax = number_format((float)$total_tax_price, 2, '.', '');
                            $netTotal1 = $total - $couponDiscount;
                            // $netTotal1 = $total+$request->delivery_price - $couponDiscount;
                            $netTotal = number_format((float)$netTotal1, 2, '.', '');
                            $order_sequence_number = 0;
                            $se1 = explode('ASTR', $order_id);
                            if (isset($se1[1])) {
                                $order_sequence_number = $se1[1];
                            }
                            // print_r( $total); die;
                            $order = new Order();
                            $order->user_id = $user_id;
                            // $order->order_no = $order_id;
                            $order->tracking_id = '';
                            $order->status = 5; //payment failed
                            $order->delivery_status = 5; //payment failed
                            $order->payment_mode = "online";
                            $order->currency = "inr";
                            $order->total_qty = $tQty;
                            $order->total_mrp = $total;
                            // $order->total_mrp = $total;
                            $order->tax_amt = $tax;
                            $order->payment_id = $txnId;
                            $order->coupon_no = $couponname;
                            $order->coupan_code_id = $couponid;
                            $order->coupon_discount = $couponDiscount;
                            // $order->net_amount = $netTotal;
                            $order->net_amount = $total + $deliveryprice;
                            $order->delivery_price = $deliveryprice;
                            // $order->delivery_price = $request->delivery_price;
                            $order->bank_ref_no = $request->bank_ref_no;
                            $order->decryptValues = '';
                            $order->order_sequence_number = $order_sequence_number;
                            $order->save();
                            if ($order) {
                                if ($address) {
                                    // $country = Country::where('id', $address->country_id)->first();
                                    $state = State::where('id', $address->state_id)->first();
                                    $city = City::where('id', $address->city_id)->first();
                                    $shipping_addresses = new OrderAddress();
                                    $shipping_addresses->address_type = 1;
                                    $shipping_addresses->user_id = $userid;
                                    $shipping_addresses->name = $address->name;
                                    $shipping_addresses->mobile = $address->mobile;
                                    $shipping_addresses->alternate_number = $address->alternate_number;
                                    $shipping_addresses->address = $address->address;
                                    $shipping_addresses->locality = $address->locality;
                                    $shipping_addresses->country = "india";
                                    $shipping_addresses->city = $city->name;
                                    $shipping_addresses->state = $state->name;
                                    $shipping_addresses->pincode = $address->pincode;
                                    $shipping_addresses->type = $address->type;
                                    $shipping_addresses->order_id = $order->id;
                                    $shipping_addresses->save();
                                }
                                foreach ($cart as $key) {
                                    $tQty += $key->qty;
                                    $totalp = 0;
                                    $totalptax = 0;
                                    $tax_id = 0;
                                    $tax_name = '';
                                    $tax_per_quanity = 0;
                                    $tax_percentage = 0;
                                    if ($key->type == 1) {
                                        $product = AstroShopProduct::where('status', 1)->where('id', '=', $key->product_id)->first();
                                    }
                                    if ($product) {
                                        $product_baseprice = $product->price;
                                        $product_discountprice = $product->discounted_price;
                                        $product_tax = $product->master_tax;
                                        // if ($request->currency !== 'INR') {
                                        //     $product_baseprice = $product->international_price;
                                        //     $product_discountprice = $product->international_discount_price;
                                        //     $product_tax = $product->international_master_tax;
                                        // }
                                        $tax_percentage = 0;
                                        if ($product_tax > 0) {
                                            $tax = MasterTax::find($product_tax);
                                            if ($tax) {
                                                $tax_percentage = $tax->tax_percentage;
                                                $tax_id = $tax->id;
                                                $tax_name = $tax->name;
                                            }
                                        }
                                        $finalprice = 0;
                                        $finalprice =  $product_baseprice + round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $taxprice = round(($product_baseprice * $tax_percentage) / 100, 2);
                                        $price = $finalprice;
                                        if ($product_discountprice > 0) {
                                            $finadiscountprice = $product_discountprice + round(($product_discountprice * $tax_percentage) / 100, 2);
                                            $percent = round((($finalprice - $finadiscountprice) * 100) / $finalprice);
                                            $price = $finadiscountprice;
                                            $taxprice = round(($product_discountprice * $tax_percentage) / 100, 2);
                                        }
                                        $totalp = $price * $key->qty;
                                        $totalptax = $taxprice * $key->qty;
                                        $tax_per_quanity = $taxprice;
                                        $order_products = new OrderProduct();
                                        $order_products->type = $key->type;
                                        $order_products->user_id = $userid;
                                        $order_products->product_id = $product->id;
                                        $order_products->product_name = $product->name;
                                        $order_products->variant = $key->variant;
                                        $order_products->qty = $key->qty;
                                        $order_products->astrologer_id = $key->astrologer_id;
                                        $order_products->price = $product_discountprice;
                                        $order_products->total_price = $totalp;
                                        $order_products->total_tax = $totalptax;
                                        $order_products->tax_id = $tax_id;
                                        $order_products->tax_percentage = $tax_percentage;
                                        $order_products->tax_name = $tax_name;
                                        $order_products->tax_per_quanity = $tax_per_quanity;
                                        $order_products->currency = $request->currency;
                                        $order_products->order_id = $order->id;
                                        $order_products->status = 5;
                                        $order_products->delivery_status = 5;
                                        $order_products->save();

                                        // $cartdelete = EcomCart::where('user_id',$userid)->delete();


                                    }
                                }
                                $response = ['status' => true, 'msg' => 'Order Placed Succefully!.', 'order' => $order];
                            }
                        }
                    }
                }
            }
            else {

                if (isset($payload['payment'])) {
                    $payment = $payload['payment'];
                    $entity = $payment['entity'];
                    $txnId = $entity['id'];
                    if ($entity['status']) {
                        if($entity['status'] == 'authorized' || $entity['status'] == 'captured'){
                                $exittra1 = Transactions::where('booking_txn_id', $txnId)->first();
                                if ($exittra1) {
                                    
                                }
                                else {
                                    $tempid = $entity['description'];
                                    $find = TempTransaction::where('id',$tempid)->first();
                                    if ($find) {
                                        $exittra2 = Transactions::where('booking_txn_id', $txnId)->first();
                                        if ($exittra2) {
                                        }
                                        else {
                                            if ($find->transaction_type == 2) {
                                                $date = date('Y-m-d H:i:s');
                                                $schedule_time = date("h:i a",strtotime($find->start_time));
                                                $a = new Booking();
                                                $a->user_id = $find->user_id;
                                                $a->astrologer_id = $find->astrologer_id;
                                                $a->type = $find->type;
                                                $a->member_id = $find->member_id;
                                                $a->mode  = $find->mode ;
                                                $a->is_priority = $find->is_priority;
                                                $a->status = 7;
                                                $a->schedule_date = $find->booking_date;
                                                $a->schedule_time = $schedule_time;
                                                $a->schedule_date_time = date('Y-m-d H:i:s',strtotime($find->booking_date.' '.$find->start_time));
                                                $a->end_time =date("h:i a",strtotime($find->end_time));
                                                $a->totalseconds = $find->total_seconds;
                                                $a->total_minutes = $find->total_minutes;
                                                $a->payment_mode = 'razorpay';
                                                $a->is_paid = 1;
                                                $a->is_premium = 1;
                                                $a->txn_id = $txnId;
                                                $a->subtotal = $find->subtotal;
                                                $a->subtotal = $find->subtotal;
                                                $a->wallet_deduct = $find->wallet_deduct;
                                                $a->coupon_discount = $find->coupon_discount;
                                                $a->gst = $find->gst;
                                                $a->payable_amount = $find->payable_amount;
                                                $a->coupon_id = $find->coupon_id;
                                                $a->coupon_code = $find->coupon_code;
                                                $a->save();
                                                if ($a) {
                                                    $a->bridge_id = $this->encrypt_decrypt('encrypt','booking'.$a->id);
                                                    $a->save();
                                                    $find->status = 1;
                                                    $find->booking_id = $a->id;
                                                    $find->save();
                                                    $new = new Transactions();
                                                    $new->user_id = $find->user_id;
                                                    $new->txn_name = 'Astrologer Booking';
                                                    $new->payment_mode = 'online';
                                                    $new->booking_txn_id = $txnId;
                                                    $new->booking_id = $a->id;
                                                    $new->txn_for = 'booking';
                                                    $new->type = 'debit';
                                                    $new->old_wallet = 0;
                                                    $new->txn_amount = $find->payable_amount;
                                                    $new->update_wallet = 0;
                                                    $new->status = 1;
                                                    $new->gst_perct = 0;
                                                    $new->gst_amount = $find->gst;
                                                    $new->payment = $find->payable_amount;
                                                    $new->json = $request->udf1;
                                                    $new->plan_amount = 0;
                                                    $new->coupon_id = $find->coupon_id;
                                                    $new->currency = 'INR';
                                                    $new->cpn_code = $find->coupon_code;
                                                    $new->payudata = json_encode($input);
                                                    $new->save();
                                                }
                                            }
                                            elseif ($find->transaction_type == 3) {
                                                $decrypt = json_decode(base64_decode($find->udf1));
                                                $recharge_amount = $decrypt->recharge_amount;
                                                $amount = $entity['amount']/100;
                                                $userid  = $decrypt->user_id;
                                                $txn_id = $entity['id'];
                                                $plan_id = $decrypt->plan_id ?? 0;
                                                $gst_amount = $decrypt->gst_amount;
                                                $coupon_name = $decrypt->coupon_name ?? '';
                                                $amounte = $decrypt->amounte;
                                                $gst_perct = $decrypt->gst_perct;
                                                $first_user = $decrypt->first_user;
                                                $type = $decrypt->type;
                                                $code = $decrypt->code;
                                                $cpn_cashback = 0;
                                                $copn = '';
                                                $copnid = '';
                                                if ($userid > 0) {
                                                    $coupan_code = '';
                                                    $coupan_id = '';
                                                    $original_wallet = $decrypt->amounte;
                                                    $virtual_wallet = 0;
                                                    $add_wallet = $decrypt->amounte;
                                                    $plan_amount = $decrypt->amounte;
                                                    $actual_payment = $amount;
                                                    $coupan_cashback = 0;
                                                    $transactionName = 'Wallet has been recharged with Rs '.$add_wallet;
                                                    if($code !== 'inr') {
                                                        $transactionName = 'Wallet has been recharged with $ '.$add_wallet;
                                                    }
                                                    if ($decrypt->coupon_name) {
                                                        $coupon_name = $decrypt->coupon_name;
                                                        $coupan_details = Coupan::where("code",$coupon_name)->where('status',1)->first();
                                                        if ($coupan_details) {
                                                            $coupan_code = $coupan_details->code;
                                                            $coupan_id = $coupan_details->id;
                                                            if ($coupan_details->discount_type == 'flat') 
                                                            {
                                                                $coupan_cashback = $coupan_details->amount;
                                                                $virtual_wallet += $coupan_cashback;
                                                            } 
                                                            else 
                                                            {
                                                                $coupan_cashback = $original_wallet * ($coupan_details->amount / 100);
                                                                $virtual_wallet += $coupan_cashback;
                                                            }
                                                        }
                                                    }
                                                    if ($plan_id != 0) {
                                                        $plandetails = WalletPlan::where('id',$plan_id)->where('status',1)->first();
                                                        if ($plandetails) {
                                                            $original_wallet = $plandetails->recharge;
                                                            $plan_amount = $add_wallet;
                                                            if ($plandetails->for_new_user == 1) {
                                                                $checkHowManyRechargeDoneByNewUser = Transactions::where("user_id",$userid)->where('txn_for','wallet')->where('type','credit')->count();
                                                                if ($checkHowManyRechargeDoneByNewUser < $plandetails->limit) {
                                                                    $add_benefits = $original_wallet * ($plandetails->add_percentage / 100);
                                                                    $virtual_wallet += $add_benefits;
                                                                }
                                                            }
                                                            else {
                                                                $checkHowManyRechargeDoneByNewUser = Transactions::where("user_id",$userid)->where('plan_id',$plandetails->id)->where('txn_for','wallet')->where('type','credit')->count();
                                                                if ($plandetails->everytime_benefit_flag == '1') {
                                                                    $add_benefits = $original_wallet * ($plandetails->add_percentage / 100);
                                                                    $virtual_wallet += $add_benefits;
                                                                }
                                                                elseif ($checkHowManyRechargeDoneByNewUser < $plandetails->limit) {
                                                                    $add_benefits = $original_wallet * ($plandetails->add_percentage / 100);
                                                                    $virtual_wallet += $add_benefits;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($virtual_wallet > 0) {
                                                        if($code == 'inr') {
                                                            $transactionName = 'Wallet has been recharged with Rs ' . $original_wallet . ' and received Rs ' . $original_wallet+$virtual_wallet;
                                                        }
                                                        else {
                                                            $transactionName = 'Wallet has been recharged with $ ' . $original_wallet . ' and received $ ' . $original_wallet+$virtual_wallet;
                                                        }
                                                    }
                                                    $addwalletresource = new WalletAddRequest();
                                                    $addwalletresource->user_id = $userid;
                                                    $addwalletresource->booking_id = $find->booking_id;
                                                    $addwalletresource->temp_id = $find->id;
                                                    $addwalletresource->amount = $amount;
                                                    $addwalletresource->status = 0;
                                                    $addwalletresource->save();
                                                    $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                    if ($checktran) {
                                                        
                                                    }
                                                    else {
                                                        $user = User::where('id',$userid)->where('status',1)->first();
                                                        if ($user) {
                                                            $actualwallet_fetch = $user->wallet;
                                                            $virtualwallet_fetch = $user->virtual_wallet;
                                                            $old_wallet = $actualwallet_fetch+$virtualwallet_fetch;
                                                            $new_wallet = $actualwallet_fetch+$original_wallet;
                                                            $new_virtual_wallet = $virtualwallet_fetch+$virtual_wallet;
                                                            $recharge_amount = $original_wallet+$virtual_wallet;
                                                            $update_wallet = $new_wallet+$new_virtual_wallet;
                                                            $txn_name = 'Money Added to wallet';
                                                            $txn_type = 'credit';
                                                            $dateTime = date('Y-m-d H:i:s');
                                                            $checkfirstrecharge = 0;
                                                            $checkf = Transactions::where('user_id',$userid)->where('type','credit')->first();
                                                            if ($checkf) {
                                                                $checkfirstrecharge = 1;
                                                            }
                                                            $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                            if ($checktran) {
                                                                
                                                            }
                                                            else {
                                                                $new = new Transactions();
                                                                $new->user_id = $userid;
                                                                $new->txn_name = $txn_name;
                                                                $new->payment_mode = 'online';
                                                                $new->booking_txn_id = $txnId;
                                                                $new->txn_for = 'wallet';
                                                                $new->type = $txn_type;
                                                                $new->old_wallet = $old_wallet;
                                                                $new->txn_amount = $recharge_amount;
                                                                $new->update_wallet = $update_wallet;
                                                                $new->status = 1;
                                                                $new->gst_perct = $gst_perct;
                                                                $new->gst_amount = $gst_amount;
                                                                $new->payment = $actual_payment;
                                                                $new->json = $tempid;
                                                                $new->plan_amount = $plan_amount;
                                                                $new->coupon_id = $coupan_id;
                                                                $new->currency = $code;
                                                                $new->cpn_code = $coupan_code;
                                                                $new->payudata = json_encode($input);
                                                                $new->actual_amount = $original_wallet;
                                                                $new->virtual_amount = $virtual_wallet;
                                                                $new->coupan_cashback = $coupan_cashback;
                                                                $new->plan_id = $plan_id;
                                                                $new->save();
                                                                if ($new) {
                                                                    $user->wallet = $new_wallet;
                                                                    $user->virtual_wallet = $new_virtual_wallet;
                                                                    $user->save();
                                                                    $find->status = 1;
                                                                    // $find->booking_id = $new->id;
                                                                    $find->save();
                                                                    $checkbooking = Booking::where('id',$addwalletresource->booking_id)->where('status',6)->first();
                                                                    if ($checkbooking) {
                                                                        $how_much_minutes = 0;
                                                                        $txn_amount = $original_wallet + $virtual_wallet;
                                                                        $price_lag = $txn_amount / $checkbooking->price_per_mint ;
                                                                        $how_much_minutes = floor($price_lag);
                                                                        $totalseconds = $how_much_minutes*60;
                                                                        if ($totalseconds > 0) {
                                                                            $old_seconds = $checkbooking->totalseconds;
                                                                            $update_seconds = $checkbooking->totalseconds + $totalseconds;
                                                                            $checkbooking->totalseconds = $update_seconds;
                                                                            $checkbooking->total_minutes = $checkbooking->total_minutes + $how_much_minutes;
                                                                            $checkbooking->save();
                                                                            $addwalletresource->old_wallet = $old_wallet;
                                                                            $addwalletresource->update_wallet    = $new_wallet;
                                                                            $addwalletresource->old_seconds    = $old_seconds;
                                                                            $addwalletresource->update_seconds    = $checkbooking->totalseconds;
                                                                            $addwalletresource->status    = 1;
                                                                            $addwalletresource->save();
                                                                        }
                                                                        
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                else {
                                                    if ($decrypt->coupon_name) {
                                                        $coupon_name = $decrypt->coupon_name;
                                                        $copn = Coupan::where("code",$coupon_name)->where('status',1)->first();
                                                    }
                                                    $addwalletresource = new WalletAddRequest();
                                                    $addwalletresource->user_id = $userid;
                                                    $addwalletresource->booking_id = $find->booking_id;
                                                    $addwalletresource->temp_id = $find->id;
                                                    $addwalletresource->amount = $amount;
                                                    $addwalletresource->status = 0;
                                                    $addwalletresource->save();
                                                    $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                    if ($checktran) {
                                                        
                                                    }
                                                    else {
                                                        $user = User::where('id',$userid)->where('status',1)->first();
                                                        if ($user) {
                                                            $txn_amount = $amount;
                                                            $plan_amount = $amount;
                                                            $payment = $amount;
                                                            if ($plan_id != 0) {
                                                                $plan = WalletPlan::where('id',$plan_id)->first();
                                                                if ($plan) {
                                                                    $txn_amount = $plan->recharge;
                                                                    $plan_amount = $txn_amount;
                                                                    $payment = $plan->recharge+$gst_amount;
                                                                }
                                                            }
                                                            else {
                                                                $txn_amount = ($amounte/100);//(($amounte/100)*50)/59;
                                                                $plan_amount = ($amounte/100);//(($amounte/100)*50)/59;
                                                            }
                            
                                                            $old_wallet = $user->wallet;
                                                            
                                                            $txn_name = "Recharge Wallet";
                                                            $offrs = '';
                                                            $getrs = '';
                                                            if ($copn) {
                                                                $cpn_code = $copn->code;
                                                                $copnid = $copn->id;
                                                                $typecpn = $copn->discount_type;
                                                                if ($typecpn == 'flat') {
                                                                $cpn_cashback = $copn->amount;
                                                                } else {
                                                                $cpn_cashback = $txn_amount * ($copn->amount / 100);
                                                                }
                                                                $recharge_amount = +($cpn_cashback + $amounte);
                                                            }
                                                            else {
                                                                $recharge_amount = ($amounte/100);//(($amounte/100)*50)/59;
                                                            }
                            
                                                            if ($coupon_name) {
                                                                $final_amount = 0;
                                                                if ($plan_id != 0) {
                                                                    $final_amount = $amounte;
                                                                }
                                                                else{
                                                                $final_amount = $txn_amount;
                                                                }
                                                                if($code == 'inr') {
                                                                    $offrs = $amounte;
                                                                    if ($plan_id != 0) {
                                                                        $offrs = $plan->recharge;
                                                                    }
                                                                    $getrs = $final_amount + $cpn_cashback;
                                                                    $txn_name =   'Wallet recharged for Rs '.$getrs;//.' get Rs '.$getrs;
                                                                } else {
                                                                    $offrs = $amounte;
                                                                    if ($plan_id != 0) {
                                                                        $offrs = $plan->recharge;
                                                                    }
                                                                    $getrs = $final_amount + $cpn_cashback;
                                                                    $txn_name = 'Wallet recharged for $ '.$getrs;//.' get $ '.$getrs;
                                                                }
                            
                                                            }
                                                            else {
                                                                if($code == 'inr') {
                                                                    if ($plan_id == 0) {
                                                                            $txn_name =   'Wallet recharged for Rs '.$txn_amount;
                                                                    }
                                                                    else {
                                                                            $offrs = $amounte;
                                                                            if ($plan_id != 0) {
                                                                            $offrs = $plan->recharge;
                                                                            }
                                                                            $getrs = $txn_amount;
                                                                            $txn_name =   'Wallet recharged for Rs '.$getrs;//.' get Rs '.$getrs;
                                                                    }
                                                                } else {
                                                                    $offrs = $amounte;
                                                                    if ($plan_id != 0) {
                                                                        $offrs = $plan->recharge;
                                                                    }
                                                                    $getrs = $txn_amount;
                                                                    $txn_name = 'Wallet recharged for $ '.$getrs;//.' get $ '.$getrs;
                                                                }
                                                            }
                                                            // alterante
                                                            $txn_name = 'Money Added to wallet';
                                                            $new_wallet = $old_wallet+$txn_amount;
                                                            $new_wallet += $cpn_cashback;
                                                            $txn_type = 'credit';
                                                            $dateTime = date('Y-m-d H:i:s');
                                                            $checkfirstrecharge = 0;
                                                            $checkf = Transactions::where('user_id',$userid)->first();
                                                            if ($checkf) {
                                                                $checkfirstrecharge = 1;
                                                            }
                                                            $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                            if ($checktran) {
                                                                
                                                            }
                                                            else {
                                                                $new = new Transactions();
                                                                $new->user_id = $userid;
                                                                $new->txn_name = $txn_name;
                                                                $new->payment_mode = 'online';
                                                                $new->booking_txn_id = $txnId;
                                                                $new->txn_for = 'wallet';
                                                                $new->type = $txn_type;
                                                                $new->old_wallet = $old_wallet;
                                                                $new->txn_amount = $recharge_amount;
                                                                $new->update_wallet = $new_wallet;
                                                                $new->status = 1;
                                                                $new->gst_perct = $gst_perct;
                                                                $new->gst_amount = $gst_amount;
                                                                $new->payment = $payment;
                                                                $new->json = $request->udf1;
                                                                $new->plan_amount = $plan_amount;
                                                                $new->coupon_id = $copnid;
                                                                $new->currency = $code;
                                                                $new->cpn_code = $coupon_name;
                                                                $new->payudata =json_encode($input);
                                                                $new->save();
                                                                if ($new) {
                                                                    $user->wallet = $new_wallet;
                                                                    $user->save();
                                                                    $find->status = 1;
                                                                    $find->save();
                                                                    $checkbooking = Booking::where('id',$addwalletresource->booking_id)->where('status',6)->first();
                                                                    if ($checkbooking) {
                                                                        $how_much_minutes = 0;
                                                                        $price_lag = $txn_amount / $checkbooking->price_per_mint ;
                                                                        $how_much_minutes = floor($price_lag);
                                                                        $totalseconds = $how_much_minutes*60;
                                                                        if ($totalseconds > 0) {
                                                                            $old_seconds = $checkbooking->totalseconds;
                                                                            $update_seconds = $checkbooking->totalseconds + $totalseconds;
                                                                            $checkbooking->totalseconds = $update_seconds;
                                                                            $checkbooking->total_minutes = $checkbooking->total_minutes + $how_much_minutes;
                                                                            $checkbooking->save();
                                                                            $addwalletresource->old_wallet = $old_wallet;
                                                                            $addwalletresource->update_wallet    = $new_wallet;
                                                                            $addwalletresource->old_seconds    = $old_seconds;
                                                                            $addwalletresource->update_seconds    = $checkbooking->totalseconds;
                                                                            $addwalletresource->status    = 1;
                                                                            $addwalletresource->save();
                                                                        }
                                                                        
                                                                    }
                                                                }
                                                            }
                            
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                $decrypt = json_decode(base64_decode($find->udf1));
                                                $recharge_amount = $decrypt->recharge_amount;
                                                $amount = $entity['amount']/100;
                                                $userid  = $decrypt->user_id;
                                                $txn_id = $entity['id'];
                                                $plan_id = $decrypt->plan_id ?? 0;
                                                $gst_amount = $decrypt->gst_amount;
                                                $coupon_name = $decrypt->first_user;
                                                $amounte = $decrypt->amounte;
                                                $gst_perct = $decrypt->gst_perct;
                                                $first_user = $decrypt->first_user;
                                                $type = $decrypt->type;
                                                $code = $decrypt->code;
                                                $cpn_cashback = 0;
                                                $copn = '';
                                                $copnid = '';
                                                if ($userid > 0) {
                                                    $coupan_code = '';
                                                    $coupan_id = '';
                                                    $original_wallet = $decrypt->amounte;
                                                    $virtual_wallet = 0;
                                                    $add_wallet = $decrypt->amounte;
                                                    $plan_amount = $decrypt->amounte;
                                                    $actual_payment = $amount;
                                                    $coupan_cashback = 0;
                                                    $transactionName = 'Wallet has been recharged with Rs '.$add_wallet;
                                                    if($code !== 'inr') {
                                                        $transactionName = 'Wallet has been recharged with $ '.$add_wallet;
                                                    }
                                                    if ($decrypt->coupon_name) {
                                                        $coupon_name = $decrypt->coupon_name;
                                                        $coupan_details = Coupan::where("code",$coupon_name)->where('status',1)->first();
                                                        if ($coupan_details) {
                                                            $coupan_code = $coupan_details->code;
                                                            $coupan_id = $coupan_details->id;
                                                            if ($coupan_details->discount_type == 'flat') 
                                                            {
                                                                $coupan_cashback = $coupan_details->amount;
                                                                $virtual_wallet += $coupan_cashback;
                                                            } 
                                                            else 
                                                            {
                                                                $coupan_cashback = $original_wallet * ($coupan_details->amount / 100);
                                                                $virtual_wallet += $coupan_cashback;
                                                            }
                                                        }
                                                    }
                                                    if ($plan_id != 0) {
                                                        $plandetails = WalletPlan::where('id',$plan_id)->where('status',1)->first();
                                                        if ($plandetails) {
                                                            $original_wallet = $plandetails->recharge;
                                                            $plan_amount = $add_wallet;
                                                            if ($plandetails->for_new_user == 1) {
                                                                $checkHowManyRechargeDoneByNewUser = Transactions::where("user_id",$userid)->where('txn_for','wallet')->where('type','credit')->count();
                                                                if ($checkHowManyRechargeDoneByNewUser < $plandetails->limit) {
                                                                    $add_benefits = $original_wallet * ($plandetails->add_percentage / 100);
                                                                    $virtual_wallet += $add_benefits;
                                                                }
                                                            }
                                                            else {
                                                                $checkHowManyRechargeDoneByNewUser = Transactions::where("user_id",$userid)->where('plan_id',$plandetails->id)->where('txn_for','wallet')->where('type','credit')->count();
                                                                if ($plandetails->everytime_benefit_flag == '1') {
                                                                    $add_benefits = $original_wallet * ($plandetails->add_percentage / 100);
                                                                    $virtual_wallet += $add_benefits;
                                                                }
                                                                elseif ($checkHowManyRechargeDoneByNewUser < $plandetails->limit) {
                                                                    $add_benefits = $original_wallet * ($plandetails->add_percentage / 100);
                                                                    $virtual_wallet += $add_benefits;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($virtual_wallet > 0) {
                                                        if($code == 'inr') {
                                                            $transactionName = 'Wallet has been recharged with Rs ' . $original_wallet . ' and received Rs ' . $original_wallet+$virtual_wallet;
                                                        }
                                                        else {
                                                            $transactionName = 'Wallet has been recharged with $ ' . $original_wallet . ' and received $ ' . $original_wallet+$virtual_wallet;
                                                        }
                                                    }
                                                    $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                    if ($checktran) {
                                                        
                                                    }
                                                    else {
                                                        $user = User::where('id',$userid)->where('status',1)->first();
                                                        if ($user) {
                                                            $actualwallet_fetch = $user->wallet;
                                                            $virtualwallet_fetch = $user->virtual_wallet;
                                                            $old_wallet = $actualwallet_fetch+$virtualwallet_fetch;
                                                            $new_wallet = $actualwallet_fetch+$original_wallet;
                                                            $new_virtual_wallet = $virtualwallet_fetch+$virtual_wallet;
                                                            $recharge_amount = $original_wallet+$virtual_wallet;
                                                            $update_wallet = $new_wallet+$new_virtual_wallet;
                                                            $txn_name = 'Money Added to wallet';
                                                            $txn_type = 'credit';
                                                            $dateTime = date('Y-m-d H:i:s');
                                                            $checkfirstrecharge = 0;
                                                            $checkf = Transactions::where('user_id',$userid)->where('type','credit')->first();
                                                            if ($checkf) {
                                                                $checkfirstrecharge = 1;
                                                            }
                                                            $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                            if ($checktran) {
                                                                
                                                            }
                                                            else {
                                                                $checkHowManyRechargeDoneByNewUser = Transactions::where("user_id",$userid)->where('txn_for','wallet')->where('type','credit')->where('payment_mode','<>','refer')->count();
                                                                $new = new Transactions();
                                                                $new->user_id = $userid;
                                                                $new->txn_name = $txn_name;
                                                                $new->payment_mode = 'online';
                                                                $new->booking_txn_id = $txnId;
                                                                $new->txn_for = 'wallet';
                                                                $new->type = $txn_type;
                                                                $new->old_wallet = $old_wallet;
                                                                $new->txn_amount = $recharge_amount;
                                                                $new->update_wallet = $update_wallet;
                                                                $new->status = 1;
                                                                $new->gst_perct = $gst_perct;
                                                                $new->gst_amount = $gst_amount;
                                                                $new->payment = $actual_payment;
                                                                $new->json = $request->udf1;
                                                                $new->plan_amount = $plan_amount;
                                                                $new->coupon_id = $coupan_id;
                                                                $new->currency = $code;
                                                                $new->cpn_code = $coupan_code;
                                                                $new->payudata = json_encode($input);
                                                                $new->actual_amount = $original_wallet;
                                                                $new->virtual_amount = $virtual_wallet;
                                                                $new->coupan_cashback = $coupan_cashback;
                                                                $new->plan_id = $plan_id;
                                                                $new->save();
                                                                if ($new) {
                                                                    $user->wallet = $new_wallet;
                                                                    $user->virtual_wallet = $new_virtual_wallet;
                                                                    $user->save();
                                                                    $find->status = 1;
                                                                    $find->booking_id = $new->id;
                                                                    $find->save();
                                                                    
                                                                    if($checkHowManyRechargeDoneByNewUser == 0) {
                                                                        $this->referBenefitadd($userid);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                else {
                                                    if ($decrypt->coupon_name) {
                                                        $coupon_name = $decrypt->coupon_name;
                                                        $copn = Coupan::where("code",$coupon_name)->where('status',1)->first();
                                                    }
                                                    $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                    if ($checktran) {
                                                        
                                                    }
                                                    else {
                                                        $user = User::where('id',$userid)->where('status',1)->first();
                                                        if ($user) {
                                                            $txn_amount = $amount;
                                                            $plan_amount = $amount;
                                                            $payment = $amount;
                                                            if ($plan_id != 0) {
                                                                $plan = WalletPlan::where('id',$plan_id)->first();
                                                                if ($plan) {
                                                                    $txn_amount = $plan->recharge;
                                                                    $plan_amount = $txn_amount;
                                                                    $payment = $plan->recharge+$gst_amount;
                                                                }
                                                            }
                                                            else {
                                                                $txn_amount = $amounte;
                                                                $plan_amount = $amounte;
                                                            }
                            
                                                            $old_wallet = $user->wallet;
                                                            
                                                            $txn_name = "Recharge Wallet";
                                                            $offrs = '';
                                                            $getrs = '';
                                                            if ($copn) {
                                                                $cpn_code = $copn->code;
                                                                $copnid = $copn->id;
                                                                $typecpn = $copn->discount_type;
                                                                if ($typecpn == 'flat') {
                                                                $cpn_cashback = $copn->amount;
                                                                } else {
                                                                $cpn_cashback = $txn_amount * ($copn->amount / 100);
                                                                }
                                                                $recharge_amount = +($cpn_cashback + $amounte);
                                                            }
                                                            else {
                                                                $recharge_amount = +$amounte;
                                                            }
                            
                                                            if ($coupon_name) {
                                                                $final_amount = 0;
                                                                if ($plan_id != 0) {
                                                                    $final_amount = $amounte;
                                                                }
                                                                else{
                                                                $final_amount = $txn_amount;
                                                                }
                                                                if($code == 'inr') {
                                                                    $offrs = $amounte;
                                                                    if ($plan_id != 0) {
                                                                        $offrs = $plan->recharge;
                                                                    }
                                                                    $getrs = $final_amount + $cpn_cashback;
                                                                    $txn_name =   'Wallet recharged for Rs '.$getrs;//.' get Rs '.$getrs;
                                                                } else {
                                                                    $offrs = $amounte;
                                                                    if ($plan_id != 0) {
                                                                        $offrs = $plan->recharge;
                                                                    }
                                                                    $getrs = $final_amount + $cpn_cashback;
                                                                    $txn_name = 'Wallet recharged for $ '.$getrs;//.' get $ '.$getrs;
                                                                }
                            
                                                            }
                                                            else {
                                                                if($code == 'inr') {
                                                                    if ($plan_id == 0) {
                                                                            $txn_name =   'Wallet recharged for Rs '.$txn_amount;
                                                                    }
                                                                    else {
                                                                            $offrs = $amounte;
                                                                            if ($plan_id != 0) {
                                                                            $offrs = $plan->recharge;
                                                                            }
                                                                            $getrs = $txn_amount;
                                                                            $txn_name =   'Wallet recharged for Rs '.$getrs;//.' get Rs '.$getrs;
                                                                    }
                                                                } else {
                                                                    $offrs = $amounte;
                                                                    if ($plan_id != 0) {
                                                                        $offrs = $plan->recharge;
                                                                    }
                                                                    $getrs = $txn_amount;
                                                                    $txn_name = 'Wallet recharged for $ '.$getrs;//.' get $ '.$getrs;
                                                                }
                                                            }
                                                            $txn_name = 'Money Added to wallet';
                                                            $new_wallet = $old_wallet+$txn_amount;
                                                            $new_wallet += $cpn_cashback;
                                                            $txn_type = 'credit';
                                                            $dateTime = date('Y-m-d H:i:s');
                                                            $checkfirstrecharge = 0;
                                                            $checkf = Transactions::where('user_id',$userid)->first();
                                                            if ($checkf) {
                                                                $checkfirstrecharge = 1;
                                                            }
                                                            $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                                            if ($checktran) {
                                                                
                                                            }
                                                            else {
                                                                $new = new Transactions();
                                                                $new->user_id = $userid;
                                                                $new->txn_name = $txn_name;
                                                                $new->payment_mode = 'online';
                                                                $new->booking_txn_id = $txnId;
                                                                $new->txn_for = 'wallet';
                                                                $new->type = $txn_type;
                                                                $new->old_wallet = $old_wallet;
                                                                $new->txn_amount = $recharge_amount;
                                                                $new->update_wallet = $new_wallet;
                                                                $new->status = 1;
                                                                $new->gst_perct = $gst_perct;
                                                                $new->gst_amount = $gst_amount;
                                                                $new->payment = $payment;
                                                                $new->json = $request->udf1;
                                                                $new->plan_amount = $plan_amount;
                                                                $new->coupon_id = $copnid;
                                                                $new->currency = $code;
                                                                $new->cpn_code = $coupon_name;
                                                                $new->payudata = json_encode($input);
                                                                $new->save();
                                                                if ($new) {
                                                                    $user->wallet = $new_wallet;
                                                                    $user->save();
                                                                    $find->status = 1;
                                                                    $find->booking_id = $new->id;
                                                                    $find->save();
                                                                    $checkHowManyRechargeDoneByNewUser = Transactions::where("user_id",$userid)->where('txn_for','wallet')->where('type','credit')->where('payment_mode','<>','refer')->count();
                                                                    if($checkHowManyRechargeDoneByNewUser == 1) {
                                                                        $this->referBenefitadd($userid);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $this->capturepayment($entity['id'],$entity['amount']);
                                        }
                                    }
                                    $what= 'success';
                                    $trxnid = '';//$input['transactionId'];
                                    // return view('successfailed',compact('what','trxnid'));
                                    $url = route('successfailed',['success',$trxnid]);
                                    return Redirect::to($url);
                                }
                        }
                        else {
                                $decrypt = $entity['description'];
                                $find = TempTransaction::where('id',$decrypt)->first();
                                if ($find) {
                                    $decrypt = json_decode(base64_decode($find->udf1));
                                    $recharge_amount = $decrypt->recharge_amount ?? 0;
                                    $amount = $entity['amount']/100;
                                    $userid  = $decrypt->user_id;
                                    $txn_id = $entity['id'];
                                    $plan_id = $decrypt->plan_id ?? 0;
                                    $gst_amount = $decrypt->gst_amount ?? 0;
                                    $coupon_name = $decrypt->first_user ?? '';
                                    $amounte = $decrypt->amounte;
                                    $gst_perct = $decrypt->gst_perct;
                                    $first_user = $decrypt->first_user;
                                    $type = $decrypt->type;
                                    $code = $decrypt->code;
                                    $cpn_cashback = 0;
                                    $copn = '';
                                    $copnid = '';

                                    if ($decrypt->coupon_name) {
                                        $coupon_name = $decrypt->coupon_name;
                                        $copn = Coupan::where("code",$coupon_name)->where('status',1)->first();
                                    }
                                    $checktran = Transactions::where('booking_txn_id',$txnId)->first();
                                    if ($checktran) {
                                        
                                    }
                                    else {
                                        $old_wallet = 0;
                                        $user = User::where('id',$userid)->where('status',1)->first();
                                        if ($user) {
                                            $old_wallet = $user->wallet;
                                        }
                                        $new = new Transactions();
                                        $new->user_id = $userid;
                                        $new->txn_name = $txn_name ?? 'failed';
                                        $new->payment_mode = 'online';
                                        $new->booking_txn_id = $txnId;
                                        $new->txn_for = 'wallet';
                                        $new->type = $txn_type ?? 'failed transaction';
                                        $new->old_wallet = $old_wallet;
                                        $new->txn_amount = $recharge_amount;
                                        $new->update_wallet = $old_wallet;
                                        $new->status = 2;

                                        $new->gst_perct = $gst_perct;
                                        $new->gst_amount = $gst_amount;
                                        $new->payment = $amount;
                                        $new->json = json_encode($input);
                                        $new->plan_amount = $plan_amount ?? '';
                                        $new->coupon_id = $copnid;
                                        $new->currency = $code;
                                        $new->cpn_code = $coupon_name;
                                        $new->payudata = json_encode($input);
                                        $new->save();
                                        $find->status = 2;
                                        $find->booking_id = $new->id;
                                        $find->save();
                                    }
                                }
                                $what= 'failed';
                                $trxnid = time();
                                $url = route('successfailed',['failed',$trxnid]);
                                return Redirect::to($url);
                        }
                    }
                    else {
                            $what= 'failed';
                            $trxnid = time();
                            $url = route('successfailed',['failed',$trxnid]);
                            return Redirect::to($url);
                    }
                }
                else {

                }

            }
        }
        
        $response2 = ['status' => true];
        return response($response2, 200);
    }

    function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'SECRET_KEY';
        $secret_iv = 'SECRET_IV';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if($action == 'encrypt')
        {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' )
        {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public function razorPayMentURl(Request $request)
    {
        // print_r("Ddd"); die;
        $input = $request->all();
        $validator = Validator::make($input, [
            'amount' => 'required',
            'mobile' => 'required',
            'merchantUserId'=>'required',
            'udf1'=>'required',
        ]);
        if ($request->astrologer_id) {
            $validator = Validator::make($input, [
                'member_id' => 'required',
                'type' => 'required',
                'mode'=>'required',
                'is_priority'=>'required',
                'astrologer_id'=>'required',
                'booking_date'=>'required',
                'start_time'=>'required',
                'end_time'=>'required',
                'subtotal'=>'required',
                'wallet_deduct'=>'required',
                'coupon_discount'=>'required',
                'gst'=>'required',
                'payable_amount'=>'required',
                'coupon_id'=>'required',
                // 'coupon_code'=>'required',
            ]);
        }
        if ($request->booking_id) {
            $validator = Validator::make($input, [
                'booking_id' => 'required',
            ]);
        }
        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 200);
        }
        $response2 = ['status' => false, 'msg' => 'Something Error Happen Try Again!'];
        $decrypt = json_decode(base64_decode($request->udf1));
        $n = new TempTransaction();
        $n->user_id = $decrypt->user_id ?? 0;
        // if (isset(auth()->user()->id)) {
        //     $n->user_id = auth()->user()->id;
        // }
        $n->udf1 = $request->udf1;
        $n->amount = $request->amount;
        $n->mobile = $request->mobile;
        $n->merchantUserId = $request->merchantUserId;
        $n->currency = $decrypt->code ?? 'inr';
        $n->status = 0;
        $n->booking_id = 0;
        if ($request->astrologer_id) {
            $total_minutes = 0;
            $total_seconds = 0;
            $start_date = new \DateTime(date('Y-m-d H:i:s', strtotime($request->booking_date.' '.$request->start_time)));
            $since_start = $start_date->diff(new \DateTime(date('Y-m-d H:i:s', strtotime($request->booking_date.' '.$request->end_time))));
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $total_minutes = $minutes;
            $total_seconds = $minutes*60;

            $n->transaction_type = 2;
            $n->member_id = $request->member_id;
            $n->type = $request->type;
            $n->mode = $request->mode;
            $n->is_priority = $request->is_priority;
            $n->astrologer_id = $request->astrologer_id;
            $n->booking_date = $request->booking_date;
            $n->start_time = $request->start_time;
            $n->end_time = $request->end_time;
            $n->total_minutes = $total_minutes;
            $n->total_seconds = $total_seconds;
            $n->subtotal = $request->subtotal;
            $n->wallet_deduct = $request->wallet_deduct;
            $n->coupon_discount = $request->coupon_discount;
            $n->gst = $request->gst;
            $n->payable_amount = $request->payable_amount;
            $n->coupon_id = $request->coupon_id;
            $n->coupon_code = $request->coupon_code;
        }
        if ($request->booking_id) {
            $n->transaction_type = 3;
            $n->booking_id = $request->booking_id;
        }
        $n->save();
        if ($n) {
            // $encrypt_string = $this->encrypt_decrypt('encrypt',$n->id);
            // $data = array (
            //   'merchantId' => 'GOLDTONLINE',
            //   'merchantTransactionId' => 'MT'.rand(),
            //   'merchantUserId' => $request->merchantUserId,
            //   'amount' => $request->amount,
            //   'redirectUrl' => route('phonepayresponse',$encrypt_string),
            //   'redirectMode' => 'POST',
            //   'callbackUrl' => route('phonepayresponsecallback',$encrypt_string),
            //   'mobileNumber' => $request->mobile,
            //   'paymentInstrument' => 
            //   array (
            //     'type' => 'PAY_PAGE'
            //     // "targetApp"=> "com.devvani.app"

                
            //   ),
            // );
            

            // $encode = base64_encode(json_encode($data));

            // $saltKey = 'e6334bdb-ef7b-48c8-9c18-72fda0d38298';//'099eb0cd-02cf-4e2a-8aca-3e6c6aff0399';
            // $saltIndex = 1;

            // $string = $encode.'/pg/v1/pay'.$saltKey;
            // $sha256 = hash('sha256',$string);

            // $finalXHeader = $sha256.'###'.$saltIndex;
            // $response= Http::withHeaders([
            //             'Content-Type' => 'application/json',
            //             'X-VERIFY' => $finalXHeader,
            //         ])->post('https://api.phonepe.com/apis/hermes/pg/v1/pay', [
            //             'request' => $encode,
            //         ]);

            // $rData = json_decode($response);
            // dd($rData);
            $response2 = ['status' => true, 'temp_id' => $n->id];
            // if(isset($rData->success)) {
            //     if (isset($rData->data->instrumentResponse->redirectInfo->url)) {
            //         $response2 = ['status' => true, 'redirectInfo' => $rData->data->instrumentResponse->redirectInfo->url];
            //     }
            // }
            return response($response2, 200);
        }
    }

    public function capturepayment($value,$amount)
    {

    $key = "rzp_live_RCGVCm3xkWYVRs";
    $secret = "73XN1JZWDZSEf69PcIzHz2Fd";

    $auth = base64_encode($key . ":" . $secret);

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.razorpay.com/v1/payments/'.$value.'/capture',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
          "amount": '.$amount.',
          "currency": "INR"
        }',
          CURLOPT_HTTPHEADER => array(
            'content-type: application/json',
              'Authorization: Basic ' . $auth
            // 'Authorization: Basic cnpwX2xpdmVfdlE1U0FUQXZhRGZTenU6N1ptemhRYTRmTE1jZXdkbjEwNmdrd05l'
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $file = time() . rand() . '_file.json';
        $destinationPath = "project/razorPaywebhook/".date('Y-m-d')."/captured/";
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, $response);
        return true;
    }

    public function referBenefitadd($user_id)
    {
        $getrefree = ReferCodeHistory::where('refer_to_uid',$user_id)->where('refer_to_uid_wallet_add_flag',1)->where('refer_by_uid_wallet_add_flag',0)->first();
        if ($getrefree) {
            $user = User::where('id',$getrefree->refer_by_uid)->where('status',1)->first();
            $otheruser = User::where('id',$getrefree->refer_to_uid)->where('status',1)->first();

            if ($user) {
                $bodyother1 = '';
                $bodyother2 = '';
                $othername = $otheruser->name ?? 'User';
                $othermobile = $otheruser->mobile ?? '*******';
                $set = Generalsetting::find(1);
                if ($user->country_code == "+91") {
                   $price =  $set->referee_inr;
                }
                else{
                   $price =  $set->referee_usd;
                }
                if ($otheruser) {
                    if ($otheruser->country_code == '+91' || $otheruser->country_code == '91') {
                        $selfnumber = $this->convertMobile($user->mobile);
                        $bodyother1 = "Referral amount credited to your referee $user->name [phone number $selfnumber]";
                        $bodyother2 = 'Referral amount credited to your referee';
                        $othermobile = $this->convertMobile($otheruser->mobile);
                    }
                    else {
                        $selfnumber = $this->convertMobile($user->email);
                        $bodyother1 = "Referral amount credited to your referee $user->name [email $selfnumber]";
                        $bodyother2 = 'Referral amount credited to your referee';
                        $othermobile = $this->convertEmail($otheruser->email);
                    }

                }
                
                if ($user->country_code == '+91' || $user->country_code == '91') {
                    $visibleDigits = substr($user->mobile, -4);
                    $hiddenDigits = str_repeat("*", strlen($user->mobile) - 4);
                    $visibleMobileNumber = $hiddenDigits . $visibleDigits;
                    $body = "Referral amount for $othername [phone number $othermobile] has been credited to your wallet.";
                    $body2 = "Welcome bonus credited - Rs $price";
                    $selfnumber = $visibleMobileNumber;
                    
                }
                else {
                    $fullEmailAddress = $user->email;
                    list($localPart, $domainPart) = explode('@', $fullEmailAddress);
                    $visibleCharacters = 1;
                    $hiddenCharacters = str_repeat("*", strlen($localPart) - $visibleCharacters);
                    $visibleLocalPart = substr($localPart, 0, $visibleCharacters) . $hiddenCharacters;
                    $visibleEmailAddress = $visibleLocalPart . '@' . $domainPart;
                    $body = "Referral amount for $othername [email $othermobile] has been credited to your wallet.";
                    $body2 = "Welcome bonus credited - $ $price";
                    $selfnumber = $visibleEmailAddress;
                }

                
                $virtualwallet_amount =  $user->virtual_wallet;
                $oldwallet_amount = $user->virtual_wallet + $user->wallet;
                $new_wallet = $virtualwallet_amount+$price;
                $new = new Transactions();
                $new->user_id = $user->id;
                $new->txn_name = $body2;
                $new->booking_txn_id= 'Ad'.time();
                $new->payment_mode = 'refer';
                $new->booking_id  = $getrefree->id;
                $new->txn_for = 'wallet';
                $new->type = 'credit';
                $new->old_wallet = $oldwallet_amount;
                $new->txn_amount = $price;
                $new->message = $body;
                $new->update_wallet = $user->wallet + $new_wallet;
                $new->status = 1;
                $new->by_admin = 0;
                $new->actual_amount = 0;
                $new->virtual_amount = $price;
                // $new->coupan_cashback = 0;
                $new->save();
                if ($new) {
                    $user->virtual_wallet = $new_wallet;
                    $user->save();
                    $getrefree->refer_by_uid_wallet_add_flag = 1;
                    $getrefree->save();
                    $this->async_to_all('Referral Bonus credited',$body,$user->id,'refercodebenfit');
                    if ($otheruser) {
                        $this->async_to_all('Referee received bonus',$bodyother1,$otheruser->id,'refercodebenfit');
                    }
                }
            }
        }
        return true;
    }

    public function convertMobile($mobile = '')
    {
        $visibleMobileNumber = '*********';
        if ($mobile) {
            $visibleDigits = substr($mobile, -4);
            $hiddenDigits = str_repeat("*", strlen($mobile) - 4);
            $visibleMobileNumber = $hiddenDigits . $visibleDigits;
        }
        return $visibleMobileNumber;
    }

    public function convertEmail($email = '')
    {
        $visibleEmailAddress = '******@***.com';
        if (strpos($email, '@') !== false) {
            $fullEmailAddress = $email;
            list($localPart, $domainPart) = explode('@', $fullEmailAddress);
            $visibleCharacters = 1;
            $hiddenCharacters = str_repeat("*", strlen($localPart) - $visibleCharacters);
            $visibleLocalPart = substr($localPart, 0, $visibleCharacters) . $hiddenCharacters;
            $visibleEmailAddress = $visibleLocalPart . '@' . $domainPart;
        }
        return $visibleEmailAddress;
    }
}
