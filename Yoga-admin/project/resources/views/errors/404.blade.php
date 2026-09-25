<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <title>404 Error </title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
      <meta content="Themesbrand" name="author" />
      <!-- App favicon -->
      <link rel="shortcut icon" href="{{ asset('adminassets') }}/images/favicon.ico">
      <!-- preloader css -->
      <link rel="stylesheet" href="{{ asset('adminassets') }}/css/preloader.min.css" type="text/css" />
      <!-- Bootstrap Css -->
      <link href="{{ asset('adminassets') }}/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
      <!-- Icons Css -->
      <link href="{{ asset('adminassets') }}/css/icons.min.css" rel="stylesheet" type="text/css" />
      <!-- App Css-->
      <link href="{{ asset('adminassets') }}/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
   </head>
   <body>
      <!-- <body data-layout="horizontal"> -->
      <div class="my-5 pt-5">
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <div class="text-center mb-5">
                     <h1 class="display-1 fw-semibold">4<span class="text-primary mx-2">0</span>4</h1>
                     <h4 class="text-uppercase">Sorry, page not found</h4>
                     <div class="mt-5 text-center">
                        <a class="btn btn-primary waves-effect waves-light" href="{{ route('webfront.index') }}">Back to Dashboard</a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row justify-content-center">
               <div class="col-md-10 col-xl-8">
                  <div>
                     <img src="{{ asset('adminassets') }}/images/error-img.png" alt="" class="img-fluid">
                  </div>
               </div>
               <!-- end row -->
            </div>
            <!-- end row -->
         </div>
         <!-- end container -->
      </div>
      <!-- end content -->
      <!-- JAVASCRIPT -->
      <script src="{{ asset('adminassets') }}/libs/jquery/jquery.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/metismenu/metisMenu.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/simplebar/simplebar.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/node-waves/waves.min.js"></script>
      <script src="{{ asset('adminassets') }}/libs/feather-icons/feather.min.js"></script>
      <!-- pace js -->
      <script src="{{ asset('adminassets') }}/libs/pace-js/pace.min.js"></script>
   </body>
</html>