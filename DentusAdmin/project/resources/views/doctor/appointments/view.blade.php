@extends('layouts.admin')
@section('content')
<?php
$set = App\Models\Generalsetting::find(1);
$u = App\Models\User::where('status', 1)->orderBy('name')->get();
?>
<div class="page-wrapper">
   <div class="content">
      <div class="row">
         <div class="col-sm-7 col-6">
            <ul class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{$listurl}}">Appointment </a></li>
               <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
               <li class="breadcrumb-item active">Appointment Details</li>
            </ul>
         </div>
         <div class="col-sm-5 col-6 text-end m-b-30">
            <!-- <a href="#" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Edit Profile</a> -->
         </div>
      </div>
      <?php 


         if ($a) {
             if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {
                 $member = App\Models\UserModels\Member::find($a->member_id);
                 if ($member) {
                     $member->image = asset('project/public/member_images/' . $member->image);
                     $a->member = $member;
                 } else {
                     $a->member = null;
                 }

                 $userMedical_xray = App\Models\UserModels\MedicalRecord::where('user_id', $a->user_id)
                     ->where('member_id', $a->member_id)
                     ->where('type', "xray")
                     ->where('status', 1)
                     ->get();
                 $userMedical_lab = App\Models\UserModels\MedicalRecord::where('user_id', $a->user_id)
                     ->where('member_id', $a->member_id)
                     ->where('type', "lab")
                     ->where('status', 1)
                     ->get();
                 $userMedical_prescription = App\Models\UserModels\MedicalRecord::where('user_id', $a->user_id)
                     ->where('member_id', $a->member_id)
                     ->where('type', "prescription")
                     ->where('status', 1)
                     ->get();

             } else {
                 $a->member = null;

                 $userMedical_xray = App\Models\UserModels\MedicalRecord::where('user_id', $a->user_id)
                     ->where('type', "xray")
                     ->where('status', 1)
                     ->get();
                 $userMedical_lab = App\Models\UserModels\MedicalRecord::where('user_id', $a->user_id)
                     ->where('type', "lab")
                     ->where('status', 1)
                     ->get();
                 $userMedical_prescription = App\Models\UserModels\MedicalRecord::where('user_id', $a->user_id)
                     ->where('type', "prescription")
                     ->where('status', 1)
                     ->get();
             }

             // Attach image URLs to records
             $userMedical_xray->each(fn ($record) => $record->image = asset('../project/public/medical_records/' . $record->image));
             $userMedical_lab->each(fn ($record) => $record->image = asset('../project/public/medical_records/' . $record->image));
             $userMedical_prescription->each(fn ($record) => $record->image = asset('../project/public/medical_records/' . $record->image));

             $a->userMedical_xray = $userMedical_xray;
             $a->userMedical_lab = $userMedical_lab;
             $a->userMedical_prescription = $userMedical_prescription;

             $userProfile = App\Models\UserModels\UserProfile::find($a->user_id);
             if ($userProfile) {
                 $userProfile->image = asset('content/user/' . $userProfile->image);
             }

             $doctor = App\Models\User::find($a->doctor_id);
             if ($doctor) {
                 $doctor->image = asset('content/doctor/' . $doctor->image);

                 // Fetch doctor's specializations
                 $doctorProfile = App\Models\UserInformation::where('user_id', $doctor->id)->first(['specialisations']);
                 if ($doctorProfile && $doctorProfile->specialisations) {
                     $specialisationIds = explode('|', $doctorProfile->specialisations);
                     $specialisationNames = \DB::table('master_specialsations')
                         ->whereIn('id', $specialisationIds)
                         ->pluck('name')
                         ->toArray();
                 } else {
                     $specialisationNames = [];
                 }

                 // Attach doctor and user data
                 $a->doctor = $doctor;
                 $a->user = $userProfile;
                 $a->doctorprofile = $specialisationNames;

                 // Fetch treatment details
                 $treatment = DB::table('treatments')
                     ->where('id', $a->treatment_id)
                     ->select(['treatment_name', 'call_before_confirmation', 'average_duration'])
                     ->first();

                 if ($treatment) {
                     $a->treatment_name = $treatment->treatment_name;
                     $a->call_before_confirmation = $treatment->call_before_confirmation;
                     $a->average_duration = $treatment->average_duration;
                 } else {
                     $a->treatment_name = null;
                     $a->call_before_confirmation = null;
                     $a->average_duration = null;
                 }

                 // Start flag logic
                 $startflag = 0;
                 if ($a->status == 4 && $a->consultation_type == 'online') {
                     $currenttime = time();
                     $starttime = strtotime($a->schedule_date . ' ' . $a->schedule_time);
                     if ($currenttime >= $starttime) {
                         $startflag = 1;
                     }
                 }
                 $a->startflag = $startflag;
             }
         }

         // dd($a);


      ?>
      <div class="card-box profile-header">
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
         <div class="row">
            <div class="col-md-12">
               <div class="profile-view">
                  <div class="profile-img-wrap">
                     <div class="profile-img">
                        @if($a->member)
                        <a href="#"><img class="avatar" src="{{$a->member->image}}" alt></a>
                        @else
                        <a href="#"><img class="avatar" src="{{$a->user->image}}" alt></a>
                        @endif
                     </div>
                  </div>
                  <div class="profile-basic">
                     <div class="row">
                        <div class="col-md-5">
                           <div class="profile-info-left">
                              @if($a->member)
                              <h3 class="user-name m-t-0 mb-1">{{$a->user->name}} {{$a->user->last_name}}</h3>
                              <p class="text-muted mb-1">For Member</p>
                              <p class="text-muted mb-1">{{$a->member->relation}}</p>
                              <p class="text-muted mb-1">{{$a->user->gender}}</p>
                              <p class="text-muted">{{$a->user->date_of_birth}}</p>
                              @else
                              <h3 class="user-name m-t-0 mb-1">{{$a->user->name}} {{$a->user->last_name}}</h3>
                              <p class="text-muted mb-1">For Self</p>
                              <p class="text-muted mb-1">{{$a->user->gender}}</p>
                              <p class="text-muted">{{$a->user->date_of_birth}}</p>
                              @endif
                              <p class="text-muted">{{$a->user->mobile}}</p>
                              <p class="text-muted">{{$a->user->email}}</p>
                           </div>
                        </div>
                        <div class="col-md-7">
                           <ul class="personal-info">
                              <li>
                                 
                                 <span class="text">
                                 @if($a->status==0)
                                    <a href="{{route('admin.appoint.updatemyappointment',[$a->id,4])}}"><span class="btn btn-success my-2">Accept</span></a>
                                    <a href="{{route('admin.appoint.updatemyappointment',[$a->id,3])}}"><span class="btn btn-danger my-2">Reject </span></a>
                                    <a href="{{ route('admin.appoint.cancel_appoint', $a->id) }}" class="btn btn-danger my-2"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                                    {{-- <a href="{{route('admin.appoint.updatemyappointment',[$a->id,2])}}"><span class="btn btn-danger my-2">Cancel </span></a> --}}
                                 @endif</span>
                              </li>
                              <li>
                                 <span class="title">Booking ID:</span>
                                 <span class="text"><a href="#">{{$a->id}}</a></span>
                              </li>
                              <li>
                                 <span class="title">Date & time:</span>
                                 <span class="text"><a href="#">{{date('d M Y h:i a', strtotime($a->schedule_date.' '.$a->schedule_time))}}</a></span>
                              </li>
                              <li>
                                 <span class="title">Cancel Date</span>
                                 <span class="text"><a href="#">{{$a->cancel_date ?? 'NA'}}</a></span>
                              </li>
                              <li>
                                 <span class="title">Status:</span>
                                 <span class="text"><a href="#">
                                 @if($a->status==0)
                                    <span class="btn btn-primary my-2">Pending </span>
                                 @elseif($a->status==1)
                                    <span class="btn btn-success my-2">Completed </span>
                                 @elseif($a->status==2)
                                    <span class="btn btn-danger my-2">Canceled </span>
                                 @elseif($a->status==3)
                                    <span class="btn btn-warning my-2">Rejected </span>
                                @elseif($a->status==4)
                                    <span class="btn btn-info my-2">Accepted </span>
                                @elseif($a->status==5)
                                    <span class="btn btn-primary my-2">Payment Failed </span>
                                @elseif($a->status==6)
                                    <span class="btn btn-primary my-2">Refunded </span>
                                @endif</a></span>
                                 </li>
                              <li>
                                 <span class="title">Payment Status:</span>
                                 <span class="text">
                                    @if($a->is_paid==0)
                                    <span class="btn btn-warning my-2">Not Paid </span>
                                      @elseif($a->is_paid==1)
                                          <span class="btn btn-success my-2">Fully Paid </span>
                                      @elseif($a->status==2)
                                          <span class="btn btn-info my-2">Partially Paid </span>
                                      @endif
                                 </span>
                              </li>
                              <li>
                                 <span class="title">Treatment:</span>
                                 <span class="text">{{$a->treatment_name}}</span>
                              </li>
                              <li>
                                 <span class="title">Symptoms:</span>
                                 <span class="text">{{$a->symptoms}}</span>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="profile-tabs mt-3">
         <ul class="nav nav-tabs nav-tabs-bottom">
            <li class="nav-item"><a class="nav-link active" href="#about-cont" data-bs-toggle="tab">X Ray</a></li>
            <li class="nav-item"><a class="nav-link" href="#about-cont1" data-bs-toggle="tab">X Lab</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab2" data-bs-toggle="tab">Prescription</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab3" data-bs-toggle="tab">Transaction Details</a></li>
            
         </ul>
         <div class="tab-content">
            <div class="tab-pane show active" id="about-cont">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">X-Ray</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>Image</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @if($a->userMedical_xray->count())
                                 	@foreach($a->userMedical_xray as $x)	
                                 	
	                                 <tr>
	                                    <td class="profile-image"><a href="#"><img width="28" height="28" src="{{$x->image}}" class="rounded-circle m-r-5" alt></a></td>
	                                    <td>
	                                       <a target="_blank" href="{{$x->image}}"><button  type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button></a>
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
            <div class="tab-pane show" id="about-cont1">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Lab</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>Image</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @if($a->userMedical_lab->count())
                                    @foreach($a->userMedical_lab as $lab)   
                                    @php
                                
                           @endphp
                                    <tr>
                                       <td class="profile-image"><a href="#"><img width="28" height="28" src="{{$lab->image}}" class="rounded-circle m-r-5" alt></a></td>
                                       <td>
                                          <a target="_blank" href="{{$lab->image}}"><button  type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button></a>
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
            <div class="tab-pane" id="bottom-tab2">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Prescription</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Prescription ID</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @if($a->userMedical_prescription->count())
                                 	@php
								        $i =1;
									@endphp
                                 	@foreach($a->userMedical_prescription as $p)	
                                 	
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>#{{$p->id}}</td>
                                    <td>{{date('d M Y h:i a', strtotime($p->updated_at))}}</td>
                                    <td>
                                       <a target="_blank" href="{{route('admin.myuser.prescriptionView',$p->id)}}"><button  type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button></a>
                                       <a target="_blank" href="{{route('admin.myuser.dwnprescriptionView',$p->id)}}"><button type="button" class="btn btn-success"><i class="fas fa-file-download"></i></button></a>
                                    </td>
                                 </tr>
                                <?php $i++; ?>
                                 @endforeach
                                 @endif
                                 
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            
            <div class="tab-pane" id="bottom-tab3">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Transaction Details</h3>
                        <div class="table-responsive">
                           <div class="col-md-7">
                           <ul class="personal-info">
                              <li>
                                 <span class="title">Trxn Id:</span>
                                 <span class="text">{{$a->payid ?? '0'}}</span>
                              </li>
                              <li>
                                 <span class="title">Subtotal:</span>
                                 <span class="text"><a href="#">{{$a->subtotal}}</a></span>
                              </li>
                                 
                              <li>
                                 <span class="title">Gst:</span>
                                 <span class="text">{{$a->gst}}</span>
                              </li>
                              <li>
                                 <span class="title">Partial Amount:</span>
                                 <span class="text">{{$a->partial_amount}}</span>
                              </li>
                              <li>
                                 <span class="title">Total Amount:</span>
                                 <span class="text">{{$a->total_amount}}</span>
                              </li>
                           </ul>
                        </div>
                        </div>

                        @if($a->refund_status == 1)
                        <h3 class="card-title">Refund Details</h3>
                        <div class="table-responsive">
                           <div class="col-md-7">
                           @php
                           $f = json_decode($a->refund_details);
                           $fees = '';
                           if($set->refund_deduction_percent > 0 && isset($f->amount)) {
                              $fees = ($f->amount/100)*$set->refund_deduction_percent/100;
                           }
                           @endphp
                           <ul class="personal-info">
                              <li>
                                 <span class="title">Refund Amount:</span>
                                 <span class="text">{{$f->amount/100 ?? ''}}</span>
                              </li>
                              <li>
                                 <span class="title">Refund ID:</span>
                                 <span class="text">{{$f->id ?? ''}}/span>
                              </li>
                                 
                              <li>
                                 <span class="title">Payment Fees:</span>
                                 <span class="text">{{$fees}}</span>
                              </li>
                           </ul>
                        </div>
                        </div>
                        @endif

                     </div>
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