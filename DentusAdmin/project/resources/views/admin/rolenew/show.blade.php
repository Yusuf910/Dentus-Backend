@extends('layouts.admin') @section('content')
<div class="content-wrapper">
  <div class="container-full">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="d-flex align-items-center">
        <div class="mr-auto">
          <h3 class="page-title">Show Role</h3>
          <div class="d-inline-block align-items-center">
            <nav>
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="#"><i class="mdi mdi-home-outline"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Home</li>
                   <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.roles.index') }}">Role List</a></li>
                <li class="breadcrumb-item" aria-current="page">Show Role</li>
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
            <div class="box-body">
              <div class="row">
                <div class="col-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                     {{ $role->name }}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Permissions:</label>
                    <div class="col-lg-10">
                      @if(!empty($rolePermissions))
                          @foreach($rolePermissions as $v)
                              <label class="label label-success">{{ $v->name }},</label>
                          @endforeach
                      @endif
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
           
          </div>
          
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
</div>
@endsection
