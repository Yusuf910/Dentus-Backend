@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{$listurl}}">{{$title}} </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Add {{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body pt-4">
                  @include('includes.admin.form-success') 
                     @if (count($errors) > 0)
                    <div class="alert alert-danger">
                      <strong>Whoops!</strong> There were some problems with your input.<br><br>
                      <ul>
                         @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                         @endforeach
                      </ul>
                    </div>
                  @endif
                  <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Name <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="clinic_name" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Description <span class="login-danger">*</span></label>
                              <textarea class="form-control" name="clinic_description" required></textarea>
                           </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Address <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="address" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>City <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="city" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>State <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="state" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Pincode <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="pincode" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Latitude <span class="login-danger">*</span></label>
                              <input type="text" class="form-control" name="latitude" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Longitude <span class="login-danger">*</span></label>
                              <input type="text" class="form-control" name="longitude" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Register Number <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="register_number" value="">
                           </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Image<span class="login-danger">*</span></label>
                              <input type="file" class="form-control" name="image"  accept="image/*">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Status <span class="login-danger">*</span></label>
                              <select name="status" required class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                             </select>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="doctor-submit text-start">
                              <button type="submit" class="btn btn-primary submit-form me-2">Save</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')

@endsection
@endsection
