<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Invitation;
use App\Models\UserModels\UserProfile;
use App\Models\Treatment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DB;
use HasApiTokens;

class PatientController extends Controller
{

    public function patient()
     {
         $data = UserProfile::whereNOTIN('status',[2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at','ASC')->get();
         $addbutton = 'Add Patient';
         $listurl = route('user.patient');
         $addurl = route('user.create_treatment');
         $editurl = 'user.edit_treatment';
         $destroyurl = route('user.destroy_treatment');
         $addurl = route('user.store_treatment');
         return view('admin.patient.index',compact('data','addbutton','addurl','listurl','editurl','destroyurl','addurl'));
     }


     public function create_treatment()
     {
         $title = 'Add Banner';
         $listname = 'Banners';
         $listurl = route('admin.master.blog');
         $addurl = route('admin.master.store_blog');
         return view('admin.banner.create',compact('title','listurl','listname','addurl'));
     }

     public function store_treatment(Request $request)
     {
         $this->validate($request, [
             'maintype' => 'required',
             'linktype' => 'required',
             'position' => 'required',
             'image' => 'required',
             'link_id' => 'required',
             'status' => 'required',
         ]);


         $image = 'default.png';
         if (request('image'))
         {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_'.$fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/banner', $image);


            //  $fileNameWithTheExtension = request('image')->getClientOriginalName();
            //  $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            //  $extension = request('image')->getClientOriginalExtension();
            //  $image_name = rand().'_' . time() . '.' . $extension;
            //  $d = request()->image->storeAs('banner', $image_name,'content');
            // //  print_r($d);
            //  $image = $image_name;
         }


         $a = new Banner();
         $a->maintype = $request->maintype;
         $a->type = $request->type;
         $a->linktype = $request->linktype;
         $a->link_id = $request->link_id;
         $a->position = $request->position;
         $a->status = $request->status;
         $a->image = $image;
         $a->save();
         return redirect()->route('admin.master.banner')
                         ->with('success','blog created successfully');
     }


     public function edit_treatment($id)
     {
         $title = 'Edit Banner';
         $listname = 'Banners';
         $listurl = route('admin.master.blog');
         $editurl = 'admin.master.update_blog';
         $a = Banner::find($id);
         if ($a)
         {
             return view('admin.banner.edit',compact('a','title','listurl','listname','editurl'));
         }
         else
         {
             return redirect()->route('admin.master.blog')
                         ->with('failure','Not found any data');
         }

     }

     public function update_treatment(Request $request, $id)
     {
         $this->validate($request, [
             'title' => 'required',
             'description' => 'required',
             'published_date' => 'required',
             'position' => 'required',
             'position' => 'required',
             'status' => 'required',
         ]);

         $a = Banner::find($id);
         $image = $a->image;
         if (request('image'))
         {
             $fileNameWithTheExtension = request('image')->getClientOriginalName();
             $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
             $extension = request('image')->getClientOriginalExtension();
             $image_name = rand().'_' . time() . '.' . $extension;
             $d = request()->image->storeAs('banner', $image_name,'contentfolder');
             $image = $image_name;
         }
         $a->title = $request->title;
         $a->description = $request->description;
         $a->published_date = $request->published_date;
         $a->position = $request->position;
         $a->status = $request->status;
         $a->image = $image;
         $a->save();
         return redirect()->route('admin.master.blog')
                         ->with('success','Blog updated successfully');
     }

     public function destroy_treatment(Request $request)
     {
         $input = $request->all();
         // $bookcheck = Blog::where('blog_id',$input['id'])->whereNOTIN('status',[2])->get();
         // if ($bookcheck->count() > 0) {
         //     return redirect()->route('admin.master.blog')
         //                 ->with('failure','You can not delete blog due to linking with book');
         // }
         $a = Banner::find($input['id']);
         $a->status = 2;
         $a->save();
         return redirect()->route('admin.master.banner')
                         ->with('success','Banner deleted successfully');
     }



}
