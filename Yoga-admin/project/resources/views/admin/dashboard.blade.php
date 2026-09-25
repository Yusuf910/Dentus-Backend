@extends('layouts.admin')
@section('content')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- ---------------- Page Title / Breadcrumb ---------------- --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Dashboard</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ------------------ MAIN COUNT CARDS ----------------------- --}}
            <div class="row">

                {{-- Total Users --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.user_list')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total User</span>
                                <h3>{{ $userCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Coaches --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.coach_list')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Coach</span>
                                <h3>{{ $coachCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Packages --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.package_users')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Packages</span>
                                <h3>{{ $packageCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Categories --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.yoga_categories')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Category</span>
                                <h3>{{ $catCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Poses --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.yoga_poses')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Poses</span>
                                <h3>{{ $poseCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Pose Levels --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.yoga_pose_levels')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Pose Levels</span>
                                <h3>{{ $poselCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Practice Routines --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.practice_routines')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Practice Routine</span>
                                <h3>{{ $pracCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Package Purchase --}}
                <div class="col-xl-3 col-md-6">
                    <a href="{{route('admin.purchase_history')}}">
                        <div class="card card-h-100 bg-temple-white">
                            <div class="card-body">
                                <span class="d-block mb-2 font-size-15">Total Package Purchase</span>
                                <h3>{{ $purCount }}</h3>
                            </div>
                        </div>
                    </a>
                </div>

            </div>


            {{-- ------------------ ADVANCED ANALYTICS ------------------ --}}
            <h4 class="mt-4">Analytics & Insights</h4>
            <div class="row">

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100 bg-temple-white">
                        <div class="card-body">
                            <span class="d-block mb-2 font-size-15">Today's New Users</span>
                            <h3>{{ $todayUsers }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100 bg-temple-white">
                        <div class="card-body">
                            <span class="d-block mb-2 font-size-15">Today's Purchases</span>
                            <h3>{{ $todayPurchases }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100 bg-temple-white">
                        <div class="card-body">
                            <span class="d-block mb-2 font-size-15">Total Routines Performed</span>
                            <h3>{{ $totalRoutinePerformed }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-h-100 bg-temple-white">
                        <div class="card-body">
                            <span class="d-block mb-2 font-size-15">Average Score</span>
                            <h3>{{ number_format($avgScore, 2) }}</h3>
                        </div>
                    </div>
                </div>

            </div>


            {{-- ---------------- Popular Items ---------------- --}}
            <div class="row mt-3">

                <div class="col-xl-6">
                    <div class="card card-h-100 bg-temple-white">
                        <div class="card-body">
                            <h5>Most Popular Pose</h5>
                            <p>
                                @if($popularPose)
                                    {{ $popularPose->name }}
                                @else
                                    No data available.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card card-h-100 bg-temple-white">
                        <div class="card-body">
                            <h5>Most Performed Routine</h5>
                            <p>
                                @if($popularRoutine)
                                    <p>{{ $popularRoutine->name }}</p>
                                @else
                                    <p>No data available.</p>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>


            {{-- ------------------ MONTHLY PERFORMANCE GRAPH ------------------ --}}
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Monthly Routine Performance</h5>
                            <canvas id="routineChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <script>document.write(new Date().getFullYear())</script> © Yoga.
                </div>
            </div>
        </div>
    </footer>

</div>

@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let labels = [
    @foreach($monthlyPerformance as $mp)
        "{{ $mp->_id['month'] }}/{{ $mp->_id['year'] }}",
    @endforeach
];

let scores = [
    @foreach($monthlyPerformance as $mp)
        "{{ $mp->totalScore }}",
    @endforeach
];

const ctx = document.getElementById('routineChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Avg Score',
            data: scores,
            borderWidth: 3,
        }]
    }
});
</script>
@endsection
