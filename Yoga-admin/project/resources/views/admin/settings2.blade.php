@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Content Setting</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Content Setting</li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
         <!-- end page title -->

         <div class="row">
            <div class="col-12">
               @include('includes.admin.form-success') 
               <div class="card">
                  <div class="card-body p-3">
                     {!! Form::model($settings, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['admin.settings2update', $settings->_id]]) !!}
                     @csrf
                     @method('PATCH')
                     <div class="row">
                        <div class="col-md-12">
                           <div class="alert alert-light" role="alert">
                              Manage Content Settings
                           </div>
                        </div>

                        <!-- Title + Enquiry Email + Mobile in same row -->
                        <div class="row mb-3">
                           <div class="col-md-4">
                              <label class="form-label">Title</label>
                              <input type="text" name="title" class="form-control" 
                                     value="{{ $settings->title }}" placeholder="Enter Title">
                           </div>

                           <div class="col-md-4">
                              <label class="form-label">Enquiry Email</label>
                              <input type="email" name="enquiry_email" class="form-control" 
                                     value="{{ $settings->enquiry_email }}" placeholder="support@yoga.com">
                           </div>

                           <div class="col-md-4">
                              <label class="form-label">Mobile</label>
                              <input type="text" name="mobile" class="form-control" 
                                     value="{{ $settings->mobile }}" placeholder="Enter Mobile Number">
                           </div>
                        </div>

                        <!-- About Us -->
                        <div class="col-md-12 mb-3">
                           <label class="form-label">About Us</label>
                           <textarea id="ckeditor-about" rows="5" class="form-control" name="about_us">{{ $settings->about_us }}</textarea>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="col-md-12 mb-3">
                           <label class="form-label">Terms & Conditions</label>
                           <textarea id="ckeditor-terms" rows="5" class="form-control" name="terms_and_condition">{{ $settings->terms_and_condition }}</textarea>
                        </div>

                        <!-- Privacy Policy -->
                        <div class="col-md-12 mb-3">
                           <label class="form-label">Privacy Policy</label>
                           <textarea id="ckeditor-privacy" rows="5" class="form-control" name="privacy_policy">{{ $settings->privacy_policy }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                           <label class="form-label">How It WOrk</label>
                           <textarea id="ckeditor-how_it_work" rows="5" class="form-control" name="how_it_work">{{ $settings->how_it_work }}</textarea>
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
   </div>
</div>

@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
  [
     '#ckeditor-about',
     '#ckeditor-terms',
     '#ckeditor-privacy',
     '#ckeditor-how_it_work'
   ].forEach(selector => {
     const el = document.querySelector(selector);
     if (el) {
       ClassicEditor
         .create(el)
         .catch(error => console.error(error));
     }
   });

</script>
@endsection
@endsection
