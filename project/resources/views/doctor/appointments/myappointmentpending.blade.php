@extends('layouts.admin')
@section('content')

<?php 
$name = '';
$email = '';
$mobile = '';
$start_date = '';
$end_date = '';
$doctor_id = '';
if (isset($_GET['name'])) 
{
   $name = $_GET['name'];
}
if (isset($_GET['email'])) 
{
   $email = $_GET['email'];
}
if (isset($_GET['mobile'])) 
{
   $mobile = $_GET['mobile'];
}
if (isset($_GET['start_date'])) 
{
   $start_date = $_GET['start_date'];
}
if (isset($_GET['end_date'])) 
{
   $end_date = $_GET['end_date'];
}
if (isset($_GET['doctor_id'])) 
{
   $doctor_id = $_GET['doctor_id'];
}

$exportUrl = route('admin.appoint.myappointmentpending') . '?exp=export';

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
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Appointment List</li>
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
                        <div class="col">
                           <div class="doctor-table-blk1 d-flex justify-content-between">
                              <h3>Appointment List</h3>
                              <div class="doctor-search-blk1">
                                 <div class="table-search-blk">
                                    <form method="GET" action="{{ route('admin.appoint.myappointmentpending') }}">
                                       <div class="d-flex align-items-center">
                                          <!-- <div class="col-md-2">
                                              <div class="mb-3">
                                              <label for="treatment_name" class="form-label">Name</label>
                                              <input type="text" class="form-control" name="name" placeholder="Name" value="{{$name}}">
                                              </div>
                                          </div>  --> 
                                         <!--  <div class="col-md-2">
                                              <div class="mb-3">
                                              <label for="average_duration" class="form-label">Email</label>
                                              <input type="text" class="form-control" name="email" placeholder="email" value="{{$email}}">
                                              </div>
                                          </div>  -->
                                         <!--  <div class="col-md-2">
                                              <div class="mb-3">
                                              <label for="mobile" class="form-label">Mobile</label>
                                              <input type="text" class="form-control" name="mobile" placeholder="mobile" value="{{$mobile}}">
                                              </div>
                                          </div>   -->
                                          <!-- <div class="col-md-2">
                                              <div class="mb-3">
                                              <label for="mobile" class="form-label">Start Date</label>
                                              <input type="date" class="form-control" name="start_date" placeholder="start_date" value="{{$start_date}}">
                                              </div>
                                          </div> -->
                                          <!-- <div class="col-md-2">
                                              <div class="mb-3">
                                              <label for="mobile" class="form-label">End Date</label>
                                              <input type="date" class="form-control" name="end_date" placeholder="end_date" value="{{$end_date}}">
                                              </div>
                                          </div> -->
                                         
                                         
                                              <div class="mb-3">
                                              <label for="average_duration" class="form-label">Doctor</label>
                                              <select name="doctor_id" class="form-control">
                                                <option value="">Select</option>
                                                <option  {{$doctor_id == auth()->user()->id ? 'selected' : ''}} value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                                                @if($u->count() > 0)
                                                @foreach($u as $ab1)
                                                   <option  {{$doctor_id == $ab1->id ? 'selected' : ''}} value="{{$ab1->id}}">{{$ab1->name}}</option>
                                                @endforeach
                                                @endif
                                                </select>
                                              </div>
                                     
                                           
                                          <button type="submit" class="btn btn-success mt-2 ms-2" alt="alert" id="sa-success">Search</button>
                                      

                                        
                                          <div class="add-group mt-2">
                                    <a href="{{ route('admin.appoint.myappointmentpending') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                           
                                          
                                       </div>
                                    </form>
                                 </div>
                                 
                                 
                              </div>
                           </div>
                        </div>

                        
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table  mb-0">
                        <thead>
                           <tr>
                              <th>User Details</th>
                              <th>Doctor Details</th>
                              <th>Type</th>
                              <th>Details</th>
                              <th>Date & Time</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($appointmentsWithDoctor->count() > 0)
                           @foreach($appointmentsWithDoctor as $a)
                           <?php //dd($a); ?>
                           <tr>
                              <td class="profile-image">
                                 @if($a->member)
                                    <ul>
                                       <li>
                                          <a href="#"><img width="28" height="28" src="{{$a->member->image}}" class="rounded-circle m-r-5" alt> {{$a->member->name}} {{$a->member->last_name}}</a></li>
                                       <li>{{$a->member->mobile}}</li>
                                       <!-- <li>{{ $a->member->dob ? \Carbon\Carbon::parse($a->member->dob)->age : 'N/A' }}</li> -->
                                       <li>{{$a->member->relation}}</li>
                                       <li>{{$a->member->gender}}</li>
                                    </ul>
                                    
                                 @else
                                    <ul>
                                       <li>
                                          <a href="#"><img width="28" height="28" src="{{$a->user->image}}" class="rounded-circle m-r-5" alt> {{$a->user->name}} {{$a->user->last_name}}</a></li>
                                       <li>{{$a->user->mobile}}</li>
                                       <!-- <li>{{ $a->user->date_of_birth ? \Carbon\Carbon::parse($a->user->date_of_birth)->age : 'N/A' }}</li> -->
                                       <li>{{$a->user->relation}}</li>
                                       <li>{{$a->user->gender}}</li>
                                    </ul>
                                 @endif
                              </td>
                              <td class="profile-image">
                                 <a href="#"><div class="user-imgs-blk">
                                    <img src="{{asset('content/doctor')}}/{{$a->doctordetail->image ?? 'default.png'}}" alt="">
                                    <div class="active-user-detail flex-grow-1">
                                       <h4>{{$a->doctordetail->name ?? ''}}</h4>
                                    </div>
                                 </div></a>
                              </td>
                              <td><span class="badge badge-soft-success">{{$a->consultation_type}}</span></td>
                              <td>
                                    <ul>
                                       <li>Symptoms: {{$a->symptoms}}</li>
                                       <li>Treatment{{$a->treatment_name}}</li>
                                       
                                    </ul>
                              </td>
                              <td><span class="badge badge-soft-success">{{date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time))}}</span></td>
                              <td>
                                 @if($a->status==0)
                                    <span class="btn btn-primary my-2">Pending </span>
                                @elseif($a->status==1)
                                    <span class="btn btn-success my-2">completed </span>
                                @elseif($a->status==2)
                                    <span class="btn btn-danger my-2">cancel </span>
                                @elseif($a->status==3)
                                    <span class="btn btn-warning my-2">reject </span>
                                @elseif($a->status==4)
                                    <span class="btn btn-info my-2">accept </span>
                                @elseif($a->status==5)
                                    <span class="btn btn-primary my-2">Payment Failed </span>
                                @elseif($a->status==6)
                                    <span class="btn btn-primary my-2">Refunded </span>
                                @endif
                              </td>
                              <td>
                                 <a href="{{ route('admin.appoint.myappointmentdetails',$a->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                 
                              </td>
                           </tr>
                           @endforeach
                           @endif
                           
                        </tbody>
                     </table>
                     {{ $data->links('pagination.custom') }}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   
</div>
@endsection