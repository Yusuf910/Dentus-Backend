@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Edit Settings</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.settings.edit',1) }}">Settings</a></li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body p-3">
                     {{-- @if (count($errors) > 0)
                     <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                           @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                     @endif --}}
                     {!! Form::model($settings, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['admin.settings1update', $settings->id]]) !!}
                     @csrf
                     @method('PATCH')
                     @include('includes.admin.form-success') 
                     <div class="row">
                        <div class="col-md-12">
                           <div class="alert alert-light" role="alert">
                              Admin Settings
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Header Logo<span class="text-danger">*</span></label>
                              <input type="hidden" name="header_logo" value="{{ $settings->header_logo}}">
                              <input type="file" name="header_logo" class="form-control" required>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Footer Logo<span class="text-danger">*</span></label>
                              <input type="hidden" name="footer_logo" value="{{ $settings->footer_logo}}">
                              <input type="file" name="footer_logo" tag="role_slug" class="form-control" id="role_slug" required>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Favicon Icon<span class="text-danger">*</span></label>
                              <input type="hidden" name="favicon_icon" value="{{ $settings->favicon_icon}}">
                              <input type="file" name="favicon_icon" class="form-control" required>
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="alert alert-light" role="alert">
                              Add Contact Info
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Mobile Number<span class="text-danger">*</span></label>
                              <input type="number" name="mobile_number" tag="number" class="form-control" id="mobile_number" placeholder="Mobile Number" min="1" maxlength="10" value="{{$settings->mobile_number}}"
                                 oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Email<span class="text-danger">*</span></label>
                              <input type="email" name="admin_email" tag="role_slug" class="form-control" id="role_slug" placeholder="Email Address..." value="{{$settings->admin_email}}" pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-zA-Z]{2,3}$" required>
                              <span class="text-danger" id="emailErrorMsg"></span>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Address<span class="text-danger">*</span></label>
                              <input type="text" name="address" tag="role_slug" class="form-control" id="role_slug" placeholder="Address..." value="{{$settings->address}}">
                           </div>
                        </div>
                        <div class="col-md-12   ">
                           <div class="alert alert-light" role="alert">Add Social Links
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Facebook Link</label>
                              <input type="text" name="facebook_link" tag="facebook_link" class="form-control" id="facebook_link" placeholder="Facebook Link..." value="{{$settings->facebook_link}}" >
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Linkedin Link</label>
                              <input type="text" name="linkedin_link" tag="role_slug" class="form-control" id="linkedin_link" placeholder="linkedin_link Link..." value="{{$settings->linkedin_link}}" >
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Twitter Link</label>
                              <input type="text" name="twitter_link" tag="twitter_link" class="form-control" id="twitter_link" placeholder="Twitter Link..." value="{{$settings->twitter_link}}" >
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Youtube Link</label>
                              <input type="text" name="youtube_link" tag="role_slug" class="form-control" id="role_slug" placeholder="Youtube Link..." value="{{$settings->youtube_link}}">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Instagram Link</label>
                              <input type="text" name="instagram_link" tag="role_slug" class="form-control" id="role_slug" placeholder="Instagram Link..." value="{{$settings->instagram_link}}">
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="alert alert-light" role="alert">Add App Links                        
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Playstore Link</label>
                              <input type="text" name="google_play_app_link" tag="google_play_app_link" class="form-control" id="role_slug" placeholder="Playstore Link..." value="{{$settings->google_play_app_link}}">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Appstore Link</label>
                              <input type="text" name="ios_app_link" tag="ios_app_link" class="form-control" id="ios_app_link" placeholder="Appstore Link..." value="{{$settings->ios_app_link}}" >
                           </div>
                        </div>
                     </div>
                     <div>
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                     </div>
                  </div>
                  {!! Form::close() !!}
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
   </div>
   <!-- End Page-content -->
   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © Taskaid.
            </div>
            <!-- <div class="col-sm-6">
               <div class="text-sm-end d-none d-sm-block">
                   
               </div>
               </div> -->
         </div>
      </div>
   </footer>
</div>
<!-- end main content-->
</div>
<!-- END layout-wrapper -->
@section('scripts')
<script>
   $(document).ready(function(){
       $('#role_name').on('input', function(e){
        var str = $(this).val();
        str = str.replace(/[\W_]+/g, '-').replace(/^-+|-+$/g, '').toLowerCase();
        $('#role_slug').val(str).attr('placeholder', str);
       });
   });
</script> 
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   ClassicEditor.create(document.querySelector("#ckeditor-classic1"))   
</script>
@endsection
@endsection