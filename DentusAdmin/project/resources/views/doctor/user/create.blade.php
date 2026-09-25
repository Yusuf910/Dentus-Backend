@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{$listurl}}">Patient </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Add Patient</li>
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
                  <form method="POST" action="{{ route('admin.myuser.store_patient') }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>First Name <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="name" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Last Name <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="lname" value="">
                           </div>
                        </div>
                        
                        @php $doctor = DB::table('users')->where('approved',1)->get(); @endphp
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                                <label>Doctors <span class="login-danger">*</span></label>
                                 <select class="form-select" id="link_id " name="link_id" required>
                                    <option value="">Select</option>
                                    @if(isset($doctor) && count($doctor) > 0)
                                    @foreach ($doctor as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                           </div>
                           
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Email <span class="login-danger">*</span></label>
                              <input type="email" required class="form-control" name="email" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Mobile <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="mobile" value="">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>DOB<span class="login-danger">*</span></label>
                              <input type="date" required max="{{date('Y-m-d')}}" class="form-control" name="date_of_birth" value="">
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
                              <label>Gender<span class="login-danger">*</span></label>
                              <select name="gender" required class="form-control">
                                <option  value="Male">Male</option>
                                <option  value="Female">Female</option>
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
