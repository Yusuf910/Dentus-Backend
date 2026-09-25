<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <title>Staff List</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta content="" name="description" />
      <meta content="" name="author" />
      <!-- App favicon -->
      <link rel="shortcut icon" href="{{ asset('adminassets') }}/images/favicon.ico">
      <!-- DataTables -->
      <link href="{{ asset('adminassets') }}/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
      <link href="{{ asset('adminassets') }}/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
      <!-- Responsive datatable examples -->
      <link href="{{ asset('adminassets') }}/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
      <!-- preloader css -->
      <link rel="stylesheet" href="{{ asset('adminassets') }}/css/preloader.min.css" type="text/css" />
      <!-- Bootstrap Css -->
      <link href="{{ asset('adminassets') }}/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
      <!-- Icons Css -->
      <link href="{{ asset('adminassets') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
      <!-- App Css-->
      <link href="{{ asset('adminassets') }}/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
      <link href="{{ asset('adminassets') }}/css/custom.css" rel="stylesheet" type="text/css" />
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
                     <a href="index.html" class="logo logo-dark">
                     <span class="logo-sm">
                     <img src="{{ asset('adminassets') }}/images/logo-sm.svg" alt="" height="24">
                     </span>
                     <span class="logo-lg">
                     <img src="{{ asset('adminassets') }}/images/logo-sm.svg" alt="" height="24">
                     <span class="logo-txt">Eezywee</span>
                     </span>
                     </a>
                     <a href="index.html" class="logo logo-light">
                     <span class="logo-sm">
                     <img src="{{ asset('adminassets') }}/images/logo-sm.svg" alt="" height="24">
                     </span>
                     <span class="logo-lg">
                     <img src="{{ asset('adminassets') }}/images/logo-sm.svg" alt="" height="24">
                     <span class="logo-txt">Eezywee</span>
                     </span>
                     </a>
                  </div>
                  <button type="button" class="btn btn-sm px-3 font-size-20 header-item" id="vertical-menu-btn">
                  <i class="fa fa-fw fa-bars"></i>
                  </button>
                  <!-- App Search-->
               </div>
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
                     <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <i class="icon-lg mdi mdi-bell"></i>
                     <span class="badge bg-danger rounded-pill">5</span>
                     </button>
                     <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                        <div class="p-3">
                           <div class="row align-items-center">
                              <div class="col">
                                 <h6 class="m-0"> Notifications </h6>
                              </div>
                              <div class="col-auto">
                                 <a href="#!" class="small text-reset text-decoration-underline"> Unread (3)</a>
                              </div>
                           </div>
                        </div>
                        <div data-simplebar style="max-height: 230px;">
                           <a href="#!" class="text-reset notification-item">
                              <div class="d-flex">
                                 <div class="flex-shrink-0 avatar-sm me-3">
                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                    <i class="bx bx-badge-check"></i>
                                    </span>
                                 </div>
                                 <div class="flex-grow-1">
                                    <h6 class="mb-1">Lorem Ipsum</h6>
                                    <div class="font-size-13 text-muted">
                                       <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                       <p class="mb-0">
                                          <i class="mdi mdi-clock-outline"></i>
                                          <span>3 min ago</span>
                                       </p>
                                    </div>
                                 </div>
                              </div>
                           </a>
                           <a href="#!" class="text-reset notification-item">
                              <div class="d-flex">
                                 <div class="flex-shrink-0 avatar-sm me-3">
                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                    <i class="bx bx-badge-check"></i>
                                    </span>
                                 </div>
                                 <div class="flex-grow-1">
                                    <h6 class="mb-1">Lorem Ipsum</h6>
                                    <div class="font-size-13 text-muted">
                                       <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                       <p class="mb-0">
                                          <i class="mdi mdi-clock-outline"></i>
                                          <span>3 min ago</span>
                                       </p>
                                    </div>
                                 </div>
                              </div>
                           </a>
                           <a href="#!" class="text-reset notification-item">
                              <div class="d-flex">
                                 <div class="flex-shrink-0 avatar-sm me-3">
                                    <span class="avatar-title bg-success rounded-circle font-size-16">
                                    <i class="bx bx-badge-check"></i>
                                    </span>
                                 </div>
                                 <div class="flex-grow-1">
                                    <h6 class="mb-1">Lorem Ipsum</h6>
                                    <div class="font-size-13 text-muted">
                                       <p class="mb-1">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                                       <p class="mb-0">
                                          <i class="mdi mdi-clock-outline"></i>
                                          <span>3 min ago</span>
                                       </p>
                                    </div>
                                 </div>
                              </div>
                           </a>
                        </div>
                        <div class="p-2 border-top d-grid">
                           <a class="btn btn-sm btn-link font-size-12 text-center" href="javascript:void(0)">
                           <i class="mdi mdi-arrow-right-circle me-1"></i>
                           <span>View More..</span>
                           </a>
                        </div>
                     </div>
                  </div>
                  <div class="dropdown d-inline-block">
                     <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <img class="rounded-circle header-profile-user" src="{{ asset('adminassets') }}/images/users/avatar-1.jpg" alt="Header Avatar">
                     <span class="d-none d-xl-inline-block ms-1 fw-medium">Shawn Lawn</span>
                     <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                     </button>
                     <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a class="dropdown-item" href="#">
                        <i class="mdi mdi-face-profile font-size-16 align-middle me-1"></i> Profile </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                        <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout </a>
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
                        <a href="index.html">
                        <i class="mdi mdi-home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                        </a>
                     </li>
                     <li>
                        <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span data-key="t-apps">Admin Management</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                           <li>
                              <a href="staff.html">
                              <span data-key="t-calendar">Staff</span>
                              </a>
                           </li>
                           <li>
                              <a href="roles.html">
                              <span data-key="t-calendar">Roles</span>
                              </a>
                           </li>
                        </ul>
                     </li>
                     <li>
                        <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-sitemap-outline"></i>
                        <span data-key="t-apps">Master</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                           <li>
                              <a href="category.html">
                              <span data-key="t-calendar">Category</span>
                              </a>
                           </li>
                           <li>
                              <a href="sub-category.html">
                              <span data-key="t-calendar">Sub Category</span>
                              </a>
                           </li>
                           <li>
                              <a href="work-size.html">
                              <span data-key="t-calendar">Work Size</span>
                              </a>
                           </li>
                           <li>
                              <a href="loyalty-point-management.html">
                              <span data-key="t-calendar">Loyalty Point Management</span>
                              </a>
                           </li>
                           <li>
                              <a href="incentive-provider.html">
                              <span data-key="t-calendar">Incentive Provider</span>
                              </a>
                           </li>
                           <li>
                              <a href="rate-management.html">
                              <span data-key="t-calendar">Rate Management</span>
                              </a>
                           </li>
                        </ul>
                     </li>
                     <li>
                        <a href="user-list.html">
                        <i class="mdi mdi-account"></i>
                        <span data-key="t-horizontal">Manage User</span>
                        </a>
                     </li>
                     <li>
                        <a href="#" class="has-arrow"> <i class="mdi mdi-history"></i> <span data-key="t-authentication">Booking Mgmt</span> </a>
                        <ul class="treeview-menu">
                           <li class="">
                              <a href="all-booking.html">All Bookings</a>
                           </li>
                           <li class="">
                              <a href="upcoming-booking.html">Upcoming</a>
                           </li>
                           <li class="">
                              <a href="completed-booking.html">Completed</a>
                           </li>
                           <li class="">
                              <a href="cancel-booking.html">Cancel</a>
                           </li>
                        </ul>
                     </li>
                     <li>
                        <a href="#" class="has-arrow"> <i class="mdi mdi-account-tie"></i> <span data-key="t-authentication">Providers Mgmt</span> </a>
                        <ul class="treeview-menu">
                           <li class="">
                              <a href="verified-provider.html">Verified Providers</a>
                           </li>
                           <li class="">
                              <a href="pending-provider.html">Pending Providers</a>
                           </li>
                           <li class="">
                              <a href="inactive-provider.html">Inactive Providers</a>
                           </li>
                           <li class="">
                              <a href="reviews-provider.html">Providers Reviews</a>
                           </li>
                        </ul>
                     </li>
                     <li>
                        <a href="transaction.html">
                        <i class="mdi mdi-currency-usd"></i>
                        <span data-key="t-horizontal">Transactions</span>
                        </a>
                     </li>
                     <li>
                        <a href="discount-coupon.html">
                        <i class="mdi mdi-rss-box"></i>
                        <span data-key="t-horizontal">Discount Coupon</span>
                        </a>
                     </li>
                     <li>
                        <a href="banner.html">
                        <i class="mdi mdi-rss-box"></i>
                        <span data-key="t-horizontal">Banner</span>
                        </a>
                     </li>
                     <li>
                        <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-bell"></i>
                        <span data-key="t-apps">Send Notification</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                           <li>
                              <a href="notification-user.html">
                              <span data-key="t-calendar">User</span>
                              </a>
                           </li>
                           <li>
                              <a href="notification-provider.html">
                              <span data-key="t-calendar">Providers</span>
                              </a>
                           </li>
                           <li>
                              <a href="notification-all.html">
                              <span data-key="t-calendar">All</span>
                              </a>
                           </li>
                        </ul>
                     </li>
                     <li>
                        <a href="javascript: void(0);" class="has-arrow">
                        <i class="mdi mdi-cog"></i>
                        <span data-key="t-apps">Settings</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                           <li>
                              <a href="settings.html">
                              <span data-key="t-calendar">Cong Settings</span>
                              </a>
                           </li>
                           <li>
                              <a href="about.html">
                              <span data-key="t-calendar">About Us</span>
                              </a>
                           </li>
                           <li>
                              <a href="terms.html">
                              <span data-key="t-calendar">Terms & Condition</span>
                              </a>
                           </li>
                           <li>
                              <a href="privacy.html">
                              <span data-key="t-calendar">Privacy Policy</span>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </div>
               <!-- Sidebar -->
            </div>
         </div>
         <!-- Left Sidebar End -->
         <!-- ============================================================== -->
         <!-- Start right Content here -->
         <!-- ============================================================== -->
         <div class="main-content">
            <div class="page-content">
               <div class="container-fluid">
                  <!-- start page title -->
                  <div class="row">
                     <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                           <h4 class="mb-sm-0 font-size-18">Staff List</h4>
                           <div class="page-title-right">
                              <div class="d-flex flex-wrap gap-2">
                                 <a href="add-staff.html" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Add
                                 </a> 
                                 <!-- <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                                    </button> -->
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- end page title -->
                  <div class="row">
                     <div class="col-12">
                        <div class="card">
                           <div class="card-body table-responsive">
                              <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                                 <thead>
                                    <tr>
                                       <th>#ID</th>
                                       <th>Name</th>
                                       <th>Email</th>
                                       <th>Mobile No.</th>
                                       <th>Created at</th>
                                       <th>Roles</th>
                                       <th>Permission</th>
                                       <th>Status</th>
                                       <th>Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr>
                                       <td>1</td>
                                       <td>Deepak Rajor</td>
                                       <td>deepak@gmail.com</td>
                                       <td>8279417800</td>
                                       <td>2023-03-28 00:15:41</td>
                                       <td><strong>Operations</strong></td>
                                       <td>
                                          <div class="d-flex flex-wrap gap-2 mt-1">
                                             <span class="badge bg-primary">User</span>
                                             <span class="badge bg-primary">Banner</span>
                                             <span class="badge bg-primary">Videos</span>
                                             <span class="badge bg-primary">Blog</span>
                                          </div>
                                       </td>
                                       <td>
                                          <span class="badge bg-success">Active</span>
                                       </td>
                                       <td>
                                          <button type="button" class="btn btn-warning waves-effect waves-light btn-sm">
                                          <i class="mdi mdi-pencil d-block font-size-12"></i>
                                          </button>
                                          <button type="button" class="btn btn-danger waves-effect waves-light btn-sm">
                                          <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                          </button>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>2</td>
                                       <td>Deepak Rajor</td>
                                       <td>deepak@gmail.com</td>
                                       <td>8279417800</td>
                                       <td>2023-03-28 00:15:41</td>
                                       <td><strong>Operations</strong></td>
                                       <td>
                                          <div class="d-flex flex-wrap gap-2 mt-1">
                                             <span class="badge bg-primary">User</span>
                                             <span class="badge bg-primary">Banner</span>
                                             <span class="badge bg-primary">Videos</span>
                                             <span class="badge bg-primary">Blog</span>
                                          </div>
                                       </td>
                                       <td>
                                          <span class="badge bg-success">Active</span>
                                       </td>
                                       <td>
                                          <button type="button" class="btn btn-warning waves-effect waves-light btn-sm">
                                          <i class="mdi mdi-pencil d-block font-size-12"></i>
                                          </button>
                                          <button type="button" class="btn btn-danger waves-effect waves-light btn-sm">
                                          <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                          </button>
                                       </td>
                                    </tr>
                                    <tr>
                                       <td>3</td>
                                       <td>Deepak Rajor</td>
                                       <td>deepak@gmail.com</td>
                                       <td>8279417800</td>
                                       <td>2023-03-28 00:15:41</td>
                                       <td><strong>Operations</strong></td>
                                       <td>
                                          <div class="d-flex flex-wrap gap-2 mt-1">
                                             <span class="badge bg-primary">User</span>
                                             <span class="badge bg-primary">Banner</span>
                                             <span class="badge bg-primary">Videos</span>
                                             <span class="badge bg-primary">Blog</span>
                                          </div>
                                       </td>
                                       <td>
                                          <span class="badge bg-success">Active</span>
                                       </td>
                                       <td>
                                          <button type="button" class="btn btn-warning waves-effect waves-light btn-sm">
                                          <i class="mdi mdi-pencil d-block font-size-12"></i>
                                          </button>
                                          <button type="button" class="btn btn-danger waves-effect waves-light btn-sm">
                                          <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                          </button>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                     <!-- end col -->
                  </div>
                  <!-- end row -->
               </div>
               <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <footer class="footer">
               <div class="container-fluid">
                  <div class="row">
                     <div class="col-sm-6">
                        <script>
                           document.write(new Date().getFullYear())
                        </script> © Eezywee.
                     </div>
                     <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
                  </div>
               </div>
            </footer>
         </div>
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
      <script src="{{ asset('adminassets') }}/js/app.js"></script>
   </body>
</html>