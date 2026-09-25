<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('adminassets') }}/img/favicon.png">
<title>Dentus</title>

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
        <h2>Login</h2>

        <form class="mt-4 pt-2" action="{{ route('admin.check') }}" method="post">
            @if (Session::get('fail'))
                <div class="alert alert-danger">
                    {{ Session::get('fail') }}
                </div>
            @endif
            @csrf
        <div class="input-block">
        {{-- <label>Email <span class="login-danger">*</span></label>
        <input class="form-control" type="text"> --}}
        <label>Email <span class="login-danger">*</span></label>
        <input required value="{{ old('email') }}" name="email" type="email" class="form-control" id="username" placeholder="">
        <span class="text-danger">@error('email'){{ $message }}@enderror</span>
        </div>
        <div class="input-block">
        <label>Password <span class="login-danger">*</span></label>
        {{-- <input class="form-control pass-input" type="password">
        <span class="profile-views feather-eye-off toggle-password"></span> --}}
        <input required value="{{ old('password') }}" name="password" type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
        <span class="profile-views feather-eye-off toggle-password" type="button" id="password-addon"></span>
        </div>
        <span class="text-danger">@error('password'){{ $message }}@enderror</span>
        <div class="forgotpass">
        <div class="remember-me">
        <label class="custom_check mr-2 mb-0 d-inline-flex remember-me"> Remember me
        <input type="checkbox" name="radio">
        <span class="checkmark"></span>
        </label>
        </div>
        {{-- <a href="#">Forgot Password?</a> --}}
        </div>
        <div class="input-block login-btn">
        {{-- <a href="index.html" class="btn btn-primary btn-block">
        Login
        </a> --}}
        <button class="btn btn-primary btn-block">Log In</button>
        </div>
        </form>

        <div class="next-sign">
        <p class="account-subtitle">Need an account? <a href="{{ route('user.register') }}">Sign Up</a></p>

        <!-- <div class="social-login">
        <a href="javascript:;"><img src="{{ asset('adminassets') }}/img/icons/login-icon-01.svg" alt></a>
        <a href="javascript:;"><img src="{{ asset('adminassets') }}/img/icons/login-icon-02.svg" alt></a>
        <a href="javascript:;"><img src="{{ asset('adminassets') }}/img/icons/login-icon-03.svg" alt></a>
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


<script src="{{ asset('adminassets') }}/js/jquery-3.7.1.min.js" type="javascript"></script>

<script src="{{ asset('adminassets') }}/js/bootstrap.bundle.min.js" type="javascript"></script>

<script src="{{ asset('adminassets') }}/js/feather.min.js" type="javascript"></script>

<script src="{{ asset('adminassets') }}/js/app.js" type="javascript"></script>
<script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="7a4bad6af988fa55e5d4ea8e-|49" defer></script>
</body>

</html>
