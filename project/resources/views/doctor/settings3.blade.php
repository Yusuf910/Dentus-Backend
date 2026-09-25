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
                  <li class="breadcrumb-item active">Patients Payments</li>
               </ul>
            </div>
         </div>
      </div>
      @php
          $userInfo = auth()->user()->UserInformationDetails;
          $partialPayment = $userInfo->partial_payment ?? '0';
          $partialPercentage = $userInfo->partial_percentage ?? '';
          $loyaltyPoints = $userInfo->loyalty_points ?? '0';
      @endphp
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body pt-4">


                <!-- new code -->
<style>
       
        .form-check-input:checked {
            background-color: #ff6600;
            border-color: #ff6600;
        }
        .btn-custom {
            background-color: #ff6600;
            border: none;
            color: white;
        }
    </style>
 

    <script>
        document.getElementById('partialPaymentSwitch').addEventListener('change', function() {
            document.getElementById('advancePayment').disabled = !this.checked;
        });
    </script>
                <!-- new code -->



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
                  <form method="POST" action="{{ route('admin.settings3update') }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="mb-3 form-check form-switch">
                              <label class="form-check-label" for="partialPaymentSwitch">Do you want to enable partial payment?</label>
                                <input class="form-check-input" type="checkbox" name="partial_payment" id="partialPaymentSwitch" {{ $partialPayment == '1' ? 'checked' : '' }}>
                              <label>
                               
                         </div>
                     </div>

                     <div class="col-12 col-md-6 col-xl-6">
                         <div class="input-block local-forms">
                             <label>Advance Payment Percentage <span class="login-danger">*</span></label>
                             <input type="number" id="advance_payment" name="partial_percentage" class="form-control"
                                     min="0" placeholder="Enter percentage" 
                                     value="{{ $partialPercentage }}" 
                                     max="99">
                         </div>
                     </div>

                     <div class="col-12 col-md-6 col-xl-6">
                         <div class="col-12 col-md-6 col-xl-6">
                            <div class="mb-3 form-check form-switch">
                              <label class="form-check-label" for="loyaltyPointsSwitch">Use Loyalty Points?</label>
                                <input class="form-check-input" type="checkbox" name="loyalty_points" id="loyaltyPointsSwitch" {{ $loyaltyPoints == '1' ? 'checked' : '' }}>
                              <label>
                               
                         </div>
                         
                     </div>

                     
                        
                        
                        <div class="col-12">
                           <div class="doctor-submit text-start">
                              <button type="submit" class="btn btn-primary submit-form me-2">Save</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')

@endsection
@endsection
