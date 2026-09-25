<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Carbon\Carbon;
use PDF;


class WebsiteController extends Controller
{


   

    public function home()
    {

        // phpinfo(); die;
        return view('website.index');
    }




}
