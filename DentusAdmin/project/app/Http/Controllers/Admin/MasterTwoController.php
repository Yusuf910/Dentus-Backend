<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupan;
use App\Models\WalletPlan;
use App\Models\Mantra;
use App\Models\ThreadColor;
use App\Models\MasterDonation;
use App\Models\Rudrakshi;
use App\Models\SemiPreciousStone;
use App\Models\PreciousStone;

use Illuminate\Support\Str;
use DB;
use Illuminate\Validation\Rule;
class MasterTwoController extends Controller
{
    //coupans
    public function coupans()
    {
        $data = Coupan::whereNOTIN('status',[2])->orderBy('updated_at','ASC')->get();
        $addbutton = 'Add Coupan';
        $importbutton = 'Upload Excel';
        $title = 'Coupan';
        $listurl = route('admin.master.coupans');
        $addurl = route('admin.master.create_coupans');
        $importurl = route('admin.master.import_coupans');
        $editurl = 'admin.master.edit_coupans';
        $destroyurl = route('admin.master.destroy_coupans');
        $addurl1 = route('admin.master.store_coupans');
        return view('admin.coupans.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_coupans()
    {
        $title = 'Add Coupan';
        $listname = 'Coupan';
        $listurl = route('admin.master.coupans');
        $addurl = route('admin.master.store_coupans');
        return view('admin.coupans.create',compact('title','listurl','listname','addurl'));
    }

    public function store_coupans(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'code' => 'required',
            'coupon_value' => 'required',
            'discount_type' => 'required',
            'cashback_amount' => 'required',
            'total_coupan' => 'required',
            'start_date' => 'required',
            'expiry_date' => 'required',
            // 'coupan_for' => 'required',
            'status' => 'required',
        ]);


        $a = new Coupan();
        $a->heading  = $request->title;
        $a->code = $request->code;
        $a->amount  = $request->coupon_value;
        $a->discount_type = $request->discount_type;
        $a->cashback_amount = $request->cashback_amount;
        $a->uses_limit = $request->total_coupan;
        $a->start_date = $request->start_date;
        $a->expiry_date = $request->expiry_date;
        // $a->discount_on = $request->coupan_for;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.coupans')
                        ->with('success','Coupan created successfully');
    }


    public function edit_coupans($id)
    {
        $title = 'Edit Coupan';
        $listname = 'Coupan';
        $listurl = route('admin.master.coupans');
        $editurl = 'admin.master.update_coupans';
        $a = Coupan::find($id);
        if ($a)
        {
            return view('admin.coupans.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.coupans')
                        ->with('failure','Not found any data');
        }

    }

    public function update_coupans(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'code' => 'required',
            'coupon_value' => 'required',
            'discount_type' => 'required',
            'cashback_amount' => 'required',
            'total_coupan' => 'required',
            'start_date' => 'required',
            'expiry_date' => 'required',
            // 'coupan_for' => 'required',
            'status' => 'required',
        ]);

        $a = Coupan::find($id);
        $a->heading  = $request->title;
        $a->code = $request->code;
        $a->amount  = $request->coupon_value;
        $a->discount_type = $request->discount_type;
        $a->cashback_amount = $request->cashback_amount;
        $a->uses_limit = $request->total_coupan;
        $a->start_date = $request->start_date;
        $a->expiry_date = $request->expiry_date;
        // $a->discount_on = $request->coupan_for;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.coupans')
                        ->with('success','coupan updated successfully');
    }

    public function destroy_coupans(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('coupans_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.coupans')
        //                 ->with('failure','You can not delete coupans due to linking with book');
        // }
        $a = Coupan::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.coupans')
                        ->with('success','Coupan deleted successfully');
    }

    //walletplan
    public function walletplan()
    {
        $data = WalletPlan::whereNOTIN('status',[2])->orderBy('updated_at','ASC')->get();
        $addbutton = 'Add Wallet Plan';
        $importbutton = 'Upload Excel';
        $title = 'Wallet Plan';
        $listurl = route('admin.master.walletplan');
        $addurl = route('admin.master.create_walletplan');
        $importurl = route('admin.master.import_walletplan');
        $editurl = 'admin.master.edit_walletplan';
        $destroyurl = route('admin.master.destroy_walletplan');
        $addurl1 = route('admin.master.store_walletplan');
        $getcoupan = Coupan::where('status',1)->orderBy('updated_at','DESC')->get();
        return view('admin.walletplan.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1','getcoupan'));
    }

    public function create_walletplan()
    {
        $title = 'Add Wallet Plan';
        $listname = 'Wallet Plan';
        $listurl = route('admin.master.walletplan');
        $addurl = route('admin.master.store_walletplan');
        $getcoupan = Coupan::where('status',1)->orderBy('updated_at','DESC')->first();
        return view('admin.walletplan.create',compact('title','listurl','listname','addurl','getcoupan'));
    }

    public function store_walletplan(Request $request)
    {
        // dd($request->all());
        // $this->validate($request, [
        //     'recharge_amount' => 'required',
        //     'position' => 'required',
        //     'is_for_new_user' => 'required',
        //     'status' => 'required',
        //     // 'coupan_id' => 'required',
        // ]);

        $a = new WalletPlan();
        $a->recharge = $request->recharge_amount;
        $a->position = $request->position;
        $a->for_new_user = $request->for_new_user;
        $a->status = $request->status;
        $a->coupan_id = $request->coupan_id;
        $a->coupon = $request->coupan_id;
        $a->save();
        return redirect()->route('admin.master.walletplan')
                        ->with('success','Wallet Plan created successfully');
    }


    public function edit_walletplan($id)
    {
        $title = 'Edit Wallet Plan';
        $listname = 'Wallet Plan';
        $listurl = route('admin.master.walletplan');
        $editurl = 'admin.master.update_walletplan';
        $a = WalletPlan::find($id);
        if ($a)
        {
            $getcoupan = Coupan::where('status',1)->orderBy('updated_at','DESC')->get();
            return view('admin.walletplan.edit',compact('a','title','listurl','listname','editurl','getcoupan'));
        }
        else
        {
            return redirect()->route('admin.master.walletplan')
                        ->with('failure','Not found any data');
        }

    }

    public function update_walletplan(Request $request, $id)
    {
        $this->validate($request, [
            'recharge_amount' => 'required',
            'position' => 'required',
            'is_for_new_user' => 'required',
            'status' => 'required',
            // 'coupan_id' => 'required',
        ]);

        $a = WalletPlan::find($id);
        $a->recharge = $request->recharge_amount;
        $a->position = $request->position;
        $a->for_new_user = $request->is_for_new_user;
        $a->status = $request->status;
        $a->coupan_id = $request->coupan_id;
        $a->coupon = $request->coupan_id;
        $a->save();
        return redirect()->route('admin.master.walletplan')
                        ->with('success','Wallet Plan updated successfully');
    }

    public function destroy_walletplan(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('walletplan_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.walletplan')
        //                 ->with('failure','You can not delete walletplan due to linking with book');
        // }
        $a = WalletPlan::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.walletplan')
                        ->with('success','Wallet Plan deleted successfully');
    }




     //rudrakshi
     public function rudrakshi()
     {
         $data = Rudrakshi::whereNOTIN('status',[2])->orderBy('position','ASC')->get();
         $addbutton = 'Add Rudrakshi';
         $importbutton = 'Upload Excel';
         $title = 'Rudrakshi';
         $listurl = route('admin.master.rudrakshi');
         $addurl = route('admin.master.create_rudrakshi');
         $importurl = route('admin.master.import_rudrakshi');
         $editurl = 'admin.master.edit_rudrakshi';
         $destroyurl = route('admin.master.destroy_rudrakshi');
         $addurl1 = route('admin.master.store_rudrakshi');
         return view('admin.rudrakshi.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
     }
 
     public function create_rudrakshi()
     {
         $title = 'Add Rudrakshi';
         $listname = 'Rudrakshi';
         $listurl = route('admin.master.rudrakshi');
         $addurl = route('admin.master.store_rudrakshi');
         return view('admin.rudrakshi.create',compact('title','listurl','listname','addurl'));
     }
 
     public function store_rudrakshi(Request $request)
     {
         $this->validate($request, [
             'title' => 'required',
             'position' => 'required',
             'status' => 'required',
         ]);
 
 
         $a = new Rudrakshi();
         $a->title = $request->title;
         $a->description = $request->description;
         $a->position = $request->position;
         $a->status = $request->status;
         $a->save();
         return redirect()->route('admin.master.rudrakshi')
                         ->with('success','Rudrakshi created successfully');
     }
 
 
     public function edit_rudrakshi($id)
     {
         $title = 'Edit Rudrakshi';
         $listname = 'Rudrakshi';
         $listurl = route('admin.master.rudrakshi');
         $editurl = 'admin.master.update_rudrakshi';
         $a = Rudrakshi::find($id);
         if ($a)
         {
             return view('admin.rudrakshi.edit',compact('a','title','listurl','listname','editurl'));
         }
         else
         {
             return redirect()->route('admin.master.rudrakshi')
                         ->with('failure','Not found any data');
         }
 
     }
 
     public function update_rudrakshi(Request $request, $id)
     {
         $this->validate($request, [
             'title' => 'required',
             'position' => 'required',
             'status' => 'required',
         ]);
 
         $a = Rudrakshi::find($id);
         $a->title = $request->title;
         $a->description = $request->description;
         $a->position = $request->position;
         $a->status = $request->status;
         $a->save();
         return redirect()->route('admin.master.rudrakshi')
                         ->with('success','Rudrakshi updated successfully');
     }
 
     public function destroy_rudrakshi(Request $request)
     {
         $input = $request->all();
         // $bookcheck = Blog::where('rudrakshi_id',$input['id'])->whereNOTIN('status',[2])->get();
         // if ($bookcheck->count() > 0) {
         //     return redirect()->route('admin.master.rudrakshi')
         //                 ->with('failure','You can not delete rudrakshi due to linking with book');
         // }
         $a = Rudrakshi::find($input['id']);
         $a->status = 2;
         $a->save();
         return redirect()->route('admin.master.rudrakshi')
                         ->with('success','Rudrakshi deleted successfully');
     }




       //precious_stone
       public function precious_stone()
       {
           $data = PreciousStone::whereNOTIN('status',[2])->orderBy('position','ASC')->get();
           $addbutton = 'Add Precious Stone';
           $importbutton = 'Upload Excel';
           $title = 'Precious Stone';
           $listurl = route('admin.master.precious_stone');
           $addurl = route('admin.master.create_precious_stone');
           $importurl = route('admin.master.import_precious_stone');
           $editurl = 'admin.master.edit_precious_stone';
           $destroyurl = route('admin.master.destroy_precious_stone');
           $addurl1 = route('admin.master.store_precious_stone');
           return view('admin.precious_stone.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
       }
   
       public function create_precious_stone()
       {
           $title = 'Add Precious Stone';
           $listname = 'Precious Stone';
           $listurl = route('admin.master.precious_stone');
           $addurl = route('admin.master.store_precious_stone');
           return view('admin.precious_stone.create',compact('title','listurl','listname','addurl'));
       }
   
       public function store_precious_stone(Request $request)
       {
           $this->validate($request, [
               'title' => 'required',
               'position' => 'required',
               'status' => 'required',
           ]);
   
   
           $a = new PreciousStone();
           $a->title = $request->title;
           $a->description = $request->description;
           $a->position = $request->position;
           $a->status = $request->status;
           $a->save();
           return redirect()->route('admin.master.precious_stone')
                           ->with('success','Precious Stone created successfully');
       }
   
   
       public function edit_precious_stone($id)
       {
           $title = 'Edit Precious Stone';
           $listname = 'Precious Stone';
           $listurl = route('admin.master.precious_stone');
           $editurl = 'admin.master.update_precious_stone';
           $a = PreciousStone::find($id);
           if ($a)
           {
               return view('admin.precious_stone.edit',compact('a','title','listurl','listname','editurl'));
           }
           else
           {
               return redirect()->route('admin.master.precious_stone')
                           ->with('failure','Not found any data');
           }
   
       }
   
       public function update_precious_stone(Request $request, $id)
       {
           $this->validate($request, [
               'title' => 'required',
               'position' => 'required',
               'status' => 'required',
           ]);
   
           $a = PreciousStone::find($id);
           $a->title = $request->title;
           $a->description = $request->description;
           $a->position = $request->position;
           $a->status = $request->status;
           $a->save();
           return redirect()->route('admin.master.precious_stone')
                           ->with('success','Precious Stone updated successfully');
       }
   
       public function destroy_precious_stone(Request $request)
       {
           $input = $request->all();
           // $bookcheck = Blog::where('precious_stone_id',$input['id'])->whereNOTIN('status',[2])->get();
           // if ($bookcheck->count() > 0) {
           //     return redirect()->route('admin.master.precious_stone')
           //                 ->with('failure','You can not delete precious_stone due to linking with book');
           // }
           $a = PreciousStone::find($input['id']);
           $a->status = 2;
           $a->save();
           return redirect()->route('admin.master.precious_stone')
                           ->with('success','PreciousStone deleted successfully');
       }




        //semi_precious_stone
        public function semi_precious_stone()
        {
            $data = SemiPreciousStone::whereNOTIN('status',[2])->orderBy('position','ASC')->get();
            $addbutton = 'Add Semi Precious Stone';
            $importbutton = 'Upload Excel';
            $title = 'Semi Precious Stone';
            $listurl = route('admin.master.semi_precious_stone');
            $addurl = route('admin.master.create_semi_precious_stone');
            $importurl = route('admin.master.import_semi_precious_stone');
            $editurl = 'admin.master.edit_semi_precious_stone';
            $destroyurl = route('admin.master.destroy_semi_precious_stone');
            $addurl1 = route('admin.master.store_semi_precious_stone');
            return view('admin.semi_precious_stone.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
        }
    
        public function create_semi_precious_stone()
        {
            $title = 'Add Semi Precious Stone';
            $listname = 'Semi Precious Stone';
            $listurl = route('admin.master.semi_precious_stone');
            $addurl = route('admin.master.store_semi_precious_stone');
            return view('admin.semi_precious_stone.create',compact('title','listurl','listname','addurl'));
        }
    
        public function store_semi_precious_stone(Request $request)
        {
            $this->validate($request, [
                'title' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);
    
    
            $a = new SemiPreciousStone();
            $a->title = $request->title;
            $a->description = $request->description;
            $a->position = $request->position;
            $a->status = $request->status;
            $a->save();
            return redirect()->route('admin.master.semi_precious_stone')
                            ->with('success','Semi Precious Stone created successfully');
        }
    
    
        public function edit_semi_precious_stone($id)
        {
            $title = 'Edit Semi Precious Stone';
            $listname = 'Semi Precious Stone';
            $listurl = route('admin.master.semi_precious_stone');
            $editurl = 'admin.master.update_semi_precious_stone';
            $a = SemiPreciousStone::find($id);
            if ($a)
            {
                return view('admin.semi_precious_stone.edit',compact('a','title','listurl','listname','editurl'));
            }
            else
            {
                return redirect()->route('admin.master.semi_precious_stone')
                            ->with('failure','Not found any data');
            }
    
        }
    
        public function update_semi_precious_stone(Request $request, $id)
        {
            $this->validate($request, [
                'title' => 'required',
                'position' => 'required',
                'status' => 'required',
            ]);
    
            $a = SemiPreciousStone::find($id);
            $a->title = $request->title;
            $a->description = $request->description;
            $a->position = $request->position;
            $a->status = $request->status;
            $a->save();
            return redirect()->route('admin.master.semi_precious_stone')
                            ->with('success','Semi Precious Stone updated successfully');
        }
    
        public function destroy_semi_precious_stone(Request $request)
        {
            $input = $request->all();
            // $bookcheck = Blog::where('semi_precious_stone_id',$input['id'])->whereNOTIN('status',[2])->get();
            // if ($bookcheck->count() > 0) {
            //     return redirect()->route('admin.master.semi_precious_stone')
            //                 ->with('failure','You can not delete semi_precious_stone due to linking with book');
            // }
            $a = SemiPreciousStone::find($input['id']);
            $a->status = 2;
            $a->save();
            return redirect()->route('admin.master.semi_precious_stone')
                            ->with('success','Semi Precious Stone deleted successfully');
        }






    //mantras
    public function mantras()
    {
        $data = Mantra::whereNOTIN('status',[2])->orderBy('position','ASC')->get();
        $addbutton = 'Add Mantra';
        $importbutton = 'Upload Excel';
        $title = 'Mantra';
        $listurl = route('admin.master.mantras');
        $addurl = route('admin.master.create_mantras');
        $importurl = route('admin.master.import_mantras');
        $editurl = 'admin.master.edit_mantras';
        $destroyurl = route('admin.master.destroy_mantras');
        $addurl1 = route('admin.master.store_mantras');
        return view('admin.mantras.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_mantras()
    {
        $title = 'Add Mantras';
        $listname = 'Mantras';
        $listurl = route('admin.master.mantras');
        $addurl = route('admin.master.store_mantras');
        return view('admin.mantras.create',compact('title','listurl','listname','addurl'));
    }

    public function store_mantras(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);


        $a = new Mantra();
        $a->title = $request->title;
        $a->description = $request->description;
        $a->mantra = $request->mantra;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.mantras')
                        ->with('success','Mantra created successfully');
    }


    public function edit_mantras($id)
    {
        $title = 'Edit Mantra';
        $listname = 'Mantra';
        $listurl = route('admin.master.mantras');
        $editurl = 'admin.master.update_mantras';
        $a = Mantra::find($id);
        if ($a)
        {
            return view('admin.mantras.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.mantras')
                        ->with('failure','Not found any data');
        }

    }

    public function update_mantras(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);

        $a = Mantra::find($id);
        $a->title = $request->title;
        $a->description = $request->description;
        $a->mantra = $request->mantra;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.mantras')
                        ->with('success','Mantra updated successfully');
    }

    public function destroy_mantras(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('mantras_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.mantras')
        //                 ->with('failure','You can not delete mantras due to linking with book');
        // }
        $a = Mantra::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.mantras')
                        ->with('success','Mantra deleted successfully');
    }

    //threadcolor
    public function threadcolor()
    {
        $data = ThreadColor::whereNOTIN('status',[2])->orderBy('position','ASC')->get();
        $addbutton = 'Add Thread Color';
        $importbutton = 'Upload Excel';
        $title = 'Thread Color';
        $listurl = route('admin.master.threadcolor');
        $addurl = route('admin.master.create_threadcolor');
        $importurl = route('admin.master.import_threadcolor');
        $editurl = 'admin.master.edit_threadcolor';
        $destroyurl = route('admin.master.destroy_threadcolor');
        $addurl1 = route('admin.master.store_threadcolor');
        return view('admin.threadcolor.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_threadcolor()
    {
        $title = 'Add Thread Color';
        $listname = 'Thread Color';
        $listurl = route('admin.master.threadcolor');
        $addurl = route('admin.master.store_threadcolor');
        return view('admin.threadcolor.create',compact('title','listurl','listname','addurl'));
    }

    public function store_threadcolor(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);


        $a = new ThreadColor();
        $a->title = $request->title;
        $a->description = $request->description;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.threadcolor')
                        ->with('success','Thread Color created successfully');
    }


    public function edit_threadcolor($id)
    {
        $title = 'Edit Thread Color';
        $listname = 'Thread Color';
        $listurl = route('admin.master.threadcolor');
        $editurl = 'admin.master.update_threadcolor';
        $a = ThreadColor::find($id);
        if ($a)
        {
            return view('admin.threadcolor.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.threadcolor')
                        ->with('failure','Not found any data');
        }

    }

    public function update_threadcolor(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);

        $a = ThreadColor::find($id);
        $a->title = $request->title;
        $a->description = $request->description;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.threadcolor')
                        ->with('success','Thread Color updated successfully');
    }

    public function destroy_threadcolor(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('threadcolor_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.threadcolor')
        //                 ->with('failure','You can not delete threadcolor due to linking with book');
        // }
        $a = ThreadColor::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.threadcolor')
                        ->with('success','Thread Color deleted successfully');
    }

    //masterdonation
    public function masterdonation()
    {
        $data = MasterDonation::whereNOTIN('status',[2])->orderBy('position','ASC')->get();
        $addbutton = 'Add Master Donation';
        $importbutton = 'Upload Excel';
        $title = 'Master Donation';
        $listurl = route('admin.master.masterdonation');
        $addurl = route('admin.master.create_masterdonation');
        $importurl = route('admin.master.import_masterdonation');
        $editurl = 'admin.master.edit_masterdonation';
        $destroyurl = route('admin.master.destroy_masterdonation');
        $addurl1 = route('admin.master.store_masterdonation');
        return view('admin.masterdonation.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_masterdonation()
    {
        $title = 'Add Master Donation';
        $listname = 'Master Donation';
        $listurl = route('admin.master.masterdonation');
        $addurl = route('admin.master.store_masterdonation');
        return view('admin.masterdonation.create',compact('title','listurl','listname','addurl'));
    }

    public function store_masterdonation(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);


        $a = new MasterDonation();
        $a->title = $request->title;
        $a->description = $request->description;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.masterdonation')
                        ->with('success','Master Donation created successfully');
    }


    public function edit_masterdonation($id)
    {
        $title = 'Edit Master Donation';
        $listname = 'Master Donation';
        $listurl = route('admin.master.masterdonation');
        $editurl = 'admin.master.update_masterdonation';
        $a = MasterDonation::find($id);
        if ($a)
        {
            return view('admin.masterdonation.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.masterdonation')
                        ->with('failure','Not found any data');
        }

    }

    public function update_masterdonation(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);

        $a = MasterDonation::find($id);
        $a->title = $request->title;
        $a->description = $request->description;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.masterdonation')
                        ->with('success','Master Donation updated successfully');
    }

    public function destroy_masterdonation(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('masterdonation_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.masterdonation')
        //                 ->with('failure','You can not delete masterdonation due to linking with book');
        // }
        $a = MasterDonation::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.masterdonation')
                        ->with('success','Master Donation deleted successfully');
    }

}
