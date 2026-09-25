<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\TreatmentDoctor;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
class TreatmentController extends Controller
{
    // Store a new treatment
    public function store(Request $request)
    {
        $request->validate([
            'treatment_name' => 'required|string|max:255',
            'average_duration' => 'required|string',
            'call_before_confirmation' => 'required|boolean',
            'instructions' => 'nullable|string',
            'treatment_fees' => 'required|numeric',
            'doctors' => 'required|array', // doctors IDs
        ]);

        // Create the treatment
        $treatment = Treatment::create([
            'doctor_id' => auth()->user()->id, // assuming the logged-in user is the main doctor
            'treatment_name' => $request->treatment_name,
            'average_duration' => $request->average_duration,
            'call_before_confirmation' => $request->call_before_confirmation,
            'instructions' => $request->instructions,
            'treatment_fees' => $request->treatment_fees,
            'tax_id' => $request->tax_id,
        ]);

        // Assign doctors to the treatment
        foreach ($request->doctors as $doctorId) {
            TreatmentDoctor::create([
                'treatment_id' => $treatment->id,
                'doctor_id' => $doctorId,
            ]);
        }

        return response()->json(['status' => true,'message' => 'Treatment created successfully']);
    }

    // Show a single treatment
    public function show()
    {
        $totalquantity = $this->getquantity(19);
        
        $treatment_list = Treatment::where('doctor_id',auth()->user()->id)->where('status',1)->get();
        if(!empty($treatment_list)) {
            foreach ($treatment_list as $key) {
                $treamentdoctor = TreatmentDoctor::where('treatment_id',$key->id)->with('doctor')->get();
                $key->doctorlist = $treamentdoctor;
                // dd($key);
            }
            // $canaddchild = 0;
            // if ($totalquantity > 0) {
            //    $canaddchild = $totalquantity-count($treatment_list);
            // }
            // elseif ($totalquantity < 0) {
            //    $canaddchild = 1;
            // }
            $canaddchild = 1;
            $response = ['status' => true, 'msg' => 'l List', 'data' => $treatment_list,'canaddchild'=>$canaddchild];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No  found.'];
            return response($response, 200);
        }
        //  $treatment = Treatment::with('doctors')->find($treatment_list->id);


    }


    // Update an existing treatment
    public function update(Request $request)
    {
        $treatment = Treatment::find($request->treatment_id);

        if (!$treatment) {
            return response()->json(['message' => 'Treatment not found'], 404);
        }

        $request->validate([
            'treatment_name' => 'required|string|max:255',
            'average_duration' => 'required|string',
            'call_before_confirmation' => 'required|boolean',
            'instructions' => 'nullable|string',
            'treatment_fees' => 'required|numeric',
            'doctors' => 'required|array', // doctors IDs
        ]);

        // Update the treatment
        $treatment->update([
            'treatment_name' => $request->treatment_name,
            'average_duration' => $request->average_duration,
            'call_before_confirmation' => $request->call_before_confirmation,
            'instructions' => $request->instructions,
            'treatment_fees' => $request->treatment_fees,
            'tax_id' => $request->tax_id,
        ]);

        // Update assigned doctors
        TreatmentDoctor::where('treatment_id', $request->treatment_id)->delete(); // remove previous assignments
        foreach ($request->doctors as $doctorId) {
            TreatmentDoctor::create([
                'treatment_id' => $request->treatment_id,
                'doctor_id' => $doctorId,
            ]);
        }

        return response()->json(['status' => true,'message' => 'Treatment updated successfully']);
    }

    // Delete a treatment
    public function destroy($id)
{
    // Find the treatment with the related doctors
    $treatment = Treatment::with('doctors')->find($id);

    if (!$treatment) {
        return response()->json(['message' => 'Treatment not found'], 404);
    }

    // Delete the treatment and related records in treatment_doctors will be automatically removed
    $treatment->delete();

    return response()->json(['status' => true, 'message' => 'Treatment deleted successfully']);
}


    public function doctor_list(Request $request) {
        // Get authenticated user's ID
        $userId = auth()->user()->id;

        // Query to get doctors who match the status and either the user's ID or parent's ID
        $doctors = User::where('status', 1)
            ->where(function($query) use ($userId) {
                // Check if either the doctor is the authenticated user or if their parent_id matches the authenticated user's ID
                $query->where('id', $userId)
                    ->orWhere('parent_id', $userId);
            })
            ->orderBy('id', 'ASC') // Order by doctor ID in ascending order
            ->get(); // Get the results

        // Check if any doctors were found
        if (!$doctors->isEmpty()) {
            // Return success response with the list of doctors
            $response = ['status' => true, 'msg' => 'Doctor List', 'data' => $doctors];
            return response($response, 200);
        } else {
            // Return error response if no doctors were found
            $response = ['status' => false, 'msg' => 'Sorry! No Doctor found.'];
            return response($response, 422);
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

}

