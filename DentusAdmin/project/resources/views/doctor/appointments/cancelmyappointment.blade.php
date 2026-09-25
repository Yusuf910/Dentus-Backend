@extends('layouts.admin')
@section('content')
<?php
$set = App\Models\Generalsetting::find(1);
$u = App\Models\User::where('status', 1)->orderBy('name')->get();

$name = '';
$u1 = '';
$email = '';
$mobile = '';
$start_date = '';
$end_date = '';
$status = '';
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
if (isset($_GET['status']))
{
   $status = $_GET['status'];
}
if (isset($_GET['u1']))
{
   $u1 = $_GET['u1'];
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
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Cancel Appointment List</li>
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
                          <div class="doctor-table-blk">
                             <h3>Appointment List</h3>
                             <div class="doctor-search-blk">
                                <div class="table-search-blk"></div>
                                   
                                
                                <div class="add-group">
                                   <a href="{{ route('admin.appoint.cancelmyappointment') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                </div>

                             </div>
                          </div>
                       </div>
                       <div class="col-auto text-end float-end ms-auto download-grp">
                          
                          <a href="javascript:;" class=" me-2"><img src="{{ asset('adminassets') }}/img/icons/pdf-icon-03.svg" alt></a>
                                    <a href="javascript:;"><img src="{{ asset('adminassets') }}/img/icons/pdf-icon-04.svg" alt></a>
                       </div>
                    </div>
                 
                 <div class="row align-items-center mt-3">
                    <form method="GET" action="{{ route('admin.appoint.cancelmyappointment') }}">
                                      <div class="row">
                                         <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="treatment_name" class="form-label">Doctor List</label>
                                             <select name="u1" class="form-control">
                                                <option value="">Select</option>
                                                @if($u->count() > 0)
                                                @foreach($u as $ab1)
                                                   <option {{$u1 == $ab1->id ? 'selected' : ''}} value="{{$ab1->id}}">{{$ab1->name}}</option>
                                                @endforeach
                                                @endif
                                             </select>
                                         </div>
                                      </div>
                                         <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="treatment_name" class="form-label">Name</label>
                                             <input type="text" class="form-control" name="name" placeholder="User/Doctor" value="{{$name}}">
                                             </div>
                                         </div>
                                         <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="average_duration" class="form-label">Email</label>
                                             <input type="text" class="form-control" name="email" placeholder="email" value="{{$email}}">
                                             </div>
                                         </div>
                                         <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="mobile" class="form-label">Mobile</label>
                                             <input type="text" class="form-control" name="mobile" placeholder="mobile" value="{{$mobile}}">
                                             </div>
                                         </div>
                                         <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="mobile" class="form-label">Start Date</label>
                                             <input type="date" class="form-control" name="start_date" placeholder="start_date" value="{{$start_date}}">
                                             </div>
                                         </div>
                                         <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="mobile" class="form-label">End Date</label>
                                             <input type="date" class="form-control" name="end_date" placeholder="end_date" value="{{$end_date}}">
                                             </div>
                                         </div>
                                          <div class="col-md-2">
                                             <div class="mb-3">
                                             <label for="mobile" class="form-label"></label>
                                             <button type="submit" class="btn btn-success" alt="alert" id="sa-success">Search</button>
                                             </div>
                                         </div>
                                      </div>
                                   </form>
                 </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table  mb-0">
                        <thead>
                           <tr>
                              <th>User Details</th>
                              <th>Type</th>
                              <th>Details</th>
                              <th>Refund Details</th>
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
                                          <a href="{{route('admin.myuser.paitentprofile',$a->user_id)}}">{{$a->member->name}} {{$a->member->last_name}}</a></li>
                                       <li>{{$a->member->mobile}}</li>
                                       
                                       <li>{{$a->member->relation}}</li>
                                       <li>{{$a->member->gender}}</li>
                                    </ul>

                                 @else
                                    <ul>
                                       <li>
                                          <a href="{{route('admin.myuser.paitentprofile',$a->user_id)}}">{{$a->user->name}} {{$a->user->last_name}}</a></li>
                                       <li>{{$a->user->mobile}}</li>
                                       <li>{{$a->user->relation}}</li>
                                       <li>{{$a->user->gender}}</li>
                                    </ul>
                                 @endif
                              </td>
                              <td><span class="badge badge-soft-success">{{$a->consultation_type}}</span></td>
                              <td>
                                    <ul>
                                       <li>Symptoms: {{$a->symptoms}}</li>
                                       <li>Treatment{{$a->treatment_name}}</li>

                                    </ul>
                              </td>
                              <td>
                                    @if($a->refund_status == 1)
                                    <ul>
                                       @php
                                       $f = json_decode($a->refund_details);
                                       $fees = '';
                                       if($set->refund_deduction_percent > 0 && isset($f->amount)) {
                                          $fees = ($f->amount/100)*$set->refund_deduction_percent/100;
                                       }
                                       @endphp
                                       <li>Refund Amount: {{$f->amount/100 ?? ''}}</li>
                                       <li>Refund ID:{{$f->id ?? ''}}</li>
                                       <li>Payment Fees: {{$fees}}</li>
                                    </ul>
                                    @endif
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
                                @elseif($a->status == 4)
                                 <a href="{{ route('admin.appoint.complete_appoint', $a->id) }}" class="btn btn-success my-2" onclick="return confirm('Mark booking as complete?')">Complete</a>

                                 <a href="{{ route('admin.appoint.cancel_appoint', $a->id) }}" class="btn btn-danger my-2"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
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
                           @else
                           No Booking Found!
                           @endif

                        </tbody>
                     </table>
                     {{-- {!! $data->links(); !!} --}}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

</div>
@endsection
