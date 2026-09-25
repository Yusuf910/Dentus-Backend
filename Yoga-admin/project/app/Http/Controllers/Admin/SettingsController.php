<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Models\Setting;
use File;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Support\Facades\Storage;
use App\Models\Practiceyogaposes;
use App\Models\Practiceyogacategories;
use MongoDB\BSON\ObjectId;
use App\Models\Yogaposeslevels;
class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Generalsetting $setting)
    {
        return view('admin.settings', ['settings' => $setting]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Generalsetting $setting)
    {
        $this->validate(
            $request,
            [
                'header_logo' => 'required|max:255',
                'favicon_icon' => 'required|max:255',
                'footer_logo' => 'required|max:255',
                // 'support_email' => 'required|max:255',    
                // 'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            ]
        );

        if (request('header_logo')) {
            $fileNameWithTheExtension = request('header_logo')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('header_logo')->getClientOriginalExtension();
            $header_logo_name = 'header_logo_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('header_logo')->move(public_path('adminassets/images'), $header_logo_name);
        } else {
            $header_logo_name = request('header_logoname');
        }

        if (request('favicon_icon')) {
            $fileNameWithTheExtension = request('favicon_icon')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('favicon_icon')->getClientOriginalExtension();
            $favicon_icon_name = 'favicon_icon_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('favicon_icon')->move(public_path('adminassets/images'), $favicon_icon_name);
        } else {
            $favicon_icon_name = request('favicon_icon_name');
        }

        if (request('footer_logo')) {
            $fileNameWithTheExtension = request('footer_logo')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('footer_logo')->getClientOriginalExtension();
            $footer_logo_name = 'footer_logo_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('footer_logo')->move(public_path('adminassets/images'), $footer_logo_name);
        } else {
            $footer_logo_name = request('footer_logo_name');
        }

        // if (request('checklistnew')) {
        //     $fileNameWithTheExtension = request('checklistnew')->getClientOriginalName();
        //     $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
        //     $extension = request('checklistnew')->getClientOriginalExtension();
        //     $checklist_name = 'checklistnew' . $fileName . '_' . time() . '.' . $extension;
        //     $filePath = request('checklistnew')->move( /*public_path('*/'/var/www/html/assets/checklist', $checklist_name);
        // } else {
        //     $checklist_name = request('checklist');
        // }
        // dd($extension);

        $setting = Generalsetting::findOrFail(1);

        $setting->title = request('title');
        $setting->footer = request('footer');
        $setting->header_logo = $header_logo_name;
        $setting->favicon_icon = $favicon_icon_name;
        $setting->footer_logo = $footer_logo_name;
        $setting->save();
        return redirect()->route('admin.settings.edit', 1)
            ->with('success', 'Settings updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function settings1()
    {
        $settings = Setting::find(1);
        return view('admin.settings1', ['settings' => $settings]);
    }

    public function settings1update(Request $request, Generalsetting $setting)
    {
        $this->validate(
            $request,
            [
                'header_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:255',
                'favicon_icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:255',
                'footer_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:255',
                // 'support_email' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:255',    
                'admin_email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            ]
        );

        if (request('header_logo')) {
            $fileNameWithTheExtension = request('header_logo')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('header_logo')->getClientOriginalExtension();
            $header_logo_name = 'header_logo_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('header_logo')->move(public_path('adminassets/images'), $header_logo_name);
        } else {
            $header_logo_name = request('header_logoname');
        }

        if (request('favicon_icon')) {
            $fileNameWithTheExtension = request('favicon_icon')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('favicon_icon')->getClientOriginalExtension();
            $favicon_icon_name = 'favicon_icon_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('favicon_icon')->move(public_path('adminassets/images'), $favicon_icon_name);
        } else {
            $favicon_icon_name = request('favicon_icon_name');
        }

        if (request('footer_logo')) {
            $fileNameWithTheExtension = request('footer_logo')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('footer_logo')->getClientOriginalExtension();
            $footer_logo_name = 'footer_logo_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('footer_logo')->move(public_path('adminassets/images'), $footer_logo_name);
        } else {
            $footer_logo_name = request('footer_logo_name');
        }
        $setting = Setting::findOrFail(1);
        $setting->header_logo = $header_logo_name;
        $setting->favicon_icon = $favicon_icon_name;
        $setting->footer_logo = $footer_logo_name;
        $setting->admin_email = request('admin_email');
        $setting->mobile_number = request('mobile_number');
        $setting->facebook_link = request('facebook_link');
        $setting->linkedin_link = request('linkedin_link');
        $setting->twitter_link = request('twitter_link');
        $setting->youtube_link = request('youtube_link');
        $setting->instagram_link = request('instagram_link');
        $setting->google_play_app_link = request('google_play_app_link');
        $setting->ios_app_link = request('ios_app_link');
        $setting->address = request('address');
        $setting->save();
        return redirect()->route('admin.settings1')
            ->with('success', 'Updated Successfully !');
    }

   public function settings2()
{
    $settings = Generalsetting::find('67f74619b17014c07aff95b6');
    return view('admin.settings2', compact('settings'));
}

public function settings2update(Request $request)
{
    $setting = Generalsetting::findOrFail('67f74619b17014c07aff95b6');

    $setting->title = $request->input('title');
    $setting->terms_and_condition = $request->input('terms_and_condition');
    $setting->privacy_policy = $request->input('privacy_policy');
    $setting->about_us = $request->input('about_us');
    $setting->enquiry_email = $request->input('enquiry_email');
    $setting->mobile = $request->input('mobile');
    $setting->how_it_work = $request->how_it_work;
    $setting->save();

    return redirect()->route('admin.settings2')
        ->with('success', 'Settings Updated Successfully !');
}


    public function settings3()
    {
        $settings = Generalsetting::find(1);
        return view('admin.settings3', ['settings' => $settings]);
    }

    public function settings3update(Request $request, Generalsetting $setting)
    {
        $setting = Generalsetting::findOrFail(1);

        $setting->terms_and_condition = request('terms_and_condition');
        $setting->save();
        return redirect()->route('admin.settings3')
            ->with('success', 'Updated Successfully !');
    }

    public function settings4()
    {
        $settings = Generalsetting::find(1);
        return view('admin.settings4', ['settings' => $settings]);
    }

    public function settings4update(Request $request, Generalsetting $setting)
    {
        $setting = Generalsetting::findOrFail(1);

        $setting->privacy_policy = request('privacy_policy');
        $setting->save();
        return redirect()->route('admin.settings4')
            ->with('success', 'Updated Successfully !');
    }


    public function listpromotion()
    {
        $data = AdvertismentPromo::whereNOTIN('status', [2])->orderBy('updated_at', 'DESC')->get();
        $addbutton = 'Promo Image';
        $importbutton = 'Upload Excel';
        $title = 'Promo Image List';
        $listurl = route('admin.promotion.listpromotion');
        $addurl = route('admin.promotion.create_promotion');
        $importurl = '';
        $editurl = 'admin.promotion.edit_promotion';
        $destroyurl = route('admin.promotion.destroy_promotion');
        return view('admin.promotion.listpromotion', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl'));
    }

    public function create_promotion()
    {
        $title = 'Add Promo Image';
        $listname = 'Promo Image';
        $listurl = route('admin.promotion.listpromotion');
        return view('admin.promotion.create', compact('title', 'listurl', 'listname'));
    }

    public function store_promotion(Request $request)
    {
        $this->validate($request, [
            'photo' => 'required',
            'status' => 'required',
        ]);

        $fileNameWithTheExtension = request('photo')->getClientOriginalName();
        $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
        $extension = request('photo')->getClientOriginalExtension();
        $logo_name = 'logo_' . $fileName . '_' . time() . '.' . $extension;
        $filePath = request('photo')->move(public_path('promos/'), $logo_name);
        $image_name = $logo_name;
        $a = new AdvertismentPromo();
        $a->title = '';
        $a->promo_type = 'app';
        $a->status = $request->status;
        $a->image = $image_name;
        $a->save();
        return redirect()->route('admin.promotion.listpromotion')
            ->with('success', 'Promo image Created Successfully !');
    }

    public function destroy_promotion(Request $request)
    {
        $input = $request->all();
        $a = AdvertismentPromo::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Image Deleted Successfully !');
    }

    public function settings4invoice()
    {
        $settings = Generalsetting::find(1);
        return view('admin.settings4invoice', ['settings' => $settings]);
    }
    public function settings4invoiceupdate(Request $request, Generalsetting $setting)
    {
        $setting = Generalsetting::findOrFail(1);
        $setting->ecomm_invoice_title = request('ecomm_invoice_title');
        $setting->ecomm_invoice_subtitle = request('ecomm_invoice_subtitle');
        $setting->ecomm_invoice_gstin = request('ecomm_invoice_gstin');
        $setting->ecomm_invoice_ref_first = request('ecomm_invoice_ref_first');
        $setting->ecomm_invoice_ref_second = request('ecomm_invoice_ref_second');
        $setting->ecomm_invoice_financial_year = request('ecomm_invoice_financial_year');
        $setting->save();
        return redirect()->route('admin.settings4invoice')
            ->with('success', 'Invoice Format Updated Successfully !s');
    }

    public function import_country()
    {
        $title = 'Upload Book Type File';
        $listname = 'Book Type';
        $listurl = route('admin.import_country');
        $samplebutton = 'Sample File';
        $addurl = route('admin.store_import_country');
        $sampleurl = asset('project/public/temp_files/1657351464-countrycsv.csv');
        return view('admin.country.import',compact('title','listurl','listname','samplebutton','sampleurl','addurl'));
    }

    public function import_country2()
    {
        $title = 'Upload Book Type File';
        $listname = 'Book Type';
        $listurl = route('admin.import_country');
        $samplebutton = 'Sample File';
        $addurl = route('admin.store_import_country2');
        $sampleurl = asset('project/public/temp_files/1657351464-countrycsv.csv');
        return view('admin.country.import',compact('title','listurl','listname','samplebutton','sampleurl','addurl'));
    }

    public function store_import_country(Request $request)
    {
        $this->validate($request, [
                    'importfile' => 'required|mimes:csv,txt',
                    ]);
        $log = "";
        $filename = '';
        if ($file = $request->file('importfile'))
        {
            $filename = time().'-'.$file->getClientOriginalName();
            $file->move(public_path('/temp_files'),$filename);
        }
        $poses = [];
        $datas = "";
        $file = fopen(public_path('/temp_files/'.$filename),"r");
        $i = 0;
        while (($line = fgetcsv($file)) !== FALSE)
        {
            if($i > 0)
            {
                $poseId = $line[0]; // 1
                $poses[$poseId] = [
                    'id' => $poseId,
                    'name' => $line[1],
                    'category' => $line[2],
                    'profile' => $line[3],
                    'type' => $line[4],
                    'gender' => $line[5],
                    'position' => $line[6],
                    'image' => $line[7],
                ];
            }
            $i++;
        }
        session(['pose_master' => $poses]);
        return redirect()->route('admin.master.country')
                        ->with('success','Book Type impoted successfully '.$log);
    }

    public function store_import_country2(Request $request)
    {
        $this->validate($request, [
                    'importfile' => 'required|mimes:csv,txt',
                    ]);
        $log = "";
        $filename = '';
        if ($file = $request->file('importfile'))
        {
            $filename = time().'-'.$file->getClientOriginalName();
            $file->move(public_path('/temp_files'),$filename);
        }
        // $poses = [];
        $poses = session('pose_master');
        $datas = "";
        $file = fopen(public_path('/temp_files/'.$filename),"r");
        $i = 0;
        while (($line = fgetcsv($file)) !== FALSE)
        {
            if($i > 0)
            {
                $poseId = $line[0];
                $stepText = $line[1];

                // push step inside session
                $poses[$poseId]['steps'][] = $stepText;
            }
            $i++;
        }
        session(['pose_master' => $poses]);
        // session(['pose_master' => $poses]);
        $this->save_pose_to_db();
    }

    public function save_pose_to_db()
    {
        $poses = session('pose_master');

        foreach($poses as $key => $pose) {
            $cat = Practiceyogacategories::where('name', $pose['position'])->first();
            $categoryId = new ObjectId($cat->id);
            $a = new Practiceyogaposes();
            $a->name = $pose['name'];
            $a->Type_of_tracking = $pose['category'];
            $a->Angle = $pose['profile'];
            $a->symetric = $pose['type'];
            $a->categoryId = $categoryId;
            $a->image = $pose['image'];
            $a->status = 1;

            $toDo = [];
            foreach ($pose['steps'] as $step) {
                $toDo[] = [
                    'points' => $step,
                    '_id' => (string) new \MongoDB\BSON\ObjectId()
                ];
            }

            $a->to_do = $toDo;
            $a->created_at = now();
            $a->updated_at = now();
            // dd($a);
            $a->save();
            $levels = [
                [ "levelname" => "Beginner" ],
                [ "levelname" => "Intermediate" ],
                [ "levelname" => "Advanced" ],
            ];

            foreach ($levels as $lvl) {
                $level = new Yogaposeslevels();
                $level->levelname   = $lvl['levelname'];
                $level->video       = "";
                $level->posesId     = new ObjectId($a->id);
                $level->duration    = 10;
                $level->about       = "";
                $level->status      = "1";
                $level->to_do       = [];
                $level->created_at  = now();
                $level->updated_at  = now();
                $level->save();
            }
        }

        // remove session
        session()->forget('pose_master');

        // return back()->with('success', 'All yoga poses imported successfully.');
    }
}
