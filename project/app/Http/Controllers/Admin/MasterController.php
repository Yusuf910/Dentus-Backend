<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppLanguage;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;
use App\Models\Generalsetting;
use App\Models\TreatmentDoctor;
use Illuminate\Validation\Rule;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\PackFeature;
use App\Models\UserEstablishmentClinic;
use App\Models\UserEstablishmentHoliday;
use App\Models\UserEstablishmentGallery;
use App\Models\Blog;
use App\Models\State;
use App\Models\City;
use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\UserPremiumAddon;
use App\Models\DoctorClinicSlot;
use App\Models\UserInformation;
class MasterController extends Controller
{
    public function treatment(Request $request)
    {
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'treatment_name',
                'average_duration',
                'doctors',
                'treatment_fees',
                'instructions',
                'Added On',
                
            );
            $i = 1;
            $queryqb = Treatment::query();
            $queryqb->orderBy('updated_at', 'DESC');
            $queryqb->where('doctor_id',auth()->user()->id);
            $queryqb->whereNOTIN('status', [2]);
            $arrays = $queryqb->get();
            
            foreach ($arrays as $a) {
                $treamentdoctor = TreatmentDoctor::where('treatment_id',$a->id)->with('doctor')->get();
                $docid = array();
                    if($treamentdoctor->count() > 0 ) {
                        foreach($treamentdoctor as $t){
                            array_push($docid, $t->doctor->name);
                        }
                    }
                $dataexport[] = [
                    $a->treatment_name,
                    $a->average_duration,
                    implode(',', $docid),
                    $a->treatment_fees,
                    $a->instructions,
                    date('d-M-Y h:ia', strtotime($a->created_at))

                ];
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Treament" . date('Y-m-d-h:i:s') . '.csv');
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            foreach ($dataexport as $dataexport) {
                fputcsv($handle, $dataexport);
            }
            fclose($handle);
            exit;
            // return Excel::download(new SalesUserProductExport($request,$full=0), 'SalesUserProduct'.date('Y-m-d-h:i:s').'.xlsx');
        }
        $data = Treatment::whereNOTIN('status', [2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add Treatment';
        $importbutton = 'Upload Excel';
        $title = 'treatments';
        $listurl = route('admin.master.treatment');
        $addurl = route('admin.master.create_treatment');
        $importurl = 'admin.master.update_treatment';
        $editurl = 'admin.master.edit_treatment';
        $destroyurl = route('admin.master.destroy_treatment');
        $addurl1 = route('admin.master.store_treatment');
        return view('doctor.treatment.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_treatment()
    {
        $title = 'Add treatment';
        $listname = 'treatments';
        $listurl = route('admin.master.treatment');
        $addurl = route('admin.master.store_treatment');
        return view('admin.treatment.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_treatment(Request $request)
    {
        $totalquantity = $this->getquantity(19);
        $treatment_list = Treatment::where('doctor_id',auth()->user()->id)->where('status',1)->get();
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($treatment_list);
        }
        elseif ($totalquantity < 0) {
           $canaddchild = 1;
        }
        if ($canaddchild > 0) {
                $a = new Treatment();
                $a->doctor_id = auth()->user()->id;
                $a->treatment_name = $request->treatment_name;
                $a->average_duration = $request->average_duration;
                $a->call_before_confirmation = $request->call_before_confirmation;
                $a->instructions = $request->instructions;
                $a->treatment_fees = $request->treatment_fees;
                $a->tax_id = $request->tax_id;
                $a->save();
                foreach ($request->doctors as $doctorId) {
                    TreatmentDoctor::create([
                        'treatment_id' => $a->id,
                        'doctor_id' => $doctorId,
                    ]);
                }
                return redirect()->back()
                ->with('success', 'Treatment created successfully'); 
        } 
        else {
            return redirect()->back()
                ->with('failure', 'You cannot add more!');    
        }
        
    }


    public function edit_treatment($id)
    {
        $title = 'Edit treatment';
        $listname = 'treatments';
        $listurl = route('admin.master.treatment');
        $editurl = 'admin.master.update_treatment';
        $a = treatment::find($id);
        if ($a) {
            return view('admin.treatment.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.treatment')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_treatment(Request $request, $id)
    {
        
        $a = treatment::find($id);
        $a->treatment_name = $request->treatment_name;
        $a->average_duration = $request->average_duration;
        $a->call_before_confirmation = $request->call_before_confirmation;
        $a->instructions = $request->instructions;
        $a->treatment_fees = $request->treatment_fees;
        $a->tax_id = $request->tax_id;
        $a->save();
        TreatmentDoctor::where('treatment_id', $id)->delete();
        foreach ($request->doctors as $doctorId) {
            TreatmentDoctor::create([
                'treatment_id' => $a->id,
                'doctor_id' => $doctorId,
            ]);
        }
        return redirect()->route('admin.master.treatment')
            ->with('success', 'treatment updated successfully');
    }

    public function destroy_treatment(Request $request)
    {
        $input = $request->all();
        // $bookcheck = treatment::where('treatment_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.treatment')
        //                 ->with('failure','You can not delete treatment due to linking with book');
        // }
        $a = treatment::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.treatment')
            ->with('success', 'Treatment deleted successfully');
    }

    public function packtreatment()
    {

        $data = PackTreatment::where('user_id', auth()->user()->id)->where('user_type',2)->where('status',1)
        ->with('featurelist')
        ->withCount(['subscriptions as purchase_count' => function ($query) {
            // $query->where('status', 1); // Count only active purchases
        }])
        ->get();
        $addbutton = 'Add Patients’ Plans';
        $importbutton = 'Upload Excel';
        $title = 'Patients’ Plans';
        $listurl = route('admin.master.packtreatment');
        $addurl = route('admin.master.create_packtreatment');
        $importurl = 'admin.master.update_packtreatment';
        $editurl = 'admin.master.edit_packtreatment';
        $destroyurl = route('admin.master.destroy_packtreatment');
        $addurl1 = route('admin.master.store_packtreatment');
        return view('doctor.packtreatment.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }
    public function store_packtreatment(Request $request)
    {
        $totalquantity = $this->getquantity(16);
        $treatments = PackTreatment::where('user_id', auth()->user()->id)->where('user_type',2)->whereIN('status',[0,1])
        ->with('featurelist')
        ->withCount(['subscriptions as purchase_count' => function ($query) {
            // $query->where('status', 1); // Count only active purchases
        }])
        ->orderBy('status','DESC')
        ->get();
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($treatments);
        }
        elseif ($totalquantity < 0) {
           $canaddchild = 1;
        }
        if ($canaddchild > 0) {
            $a = new PackTreatment();
            $a->user_id = auth()->user()->id;
            $a->user_type = 2;
            $a->name = $request->name;
            $a->description = $request->description;
            $a->price = $request->price;
            $a->discount_price = $request->discount_price ?? 0;
            $a->validity = $request->validity;
            $a->type = $request->type;
            $a->tax_id = $request->tax_id;
            $a->status = 1;
            $a->save();
            if ($a) {
                foreach ($request->treatment_id as $id => $treatment_id) {
                    if (!isset($request->quantity[$id]) || $request->quantity[$id] <= 0) {
                        continue;
                    }
                    $ab = new PackFeature();
                    $ab->pack_id = $a->id;
                    $ab->treatment_id = $treatment_id;
                    $ab->name = $request->treatment_name[$id];
                    $ab->quantity = $request->quantity[$id];
                    $ab->status = 1;
                    $ab->save();
                }
                $response = [
                        'status' => true,
                        'message' => 'Subscription purchased successfully!',
                        'subscription' => $a,
                    ];
            }
            return redirect()->back()
            ->with('success', 'packtreatment created successfully');
        } else {
            return redirect()->back()
            ->with('failure', 'You cannot add more!');
        }
        
    }
    // public function update_packtreatment(Request $request, $id)
    // {
    //     dd($request->all());
    //     $a = PackTreatment::find($id);
    //     $a->user_type = 2;
    //     $a->name = $request->name;
    //     $a->description = $request->description;
    //     $a->price = $request->price;
    //     $a->discount_price = $request->discount_price ?? 0;
    //     $a->validity = $request->validity;
    //     $a->type = $request->type;
    //     $a->tax_id = $request->tax_id;
    //     $a->status = 1;
    //     $a->save();
    //     PackFeature::where('pack_id', $a->id)->update(['status' => 2]);
    //     foreach ($request->treatment_id as $id => $treatment_id) {
    //         if (!isset($request->quantity[$id]) || $request->quantity[$id] <= 0) {
    //             continue;
    //         }
    //         $existingFeature = PackFeature::where('pack_id', $a->id)
    //             ->where('treatment_id', $treatment_id)
    //             ->first();

    //         if ($existingFeature) {
    //             $existingFeature->name = $request->treatment_name[$id];
    //             $existingFeature->quantity = $request->quantity[$id];
    //             $existingFeature->status = 1;
    //             $existingFeature->save();
    //         }
    //         else {
    //             $ab = new PackFeature();
    //             $ab->pack_id = $a->id;
    //             $ab->treatment_id = $treatment_id;
    //             $ab->name = $request->treatment_name[$id];
    //             $ab->quantity = $request->quantity[$id];
    //             $ab->status = 1;
    //             $ab->save();
    //         }
    //     }
    //     return redirect()->route('admin.master.packtreatment')
    //         ->with('success', 'pack treatment updated successfully');
    // }
    public function update_packtreatment(Request $request, $id)
{
    $a = PackTreatment::findOrFail($id);

    $a->user_type = 2;
    $a->name = $request->name;
    $a->description = $request->description;
    $a->price = $request->price;
    $a->discount_price = $request->discount_price ?? 0;
    $a->validity = $request->validity;
    $a->type = $request->type;
    $a->tax_id = $request->tax_id;
    $a->status = 1;
    $a->save();

    // Existing features inactive
    PackFeature::where('pack_id', $a->id)
        ->update(['status' => 2]);

    // Treatment data exists then process
    if (!empty($request->treatment_id) && is_array($request->treatment_id)) {

        foreach ($request->treatment_id as $key => $treatment_id) {

            if (
                !isset($request->quantity[$key]) ||
                empty($request->quantity[$key]) ||
                $request->quantity[$key] <= 0
            ) {
                continue;
            }

            $existingFeature = PackFeature::where('pack_id', $a->id)
                ->where('treatment_id', $treatment_id)
                ->first();

            if ($existingFeature) {

                $existingFeature->name = $request->treatment_name[$key] ?? '';
                $existingFeature->quantity = $request->quantity[$key];
                $existingFeature->status = 1;
                $existingFeature->save();

            } else {

                $feature = new PackFeature();
                $feature->pack_id = $a->id;
                $feature->treatment_id = $treatment_id;
                $feature->name = $request->treatment_name[$key] ?? '';
                $feature->quantity = $request->quantity[$key];
                $feature->status = 1;
                $feature->save();
            }
        }
    }

    return redirect()
        ->route('admin.master.packtreatment')
        ->with('success', 'Pack Treatment updated successfully');
}

    public function destroy_packtreatment(Request $request)
    {
        $input = $request->all();
        
        $a = PackTreatment::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }

    public function clinic()
    {
        $data = UserEstablishmentClinic::where('user_id', auth()->user()->id)->whereNOTIN('status',[2])->get();
        $addbutton = 'Add clinic';
        $importbutton = 'Upload Excel';
        $title = 'Clinics';
        $listurl = route('admin.master.clinic');
        $addurl = route('admin.master.create_clinic');
        $importurl = 'admin.master.update_clinic';
        $editurl = 'admin.master.edit_clinic';
        $destroyurl = route('admin.master.destroy_clinic');
        $addurl1 = route('admin.master.store_clinic');
        return view('doctor.clinic.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_clinic()
    {
        $title = 'Clinic';
        $listname = 'clinics';
        $listurl = route('admin.master.clinic');
        $addurl = route('admin.master.store_clinic');
        $State = State::select('id','name as state_title')->where('country_id',101)->whereNOTIN('status',[2])->where('status',1)->orderBy('name','ASC')->get();
        return view('doctor.clinic.create', compact('title', 'listurl', 'listname', 'addurl','State'));
    }

    public function store_clinic(Request $request)
    {
        if (auth()->user()->parent_id == 0) {
            $id = auth()->user()->id;
            
        } else $id = auth()->user()->parent_id;
        $clinics = UserEstablishmentClinic::where('user_id', $id)->where('status',1)->get();
        $totalquantity = $this->getquantity(13);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-count($clinics);
        }
        if ($canaddchild > 0) {
            $image_name = 'default';
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_clinic_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/doctor/clinic', $image_name);
                
            }
            $state = '';
            if ($request->state) {
                $State1 = State::where('id',$request->state)->first();
                $state = $State1->name ?? '';
            }
            $city = '';
            if ($request->city) {
                $city1 = City::where('id',$request->city)->first();
                $city = $city1->name ?? '';
            }
            $a = new UserEstablishmentClinic();
            $a->user_id = auth()->user()->id;
            $a->clinic_name = $request->clinic_name;
            $a->clinic_description = $request->clinic_description;
            $a->latitude = $request->latitude;
            $a->longitude = $request->longitude;
            $a->address = $request->address;
            $a->state = $state;
            $a->city = $city;
            $a->state_id = $request->state;
            $a->city_id = $request->city;
            $a->pincode = $request->pincode;
            $a->logo = $image_name;
            $a->register_number = $request->register_number;
            $a->status = $request->status;
            $a->save();
            return redirect()->route('admin.master.clinic')
                ->with('success', 'clinic created successfully');
        } else {
            return redirect()->back()
                ->with('failure', 'You cannot add more');
        }
    }


    public function edit_clinic($id)
    {
        $title = 'Edit clinic';
        $listname = 'clinics';
        $listurl = route('admin.master.clinic');
        $editurl = 'admin.master.update_clinic';
        $a = UserEstablishmentClinic::find($id);
        if ($a) {
            $State1 = State::where('name',$a->state)->first();
            $stateid = $State1->id ?? '';
            $State = State::select('id','name as state_title')->where('country_id',101)->whereNOTIN('status',[2])->where('status',1)->orderBy('name','ASC')->get();
            $city = City::whereNOTIN('status',[2])->where('state_id',$stateid)->orderBy('name','ASC')->get();
            return view('doctor.clinic.edit', compact('a', 'title', 'listurl', 'listname', 'editurl','State','city'));
        } else {
            return redirect()->route('admin.master.clinic')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_clinic(Request $request, $id)
    {
        $a = UserEstablishmentClinic::find($id);
        $image_name = $a->logo;
        if (request('image')) 
        {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand().'_clinic_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/doctor/clinic', $image_name);
            
        }
        $state = '';
        if ($request->state) {
            $State1 = State::where('id',$request->state)->first();
            $state = $State1->name ?? '';
        }
        $city = '';
        if ($request->city) {
            $city1 = City::where('id',$request->city)->first();
            $city = $city1->name ?? '';
        }
        $a->state = $state;
        $a->city = $city;
        $a->state_id = $request->state;
        $a->city_id = $request->city;
        $a->clinic_name = $request->clinic_name;
        $a->clinic_description = $request->clinic_description;
        $a->latitude = $request->latitude;
        $a->longitude = $request->longitude;
        $a->address = $request->address;
        $a->pincode = $request->pincode;
        $a->logo = $image_name;
        $a->register_number = $request->register_number;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.clinic')
            ->with('success', 'clinic updated successfully');
    }

    public function destroy_clinic(Request $request)
    {
        $input = $request->all();
        // $bookcheck = clinic::where('clinic_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.clinic')
        //                 ->with('failure','You can not delete clinic due to linking with book');
        // }
        $a = UserEstablishmentClinic::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.clinic')
            ->with('success', 'clinic deleted successfully');
    }

    public function clinicimages($id)
    {
        $title = 'Clinic Images';
        $listname = 'Clinics';
        $listurl = route('admin.master.clinic');
        $addurl = route('admin.master.addimages_clinic',$id);
        $data = UserEstablishmentGallery::where('establishment_clinics_id',$id)->whereNOTIN('status',[2])->get();
        $destroyurl = route('admin.master.destroy_clinicimages');
        return view('doctor.clinic.clinicimages', compact('data', 'title', 'listurl', 'listname', 'addurl','destroyurl','id'));
    }

    public function destroy_clinicimages(Request $request)
    {
        $input = $request->all();
        $a = UserEstablishmentGallery::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'deleted successfully');
    }

    public function addimages_clinic(Request $request,$id)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:20000',
        ]);

        $uploadedImages = [];

        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $filePath = $image->move('content/doctor/gallery', $imageName);

                $gallery = new UserEstablishmentGallery();
                $gallery->establishment_clinics_id = $id;
                $gallery->name = $imageName;
                $gallery->status = 1;
                $gallery->save();
                $uploadedImages[] = $gallery;
            }
        }
        return redirect()->back()
            ->with('success', 'added successfully');
    }

    public function clinictiming($id)
    {
        $title = 'Clinic Timings';
        $listname = 'Clinics';
        $listurl = route('admin.master.clinic');
        $addurl = route('admin.master.addimages_clinic',$id);
        $data = UserEstablishmentClinic::where('id',$id)->first();
        $destroyurl = route('admin.master.destroy_clinictiming');
        return view('doctor.clinic.clinictiming', compact('data', 'title', 'listurl', 'listname', 'addurl','destroyurl','id'));
    }

    public function timingupdateclinictiming($id, Request $request)
    {
        $a = UserEstablishmentClinic::where('id', $id)->first();
        if ($a) {
            $formattedSchedule = [];
            $scheduleData = $request->input('schedule');
            foreach ($scheduleData as $day => $times) {
                $morning_start = isset($times['morning_start']) ? Carbon::parse($times['morning_start'])->format('h:i A') : null;
                $morning_end = isset($times['morning_end']) ? Carbon::parse($times['morning_end'])->format('h:i A') : null;
                $evening_start = isset($times['evening_start']) ? Carbon::parse($times['evening_start'])->format('h:i A') : null;
                $evening_end = isset($times['evening_end']) ? Carbon::parse($times['evening_end'])->format('h:i A') : null;

                $formattedSchedule[$day] = [
                    "morning" => $morning_start && $morning_end ? ["{$morning_start} - {$morning_end}"] : [],
                    "evening" => $evening_start && $evening_end ? ["{$evening_start} - {$evening_end}"] : [],
                ];
            }
            $finalJson = json_encode(["schedule" => $formattedSchedule], JSON_PRETTY_PRINT);
            $a->timings = $finalJson;
            $a->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function clinicholiday($id)
    {
        $title = 'Clinic Holidays';
        $listname = 'Clinics';
        $listurl = route('admin.master.clinic');
        $addurl = route('admin.master.addimages_holiday',$id);
        $data = UserEstablishmentHoliday::where('establishment_clinics_id',$id)->whereNOTIN('status',[2])->orderBy('date','DESC')->get();
        $destroyurl = route('admin.master.destroy_holiday');
        return view('doctor.clinic.clinicholiday', compact('data', 'title', 'listurl', 'listname', 'addurl','destroyurl','id'));
    }

    public function destroy_holiday(Request $request)
    {
        $input = $request->all();
        $a = UserEstablishmentHoliday::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'deleted successfully');
    }

    public function addimages_holiday(Request $request,$id)
    {
        $request->validate([
            'date_of_birth' => 'required',
            'event_name' => 'required',

        ]);

        $gallery = new UserEstablishmentHoliday();
        $gallery->establishment_clinics_id = $id;
        $gallery->date = $request->date_of_birth;
        $gallery->event_name = $request->event_name;
        $gallery->status = 1;
        $gallery->save();
        return redirect()->back()
            ->with('success', 'added successfully');
    }

    public function blog()
    {

        $data = blog::whereNOTIN('status', [2])->where('user_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add Blog';
        $importbutton = 'Upload Excel';
        $title = 'Social Connect';
        $listurl = route('admin.master.blog');
        $addurl = route('admin.master.create_blog');
        $importurl = 'admin.master.update_blog';
        $editurl = 'admin.master.edit_blog';
        $destroyurl = route('admin.master.destroy_blog');
        $addurl1 = route('admin.master.store_blog');
        return view('doctor.blog.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_blog()
    {
        $title = 'Add Social Connect';
        $listname = 'Social Connect';
        $listurl = route('admin.master.blog');
        $addurl = route('admin.master.store_blog');
        return view('admin.blog.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_blog(Request $request)
    {
        $blogs1 = Blog::where('user_id', auth()->user()->id)->where('status',1)->count();
        $totalquantity = $this->getquantity(14);
        $canaddchild = 0;
        if ($totalquantity > 0) {
           $canaddchild = $totalquantity-$blogs1;
        }
        if ($canaddchild > 0) {
            $image_name = 'default';
            if (request('image')) 
            {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand().'_blog_' . time() . '.' . $extension;
                $filePath = request('image')->move('content/blogs', $image_name);
                
            }
            $a = new blog();
            $a->user_id = auth()->user()->id;
            $a->name = $request->name;
            $a->type = $request->type;
            $a->description = $request->description;
            $a->video_link = $request->video_link;
            $a->position = $request->position;
            $a->image =  $image_name;
            $a->status = 1;
            $a->save();
            return redirect()->back()
            ->with('success', 'Social Connect created successfully');
        } else {
            return redirect()->back()
            ->with('failure', 'You cannot add more!');
        }
        
    }


    public function edit_blog($id)
    {
        $title = 'Edit Social Connect';
        $listname = 'Social Connect';
        $listurl = route('admin.master.blog');
        $editurl = 'admin.master.update_blog';
        $a = blog::find($id);
        if ($a) {
            return view('admin.blog.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.blog')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_blog(Request $request, $id)
    {
        
        $a = blog::find($id);
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
        
        return redirect()->route('admin.master.blog')
            ->with('success', 'Social Connect updated successfully');
    }

    public function destroy_blog(Request $request)
    {
        $input = $request->all();
        
        $a = Blog::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'deleted successfully');
    }

    public function load_city2($state_id)
    {
        $a = City::whereNOTIN('status',[2])->where('state_id',$state_id)->orderBy('name','ASC')->get();
        $title = 'City';
        return view('chapter',compact('a','title'));
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

    public function doctimeslots($id)
    {
        $data = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $id)->orderBy('id', 'desc')->where('status',1)->first();
        $addbutton = 'Add Timeslots';
        $importbutton = 'Upload Excel';
        $title = 'Timeslots';
        $listurl = route('admin.master.clinic');
        $addurl = route('admin.master.create_clinic');
        $importurl = 'admin.master.update_clinic';
        $editurl = 'admin.master.edit_clinic';
        $destroyurl = route('admin.master.destroy_clinic');
        $addurl1 = route('admin.master.store_clinic');
        $c = UserEstablishmentClinic::where('id', $id)->first();
        $check = UserInformation::where('user_id',auth()->user()->id)->first();
        if ($data) {
            
        } else {
            $new = new DoctorClinicSlot();
            $new->doctor_id = auth()->user()->id;
            $new->clinic_id = $id;
            $new->doctor_availability = $check->doctor_availability ?? '';
            $new->status = 1;
            $new->save();
            $data = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $id)->orderBy('id', 'desc')->where('status',1)->first();
        }
        return view('doctor.clinic.doctimeslots', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1','id','c','check'));
    }

    public function timingupdateclininc(Request $request,$id)
    {
        $formattedSchedule = [];
        $scheduleData = $request->input('schedule');
        foreach ($scheduleData as $day => $times) {
            $isFullDay = isset($times['_isFullDay']);
            $isHalfDay = isset($times['_isHalfDay']);
            $halfDaySlot = $times['_halfDaySlot'] ?? null;
            $morning_start = isset($times['morning_start']) ? Carbon::parse($times['morning_start'])->format('h:i A') : null;
            $morning_end = isset($times['morning_end']) ? Carbon::parse($times['morning_end'])->format('h:i A') : null;
            $evening_start = isset($times['evening_start']) ? Carbon::parse($times['evening_start'])->format('h:i A') : null;
            $evening_end = isset($times['evening_end']) ? Carbon::parse($times['evening_end'])->format('h:i A') : null;

            $morningSlot = ($morning_start && $morning_end) ? ["{$morning_start} - {$morning_end}"] : [];
            $eveningSlot = ($evening_start && $evening_end) ? ["{$evening_start} - {$evening_end}"] : [];

            if ($isFullDay) {
                $formattedSchedule[$day] = [
                    "morning" => [],
                    "evening" => [],
                ];
            } elseif ($isHalfDay && in_array($halfDaySlot, ['morning', 'evening'])) {
                $formattedSchedule[$day] = [
                    "morning" => $halfDaySlot === 'evening' ? $morningSlot : [],
                    "evening" => $halfDaySlot === 'morning' ? $eveningSlot : [],
                ];
            } else {
                $formattedSchedule[$day] = [
                    "morning" => $morningSlot,
                    "evening" => $eveningSlot,
                ];
            }
        }
        $finalJson = json_encode(["schedule" => $formattedSchedule], JSON_PRETTY_PRINT);
        $existing = DoctorClinicSlot::where('doctor_id', auth()->user()->id)->where('clinic_id', $id)->orderBy('id', 'desc')->where('status',1)->first();

        if ($existing) {
            $existing->doctor_availability = $finalJson;
            $existing->save();
        } else {

            $new = new DoctorClinicSlot();
            $new->doctor_id = auth()->user()->id;
            $new->clinic_id = $request->clinic_id;
            $new->doctor_availability = $finalJson;
            $new->status = 1;
            $new->save();
        }
        return redirect()->back()
            ->with('success', 'added successfully');
    }
    
    
}
