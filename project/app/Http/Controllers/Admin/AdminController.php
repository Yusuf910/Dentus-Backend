<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Generalsetting;
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Validation\Rule;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use File;
use App\Models\Invitation;

use App\Traits\SdSendSms;
class AdminController extends Controller
{
    use SdSendSms;
    function check(Request $request){
         //Validate Inputs
         $request->validate([
            'email'=>'required|email|exists:users,email',
            'password'=>'required|min:6|max:30'
         ],[
             'email.exists'=>'This email is not exists in admins table'
         ]);

         $creds = $request->only('email','password');
         $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && $user->status == 4) {
            return redirect()->route('admin.login')->with('fail', 'Your account is inactive.');
        }
         if( Auth::guard('admin')->attempt($creds) ){
             return redirect()->route('admin.home');
         }else{
             return redirect()->route('admin.login')->with('fail','Incorrect credentials');
         }
    }

    function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success','Logout Successfully!');
    }

    function forgetcheck(Request $request){
        $data = $request->all();
        if (strpos($data['emailmobileusername'], '@') !== false) 
        {
            $user = User::where('email', $data['emailmobileusername'])->where([/*['user_type','=', $user_type],*/['status','=', 1]])->first();
        
        }
        else
        {
            $user = User::where('mobile', $data['emailmobileusername'])->where([/*['user_type','=', $user_type],*/['status','=', 1]])->first();
        }
        if($user) {
            $otp = mt_rand(100000, 999900);
            $data = [
                'to' => $user->email,
                'subject' => "Dentus Verification Code",
                'name' => $user->name,
                'generalsettings' => Generalsetting::find(1),
                'otp' => $otp
            ];
            $string = $otp.'|'.$user->id;
            $encrypted = Crypt::encryptString($string);
            Mail::send('dentusotp', compact('data'), function ($message) use ($data) {
                $message->to($data['to'])
                        ->subject($data['subject']);
            });
            $msg = 'Dear User,Your login OTP for using Dentist platform is '.$otp.'. Please do not share this code with anyone. TBASPL';
            $this->otpmsg_sd($user->mobile,$msg,'1705174825127580471','','582582'); 
            return redirect()->route('admin.otpforforget',$encrypted);
        } else {
            return redirect()->back()->with('fail','User not found!');
        }
    }

    public function otpforforget(Request $request, $token=null){
        if ($token == '') {
            return redirect()->back()->with('fail','Something error happen plz try again!');
        }
        $title = "Otp";
        return view('doctor.otpforforget',compact('title','token'));
    }

    public function otpverifyforget (Request $request)
    {
        $request->validate([
                'token'=>'required',
                'otp'=>'required',
        ]);
        $decrypted = Crypt::decryptString($request->token);
        $a1 = explode('|', $decrypted);
        if ($a1[0] == $request->otp) {
             $user = User::where('id', $a1[1])->where('status', 1)->first();
             if ($user) {
                $token = $request->token;
                return view('doctor.changepassword',compact('token'));
             }
             else return redirect()->back()->with('fail','Something error happen please try again later!!');
        }
        else {
            return redirect()->back()->with('fail','OTP Not match kindly submit correct otp!');
        }
    }

    public function changepassword (Request $request)
    {
        $request->validate([
            'password'  => 'required|string|min:6',
            'npassword' => 'required|string|min:6|same:password',
        ]);

        $decrypted = Crypt::decryptString($request->token);
        $a1 = explode('|', $decrypted);
        $user = User::where('id', $a1[1])->where('status', 1)->first();
        if ($user) {
            $password = Hash::make($request->npassword);
            User::where('id', $user->id)->update(['password' => $password]);
            return redirect()->route('admin.login')->with('success','Password reset Successfully!!');
        }
        else return redirect()->back()->with('failure','Something error happen please try again later!!');
    }

    public function resendotpforforget (Request $request, $token=null)
    {
        if ($token == '') {
            return redirect()->back();
        }
        $decrypted = Crypt::decryptString($request->token);
        $a1 = explode('|', $decrypted);
        if ($a1[0]) {
            $user = User::where('id', $a1[1])->where('status', 1)->first();
            if ($user) {
                $otp = $a1[0];
                $data = [
                    'to' => $user->email,
                    'subject' => "Dentus Verification Code",
                    'name' => $user->name,
                    'generalsettings' => Generalsetting::find(1),
                    'otp' => $otp
                ];
                $string = $otp.'|'.$user->id;
                $encrypted = Crypt::encryptString($string);
                Mail::send('dentusotp', compact('data'), function ($message) use ($data) {
                    $message->to($data['to'])
                            ->subject($data['subject']);
                });
                $msg = 'Dear User,Your login OTP for using Dentist platform is '.$otp.'. Please do not share this code with anyone. TBASPL';
                $this->otpmsg_sd($user->mobile,$msg,'1705174825127580471','','582582'); 
            }
        }
        return redirect()->back()->with('success','OTP resend successfully!');
    }

    function check2(Request $request){
         $request->validate([
            'name'=>'required',
            'email' => [
                    'required',
                    Rule::unique('users')->where(function ($query) {
                        return $query->whereNOTIN('status', [2]);
                    }),
                ],
            'mobile' => [
                    'required',
                    Rule::unique('users')->where(function ($query) {
                        return $query->whereNOTIN('status', [2]);
                    }),
                ],
            'password'=>'required|min:6|max:30',
        ]);
        $otp = mt_rand(100000, 999900);
        $user_message = "One Time Password ".$otp." to verify your Mobile on Goyal's Online Support";
        $country_code = '+91';
        $country = '';
        if ($request->country_code) {
            $country_code = $request->country_code;
        }
        if ($request->country) {
            $country = $request->country;
        }
        $phone = $country_code.$request->mobile;
        $msg = $user_message;
        $string = $otp.'|'.$request->email.'|'.$request->mobile.'|'.$request->password.'|'.$request->name.'|'.$country_code.'|'.$country;
        
        $encrypted = Crypt::encryptString($string);
        // $otpmsg = $this->otpmsg_sd($phone,$msg,$temp_id,$entity_id);
        // $this->twofactorsms($otp,$phone);
        $data = [
            'to' => $request->email,
            'subject' => "Dentus Verification Code",
            'name' => $request->name,
            'generalsettings' => Generalsetting::find(1),
            'otp' => $otp
        ];
        Mail::send('dentusotp', compact('data'), function ($message) use ($data) {
            $message->to($data['to'])
                    ->subject($data['subject']);
        });
        $msg = 'Dear User,Your login OTP for using Dentist platform is '.$otp.'. Please do not share this code with anyone. TBASPL';
        $this->otpmsg_sd($request->mobile,$msg,'1705174825127580471','','582582'); 
        return redirect()->route('admin.otpforsignup',$encrypted);

    }

    public function otpforsignup(Request $request, $token=null){
        if ($token == '') {
            return redirect()->back()->with('fail','Something error happen plz try again!');
        }
        $title = "Otp";
         $decrypted = Crypt::decryptString($request->token);
        $a1 = explode('|', $decrypted);
        return view('doctor.otpforsignup',compact('title','token','a1'));
    }

    public function otpverifysignup (Request $request)
    {
        $request->validate([
                'token'=>'required',
                'otp'=>'required',
        ]);
        $decrypted = Crypt::decryptString($request->token);
        $a1 = explode('|', $decrypted);
        if ($a1[0] == $request->otp) {
             $data['ip_address'] = request()->ip();
             $checkinvitation = Invitation::where('email',$a1[1])->where('mobile',$a1[2])->where('status',0)->first();
             if ($checkinvitation) {
                $parent_id = $checkinvitation->doctor_id;
             }
             $m = new User();
             $m->email = $a1[1];
             $m->password = Hash::make($a1[3]);
             $m->status = 3;
             $m->parent_id = $parent_id ?? 0;
             $m->name = $a1[4];
             $m->mobile = $a1[2];
             $m->save();
             $m->qrcode = $this->QrcodeCreation($m->id);
             $m->save();
             if ($checkinvitation) {
                $checkinvitation->user_id = $m->id;
                $checkinvitation->status = 1;
                $checkinvitation->save();
            }
            $creds = [
                'email' => $m->email,
                'password' => $a1[3],
            ];
            if (Auth::guard('admin')->attempt($creds)) {
                return redirect()->route('admin.user.myprofiledetail',$m->id)->with('success','Registration successfully Plz complete your profile thanks!!');
            }else{
                return redirect()->back()->with('fail','Something error happen please try again later!!');
            }
        }
        else {
            return redirect()->back()->with('fail','OTP Not match kindly submit correct otp!');
        }
    }

    
    public function resendotpforsignup (Request $request, $token=null)
    {
        if ($token == '') {
            return redirect()->back();
        }
        $decrypted = Crypt::decryptString($request->token);
        $a1 = explode('|', $decrypted);
        if ($a1[0]) {
            $otp = $a1[0];
            $data = [
                'to' => $a1[1],
                'subject' => "Dentus Verification Code",
                'name' => $a1[4],
                'generalsettings' => Generalsetting::find(1),
                'otp' => $otp
            ];
            Mail::send('dentusotp', compact('data'), function ($message) use ($data) {
                $message->to($data['to'])
                        ->subject($data['subject']);
            });
            $msg = 'Dear User,Your login OTP for using Dentist platform is '.$otp.'. Please do not share this code with anyone. TBASPL';
            $this->otpmsg_sd($a1[2],$msg,'1705174825127580471','','582582');
        }
        return redirect()->back()->with('success','OTP resend successfully!');
    }

    public function QrcodeCreation($id)
    {
        $encryptedString = Crypt::encrypt($id);
        $qrCode = Builder::create()
                ->writer(new PngWriter())
                ->data($encryptedString)
                ->size(300)
                ->margin(10)
                ->backgroundColor(new Color(255, 255, 255))
                ->foregroundColor(new Color(0, 0, 255))
                ->logoPath('content/Artboard.png')
                ->logoResizeToWidth(50)
                ->build();
        $name = rand().'_qr_' . time() .'.png';
        $qrCode->saveToFile('content/doctor/qrcode/' . $name);
        return $name;
    }
}
