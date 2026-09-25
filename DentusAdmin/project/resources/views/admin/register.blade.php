<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('adminassets') }}/img/favicon.png">
<title>Register</title>

<link rel="stylesheet" type="text/css" href="{{ asset('adminassets') }}/css/bootstrap.min.css">

<link rel="stylesheet" href="{{ asset('adminassets') }}/css/feather.css">

<link rel="stylesheet" href="{{ asset('adminassets') }}/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="{{ asset('adminassets') }}/plugins/fontawesome/css/all.min.css">

<link rel="stylesheet" type="text/css" href="{{ asset('adminassets') }}/css/style.css">
</head>
<body>

<div class="main-wrapper login-body">
<div class="container-fluid px-0">
<div class="row">

<div class="col-lg-6 login-wrap">
<div class="login-sec">
<div class="log-img">
<img class="img-fluid" src="{{ asset('adminassets') }}/img/doc.png" alt="Logo">
</div>
</div>
</div>


<div class="col-lg-6 login-wrap-bg">
<div class="login-wrapper">
<div class="loginbox">
<div class="login-right">
<div class="login-right-wrap">
<div class="account-logo">
<a href="index.html"><img src="{{ asset('adminassets') }}/img/login-logo.svg" alt></a>
</div>
<h2>Register</h2>

<form class="mt-4 pt-2" action="{{ route('user.create') }}" method="post">
    @if (Session::get('fail'))
        <div class="alert alert-danger">
            {{ Session::get('fail') }}
        </div>
    @endif
    @csrf
<div class="input-block">
<label>Full Name <span class="login-danger">*</span></label>
<input class="form-control" type="text" name="name">
<span class="text-danger">@error('name'){{ $message }}@enderror</span>
</div>
<div class="input-block">
<label>Email <span class="login-danger">*</span></label>
<input class="form-control" type="email" name="email">
<span class="text-danger">@error('email'){{ $message }}@enderror</span>
</div>
<div class="input-block">
<label>Mobile No. <span class="login-danger">*</span></label>
<input class="form-control" type="number" name="mobile">
<span class="text-danger">@error('mobile'){{ $message }}@enderror</span>
</div>
<div class="input-block">
<label>Password <span class="login-danger">*</span></label>
<input class="form-control pass-input" type="password" name="password">
<span class="text-danger">@error('password'){{ $message }}@enderror</span>
<span class="profile-views feather-eye-off toggle-password"></span>
</div>

<div class="input-block login-btn">
{{-- <a href="otp.html" class="btn btn-primary btn-block">
  Sign Up
</a> --}}
<button class="btn btn-primary btn-block"> Sign Up</button>

</div>
</form>

<div class="next-sign">
<p class="account-subtitle">Already have account? <a href="{{ route('user.login') }}">Login</a></p>

<!-- <div class="social-login">
<a href="javascript:;"><img src="assets/img/icons/login-icon-01.svg" alt></a>
<a href="javascript:;"><img src="assets/img/icons/login-icon-02.svg" alt></a>
<a href="javascript:;"><img src="assets/img/icons/login-icon-03.svg" alt></a>
</div> -->

</div>
</div>
</div>
</div>
</div>
</div>

</div>
</div>
</div>


<script src="assets/js/jquery-3.7.1.min.js" type="javascript"></script>

<script src="assets/js/bootstrap.bundle.min.js" type="javascript"></script>

<script src="assets/js/feather.min.js" type="javascript"></script>

<script src="assets/js/app.js" type="javascript"></script>
<script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="809e4acc3f2824340c8d23dd-|49" defer></script>

</body>
</html>
