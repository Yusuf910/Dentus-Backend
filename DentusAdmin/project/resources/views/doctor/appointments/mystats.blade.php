@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{$listurl}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Earning & Statistics</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">

         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.earninghistory')}}">
               <div class="dash-widget">
                  <div class="dash-boxs comman-flex-center">
                     <img src="{{asset('content/admin')}}/img/icons/calendar.svg" alt>
                  </div>
                  <div class="dash-content dash-count">
                     <h4 class="text-dark">Total Earning</h4>
                     <h2 class="mb-0">₹ <span class="counter-up">{{round($totalEarnings)}}</span></h2>
                  </div>
               </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.myappointmentcond','allbooking')}}">
               <div class="dash-widget">
                  <div class="dash-boxs comman-flex-center">
                     <img src="{{asset('content/admin')}}/img/icons/profile-add.svg" alt>
                  </div>
                  <div class="dash-content dash-count">
                     <h4 class="text-dark">Total Appointment</h4>
                     <h2 class="mb-0"><span class="counter-up">{{round($totalBookings)}}</span></h2>

                  </div>
               </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.myappointmentcond','rebooking')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/calendar.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Total Rebooking</h4>
                  <h2 class="mb-0"><span class="counter-up">{{round($rebookCount)}}</span></h2>

               </div>
            </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.myappointmentcond','upcomingbooking')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Total Upcoming</h4>
                  <h2 class="mb-0"><span class="counter-up">{{round($upcomingBookings)}}</span></h2>

               </div>
            </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.loyalitypoints')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Patient’s Loyalty Points</h4>
                  <h6>View Details</h6>
               </div>
            </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Total App Downloads</h4>
                  <h2 class="mb-0"><span class="counter-up">{{round($totalAppDownloads)}}</span></h2>
               </div>
            </div>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Total Social Connect Views</h4>
                  <h2 class="mb-0"><span class="counter-up">{{round($totalSocialConnectViews)}}</span></h2>
               </div>
            </div>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">New patients from refer a friend</h4>
                  <h2 class="mb-0"><span class="counter-up">0</span></h2>
               </div>
            </div>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.plansold')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Patient Plans Sold</h4>

               </div>
            </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.treatmentperfom')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Treatments Performed</h4>

               </div>
            </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.docactivity')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Doctors Activity</h4>
                  <h6>View Details</h6>
               </div>
            </div>
            </a>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <a href="{{route('admin.appoint.bestpatient')}}">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Best Patients</h4>
                  <h6>View Details</h6>
               </div>
            </div>
            </a>
         </div>

         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4 class="text-dark">Total Refund amount</h4>
                  <h2 class="mb-0">{{$finalAmount}}</h2>
               </div>
            </div>
         </div>

      </div>

   </div>
</div>
@endsection
