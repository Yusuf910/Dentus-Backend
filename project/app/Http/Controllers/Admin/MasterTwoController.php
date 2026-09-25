<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupan;
use App\Models\WalletPlan;
use App\Models\Mantra;
use App\Models\ThoughtsForTheDay;
use App\Models\BhagavathGeeta;
use App\Models\ThreadColor;
use App\Models\MasterDonation;
use App\Models\Rudrakshi;
use App\Models\SemiPreciousStone;
use App\Models\PreciousStone;
use App\Models\SettingPaidChargeMatrix;
use App\Models\SettingFreeChargeMatrix;
use App\Models\SettingAAQChargeMatrix;
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
        $data = WalletPlan::whereNOTIN('status',[2])->orderBy('recharge','ASC')->orderBy('status','DESC')->get();
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
        $a->currency_code = $request->currency_code;
        $a->status = $request->status;
        $a->coupan_id = $request->coupan_id;
        $a->coupon = $request->coupan_id;
        $a->add_percentage = $request->add_percentage;
        $a->limit = $request->limit;
        $a->everytime_benefit_flag = $request->everytime_benefit_flag;
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
        $a->currency_code = $request->currency_code;

        $a->status = $request->status;
        $a->coupan_id = $request->coupan_id;
        $a->coupon = $request->coupan_id;
        $a->add_percentage = $request->add_percentage;
        $a->limit = $request->limit;
        $a->everytime_benefit_flag = $request->everytime_benefit_flag;
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




    //new


    

    //bhagavath_geeta
    public function bhagavath_geeta()
    {
        $data = BhagavathGeeta::whereNOTIN('status',[2])->get();
        $addbutton = 'Add Bhagavath Geeta';
        $importbutton = 'Upload Excel';
        $title = 'Bhagavath Geeta';
        $listurl = route('admin.master.bhagavath_geeta');
        $addurl = route('admin.master.create_bhagavath_geeta');
        $importurl = route('admin.master.import_bhagavath_geeta');
        $editurl = 'admin.master.edit_bhagavath_geeta';
        $destroyurl = route('admin.master.destroy_bhagavath_geeta');
        $addurl1 = route('admin.master.store_bhagavath_geeta');
        return view('admin.bhagavath_geeta.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_bhagavath_geeta()
    {
        $title = 'Add bhagavath_geeta';
        $listname = 'bhagavath_geeta';
        $listurl = route('admin.master.bhagavath_geeta');
        $addurl = route('admin.master.store_bhagavath_geeta');
        return view('admin.bhagavath_geeta.create',compact('title','listurl','listname','addurl'));
    }

    public function store_bhagavath_geeta(Request $request)
    {
        $this->validate($request, [
            'geeta_text' => 'required',
            'status' => 'required',
        ]);


        $a = new BhagavathGeeta();
        $a->geeta_text = $request->geeta_text;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.bhagavath_geeta')
                        ->with('success','Bhagavath Geeta created successfully');
    }


    public function edit_bhagavath_geeta($id)
    {
        $title = 'Edit Bhagavath Geeta';
        $listname = 'Bhagavath Geeta';
        $listurl = route('admin.master.bhagavath_geeta');
        $editurl = 'admin.master.update_bhagavath_geeta';
        $a = BhagavathGeeta::find($id);
        if ($a)
        {
            return view('admin.bhagavath_geeta.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.bhagavath_geeta')
                        ->with('failure','Not found any data');
        }

    }

    public function update_bhagavath_geeta(Request $request, $id)
    {
        $this->validate($request, [
            'geeta_text' => 'required',
           
            'status' => 'required',
        ]);

        $a = BhagavathGeeta::find($id);
        $a->geeta_text = $request->geeta_text;
      
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.bhagavath_geeta')
                        ->with('success','Bhagavath Geeta updated successfully');
    }

    public function destroy_bhagavath_geeta(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('bhagavath_geeta_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.bhagavath_geeta')
        //                 ->with('failure','You can not delete bhagavath_geeta due to linking with book');
        // }
        $a = BhagavathGeeta::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.bhagavath_geeta')
                        ->with('success','Bhagavath Geeta deleted successfully');
    }





    //thoughts_for_the_day
    public function thoughts_for_the_day()
    {
        $data = ThoughtsForTheDay::whereNOTIN('status',[2])->get();
        $addbutton = 'Add Thoughts For The Day';
        $importbutton = 'Upload Excel';
        $title = 'Thoughts For The Day';
        $listurl = route('admin.master.thoughts_for_the_day');
        $addurl = route('admin.master.create_thoughts_for_the_day');
        $importurl = route('admin.master.import_thoughts_for_the_day');
        $editurl = 'admin.master.edit_thoughts_for_the_day';
        $destroyurl = route('admin.master.destroy_thoughts_for_the_day');
        $addurl1 = route('admin.master.store_thoughts_for_the_day');
        return view('admin.thoughts_for_the_day.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_thoughts_for_the_day()
    {
        $title = 'Add Thoughts For The Day';
        $listname = 'Thoughts For The Day';
        $listurl = route('admin.master.thoughts_for_the_day');
        $addurl = route('admin.master.store_thoughts_for_the_day');
        return view('admin.thoughts_for_the_day.create',compact('title','listurl','listname','addurl'));
    }

    public function store_thoughts_for_the_day(Request $request)
    {
        $this->validate($request, [
            'thought' => 'required',
            'author' => 'required',
            'status' => 'required',
        ]);


        $a = new ThoughtsForTheDay();
        $a->thought = $request->thought;
        $a->author = $request->author;
     
        $a->save();
        return redirect()->route('admin.master.thoughts_for_the_day')
                        ->with('success','Thoughts For The Day created successfully');
    }


    public function edit_thoughts_for_the_day($id)
    {
        $title = 'Edit Thoughts For The Day';
        $listname = 'Thoughts For The Day';
        $listurl = route('admin.master.thoughts_for_the_day');
        $editurl = 'admin.master.update_thoughts_for_the_day';
        $a = ThoughtsForTheDay::find($id);
        if ($a)
        {
            return view('admin.thoughts_for_the_day.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.thoughts_for_the_day')
                        ->with('failure','Not found any data');
        }

    }

    public function update_thoughts_for_the_day(Request $request, $id)
    {
        $this->validate($request, [
            'thought' => 'required',
            'author' => 'required',
            'status' => 'required',
        ]);

        $a = ThoughtsForTheDay::find($id);
        $a->thought = $request->thought;
        $a->author = $request->author;
      
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.thoughts_for_the_day')
                        ->with('success','Thoughts For The Day updated successfully');
    }

    public function destroy_thoughts_for_the_day(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('thoughts_for_the_day_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.thoughts_for_the_day')
        //                 ->with('failure','You can not delete thoughts_for_the_day due to linking with book');
        // }
        $a = ThoughtsForTheDay::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.thoughts_for_the_day')
                        ->with('success','Thoughts For The Day deleted successfully');
    }





    //end new

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

    public function paidcommsionconfig()
    {
        $data = SettingPaidChargeMatrix::where('status',1)->orderBy('start','ASC')->get();
        $addbutton = 'Add Paid Commission';
        $importbutton = 'Upload Excel';
        $title = 'Paid Commission Config';
        $listurl = route('admin.master.paidcommsionconfig');
        $addurl = route('admin.master.create_paidcommsionconfig');
        $importurl = route('admin.master.import_paidcommsionconfig');
        $editurl = 'admin.master.edit_paidcommsionconfig';
        $destroyurl = route('admin.master.destroy_paidcommsionconfig');
        $addurl1 = route('admin.master.store_paidcommsionconfig');
        return view('admin.paidcommsionconfig.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_paidcommsionconfig()
    {
        $title = 'Add Paid Commission';
        $listname = 'Paid Commission';
        $listurl = route('admin.master.paidcommsionconfig');
        $addurl = route('admin.master.store_paidcommsionconfig');
        return view('admin.paidcommsionconfig.create',compact('title','listurl','listname','addurl'));
    }

    public function store_paidcommsionconfig(Request $request)
    {
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required',
            'percentage' => 'required|integer|min:0|max:99'
        ]);


        $a = new SettingPaidChargeMatrix();
        $a->start = $request->start;
        $a->endrange = $request->endrange;
        $a->percentage = $request->percentage;
        $a->save();
        return redirect()->route('admin.master.paidcommsionconfig')
                        ->with('success','created successfully');
    }


    public function edit_paidcommsionconfig($id)
    {
        $title = 'Edit Paid Commission';
        $listname = 'Paid Commission';
        $listurl = route('admin.master.paidcommsionconfig');
        $editurl = 'admin.master.update_paidcommsionconfig';
        $a = SettingPaidChargeMatrix::find($id);
        if ($a)
        {
            return view('admin.paidcommsionconfig.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.paidcommsionconfig')
                        ->with('failure','Not found any data');
        }

    }

    public function update_paidcommsionconfig(Request $request, $id)
    {
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required',
            'percentage' => 'required|integer|min:0|max:99'
        ],[
            'start.less_than_end' => 'The start must be less than the end.',
        ]);
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required',
            'endrange' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value == '>') {
                        // code...
                    }
                    elseif ($request->start >= $value) {
                        $fail('The start must be less than the end.');
                    }
                },
            ],
        ]);
        $a = SettingPaidChargeMatrix::find($id);
        $a->start = $request->start;
        $a->endrange = $request->endrange;
        $a->percentage = $request->percentage;
        $a->save();
        return redirect()->route('admin.master.paidcommsionconfig')
                        ->with('success','updated successfully');
    }

    public function destroy_paidcommsionconfig(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('paidcommsionconfig_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.paidcommsionconfig')
        //                 ->with('failure','You can not delete paidcommsionconfig due to linking with book');
        // }
        $a = SettingPaidChargeMatrix::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.paidcommsionconfig')
                        ->with('success','Thread Color deleted successfully');
    }

    //threadcolor
    public function freecommsionconfig()
    {
        $data = SettingFreeChargeMatrix::where('status',1)->orderBy('start','ASC')->get();
        $addbutton = 'Add Free Commission';
        $importbutton = 'Upload Excel';
        $title = 'Free Commission Config';
        $listurl = route('admin.master.freecommsionconfig');
        $addurl = route('admin.master.create_freecommsionconfig');
        $importurl = route('admin.master.import_freecommsionconfig');
        $editurl = 'admin.master.edit_freecommsionconfig';
        $destroyurl = route('admin.master.destroy_freecommsionconfig');
        $addurl1 = route('admin.master.store_freecommsionconfig');
        return view('admin.freecommsionconfig.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_freecommsionconfig()
    {
        $title = 'Add Free Commission';
        $listname = 'Free Commission';
        $listurl = route('admin.master.freecommsionconfig');
        $addurl = route('admin.master.store_freecommsionconfig');
        return view('admin.freecommsionconfig.create',compact('title','listurl','listname','addurl'));
    }

    public function store_freecommsionconfig(Request $request)
    {
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required|integer|',
            'perminutes' => 'required'
        ]);


        $a = new SettingFreeChargeMatrix();
        $a->start = $request->start;
        $a->endrange = $request->endrange;
        $a->perminutes = $request->perminutes;
        $a->save();
        return redirect()->route('admin.master.freecommsionconfig')
                        ->with('success','created successfully');
    }


    public function edit_freecommsionconfig($id)
    {
        $title = 'Edit Free Commission';
        $listname = 'Free Commission';
        $listurl = route('admin.master.freecommsionconfig');
        $editurl = 'admin.master.update_freecommsionconfig';
        $a = SettingFreeChargeMatrix::find($id);
        if ($a)
        {
            return view('admin.freecommsionconfig.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.freecommsionconfig')
                        ->with('failure','Not found any data');
        }

    }

    public function update_freecommsionconfig(Request $request, $id)
    {
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required|integer|',
            'perminutes' => 'required'
        ]);
        
            
        $a = SettingFreeChargeMatrix::find($id);
        $a->start = $request->start;
        $a->endrange = $request->endrange;
        $a->perminutes = $request->perminutes;
        $a->save();
        return redirect()->route('admin.master.freecommsionconfig')
                        ->with('success','updated successfully');
    }

    public function destroy_freecommsionconfig(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('freecommsionconfig_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.freecommsionconfig')
        //                 ->with('failure','You can not delete freecommsionconfig due to linking with book');
        // }
        $a = SettingFreeChargeMatrix::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.freecommsionconfig')
                        ->with('success','deleted successfully');
    }

    public function aaqcommsionconfig()
    {
        $data = SettingAAQChargeMatrix::where('status',1)->orderBy('start','ASC')->get();
        $addbutton = 'Add AAQ Commission';
        $importbutton = 'Upload Excel';
        $title = 'AAQ Commission Config';
        $listurl = route('admin.master.aaqcommsionconfig');
        $addurl = route('admin.master.create_aaqcommsionconfig');
        $importurl = route('admin.master.import_aaqcommsionconfig');
        $editurl = 'admin.master.edit_aaqcommsionconfig';
        $destroyurl = route('admin.master.destroy_aaqcommsionconfig');
        $addurl1 = route('admin.master.store_aaqcommsionconfig');
        return view('admin.aaqcommsionconfig.index',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl','addurl1'));
    }

    public function create_aaqcommsionconfig()
    {
        $title = 'Add AAQ Commission';
        $listname = 'AAQ Commission';
        $listurl = route('admin.master.aaqcommsionconfig');
        $addurl = route('admin.master.store_aaqcommsionconfig');
        return view('admin.aaqcommsionconfig.create',compact('title','listurl','listname','addurl'));
    }

    public function store_aaqcommsionconfig(Request $request)
    {
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required',
            'price' => 'required'
        ]);


        $a = new SettingAAQChargeMatrix();
        $a->start = $request->start;
        $a->endrange = $request->endrange;
        $a->price = $request->price;
        $a->save();
        return redirect()->route('admin.master.aaqcommsionconfig')
                        ->with('success','created successfully');
    }


    public function edit_aaqcommsionconfig($id)
    {
        $title = 'Edit AAQ Commission';
        $listname = 'AAQ Commission';
        $listurl = route('admin.master.aaqcommsionconfig');
        $editurl = 'admin.master.update_aaqcommsionconfig';
        $a = SettingAAQChargeMatrix::find($id);
        if ($a)
        {
            return view('admin.aaqcommsionconfig.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.aaqcommsionconfig')
                        ->with('failure','Not found any data');
        }

    }

    public function update_aaqcommsionconfig(Request $request, $id)
    {
        $this->validate($request, [
            'start' => 'required|integer|min:0',
            'endrange' => 'required',
            'price' => 'required'
        ]);
        
            
        $a = SettingAAQChargeMatrix::find($id);
        $a->start = $request->start;
        $a->endrange = $request->endrange;
        $a->price = $request->price;
        $a->save();
        return redirect()->route('admin.master.aaqcommsionconfig')
                        ->with('success','updated successfully');
    }

    public function destroy_aaqcommsionconfig(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('aaqcommsionconfig_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.aaqcommsionconfig')
        //                 ->with('failure','You can not delete aaqcommsionconfig due to linking with book');
        // }
        $a = SettingAAQChargeMatrix::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.aaqcommsionconfig')
                        ->with('success','deleted successfully');
    }

}
