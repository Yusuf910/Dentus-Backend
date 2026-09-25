@extends('layouts.admin')
@section('content')
<?php 
$date_filter = '';
$start_date = '';
$end_date = '';
if (isset($_GET['date_filter'])) 
{
   $date_filter = $_GET['date_filter'];
}

if (isset($_GET['start_date'])) 
{
   $start_date = $_GET['start_date'];
}
if (isset($_GET['end_date'])) 
{
   $end_date = $_GET['end_date'];
}


$exportUrl = route('admin.appoint.myappointment') . '?exp=export';

$params = [
    'name'       => $_GET['name'] ?? '',
    'email'      => $_GET['email'] ?? '',
    'mobile'     => $_GET['mobile'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date'   => $_GET['end_date'] ?? '',
    'status'     => $_GET['status'] ?? '',
];

$queryString = http_build_query(array_filter($params));

if (!empty($queryString)) {
    $exportUrl .= '&' . $queryString;
}

?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item"><a href="{{route('admin.appoint.mystats')}}">Stats </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">{{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
      	@include('includes.admin.form-success') 
         <div class="page-table-header mb-2">
            <div class="row align-items-center">
               <div class="col">
                  <div class="doctor-table-blk">
                     <h3>Best Patients</h3>
                  </div>
               </div>
              <div class="col-auto text-end float-end ms-auto download-grp">
                   <div class="input-block mb-0 me-2">
                       <select class="form-control" id="dateFilter1">
                           <option value="">Select</option>
                           <option {{$date_filter == auth()->user()->id ? 'selected' : ''}} value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                           @if($u->count() > 0)
                           @foreach($u as $ab1)
                              <option {{$date_filter == $ab1->id ? 'selected' : ''}} value="{{$ab1->id}}">{{$ab1->name}}</option>
                           @endforeach
                           @endif
                       </select>
                   </div>
                   <div class="add-group">
                     <a href="{{ route('admin.appoint.vgraph') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-12">
            <div class="doctor-list-blk">
               <div class="row">
                  <div class="col-md-4">
                     <div class="doctor-widget border-right-bg">
                        <div class="doctor-box-icon flex-shrink-0">
                           <img src="assets/img/icons/doctor-dash-02.svg" alt>
                        </div>
                        <div class="doctor-content dash-count flex-grow-1">
                           <h4><span class="counter-up">{{$res['total']}}</span>
                              <span></span><span class="status-green"></span>
                           </h4>
                           <h5>Total</h5>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="doctor-widget border-right-bg">
                        <div class="doctor-box-icon flex-shrink-0">
                           <img src="assets/img/icons/doctor-dash-02.svg" alt>
                        </div>
                        <div class="doctor-content dash-count flex-grow-1">
                           <h4><span class="counter-up">{{$res['online']['count']}}</span><span></span></h4>
                           <h5>Online</h5>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="doctor-widget border-right-bg">
                        <div class="doctor-box-icon flex-shrink-0">
                           <img src="assets/img/icons/doctor-dash-02.svg" alt>
                        </div>
                        <div class="doctor-content dash-count flex-grow-1">
                           <h4><span class="counter-up">{{$res['in_person']['count']}}</span><span></span></h4>
                           <h5>In Person</h5>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="card">
                     <div class="card-body">
                        <div class="chart-title patient-visit">
                           <h4>Appointments Chart</h4>
                           
                        </div>
                        <canvas id="appointmentChart"></canvas>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
        // Convert PHP array to JSON
        const data = @json($res);

        // Extract months and appointment data
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const onlineData = [];
        const inClinicData = [];

        for (let i = 1; i <= 12; i++) {
            if (data.graph[i]) {
                onlineData.push(parseInt(data.graph[i].online) || 0);
                inClinicData.push(parseInt(data.graph[i].in_clinic) || 0);
            } else {
                onlineData.push(0);
                inClinicData.push(0);
            }
        }

        // Chart Configuration
        const ctx = document.getElementById('appointmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Online Consultation',
                        data: onlineData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)', // Blue
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'In Person Visit',
                        data: inClinicData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)', // Green
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#dateFilter1').change(function () {
            let selectedValue = $(this).val();
            if (selectedValue) {
                window.location.href = "{{ route('admin.appoint.vgraph') }}?date_filter=" + selectedValue;
            }
        });
    });
</script>
@endsection
@endsection