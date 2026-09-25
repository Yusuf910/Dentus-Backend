<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('adminassets') }}/img/favicon.png">
<title>OTP</title>

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
<h2>OTP Verification</h2>

{{-- <form action="" id="otp-screen"> --}}
<form class="mt-4 pt-2" action="{{ route('user.signup_otpmatch') }}" method="post">
        @if (Session::get('fail'))
            <div class="alert alert-danger">
                {{ Session::get('fail') }}
            </div>
        @endif
        @csrf
<div class="input-block">
<!-- <label>OTP <span class="login-danger">*</span></label> -->
<!-- <input class="form-control" type="text"> -->
<div class="row g-3">
    <div class="col">
      <input type="text" class="form-control text-center text-6 py-2" maxlength="1" required autocomplete="off" name="otp[]" />
    </div>
    <div class="col">
      <input type="text" class="form-control text-center text-6 py-2" maxlength="1" required autocomplete="off" name="otp[]" />
    </div>
    <div class="col">
      <input type="text" class="form-control text-center text-6 py-2" maxlength="1" required autocomplete="off" name="otp[]" />
    </div>
    <div class="col">
      <input type="text" class="form-control text-center text-6 py-2" maxlength="1" required autocomplete="off" name="otp[]" />
    </div>
  </div>

</div>
<div class="input-block login-btn">
{{-- <a class="btn btn-primary btn-block" href="index.html">Verify</a> --}}
<button type="submit" class="btn btn-primary btn-block">Verify</button>
</div>
</form>

<div class="next-sign">
<p class="account-subtitle">Didn't receive the code? <a href="#">Resend Code</a></p>



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
<script type="text/javascript">
   // OTP Form (Focusing on next input)
$("#otp-screen .form-control").keyup(function() {
if (this.value.length == 0) {
   $(this).blur().parent().prev().children('.form-control').focus();
   $(this).blur().prev('.form-control').focus();
}
else if (this.value.length == this.maxLength) {
   $(this).blur().parent().next().children('.form-control').focus();
   $(this).blur().next('.form-control').focus();
}
});
</script>

<script src="{{ asset('adminassets') }}/js/bootstrap.bundle.min.js" type="javascript"></script>

<script src="{{ asset('adminassets') }}/js/feather.min.js" type="javascript"></script>

<script src="{{ asset('adminassets') }}/js/app.js" type="javascript"></script>

<script src="../../cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="e870d3d70f84871748e3da44-|49" defer></script>



</body>
</html>
