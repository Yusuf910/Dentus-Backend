<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Coach;
use App\Models\Practiceyogacategories;
use App\Models\Practiceyogaposes;
use App\Models\Yogaposeslevels;
use App\Models\Practiceroutines;
use App\Models\Faq;
use App\Models\Packageusers;
use App\Models\Mastertaxes;
use App\Models\Userpurchases;
use App\Models\PracticeYogaCoachFile;
use App\Models\VideoZipFile;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use MongoDB\BSON\ObjectId;
use App\Models\Routineperformance;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $today = new \MongoDB\BSON\UTCDateTime(strtotime(date("Y-m-d 00:00:00")) * 1000);
        $tomorrow = new \MongoDB\BSON\UTCDateTime(strtotime(date("Y-m-d 23:59:59")) * 1000);

        /* BASIC COUNTS */
        $userCount     = User::whereNotIn('status', [2])->count();
        $coachCount    = Coach::whereNotIn('status', [2])->count();
        $packageCount  = Packageusers::whereNotIn('status', [2])->count();
        $catCount      = Practiceyogacategories::whereNotIn('status', [2])->count();
        $poseCount     = Practiceyogaposes::whereNotIn('status', [2])->count();
        $poselCount    = Yogaposeslevels::whereNotIn('status', [2])->count();
        $pracCount     = Practiceroutines::where('status', 1)->count();
        $purCount      = Userpurchases::whereNotIn('status', [2])->count();

        /* ADVANCED ANALYTICS */

        // 1. Today new users
        $todayUsers = User::whereBetween('createdAt', [$today, $tomorrow])->count();

        // 2. Today package purchases
        $todayPurchases = Userpurchases::whereBetween('createdAt', [$today, $tomorrow])->count();

        // 3. Most popular pose
        $popularPose = Practiceyogaposes::where('status', 1)
                        ->orderBy('views', 'DESC') // if you have views
                        ->first();

        // 4. Most popular routine (based on performances)
        $popularRoutineAgg = Routineperformance::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => ['_id' => '$routineId', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 1]
            ]);
        });

        // Convert to usable array
        $popularRoutineAgg = iterator_to_array($popularRoutineAgg);

        // Fetch routine name
        $popularRoutine = null;

        if (count($popularRoutineAgg) > 0) {
            $routineId = $popularRoutineAgg[0]['_id'];
            $popularRoutine = Practiceroutines::where('_id', $routineId)->first();
        }

        // 5. Total performed routines
        $totalRoutinePerformed = Routineperformance::count();

        // 6. Average performance score
        $avgScore = Routineperformance::avg('score');

        // 7. Monthly routine performance chart data
        $monthlyPerformance = Routineperformance::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$group' => [
                        '_id' => [
                            'year'  => ['$year' => '$createdAt'],
                            'month' => ['$month' => '$createdAt']
                        ],
                        'totalScore' => ['$avg' => '$score'],
                        'count'      => ['$sum' => 1]
                    ]
                ],
                ['$sort' => ['_id.year' => -1, '_id.month' => -1]]
            ]);
        });

        return view(
            'admin.dashboard',
            compact(
                'today',
                'userCount',
                'coachCount',
                'packageCount',
                'catCount',
                'poseCount',
                'poselCount',
                'pracCount',
                'purCount',
                'todayUsers',
                'todayPurchases',
                'popularPose',
                'popularRoutine',
                'totalRoutinePerformed',
                'avgScore',
                'monthlyPerformance',

            )
        );
    }

    public function user_list(Request $request)
    {
        if (isset($_GET['export_file'])) {
            $data[] = [
                "ID",
                "User Details (Name)",
                "Mail ID",
                "Mobile",
                "Status",
                "Gender",
                "Registered On"
            ];

            $queryUser = User::where('status', '!=', 2);

            // Name filter
            if ($request->name) {
                $queryUser->where('name', 'like', '%' . $request->name . '%');
            }

            // Email filter
            if ($request->email) {
                $queryUser->where('email', 'like', '%' . $request->email . '%');
            }

            // Mobile filter
            if ($request->mobile) {
                $queryUser->where('mobile', 'like', '%' . $request->mobile . '%');
            }

            // Status filter
            if ($request->status !== null && $request->status !== "") {
                $queryUser->where('status', (int)$request->status);
            }

            // Date filter (MongoDB Format)
            if ($request->start_date || $request->end_date) {

                $start = $request->start_date
                    ? new \MongoDB\BSON\UTCDateTime(strtotime($request->start_date . " 00:00:00") * 1000)
                    : null;

                $end = $request->end_date
                    ? new \MongoDB\BSON\UTCDateTime(strtotime($request->end_date . " 23:59:59") * 1000)
                    : null;

                if ($start && $end) {
                    $queryUser->whereBetween('createdAt', [$start, $end]);
                } elseif ($start) {
                    $queryUser->where('createdAt', '>=', $start);
                } elseif ($end) {
                    $queryUser->where('createdAt', '<=', $end);
                }
            }

            // Fetch filtered results
            $fetch = $queryUser->orderBy('createdAt', 'DESC')->get();

            $i = 1;
            foreach ($fetch as $user) {
                $st = "";

                switch ($user->status) {
                    case 1:
                        $st = "Active";
                        break;
                    case 2:
                        $st = "Deleted";
                        break;
                    default:
                        $st = "Inactive";
                }
                $data[] = [
                    "ID" => $i,
                    "name" => $user->name,
                    "email" => $user->email,
                    "mobile" => $user->mobile,
                    "status" => $st,
                    "gender" => $user->gender,
                    "createdAt" => date('d/m/Y h:i:s A', strtotime($user->createdAt)),
                ];
                $i++;
            }

            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=\"ManageUser_" . $string_file . ".csv\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
            exit;
        }

        $title = "User List";
        $page_limit = 20;
        $queryUser = User::query();
        $queryUser->where('status', '!=', 2);
        $queryUser->orderBy('updatedAt', 'DESC');

        if (!is_null($request->name)) {
            $queryUser->where('name', 'like', '%' . $request->name . '%');
        }

        if (!is_null($request->email)) {
            $queryUser->where('email', 'like', '%' . $request->email . '%');
        }

        if (!is_null($request->mobile)) {
            $queryUser->where('mobile', 'like', '%' . $request->mobile . '%');
        }

        if (!is_null($request->status)) {
            $queryUser->where('status', $request->status);
        }

        if (!is_null($request->start_date) || !is_null($request->end_date)) {
            $start = $request->start_date ? new \MongoDB\BSON\UTCDateTime(strtotime($request->start_date . " 00:00:00") * 1000) : null;
            $end   = $request->end_date   ? new \MongoDB\BSON\UTCDateTime(strtotime($request->end_date . " 23:59:59") * 1000) : null;

            if ($start && $end) {
                $queryUser->whereBetween('createdAt', [$start, $end]);
            } elseif ($start) {
                $queryUser->where('createdAt', '>=', $start);
            } elseif ($end) {
                $queryUser->where('createdAt', '<=', $end);
            }
        }

        // Fetch list of results
        $data = $queryUser->paginate($page_limit);

        if (!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if (!is_null($request->email)) {
            $data->appends(['email' => $request->get('email')]);
        }
        if (!is_null($request->mobile)) {
            $data->appends(['mobile' => $request->get('mobile')]);
        }
        if (!is_null($request->status)) {
            $data->appends(['status' => $request->get('status')]);
        }
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        return view('admin.manage_user.index', [
            'users' => $data,
            'title' => $title,
            '_id' => request('_id'),
            'name' => request('name'),
            'gender' => request('gender'),
            'mobile' => request('mobile'),
            'email' => request('email'),
            'status' => request('status'),
            'start_date' => request('start_date'),
            'end_date' => request('end_date')
        ]);
    }

    public function user_details($id)
    {
        $user = User::find($id);
        if (!$user) {
            return abort(404, "User not found");
        }

        $r = new ObjectId($id);
        $packages = Userpurchases::where('userId', $r)
            ->orderBy('_id', 'DESC')
            ->get();

        // ROUTINES CREATED BY USER
        $routines = Practiceroutines::where('userId', $r)
            ->orderBy('_id', 'DESC')
            ->get();

        // PERFORMANCE HISTORY
        $performances = Routineperformance::where('userId', $r)
            ->orderBy('_id', 'DESC')
            ->get();

        // PERFORMANCE MONTHLY GRAPH
        $monthlyGraph = $performances->groupBy(function ($p) {
            return date('Y-m', strtotime($p->createdAt));
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'avg_score' => round($group->avg('score')),
                'avg_duration' => round($group->avg('timeDuration')),
            ];
        });

        // PERFORMANCE YEARLY GRAPH
        $yearlyGraph = $performances->groupBy(function ($p) {
            return date('Y', strtotime($p->createdAt));
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'avg_score' => round($group->avg('score')),
                'avg_duration' => round($group->avg('timeDuration')),
            ];
        });

        // POSE & LEVEL LOOKUP TABLES
        $poses = Practiceyogaposes::all()->keyBy('_id');
        $levels = Yogaposeslevels::all()->keyBy('_id');

        return view('admin.manage_user.detail', compact(
            'user',
            'packages',
            'routines',
            'performances',
            'monthlyGraph',
            'yearlyGraph',
            'poses',
            'levels'
        ));
    }

    public function create_user_list()
    {
        $title = 'Add User';
        $listname = 'User';
        $listurl = route('admin.user_list');
        $addurl = route('admin.store_user_list');
        return view('admin.manage_user.create', compact(
            'title',
            'listurl',
            'listname',
            'addurl'
        ));
    }

    public function store_user_list(Request $request)
    {
        // dd($request->all()); die;
        $this->validate($request, [
            'email' => [
                'required',
                Rule::unique(User::class)->where(function ($query) {
                    return $query->whereNotIn('status', [2]);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique(User::class)->where(function ($query) {
                    return $query->whereNotIn('status', [2]);
                })
            ],
            'name' => 'required',
            'status' => 'required',

        ]);
        $a = new User();
        $a->name = $request->name;
        $a->email = $request->email;
        $a->gender = $request->gender;
        $a->mobile = $request->mobile;
        $a->status = (int)$request->status;
        $a->user_type = 1;
        $a->createdAt = now();
        $a->updatedAt = now();
        $a->save();
        return redirect()->route('admin.user_list')
            ->with('success', 'User created successfully !');
    }

    public function edit_user_list($id)
    {
        $title = 'Edit User';
        $listname = 'User';
        $listurl = route('admin.user_list');
        $editurl = 'admin.update_user_list';
        $a = User::find($id);
        if ($a) {
            return view('admin.manage_user.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.manage_user_listuser')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_user_list(Request $request, $id)
    {
        $this->validate($request, [
            // 'status' => 'required',
        ]);

        $a = User::find($id);
        $a->name = $request->username;
        $a->email = $request->email;
        $a->gender = $request->gender;
        $a->mobile = $request->mobile;
        $a->status = (int)$request->status;
        $a->user_type = 1;
        $a->createdAt = now();
        $a->updatedAt = now();
        $a->save();
        return redirect()->route('admin.user_list')
            ->with('success', 'User updated successfully');
    }

    public function delete_user_list(Request $request)
    {
        $input = $request->all();
        // dd($input); die;
        $a = User::find($input['_id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.user_list')
            ->with('success', 'User deleted successfully');
    }


    public function coach_list(Request $request)
    {
        if ($request->export_file) {

            $queryUser = Coach::where('status', '!=', 2);

            // Name filter
            if ($request->name) {
                $queryUser->where('name', 'like', '%' . $request->name . '%');
            }

            // Email filter
            if ($request->email) {
                $queryUser->where('email', 'like', '%' . $request->email . '%');
            }

            // Mobile filter
            if ($request->mobile) {
                $queryUser->where('mobile', 'like', '%' . $request->mobile . '%');
            }

            // Status filter
            if ($request->status !== null && $request->status !== "") {
                $queryUser->where('status', (int)$request->status);
            }

            // MongoDB Date filter
            if ($request->start_date || $request->end_date) {

                $start = $request->start_date
                    ? new \MongoDB\BSON\UTCDateTime(strtotime($request->start_date . " 00:00:00") * 1000)
                    : null;

                $end = $request->end_date
                    ? new \MongoDB\BSON\UTCDateTime(strtotime($request->end_date . " 23:59:59") * 1000)
                    : null;

                if ($start && $end) {
                    $queryUser->whereBetween('createdAt', [$start, $end]);
                } elseif ($start) {
                    $queryUser->where('createdAt', '>=', $start);
                } elseif ($end) {
                    $queryUser->where('createdAt', '<=', $end);
                }
            }

            // FINAL FETCH
            $fetch = $queryUser->orderBy('createdAt', 'DESC')->get();

            // CSV HEADER
            $data[] = [
                "ID",
                "Coach Name",
                "Email",
                "Mobile",
                "Gender",
                "Status",
                "Registered On"
            ];

            $i = 1;

            foreach ($fetch as $coach) {

                $st = $coach->status == 1
                    ? "Active"
                    : ($coach->status == 2 ? "Deleted" : "Inactive");

                $data[] = [
                    "ID" => $i,
                    "name" => $coach->name,
                    "email" => $coach->email,
                    "mobile" => $coach->mobile,
                    "gender" => $coach->gender,
                    "status" => $st,
                    "createdAt" => date('d/m/Y h:i:s A', strtotime($coach->createdAt)),
                ];

                $i++;
            }

            // OUTPUT CSV
            $filename = "CoachList_" . date("d-m-Y_h-i-A") . ".csv";

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 safe BOM

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
            exit;
        }


        $title = "Coach List";
        $page_limit = 20;
        $queryUser = Coach::query();
        $queryUser->where('status', '!=', 2);
        $queryUser->orderBy('createdAt', 'DESC');

        if (!is_null($request->name)) {
            $queryUser->where('name', 'like', '%' . $request->name . '%');
        }

        if (!is_null($request->email)) {
            $queryUser->where('email', 'like', '%' . $request->email . '%');
        }

        if (!is_null($request->mobile)) {
            $queryUser->where('mobile', 'like', '%' . $request->mobile . '%');
        }

        if (!is_null($request->status)) {
            $queryUser->where('status', $request->status);
        }

        if ($request->start_date || $request->end_date) {

            $start = $request->start_date
                ? new \MongoDB\BSON\UTCDateTime(strtotime($request->start_date . " 00:00:00") * 1000)
                : null;

            $end = $request->end_date
                ? new \MongoDB\BSON\UTCDateTime(strtotime($request->end_date . " 23:59:59") * 1000)
                : null;

            if ($start && $end) {
                $queryUser->whereBetween('createdAt', [$start, $end]);
            } elseif ($start) {
                $queryUser->where('createdAt', '>=', $start);
            } elseif ($end) {
                $queryUser->where('createdAt', '<=', $end);
            }
        }

        // Fetch list of results
        $data = $queryUser->paginate($page_limit);

        if (!is_null($request->name)) {
            $data->appends(['name' => $request->get('name')]);
        }
        if (!is_null($request->email)) {
            $data->appends(['email' => $request->get('email')]);
        }
        if (!is_null($request->mobile)) {
            $data->appends(['mobile' => $request->get('mobile')]);
        }
        if (!is_null($request->status)) {
            $data->appends(['status' => $request->get('status')]);
        }
        if (!is_null($request->start_date)) {
            $data->appends(['start_date' => $request->get('start_date')]);
        }
        if (!is_null($request->end_date)) {
            $data->appends(['end_date' => $request->get('end_date')]);
        }

        return view('admin.manage_user.coach_list', [
            'users' => $data,
            'title' => $title,
            '_id' => request('_id'),
            'name' => request('name'),
            'gender' => request('gender'),
            'mobile' => request('mobile'),
            'email' => request('email'),
            'status' => request('status'),
            'start_date' => request('start_date'),
            'end_date' => request('end_date')
        ]);
    }

    public function create_coach_list()
    {
        $title = 'Add Coach';
        $listname = 'Coach';
        $listurl = route('admin.coach_list');
        $addurl = route('admin.store_coach_list');
        return view('admin.manage_user.manage_coach.create', compact(
            'title',
            'listurl',
            'listname',
            'addurl'
        ));
    }

    public function store_coach_list(Request $request)
    {
        // dd($request->all()); die;
        $this->validate($request, [
            'email' => [
                'required',
                Rule::unique(Coach::class)->where(function ($query) {
                    return $query->whereNotIn('status', [2]);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique(Coach::class)->where(function ($query) {
                    return $query->whereNotIn('status', [2]);
                })
            ],
            'name' => 'required',
            'status' => 'required',
        ]);

        $a = new Coach();
        $a->name = $request->name;
        $a->email = $request->email;
        $a->gender = $request->gender;
        $a->mobile = $request->mobile;
        $a->status = (int)$request->status;
        $a->user_type = 1;
        $a->createdAt = now();
        $a->updatedAt = now();
        $a->save();

        return redirect()->route('admin.coach_list')
            ->with('success', 'Coach created successfully !');
    }

    public function edit_coach_list($id)
    {
        $title = 'Edit Coach';
        $listname = 'Coach';
        $listurl = route('admin.coach_list');
        $editurl = 'admin.update_coach_list';
        $a = Coach::find($id);
        if ($a) {
            return view('admin.manage_user.manage_coach.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.coach_list')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_coach_list(Request $request, $id)
    {
        $this->validate($request, [
            'email' => [
                'required',
                Rule::unique(Coach::class)->where(function ($query) use ($id) {
                    return $query->whereNotIn('status', [2]);
                })
            ],
            'mobile' => [
                'required',
                Rule::unique(Coach::class)->where(function ($query) use ($id) {
                    return $query->whereNotIn('status', [2]);
                })
            ],
            'username' => 'required',
            'status' => 'required',
        ]);

        $a = Coach::find($id);
        $a->name = $request->username;
        $a->email = $request->email;
        $a->gender = $request->gender;
        $a->mobile = $request->mobile;
        $a->status = (int)$request->status;
        $a->user_type = 1;
        // $a->createdAt = now();
        $a->updatedAt = now();
        $a->save();

        return redirect()->route('admin.coach_list')
            ->with('success', 'Coach updated successfully');
    }

    public function delete_coach_list(Request $request)
    {
        $input = $request->all();
        $a = Coach::find($input['_id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.coach_list')
            ->with('success', 'Coach deleted successfully');
    }


    public function yoga_categories()
    {
        $data = Practiceyogacategories::whereNotIn('status', [2])
            ->orderBy('_id', 'DESC')
            ->get();
        $addbutton = 'Add Category';
        $title = 'Category List';
        $listurl = route('admin.yoga_categories');
        $addurl = route('admin.store_yoga_categories');
        $editurl = 'admin.edit_yoga_categories';
        $destroyurl = route('admin.destroy_yoga_categories');

        return view(
            'admin.YogaCategories.index',
            compact('data', 'title', 'listurl', 'addbutton', 'addurl', 'editurl', 'destroyurl')
        );
    }

    public function create_yoga_categories()
    {
        $title = 'Add Yoga Category';
        $listname = 'Yoga Category';
        $listurl = route('admin.yoga_categories');
        $addurl = route('admin.store_yoga_categories');
        return view('admin.YogaCategories.create', compact(
            'title',
            'listurl',
            'listname',
            'addurl'
        ));
    }


    public function store_yoga_categories(Request $request)
    {
        $request->validate([
            //     'name'   => 'required|string|max:255',
            //     'status' => 'required|in:0,1',
            //     'image'  => 'nullable|image|mimes:jpg,png|max:2048',
            // ], [
            //     'name.required'   => 'Name is required.',
            //     'status.required' => 'Status is required.',
            //     'status.in'       => 'Status must be 0 or 1.',
            //     'image.image'     => 'Invalid image format.',
            //     'image.mimes'     => 'Only JPG and PNG are allowed.',
            //     'image.max'       => 'Image size should not exceed 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $fileNameWithExtension = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $newFileName = 'yoga_category_' . $fileName . '_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('yoga/category/'), $newFileName);

            $imagePath = asset('project/public/yoga/category/' . $newFileName);
        }

        $a = new Practiceyogacategories();
        $a->name = $request->name;
        $a->image = $imagePath;
        $a->status = (int) $request->status;
        $a->created_at = now();
        $a->updated_at = now();
        $a->save();

        return redirect()->route('admin.yoga_categories')
            ->with('success', 'Yoga category created successfully!');
    }


    public function edit_yoga_categories($id)
    {
        $title = 'Edit Category';
        $listname = 'Category';
        $listurl = route('admin.yoga_categories');
        $editurl = 'admin.update_yoga_categories';
        $a = Practiceyogacategories::find($id);
        if ($a) {
            return view('admin.YogaCategories.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.yoga_categories')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_yoga_categories(Request $request, $id)
    {
        $a = Practiceyogacategories::find($id);
        $imagePath = $a->image;

        if ($request->hasFile('image')) {
            $fileNameWithExtension = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $newFileName = 'yoga_category_' . $fileName . '_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('yoga/category/'), $newFileName);
            $imagePath = asset('project/public/yoga/category/' . $newFileName);
        }
        $a->name = $request->name;
        $a->status = (int) $request->status;
        $a->image = $imagePath;
        $a->updated_at = now();
        $a->save();

        return redirect()->route('admin.yoga_categories')
            ->with('success', 'Yoga category updated successfully!');
    }


    public function destroy_yoga_categories(Request $request)
    {
        $input = $request->all();
        $a = Practiceyogacategories::find($input['_id']);
        $a->status = 2;
        $a->save();

        return redirect()->route('admin.yoga_categories')
            ->with('success', 'Yoga category deleted successfully!');
    }


    // public function yoga_poses()
    // {
    //     $data = Practiceyogaposes::whereNotIn('status', [2])
    //         ->orderBy('_id', 'DESC')
    //         ->get();
    //     $addbutton = 'Add Pose';
    //     $title = 'Pose List';
    //     $listurl = route('admin.yoga_poses');
    //     $addurl = route('admin.store_yoga_poses');
    //     $editurl = 'admin.edit_yoga_poses';
    //     $destroyurl = route('admin.destroy_yoga_poses');

    //     return view(
    //         'admin.YogaPoses.index',
    //         compact('data', 'title', 'listurl', 'addbutton', 'addurl', 'editurl', 'destroyurl')
    //     );
    // }
    public function yoga_poses(Request $request)
    {
        $query = Practiceyogaposes::whereNotIn('status', [2]);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->categoryId) {
            $categoryIdObj = new ObjectId($request->categoryId);
            $query->where('categoryId', $categoryIdObj);
        }
        if ($request->Type_of_tracking) {
            $query->where('Type_of_tracking', $request->Type_of_tracking);
        }

        if ($request->status !== null && $request->status !== "") {
            $query->where('status', intval($request->status));
        }

        if ($request->from_date && $request->to_date) {
            $from = date('Y-m-d 00:00:00', strtotime($request->from_date));
            $to   = date('Y-m-d 23:59:59', strtotime($request->to_date));

            $query->whereBetween('createdAt', [$from, $to]);
        }

        $data = $query->orderBy('_id', 'DESC')->paginate(20);

        $categories = Practiceyogacategories::where('status', 1)->get()->keyBy('_id');

        $addbutton = 'Add Pose';
        $title = 'Pose List';
        $listurl = route('admin.yoga_poses');
        $addurl = route('admin.store_yoga_poses');
        $editurl = 'admin.edit_yoga_poses';
        $destroyurl = route('admin.destroy_yoga_poses');

        return view(
            'admin.YogaPoses.index',
            compact('data', 'categories', 'title', 'listurl', 'addbutton', 'addurl', 'editurl', 'destroyurl')
        );
    }

    public function toggleStatus(Request $request)
    {
        $id = $request->id;
        $currentStatus = (int)$request->status;

        $newStatus = $currentStatus === 1 ? 0 : 1;

        Practiceyogaposes::where('_id', new ObjectId($id))->update(['status' => $newStatus]);

        return response()->json([
            'status' => true,
            'new_status' => $newStatus
        ]);
    }




    public function create_yoga_poses()
    {
        $title = 'Add Yoga Pose';
        $listname = 'Yoga Pose';
        $listurl = route('admin.yoga_poses');
        $addurl = route('admin.store_yoga_poses');
        return view('admin.YogaPoses.create', compact(
            'title',
            'listurl',
            'listname',
            'addurl'
        ));
    }

    public function store_yoga_poses(Request $request)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $fileNameWithExtension = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $newFileName = 'yoga_pose_' . $fileName . '_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('yoga/poses/'), $newFileName);
            $imagePath = asset('project/public/yoga/poses/' . $newFileName);
        }

        $categoryIdObj = new ObjectId($request->categoryId);
        $checkExists = Practiceyogaposes::where('name', $request->name)
            ->where('categoryId', $categoryIdObj)
            ->first();

        if ($checkExists) {
            return redirect()->route('admin.yoga_poses')
                ->with('error', 'This yoga pose already exists in the selected category !');
        }

        $a = new Practiceyogaposes();
        $a->name = $request->name;
        // $a->to_do_steps = $request->to_do_steps;
        $a->categoryId = $categoryIdObj;
        $a->image = $imagePath;
        $a->status = (int) $request->status;
        $toDo = [];
        foreach ($request->to_do as $item) {
            $toDo[] = [
                'points' => $item['points'],
                '_id' => (string) new \MongoDB\BSON\ObjectId()
            ];
        }
        $a->to_do = $toDo;
        $a->Type_of_tracking = $request->Type_of_tracking;
        $a->Angle = $request->Angle;
        $a->position = $request->position;
        $a->symetric = $request->symetric;
        $a->created_at = now();
        $a->updated_at = now();
        $a->save();

        return redirect()->route('admin.yoga_poses')
            ->with('success', 'Yoga pose created successfully!');
    }

    public function edit_yoga_poses($id)
    {
        $title = 'Edit Pose';
        $listname = 'Pose';
        $listurl = route('admin.yoga_poses');
        $editurl = 'admin.update_yoga_poses';
        $a = Practiceyogaposes::find($id);
        if ($a) {
            return view('admin.YogaPoses.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.yoga_poses')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_yoga_poses(Request $request, $id)
    {

        $categoryIdObj = new ObjectId($request->categoryId);
        $checkExists = Practiceyogaposes::where('name', $request->name)
            ->where('categoryId', $categoryIdObj)
            ->where('_id', '!=', $id)
            ->first();

        if ($checkExists) {
            return redirect()->route('admin.yoga_poses')
                ->with('error', 'This yoga pose already exists in the selected category!');
        }
        $a = Practiceyogaposes::find($id);
        $imagePath = $a->image;

        if ($request->hasFile('image')) {
            $fileNameWithExtension = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $newFileName = 'yoga_pose_' . $fileName . '_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('yoga/poses/'), $newFileName);
            $imagePath = asset('project/public/yoga/poses/' . $newFileName);
        }

        $a->name = $request->name;
        $a->categoryId = $categoryIdObj;
        $a->status = (int) $request->status;
        $a->image = $imagePath;
        $a->position = $request->position;
        $a->updated_at = now();
        $toDo = [];
        foreach ($request->to_do as $item) {
            $_id = $item['_id'] ?? (string) new \MongoDB\BSON\ObjectId();

            $toDo[] = [
                'points' => $item['points'],
                '_id' => $_id
            ];
        }
        $a->Type_of_tracking = $request->Type_of_tracking;
        $a->Angle = $request->Angle;
        $a->symetric = $request->symetric;
        $a->to_do = $toDo;
        $a->save();

        return redirect()->route('admin.yoga_poses')
            ->with('success', 'Yoga pose updated successfully!');
    }

    public function destroy_yoga_poses(Request $request)
    {
        $input = $request->all();
        $a = Practiceyogaposes::find($input['_id']);
        $a->status = 2;
        $a->save();

        return redirect()->route('admin.yoga_poses')
            ->with('success', 'Yoga pose deleted successfully!');
    }

    public function faq()
    {
        if (isset($_GET['export_file'])) {
            $data[] = array(
                "ID",
                "Question",
                "Answer",
                "Position",
                "Status",
                "Created At"
            );
            $fetch = Faq::where('status', '!=', 2)->paginate(500000); //status is not equal to  2
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
                    "question" => $user->question,
                    "answer" => $user->answer,
                    "position" => $user->position,
                    "status" => $st,
                    "created_at" => date('d M y', strtotime($user->created_at)),
                );
                $i++;
            }

            $string_file = date("d-m-Y h:i:s A");

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"FAQ's" . $string_file . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
            fclose($handle);
            exit;
        }
        $data = Faq::whereNOTIN('status', [2])->orderBy('id', 'ASC')->get();
        $addbutton = 'Add FAQ';
        $importbutton = 'Upload Excel';
        $title = 'FAQ List';
        $listurl = route('admin.faq');
        $addurl = route('admin.create_faq');
        $editurl = 'admin.edit_faq';
        $destroyurl = route('admin.destroy_faq');
        $addurl1 = route('admin.store_faq');
        return view('admin.faq.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'editurl', 'destroyurl'));
    }

    public function create_faq()
    {
        $title = 'Add FAQ';
        $listname = 'FAQ';
        $listurl = route('admin.faq');
        $addurl = route('admin.store_faq');
        return view('admin.faq.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_faq(Request $request)
    {
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required',
        ]);

        $a = new Faq();
        $a->question = $request->question;
        $a->answer = $request->answer;
        $a->position = $request->position ?? 0;
        $a->status = $request->status;
        $a->save();
        return redirect()->route('admin.faq')
            ->with('success', 'FAQ Created Successfully !');
    }
    public function edit_faq($id)
    {
        $title = 'Edit FAQ';
        $listname = 'FAQ';
        $listurl = route('admin.faq');
        $editurl = 'admin.update_faq';
        $a = Faq::find($id);
        if ($a) {
            return view('admin.faq.edit', compact('a', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.faq')
                ->with('failure', 'Not found any data');
        }
    }
    public function update_faq(Request $request, $id)
    {
        $this->validate($request, [
            // 'question' => 'required',
        ]);

        $a = Faq::find($id);
        $a->question = is_null($request->question) ? $a->question : $request->question;
        $a->answer = is_null($request->answer) ? $a->answer : $request->answer;
        $a->position = is_null($request->position) ? $a->position : $request->position;
        $a->status = is_null($request->status) ? $a->status : $request->status;
        $a->save();
        return redirect()->route('admin.faq')
            ->with('update', 'FAQ Updated Successfully !');
    }

    public function destroy_faq(Request $request)
    {

        $input = $request->all();
        $a = Faq::find($input['id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.faq')
            ->with('delete', 'FAQ Deleted Successfully !');
    }

    public function package_users()
    {
        $data = Packageusers::whereNOTIN('status', [2])->orderBy('id', 'ASC')->get();
        $addbutton = 'Add';
        $importbutton = 'Upload Excel';
        $title = 'Package List';
        $listurl = route('admin.package_users');
        $addurl = route('admin.create_package_users');
        $editurl = 'admin.edit_package_users';
        $destroyurl = route('admin.destroy_package_users');
        $addurl1 = route('admin.store_package_users');
        $tax = Mastertaxes::where('status', 1)->orderBy('id', 'ASC')->select('id', 'name', 'taxvalue')->get();
        return view('admin.Subscription.index', compact('data', 'tax', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'editurl', 'destroyurl'));
    }

    public function create_package_users()
    {
        $title = 'Add Subscription';
        $listname = 'Subscription';
        $listurl = route('admin.package_users');
        $addurl = route('admin.store_package_users');
        return view('admin.Subscription.create', compact('title', 'listurl', 'listname', 'addurl'));
    }

    public function store_package_users(Request $request)
    {
        // dd($request->all()); die;
        $this->validate($request, [
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'type' => 'required',
            'status' => 'required',
            'taxId' => 'required',
            'what_you_got' => 'required|array|min:1',
            'what_you_got.*.posesId' => 'required',
            'what_you_got.*.is_free' => 'required',
        ]);

        $subscription = new Packageusers();
        $subscription->name = $request->name;
        $subscription->price = $request->price;
        $subscription->discount_price = $request->discount_price ?? 0;
        $subscription->validity_days = $request->validity_days;
        $subscription->type = $request->type;
        $subscription->status = $request->status;
        $subscription->taxId = $request->taxId;
        $features = [];
        foreach ($request->what_you_got as $item) {
            $features[] = [
                'posesId' => $item['posesId'],
                'is_free' => $item['is_free'],
                '_id' => (string) new \MongoDB\BSON\ObjectId()
            ];
        }
        $subscription->what_you_got = $features;
        $subscription->save();

        return redirect()->route('admin.package_users')
            ->with('success', 'Subscription Created Successfully!');
    }

    public function edit_package_users($id)
    {
        $title = 'Edit Subscription';
        $listname = 'Subscription List';
        $listurl = route('admin.package_users');
        $editurl = 'admin.update_package_users';
        $a = Packageusers::findOrFail($id);
        if ($a) {
            $tax = Mastertaxes::whereNOTIN('status', [2])->where('status', 1)->orderBy('id', 'ASC')->get();
            return view('admin.Subscription.edit', compact('a', 'tax', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.package_users')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_package_users(Request $request, $id)
    {
        $this->validate($request, [
            // 'name' => 'required|string',
            // 'price' => 'required|numeric|min:0',
            // 'validity_days' => 'required|integer|min:1',
            // 'type' => 'required',
            // 'status' => 'required',
            // 'taxId' => 'required',
            // 'what_you_got' => 'required|array|min:1',
            // 'what_you_got.*.posesId' => 'required',
            // 'what_you_got.*.is_free' => 'required',
        ]);
        $taxIdObj = new ObjectId($request->taxId);
        $a = Packageusers::findOrFail($id);
        $a->name = $request->name;
        $a->price = $request->price;
        $a->discount_price = $request->discount_price ?? 0;
        $a->validity_days = $request->validity_days;
        $a->type = $request->type;
        $a->status = $request->status;
        $a->taxId = $taxIdObj;
        $features = [];
        foreach ($request->what_you_got as $item) {
            $_id = $item['_id'] ?? (string) new \MongoDB\BSON\ObjectId();

            $features[] = [
                'posesId' => $item['posesId'],
                'is_free' => $item['is_free'],
                '_id' => $_id
            ];
        }

        $a->what_you_got = $features;
        $a->save();

        return redirect()->route('admin.package_users')
            ->with('success', 'Subscription Updated Successfully !');
    }

    public function destroy_package_users(Request $request)
    {
        $input = $request->all();
        $a = Packageusers::find($input['_id']);
        if (!$a) {
            return redirect()->back()->with('error', 'Subscription not found.');
        }
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.package_users')
            ->with('delete', 'Subscription Deleted Successfully !');
    }


    // public function yoga_pose_levels()
    // {
    //     $data = Yogaposeslevels::whereNOTIN('status', [2])->orderBy('name', 'DESC')->paginate(10);
    //     $addbutton = 'Add Pose Levels';
    //     $importbutton = 'Upload Excel';
    //     $title = 'Yoga Pose Levels';
    //     $listurl = route('admin.yoga_pose_levels');
    //     $addurl = route('admin.create_yoga_pose_levels');
    //     $editurl = 'admin.edit_yoga_pose_levels';
    //     $destroyurl = route('admin.destroy_yoga_pose_levels');
    //     $addurl1 = route('admin.store_yoga_pose_levels');

    //     return view('admin.YogaPoseLevels.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'editurl', 'destroyurl'));
    // }
    public function yoga_pose_levels(Request $request)
    {
        // Main Query
        $query = Yogaposeslevels::whereNotIn('status', [2]);

        // 🔍 SEARCH FILTER (name, levelname)
        if ($request->search) {
            $query->where('levelname', 'like', '%' . $request->search . '%');
        }

        // 📌 FILTER BY POSE ID
        if ($request->posesId) {
            $query->where('posesId', new ObjectId($request->posesId));
        }

        // 🟢 STATUS FILTER
        if ($request->status !== null && $request->status !== "") {
            $query->where('status', intval($request->status));
        }
        if ($request->levelname) {
            $query->where('levelname', $request->levelname);
        }

        // 📅 DATE RANGE FILTER
        if ($request->from_date && $request->to_date) {
            $from = date('Y-m-d 00:00:00', strtotime($request->from_date));
            $to   = date('Y-m-d 23:59:59', strtotime($request->to_date));
            $query->whereBetween('createdAt', [$from, $to]);
        }

        // PAGINATION
        $data = $query->orderBy('_id', 'DESC')->paginate(15);

        // Preload all poses (to avoid N+1 problem)
        $posesList = Practiceyogaposes::whereNotIn('status', [2])->get()->keyBy('_id');

        // Page Variables
        $addbutton = 'Add Pose Levels';
        $importbutton = 'Upload Excel';
        $title = 'Yoga Pose Levels';
        $listurl = route('admin.yoga_pose_levels');
        $addurl = route('admin.create_yoga_pose_levels');
        $editurl = 'admin.edit_yoga_pose_levels';
        $destroyurl = route('admin.destroy_yoga_pose_levels');
        $addurl1 = route('admin.store_yoga_pose_levels');

        return view('admin.YogaPoseLevels.index', compact(
            'data', 
            'posesList',
            'title', 
            'listurl', 
            'addbutton', 
            'importbutton',
            'addurl', 
            'addurl1', 
            'editurl', 
            'destroyurl'
        ));
    }


    public function create_yoga_pose_levels()
    {
        $title = 'Add Yoga Pose Level';
        $listname = 'Yoga Pose Levels';
        $listurl = route('admin.yoga_pose_levels');
        $addurl = route('admin.store_yoga_pose_levels');
        $poses = Practiceyogaposes::where('status', 1)->orderBy('id', 'ASC')->get();

        return view('admin.YogaPoseLevels.create', compact('title', 'listurl', 'listname', 'addurl', 'poses'));
    }

    public function store_yoga_pose_levels(Request $request)
    {
        $this->validate($request, [
            'posesId'    => 'required',
            'levelname'  => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Yogaposeslevels::where('posesId', new ObjectId($request->posesId))
                        ->where('levelname', $value)
                        ->exists();

                    if ($exists) {
                        $fail('This level already exists for the selected Yoga Pose.');
                    }
                },
            ],
            'video'      => 'required',
            'status'     => 'required',
            // 'duration'   => 'required|integer|min:1',
            // 'about'      => 'required|string',
            // 'to_do'      => 'required',
            // 'to_do.*.points' => 'required|string',
        ]);
        $posesIdObj = new ObjectId($request->posesId);
        $a = new Yogaposeslevels();
        $a->levelname = $request->levelname;
        $a->video = $request->video;
        $a->posesId = $posesIdObj;
        $a->status = $request->status;
        $a->duration = $request->duration;
        $a->about = $request->about;

        $toDo = [];
        foreach ($request->to_do as $item) {
            $toDo[] = [
                'points' => $item['points'],
                '_id' => (string) new \MongoDB\BSON\ObjectId()
            ];
        }
        $a->to_do = $toDo;
        $a->save();

        return redirect()->route('admin.yoga_pose_levels')
            ->with('success', 'Yoga Pose Level Created Successfully!');
    }

    public function edit_yoga_pose_levels($id)
    {
        $title = 'Edit Yoga Pose Level';
        $listname = 'Yoga Pose Levels';
        $listurl = route('admin.yoga_pose_levels');
        $editurl = 'admin.update_yoga_pose_levels';
        $a = Yogaposeslevels::findOrFail($id);

        if ($a) {
            $poses = Practiceyogaposes::where('status', 1)->orderBy('id', 'ASC')->get();
            return view('admin.YogaPoseLevels.edit', compact('a', 'poses', 'title', 'listurl', 'listname', 'editurl'));
        } else {
            return redirect()->route('admin.yoga_pose_levels')
                ->with('failure', 'Not found any data');
        }
    }

    public function update_yoga_pose_levels(Request $request, $id)
    {
        $this->validate($request, [
            'posesId'    => 'required',
            'levelname'  => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request, $id) {
                    $exists = Yogaposeslevels::where('posesId', new ObjectId($request->posesId))
                        ->where('levelname', $value)
                        ->where('_id', '!=', new ObjectId($id))
                        ->exists();

                    if ($exists) {
                        $fail('This level already exists for the selected Yoga Pose.');
                    }
                },
            ],
            'video'      => 'required',
            'status'     => 'required',
            // 'duration'   => 'required|integer|min:1',
            // 'about'      => 'required|string',
            // 'to_do'      => 'required',
            // 'to_do.*.points' => 'required|string',
        ]);
        $posesIdObj = new ObjectId($request->posesId);
        $a = Yogaposeslevels::findOrFail($id);
        $a->levelname = $request->levelname;
        $a->video = $request->video;
        $a->posesId = $posesIdObj;
        $a->status = $request->status;
        $a->duration = $request->duration;
        $a->about = $request->about;

        $toDo = [];
        foreach ($request->to_do as $item) {
            $_id = $item['_id'] ?? (string) new \MongoDB\BSON\ObjectId();

            $toDo[] = [
                'points' => $item['points'],
                '_id' => $_id
            ];
        }
        $a->to_do = $toDo;
        $a->save();

        return redirect()->route('admin.yoga_pose_levels')
            ->with('success', 'Yoga Pose Level Updated Successfully!');
    }

    public function destroy_yoga_pose_levels(Request $request)
    {
        $input = $request->all();
        $a = Yogaposeslevels::find($input['_id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.yoga_pose_levels')
            ->with('delete', 'Yoga Pose Level Deleted Successfully!');
    }


    // public function practice_routines()
    // {
    //     $data = Practiceroutines::whereNOTIN('status', [2])->orderBy('id', 'ASC')->paginate(20);
    //     $addbutton = 'Add Practice Routines';
    //     $importbutton = 'Upload Excel';
    //     $title = 'Yoga Practice Routines';
    //     $listurl = route('admin.practice_routines');
    //     $addurl = route('admin.create_practice_routines');
    //     $editurl = 'admin.edit_practice_routines';
    //     $destroyurl = route('admin.destroy_practice_routines');
    //     $addurl1 = route('admin.store_practice_routines');

    //     return view('admin.PracticeRoutines.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton', 'addurl', 'addurl1', 'editurl', 'destroyurl'));
    // }
    public function practice_routines(Request $request)
    {
        $query = Practiceroutines::where('status', 1);

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->userId) {
            $query->where('userId', new ObjectId($request->userId));
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', intval($request->status));
        }

        if ($request->from_date || $request->to_date) {

            $start = $request->start_date
                ? new \MongoDB\BSON\UTCDateTime(strtotime($request->from_date . " 00:00:00") * 1000)
                : null;

            $end = $request->end_date
                ? new \MongoDB\BSON\UTCDateTime(strtotime($request->to_date . " 23:59:59") * 1000)
                : null;

            if ($start && $end) {
                $queryUser->whereBetween('createdAt', [$start, $end]);
            } elseif ($start) {
                $queryUser->where('createdAt', '>=', $start);
            } elseif ($end) {
                $queryUser->where('createdAt', '<=', $end);
            }
        }

        if ($request->poseId) {
            $query->where('poseslist.posesId', new ObjectId($request->poseId));
        }

        $data = $query->orderBy('_id', 'DESC')->paginate(20);

        // PRELOAD USERS (avoid N+1)
        $users = \App\Models\User::all()->where('status',1)->keyBy('_id');

        // PRELOAD POSES
        $allPoses = \App\Models\Practiceyogaposes::all()->where('status',1)->keyBy('_id');

        // PRELOAD LEVELS
        $allLevels = \App\Models\Yogaposeslevels::all()->where('status',1)->keyBy('_id');

        // PAGE DATA
        $addbutton = 'Add Practice Routines';
        $importbutton = 'Upload Excel';
        $title = 'Yoga Practice Routines';
        $listurl = route('admin.practice_routines');
        $addurl = route('admin.create_practice_routines');
        $editurl = 'admin.edit_practice_routines';
        $destroyurl = route('admin.destroy_practice_routines');
        $addurl1 = route('admin.store_practice_routines');

        return view(
            'admin.PracticeRoutines.index',
            compact(
                'data',
                'users',
                'allPoses',
                'allLevels',
                'title',
                'listurl',
                'addbutton',
                'importbutton',
                'addurl',
                'editurl',
                'addurl1',
                'destroyurl'
            )
        );
    }

    public function practice_routine_details($id)
    {
        $routine = Practiceroutines::find($id);

        if (!$routine) {
            return abort(404, "Routine not found");
        }

        $user = User::find($routine->userId);

        $poses = Practiceyogaposes::all()->keyBy('_id');
        $levels = Yogaposeslevels::all()->keyBy('_id');

        // Performance History for this routine
        $history = Routineperformance::where('routineId', $id)
            ->orderBy('_id', 'DESC')
            ->get();

        return view('admin.PracticeRoutines.details', compact(
            'routine',
            'user',
            'poses',
            'levels',
            'history'
        ));
    }



    public function destroy_practice_routines(Request $request)
    {
        $input = $request->all();
        $a = Practiceroutines::find($input['_id']);
        $a->status = 2;
        $a->save();
        return redirect()->route('admin.practice_routines')
            ->with('delete', 'Practice Routine Deleted Successfully!');
    }

    public function purchase_history()
    {
        $data = Userpurchases::whereNOTIN('status', [2])->orderBy('_id', 'ASC')->get();
        $addbutton = 'Add Purchase History';
        $importbutton = 'Upload Excel';
        $title = 'Purchase History';
        $listurl = route('admin.purchase_history');
        // $addurl = route('admin.create_purchase_history');
        // $editurl = 'admin.edit_purchase_history';
        // $destroyurl = route('admin.destroy_purchase_history');
        // $addurl1 = route('admin.store_purchase_history');

        return view('admin.PurchaseHistory.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton'));
    }

    public function  practice_yoga_coach_file()
    {
        $data = \DB::connection('mongodb')->table('practiceyogacoachfiles')->where('status', 1)->get();
        $addbutton = 'Add Practice Yoga Coach File';
        $importbutton = 'Upload Excel';
        $title = 'Practice Yoga Coach File';
        $listurl = route('admin.practice_yoga_coach_file');
        return view('admin.coachFiles.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton'));
    }

    public function delete_coachfile(Request $request)
    {
        $input = $request->all();
        $updated = \DB::connection('mongodb')
            ->table('practiceyogacoachfiles')
            ->where('_id', new ObjectId($input['_id']))
            ->update(['status' => 2]);

        // if ($updated) {
        //     return response()->json(['status' => true, 'message' => 'Updated successfully']);
        // }

        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }
    public function  video_zip_files()
    {
        $data = \DB::connection('mongodb')->table('videozipfiles')->where('status', 1)->get();
        $addbutton = 'Add Video Zip File';
        $importbutton = 'Upload Excel';
        $title = 'Video Zip File';
        $listurl = route('admin.video_zip_files');
        return view('admin.videozipFiles.index', compact('data', 'title', 'listurl', 'addbutton', 'importbutton'));
    }
    public function delete_video_zip_files(Request $request)
    {
        $input = $request->all();
        $updated = \DB::connection('mongodb')
            ->table('videozipfiles')
            ->where('_id', new ObjectId($input['_id']))
            ->update(['status' => 2]);

        // if ($updated) {
        //     return response()->json(['status' => true, 'message' => 'Updated successfully']);
        // }

        return redirect()->back()
            ->with('success', 'Deleted successfully');
    }

    public function analytics()
    {
        // RANGE SUPPORT (default last 30 days)
        $startDate = request('start_date') 
            ? new \MongoDB\BSON\UTCDateTime(strtotime(request('start_date').' 00:00:00') * 1000)
            : new \MongoDB\BSON\UTCDateTime(strtotime('-30 days') * 1000);

        $endDate = request('end_date') 
            ? new \MongoDB\BSON\UTCDateTime(strtotime(request('end_date').' 23:59:59') * 1000)
            : new \MongoDB\BSON\UTCDateTime(strtotime('today 23:59:59') * 1000);

        /* -----------------------------------------------------
           BASIC COUNTS
        ------------------------------------------------------ */
        $totalUsers     = User::where('status', 1)->count();
        $totalCoaches   = Coach::where('status', 1)->count();
        $totalRoutines  = Practiceroutines::where('status', 1)->count();
        $totalPurchases = Userpurchases::where('status', '!=', 2)->count();

        /* -----------------------------------------------------
           TREND DATA (Users & Purchases)
        ------------------------------------------------------ */
        $userTrend = User::raw(function ($collection) use ($startDate, $endDate) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'createdAt' => ['$gte' => $startDate, '$lte' => $endDate]
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'year' => ['$year' => '$createdAt'],
                            'month' => ['$month' => '$createdAt'],
                        ],
                        'count' => ['$sum' => 1]
                    ]
                ],
                ['$sort' => ['_id.year' => 1, '_id.month' => 1]]
            ]);
        });

        $purchaseTrend = Userpurchases::raw(function ($collection) use ($startDate, $endDate) {
            return $collection->aggregate([
                ['$match' => ['createdAt' => ['$gte' => $startDate, '$lte' => $endDate]]],
                [
                    '$group' => [
                        '_id' => [
                            'year' => ['$year' => '$createdAt'],
                            'month' => ['$month' => '$createdAt'],
                        ],
                        'total' => ['$sum' => '$total_price']
                    ]
                ],
                ['$sort' => ['_id.year' => 1, '_id.month' => 1]]
            ]);
        });

        /* -----------------------------------------------------
           MOST ACTIVE USER
        ------------------------------------------------------ */
        $topUserAgg = Routineperformance::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => ['_id' => '$userId', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 1]
            ]);
        });

        $topUser = null;
        if (iterator_count($topUserAgg) > 0) {
            $topUserId = iterator_to_array($topUserAgg)[0]['_id'];
            $topUser = User::find($topUserId);
        }

        /* -----------------------------------------------------
           MOST POPULAR ROUTINE
        ------------------------------------------------------ */
        $popRoutineAgg = Routineperformance::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => ['_id' => '$routineId', 'count' => ['$sum' => 1]]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 1]
            ]);
        });

        $popularRoutine = null;
        if (iterator_count($popRoutineAgg) > 0) {
            $routineId = iterator_to_array($popRoutineAgg)[0]['_id'];
            $popularRoutine = Practiceroutines::find($routineId);
        }

        return view('admin.overview', compact(
            'startDate', 'endDate',
            'totalUsers', 'totalCoaches', 'totalRoutines', 'totalPurchases',
            'userTrend', 'purchaseTrend',
            'topUser', 'popularRoutine'
        ));
    }

    public function analytics2()
    {
        /* DATE RANGE (30 days default) */
        $startDate = request('start_date')
            ? new \MongoDB\BSON\UTCDateTime(strtotime(request('start_date') . " 00:00:00") * 1000)
            : new \MongoDB\BSON\UTCDateTime(strtotime('-30 days') * 1000);

        $endDate = request('end_date')
            ? new \MongoDB\BSON\UTCDateTime(strtotime(request('end_date') . " 23:59:59") * 1000)
            : new \MongoDB\BSON\UTCDateTime(strtotime('today 23:59:59') * 1000);

        /* -----------------------------------------
           1) DAU (Daily Active Users)
        ----------------------------------------- */
        $dau = Routineperformance::raw(function ($collection) use ($startDate, $endDate) {
            return $collection->distinct('userId', [
                'createdAt' => ['$gte' => $startDate, '$lte' => $endDate]
            ]);
        });

        $dailyActiveCount = count($dau);

        /* -----------------------------------------
           2) WAU (Weekly Active Users)
        ----------------------------------------- */
        $weekStart = new \MongoDB\BSON\UTCDateTime(strtotime('-7 days') * 1000);

        $wau = Routineperformance::raw(function ($collection) use ($weekStart, $endDate) {
            return $collection->distinct('userId', [
                'createdAt' => ['$gte' => $weekStart, '$lte' => $endDate]
            ]);
        });

        $weeklyActiveCount = count($wau);

        /* -----------------------------------------
           3) MAU (Monthly Active Users)
        ----------------------------------------- */
        $monthStart = new \MongoDB\BSON\UTCDateTime(strtotime('-30 days') * 1000);

        $mau = Routineperformance::raw(function ($collection) use ($monthStart, $endDate) {
            return $collection->distinct('userId', [
                'createdAt' => ['$gte' => $monthStart, '$lte' => $endDate]
            ]);
        });

        $monthlyActiveCount = count($mau);

        /* -----------------------------------------
           4) TOP 5 COACHES (by number of sessions)
        ----------------------------------------- */
        $topCoaches = Userpurchases::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' =>
                    [
                        '_id' => '$coachId',
                        'count' => ['$sum' => 1],
                        'revenue' => ['$sum' => '$total_price']
                    ]
                ],
                ['$sort' => ['count' => -1]],
                ['$limit' => 5]
            ]);
        });

        /* -----------------------------------------
           5) Pose Heatmap (Most used poses)
        ----------------------------------------- */
        $poseAgg = Practiceroutines::raw(function ($collection) {
            return $collection->aggregate([
                ['$unwind' => '$poseslist'],
                ['$group' =>
                    ['_id' => '$poseslist.posesId', 'count' => ['$sum' => 1]]
                ],
                ['$sort' => ['count' => -1]],
                ['$limit' => 10]
            ]);
        });

        /* -----------------------------------------
           6) Routine Difficulty Distribution
        ----------------------------------------- */
        $difficulty = Yogaposeslevels::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' =>
                    ['_id' => '$levelname', 'count' => ['$sum' => 1]]
                ]
            ]);
        });

        /* -----------------------------------------
           7) Package Revenue Breakdown
        ----------------------------------------- */
        $packageRevenue = Userpurchases::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' =>
                    [
                        '_id' => '$packageId',
                        'total' => ['$sum' => '$total_price']
                    ]
                ]
            ]);
        });

        return view(
            'admin.advanced',
            compact(
                'dailyActiveCount',
                'weeklyActiveCount',
                'monthlyActiveCount',
                'topCoaches',
                'poseAgg',
                'difficulty',
                'packageRevenue'
            )
        );
    }


}
