<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserModels\Booking;
use App\Models\UserInformation;

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
        $data = request()->validate([
            'title' => 'required|max:255',
            'footer' => 'required|max:255',
            // 'helpline_number' => 'required|max:255',
            // 'support_email' => 'required|max:255',
        ]);

        if (request('logo')) 
        {
            $fileNameWithTheExtension = request('logo')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('logo')->getClientOriginalExtension();
            $logo_name = 'logo_'.$fileName . '_' . time() . '.' . $extension;
            $filePath = request('logo')->move('adminassets/images', $logo_name);
            
        }
        else
        {
            $logo_name = request('logoname');
        }

        

        if (request('favicon')) 
        {
            $fileNameWithTheExtension = request('favicon')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('favicon')->getClientOriginalExtension();
            $favicon_name = 'favicon_'.$fileName . '_' . time() . '.' . $extension;
            $filePath = request('favicon')->move('adminassets/images', $favicon_name);
        }
        else
        {
            $favicon_name = request('favicon_name');
        }


        
        if (request('thoughts_for_the_day_image')) 
        {
            $fileNameWithTheExtension = request('thoughts_for_the_day_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('thoughts_for_the_day_image')->getClientOriginalExtension();
            $thoughts_for_the_day_image_name = 'thoughts_for_the_day_image_'.$fileName . '_' . time() . '.' . $extension;
            $filePath = request('thoughts_for_the_day_image')->move('adminassets/images', $thoughts_for_the_day_image_name);
        }
        else
        {
            $thoughts_for_the_day_image_name = request('thoughts_for_the_day_image_name');
        }




        $setting = Generalsetting::findOrFail(1);

        $setting->title = request('title');
        $setting->footer = request('footer');
        $setting->logo = $logo_name;
        $setting->favicon = $favicon_name;
        $setting->thoughts_for_the_day_image = $thoughts_for_the_day_image_name;
        $setting->helpline_number = request('helpline_number');
        $setting->support_email = request('support_email');
        $setting->adminnumber = request('adminnumber');
        $setting->fb_link = request('fb_link');
        $setting->linkedin_link = request('linkedin_link');
        $setting->twitter_link = request('twitter_link');
        $setting->youtube_link = request('youtube_link');
        $setting->playstore_link = request('playstore_link');
        $setting->appstore_link = request('appstore_link');
        $setting->enquiry_email = request('enquiry_email');
        $setting->refer_inr = request('refer_inr');
        $setting->refer_usd = request('refer_usd');

        $setting->referee_inr = request('referee_inr');
        $setting->referee_usd = request('referee_usd');

        $setting->chat_reminder_number = request('chat_reminder_number');
        $setting->video_reminder_number = request('video_reminder_number');
        $setting->audio_reminder_number = request('audio_reminder_number');


        $setting->smssignature = request('smssignature');
        $setting->smssignature_modal = request('smssignature_modal');
        $setting->discount_percentage = request('discount_percentage');
        $setting->gift_percentage = request('gift_percentage');
        $setting->astroshop_percentage = request('astroshop_percentage');
        $setting->maintenance_user_app = request('maintenance_user_app');
        $setting->maintenance_user_app_msg = request('maintenance_user_app_msg');
        $setting->maintenance_astrologer_app = request('maintenance_astrologer_app');
        $setting->maintenance_astrologer_app_msg = request('maintenance_astrologer_app_msg');

        $setting->maintenance_chat = request('maintenance_chat');
        $setting->maintenance_call = request('maintenance_call');
        $setting->maintenance_video = request('maintenance_video');


        $setting->astrologer_maintenance_chat = request('astrologer_maintenance_chat');
        $setting->astrologer_maintenance_call = request('astrologer_maintenance_call');
        $setting->astrologer_maintenance_video = request('astrologer_maintenance_video');

        $setting->booking_caller_id_chat_video = request('booking_caller_id_chat_video');
        $setting->booking_caller_id_audio = request('booking_caller_id_audio');
        $setting->booking_caller_to_astrologer = request('booking_caller_to_astrologer');




        $setting->min_call_price_inr = request('min_call_price_inr');
        $setting->min_call_price_usd = request('min_call_price_usd');
        $setting->min_video_price_inr = request('min_video_price_inr');
        $setting->min_video_price_usd = request('min_video_price_usd');
        $setting->max_call_price_inr = request('max_call_price_inr');
        $setting->max_call_price_usd = request('max_call_price_usd');
        $setting->max_video_price_inr = request('max_video_price_inr');
        $setting->max_video_price_usd = request('max_video_price_usd');
        $setting->metrix_calculation = request('metrix_calculation');
        
        $setting->booking_caller_to_astrologerlivebroadcast  = request('booking_caller_to_astrologerlivebroadcast');
        $setting->booking_caller_for_reminder  = request('booking_caller_for_reminder');
        
        

        $setting->save();
        return redirect()->route('admin.settings.edit',1)
                        ->with('success','Settings updated successfully');
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
        $s = Generalsetting::find(1);
        return view('doctor.settings2',compact('s'));
    }

    public function settings2update(Request $request, Generalsetting $setting)
    {
        $a = User::where('id', auth()->user()->id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->loyalty_points_base_100 = $request->loyalty_points_base_100;
            $user_information->referrer_a_friend_points = $request->referrer_a_friend_points;
            $user_information->referred_after_booking_points = $request->referred_after_booking_points;
            $user_information->redemption_points = $request->redemption_points;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
        return redirect()->route('admin.settings2')
                        ->with('success','Updated successfully');
    }



    public function settings4($type)
    {
        $a = Generalsetting::select('about_us','privacy_policy','terms_and_condition','contact_us')->first();
        $content = '';
        if ($type == 'about_us') {
           $title = 'About Us';
           $content = $a->about_us;
        }
        if ($type == 'privacy_policy') {
            $title = 'Privacy Poilicy';
           $content = $a->privacy_policy;
        }
        if ($type == 'terms_and_condition') {
            $title = 'Terms & Condition';
           $content = $a->terms_and_condition;
        }
        if ($type == 'contact_us') {
            $title = 'Contact Us';
           $content = $a->contact_us;
        }
        return view('doctor.settings4',compact('content','title'));
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
        return view('doctor.settings3');
    }

    public function settings3update(Request $request)
    {
        $a = UserInformation::where('user_id', auth()->user()->id)->first();
        if ($a) {
            $a->partial_payment = $request->partial_payment === "on" ? 1 : 0;
            $a->partial_percentage = $request->partial_percentage ?? 0;
            $a->loyalty_points = $request->loyalty_points === "on" ? 1 : 0;
            $a->save();
        }
        return redirect()->back()
                        ->with('success','Updated successfully');
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
