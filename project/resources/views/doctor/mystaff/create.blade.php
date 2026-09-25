@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.user.mystaff')}}">Doctor</a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Add Team</li>
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
                  <form method="POST" action="{{ route('admin.user.store_staff') }}" enctype="multipart/form-data">
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
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Password<span class="login-danger">*</span></label>
                              <input type="password" required class="form-control" name="password" value="">
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
                              <label>Image</label>
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
                        <?php $data = DB::table('master_langauages')->where('status',1)->orderBy('name','ASC')->get();?>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Language</label>
                              <select name="language_known[]" class="form-control" multiple>
                                <option value="">select</option>
                               @foreach ($data as $aa)
                                 <option value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
                               @endforeach
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
