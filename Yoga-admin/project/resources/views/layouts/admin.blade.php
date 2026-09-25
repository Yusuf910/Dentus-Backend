<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <title>Yoga</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta content="" name="description" />
      <meta content="" name="author" />
      <!-- App favicon -->
      <link rel="shortcut icon" href="{{ asset('adminassets') }}/images/favicon.png">
      <!-- plugin css -->
      <link href="{{ asset('adminassets') }}/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
      <!-- preloader css -->
      <link rel="stylesheet" href="{{ asset('adminassets') }}/css/preloader.min.css" type="text/css" />
      <!-- Bootstrap Css -->
      <link href="{{ asset('adminassets') }}/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
      <!-- Icons Css -->
      <link href="{{ asset('adminassets') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
      <!-- App Css-->
      <link href="{{ asset('adminassets') }}/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
      <link href="{{ asset('adminassets') }}/css/custom.css" rel="stylesheet" type="text/css" />
      <link href="{{ asset('adminassets') }}/libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" type="text/css" />
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/css/select2.min.css" rel="stylesheet" />

      {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
      <style>
         span.select2.select2-container.select2-container--classic{
         width: 100% !important;
         }
      </style>
      @yield('styles')
   </head>
   <body>
      <!-- <body data-layout="horizontal"> -->
      <!-- Begin page -->
      <div id="layout-wrapper">
         <header id="page-topbar">
            <div class="navbar-header">
               <div class="d-flex">
                  <!-- LOGO -->
                  <div class="navbar-brand-box">
                     <a href="{{ route('admin.home') }}" class="logo logo-dark">
                     <span class="logo-sm">
                     <img src="{{ asset('adminassets') }}/images/favicon.png" alt="" height="24">
                     </span>
                     <span class="logo-lg">
                     <img src="{{ asset('adminassets') }}/images/favicon.png" alt="" height="50">
                     <span class="logo-txt">Yoga</span>
                     </span>
                     </a>
                     <a href="{{ route('admin.home')   }}" class="logo logo-light">
                     <span class="logo-sm">
                     <img src="{{ asset('adminassets') }}/images/favicon.png" alt="" height="24">
                     </span>
                     <span class="logo-lg">
                     <img src="{{ asset('adminassets') }}/images/favicon.png" alt="" height="24">
                     <span class="logo-txt">Yoga</span>
                     </span>
                     </a>
                  </div>
                  <button type="button" class="btn btn-sm px-3 font-size-15 header-item" id="vertical-menu-btn">
                  <i class="fa fa-fw fa-bars"></i>
                  </button>
                  <!-- App Search-->
               </div>
               <?php $noti = 0; ?>
               @if(Auth::guard('admin')->user()->IsSuper())
               <?php $noti = 1; ?>
               @else
               @if(Auth::guard('admin')->user()->hasPermission('admin-notification'))
               <?php $noti = 1; ?>
               @endif
               @endif
               <div class="d-flex">
                  <div class="dropdown d-inline-block d-lg-none ms-2">
                     <button type="button" class="btn header-item" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i data-feather="search" class="icon-lg"></i>
                     </button>
                     <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                        <form class="p-3">
                           <div class="form-group m-0">
                              <div class="input-group">
                                 <input type="text" class="form-control" placeholder="Search ..." aria-label="Search Result">
                                 <button class="btn btn-primary" type="submit">
                                 <i class="mdi mdi-magnify"></i>
                                 </button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
                  
                  
                  <div class="dropdown d-inline-block">
                     <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img class="rounded-circle header-profile-user" src="{{ asset('adminassets') }}/images/users/avatar-1.jpg" alt="Header Avatar">
                     <span class="d-none d-xl-inline-block ms-1 fw-medium">{{Auth()->guard('admin')->user()->name}}</span>
                     <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        {{-- <a class="dropdown-item" href="{{ route('admin.admins.show',Auth()->guard('admin')->user()->id) }}">
                        <i class="mdi mdi-face-profile font-size-15 align-middle me-1"></i> Profile </a> --}}
                        {{-- <div class="dropdown-divider"></div> --}}
                        <a class="dropdown-item" href="{{ route('admin.logout') }}">
                        <i class="mdi mdi-logout font-size-15 align-middle me-1"></i> Logout </a>
                     </div>
                  </div>
               </div>
            </div>
         </header>
         <!-- ========== Left Sidebar Start ========== -->
         <div class="vertical-menu">
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
                     </li>
                     @if(Auth::guard('admin')->user()->IsSuper())
                     @include('sidebar.admin.super')
                     @else
                     @include('sidebar.admin.normal')
                     @endif
                  </ul>
               </div>
               <!-- Sidebar -->
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
      <script src="{{ asset('adminassets') }}/libs/jquery/jquery.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/metismenu/metisMenu.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/simplebar/simplebar.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/node-waves/waves.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/feather-icons/feather.min.js"></script>
      <!-- pace js -->
      <script src="{{ asset('adminassets') }}/libs/pace-js/pace.min.js"></script>
      <!-- dashboard init -->
      <script src="{{ asset('adminassets') }}/js/pages/dashboard.init.js"></script>
      <script src="{{ asset('adminassets') }}/js/app.js"></script>
      <!-- Drag & Drop js-->
      <script src="{{ asset('adminassets') }}/js/pages/jquery.min.js"></script>
      <script src="{{ asset('adminassets') }}/js/pages/jquery-ui.js"></script>
      <!-- Required datatable js -->
      <script src="{{ asset('adminassets') }}/libs/datatables.net/js/jquery.dataTables.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
      <!-- Buttons examples -->
      <script src="{{ asset('adminassets') }}/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/jszip/jszip.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/pdfmake/build/pdfmake.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/pdfmake/build/vfs_fonts.js"></script>
      <script src="{{ asset('adminassets') }}/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
      <!-- Responsive examples -->
      <script src="{{ asset('adminassets') }}/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
      <!-- Datatable init js -->
      <script src="{{ asset('adminassets') }}/js/pages/datatables.init.js"></script>
      <!-- choices js -->
      <script src="{{ asset('adminassets') }}/libs/choices.js/public/assets/scripts/choices.min.js"></script>
      <script src="{{ asset('adminassets') }}/js/pages/form-advanced.init.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/js/select2.min.js"></script>

      {{-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script> --}}
      @yield('scripts')
   </body>
</html>