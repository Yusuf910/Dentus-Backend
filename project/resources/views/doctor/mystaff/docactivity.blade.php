@extends('layouts.admin')
@section('content')
<?php 
$condition = '';
$start_date = '';
$end_date = '';
if (isset($_GET['condition'])) 
{
   $condition = $_GET['condition'];
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
         <div class="col-sm-12">
             <div class="card card-table show-entire">
                <div class="card-body">
                   <div class="page-table-header mb-2">
                      <div class="row align-items-center">
                         <div class="col">
                            <div class="doctor-table-blk">
                               <h3>Earning History</h3>
                               <div class="doctor-search-blk">
                                  <div class="add-group">
                                     <!-- <a href="{{ route('admin.appoint.earninghistory') }}" class="btn btn-primary doctor-refresh ms-2"><img src="assets/img/icons/re-fresh.svg" alt></a> -->
                                  </div>
                               </div>
                            </div>
                         </div>
                      </div>
                   </div>
                   <div class="staff-search-table">
                      <form method="GET" action="{{ route('admin.appoint.earninghistory') }}">
                         <div class="row">
                           
                            <div class="col-12 col-md-6 col-xl-3">
                               <div class="input-block local-forms">
                                  <label>Type </label>
                                  <select name="condition" class="form-control select">
                                     <option disabled selected>Select Type</option>
                                     <option {{$condition == 'last10' ? 'selected' : ''}} value="last10">Last 10</option>
                                     <option {{$condition == 'lastWeek' ? 'selected' : ''}} value="lastWeek">Last Week</option>
                                     <option {{$condition == 'lastMonth' ? 'selected' : ''}} value="lastMonth">Last Month</option>
                                  </select>
                               </div>
                            </div>
                            
                            <div class="col-12 col-md-6 col-xl-3">
                               <div class="input-block local-forms">
                                  <label>From </label>
                                  <input name="start_date" value="{{$start_date}}" class="form-control" type="date">
                               </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                               <div class="input-block local-forms">
                                  <label>To </label>
                                  <input name="end_date" value="{{$end_date}}" class="form-control" type="date">
                               </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                               <div class="doctor-submit">
                                  <button type="submit" class="btn btn-primary submit-list-form me-2">Search</button>
                               </div>
                            </div>
                         </div>
                      </form>
                   </div>
                   <div class="row d-flex justify-content-center align-items-center">
                      <div class="col-md-8">
                         <div class="treat-box mb-2 badge-soft-success">
                            <div class="user-imgs-blk">
                               <!--  <img src="assets/img/profiles/avatar-05.jpg" alt=""> -->
                               <div class="active-user-detail flex-grow-1">
                                  <h4>Total Earnings</h4>
                               </div>
                            </div>
                            <h4>&#8377; {{$response['total']}}</h4>
                         </div>
                      </div>
                   </div>
                   <div class="table-responsive">
                      <table class="table border-0 custom-table comman-table datatable mb-0">
                         <thead>
                            <tr>
                               <th>Id</th>
                               <th>Doctor Name</th>
                               <th>Treatments Performed</th>
                               <th>Revenue</th>
                            </tr>
                         </thead>
                         <tbody>
                            @if($response['data'])
                            @foreach($response['data'] as $a)
                            <tr>
                               <td>#{{$a['doctor_id']}}</td>
                               <td><div class="user-imgs-blk">
                                    <img src="assets/img/profiles/avatar-05.jpg" alt="">
                                    <div class="active-user-detail flex-grow-1">
                                       <h4>{{$a['doctordetail']->name}}</h4>
                                       <p>Cosmetic/Aesthetic Dentist</p>
                                    </div>
                                 </div>
                                </td>
                               <td>{{$a['total_bookings']}}</td>
                               <td><span class="badge badge-soft-success">&#8377; {{$a['total_revenue']}}</span></td>
                            </tr>
                            @endforeach
                            @endif
                            
                         </tbody>
                      </table>
                   </div>
                </div>
             </div>
          </div>
      </div>
   </div>
</div>
@section('js_user_page')
@endsection
@endsection