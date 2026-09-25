<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\Testimonial;
use App\Models\Support;
use App\Models\Setting;
use App\Models\About;
use App\Models\Ourteam;
use App\Models\Blog;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
use App\Models\OtpUser;
use App\Models\BlogComment;
use App\Models\Astrologer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Redirect;


class HomeController extends Controller
{

    function otp_login(Request $request)
    {
          if(!empty($request->email))
          {
            $checkphone = User::where('email',$request->email)->first();
           if($checkphone){
            $otp = 1234;//rand(1000,9999);

            // $data = ['name' => $checkphone->name, 'data' => $otp];
            // Mail::send('mail', $data, function ($message) use ($checkphone) {
            //     $message->from('yusuf@appslure.com', 'KYND');
            //     $message->replyTo('yusuf@appslure.com', 'KYND');
            //     $message->returnPath('yusuf@appslure.com', 'KYND');
            //     $message->to($checkphone->email);
            //     $message->subject('KYND | Signin your account');
            // });
            DB::table('otp_users')->where('email',$request->email)->delete();
            // dd($otp);
            $enter = new OtpUser();
            $enter->email = $request->email;
            $enter->otp = $otp;
            $enter->save();
            session()->put('email',$request->email);
            $notice = "OTP sent sucessfully";
            return redirect()->route('Email_otp_page')->with('success','Otp sent successfully');
          }
          else
          {
            return redirect()->route('login')->with('danger','wrong email');
          }
          }
          else {

                $this->validate( $request, [
                    'phone'=>'required|numeric|digits_between:6,13',

                    ]);

          $checkphone = User::where('phone',$request->phone)->first();
          if($checkphone){
            $otp = 1234;//rand(1000,9999);

            // $data = ['name' => $checkphone->name, 'data' => $otp];
            // Mail::send('mail', $data, function ($message) use ($checkphone) {
            //     $message->from('yusuf@appslure.com', 'KYND');
            //     $message->replyTo('yusuf@appslure.com', 'KYND');
            //     $message->returnPath('yusuf@appslure.com', 'KYND');
            //     $message->to($checkphone->email);
            //     $message->subject('KYND | Signin your account');
            // });
            DB::table('otp_users')->where('phone',$request->phone)->delete();
            // dd($otp);
            $enter = new OtpUser();
            $enter->phone = $request->phone;
            $enter->otp = $otp;
            $enter->save();
            session()->put('country_code',$request->country_code);
            session()->put('phone',$request->phone);
            $notice = "OTP sent sucessfully";
            $token = $this->encrypt_decrypt('encrypt',$otp);
            return redirect()->route('login_otp_page',$token)->with('success','Otp sent successfully');
          }
          else
          {
            return redirect()->route('login')->with('danger','wrong number');
          }

        }
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


    function sign_up(Request $request)
    {
            $this->validate( $request, [
                'name' => 'required',
                'email'=> 'required|unique:users|email',
                'country_code'=>'required',
                // 'phone'=>'required|string|unique:users|min:6|max:16',
                ]);
        session()->put('name',$request->name);
        session()->put('email',$request->email);
        session()->put('country_code',$request->country_code);
        session()->put('phone',$request->phone);
        //dd(session('name'));
         $eml = $request->email;
        //  dd($eml);
            $otp = 1234;//rand(1000,9999);

            // $data = ['name' => $request->name, 'data' => $otp];
            // Mail::send('mail', $data, function ($message) use ($eml) {
            //     $message->from('yusuf@appslure.com', 'KYND');
            //     $message->replyTo('yusuf@appslure.com', 'KYND');
            //     $message->returnPath('yusuf@appslure.com', 'KYND');
            //     $message->to($eml);
            //     $message->subject('KYND | Signup your account');
            // });

            // dd($otp);
            // DB::table('otp_users')->where('phone',session('phone'))->where('country_code',session('country_code'))->delete();
            // $enter = new OtpUser();
            // $enter->country_code = $request->country_code;
            // $enter->phone = $request->phone;
            // $enter->otp = $otp;
            // $enter->save();

            DB::table('otp_users')->where('email',$request->email)->delete();
                  // dd($otp);
                  $enter = new OtpUser();
                  $enter->email = $request->email;
                  $enter->otp = $otp;
                  $enter->save();

            session()->put('country_code',$request->country_code);
            session()->put('phone',$request->phone);
            session()->put('type',$request->type);
            session()->put('lastname',$request->lastname);
            session()->put('company',$request->company);
            session()->put('address',$request->address);
            session()->put('lat',$request->lat);
            session()->put('lon',$request->lon);
            session()->put('apartment',$request->apartment);
            session()->put('postcode',$request->postcode);
            session()->put('country_id',$request->country_id);
            session()->put('state_id',$request->state_id);
            session()->put('city_id',$request->city_id);
            return redirect()->route('signup_otp_page')->with('success','Otp sent successfully');

    }

    function signup_otp_page()
    {

        return view('auth.signup_otp');
    }


    function signup_otpmatch(Request $request)
    {
        $this->validate( $request, [
            'otp' => 'required|min:4',
            ]);

        //$match = OtpUser::where('phone',$request->hidd)->latest();
        //dd($match);
        $match =  DB::table('otp_users')->where('email',session('email'))->first();
      // dd($match->otp);die();
      if($match->otp==$request->otp)
      {
        $checkphone = User::where('phone',session('phone'))->where('country_code',session('country_code'))->first();
        // DB::table('otp_users')->where('phone',session('phone'))->where('country_code',session('country_code'))->delete();
        DB::table('otp_users')->where('email',session('email'))->delete();
        $entry = new User();
        $entry->name = session('name');
        $entry->email = session('email');
        $entry->country_code = session('country_code');
        $entry->phone = session('phone');
        $entry->save();
        Auth::login($entry);

        $data_add = new UserAddress();
        $data_add->user_id = $entry->id;
        $data_add->type = 0;
        $data_add->firstname = session('name');
        $data_add->lastname = session('lastname');
        $data_add->email = session('email');
        $data_add->phone = session('phone');
        $data_add->company = session('company');
        $data_add->address = session('address');
        $data_add->lat = session('lat');
        $data_add->lon = session('lon');
        $data_add->apartment = session('apartment');
        $data_add->postcode = session('postcode');
        $data_add->country_id = session('country_id');
        $data_add->state_id = session('state_id');
        $data_add->city_id = session('city_id');
        $data_add->save();

        $addcartsession=session('temp_cart.id');
        $addqtysession=session('temp_cart.qty');

        if($addcartsession != 0)
        {
        foreach($addcartsession as $temp)
        {
            //dd($addqtysession[$temp]);
            $data = new Cart;
            $data->user_id= $entry->id;
            $data->product_id=$temp;
            $data->qty=$addqtysession[$temp];
            $data->save();
        }
    }
        if(auth()->user()->carts()->count() > 0) {
            return redirect()->route('checkout');
        }
        return redirect()->intended('/');

      }
      else
      {
        return redirect()->route('signup_otp_page')->with('danger','wrong otp');
      }


    }


    function sign_upsess(Request $request)
    {
               $checkphone_em = User::where('email',$request->email)->first();
               $checkphone_ph = User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
                if(!empty($checkphone_em))
                {
                 $checkphone = User::where('email',$request->email)->first();
                 if($checkphone){
                  $otp = rand(1000,9999);

                  $data = ['name' => $checkphone->name, 'data' => $otp];
                  Mail::send('mail', $data, function ($message) use ($checkphone) {
                      $message->from('yusuf@appslure.com', 'KYND');
                      $message->replyTo('yusuf@appslure.com', 'KYND');
                      $message->returnPath('yusuf@appslure.com', 'KYND');
                      $message->to($checkphone->email);
                      $message->subject('KYND | Signin your account');
                  });
                  DB::table('otp_users')->where('email',$request->email)->delete();
                  // dd($otp);
                  $enter = new OtpUser();
                  $enter->email = $request->email;
                  $enter->otp = $otp;
                  $enter->save();
                  session()->put('email',$request->email);
                  session()->put('entry_id',$request->entry_id);
                  session()->put('country_code',$request->country_code);
                    session()->put('phone',$request->phone);
                    session()->put('type',$request->type);
                    session()->put('name',$request->name);
                    session()->put('lastname',$request->lastname);
                    session()->put('company',$request->company);
                    session()->put('address',$request->address);
                    session()->put('lat',$request->lat);
                    session()->put('lon',$request->lon);
                    session()->put('apartment',$request->apartment);
                    session()->put('postcode',$request->postcode);
                    session()->put('country_id',$request->country_id);
                    session()->put('state_id',$request->state_id);
                    session()->put('city_id',$request->city_id);
                  $notice = "OTP sent sucessfully";
                  return redirect()->route('Email_otp_page')->with('success','Otp sent successfully');
                }
                else
                {
                  return redirect()->route('login')->with('danger','wrong email');
                }
                }

                // $checkphone_ph = User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
            //     elseif(!empty($checkphone_ph)) {

            //           $this->validate( $request, [
            //               'phone'=>'required|numeric|digits_between:6,13',
            //               'country_code'=>'required',

            //               ]);

            //     $checkphone = User::where('phone',$request->phone)->where('country_code',$request->country_code)->first();
            //     if($checkphone){
            //       $otp = rand(1000,9999);

            //       $data = ['name' => $checkphone->name, 'data' => $otp];
            //       Mail::send('mail', $data, function ($message) use ($checkphone) {
            //           $message->from('yusuf@appslure.com', 'KYND');
            //           $message->replyTo('yusuf@appslure.com', 'KYND');
            //           $message->returnPath('yusuf@appslure.com', 'KYND');
            //           $message->to($checkphone->email);
            //           $message->subject('KYND | Signin your account');
            //       });
            //       DB::table('otp_users')->where('phone',$request->phone)->where('country_code',$request->country_code)->delete();
            //       // dd($otp);
            //       $enter = new OtpUser();
            //       $enter->country_code = $request->country_code;
            //       $enter->phone = $request->phone;
            //       $enter->otp = $otp;
            //       $enter->save();
            //       session()->put('email',$request->email);
            //       session()->put('entry_id',$request->entry_id);
            //       session()->put('country_code',$request->country_code);
            //         session()->put('phone',$request->phone);
            //         session()->put('type',$request->type);
            //         session()->put('lastname',$request->lastname);
            //         session()->put('company',$request->company);
            //         session()->put('address',$request->address);
            //         session()->put('lat',$request->lat);
            //         session()->put('lon',$request->lon);
            //         session()->put('apartment',$request->apartment);
            //         session()->put('postcode',$request->postcode);
            //         session()->put('country_id',$request->country_id);
            //         session()->put('state_id',$request->state_id);
            //         session()->put('city_id',$request->city_id);
            //       $notice = "OTP sent sucessfully";
            //       return redirect()->route('login_otp_page')->with('success','Otp sent successfully');
            //     }
            //     else
            //     {
            //       return redirect()->route('login')->with('danger','wrong number');
            //     }

            //   }
            else {


            $this->validate( $request, [
                'name' => 'required',
                'email'=> 'required|unique:users|email',
                'country_code'=>'required',
                // 'phone'=>'required|string|unique:users|min:6|max:16',
                ]);
        session()->put('name',$request->name);
        session()->put('email',$request->email);
        session()->put('country_code',$request->country_code);
        session()->put('phone',$request->phone);
        //dd(session('name'));
         $eml = $request->email;
        //  dd($eml);
            $otp = rand(1000,9999);

            $data = ['name' => $request->name, 'data' => $otp];
            Mail::send('mail', $data, function ($message) use ($eml) {
                $message->from('yusuf@appslure.com', 'KYND');
                $message->replyTo('yusuf@appslure.com', 'KYND');
                $message->returnPath('yusuf@appslure.com', 'KYND');
                $message->to($eml);
                $message->subject('KYND | Signup your account');
            });
            // dd($otp);
            // DB::table('otp_users')->where('phone',session('phone'))->where('country_code',session('country_code'))->delete();
            // $enter = new OtpUser();
            // $enter->country_code = $request->country_code;
            // $enter->phone = $request->phone;
            // $enter->otp = $otp;
            // $enter->save();

            DB::table('otp_users')->where('email',$request->email)->delete();
                  // dd($otp);
                  $enter = new OtpUser();
                  $enter->email = $request->email;
                  $enter->otp = $otp;
                  $enter->save();

            session()->put('country_code',$request->country_code);
            session()->put('phone',$request->phone);
            session()->put('type',$request->type);
            session()->put('lastname',$request->lastname);
            session()->put('company',$request->company);
            session()->put('address',$request->address);
            session()->put('lat',$request->lat);
            session()->put('lon',$request->lon);
            session()->put('apartment',$request->apartment);
            session()->put('postcode',$request->postcode);
            session()->put('country_id',$request->country_id);
            session()->put('state_id',$request->state_id);
            session()->put('city_id',$request->city_id);
            return redirect()->back()->with('success','Otp sent successfully');
            // return redirect()->route('signup_otp_page')->with('success','Otp sent successfully');
        }
    }


    function signup_otpmatch_sess(Request $request)
    {
        $this->validate( $request, [
            'otp' => 'required|min:4',
            ]);

        //$match = OtpUser::where('phone',$request->hidd)->latest();
        //dd($match);
        // $match =  DB::table('otp_users')->where('phone',session('phone'))->where('country_code',session('country_code'))->first();
        $match =  DB::table('otp_users')->where('email',session('email'))->first();
      // dd($match->otp);die();
      if($match->otp==$request->otp)
      {
        // $checkphone = User::where('phone',session('phone'))->where('country_code',session('country_code'))->first();
        // DB::table('otp_users')->where('phone',session('phone'))->where('country_code',session('country_code'))->delete();

        $checkphone = User::where('email',session('email'))->first();
        if( $checkphone){
        Auth::Login($checkphone);
        DB::table('otp_users')->where('email',session('email'))->delete();
        }
        else
        {
        $entry = new User();
        $entry->name = session('name');
        $entry->email = session('email');
        $entry->country_code = session('country_code');
        $entry->phone = session('phone');
        $entry->save();
        Auth::login($entry);
       }
       if( $checkphone)
       {
        $data_add = new UserAddress();
        $data_add->user_id = $checkphone->id;
        $data_add->type = session('type');
        $data_add->firstname = session('name');
        $data_add->lastname = session('lastname');
        $data_add->email = session('email');
        $data_add->phone = session('phone');
        $data_add->company = session('company');
        $data_add->address = session('address');
        $data_add->lat = session('lat');
        $data_add->lon = session('lon');
        $data_add->apartment = session('apartment');
        $data_add->postcode = session('postcode');
        $data_add->country_id = session('country_id');
        $data_add->state_id = session('state_id');
        $data_add->city_id = session('city_id');
        $data_add->save();
       }
       else {
        $data_add = new UserAddress();
        $data_add->user_id = $entry->id;
        $data_add->type = session('type');
        $data_add->firstname = session('name');
        $data_add->lastname = session('lastname');
        $data_add->email = session('email');
        $data_add->phone = session('phone');
        $data_add->company = session('company');
        $data_add->address = session('address');
        $data_add->lat = session('lat');
        $data_add->lon = session('lon');
        $data_add->apartment = session('apartment');
        $data_add->postcode = session('postcode');
        $data_add->country_id = session('country_id');
        $data_add->state_id = session('state_id');
        $data_add->city_id = session('city_id');
        // dd($data_add);
        $data_add->save();
       }
        $addcartsession=session('temp_cart.id');
        $addqtysession=session('temp_cart.qty');

        if( $checkphone)
        {
        if($addcartsession) {
            auth()->user()->carts()->delete();
        }
        }
        else
        {
        }

        if($addcartsession != 0)
        {
        foreach($addcartsession as $temp)
        {
            //dd($addqtysession[$temp]);
            $data = new Cart;
            if( $checkphone)
            {
            $data->user_id= $checkphone->id;
            }
            else
            {
                $data->user_id= $entry->id;
            }
            $data->product_id=$temp;
            $data->qty=$addqtysession[$temp];
            $data->save();
        }
    }
        if(auth()->user()->carts()->count() > 0) {
            return redirect()->route('checkout');
            // return redirect()->route('blg_address');
        }
        return redirect()->intended('/');

      }
      else
      {
        return redirect()->route('signup_otp_page')->with('danger','wrong otp');
      }


    }


    function login_otp_page(Request $request, $token=null)
    {
        if ($token == '') {
            return redirect()->route('webfront.index');
        }
        $title = "Otp";
        return view('auth.enter_otp',compact('title','token'));
    }

    function Email_otp_page()
    {

        return view('auth.email_otp');
    }

    function otp_match(Request $request)
    {
        // dd($request->otp1);
        $request->validate([
            // 'token'=>'required',
            'otp1'=>'required',
            'otp2'=>'required',
            'otp3'=>'required',
            'otp4'=>'required',

    ]);
    // $decrypted = Crypt::decryptString($request->token);
    $a1 = 1234;
    if ($a1 == $request->otp1.$request->otp2.$request->otp3.$request->otp4) {
        $checkphone = User::where('phone',session('phone'))->first();
        Auth::Login($checkphone);
                return redirect()->route('my_account')->with('success','Login successfully!!');

        }

    else {
        return redirect()->back()->with('fail','OTP Not match kindly submit correct otp!');
    }

    }

    function otp_matchEmail(Request $request)
    {
        $this->validate( $request, [
            'otp' => 'required|min:4',
            ]);

        //$match = OtpUser::where('phone',$request->hidd)->latest();
        //dd(session('country_code'));

       $match =  DB::table('otp_users')->where('email',session('email'))->first();
       //dd($match->otp);
      if($match->otp==$request->otp)
      {
        $checkphone = User::where('email',session('email'))->first();
        Auth::Login($checkphone);
        DB::table('otp_users')->where('email',session('email'))->delete();
        if(!empty(session('name'))){
        $data_add = new UserAddress();
        $data_add->user_id = $checkphone->id;
        $data_add->type = session('type');
        $data_add->firstname = session('name');
        $data_add->lastname = session('lastname');
        $data_add->email = session('email');
        $data_add->phone = session('phone');
        $data_add->company = session('company');
        $data_add->address = session('address');
        $data_add->lat = session('lat');
        $data_add->lon = session('lon');
        $data_add->apartment = session('apartment');
        $data_add->postcode = session('postcode');
        $data_add->country_id = session('country_id');
        $data_add->state_id = session('state_id');
        $data_add->city_id = session('city_id');
        // dd($data_add);
        $data_add->save();
      }

        $addcartsession=session('temp_cart.id');
        $addqtysession=session('temp_cart.qty');

        if($addcartsession) {
            auth()->user()->carts()->delete();
        }

        if($addcartsession != 0)
        {
        foreach($addcartsession as $temp)
        {
            //dd($addqtysession[$temp]);
            $data = new Cart;
            $data->user_id= auth()->user()->id;
            $data->product_id=$temp;
            $data->qty=$addqtysession[$temp];
            $data->save();
        }
    }

        if(auth()->user()->carts()->count() > 0) {
            return redirect()->route('checkout');
        }
        return redirect()->intended('/');
      }
      else
      {
        return redirect()->route('login_otp_page')->with('danger','wrong otp');
      }

    }

    function index(Request $request)
    {
       // echo $request->ip();
        // $banners = Banner::where('type','main')->whereNOTIN('is_active',[2])->get();
        return view('successpassword');
    }

    function home_api(Request $request)
    {
        $name = $request->name;

        $abc =  DB::table('products')->where('category_id', $name)->take(8)->get();
        $html = '';
        foreach ($abc as $abcimage) {
            $html .=   "<div class='col-6 col-sm-6 col-md-4 col-lg-3 item'>
                                    <!-- start product image -->
                                    <a href='" . route('product_detail', $abcimage->id) . "' class='product-img'>
                                       <!-- image -->

                                    <div class='product-image'>
                                       <!-- start product image -->


                                          <img class='primary blur-up lazyload' data-src='" . asset('assets/images/products') .
                                          '/' . $abcimage->image . "' src='" . asset('assets/images/products') . '/' . $abcimage->image . "' alt='image' title=''>
                                          <!-- End image -->
                                          <!-- Hover image -->
                                          <img class='hover blur-up lazyload' data-src='" . asset('assets/images/products') . '/' . $abcimage->image . "' src='" . asset('assets/images/products') . '/' . $abcimage->image . "' alt='image' title=''>
                                          <!-- End hover image -->
                                          <!-- product label -->
                                          <!-- <div class='product-labels'><span class='lbl on-sale'>50% Off</span></div> -->
                                          <!-- End product label -->
                                       </a>
                                       <!-- end product image -->
                                       <!--Product Button-->
                                       <div class='button-set style2'>
                                          <ul>";

                                        if(auth()->check()){
                                          $html .=  "<li>
                                                <!--Cart Button-->
                                                <a class='btn-icon btn btn-addto-cart pro-addtocart-popup'  data-ajax-type='cart' data-ajax-url='".route('produ_pop', ['product' => $abcimage->id])."' href='#pro-addtocart-popup'>
                                                <i class='icon an an-cart-l'></i> <span class='tooltip-label'>Add to Cart</span>
                                                </a>
                                                <!--end Cart Button-->
                                             </li>
                                             <li>
                                                <!--Quick View Button-->
                                                <a href='quick-view-popup' title='Quick View' data-ajax-url='" . route('quick_pop', ['product' => $abcimage->id]) . "' class='btn-icon quick-view-popup quick-view' data-toggle='modal' data-target='#content_quickview'>
                                                <i class='icon an an-search-l'></i>
                                                <span class='tooltip-label'>Quick View</span>
                                                </a>
                                                <!--End Quick View Button-->
                                             </li>
                                             <li>
                                                <!--Wishlist Button-->
                                                <a class='btn-icon wishlist add-to-wishlist' data-ajax-url='" . route('produ_pop', ['product' => $abcimage->id]) . "' data-ajax-type='wishlist' href='" . route('my_whishlist') . "'><i class='icon an an-heart-l'></i> <span class='tooltip-label top'>Add To Wishlist</span></a>
                                                <!--End Wishlist Button-->
                                             </li>
                                             <li>
                                             </li>";
                                         } else {

                                            $html .=  "<li><a class='btn-icon btn cartIcon'  href='" . route('temporary_card',$abcimage->id) . "'><i class='icon an an-cart-l'></i> <span class='tooltip-label top'>Add to Cart</span></a></li>
                                                <!--End Cart Button-->
                                                <!--Quick View Button-->
                                                <li><a class='btn-icon quick-view-popup quick-view' href='quick-view-popup' data-toggle='modal' data-ajax-url='" . route('quick_pop', ['product' => $abcimage->id]) . "' data-target='#content_quickview'><i class='icon an an-search-l'></i> <span class='tooltip-label top'>Quick View</span></a></li>
                                                <!--End Quick View Button-->
                                                <!--Wishlist Button-->
                                                <li><a class='btn-icon wishlist' href='" . route('login') . "'><i class='icon an an-heart-l'></i> <span class='tooltip-label top'>Add To Wishlist</span></a></li>";
                                         }
                                                $html .=  "</ul>
                                       </div>
                                       <!--End Product Button-->
                                    </div>
                                    <!-- end product image -->
                                    <!--start product details -->
                                    <div class='product-details text-left'>
                                       <!-- product name -->
                                       <div class='product-name'>
                                          <a href='{{route('product_detail')}}'>" . $abcimage->name . "</a>
                                       </div>
                                       <div class='product-price'>";

                                       if($abcimage->mrp)
                                       {
                                        $html .=  " <span class='old-price'>$" . $abcimage->mrp . "</span>";
                                       }
                                       else
                                       {
                                       }
                                       $html .= "<span class='price'>$" . $abcimage->selling_price . "</span>
                                       </div>
                                       <!-- End product price -->

                                    </div>
                                    <!-- End product details -->
                                 </div>";
        }

        return $html;
    }




    function about()
    {
        $about = About::find(1);
        $ourteam = Ourteam::get();
        $supp = Support::get();
        $testimon = Testimonial::get();
        return view('website.about', compact('supp', 'about', 'ourteam', 'testimon'));
    }

    function blog()
    {
        $blog = Blog::paginate(6);
        return view('website.blog', compact('blog'));
    }

    function blog_detail(Blog $blog)
    {
        $comments = BlogComment::where('blog_id', $blog->id)->whereNull('parent_comment')->get();
        return view('website.blog_detail', compact('blog','comments'));
    }



    function my_whishlist()
    {
        $wishlist = auth()->user()->wishlists()->paginate(8);
        return view('website.my_whishlist', compact('wishlist'));
    }

    function cart()
    {
        $cart = auth()->user()->carts()->get();
        return view('website.cart', compact('cart'));
    }

    function temp_cart()
    {
        // $cart = Product::find($id);
        return view('website.cart');
    }

    function login()
    {
        return view('website.login');
    }

    function my_account()
    {
        $wishlist = auth()->user()->wishlists()->get();
        return view('website.my_account', compact('wishlist'));
    }

    function product_detail()
    {

        return view('website.product_detail');
    }

    function blg_address(Request $request)
    {
        $data = auth()->user()->addresses()->get();
        $shippingAddress = auth()->user()->addresses()->where('type', 0)->get();
        $billingAddress = auth()->user()->addresses()->where('type', 1)->get();
        $cart = auth()->user()->carts()->get();
        $coupon = Coupon::get();
        return view('website.blg_address', compact('data','shippingAddress', 'billingAddress','cart','coupon'));
    }


    function checkout()
    {
        if(auth()->user()->carts()->count() == 0){
            return redirect()->route('home');
        }
        $data = auth()->user()->addresses()->get();
        $shippingAddress = auth()->user()->addresses()->where('type', 0)->get();
        $billingAddress = auth()->user()->addresses()->where('type', 1)->get();
        $cart = auth()->user()->carts()->get();
        $coupon = Coupon::get();
        return view('website.checkout', compact('data','shippingAddress', 'billingAddress','cart','coupon'));
    }

    function checkout_success(Order $order)
    {
        return view('website.checkout-success', compact('order'));
    }

    function coupon(Request $request)
    {
        $total_price=0;
        $discount = 0;

        $response = null;

        $cart = auth()->user()->carts()->get();
        foreach ($cart as $cart) {
            $check = DB::table('products')->where('id',$cart->product_id)->first();

            $price =$cart->qty * $check->selling_price;
            $total_price = $price+$total_price;

            }
        if ($request->type == "apply") {
            $user = Coupon::where("name", $request->name)->where('status',1)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    "message" => "Wrong Coupon"
                ]);

            }
            $response = $user;
            if($user->type == 1) {
                $discount =  $total_price * ($user->discount / 100);
            } else {
                $discount = $user->discount;
            }

        }
        else {

        }

            $shipping_charge= 50;
            $total_price += $shipping_charge;
            $total_price -= $discount;
            return response()->json([
            'status' => true,
            'data' => $response,
            'total'=>$total_price,
            'type' => $request->type,
            "message" => "Wrong Coupon"
        ]);
    }
}
