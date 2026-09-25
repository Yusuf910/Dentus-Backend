<?php
// app/Http/Controllers/Api/LoginController.php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // Handle the login request
    public function login(Request $request)
    {
        // echo "ccddcdc"; die;
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password matches
        if ($user && Hash::check($request->password, $user->password)) {
            // Return a success response with user details
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful!',
                'data' => [
                    'user_id' => $user->user_id,
                    'name' => $user->first_name,
                    'email' => $user->email,
                    'mobile_number' => $user->mobile_number,
                    'token' => $token,
                ]
            ], 200);
        }

        // If credentials are invalid, return an error
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials!'
        ], 401);
    }
}
