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
                  <li class="breadcrumb-item active">Edit Patient</li>
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
                 {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->id]]) !!}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>First Name <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="name" value="{{ $a->name }}">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Last Name <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="lname" value="{{ $a->last_name }}">
                           </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Email <span class="login-danger">*</span></label>
                              <input type="email" required class="form-control" name="email" value="{{ $a->email }}">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Mobile <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="mobile" value="{{ $a->mobile }}">
                           </div>
                        </div>

                        <div class="col-12 col-md-6 col-xl-6">
                            <div class="input-block select-gender">
                               <label class="gen-label">Status <span class="login-danger">*</span></label>
                               <div class="form-check-inline">
                                  <label class="form-check-label">
                                  <input type="radio" name="status" class="form-check-input" value="1"
                                     <?php echo ($a->status == 1) ? 'checked' : ''; ?>>Active
                                  </label>
                               </div>
                               <div class="form-check-inline">
                                  <label class="form-check-label">
                                  <input type="radio" name="status" class="form-check-input" value="0"
                                     <?php echo ($a->status == 0) ? 'checked' : ''; ?>>In Active
                                  </label>
                               </div>
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
