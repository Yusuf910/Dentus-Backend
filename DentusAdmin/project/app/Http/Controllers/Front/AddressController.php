<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\OrderHistory;
use App\Models\Order;
use App\Models\BlogComment;
use App\Models\OrderPoint;
use App\Models\PointRedeem;
use App\Models\Charity;
use Illuminate\Support\Facades\Mail;
use DB;
use PDF;

class AddressController extends Controller
{
    public function create(Request $request)
    {
        $data = new UserAddress();

        $data->user_id = auth()->user()->id;
        $data->type = $request->type;
        $data->firstname = $request->firstname;
        $data->lastname = $request->lastname;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->company = $request->company;
        $data->address = $request->address;
        $data->lat = $request->lat;
        $data->lon = $request->lon;
        $data->apartment = $request->apartment;
        $data->postcode = $request->postcode;
        $data->country_id = $request->country_id;
        $data->state_id = $request->state_id;
        $data->city_id = $request->city_id;
        // dd($data);
        $data->save();

        return redirect()->back()->with('success', 'Address added successfully');
    }

    public function create1(Request $request)
    {
        $data = new UserAddress();

        $data->user_id = auth()->user()->id;
        $data->type = $request->type;
        $data->firstname = $request->firstname;
        $data->lastname = $request->lastname;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->company = $request->company;
        $data->address = $request->address;
        $data->lat = $request->lat;
        $data->lon = $request->lon;
        $data->apartment = $request->apartment;
        $data->postcode = $request->postcode;
        $data->country_id = $request->country_id;
        $data->state_id = $request->state_id;
        $data->city_id = $request->city_id;
        // dd($data);
        $data->save();

        return redirect()->route('checkout')->with('success', 'Address added successfully');
    }

    public function blog_comment(Request $request, $blog_id)
    {
        $data = new BlogComment();
        $data->type = $request->type;
        $data->parent_comment = $request->parent_comment;
        $data->user_id = auth()->user()->id;
        $data->blog_id = $blog_id;
        //dd($blog_id);
        $data->message = $request->message;
        $data->save();
        return redirect()->back();
    }


    function update(Request $request, $id)
    {
        $data = UserAddress::find($id);
        $data->user_id = auth()->user()->id;
        $data->type = $request->type;
        $data->firstname = $request->firstname;
        $data->lastname = $request->lastname;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->company = $request->company;
        $data->address = $request->address;
        $data->lat = $request->lat;
        $data->lon = $request->lon;
        $data->apartment = $request->apartment;
        $data->postcode = $request->postcode;
        $data->country_id = $request->country_id;
        $data->state_id = $request->state_id;
        $data->city_id = $request->city_id;
        $data->update();
        return redirect()->route('my_account')->with('success', 'Address updated successfully');
    }

    public function user_update(Request $request, $id)
    {
        $upd = User::find($id);
        $upd->name = $request->name;
        $upd->email = $request->email;
        $upd->country_code = $request->country_code;
        $upd->phone = $request->phone;
        $upd->update();
        return redirect()->route('my_account')->with('success', 'updated successfully');
    }


    function index(Request $request)
    {
        try {
            $upd = auth()->user();
            // $data = auth()->user()->addresses()->get();
            // print_r($orders); die;
            return view('website.my_account', compact('upd'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    public function show($id)
    {
        try {
            $data = OrderHistory::find($id);
            $order = auth()->user()->orders()->whereId($id)->first();
            // print_r($orders); die;
            return view('website.viewpage', compact('data', 'order'));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function generatePDF($id)
    {
        // $data = [
        //     'title' => 'Welcome to ItSolutionStuff.com',
        //     'date' => date('m/d/Y')
        // ];
        $order = auth()->user()->orders()->whereId($id)->first();
        $image = asset('assets/images/logo-demo2.png');
        $pdf = "";
        $pdf = PDF::loadView('website.viewpage', compact('order', 'image', 'pdf'))->setOptions(['defaultFont' => 'sans-serif', 'enable_remote' => true]);

        return $pdf->download('invoice.pdf');
    }



}
