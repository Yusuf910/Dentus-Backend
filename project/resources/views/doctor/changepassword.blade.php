<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
      <link rel="shortcut icon" type="image/x-icon" href="{{asset('content/admin')}}/img/favicon.png">
      <title>Dentus</title>
      <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/bootstrap.min.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/css/feather.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/plugins/fontawesome/css/fontawesome.min.css">
      <link rel="stylesheet" href="{{asset('content/admin')}}/plugins/fontawesome/css/all.min.css">
      <link rel="stylesheet" type="text/css" href="{{asset('content/admin')}}/css/style.css">
   </head>
   <body>
      <div class="main-wrapper login-body">
         <div class="container-fluid px-0">
            <div class="row">
               <div class="col-lg-6 login-wrap">
                  <div class="login-sec">
                     <div class="log-img">
                        <img class="img-fluid" src="{{asset('content/admin')}}/img/doc.png" alt="Logo">
                     </div>
                  </div>
               </div>
               <div class="col-lg-6 login-wrap-bg">
                  <div class="login-wrapper">
                     <div class="loginbox">
                        <div class="login-right">
                           <div class="login-right-wrap">
                              <div class="account-logo">
                                 <a href="{{route('admin.login')}}"><img src="{{asset('content/admin')}}/img/login-logo.svg" alt></a>
                              </div>
                              <h2>Change Password</h2>
                              @include('includes.admin.form-success') 
                              <p class="mb-0"><span class="text-danger"><span class="text-danger">@error('password')*{{ $message }} @enderror
                                 <p class="mb-0"><span class="text-danger"><span class="text-danger">@error('npassword')*{{ $message }} @enderror
                              <form action="{{ route('admin.changepassword') }}" method="post">
                                 @if (Session::get('fail'))
                                    <div class="alert alert-danger">
                                        {{ Session::get('fail') }}
                                    </div>
                                @endif
                                @csrf
                                 <div class="form-group">
                                    <input type="hidden" name="token" value="{{$token}}">

                                    <label>New Password</label>
                                    <div class="input-group mb-3">
                                       <input id="ng-password" type="password" name="password" class="form-control" placeholder="New Password">
                                       </div>
                                 </div>
                                 <div class="form-group">
                                    <label>Confirm Password</label>
                                    <div class="input-group mb-3">
                                       <input id="ng-repeatpassword" name="npassword" type="password" class="form-control" placeholder="Confirm Password">
                                       </div>
                                 </div>
                                 <span id='PWDmessage'></span>
                                 <div class="input-block login-btn">
                                    <button class="btn btn-primary btn-block">
                                    Change Password
                                    </a>
                                 </div>
                              </form>
                              
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script src="{{asset('content/admin')}}/js/jquery-3.7.1.min.js" type="javascript"></script>
      <script src="{{asset('content/admin')}}/js/bootstrap.bundle.min.js" type="javascript"></script>
      <script src="{{asset('content/admin')}}/js/feather.min.js" type="javascript"></script>
      <script src="{{asset('content/admin')}}/js/app.js" type="javascript"></script>
      <script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="7a4bad6af988fa55e5d4ea8e-|49" defer></script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

      <script>
         $('#ng-password, #ng-repeatpassword').on('keyup', function () {
           if ($('#ng-password').val() == $('#ng-repeatpassword').val()) {
             $('#PWDmessage').html('Matching').css('color', 'green');
           } else 
             $('#PWDmessage').html('Not Matching').css('color', 'red');
         });
      </script>
   </body>
</html>