@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
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
         <!-- Main content -->
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
                     {!! Form::model($settings, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['admin.settings.update', $settings->id]]) !!}
                     @csrf
                     @method('PATCH')
                     @include('includes.admin.form-success') 
                     <div class="row">
                        {{-- 
                        <div class="form-group row">
                           <label for="example-text-input" class="col-sm-2 col-form-label">Logo <span class="text-danger">*</span></label>
                           <div class="col-sm-10">
                              <img height="100" src="{{asset('project/public/adminassets')}}/images/{{ $settings->logo}}" width="100">
                           </div>
                        </div>
                        --}}
                        <div class="form-group row">
                           <label for="example-text-input" class="col-sm-2 col-form-label">Header Logo <span class="text-danger">*</span></label>
                           <div class="col-sm-10">
                              <input type="hidden" name="logoname" value="{{ $settings->logo ?? ""}}">
                              <input type="file" name="logo" class="form-control">
                           </div>
                        </div>
                        <div class="form-group row">
                           <label for="example-text-input" class="col-sm-2 col-form-label">Footer Logo<span class="text-danger">*</span></label>
                           <div class="col-sm-10">
                              <input type="hidden" name="footer" value="{{ $settings->footer ?? ""}}">
                              <input type="file" name="footer" tag="role_slug" class="form-control" id="role_slug">
                              {{-- <input type="text" name="footer" tag="role_slug" class="form-control" id="role_slug" placeholder="Footer..." value="{{$settings->footer}}" required> --}}
                           </div>
                        </div>
                        <div class="form-group row">
                           <label for="example-text-input" class="col-sm-2 col-form-label">Favicon Icon <span class="text-danger">*</span></label>
                           <div class="col-sm-10">
                              <input type="hidden" name="favicon_name" value="{{ $settings->favicon ?? ""}}">
                              <input type="file" name="favicon" class="form-control">
                           </div>
                        </div>
                        <div class="box-footer">
                           <input type="submit" class="btn btn-primary">
                        </div>
                     </div>
                     {!! Form::close() !!}
                     <!-- /.box -->
                  </div>
               </div>
               <!-- /.content -->
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_role_page')
<script>
   $(document).ready(function(){
       $('#role_name').keyup(function(e){
           var str = $('#role_name').val();
           str = str.replace(/\W+(?!$)/g, '-').toLowerCase();//rplace stapces with dash
           $('#role_slug').val(str);
           $('#role_slug').attr('placeholder', str);
       });
   });
   
</script>
<script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>
<script>
   CKEDITOR.replace('editor1')
   CKEDITOR.replace('editor2')
</script>
@endsection
@endsection