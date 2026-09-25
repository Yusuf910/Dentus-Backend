@extends('layouts.admin') 
@section('content')
<div class="main-content">
  <div class="page-content">
      <div class="container-fluid">
    <!-- Content Header (Page header) -->
    
    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0 font-size-18">Edit Role</h4>

              <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                      <li class="breadcrumb-item active"><a href="{{ route('admin.roles.index') }}">Role List</a></li>
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
            {!! Form::model($role, ['method' => 'PATCH','route' => ['admin.roles.update', $role->id]]) !!}
        
              <div class="row">
                <div class="col-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                       <input type="text" name="role_name" class="form-control" id="role_name" placeholder="Role name..." value="{{$role->name}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Slug</label>
                    <div class="col-sm-10">
                       <input type="text" name="role_slug" tag="role_slug" class="form-control" id="role_slug" placeholder="Role Slug..." value="{{$role->slug}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Permissions</label>
                    <div class="col-lg-10">
                      <?php 

                          $selectedarray = array();
                          foreach ($role->permissions as $permissionss)
                          {
                              array_push($selectedarray, $permissionss->id);
                          }
                      ?>
                      <select name="roles_permissions[]" class="choices-multiple-default" data-trigger id="choices-multiple-default" multiple  data-placeholder="Select Permission"
                              style="width: 100%;">
                          <option value="">Select Permission...</option>
                          @foreach ($permission as $p)
                          @if(in_array($p->id,$selectedarray))
                          <option selected data-role-id="{{$p->id}}" data-role-slug="{{$p->slug}}" value="{{$p->id}}">{{$p->name}}</option>
                          @else
                          <option  data-role-id="{{$p->id}}" data-role-slug="{{$p->slug}}" value="{{$p->id}}">{{$p->name}}</option>
                          @endif
                          @endforeach
                       </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                      <select name="status"  class="form-select">
                        <option value="1"<?php if ($role->status == 1) {
                           echo "selected";
                           # code...
                           } else {
                           echo "";
                           # code...
                           }
                           ?>>Active</option>
                        <option value="0" <?php if ($role->status == 0) {
                           # code...
                           echo "selected";
                           } else {
                           # code...
                           echo "";
                           }
                           ?>>Inactive</option>
                     </select>
                    </div>
                  </div>
                
              </div>
              <!-- /.row -->
           
            <div class="box-footer">
              <input type="submit" class="btn btn-primary">
            </div>
          </div>
          {!! Form::close() !!}
          <!-- /.box -->
        </div>
      </div>
      </div>
    </div>
    <!-- /.content -->
  </div>
  </div>
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
