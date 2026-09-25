@extends('layouts.admin')

@section('content')
<div class="main-content">
<div class="page-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Routine Details</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.practice_routines')}}">Routine List</a></li>
                        <li class="breadcrumb-item active">Routine Details</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <h4 class="mb-3">{{ $routine->name }} {{ \Carbon\Carbon::parse($routine['createdAt'])->format('d M Y h:i a') }} (Routine Details)</h4>

    <!-- DISPLAY BASIC INFO -->
    <div class="card mb-3">
        <div class="card-body">
            <h5>User Information</h5>
            <p>Name: {{ $user->name ?? 'Unknown' }}</p>
            <p>Email: {{ $user->email ?? '-' }}</p>
        </div>
    </div>

    <!-- POSES LIST -->
    <div class="card mb-3">
        <div class="card-body">
            <h5>Routine Poses</h5>

            <ul>
                @foreach ($routine->poseslist as $p)
                    @php
                        $pose = $poses[(string)$p['posesId']] ?? null;
                        $level = $levels[(string)$p['levelId']] ?? null;
                    @endphp

                    <li class="mb-3">
                        <strong>{{ $pose->name ?? 'Pose not found' }}</strong><br>

                        Type of Tracking: {{ $pose->Type_of_tracking ?? 'N/A' }}<br>
                        Angle: {{ $pose->Angle ?? 'N/A' }}<br>
                        Symmetric: {{ $pose->symetric ?? 'N/A' }}<br>

                        Level: {{ $level->levelname ?? 'N/A' }}<br>
                        Duration: {{ $p['duration'] }} mins<br>

                        <img src="{{ $pose->image }}" width="80" class="mt-1"
                             style="border-radius:5px;object-fit:cover;">
                    </li>
                @endforeach
            </ul>

        </div>
    </div>

    <!-- PERFORMANCE HISTORY -->
    <div class="card">
        <div class="card-body">
            <h5>Performance History</h5>

            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Duration</th>
                        <th>Poses Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($history as $h)
                    <tr>
                        <td>{{ date('d M Y', strtotime($h->createdAt)) }}</td>
                        <td>{{ $h->score }}</td>
                        <td>{{ $h->timeDuration }} mins</td>
                        <td>{{ $h->posesCount }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
</div>
@endsection
