@extends('layouts.admin')
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                           <a href="javascript: void(0);">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
       
         <div class="row">
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.user_list')}}">
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->
                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total User</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.user_list')}}"><span class="counter-value" data-target="{{$userCount}}">{{$userCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-user"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
                </a>
            </div>
           
                                  
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.coach_list')}}">
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->
                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Coach</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.coach_list')}}"><span class="counter-value" data-target="{{$coachCount}}">{{$coachCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-user-tie"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
            
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.package_users')}}">
            
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->

                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Packages</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.package_users')}}"><span class="counter-value" data-target="{{$packageCount}}">{{$packageCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-box"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
            
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.yoga_categories')}}">
            
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->

                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Category</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.yoga_categories')}}"><span class="counter-value" data-target="{{$catCount}}">{{$catCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-box"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
            
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.yoga_poses')}}">
            
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->

                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Poses</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.yoga_poses')}}"><span class="counter-value" data-target="{{$poseCount}}">{{$poseCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-box"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
            
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.yoga_pose_levels')}}">
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->

                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Poses Level</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.yoga_pose_levels')}}"><span class="counter-value" data-target="{{$poselCount}}">{{$poselCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-box"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.practice_routines')}}">
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->

                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Practice Routine</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.practice_routines')}}"><span class="counter-value" data-target="{{$pracCount}}">{{$pracCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-box"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
            <div class="col-xl-3 col-md-6">
               <a href="{{route('admin.purchase_history')}}">
               <div class="card card-h-100 bg-temple-white">
                  <!-- card body -->

                  <div class="card-body">
                     <div class="row align-items-center">
                        <div class="col-12">
                           <span class="mb-3 lh-1 d-block font-size-15">Total Package Purchase</span>
                        </div>
                        <div class="col-6">
                           <h3 class="mb-2">
                              <a href="{{route('admin.purchase_history')}}"><span class="counter-value" data-target="{{$purCount}}">{{$purCount}}</span></a>
                           </h3>
                        </div>
                        <div class="col-6 text-right-1">
                           <span class="home-icon">
                           <i class="fas fa-box"></i>
                           </span>
                        </div>
                     </div>
                  </div>
                  <!-- end card body -->
               </div>
               </a>
            </div>
           
         </div>
         <!-- end row-->
         <!-- end row -->
      </div>
      <!-- container-fluid -->
   </div>

   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               <script>
                  document.write(new Date().getFullYear())
               </script> © Yoga.
            </div>
            <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
         </div>
      </div>
   </footer>
</div>
@endsection