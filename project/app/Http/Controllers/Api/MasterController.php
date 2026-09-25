<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Generalsetting;
use App\Models\MasterCollegeInstitute;
use App\Models\MasterDegree;
use App\Models\MasterRegistraionCouncil;
use App\Models\MasterServices;
use App\Models\MasterSpecialsation;
use App\Models\MasterLangauage;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Blog;
use App\Models\SeasonalOffers;
use App\Models\OfferType;
use App\Models\MasterTemplate;
use App\Models\MasterTax;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;
use Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
class MasterController extends Controller
{

    public function masterLangSpeciaService(Request $request) {
        $Langauage = MasterLangauage::select('name','id')->where('status',1)->orderBy('position','ASC')->get();
        $Specialsation = MasterSpecialsation::select('name','id')->where('status',1)->orderBy('position','ASC')->get();
        $Services = MasterServices::select('name','id')->where('status',1)->orderBy('position','ASC')->get();
        $response = ['status' => true, 'msg' => 'List', 'Langauage' => $Langauage,'Specialsation' =>$Specialsation,'Services'=>$Services];
        return response($response, 200);
    }

    public function masterDegreeCollege(Request $request) {
        $college = MasterCollegeInstitute::select('name','id')->where('status',1)->orderBy('position','ASC')->get();
        $degree = MasterDegree::select('name','id')->where('status',1)->orderBy('position','ASC')->get();
        $response = ['status' => true, 'msg' => 'List', 'college' => $college,'degree' =>$degree];
        return response($response, 200);
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
        $City = city::whereNOTIN('status',[2])->where('status',1)->where('state_id',$input['state_id'])->orderBy('name', 'ASC')->get();
        if(!empty($City)) {
            $response = ['status' => true, 'msg' => 'City List', 'data' => $City];   
            return response($response, 200);
        } else {
            $response = ['status' => false, 'msg' => 'Sorry ! No City found.'];  
            return response($response, 422);
        }
    }

    public function mastertheme(Request $request) {
        $theme = MasterTemplate::where('status',1)->orderBy('position','ASC')->get();
        $response = ['status' => true, 'msg' => 'List', 'theme' => $theme,'path' =>asset('content/theme/')];
        return response($response, 200);
    }

    public function mastercouncil(Request $request) {
        $theme = MasterRegistraionCouncil::where('status',1)->orderBy('position','ASC')->get();
        $response = ['status' => true, 'msg' => 'List', 'list' => $theme];
        return response($response, 200);
    }

    public function mastertax(Request $request) {
        $tax = MasterTax::where('status',1)->get();
        $response = ['status' => true, 'msg' => 'List', 'list' => $tax];
        return response($response, 200);
    }



    public function addBlog(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'description' => 'required',
            'video_link' => 'required',
            'image' => 'required',
        ]);

        $image_name = 'default';
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_blog_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/blogs', $image_name);
                
            }

        $a = new Blog();
        $a->user_id = auth()->user()->id;
        $a->name = $request->name;
        $a->type = $request->type;
        $a->description = $request->description;
        $a->video_link = $request->video_link;
        $a->position = $request->position;
        $a->image =  $image_name;
        $a->status = 1;
        $a->save();

        return response()->json(['status' => true,'message' => 'Blog created successfully', 'blog' => $a], 201);
    }

    public function editBlog(Request $request)
    {
        // $validated = $request->validate([
        //     'name' => 'required',
        //     'type' => 'required',
        //     'description' => 'required',
        //     'video_link' => 'required',
        //     // 'image' => 'required',
        // ]);

        $a = Blog::where('id',$request->id)->first();
        $image_name = $a->image;
        if (request('image')) 
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand().'_blog_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/blogs', $image_name);
            
        }
        $a->name = $request->name;
        $a->type = $request->type;
        $a->description = $request->description;
        $a->video_link = $request->video_link;
        $a->position = $request->position;
        $a->image =  $image_name;
        $a->save();

        return response()->json(['status' => true,'message' => 'Blog updated successfully', 'blog' => $a], 201);
    }

    public function deleteBlog(Request $request)
    {
        $a = Blog::where('id',$request->id)->first();
        $a->status =  2;
        $a->save();

        return response()->json(['status' => true,'message' => 'Blog deleted successfully', 'blog' => $a], 201);
    }




    public function BlogList(Request $request) {
        // Retrieve all blogs for the authenticated user
        $blogs = Blog::where('user_id', auth()->user()->id)->where('type',$request->type)->orderBy('updated_at','ASC')->where('status',1)->get();
        $blogs1 = Blog::where('user_id', auth()->user()->id)->where('status',1)->count();
        $totalquantity = $this->getquantity(14);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-$blogs1;
        }
        // Loop through each blog and update the image URL
        $blogs->each(function ($blog) {
            $blog->image = asset('content/blogs') . '/' . $blog->image;
        });
    
        // Return the response with the blogs and status
        return response()->json(['status' => true, 'blog' => $blogs,'canaddchild'=>$canaddchild], 200);
    }


    public function BlogSingleList(Request $request) {
        // Retrieve all blogs for the authenticated user
        $blogs = Blog::where('id', $request->blog_id)->first();
        $blogs->image = asset('content/blogs') . '/' . $blogs->image;
    
        // Return the response with the blogs and status
        return response()->json(['status' => true, 'blog' => $blogs], 200);
    }


    public function SeasonalPromotions(Request $request)
    {
        $validated = $request->validate([
            'offer_type' => 'required',
            'treat_package_id' => 'required',
            'discount' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'image' => 'required',
        ]);

        $image_name = 'default';
        if (request('image')) 
        {
            if (strpos(request('image'), 'https') !== false) 
            {
                $image_name = str_replace(asset('content/SeasonalOffers').'/', '', request('image'));

            }
            else if (strpos(request('image'), 'http') !== false) 
            {
                $image_name = str_replace(asset('content/SeasonalOffers').'/', '', request('image'));

            }
             else {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_blog_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/SeasonalOffers', $image_name);
            }
        }

        $a = new SeasonalOffers();
        $a->user_id = auth()->user()->id;
        $a->created_by = 1;
        $a->offer_type = $request->offer_type;
        $a->treat_package_id = $request->treat_package_id;
        $a->discount = $request->discount;
        $a->start_date = $request->start_date;
        $a->end_date = $request->end_date;
        $a->image =  $image_name;
        $a->status = 1;
        $a->save();

        return response()->json(['status' => true,'message' => 'SeasonalOffers created successfully', 'blog' => $a], 201);
    }



    public function UpdateSeasonalPromotions(Request $request)
    {
        $validated = $request->validate([
            'offer_type' => 'required',
            'treat_package_id' => 'required',
            'discount' => 'required',
        ]);

        $a = SeasonalOffers::find($request->seasonal_id);

        $image_name = $a->image;
        if (request('image')) 
        {
            if (strpos(request('image'), 'https') !== false) 
            {
                $image_name = str_replace(asset('content/SeasonalOffers').'/', '', request('image'));

            }
            else if (strpos(request('image'), 'http') !== false) 
            {
                $image_name = str_replace(asset('content/SeasonalOffers').'/', '', request('image'));

            }
             else {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_blog_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/SeasonalOffers', $image_name);
            }
        }

        $a->offer_type = $request->offer_type;
        $a->treat_package_id = $request->treat_package_id;
        $a->discount = $request->discount;
        $a->start_date = $request->start_date;
        $a->end_date = $request->end_date;
        $a->image =  $image_name;
        $a->status = 1;
        $a->save();

        return response()->json(['status' => true,'message' => 'SeasonalOffers update successfully', 'blog' => $a], 201);
    }

    public function SeasonalPromotionsdelete(Request $request)
    {
        $a = SeasonalOffers::find($request->seasonal_id);
        $a->status = 2;
        $a->save();

        return response()->json(['status' => true,'message' => 'SeasonalOffers delete successfully'], 201);
    }


    public function SeasonalPromotionsList(Request $request) {
        $totalquantity = $this->getquantity2(17);
        $blogs = SeasonalOffers::where(function ($query) {
            $query->where('user_id', auth()->user()->id)
              ->where('created_by', 1);
        })
        ->where('status',1)
        ->orderBy('start_date')
        ->get();
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($blogs);
        }
        elseif ($totalquantity < 0) {
           $canaddchild = 1;
        }
        
        $blogs->each(function ($blog) {
            $treatment = DB::table('treatments')
                ->where('id', $blog->treat_package_id)
                ->first(['treatment_name']);
    
            if ($treatment) {
                $blog->treatment_name = $treatment->treatment_name;
            } else {
                $blog->treatment_name = null;
            }
    
            $blog->image = asset('content/SeasonalOffers') . '/' . $blog->image;
        });
    
        return response()->json(['status' => true, 'seasonal' => $blogs,'canaddchild'=>$canaddchild], 200);
    }

    public function promotionsadmin(Request $request) {
        $blogs = SeasonalOffers::where('created_by', 0)
        ->where('status', 1)
        ->orderBy('start_date')
        ->get();

        $blogs->each(function ($blog) {
            $treatment = DB::table('treatments')
                ->where('id', $blog->treat_package_id)
                ->first(['treatment_name']);
    
            if ($treatment) {
                $blog->treatment_name = $treatment->treatment_name;
            } else {
                $blog->treatment_name = null;
            }
    
            $blog->image = asset('content/SeasonalOffers') . '/' . $blog->image;
        });
    
        return response()->json(['status' => true, 'seasonal' => $blogs], 200);
    }




    public function OfferType(Request $request) {
        // Retrieve all blogs for the authenticated user
        $offer = OfferType::get();
        
        // Return the response with the blogs and status
        return response()->json(['status' => true, 'Offer' => $offer], 200);
    }
    
    


    public function PatientsPlansAdd(Request $request)
    {
        $validated = $request->validate([
            'offer_type' => 'required',
            'treat_package_id' => 'required',
            'discount' => 'required',
            'offer_validity' => 'required',
            'image' => 'required',
        ]);


        // Create the PackTreatment record
        $packTreatment = new PackTreatment();
        $packTreatment->user_id = auth()->user()->id;
        $packTreatment->name = $request->name;
        $packTreatment->user_type = 2;
        $packTreatment->validity = $request->validity;
        $packTreatment->type = 'Month';
        $packTreatment->price = $request->price;
        $packTreatment->discount_price = $request->discount_price;
        $packTreatment->status = 1;
        $packTreatment->save();

        // Loop through the treatment IDs and quantities if multiple
        foreach ($request->treatment_id as $key => $treatmentId) {
            $packFeature = new PackFeature();
            $packFeature->pack_id = $packTreatment->id;  // Use the ID from PackTreatment
            $packFeature->treatment_id = $treatmentId;
            $packFeature->quantity = $request->quantity[$key];  // Get corresponding quantity
            $packFeature->status = 1;
            $packFeature->save();
        }

        return response()->json(['status' => true,'message' => 'SeasonalOffers created successfully', 'blog' => $a], 201);
    }

    public function masterpoints($value='')
    {
        $a = Generalsetting::where('id',1)->select('total_invoice_points','referrer_a_friend_points_master','referred_after_booking_points_master','redemption_points_master')->first();
        return response()->json([
            'status' => true,
            'message' => 'list',
            'total_invoice_points' => explode(',', $a->total_invoice_points),
            'referrer_a_friend_points_master' => explode(',', $a->referrer_a_friend_points_master),
            'referred_after_booking_points_master' => explode(',', $a->referred_after_booking_points_master),
            'redemption_points_master' => explode(',', $a->redemption_points_master),
        ], 200);
    }

    public function getquantity($for)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();
        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

        
            foreach ($subscription->features as $feature) {
                if ($feature->id == $for) {
                    $pivotId = $feature->pivot->id;
                    $addon = $premiumAddons->get($pivotId);
                    if ($alreadySubscribed->subscription_id == 9) {
                        if ($addon) {
                            $feature12Quantity = $addon->quantity;
                        } else {
                            $feature12Quantity = $feature->pivot->basic_quantity ?? 0;
                        }
                    } else {
                        $feature12Quantity = $feature->pivot->quantity ?? 0;
                    }

                    break;
                }
            }
        }
        
        return $feature12Quantity;
    }

    public function getquantity2($for)
    {
        $feature12Quantity = 0;
        $alreadySubscribed = UserSubscription::where('user_id', auth()->user()->id)
            ->where('status', 1)
            ->first();

        if ($alreadySubscribed) {
            $subscription = Subscription::with(['features'])->find($alreadySubscribed->subscription_id);

            $premiumAddons = UserPremiumAddon::where('user_id', auth()->user()->id)
                ->where('subscription_id', $alreadySubscribed->id)
                ->get()
                ->keyBy('premium_feature_id');

            foreach ($subscription->features as $feature) {
                if ($feature->id == $for) {
                    $pivotId = $feature->pivot->id;
                    $addon = $premiumAddons->get($pivotId);

                    if ($alreadySubscribed->subscription_id == 9) {
                        if ($addon) {
                            if ($addon->unlimited == 1) {
                              $feature12Quantity = -1;  
                            } else
                            $feature12Quantity = $addon->quantity;
                        } else {
                            $feature12Quantity = $feature->pivot->basic_quantity ?? 0;
                        }
                    } else {
                        $feature12Quantity = $feature->pivot->quantity ?? 0;
                    }

                    break;
                }
            }
        }
        return $feature12Quantity;
    }
}
