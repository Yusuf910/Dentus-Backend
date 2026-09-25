@extends('layouts.admin')
@section('content')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Analytics Overview</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Analytics</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="row mb-3">
                <div class="col-md-4">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ request('start_date') }}">
                </div>

                <div class="col-md-4">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ request('end_date') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Apply Filter</button>
                </div>
            </form>


            {{-- KPI CARDS --}}
            <div class="row">

                <div class="col-xl-3 col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Total Users</h5>
                            <h2>{{ $totalUsers }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Total Coaches</h5>
                            <h2>{{ $totalCoaches }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Total Practice Routines</h5>
                            <h2>{{ $totalRoutines }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Total Purchases</h5>
                            <h2>{{ $totalPurchases }}</h2>
                        </div>
                    </div>
                </div>

            </div>


            {{-- TOP PERFORMERS --}}
            <div class="row mt-4">

                <div class="col-xl-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Most Active User</h5>
                            <p>
                                @if($topUser)
                                    {{ $topUser->name }} ({{ $topUser->email }})
                                @else
                                    No data available
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Most Performed Routine</h5>
                            <p>
                                @if($popularRoutine)
                                    {{ $popularRoutine->name }}
                                @else
                                    No data available
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

            </div>



            {{-- GRAPHS --}}
            <div class="row mt-4">

                <div class="col-xl-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>User Registration Trend</h5>
                            <canvas id="userTrendChart" height="180"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Purchase Revenue Trend</h5>
                            <canvas id="purchaseTrendChart" height="180"></canvas>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var userLabels = [
@foreach($userTrend as $u)
    "{{ $u->_id['month'] }}/{{ $u->_id['year'] }}",
@endforeach
];

var userData = [
@foreach($userTrend as $u)
    "{{ $u->count }}",
@endforeach
];

new Chart(document.getElementById("userTrendChart"), {
    type: 'line',
    data: {
        labels: userLabels,
        datasets: [{
            label: "Users Joined",
            data: userData,
            borderWidth: 3
        }]
    }
});


var purLabels = [
@foreach($purchaseTrend as $p)
    "{{ $p->_id['month'] }}/{{ $p->_id['year'] }}",
@endforeach
];

var purData = [
@foreach($purchaseTrend as $p)
    "{{ $p->total }}",
@endforeach
];

new Chart(document.getElementById("purchaseTrendChart"), {
    type: 'bar',
    data: {
        labels: purLabels,
        datasets: [{
            label: "Revenue",
            data: purData,
            borderWidth: 3
        }]
    }
});
</script>
@endsection
