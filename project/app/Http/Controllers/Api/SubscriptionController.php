<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\SubscriptionFeatureMapping;
use App\Models\Payment;
use App\Models\PremiumFeature;
use App\Models\UserPremiumAddon;
use App\Models\SubscriptionFeatures;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;
class SubscriptionController extends Controller
{
    public function chooseSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'premium_features' => 'array',
            // 'premium_features.*' => 'exists:premium_features,id',
            'amount' => 'required',
            'tax_amount' => 'required',
            'extra_amount'=>'required'
            
        ]);
        $alreadySubscribed = UserSubscription::where('user_id', Auth::id())
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            return response()->json([
                'status' => false,
                'msg' => 'You have already purchased this subscription.'
            ], 409); // HTTP 409 Conflict
        }
        $subscription = Subscription::find($request->subscription_id);
        $premiumFeatures = $request->premium_features ?? [];
        $premium = SubscriptionFeatureMapping::whereIn('id', $premiumFeatures)->get();
        
        // $totalAmount = $subscription->price + $premiumTotal;
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'tax_amount' => $request->tax_amount,
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'extra_amount'=>$request->extra_amount
        ]);

        $userSubscription = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'payment_id' => $payment->id,
            'status'=>1
        ]);

        foreach ($premium as $featureId) {
            $h = UserPremiumAddon::create([
                'user_id' => Auth::id(),
                'subscription_id' => $userSubscription->id,
                'premium_feature_id' => $featureId->id,
                'payment_id' => $payment->id,
                'quantity'=>$featureId->quantity,
                'price'=>$featureId->price,
                'unlimited'=>$featureId->unlimited,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription purchased successfully!',
            'subscription' => $userSubscription,
        ]);
    }

    public function updatepackage(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'premium_features' => 'array',
            // 'premium_features.*' => 'exists:premium_features,id',
            'amount' => 'required',
            'tax_amount' => 'required',
            'extra_amount'=>'required'
            
        ]);
        $alreadySubscribed = UserSubscription::where('user_id', Auth::id())
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $alreadySubscribed->status = 0;
            $alreadySubscribed->save();

        }
        $subscription = Subscription::find($request->subscription_id);
        $premiumFeatures = $request->premium_features ?? [];
        $premium = SubscriptionFeatureMapping::whereIn('id', $premiumFeatures)->get();
        
        // $totalAmount = $subscription->price + $premiumTotal;
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'tax_amount' => $request->tax_amount,
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'extra_amount'=>$request->extra_amount
        ]);

        $userSubscription = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'payment_id' => $payment->id,
            'status'=>1
        ]);

        foreach ($premium as $featureId) {
            $h = UserPremiumAddon::create([
                'user_id' => Auth::id(),
                'subscription_id' => $userSubscription->id,
                'premium_feature_id' => $featureId->id,
                'payment_id' => $payment->id,
                'quantity'=>$featureId->quantity,
                'price'=>$featureId->price,
                'unlimited'=>$featureId->unlimited,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription purchased successfully!',
            'subscription' => $userSubscription,
        ]);
    }

    public function parentlist(Request $request)
    {
        $l = Subscription::where('status',1)->where('free',0)->where('parent_id',0)->orderBy('position','ASC')->get()->map(function ($subscription) {
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
        $l = Subscription::where('id',$request->id)->where('status',1)->with(['features', 'masterTax'])->orderBy('position','ASC')->get();
        $p = PremiumFeature::where('status',1)->where('subcription_id',$request->id)->orderBy('name','ASC')->get();

        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l,'premium'=>$p];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 422);
        }
    }

    public function upgradeparentlist(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Sorry ! No data found.']; 
        $l = Subscription::where('status',1)/*->where('free',0)*/->where('parent_id',0)->with(['features', 'masterTax'])->orderBy('position','ASC')->get()->map(function ($subscription) {
                $hasChildren = Subscription::where('parent_id', $subscription->id)->where('status',1)->exists();
                $subscription->has_children = $hasChildren ? 1 : 0;
                return $subscription;
            });
        if ($l->count() > 0) {
            foreach ($l as $key => $v) {
                $checktake = UserSubscription::where('user_id',auth()->user()->id)->where('status',1)->where('subscription_id',$v->id)->first();
                if ($checktake) {
                    unset($l[$key]);
                }
            }
            $l = $l->values();
            if(!empty($l)) {

                $p = SubscriptionFeatures::where('status',1)->orderBy('name','ASC')->get();
                $response = ['status' => true, 'msg' => 'l List', 'data' => $l,'premium'=>$p];   
                return response($response, 200);
            }
        }
        return response($response, 200);
    }

    public function upgradeSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'premium_features' => 'array',
            'premium_features.*' => 'exists:premium_features,id',
            'amount' => 'required',
            'tax_amount' => 'required',
            
        ]);
        $checktake = UserSubscription::where('user_id',auth()->user()->id)->where('status',1)->first();
        
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
        if ($checktake) {
            $checktake->status = 0;
            $checktake->save();
            $new = UserSubscription::where('id',$userSubscription->id)->first();
            $new->upgrade_from = $checktake->id;
            $new->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Subscription purchased successfully!',
            'subscription' => $userSubscription,
        ]);
    }

    public function selfpackage_create(Request $request)
    {
        $response = ['status' => false];
        $request->validate([
            'name' => 'required',
            'premium_features' => 'array',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'discount_price' => 'required',
            'tax_id' => 'required',
            'validity' => 'required',
            'type' => 'required',
        ]);
        
        $a = new PackTreatment();
        $a->user_id = auth()->user()->id;
        $a->user_type = 2;
        $a->name = $request->name;
        $a->description = $request->description;
        $a->price = $request->price;
        $a->discount_price = $request->discount_price;
        $a->tax_id = $request->tax_id;
        $a->validity = $request->validity;
        $a->type = $request->type;
        $a->tax_id = $request->tax_id;
        $a->status = 1;

        $a->save();
        if ($a) {
            $premiumFeatures = $request->premium_features ?? [];

            foreach ($premiumFeatures as $featureId) {
                $ab = new PackFeature();
                $ab->pack_id = $a->id;
                $ab->name = $featureId['name'];
                $ab->treatment_id = $featureId['treatment_id'];
                $ab->status = 1;
                $ab->quantity = $featureId['quantity'];
                $ab->save();
                
            }
            $response = [
                    'status' => true,
                    'message' => 'Subscription purchased successfully!',
                    'subscription' => $a,
                ];
        }

        

        return response()->json($response);
    }

    public function selfpackage_update(Request $request)
    {
        $response = ['status' => false];

        $request->validate([
            'name' => 'required',
            'premium_features' => 'array',
            'description' => 'required',
            'price' => 'required',
            'discount_price' => 'required',
            'tax_id' => 'required',
            'validity' => 'required',
            'type' => 'required',
        ]);

        if ($request->has('id') && !empty($request->id)) {
            $a = PackTreatment::find($request->id);
            if (!$a) {
                return response()->json(['status' => false, 'message' => 'PackTreatment not found'], 404);
            }
        } else {
            $a = new PackTreatment();
            $a->user_id = auth()->user()->id;
            $a->user_type = 2;
            $a->status = 1;
        }

        $a->name = $request->name;
        $a->description = $request->description;
        $a->price = $request->price;
        $a->discount_price = $request->discount_price;
        $a->tax_id = $request->tax_id;
        $a->validity = $request->validity;
        $a->type = $request->type;
        $a->tax_id = $request->tax_id;

        $a->save();

        if ($a) {
            $premiumFeatures = $request->premium_features ?? [];

            if ($request->has('id')) {
                PackFeature::where('pack_id', $a->id)->update(['status' => 2]);
            }

            foreach ($premiumFeatures as $feature) {
                $existingFeature = PackFeature::where('pack_id', $a->id)
                ->where('treatment_id', $feature['treatment_id'])
                ->first();

                if ($existingFeature) {
                    $existingFeature->name = $feature['name'];
                    $existingFeature->quantity = $feature['quantity'];
                    $existingFeature->status = 1; // Set status to active
                    $existingFeature->save();
                } else {
                    // Insert new feature if not found
                    $ab = new PackFeature();
                    $ab->pack_id = $a->id;
                    $ab->name = $feature['name'];
                    $ab->treatment_id = $feature['treatment_id'];
                    $ab->status = 1;
                    $ab->quantity = $feature['quantity'];
                    $ab->save();
                }
            }

            $response = [
                'status' => true,
                'message' => $request->has('id') ? 'PackTreatment updated successfully!' : 'PackTreatment created successfully!',
                'subscription' => $a,
            ];
        }

        return response()->json($response);
    }


    public function selfpackage(Request $request)
    {
        $totalquantity = $this->getquantity(16);
        $treatments = PackTreatment::where('user_id', auth()->user()->id)->where('user_type',2)->whereIN('status',[0,1])
        ->with('featurelist')
        ->withCount(['subscriptions as purchase_count' => function ($query) {
            // $query->where('status', 1); // Count only active purchases
        }])
        ->orderBy('status','DESC')
        ->get();
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($treatments);
        }
        elseif ($totalquantity < 0) {
           $canaddchild = 1;
        }
        return response()->json([
            'success' => true,
            'message' => 'Pack Treatments retrieved successfully!',
            'data' => $treatments,
            'canaddchild'=>$canaddchild
        ], 200);
    }

    public function selfpackage_delete(Request $request)
    {
        $a = PackTreatment::where('id', $request->id)->where('status',1)
        ->with('featurelist')
        ->withCount(['subscriptions as purchase_count' => function ($query) {
            // $query->where('status', 1); // Count only active purchases
        }])
        ->first();
        if ($a) {
            if ($a->purchase_count == 0) {
                $a->status = 2;
                $a->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Pack Treatments retrieved successfully!',
                ], 200);
            }
            else {
                return response()->json([
                        'success' => false,
                        'message' => 'user purchased this package',
                    ], 200);
            }
        } else {
            return response()->json([
                    'success' => false,
                    'message' => 'Not found!',
                ], 200);
        }
        
    }

    public function getquantity($for)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

            foreach ($subscription->features as $feature) {
                if ($feature->id == $for) {
                    $pivotId = $feature->pivot->id;
                    $addon = $premiumAddons->get($pivotId);

                    if ($alreadySubscribed->subscription_id == 9) {
                        if ($addon) {
                            if ($addon->unlimited == 1) {
                              $feature12Quantity = -1;  
                            } else
                            $feature12Quantity = $addon->quantity;
                        } else {
                            $feature12Quantity = $feature->pivot->basic_quantity ?? 0;
                        }
                    } else {
                        $feature12Quantity = $feature->pivot->quantity ?? 0;
                    }

                    break;
                }
            }
        }
        return $feature12Quantity;
    }

    public function selfpackageactiveinactive(Request $request)
    {
        $response = ['status' => false];

        $a = PackTreatment::find($request->id);
        if (!$a) {
            return response()->json(['status' => false, 'message' => 'PackTreatment not found'], 404);
        }

        $a->status = $request->status;
        $a->save();

        $response = [
            'status' => true,
            'subscription' => $a,
        ];

        return response()->json($response);
    }

}
