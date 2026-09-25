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
                   
                   <div class="table-responsive">
                      <table class="table border-0 custom-table comman-table mb-0">
                         <thead>
                            <tr>
                               <th>#</th>
                               <th>Name</th>
                               <th>Date</th>
                               <th>Points</th>
                               
                            </tr>
                         </thead>
                         <tbody>
                            @if($appointmentsWithLoyality)
                            @foreach($appointmentsWithLoyality as $a)
                            <tr>
                               <td>#{{$a['id']}}</td>
                               <td>{{$a->userdetail->name.' '.$a->userdetail->last_name}}</td>
                               <td>{{date('d/m/Y | h:i:s A', strtotime($a->created_at ?? "" ))}}</td>

                               {{-- <td>{{$a->created_at}}</td> --}}
                               <td><span class="badge badge-soft-success">{{$a->loyalitydetail->amount}}</span></td>
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