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
use App\Models\MasterTemplate;
use Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;
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
}
