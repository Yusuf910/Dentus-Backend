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
                                 <a href="{{ route('admin.login') }}"><img src="{{asset('content/admin')}}/img/login-logo.svg" alt></a>
                              </div>
                              <h2>Login</h2>
                              @include('includes.admin.form-success') 
                              <form action="{{ route('admin.check') }}" method="post">
                                 @if (Session::get('fail'))
                                    <div class="alert alert-danger">
                                        {{ Session::get('fail') }}
                                    </div>
                                @endif
                                @csrf
                                 <div class="input-block">
                                    <label>Email <span class="login-danger">*</span></label>
                                    <input required class="form-control" type="text" value="{{ old('email') }}" name="email">
                                    <span class="text-danger">@error('email'){{ $message }}@enderror</span>
                                 </div>
                                 <div class="input-block">
                                    <label>Password <span class="login-danger">*</span></label>
                                    <input min="6" required class="form-control pass-input" type="password" value="{{ old('password') }}" name="password">
                                    <span class="text-danger">@error('password'){{ $message }}@enderror</span>
                                 </div>
                                 <div class="forgotpass">
                                    <a href="{{ route('admin.forget') }}">Forgot Password?</a>
                                 </div>
                                 
                                 <div class="input-block login-btn">
                                    <button class="btn btn-primary btn-block">
                                    Login
                                    </a>
                                 </div>
                                 <div class="next-sign">
                                 <p class="account-subtitle">
                                    <a href="{{ route('admin.signup') }}">Sign Up?</a>
                                    </p>
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
   </body>
</html>