<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class BannerController extends Controller
{

    function index() {
        $title = "Main Banner List";
        $addbutton = "Add Banner";
        $is_type = 1;
        $editurl = 'admin.master.banner.edit';
        $destroyurl = route('admin.master.destroy_bnnr');
        $data = Banner::whereNOTIN('is_active',[2])->where('type',"main")->get();
        return view('admin.banner.index', compact('title', 'addbutton', 'data','editurl','destroyurl','is_type'));
    }


    public function mid_banner(Request $request) {
        $title = "Mid Banner List";
        $addbutton = "Add Banner";
        $is_type = 2;
        $editurl = 'admin.master.banner.edit';
        $destroyurl = route('admin.master.destroy_bnnr');
        $data = Banner::whereNOTIN('is_active',[2])->where('type',"mid")->get();
        return view('admin.banner.index', compact('title', 'addbutton', 'data','editurl','destroyurl','is_type'));
    }

    public function offer_banner(Request $request) {
        $title = "Offer Banner List";
        $addbutton = "Add Banner";
        $is_type = 3;
        $editurl = 'admin.master.banner.edit';
        $destroyurl = route('admin.master.destroy_bnnr');
        $data = Banner::whereNOTIN('is_active',[2])->whereNOTIN('type',['mid','main'])->get();
        return view('admin.banner.index', compact('title', 'addbutton', 'data','editurl','destroyurl','is_type'));
    }





    function create() {
        $title = "Add Banner";
        $listurl = route('admin.master.banner.index');
        $listname = "Banner List";
        return view('admin.banner.create', compact('title', 'listurl', 'listname'));
    }

    function edit($id) {
        $title = "Edit Banner";
        $listurl = route('admin.master.banner.index');
        $editurl = 'admin.master.banner.update';
        $listname = "Banner List";
        $a = Banner::find($id);
        if ($a)
        {
            return view('admin.banner.edit',compact('a','title','listurl','listname','editurl'));
        }
        else
        {
            return redirect()->route('admin.master.banner.index')
                        ->with('failure','Not found any data');
        }
       
    }

    function store(Request $request) {

        $request->validate([
           'bannerimage' => ['required']
        ]);
        if ($request->link_id_chat == '') {
            $link_id = 0;
        }
        else {
            $link_id = $request->link_id_chat;
        }

        if ($request->link_type_chat == '') {
            $link_type = 'chat_listing';
        }
        else {
            $link_type = $request->link_type_chat;
        }
        $banner = new Banner();
        $banner->link_id  = $link_id ;
        $banner->link_type  = $link_type ;
        $banner->type = $request->type;
        $banner->is_active = $request->status;
        $banner->position = $request->position;
        $banner->type = 'main';
        if (request()->hasFile('bannerimage'))
        {
            $sFile = request()->file('bannerimage');
            $fileNameWithTheExtension = $sFile->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = $sFile->getClientOriginalExtension();
            $image_name = $fileName.'_' . time() . '.' . $extension;


            $filePath = $sFile->move('project/public/adminassets/banner/user/', $image_name);

           
        }
        $banner->image = $image_name;
        $banner->save();
        return redirect()->route('admin.master.banner.index')
            ->with('success','Banner added successfully');
    }

    function update(Request $request, $id) 
    {
// print_r("ddd"); die;
        $a = Banner::find($id);
        $image_name = $a->image;
        if (request('image'))
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('project/public/adminassets/banner/user/', $image_name);

        }
        // $a->name = $request->name;
        // $a->price = $request->price;
        $a->position = $request->position;
        $a->is_active = $request->is_active;
        $a->image = $image_name;
        $a->save();




        // $post = new User();
        // $post->user_offer = 0; //already exists in database.
        // $post->new_user = 0; //already exists in database.
        // $post->save();



        if ( $a->type == "main") {
            return redirect()->route('admin.master.banner.index')
        ->with('success','Banner updated successfully');
        }
        elseif ($a->type == "mid") {
            return redirect()->route('admin.master.mid_banner')
            ->with('success','Banner updated successfully');
        }

        elseif ($a->type == "offer_main") {
            User::where('user_offer',1)->update(['user_offer'=>0]);
            return redirect()->route('admin.master.offer_banner')
            ->with('success','Banner updated successfully');
        }
        else{        
            User::where('new_user',1)->update(['new_user'=>0]);
            return redirect()->route('admin.master.offer_banner')
            ->with('success','Banner updated successfully');
        }

       
       
    }

    public function destroy_bnnr(Request $request) {
        $input = $request->all();
        //dd($input);
        $a = Banner::find($input['id']);
        $a->is_active = 2;
        $a->save();

        return redirect()->route('admin.master.banner.index')
            ->with('success','Banner deleted successfully');
    }

    public function destroy_banner(Request $request) {
        $input = $request->all();
        //dd($input);
        $a = Banner::find($input['id']);
        $a->is_active = 2;
        $a->save();

        return redirect()->route('admin.master.banner.index')
            ->with('success','Banner deleted successfully');
    }

    

}
