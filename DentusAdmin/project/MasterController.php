<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\AppLanguage;
use App\Models\MasterLangauage;
use App\Models\AreaOfExpertise;
use App\Models\Generalsetting;
use App\Models\AstroShopCategory;
use App\Models\Video;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\SuggestionCategories;
use App\Models\SuggestionSubCategories;
use App\Models\SuggestionList;
use App\Models\Faq;
use App\Models\MasterDonation;
use App\Models\ThreadColor;
use App\Models\Mantra;

use App\Models\Rudrakshi;
use App\Models\SemiPreciousStone;
use App\Models\PreciousStone;



use Hash;
use DB;
use Carbon\Carbon;
use File;
class MasterController extends Controller
{


    public function suggestion_categories(Request $request) {
        $l = SuggestionCategories::where('status',1)->orderBy('id','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 422);
        }
    }



    public function get_donation(Request $request) {
        $l = MasterDonation::where('status',1)->orderBy('id','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 422);
        }
    }
    public function get_threadcolor(Request $request) {
        $l = ThreadColor::where('status',1)->orderBy('id','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 422);
        }
    }
    public function get_mantras(Request $request) {
        $l = Mantra::where('status',1)->orderBy('id','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 422);
        }
    }



    
    public function get_remedy_data(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        if ($input['type'] == "Mantra") {
            $SubCategories = Mantra::where('status',1)->orderBy('position','ASC')->get();
            foreach ($SubCategories as $key ) {
                $key->is_selected = "";
            }
        }
        elseif ($input['type'] == "Donation") {
            $SubCategories = MasterDonation::where('status',1)->orderBy('position','ASC')->get();
            foreach ($SubCategories as $key ) {
                $key->is_selected = "";
            }
        }
        elseif ($input['type'] == "ThreadColor") {
            $SubCategories = ThreadColor::where('status',1)->orderBy('position','ASC')->get();
            foreach ($SubCategories as $key ) {
                $key->is_selected = "";
            }
        }
        elseif ($input['type'] == "Rudrakshi") {
            $SubCategories = Rudrakshi::where('status',1)->orderBy('position','ASC')->get();
            foreach ($SubCategories as $key ) {
                $key->is_selected = "";
            }
        }
        elseif ($input['type'] == "SemiPreciousStone") {
            $SubCategories = SemiPreciousStone::where('status',1)->orderBy('position','ASC')->get();
            foreach ($SubCategories as $key ) {
                $key->is_selected = "";
            }
        }
        elseif ($input['type'] == "PreciousStone") {
            $SubCategories = PreciousStone::where('status',1)->orderBy('position','ASC')->get();
            foreach ($SubCategories as $key ) {
                $key->is_selected = "";
            }
        }

        if(!empty($SubCategories)) {
            $response = ['status' => true, 'msg' => 'List', 'data' => $SubCategories];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No Suggestion List found.'];  
            return response($response, 422);
        }
    }




    public function suggestion_list(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'categories_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }

        // print_r($input['categories_id']); die;
        $SubCategories = SuggestionSubCategories::where('status',1)->where('category_id',$input['categories_id'])->orderBy('id', 'ASC')->get();
// dd($SubCategories);
        foreach ($SubCategories as $key ) {

            $key->image = asset('content/suggestion').'/'.$key->image;


            $list = SuggestionList::where('status',1)->where('category_id',$input['categories_id'])->where('sub_category_id',$key->id)->orderBy('id', 'ASC')->get();
            $key->list = $list;
            $key->is_selected = "";
        }

        if(!empty($SubCategories)) {
            $response = ['status' => true, 'msg' => 'City List', 'data' => $SubCategories];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No Suggestion List found.'];  
            return response($response, 422);
        }
    }

    public function AppLanguage(Request $request) {
        $l = AppLanguage::where('status',1)->orderBy('position','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 422);
        }
    }

    public function masterLanguages(Request $request) {
        $l = MasterLangauage::where('status',1)->orderBy('position','ASC')->get();
        if(!empty($l)) {

            foreach ($l as $key) {
                $key->is_selected = "";
            }


            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 200);
        }
    }

    public function areaOfExpertise(Request $request) {
        $l = AreaOfExpertise::where('status',1)->orderBy('position','ASC')->get();
        if(!empty($l)) {
            foreach ($l as $key) {
                $name = json_decode($key->name,true);
                $key->name = $name[1] ?? '';
                $key->is_selected = "";
            }
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No  found.'];  
            return response($response, 200);
        }
    }

    public function tempupload(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Sorry ! No  found.'];  
        if (request('image')) 
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand().'__' . time() . '.' . $extension;
            $filePath = request('image')->move('content/tempfolder', $image_name);
            $response = ['status' => true, 'filename' => $image_name,"path"=>asset('content/tempfolder').'/'];  
            
        }
        return response($response, 200);
        
    }

    public function termsPrivacyAbout(Request $request)
    {
        $settings = Generalsetting::find(1);
        $response = ['status' => true, "about_us" => $settings->about_us, "terms_and_condition" => $settings->terms_and_condition, "privacy_policy" => $settings->privacy_policy];   
        return response($response, 200);
    }

    public function astroshopcategory($value='')
    {
        $category =  AstroShopCategory::where('status',1)
                        ->select('name','slug','id','image')
                        ->orderBy('position','DESC')
                        ->get();
        if(!empty($category)) {
            foreach ($category as $cat) {
                $cat->image = asset('content/astroshop_category').'/'.$cat->image;
                $name = json_decode($cat->name,true);
                $cat->name = $name[1] ?? 'Astro N';
                if ($name[auth()->user()->lang_id]) {
                    $cat->name = $name[auth()->user()->lang_id] ?? 'Astro N';
                }
            }
            $response = ['status' => true, 'msg' => 'l List', 'data' => $category];   
            return response($response, 200); 
        }
        else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 200);
        }
    }

    public function astrovideos($value='')
    {
        $astrovideos =  Video::where('status',1)
                        ->where('user_type',1)->where('type','astrovideos')
                        ->select('name','video_url','id','image','created_at')
                        ->orderBy('position','DESC')
                        ->get();
        if(!empty($astrovideos)) {
            foreach ($astrovideos as $av) {
                $av->image = asset('content/videos_image').'/'.$av->image;
                $name = json_decode($av->name,true);
                $av->name = $name[1] ?? 'Astro N';
                if ($name[auth()->user()->lang_id]) {
                    $av->name = $name[auth()->user()->lang_id] ?? 'Astro N';
                } 
            }
            $response = ['status' => true, 'msg' => 'l List', 'data' => $astrovideos];   
            return response($response, 200); 
        }
        else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 200);
        }
    }

    public function blogslist($value='')
    {
        $blogs =  Blog::where('status',1)
                        ->select('title','image','id','description','published_date')
                        ->orderBy('position','DESC')
                        ->get();
        if(!empty($blogs)) {
            foreach ($blogs as $blg) {
            $blg->image = asset('content/blogs').'/'.$blg->image;
            $title = json_decode($blg->title,true);
         
            $blg->title = $title[1] ?? 'Astro N';
            //    print_r( $blg->title ); die;

            // if ($title[auth()->user()->lang_id]) {
            //     $blg->title = $title[auth()->user()->lang_id] ?? 'Astro N';
            // } 
            $description = json_decode($blg->description,true);
            $blg->description = $description[1] ?? 'Astro N';
            // if ($description[auth()->user()->lang_id]) {
            //     $blg->description = $description[auth()->user()->lang_id] ?? '';
            // } 
        }
            $response = ['status' => true, 'msg' => 'l List', 'data' => $blogs];   
            return response($response, 200); 
        }
        else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 200);
        }
    }

    public function tutorialvideos($value='')
    {
        $astrovideos =  Video::where('status',1)
                        ->where('user_type',1)->where('type','tutorialvideos')
                        ->select('name','video_url','id','image','created_at')
                        ->orderBy('position','DESC')
                        ->get();
        if(!empty($astrovideos)) {
            foreach ($astrovideos as $av) {
                $av->image = asset('content/videos_image').'/'.$av->image;
                $name = json_decode($av->name,true);
                $av->name = $name[1] ?? 'Astro N';
                if ($name[auth()->user()->lang_id]) {
                    $av->name = $name[auth()->user()->lang_id] ?? 'Astro N';
                } 
            }
            $response = ['status' => true, 'msg' => 'l List', 'data' => $astrovideos];   
            return response($response, 200); 
        }
        else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 200);
        }
    }

    public function testimonial($value='')
    {
        $testimonial = Testimonial::where('status',1)->where('astrologer_id',0)
                        ->orderBy('position','ASC')
                        // ->take(5)
                        ->get();
        if(!empty($testimonial)) {
            foreach ($testimonial as $test) {
                $image = $test->muser->image ?? 'default.png';
                $name =  'User';
                $test->image = asset('content/master-user').'/'.$image;
                $title = json_decode($test->muser->name,true);
                $test->name = $title[1] ?? 'Astro N';
                if ($title[auth()->user()->lang_id]) {
                    $test->name = $title[auth()->user()->lang_id] ?? 'Astro N';
                } 
                $description = json_decode($test->description,true);
                $test->description = $description[1] ?? 'Astro N';
                if ($description[auth()->user()->lang_id]) {
                    $test->description = $description[auth()->user()->lang_id] ?? '';
                } 
            }
            $response = ['status' => true, 'msg' => 'l List', 'data' => $testimonial];   
            return response($response, 200); 
        }
        else {
            $response = ['status' => false, 'msg' => 'Sorry ! No data found.'];  
            return response($response, 200);
        }
    }

    public function country(Request $request) {
        
        $State = Country::where('flag',[1])->whereIN('id',[101])->orderBy('position','ASC')->get();
        $State1 = Country::where('flag',[1])->whereNOTIN('id',[101])->orderBy('name','ASC')->get();
        if(!empty($State)) {
            foreach ($State1 as $key) {
                $State[] = $key;
            }
            $response = ['status' => true, 'msg' => 'Country List', 'data' => $State];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No State found.'];  
            return response($response, 422);
        }
    }

    public function countryecommerce(Request $request) {
        
        $State = country::where('flag',[1])->where('ecomm_avail',1)->whereIN('id',[101,231,166,179,194,18,117,245,204,161,113,83,218,229,232,233,39])->orderBy('position','ASC')->get();
        $State1 = country::where('flag',[1])->where('ecomm_avail',1)->whereNOTIN('id',[101,231,166,179,194,18,117,245,204,161,113,83,218,229,232,233,39])->orderBy('name','ASC')->get();
        if(!empty($State)) {
            foreach ($State1 as $key) {
                $State[] = $key;
            }
            $response = ['status' => true, 'msg' => 'Country List', 'data' => $State];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No State found.'];  
            return response($response, 422);
        }
    }

    public function state(Request $request) {
        if ($request->country_id) {
            $State = State::select('id','name as state_title')->where('country_id',$request->country_id)->whereNOTIN('status',[2])->where('status',1)->orderBy('name','ASC')->get();
            if(!empty($State)) {
                $response = ['status' => true, 'msg' => 'State List', 'data' => $State];   
                return response($response, 200);
            } else {
                $response = ['status' => false, 'msg' => 'Sorry ! No State found.'];  
                return response($response, 422);
            }
        }
        else {
            $State = State::select('id','name as state_title')->where('country_id',101)->whereNOTIN('status',[2])->where('status',1)->orderBy('name','ASC')->get();
            if(!empty($State)) {
                $response = ['status' => true, 'msg' => 'State List', 'data' => $State];   
                return response($response, 200);
            } else {
                $response = ['status' => false, 'msg' => 'Sorry ! No State found.'];  
                return response($response, 422);
            }    
        }
        
    }
    public function city(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'state_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $City = City::whereNOTIN('status',[2])->where('status',1)->where('state_id',$input['state_id'])->orderBy('name', 'ASC')->get();
        if(!empty($City)) {
            $response = ['status' => true, 'msg' => 'City List', 'data' => $City];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No City found.'];  
            return response($response, 422);
        }
    }

    public function faq(Request $request) {
        $l = Faq::where('status',1)->orderBy('position','ASC')->get();
        if(!empty($l)) {
            $response = ['status' => true, 'msg' => 'l List', 'data' => $l];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No language found.'];  
            return response($response, 422);
        }
    }

}
