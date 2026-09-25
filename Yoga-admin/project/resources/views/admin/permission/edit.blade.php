@extends('layouts.admin') 
@section('content')
<div class="content-wrapper">
  <div class="container-full">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="d-flex align-items-center">
        <div class="mr-auto">
          <h3 class="page-title">Edit Role</h3>
          <div class="d-inline-block align-items-center">
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{ route('admin.home') }}"><i class="mdi mdi-home-outline"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.home') }}">Home</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.permissions.index') }}">Permission List</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Permission Details</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12 col-12">
          <!-- Basic Forms -->
          <div class="box">
            <!-- <div class="box-header with-border">
                    <h4 class="box-title">Add Board Name</h4>
                  </div> -->
            <!-- /.box-header -->
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
            {!! Form::model($permission, ['method' => 'PATCH','route' => ['admin.permissions.update', $permission->id]]) !!}
            <div class="box-body">
              <div class="row">
                <div class="col-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                       <input type="text" name="name" class="form-control" id="role_name" placeholder="Permission name..." value="{{$permission->name}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Slug</label>
                    <div class="col-sm-10">
                       <input type="text" name="slug" tag="role_slug" class="form-control" id="role_slug" placeholder="Permission Slug..." value="{{$permission->slug}}" required>
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <input type="submit" class="btn btn-primary">
            </div>
          </div>
          {!! Form::close() !!}
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
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

@endsection
@endsection
