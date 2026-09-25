<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserModels\Booking;
use App\Models\UserInformation;
use App\Models\UserModels\UserProfile;
use App\Models\Prescription;
use App\Models\UserEstablishmentClinic;
use App\Models\UserModels\Member;
use App\Models\UserModels\MedicalRecord;
use App\Models\Treatment;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\UserModels\PackTreatment;
use App\Models\UserModels\UserSubscriptionsPack;
use Illuminate\Support\Str;
use DB;
use Illuminate\Validation\Rule;
use Hash;
use Carbon\Carbon;
use DateTimeImmutable;
use DateInterval;
use DatePeriod;
use App\Traits\SdSendSms;
use App\Models\SocialConnectView;
use App\Models\MasterSpecialsation;
use App\Models\UserPremiumAddon;
use App\Models\SubscriptionFeatureMapping;
use App\Models\Payment;
use App\Models\PremiumFeature;

class DoctorController extends Controller
{
    use SdSendSms;
    public function mystaff(Request $request, $what = '')
{
    // $data = [];  // Set default empty array for data
    // $title = 'Doctors';
    // $dr_id = $what;
    // if ($request->exp == 3) {
    //     $dataexport[] = array(
    //         'UserID',
    //         'Name',
    //         'Email',
    //         'Mobile',
    //         'Specialization',
    //         'Added Date',
    //     );
    //     $i = 1;
    //     $queryqb = User::query();
    //     $queryqb->select('users.*');
    //     $queryqb->orderBy('users.updated_at', 'DESC');
    //     $queryqb->where('status', 3);
    //     $queryqb->where('approved', 3);
    //     if (!is_null($request->name)) {
    //         $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
    //     }
    //     if (!is_null($request->start_date) || !is_null($request->end_date)) {
    //         if (!is_null($request->start_date) && !is_null($request->end_date)) {
    //             $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    //         } elseif (!is_null($request->start_date)) {
    //             $queryqb->whereDate('users.created_at', $request->start_date);
    //         } elseif (!is_null($request->end_date)) {
    //             $queryqb->whereDate('users.created_at', $request->end_date);
    //         }
    //     }

    //     $arrays = $queryqb->get();
    //     foreach ($arrays as $a) {
    //         $doctor_info = $a->UserInformationDetails;
    //         $specialisation_ids = $doctor_info && $doctor_info->specialisations
    //             ? explode('|', $doctor_info->specialisations)
    //             : [];

    //         $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
    //         $specialisations_data = $specialisations->map(function ($specialisation) {
    //             return $specialisation->name;
    //         })->toArray();
    //         $dataexport[] = [
    //             $a->id,
    //             $a->name,
    //             $a->email,
    //             $a->mobile,
    //             implode(', ', $specialisations_data),
    //             date('d-M-Y h:ia', strtotime($a->created_at))
    //         ];
    //         $i++;
    //     }
    //     $string_file = date("d-m-Y h:i:s A");
    //     header("Content-type: application/csv");
    //     header("Content-Disposition: attachment; filename=\"List" . date('Y-m-d-h:i:s') . '.csv');
    //     header("Pragma: no-cache");
    //     header("Expires: 0");

    //     $handle = fopen('php://output', 'w');

    //     foreach ($dataexport as $dataexport) {
    //         fputcsv($handle, $dataexport);
    //     }
    //     fclose($handle);
    //     exit;
    // }

    // if ($request->exp == 1) {
    //     $dataexport[] = array(
    //         'UserID',
    //         'Name',
    //         'Email',
    //         'Mobile',
    //         'Specialization',
    //         'Added Date',
    //     );
    //     $i = 1;
    //     $queryqb = User::query();
    //     $queryqb->select('users.*');
    //     $queryqb->orderBy('users.updated_at', 'DESC');
    //     $queryqb->where('status', 1);
    //     $queryqb->where('approved', 1);
    //     if (!is_null($request->name)) {
    //         $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
    //     }
    //     if (!is_null($request->start_date) || !is_null($request->end_date)) {
    //         if (!is_null($request->start_date) && !is_null($request->end_date)) {
    //             $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    //         } elseif (!is_null($request->start_date)) {
    //             $queryqb->whereDate('users.created_at', $request->start_date);
    //         } elseif (!is_null($request->end_date)) {
    //             $queryqb->whereDate('users.created_at', $request->end_date);
    //         }
    //     }

    //     $arrays = $queryqb->get();
    //     foreach ($arrays as $a) {
    //         $doctor_info = $a->UserInformationDetails;
    //         $specialisation_ids = $doctor_info && $doctor_info->specialisations
    //             ? explode('|', $doctor_info->specialisations)
    //             : [];

    //         $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
    //         $specialisations_data = $specialisations->map(function ($specialisation) {
    //             return $specialisation->name;
    //         })->toArray();
    //         $dataexport[] = [
    //             $a->id,
    //             $a->name,
    //             $a->email,
    //             $a->mobile,
    //             implode(', ', $specialisations_data),
    //             date('d-M-Y h:ia', strtotime($a->created_at))
    //         ];
    //         $i++;
    //     }
    //     $string_file = date("d-m-Y h:i:s A");
    //     header("Content-type: application/csv");
    //     header("Content-Disposition: attachment; filename=\"List" . date('Y-m-d-h:i:s') . '.csv');
    //     header("Pragma: no-cache");
    //     header("Expires: 0");

    //     $handle = fopen('php://output', 'w');

    //     foreach ($dataexport as $dataexport) {
    //         fputcsv($handle, $dataexport);
    //     }
    //     fclose($handle);
    //     exit;
    // }

    // if ($request->exp == 2) {
    //     $dataexport[] = array(
    //         'UserID',
    //         'Name',
    //         'Email',
    //         'Mobile',
    //         'Specialization',
    //         'Added Date',
    //     );
    //     $i = 1;
    //     $queryqb = User::query();
    //     $queryqb->select('users.*');
    //     $queryqb->orderBy('users.updated_at', 'DESC');
    //     $queryqb->where('status', 2);
    //     $queryqb->where('approved', 2);
    //     if (!is_null($request->name)) {
    //         $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
    //     }
    //     if (!is_null($request->start_date) || !is_null($request->end_date)) {
    //         if (!is_null($request->start_date) && !is_null($request->end_date)) {
    //             $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    //         } elseif (!is_null($request->start_date)) {
    //             $queryqb->whereDate('users.created_at', $request->start_date);
    //         } elseif (!is_null($request->end_date)) {
    //             $queryqb->whereDate('users.created_at', $request->end_date);
    //         }
    //     }

    //     $arrays = $queryqb->get();
    //     foreach ($arrays as $a) {
    //         $doctor_info = $a->UserInformationDetails;
    //         $specialisation_ids = $doctor_info && $doctor_info->specialisations
    //             ? explode('|', $doctor_info->specialisations)
    //             : [];

    //         $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
    //         $specialisations_data = $specialisations->map(function ($specialisation) {
    //             return $specialisation->name;
    //         })->toArray();
    //         $dataexport[] = [
    //             $a->id,
    //             $a->name,
    //             $a->email,
    //             $a->mobile,
    //             implode(', ', $specialisations_data),
    //             date('d-M-Y h:ia', strtotime($a->created_at))
    //         ];
    //         $i++;
    //     }
    //     $string_file = date("d-m-Y h:i:s A");
    //     header("Content-type: application/csv");
    //     header("Content-Disposition: attachment; filename=\"List" . date('Y-m-d-h:i:s') . '.csv');
    //     header("Pragma: no-cache");
    //     header("Expires: 0");

    //     $handle = fopen('php://output', 'w');

    //     foreach ($dataexport as $dataexport) {
    //         fputcsv($handle, $dataexport);
    //     }
    //     fclose($handle);
    //     exit;
    // }


    // if ($request->exp == 0) {
    //     $dataexport[] = array(
    //         'UserID',
    //         'Name',
    //         'Email',
    //         'Mobile',
    //         'Specialization',
    //         'Added Date',
    //     );
    //     $i = 1;
    //     $queryqb = User::query();
    //     $queryqb->select('users.*');
    //     $queryqb->orderBy('users.updated_at', 'DESC');
    //     $queryqb->where('status', 0);
    //     // $queryqb->where('approved', 2);
    //     if (!is_null($request->name)) {
    //         $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
    //     }
    //     if (!is_null($request->start_date) || !is_null($request->end_date)) {
    //         if (!is_null($request->start_date) && !is_null($request->end_date)) {
    //             $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    //         } elseif (!is_null($request->start_date)) {
    //             $queryqb->whereDate('users.created_at', $request->start_date);
    //         } elseif (!is_null($request->end_date)) {
    //             $queryqb->whereDate('users.created_at', $request->end_date);
    //         }
    //     }

    //     $arrays = $queryqb->get();
    //     foreach ($arrays as $a) {
    //         $doctor_info = $a->UserInformationDetails;
    //         $specialisation_ids = $doctor_info && $doctor_info->specialisations
    //             ? explode('|', $doctor_info->specialisations)
    //             : [];

    //         $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
    //         $specialisations_data = $specialisations->map(function ($specialisation) {
    //             return $specialisation->name;
    //         })->toArray();
    //         $dataexport[] = [
    //             $a->id,
    //             $a->name,
    //             $a->email,
    //             $a->mobile,
    //             implode(', ', $specialisations_data),
    //             date('d-M-Y h:ia', strtotime($a->created_at))
    //         ];
    //         $i++;
    //     }
    //     $string_file = date("d-m-Y h:i:s A");
    //     header("Content-type: application/csv");
    //     header("Content-Disposition: attachment; filename=\"List" . date('Y-m-d-h:i:s') . '.csv');
    //     header("Pragma: no-cache");
    //     header("Expires: 0");

    //     $handle = fopen('php://output', 'w');

    //     foreach ($dataexport as $dataexport) {
    //         fputcsv($handle, $dataexport);
    //     }
    //     fclose($handle);
    //     exit;
    // }

    if ($what == 3) {
        $dr_id = $what;
        $title = 'Pending Doctors';
        $data = User::orderBy('id', 'DESC')->where('status', 3)->where('approved', 3)->get();


    } elseif ($what == 1) {
        $dr_id = $what;
        $title = 'Verified Doctors';
        $data = User::orderBy('id', 'DESC')->where('status', 1)->where('approved', 1)->get();


    } elseif ($what == 2) {
        $dr_id = $what;
        $title = 'Unverified Doctors';
        $data = User::orderBy('id', 'DESC')->where('status', 2)->where('approved', 2)->get();


    }

    elseif ($what == 0) {
        $dr_id = $what;
        $title = 'Deactivated Doctors';
        $data = User::orderBy('id', 'DESC')->where('status', 0)->get();

    }

    // Make sure compact will always have $data defined
    $exporturl = 'admin.user.mystaff';
    $addbutton = 'Add Doctor';
    $importbutton = 'Upload Excel';
    $listurl = route('admin.user.mystaff');
    $addurl = route('admin.user.create_mystaff', $what);
    $importurl = route('admin.user.import_mystaff');
    $editurl = 'admin.user.edit_mystaff';
    $loginurl = 'admin.user.login_mystaff';
    $destroyurl = route('admin.user.destroy_mystaff');
    $addurl1 = route('admin.user.store_mystaff');
    $viewurl = 'admin.user.view_mystaff';

    return view('doctor.mystaff.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'exporturl', 'what', 'viewurl', 'loginurl', 'dr_id'));
}

public function destroy_mystaff(Request $request)
{
    $input = $request->all();
    $a = User::find($input['id']);

    if (!$a) {
        return redirect()->back()->with('error', 'User not found!');
    }

    $a->status = 4;
    $a->save();
    return redirect()->back()->with('success', 'Doctor deleted successfully!');
}

public function export(Request $request)
{
    $exp = $request->exp; // 0, 1, 2, 3
    $dataexport[] = [
        'UserID',
        'Name',
        'Email',
        'Mobile',
        'Specialization',
        'Added Date',
    ];

    $queryqb = User::query();
    $queryqb->select('users.*');
    $queryqb->orderBy('users.updated_at', 'DESC');

    // Apply status filter (always)
    if (!is_null($exp)) {
        $queryqb->where('status', $exp);

        // Apply approved filter only if exp != 0
        if ($exp != 0) {
            $queryqb->where('approved', $exp);
        }
    }

    // Optional search by name, email, or mobile
    if (!is_null($request->name)) {
        $queryqb->whereRaw("(
            users.name LIKE ? OR
            users.email LIKE ? OR
            users.mobile LIKE ?
        )", ["%{$request->name}%", "%{$request->name}%", "%{$request->name}%"]);
    }

    // Date filtering Check the filter Is Correct
    if (!is_null($request->start_date) || !is_null($request->end_date)) {
        if (!is_null($request->start_date) && !is_null($request->end_date)) {
            $queryqb->whereBetween('users.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        } elseif (!is_null($request->start_date)) {
            $queryqb->whereDate('users.created_at', $request->start_date);
        } elseif (!is_null($request->end_date)) {
            $queryqb->whereDate('users.created_at', $request->end_date);
        }
    }

    // Get the filtered users
    $arrays = $queryqb->get();

    foreach ($arrays as $a) {
        $doctor_info = $a->UserInformationDetails;
        $specialisation_ids = $doctor_info && $doctor_info->specialisations
            ? explode('|', $doctor_info->specialisations)
            : [];

        $specialisations = MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
        $specialisations_data = $specialisations->pluck('name')->toArray();

        $dataexport[] = [
            $a->id,
            $a->name,
            $a->email,
            $a->mobile,
            implode(', ', $specialisations_data),
            date('d-M-Y h:ia', strtotime($a->created_at)),
        ];
    }

    // File name with timestamp
    $filename = 'UserList_' . date('Y-m-d-His') . '.csv';

    // Send headers for CSV download
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Output CSV
    $handle = fopen('php://output', 'w');
    foreach ($dataexport as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
    exit;
}



public function update_status(Request $request)
{
    $id = $request->id;
    $status = $request->status;

    $user = User::find($id);
    if ($user) {
        // Ensure status is either 0, 1, or 3
        if (!in_array($status, [0, 1, 3])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value!'
            ]);
        }

        // Update the status
        $user->status = $status;
        $user->save();

        // Determine the new status text for the response
        $newStatusText = $status == 1 ? 'Active' : 'Inactive';

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!',
            'newStatus' => $status,
            'newStatusText' => $newStatusText // Optional: Send the new status text
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Record not found!'
    ]);
}


public function staff_list(Request $request,  $id = '')
    {
        $title = ' Staff List';
        $listname = 'Staff';
        $listurl = route('admin.user.mystaff');
        $addurl = route('admin.user.store_staff_list');
        $destroyurl = route('admin.user.destroy_staff_list');
        $editurl = 'admin.user.edit_staff_list';
        $data = DB::table('users')
            ->where('parent_id', $id)
            ->orderBy('id', 'DESC')
            ->get();
        $addurl1 = $data ? route('admin.user.add_staff_list', $id) : null;


        return view('doctor.mystaff.stafflist', compact('title', 'listurl', 'listname', 'addurl', 'destroyurl', 'data', 'id', 'editurl', 'addurl1'));
    }



    public function add_staff_list(Request $request, $id = '')
    {
        // dd($request->all()); die;
        $title = 'Add Staff List';
        $listname = 'Staff List';
        $listurl = route('admin.user.mystaff');
        $addurl = route('admin.user.store_staff_list');
        $destroyurl = route('admin.user.destroy_staff_list');
        $data = DB::table('users')
            ->where('parent_id', $id)
            // ->whereNotIn('status', [2])
            ->orderBy('id', 'DESC')
            ->get();

        return view('doctor.mystaff.create_stafflist', compact('title', 'listurl', 'listname', 'addurl', 'destroyurl', 'data', 'id'));
    }


    public function store_staff_list(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    $query->whereNOTIN('status', [2]);
                    // $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) {
                    $query->whereNOTIN('status', [2]);
                    // $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image_name = 'default.png';
        $a = new User();

        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_staff_' . time() . '.' . $extension;
            $filePath = request('image')->move('../content/doctor', $image_name);
        }

        $a->parent_id = $request->parent_id;
        $a->name = $request->name . ' ' . $request->lname;
        $a->last_name = $request->lname;

        $a->password = Hash::make($request->password);

        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->image = $image_name;
        $a->gender = $request->gender;
        $a->date_of_birth = $request->date_of_birth;
        $a->save();
        $check = UserInformation::where('user_id', $a->id)->first();
        if (!$check) {
            $ab = new UserInformation();
            $ab->user_id = $a->id;
            $ab->save();
            $user_information = $ab;
        } else $user_information = $check;
        $user_information->language_known = implode('|', $request->language_known);
        $user_information->save();
        return redirect()->route('admin.user.staff_list', $request->input('parent_id'))
            ->with('success', 'Staff created successfully!');
    }


    public function edit_staff_list($id)
    {
        $title = 'Edit Staff List';
        $listname = 'Staff List List';
        $booklistname = 'Staff List';
        $a = User::find($id);
        if ($a) {
            $listurl = route('admin.user.staff_list', $a->parent_id);
            $editurl = 'admin.user.update_staff_list';
            return view('doctor.mystaff.editstaff_list', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }


    public function update_staff_list(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($id) {
                    $query->whereNotIn('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($id) {
                    $query->whereNotIn('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $a = User::find($id);

        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_staff_' . time() . '.' . $extension;
            $filePath = request('image')->move('../content/doctor', $image_name);
        } else {
            $image_name = $a->image;
        }

        // $a->parent_id = $request->parent_id;
        $a->name = $request->name . ' ' . $request->lname;
        $a->last_name = $request->lname;

        if ($request->password) {
            $a->password = Hash::make($request->password);
        }

        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->gender = $request->gender;
        $a->date_of_birth = $request->date_of_birth;
        $a->image = $image_name;

        $a->save();

        // Update or create user information
        $user_information = UserInformation::where('user_id', $a->id)->first();
        if (!$user_information) {
            $user_information = new UserInformation();
            $user_information->user_id = $a->id;
        }
        $user_information->language_known = implode('|', $request->language_known);
        $user_information->save();

        return redirect()->route('admin.user.staff_list', $a->parent_id)->with('success', 'Staff updated successfully!');
    }


    public function destroy_staff_list(Request $request)
    {
        $input = $request->all();
        $user = User::find($input['id']);

        if ($user) {
            $user->status = 2;
            $user->save();
            return redirect()->route('admin.user.staff_list', $user->parent_id)
                ->with('success', 'Staff deleted successfully');
        }
    }



    public function toggleApprove($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->approved = 1;
            $user->status = 1;
            $user->save();

            return redirect()->back()->with('success', 'Doctor approved successfully!');
        }

        return redirect()->back()->with('error', 'Doctor not found!');
    }

    public function toggleReject($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->approved = 2;
            $user->status = 2;
            $user->save();

            return redirect()->back()->with('success', 'Doctor rejected successfully!');
        }

        return redirect()->back()->with('error', 'Doctor not found!');
    }


    public function packagelist()
        {
            $data = Subscription::whereNOTIN('status', [2])->orderBy('updated_at', 'ASC')->get();
            $addbutton = 'Add Subscription';
            $importbutton = 'Upload Excel';
            $title = 'Subscription';
            $listurl = route('admin.package.packagelist');
            $addurl = route('admin.package.create_package');
            $editurl = 'admin.package.edit_package';
            $destroyurl = route('admin.package.destroy_package');
            $addurl1 = route('admin.package.store_package');
            return view('admin.package.index', compact('data', 'title', 'listurl', 'addbutton', 'addurl', 'editurl', 'destroyurl', 'addurl1'));
        }



        public function edit_package($id)
        {
            $title = 'Edit Subscription';
            $listname = 'Subscription';
            $listurl = route('admin.package.packagelist');
            $editurl = 'admin.package.update_package';
            $a = Subscription::find($id);
            if ($a) {
                return view('admin.package.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
            } else {
                return redirect()->route('admin.package.packagelist')
                    ->with('failure', 'Not found any data');
            }
        }

        public function update_package(Request $request, $id)
        {
            $this->validate($request, [
                // 'name' => 'required',
                'status' => 'required',
            ]);

            $a = Subscription::find($id);
            $a->name = $request->name;
            $a->price = $request->price;
            $a->discount_price = $request->discount_price;
            $a->tax_id = $request->tax_id;
            $a->status = $request->status;
            $a->save();
            return redirect()->route('admin.package.packagelist')
                ->with('success', 'Package updated successfully');
        }


        public function destroy_package(Request $request)
        {
            $input = $request->all();
            $a = Subscription::find($input['id']);
            $a->status = 2;
            $a->save();
            return redirect()->route('admin.package.packagelist')
                ->with('success', 'Package deleted successfully');
        }



        public function subscriptionlist(Request $request)
        {
            $query = UserSubscription::with(['user', 'subscription', 'payment'])
                ->whereNotIn('status', [2]);

            // 🔍 FILTER: Doctor Name
            if ($request->filled('name')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                });
            }

            // 📅 FILTER: Start Date
            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }

            // 📅 FILTER: End Date
            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }

            $data = $query->orderBy('updated_at', 'DESC')->paginate(10);

            return view('admin.package.subscriptionlist', [
                'data'        => $data,
                'title'       => 'Subscription List',
                'listurl'     => route('admin.subscription.subscriptionlist'),
                'addurl'      => route('admin.subscription.create_subscription'),
                'editurl'     => 'admin.subscription.edit_subscription',
                'destroyurl'  => route('admin.subscription.destroy_subscription'),
            ]);
        }


        public function create_subscription(Request $request)
        {
            $title = 'Add Subscription';
            $listname = 'Subscription';
            $listurl = route('admin.subscription.subscriptionlist');
            $addurl = route('admin.subscription.store_subscription');
            $packages = Subscription::where('status', 1)->orderBy('updated_at', 'ASC')->get();
            $users = DB::table('users')->where('approved',1)->get();
            $p = PremiumFeature::where('status',1)->where('subcription_id',9)->orderBy('name','ASC')->get();
            return view('admin.package.create', compact('title', 'listurl', 'listname', 'addurl', 'packages','users','p'));
        }

        public function store_subscription(Request $request)
        {
            $request->validate([
                'user_id'          => 'required|exists:users,id',
                'subscription_id'  => 'required|exists:subscriptions,id',
                'amount'           => 'required',
                'tax_amount'       => 'required',
                'extra_amount'     => 'required',
                'premium_features' => 'array'
            ]);

            DB::beginTransaction();

            try {
                $subscription = Subscription::findOrFail($request->subscription_id);

                // Check existing subscription
                $already = UserSubscription::where('user_id', $request->user_id)
                    ->where('status', 1)
                    ->first();

                if ($already) {
                    return back()->with('failure', 'User already has an active subscription');
                }

                // PAYMENT
                $payment = new Payment();
                $payment->user_id        = $request->user_id;
                $payment->amount        = $request->amount;
                $payment->tax_amount     = $request->tax_amount;
                $payment->extra_amount   = $request->extra_amount;
                $payment->payment_status = 'completed';
                $payment->payment_method = 'admin';
                $payment->save();

                // USER SUBSCRIPTION
                $userSubscription = UserSubscription::create([
                    'user_id'        => $request->user_id,
                    'subscription_id'=> $subscription->id,
                    'start_date'     => now(),
                    'end_date'       => now()->addDays($subscription->duration_days),
                    'payment_id'     => $payment->id,
                    'status'         => 1,
                    'addedby'=>auth()->user()->id
                ]);

                // PREMIUM ADDONS (SAME AS API)
                if (!empty($request->premium_features)) {
                    $premium = SubscriptionFeatureMapping::whereIn('id', $request->premium_features)->get();

                    foreach ($premium as $feature) {
                        $h = new UserPremiumAddon();
                        $h->user_id=$request->user_id;
                        $h->subscription_id=$userSubscription->id;
                        $h->premium_feature_id=$feature->id;
                        $h->payment_id=$payment->id;
                        $h->quantity=$feature->quantity;
                        $h->price=$feature->price;
                        $h->unlimited=$feature->unlimited;
                        $h->save();

                    }
                }

                DB::commit();

                    return redirect()->route('admin.subscription.subscriptionlist')
                    ->with('success', 'Subscription assigned successfully');

            } catch (\Exception $e) {
                DB::rollback();
                return back()->with('failure', $e->getMessage());
            }
        }

        public function packageDetail($id)
        {
            $subscription = Subscription::with(['features', 'masterTax'])
                ->where('id', $id)
                ->where('status', 1)
                ->first();
            if ($id == 9) {
                $premium = DB::table('premium_features')
                ->where('status', 1)
                ->where('subcription_id', $id)
                ->get();
            } else
            $premium = [];
            

            return response()->json([
                'subscription' => $subscription,
                'premium' => $premium
            ]);
        }



        public function edit_subscription($id)
        {
            $title = 'Edit subscriptionlist';
            $listname = 'subscriptionlist';
            $listurl = route('admin.subscription.subscriptionlist');
            $editurl = 'admin.subscription.update_subscription';
            $subscription = UserSubscription::find($id);
            if ($subscription) {
                return view('admin.package.edituserpakagce', compact('subscription', 'title', 'listurl', 'listname', 'editurl'));
            } else {
                return redirect()->route('admin.subscription.subscriptionlist')
                    ->with('failure', 'Not found any data');
            }
        }

        public function update_subscription(Request $request, $id)
        {
            $this->validate($request, [
                // 'name' => 'required',
                'status' => 'required',
            ]);

            $a = UserSubscription::find($id);
            $a->end_date = $request->end_date;
            $a->status = $request->status;
            $a->save();
            return redirect()->route('admin.subscription.subscriptionlist')
                ->with('success', 'Subscriptionlist updated successfully');
        }


        public function destroy_subscription(Request $request)
        {
            $input = $request->all();
            $a = UserSubscription::find($input['id']);
            $a->status = 2;
            $a->save();
            return redirect()->route('admin.subscription.subscriptionlist')
                ->with('success', 'Subscription deleted successfully');
        }



    public function subscription_update_status(Request $request)
        {
            $id = $request->id;
            $status = $request->status;

            $user = UserSubscription::find($id);
            if ($user) {
                // Ensure status is either 0, 1, or 3
                if (!in_array($status, [0, 1, 3])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid status value!'
                    ]);
                }

                // Update the status
                $user->status = $status;
                $user->save();

                // Determine the new status text for the response
                $newStatusText = $status == 1 ? 'Active' : 'Inactive';

                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully!',
                    'newStatus' => $status,
                    'newStatusText' => $newStatusText // Optional: Send the new status text
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Record not found!'
            ]);
        }

    public function login_astrologer($id, $what = '')
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Astrologer",
                "Ip Address",
                "Status",
                "Created At"
            );
            $fetch = DB::table('astrologer_login_histories')->where('user_id', $id)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $st = "";
                if ($user->online_status == 1) {
                    $st = "Online";
                } else {
                    $st = "Offline";
                }
                $ad = DB::table('astrologers')->where('id', $user->user_id)->first();
                $name = json_decode($ad->name, true);
                $data[] = array(
                    "ID" => $i,
                    "name" => $name[1],
                    "email " => $user->ip_address,
                    "status" => $st,
                    "created_at" => $user->created_at,
                    // "created_at" => date('d M y', strtotime($user->created_at)),
                );

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Login Astrologer" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }

        $title = 'Login details';
        $listname = 'Login details';
        $listurl = route('admin.astrologer-manage.astrologer', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        // $a = Astrologer::find($id);
        $data = DB::table('astrologer_login_histories')->where('user_id', $id)->get();
        if ($data) {
            return view('admin.astrologerlogin.index', compact('data', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->route('admin.astrologer-manage.astrologer', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function mystaffdetail($id, $what = '')
    {
        $title = 'View Staf Detail';
        $listname = 'Detail';
        $listurl = route('admin.user.mystaff', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = User::where('id', $id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->with(['ratings.userProfile' => function ($query) {
            $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
        }])->withCount([
            'ratings',
            'bookings as active_bookings_count' => function ($query) {
                $query->where('status', 1);
            }
        ])
        ->withAvg('ratings', 'rating')->whereNOTIN('status',[2])->first();
        if ($a) {
            $user_profile = $a;
                $check = UserInformation::where('user_id',$id)->first();
                if (!$check) {
                    $a = new UserInformation();
                    $a->user_id = $id;
                    $a->save();
                    $user_information = $a;
                } else $user_information = $check;
            $user_clinic = $user_profile->UserClinicDetails;
            $step1Fields = ['name',  'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

            $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
            $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'experience'];
            $step4Fields = ['about'];
            $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
            $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
            $step7Fields = ['doctor_availability'];
            $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
            $step9Fields = ['theme'];
            $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;
            $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;

            $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
            $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
            $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
            $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
            $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
            $step8Completion = 100;//(count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
            $step9Completion = 100;//(count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
            // dd($user_information);
            // dd($step9Completion);
            $completionByStep = [
                'step1' => $step1Completion,
                'step2' => $step2Completion,
                'step3' => $step3Completion,
                'step4' => $step4Completion,
                'step5' => $step5Completion,
                'step6' => $step6Completion,
                'step7' => $step7Completion,
                'step8' => $step8Completion,
                'step9' => $step9Completion,
            ];
            $totalCompletion = array_sum($completionByStep);
            $totalSteps = count($completionByStep);
            $finalCompletionPercentage = $totalCompletion / $totalSteps;
            return view('doctor.mystaff.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','finalCompletionPercentage'));
        } else {
            return redirect()->route('admin.user.mystaff', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function aboutupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->about = $request->about;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function professionalupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->specialisations = implode('|', $request->specialisations);
            $user_information->services = implode('|', $request->services);
            $user_information->hospital_worked_in = $request->hospital_worked_in;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function educationupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->degree = $request->degree;
            $user_information->college_institute = $request->college_institute;
            $user_information->year_of_completion = $request->year_of_completion;
            $user_information->year_of_experience = $request->year_of_experience;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function awardupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->award_college_institute = $request->award_college_institute;
            $user_information->award_year = $request->award_year;
            $user_information->awards_name = $request->awards_name;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function counsilupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->registration_number = $request->registration_number;
            $user_information->registration_council = $request->registration_council;
            $user_information->registration_year = $request->registration_year;
            $user_information->save();

            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function timingupdate($id, Request $request)
    {
        $a = User::where('id', $id)->first();
        if ($a) {
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
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
            $user_information->doctor_availability = $finalJson;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function profileupdate(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        $a = User::where('id', $id)->first();
        if ($a) {
            $image_name = $a->image;
            if (request('image')) {
                $fileNameWithTheExtension = request('image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('image')->getClientOriginalExtension();
                $image_name = rand() . '_profile_' . time() . '.' . $extension;
                $filePath = request('image')->move('../content/doctor', $image_name);
            }


            $a->name = $request->name;
            $a->email = $request->email;
            $a->mobile = $request->mobile;
            $a->image = $image_name;
            $a->gender = $request->gender;
            $a->date_of_birth = $request->date_of_birth;
            $a->save();
            $check = UserInformation::where('user_id',$a->id)->first();
            if (!$check) {
                $a = new UserInformation();
                $a->user_id = auth()->user()->id;
                $a->save();
                $user_information = $a;
            } else $user_information = $check;
            $user_information->language_known = implode('|', $request->language_known);
            $user_information->experience = $request->experience;
            $user_information->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        }
        else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }


    public function myprofiledetail( $id)
    {
        $what = 1;
        $title = 'View Staf Detail';
        $listname = 'Detail';
        $listurl = route('admin.user.mystaff', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = User::where('id', $id)->with(['UserInformationDetails','UserClinicDetails','UserClinicDetails.galleries','UserClinicDetails.holidays'])->with(['ratings.userProfile' => function ($query) {
            $query->select('id', 'name', DB::raw("CONCAT('" . asset('content/user/') . "/', image) as image"));
        }])->withCount([
            'ratings',
            'bookings as active_bookings_count' => function ($query) {
                $query->where('status', 1);
            }
        ])
        ->withAvg('ratings', 'rating')->whereNOTIN('status',[2])->first();
        if ($a) {
            $user_profile = $a;
                $check = UserInformation::where('user_id',auth()->user()->id)->first();
                if (!$check) {
                    $a = new UserInformation();
                    $a->user_id = auth()->user()->id;
                    $a->save();
                    $user_information = $a;
                } else $user_information = $check;
            $user_clinic = $user_profile->UserClinicDetails;
            $step1Fields = ['name',  'mobile', 'email', 'gender', 'date_of_birth', 'image']; // Step 1 (User model)

            $step2Fields = ['experience', 'language_known', 'specialisations', 'services'];
            $step3Fields = ['hospital_worked_in', 'about', 'degree', 'college_institute', 'year_of_completion', 'year_of_experience'];
            $step4Fields = ['about'];
            $step5Fields = ['awards_name', 'award_college_institute', 'award_year'];
            $step6Fields = ['registration_number', 'registration_council', 'registration_year'];
            $step7Fields = ['doctor_availability'];
            $step8Fields = ['clinic_name', 'clinic_description', 'latitude', 'longitude', 'address','state','city','pincode','timings',/*'holidays','gallery'*/];
            $step9Fields = ['theme'];
            $step1Completion = (count(array_filter($step1Fields, fn($field) => !empty($user_profile->$field))) / count($step1Fields)) * 100;

            $step2Completion = (count(array_filter($step2Fields, fn($field) => !empty($user_information->$field))) / count($step2Fields)) * 100;
            $step3Completion = (count(array_filter($step3Fields, fn($field) => !empty($user_information->$field))) / count($step3Fields)) * 100;
            $step4Completion = (count(array_filter($step4Fields, fn($field) => !empty($user_information->$field))) / count($step4Fields)) * 100;
            $step5Completion = (count(array_filter($step5Fields, fn($field) => !empty($user_information->$field))) / count($step5Fields)) * 100;
            $step6Completion = (count(array_filter($step6Fields, fn($field) => !empty($user_information->$field))) / count($step6Fields)) * 100;
            $step7Completion = (count(array_filter($step7Fields, fn($field) => !empty($user_information->$field))) / count($step7Fields)) * 100;
            $step8Completion = 100;//(count(array_filter($step8Fields, fn($field) => !empty($user_clinic->$field))) / count($step8Fields)) * 100;
            $step9Completion = (count(array_filter($step9Fields, fn($field) => !empty($user_information->$field))) / count($step9Fields)) * 100;
            // dd($step2Completion);
            $completionByStep = [
                'step1' => $step1Completion,
                'step2' => $step2Completion,
                'step3' => $step3Completion,
                'step4' => $step4Completion,
                'step5' => $step5Completion,
                'step6' => $step6Completion,
                'step7' => $step7Completion,
                'step8' => $step8Completion,
                'step9' => $step9Completion,
            ];
            $totalCompletion = array_sum($completionByStep);
            $totalSteps = count($completionByStep);
            $finalCompletionPercentage = $totalCompletion / $totalSteps;
            return view('doctor.mystaff.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what','finalCompletionPercentage'));
        } else {
            return redirect()->route('admin.user.mystaff', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function addmystaff ($what = '')
    {
        $title = 'Add Doctor';
        $listname = 'Doctor';
        $listurl = route('admin.user.mystaff');
        $addurl = route('admin.user.store_staff', $what);
        return view('doctor.mystaff.create', compact('title', 'listurl', 'listname', 'addurl', 'what'));
    }



    public function import_astrologer()
    {
        $title = 'Upload Astrologer File';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $samplebutton = 'Sample File';
        $addurl = route('admin.astrologer-manage.store_import_astrologer');
        $sampleurl = asset('project/public/temp_files/astrologerImportFileSample.csv');
        return view('admin.astrologer.import', compact('title', 'listurl', 'listname', 'samplebutton', 'sampleurl', 'addurl'));
    }

    public function store_import_astrologer_old(Request $request)
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
            // print_r($line); die;
            if ($i > 0) {

                $name[1] = $line[0];
                $name[2] = $line[0];
                $name[3] = $line[0];
                $name[4] = $line[0];
                $name[5] = $line[0];
                $name[6] = $line[0];


                $d[1] = $line[0];
                $d[2] = $line[1];
                $d[3] = $line[2];
                $d[4] = $line[3];
                $d[5] = $line[4];
                $d[6] = $line[5];




                $about[1] = $line[10];
                $about[2] = $line[11];
                $about[3] = $line[12];
                $about[4] = $line[13];
                $about[5] = $line[14];
                $about[6] = $line[15];



                // dd( $ddddd);
                $a = new astrologer();
                $a->name = json_encode($name, JSON_UNESCAPED_UNICODE);
                $a->slug = $line[0];
                $a->slug = $line[0];
                $a->screen_name = json_encode($d, JSON_UNESCAPED_UNICODE);
                $a->mobile = $line[16];
                $a->experience = $line[7];

                $a->average_rating = $line[8];
                $a->image = $line[9];
                $a->status = 1;
                $a->about = json_encode($about, JSON_UNESCAPED_UNICODE);


                $a->save();


                $ddddd = trim($line[6]);

                $ff =  explode(",", $ddddd);

                if ($a) {
                    foreach ($ff as $areaa) {
                        // dd(trim($areaa));

                        $ss = trim($areaa);
                        $check = AreaOfExpertise::where('exp_name', $ss)->first();
                        if ($check) {
                            $l = new AstrologerAreaofExpertise();
                            $l->astrologer_id = $a->id;
                            $l->areaofexpertise_id = $check->id;
                            $l->status = 1;
                            $l->save();
                        }
                    }
                }
            }
            $i++;
        }
        return redirect()->route('admin.astrologer-manage.astrologer')
            ->with('success', 'master user impoted successfully ' . $log);
    }



    public function store_import_astrologer(Request $request)
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
            // print_r($line); die;
            if ($i > 0) {

                $name[1] = $line[0];
                $name[2] = $line[0];
                $name[3] = $line[0];
                $name[4] = $line[0];
                $name[5] = $line[0];
                $name[6] = $line[0];


                $screen_name[1] = $line[19];
                $screen_name[2] = $line[20];
                $screen_name[3] = $line[21];
                $screen_name[4] = $line[22];
                $screen_name[5] = $line[24];
                $screen_name[6] = $line[23];




                $about[1] = $line[25];
                $about[2] = $line[26];
                $about[3] = $line[27];
                $about[4] = $line[28];
                $about[5] = $line[30];
                $about[6] = $line[29];



                // dd( $ddddd);
                $a = new astrologer();
                $a->name = json_encode($name, JSON_UNESCAPED_UNICODE);
                $a->age = $line[1];
                $a->dob = $line[2];
                $a->gender = $line[3];
                $a->mobile = $line[4];
                $a->alternate_mobile = $line[5];
                $a->email = $line[6];
                $a->address = $line[7];
                $a->experience = $line[10];
                $a->total_consultation = $line[11];
                $a->average_rating = $line[14];

                $a->screen_name = json_encode($screen_name, JSON_UNESCAPED_UNICODE);

                $a->status = 0;
                $a->about = json_encode($about, JSON_UNESCAPED_UNICODE);


                $a->save();

                // AreaOfExpertise
                $area = trim($line[13]);
                $areaexpertise =  explode(",", $area);
                // astrologer_languages
                $languages = trim($line[12]);
                $a_languages =  explode(",", $languages);

                if ($a) {
                    // AstrologerDocumentDetai

                    $doc = new AstrologerDocumentDetail();
                    $doc->astrologer_id =    $a->id;
                    $doc->aadhar_number = $line[8];
                    $doc->pancard = $line[9];
                    $doc->bank_account_number  = $line[15];
                    $doc->bank_name  = $line[16];
                    $doc->ifsc_code  = $line[17];
                    $doc->account_holder_name  = $line[18];
                    $doc->save();


                    foreach ($a_languages as $lang) {
                        // dd(trim($areaa));
                        $lang_name = trim($lang);
                        $check_lang = MasterLangauage::where('name', $lang_name)->first();
                        if ($check_lang) {
                            $l = new AstrologerLanguage();
                            $l->astrologer_id = $a->id;
                            $l->language_id = $check_lang->id;
                            $l->status = 1;
                            $l->save();
                        }
                    }




                    foreach ($areaexpertise as $areaa) {
                        // dd(trim($areaa));
                        $ss = trim($areaa);
                        $check = AreaOfExpertise::where('exp_name', $ss)->first();
                        if ($check) {
                            $l = new AstrologerAreaofExpertise();
                            $l->astrologer_id = $a->id;
                            $l->areaofexpertise_id = $check->id;
                            $l->status = 1;
                            $l->save();
                        }
                    }
                }
            }
            $i++;
        }
        return redirect()->route('admin.astrologer-manage.astrologer')
            ->with('success', 'Astrologer impoted successfully ' . $log);
    }




    public function store_staff(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query)  {
                    $query->whereNOTIN('status', [2]);
                    // $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('users')->where(function ($query)  {
                    $query->whereNOTIN('status', [2]);
                    // $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        $image_name = 'default.png';
        $a = new User();

        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('../content/doctor', $image_name);
        }

        // $a->parent_id = auth()->user()->id;
        $a->name = $request->name.' '.$request->lname;
        $a->password = Hash::make($request->password);

        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->image = $image_name;
        $a->gender = $request->gender;
        $a->date_of_birth = $request->date_of_birth;
        $a->save();
        $check = UserInformation::where('user_id',$a->id)->first();
        if (!$check) {
            $ab = new UserInformation();
            $ab->user_id = $a->id;
            $ab->save();
            $user_information = $ab;
        } else $user_information = $check;
        $user_information->language_known = implode('|', $request->language_known);
        $user_information->save();
        return redirect()->route('admin.user.mystaffdetail', $a->id)
            ->with('success', 'Created successfully');
    }


    public function myuserlist(Request $request, $what = 1)
    {

        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Email',
                'Mobile',
                'Added Date',
            );
            $i = 1;
            $queryqb = UserProfile::query();
            $queryqb->orderBy('users.updated_at', 'DESC');
            // $queryqb->where('link_id',auth()->user()->id);
            $queryqb->whereNOTIN('users.status', [2]);
            if (!is_null($request->name)) {
                $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);


                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('users.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('users.created_at', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                $dataexport[] = [
                    $a->name,
                    $a->email,
                    $a->mobile,
                    date('d-M-Y h:ia', strtotime($a->created_at))

                ];
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"User" . date('Y-m-d-h:i:s') . '.csv');
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

        $queryqb = UserProfile::query();
        $queryqb->orderBy('users.updated_at', 'DESC');
        // $queryqb->where('link_id',auth()->user()->id);
        $queryqb->whereNOTIN('users.status', [2]);
        if (!is_null($request->name)) {
            $queryqb->whereRaw("((users.name like '%" . $request->name . "%' ) OR (users.email like '%" . $request->name . "%' ) OR (users.mobile like '%" . $request->name . "%' ))");
        }
        if (!is_null($request->mobile)) {
            $queryqb->whereRaw("((users.name like '%" . $request->mobile . "%' ) OR (users.email like '%" . $request->mobile . "%' ) OR (users.mobile like '%" . $request->mobile . "%' ))");
        }
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('users.created_at',  [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);


            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('users.created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('users.created_at', $request->end_date);
            }
        }

        $data = $queryqb->paginate(1000);


        if (!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if (!is_null($request->mobile)) {
            $data->appends(['mobile' => $request->get('mobile')]);
        }
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }
        $data->appends(request()->except('page'));
        $exporturl = 'admin.user.myuserlist';
        $addbutton = 'Add Doctor';
        $importbutton = 'Upload Excel';
        $title = 'User List';
        $listurl = route('admin.myuser.myuserlist');
        $addurl = route('admin.myuser.create_myuser', $what);
        $importurl = route('admin.user.import_mystaff');
        $editurl = 'admin.myuser.edit_patient';
        $loginurl = 'admin.user.login_mystaff';
        $destroyurl = route('admin.user.destroy_mystaff');
        $addurl1 = route('admin.user.store_mystaff');
        $viewurl = 'admin.user.view_mystaff';

        return view('doctor.user.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'importurl', 'editurl', 'destroyurl', 'exporturl', 'what', 'viewurl', 'loginurl'));
    }

    public function create_myuser  ($what = '')
    {
        $title = 'Add Paitent';
        $listname = 'Paitent';
        $listurl = route('admin.myuser.myuserlist');
        $addurl = route('admin.myuser.store_patient', $what);
        return view('doctor.user.create', compact('title', 'listurl', 'listname', 'addurl', 'what'));
    }

    public function store_patient(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('mysql2')
                        ->table('users')
                        ->where('email', $value)
                        ->whereNotIn('status', [2])
                        ->exists();

                    if ($exists) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'mobile' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('mysql2')
                        ->table('users')
                        ->where('mobile', $value)
                        ->whereNotIn('status', [2])
                        ->exists();

                    if ($exists) {
                        $fail('The mobile has already been taken.');
                    }
                },
            ],
            'gender' => 'required',
            'date_of_birth' => 'required',
        ]);

        $image_name = 'default.png';
        $a = new UserProfile();

        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/user', $image_name);
        }

        $a->link_id = auth()->user()->id;
        $a->name = $request->name;
        $a->last_name = $request->lname;
        $a->password = Hash::make(12345);

        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->link_id = $request->link_id;
        $a->image = $image_name;
        $a->gender = $request->gender;
        $a->date_of_birth = $request->date_of_birth;
        $a->save();
        return redirect()->route('admin.myuser.myuserlist', $a->id)
            ->with('success', 'Created successfully');
    }


        public function edit_patient($id)
    {
        $title = 'Edit Patient';
        $listname = 'Patient';
        $listurl = route('admin.myuser.myuserlist');
        $editurl = 'admin.myuser.update_patient';
        $a = UserProfile::find($id);
        if ($a) {
            return view('doctor.user.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.myuser.myuserlist')
                ->with('failure', 'Not found any data');
        }
    }


    public function update_patient(Request $request, $id)
    {

        $a = UserProfile::find($id);
        $a->name = $request->name;
        $a->last_name = $request->lname;
        $a->email = $request->email;
        $a->mobile = $request->mobile;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.myuser.myuserlist')
            ->with('success', 'User updated successfully');
    }

    public function paitentprofile ($id, $what = '')
    {
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = UserProfile::where('id', $id)->whereNOTIN('status',[2])->first();
        if ($a) {
            $user_profile = $a;
            return view('doctor.user.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function prescriptionView  ($id, $what = '')
    {
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = Prescription::where('id', $id)->first();
        if ($a) {
            $book = Booking::find($a->booking_id);
            $doctor = User::find($book->doctor_id);
            $clinic = UserEstablishmentClinic::where('id', $book->clinic_id)->first();

            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
            $firstSpecialisation = $specialisationNames[0] ?? '';
            $user = UserProfile::find($book->user_id);
            if (!$book) {
                return redirect()->back()
                ->with('failure', 'Not found any data');
            }
            $data = [
                'dr_name' => $doctor->name,
                'dr_email' => $doctor->email,
                'dr_mobile' => $doctor->mobile,
                'user_name' => $user->name,
                'user_gender' => $user->gender,
                'dr_spec' => $firstSpecialisation,//implode(', ', $specialisationNames),
                'clinic_address' => $clinic->address,
                'clinic_register' => $clinic->register_number,
                'booking_id' => $a->booking_id,
                'booking_created' => $a->created_at,
                'notes' => $a->notes,
                'diagnosis' => $a->diagnosis,
                'advice' => $a->advice,
                'prescription' => $a->prescription
            ];
           return view('doctor.user.prescription', [
                    'dr_name' => $doctor->name,
                    'dr_email' => $doctor->email,
                    'dr_mobile' => $doctor->mobile,
                    'user_name' => $user->name,
                    'user_gender' => $user->gender,
                    'dr_spec' => $firstSpecialisation, // or implode(', ', $specialisationNames)
                    'clinic_address' => $clinic->address,
                    'clinic_register' => $clinic->register_number,
                    'booking_id' => $a->booking_id,
                    'booking_created' => $a->created_at,
                    'notes' => $a->notes,
                    'diagnosis' => $a->diagnosis,
                    'advice' => $a->advice,
                    'prescription' => $a->prescription
                ]);

        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function dwnprescriptionView  ($id, $what = '')
    {
        $title = 'View Patient Detail';
        $listname = 'Detail';
        $listurl = route('admin.myuser.myuserlist', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = Prescription::where('id', $id)->first();
        if ($a) {
            $book = Booking::find($a->booking_id);
            $doctor = User::find($book->doctor_id);
            $clinic = UserEstablishmentClinic::where('id', $book->clinic_id)->first();

            $doctorprofile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
            $specialisationIds = explode('|', $doctorprofile->specialisations);
                $specialisationNames = \DB::table('master_specialsations')
                    ->whereIn('id', $specialisationIds)
                    ->pluck('name')
                    ->toArray();
            $firstSpecialisation = $specialisationNames[0] ?? '';
            $user = UserProfile::find($book->user_id);
            if (!$book) {
                return redirect()->back()
                ->with('failure', 'Not found any data');
            }
            $data = [
                'dr_name' => $doctor->name,
                'dr_email' => $doctor->email,
                'dr_mobile' => $doctor->mobile,
                'user_name' => $user->name,
                'user_gender' => $user->gender,
                'dr_spec' => $firstSpecialisation,//implode(', ', $specialisationNames),
                'clinic_address' => $clinic->address,
                'clinic_register' => $clinic->register_number,
                'booking_id' => $a->booking_id,
                'booking_created' => $a->created_at,
                'notes' => $a->notes,
                'diagnosis' => $a->diagnosis,
                'advice' => $a->advice,
                'prescription' => $a->prescription
            ];
           return view('doctor.user.dwnprescriptionView', [
                    'dr_name' => $doctor->name,
                    'dr_email' => $doctor->email,
                    'dr_mobile' => $doctor->mobile,
                    'user_name' => $user->name,
                    'user_gender' => $user->gender,
                    'dr_spec' => $firstSpecialisation, // or implode(', ', $specialisationNames)
                    'clinic_address' => $clinic->address,
                    'clinic_register' => $clinic->register_number,
                    'booking_id' => $a->booking_id,
                    'booking_created' => $a->created_at,
                    'notes' => $a->notes,
                    'diagnosis' => $a->diagnosis,
                    'advice' => $a->advice,
                    'prescription' => $a->prescription
                ]);

        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }
    public function update_astrologer(Request $request, $id)
    {
        // print_r("ddd"); die;
        $this->validate($request, [
            'name' => 'required',
            'screen_name' => 'required',
            'slug' => 'required',
            'email' => [
                'required',
                Rule::unique('astrologers')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique('astrologers')->where(function ($query) use ($id) {
                    $query->whereNOTIN('status', [2]);
                    $query->where('id', '!=', $id);
                })
            ],
            'gender' => 'required',
            'experience' => 'required',
            'average_rating' => 'required',
            // 'average_price' => 'required',
            // 'position' => 'required',
            'status' => 'required',
        ]);

        $a = Astrologer::find($id);
        $image_name = $a->image;
        if (request('image')) {
            $fileNameWithTheExtension = request('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('image')->getClientOriginalExtension();
            $image_name = rand() . '_profile_' . time() . '.' . $extension;
            $filePath = request('image')->move('content/astrologer/image', $image_name);
        }





        if ($request->tag_image_show == 1) {
            $tag_image_name = $a->tag_image;
            if (request('tag_image')) {
                $fileNameWithTheExtension = request('tag_image')->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('tag_image')->getClientOriginalExtension();
                $tag_image_name = rand() . '_profile_' . time() . '.' . $extension;
                $filePath = request('tag_image')->move('content/astrologer/image', $tag_image_name);
            }
        } else {
            $tag_image_name = "";
        }






        if ($request->status == 1) {
            $create_profile = 1;
        } else {
            $create_profile = 0;
        }

        $a->name = json_encode($request->name);
        $a->slug = $request->slug;
        $a->screen_name = json_encode($request->screen_name);
        $a->email = $request->email;
        $a->country_code = $request->country_code;
        $a->mobile = $request->mobile;
        $a->alternate_mobile = $request->alternate_mobile;
        $a->password = Hash::make(123456);
        $a->gender = $request->gender;
        $a->age = $request->age;
        $a->dob = $request->dob;
        $a->address = $request->address;
        $a->total_consultation = $request->total_consultation;
        // $a->free_call = $request->free_call;
        $a->about = json_encode($request->about);
        $a->image = $image_name;
        $a->tag_image = $tag_image_name;
        $a->experience = $request->experience;
        $a->tags = $request->tags;
        $a->in_homepage = $request->in_homepage;
        $a->in_house_astrologer = $request->in_house_astrologer;
        $a->status = $request->status;
        $a->create_profile = $create_profile;
        $a->position = $request->position;
        $a->recommend_status = $request->recommend_status;
        $a->free_call = $request->free_call ?? 0;
        $a->online_status = $request->online_status;
        $a->average_rating = $request->average_rating;
        // $a->average_price = $request->average_price;
        $a->is_premium = $request->is_premium;
        $a->website_link = $request->website_link;
        $a->wikipedia_link = $request->wikipedia_link;
        // $a->share_percentage = $request->share_percentage;
        // $a->gst_perct = $request->gst_perct;
        // $a->tds_perct = $request->tds_perct;
        $a->consultation = $request->consultation;
        // $a->is_fixed_percentage = $request->is_fixed_percentage;
        // $a->fixed_commission = $request->fixed_commission;
        if ($request->in_house_astrologer == 1) {
            $a->can_askquestion = 1;
        } else {
            $a->can_askquestion = 0;
        }
        $a->save();


        // AstrologerAreaofExpertise::where('astrologer_id', $a->id)->delete();

        // AstrologerAreaofExpertise::where('astrologer_id',$a->id)
        // ->update([
        //    'status' => 0
        // ]);




        // if($request->area != null){
        //     foreach ($request->area as $areaa) {
        //         $check = AstrologerAreaofExpertise::where('astrologer_id',$a->id)->where('areaofexpertise_id',$areaa)->first();
        //         if ($check) {
        //             $check->status = 1;
        //             $check->save();
        //         }
        //         else {
        //             $l = new AstrologerAreaofExpertise();
        //             $l->astrologer_id = $a->id;
        //             $l->areaofexpertise_id = $areaa;
        //             $l->status = 1;
        //             $l->save();
        //         }
        //     }
        // }
        // AstrologerLanguage::where('astrologer_id', $a->id)->delete();

        // AstrologerLanguage::where('astrologer_id',$a->id)
        // ->update([
        //    'status' => 0
        // ]);
        // if($request->lang != null){
        //     foreach ($request->lang as $llang) {
        //         $check = AstrologerLanguage::where('astrologer_id', $a->id)->where('language_id',$llang)->first();
        //         if ($check) {
        //             $check->status = 1;
        //             $check->save();
        //         }
        //         else {
        //             $ln = new AstrologerLanguage();
        //             $ln->astrologer_id = $a->id;
        //             $ln->language_id = $llang;
        //             $ln->status = 1;
        //             $ln->save();
        //         }
        //     }
        // }

        $c = AstrologerDocumentDetail::where('astrologer_id', $id)->first();
        if ($c) {
            $aadharcard_image = $c->aadharcard_image;
            $pancard_image = $c->pancard_image;
            $qualification = $c->qualification;
        } else {
            $c = new AstrologerDocumentDetail();
            $c->astrologer_id = $id;
            $aadharcard_image = 'default.png';
            $pancard_image = 'default.png';
            $qualification = 'default.png';
        }

        if (request('aadharcard_image')) {
            $fileNameWithTheExtension = request('aadharcard_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('aadharcard_image')->getClientOriginalExtension();
            $aadhaar_name = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('aadharcard_image')->move('content/astrologer/documents', $aadhaar_name);
            $aadharcard_image = $aadhaar_name;
        }


        if (request('pancard_image')) {
            $fileNameWithTheExtension = request('pancard_image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('pancard_image')->getClientOriginalExtension();
            $pan_name = 'image_' . $fileName . '_' . time() . '.' . $extension;
            $filePath = request('pancard_image')->move('content/astrologer/documents', $pan_name);
            $pancard_image = $pan_name;
        }

        if (request('qualification')) {
            $fileNameWithTheExtension = request('qualification')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
            $extension = request('qualification')->getClientOriginalExtension();
            $qualification = rand() . '_qualification_' . time() . '.' . $extension;
            $filePath = request('qualification')->move('content/astrologer/documents', $qualification);
        }
        $c->pancard = $request->pancard;
        $c->aadhar_number = $request->aadhar_number;
        $c->aadharcard_image =  $aadharcard_image;
        $c->pancard_image =  $pancard_image;
        $c->qualification = $qualification;
        $c->bank_account_number = $request->bank_account_number;
        $c->account_type = $request->account_type;
        $c->ifsc_code = $request->ifsc_code;
        $c->account_holder_name = $request->account_holder_name;
        $c->save();

        // $tkt_data = TicketList::where('user_id',$id)->first();
        // if($tkt_data)
        // {
        // $tkt_data->admin_id = auth()->user()->id;
        // $tkt_data->user_id = $id;
        // $tkt_data->type = $request->type;
        // $tkt_data->ticket_id = $request->ticket_id;
        // $tkt_data->ticket_comment = $request->ticket_comment;
        // $tkt_data->save();
        // }
        // else{
        $tkt_data = new TicketList();
        $tkt_data->admin_id = auth()->user()->id;
        $tkt_data->user_id = $id;
        $tkt_data->type = $request->type;
        $tkt_data->ticket_id = $request->ticket_id;
        $tkt_data->ticket_comment = $request->ticket_comment;
        $tkt_data->save();
        // }


        return redirect()->route('admin.astrologer-manage.astrologer', $request->what)
            ->with('success', 'Astrologer updated successfully');
    }

    public function mycalender ($id, $what = '')
    {
        $title = 'View Calender';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $userId = auth()->user();
        if ($userId->parent_id == 0) {
            $users = User::where(function ($query) use ($userId) {
                $query->where('id', $userId->id)
                    ->orWhere('parent_id', $userId->id);
            })
            ->get();
        } elseif ($userId->parent_id > 0) {
            $users = User::where('id', $userId->id)->first(); // This will return a single user
        }
        $bookings = Booking::whereIn('doctor_id', $users->pluck('id'))
        ->orderBy('schedule_date', 'desc')
        ->get();

        $events = [];
        foreach ($bookings as $booking) {
            $a = $booking;
            $name = $booking->userdetail->name;
            if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $name = $member->name;
                 }
            }
            $events[] = [
                'title' => 'Booking with ' . $name,
                'start' => $booking->schedule_date, // Ensure this is in Y-m-d format
                'url' => route('admin.appoint.myappointmentdetails', ['id' => $booking->id]), // Redirect to details page
                'backgroundColor' => '#28a745', // Custom color
                'borderColor' => '#28a745',
                'textColor' => '#ffffff'
            ];
        }
        return view('doctor.appointments.calender', compact('title', 'listurl', 'listname', 'editurl', 'what','events'));
    }

    public function mystats ($id, $what = '')
    {
        $title = 'View Stats';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $doctorId = auth()->user()->id;

        $totalBookings = Booking::count();

        $rebookCount = Booking::select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $upcomingBookings = Booking::whereIn('status', [0, 4])
            ->count();

        $pendingBookings = Booking::where('status', 0)
            ->count();
        $totalAppDownloads = UserProfile::count();

        $totalSocialConnectViews = SocialConnectView::count();
        $totalEarnings = Booking::where('status', 1)
            ->sum('total_amount');

        $totalAmount = Booking::where('refund_status', 1)
            ->get()
            ->sum(function ($booking) {
                $details = json_decode($booking->refund_details, true);
                return isset($details['amount']) ? $details['amount'] : 0;
            });

        $finalAmount = $totalAmount / 100;

        return view('doctor.appointments.mystats', compact('title', 'listurl', 'listname', 'editurl', 'what','totalBookings'
                            ,'rebookCount'
                            ,'upcomingBookings'
                            ,'pendingBookings'
                            ,'totalAppDownloads'
                            ,'totalSocialConnectViews'
                            ,'totalEarnings'
                            ,'finalAmount'));
    }

    public function complete_appoint($id)
        {
            $booking = Booking::find($id);
            if (!$booking) {
                return redirect()->back()->with('error', 'Booking not found.');
            }

            $booking->status = 1;
            $booking->save();

            return redirect()->back()->with('success', 'Booking marked as complete.');
        }


     public function cancel_appoint(Request $request,$id)
        {

        $cancel_by = 'fromadmin';
            $a =Booking::find($id);
            if ($a) {
                $a->cancel_by = $cancel_by;
                $a->cancel_other = $request->cancel_other;
                $a->status = 2;
                $a->save();
            } else {
                // Handle the error properly
                return response()->json(['error' => 'Booking not found.'], 404);
            }
        if(!empty($a->payid)) {
            $scheduleDate = $a->schedule_date;
            $scheduleTime = $a->schedule_time;
            $scheduledDateTime = Carbon::parse($scheduleDate . ' ' . $scheduleTime);
            $now = Carbon::now();
            $hoursUntilBooking = $now->diffInHours($scheduledDateTime, false);
            if ($hoursUntilBooking > 24) { 
                $key_id = 'rzp_test_SX4QzG6XiXS8EU';
                $key_secret = 'byjfz2JUkm7O2lJTlYYxh63l';

                $originalPayId = $a->payid;
                $paymentId = null;

                if (str_starts_with($originalPayId, 'pay_')) {
                    $paymentId = $originalPayId;
                } elseif (str_starts_with($originalPayId, 'order_')) {
                    $orderResponse = Http::withBasicAuth($key_id, $key_secret)
                        ->get("https://api.razorpay.com/v1/orders/{$originalPayId}/payments");

                    if ($orderResponse->successful() && isset($orderResponse['items'][0]['id'])) {
                        $paymentId = $orderResponse['items'][0]['id'];
                    }
                }
                $refundUrl = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";
                $amountInPaise = (int) ($a->total_amount * 100);
                $refundPayload = [];

                if ($amountInPaise > 0) {
                    $refundPayload['amount'] = $amountInPaise;
                }
                try {
                    $refundResponse = empty($refundPayload)
                        ? Http::withBasicAuth($key_id, $key_secret)->post($refundUrl)
                        : Http::withBasicAuth($key_id, $key_secret)->post($refundUrl, $refundPayload);

                    if ($refundResponse->successful()) {
                        $a->refund_details = $refundResponse->json();
                        $a->refund_status = 1;
                        $a->save();
                    } else {
                        $a->refund_details = $refundResponse->json();
                        $a->refund_status = 2;
                        $a->save();
                    }
                } catch (\Exception $e) {
                    $a->refund_details = $e;
                    $a->refund_status = 3;
                    $a->save();
                }
            }
        }

        return redirect()->route('admin.appoint.myappointment')
                     ->with('success', 'Booking cancelled and refund processed.');
        }

// public function cancel_appoint(Request $request)
// {
//     // Validate input
//     $request->validate([
//         'cancel_by' => 'required|string',
//         'booking_id' => 'required|exists:bookings,id',
//     ]);

//     $booking = Booking::find($request->booking_id);

//     if (empty($booking->payid)) {
//         $booking->cancel_by = $request->cancel_by;
//         $booking->cancel_other = $request->cancel_other;
//         $booking->status = 2;
//         $booking->save();

//         return redirect()->route('admin.appoint.myappointment') // Change to your actual route
//                          ->with('success', 'Booking cancelled successfully.');
//     }

//     $key_id = 'rzp_test_SX4QzG6XiXS8EU';
//     $key_secret = 'byjfz2JUkm7O2lJTlYYxh63l';

//     $originalPayId = $booking->payid;
//     $paymentId = null;

//     // Determine actual payment ID
//     if (str_starts_with($originalPayId, 'pay_')) {
//         $paymentId = $originalPayId;
//     } elseif (str_starts_with($originalPayId, 'order_')) {
//         $orderResponse = Http::withBasicAuth($key_id, $key_secret)
//             ->get("https://api.razorpay.com/v1/orders/{$originalPayId}/payments");

//         if ($orderResponse->successful() && isset($orderResponse['items'][0]['id'])) {
//             $paymentId = $orderResponse['items'][0]['id'];
//         } else {
//             return redirect()->back()->with('error', 'Unable to fetch payment from Razorpay.');
//         }
//     } else {
//         return redirect()->back()->with('error', 'Invalid payment/order ID format.');
//     }

//     // Prepare refund
//     $refundUrl = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";
//     $amountInPaise = (int) ($booking->total_amount * 100);

//     $refundPayload = [];
//     if ($amountInPaise > 0) {
//         $refundPayload['amount'] = $amountInPaise;
//     }

//     try {
//         // Refund request to Razorpay
//         $refundResponse = empty($refundPayload)
//             ? Http::withBasicAuth($key_id, $key_secret)->post($refundUrl)
//             : Http::withBasicAuth($key_id, $key_secret)->post($refundUrl, $refundPayload);

//         if ($refundResponse->successful()) {
//             // Update booking status
//             $booking->cancel_by = $request->cancel_by;
//             $booking->cancel_other = $request->cancel_other ?? null;
//             $booking->status = 2;
//             $booking->save();

//             return redirect()->route('admin.appoint.myappointment') // Update this route
//                              ->with('success', 'Booking cancelled and refund processed.');
//         } else {
//             return redirect()->back()->with('error', 'Refund failed from Razorpay.');
//         }
//     } catch (\Exception $e) {
//         return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
//     }
// }



    public function earninghistory (Request $request)
    {
        // $doctorId = auth()->user()->id;

        $query = Booking::where('status', 1)
            ->with(['userDetail' => function ($query) {
                $query->select('id', 'name');
            }])
            ->orderBy('created_at', 'desc');

        if ($request->condition === 'last10') {
            $query->limit(10);
        } elseif ($request->condition === 'lastWeek') {
            $query->whereBetween('created_at', [now()->subWeek(), now()]);
        } elseif ($request->condition === 'lastMonth') {
            $query->whereBetween('created_at', [now()->subMonth(), now()]);
        } elseif ($request->condition === 'custom' && $request->has(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $bookings = $query->get();

        $cumulativeTotal = $bookings->sum('total_amount');

        $response = [
            'status'=>true,
            'total' => $cumulativeTotal,
            'data' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'user_name' => $booking->userDetail->name ?? 'N/A',
                    'total_amount' => $booking->total_amount,
                    'created_at' => $booking->created_at,
                    'created_attime' => $booking->created_at->diffForHumans(),
                    'session_no'=>$booking->session_no,
                ];
            }),
        ];
        $title = 'Earning History';
        return view('doctor.stats.earninghistory', compact('response','title'));
    }


    public function loyalitypoints (Request $request)
    {
        // $doctorId = auth()->user()->id;

        $appointments = Booking::with('loyalitydetail','userdetail')
            ->orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->get();
        $appointmentsWithLoyality = $appointments->filter(function ($appointment) {
            return $appointment->loyalitydetail !== null;
        });
        $title = 'Patient’s Loyalty Points';
        return view('doctor.stats.loyalitypoints', compact('appointmentsWithLoyality','title'));
    }


    public function plansold (Request $request)
    {
        $response = [
            'status' => true,
            'data' => []
        ];

        // $doctorId = auth()->user()->id;

        $treatments = PackTreatment::where('user_type', 2)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();



        $dateFilter = $request->input('date_filter');

        $query = UserSubscriptionsPack::whereIn('pack_id', $treatments)
            ->selectRaw('pack_id, COUNT(user_subscriptions_pack.id) as total_sales, SUM(user_payments.amount) as total_revenue')
            ->join('user_payments', 'user_payments.id', '=', 'user_subscriptions_pack.payment_id')
            ->groupBy('pack_id');

        if (in_array($dateFilter, ['30', '60', '90', '365'])) {
            $query->where('user_subscriptions_pack.updated_at', '>=', now()->subDays($dateFilter));
        }

        $packagePerformance = $query->get();

        $packageIds = $packagePerformance->pluck('pack_id')->toArray();
        $packages = PackTreatment::whereIn('id', $packageIds)->get()->keyBy('id');

        $totalSales = 0;
        $totalRevenue = 0;

        foreach ($packagePerformance as $data) {
            if (isset($packages[$data->pack_id])) {
                $totalSales += $data->total_sales;
                $totalRevenue += $data->total_revenue;

                $response['data'][] = [
                    'pack_id'      => $data->pack_id,
                    'pack_name'    => $packages[$data->pack_id]->name,
                    'total_sales'  => $data->total_sales,
                    'total_revenue' => $data->total_revenue
                ];
            }
        }
        $title = 'Patient Plans Sold';
        return view('doctor.stats.plansold', compact('response','title'));
    }


    public function treatmentperfom (Request $request,$doctorId = 0)
    {
        // if ($doctorId == 0) {
        //     $doctorId = auth()->user()->id;
        // }
        $response = [
            'status' => true,
            'data' => []
        ];



        $startDate = '';
        $endDate = '';

        if ($request->condition == 'lastWeek') {
            $startDate = now()->subWeek()->startOfWeek();
            $endDate = now()->subWeek()->endOfWeek();
        } elseif ($request->condition == 'lastMonth') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
        } elseif ($request->start_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        }

        $query = Booking::where('status', 1)
            ->selectRaw('treatment_id, COUNT(id) as total_bookings')
            ->groupBy('treatment_id');

        if ($startDate && $endDate) {
            $query->whereBetween('schedule_date', [$startDate, $endDate]);
        }

        $treatmentBookings = $query->get();

        $treatmentIds = $treatmentBookings->pluck('treatment_id')->toArray();
        $treatments = Treatment::whereIn('id', $treatmentIds)->get()->keyBy('id');

        foreach ($treatmentBookings as $booking) {
            if (isset($treatments[$booking->treatment_id])) {
                $response['data'][] = [
                    'treatment_id'   => $booking->treatment_id,
                    'treatment_name' => $treatments[$booking->treatment_id]->treatment_name,
                    'total_bookings' => $booking->total_bookings
                ];
            }
        }
        $title = 'Treatments Performed';
        return view('doctor.stats.treatmentperfom', compact('response','title','doctorId'));
    }


    public function bestpatient (Request $request)
    {
        // $authUser = auth()->user();
        // if ($authUser->parent_id) {
        //     $doctorIds = [$authUser->id];
        // } else {
        //     $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
        //     $doctorIds[] = $authUser->id;
        // }
        if ($request->date_filter) {
            $dateLimit = now()->subDays($request->date_filter);
            $bestCustomers = Booking::where('status', 1)->whereIn('doctor_id',$doctorIds)
            ->where('updated_at', '>=', $dateLimit)
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'))->with('userdetail')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->get();
        } else
        $bestCustomers = Booking::where('status', 1)
            // ->whereIn('doctor_id',$doctorIds)
            ->select('user_id', DB::raw('SUM(total_amount) as total_spent'))->with('userdetail')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->get();
        $title = 'Best Patients';
        return view('doctor.stats.bestpatient', compact('bestCustomers','title'));
    }



    public function docactivity (Request $request)
    {
        $response = [
            'status' => true,
            'data' => [],
            'path'=>asset('content/doctor/')
        ];
        $authUser = auth()->user();
        if ($authUser->parent_id) {
            $doctorIds = [];
        } else {
            $doctorIds = User::where('parent_id', $authUser->id)->where('status',1)->pluck('id')->toArray();
        }

        if (!empty($doctorIds)) {
            array_push($doctorIds,$authUser->id);
            $dateFilter = $request->input('date_filter2');
            $startDate = '';
            $endDate = '';
            if ($request->condition == 'lastWeek') {
                $startDate = now()->subWeek()->startOfWeek();
                $endDate = now()->subWeek()->endOfWeek();
            } elseif ($request->condition == 'lastMonth') {
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();

            } elseif ($request->start_date) {
                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
            }

            $query = Booking::whereIn('doctor_id', $doctorIds)
                ->where('status', 1)
                ->selectRaw('doctor_id, COUNT(id) as total_bookings, SUM(total_amount) as total_revenue')->with('doctordetail')
                ->groupBy('doctor_id');
            if ($startDate && $endDate) {
                $query->whereBetween('schedule_date', [$startDate, $endDate]);
            }
            if (in_array($dateFilter, ['30', '60', '90', '365'])) {
                $query->where('schedule_date', '>=', now()->subDays($dateFilter));
            }
            $performance = $query->get();
            $response = [
                'status' => true,
                'data' => $performance,
                'path'=>asset('content/doctor/')
            ];
        }
        $title = 'Doctors Activity';
        return view('doctor.stats.docactivity', compact('response','title'));
    }

    public function myappointmentcond(Request $request, $condition = '')
    {
        // $userId = auth()->user();
        // $doctorId = auth()->user()->id;
        // if ($userId->parent_id == 0) {
        //     $users = User::where(function ($query) use ($userId) {
        //         $query->where('id', $userId->id)
        //             ->orWhere('parent_id', $userId->id);
        //     })
        //     ->get();
        // } elseif ($userId->parent_id > 0) {
        //     $users = User::where('id', $userId->id)->get();
        // }
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Booking for',
                'Email',
                'Mobile',
                'Type',
                'Symptoms'
                ,'Treatment'
                ,'Date & time'
                ,'Status'
                ,'Payment Status',
                'Subtotal',
                'gst',
                'partial amount',
                'total amount',
                'Created at',
            );
            $i = 1;
            $queryqb = Booking::query();
            $queryqb->orderBy('schedule_date', 'desc');
            // $queryqb->whereIn('doctor_id', $users->pluck('id'));
            if (!is_null($request->name)) {
                $queryqb->whereHas('userdetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('mobile', 'LIKE', '%' . $request->name . '%');
                })->orWhereHas('doctordetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%');
                });
            }
            if (!is_null($request->status)) {
                $queryqb->where('status', $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('schedule_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('schedule_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('schedule_date', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }



                 } else {
                     $a->member = null;


                 }
                //  $userProfile = UserProfile::find($a->user_id);
                //  $doctor = User::find($a->doctor_id);
                //  if ($doctor) {
                //      $a->doctor = $doctor;
                //      $a->user = $userProfile;
                     $treatment = DB::table('treatments')
                         ->where('id', $a->treatment_id)
                         ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                         ->first();

                     if ($treatment) {
                         $a->treatment_name = $treatment->treatment_name;
                         $a->call_before_confirmation = $treatment->call_before_confirmation;
                         $a->average_duration = $treatment->average_duration;
                     } else {
                         $a->treatment_name = null;
                         $a->call_before_confirmation = null;
                         $a->average_duration = null;
                     }
                //  }
                 $status = '';
                 if($a->status == 0) {
                    $status = 'Pending';
                 }
                 if($a->status == 1) {
                    $status = 'Completed';
                 }
                 if($a->status == 2) {
                    $status = 'Canceled';
                 }
                 if($a->status == 3) {
                    $status = 'Rejected';
                 }
                 if($a->status == 4) {
                    $status = 'Accepted';
                 }

                 if($a->status == 5) {
                    $status = 'Payment Failed';
                 }
                 if($a->status == 6) {
                    $status = 'Refunded';
                 }
                 $ps = '';
                if($a->is_paid==0) {
                    $ps = 'Not Paid';
                }
                if($a->is_paid==1) {
                    $ps = 'Fully Paid';

                }
                if($a->is_paid==2) {
                    $ps = 'Partially Paid';

                }
                if ($a->member) {
                    $dataexport[] = [
                        $a->member->name.' '.$a->member->last_name,
                        'Member',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                } else {
                    $dataexport[] = [
                        $a->user->name.' '.$a->user->last_name,
                        'Self',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                }

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Appointment" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = ucfirst($condition) .' Appointments';
        $listname = 'Detail';
        $listurl = route('admin.home');
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $dateFilter = $request->date_filter ?? 365;
        $dateLimit = '';
        if ($dateFilter) {
            $dateLimit = now()->subDays($dateFilter);
        }

        $dateFilter = $request->date_filter ?? 0;
        if ($dateFilter) {
            $dateLimit = now()->subDays($dateFilter);
        }

        if ($condition == 'earning') {
            if ($dateFilter > 0) {
                $data = Booking::orderBy('schedule_date', 'desc')
                    ->where('status', 1)
                    ->where('schedule_date', '>=', $dateLimit)
                    ->get();
            } else
            $data = Booking::orderBy('schedule_date', 'desc')
            ->where('status', 1)
            ->get();
        }
        if ($condition == 'allbooking') {
            if ($dateFilter > 0) {
                $data = Booking::orderBy('schedule_date', 'desc')
                    ->where('schedule_date', '>=', $dateLimit)
                    ->get();
            } else
            $data = Booking::orderBy('schedule_date', 'desc')
            ->get();
        }
        if ($condition == 'rebooking') {
            $subquery = Booking::selectRaw('MAX(id) as id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 1');

            if ($dateFilter > 0) {
                $data = Booking::whereIn('id', $subquery)
                ->where('schedule_date', '>=', $dateLimit)
                ->orderBy('schedule_date', 'desc')
                ->get();
            } else
            $data = Booking::whereIn('id', $subquery)
                ->orderBy('schedule_date', 'desc')
                ->get();

        }
        if ($condition == 'upcomingbooking') {
            if ($dateFilter > 0) {
                $data = Booking::whereIn('status', [0, 4])
                ->where('schedule_date', '>=', $dateLimit)
                ->orderBy('schedule_date', 'desc')
                ->get();
            } else
            $data = Booking::whereIn('status', [0, 4])
            ->orderBy('schedule_date', 'desc')
            ->get();

        }
        if ($condition == 'pendingbooking') {
            if ($dateFilter > 0) {
                $data = Booking::where('status', 0)
                    ->where('schedule_date', '>=', $dateLimit)
                    ->orderBy('schedule_date', 'desc')
                    ->get();
            } else
            $data = Booking::where('status', 0)
            ->orderBy('schedule_date', 'desc')
            ->get();
        }



        if ($data) {
            $appointmentsWithDoctor = $data->map(function ($appointment) {
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }

                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('content/user/' . $userProfile->image);
                }

                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);

                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();

                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }

                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();

                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }


                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;
                }
                return $appointment;
            });
            return view('doctor.appointments.myappointmentcond', compact('appointmentsWithDoctor', 'title', 'listurl', 'listname', 'editurl', 'data','condition'))
            ->with('i', ($request->input('page', 1) - 1) * 5);;
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function myappointment (Request $request,$id, $what = '')
    {
        $users = User::orderBy('id', 'DESC')->get();
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Booking for',
                'Email',
                'Mobile',
                'Type',
                'Symptoms'
                ,'Treatment'
                ,'Date & time'
                ,'Status'
                ,'Payment Status',
                'Subtotal',
                'gst',
                'partial amount',
                'total amount',
                'Created at',
            );
            $i = 1;
            $queryqb = Booking::query();
            $queryqb->orderBy('schedule_date', 'desc');
            $queryqb->whereIn('doctor_id', $users->pluck('id'));
            if (!is_null($request->name)) {
                $queryqb->whereHas('userdetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('mobile', 'LIKE', '%' . $request->name . '%');
                })->orWhereHas('doctordetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%');
                });
            }
            if (!is_null($request->status)) {
                $queryqb->where('status', $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('schedule_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('schedule_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('schedule_date', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }



                 } else {
                     $a->member = null;


                 }
                 $userProfile = UserProfile::find($a->user_id);
                 $doctor = User::find($a->doctor_id);
                 if ($doctor) {
                     $a->doctor = $doctor;
                     $a->user = $userProfile;
                     $treatment = DB::table('treatments')
                         ->where('id', $a->treatment_id)
                         ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                         ->first();

                     if ($treatment) {
                         $a->treatment_name = $treatment->treatment_name;
                         $a->call_before_confirmation = $treatment->call_before_confirmation;
                         $a->average_duration = $treatment->average_duration;
                     } else {
                         $a->treatment_name = null;
                         $a->call_before_confirmation = null;
                         $a->average_duration = null;
                     }
                 }
                 $status = '';
                 if($a->status == 0) {
                    $status = 'Pending';
                 }
                 if($a->status == 1) {
                    $status = 'Completed';
                 }
                 if($a->status == 2) {
                    $status = 'Canceled';
                 }
                 if($a->status == 3) {
                    $status = 'Rejected';
                 }
                 if($a->status == 4) {
                    $status = 'Accepted';
                 }

                 if($a->status == 5) {
                    $status = 'Payment Failed';
                 }
                 if($a->status == 6) {
                    $status = 'Refunded';
                 }
                 $ps = '';
                if($a->is_paid==0) {
                    $ps = 'Not Paid';
                }
                if($a->is_paid==1) {
                    $ps = 'Fully Paid';

                }
                if($a->is_paid==2) {
                    $ps = 'Partially Paid';

                }
                if ($a->member) {
                    $dataexport[] = [
                        $a->member->name.' '.$a->member->last_name,
                        'Member',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                } else {
                    $dataexport[] = [
                        $a->user->name.' '.$a->user->last_name,
                        'Self',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                }

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Appointment" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = 'View Appointments';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';

        $data = Booking::whereIn('doctor_id', $users->pluck('id'))
                        ->with(['userdetail', 'doctordetail',])
                        ->when(!is_null($request->name), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            })->orWhereHas('doctordetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            });
                        })
                        ->when(!is_null($request->email), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('email', 'LIKE', '%' . $request->email . '%');
                            });
                        })
                        ->when(!is_null($request->mobile), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('mobile', 'LIKE', '%' . $request->mobile . '%');
                            });
                        })
                        ->when(!is_null($request->status), function ($query) use ($request) {
                            $query->where('status', $request->status);
                        })
                        ->when(!is_null($request->start_date) && !is_null($request->end_date), function ($query) use ($request) {
                            $query->whereBetween('schedule_date', [$request->start_date, $request->end_date]);
                        })
                        ->orderBy('schedule_date', 'desc')
                        ->orderBy('schedule_time', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->appends($request->except('page'));
        if ($data) {
            $appointmentsWithDoctor = $data->map(function ($appointment) {
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('../project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }

                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('../content/user/' . $userProfile->image);
                }

                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('../content/doctor/' . $doctor->image);

                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();

                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }

                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();

                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }


                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;
                }
                return $appointment;
            });
            return view('doctor.appointments.list', [
                'appointmentsWithDoctor' => $data,
                'title' => $title,
                'listurl' => $listurl,
                'listname' => $listname,
                'editurl' => $editurl,
                'what' => $what,
            ]);
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function cancelmyappointment (Request $request,$id, $what = '')
    {
        $users = User::orderBy('id', 'DESC')->get();
        if ($request->exp == 'export') {
            $dataexport[] = array(
                'Name',
                'Booking for',
                'Email',
                'Mobile',
                'Type',
                'Symptoms'
                ,'Treatment'
                ,'Date & time'
                ,'Status'
                ,'Payment Status',
                'Subtotal',
                'gst',
                'partial amount',
                'total amount',
                'Created at',
            );
            $i = 1;
            $queryqb = Booking::query();
            $queryqb->orderBy('schedule_date', 'desc');
            $queryqb->whereIn('doctor_id', $users->pluck('id'));
            if (!is_null($request->name)) {
                $queryqb->whereHas('userdetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->name . '%')
                      ->orWhere('mobile', 'LIKE', '%' . $request->name . '%');
                })->orWhereHas('doctordetail', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->name . '%');
                });
            }
            if (!is_null($request->status)) {
                $queryqb->where('status', $request->status);
            }
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('schedule_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('schedule_date', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('schedule_date', $request->end_date);
                }
            }

            $arrays = $queryqb->get();
            foreach ($arrays as $a) {
                if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }



                 } else {
                     $a->member = null;


                 }
                 $userProfile = UserProfile::find($a->user_id);
                 $doctor = User::find($a->doctor_id);
                 if ($doctor) {
                     $a->doctor = $doctor;
                     $a->user = $userProfile;
                     $treatment = DB::table('treatments')
                         ->where('id', $a->treatment_id)
                         ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                         ->first();

                     if ($treatment) {
                         $a->treatment_name = $treatment->treatment_name;
                         $a->call_before_confirmation = $treatment->call_before_confirmation;
                         $a->average_duration = $treatment->average_duration;
                     } else {
                         $a->treatment_name = null;
                         $a->call_before_confirmation = null;
                         $a->average_duration = null;
                     }
                 }
                 $status = '';
                 if($a->status == 0) {
                    $status = 'Pending';
                 }
                 if($a->status == 1) {
                    $status = 'Completed';
                 }
                 if($a->status == 2) {
                    $status = 'Canceled';
                 }
                 if($a->status == 3) {
                    $status = 'Rejected';
                 }
                 if($a->status == 4) {
                    $status = 'Accepted';
                 }

                 if($a->status == 5) {
                    $status = 'Payment Failed';
                 }
                 if($a->status == 6) {
                    $status = 'Refunded';
                 }
                 $ps = '';
                if($a->is_paid==0) {
                    $ps = 'Not Paid';
                }
                if($a->is_paid==1) {
                    $ps = 'Fully Paid';

                }
                if($a->is_paid==2) {
                    $ps = 'Partially Paid';

                }
                if ($a->member) {
                    $dataexport[] = [
                        $a->member->name.' '.$a->member->last_name,
                        'Member',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                } else {
                    $dataexport[] = [
                        $a->user->name.' '.$a->user->last_name,
                        'Self',
                        $a->user->email,
                        $a->user->mobile,
                        $a->consultation_type,
                        $a->symptoms,
                        $a->treatment_name,
                        date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time)),
                        $status,
                        $ps,
                        $a->subtotal,
                        $a->gst,
                        $a->partial_amount,
                        $a->total_amount,
                        date('d-M-Y h:ia', strtotime($a->created_at))

                    ];
                }

                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"Appointment" . date('Y-m-d-h:i:s') . '.csv');
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
        $title = 'View Appointments';
        $listname = 'Detail';
        $listurl = route('admin.home', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';

        $data = Booking::whereIn('doctor_id', $users->pluck('id'))
                        ->where('status', 2)
                        ->with(['userdetail', 'doctordetail',])
                        ->when(!is_null($request->name), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            })->orWhereHas('doctordetail', function ($q) use ($request) {
                                $q->where('name', 'LIKE', '%' . $request->name . '%');
                            });
                        })
                        ->when(!is_null($request->email), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('email', 'LIKE', '%' . $request->email . '%');
                            });
                        })
                        ->when(!is_null($request->mobile), function ($query) use ($request) {
                            $query->whereHas('userdetail', function ($q) use ($request) {
                                $q->where('mobile', 'LIKE', '%' . $request->mobile . '%');
                            });
                        })
                        ->when(!is_null($request->status), function ($query) use ($request) {
                            $query->where('status', $request->status);
                        })
                        ->when(!is_null($request->u1), function ($query) use ($request) {
                            $query->where('doctor_id', $request->u1);
                        })
                        ->when(!is_null($request->start_date) && !is_null($request->end_date), function ($query) use ($request) {
                            $query->whereBetween('schedule_date', [$request->start_date, $request->end_date]);
                        })
                        ->orderBy('schedule_date', 'desc')
                        ->get();
        if ($data) {
            $appointmentsWithDoctor = $data->map(function ($appointment) {
                if (($appointment->user_id != $appointment->member_id && $appointment->member_type == 'relation') || ($appointment->user_id == $appointment->member_id && $appointment->member_type == 'relation')) {
                        $member = Member::find($appointment->member_id);
                        if ($member) {
                            // Add full URL for the member's image
                            $member->image = asset('project/public/member_images/' . $member->image);
                            $appointment->member = $member; // Add member details to the appointment
                        } else {
                            $appointment->member = null; // If no member found, set to null
                        }

                        $userMedical_xray = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('user_id', $appointment->user_id)->where('member_id', $appointment->member_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null



                    } else {
                        $appointment->member = null; // If user_id and member_id are the same, set member to null

                        $userMedical_xray = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"xray")->where('status',1)->get(); // Get all records
                        $userMedical_lab = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"lab")->where('status',1)->get(); // Get all records
                        $userMedical_prescription = MedicalRecord::where('member_id', $appointment->user_id)->where('type',"prescription")->where('status',1)->get(); // Get all records

                        // Map the medical records images to URLs (make sure it's an array)
                        $userMedical_xray->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_lab->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $userMedical_prescription->each(function ($record) {
                            $record->image = asset('project/public/medical_records/') . '/' . $record->image;
                        });
                        $appointment->userMedical_xray = $userMedical_xray; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_lab = $userMedical_lab; // If user_id and member_id are the same, set member to null
                        $appointment->userMedical_prescription = $userMedical_prescription; // If user_id and member_id are the same, set member to null


                    }

                $userProfile = UserProfile::find($appointment->user_id);
                if ($userProfile) {
                    $userProfile->image = asset('content/user/' . $userProfile->image);
                }

                $doctor = User::find($appointment->doctor_id);
                if ($doctor) {
                    $doctor->image = asset('content/doctor/' . $doctor->image);

                    // Get doctor's specializations
                    $doctorProfile = UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                    if ($doctorProfile && $doctorProfile->specialisations) {
                        $specialisationIds = explode('|', $doctorProfile->specialisations);
                        $specialisationNames = \DB::table('master_specialsations')
                            ->whereIn('id', $specialisationIds)
                            ->pluck('name')
                            ->toArray();

                        // Add specializations to the appointment
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = $specialisationNames;
                    } else {
                        // Handle case with no specializations
                        $appointment->doctor = $doctor;
                        $appointment->user = $userProfile;
                        $appointment->userMedical_xray = $userMedical_xray; // Array of medical records
                        $appointment->userMedical_lab = $userMedical_lab; // Array of medical records
                        $appointment->userMedical_prescription = $userMedical_prescription; // Array of medical records
                        $appointment->doctorprofile = [];
                    }

                    // Fetch treatment details
                    $treatment = DB::table('treatments')
                        ->where('id', $appointment->treatment_id)
                        ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                        ->first();

                    if ($treatment) {
                        $appointment->treatment_name = $treatment->treatment_name;
                        $appointment->call_before_confirmation = $treatment->call_before_confirmation;
                        $appointment->average_duration = $treatment->average_duration;
                    } else {
                        $appointment->treatment_name = null;
                        $appointment->call_before_confirmation = null;
                        $appointment->average_duration = null;
                    }


                    $startflag = 0;
                    if ($appointment->status == 4 && $appointment->consultation_type == 'online') {
                        $currenttime = time();
                        $starttime = strtotime(date('Y-m-d H:i:s',strtotime($appointment->schedule_date.' '.$appointment->schedule_time)));
                        if ($currenttime >= $starttime) {
                            $startflag = 1;
                        }
                    }
                    $appointment->startflag = $startflag;
                }
                return $appointment;
            });
            return view('doctor.appointments.cancelmyappointment', compact('appointmentsWithDoctor', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function myappointmentdetails($id, $what = '')
    {
        $title = 'View Appointment Detail';
        $listname = 'Detail';
        $listurl = route('admin.appoint.myappointment', $what);
        $editurl = 'admin.astrologer-manage.edit_astrologer';
        $a = Booking::where('id', $id)->first();
        if ($a) {

            return view('doctor.appointments.view', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function updatemyappointment($id, $what = '')
    {
        $a = Booking::where('id', $id)->first();
        if ($a) {
            $a->status = $what;
            $a->save();
            return redirect()->back()
                ->with('success', 'Update successfully!!');
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function addappointment($what = '')
    {
        $title = 'Add Appointment';
        $listname = 'Appointment';
        $listurl = route('admin.appoint.myappointment');
        $a = UserProfile::where('status', 1)->where('link_id',auth()->user()->id)->orderBy('name', 'ASC')->get();
        $t = Treatment::whereNOTIN('status', [2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();
        $c = UserEstablishmentClinic::where('user_id', auth()->user()->id)->whereNOTIN('status',[2])->get();
        $addurl = route('admin.appoint.store_appointment', $what);
        return view('doctor.appointments.create', compact('title', 'listurl', 'listname', 'addurl', 'what','a','c','t'));
    }

    public function getslots(Request $request)
    {
        $key1 = UserInformation::where('user_id', auth()->user()->id)
                ->first();
        if ($key1) {
            $request->total_minutes = 30;
            $dateslots = array();
            $slot = array();
            $apointment_date = $request->dateselect;

            $today_date = strtotime(date('Y-m-d'));
            $given_date = strtotime($apointment_date);
            $id = $key1->user_id;
            if ($given_date >= $today_date) {
                $dayOfWeek = strtolower(date('l', strtotime($apointment_date)));
                // $check_bt_ = DB::select("SELECT * FROM `bookings` WHERE `schedule_date` = '$apointment_date' AND `astrologer_id` = '$id' AND `status` IN ('0','1','6','2','7') AND `type` IN ('1','2','3','4') ORDER BY `schedule_date_time` ASC");
                $already_in_time_check = array();
                // if (count($check_bt_) > 0) {
                //     foreach ($check_bt_ as $keybt) {
                //         $time_bt = $keybt->schedule_time;
                //         $minutes = $keybt->total_minutes;

                //         $one_Cal_time = strtotime($time_bt);
                //         $cal_time = strtotime('+' . $minutes . ' minutes', strtotime($time_bt));

                //         $array_inn_ol = array("time_bt" => $one_Cal_time, "time_bt_add" => $cal_time, "minutes_bt" => $minutes);
                //         array_push($already_in_time_check, $array_inn_ol);
                //     }
                // }
                $working_time = json_decode($key1->doctor_availability, true);
                if (!empty($working_time)) {
                    $working_time = json_decode($key1->doctor_availability, true);
                    $schedule = $working_time['schedule'][$dayOfWeek] ?? [];
                    if (!empty($schedule)) {
                        if (isset($schedule['morning'])) {
                            $fistslots = explode(' - ', $schedule['morning'][0]);
                            if (isset($fistslots[0]) && isset($fistslots[1])) {
                                $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                foreach ($period as $dt) {
                                    $start = $dt->format('h:ia');
                                    $end = $dt->add($interval)->format('h:ia');
                                    $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                    if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $check = Booking::whereIN('status',['0','1','4','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>0
                                        ]);
                                    }  else {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $check = Booking::whereIN('status',['0','1','4','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>1
                                        ]);
                                    }
                                }
                            }
                        }
                        if (isset($schedule['evening'])) {
                            $fistslots = explode(' - ', $schedule['evening'][0]);
                            if (isset($fistslots[0]) && isset($fistslots[1])) {
                                $live_time_plus_minutes = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                                $interval = new DateInterval('PT'.$request->total_minutes.'M');
                                $period = new DatePeriod(new \DateTime($fistslots[0]), $interval, new \DateTime($fistslots[1]));
                                foreach ($period as $dt) {
                                    $start = $dt->format('h:ia');
                                    $end = $dt->add($interval)->format('h:ia');
                                    $time_derive_with_given_date = date('Y-m-d H:i:s', strtotime($apointment_date . ' ' . $start));
                                    if (strtotime($time_derive_with_given_date) >= strtotime($live_time_plus_minutes)) {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $check = Booking::whereIN('status',['0','1','4','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>0
                                        ]);
                                    } else {
                                        $time_derive = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $time_derive2 = date('h:ia', strtotime($apointment_date . ' ' . $start));
                                        $check = Booking::whereIN('status',['0','1','4','6','7'])->where('schedule_date',$apointment_date)->where('schedule_time',$time_derive2)->first();
                                        $is_book = $check ? 1 : 0;
                                        array_push($slot, [
                                            "start_time" => $time_derive,
                                            "end_time" => $end,
                                            "is_book" => $is_book,
                                            "timepass"=>1
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($slot)) {
                array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 1, "slot" => $slot]);
            } else {
                array_push($dateslots, ["apointment_date" => $apointment_date, "is_avail" => 0, "slot" => $slot]);
            }
            if (!empty($dateslots)) {
                $response = ['status' => true, 'dateslots' => $dateslots];
            }
        }
        return response($response, 200);
    }


    public function store_appointment(Request $request)
    {
        if ($request->user_type == 'existing_user') {
            $userid = $request->selected_user;
        } else {

            $this->validate($request, [
                'name' => 'required',
                'email' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('email', $value)
                            ->whereNotIn('status', [2])
                            ->exists();

                        if ($exists) {
                            $fail('The email has already been taken.');
                        }
                    },
                ],
                'mobile' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $exists = DB::connection('mysql2')
                            ->table('users')
                            ->where('mobile', $value)
                            ->whereNotIn('status', [2])
                            ->exists();

                        if ($exists) {
                            $fail('The mobile has already been taken.');
                        }
                    },
                ],
                'gender' => 'required',
                'date_of_birth' => 'required',
            ]);
            $image_name = 'default.png';
            $a = new UserProfile();
            $a->link_id = auth()->user()->id;
            $a->name = $request->name;
            $a->last_name = $request->lname;
            $a->password = Hash::make(12345);

            $a->email = $request->email;
            $a->mobile = $request->mobile;
            $a->gender = $request->gender;
            $a->date_of_birth = $request->date_of_birth;
            $a->save();
            $userid = $a->id;
        }
        $doctor_id = auth()->user()->id;
        list($start_time, $end_time) = explode(" - ", $request->start_time);
        $appointment = Booking::create([
            'doctor_id' => $doctor_id,
            'user_id' => $userid,
            'consultation_type' => $request->consultation_type,
            'appointment_type' => 'New Appointment  ',
            'treatment_id' => $request->treatment_id,
            'member_id' => $userid,
            'member_type' => 'self',
            'symptoms' => $request->symptoms,
            'clinic_id' => $request->clinic_id,
            'schedule_date' => $request->schedule_date,
            'schedule_time' => $start_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'payment_mode' => 'online',
            'subtotal' => 0,
            'total_amount' => $request->total_amount,
            'gst' => 0,
            'notes' => $request->notes,
            'status' => 4,
            'is_paid'=>1
        ]);
        $bdate = date("l, F j, Y g:i A", strtotime($appointment->schedule_date.' '.$appointment->schedule_time));
        $msgarray = array("title"=>"📌New Appointment Scheduled",
                          "msg"=>"Your appointment with Dr. ".$appointment->doctordetail->name." at ".$appointment->clinicdetail->clinic_name." has been successfully scheduled for ".$bdate.'.',

                         "msg2"=>"You have a new appointment with ".$appointment->userdetail->name." at ".$appointment->clinicdetail->clinic_name." on ".$bdate."."
                   );
        $this->async_to_all($msgarray,'',$appointment->id,'bookingadd');
        return redirect()->route('admin.appoint.myappointmentdetails',$appointment->id)
            ->with('success', 'Created successfully');
    }
    public function destroy_astrologer(Request $request)
    {
        $input = $request->all();
        // dd($input);die;
        $a = Astrologer::find($input['id']);
        $a->status = 2;
        $a->save();

        // $data = AstrologerAreaofExpertise::where('astrologer_id',$input['id'])->first();
        // // dd($data->id);die;
        // if($data){
        // $data->status = 2;
        // $data->save();
        // }

        // $b = AstrologerLanguage::where('astrologer_id',$input['id'])->first();
        // if($b){
        // $b->status = 2;
        // $b->save();
        // }

        // $c = AstrologerDocumentDetail::where('astrologer_id',$input['id'])->first();
        // if($c){
        // $c->status = 2;
        // $c->save();
        // }

        return redirect()->route('admin.astrologer-manage.astrologer')
            ->with('success', 'Astrologer deleted successfully');
    }

    public function destroy_astrologer_file(Request $request)
    {
        $input = $request->all();
        $a = AstrologerGallery::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }

    public function addFiles(Request $request)
    {
        if (request('gfile')) {
            for ($i = 0; $i < count($request->gfile); $i++) {
                $fileNameWithTheExtension = request('gfile')[$i]->getClientOriginalName();
                $fileName = pathinfo($fileNameWithTheExtension, PATHINFO_FILENAME);
                $extension = request('gfile')[$i]->getClientOriginalExtension();
                $image_name = rand() . '_file_' . time() . '.' . $extension;
                // if ($request->type == 'image') {
                $filePath = request('gfile')[$i]->move('content/astrologer/gallery', $image_name);
                // }
                // else {
                //     $filePath = request('gfile')[$i]->move('content/astrologer/video', $image_name);
                // }
                $l = new AstrologerGallery();
                $l->astrologer_id = $request->astrologer_id;
                $l->type = $request->type;
                $l->name = $request->name ?? '';
                $l->video_title = $request->video_title;
                $l->video_image = $image_name;
                $l->position = $i + 1;
                $l->status = 1;
                $l->save();
            }
        }
        return redirect()->back()
            ->with('success', 'Add successfully');
    }

    public function edit_astrologer_service($id, $what = '')
    {
        $title = 'Edit Astrologer Service';
        $listname = 'Astrologer Service';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.astrologer-manage.update_astrologer_service';
        $a = Astrologer::find($id);
        if ($a) {
            $s = AstrologerServiceFlag::where('astrologer_id', $a->id)->first();
            if ($s) {
            } else {
                $nn = new AstrologerServiceFlag();
                $nn->astrologer_id = $a->id;
                $nn->chat_flag = 2;
                $nn->priority_chat = 2;
                $nn->free_chat = 2;
                $nn->call_flag = 2;
                $nn->priority_call = 2;
                $nn->free_call = 2;
                $nn->video_call_flag = 2;
                $nn->priority_call_flag = 2;
                $nn->free_video = 2;
                $nn->astrochart_flag = 2;
                $nn->ask_a_question = 2;
                $nn->can_take_schedule = 2;
                $nn->can_take_live_broadcast = 2;
                $nn->save();
                $s = $nn;
            }
            $p = AstrologerPrice::where('astrologer_id', $a->id)->first();
            if ($p) {
            } else {
                $pp = new AstrologerPrice();
                $pp->astrologer_id = $a->id;
                $pp->save();
                $p = $pp;
            }


            return view('admin.astrologer.edit_services', compact('a', 'title', 'listurl', 'listname', 'editurl', 'what', 's', 'p', 'a'));
        } else {
            return redirect()->route('admin.astrologer-manage.astrologer', $what)
                ->with('failure', 'Not found any data');
        }
    }

    public function update_astrologer_service(Request $request, $id)
    {


        if ($request->take_free_call == 0) {
            $a_free_chat = 2;
            $a_free_call = 2;
            $a_free_video = 2;
        } else {
            $a_free_chat = $request->free_chat;
            $a_free_call = $request->free_call;
            $a_free_video = $request->free_video;
        }


        $a = AstrologerServiceFlag::where('astrologer_id', $request->astrologer_id)->first();
        $a->chat_flag = $request->chat_flag;
        $a->priority_chat = $request->priority_chat;
        $a->free_chat = $a_free_chat;
        $a->schedule_chat =  $request->schedule_chat;
        $a->call_flag = $request->call_flag;
        $a->priority_call = $request->priority_call;
        $a->free_call = $a_free_call;

        $a->schedule_call =  $request->schedule_call;

        $a->video_call_flag = $request->video_call_flag;
        $a->priority_call_flag = $request->priority_call_flag;
        $a->free_video = $a_free_video;

        $a->schedule_video =  $request->schedule_video;


        $a->astrochart_flag = $request->astrochart_flag;
        $a->ask_a_question = $request->ask_a_question;
        $a->can_take_schedule = $request->can_take_schedule;
        $a->can_take_live_broadcast = $request->can_take_live_broadcast;
        $a->save();
        $p = AstrologerPrice::where('astrologer_id', $request->astrologer_id)->first();
        $p->normal_chat_price = $request->normal_chat_price;
        $p->normal_chat_price_usd = $request->normal_chat_price_usd;
        $p->priority_chat_price = $request->priority_chat_price;
        $p->priority_chat_price_usd = $request->priority_chat_price_usd;
        $p->scheduled_chat_price = $request->scheduled_chat_price;
        $p->scheduled_chat_price_usd = $request->scheduled_chat_price_usd;
        $p->chat_price_commision_type = $request->chat_price_commision_type;
        $p->chat_price_commision_value = $request->chat_price_commision_value;
        $p->normal_call_price = $request->normal_call_price;
        $p->normal_call_price_usd = $request->normal_call_price_usd;
        $p->priority_call_price = $request->priority_call_price;
        $p->priority_call_price_usd = $request->priority_call_price_usd;
        $p->scheduled_call_price = $request->scheduled_call_price;
        $p->scheduled_call_price_usd = $request->scheduled_call_price_usd;
        $p->call_price_commision_type = $request->call_price_commision_type;
        $p->call_price_commision_value = $request->call_price_commision_value;
        $p->normal_video_price = $request->normal_video_price;
        $p->normal_video_price_usd = $request->normal_video_price_usd;
        $p->priority_video_price = $request->priority_video_price;
        $p->priority_video_price_usd = $request->priority_video_price_usd;
        $p->scheduled_video_price = $request->scheduled_video_price;
        $p->scheduled_video_price_usd = $request->scheduled_video_price_usd;
        $p->video_price_commision_type = $request->video_price_commision_type;
        $p->video_price_commision_value = $request->video_price_commision_value;



        // $p->astrochart_price= $request->astrochart_price;
        // $p->astrochart_commision_type= $request->astrochart_commision_type;
        // $p->astrochart_commision_value= $request->astrochart_commision_value;
        // $p->ask_a_question_price= $request->ask_a_question_price;
        // $p->ask_a_question_commision_type= $request->ask_a_question_commision_type;
        // $p->ask_a_question_commision_value= $request->ask_a_question_commision_value;
        $p->save();

        // $sum = $request->normal_chat_price+$request->normal_chat_price_usd+$request->priority_chat_price+$request->priority_chat_price_usd+$request->scheduled_chat_price+$request->scheduled_chat_price_usd+$request->chat_price_commision_type+$request->chat_price_commision_value+$request->normal_call_price+$request->normal_call_price_usd+$request->priority_call_price+$request->priority_call_price_usd+$request->scheduled_call_price+$request->scheduled_call_price_usd+$request->call_price_commision_type+$request->call_price_commision_value+$request->normal_video_price+$request->normal_video_price_usd+$request->priority_video_price+$request->priority_video_price_usd+$request->scheduled_video_price+$request->scheduled_video_price_usd;
        // $frequency = 18;
        // $average = $sum/$frequency;

        $avg = Astrologer::where('id', $request->astrologer_id)->first();
        $avg->average_price = $request->normal_chat_price;
        $avg->free_call = $request->take_free_call;


        $avg->custom_percentage = $request->custom_percentage;

        $avg->is_fixed_percentage = $request->is_fixed_percentage;
        $avg->fixed_commission = $request->fixed_commission;
        $avg->share_percentage = $request->share_percentage;
        $avg->gst_perct = $request->gst_perct;
        $avg->tds_perct = $request->tds_perct;
        $avg->astroshop_percentage = $request->astroshop_percentage;


        $avg->save();
        // $avg = Astrologer::where('id',$request->astrologer_id)->first();
        // $avg->average_price = $average;
        // $avg->save();

        $tkt_data = new TicketList();
        $tkt_data->admin_id = auth()->user()->id;
        $tkt_data->user_id = $request->astrologer_id;
        $tkt_data->type = 2;
        $tkt_data->ticket_id = $request->ticket_id;
        $tkt_data->ticket_comment = $request->ticket_comment;
        $tkt_data->save();
        return redirect()->route("admin.astrologer-manage.view_astrologer", [$request->astrologer_id, $request->what])
            ->with('success', 'Update successfully');
    }



    public function payout_astrolger(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            // 'account_number'=>'required',
            'booking_txn_id' => 'required',
            'payment_mode' => 'required',
            'id' => 'required',
            'cond' => 'required',
            // 'trxn_date'=>'required',

        ]);


        $totalamount = AstrologerTransaction::where('user_id', $request->id)->where('txn_for', '!=', 'payout')->where('type', 'credit')->where('status', 1)->sum('amount');
        $payout = AstrologerTransaction::where('user_id', $request->id)->where('txn_for', 'payout')->where('type', 'debit')->where('status', 1)->sum('amount');
        $mainamount = number_format($totalamount, 2);


        // print_r($totalamount);
        // print_r("ssasas");
        // print_r($payout);
        // die;


        $remainbalance = $totalamount - $payout;
        // $remainbalance = $mainamount - $payout;



        if ($remainbalance > 0) {

            if ($remainbalance >= (float)$request->amount) {
                $t = new AstrologerTransaction();
                $t->user_id = $request->id;
                $t->txn_name = 'payout';
                $t->booking_id  = 0;
                $t->payment_mode = $request->payment_mode;
                $t->booking_txn_id = $request->booking_txn_id;
                $t->trxn_date = $request->trxn_date;
                $t->txn_for = 'payout';
                $t->type = 'debit';
                $t->price = $request->amount;
                $t->tax_price = 0;
                $t->amount = $request->amount;
                $t->status = 1;
                $t->currency = 'INR';
                // $t->added_by = auth()->user()->id;
                // $t->account_number = $request->account_number;
                $t->save();
                return redirect()->back()
                    ->with('success', 'Payout successfully added!!');
            } else return redirect()->back()
                ->with('failure', 'You cannot perform this action!');
        } else return redirect()->back()
            ->with('failure', 'You cannot perform this action!');
    }







    public function payout(Request $request, $condition, $id)
    {

        $title = ' Astrologer Gift Manage';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.update_astrologers';
        $astrologer_ide = $id;
        $data =  DB::table('astrologer_transactions')->where('user_id', $id)->get();

        // print_r($_GET); die;

        if (isset($_GET['start_date']) && !empty($_GET['end_date'])) {

            $s_date = $_GET['start_date'];
            $e_date = $_GET['end_date'];

            $booking_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                // ->where('is_incentive', '=', "0")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('price');

            // $booking_amount = DB::table('astrologer_transactions')
            // ->where('user_id', '=', $user->id)
            // ->where('type', '=', "credit")
            // ->where('is_incentive', '=', "0")
            // ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
            // ->sum('price');


            $astrologer_comission_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');


            $astrologer_incentive_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "1")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');


            $total_pending_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');



            $total_credit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');

            $total_debit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "debit")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');





            // $total_booking = DB::table('astrologer_transactions')
            // ->where('user_id', '=',$id)
            // ->where('type', '=', "credit")
            // ->where('is_incentive', '=', "0")
            // ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
            // ->count();

            $total_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 0)
                ->where('status', '=', 2)
                ->whereBetween('start_time', [$s_date, $e_date])
                ->count();





            $total_tds_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)

                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                // ->where('type', '=', 0)
                ->sum('tds_price');

            $total_gst_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)

                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                // ->where('type', '=', 0)
                ->sum('tax_price');



            $total_free_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 1)
                ->where('status', '=', 2)
                ->whereBetween('start_time', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->count();

            $total_ask_booking = DB::table('ask_a_questions')
                ->where('astrologer_id', '=', $id)
                ->where('status', '=', 1)
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->count();

            $total_freeminutescall = Booking::select(
                'astrologer_id',
                DB::raw('SUM(total_minutes) AS total_minutes'),
                DB::raw('COUNT(*) AS booking_count'),

            )
                ->where('status', '2')
                ->where('type', '2')
                ->where('free', '1')
                ->where('astrologer_id', $id)
                ->whereBetween('start_time', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->first();
            $total_freeminuteschat = Booking::select(
                'astrologer_id',
                DB::raw('SUM(total_minutes) AS total_minutes'),
                DB::raw('COUNT(*) AS booking_count'),

            )
                ->where('status', '2')
                ->where('type', '3')
                ->where('free', '1')
                ->where('astrologer_id', $id)
                ->whereBetween('start_time', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->first();

            $total_paidminutescall = Booking::where('status', '2')
                ->where('free', '0')
                ->where('type', '2')
                ->where('astrologer_id', $id)
                ->whereBetween('start_time', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->count();

            // astrologer_comission_amount
            $eshop_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "5")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('amount');

            $eshop_tds_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "5")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('tds_price');


            $eshop_gst_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "5")
                ->whereBetween('created_at', [$s_date . ' 00:00:00', $e_date . ' 23:59:59'])
                ->sum('tax_price');
        } else {


            $eshop_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "5")
                ->sum('amount');

            $eshop_tds_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "5")
                ->sum('tds_price');


            $eshop_gst_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "5")
                ->sum('tax_price');


            $booking_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "1")
                ->sum('price');

            $astrologer_comission_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "0")
                ->where('txn_type', '=', "1")
                ->sum('amount');

            $astrologer_incentive_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                ->where('is_incentive', '=', "1")
                // ->where('txn_type', '=', "1")
                ->sum('amount');

            $total_pending_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                // ->where('txn_type', '=', "1")
                ->sum('amount');


            $total_credit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "credit")
                // ->where('txn_type', '=', "1")
                ->sum('amount');

            $total_debit_payouts = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->where('type', '=', "debit")
                ->sum('amount');




            $total_tds_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                // ->where('type ', '=', "credit")
                // ->where('type', '=', 0)
                ->where('txn_type', '=', "1")
                ->sum('tds_price');

            $total_gst_astro = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                // ->where('status', '=', 2)
                // ->where('type', '=', 0)
                ->where('txn_type', '=', "1")
                ->sum('tax_price');


            // $total_booking = DB::table('astrologer_transactions')
            // ->where('user_id', '=',$id)
            // ->count();

            $total_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 0)
                ->where('status', '=', 2)
                // ->whereBetween('created_at', [$s_date.' 00:00:00', $e_date.' 23:59:59'])
                ->count();



            $total_free_booking = DB::table('bookings')
                ->where('astrologer_id', '=', $id)
                ->where('free', '=', 1)
                ->where('status', '=', 2)
                ->count();

            $total_ask_booking = DB::table('ask_a_questions')
                ->where('astrologer_id', '=', $id)
                ->where('status', '=', 1)
                ->count();

            $total_freeminutes = Booking::select(
                'astrologer_id',
                DB::raw('SUM(total_minutes) AS total_minutes'),
                DB::raw('COUNT(*) AS booking_count'),

            )
                ->where('status', '2')
                ->where('free', '1')
                ->where('astrologer_id', $id)
                ->first();

            $total_freeminutescall = Booking::select(
                'astrologer_id',
                DB::raw('SUM(total_minutes) AS total_minutes'),
                DB::raw('COUNT(*) AS booking_count'),

            )
                ->where('status', '2')
                ->where('type', '2')
                ->where('free', '1')
                ->where('astrologer_id', $id)
                ->first();
            $total_freeminuteschat = Booking::select(
                'astrologer_id',
                DB::raw('SUM(total_minutes) AS total_minutes'),
                DB::raw('COUNT(*) AS booking_count'),

            )
                ->where('status', '2')
                ->where('type', '3')
                ->where('free', '1')
                ->where('astrologer_id', $id)
                ->first();

            $total_paidminutescall = Booking::where('status', '2')
                ->where('free', '0')
                ->where('type', '2')
                ->where('astrologer_id', $id)
                ->count();
        }





        // $data = AstrologerCustomeTime::where('user_id',$id)->whereNOTIN('status',[2])->get();
        if ($data) {
            $ab = Astrologer::find($id);
            $cond = $condition;
            return view('admin.astrologer.astrologerpayout', compact(
                'data',
                'listurl',
                'title',
                'listname',
                'total_credit_payouts',
                'total_debit_payouts',
                'editurl',
                'cond',
                'ab',
                'booking_amount',
                'total_booking',
                'astrologer_comission_amount',
                'astrologer_incentive_amount',
                'total_pending_payouts',
                'astrologer_ide',
                'total_tds_astro',
                'total_gst_astro',
                'total_free_booking',
                'total_ask_booking',
                'eshop_amount',
                'eshop_tds_amount',
                'eshop_gst_amount',
                'total_freeminutescall',
                'total_freeminuteschat',
                'total_paidminutescall'
            ));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }




    public function booking_payout_done($condition, $id, $start_date, $end_date)
    {

        // print_r($start_date);
        // echo"Ddd";
        // print_r($end_date);
        // die;
        $booking_amount = DB::table('astrologer_transactions')
            ->where('user_id', '=', $id)
            ->where('type', '=', "credit")
            ->whereBetween('created_at', [$start_date, $end_date])
            ->sum('amount');
        $total_pay =  floor($booking_amount);


        $sum_amount =   $total_pay;

        if ($sum_amount) {
            DB::table('astrologer_transactions')
                ->where('user_id', $id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->update(array('type' => 'debit'));


            $a = new Astrologerpayouts();
            $a->astrologer_id = $id;
            $a->amount = $sum_amount;
            $a->start_date = $start_date;
            $a->end_date = $end_date;
            $a->type = 1;
            $a->save();
            return redirect()->back()
                ->with('success', 'Position update successfully!');
        }
    }




    public function index_incentive_astrologer(Request $request)
    {

        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Astrolger Name",

                "Payment For",
                "Reason",
                "Type",
                "Currency",
                "TDS",
                "Price",

                "Created At"
            );

            // $queryqb = AstrologerTransaction::query();
            // $queryqb->select('astrologer_transactions.*');
            // $queryqb->orderBy('astrologer_transactions.updated_at','DESC');
            // // $queryqb->whereNOTIN('astrologers.id',[0]);
            // $queryqb->where('astrologer_transactions.status',1);
            // // if(!is_null($request->name)) {

            // //     $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            // // }
            // if(!is_null($request->start_date) || !is_null($request->end_date)) {
            //     if (!is_null($request->start_date) && !is_null($request->end_date)) {
            //         $queryqb->whereBetween('astrologers.created_at', [$request->start_date, $request->end_date]);
            //     }
            //     elseif (!is_null($request->start_date)) {
            //         $queryqb->whereDate('astrologers.created_at', $request->start_date);

            //     }
            //     elseif (!is_null($request->end_date)) {
            //         $queryqb->whereDate('astrologers.created_at', $request->end_date);
            //     }

            // }

            // $fetch = $queryqb->get();



            $queryqb = AstrologerTransaction::query();
            $queryqb->select('astrologer_transactions.*');
            $queryqb->orderBy('astrologer_transactions.updated_at', 'DESC');
            // $queryqb->whereNOTIN('astrologers.id',[0]);
            $queryqb->where('astrologer_transactions.is_incentive', 1);
            if (!is_null($request->name)) {

                // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();
                $astroname = DB::table('astrologers')->whereRaw("(name  like '%" . $request->name . "%' )")->first();
                $queryqb->where('astrologer_transactions.user_id', $astroname->id);
                // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }

            if (!is_null($request->mobile)) {

                // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();
                $astromobile = DB::table('astrologers')->where('mobile', $request->mobile)->first();
                $queryqb->where('astrologer_transactions.user_id', $astromobile->id);
                // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
            }

            // print_r()
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryqb->whereBetween('astrologer_transactions.created_at', [$request->start_date, $request->end_date]);
                } elseif (!is_null($request->start_date)) {
                    $queryqb->whereDate('astrologer_transactions.created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryqb->whereDate('astrologer_transactions.created_at', $request->end_date);
                }
            }

            $fetch = $queryqb->get();


            // dd( $fetch);
            // $fetch = DB::table('astrologer_transactions')->where('status', 1)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {

                $astroname = DB::table('astrologers')->where('id', $user->user_id)->first();


                if ($astroname) {
                    $name5 = json_decode($astroname->name, true);
                    $a_name5 = $name5[1] ?? 'Astro N';
                } else {
                    $a_name5 = "Astro N";
                }

                // echo   $a_name5."(".$astroname->mobile.")" ;
                $txn_type = "";
                if ($user->txn_type == 1) {
                    $txn_type =  "Booking";
                } elseif ($user->txn_type == 2) {
                    $txn_type =  "Free Call";
                } elseif ($user->txn_type == 3) {
                    $txn_type =  "Ask A question";
                } else {
                    $txn_type =  "Other";
                }

                $type = "";
                if ($user->type == "credit") {
                    $type =  "Add Money";
                } elseif ($user->type == 2) {
                    $type =  "Deduct Money";
                } else {
                    $type =  "Deduct Money";
                }






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
                    "name" => $a_name5,
                    "txn_type" => $txn_type,
                    "txn_for" => $user->txn_for,
                    "type" => $type,
                    "currency" => $user->currency,
                    "tds_price" => $user->tds_price,
                    "price" => $user->price,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"AstrologerTransaction" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }
        $title = "Astrologer Incentives";
        $listurl = route('admin.astrologer-manage.index_incentive_astrologer');
        $addurl = route('admin.astrologer-manage.create_incentive_astrologer');
        $addurl1 = route('admin.astrologer-manage.store_incentive_astrologer');
        $destroyurl = route('admin.astrologer-manage.approve_incentive_astrologer');
        $destroyurl1 = route('admin.astrologer-manage.approve_incentive_astrologer');


        $queryqb = AstrologerTransaction::query();
        $queryqb->select('astrologer_transactions.*');
        $queryqb->orderBy('astrologer_transactions.updated_at', 'DESC');
        // $queryqb->whereNOTIN('astrologers.id',[0]);
        $queryqb->where('astrologer_transactions.is_incentive', 1);
        if (!is_null($request->name)) {

            // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();
            $astroname = DB::table('astrologers')->whereRaw("(name  like '%" . $request->name . "%' )")->first();
            $queryqb->where('astrologer_transactions.user_id', $astroname->id);
            // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
        }

        if (!is_null($request->mobile)) {

            // $astroname = DB::table('astrologers')->whereRaw('id', $request->name )->first();
            $astromobile = DB::table('astrologers')->where('mobile', $request->mobile)->first();
            $queryqb->where('astrologer_transactions.user_id', $astromobile->id);
            // $queryqb->whereRaw("((astrologers.name like '%" . $request->name . "%' ) OR (astrologers.screen_name like '%" . $request->name . "%' ) OR (astrologers.email like '%" . $request->name . "%' ) OR (astrologers.mobile like '%" . $request->name . "%' ))");
        }


        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryqb->whereBetween('astrologer_transactions.created_at', [$request->start_date, $request->end_date]);
            } elseif (!is_null($request->start_date)) {
                $queryqb->whereDate('astrologer_transactions.created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryqb->whereDate('astrologer_transactions.created_at', $request->end_date);
            }
        }

        $data = $queryqb->get();



        // $data = DB::table('astrologer_transactions')->where('is_incentive', 1)->latest()->orderby('id', 'ASC')->get();
        // $data1 = Paginator::useBootstrap($data);
        // return response()->json($data);
        return view('admin.incentive.index', compact('title', 'listurl', 'addurl', 'addurl1', 'destroyurl', 'destroyurl1', 'data'));
        // dd($data);
    }
    public function create_incentive_astrologer()
    {
        $title = "Add Astrologer Incentives";
        $listname = "Incentives";
        $listurl = route('admin.astrologer-manage.index_incentive_astrologer');
        $addurl1 = route('admin.astrologer-manage.store_incentive_astrologer');
        $data = DB::table('astrologer_transactions')->where('status', 1)->orderby('id', 'ASC')->get();
        $astrologerdetail = Astrologer::all();
        return view('admin.incentive.create', compact('title', 'addurl1', 'data', 'listurl', 'listname', 'astrologerdetail'));
    }

    public function store_incentive_astrologer(Request $request)
    {
        // [ "required", "regex:/^(\d+|\d+(\.\d{1,2})?|(\.\d{1,2}))$/" ]
        $this->validate($request, [
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'status' => 'required',
        ]);




        $as_id =  $request->get('user_id');

        $astro_data  = Astrologer::where('id', $as_id)->first();

        $tds_amount1 = $request->price * ($astro_data->tds_perct / 100);


        $amount = $request->price - $tds_amount1;

        $data = AstrologerTransaction::create();
        $data->user_id = $request->get('user_id');
        $data->booking_id = 0;
        $data->payment_mode = "online";
        $data->booking_txn_id =  rand(1111111111, 9999999999);;
        $data->txn_name = $request->txn_name;
        $data->txn_for = $request->txn_for;
        $data->txn_type = 4;
        $data->type = $request->type;
        $data->txn_mode = "";
        $data->currency = $request->currency;
        $data->price = $request->price;
        $data->tds_price = $tds_amount1;
        $data->amount = $amount;

        // $data->amount = $request->amount;
        $data->status = 1;
        $data->is_incentive = 1;
        // $data->is_incentive = $request->is_incentive;
        $data->save();
        return redirect()->route('admin.astrologer-manage.index_incentive_astrologer',)
            ->with('success', 'Astrologer Incentive Created Successfully');
    }





    public function show_reviews(Request $request)
    {



        if (isset($_GET['export_file'])) {

            $data[] = array(
                "ID",
                "Type",
                "Astrologer Name",
                "Astrologer Mobile",
                "User id",
                "User mobile",
                "BookingID",
                "Rate",
                "Message",
                "Reply Message",
                "Register On",
                "Is show"
            );


            $queryUser = AstrologerRatingReviews::query();
            $queryUser->whereNOTIN('status', [2]);
            // $astrologer_data = Astrologer::query();
            if (!is_null($request->start_date) || !is_null($request->end_date)) {
                if (!is_null($request->start_date) && !is_null($request->end_date)) {
                    $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
                } elseif (!is_null($request->start_date)) {
                    $queryUser->whereDate('created_at', $request->start_date);
                } elseif (!is_null($request->end_date)) {
                    $queryUser->whereDate('created_at', $request->end_date);
                }
            }
            if (!is_null($request->name)) {
                $queryUser->whereRaw("(name like '%" . $request->name . "%' )");
                //$queryUser->appends(['name' => $request->name]);
            }
            if (!is_null($request->astrologer_id)) {
                // print_r($request->astrologer_id); die;
                $astro_data1 = DB::table('astrologers')
                    ->whereRaw("(name   like '%" . $request->astrologer_id . "%' )")
                    ->first();
                if ($astro_data1) {
                    $queryUser->where('astrologer_id', $astro_data1->id);
                } else {
                    $queryUser->where('astrologer_id', $request->astrologer_id);
                }
            }


            if (!is_null($request->user_id)) {
                $queryUser->where('user_id', $request->user_id);
                //$queryUser->appends(['name' => $request->name]);
            }
            if (!is_null($request->user_mobile)) {

                $user_data = DB::table('users')
                    ->where('mobile', '=', $request->user_mobile)
                    ->first();

                if ($user_data) {
                    $queryUser->where('user_id', $user_data->id);
                } else {
                    $queryUser->where('user_id', $request->mobile);
                }
            }

            if (!is_null($request->astrologer_number)) {

                $astro_data = DB::table('astrologers')
                    ->where('mobile', '=', $request->astrologer_number)
                    ->first();

                if ($astro_data) {
                    $queryUser->where('astrologer_id', $astro_data->id);
                } else {
                    $queryUser->where('astrologer_id', $request->astrologer_number);
                }
            }






            if (!is_null($request->email)) {
                $queryUser->whereRaw("(email like '%" . $request->email . "%' )");
            }

            if (!is_null($request->mobile)) {
                $queryUser->whereRaw("(phone like '%" . $request->mobile . "%' )");
            }
            if (!is_null($request->status)) {
                $queryUser->whereRaw("(status like '%" . $request->status . "%' )");
            }


            $fetch = $queryUser->get();


            // dd( $fetch);
            // $fetch = DB::table('astrologer_transactions')->where('status', 1)->paginate(500000); //status is not equal to  2
            $i = 1;
            foreach ($fetch as $user) {
                $astr = Astrologer::find($user->astrologer_id);
                $nam = json_decode($astr->name, true);
                $a_name5 = $nam[1] ?? 'Astro N';
                $a_mobile = $astr->mobile ?? '';

                $userdata =  User::find($user->user_id);

                $type = "";
                if ($user->type == "1") {
                    $type =  "Puja";
                } else {
                    $type =  "Astrologer";
                }






                $st = "";
                if ($user->is_show == 1) {
                    $st = "Show";
                } else {
                    $st = "Hide";
                }




                $data[] = array(
                    "ID" => $i,
                    "type" => $type,
                    "name" => $a_name5,
                    "a_mobile" => $a_mobile,
                    "userid" => $userdata->id,
                    "usermobile" => $userdata->mobile,
                    "booking_id " => $user->booking_id,
                    "rating" => $user->rating,
                    "reviews" => $user->reviews,
                    "astrologer_review" => $user->astrologer_review,
                    "created_at" => $user->created_at,
                    "st" => $st,

                );
                $i++;
            }
            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"AstrologerRatingReviews" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }



        $page_limit = 20;
        $queryUser = AstrologerRatingReviews::query();
        $queryUser->whereNOTIN('status', [2]);
        // $astrologer_data = Astrologer::query();
        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            if (!is_null($request->start_date) && !is_null($request->end_date)) {
                $queryUser->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            } elseif (!is_null($request->start_date)) {
                $queryUser->whereDate('created_at', $request->start_date);
            } elseif (!is_null($request->end_date)) {
                $queryUser->whereDate('created_at', $request->end_date);
            }
        }
        if (!is_null($request->name)) {
            $queryUser->whereRaw("(name like '%" . $request->name . "%' )");
            //$queryUser->appends(['name' => $request->name]);
        }
        if (!is_null($request->astrologer_id)) {
            // print_r($request->astrologer_id); die;
            $astro_data1 = DB::table('astrologers')
                ->whereRaw("(name   like '%" . $request->astrologer_id . "%' )")
                ->first();

            if ($astro_data1) {
                $queryUser->where('astrologer_id', $astro_data1->id);
            } else {
                $queryUser->where('astrologer_id', $request->astrologer_id);
            }

            //$queryUser->appends(['name' => $request->name]);
        }


        if (!is_null($request->user_id)) {
            $queryUser->where('user_id', $request->user_id);
            //$queryUser->appends(['name' => $request->name]);
        }
        if (!is_null($request->user_mobile)) {

            $user_data = DB::table('users')
                ->where('mobile', '=', $request->user_mobile)
                ->first();

            if ($user_data) {
                $queryUser->where('user_id', $user_data->id);
            } else {
                $queryUser->where('user_id', $request->mobile);
            }
        }

        if (!is_null($request->astrologer_number)) {

            $astro_data = DB::table('astrologers')
                ->where('mobile', '=', $request->astrologer_number)
                ->first();

            if ($astro_data) {
                $queryUser->where('astrologer_id', $astro_data->id);
            } else {
                $queryUser->where('astrologer_id', $request->astrologer_number);
            }
        }






        if (!is_null($request->email)) {
            $queryUser->whereRaw("(email like '%" . $request->email . "%' )");
        }

        if (!is_null($request->mobile)) {
            $queryUser->whereRaw("(phone like '%" . $request->mobile . "%' )");
        }
        if (!is_null($request->status)) {
            $queryUser->whereRaw("(status like '%" . $request->status . "%' )");
        }
        $queryUser->orderBy('id', 'DESC');
        //Fetch list of results

        $data = $queryUser->paginate($page_limit);
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        if (!is_null($request->astrologer_id)) {
            $data->appends(['astrologer_id' => $request->get('astrologer_id')]);
        }
        if (!is_null($request->user_id)) {
            $data->appends(['user_id' => $request->get('user_id')]);
        }
        if (!is_null($request->user_mobile)) {
            $data->appends(['user_mobile' => $request->get('user_mobile')]);
        }

        $title = 'Reviews List';
        $exporturl = 'admin.astrologers-reviews';
        $astrologer_list = Astrologer::where('status', 1)->get();
        $user_list = User::where('status', 1)->get();
        $addurl1 = route('admin.astrologer-manage.create_reviews');
        $editurl = 'admin.astrologer-manage.edit_reviews';
        $destroyurl = route('admin.astrologer-manage.destroy_reviews');
        $approve_url = route('admin.astrologer-manage.approveurl');
        $hide_url = route('admin.astrologer-manage.hide_url');
        return view('admin.astrologer.reviewslist', compact('title', 'data', 'exporturl', 'destroyurl', 'astrologer_list', 'addurl1', 'user_list', 'editurl', 'approve_url', 'hide_url'));
    }



    public function create_reviews(Request $request)
    {
        $input = $request->all();
        // print_r($input);
        // die;
        $this->validate($request, [
            // 'type_id' => 'required',
            // 'user_id' => 'required',
            'reviews' => 'required',
            'rating' => 'required',
            'status' => 'required',

        ]);

        $a = new AstrologerRatingReviews();
        $a->astrologer_id = $request->type_id;
        $a->reviews = $request->reviews;
        $a->rating = $request->rating;
        // $a->type = 2;
        $a->user_id = $request->id;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologers-reviews')
            ->with('success', 'Review created successfully');
    }


    public function edit_reviews($id)
    {
        $title = 'Edit Reviews';
        $listname = 'gifts';
        $listurl = "";
        $editurl = 'admin.astrologer-manage.update_reviews';
        $astrologer_list = Astrologer::where('status', 1)->get();
        $user_list = User::where('status', 1)->get();
        $a = AstrologerRatingReviews::find($id);
        if ($a) {
            return view('admin.astrologer.edit_review', compact('a', 'title', 'listurl', 'listname', 'editurl', 'astrologer_list', 'user_list'));
        } else {
            return redirect()->route('admin.astrologers-reviews')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_reviews(Request $request, $id)
    {
        $this->validate($request, [
            // 'type_id' => 'required',
            'user_id' => 'required',
            // 'message' => 'required',
            'rating' => 'required',
            'status' => 'required',
        ]);

        $a = AstrologerRatingReviews::find($id);

        // $a->type_id = $request->type_id;
        $a->user_id = $request->user_id;
        $a->reviews = $request->reviews;
        $a->astrologer_review = $request->astrologer_review;
        $a->status = $request->status;
        $a->rating = $request->rating;

        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologers-reviews')
            ->with('success', 'reviews updated successfully');
    }




    public function approveurl(Request $request)
    {

        // $a =  DB::table('astrologer_rating_reviews')->where('id',$request->id);
        // $a->is_show = 1;
        // $a->save();
        $input = $request->all();

        // print_r($input['id']); die;
        $a = AstrologerRatingReviews::find($input['id']);
        $a->is_show = 1;
        $a->save();
        return redirect()->back()
            ->with('success', 'Review approved successfully');
    }
    public function hide_url(Request $request)
    {
        $input = $request->all();
        $a = AstrologerRatingReviews::find($input['id']);
        $a->is_show = 0;
        $a->save();
        return redirect()->back()
            ->with('success', 'Review approved successfully');
    }

    public function destroy_reviews(Request $request)
    {
        $input = $request->all();
        $a = AstrologerRatingReviews::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Review deleted successfully');
    }




    public function astrologertimemanage($condition, $id)
    {
        $title = ' Astrologer Time Manage';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $editurl = 'admin.update_astrologers';
        $data = AstrologerDatewiseTimes::where('user_id', $id)->whereNOTIN('status', [2])->orderBy('date', 'DESC')->get();
        if ($data) {
            $ab = Astrologer::find($id);
            $cond = $condition;
            return view('admin.astrologer.astrologertimemanage', compact('data', 'title', 'listurl', 'listname', 'editurl', 'cond', 'ab'));
        } else {
            return redirect()->back()
                ->with('failure', 'Not found any data');
        }
    }

    public function create_custome_time($condition, $id)
    {
        $title = 'Add Astrologer Time';
        $listname = 'Astrologer';
        $listurl = route('admin.astrologer-manage.astrologer');
        $addurl = route('admin.astrologer-manage.store_custome_time', $condition);
        return view('admin.astrologer.create_custome_time', compact('title', 'listurl', 'listname', 'addurl', 'condition', 'id'));
    }

    public function store_custome_time($condition, Request $request)
    {
        $this->validate($request, [
            'datevalue' => 'required',
            'times' => 'required',
            'timesend' => 'required',
        ]);

        $period = new \DatePeriod(
            new \DateTime($request->datevalue),
            new \DateInterval('P1D'),
            new \DateTime($request->end_date)
        );
        $dates = array();
        foreach ($period as $key => $value) {
            array_push($dates, $value->format('Y-m-d'));
        }
        array_push($dates, $request->end_date);

        $check = AstrologerDatewiseTimes::where('user_id', $request->id)->where('date', $request->datevalue)->where('status', 1)->first();
        if ($check) {
            return redirect()->back()
                ->with('failure', 'Already Date time added ');
        }
        $timeslots = array();
        foreach ($request->times as $t => $values) {
            $start = date('h:ia', strtotime($values));
            $end = date('h:ia', strtotime($request->timesend[$t]));
            if ($start != null && $end != null) {
                $timeslots[] = array(
                    "start" => $start,
                    "end" => $end,
                );
            }
        }

        if (!empty($dates)) {
            foreach ($dates as $key2) {
                $a = DB::table('astrologer_datewise_times')->whereDate('date', '=', $key2)->where('user_id', $request->id)->where('status', 1)->first();
                if ($a) {
                    DB::table('astrologer_datewise_times')
                        ->where('id', $a->id)  // find your user by their email
                        ->limit(1)  // optional - to ensure only one record is updated.
                        ->update(array('json' => json_encode($request->timslots), 'updated_at' => date('Y-m-d H:i:s')));
                    $a = DB::table('astrologer_datewise_times')->whereDate('date', '=', $key2)->where('user_id', $request->astrologer_id)->where('status', 1)->first();
                    return redirect()->back()
                        ->with('failure', 'Already Date time added ');
                }

                $a = new AstrologerDatewiseTimes();
                $a->json = json_encode($timeslots);
                $a->user_id = $request->id;
                $a->date = $key2;
                $a->status = 1;
                $a->save();
            }
        }
        if ($a) {
            return redirect()->route('admin.astrologer-manage.astrologertimemanage', [$condition, $request->id])
                ->with('success', 'Astrologer created successfully');
        } else {
            return redirect()->back()
                ->with('failure', 'Something Error Happen Please try again!');
        }
    }



    public function delete_timemanage($cond, $id, $mainid)
    {
        $a = AstrologerDatewiseTimes::find($id);
        $a->status = 2;
        $a->save();
        return redirect()->back()
            ->with('success', 'Custom Time deleted successfully');
    }


    public function astrologeraoe($id)
    {
        $data = AstrologerAreaofExpertise::where('status', 1)->where('astrologer_id', $id)->get();
        $addbutton = 'Add Astrologer Area of expertise';
        $importbutton = 'Upload Excel';
        $title = 'Area of expertise';
        $listurl = route('admin.astrologer-manage.astrologeraoe', $id);
        $addurl = route('admin.astrologer-manage.create_astrologeraoe', $id);
        $importurl = route('admin.astrologer-manage.import_astrologeraoe');
        $editurl = 'admin.astrologer-manage.edit_astrologeraoe';
        $destroyurl = route('admin.astrologer-manage.destroy_astrologeraoe');
        $addurl1 = route('admin.astrologer-manage.store_astrologeraoe');
        return view('admin.astrologeraoe.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1', 'id'));
    }

    public function create_astrologeraoe($id)
    {
        $title = 'Add Area of expertise';
        $listname = 'Area of expertise';
        $listurl = route('admin.astrologer-manage.astrologeraoe', $id);
        $addurl = route('admin.astrologer-manage.store_astrologeraoe');
        return view('admin.astrologeraoe.create', compact('title', 'listurl', 'listname', 'addurl', 'id'));
    }

    public function store_astrologeraoe(Request $request)
    {
        $this->validate($request, [
            'astrologer_id' => 'required',
            'areaofexpertise_id' => 'required',
        ]);
        $a = new AstrologerAreaofExpertise();
        $a->astrologer_id = $request->astrologer_id;
        $a->areaofexpertise_id = $request->areaofexpertise_id;
        $a->status = 1;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologeraoe', $request->astrologer_id)
            ->with('success', 'Area of expertise created successfully');
    }


    public function destroy_astrologeraoe(Request $request)
    {
        $input = $request->all();
        $a = AstrologerAreaofExpertise::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologeraoe', $a->astrologer_id)
            ->with('success', 'Area of expertise  deleted successfully');
    }


    public function astrologerlanguage($id)
    {
        $data = AstrologerLanguage::where('status', 1)->where('astrologer_id', $id)->get();
        $addbutton = 'Add Astrologer Language';
        $importbutton = 'Upload Excel';
        $title = 'Language';
        $listurl = route('admin.astrologer-manage.astrologerlanguage', $id);
        $addurl = route('admin.astrologer-manage.create_astrologerlanguage', $id);
        $importurl = route('admin.astrologer-manage.import_astrologerlanguage');
        $editurl = 'admin.astrologer-manage.edit_astrologerlanguage';
        $destroyurl = route('admin.astrologer-manage.destroy_astrologerlanguage');
        $addurl1 = route('admin.astrologer-manage.store_astrologerlanguage');
        return view('admin.astrologerlanguage.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'importurl', 'editurl', 'destroyurl', 'addurl1', 'id'));
    }

    public function create_astrologerlanguage($id)
    {
        $title = 'Add Language';
        $listname = 'Language';
        $listurl = route('admin.astrologer-manage.astrologerlanguage', $id);
        $addurl = route('admin.astrologer-manage.store_astrologerlanguage');
        return view('admin.astrologerlanguage.create', compact('title', 'listurl', 'listname', 'addurl', 'id'));
    }

    public function store_astrologerlanguage(Request $request)
    {
        $this->validate($request, [
            'astrologer_id' => 'required',
            'language_id' => 'required',
        ]);
        $a = new AstrologerLanguage();
        $a->astrologer_id = $request->astrologer_id;
        $a->language_id = $request->language_id;
        $a->status = 1;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologerlanguage', $request->astrologer_id)
            ->with('success', 'Language created successfully');
    }


    public function destroy_astrologerlanguage(Request $request)
    {
        $input = $request->all();
        $a = AstrologerLanguage::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.astrologer-manage.astrologerlanguage', $a->astrologer_id)
            ->with('success', 'Language  deleted successfully');
    }

    public function testdp2(Request $request)
    {
        $data[] = array(

            "Id",
            "Name",
            "Paid Booking Count",
            "Paid Earnings [Minus PG]",
            "Free Minutes",    "Free Earnings",
            "AAQ Cout",
            "AAQ Earnings [Minus PG]",
            "Gift Count",
            "Gift Earnings [Minus PG]",
            "Incentive Earnings",
            "Selling Count",
            "Selling Earnings",
            "Total PG [Paid PG + AAQ PG + Gift PG]",
            "Total TDS [All earnings * TDS 10%]",
            "Total Earnings [All earnings w/o TDS, value needed by Govt]",
            "Total Bank Credit [All earnings - TDS]",
            "PAN",
            "Account Name",
            "Account Number",
            "Account Type",
            "IFSC Code"

        );
        $reprotdata[] = array(
            "Month",
            "Paid Consultation Count",
            "Paid Earnings",
            "Free Consultation Minutes",
            "Free earnings ",
            "AAQ Count",
            "AAQ Earnings",
            "Gift Count",
            "Gift Earnings",
            "Incentive/Referral Earnings",
            "Selling Commission",
            "Total PG",
            "Total TDS",
            "Total Earnings",
            "PAN",
            "Account Name",
            "Account Number",
            "Account Type",
            "IFSC Code"

        );
        /*16,17,26,48,55,65,74,107,122,144,165,184*/
        $fetch = DB::table('astrologers')->select('id','name')/*->whereIN('id',[16])*/->whereNOTIN('status', [3, 2])->get();
        $currentYear = now()->year;
        if ($currentYear > 2024) {
            $startMonth = 1;
        } else {
            $startMonth = 6;
        }
        $currentMonth = now()->month;
        $i = 1;
        foreach ($fetch as $user) {
            $bank_details =  DB::table('astrologer_document_details')->where('astrologer_id', $user->id)->first();
            if ($bank_details->bank_account_number ==  "SBI098989887") {
                $pan_number =  "";
                $account_name =  "";
                $account_number =  "";
                $account_type =  "";
                $ifsc_code =  "";
            } else {
                $pan_number =  $bank_details->pancard;
                $account_name =  $bank_details->account_holder_name;
                $account_number =   "'" . $bank_details->bank_account_number;
                $account_type =  $bank_details->account_type;
                $ifsc_code =  $bank_details->ifsc_code;
            }

            if (!is_null($request->s_date) && !is_null($request->e_date)) {

                $total_book = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->count();

                $booking_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "0")
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('price');

                $astrologer_comission_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('amount');
                    // ->toSql();
                // print_r( $astrologer_comission_amount); die;

                $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_price');
                    // $astrologer_comission_amount_tds = 0;


                $incentive_earning_credit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('price');


                $incentive_earning_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('price');

                $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;




                $total_tds_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', 'credit')
                    // ->where('txn_type', '=', 4)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_price');


                $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                ->where('user_id', '=', $user->id)
                ->where('type', '=', "debit")
                ->where('is_incentive', '=', "1")
                ->where('txn_type', 4)
                ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                ->sum('tds_price');




                $total_gift_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_astro');

                $total_ask_tds_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_astro');


                $total_gst_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    // ->where('status', '=', 2)
                    // ->where('type', '=', 0)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tax_price');

                $total_gift_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('gst_astro');


                $total_ask_gst_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('gst_astro');


                $total_free_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 1)
                    ->where('status', '=', 2)
                    ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->count();


                $total_paid_complete_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 0)
                    ->where('status', '=', 2)
                    ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->count();


                $total_ask_booking = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 1)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->count();

                // Free Minutes


                $total_free_minutes = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    // ->where('status', '=', 2)
                    ->where('status', '2')
                    ->where('free', '1')
                    ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('total_minutes');

                $freeearning =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('astrologer_comission_amount');

                $freeearning_tds =  DB::table('bookings')
                ->where('status', '2')
                ->where('free', '1')
                ->where('astrologer_id',  $user->id)
                ->whereBetween('start_time', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                ->sum('tds_astro');



                $aaqmoney = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('astrologer_comission_amount');

                    $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_astro');



                $gifts = DB::table('send_gifts')
                    ->where('astrologer_id', $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->count();

                $giftsmoney = DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('astrologer_comission_amount');

                    $giftsmoney_tds_astro= DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_astro');


                $sellingcount = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->count();


                $sellingearning = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('amount');


                    $sellingearning_tds_price = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereBetween('created_at', [$request->s_date . ' 00:00:00', $request->e_date . ' 23:59:59'])
                    ->sum('tds_price');

            } elseif (!is_null($request->s_date)) {


                $total_book = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->count();

                $booking_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "0")
                    ->whereDate('created_at', $request->s_date)
                    ->sum('price');

                $astrologer_comission_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->whereDate('created_at', $request->s_date)
                    ->sum('amount');


                $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_price');


                $incentive_earning_credit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('price');


                $incentive_earning_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('price');

                $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;

                // $total_credit_payouts = DB::table('astrologer_transactions')
                //     ->where('user_id', '=', $user->id)
                //     ->where('type', '=', "credit")
                //     ->whereDate('created_at', $request->s_date)
                //     ->sum('amount');

                // $total_debit_payouts = DB::table('astrologer_transactions')
                //     ->where('user_id', '=', $user->id)
                //     ->where('type', '=', "debit")
                //     ->whereDate('created_at', $request->s_date)
                //     ->sum('amount');

                $total_tds_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type ', '=', "credit")
                    // ->where('type', '=', 0)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_price');


                    $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_price');


                $total_gift_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_astro');

                $total_ask_tds_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_astro');



                $total_gst_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    // ->where('status', '=', 2)
                    // ->where('type', '=', 0)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tax_price');

                $total_gift_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('gst_astro');


                $total_ask_gst_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('gst_astro');




                $total_free_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 1)
                    ->where('status', '=', 2)
                    ->whereDate('created_at', $request->s_date)
                    ->count();


                $total_paid_complete_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 0)
                    ->where('status', '=', 2)
                    ->whereDate('start_time', $request->s_date)
                    ->count();


                $total_ask_booking = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 1)
                    ->whereDate('created_at', $request->s_date)
                    ->count();

                $total_free_minutes = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 2)
                    ->where('free', '1')
                    ->whereDate('created_at', $request->s_date)
                    ->sum('total_minutes');

                $freeearning =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('astrologer_comission_amount');

                    $freeearning_tds =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_astro');


                $aaqmoney = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('astrologer_comission_amount');


                    $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_astro');



                $gifts = DB::table('send_gifts')
                    ->where('astrologer_id', $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->count();

                $giftsmoney = DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('astrologer_comission_amount');

                    $giftsmoney_tds_astro= DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_astro');



                $sellingcount = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereDate('created_at', $request->s_date)
                    ->count();


                $sellingearning = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('amount');

                    $sellingearning_tds_price = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereDate('created_at', $request->s_date)
                    ->sum('tds_price');


            } elseif (!is_null($request->e_date)) {

                $total_book = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->count();

                $booking_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "0")
                    ->whereDate('created_at', $request->e_date)
                    ->sum('price');

                $astrologer_comission_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->whereDate('created_at', $request->e_date)
                    ->sum('amount');


                $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_price');


                // $astrologer_incentive_amount = DB::table('astrologer_transactions')
                //     ->where('user_id', '=', $user->id)
                //     ->where('type', '=', "credit")
                //     ->where('is_incentive', '=', "1")
                //     ->whereDate('created_at', $request->e_date)
                //     ->sum('amount');

                $incentive_earning_credit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('price');


                $incentive_earning_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('price');

                $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;



                // $total_credit_payouts = DB::table('astrologer_transactions')
                //     ->where('user_id', '=', $user->id)
                //     ->where('type', '=', "credit")
                //     ->whereDate('created_at', $request->e_date)
                //     ->sum('amount');

                // $total_debit_payouts = DB::table('astrologer_transactions')
                //     ->where('user_id', '=', $user->id)
                //     ->where('type', '=', "debit")
                //     ->whereDate('created_at', $request->e_date)
                //     ->sum('amount');

                $total_tds_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type ', '=', "credit")
                    // ->where('type', '=', 0)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_price');

                    $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_price');

                $total_gift_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_astro');

                $total_ask_tds_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_astro');



                $total_gst_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    // ->where('status', '=', 2)
                    // ->where('type', '=', 0)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tax_price');

                $total_gift_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('gst_astro');

                $total_ask_gst_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('gst_astro');




                $total_free_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 1)
                    ->where('status', '=', 2)
                    ->whereDate('created_at', $request->e_date)
                    ->count();


                $total_paid_complete_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 0)
                    ->where('status', '=', 2)
                    ->whereDate('start_time', $request->e_date)
                    ->count();


                $total_ask_booking = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 1)
                    ->whereDate('created_at', $request->e_date)
                    ->count();

                $total_free_minutes = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 2)
                    ->where('free', '1')
                    ->whereDate('created_at', $request->e_date)
                    ->sum('total_minutes');

                $freeearning =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('astrologer_comission_amount');

                    $freeearning_tds =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_astro');


                $aaqmoney = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('astrologer_comission_amount');

                    $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_astro');



                $gifts = DB::table('send_gifts')
                    ->where('astrologer_id', $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->count();

                $giftsmoney = DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('astrologer_comission_amount');

                    $giftsmoney_tds_astro= DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_astro');

                $sellingcount = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereDate('created_at', $request->e_date)
                    ->count();


                $sellingearning = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('amount');

                    $sellingearning_tds_price = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->whereDate('created_at', $request->e_date)
                    ->sum('tds_price');


            } else {
                $total_book = DB::table('astrologer_transactions')
                        ->where('user_id', '=', $user->id)
                        ->count();
                $booking_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "0")
                    ->sum('price');
                $astrologer_comission_amount = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->sum('amount');
                $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('txn_type', '=', "1")
                    ->where('is_incentive', '=', "0")
                    ->sum('tds_price');
                $incentive_earning_credit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    // ->whereDate('created_at', $request->e_date)
                    ->sum('price');


                $incentive_earning_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    // ->whereDate('created_at', $request->e_date)
                    ->sum('price');

                $astrologer_incentive_amount = $incentive_earning_credit - $incentive_earning_debit;
                $total_credit_payouts = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    ->sum('amount');
                $total_debit_payouts = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->sum('amount');
                $total_tds_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "credit")
                    // ->where('type', '=', 0)
                    ->sum('tds_price');
                $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    // ->whereDate('created_at', $request->e_date)
                    ->sum('tds_price');

                $total_gift_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)

                    ->sum('tds_astro');

                $total_ask_tds_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)

                    ->sum('tds_astro');

                $total_gst_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $user->id)
                    // ->where('status', '=', 2)
                    // ->where('type', '=', 0)
                    ->sum('tax_price');


                $total_gift_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $user->id)
                    ->sum('gst_astro');

                $total_ask_gst_astro = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->sum('gst_astro');
                $total_free_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 1)
                    ->where('status', '=', 2)
                    ->count();


                $total_paid_complete_booking = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('free', '=', 0)
                    ->where('status', '=', 2)
                    ->count();


                $total_ask_booking = DB::table('ask_a_questions')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 1)
                    ->count();

                $total_free_minutes = DB::table('bookings')
                    ->where('astrologer_id', '=', $user->id)
                    ->where('status', '=', 2)
                    ->where('free', '1')
                    ->sum('total_minutes');

                $freeearning =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)

                    ->sum('astrologer_comission_amount');


                $freeearning_tds =  DB::table('bookings')
                    ->where('status', '2')
                    ->where('free', '1')
                    ->where('astrologer_id',  $user->id)
                    ->sum('tds_astro');


                $aaqmoney = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->sum('astrologer_comission_amount');

                $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $user->id)
                    ->sum('tds_astro');




                $gifts = DB::table('send_gifts')
                    ->where('astrologer_id', $user->id)
                    ->count();

                $giftsmoney = DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    ->sum('astrologer_comission_amount');

                    $giftsmoney_tds_astro= DB::table('send_gifts')
                    ->where('astrologer_id',  $user->id)
                    // ->whereDate('created_at', $request->e_date)
                    ->sum('tds_astro');


                $sellingcount = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->count();


                $sellingearning = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->sum('amount');


                    $sellingearning_tds_price = DB::table('astrologer_transactions')
                    ->where('user_id', $user->id)
                    ->where('txn_type', 5)
                    ->sum('tds_price');

            }
            $total_free_earning =     $freeearning + $freeearning_tds ;
            $total_earnings_all =   $sellingearning_tds_price + $sellingearning +  $astrologer_incentive_amount + $giftsmoney + $giftsmoney_tds_astro + $aaqmoney + $aaqmoney_tds + $astrologer_comission_amount + $astrologer_comission_amount_tds;
            // dd($aaqmoney);
            $total_tds_all =   $total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit ;
            $total_bank_credit =  $total_earnings_all -  $total_tds_all;
            $nm = json_decode($user->name, true);
            $data[] = array(
                "ID" => $i,
                "name" => $nm[1] ?? '',
                "total_paid_complete_booking" => $total_paid_complete_booking,
                "astrologer_comission_amount" =>  sprintf( '%0.2f',$astrologer_comission_amount + $astrologer_comission_amount_tds -  $total_free_earning),
                "total_free_minutes" =>  $total_free_minutes,
                "free_earnings" =>   sprintf('%0.2f',$total_free_earning),
                "total_ask_booking" => $total_ask_booking,
                "aaq_earnings" => sprintf('%0.2f',$aaqmoney + $aaqmoney_tds),
                "gift_count" =>  $gifts,
                "gift_earnings" =>   sprintf('%0.2f',$giftsmoney + $giftsmoney_tds_astro),
                "incentive_earnings " => sprintf('%0.2f', $astrologer_incentive_amount),
                "selling_count" => $sellingcount,
                "selling_earnings" =>  $sellingearning + $sellingearning_tds_price,
                "total_pg" =>  sprintf('%0.2f', $total_gst_astro + $total_gift_gst_astro),
                "total_tds" => sprintf('%0.2f', $total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit),
                "total_earnings" =>     sprintf('%0.2f', $total_earnings_all),
                "total_bank_credit" =>  sprintf('%0.2f', $total_bank_credit),
                "pan_number" =>  $pan_number,
                "account_name" => $account_name,
                "account_number" => $account_number,
                "account_type" => $account_type,
                "ifsc_code" => $ifsc_code,
                // "created_at" => date('d M y', strtotime($user->created_at)),
            );
            $id = $user->id;
            for ($month = $startMonth; $month <= $currentMonth; $month++) {
                $monthName = Carbon::createFromDate($currentYear, $month, 1)->format('M-y');
                $paid= Booking::whereYear('created_at', $currentYear)
                    ->whereMonth('start_time', $month)
                    ->where('status', '2')
                    ->where('free', '0')
                    ->where('astrologer_id', $id)
                    ->count();
                $paidamount= Booking::whereYear('start_time', $currentYear)
                    ->whereMonth('start_time', $month)
                    ->where('status', '2')
                    ->where('free', '0')
                    ->where('astrologer_id', $id)
                    ->sum('astrologer_comission_amount');

                $paidamount_tds_astro = Booking::whereYear('start_time', $currentYear)
                ->whereMonth('start_time', $month)
                ->where('status', '2')
                ->where('free', '0')
                ->where('astrologer_id', $id)
                ->sum('tds_astro');

                $paidamount_gst_astro = Booking::whereYear('start_time', $currentYear)
                ->whereMonth('start_time', $month)
                ->where('status', '2')
                ->where('free', '0')
                ->where('astrologer_id', $id)
                ->sum('gst_astro');

                $paidamount_tds_gst  = $paidamount_tds_astro + $paidamount_gst_astro;


                $astrologer_comission_amount = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->where('type', '=', "credit")
                ->where('txn_type', '=', "1")
                ->where('is_incentive', '=', "0")
                ->sum('amount');

                $astrologer_comission_amount_tds = DB::table('astrologer_transactions')
                ->where('user_id', '=', $id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->where('type', '=', "credit")
                ->where('txn_type', '=', "1")
                ->where('is_incentive', '=', "0")
                ->sum('tds_price');



                $freeearning1 =  DB::table('bookings')
                ->where('status', '2')
                ->where('free', '1')
                ->where('astrologer_id', '=', $id)
                ->whereYear('start_time', $currentYear)
                ->whereMonth('start_time', $month)

                ->sum('astrologer_comission_amount');


                $freeearning_tds =  DB::table('bookings')
                ->where('status', '2')
                ->where('free', '1')
                ->where('astrologer_id',  $id)
                ->sum('tds_astro');

                $total_free_earning = $freeearning1 ;
                $free= Booking::select(
                                    'astrologer_id',
                                    DB::raw('SUM(total_minutes) AS total_minutes'),
                                    DB::raw('COUNT(*) AS booking_count'),
                                    DB::raw($month . ' AS current_month')
                                )
                                ->where('status', '2')
                                ->where('free', '1')
                                ->whereMonth('start_time', '=', $month)
                                ->whereYear('start_time', '=', now()->year)
                                ->where('astrologer_id', $id)
                                ->first();
                $freeearning= Booking::select(
                                    'astrologer_id',
                                    DB::raw('SUM(astrologer_comission_amount) AS total_commsion'),
                                    DB::raw('COUNT(*) AS booking_count'),
                                    DB::raw($month . ' AS current_month')
                                )
                                ->where('status', '2')
                                ->where('free', '1')
                                ->whereMonth('start_time', '=', $month)
                                ->whereYear('start_time', '=', now()->year)
                                ->where('astrologer_id', $id)
                                ->first();
                $aaq = AskAQuestion::whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $month)->where('from_type', '2')
                                ->where('status', '1')
                               ->where('astrologer_id', $id)
                                ->count();
                $aaqmoney = AskAQuestion::whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $month)->where('from_type', '2')
                                ->where('status', '1')
                                ->where('astrologer_id', $id)
                                ->sum('astrologer_comission_amount');
                $gifts = DB::table('send_gifts')->whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $month)->where('astrologer_id', $id)->count();
                $giftsmoney = DB::table('send_gifts')->whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $month)->where('astrologer_id', $id)->sum('astrologer_comission_amount');
                $sellingearning = DB::table('astrologer_transactions')->whereYear('created_at', $currentYear)
                                ->whereMonth('created_at', $month)->where('user_id', $id)->where('txn_type', 5)->sum('amount');


                $sellingearning_tds_price = DB::table('astrologer_transactions')
                ->where('user_id',$id)
                ->where('txn_type', 5)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('tds_price');


                $incentive_earning_credit = DB::table('astrologer_transactions')->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)->where('user_id', $id)->where('txn_type', 4)->where('type', 'credit')->sum('amount');
                $incentive_earning_debit = DB::table('astrologer_transactions')->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)->where('user_id', $id)->where('txn_type', 4)->where('type', 'debit')->sum('amount');
                $incentive_earning =$incentive_earning_credit - $incentive_earning_debit ;
                $total_gst_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $id)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('tax_price');

                    $total_gift_gst_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $id)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('gst_astro');




                $total_tds_astro = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $id)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->where('type', 'credit')
                    ->sum('tds_price');


                $total_gift_tds_astro = DB::table('send_gifts')
                    ->where('astrologer_id', '=', $id)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('tds_astro');

                $total_tds_astro_incentive_debit = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('tds_price');




                $giftsmoney_tds_astro= DB::table('send_gifts')
                    ->where('astrologer_id', $id)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('tds_astro');

                $aaqmoney_tds = AskAQuestion::where('from_type', '2')
                    ->where('status', '1')
                    ->where('astrologer_id', $id)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('tds_astro');



                $incentive_earning_credit1 = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $id)
                    ->where('type', '=', "credit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('price');


                $incentive_earning_debit1 = DB::table('astrologer_transactions')
                    ->where('user_id', '=', $id)
                    ->where('type', '=', "debit")
                    ->where('is_incentive', '=', "1")
                    ->where('txn_type', 4)
                    ->whereYear('created_at', $currentYear)->whereMonth('created_at', $month)
                    ->sum('price');

                $astrologer_incentive_amount1 = $incentive_earning_credit1 - $incentive_earning_debit1;



                $total_earnings_all =   $sellingearning_tds_price + $sellingearning +  $astrologer_incentive_amount1 + $giftsmoney + $giftsmoney_tds_astro + $aaqmoney + $aaqmoney_tds + $astrologer_comission_amount + $astrologer_comission_amount_tds;
                $total_tds_all =   $total_tds_astro + $total_gift_tds_astro -  $total_tds_astro_incentive_debit ;

                $total_bank_credit =  $total_earnings_all -  $total_tds_all;

                $reprotdata[] = array(
                        "month_name" =>$monthName,
                        $paid,
                        sprintf('%0.2f', $astrologer_comission_amount  -  $total_free_earning),
                        $free->total_minutes,
                        sprintf('%0.2f', $freeearning->total_commsion),
                        $aaq,
                        sprintf('%0.2f', $aaqmoney),
                        $gifts,
                        sprintf('%0.2f', $giftsmoney),
                        sprintf('%0.2f', $incentive_earning),
                        sprintf('%0.2f', $sellingearning),
                        sprintf( '%0.2f', $total_gst_astro + $total_gift_gst_astro),
                        sprintf( '%0.2f', $total_tds_astro  + $total_gift_tds_astro -  $total_tds_astro_incentive_debit),
                        sprintf( '%0.2f',($total_bank_credit)),
                        $pan_number,
                        $account_name,
                        $account_number ,
                        $account_type,
                        $ifsc_code,
                        "name" => $nm[1] ?? '',
                );
            }
            $i++;
        }
        // print_r(json_encode($data[1]));
        // print_r(json_encode($reprotdata[1]));
        // dd(1);
        return view('print.getreport2', compact('data','reprotdata'));
    }
}
