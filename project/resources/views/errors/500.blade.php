@if (Request::is('admin/*'))
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <link rel="icon" href="{{asset('project/public/adminassets')}}/images/{{ config('GSsettings.favicon') }}">
      <title>{{ config('GSsettings.title') }} </title>
      <!-- Vendors Style-->
      <link rel="stylesheet" href="{{asset('project/public/adminassets')}}/css/vendors_css.css">
      <!-- Style-->  
      <link rel="stylesheet" href="{{asset('project/public/adminassets')}}/css/style.css">
      <link rel="stylesheet" href="{{asset('project/public/adminassets')}}/css/skin_color.css">
   </head>
   <body class="hold-transition theme-primary bg-img" style="background-image: url({{asset('project/public/adminassets')}}/images/auth-bg/bg-5.jpg)">
      <section class="error-page h-p100">
         <div class="container h-p100">
            <div class="row h-p100 align-items-center justify-content-center text-center">
               <div class="col-lg-7 col-md-10 col-12">
                  <div class="rounded30 p-50">
                     <img src="{{asset('project/public/adminassets')}}/images/auth-bg/500.jpg" class="max-w-200" alt="" />
                     <h1>Uh-Ah</h1>
                     <h3>Internal Server Error !</h3>
                     <div class="my-30"><a href="{{ route('admin.home') }}" class="btn btn-info">Back to dashboard</a></div>
                     <h5 class="mb-15">-- OR --</h5>
                     <h4>Please try after some time</h4>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- Vendor JS -->
      <script src="{{asset('project/public/adminassets')}}/js/vendors.min.js"></script>
      <script src="{{asset('project/public/adminassets')}}/js/pages/chat-popup.js"></script>
      <script src="{{asset('project/public/adminassets')}}/assets/icons/feather-icons/feather.min.js"></script>	
   </body>
</html>
@else
@extends('layouts.webfront')
@section('content')
<section class="bg-img pt-50 pb-20" data-overlay="7" style="background-image: url({{asset('assets/images/front-end-img/background/bg-1.jpg')}});">
   <div class="container">
      <div class="row">
         <div class="col-12">
            <div class="text-center">
               <h2 class="page-title text-white">500 Error</h2>
               <ol class="breadcrumb bg-transparent justify-content-center">
                  <li class="breadcrumb-item"><a href="{{route('webfront.index')}}" class="text-white-50"><i class="mdi mdi-home-outline"></i></a></li>
                  <li class="breadcrumb-item text-white active" aria-current="page">500 Error</li>
               </ol>
            </div>
         </div>
      </div>
   </div>
</section>
<!--Page content --> 
<section class="error-page h-p100 py-50">
   <div class="container h-p100">
      <div class="row h-p100 align-items-center justify-content-center text-center">
         <div class="col-lg-7 col-md-10 col-12">
            <div>
               <img src="{{asset('assets/images/500.png')}}" class="max-w-650 w-p100" alt="" />
               <h1>Uh-Ah</h1>
               <h3>Internal Server Error !</h3>
               <div class="my-30"><a href="{{route('webfront.index')}}" class="btn btn-primary">Back to Home</a></div>
               <h5 class="mb-15">-- OR --</h5>
               <h4>Please try after some time</h4>
            </div>
         </div>
      </div>
   </div>
</section>
@endsection
@endif