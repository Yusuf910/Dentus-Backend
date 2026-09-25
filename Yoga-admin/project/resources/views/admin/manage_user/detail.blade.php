@extends('layouts.admin')

@section('content')

<div class="main-content">
<div class="page-content">
<div class="container-fluid">

    <!-- BREADCRUMB -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">User Details</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.user_list') }}">Users List</a>
                        </li>
                        <li class="breadcrumb-item active">User Details</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>

    <!-- USER INFO -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Basic Information</h5>

            <div class="row">
                <div class="col-md-3">
                    <img src="{{ $user->image }}" width="140" class="rounded" style="object-fit:cover;">
                </div>
                <div class="col-md-9">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
                    <p><strong>Gender:</strong> {{ $user->gender }}</p>
                    <p><strong>AddedOn:</strong> {{ \Carbon\Carbon::parse($user['createdAt'])->format('d M Y h:i a') }}</p>
                    <p><strong>Status:</strong>
                        @if($user->status == 1)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- PACKAGE PURCHASE DETAILS -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Package Purchases</h5>

            @if(count($packages) == 0)
                <p class="text-muted">No package purchased.</p>
            @else
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#ID</th>
                             {{-- <th>Order Number</th> --}}
                             <th>User Name</th>
                             {{-- <th>User Type</th> --}}
                             <th>Package Name</th>
                             <th>Transaction ID</th>
                             <th>Price</th>
                             <th>Tax Price</th>
                             <th>Total Price</th>
                             <th>Expiry Date</th>
                             <th>Status</th>
                             <th>Created At</th>
                             {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        @foreach ($packages as $a)
                            <tr>
                               <td>{{ $i }}</td>
                               {{-- <td>{{ $a->ordernumber ?? 'N/A' }}</td> --}}
                               @php
                                        $username = \App\Models\User::where('_id', $a->userId)->first();
                                        $package = \App\Models\Packageusers::where('_id', $a->packageId)->first();
                               @endphp
                               <td>{{ $username->name ?? 'N/A' }}</td>
                               {{-- <td>{{ $a->user_type == 1 ? 'User' : 'Other' }}</td> --}}
                               <td>{{ $package->name ?? 'N/A' }}</td>
                               <td>{{ $a->transaction_id ?? 'N/A' }}</td>
                               @php
                               $price = isset($a->price) ? (float) (is_object($a->price) ? $a->price->__toString() : $a->price) : 0;
                               $taxPrice = isset($a->tax_price) ? (float) (is_object($a->tax_price) ? $a->tax_price->__toString() : $a->tax_price) : 0;
                               $totalPrice = isset($a->total_price) ? (float) (is_object($a->total_price) ? $a->total_price->__toString() : $a->total_price) : 0;
                               @endphp
                               <td>{{ number_format($price, 0) }}</td>
                               <td>{{ number_format($taxPrice, 0) }}</td>
                               <td>{{ number_format($totalPrice, 0) }}</td>
                               <td>{{ !empty($a->expiry_date) ? \Carbon\Carbon::parse($a->expiry_date)->format('d-m-Y') : 'N/A' }}</td>
                               <td>
                                     <span class="btn {{ $a->status == 1 ? 'btn-primary btn-sm' : 'btn-danger btn-sm' }}">
                                        {{ $a->status == 1 ? 'Active' : 'Inactive' }}
                                     </span>
                               </td>
                               <td>
                                  <ul class="table-ul">        
                                  <li>Date: {{date('d/m/Y', strtotime($a->createdAt ?? "" ))}}</li>
                                  <li>Time: {{date('h:i:s A', strtotime($a->createdAt ?? "" ))}}</li>                            
                                  </ul>
                               </td>
                               {{-- <td>
                                     <a href="{{ route($editurl, $a->_id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                        <span class="mdi mdi-pencil d-block font-size-12"></span>
                                     </a>
                                     <button id="modal-danger" data-userid="{{ $a->_id }}" type="button"
                                        class="btn btn-danger btn-sm waves-effect waves-light sddel"
                                        data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                        <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                     </button>
                               </td> --}}
                            </tr>
                            <?php $i++; ?>
                         @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- ROUTINES CREATED -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Practice Routines</h5>

            @if(count($routines) == 0)
                <p class="text-muted">No routines created.</p>
            @else
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Poses</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($routines as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>
                                <ul>
                                    @foreach($r->poseslist as $p)
                                        @php
                                            $pose = $poses[(string)$p['posesId']];
                                            $level = $levels[(string)$p['levelId']];
                                        @endphp
                                        <li>
                                            <strong>{{ $pose->name }}</strong><br>
                                            Level: {{ $level->levelname }}<br>
                                            Duration: {{ $p['duration'] }} min
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @if($r->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Archived</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>

    <!-- PERFORMANCE HISTORY -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Routine Performance History</h5>

            @if(count($performances) == 0)
                <p>No performance recorded.</p>
            @else
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Duration</th>
                            <th>Poses</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performances as $p)
                        <tr>
                            <td>{{ date('d M Y', strtotime($p->createdAt)) }}</td>
                            <td>{{ $p->score }}</td>
                            <td>{{ $p->timeDuration }} min</td>
                            <td>{{ $p->posesCount }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>

    <!-- GRAPHS -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>Monthly Performance Graph</h5>
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Yearly Performance Graph</h5>
            <canvas id="yearlyChart"></canvas>
        </div>
    </div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const monthlyLabels = {!! json_encode($monthlyGraph->keys()) !!};
const monthlyScores = {!! json_encode($monthlyGraph->pluck('avg_score')) !!};

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Avg Score',
            data: monthlyScores,
            borderWidth: 3
        }]
    }
});

const yearlyLabels = {!! json_encode($yearlyGraph->keys()) !!};
const yearlyScores = {!! json_encode($yearlyGraph->pluck('avg_score')) !!};

new Chart(document.getElementById('yearlyChart'), {
    type: 'bar',
    data: {
        labels: yearlyLabels,
        datasets: [{
            label: 'Avg Score',
            data: yearlyScores,
            borderWidth: 3
        }]
    }
});
</script>

@endsection
