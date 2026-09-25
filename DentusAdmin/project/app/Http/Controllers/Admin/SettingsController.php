<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Models\Setting;


use Illuminate\Support\Facades\Storage;

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
        return view('admin.settings',['settings' => $setting]);
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
        $data = $request->validate([
            'title' => 'required|max:255',
            'footer' => 'required|max:255',
            'helpline_number' => 'nullable|max:255',
            'support_email' => 'nullable|email|max:255',
            'adminnumber' => 'nullable|max:255',
            'fb_link' => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
            'twitter_link' => 'nullable|url|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'enquiry_email' => 'nullable|email|max:255',
            'address' => 'nullable|max:500',
        ]);

        if (request('logo')) {
            $fileNameWithTheExtension = request('logo')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('logo')->getClientOriginalExtension();
            $logo_name = 'logo_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('logo')->move('adminassets/img', $logo_name);
        } else {
            $logo_name = request('logoname');
        }

        if (request('favicon')) {
            $fileNameWithTheExtension = request('favicon')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('favicon')->getClientOriginalExtension();
            $favicon_name = 'favicon_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('favicon')->move('adminassets/img', $favicon_name);
        } else {
            $favicon_name = request('favicon_name');
        }

        $setting = Generalsetting::findOrFail(1);

        $setting->title = request('title');
        $setting->footer = request('footer');
        $setting->logo = $logo_name;
        $setting->favicon = $favicon_name;
        $setting->helpline_number = request('helpline_number');
        $setting->support_email = request('support_email');
        $setting->adminnumber = request('adminnumber');
        $setting->fb_link = request('fb_link');
        $setting->linkedin_link = request('linkedin_link');
        $setting->twitter_link = request('twitter_link');
        $setting->youtube_link = request('youtube_link');
        // $setting->adminname = request('adminname');
        // $setting->appstore_link = request('appstore_link');
        $setting->enquiry_email = request('enquiry_email');
        $setting->address = request('address');
        $setting->refund_deduction_percent = request('refund_deduction_percent');
        $setting->insta_link = request('insta_link');
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

    public function settings2()
    {
        $settings = Generalsetting::find(1);
        return view('admin.settings2',['settings' => $settings]);
    }

    public function settings2update(Request $request, Generalsetting $setting)
    {
        $setting = Generalsetting::findOrFail(1);
        $setting->about_us_footer = request('about_us_footer');
        $setting->terms_and_condition = request('terms_and_condition');
        $setting->privacy_policy = request('privacy_policy');
        $setting->contact_us = request('contact_us');
        $setting->loyality_program_about = request('loyality_program_about');

        $setting->save();
        return redirect()->route('admin.settings2')
                        ->with('success','Updated successfully !');
    }

    public function settings4()
    {
        $settings = Generalsetting::find(1);
        return view('admin.settings4',['settings' => $settings]);
    }

    public function settings4update(Request $request, Generalsetting $setting)
    {
        $setting = Generalsetting::findOrFail(1);
        $setting->free_call = request('free_call');
        $setting->free_call_minutes = request('free_call_minutes');
        $setting->save();
        return redirect()->route('admin.settings4')
                        ->with('success','Updated successfully');
    }



    public function settings3()
    {
        $settings = Setting::find(1);
        return view('admin.settings3',['settings' => $settings]);
    }

    public function settings3update(Request $request, Generalsetting $setting)
    {
        $setting = Setting::findOrFail(1);

        $setting->about_us = request('about_us');
        $setting->terms_and_condition = request('terms_and_condition');
        $setting->privacy_policy = request('privacy_policy');
        $setting->save();
        return redirect()->route('admin.settings3')
                        ->with('success','Content updated successfully');
    }

    public function listpromotion()
    {
        $data = AdvertismentPromo::whereNOTIN('status',[2])->orderBy('updated_at','DESC')->get();
        $addbutton = 'Promo Image';
        $importbutton = 'Upload Excel';
        $title = 'Promo Image List';
        $listurl = route('admin.promotion.listpromotion');
        $addurl = route('admin.promotion.create_promotion');
        $importurl = '';
        $editurl = 'admin.promotion.edit_promotion';
        $destroyurl = route('admin.promotion.destroy_promotion');
        return view('admin.promotion.listpromotion',compact('data','title','listurl','addbutton','importbutton','addurl','importurl','editurl','destroyurl'));
    }

    public function create_promotion()
    {
        $title = 'Add Promo Image';
        $listname = 'Promo Image';
        $listurl = route('admin.promotion.listpromotion');
        return view('admin.promotion.create',compact('title','listurl','listname'));
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
        $logo_name = 'logo_'.$fileName . '_' . time() . '.' . $extension;
        $filePath = request('photo')->move(public_path('promos/'), $logo_name);
        $image_name = $logo_name;
        $a = new AdvertismentPromo();
        $a->title = '';
        $a->promo_type = 'app';
        $a->status = $request->status;
        $a->image = $image_name;
        $a->save();
        return redirect()->route('admin.promotion.listpromotion')
                        ->with('success','promo image created successfully');
    }

    public function destroy_promotion(Request $request)
    {
        $input = $request->all();
        $a = AdvertismentPromo::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
                        ->with('success','image deleted successfully');
    }

    public function settings4invoice()
    {
        $settings = Generalsetting::find(1);
        return view('admin.settings4invoice',['settings' => $settings]);
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
                        ->with('success','Invoice Format updated successfully');
    }
}
