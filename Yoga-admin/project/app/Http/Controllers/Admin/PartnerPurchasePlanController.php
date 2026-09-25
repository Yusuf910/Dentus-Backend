<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PartnerPurchasePlan;
use App\Models\UserPackage;
use App\Models\PartnerPurchasePlanTransaction;



class PartnerPurchasePlanController extends Controller
{

    // private $razorpay;


    // public function __construct()
    // {
    //     $this->razorpay = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    // }



 

    
    public function partner_purchase_history()
    {
        // $packages = UserPackage::all();
        // return view('admin.user_packages.index', compact('packages'));

        $data = PartnerPurchasePlanTransaction::orderBy('id', 'DESC')->get();
        $addbutton = 'Add Partner Purchase Plan Transaction';
        $importbutton = 'Upload Excel';
        $title = 'Partner Purchase Plan Transaction List';
        $listurl = route('admin.user_purchase_plans');
        $addurl = route('admin.store_user_purchase_plans');
        $editurl = 'admin.edit_user_purchase_plans';
        $destroyurl = route('admin.destroy_user_purchase_plans');
        return view('admin.partner_purchase_plans.purchase_history', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'editurl', 'destroyurl'));
   

    }
    
    public function user_purchase_plans()
    {
        // $packages = UserPackage::all();
        // return view('admin.user_packages.index', compact('packages'));

        $data = UserPackage::whereNOTIN('status', [2])->orderBy('id', 'DESC')->get();
        $addbutton = 'Add User Purchase Plan';
        $importbutton = 'Upload Excel';
        $title = 'User Purchase Plan List';
        $listurl = route('admin.user_purchase_plans');
        $addurl = route('admin.store_user_purchase_plans');
        $editurl = 'admin.edit_user_purchase_plans';
        $destroyurl = route('admin.destroy_user_purchase_plans');
        return view('admin.user_packages.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'editurl', 'destroyurl'));
   

    }

    // Show the form for creating a new package
    public function create_user_purchase_plan()
    {
        return view('admin.user_packages.create');
    }

    // Store a newly created package in the database
    public function store_user_purchase_plans(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:500',
            'price' => 'required|numeric',
            'discount_price' => 'required|numeric',
            'mode' => 'required|integer',
            'benefits' => 'array',
            'benefits.*' => 'string|max:255',
            'description' => 'required|string',
            'status' => 'required|integer',
        ]);
    
        $data = $request->all();
        $data['benefits'] = json_encode(['features' => $request->benefits]); // Convert benefits to JSON
    
        UserPackage::create($data);
    

        return redirect()->route('admin.user_purchase_plans')->with('success', 'Package created successfully.');
    }

    // Show the form for editing the specified package
    public function edit_user_purchase_plans($id)
    {
        // $package = UserPackage::findOrFail($id);
        // return view('user_packages.edit', compact('package'));


        $title = 'Edit User Purchase Plan';
        $listname = 'User Purchase Plan';
        $listurl = route('admin.user_purchase_plans');
        $editurl = 'admin.update_user_purchase_plans';
        $a = UserPackage::find($id);
        if ($a) {
            return view('admin.user_packages.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.user_purchase_plans')
                ->with('failure', 'Not found any data');
        }


    }

    // Update the specified package in the database
    public function update_user_purchase_plans(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:500',
            'price' => 'required|numeric',
            'discount_price' => 'required|numeric',
            'mode' => 'required|integer',
            'benefits' => 'array',
            'benefits.*' => 'string|max:255',
            'description' => 'required|string',
            'status' => 'required|integer',
        ]);
    
        $data = $request->all();
        $data['benefits'] = json_encode(['features' => $request->benefits]);
    
        $package = UserPackage::findOrFail($id);
        $package->update($data);

        return redirect()->route('admin.user_purchase_plans')->with('success', 'Package updated successfully.');
    }

    // Remove the specified package from the database
    public function destroy_user_purchase_plans(Request $request)
    {
        $package = UserPackage::findOrFail($request->id);
        $package->delete();

        return redirect()->route('user_purchase_plans')->with('success', 'Package deleted successfully.');
    }



    public function index()
    {
        $data = PartnerPurchasePlan::whereNOTIN('status', [2])->orderBy('id', 'DESC')->get();
        $addbutton = 'Add Partner Purchase Plan';
        $importbutton = 'Upload Excel';
        $title = 'Partner Purchase Plan List';
        $listurl = route('admin.partner_purchase_plans');
        $addurl = route('admin.store_partner_purchase_plans');
        $editurl = 'admin.edit_partner_purchase_plans';
        $destroyurl = route('admin.destroy_partner_purchase_plans');
        return view('admin.partner_purchase_plans.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'editurl', 'destroyurl'));
   


    }


    public function store_partner_purchase_plans(Request $request)
    {

        // $a = new PartnerPurchasePlan();
        // $a->name = $request->name;
        // $a->status = $request->status;
        // $a->save();
        // return redirect()->route('admin.master.partner_purchase_plans')
        //     ->with('success', 'HomeService created successfully');


            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'duration' => 'required|string|max:50',
                'product_limit' => 'nullable|integer',
                'store_limit' => 'nullable|integer',
                'gift_card_count' => 'nullable|integer',
                'referral_bonus' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'status' => 'nullable|integer',
            ]);
    
            PartnerPurchasePlan::create($validated);
    
            return redirect()->route('admin.partner_purchase_plans')
                ->with('success', 'Plan created successfully');

    }


    public function edit_partner_purchase_plans($id)
    {
        $title = 'Edit Partner Purchase Plan';
        $listname = 'Partner Purchase Plan';
        $listurl = route('admin.partner_purchase_plans');
        $editurl = 'admin.update_partner_purchase_plans';
        $a = PartnerPurchasePlan::find($id);
        if ($a) {
            return view('admin.partner_purchase_plans.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.partner_purchase_plans')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_partner_purchase_plans(Request $request, $id)
    {



        // print_r($tags); die;
        $a = PartnerPurchasePlan::find($id);
        $a->name = $request->name;
        $a->status = $request->status;
        $a->price = $request->price;
        $a->duration = $request->duration;
        $a->product_limit = $request->product_limit;
        $a->store_limit = $request->store_limit;
        $a->gift_card_count = $request->gift_card_count;
        $a->referral_bonus = $request->referral_bonus;
        $a->description = $request->description;
        
        $a->save();
        return redirect()->route('admin.partner_purchase_plans')
            ->with('success', 'Partner Purchase Plan updated successfully');
    }

    public function destroy_partner_purchase_plans(Request $request)
    {
        $input = $request->all();
        $a = PartnerPurchasePlan::find($input['id']);
        $a->status = 2;
        $a->save();
        // DB::table('ask_a_question_plans')->where('id',$input['id'])->delete();
        return redirect()->route('admin.master.partner_purchase_plans')
            ->with('success', 'Home Service deleted successfully');
    }




}
