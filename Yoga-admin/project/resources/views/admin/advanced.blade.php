@extends('layouts.admin')
@section('content')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Breadcrumb --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex justify-content-between">
                        <h4 class="font-size-18">Advanced Analytics</h4>

                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Analytics 2.0</li>
                        </ol>
                    </div>
                </div>
            </div>


            {{-- USER ACTIVITY (DAU / WAU / MAU) --}}
            <div class="row">

                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h5>Daily Active Users</h5>
                            <h2>{{ $dailyActiveCount }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h5>Weekly Active Users</h5>
                            <h2>{{ $weeklyActiveCount }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h5>Monthly Active Users</h5>
                            <h2>{{ $monthlyActiveCount }}</h2>
                        </div>
                    </div>
                </div>

            </div>


            {{-- TOP COACHES --}}
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Top 5 Coaches (Sessions)</h5>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Coach</th>
                                    <th>Sessions</th>
                                    <th>Revenue</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topCoaches as $coach)
                                    @php $c = \App\Models\Coach::find($coach->_id); @endphp

                                    <tr>
                                        <td>{{ $c ? $c->name : 'Unknown Coach' }}</td>
                                        <td>{{ $coach->count }}</td>
                                        <td>$coach->revenue</td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>


            {{-- POSE HEATMAP --}}
            <div class="row mt-4">
                <div class="col-xl-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Top 10 Most Used Poses</h5>
                            <canvas id="poseChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- DIFFICULTY DISTRIBUTION --}}
                <div class="col-xl-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Routine Difficulty Breakdown</h5>
                            <canvas id="difficultyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>


            {{-- PACKAGE REVENUE PIE CHART --}}
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5>Revenue by Packages</h5>
                            <canvas id="packageRevenueChart"></canvas>
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
/* POSE HEATMAP */
new Chart(document.getElementById("poseChart"), {
    type: 'bar',
    data: {
        labels: [
            @foreach($poseAgg as $p)
                "{{ \App\Models\Practiceyogaposes::find($p->_id)->name ?? 'Unknown' }}",
            @endforeach
        ],
        datasets: [{
            label: "Used",
            data: [
                @foreach($poseAgg as $p)
                    {{ $p->count }},
                @endforeach
            ]
        }]
    }
});

/* DIFFICULTY PIE */
new Chart(document.getElementById("difficultyChart"), {
    type: 'pie',
    data: {
        labels: [
            @foreach($difficulty as $d)
                "{{ $d->_id }}",
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($difficulty as $d)
                    {{ $d->count }},
                @endforeach
            ]
        }]
    }
});

/* PACKAGE REVENUE */
new Chart(document.getElementById("packageRevenueChart"), {
    type: 'doughnut',
    data: {
        labels: [
            @foreach($packageRevenue as $pr)
                "{{ \App\Models\Packages::find($pr->_id)->name ?? 'Package' }}",
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($packageRevenue as $pr)
                    {{ $pr->total }},
                @endforeach
            ]
        }]
    }
});
</script>
@endsection
