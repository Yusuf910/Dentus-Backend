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
                  {!! Form::model($settings, ['method' => 'PATCH', 'enctype' => 'multipart/form-data', 'route' => ['admin.settings2update', $settings->id]]) !!}
                  @csrf
                  <div class="row">
                     <div class="col-md-12">
                        <div class="alert alert-light" role="alert">
                           Manage
                        </div>
                     </div>
                    
                     <!-- Privacy Policy Field -->
                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="about_us_footer" class="form-label">About Us</label>
                           <textarea id="ckeditor-classic1" rows="5" class="form-control" placeholder="About Us" spellcheck="false" name="about_us_footer">{{ $settings->about_us_footer }}</textarea>
                           @error('about_us_footer')
                           <small class="text-danger">{{ $message }}</small>
                           @enderror
                        </div>
                     </div>   
                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="privacy_policy" class="form-label">Privacy Policy</label>
                           <textarea id="ckeditor-classic2" rows="5" class="form-control" placeholder="Privacy and Policy" spellcheck="false" name="privacy_policy">{{ $settings->privacy_policy }}</textarea>
                           @error('privacy_policy')
                           <small class="text-danger">{{ $message }}</small>
                           @enderror
                        </div>
                     </div>                       
                     <!-- Terms and Condition Field -->
                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="terms_and_condition" class="form-label">Terms and Condition</label>
                           <textarea id="ckeditor-classic3" rows="5" class="form-control" placeholder="Terms and condition" spellcheck="false" name="terms_and_condition">{{ $settings->terms_and_condition }}</textarea>
                           @error('terms_and_condition')
                           <small class="text-danger">{{ $message }}</small>
                           @enderror
                        </div>
                     </div>
                     <!-- Includes Field -->
                     
                     <!-- Notes Field -->
                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="contact_us" class="form-label">Contact Us</label>
                           <textarea id="ckeditor-classic4" rows="5" class="form-control" placeholder="Contact Us" spellcheck="false" name="contact_us">{{ $settings->contact_us }}</textarea>
                           @error('contact_us')
                           <small class="text-danger">{{ $message }}</small>
                           @enderror
                        </div>
                     </div>

                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="loyality_program_about" class="form-label">Loyality Program</label>
                           <textarea id="ckeditor-classic5" rows="5" class="form-control" placeholder="Loyality Program" spellcheck="false" name="loyality_program_about">{{ $settings->loyality_program_about }}</textarea>
                           @error('loyality_program_about')
                           <small class="text-danger">{{ $message }}</small>
                           @enderror
                        </div>
                     </div>
                     
                  </div>
                  <div>
                     <button type="submit" class="btn btn-primary w-md">Submit</button>
                  </div>
                  {!! Form::close() !!}
               </div>
            </div>
         </div>
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