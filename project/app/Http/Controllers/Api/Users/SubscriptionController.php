<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\PremiumFeature;
use App\Models\UserPremiumAddon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class SubscriptionController extends Controller
{
    public function chooseSubscription(Request $request)
    {
        // Validate input
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'premium_features' => 'array',
            'premium_features.*' => 'exists:premium_features,id',
        ]);
        
        // Calculate total price (subscription + premium features)
        $subscription = Subscription::find($request->subscription_id);
        $premiumFeatures = $request->premium_features ?? [];
        $premiumTotal = PremiumFeature::whereIn('id', $premiumFeatures)->sum('price');

        $totalAmount = $subscription->price + $premiumTotal;
        // Create a payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $totalAmount,
            'payment_status' => 'pending', // Assume payment is pending until confirmed
            'payment_method' => 'credit_card', // Placeholder
        ]);

        // Create a user subscription entry
        $userSubscription = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'payment_id' => $payment->id,
            'status'=>1
        ]);

        // Add premium features to user
        foreach ($premiumFeatures as $featureId) {
            UserPremiumAddon::create([
                'user_id' => Auth::id(),
                'subscription_id' => $subscription->id,
                'premium_feature_id' => $featureId,
                'payment_id' => $payment->id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription purchased successfully!',
            'subscription' => $userSubscription,
        ]);
    }

    public function subscribelist(Request $request)
    {
        $l = Subscription::where('status',1)->with('features')->orderBy('position','ASC')->get();
        $p = PremiumFeature::/*where('status',1)->*/orderBy('name','ASC')->get();

        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l,'premium'=>$p];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 422);
        }
    }

}
