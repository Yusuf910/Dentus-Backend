<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    function contact()
    {
        // $supp = Setting::first();
        return view('website.contact');
    }

    public function sign_up_contact(Request $request)
    {
        $data = new Contact();
        // $data->user_id = auth()->user()->id;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->message = $request->message;
        // dd($data);
        $data->save();

        return redirect()->route('contact')->with('success', 'Enquiry Registered! we will contact soon');
    }


}
