@extends('layouts.admin')
@section('content')

<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Payment</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
      	@include('includes.admin.form-success')
      	<?php 
      		$a = App\Models\User::where('id',$appointmentData['doctor_id'])->first();
      		$t = App\Models\Treatment::where('id',$appointmentData['treatment_id'])->first();
      		$c = App\Models\UserEstablishmentClinic::where('user_id', $appointmentData['clinic_id'])->whereNOTIN('status',[2])->first();
      		if ($a) {
      			$doctor_info = $a->UserInformationDetails;
				$specialisation_ids = $doctor_info && $doctor_info->specialisations 
		            ? explode('|', $doctor_info->specialisations) 
		            : [];

		        $specialisations = \App\Models\MasterSpecialsation::whereIn('id', $specialisation_ids)->get();

		        $specialisations_data = $specialisations->map(function ($specialisation) {
		            return $specialisation->name;
		        })->toArray();
      		}
      		
      	?> 
         <div class="col-sm-12">
	         <div class="card card-table show-entire">
	            <div class="card-body p-4">
	               <style>
	                  .doc-p img{width: 80px; height: 80px; object-fit: cover;}
	               </style>
	               <div class="d-flex align-items-center doc-p">
	                  <img src="{{asset('content/doctor/'.$a->image)}}" class="rounded-circle me-3" alt="Doctor">
	                  <div>
	                     <h5 class="mb-0">{{$a->name}}</h5>
	                     <small class="text-muted">{{ implode(', ', $specialisations_data) }}</small>
	                     
	                  </div>
	               </div>
	               <hr>
	               
	               <div class="d-flex justify-content-between">
	                  <span>Schedule Date & Time</span>
	                  <span>{{ $appointmentData['schedule_date'] }} {{ $appointmentData['start_time'] }}</span>
	               </div>
	               <hr>
					<h5 class="fw-bold">Payment Details</h5>
					<div class="d-flex justify-content-between">
					    <span>Fee</span>
					    <span>&#8377; {{ number_format($appointmentData['subtotal'], 2) }}</span>
					</div>
					<div class="d-flex justify-content-between">
					    <span>Service Fee & Tax (18%)</span>
					    <span>&#8377; {{ number_format($appointmentData['gst'], 2) }}</span>
					</div>
					<hr>
					<div class="d-flex justify-content-between fw-bold">
					    <span>Total Payable</span>
					    <span>&#8377; {{ number_format($appointmentData['total_amount'], 2) }}</span>
					</div>
	               <a href="{{ route('admin.appoint.appointmentconfirm') }}"><button class="btn btn-primary mt-4">Submit</button></a>
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
                window.location.href = "{{ route('admin.appoint.vgraph') }}?date_filter=" + selectedValue;
            }
        });
    });

</script>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
@endsection
@endsection
