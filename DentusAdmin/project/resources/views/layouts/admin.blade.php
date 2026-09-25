<!doctype html>
<html lang="en">
   <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('adminassets') }}/img/favicon.png">
        <title>Home</title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{ asset('adminassets') }}/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('adminassets') }}/plugins/fontawesome/css/fontawesome.min.css">
        <link rel="stylesheet" href="{{ asset('adminassets') }}/plugins/fontawesome/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('adminassets') }}/css/select2.min.css">
        <link rel="stylesheet" href="{{ asset('adminassets') }}/plugins/datatables/datatables.min.css">
        <link rel="stylesheet" href="{{ asset('adminassets') }}/css/feather.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('adminassets') }}/css/style.css">
        <link rel="stylesheet" type="text/css" href="{{ asset('adminassets') }}/css/custom.css">
        <link rel="stylesheet" href="{{ asset('adminassets') }}/plugins/summernote/summernote-bs5.min.css">


      @yield('styles')
   </head>
   <body>
      <!-- <body data-layout="horizontal"> -->
      <!-- Begin page -->
      <div class="main-wrapper">
        <div class="header">
           <div class="header-left">
              <a href="{{ route('admin.home') }}" class="logo">
              <img src="{{ asset('adminassets') }}/img/logo.png" width="35" height="35" alt> <span>Dentusapp</span>
              </a>
           </div>
           <a id="toggle_btn" href="javascript:void(0);"><img src="{{ asset('adminassets') }}/img/icons/bar-icon.svg" alt></a>
           <a id="mobile_btn" class="mobile_btn float-start" href="#sidebar"><img src="{{ asset('adminassets') }}/img/icons/bar-icon.svg" alt></a>
           <ul class="nav user-menu float-end">
              {{-- <li class="nav-item dropdown d-none d-md-block">
                 <a href="javascript:void(0);" id="open_msg_box" class="hasnotifications nav-link"><img src="{{ asset('adminassets') }}/img/icons/note-icon-01.svg" alt><span class="pulse"></span> </a>
              </li> --}}
              <li class="nav-item dropdown has-arrow user-profile-list">
                 <a href="#" class="dropdown-toggle nav-link user-link" data-bs-toggle="dropdown">
                    <div class="user-names">
                       <h5>{{ auth()->user()->name }} </h5>
                       <span>Admin</span>
                    </div>
                    <span class="user-img">
                    <img src="{{ asset('adminassets') }}/img/user-06.jpg" alt="Admin">
                    </span>
                 </a>
                 <div class="dropdown-menu">
                    <!-- <a class="dropdown-item" href="profile.html">My Profile</a>
                       <a class="dropdown-item" href="edit-profile.html">Edit Profile</a>
                       <a class="dropdown-item" href="settings.html">Settings</a> -->
                       <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    {{-- <a class="dropdown-item" href="login.html">Logout</a> --}}
                 </div>
              </li>
              <!-- <li class="nav-item ">
                 <a href="settings.html" class="hasnotifications nav-link"><img src="assets/img/icons/setting-icon-01.svg" alt> </a>
                 </li> -->
           </ul>
           <div class="dropdown mobile-user-menu float-end">
              <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></a>
              <div class="dropdown-menu dropdown-menu-end">
                 <!-- <a class="dropdown-item" href="profile.html">My Profile</a>
                    <a class="dropdown-item" href="edit-profile.html">Edit Profile</a>
                    <a class="dropdown-item" href="settings.html">Settings</a> -->
                 <a class="dropdown-item" href="#">Logout</a>
              </div>
           </div>
        </div>
         <!-- ========== Left Sidebar Start ========== -->
         {{-- <div class="vertical-menu">
            <div data-simplebar class="h-100">
               <!--- Sidemenu -->
               <div id="sidebar-menu">
                  <!-- Left Menu Start -->
                  <ul class="metismenu list-unstyled" id="side-menu">
                     <li class="menu-title" data-key="t-menu">Menu</li>
                     <li>
                        <a href="{{ route('admin.home') }}">
                        <i class="mdi mdi-home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                        </a>
                     </li> --}}
        <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li>
                    <a class="active" href="{{ route('admin.home') }}"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-01.svg" alt></span> <span>Dashboard</span></a>
                    @if(Auth::guard('admin')->user()->isSuper())
                    @include('sidebar.admin.super')
                @else
                    @include('sidebar.admin.normal')
                @endif
                {{-- @include('sidebar.admin.super') --}}
                    </ul>
                </div>
              </div>
           </div>
         <!-- Left Sidebar End -->
         <!-- ============================================================== -->
         <!-- Start right Content here -->
         <!-- ============================================================== -->
         @yield('content')
         <!-- end main content-->
      </div>
      <!-- END layout-wrapper -->
      <!-- JAVASCRIPT -->
      <script src="{{ asset('adminassets') }}/js/jquery-3.7.1.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/bootstrap.bundle.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/feather.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/jquery.slimscroll.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/select2.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/plugins/datatables/jquery.dataTables.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/plugins/datatables/datatables.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/jquery.waypoints.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/jquery.counterup.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/plugins/apexchart/apexcharts.min.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/plugins/apexchart/chart-data.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/app.js" type="52f9a6b3eb160ed79a4c6ce3-text/javascript"></script>
      <script src="{{ asset('adminassets') }}/js/rocket-loader.min.js" data-cf-settings="52f9a6b3eb160ed79a4c6ce3-|49" defer></script>
      <script src="{{ asset('adminassets') }}/plugins/summernote/summernote-bs5.min.js" type="e4f3de467030a5e2f382d6fd-text/javascript"></script>

      @yield('scripts')
   </body>
</html>
