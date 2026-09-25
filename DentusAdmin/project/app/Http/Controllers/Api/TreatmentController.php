<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\TreatmentDoctor;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    // Store a new treatment
    public function store(Request $request)
    {
        $request->validate([
            'treatment_name' => 'required|string|max:255',
            'average_duration' => 'required|integer',
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
    public function show($id)
    {
        $treatment = Treatment::with('doctors')->find($id);

        if (!$treatment) {
            return response()->json(['message' => 'Treatment not found'], 404);
        }

        return response()->json($treatment);
    }

    // Update an existing treatment
    public function update(Request $request, $id)
    {
        $treatment = Treatment::find($id);

        if (!$treatment) {
            return response()->json(['message' => 'Treatment not found'], 404);
        }

        $request->validate([
            'treatment_name' => 'required|string|max:255',
            'average_duration' => 'required|integer',
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
        ]);

        // Update assigned doctors
        TreatmentDoctor::where('treatment_id', $id)->delete(); // remove previous assignments
        foreach ($request->doctors as $doctorId) {
            TreatmentDoctor::create([
                'treatment_id' => $id,
                'doctor_id' => $doctorId,
            ]);
        }

        return response()->json(['status' => true,'message' => 'Treatment updated successfully']);
    }

    // Delete a treatment
    public function destroy($id)
    {
        $treatment = Treatment::find($id);

        if (!$treatment) {
            return response()->json(['message' => 'Treatment not found'], 404);
        }

        $treatment->delete();

        return response()->json(['status' => true,'message' => 'Treatment deleted successfully']);
    }
}

