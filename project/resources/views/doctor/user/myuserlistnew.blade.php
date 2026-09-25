@extends('layouts.admin')
@section('content')
<?php 
$name = '';
$email = '';
$mobile = '';
$start_date = '';
$end_date = '';
$status = '';
if (isset($_GET['name'])) 
{
   $name = $_GET['name'];
}
if (isset($_GET['start_date'])) 
{
   $start_date = $_GET['start_date'];
}
if (isset($_GET['end_date'])) 
{
   $end_date = $_GET['end_date'];
}

$exportUrl = route('admin.myuser.myuserlist') . '?exp=export';

$params = [
    'name'       => $_GET['name'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date'   => $_GET['end_date'] ?? '',
];

$queryString = http_build_query(array_filter($params));

if (!empty($queryString)) {
    $exportUrl .= '&' . $queryString;
}

?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Patient List</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            @include('includes.admin.form-success') 
               @if (count($errors) > 0)
              <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                   @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                   @endforeach
                </ul>
              </div>
            @endif
            <div class="card card-table show-entire">
               <div class="card-body">
                  <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <h3>Patient List</h3>
                              
                        
                       
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table id="userTable" class="table border-0 custom-table comman-table  mb-0">
                        <thead>
                           <tr>
                              <th>Img</th>
                              <th>Name</th>
                              <th>Mobile</th>
                              <th>User Type</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                        	@if($userlist)
                            @foreach($userlist as $a)
                                <tr>
                                    <td class="text-center">
                                        @if($a['is_member'] == true)
                                          <a href="{{route('admin.myuser.paitentprofile',[1,$a['member_details']['id'] ?? 0])}}">
                                             <img src="{{asset('project/public/member_images/')}}/{{$a['member_details']['image'] ?? 'default.png'}}" 
                                             class="rounded-circle" width="50" height="50" alt="Profile Image">
                                          </a>
                                        @else
                                        <a href="{{route('admin.myuser.paitentprofile',[0,$a['user_details']['id']])}}">
                                          <img src="{{asset('content/user/'.$a['user_details']['image'])}}" 
                                             class="rounded-circle" width="50" height="50" alt="Profile Image">
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($a['is_member'] == true)
                                            {{ $a['member_details']['name'] ?? 'N/A' }} {{ $a['member_details']['last_name'] ?? '' }}
                                        @else
                                            {{ $a['user_details']['name'] ?? 'N/A' }} {{ $a['user_details']['last_name'] ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($a['is_member'] == true)
                                            {{ $a['member_details']['mobile'] ?? 'N/A' }}
                                        @else
                                            {{ $a['user_details']['mobile'] ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($a['is_member'])
                                            <span class="badge badge-primary">
                                                <i class="fas fa-user-friends"></i> Member
                                            </span>
                                        @else
                                            <span class="badge badge-success">
                                                <i class="fas fa-user"></i> User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="tel:{{ $a['is_member'] ? $a['member_details']['mobile'] ?? '#' : $a['user_details']['mobile'] ?? '#' }}" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-phone"></i> Call
                                        </a>
                                        @if($a['is_member'] == true)
                                          <a href="{{route('admin.myuser.paitentprofile',[1,$a['member_details']['id'] ?? 0])}}">
                                          @else
                                        <a href="{{route('admin.myuser.paitentprofile',[0,$a['user_details']['id']])}}">@endif<button  type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button></a>
                                    </td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
</script>
@endsection
@endsection