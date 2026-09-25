<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageTreatment;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    // Create a new package
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'validity_period' => 'required|integer',
            'base_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'treatments' => 'required|array', // Treatments IDs and sessions
            'treatments.*.id' => 'required|integer', // Treatment ID
            'treatments.*.sessions' => 'required|integer', // Number of sessions for the treatment
        ]);

        // Create the package
        $package = Package::create([
            'doctor_id' => auth()->user()->id, // The main doctor
            'package_name' => $request->package_name,
            'validity_period' => $request->validity_period, // in months
            'base_price' => $request->base_price,
            'discount_price' => $request->discount_price,
        ]);

        // Assign treatments to the package
        foreach ($request->treatments as $treatment) {
            PackageTreatment::create([
                'package_id' => $package->id,
                'treatment_id' => $treatment['id'],
                'sessions' => $treatment['sessions'],
            ]);
        }

        return response()->json(['status' => true,'message' => 'Package created successfully']);
    }

    // List all packages
    public function index()
    {
        $packages = Package::with('treatments')->get();

        return response()->json($packages);
    }

    // Show a single package
    public function show($id)
    {
        $package = Package::with('treatments')->find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        return response()->json($package);
    }

    // Update a package
    public function update(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $request->validate([
            'package_name' => 'required|string|max:255',
            'validity_period' => 'required|integer',
            'base_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'treatments' => 'required|array', // Treatments IDs and sessions
            'treatments.*.id' => 'required|integer',
            'treatments.*.sessions' => 'required|integer',
        ]);

        // Update the package
        $package->update([
            'package_name' => $request->package_name,
            'validity_period' => $request->validity_period,
            'base_price' => $request->base_price,
            'discount_price' => $request->discount_price,
        ]);

        // Update the treatments associated with the package
        PackageTreatment::where('package_id', $id)->delete(); // Remove old treatments
        foreach ($request->treatments as $treatment) {
            PackageTreatment::create([
                'package_id' => $id,
                'treatment_id' => $treatment['id'],
                'sessions' => $treatment['sessions'],
            ]);
        }

        return response()->json(['status' => true,'message' => 'Package updated successfully']);
    }

    // Delete a package
    public function destroy($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $package->delete();

        return response()->json(['status' => true,'message' => 'Package deleted successfully']);
    }
}
