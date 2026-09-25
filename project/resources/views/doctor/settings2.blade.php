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
                  <li class="breadcrumb-item active">Settings</li>
               </ul>
            </div>
         </div>
      </div>
      <?php 
            $total_invoice_points = explode(',', $s->total_invoice_points);
            $referrer_a_friend_points_master = explode(',', $s->referrer_a_friend_points_master);
            $referred_after_booking_points_master = explode(',', $s->referred_after_booking_points_master);
            $redemption_points_master = explode(',', $s->redemption_points_master);


       ?>
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body pt-4">
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
                  <form method="POST" action="{{ route('admin.settings2update') }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>loyalty_points_base_100 <span class="login-danger">*</span></label>
                              <select class="form-control" name="loyalty_points_base_100">
                                 <option>Select</option>
                                 @if(count($total_invoice_points) > 0)
                                 @foreach($total_invoice_points as $t)
                                 <option {{$t == auth()->user()->UserInformationDetails->loyalty_points_base_100 ? 'selected' : ""}} value="{{$t}}">{{$t}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>referrer_a_friend_points <span class="login-danger">*</span></label>
                              <select class="form-control" name="referrer_a_friend_points">
                                 <option>Select</option>
                                 @if(count($referrer_a_friend_points_master) > 0)
                                 @foreach($referrer_a_friend_points_master as $t)
                                 <option {{$t == auth()->user()->UserInformationDetails->referrer_a_friend_points ? 'selected' : ""}} value="{{$t}}">{{$t}}</option>
                                 @endforeach
                                 @endif
                              </select>
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>referred_after_booking_points <span class="login-danger">*</span></label>
                              <select class="form-control" name="referred_after_booking_points">
                                 <option>Select</option>
                                 @if(count($referred_after_booking_points_master) > 0)
                                 @foreach($referred_after_booking_points_master as $t)
                                 <option {{$t == auth()->user()->UserInformationDetails->referred_after_booking_points ? 'selected' : ""}} value="{{$t}}">{{$t}}</option>
                                 @endforeach
                                 @endif
                              </select>
                           </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>redemption_points <span class="login-danger">*</span></label>
                              <select class="form-control" name="redemption_points">
                                 <option>Select</option>
                                 @if(count($redemption_points_master) > 0)
                                 @foreach($redemption_points_master as $t)
                                 <option {{$t == auth()->user()->UserInformationDetails->redemption_points ? 'selected' : ""}} value="{{$t}}">{{$t}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              
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
