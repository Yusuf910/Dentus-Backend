<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Validator;
use App\Models\Generalsetting;
use App\Models\Book;
use App\Models\Bookings;
use App\Models\BookVoucher;
use App\Models\UserActivity;
use App\Models\UserInfo;
use App\Models\QrCodeLinks;
use App\Models\FileLibrary;
use App\Models\GlobalFile;
use App\Models\BookManagementCate;
use App\Models\Bookmark;
use App\Models\UserNotification;
use App\Models\ClassRelationship;
use App\Models\ClassSubjectRelationship;
use App\Models\Enquiry;
use App\Models\Country;
use App\Models\AdvertismentPromo;
use App\Models\AdvertismentPromoRead;
use App\Models\ClassSubject;
use App\Traits\SdSendSms;
use App\Models\CommunityPost;
use App\Models\CommunityJoinStudent;
use App\Models\CommunityPostComment;
use App\Models\CommunityPostLikeDislike;
use App\Models\CommunityPostReport;
use App\Models\LoginActivity;
use App\Models\StudyMaterial;
use DB;
use Hash;
use Carbon;
class UserController extends Controller
{
    //
    use SdSendSms;
    public function profile_user()
    {
        $user_profile[] = User::where('id', auth()->user()->id)->first();
        $userinfo = UserInfo::where('user_id', auth()->user()->id)->first();
        $boardname = '';
        $boardid = '';
        $classname = '';
        $classid = '';
        $streamname = '';
        $streamid = '';
        $state_title = '';
        $stateid = '';
        $cityname = '';
        $cityid = '';
        $schoolname = '';
        $schoolid = '';
        $exper = '';
        $experinnum = 1;
        $pin = '';
        $country_id = '';
        $country_name = '';
        $schoolname1 = '';
        if ($userinfo) {
            // $boards= DB::table('boards')->where('id',$userinfo->board_id)->first();
            // if ($boards) {
            //     $boardsname = $boards->name;
            // }
            // $user_profile[0]->board_id = $userinfo->board_id;
            // $user_profile[0]->board_name = $boardsname;
            

            $selectidssubjects = array();
            if ($userinfo) {
               if (isset($userinfo->board->name)) {
                 $boardname = $userinfo->board->name;
                 $boardid = $userinfo->board_id;
               }

               if (isset($userinfo->getclass->name)) {
                 $classname = $userinfo->getclass->name;
                 $classid = $userinfo->class_id;
               }

               if (isset($userinfo->subjectstream->name)) {
                 $streamname = $userinfo->subjectstream->name;
                 $streamid = $userinfo->stream_id;
               }

               if (isset($userinfo->getstate->name)) {
                 $state_title = $userinfo->getstate->name;
                 $stateid = $userinfo->state_id;
                 $getcountry = Country::where('id',$userinfo->getstate->country_id)->first();
                 if ($getcountry) {
                    $country_id = $getcountry->id;
                    $country_name = $getcountry->name;
                 }
                 
               }

               if (isset($userinfo->getcity->name)) {
                 $cityname = $userinfo->getcity->name;
                 $cityid = $userinfo->city_id;
               }

               if (isset($userinfo->getschool->school_name)) {
                 $schoolname = $userinfo->getschool->school_name;
                 $schoolid = $userinfo->school_id;
               }

               if (isset($userinfo->pin)) {
                 $pin = $userinfo->pin;
               }
               $schoolname1 = $userinfo->school_name;

               if (auth()->user()->user_type == 2) {
                  $classname = '';
                  $exper = $userinfo->total_experience .' Year';
                  $experinnum = $userinfo->total_experience;
                  if ($userinfo->class_subjects_ids) {
                     $ab = json_decode($userinfo->class_subjects_ids);
                     $clast = array();
                     foreach ($ab as $key2) {
                       $subjectstreams =  ClassRelationship::select('id','name')->whereNOTIN('status',[2])->where('id',$key2->subjectstreamsid)->first();
                       if ($subjectstreams) {
                          $classname .= $subjectstreams->name;
                          $sub = explode('|', $key2->subjectsids);
                          foreach ($sub as $sinj) {
                             array_push($selectidssubjects, $sinj);
                          }
                          $getsubjectname = ClassSubjectRelationship::whereNOTIN('status',[2])->whereIN('id',$sub)->with('subject')->get();
                          $subgrop = array();
                          if ($getsubjectname) {
                             foreach ($getsubjectname as $sub) {
                                $subgrop[] = $sub->subject->name;
                             }
                          }
                          if (!empty($subgrop)) {
                             $classname .= '('.implode(",", $subgrop).')';
                          }
                       }
                     }
                  }
               }
            }
        }
        $user_profile[0]->boardname = $boardname;
        $user_profile[0]->boardid = $boardid;
        $user_profile[0]->classname = $classname;
        $user_profile[0]->classid = $classid;
        $user_profile[0]->streamname = $streamname;
        $user_profile[0]->streamid = $streamid;
        $user_profile[0]->state_title = $state_title;
        $user_profile[0]->stateid = $stateid;
        $user_profile[0]->cityname = $cityname;
        $user_profile[0]->cityid = $cityid;
        $user_profile[0]->schoolname = $schoolname;
        $user_profile[0]->schoolid = $schoolid;
        $user_profile[0]->pin = $pin;
        $user_profile[0]->country_id = $country_id;
        $user_profile[0]->country_name = $country_name;
        $user_profile[0]->schoolname1 = $schoolname1;
        $response = ['status' => true, 'user_profile' => $user_profile ,'path'=>asset('project/public/user/')];   
        return response($response, 200);
    }

    public function update_user_profile(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'flag' => 'required',
            // 'username' => 'required|unique:users,username,'.auth()->user()->id,
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        if ($request->flag == 1) {
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = $fileName.'_profile_' . time() . '.' . $extension;
                $filePath = request('image')->move(public_path('user'), $image_name);
                
            }
            else
            {
                $image_name = 'default.png';
            }
        }
        else {
            $image_name = auth()->user()->image;
        }

        $checkanyuserinfo = UserInfo::where('user_id','=',auth()->user()->id)
                                    ->first();
        if ($checkanyuserinfo) {
                $school_name = $checkanyuserinfo->school_name;
                if (isset($request->school_name)) {
                    $school_name = $request->school_name;
                }

                $pin = $checkanyuserinfo->pin;
                if (isset($request->pin)) {
                    $pin = $request->pin;
                }

                $state_id = $checkanyuserinfo->state_id;
                if (isset($request->state_id)) {
                    $state_id = $request->state_id;
                }

                $city_id = $checkanyuserinfo->city_id;
                if (isset($request->city_id)) {
                    $city_id = $request->city_id;
                }

                $school_id = $checkanyuserinfo->school_id;
                if (isset($request->school_id)) {
                    $school_id = $request->school_id;
                }
                $a = UserInfo::find($checkanyuserinfo->id);
                $a->state_id = $state_id;
                $a->city_id = $city_id;
                $a->school_id = $school_id;
                $a->school_name = $school_name;
                $a->pin = $pin;
                $a->board_id = $request->board_id ?? 0;
                $a->class_id = $request->class_id ?? 0;
                $a->save();
        }
        else {
            $school_name = '';
            if (isset($request->school_name)) {
                $school_name = $request->school_name;
            }

            $pin = '';
            if (isset($request->pin)) {
                $pin = $request->pin;
            }

            $state_id = 0;
            if (isset($request->state_id)) {
                $state_id = $request->state_id;
            }

            $city_id = 0;
            if (isset($request->city_id)) {
                $city_id = $request->city_id;
            }

            $school_id = 0;
            if (isset($request->school_id)) {
                $school_id = $request->school_id;
            }
            $a = new UserInfo();
            $a->user_id = Auth()->user()->id;
            $a->state_id = $state_id;
            $a->city_id = $city_id;
            $a->school_id = $school_id;
            $a->school_name = $school_name;
            $a->pin = $pin;
            $a->board_id = $request->board_id ?? 0;
            $a->class_id = $request->class_id ?? 0;
            $a->save();
            $update = array("step_one"=>1,
                        );
           User::whereId(auth()->user()->id)->update($update);
        }

        $update = array("name"=>$request->name,
                        // "username"=>$request->username,
                        "image"=>$image_name);
        User::whereId(auth()->user()->id)->update($update);
        $user_profile = User::where('id', auth()->user()->id)->get();
        $response = ['status' => true, 'user_profile' => $user_profile ,'path'=>asset('user/')];   
        return response($response, 200);
    }

    public function home(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            // 'device_id' => 'required',
            // 'device_token' => 'required',
            // 'device_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $logoutflag = 0;
        // $checkuserauth = User::where('id',auth()->user()->id)->first();
        // if ($checkuserauth->user_login == 2) {
        //     $logoutflag = 1;
        // }
        // else {
        //     if ($checkuserauth->device_token != $request->device_token) {
        //         $logoutflag = 1;
        //     }
        // }
        if (isset($request->device_id) && isset($request->device_token) && isset($request->device_type)) {
            $update = array("device_id"=>$request->device_id,
                        "device_token"=>$request->device_token,
                        "device_type"=>$request->device_type);
            User::whereId(auth()->user()->id)->update($update);
            $checkhave = LoginActivity::where("user_id",auth()->user()->id)->whereDate('created_at',date('Y-m-d'))->first();
            if ($checkhave) {
                $updatela = array("user_agent"=>request()->header('User-Agent'),
                        "ip_address"=>request()->ip()
                        );
                LoginActivity::whereId($checkhave->id)->update($updatela);
            }
            else {
                $la = new LoginActivity();
                $la->user_id  = auth()->user()->id;
                $la->user_agent  = request()->header('User-Agent');
                $la->ip_address = request()->ip();
                $la->save();
            }
        }
        
        $total_unread_notification = 0;
        $path = asset('assets').'/checklist/';
        $settings = Generalsetting::select(DB::raw("CONCAT('$path',checklist) AS checklist"),DB::raw("CONCAT('$path',catloge) AS catloge"),'helpline_number', 'support_email','fb_link','linkedin_link','twitter_link','youtube_link','telegramlink','contactuslink','privacylink','termlink','insta_link','terms_content','privacy_policy','share_text','share_text_question_genrater','share_text_app','limit_book_catlog','share_voucher_app_new')
            ->first();
        $featuredbooks = Book::where('books.status',1)
            ->whereIN('booktype',[1,3])
            ->withCount(['reviews as reviews_avg' => function($query) {
                $query->select(DB::raw('ROUND(avg(rating))'));
            }])
            ->leftJoin('bookings', function($join) {
                $join->on('books.id', '=', 'bookings.book_id');
                $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                $join->where('bookings.status', '=', '1');
            })
            ->with('previewsBook')
            ->select('books.*', 'bookings.id as is_purchase_by_user')
            ->orderBy('updated_at','DESC')
            ->take(10)
            ->get();

        $headings = BookManagementCate::whereNOTIN('status',[2])->where('status',1)->where('in_home',1)->orderBy('position','ASC')->limit($settings->limit_book_catlog)->get();
        if ($headings->count() > 0) {
            foreach ($headings as $h) {
                if (isset($request->board_id) || isset($request->class_id) || isset($request->subject_id) || isset($request->book_type_id) || isset($request->author_id) ) {
            
                    $latestbooks = Book::where('books.status',1)
                    ->whereIN('booktype',[$h->id,3])
                    ->where( function($query) use($request){
                             return $request->board_id ?
                                    $query->where('board_id',$request->board_id) : '';
                    })->where(function($query) use($request){
                         return $request->class_id ?
                                $query->where('class_id',$request->class_id) : '';
                    })->where(function($query) use($request){
                         return $request->subject_id ?
                                $query->where('subject_id',$request->subject_id) : '';
                    })
                    ->where(function($query) use($request){
                         return $request->book_type_id ?
                                $query->where('book_type_id',$request->book_type_id) : '';
                    })
                    ->where(function($query) use($request){
                         return $request->author_id ?
                                $query->where('author_id',$request->author_id) : '';
                    })
                    ->withCount(['reviews as reviews_avg' => function($query) {
                        $query->select(DB::raw('ROUND(avg(rating))'));
                    }])
                    ->leftJoin('bookings', function($join) {
                        $join->on('books.id', '=', 'bookings.book_id');
                        $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                        $join->where('bookings.status', '=', '1');
                    })
                    ->with('previewsBook')
                    ->select('books.*', 'bookings.id as is_purchase_by_user')
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
                }
                else
                {
                    $latestbooks = Book::where('books.status',1)
                    ->whereIN('booktype',[$h->id,3])
                    ->withCount(['reviews as reviews_avg' => function($query) {
                        $query->select(DB::raw('ROUND(avg(rating))'));
                    }])
                    ->leftJoin('bookings', function($join) {
                        $join->on('books.id', '=', 'bookings.book_id');
                        $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                        $join->where('bookings.status', '=', '1');
                    })
                    ->with('previewsBook')
                    ->select('books.*', 'bookings.id as is_purchase_by_user')
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
                }
                $h->booklist = $latestbooks;
            }
        }
        $interactive_classes = array();
        $test_series = array();
        $unread = UserNotification::where('user_id', auth()->user()->id)->where('read', '0')->get();
        $total_unread_notification = $unread->count();
        $promo = AdvertismentPromo::where('status',1)->first();
        $is_promo = 0;
        $is_promo_read = 0;
        $path_promo = '';
        if ($promo) {
            $is_promo = $promo->id;
            $path_promo = asset('project/public/promos/').'/'.$promo->image;
            $checkpromoread = AdvertismentPromoRead::where('advertis_id',$promo->id)->where('user_id',auth()->user()->id)->where('status',1)->first();
            if ($checkpromoread) {
                $is_promo_read = 1;
            }

        }
        $quiz_array = array();
       /* $userinfo = UserInfo::where('user_id', auth()->user()->id)->first();
        if ($userinfo) {
            $post = CommunityPost::where('status',1)->where('class_id',$userinfo->class_id)->where('type','quiz')
                ->orderBy('updated_at','DESC')
                ->take(7)
                ->get();
            if ($post->count() > 0) {
                foreach ($post as $keyp) {
                    $keyp->subject = '';
                    $keyp->readtime = Carbon\Carbon::parse($keyp->created_at)->diffForHumans();
                    $keyp->username = 'Admin';
                    $keyp->userimage = asset('project/public/user/defaul.png');
                    if ($keyp->created_by == 'user') {
                        $keyp->username = $keyp->user->name;
                        $keyp->userimage = asset('project/public/user/').'/'.$keyp->user->image;
                    }
                    if ($keyp->type == 'quiz') {
                        $keyp->previewlink = route('quizpreview',$keyp->id);
                    }
                    else {
                        $keyp->previewlink = asset('project/public/community_post').'/'.$keyp->file_upload_or_image;
                    }
                    $keyp->is_self = 0;
                    if ($keyp->user_id == auth()->user()->id) {
                        $keyp->is_self = 1;
                    }
                    $keyp->coverimage = asset('project/public/community_post').'/'.$keyp->file_upload_or_image;
                    $keyp->is_like = '';
                    $checkp =  CommunityPostLikeDislike::where('user_id',auth()->user()->id)->where('community_post_id',$keyp->id)->first();
                    if ($checkp) {
                        $keyp->is_like = $checkp->status;
                    }
                    $is_reportp = 0;
                    $checkreportp = CommunityPostReport::where('community_post_id',$keyp->id)->where('user_id',auth()->user()->id)->first();
                    if ($checkreportp) {
                        $is_reportp = 1;
                    }
                    if ($is_reportp == 0) {
                        $quiz_array[] = $keyp;
                    }

                }
            }
        }*/
        $profileflag = 0;
        $checkanyuserinfo = UserInfo::where('user_id','=',auth()->user()->id)
                                    ->first();
        if ($checkanyuserinfo) {
            if ($checkanyuserinfo->board_id > 0 && $checkanyuserinfo->class_id > 0) {
                $profileflag = 1;
            }
        }
        $response = ['status' => true, 'settings' => $settings,"headings"=>$headings ,'interactive_classes'=>$interactive_classes,'test_series'=>$test_series,'featuredbooks'=>$featuredbooks,'path'=>asset('project/public/book/'),"total_unread_notification"=>$total_unread_notification,"is_promo"=>$is_promo,"is_promo_read"=>$is_promo_read,"path_promo"=>$path_promo,"quiz_array"=>$quiz_array,"logoutflag"=>$logoutflag,'profileflag'=>$profileflag];   
        return response($response, 200);
    }

    public function fetauredbooklist(Request $request)
    {

        if (isset($request->board_id) || isset($request->class_id) || isset($request->subject_id) || isset($request->book_type_id) || isset($request->author_id) ) {
            
            $featuredbooks = Book::where('status',1)->where('is_book_course',1)
            ->where( function($query) use($request){
                     return $request->board_id ?
                            $query->where('board_id',$request->board_id) : '';
            })->where(function($query) use($request){
                 return $request->class_id ?
                        $query->where('class_id',$request->class_id) : '';
            })->where(function($query) use($request){
                 return $request->subject_id ?
                        $query->where('subject_id',$request->subject_id) : '';
            })
            ->where(function($query) use($request){
                 return $request->book_type_id ?
                        $query->where('book_type_id',$request->book_type_id) : '';
            })
            ->where(function($query) use($request){
                 return $request->author_id ?
                        $query->where('author_id',$request->author_id) : '';
            })
            ->withCount(['reviews as reviews_avg' => function($query) {
                $query->select(DB::raw('ROUND(avg(rating))'));
            }])
            ->with('previewsBook')
            ->orderBy('updated_at','DESC')
            ->take(10)
            ->get();


            $latestbooks = Book::where('status',1)->where('is_book_course',1)
            ->where( function($query) use($request){
                     return $request->board_id ?
                            $query->where('board_id',$request->board_id) : '';
            })->where(function($query) use($request){
                 return $request->class_id ?
                        $query->where('class_id',$request->class_id) : '';
            })->where(function($query) use($request){
                 return $request->subject_id ?
                        $query->where('subject_id',$request->subject_id) : '';
            })
            ->where(function($query) use($request){
                 return $request->book_type_id ?
                        $query->where('book_type_id',$request->book_type_id) : '';
            })
            ->where(function($query) use($request){
                 return $request->author_id ?
                        $query->where('author_id',$request->author_id) : '';
            })
            ->withCount(['reviews as reviews_avg' => function($query) {
                $query->select(DB::raw('ROUND(avg(rating))'));
            }])
            ->with('previewsBook')
            ->inRandomOrder()
            ->take(10)
            ->get();
        }
        else
        {
            $featuredbooks = Book::where('books.status',1)->where('is_book_course',1)
            ->whereIN('booktype',[1,3])
            ->withCount(['reviews as reviews_avg' => function($query) {
                $query->select(DB::raw('ROUND(avg(rating))'));
            }])
            ->leftJoin('bookings', function($join) {
                $join->on('books.id', '=', 'bookings.book_id');
                $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                $join->where('bookings.status', '=', '1');
            })
            ->with('previewsBook')
            ->select('books.*', 'bookings.id as is_purchase_by_user')
            ->orderBy('updated_at','DESC')
            ->get();

            
        }

        if($featuredbooks) {
            $response = ['status' => true, 'books' => $featuredbooks,'path'=>asset('project/public/book/')];
        } else {
            $response = ['status' => false, 'msg' => 'No Book found!.'];
        }
        return response($response, 200);
    }

    public function explore_books(Request $request)
    {
        $mainheadings = array();
        $headings = BookManagementCate::whereNOTIN('status',[2])->where('status',1)->where('book_or_course',1)->orderBy('position','ASC')->get();
        if ($headings->count() > 0) {
            foreach ($headings as $h) {
                if (isset($request->board_id) || isset($request->class_id) || isset($request->subject_id) || isset($request->book_type_id) || isset($request->author_id) ) {
            
                    $latestbooks = Book::where('books.status',1)
                    ->whereIN('booktype',[$h->id,3])
                    ->where( function($query) use($request){
                             return $request->board_id ?
                                    $query->where('board_id',$request->board_id) : '';
                    })->where(function($query) use($request){
                         return $request->class_id ?
                                $query->where('class_id',$request->class_id) : '';
                    })->where(function($query) use($request){
                         return $request->subject_id ?
                                $query->where('subject_id',$request->subject_id) : '';
                    })
                    ->where(function($query) use($request){
                         return $request->book_type_id ?
                                $query->where('book_type_id',$request->book_type_id) : '';
                    })
                    ->where(function($query) use($request){
                         return $request->author_id ?
                                $query->where('author_id',$request->author_id) : '';
                    })
                    ->withCount(['reviews as reviews_avg' => function($query) {
                        $query->select(DB::raw('ROUND(avg(rating))'));
                    }])
                    ->leftJoin('bookings', function($join) {
                        $join->on('books.id', '=', 'bookings.book_id');
                        $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                        $join->where('bookings.status', '=', '1');
                    })
                    ->with('previewsBook')
                    ->select('books.*', 'bookings.id as is_purchase_by_user')
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
                }
                else
                {
                    $latestbooks = Book::where('books.status',1)
                    ->whereIN('booktype',[$h->id,3])
                    ->withCount(['reviews as reviews_avg' => function($query) {
                        $query->select(DB::raw('ROUND(avg(rating))'));
                    }])
                    ->leftJoin('bookings', function($join) {
                        $join->on('books.id', '=', 'bookings.book_id');
                        $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                        $join->where('bookings.status', '=', '1');
                    })
                    ->with('previewsBook')
                    ->select('books.*', 'bookings.id as is_purchase_by_user')
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
                }
                $h->booklist = $latestbooks;
                if ($latestbooks->count() > 0) {
                    $mainheadings[] = $h;
                }
            }
        }
        // if (isset($request->board_id) || isset($request->class_id) || isset($request->subject_id) || isset($request->book_type_id) || isset($request->author_id) ) {
            
        //     $featuredbooks = Book::where('books.status',1)->where('is_book_course',1)
        //     ->whereIN('booktype',[1,3])
        //     ->where( function($query) use($request){
        //              return $request->board_id ?
        //                     $query->where('board_id',$request->board_id) : '';
        //     })->where(function($query) use($request){
        //          return $request->class_id ?
        //                 $query->where('class_id',$request->class_id) : '';
        //     })->where(function($query) use($request){
        //          return $request->subject_id ?
        //                 $query->where('subject_id',$request->subject_id) : '';
        //     })
        //     ->where(function($query) use($request){
        //          return $request->book_type_id ?
        //                 $query->where('book_type_id',$request->book_type_id) : '';
        //     })
        //     ->where(function($query) use($request){
        //          return $request->author_id ?
        //                 $query->where('author_id',$request->author_id) : '';
        //     })
        //     ->withCount(['reviews as reviews_avg' => function($query) {
        //         $query->select(DB::raw('ROUND(avg(rating))'));
        //     }])
        //     ->leftJoin('bookings', function($join) {
        //         $join->on('books.id', '=', 'bookings.book_id');
        //         $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
        //         $join->where('bookings.status', '=', '1');
        //     })
        //     ->with('previewsBook')
        //     ->select('books.*', 'bookings.id as is_purchase_by_user')
        //     ->orderBy('updated_at','DESC')
        //     ->take(10)
        //     ->get();


        //     $latestbooks = Book::where('books.status',1)->where('is_book_course',1)
        //     ->whereIN('booktype',[2,3])
        //     ->where( function($query) use($request){
        //              return $request->board_id ?
        //                     $query->where('board_id',$request->board_id) : '';
        //     })->where(function($query) use($request){
        //          return $request->class_id ?
        //                 $query->where('class_id',$request->class_id) : '';
        //     })->where(function($query) use($request){
        //          return $request->subject_id ?
        //                 $query->where('subject_id',$request->subject_id) : '';
        //     })
        //     ->where(function($query) use($request){
        //          return $request->book_type_id ?
        //                 $query->where('book_type_id',$request->book_type_id) : '';
        //     })
        //     ->where(function($query) use($request){
        //          return $request->author_id ?
        //                 $query->where('author_id',$request->author_id) : '';
        //     })
        //     ->withCount(['reviews as reviews_avg' => function($query) {
        //         $query->select(DB::raw('ROUND(avg(rating))'));
        //     }])
        //     ->leftJoin('bookings', function($join) {
        //         $join->on('books.id', '=', 'bookings.book_id');
        //         $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
        //         $join->where('bookings.status', '=', '1');
        //     })
        //     ->with('previewsBook')
        //     ->select('books.*', 'bookings.id as is_purchase_by_user')
        //     ->inRandomOrder()
        //     ->take(10)
        //     ->get();
        // }
        // else
        // {
        //     $featuredbooks = Book::where('books.status',1)->where('is_book_course',1)
        //     ->whereIN('booktype',[1,3])
        //     ->withCount(['reviews as reviews_avg' => function($query) {
        //         $query->select(DB::raw('ROUND(avg(rating))'));
        //     }])
        //     ->leftJoin('bookings', function($join) {
        //         $join->on('books.id', '=', 'bookings.book_id');
        //         $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
        //         $join->where('bookings.status', '=', '1');
        //     })
        //     ->with('previewsBook')
        //     ->select('books.*', 'bookings.id as is_purchase_by_user')
        //     ->orderBy('updated_at','DESC')
        //     ->take(10)
        //     ->get();

        //     $latestbooks = Book::where('books.status',1)->where('is_book_course',1)
        //     ->whereIN('booktype',[2,3])
        //     ->withCount(['reviews as reviews_avg' => function($query) {
        //         $query->select(DB::raw('ROUND(avg(rating))'));
        //     }])
        //     ->leftJoin('bookings', function($join) {
        //         $join->on('books.id', '=', 'bookings.book_id');
        //         $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
        //         $join->where('bookings.status', '=', '1');
        //     })
        //     ->with('previewsBook')
        //     ->select('books.*', 'bookings.id as is_purchase_by_user')
        //     ->inRandomOrder()
        //     ->take(10)
        //     ->get();
        // }

        if($mainheadings) {
            $response = ['status' => true, 'books' => array()/*$featuredbooks*/,"headings"=>$mainheadings,'latestbooks'=>array()/*$latestbooks*/,'path'=>asset('project/public/book/')];
        } else {
            $response = ['status' => false, 'msg' => 'No Book found!.'];
        }
        return response($response, 200);
    }

    public function exploreBookscat(Request $request)
    {
        $headings = BookManagementCate::whereNOTIN('status',[2])->where('book_or_course',1)->orderBy('position','ASC')->get();
        if ($headings->count() > 0) {
            foreach ($headings as $h) {
                if (isset($request->board_id) || isset($request->class_id) || isset($request->subject_id) || isset($request->book_type_id) || isset($request->author_id) ) {
            
                    $latestbooks = Book::where('books.status',1)
                    ->whereIN('booktype',[$h->id,3])
                    ->where( function($query) use($request){
                             return $request->board_id ?
                                    $query->where('board_id',$request->board_id) : '';
                    })->where(function($query) use($request){
                         return $request->class_id ?
                                $query->where('class_id',$request->class_id) : '';
                    })->where(function($query) use($request){
                         return $request->subject_id ?
                                $query->where('subject_id',$request->subject_id) : '';
                    })
                    ->where(function($query) use($request){
                         return $request->book_type_id ?
                                $query->where('book_type_id',$request->book_type_id) : '';
                    })
                    ->where(function($query) use($request){
                         return $request->author_id ?
                                $query->where('author_id',$request->author_id) : '';
                    })
                    ->withCount(['reviews as reviews_avg' => function($query) {
                        $query->select(DB::raw('ROUND(avg(rating))'));
                    }])
                    ->leftJoin('bookings', function($join) {
                        $join->on('books.id', '=', 'bookings.book_id');
                        $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                        $join->where('bookings.status', '=', '1');
                    })
                    ->with('previewsBook')
                    ->select('books.*', 'bookings.id as is_purchase_by_user')
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
                }
                else
                {
                    $latestbooks = Book::where('books.status',1)
                    ->whereIN('booktype',[$h->id,3])
                    ->withCount(['reviews as reviews_avg' => function($query) {
                        $query->select(DB::raw('ROUND(avg(rating))'));
                    }])
                    ->leftJoin('bookings', function($join) {
                        $join->on('books.id', '=', 'bookings.book_id');
                        $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                        $join->where('bookings.status', '=', '1');
                    })
                    ->with('previewsBook')
                    ->select('books.*', 'bookings.id as is_purchase_by_user')
                    ->inRandomOrder()
                    ->take(10)
                    ->get();
                }
                $h->booklist = $latestbooks;
            }
        }
        if($headings->count() > 0) {
            $response = ['status' => true, 'list' => $headings,'path'=>asset('project/public/book/')];
        } else {
            $response = ['status' => false, 'msg' => 'No Book found!.'];
        }
        return response($response, 200);
    }

    public function activatebook(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'voucher_code' => 'required',
            'book_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $response = ['status' => false, 'msg' => 'Something error found!.'];
        if (auth()->user()->id > 1) {
            $voucher_code = BookVoucher::where([
                            ['status','=',1],
                            ['book_id','=',$input['book_id']],
                            ['voucher','=',$input['voucher_code']]])->first();
            if ($voucher_code) {
                $howmuch = Bookings::where([['user_id','=',auth()->user()->id],['voucher_code_id','=',$voucher_code->id]])->get();
                $totaluse = $howmuch->count();
                $alreadyhavevoucher = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id],['voucher_code_id','=',$voucher_code->id]])->first();
                if($alreadyhavevoucher) {
                    $response = ['status' => false, 'msg' => 'Voucher already scanned and added.','data'=>$alreadyhavevoucher];
                }
                else {
                    if ($voucher_code->expiry_in_days > 0) {
                        if ($voucher_code->voucher_for == auth()->user()->user_type || $voucher_code->voucher_for == 0) {
                            if ($voucher_code->is_repeat == 1) {
                                $expiry_date = date('Y-m-d', strtotime(' + '.$voucher_code->expiry_in_days.' days'));
                                $a = new Bookings();
                                $a->user_id  = auth()->user()->id;
                                $a->book_id  = $input['book_id'];
                                $a->voucher_code_id  = $voucher_code->id;
                                $a->expiry_date = $expiry_date;
                                $a->status = 1;
                                $a->booking_type = 'bookscan';
                                $a->voucher_code = $input['voucher_code'];
                                $a->save();
                                $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                            }
                            else {
                                if ($totaluse == 0) {
                                    $expiry_date = date('Y-m-d', strtotime(' + '.$voucher_code->expiry_in_days.' days'));
                                    $a = new Bookings();
                                    $a->user_id  = auth()->user()->id;
                                    $a->book_id  = $input['book_id'];
                                    $a->voucher_code_id  = $voucher_code->id;
                                    $a->expiry_date = $expiry_date;
                                    $a->status = 1;
                                    $a->booking_type = 'bookscan';
                                    $a->voucher_code = $input['voucher_code'];
                                    $a->save();
                                    $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                                }
                            }
                            
                        }
                    }
                }
            }
            else {
                $voucher_code = BookVoucher::where([
                            ['status','=',1],
                            ['voucher','=',$input['voucher_code']]])->first();
                if ($voucher_code) {
                    $alreadyhavevoucher = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id],['voucher_code_id','=',$voucher_code->id]])->first();
                    if($alreadyhavevoucher) {
                        $response = ['status' => false, 'msg' => 'Voucher already scanned and added.','data'=>$alreadyhavevoucher];
                    }
                    else {
                        if ($voucher_code->expiry_in_days > 0) {
                            if ($voucher_code->voucher_for == auth()->user()->user_type || $voucher_code->voucher_for == 0) {
                                if ($voucher_code->is_repeat == 1) {
                                    $expiry_date = date('Y-m-d', strtotime(' + '.$voucher_code->expiry_in_days.' days'));
                                    $a = new Bookings();
                                    $a->user_id  = auth()->user()->id;
                                    $a->book_id  = $input['book_id'];
                                    $a->voucher_code_id  = $voucher_code->id;
                                    $a->expiry_date = $expiry_date;
                                    $a->status = 1;
                                    $a->booking_type = 'bookscan';
                                    $a->voucher_code = $input['voucher_code'];
                                    $a->save();
                                    $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                                }
                                else {
                                    if ($totaluse == 0) {
                                        $expiry_date = date('Y-m-d', strtotime(' + '.$voucher_code->expiry_in_days.' days'));
                                        $a = new Bookings();
                                        $a->user_id  = auth()->user()->id;
                                        $a->book_id  = $input['book_id'];
                                        $a->voucher_code_id  = $voucher_code->id;
                                        $a->expiry_date = $expiry_date;
                                        $a->status = 1;
                                        $a->booking_type = 'bookscan';
                                        $a->voucher_code = $input['voucher_code'];
                                        $a->save();
                                        $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return response($response, 200);
    }

    public function activebooks(Request $request)
    {
        $activebooks = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id]])
        ->select(DB::raw('*,GREATEST(DATEDIFF(`expiry_date`, NOW()),0) AS remaindays, GREATEST(DATEDIFF(`expiry_date`,date(`created_at`)),0) AS total_days'))
        ->with('book')
        ->withCount(['reviews as reviews_avg' => function($query) {
            $query->select(DB::raw('ROUND(avg(rating))'));
        }])
        ->with('previews')
        ->orderBy('updated_at','DESC')
        // ->take(10)
        ->get();
        $bookslist = array();
        foreach ($activebooks as $key) {
            $key->getclass = DB::table('class_genres')->where('id',$key->book->class_id)->first();
            $key->getsubject = DB::table('subjects')->where('id',$key->book->subject_id)->first();
            if ($key->book->status == 1) {
                $bookslist[] = $key;
            }
            
        }

        if($activebooks) {
            $response = ['status' => true, 'books' => $bookslist,'path'=>asset('project/public/book/')];
        } else {
            $response = ['status' => false, 'msg' => 'No Book found!.'];
        }
        return response($response, 200);
    }

    public function activatevoucher(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'voucher_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $response = ['status' => false, 'msg' => 'Something error found!.'];
        if (auth()->user()->id > 1) {
            $voucher_code = BookVoucher::where([
                            ['status','=',1],
                            ['voucher','=',$input['voucher_code']]])->first();
            if ($voucher_code) {
                $howmuch = Bookings::where([['user_id','=',auth()->user()->id],['voucher_code_id','=',$voucher_code->id]])->get();
                $totaluse = $howmuch->count();
                $alreadyhavevoucher = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id],['voucher_code_id','=',$voucher_code->id]])->first();
                if($alreadyhavevoucher) {
                    $response = ['status' => true, 'msg' => 'Voucher already scanned and added.','data'=>$alreadyhavevoucher];
                }
                else {
                    if (!is_null($voucher_code->book_id)) {
                            if ($voucher_code->expiry_in_days > 0) {
                            if ($voucher_code->voucher_for == auth()->user()->user_type || $voucher_code->voucher_for == 0) {
                                if ($voucher_code->is_repeat == 1) {
                                    $expiry_date = date('Y-m-d', strtotime(' + '.$voucher_code->expiry_in_days.' days'));
                                    $a = new Bookings();
                                    $a->user_id  = auth()->user()->id;
                                    $a->book_id  = $voucher_code->book_id;
                                    $a->voucher_code_id  = $voucher_code->id;
                                    $a->expiry_date = $expiry_date;
                                    $a->status = 1;
                                    $a->booking_type = 'bookscan';
                                    $a->voucher_code = $input['voucher_code'];
                                    $a->save();
                                    $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                                }
                                else {
                                    if ($totaluse == 0) {
                                        $expiry_date = date('Y-m-d', strtotime(' + '.$voucher_code->expiry_in_days.' days'));
                                        $a = new Bookings();
                                        $a->user_id  = auth()->user()->id;
                                        $a->book_id  = $voucher_code->book_id;
                                        $a->voucher_code_id  = $voucher_code->id;
                                        $a->expiry_date = $expiry_date;
                                        $a->status = 1;
                                        $a->booking_type = 'bookscan';
                                        $a->voucher_code = $input['voucher_code'];
                                        $a->save();
                                        $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                                    }
                                }
                            }
                            else {
                                $response = ['status' => false, 'msg' => 'Voucher not have any book related'];
                            }
                        }
                    }
                    else {
                        $response = ['status' => false, 'msg' => 'Voucher not have any book related'];
                    }
                    
                }
            }
            else {
                $response = ['status' => false, 'msg' => 'No Voucher found!.'];
            }
        }
        return response($response, 200);
    }

    public function aciTivity(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'activity_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $response = ['status' => false, 'message' => 'Not Allowed this activity type!'];
        if ($input['activity_type'] == 'book') {
            $validator = Validator::make($input, [
            // 'booking_id' => 'required',
            // 'book_id' => 'required',
            'study_material_id' => 'required',
        ]);

            if ($validator->fails()) {
                return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
            }
            if (isset($request->booking_id) && isset($request->book_id)) {
            
            $check =  UserActivity::where([
                                    ['booking_id','=',$input['booking_id']],
                                    ['book_id','=',$input['book_id']],
                                    ['activity_type','=','book'],
                                    ['study_material_id','=',$input['study_material_id']],
                                    ['user_id','=',Auth()->user()->id]]
                                    )
                                    ->first();

                if ($check) {
                    $a = UserActivity::find($check->id);
                    $a->status = 1;
                    $a->last_use_date_time = date('Y-m-d H:i:s');
                    $a->completed_at = date('Y-m-d H:i:s');
                    $a->update();
                    $response = ['status' => true, 'message' => 'Activity updated!'];
                }
                else {
                    $a = new UserActivity();
                    $a->user_id = Auth()->user()->id;
                    $a->booking_id = $request->booking_id;
                    $a->book_id = $request->book_id;
                    $a->study_material_id = $request->study_material_id;
                    $a->filetype = $request->filetype;
                    $a->activity_type = 'book';
                    $a->status = 0;
                    $a->start_date_time = date('Y-m-d H:i:s');
                    $a->save();
                    $response = ['status' => true, 'message' => 'Activity added!'];  
                }
            }
        }
        return response($response, 200);
    } 


    public function update_user_profile2(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'board_id' => 'required',
            'class_id' => 'required',
            // 'stream_id' => 'required',
            // 'state_id' => 'required',
            // 'city_id' => 'required',
            // 'school_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        
        $school_name = '';
        if (isset($request->school_name)) {
            $school_name = $request->school_name;
        }

        $pin = '';
        if (isset($request->pin)) {
            $pin = $request->pin;
        }

        $state_id = 0;
        if (isset($request->state_id)) {
            $state_id = $request->state_id;
        }

        $city_id = 0;
        if (isset($request->city_id)) {
            $city_id = $request->city_id;
        }

        $school_id = 0;
        if (isset($request->school_id)) {
            $school_id = $request->school_id;
        }

        $checkanyuserinfo = UserInfo::where('user_id','=',auth()->user()->id)
                                    ->first();

        if ($checkanyuserinfo) {
                $a = UserInfo::find($checkanyuserinfo->id);
                $a->board_id = $request->board_id;
                $a->class_id = $request->class_id;
                // $a->stream_id = $request->stream_id;
                $a->state_id = $state_id;
                $a->city_id = $city_id;
                $a->school_id = $school_id;
                $a->school_name = $school_name;
                $a->pin = $pin;
                $a->save();
        }
        else {
            $a = new UserInfo();
            $a->user_id = Auth()->user()->id;
            $a->board_id = $request->board_id;
            $a->class_id = $request->class_id;
            // $a->stream_id = $request->stream_id;
            $a->state_id = $state_id;
            $a->city_id = $city_id;
            $a->school_id = $school_id;
            $a->school_name = $school_name;
            $a->pin = $pin;
            $a->save();
            $update = array("step_one"=>1,
                        );
           User::whereId(auth()->user()->id)->update($update);
        }

        $user_profile = User::where('id', auth()->user()->id)->get();
        $response = ['status' => true, 'user_profile' => $user_profile ,'path'=>asset('user/')];   
        return response($response, 200);
    }

    public function vdoOTPgenrate(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'vdo_video_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $response1 = ['status' => false, 'message' => 'something happen plz try again!']; 
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://dev.vdocipher.com/api/videos/".$input['vdo_video_id']."/otp",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode([
            "ttl" => 300,
          ]),
          CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Authorization: Apisecret 8yJVqtyECX0DcaT08gTxT5qehiuhEVBlz4iZYKzgmlFIZa78roHVk2EYdIOXPIFL",
            "Content-Type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          $response1 = ['status' => false, 'message' => 'something happen plz try again!'];  

        } else {
          $a = json_decode($response);  
          if (isset($a->otp)) {
              $response1 = ['status' => true, 'vdoresponse' => $a];  
          }
        }
        return response($response1, 200);
    }

    public function qrcodecontent(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $input = $request->all();
        $validator = Validator::make($input, [
            'ids' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }

        $qrcode = QrCodeLinks::where([
                        ['status','=',1],
                        ['id','=',$input['ids']]])->first();
        if ($qrcode) {
            $maincontentarrayheadingnew = array();
            $datacontent = array();
            if ($qrcode->code_type == 'studymatrials') {
                $d = explode('_', $qrcode->linkids); 
                if (count($d) > 0) {
                    foreach ($d as $key) {
                        $book = StudyMaterial::where('file_id',$key)->where('status',1)->get();
                        $bookactiveflag = array();   
                        if ($book->count() > 0) {
                            foreach ($book as $b) {
                                $bookD = Book::where('id',$b->book_id)->where('status',1)->first();
                                if ($bookD) {
                                    if ($bookD->qr_code_content_status == 1) {
                                        $checkbookactive = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id],['book_id','=',$b->book_id]])->first();
                                        if ($checkbookactive) {
                                            array_push($bookactiveflag, 1);
                                            break;
                                        }
                                        else {
                                            array_push($bookactiveflag, 0);
                                        }
                                    }
                                    else {
                                        array_push($bookactiveflag, 1);
                                        break;
                                    }
                                }
                            }
                        }
                        else {
                            array_push($bookactiveflag, 1);
                        } 
                        if (in_array(1, $bookactiveflag)) {
                            $a = FileLibrary::where('status',1)->where('id',$key)->first();
                            if ($a) {
                                $maincontentarrayheadingnew[$a->contentTypefl->position]= $a->contentTypefl->name;
                                $is_bookmark = 0;
                                
                                if ($a->type == 'Flash Cards') 
                                {
                                    $gfile1 = GlobalFile::where('status','1')
                                            ->whereIn('id',[$a->front_data_id])->first();
                                    $gfile2 = GlobalFile::where('status','1')
                                    ->whereIn('id',[$a->back_data_id])->first();
                                    if ($gfile1 && $gfile2) {
                                        $flash1  = array( 
                                                        "icon"=>asset('project/public/icons/').'/'.$a->contentTypefl->image,
                                                        "study_material_id"=>0,
                                                        "file_id"=>$gfile1->id,
                                                        "filetype"=>$gfile1->type,
                                                        "content_type"=>$gfile1->content_type,
                                                        "name"=>$gfile1->name,
                                                        "tags"=>$gfile1->tags,
                                                        "video_type"=>$gfile1->video_type,
                                                        "content"=>$gfile1->content,
                                                        "status"=>$gfile1->status,
                                                        "is_bookmark"=>$is_bookmark,
                                                        "caption"=>$a->caption,
                                                        "language_id"=>$a->language_id,
                                                        "type"=>$a->type);

                                        $flash2 = array(
                                                        "icon"=>asset('project/public/icons/').'/'.$a->contentTypefl->image,
                                                        "study_material_id"=>0,
                                                        "file_id"=>$gfile2->id,
                                                        "filetype"=>$gfile2->type,
                                                        "content_type"=>$gfile2->content_type,
                                                        "name"=>$gfile2->name,
                                                        "tags"=>$gfile2->tags,
                                                        "video_type"=>$gfile2->video_type,
                                                        "content"=>$gfile2->content,
                                                        "status"=>$gfile2->status,
                                                        "is_bookmark"=>$is_bookmark,
                                                        "caption"=>$a->caption,
                                                        "language_id"=>$a->language_id,
                                                        "type"=>$a->type);
                                        $datacontent[] = array($flash1,$flash2);
                                    }
                                }
                                else
                                {
                                    $gfile = GlobalFile::where([
                                        ['status','=','1'],
                                        ['id','=',$a->file_id],
                                    ])
                                    ->first();
                                    if ($gfile) {
                                        $datacontent[] = array( 
                                                                "icon"=>asset('project/public/icons/').'/'.$a->contentTypefl->image,
                                                                "study_material_id"=>0,
                                                                "file_id"=>$gfile->id,
                                                                "filetype"=>$gfile->type,
                                                                "content_type"=>$gfile->content_type,
                                                                "name"=>$gfile->name,
                                                                "tags"=>$gfile->tags,
                                                                "video_type"=>$gfile->video_type,
                                                                "content"=>$gfile->content,
                                                                "status"=>$gfile->status,
                                                                "is_bookmark"=>$is_bookmark,
                                                                "caption"=>$a->caption,
                                                                "language_id"=>$a->language_id,
                                                                "type"=>$a->type); 
                                    }
                                }
                            }
                        }
                    }
                }
                
                if (!empty($maincontentarrayheadingnew) && !empty($datacontent)) {
                   ksort($maincontentarrayheadingnew);
                   $maincontentarrayheading = array_values($maincontentarrayheadingnew); 
                   $response = ['status' => true, 'headings' => $maincontentarrayheading,'contents'=>$datacontent,'path'=>asset('project/public/global_files/'),"book_id"=>$qrcode->linkids,"type"=>$qrcode->code_type];
                }
                else {
                    $response = ['status' => false, 'msg' => 'Please activate the book for access the content thanks!'];
                }
            }
            elseif ($qrcode->code_type == 'book') {
                $alreadyactivate = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id],['book_id','=',$qrcode->linkids]])->first();
                if($alreadyactivate) {
                    $response = ['status' => true, 'msg' => 'Book Already Activated.', 'headings' => array(),'contents'=>$alreadyactivate,"book_id"=>$qrcode->linkids,"type"=>$qrcode->code_type,'path'=>asset('project/public/global_files/')];
                }
                else {
                    if (auth()->user()->id > 1) {
                        $expiry_date = date('Y-m-d', strtotime(' + 365 days'));
                        $a = new Bookings();
                        $a->user_id  = auth()->user()->id;
                        $a->book_id  = $qrcode->linkids;
                        $a->voucher_code_id  = $qrcode->id;
                        $a->expiry_date = $expiry_date;
                        $a->status = 1;
                        $a->booking_type = 'bookscanqrcode';
                        $a->voucher_code = 'qrcodescan';
                        $a->save();
                        $response = ['status' => true,"msg"=>"Book activated successfully", 'headings' => array(),'contents'=>$datacontent,'path'=>asset('project/public/global_files/'),"book_id"=>$qrcode->linkids,"type"=>$qrcode->code_type];
                    }
                    // $response = ['status' => true, 'msg' => 'Book added','booking_data'=>$a];
                }
                
            }

            
        }
        
        return response($response, 200);
        
    }

    public function sendotpforverify(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $input = $request->all();
        $validator = Validator::make($input, [
            'emailormobile' => 'required',
            'otp' => 'required',
            'for' => 'required',
            'country_code'=>'required'
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        if ($input['for'] == 'mobile') {
            $otp = $input['otp']; //mt_rand(1000, 9999);
            $user_message = "One Time Password " . $otp . " to verify your Mobile on Goyal's Online Support";
            $phone = $input['country_code'] . $input['emailormobile'];
            // $msg = $user_message;
            // $temp_id = '1707161761166396747';
            // $entity_id = '1701159793007694875';
            // $otpmsg = $this->otpmsg_sd($phone, $msg, $temp_id, $entity_id);
            $this->twofactorsms($otp,$phone);
            $response = ['status' => true, 'msg' => 'OTP Send successfully.'];
        }
        else {
            // $subject = "Pegasus Verification Code";
            // $data = [
            //     'to' => $request->emailormobile,
            //     'subject' => "Pegasus Verification Code",
            //     'name' => auth()->user()->name,
            //     'generalsettings'=>Generalsetting::find(1),
            //     'otp'=>$input['otp']
            // ];
            // $view  = view('emails.otp',compact('data'))->render();
            // $mail = $this->check_curl($request->emailormobile,auth()->user()->name,$subject,$view,'otp');
            // $response = ['status' => true, 'msg' => 'OTP Send successfully.'];

            $data = [
            'to' => $request->emailormobile,
            'subject' => "Pegasus Verification Code",
            'name' => auth()->user()->name,
            'generalsettings'=>Generalsetting::find(1),
            'otp'=>$input['otp']
            ];
            $view  = view('emails.otp',compact('data'))->render();
            $subject = "Pegasus Verification Code";
            $mail = $this->check_curl_for_sendinblue($request->emailormobile,auth()->user()->name,$subject,$view,'otp');
            $response = ['status' => true, 'msg' => 'OTP Send successfully.'];

        }
        
        
        return response($response, 200);
        
    }

    public function updateemailmobile(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $input = $request->all();
        $validator = Validator::make($input, [
            'emailormobile' => 'required',
            'for' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        if ($input['for'] == 'email') {
            $update = array("email"=>$request->emailormobile,
                        );
            $update = User::whereId(auth()->user()->id)->update($update);
            $response = ['status' => true, 'msg' => 'Update successfully.',"update"=>$update];
        }
        else {
            $update = array("mobile"=>$request->emailormobile,
                        );
            $update = User::whereId(auth()->user()->id)->update($update);
            $response = ['status' => true, 'msg' => 'Update successfully.',"update"=>$update];
        }
        
        
        return response($response, 200);
        
    }

    public function changepwd(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $input = $request->all();
        $validator = Validator::make($input, [
            'oldpwd' => 'required',
            'newpwd' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $user = User::find(Auth()->user()->id);
        $oldpwd = \Hash::make($request->oldpwd);
        if (Hash::check($request->oldpwd, auth()->user()->password)) { 
            $user->password = \Hash::make($request->newpwd);
            $user->save();
            return response()->json(array('status'=>true,'msg' => 'Password change successfully!')); 
        }
        else {
            return response()->json(array('status'=>false,'msg' => 'Old password not match!')); 
        }
    }

    public function submitEnquiry(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $input = $request->all();
        $validator = Validator::make($input, [
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            'subject'=>'required',
            'message'=>'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }
        $a = new Enquiry();
        $a->user_id = isset(auth()->user()->id) ? auth()->user()->id : 0;
        $a->first_name= $request->name;
        $a->last_name= '';
        $a->email= $request->email;
        $a->mobile= $request->mobile;
        $a->subject= $request->subject;
        $a->message = $request->message;
        $a->save();
        $settings = Generalsetting::find(1);
        $subject = "Pegasus New Enquiry!";
        $data = [
            'to' => $settings->enquiry_email,
            'subject' => "Pegasus New Enquiry!",
            'name' => '',
            'a'=>$a,
        ];
        $view  = view('emails.enquiry',compact('data'))->render();
        $mail = $this->check_curl($settings->enquiry_email,'Enquiry',$subject,$view,'enquiry');
        return response()->json(array('status'=>true,'msg' => 'Enquiry submitted successfully!')); 
    }

    public function clearAllnotification(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $update = array("status"=>2,
                        );
        UserNotification::where("user_id",auth()->user()->id)->update($update);
        return response()->json(array('status'=>true,'msg' => 'clear successfully!')); 
    }

    public function readPromos(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $input = $request->all();
        $validator = Validator::make($input, [
            'promo_id'=>'required',
        ]);

        if ($validator->fails()) {
            return response(['msg'=>$validator->errors()->all()[0], 'status' => false], 422);
        }

        $a = new AdvertismentPromoRead();
        $a->user_id = auth()->user()->id;
        $a->advertis_id= $request->promo_id;
        $a->status= 1;
        $a->save();
        return response()->json(array('status'=>true,'msg' => 'submitted successfully!')); 
    }

    public function classbasedsubjectlist(Request $request)
    {
        $response = ['status' => false, 'msg' => 'Notfound!.'];
        $userinfo = UserInfo::where('user_id', auth()->user()->id)->first();
        if ($userinfo) {
            if (auth()->user()->user_type == 1) {
                $subject = ClassSubject::select('subject_id')->where('class_id',$userinfo->class_id)->where('status',1)->whereNOTIN('status',[2])->orderBy('position','ASC')->get();
                if(!empty($subject)) {
                    foreach ($subject as $key) {
                        $key->subject_name = $key->subjects->name;
                    }
                    $response = ['status' => true, 'msg' => 'subject List', 'data' => $subject];   
                    
                } else {
                    $response = ['status' => false, 'msg' => 'Sorry ! No subject found.'];  
                }
            }
            elseif (auth()->user()->user_type == 2) { 
                $subjectid = array();
                $subject1 = array();
                $ab = json_decode($userinfo->class_subjects_ids);
                if (!empty($ab)) {
                    foreach ($ab as $key2) {
                        $getclass = ClassRelationship::where('status',1)->where('id',$key2->subjectstreamsid)->first();
                        if ($getclass) {
                            $ab3 = explode(',', $getclass->class_one_id);
                            if (count($ab3)) {
                                foreach ($ab3 as $key3) {
                                    $subject = ClassSubject::select('subject_id')->where('class_id',$key3)->where('status',1)->whereNOTIN('status',[2])->orderBy('position','ASC')->get();
                                    foreach ($subject as $key4) {
                                        $key4->subject_name = $key4->subjects->name;
                                        if (!in_array($key4->subject_id, $subjectid)) {
                                            array_push($subjectid, $key4->subject_id);
                                            $subject1[] = $key4;

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $response = ['status' => true, 'msg' => 'subject List', 'data' => $subject1];   
                
                
            }
        }
        return response($response, 200);
    }
    

    public function activebooksPaginate(Request $request)
    {
        $limit=100;
        if ($request->offset == 0) {
            $offset=0;
        }
        else {
            $offset=$request->offset;
        }
        $activebooks = Bookings::where([['status','=',1],['user_id','=',auth()->user()->id]])
        ->select(DB::raw('*,GREATEST(DATEDIFF(`expiry_date`, NOW()),0) AS remaindays, GREATEST(DATEDIFF(`expiry_date`,date(`created_at`)),0) AS total_days'))
        ->with('book')
        ->withCount(['reviews as reviews_avg' => function($query) {
            $query->select(DB::raw('ROUND(avg(rating))'));
        }])
        ->with('previews')
        ->orderBy('updated_at','DESC')
        // ->take(10)
        ->offset($offset)->limit($limit)->get();
        // ->paginate(10);
         $bookslist = array();
        foreach ($activebooks as $key) {
           
            
            $key->getclass = DB::table('class_genres')->where('id',$key->book->class_id)->first();
            $key->getsubject = DB::table('subjects')->where('id',$key->book->subject_id)->first();
            if ($key->book->status == 1) {
                $bookslist[] = $key;
            }
        }

        if($activebooks) {
            $response = ['status' => true, 'books' => $bookslist,'path'=>asset('project/public/book/'),'offset'=>$offset];
        } else {
            $response = ['status' => false, 'msg' => 'No Book found!.'];
        }
        return response($response, 200);
    }

    public function explore_booksPaginate(Request $request)
    {
        $limit=10;
        if ($request->offset == 0) {
            $offset=0;
        }
        else {
            $offset=$request->offset;
        }
        if (isset($request->board_id) || isset($request->class_id) || isset($request->subject_id) || isset($request->book_type_id) || isset($request->author_id) ) {
            $latestbooks = Book::where('books.status',1)->where('books.is_visible_to_all',0)
            ->whereIN('booktype',[$request->id,3])
            ->where( function($query) use($request){
                     return $request->board_id ?
                            $query->where('board_id',$request->board_id) : '';
            })->where(function($query) use($request){
                 return $request->class_id ?
                        $query->where('class_id',$request->class_id) : '';
            })->where(function($query) use($request){
                 return $request->subject_id ?
                        $query->where('subject_id',$request->subject_id) : '';
            })
            ->where(function($query) use($request){
                 return $request->book_type_id ?
                        $query->where('book_type_id',$request->book_type_id) : '';
            })
            ->where(function($query) use($request){
                 return $request->author_id ?
                        $query->where('author_id',$request->author_id) : '';
            })
            ->withCount(['reviews as reviews_avg' => function($query) {
                $query->select(DB::raw('ROUND(avg(rating))'));
            }])
            ->leftJoin('bookings', function($join) {
                $join->on('books.id', '=', 'bookings.book_id');
                $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                $join->where('bookings.status', '=', '1');
            })
            ->with('previewsBook')
            ->select('books.*', 'bookings.id as is_purchase_by_user')
            ->orderBy('books.updated_at','DESC')
            // ->inRandomOrder()
            ->offset($offset)->limit($limit)->get();
        }
        else
        {
            $latestbooks = Book::where('books.status',1)->where('books.is_visible_to_all',0)
            ->whereIN('booktype',[$request->id,3])
            ->withCount(['reviews as reviews_avg' => function($query) {
                $query->select(DB::raw('ROUND(avg(rating))'));
            }])
            ->leftJoin('bookings', function($join) {
                $join->on('books.id', '=', 'bookings.book_id');
                $join->on('bookings.user_id', '=', DB::raw(Auth()->user()->id));
                $join->where('bookings.status', '=', '1');
            })
            ->with('previewsBook')
            ->select('books.*', 'bookings.id as is_purchase_by_user')
            ->orderBy('books.updated_at','DESC')
            // ->inRandomOrder()
            ->offset($offset)->limit($limit)->get();
        }
        if($latestbooks) {
            $response = ['status' => true, 'books' => $latestbooks,"offset"=>$offset,'path'=>asset('project/public/book/')];
        } else {
            $response = ['status' => false, 'msg' => 'No Book found!.'];
        }
        return response($response, 200);
    }

    public function activebooksearch(Request $request)
    {
        $limit=10;
        if ($request->offset == 0) {
            $offset=0;
        }
        else {
            $offset=$request->offset;
        }
        $input = $request->all();
        $validator = Validator::make($input, [
            'searchstring' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['msg' => $validator->errors()->all()[0], 'status' => false], 422);
        }
        $searchstring = $request->searchstring;
        $maindata = array();
        //$result = DB::select("select IF(bookings.user_id='" . Auth()->user()->id . "' && bookings.status=1, '1', '0') AS is_purchase_by_user,`books`.*, `authors`.`name` as `author_name`, `boards`.`name` as `board_name`, `subjects`.`name` as `subject_name` from `books` inner join `boards` on `boards`.`id` = `books`.`board_id` inner join `subjects` on `subjects`.`id` = `books`.`subject_id` inner join `authors` on `authors`.`id` = `books`.`author_id` left join bookings on bookings.book_id=books.id where `books`.`status` = 1 and `bookings`.`status` = '1' and `bookings`.`user_id` = '" . Auth()->user()->id . "' and `books`.`id` <> 0 and (`books`.`name` like '%" . $searchstring . "%' or `boards`.`name` like '%" . $searchstring . "%' or `subjects`.`name` like '%" . $searchstring . "%' or `authors`.`name` like '%" . $searchstring . "%' or `books`.`result` like '%" . $searchstring . "%') GROUP by books.id LIMIT 10 OFFSET '".$offset."'");
        // echo "select IF(bookings.user_id='" . Auth()->user()->id . "' && bookings.status=1, '1', '0') AS is_purchase_by_user,`books`.*, `authors`.`name` as `author_name`, `boards`.`name` as `board_name`, `subjects`.`name` as `subject_name` from `books` inner join `boards` on `boards`.`id` = `books`.`board_id` inner join `subjects` on `subjects`.`id` = `books`.`subject_id` inner join `authors` on `authors`.`id` = `books`.`author_id` left join bookings on bookings.book_id=books.id where `books`.`status` = 1 and `bookings`.`status` = '1' and `bookings`.`user_id` = '" . Auth()->user()->id . "' and `books`.`id` <> 0 and (`books`.`name` REGEXP '" . $searchstring . "' or `boards`.`name` REGEXP '" . $searchstring . "' or `subjects`.`name` REGEXP  '" . $searchstring . "' or `authors`.`name` REGEXP  '" . $searchstring . "' or `books`.`tags` REGEXP '" . $searchstring . "') GROUP by books.id LIMIT 10 OFFSET ".$offset;
        $result = DB::select("select IF(bookings.user_id='" . Auth()->user()->id . "' && bookings.status=1, '1', '0') AS is_purchase_by_user,`bookings`.*, `authors`.`name` as `author_name`, `boards`.`name` as `board_name`, `subjects`.`name` as `subject_name` from `books` inner join `boards` on `boards`.`id` = `books`.`board_id` inner join `subjects` on `subjects`.`id` = `books`.`subject_id` inner join `authors` on `authors`.`id` = `books`.`author_id` left join bookings on bookings.book_id=books.id where `books`.`status` = 1 and `bookings`.`status` = '1' and `bookings`.`user_id` = '" . Auth()->user()->id . "' and `books`.`id` <> 0 and (`books`.`name` REGEXP '" . $searchstring . "' or `boards`.`name` REGEXP '" . $searchstring . "' or `subjects`.`name` REGEXP  '" . $searchstring . "' or `authors`.`name` REGEXP  '" . $searchstring . "' or `books`.`tags` REGEXP '" . $searchstring . "') GROUP by books.id LIMIT 10 OFFSET ".$offset);
        if ($result) {

            foreach ($result as $key) {
                $key->is_purchase_by_user = null;
                $ispurchase = Bookings::where('book_id', $key->id)->where('status', 1)->where('user_id', auth()->user()->id)->first();
                if ($ispurchase) {
                    $key->is_purchase_by_user = 1;
                }
                $key->book = Book::where('id', $key->book_id)->first();
                $key->getclass = DB::table('class_genres')->where('id',$key->book->class_id)->first();
                $key->getsubject = DB::table('subjects')->where('id',$key->book->subject_id)->first();
            }
            
            $response = ['status' => true, 'books' => $result, 'path' => asset('project/public/book/'),/*'suggestionid'=>$a->id*/];
        } else $response = ['status' => false, 'books' => $result];


        return response($response, 200);
    }
}
