<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <title>Login</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta content="" name="description" />
      <meta content="" name="author" />
      <!-- App favicon -->
      <link rel="shortcut icon" href="{{asset('adminassets')}}/images/favicon.png">
      <!-- preloader css -->
      <link rel="stylesheet" href="{{asset('adminassets')}}/css/preloader.min.css" type="text/css" />
      <!-- Bootstrap Css -->
      {{-- <link href="{{asset('adminassets')}}/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" /> --}}
      <!-- Icons Css -->
      <link href="{{asset('adminassets')}}/css/icons.min.css" rel="stylesheet" type="text/css" />
      <!-- App Css-->
      <link href="{{asset('adminassets')}}/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
      <link href="{{asset('adminassets')}}/css/custom.css" id="app-style" rel="stylesheet" type="text/css" />
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
      <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/css/select2.min.css" rel="stylesheet" />

   </head>
   <body>
      <!-- <body data-layout="horizontal"> -->
      <div class="auth-page1 auth-bg">
         <!--  <div class="bg-overlay bg-primary"></div> -->
         <div class="container-fluid p-0">
            <div class="row g-0">
               <div class="col-md-5 mx-auto">
                  <div class="auth-full-page-content d-flex p-sm-5 p-4">
                     <div class="w-100">
                        <div class="d-flex flex-column h-100">
                           <div class="mb-4 mb-md-6 text-center">
                              <a href="#" class="d-block auth-logo">
                                 <img src="{{asset('adminassets')}}/images/favicon.png" alt="" width="15%">                     
                              </a>
                           </div>
                           <div class="card">
                              <div class="card-body">
                                 <div class="auth-content my-auto">
                                    <div class="text-center">
                                       <h5 class="mb-0">Welcome Back !</h5>
                                       <p class="text-muted mt-2">Sign in to continue</p>
                                    </div>
                                    <form class="mt-4 pt-2" action="{{ route('admin.check') }}" method="post">
                                       @if (Session::get('fail'))
                                       <div class="alert alert-danger">
                                          {{ Session::get('fail') }}
                                       </div>
                                       @endif
                                       @csrf
                                       <div class="mb-3">
                                          <label class="form-label">Username</label>
                                          <input required value="{{ old('email') }}" name="email" type="email" class="form-control" id="username" placeholder="Enter username">
                                          <span class="text-danger">@error('email'){{ $message }}@enderror</span>
                                       </div>
                                       <div class="mb-4">
                                          <div class="d-flex align-items-start">
                                             <div class="flex-grow-1">
                                                <label class="form-label">Password</label>
                                             </div>
                                          </div>
                                          <div class="input-group auth-pass-inputgroup">
                                             <input required value="{{ old('password') }}" name="password" type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                             <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                          </div>
                                          <span class="text-danger">@error('password'){{ $message }}@enderror</span>
                                       </div>
                                       <div class="mb-3">
                                         <button class="btn w-100 waves-effect waves-light" 
                                                style="background-color:#888C79; border-color:#888C79; color:white;">
                                             Log In
                                          </button>

                                       </div>

                                    </form>
                                 </div>
                              </div>
                           </div><br>
                           <div class="mt-1 mt-md-1 text-center">
                              <p class="mb-0 text-white">
                                 © <script>document.write(new Date().getFullYear());</script> Yoga Admin Dashboard. 
                                 {{-- Crafted with <i class="mdi mdi-heart text-danger"></i> by  --}}
                                 {{-- <a href="https://www.appslure.com/" target="_blank" rel="noopener noreferrer" class="text-white">
                                    Appslure App Development Company
                                 </a> --}}
                              </p>
                           </div>

                        </div>
                     </div>
                  </div>
                  <!-- end auth full page content -->
               </div>
               <!-- end col -->
               <!-- end col -->
            </div>
            <!-- end row -->
         </div>
         <!-- end container fluid -->
      </div>
      <!-- JAVASCRIPT -->
      {{-- <script src="{{asset('adminassets')}}/libs/jquery/jquery.min.js"></script> --}}
      <script src="{{asset('adminassets')}}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="{{asset('adminassets')}}/libs/metismenu/metisMenu.min.js"></script>
      <script src="{{asset('adminassets')}}/libs/simplebar/simplebar.min.js"></script>
      <script src="{{asset('adminassets')}}/libs/node-waves/waves.min.js"></script>
      <script src="{{asset('adminassets')}}/libs/feather-icons/feather.min.js"></script>
      <!-- pace js -->
      <script src="{{asset('adminassets')}}/libs/pace-js/pace.min.js"></script>
      <!-- password addon init -->
      <script src="{{asset('adminassets')}}/js/pages/pass-addon.init.js"></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/js/select2.min.js"></script>

   </body>
</html>