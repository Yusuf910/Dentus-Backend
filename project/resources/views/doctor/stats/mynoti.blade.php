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
         <div class="col-sm-12">
             <div class="card card-table show-entire">
                <div class="card-body">
                   

                   <div class="table-responsive">
                      <table class="table border-0 custom-table comman-table datatable mb-0">
                         <thead>
                            <tr>
                               <th>Title</th>
                               <th>Message</th>
                               <th>Date</th>
                            </tr>
                         </thead>
                         <tbody>
                            @if($list)
                            @foreach($list as $a)
                            <tr>
                               <td>{{$a->title}}</td>
                               <td>{{$a->notification}}</td>
                               <td><span class="badge badge-soft-success">{{ \Carbon\Carbon::parse($a->updated_at)->format('d/m/Y h:i A') }}</span></td>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#dateFilter1').change(function () {
            let selectedValue = $(this).val();
            if (selectedValue) {
                window.location.href = "{{ route('admin.appoint.bestpatient') }}?date_filter=" + selectedValue;
            }
        });
    });
</script>

@endsection
@endsection