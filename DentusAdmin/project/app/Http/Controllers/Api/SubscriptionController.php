<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\PremiumFeature;
use App\Models\UserPremiumAddon;
use App\Models\SubscriptionFeatures;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class SubscriptionController extends Controller
{
    public function chooseSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'premium_features' => 'array',
            'premium_features.*' => 'exists:premium_features,id',
            'amount' => 'required',
            'tax_amount' => 'required',
            
        ]);
        
        $subscription = Subscription::find($request->subscription_id);
        $premiumFeatures = $request->premium_features ?? [];
        $premiumTotal = PremiumFeature::whereIn('id', $premiumFeatures)->sum('price');
        
        $totalAmount = $subscription->price + $premiumTotal;
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'tax_amount' => $request->tax_amount,
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
        ]);

        $userSubscription = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'payment_id' => $payment->id,
            'status'=>1
        ]);

        foreach ($premiumFeatures as $featureId) {
            $h = UserPremiumAddon::create([
                'user_id' => Auth::id(),
                'subscription_id' => $userSubscription->id,
                'premium_feature_id' => $featureId,
                'payment_id' => $payment->id,
            ]);
            if ($h && $h->premium_feature_id == 1) {
                $h->quantity = $request->quantity;
                $h->save();
                // auth()->user()->add_child = auth()->user()->add_child+$request->quantity;
                // auth()->user()->save();
            
            }

        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription purchased successfully!',
            'subscription' => $userSubscription,
        ]);
    }

    public function parentlist(Request $request)
    {
        $l = Subscription::where('status',1)->where('parent_id',0)->orderBy('position','ASC')->get()->map(function ($subscription) {
                $hasChildren = Subscription::where('parent_id', $subscription->id)->where('status',1)->exists();
                $subscription->has_children = $hasChildren ? 1 : 0;
                return $subscription;
            });
        $p = SubscriptionFeatures::where('status',1)->orderBy('name','ASC')->get();

        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l,'premium'=>$p];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 422);
        }
    }

    public function PackageDetail(Request $request)
    {
        $l = Subscription::where('parent_id',$request->id)->where('status',1)->with(['features', 'masterTax'])->orderBy('position','ASC')->get();
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
