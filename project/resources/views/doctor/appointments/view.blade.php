@extends('layouts.admin')
@section('styles')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
@endsection
@section('content')
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
             
             if (($a->user_id != $a->member_id && $a->member_type == 'relation') || ($a->user_id != $a->member_id && $a->member_type == 'self') || ($a->user_id == $a->member_id && $a->member_type == 'relation')) {

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
             $userMedical_xray->each(fn ($record) => $record->image = asset('project/public/medical_records/' . $record->image));
             $userMedical_lab->each(fn ($record) => $record->image = asset('project/public/medical_records/' . $record->image));
             $userMedical_prescription->each(fn ($record) => $record->image = asset('project/public/medical_records/' . $record->image));
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
                              <h3 class="user-name m-t-0 mb-1">{{$a->member->name}} {{$a->member->last_name}}</h3>
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
                                    
                                 @endif
                                 
                                 @if($a->status==4)
                                    <a data-bs-toggle="modal" data-bs-target="#centermodal"><span class="btn btn-success my-2">Reschedule</span></a>
                                    <a data-bs-toggle="modal" data-bs-target="#centermodal2"><span class="btn btn-danger my-2">Cancel Booking</span></a>
                                 @endif

                                 <!-- $request->is_paid == 1 -->
                                 @if($a->is_paid == 0 && $a->status == 4)
                                    <a href="{{route('admin.appoint.markaspaid',[$a->id])}}"><span class="btn btn-primary my-2">Mark as Paid</span></a>
                                 @endif
                                 
                              </span>
                              </li>
                              <li>
                                 <span class="title">Booking ID:</span>
                                 <span class="text"><a href="#">{{$a->id}}</a></span>
                              </li>
                              <li>
                                 <span class="title">Date & time:</span>
                                 <span class="text"><a href="#">{{date('d M Y', strtotime($a->schedule_date))}} {{$a->schedule_time}} - {{$a->end_time}}</a></span>
                              </li>
                              <li>
                                 <span class="title">Status:</span>
                                 <span class="text"><a href="#">
                                     @switch($a->status)
                                         @case(0)
                                             <span class="btn btn-primary my-2">Pending</span>
                                             @break
                             
                                         @case(1)
                                             <span class="btn btn-success my-2">Completed</span>
                                             @break
                             
                                         @case(2)
                                             <span class="btn btn-danger my-2">Canceled</span>
                                             @break
                             
                                         @case(3)
                                             <span class="btn btn-warning my-2">Rejected</span>
                                             @break
                             
                                         @case(4)
                                             <span class="btn btn-info my-2">Accepted</span>
                                             @break
                             
                                         @case(5)
                                             <span class="btn btn-primary my-2">Payment Failed</span>
                                             @break
                             
                                         @case(6)
                                             <span class="btn btn-primary my-2">Refunded</span>
                                             @break
                                     @endswitch
                                 </a></span>
                             </li>
                             
                              <li>
                                 <span class="title">Payment Status:</span>
                                 <span class="text">
                                    @if($a->is_paid==0)
                                    <span class="btn btn-warning my-2">Not Paid </span>
                                      @elseif($a->is_paid==1)
                                          <span class="btn btn-success my-2">Fully Paid </span>
                                      @elseif($a->is_paid==2)
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
                              <?php $doctorName = App\Models\User::find($a->doctor_id);?>
                              @if($doctorName)
                              <li>
                                 <span class="title">Doctor's Name:</span>
                                 <span class="text"> {{$doctorName->name ?? "" }}</span>
                              </li>
                              @endif
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
                                       @if($p->type == 'prescription')
                                        @if(!empty($p->prescription))
                                            <a target="_blank" href="{{ asset('project/storage/app/public/prescriptions/').'/'.$p->pdf ?? '#' }}">
                                                <button type="button" class="btn btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </a>
                                        @else
                                            <a target="_blank" href="{{ $p->image ?? '#' }}">
                                                <button type="button" class="btn btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </a>
                                        @endif
                                    @else
                                        <a target="_blank" href="{{ $p->image ?? '#' }}">
                                            <button type="button" class="btn btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </a>
                                    @endif
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
                                 <span class="title">Subtotal:</span>
                                 <span class="text"><a href="#">{{$a->subtotal}}</a></span>
                              </li>
                                 
                              <li>
                                 <span class="title">gst:</span>
                                 <span class="text">{{$a->gst}}</span>
                              </li>
                              <li>
                                 <span class="title">partial_amount:</span>
                                 <span class="text">{{$a->partial_amount}}</span>
                              </li>
                              <li>
                                 <span class="title">total_amount:</span>
                                 <span class="text">{{$a->subtotal+$a->gst}}</span>
                              </li>
                           </ul>
                        </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            
         </div>
      </div>
   </div>

</div>
<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Reschedule</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ route('admin.appoint.updatemyappointmentslot',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
               <div class="col-12 col-md-12 col-xl-12">
                  <div class="input-block local-forms">
                     <label>Select Clinic</label>
                     <select name="clinic_id" onchange="clinicChanged(this)" id="clinic_id"  class="form-control">
                        <option value="0">Select Clinic</option>
                         @if($c->count() > 0)
                        @foreach($c as $ab)
                        <option {{$a->clinic_id == $ab->id ? 'selected' : ''}} value="{{$ab->id}}">{{$ab->clinic_name}}({{$ab->address}},{{$a->state}},{{$ab->city}},{{$ab->pincode}})</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
               </div>
               <div class="col-12 col-md-6 col-xl-6">
                  <div class="input-block local-forms cal-icon">
                     <label>Choose Date <span class="login-danger">*</span></label>
                     <input value="{{$a->schedule_date}}" required name="schedule_date" class="form-control" id="your_datepicker_input" type="text">
                  </div>
               </div>
               <div class="col-12 col-md-6 col-xl-6">
                  <div class="input-block local-forms">
                     <label>Select Time</label>
                     <select required id="conSlots" name="start_time" class="form-control">
                        <option>Select Time</option>
                     </select>
                  </div>
               </div>
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>

<div class="modal fade" id="centermodal2" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Cancel Booking</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ route('admin.appoint.updatemyappointmentcancel',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
               <div class="col-12 col-md-6 col-xl-6">
                  <div class="input-block local-forms">
                     <label>Select Reason</label>
                     <select required id="cancel_other2" name="cancel_other2" class="form-control">
                        <option>Select Reason</option>
                        <option value="Schedule Change">Schedule Change</option>
                        <option value="Unexpected Work">Unexpected Work</option>
                        <option value="Childcare Issue">Childcare Issue</option>
                        <option value="Other">Other(Plz explain below)</option>
                     </select>
                  </div>
               </div>
               <div class="col-12 col-md-12 col-xl-12">
                  <div class="input-block local-forms">
                     <label>Reason for cancel! <span class="login-danger"></span></label>
                     <textarea class="form-control" name="cancel_other"></textarea>
                  </div>
               </div>
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Cancel</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
@section('js_user_page')
<script>
   function clinicChanged(r) {
      $('#your_datepicker_input').val('');
      $('#conSlots').val('');
      
      
   }
   const preSelectedDate = '{{ $a->schedule_date }}';
    const preSelectedTime = '{{ $a->start_time }} - {{ $a->end_time }}';
   $(document).ready(function(){
       $('input[name="user_type"]').change(function(){
           if ($(this).val() === "new_user") {
               $('#new_user_form').show();
               $('#existing_user_select').hide();
               $('#name, #lname, #email, #mobile').attr('required', true);
           } else {
               $('#new_user_form').hide();
               $('#existing_user_select').show();
               $('#name, #lname, #email, #mobile').removeAttr('required');
           }
       });
       $('#your_datepicker_input').datetimepicker({
            timepicker: false,
            format: 'Y-m-d',     // Date format (YYYY-MM-DD)
            minDate: 0,          // Disable past dates
            onChangeDateTime: function(dp, $input) {  // Trigger when date changes
                let selectedDate = $input.val(); // Get selected date
                console.log("Selected Date: ", selectedDate);

                if (selectedDate) {
                    fetchTimeSlots(selectedDate);
                }
            }
        });
         if (preSelectedDate) {
            fetchTimeSlots(preSelectedDate, preSelectedTime);
        }
   });

   function fetchTimeSlots(e,selectedTime = null) {
       $('#conSlots').html('Loading....');
       var dateselect = e;
       var selectid = '{{$a->id}}';
       var what = $("#discount_on").val();
       var clinic_id = $('#clinic_id').val();
       if (dateselect != '') {
           var apiurl = "{{route('admin.appoint.get-timeslot')}}";

           $.post(apiurl, { dateselect: dateselect, _token: "{{ csrf_token() }}", id: selectid, type: what,treatment:{{$a->treatment_id}},doctor_id:{{$a->doctor_id}},clinic_id:clinic_id }, function (response) {
               if (response.status && response.dateslots.length > 0) {
                   let slots = response.dateslots[0].slot;
                   let slotHtml = '';

                   $.each(slots, function (index, slot) {
                       let timeRange = slot.start_time + ' - ' + slot.end_time;
                       let isDisabled = (slot.is_book === 1 || slot.timepass === 1) ? 'disabled' : '';
                       let isSelected = (selectedTime && selectedTime === timeRange) ? 'selected' : '';
                       slotHtml += `<option value="${timeRange}" ${isDisabled} ${isSelected}>${timeRange}</option>`;
                   });

                   slotHtml += '';
                   $('#conSlots').html(slotHtml);
               } else {
                   $("#conSlots").html('<p>No Time Slots Available</p>');
               }
           }).fail(function () {
               $("#conSlots").html('<p>Error fetching time slots</p>');
           });
       } else {
           $("#conSlots").html('<p>No Time Slots Available</p>');
       }
   }
</script>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
@endsection
@endsection