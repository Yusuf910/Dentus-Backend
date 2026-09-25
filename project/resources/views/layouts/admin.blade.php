<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
      <link rel="shortcut icon" type="image/x-icon" href="{{asset('content/admin')}}/img/favicon.png">
      <title>Home</title>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/bootstrap.min.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/plugins/fontawesome/css/fontawesome.min.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/plugins/fontawesome/css/all.min.css">
      <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/select2.min.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/plugins/datatables/datatables.min.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/css/feather.css">
      <!-- <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/fullcalendar.min.css"> -->
      <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/style.css">
      <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/custom.css">
      @yield('styles')
   </head>
   <body>
      <div class="main-wrapper">
         <div class="header">
            <div class="header-left">
               <a href="{{ route('admin.home') }}" class="logo">
               <img src="{{asset('content/admin')}}/img/logo.png" width="35" height="35" alt> <span>Dentusapp</span>
               </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><img src="{{asset('content/admin')}}/img/icons/bar-icon.svg" alt></a>
            <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img src="{{asset('content/admin')}}/img/icons/bar-icon.svg" alt></a>
            <ul class="nav user-menu float-end">
               @if(Auth::guard('admin')->user()->parent_id == 0)
               <li class="nav-item dropdown d-none d-md-block">
                  <a href="javascript:void(0);" id="open_msg_box" class="hasnotifications nav-link"><img src="{{asset('content/admin')}}/img/icons/note-icon-01.svg" alt><span class="pulse"></span> </a>
               </li>
               @else
               @endif
               <li class="nav-item dropdown has-arrow user-profile-list">
                  <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                     <div class="user-names">
                        <h5>{{ Auth()->guard('admin')->user()->name }} {{ Auth()->guard('admin')->user()->last_name }}! </h5>
                        <span>@if(Auth::guard('admin')->user()->parent_id == 0)
                           Admin
                           @else
                           Staff
                           @endif</span>
                     </div>
                     <span class="user-img">
                     <img src="{{asset('content/doctor/'.Auth()->guard('admin')->user()->image)}}" alt="Admin">
                     </span>
                  </a>
                  <div class="dropdown-menu">
                     <a class="dropdown-item" href="{{ route('admin.user.mystaffdetail',Auth()->guard('admin')->user()->id) }}">Profile</a>
                     <a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a>
                  </div>
                  
               </li>
               
            </ul>
            <div class="dropdown mobile-user-menu float-end">
               <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
               <div class="dropdown-menu dropdown-menu-end">
                  <a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a>
               </div>
            </div>
         </div>
         <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
               <div id="sidebar-menu" class="sidebar-menu">
                  <ul>
                     <li class="menu-title">Main</li>
                     <li>
                        <a class="active" href="{{ route('admin.home') }}"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-01.svg" alt></span> <span>Dashboard</span></a>
                     @if(Auth::guard('admin')->user()->parent_id == 0)
                     @include('includes.roles.super')
                     @else
                     @include('includes.roles.normal')
                     @endif
                     
                  </ul>
               </div>
            </div>
         </div>
         @yield('content')
         <?php 

           $nlist = App\Models\MyNotification::limit(37)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->whereNOTIN('status',[2])->get();
         ?>
         <div class="notification-box">
               <div class="msg-sidebar notifications msg-noti">
                  <div class="topnav-dropdown-header">
                     <span>Messages</span>
                  </div>
                  <div class="drop-scroll msg-list-scroll" id="msg_list">
                     <ul class="list-box">
                        @if($nlist)
                        @foreach($nlist as $r1)
                        <li>
                           <a href="#">
                              <div class="list-item">
                                 <div class="list-left">
                                    <span class="avatar">{{$r1->title}}</span>
                                 </div>
                                 <div class="list-body">
                                    <span class="message-time">{{ \Carbon\Carbon::parse($r1->updated_at)->format('d/m/Y h:i A') }}</span>

                                    <div class="clearfix"></div>
                                    <span class="message-content">{{$r1->notification}}</span>
                                 </div>
                              </div>
                           </a>
                        </li>
                        @endforeach
                        @endif
                     </ul>
                  </div>
                  <div class="topnav-dropdown-footer">
                     <a href="{{route('admin.appoint.mynoti')}}">See all messages</a>
                  </div>
               </div>
            </div>
      </div>
      <div class="sidebar-overlay" data-reff></div>
      <script src="{{asset('content/admin')}}/js/jquery-3.7.1.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/bootstrap.bundle.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/feather.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/jquery.slimscroll.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/select2.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/moment.min.js" type="171d8c19019fd6574367ee81-text/javascript"></script>
      <script src="{{asset('content/admin')}}/plugins/datatables/jquery.dataTables.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/plugins/datatables/datatables.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/jquery.waypoints.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      
      

      <script src="{{asset('content/admin')}}/js/jquery.counterup.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/plugins/apexchart/apexcharts.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/plugins/apexchart/chart-data.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/jquery-ui.min.js" type="171d8c19019fd6574367ee81-text/javascript"></script>
      <!-- <script src="{{asset('content/admin')}}/js/fullcalendar.min.js" type="171d8c19019fd6574367ee81-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/jquery.fullcalendar.js" type="171d8c19019fd6574367ee81-text/javascript"></script> -->
     
      <script src="{{asset('content/admin')}}/js/app.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{asset('content/admin')}}/js/rocket-loader.min.js" data-cf-settings="52f9a6b3eb160ed79a4c6ce3-|49" defer></script>
      @yield('js_user_page') 
   </body>
</html>