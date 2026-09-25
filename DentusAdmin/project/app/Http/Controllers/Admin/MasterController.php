<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppLanguage;
use App\Models\Faq;
use App\Models\MasterTax;
use App\Models\Video;
use App\Models\MasterUser;
use App\Models\MasterLangauage;
use App\Models\AreaOfExpertise;
use App\Models\User;
use App\Models\Transactions;
use App\Models\Blog;
use App\Models\MasterCollegeInstitute;
use App\Models\MasterDegree;
use App\Models\MasterRegistraionCouncil;
use App\Models\MasterServices;
use App\Models\MasterSpecialsation;
use App\Models\MasterTemplate;
use App\Models\SeasonalOffers;
use App\Models\Drug;
use App\Models\Banner;
use App\Models\UserMember;
use App\Models\UserModels\UserProfile;
use App\Models\UserModels\MedicalRecord;
use App\Models\UserModels\Booking;
use App\Models\AdminNotification;
use App\Models\UserNotification;
use App\Models\Order;
use App\Models\Usertoken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;





use Illuminate\Validation\Rule;


class MasterController extends Controller
{

    //mastertax
    public function mastertax()
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Name",
                "Tax Percentage",
                "Status",
                "Created At"
            );
            $fetch = MasterTax::where('status', '!=', 2)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->status == 1) {
                    $st = "Active";
                } else if ($user->status == 2) {
                    $st = "Deleted";
                } else {
                    $st = "Inactive";
                }
                $data[] = array(
                    "ID" => $i,
                    "name" => $user->name,
                    "tax_percentage" => $user->tax_percentage,
                    "status" => $st,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }

            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"MasterTax" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        $data = MasterTax::whereNOTIN('status', [2])->orderBy('id', 'ASC')->get();
        $addbutton = 'Add mastertax';
        $importbutton = 'Upload Excel';
        $title = 'mastertax List';
        $listurl = route('admin.master.mastertax');
        $addurl = route('admin.master.create_mastertax');
        $importurl = route('admin.master.import_mastertax');
        $editurl = 'admin.master.edit_mastertax';
        $destroyurl = route('admin.master.destroy_mastertax');
        $addurl1 = route('admin.master.store_mastertax');
        return view('admin.mastertax.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl'));
    }

    public function create_mastertax()
    {
        $title = 'Add mastertax';
        $listname = 'mastertax';
        $listurl = route('admin.master.mastertax');
        $addurl = route('admin.master.store_mastertax');
        return view('admin.mastertax.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_mastertax(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',

        ]);
        $a = new MasterTax();
        $a->name = $request->name;
        $a->taxvalue = $request->taxvalue;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.mastertax')
            ->with('success', 'mastertax created successfully');
    }


    public function edit_mastertax($id)
    {
        $title = 'Edit mastertax';
        $listname = 'mastertax';
        $listurl = route('admin.master.mastertax');
        $editurl = 'admin.master.update_mastertax';
        $a = MasterTax::find($id);
        if ($a) {
            return view('admin.mastertax.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.mastertax')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_mastertax(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $a = mastertax::find($id);
        $a->name = $request->name;
        $a->status = $request->status;
        $a->taxvalue = $request->taxvalue;
        $a->save();
        return redirect()->route('admin.master.mastertax')
            ->with('success', 'mastertax updated successfully');
    }

    public function destroy_mastertax(Request $request)
    {
        $input = $request->all();
        $a = mastertax::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.mastertax')
            ->with('success', 'mastertax deleted successfully');
    }


    //masteruser
    public function masteruser()
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Name Details",
                "Image",
                "Position",
                "Status",
                "Created At"
            );
            $fetch = MasterUser::where('status', '!=', 2)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->status == 1) {
                    $st = "Active";
                } else if ($user->status == 2) {
                    $st = "Deleted";
                } else {
                    $st = "Inactive";
                }
                // $im = <img height="100" width="100" src="{{ asset('content/master-user') }}/{{$a->image}}" alt="">
                // $lang = App\Models\AppLanguage::get();
                // $name = json_decode($user->name,true);

                $data[] = array(
                    "ID" => $i,
                    "name" => $user->name,
                    "image" => $user->image,
                    "position" => $user->position,
                    "status" => $st,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }

            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"MasterTax" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        $data = MasterUser::whereNOTIN('status', [2])->orderBy('name', 'ASC')->get();
        $addbutton = 'Add Testimonial User';
        $importbutton = 'Upload Excel';
        $title = 'Testimonial User List';
        $listurl = route('admin.master.masteruser');
        $addurl = route('admin.master.create_masteruser');
        $importurl = route('admin.master.import_masteruser');
        $editurl = 'admin.master.edit_masteruser';
        $destroyurl = route('admin.master.destroy_masteruser');
        $addurl1 = route('admin.master.store_masteruser');
        return view('admin.masteruser.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_masteruser()
    {
        $title = 'Add Testimonial User';
        $listname = 'Testimonial User';
        $listurl = route('admin.master.masteruser');
        $addurl = route('admin.master.store_masteruser');
        return view('admin.masteruser.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_masteruser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);
        // $image = 'default.png';
        // if (request('image'))
        // {
        //     $fileNameWithTheExtension = request('image')->getClientOriginalName();
        //     $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
        //     $extension = request('image')->getClientOriginalExtension();
        //     $image_name = rand().'_' . time() . '.' . $extension;
        //     // $d = request()->image->storeAs('master-user', $image_name,'contentfolder');
        //     $filePath = request('image')->move('content/master-user', $image);
        //     $image = $image_name;
        // }
        $image = 'default.png';
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/master-user', $image);
        }


        $a = new MasterUser();
        $a->name = json_encode($request->name);
        $a->position = $request->position;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'Testimonial user created successfully');
    }


    public function edit_masteruser($id)
    {
        $title = 'Edit Testimonial User';
        $listname = 'Testimonial User';
        $listurl = route('admin.master.masteruser');
        $editurl = 'admin.master.update_masteruser';
        $a = MasterUser::find($id);
        if ($a) {
            return view('admin.masteruser.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.masteruser')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_masteruser(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);

        $a = MasterUser::find($id);
        $image = $a->image;
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/master-user', $image);


            // $fileNameWithTheExtension = request('image')->getClientOriginalName();
            // $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            // $extension = request('image')->getClientOriginalExtension();
            // $image_name = rand().'_' . time() . '.' . $extension;
            // $d = request()->image->storeAs('master-user', $image_name,'contentfolder');
            // $image = $image_name;
        }

        // print_r( $image); die;

        $a->name = json_encode($request->name);
        $a->position = $request->position;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'Testimonial user updated successfully');
    }

    public function destroy_masteruser(Request $request)
    {
        $input = $request->all();
        // $bookcheck = Blog::where('masteruser_id',$input['id'])->whereNOTIN('status',[2])->get();
        // if ($bookcheck->count() > 0) {
        //     return redirect()->route('admin.master.masteruser')
        //                 ->with('failure','You can not delete masteruser due to linking with book');
        // }
        $a = MasterUser::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'Testimonial user deleted successfully');
    }

    public function import_masteruser()
    {
        $title = 'Upload Testimonial user File';
        $listname = 'Testimonial user';
        $listurl = route('admin.master.masteruser');
        $samplebutton = 'Sample File';
        $addurl = route('admin.master.store_import_masteruser');
        $sampleurl = asset('project/public/temp_files/masteruserImportFileSample.csv');
        return view('admin.masteruser.import', compact('title', 'listurl', 'listname', 'samplebutton', 'sampleurl', 'addurl'));
    }

    public function store_import_masteruser(Request $request)
    {
        $this->validate($request, [
            'importfile' => 'required|mimes:csv,txt',
        ]);
        $log = "";
        $filename = '';
        if ($file = $request->file('importfile')) {
            $filename = time() . '-' . $file->getClientOriginalName();
            $file->move(public_path('/temp_files/'), $filename);
        }
        $datas = "";
        $file = fopen(public_path('/temp_files/' . $filename), "r");
        $i = 0;
        while (($line = fgetcsv($file)) !== FALSE) {
            if ($i > 0) {
                $a = new MasterUser();
                $a->name_en = $line[0];
                $a->name_hi = $line[1];
                $a->name_ta = $line[2];
                $a->name_ml = $line[3];
                $a->name_te = $line[4];
                $a->name_kn = $line[5];
                $a->position = $line[6];
                $a->status = $line[7];
                $a->save();
            }
            $i++;
        }
        return redirect()->route('admin.master.masteruser')
            ->with('success', 'master user impoted successfully ' . $log);
    }

    //masterlanguage
    public function masterlanguage()
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Name",
                "Position",
                "Status",
                "Created At"
            );
            $fetch = MasterLangauage::where('status', '!=', 2)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->status == 1) {
                    $st = "Active";
                } else if ($user->status == 2) {
                    $st = "Deleted";
                } else {
                    $st = "Inactive";
                }

                $data[] = array(
                    "ID" => $i,
                    "name" => $user->name,
                    "position" => $user->position,
                    "status" => $st,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }

            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"MasterTax" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        $data = MasterLangauage::whereNOTIN('status', [2])->orderBy('id', 'ASC')->get();
        $addbutton = 'Add MasterLangauage';
        $importbutton = 'Upload Excel';
        $title = 'MasterLangauage List';
        $listurl = route('admin.master.masterlanguage');
        $addurl = route('admin.master.create_masterlanguage');
        $importurl = route('admin.master.import_masterlanguage');
        $editurl = 'admin.master.edit_masterlanguage';
        $destroyurl = route('admin.master.destroy_masterlanguage');
        $addurl1 = route('admin.master.store_masterlanguage');
        return view('admin.masterlanguage.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl'));
    }

    public function create_masterlanguage()
    {
        $title = 'Add MasterLangauage';
        $listname = 'MasterLangauage';
        $listurl = route('admin.master.masterlanguage');
        $addurl = route('admin.master.store_masterlanguage');
        return view('admin.masterlanguage.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_masterlanguage(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'position' => 'required',
            'status' => 'required',

        ]);
        $a = new MasterLangauage();
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.masterlanguage')
            ->with('success', 'MasterLangauage created successfully');
    }


    public function edit_masterlanguage($id)
    {
        $title = 'Edit MasterLangauage';
        $listname = 'MasterLangauage';
        $listurl = route('admin.master.masterlanguage');
        $editurl = 'admin.master.update_masterlanguage';
        $a = MasterLangauage::find($id);
        if ($a) {
            return view('admin.masterlanguage.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.masterlanguage')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_masterlanguage(Request $request, $id)
    {
        $this->validate($request, [
            // 'name' => 'required',
            'position' => 'required',
            'status' => 'required',
        ]);

        $a = MasterLangauage::find($id);
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.masterlanguage')
            ->with('success', 'MasterLangauage updated successfully');
    }

    public function destroy_masterlanguage(Request $request)
    {
        $input = $request->all();
        $a = MasterLangauage::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.masterlanguage')
            ->with('success', 'MasterLangauage deleted successfully');
    }


    public function master_specialsations()
    {
        $data = MasterSpecialsation::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add MasterSpecialsation';
        $importbutton = 'Upload Excel';
        $title = 'MasterSpecialsation';
        $listurl = route('admin.master.master_specialsations');
        $addurl = route('admin.master.create_master_specialsations');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_master_specialsations';
        $destroyurl = route('admin.master.destroy_master_specialsations');
        $addurl1 = route('admin.master.store_master_services');
        return view('admin.master_specialsations.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }


    public function create_master_specialsations()
    {
        $title = 'Add MasterSpecialsation';
        $listname = 'MasterSpecialsation';
        $listurl = route('admin.master.master_specialsations');
        $addurl = route('admin.master.store_master_specialsations');
        return view('admin.master_specialsations.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_specialsations(Request $request)
    {

        $this->validate($request, [
        ]);

        $a = new MasterSpecialsation();
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_specialsations')
            ->with('success', 'MasterSpecialsation created successfully!');
    }


    public function edit_master_specialsations($id)
    {
        $title = 'Edit MasterSpecialsation';
        $listname = 'MasterSpecialsation';
        $listurl = route('admin.master.master_specialsations');
        $editurl = 'admin.master.update_master_specialsations';
        $a = MasterSpecialsation::find($id);
        if ($a) {
            return view('admin.master_specialsations.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.master_specialsations')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_specialsations(Request $request, $id)
    {

        $a = MasterSpecialsation::find($id);
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_specialsations')
            ->with('success', 'MasterSpecialsation updated successfully');
    }

    public function destroy_master_specialsations(Request $request)
    {
        $input = $request->all();
        $a = MasterSpecialsation::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_specialsations')
            ->with('success', 'MasterSpecialsation deleted successfully');
    }



    public function master_services()
    {
        $data = MasterServices::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add MasterServices';
        $importbutton = 'Upload Excel';
        $title = 'MasterServices';
        $listurl = route('admin.master.master_services');
        $addurl = route('admin.master.create_master_services');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_master_services';
        $destroyurl = route('admin.master.destroy_master_services');
        $addurl1 = route('admin.master.store_master_services');
        return view('admin.master_services.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }


    public function create_master_services()
    {
        $title = 'Add MasterServices';
        $listname = 'MasterServices';
        $listurl = route('admin.master.master_registraion_councils');
        $addurl = route('admin.master.store_master_registraion_councils');
        return view('admin.master_services.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_services(Request $request)
    {

        $this->validate($request, [
        ]);

        $a = new MasterServices();
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_services')
            ->with('success', 'MasterServices created successfully!');
    }


    public function edit_master_services($id)
    {
        $title = 'Edit MasterServices';
        $listname = 'MasterServices';
        $listurl = route('admin.master.master_services');
        $editurl = 'admin.master.update_master_services';
        $a = MasterServices::find($id);
        if ($a) {
            return view('admin.master_services.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.master_services')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_services(Request $request, $id)
    {

        $a = MasterServices::find($id);
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_services')
            ->with('success', 'MasterServices updated successfully');
    }

    public function destroy_master_services(Request $request)
    {
        $input = $request->all();
        $a = MasterServices::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_services')
            ->with('success', 'MasterServices deleted successfully');
    }




    public function master_registraion_councils()
    {
        $data = MasterRegistraionCouncil::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add MasterRegistraionCouncil';
        $importbutton = 'Upload Excel';
        $title = 'MasterRegistraionCouncil';
        $listurl = route('admin.master.master_registraion_councils');
        $addurl = route('admin.master.create_master_registraion_councils');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_master_registraion_councils';
        $destroyurl = route('admin.master.destroy_master_registraion_councils');
        $addurl1 = route('admin.master.store_master_registraion_councils');
        return view('admin.master_registraion_councils.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }


    public function create_master_registraion_councils()
    {
        $title = 'Add MasterRegistraionCouncil';
        $listname = 'MasterRegistraionCouncil';
        $listurl = route('admin.master.master_registraion_councils');
        $addurl = route('admin.master.store_master_registraion_councils');
        return view('admin.master_registraion_councils.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_registraion_councils(Request $request)
    {

        $this->validate($request, [
        ]);

        $a = new MasterRegistraionCouncil();
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_registraion_councils')
            ->with('success', 'MasterRegistraionCouncil created successfully!');
    }


    public function edit_master_registraion_councils($id)
    {
        $title = 'Edit MasterRegistraionCouncil';
        $listname = 'MasterRegistraionCouncil';
        $listurl = route('admin.master.master_registraion_councils');
        $editurl = 'admin.master.update_master_registraion_councils';
        $a = MasterRegistraionCouncil::find($id);
        if ($a) {
            return view('admin.master_registraion_councils.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.master_registraion_councils')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_registraion_councils(Request $request, $id)
    {

        $a = MasterRegistraionCouncil::find($id);
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_registraion_councils')
            ->with('success', 'MasterRegistraionCouncil updated successfully');
    }

    public function destroy_master_registraion_councils(Request $request)
    {
        $input = $request->all();
        $a = MasterRegistraionCouncil::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_registraion_councils')
            ->with('success', 'MasterRegistraionCouncil deleted successfully');
    }


    public function master_degrees()
    {
        $data = MasterDegree::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add MasterDegree';
        $importbutton = 'Upload Excel';
        $title = 'MasterDegree';
        $listurl = route('admin.master.master_degrees');
        $addurl = route('admin.master.create_master_degrees');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_master_degrees';
        $destroyurl = route('admin.master.destroy_master_degrees');
        $addurl1 = route('admin.master.store_blog');
        return view('admin.master_degrees.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }


    public function create_master_degrees()
    {
        $title = 'Add MasterDegree';
        $listname = 'MasterDegree';
        $listurl = route('admin.master.master_degrees');
        $addurl = route('admin.master.store_master_degrees');
        return view('admin.master_degrees.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_degrees(Request $request)
    {

        $this->validate($request, [
        ]);

        $a = new MasterDegree();
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_degrees')
            ->with('success', 'MasterDegree created successfully!');
    }


    public function edit_master_degrees($id)
    {
        $title = 'Edit MasterDegree';
        $listname = 'MasterDegree';
        $listurl = route('admin.master.master_degrees');
        $editurl = 'admin.master.update_master_degrees';
        $a = MasterDegree::find($id);
        if ($a) {
            return view('admin.master_degrees.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.master_degrees')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_degrees(Request $request, $id)
    {

        $a = MasterDegree::find($id);
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_degrees')
            ->with('success', 'MasterDegree updated successfully');
    }

    public function destroy_master_degrees(Request $request)
    {
        $input = $request->all();
        $a = MasterDegree::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_degrees')
            ->with('success', 'MasterDegree deleted successfully');
    }




    public function master_college_institutes()
    {
        $data = MasterCollegeInstitute::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add MasterCollegeInstitute';
        $importbutton = 'Upload Excel';
        $title = 'MasterCollegeInstitute';
        $listurl = route('admin.master.master_college_institutes');
        $addurl = route('admin.master.create_master_college_institutes');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_master_college_institutes';
        $destroyurl = route('admin.master.destroy_master_college_institutes');
        $addurl1 = route('admin.master.store_blog');
        return view('admin.master_college_institutes.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }


    public function create_master_college_institutes()
    {
        $title = 'Add MasterCollegeInstitute';
        $listname = 'MasterCollegeInstitute';
        $listurl = route('admin.master.master_college_institutes');
        $addurl = route('admin.master.store_master_college_institutes');
        return view('admin.master_college_institutes.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_college_institutes(Request $request)
    {

        $this->validate($request, [
        ]);


        $a = new MasterCollegeInstitute();
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_college_institutes')
            ->with('success', 'MasterCollegeInstitute created successfully!');
    }


    public function edit_master_college_institutes($id)
    {
        $title = 'Edit MasterCollegeInstitute';
        $listname = 'MasterCollegeInstitute';
        $listurl = route('admin.master.master_college_institutes');
        $editurl = 'admin.master.update_master_college_institutes';
        $a = MasterCollegeInstitute::find($id);
        if ($a) {
            return view('admin.master_college_institutes.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.master_college_institutes')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_college_institutes(Request $request, $id)
    {

        $a = MasterCollegeInstitute::find($id);
        $a->name = $request->name;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_college_institutes')
            ->with('success', 'MasterCollegeInstitute updated successfully');
    }

    public function destroy_master_college_institutes(Request $request)
    {
        $input = $request->all();
        $a = MasterCollegeInstitute::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_college_institutes')
            ->with('success', 'MasterCollegeInstitute deleted successfully');
    }



    public function master_drugs()
    {
        $data = Drug::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add Drug';
        $importbutton = 'Upload Excel';
        $title = 'Drug';
        $listurl = route('admin.master.master_drugs');
        $addurl = route('admin.master.create_master_drugs');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_master_drugs';
        $destroyurl = route('admin.master.destroy_master_drugs');
        $addurl1 = route('admin.master.store_blog');
        return view('admin.master_drugs.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }


    public function create_master_drugs()
    {
        $title = 'Add Drug';
        $listname = 'Drug';
        $listurl = route('admin.master.master_drugs');
        $addurl = route('admin.master.store_master_drugs');
        return view('admin.master_drugs.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_drugs(Request $request)
    {

        $this->validate($request, [
        ]);


        $a = new Drug();
        $a->name = $request->name;
        // $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_drugs')
            ->with('success', 'Drug created successfully!');
    }


    public function edit_master_drugs($id)
    {
        $title = 'Edit Drug';
        $listname = 'Drug';
        $listurl = route('admin.master.master_drugs');
        $editurl = 'admin.master.update_master_drugs';
        $a = Drug::find($id);
        if ($a) {
            return view('admin.master_drugs.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.master_drugs')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_drugs(Request $request, $id)
    {

        $a = Drug::find($id);
        $a->name = $request->name;
        // $a->position = $request->position;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.master.master_drugs')
            ->with('success', 'Drug updated successfully');
    }

    public function destroy_master_drugs(Request $request)
    {
        $input = $request->all();
        $a = Drug::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_drugs')
            ->with('success', 'Drug deleted successfully');
    }


    //blog
    public function blog()
    {
        $data = Blog::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add Blog';
        $importbutton = 'Upload Excel';
        $title = 'Blogs';
        $listurl = route('admin.master.blog');
        $addurl = route('admin.master.create_blog');
        $importurl = route('admin.master.import_blog');
        $editurl = 'admin.master.edit_blog';
        $destroyurl = route('admin.master.destroy_blog');
        $addurl1 = route('admin.master.store_blog');
        return view('admin.blog.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_blog()
    {
        $title = 'Add Blog';
        $listname = 'Blogs';
        $listurl = route('admin.master.blog');
        $addurl = route('admin.master.store_blog');
        return view('admin.blog.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_blog(Request $request)
    {

        // dd($request->all()); die;
        $this->validate($request, [
            // 'type' => 'required|string',
            // 'title' => 'required|string|max:255',
            // 'content' => 'required|string',
            // 'video_link' => 'nullable|url',
            // 'position' => 'required|integer',
            // 'status' => 'required|boolean',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = 'default.png';

        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('../content/blogs', $image);
        }

        $a = new Blog();
        $a->type = $request->type;
        $a->name = $request->name;
        $a->description = $request->description;
        $a->video_link = $request->video_link;
        $a->position = $request->position;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.blog')
            ->with('success', 'Blog created successfully!');
    }



    public function edit_blog($id)
    {
        $title = 'Edit Blog';
        $listname = 'Blogs';
        $listurl = route('admin.master.blog');
        $editurl = 'admin.master.update_blog';
        $a = Blog::find($id);
        if ($a) {
            return view('admin.blog.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.blog')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_blog(Request $request, $id)
    {

        $a = Blog::find($id);
        $image = $a->image;
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('../content/blogs', $image);
        }
        $a->type = $request->type;
        $a->name = $request->name;
        $a->description = $request->description;
        $a->position = $request->position;
        $a->video_link = $request->video_link;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.blog')
            ->with('success', 'Blog updated successfully');
    }

    public function destroy_blog(Request $request)
    {
        $input = $request->all();
        $a = Blog::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.blog')
            ->with('success', 'Blog deleted successfully');
    }


    //master_templates
    public function master_templates()
    {
        $data = MasterTemplate::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
        $addbutton = 'Add MasterTemplate';
        $importbutton = 'Upload Excel';
        $title = 'MasterTemplate';
        $listurl = route('admin.master.master_templates');
        $addurl = route('admin.master.create_master_templates');
        $importurl = route('admin.master.import_banner');
        $editurl = 'admin.master.edit_master_templates';
        $destroyurl = route('admin.master.destroy_master_templates');
        $addurl1 = route('admin.master.store_banner');
        return view('admin.master_templates.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
    }

    public function create_master_templates()
    {
        $title = 'Add MasterTemplate';
        $listname = 'MasterTemplate';
        $listurl = route('admin.master.master_templates');
        $addurl = route('admin.master.store_master_templates');
        return view('admin.master_templates.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_master_templates(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);


        $image = 'default.png';
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/theme', $image);
        }


        $a = new MasterTemplate();
        $a->name = $request->name;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.master_templates')
            ->with('success', 'MasterTemplate created successfully');
    }


    public function edit_master_templates($id)
    {
        $title = 'Edit MasterTemplate';
        $listname = 'MasterTemplate';
        $listurl = route('admin.master.master_templates');
        $editurl = 'admin.master.update_master_templates';
        $a = MasterTemplate::find($id);
        if ($a) {
            return view('admin.master_templates.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.master.blog')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_master_templates(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $a = MasterTemplate::find($id);
        $image = $a->image;
        if (request('image')) {

            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/theme', $image);
        }
        $a->name = $request->name;
        $a->status = $request->status;
        $a->image = $image;
        $a->save();
        return redirect()->route('admin.master.master_templates')
            ->with('success', 'MasterTemplate updated successfully');
    }

    public function destroy_master_templates(Request $request)
    {
        $input = $request->all();
        $a = MasterTemplate::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.master.master_templates')
            ->with('success', 'MasterTemplate deleted successfully');
    }



        //seasonal_offer
        public function seasonal_offer()
        {
            $data = SeasonalOffers::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $user_data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $addbutton = 'Add SeasonalOffers';
            $importbutton = 'Upload Excel';
            $title = 'SeasonalOffers';
            $listurl = route('admin.master.seasonal_offer');
            $addurl = route('admin.master.create_seasonal_offer');
            $importurl = route('admin.master.import_banner');
            $editurl = 'admin.master.edit_seasonal_offer';
            $destroyurl = route('admin.master.destroy_seasonal_offer');
            $addurl1 = route('admin.master.store_banner');
            return view('admin.seasonal_offer.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1','user_data'));
        }

        public function create_seasonal_offer()
        {
            $title = 'Add SeasonalOffers';
            $listname = 'SeasonalOffers';
            $user_data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $listurl = route('admin.master.seasonal_offer');
            $addurl = route('admin.master.store_seasonal_offer');
            return view('admin.seasonal_offer.create', compact('title', 'listurl', 'listname', 'addurl','user_data'));
        }

        public function store_seasonal_offer(Request $request)
        {
            $this->validate($request, [
                // 'name' => 'required',
                'status' => 'required',
            ]);


            $image = 'default.png';
            if (request('image')) {

                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
                $filePath = request('image')->move('../content/SeasonalOffers', $image);
            }


            $a = new SeasonalOffers();
            $a->user_id = auth()->user()->id;
            $a->created_by = 0;
            $a->offer_type = $request->offer_type;
            $a->treat_package_id = $request->treat_package_id;
            $a->discount = $request->discount;
            $a->start_date = $request->start_date;
            $a->end_date = $request->end_date;
            $a->status = $request->status;
            $a->image = $image;
            $a->save();
            return redirect()->route('admin.master.seasonal_offer')
                ->with('success', 'SeasonalOffers created successfully');
        }


        public function edit_seasonal_offer($id)
        {
            $title = 'Edit SeasonalOffers';
            $listname = 'SeasonalOffers';
            $listurl = route('admin.master.seasonal_offer');
            $user_data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $editurl = 'admin.master.update_seasonal_offer';
            $a = SeasonalOffers::find($id);
            if ($a) {
                return view('admin.seasonal_offer.edit', compact('a', 'title', 'listurl', 'listname', 'editurl','user_data'));
            } else {
                return redirect()->route('admin.master.seasonal_offer')
                    ->with('failure', 'Not found any data');
            }
        }

        public function update_seasonal_offer(Request $request, $id)
        {
            $this->validate($request, [
                // 'name' => 'required',
                'status' => 'required',
            ]);

            $a = SeasonalOffers::find($id);
            $image = $a->image;
            if (request('image')) {

                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
                $filePath = request('image')->move('../content/SeasonalOffers', $image);
            }
            $a->user_id = auth()->user()->id;
            $a->created_by = 0;
            $a->offer_type = $request->offer_type;
            $a->treat_package_id = $request->treat_package_id;
            $a->discount = $request->discount;
            $a->start_date = $request->start_date;
            $a->end_date = $request->end_date;
            $a->status = $request->status;
            $a->image = $image;
            $a->save();
            return redirect()->route('admin.master.seasonal_offer')
                ->with('success', 'SeasonalOffers updated successfully');
        }

        public function destroy_seasonal_offer(Request $request)
        {
            $input = $request->all();
            $a = SeasonalOffers::find($input['id']);
            $a->status = 2;
            $a->save();
            return redirect()->route('admin.master.seasonal_offer')
                ->with('success', 'SeasonalOffers deleted successfully');
        }



        //user_lists
        public function user_lists()
        {
            $data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $addbutton = 'Add Patients';
            $importbutton = 'Upload Excel';
            $title = 'Patients List';
            $listurl = route('admin.master.user_lists');
            $addurl = route('admin.master.create_seasonal_offer');
            $importurl = route('admin.master.import_banner');
            $editurl = 'admin.master.edit_seasonal_offer';
            $destroyurl = route('admin.master.destroy_seasonal_offer');
            $addurl1 = route('admin.master.store_banner');
            return view('admin.users.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1'));
        }

       public function user_detail($id)
        {
            $a = UserProfile::find($id);
            $booking = Booking::where('status',1)->where('user_id', $id)->get();
            return view('admin.users.user-details',compact('a','booking'));
        }


        //medical_records
        public function medical_records()
        {
            $data = MedicalRecord::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $user_data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $addbutton = 'Add MedicalRecord';
            $importbutton = 'Upload Excel';
            $title = 'MedicalRecord';
            $listurl = route('admin.master.medical_records');
            $addurl = route('admin.master.create_medical_records');
            $importurl = route('admin.master.import_banner');
            $editurl = 'admin.master.edit_medical_records';
            $destroyurl = route('admin.master.destroy_medical_records');
            $addurl1 = route('admin.master.store_banner');
            return view('admin.medical_records.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1','user_data'));
        }

        public function create_medical_records()
        {
            $title = 'Add MedicalRecord';
            $listname = 'MedicalRecord';
            $user_data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $listurl = route('admin.master.medical_records');
            $addurl = route('admin.master.store_medical_records');
            return view('admin.medical_records.create', compact('title', 'listurl', 'listname', 'addurl','user_data'));
        }

        public function store_medical_records(Request $request)
        {
            $this->validate($request, [
                // 'name' => 'required',
                'status' => 'required',
            ]);


            $image = 'default.png';
            if (request('image')) {

                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
                $filePath = request('image')->move('../content/user', $image);
            }


            $a = new MedicalRecord();
            $a->admin_id = auth()->user()->id;
            $a->member_type = 'admin';
            $a->type = $request->type;
            $a->status = $request->status;
            $a->image = $image;
            $a->save();
            return redirect()->route('admin.master.medical_records')
                ->with('success', 'MedicalRecord created successfully');
        }


        public function edit_medical_records($id)
        {
            $title = 'Edit MedicalRecord';
            $listname = 'MedicalRecord';
            $listurl = route('admin.master.seasonal_offer');
            $user_data = UserProfile::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $editurl = 'admin.master.update_medical_records';
            $a = MedicalRecord::find($id);
            if ($a) {
                return view('admin.medical_records.edit', compact('a', 'title', 'listurl', 'listname', 'editurl','user_data'));
            } else {
                return redirect()->route('admin.master.medical_records')
                    ->with('failure', 'Not found any data');
            }
        }

        public function update_medical_records(Request $request, $id)
        {
            $this->validate($request, [
                // 'name' => 'required',
                'status' => 'required',
            ]);

            $a = MedicalRecord::find($id);
            $image = $a->image;
            if (request('image')) {

                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image = 'image_' . $fileName . '_' . time() . '.' . $extension;
                $filePath = request('image')->move('../content/user', $image);
            }
            $a->admin_id = auth()->user()->id;
            $a->member_type = 'admin';
            $a->type = $request->type;
            $a->status = $request->status;
            $a->image = $image;
            $a->save();
            return redirect()->route('admin.master.medical_records')
                ->with('success', 'MedicalRecord updated successfully');
        }

        public function destroy_medical_records(Request $request)
        {
            $input = $request->all();
            $a = MedicalRecord::find($input['id']);
            $a->status = 2;
            $a->save();
            return redirect()->route('admin.master.medical_records')
                ->with('success', 'MedicalRecord deleted successfully');
        }



    public function notification_data()
    {
        $data = AdminNotification::orderBy('id', 'DESC')->paginate(10);
        $title = 'AdminNotification List';
        $listurl = route('admin.master.notification_data');
        DB::table('admin_notifications')->where('read', 0)->update(['read' => 1]);
        return view('admin.notification_data.index', compact('data', 'title', 'listurl'));
    }

    public function askaquestionassignhistory($id)
    {
        $data = AskAQuestionAssignHistory::where('booking_id', $id)->get();
        $addbutton = 'Ask A Question Assgin History';
        $importbutton = 'Upload Excel';
        $title = 'Ask A Question Assgin History';
        $listurl = route('admin.master.askaquestionindex');
        $addurl = '';
        $importurl = '';
        $editurl = '';
        $destroyurl = '';
        $addurl1 = '';
        return view('admin.ask.assignhistory', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'id'));
    }

    public function exportaaqassignhistory($id)
    {

        $data[] = array(
            "Assign Date",
            "From Astrologer",
            "To Astrologer",
            "Date",
            "updated_at"
        );
        $date1 = AskAQuestionAssignHistory::where('booking_id', $id)->get();

        $i = 1;
        foreach ($date1 as $a) {

            $data[] = array(
                $a->assigned_on,
                $a->astrod->slug  ?? '',
                $a->astrod1->slug  ?? '',
                $a->created_at,
                $a->updated_at
            );
            $i++;
        }
        $string_file = date("d-m-Y h:i:s A");

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"AssignHistory" . $id . '-' . date('y-m-d-h:i:s') . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');
        foreach ($data as $data) {
            fputcsv($handle, $data);
        }
        fclose($handle);
        exit;
    }




    public function all_exportaaqassignhistory(Request $request,)
    {

        $data[] = array(
            "Order Id",
            // "Assign Date",
            "From Astrologer",
            "To Astrologer",
            "Date",
            "updated_at",
            "Status"
        );

        $queryUser11 = AskAQuestionAssignHistory::query();

        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser11->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            } elseif (!is_null($request->start_date)) {
                $queryUser11->whereDate('created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryUser11->whereDate('created_at', $request->end_date);
            }
        }

        $date1 = $queryUser11->get();
        // $date1 = AskAQuestionAssignHistory::orderBy('booking_id','ASC')->get();

        $i = 1;
        foreach ($date1 as $a) {


            $astrologers = AskAQuestions::where('id', $a->booking_id)->first();
            if ($astrologers->status == 1) {
                $status = "Complete";
            } else {
                $status = "Pending";
            }
            $data[] = array(
                $a->booking_id,
                // $a->assigned_on,
                $a->astrod->slug  ?? '',
                $a->astrod1->slug  ?? '',
                $a->created_at,
                $a->updated_at,
                $status
            );
            $i++;
        }


        // print_r($data); die;


        $string_file = date("d-m-Y h:i:s A");

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"AssignHistory" . '-' . date('y-m-d-h:i:s') . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');
        foreach ($data as $data) {
            fputcsv($handle, $data);
        }
        fclose($handle);
        exit;
    }
    public function all_exportaaq(Request $request,)
    {

        $data[] = array(
            "Order Id",
            "User ID",
            // "User Name",
            "Astrologer Name",
            "Message",
            "Reply Message",
            "Date",
            "updated_at",
            "Status",
            "Give Remedie"
        );

        // $date1 = AskAQuestions::where('from_type',0)->orderBy('id','ASC')->get();


        $queryUser11 = AskAQuestions::query();

        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser11->whereBetween('added_on', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            } elseif (!is_null($request->start_date)) {
                $queryUser11->whereDate('added_on', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryUser11->whereDate('added_on', $request->end_date);
            }
        }

        $queryUser11->orderBy('id', 'DESC');
        $queryUser11->where('from_type', 0);

        $date1 = $queryUser11->get();



        $i = 1;
        foreach ($date1 as $a) {
            // $astrologers = AskAQuestions::where('id',$a->booking_id)->first();

            if ($a->replied_to) {
                $astrologer_list = Astrologer::where('id', $a->replied_to)->first();
                if (!empty($astrologer_list->screen_name)) {
                    $nm2 = json_decode($astrologer_list->screen_name, true);
                    $screen_name = $nm2[1] ?? '';
                } else {
                    $screen_name = 'astrologer';
                }
            } else {
                $astrologer_list = Astrologer::where('id', $a->assigned_id)->first();
                if (!empty($astrologer_list->screen_name)) {
                    $nm2 = json_decode($astrologer_list->screen_name, true);
                    $screen_name = $nm2[1] ?? '';
                } else {
                    $screen_name = 'astrologer';
                }
            }


            $replydate = AskAQuestions::where('replied_to', $a->id)->first();

            // print_r($replydate); die;
            // $astrologers = AskAQuestions::where('id',$a->booking_id)->first();
            if ($a->status == 1) {

                $status = "Complete";
            } else {
                $status = "Pending";
            }

            if ($a->give_remedie == 1) {

                $give_remedie = "Yes";
            } else {
                $give_remedie = "No";
            }


            $data[] = array(
                $a->id,
                $a->user_id,
                $screen_name  ?? '',
                // $a->astrod1->slug  ?? '',
                $a->message,
                $replydate->message ?? " ",
                $a->added_on,
                $a->updated_at,
                $status,
                $give_remedie
            );
            $i++;
        }


        // print_r($data); die;


        $string_file = date("d-m-Y h:i:s A");

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"AAQALLHistory" . '-' . date('y-m-d-h:i:s') . ".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');
        foreach ($data as $data) {
            fputcsv($handle, $data);
        }
        fclose($handle);
        exit;
    }


    public function astrologer_request_delete(Request $request)
    {


        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Astrolger Name",
                "Astrologer Mobile",
                "Status",
                "Req Status",
                "Req date",
                "Update date",

            );



            $queryqb = Astrologer::query();
            $queryqb->select('astrologers.*');
            $queryqb->orderBy('astrologers.updated_at', 'DESC');
            // $queryqb->whereNOTIN('astrologers.id',[0]);
            $queryqb->where('astrologers.req_delete_account', 1);
            if (!is_null($request->name)) {

                // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();

                $queryqb->whereRaw("((astrologers.screen_name like '%" . $request->name . "%' ))");
            }

            if (!is_null($request->mobile)) {

                // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();

                $queryqb->whereRaw("((astrologers.mobile like '%" . $request->mobile . "%' ))");
            }

            // print_r()
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('astrologers.req_delete_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('astrologers.req_delete_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('astrologers.req_delete_date', $request->end_date);
                }
            }

            $fetch = $queryqb->get();


            // dd( $fetch);
            // $fetch = DB::table('astrologer_transactions')->where('status', 1)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {


                $name5 = json_decode($user->name, true);
                $a_name5 = $name5[1] ?? 'Astro N';



                $type = "";
                if ($user->req_delete_account == "1") {
                    $type =  "Request Delete";
                } else {
                    $type =  "";
                }


                if ($user->req_delete_date) {
                    $req_delete_date = date('d M y', strtotime($user->req_delete_date));
                } else {
                    $req_delete_date = '';
                }

                if ($user->req_update_delete) {
                    $req_update_delete = date('d M y', strtotime($user->req_update_delete));
                } else {
                    $req_update_delete = '';
                }





                $st = "";
                if ($user->status == 1) {
                    $st = "Active";
                } else if ($user->status == 2) {
                    $st = "New Pendig for verification";
                } else {
                    $st = "delete";
                }
                $data[] = array(
                    "ID" => $i,
                    "name" => $a_name5,
                    "mobile" => $user->mobile,
                    "st" => $st,
                    "type" => $type,

                    "req_delete_date" => $req_delete_date,
                    "req_update_delete" => $req_update_delete,
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"astrologer_request_delete" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }


        $title = "Astrologer Delete Account Request";
        // $data = DB::table('astrologers_request')->orderby('id', 'ASC')->get();
        // dd($data);
        // $destroyurl = route('admin.astrologer-manage.approve_incentive_astrologer');
        $destroyurl = route('admin.master.astrologer_request_delete_account');


        $queryqb = Astrologer::query();
        $queryqb->select('astrologers.*');
        $queryqb->orderBy('astrologers.updated_at', 'DESC');
        // $queryqb->whereNOTIN('astrologers.id',[0]);
        $queryqb->where('astrologers.req_delete_account', 1);
        if (!is_null($request->name)) {

            // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();

            $queryqb->whereRaw("((astrologers.screen_name like '%" . $request->name . "%' ))");
        }

        if (!is_null($request->mobile)) {


            $queryqb->whereRaw("((astrologers.mobile like '%" . $request->mobile . "%' ))");
        }

        // print_r()
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('astrologers.req_delete_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('astrologers.req_delete_date', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('astrologers.req_delete_date', $request->end_date);
            }
        }

        $data = $queryqb->get();



        // $data = Astrologer::where('req_delete_account', 1)->get();
        return view('admin.astrologer_request_list.astrologer_request_delete', compact('title', 'data', 'destroyurl'));
        // dd($data);
    }


    public function astrologer_request_delete_account(Request $request)
    {

        // $a =  DB::table('astrologer_rating_reviews')->where('id',$request->id);
        // $a->is_show = 1;
        // $a->save();
        $input = $request->all();

        // print_r($input['id']); die;
        $a = Astrologer::find($input['id']);
        $a->status = 0;
        $a->req_update_delete = date('Y-m-d H:i:s');
        $a->save();
        return redirect()->back()
            ->with('success', 'Delete successfully');
    }


    public function astrologer_request_list(Request $request)
    {
        $title = "Astrologer Request List";
        $data = DB::table('astrologers_request')->orderby('id', 'ASC')->get();
        // dd($data);
        $astrologer_list = Astrologer::where('status', 1)->get();
        return view('admin.astrologer_request_list.index', compact('title', 'data', 'astrologer_list'));
        // dd($data);
    }

    public function astrologer_request_step1_edit($id, $what = '')
    {
        $title = "Astrologer Request List Step 1";
        $editurl = 'admin.master.astrologer_request_step1_update';
        $a = DB::table('astrologers_request')->find($id);
        if ($a) {
            return view('admin.astrologer_request_list.edit', compact('title', 'a', 'editurl', 'what'));
        } else {
            return redirect()->route('admin.master.astrologer_request_list', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function astrologer_request_step2_edit($id, $what = '')
    {
        $title = "Astrologer Request List Step 2";
        $editurl = 'admin.master.astrologer_request_step2_update';
        // $a = DB::table('astrologers_request')->where('id', $id)->first();
        $a = DB::table('astrologers_request')->find($id);
        if ($a) {
            return view('admin.astrologer_request_list.edit2', compact('title', 'a', 'editurl', 'what'));
        } else {
            return redirect()->route('admin.master.astrologer_request_list', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function astrologer_request_step3_edit($id, $what = '')
    {
        $title = "Astrologer Request List Step 3";
        $editurl = 'admin.master.astrologer_request_step3_update';
        $a = DB::table('astrologers_request')->find($id);
        // $a = DB::table('astrologers_request')->where('id', $id)->first();
        if ($a) {
            return view('admin.astrologer_request_list.edit3', compact('title', 'a', 'editurl', 'what'));
        } else {
            return redirect()->route('admin.master.astrologer_request_list', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function astrologer_request_step1_update(Request $request, $id)
    {

        $title = "Astrologer Request List Step 1";

        $astrologerRequest = DB::table('astrologers_request')->where('id', $id)->first();

        if (!empty($astrologerRequest)) {


            $a = Astrologer::find($astrologerRequest->astrologer_id);

            //   print_r($request->area); die;


            $a->name = json_encode($request->input('name'));
            $a->alternate_mobile = $request->input('alternate_mobile');
            $a->screen_name = json_encode($request->input('screen_name'));
            $a->email = $request->input('email');
            $a->gender = $request->input('gender');
            $a->experience = $request->input('experience');


            $a->save();


            AstrologerAreaofExpertise::where('astrologer_id', $astrologerRequest->astrologer_id)->delete();

            if ($request->area != null) {
                foreach ($request->area as $areaa) {
                    $check = AstrologerAreaofExpertise::where('astrologer_id', $astrologerRequest->astrologer_id)->where('areaofexpertise_id', $areaa)->first();
                    if ($check) {
                        $check->status = 1;
                        $check->save();
                    } else {
                        $l = new AstrologerAreaofExpertise();
                        $l->astrologer_id = $astrologerRequest->astrologer_id;
                        $l->areaofexpertise_id = $areaa;
                        $l->status = 1;
                        $l->save();
                    }
                }
            }
            AstrologerLanguage::where('astrologer_id', $astrologerRequest->astrologer_id)->delete();

            // AstrologerLanguage::where('astrologer_id',$a->id)
            // ->update([
            // 'status' => 0
            // ]);
            if ($request->lang != null) {
                foreach ($request->lang as $llang) {
                    $check = AstrologerLanguage::where('astrologer_id', $astrologerRequest->astrologer_id)->where('language_id', $llang)->first();
                    if ($check) {
                        $check->status = 1;
                        $check->save();
                    } else {
                        $ln = new AstrologerLanguage();
                        $ln->astrologer_id = $astrologerRequest->astrologer_id;
                        $ln->language_id = $llang;
                        $ln->status = 1;
                        $ln->save();
                    }
                }
            }


            $updatedStepOneValue = 2;

            DB::table('astrologers_request')
                ->where('id', $id)
                ->update(['step_one_req' => $updatedStepOneValue, 'req_update' => 1]);

            return redirect()->route('admin.master.astrologer_request_list')
                ->with('success', 'Astrologer Request List Updated Successfully');
        }


        //     return redirect()->route('admin.master.astrologer_request_list')
        //         ->with('success', 'Astrologer Request List Updated Successfully !');
        // }
        else {

            return redirect()->route('admin.master.astrologer_request_list')
                ->with('failure', 'Failed to update Astrologer Request List. Please try again.');
        }

        // dd($data);
        // $astrologer_list = Astrologer::where('status', 1)->get();
        // return redirect()->route('admin.master.astrologer_request_list');

        // return redirect()->back();
        // return view('admin.astrologer_request_list.edit',compact('editurl','title', 'a','astrologer_list'));

    }

    public function astrologer_request_step2_update(Request $request, $id)
    {
        $data = $request->validate([]);

        DB::beginTransaction();

        try {

            $a = DB::table('astrologers_request')->where('id', $id)->first();
            if (!$a) {
                $steptwoValue = json_decode($a->step_two, true);
                $steptwoValue['1']['about'] = $request->input('about');
                $steptwoValue['1']['image'] = $request->input('image');

                $a->step_two = json_encode($steptwoValue);
                $a->save();

                $updatedSteptwoValue = json_encode($steptwoValue);

                DB::table('astrologers_request')
                    ->where('id', $id)
                    ->update(['step_two' => $updatedSteptwoValue]);

                return redirect()->route('admin.master.astrologer_request_list')
                    ->with('failure', 'Not found any data');
            }

            DB::commit();

            return redirect()->route('admin.master.astrologer_request_list')
                ->with('success', 'Astrologer Request List Updated Successfully !');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('admin.master.astrologer_request_list')
                ->with('failure', 'Failed to update Astrologer Request List. Please try again.');
        }
    }


    public function astrologer_request_step3_update(Request $request, $id)
    {
        $data = $request->validate([]);

        DB::beginTransaction();

        try {
            $a = DB::table('astrologers_request')->where('id', $id)->first();
            // $a = DB::table('astrologers_request')->find($id);
            // $about = json_decode($a->about, true);

            if (!$a) {
                $stepthreeValue = json_decode($a->step_three, true);
                $stepthreeValue['1']['pancard'] = $request->input('pancard');
                $stepthreeValue['1']['aadhar_number'] = $request->input('aadhar_number');
                $stepthreeValue['1']['aadharcard_image'] = $request->input('aadharcard_image');
                $stepthreeValue['1']['pancard_image'] = $request->input('pancard_image');
                $stepthreeValue['1']['qualification'] = $request->input('qualification');
                $updatedStepthreeValue = json_encode($stepthreeValue);

                DB::table('astrologers_request')
                    ->where('id', $id)
                    ->update(['step_three' => $updatedStepthreeValue]);

                return redirect()->route('admin.master.astrologer_request_list')
                    ->with('failure', 'Not found any data');
            }

            DB::commit();

            return redirect()->route('admin.master.astrologer_request_list')
                ->with('success', 'Astrologer Request List Updated Successfully !');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('admin.master.astrologer_request_list')
                ->with('failure', 'Failed to update Astrologer Request List. Please try again.');
        }
    }
}
