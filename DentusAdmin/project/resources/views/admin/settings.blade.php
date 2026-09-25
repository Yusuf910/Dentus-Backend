@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Settings</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-12">
            @include('includes.admin.form-success') 
            <div class="card">
               <div class="card-body p-3">
                  {!! Form::model($settings, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['admin.settings.update', $settings->id]]) !!}
                  @csrf
                  @method('PATCH')
                  <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-light" role="alert">
                           Admin Settings
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">Header Image</label>
                           <img src="{{ asset('adminassets') }}/img/{{ $settings->logo}}" width="30px">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">Header Logo</label>
                           <input type="hidden" name="logoname" value="{{ $settings->logo}}">
                           <input type="file" name="logo" class="form-control">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">Favicon Image</label>
                           <img src="{{ asset('adminassets') }}/img/{{ $settings->favicon}}" width="30px">                           
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">Favicon Icon</label>
                           <input type="hidden" name="favicon_name" value="{{ $settings->favicon}}">
                           <input type="file" name="favicon" class="form-control">
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="alert alert-light" role="alert">
                           Add Contact Info
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <label for="title" class="form-label">Title</label>
                           <input type="text" name="title" tag="title" class="form-control" id="title" placeholder="title..." value="{{$settings->title}}" required>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <label for="footer" class="form-label">Footer</label>
                           <input class="form-control" type="text" name="footer" id="footer" value="{{$settings->footer}}">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <label for="helpline_number" class="form-label">Helpline Number</label>
                           <input class="form-control" type="text" id="helpline_number" name="helpline_number" value="{{$settings->helpline_number}}">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <label for="support_email" class="form-label">Support Email</label>
                           <input class="form-control" type="email" id="support_email" name="support_email" value="{{$settings->support_email}}">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <label for="enquiry_email" class="form-label">Enquiry Email</label>
                           <input class="form-control" type="email" id="enquiry_email" name="enquiry_email" value="{{$settings->enquiry_email}}">
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="mb-3">
                           <label for="adminnumber" class="form-label">Admin's number</label>
                           <input class="form-control" type="text" id="adminnumber" name="adminnumber" value="{{$settings->adminnumber}}">
                        </div>
                     </div>
                     {{-- <div class="col-md-12">
                        <div class="mb-3">
                           <label for="adminname" class="form-label">Admin's Name</label>
                           <input class="form-control" type="text" id="adminname" name="adminname" value="{{$settings->adminname}}">
                        </div>
                     </div> --}}
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="address" class="form-label">Address</label>
                           <textarea class="form-control" id="address" name="address" rows="5">{{$settings->address ?? "" }}</textarea>
                        </div>
                     </div>
                                          
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="description" class="form-label">Description</label>
                           <textarea class="form-control" id="description" name="description" rows="5">{{$settings->description ?? "" }}</textarea>
                        </div>
                     </div>
                     
                     <div class="col-md-12">
                        <div class="alert alert-light" role="alert">
                           Add Social Links
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="fb_link" class="form-label">Facebook Link</label>
                           <input class="form-control" type="text" id="fb_link" name="fb_link" value="{{$settings->fb_link}}">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="linkedin_link" class="form-label">Linkedin Link</label>
                           <input class="form-control" type="text" id="linkedin_link" name="linkedin_link" value="{{$settings->linkedin_link}}">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="twitter_link" class="form-label">Twitter Link</label>
                           <input class="form-control" type="text" id="twitter_link" name="twitter_link" value="{{$settings->twitter_link}}">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="refund_deduction_percent" class="form-label">Razor Pay Refund Percent%</label>
                           <input class="form-control" type="text" id="refund_deduction_percent" name="refund_deduction_percent" value="{{$settings->refund_deduction_percent}}">
                        </div>
                     </div>
                     {{-- <div class="col-md-6">
                        <div class="mb-3">
                           <label for="youtube_link" class="form-label">Youtube Link</label>
                           <input class="form-control" type="text" id="youtube_link" name="youtube_link" value="{{$settings->youtube_link ?? "" }}">
                        </div>
                     </div> --}}
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="insta_link" class="form-label">Insta Link</label>
                           <input class="form-control" type="text" id="insta_link" name="insta_link" value="{{$settings->insta_link}}">
                        </div>
                     </div>
                     {{-- <div class="col-md-12">
                        <div class="alert alert-light" role="alert">
                           Add App Links
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="playstore_link" class="form-label">Playstore Link</label>
                           <input class="form-control" type="text" id="playstore_link" name="playstore_link" value="{{$settings->playstore_link}}">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="mb-3">
                           <label for="appstore_link" class="form-label">Appstore Link</label>
                           <input class="form-control" type="text" id="appstore_link" name="appstore_link" value="{{$settings->appstore_link}}">
                        </div>
                     </div>                                                                       --}}
                  </div>
                  <div>
                     <button type="submit" class="btn btn-primary w-md">Submit</button>
                  </div>
               </div>
            </div>
         </div>
         <!-- end col -->
      </div>
   </div>
   
   <div class="notification-box">
      <div class="msg-sidebar notifications msg-noti">
         <div class="topnav-dropdown-header">
            <span>Messages</span>
         </div>
         <div class="drop-scroll msg-list-scroll" id="msg_list">
            <ul class="list-box">
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">R</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Richard Miles </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item new-message">
                        <div class="list-left">
                           <span class="avatar">J</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">John Doe</span>
                           <span class="message-time">1 Aug</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">T</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Tarah Shropshire </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">M</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Mike Litorus</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">C</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Catherine Manseau </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">D</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Domenic Houston </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">B</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Buster Wigton </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">R</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Rolland Webber </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">C</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Claire Mapes </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">M</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Melita Faucher</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">J</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Jeffery Lalor</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">L</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Loren Gatlin</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">T</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Tarah Shropshire</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
            </ul>
         </div>
         <div class="topnav-dropdown-footer">
            <a href="chat.html">See all messages</a>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="myCenterModalLabel">Treatment & Session</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <ul style="list-style-type: none; padding-left: 0px;" class="mb-0">
               <li style="display: flex; justify-content: space-between;" class="mb-2">
                  <span>Teeth Whitening</span> <span>2 Sessions</span>
               </li>
               <li style="display: flex; justify-content: space-between;" class="mb-2">
                  <span>Dental Implant</span> <span>3 Sessions</span>
               </li>
               <li style="display: flex; justify-content: space-between;" class="mb-2">
                  <span>Root Canal Treatment</span> <span>1 Session</span>
               </li>
            </ul>
         </div>
      </div>
   </div>
</div>
<div id="delete_patient" class="modal fade delete-modal" role="dialog">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-body text-center">
            <img src="assets/img/sent.png" alt width="50" height="46">
            <h3>Are you sure want to delete this ?</h3>
            <div class="m-t-20"> <a href="#" class="btn btn-white" data-bs-dismiss="modal">Close</a>
               <button type="submit" class="btn btn-danger">Delete</button>
            </div>
         </div>
      </div>
   </div>
</div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })
   
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/38.1.1/classic/ckeditor.js"></script>
<script>  
   ClassicEditor.create(document.querySelector("#ckeditor-classic1")).catch(error => {
      console.error(error);
   });
   ClassicEditor.create(document.querySelector("#ckeditor-classic2")).catch(error => {
      console.error(error);
   });
   ClassicEditor.create(document.querySelector("#ckeditor-classic3")).catch(error => {
      console.error(error);
   });
   ClassicEditor.create(document.querySelector("#ckeditor-classic4")).catch(error => {
      console.error(error);
   });
   ClassicEditor.create(document.querySelector("#ckeditor-classic5")).catch(error => {
      console.error(error);
   });
</script>
<script>
   $(document).ready(function() {
   $('.summernote').summernote();
   });
   
   
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