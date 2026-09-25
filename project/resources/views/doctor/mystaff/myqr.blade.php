@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">My Qr Code</li>
               </ul>
            </div>
         </div>
      </div>
     
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-box">
                  <style>
                     .qr-box {
                     display: flex;
                     justify-content: center;
                     align-items: center;
                     height: 100vh;
                     background-color: #f8f9fa;
                     }
                     .card-qr {
                     text-align: center;
                     padding: 20px;
                     border-radius: 15px;
                     box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                     max-width: 400px;
                     background-color: #fff;
                     }
                     .qr-code img {
                     width: 200px;
                     height: 200px;
                     }
                  </style>
                  <div class="qr-box">
                     <div class="card-qr">
                        <img src="{{asset('content/doctor/'.auth()->user()->image)}}" alt="Profile Picture" width="100">
                        <h4>{{auth()->user()->name}}</h4>
                        <p>Connecting in a better way and Download my app using the QR code below</p>
                        <div class="qr-code mb-3">
                           <img src="{{asset('content/doctor/qrcode/'.auth()->user()->qrcode)}}" alt="QR Code">
                        </div>
                        <button class="btn btn-primary w-100 mb-2" onclick="shareQRCode()">Share my QR code</button>
                        <a href="{{asset('content/doctor/qrcode/'.auth()->user()->qrcode)}}" target="_blank"><button class="btn btn-secondary w-100">Save to gallery</button></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')
<script>
    function shareQRCode() {
        if (navigator.share) {
            navigator.share({
                title: 'My QR Code',
                text: 'Scan this QR code to connect with me!',
                url: "{{asset('content/doctor/qrcode/'.auth()->user()->qrcode)}}"
            })
            .then(() => console.log('Shared successfully'))
            .catch((error) => console.error('Error sharing:', error));
        } else {
            alert('Sharing not supported on this browser');
        }
    }
</script>
@endsection
@endsection
