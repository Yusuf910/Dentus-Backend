@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
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
         {!! Form::open(array('route' => 'admin.roles.store','method'=>'POST')) !!}
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Add Role</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.roles.index') }}">Role List</a></li>
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
                     <div class="row">
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Name</label>
                              {!! Form::text('role_name', null, array('placeholder' => 'Name','class' => 'form-control','id' => 'role_name')) !!}
                              @error('role_name')
                                <span class="text-danger">{{ $message }}</span>
                              @enderror
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Slug</label>
                              {!! Form::text('role_slug', null, array('placeholder' => 'Slug','class' => 'form-control','id'=>'role_slug')) !!}
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                               <label for="choices-multiple-default" class="form-label">Add Permission</label>
                               <select name="roles_permissions[]" class="choices-multiple-default" data-trigger id="choices-multiple-default" multiple  style="width: 100%;">
                               <option value="">Select Permission...</option>
                               @foreach ($permission as $p)
                               <option data-role-id="{{$p->id}}" data-role-slug="{{$p->slug}}" value="{{$p->id}}">{{$p->name}}</option>
                               @endforeach
                            </select>
                            {{-- @error('roles_permissions')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror --}}
                           </div>
                       </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Status</label>
                              <select class="form-select" name="status">
                                 <option value="1">Active</option>
                                 <option value="0">Inactive</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div>
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                     </div>
                  </div>
               </div>
               {!! Form::close() !!}
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
   </div>
   <!-- End Page-content -->
</div>
@section('scripts')
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
@endsection
@endsection