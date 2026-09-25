<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use thiagoalessio\TesseractOCR\TesseractOCR;

class UserController extends Controller
{
    public function create(Request $request)
    {
        // Validate Inputs
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|regex:/^[0-9]{10}$/|unique:users,mobile', // Adjust regex as needed for your format
            'password' => 'required|min:5|max:30',
            // 'cpassword' => 'required|min:5|max:30|same:password'
        ]);

        session()->put('name', $request->name);
        session()->put('email', $request->email);
        session()->put('mobile', $request->mobile);
        session()->put('password', $request->password);

        // $user = new User();
        // $user->name = $request->name;
        // $user->email = $request->email;
        // $user->mobile = $request->mobile;
        // $user->status = $request->status;
        // $user->password = \Hash::make($request->password);
        // $save = $user->save();

       return redirect()->route('user.signup_otp_page')->with('success','Otp sent successfully');

    }



    function signup_otp_page()
    {

        return view('admin.signup_otp');
    }


    public function signup_otpmatch(Request $request)
    {
        // Validate the OTP input
        $this->validate($request, [
            'otp' => 'required|array|min:4|max:4', // Ensure it's an array with exactly 4 elements
            'otp.*' => 'required|digits_between:0,9', // Each element should be a single digit
        ]);

        // Combine the OTP input into a single string
        $otpInput = implode('', $request->otp);
        $match = '1234'; // This should be the actual OTP you're validating against

        // Check if the entered OTP matches the expected OTP
        if ($match === $otpInput) {
            // Create a new user entry
            $entry = new User();
            $entry->name = session('name');
            $entry->email = session('email');
            $entry->mobile = session('mobile'); // Changed from phone to mobile for consistency
            $entry->password = \Hash::make(session('password')); // Hash the password
            $entry->save();

            // Attempt to log in the user
            if (Auth::guard('web')->attempt(['email' => $entry->email, 'password' => session('password')])) {
                // If successful, redirect to user home
                return redirect()->route('user.home');
            }

            // If login fails, redirect to login page
            return redirect()->route('user.login')->with('danger', 'Login failed');
        } else {
            // If OTP doesn't match, redirect back with an error message
            return redirect()->route('signup_otp_page')->with('danger', 'Wrong OTP');
        }
    }




    function check(Request $request){
        //Validate inputs
        $request->validate([
           'email'=>'required|email|exists:users,email',
           'password'=>'required|min:5|max:30'
        ],[
            'email.exists'=>'This email is not exists on users table'
        ]);

        $creds = $request->only('email','password');
        if( Auth::guard('web')->attempt($creds) ){
            return redirect()->route('user.home');
        }else{
            return redirect()->route('user.login')->with('fail','Incorrect credentials');
        }
    }

    // public function check(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Check user credentials manually for debugging
    //     $user = \App\Models\User::where('email', $request->email)->first();

    //     if ($user && Hash::check($request->password, $user->password)) {
    //         Auth::login($user); // Log in the user manually
    //         return redirect()->route('user.home');
    //     }

    //     return back()->withErrors([
    //         'email' => 'The provided credentials do not match our records.',
    //     ]);
    // }

    function logout(){
        Auth::guard('web')->logout();
        return redirect()->route('user.login');
    }

    function hi()  {
        phpinfo();
        // $tesseract = new TesseractOCR('/path/to/image.png');

        // // Recognize text from image
        // $text = $tesseract->run();

        // // Output result
        // echo $text;
    }
}
