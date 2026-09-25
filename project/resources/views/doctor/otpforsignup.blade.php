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
                              <h2>OTP Verification</h2>
                              @include('includes.admin.form-success') 
                              <form action="{{ route('admin.otpverifysignup') }}" method="get">
                                 @if (Session::get('fail'))
                                    <div class="alert alert-danger">
                                        {{ Session::get('fail') }}
                                    </div>
                                @endif
                                @csrf
                                <div class="input-block">
                                    <label>Please enter OTP <span class="login-danger">*</span></label>
                                    <input type="hidden" name="token" value="{{$token}}">
                                    <input required value="{{ old('otp') }}" name="otp" type="text" maxlength="6" class="form-control pl-15 bg-transparent" placeholder="OTP">
                                    <span class="text-danger">@error('emailmobileusername'){{ $message }}@enderror</span>
                                 </div>
                                 <div class="input-block login-btn">
                                    <button class="btn btn-primary btn-block">
                                    Verify
                                    </a>
                                 </div>
                              </form>
                              <div class="text-center">
                                 <p class="mt-15 mb-0">I did not recieve a code? <a href="{{ route('admin.resendotpforsignup',$token) }}" class="text-warning ml-5"><strong>RESEND</strong></a></p>
                                 <p><label>OTP: <span class="login-danger">{{$a1[0]}}</span></label>
                                 </p>
                              </div>
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
   </body>
</html>