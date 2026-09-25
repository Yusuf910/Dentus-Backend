@extends('layouts.admin')
@section('styles')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
@endsection
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Appointment </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Book Appointment</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body">
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
                  <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12">
                           <div class="form-heading">
                              <h4>Select User Type</h4>
                           </div>
                        </div>
                        <div class="col-12 col-md-12 col-xl-12">
                           <div class="input-block row mb-0">
                              <div class="col-md-12">
                                 <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type" id="gender_male" value="new_user" checked>
                                    <label class="form-check-label" for="gender_male">
                                    New User
                                    </label>
                                 </div>
                                 <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="user_type" id="gender_female" value="existing_user">
                                    <label class="form-check-label" for="gender_female">
                                    Already a Member
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  
               </div>
            </div>
         </div>
      </div>
      <div class="col-sm-12">
         <div class="card">
            <div class="card-body">
               
                  <div class="row">
                     <div class="col-12">
                        <div class="form-heading">
                           <h4>Patient Details</h4>
                        </div>
                     </div>
                     	<div id="new_user_form">
                           <div class="row">
						   <div class="col-12 col-md-6 col-xl-6">
						      <div class="input-block local-forms">
						         <label>First Name <span class="login-danger">*</span></label>
						         <input type="text" id="name" required class="form-control" name="name">
						      </div>
						   </div>
						   <div class="col-12 col-md-6 col-xl-6">
						      <div class="input-block local-forms">
						         <label>Last Name <span class="login-danger">*</span></label>
						         <input type="text" id="lname" required class="form-control" name="lname">
						      </div>
						   </div>
						   <div class="col-12 col-md-6 col-xl-6">
						      <div class="input-block local-forms">
						         <label>Email <span class="login-danger">*</span></label>
						         <input type="email" id="email" required class="form-control" name="email">
						      </div>
						   </div>
						   <div class="col-12 col-md-6 col-xl-6">
						      <div class="input-block local-forms">
						         <label>Mobile <span class="login-danger">*</span></label>
						         <input required type="text" id="mobile" class="form-control" name="mobile">
						      </div>
						   </div>
                  </div>
						   
						</div>

						<!-- Existing User Dropdown -->
						<div id="existing_user_select" style="display: none;">
						   <div class="col-12 col-md-6 col-xl-6">
						      <div class="input-block local-forms">
						         <label>Select User <span class="login-danger">*</span></label>
						         <select name="selected_user" class="form-control">
						            <option value="">Select a User</option>
						            @foreach($a as $user)
						            <option value="{{ $user->id }}">{{ $user->name }}{{ $user->lname }} ({{ $user->email }}, {{ $user->mobile }})</option>
						            @endforeach
						         </select>
						      </div>
						   </div>
						</div>

                    <div class="col-12 col-md-6 col-xl-6" id="a2" style="display: none;">
                        <div class="input-block local-forms">
                           <label>Select Family Member</label>
                           <select name="member_id" id="member_id" class="form-control select">
                           </select>
                        </div>
                     </div> 

                     
                  <div class="col-12 col-md-6 col-xl-6">
					      <div class="input-block local-forms">
					         <label>Type of Consultation<span class="login-danger">*</span></label>
					         <select name="consultation_type" required class="form-control">
					            <option value="Online Consultation">Online Consultation</option>
					            <option value="In-Clinic Consultation">In-Clinic Consultation</option>
					         </select>
					      </div>
					   </div>
                  <div class="col-12 col-md-6 col-xl-6" id="a1" style="display: none;">
                     <div class="input-block local-forms">
                        <label>Type of Appointment<span class="login-danger">*</span></label>
                        <select name="appointment_type" class="form-control">
                           <option  value="New Appointment">New Appointment</option>
                           <option value="Follow Up">Follow Up</option>
                        </select>
                     </div>
                  </div>
                

                       <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Select Treatment</label>
                           <select name="treatment_id" id="treatment_id" onchange="getDoctors(this.value)" required class="form-control select">
                              <option value="">Select</option>
                               @if($t->count() > 0)
	                           @foreach($t as $at)
	                           <option value="{{$at->id}}">{{$at->treatment_name}}</option>
	                           @endforeach
	                           @endif
                           </select>
                        </div>
                     </div>
                     @if(auth()->user()->parent_id == 0)
                     <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Select Doctor</label>
                           <!-- <select name="doctor_id" id="doctor_id" class="form-control select">
                              <option value="">Select</option>
                              <option value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                              @if($u->count() > 0)
                              @foreach($u as $ab1)
                                 <option value="{{$ab1->id}}">{{$ab1->name}}</option>
                              @endforeach
                              @endif
                              </select>
                           </select> -->
                           <select name="doctor_id" id="doctor_id" class="form-control select">
                              <option value="">Select</option>
                           </select>
                        </div>
                     </div>
                     @else
                     <input type="hidden" name="doctor_id" value="{{auth()->user()->id}}" id="doctor_id" >
                     @endif
                   
                     <div class="col-12 col-md-12 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Select Clinic</label>
                           <select required name="clinic_id" id="clinic_id" onchange="clinicChanged(this)"  class="form-control select">
                              <option value="">Select Clinic</option>
                               @if($c->count() > 0)
	                           @foreach($c as $a)
	                           <option value="{{$a->id}}">{{$a->clinic_name}}({{$a->address}},{{$a->state}},{{$a->city}},{{$a->pincode}})</option>
	                           @endforeach
	                           @endif
                           </select>
                        </div>
                     </div>
                     <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Symtoms <span class="login-danger">*</span></label>
                           <input name="symptoms" required class="form-control" type="text">
                        </div>
                     </div>
                     <!-- <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Amount <span class="login-danger">*</span></label>
                           <input name="total_amount" class="form-control" type="number" required min="0">
                        </div>
                     </div> -->
                     <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Notes <span class="login-danger">*</span></label>
                           <textarea name="notes" class="form-control"></textarea>
                        </div>
                     </div>
                     <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms cal-icon">
                           <label>Choose Date <span class="login-danger">*</span></label>
                           <input required name="schedule_date" class="form-control" id="your_datepicker_input" type="text">
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
                     <!-- is_paid select  -->
                     <div class="col-12 col-md-6 col-xl-6">
                        <div class="input-block local-forms">
                           <label>Payment Status</label>
                           <select name="is_paid" class="form-control">
                              <option value="0">Unpaid</option>
                              <option value="1">Paid</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-12">
                        <div class="doctor-submit text-start">
                           <input type="submit" name="" class="btn btn-primary submit-form me-2">
                        </div>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')
<script>
   
   $(document).ready(function(){
       $('input[name="user_type"]').change(function(){
           if ($(this).val() === "new_user") {
               $('#new_user_form').show();
               $('#existing_user_select, #a1, #a2').hide();
               $('#name, #lname, #email, #mobile').attr('required', true);
           } else {
               $('#new_user_form').hide();
               $('#existing_user_select, #a1, #a2').show();
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
       
         $('select[name="selected_user"]').change(function () {
           let userId = $(this).val();

           if (userId) {
               $.ajax({
                   url: "{{route('admin.appoint.memberlist')}}",
                   type: 'GET',
                   data: { user_id: userId },
                   success: function (response) {
                       let memberDropdown = $('#member_id');
                       memberDropdown.empty().append('<option value="">Select Family Member</option>');

                       if (response.length > 0) {
                           $.each(response, function (index, member) {
                               memberDropdown.append('<option value="' + member.id + '">' + member.name + '</option>');
                           });
                       }
                   }
               });
           } else {
               $('#member_id').empty().append('<option value="">Select Family Member</option>');
           }
       });

      
   });

   function getDoctors(treatment_id)
   {
       if(treatment_id == ''){
           document.getElementById('doctor_id').innerHTML =
               '<option value="">Select</option>';
           return;
       }

       fetch('/doctor-dashboard/appointments/get-doctors-by-treatment/' + treatment_id)
           .then(response => response.json())
           .then(data => {

               let doctorDropdown = document.getElementById('doctor_id');
               doctorDropdown.innerHTML = '<option value="">Select</option>';

               if(data.length > 0){

                   data.forEach(function(doctor){

                       let option = document.createElement('option');
                       option.value = doctor.id;
                       option.text = doctor.name;

                       doctorDropdown.appendChild(option);
                   });

               } else {

                   doctorDropdown.innerHTML =
                       '<option value="">No Doctors Found</option>';
               }
           })
           .catch(error => {
               console.log(error);
           });
   }
   function clinicChanged(r) {
      $('#your_datepicker_input').val('');
   }
   <?php $mid = 0; ?>
   function fetchTimeSlots(e) {
	    $('#conSlots').html('Loading....');
	    var dateselect = e;
	    var selectid = '{{$mid}}';
	    var what = $("#discount_on").val();
       var treatment = $('#treatment_id').val();
       var doctor_id = $('#doctor_id').val();
       var clinic_id = $('#clinic_id').val();
       console.log(treatment);
	    if (dateselect != '') {
	        var apiurl = "{{route('admin.appoint.get-timeslot')}}";

	        $.post(apiurl, { dateselect: dateselect, _token: "{{ csrf_token() }}", id: selectid, type: what,treatment:treatment,doctor_id:doctor_id,clinic_id:clinic_id }, function (response) {
	            if (response.status && response.dateslots.length > 0) {
	                let slots = response.dateslots[0].slot;
	                let slotHtml = '';

	                $.each(slots, function (index, slot) {
	                    let timeRange = slot.start_time + ' - ' + slot.end_time;
	                    let isDisabled = (slot.is_book === 1 || slot.timepass === 1) ? 'disabled' : '';
	                    slotHtml += `<option value="${timeRange}" ${isDisabled}>${timeRange}</option>`;
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
    window.onload = function() {
        document.querySelector('form').reset();
    }
</script>
@endsection
@endsection