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
                               <h3>{{$title}}</h3>
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
                      <form method="GET" action="{{ route('admin.appoint.treatmentperfom',$doctorId) }}">
                         <div class="row">
                           
                            <div class="col-12 col-md-6 col-xl-3">
                               <div class="input-block local-forms">
                                  <label>Type </label>
                                  <select name="condition" class="form-control select">
                                     <option value="" selected>Select Type</option>
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
                            <div class="add-group">
                              <a href="{{ route('admin.appoint.treatmentperfom') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                           </div>
                         </div>
                      </form>
                   </div>
                   
                   <div class="table-responsive">
                      <table class="table border-0 custom-table comman-table datatable mb-0">
                         <thead>
                            <tr>
                               <th>Name</th>
                               <th>Occurence</th>
                            </tr>
                         </thead>
                         <tbody>
                            @if($response['data'])
                            @foreach($response['data'] as $a)
                            <tr>
                               <td><h4>{{$a['treatment_name']}}</h4></td>
                               <td>{{$a['total_bookings']}}</td>
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