@extends('layouts.admin')
@section('content')


<style type="text/css">
   /*-----------------
   26. Chat
   -----------------------*/
   .chat-page .content {
   padding: 0;
   }
   .content{
   margin-top: 50px;
   }
   .chat-page .content > .container-fluid {
   padding: 0;
   }
   .chat-page .footer {
   display: none;
   }
   .chat-window {
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   -ms-flex-wrap: wrap;
   flex-wrap: wrap;
   position: relative;
   }
   .chat-cont-left {
   border-right: 1px solid #f0f0f0;
   -ms-flex: 0 0 35%;
   flex: 0 0 35%;
   left: 0;
   max-width: 35%;
   position: relative;
   z-index: 4;
   }
   .avatar-online::before {
   background-color: #00e65b;
   }
   .avatar-away::before, .avatar-offline::before, .avatar-online::before {
   position: absolute;
   right: 0;
   bottom: 0;
   width: 25%;
   height: 25%;
   border-radius: 50%;
   content: '';
   border: 2px solid #fff;
   }
   .avatar-away::before {
   background-color: #ffbc34;
   }
   .avatar-away::before, .avatar-offline::before, .avatar-online::before {
   position: absolute;
   right: 0;
   bottom: 0;
   width: 25%;
   height: 25%;
   border-radius: 50%;
   content: '';
   border: 2px solid #fff;
   }
   .avatar-offline::before {
   background-color: #ff0100;
   }
   .avatar-away::before, .avatar-offline::before, .avatar-online::before {
   position: absolute;
   right: 0;
   bottom: 0;
   width: 25%;
   height: 25%;
   border-radius: 50%;
   content: '';
   border: 2px solid #fff;
   }
   .chat-cont-left .chat-header {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   background-color: #fff;
   border-bottom: 1px solid #f0f0f0;
   color: #272b41;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   height: 72px;
   justify-content: space-between;
   -webkit-justify-content: space-between;
   -ms-flex-pack: space-between;
   padding: 0 15px;
   }
   .chat-cont-left .chat-header span {
   font-size: 20px;
   font-weight: 500;
   text-transform: capitalize;
   }
   .chat-cont-left .chat-header .chat-compose {
   color: #8a8a8a;
   display: -webkit-inline-box;
   display: -ms-inline-flexbox;
   display: inline-flex;
   }
   .chat-cont-left .chat-search {
   background-color: #f5f5f6;
   border-bottom: 1px solid #e5e5e5;
   padding: 10px 15px;
   width: 100%;
   }
   .chat-cont-left .chat-search .input-group {
   width: 100%;
   }
   .chat-cont-left .chat-search .input-group .form-control {
   background-color: #fff;
   border-radius: 50px;
   padding-left: 36px;
   }
   .chat-cont-left .chat-search .input-group .form-control:focus {
   border-color: #ccc;
   box-shadow: none;
   }
   .chat-cont-left .chat-search .input-group .input-group-prepend {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   bottom: 0;
   color: #666;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   left: 15px;
   pointer-events: none;
   position: absolute;
   top: 0;
   z-index: 4;
   }
   .chat-window .chat-scroll {
   min-height: 300px;
   max-height: calc(100vh - 224px);
   overflow-y: auto;
   }
   .chat-cont-left .chat-users-list {
   background-color: #fff;
   }
   .chat-cont-left .chat-users-list a.media {
   border-bottom: 1px solid #f0f0f0;
   padding: 10px 15px;
   transition: all 0.2s ease 0s;
   }
   .chat-cont-left .chat-users-list a.media:last-child {
   border-bottom: 0;
   }
   .chat-cont-left .chat-users-list a.media .media-img-wrap {
   margin-right: 15px;
   position: relative;
   }
   .chat-cont-left .chat-users-list a.media .media-img-wrap .avatar {
   height: 45px;
   width: 45px;
   }
   .chat-cont-left .chat-users-list a.media .media-img-wrap .status {
   bottom: 7px;
   height: 10px;
   right: 4px;
   position: absolute;
   width: 10px;
   border: 2px solid #fff;
   }
   .chat-cont-left .chat-users-list a.media .media-body {
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   justify-content: space-between;
   -webkit-justify-content: space-between;
   -ms-flex-pack: space-between;
   }
   .chat-cont-left .chat-users-list a.media .media-body > div:first-child .user-name,
   .chat-cont-left .chat-users-list a.media .media-body > div:first-child .user-last-chat {
   max-width: 250px;
   overflow: hidden;
   text-overflow: ellipsis;
   white-space: nowrap;
   }
   .chat-cont-left .chat-users-list a.media .media-body > div:first-child .user-name {
   color: #272b41;
   text-transform: capitalize;
   }
   .chat-cont-left .chat-users-list a.media .media-body > div:first-child .user-last-chat {
   color: #8a8a8a;
   font-size: 14px;
   line-height: 24px;
   }
   .chat-cont-left .chat-users-list a.media .media-body > div:last-child {
   text-align: right;
   }
   .chat-cont-left .chat-users-list a.media .media-body > div:last-child .last-chat-time {
   color: #8a8a8a;
   font-size: 13px;
   }
   .chat-cont-left .chat-users-list a.media:hover {
   background-color: #f5f5f6;
   }
   .chat-cont-left .chat-users-list a.media.read-chat .media-body > div:last-child .last-chat-time {
   color: #8a8a8a;
   }
   .chat-cont-left .chat-users-list a.media.active {
   background-color: #f5f5f6;
   }
   .chat-cont-right {
   -ms-flex: 0 0 100%;
   flex: 0 0 100%;
   max-width: 100%;
   }
   .chat-cont-right .chat-header {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   background-color: #fff;
   border-bottom: 1px solid #f0f0f0;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   height: 72px;
   justify-content: space-between;
   -webkit-justify-content: space-between;
   -ms-flex-pack: space-between;
   padding: 0 15px;
   }
   .chat-cont-right .chat-header .back-user-list {
   display: none;
   margin-right: 5px;
   margin-left: -7px;
   }
   .chat-cont-right .chat-header .media {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   }
   .chat-cont-right .chat-header .media .media-img-wrap {
   position: relative;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   margin-right: 15px;
   }
   .chat-cont-right .chat-header .media .media-img-wrap .avatar {
   height: 50px;
   width: 50px;
   }
   .chat-cont-right .chat-header .media .media-img-wrap .status {
   border: 2px solid #fff;
   bottom: 0;
   height: 10px;
   position: absolute;
   right: 3px;
   width: 10px;
   }
   .chat-cont-right .chat-header .media .media-body .user-name {
   color: #272b41;
   font-size: 16px;
   font-weight: 500;
   text-transform: capitalize;
   }
   .chat-cont-right .chat-header .media .media-body .user-status {
   color: #666;
   font-size: 14px;
   }
   .chat-cont-right .chat-header .chat-options {
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   }
   .chat-cont-right .chat-header .chat-options > a {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   border-radius: 50%;
   color: #8a8a8a;
   display: -webkit-inline-box;
   display: -ms-inline-flexbox;
   display: inline-flex;
   height: 30px;
   justify-content: center;
   -webkit-justify-content: center;
   -ms-flex-pack: center;
   margin-left: 10px;
   width: 30px;
   }
   .chat-cont-right .chat-body {
   background-color: #f5f5f6;
   }
   .chat-cont-right .chat-body ul.list-unstyled {
   margin: 0 auto;
   padding: 15px;
   width: 100%;
   }
   .chat-cont-right .chat-body .media .avatar {
   height: 30px;
   width: 30px;
   }
   .chat-cont-right .chat-body .media .media-body {
   margin-left: 20px;
   }
   .chat-cont-right .chat-body .media .media-body .msg-box > div {
   padding: 10px 15px;
   border-radius: .25rem;
   display: inline-block;
   position: relative;
   }
   .chat-cont-right .chat-body .media .media-body .msg-box > div p {
   color: #272b41;
   margin-bottom: 0;
   }
   .chat-cont-right .chat-body .media .media-body .msg-box + .msg-box {
   margin-top: 5px;
   }
   .chat-cont-right .chat-body .media.received {
   margin-bottom: 20px;
   }
   .chat-cont-right .chat-body .media:last-child {
   margin-bottom: 0;
   }
   .chat-cont-right .chat-body .media.received .media-body .msg-box > div {
   background-color: #fff;
   }
   .chat-cont-right .chat-body .media.sent {
   margin-bottom: 20px;
   }
   .chat-cont-right .chat-body .media.sent .media-body {
   -webkit-box-align: flex-end;
   -ms-flex-align: flex-end;
   align-items: flex-end;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   -ms-flex-direction: column;
   flex-direction: column;
   justify-content: flex-end;
   -webkit-justify-content: flex-end;
   -ms-flex-pack: flex-end;
   margin-left: 0;
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div {
   background-color: #e3e3e3;
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div p {
   color: #272b41;
   }
   .chat-cont-right .chat-body .chat-date {
   font-size: 14px;
   margin: 1.875rem 0;
   overflow: hidden;
   position: relative;
   text-align: center;
   text-transform: capitalize;
   }
   .chat-cont-right .chat-body .chat-date:before {
   background-color: #e0e3e4;
   content: "";
   height: 1px;
   margin-right: 28px;
   position: absolute;
   right: 50%;
   top: 50%;
   width: 100%;
   }
   .chat-cont-right .chat-body .chat-date:after {
   background-color: #e0e3e4;
   content: "";
   height: 1px;
   left: 50%;
   margin-left: 28px;
   position: absolute;
   top: 50%;
   width: 100%;
   }
   .chat-cont-right .chat-footer {
   background-color: #fff;
   border-top: 1px solid #f0f0f0;
   padding: 10px 15px;
   position: relative;
   }
   .chat-cont-right .chat-footer .input-group {
   width: 100%;
   }
   .chat-cont-right .chat-footer .input-group .form-control {
   background-color: #f5f5f6;
   border: none;
   border-radius: 50px;
   }
   .chat-cont-right .chat-footer .input-group .form-control:focus {
   background-color: #f5f5f6;
   border: none;
   box-shadow: none;
   }
   .chat-cont-right .chat-footer .input-group .input-group-prepend .btn,
   .chat-cont-right .chat-footer .input-group .input-group-append .btn {
   background-color: transparent;
   border: none;
   color: #9f9f9f;
   }
   .chat-cont-right .chat-footer .input-group .input-group-append .btn.msg-send-btn {
   background-color: #0052cc;
   border-color: #0052cc;
   border-radius: 50%;
   color: #fff;
   margin-left: 10px;
   width: 48px;
   height: 48px;
   font-size: 18px;
   }
   .msg-typing {
   width: auto;
   height: 24px;
   padding-top: 8px
   }
   .msg-typing span {
   height: 8px;
   width: 8px;
   float: left;
   margin: 0 1px;
   background-color: #a0a0a0;
   display: block;
   border-radius: 50%;
   opacity: .4
   }
   .msg-typing span:nth-of-type(1) {
   animation: 1s blink infinite .33333s
   }
   .msg-typing span:nth-of-type(2) {
   animation: 1s blink infinite .66666s
   }
   .msg-typing span:nth-of-type(3) {
   animation: 1s blink infinite .99999s
   }
   .chat-cont-right .chat-body .media.received .media-body .msg-box {
   position: relative;
   }
   .chat-cont-right .chat-body .media.received .media-body .msg-box:first-child:before {
   border-bottom: 6px solid transparent;
   border-right: 6px solid #fff;
   border-top: 6px solid transparent;
   content: "";
   height: 0;
   left: -6px;
   position: absolute;
   right: auto;
   top: 8px;
   width: 0;
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box {
   padding-left: 50px;
   position: relative;
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box:first-child:before {
   border-bottom: 6px solid transparent;
   border-left: 6px solid #e3e3e3;
   border-top: 6px solid transparent;
   content: "";
   height: 0;
   left: auto;
   position: absolute;
   right: -6px;
   top: 8px;
   width: 0;
   }
   .chat-msg-info {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   clear: both;
   -ms-flex-wrap: wrap;
   flex-wrap: wrap;
   list-style: none;
   padding: 0;
   margin: 5px 0 0;
   }
   .chat-msg-info li {
   font-size: 13px;
   padding-right: 16px;
   position: relative;
   }
   .chat-msg-info li:not(:last-child):after {
   position: absolute;
   right: 8px;
   top: 50%;
   content: '';
   height: 4px;
   width: 4px;
   background: #d2dde9;
   border-radius: 50%;
   transform: translate(50%, -50%)
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box .chat-msg-info li:not(:last-child)::after {
   right: auto;
   left: 8px;
   transform: translate(-50%, -50%);
   background: #aaa;
   }
   .chat-cont-right .chat-body .media.received .media-body .msg-box > div .chat-time {
   color: rgba(50, 65, 72, 0.4);
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div .chat-time {
   color: rgba(50, 65, 72, 0.4);
   }
   .chat-msg-info li a {
   color: #777;
   }
   .chat-msg-info li a:hover {
   color: #2c80ff
   }
   .chat-seen i {
   color: #00d285;
   font-size: 16px;
   }
   .chat-msg-attachments {
   padding: 4px 0;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   width: 100%;
   margin: 0 -1px
   }
   .chat-msg-attachments > div {
   margin: 0 1px
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div .chat-msg-info {
   flex-direction: row-reverse;
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div .chat-msg-attachments {
   flex-direction: row-reverse
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div .chat-msg-info li {
   padding-left: 16px;
   padding-right: 0;
   position: relative;
   }
   .chat-attachment img {
   max-width: 100%;
   }
   .chat-attachment {
   position: relative;
   max-width: 130px;
   overflow: hidden;
   }
   .chat-attachment {
   border-radius: .25rem;
   }
   .chat-attachment:before {
   position: absolute;
   top: 0;
   bottom: 0;
   left: 0;
   right: 0;
   background: #000;
   content: "";
   opacity: 0.4;
   transition: all .4s;
   }
   .chat-attachment:hover:before {
   opacity: 0.6;
   }
   .chat-attach-caption {
   position: absolute;
   left: 0;
   right: 0;
   bottom: 0;
   color: #fff;
   padding: 7px 15px;
   font-size: 13px;
   opacity: 1;
   transition: all .4s;
   }
   .chat-attach-download {
   position: absolute;
   top: 50%;
   left: 50%;
   transform: translate(-50%, -50%);
   opacity: 0;
   transition: all .4s;
   color: #fff;
   width: 32px;
   line-height: 32px;
   background: rgba(255, 255, 255, 0.2);
   text-align: center;
   }
   .chat-attach-download:hover {
   color: #495463;
   background: #fff;
   }
   .chat-attachment:hover .chat-attach-caption {
   opacity: 0;
   }
   .chat-attachment:hover .chat-attach-download {
   opacity: 1;
   }
   .chat-attachment-list {
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   margin: -5px;
   }
   .chat-attachment-list li {
   width: 33.33%;
   padding: 5px;
   }
   .chat-attachment-item {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   border: 5px solid rgba(230, 239, 251, 0.5);
   height: 100%;
   min-height: 60px;
   text-align: center;
   font-size: 30px;
   display: -webkit-box;
   display: -ms-flexbox;
   display: flex;
   justify-content: center;
   -webkit-justify-content: center;
   -ms-flex-pack: center;
   }
   .chat-cont-right .chat-body .media.sent .media-body .msg-box > div:hover .chat-msg-actions {
   opacity: 1;
   }
   .chat-msg-actions {
   position: absolute;
   left: -30px;
   top: 50%;
   transform: translateY(-50%);
   opacity: 0;
   transition: all .4s;
   z-index: 2;
   }
   .chat-msg-actions > a {
   padding: 0 10px;
   color: #495463;
   font-size: 24px;
   }
   .chat-msg-actions > a:hover {
   color: #2c80ff;
   }
   @keyframes blink {
   50% {
   opacity: 1
   }
   }
   .btn-file {
   -webkit-box-align: center;
   -ms-flex-align: center;
   align-items: center;
   display: -webkit-inline-box;
   display: -ms-inline-flexbox;
   display: inline-flex;
   font-size: 20px;
   justify-content: center;
   -webkit-justify-content: center;
   -ms-flex-pack: center;
   overflow: hidden;
   padding: 0 0.75rem;
   position: relative;
   vertical-align: middle;
   }
   .btn-file input {
   cursor: pointer;
   filter: alpha(opacity=0);
   font-size: 23px;
   height: 100%;
   margin: 0;
   opacity: 0;
   position: absolute;
   right: 0;
   top: 0;
   width: 100%;
   }
   @media only screen and (max-width: 991.98px) {
   .chat-cont-left, .chat-cont-right {
   -ms-flex: 0 0 100%;
   flex: 0 0 100%;
   max-width: 100%;
   transition: left 0.3s ease-in-out 0s, right 0.3s ease-in-out 0s;
   width: 100%;
   }
   .chat-cont-left {
   border-right: 0;
   }
   .chat-cont-right {
   position: absolute;
   top: 0;
   opacity: 0;
   visibility: hidden;
   }
   .chat-cont-right .chat-header {
   justify-content: start;
   -webkit-justify-content: start;
   -ms-flex-pack: start;
   }
   .chat-cont-right .chat-header .back-user-list {
   display: block;
   }
   .chat-cont-right .chat-header .chat-options {
   margin-left: auto;
   }
   .chat-window.chat-slide .chat-cont-left {
   left: -106%;
   }
   .chat-window.chat-slide .chat-cont-right {
   right: 0;
   opacity: 1;
   visibility: visible;
   }
   .chat-last{
   padding-right: 0px;
   padding-left: 0px;
   }
   }
   @media only screen and (max-width:479px) {
   .chat-cont-left .chat-users-list a.media .media-body > div:first-child .user-name,
   .chat-cont-left .chat-users-list a.media .media-body > div:first-child .user-last-chat {
   max-width: 160px;
   }
   }
</style>
<script src="https://www.gstatic.com/firebasejs/6.6.1/firebase-app.js"></script>
<!-- include firebase database -->
<script src="https://www.gstatic.com/firebasejs/6.6.1/firebase-database.js"></script>
<script>
   // Your web app's Firebase configuration
   var firebaseConfig = {
      apiKey: 'AIzaSyD0pyjXdfDyrePtSRA53EirKBY1_KprikU',
      authDomain: 'astrolight-3b6a8.firebaseapp.com',
      projectId: 'astrolight-3b6a8',
      storageBucket: 'astrolight-3b6a8.appspot.com',
      messagingSenderId: '944431014804',
      appId: '1:944431014804:web:3905b176bd9ca02b5d2581',
      databaseURL: 'https://astrolight-3b6a8-default-rtdb.firebaseio.com',
   };
   
   if (!firebase.apps.length) {
  firebase.initializeApp(firebaseConfig);
   }
   var myName = 'Admin';
   
</script>

<div class="main-content">
   <div class="page-content">
       <div class="container-fluid">
           <!-- start page title -->
           <div class="row">
               <div class="col-12">
                   <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <div class="content-header">
                        <div class="d-flex align-items-center">
                           <div class="mr-auto">
                              <div class="col-md-12" style="margin-bottom: 5px;">
                              </div>
                           </div>
                           
                        </div>
                     </div>
                   </div>
               </div>
           </div>


<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-xl-12 chat-last">
            <div class="chat-window">
               <div class="chat-cont-right">
                  <div class="chat-header">
                    <?php $name = json_decode($astrologer_name,true);?>
                     <div class="user-name">Astrologer Name - {{$name[1]}} </div>


                     <a id="back_user_list" href="javascript:void(0)" class="back-user-list">
                     <i class="fa fa-arrow-left"></i>
                     </a>
                     <div class="media">
                        <?php
                       
                           $booking = DB::table('bookings')
                               ->where('id', $id)
                               ->first();
                              
                           $booking_bridge_id =  $booking->bridge_id;
                           
                      
                       
                        $mainid = 2;

                         if (auth()->user()->user_type == 1):
                           $studentid =  auth()->user()->id;
                           $otherid = $we->user_id;
                           $userid = auth()->user()->id;
                           $username = auth()->user()->name;

                        ?>
                        <div class="media-img-wrap">
                           <div class="avatar avatar-online">
                              {{-- <img src="https://astromonk.com/astromonk/admin/Astromonk_admin/project/public/adminassets/images/favicon_favicon%20(2)_1676267764.png" alt="User Image" class="avatar-img rounded-circle"> --}}
                              <img src="{{asset('content/astrologer/gallery/default.png')}}" alt="User Image" class="avatar-img rounded-circle">
                           </div>
                        </div>
                       
                        <div class="media-body">
                           <div class="user-name">{{$u->name}}</div>
                           <!-- <div class="user-status">online</div> -->
                        </div>
                        <?php else:
                           $otherid = auth()->user()->id;
                           $userid = 1;
                           $username ="shubham";
                           $studentid =  1;
                        ?>
                        <div class="media-img-wrap">
                           <div class=""> 
                              {{-- <img src="{{ asset('../admin/uploads/user/') }}/{{ $user_image }}" height="60px" alt="User Image" > --}}
                              <img src="{{asset('content/astrologer/gallery/default.png')}}" alt="User Image" class="avatar-img rounded-circle" width="50px;">
                           </div>
                        </div>
                        <div class="media-body">
                           <div class="user-name">{{$user_name}}</div>
                           <!-- <div class="user-status">online</div> -->
                        </div>
                        <?php endif ?>
                     </div>
                  </div>
                  <div class="chat-body">
                     <div class="chat-scroll">
                        <ul class="list-unstyled" id="messages">
                        </ul>
                     </div>
                  </div>
                  <div class="chat-footer">
                     <form onsubmit="return sendMessage();" id="chat_div" style="width:100%;" >
                     <div class="input-group">
                        @csrf
                        <div class="input-group-prepend">
                           <div class="btn-file">
                              {{-- <i class="fa fa-paperclip"></i> --}}
                              {{-- <input type="file" accept="image/*" name="file" id="file" onchange="return uploadFile(event);" /> --}}
                           </div>
                        </div>
                        {{-- <input type="text"  id="message" class="input-msg-send form-control" autocomplete="off" placeholder="Type something">
                        <div class="input-group-append">
                           <button type="submit" class="btn msg-send-btn"><i class="fa fa-send"></i></button>
                        </div> --}}
                     </div>
                  </form>
                  </div>
               </div>
             </div>
         </div>
      </div>
      <!-- /Row -->
   </div>
</div>
       </div></div>
</div>

<script>
   function sendMessage() {
      // get message
      var message = document.getElementById("message").value;
   
      // save in database
      var dict = {
         _id:1,
         name:"<?php echo $username;?>"
      }
      firebase.database().ref().child("chat/" + '<?php echo $booking_bridge_id; ?>').push().set({
     
         "anotherid": "<?php echo $otherid;?>",
        "user_id":"<?php echo $userid;?>",
         "text": message,
        "createdAt":"<?=date('Y-m-d H:i:s')?>",
        "user":dict,
         "status":false,
         "image":""
   
      });
    document.getElementById('message').value="";
   
      // prevent form from submitting
      return false;
   }
</script>
<script>
   // listen for incoming messages
   
   firebase.database().ref().child("chat/" + '<?php echo $booking_bridge_id; ?>').on("child_added", function(snapshot) {
      // console.log(snapshot.key);
      var html = "";
      // if (snapshot.val().sender == myName || snapshot.val().sender == 'admin') {
         // give each message a unique ID
         
         // show delete button if message is sent by me
        var admin_user_id = '<?php echo $assign_id; ?>';
      //   alert(admin_user_id);
         if (snapshot.val().anotherid == admin_user_id) {
            html += "<li class='media received' id='message-" + snapshot.key + "'><div class='media-body'><div class='msg-box'><div>";
            html +=  "<p> " + snapshot.val().text+"</p>";
            if(snapshot.val().image) {
            html +=  "<img width=150 src=" + snapshot.val().image+"></img>";
            }
         html +=  "<ul class='chat-msg-info'><li><div class='chat-time'><span> " + snapshot.val().createdAt+"</span></div></li></ul></div>";
         html += "</li>";
         } else {
            html += "<li class='media sent' id='message-" + snapshot.key + "'><div class='media-body'><div class='msg-box'><div>";
         html +=  "<p> " + snapshot.val().text+"</p>";
         if(snapshot.val().image) {
            html +=  "<img width=150 src=" + snapshot.val().image+"></img>";
            }
         html +=  "<ul class='chat-msg-info'><li><div class='chat-time'><span> " + snapshot.val().createdAt+"</span></div></li></ul></div>";
         html += "</li>";
         }
   
         document.getElementById("messages").innerHTML += html;
      // }
   });
   
   function deleteMessage(self) {
      // get message ID
      var messageId = self.getAttribute("data-id");
   
      // delete message
      firebase.database().ref("messages").child(messageId).remove();
   }
   
   // attach listener for delete message
   firebase.database().ref("messages").on("child_removed", function(snapshot) {
      // remove message node
      document.getElementById("message-" + snapshot.key).innerHTML = "This message has been removed";
   });

   function uploadFile(e){
      var form = $('#chat_div')[0];
    var formData = new FormData(form);
      $.ajax({
           url: "",// your request url
           data: formData,
           processData: false,
           contentType: false,
           type: 'POST',
           success: function (data) {
               if (data.status) {
                  var html = '<li class="media sent"><div class="media-body"><div class="msg-box"><div><a href="'+data.path+'" target="_blank"><img src="'+data.path+'" width=150 /></a></div></div></li>';
                  var dict = {
                     _id:<?php echo $userid;?>,
                     name:"<?php echo $username;?>"
                  }
                  firebase.database().ref().child("chat/" + '1').push().set({
                 
                     "anotherid": "<?php echo $otherid;?>",
                      "user_id":"<?php echo $userid;?>",
                     "text": "Attachment",
                      "createdAt":"<?=date('Y-m-d H:i:s')?>",
                      "user":dict,
                     "status":false,
                     "image":data.path
         
                  });
               }
           },
           error: function () {
   
           }
       });
   }
</script>












@section('js_user_page')
<script>
   $('#modal-danger').on('show.bs.modal', function (event) {
       var button = $(event.relatedTarget)
       var user_id = button.data('userid')
   
       var modal = $(this)
       modal.find('.modal-footer #user_id').val(user_id)
       // modal.find('form').attr('action','permissions/' + user_id);
   })
</script>
@endsection
@endsection