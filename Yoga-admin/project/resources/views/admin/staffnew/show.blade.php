@extends('layouts.admin') @section('content')
<div class="main-content">
  <div class="page-content">
      <div class="container-fluid">
    <!-- Content Header (Page header) -->
    
    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0 font-size-18">User Detail</h4>

              <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                      <li class="breadcrumb-item active"><a href="{{ route('admin.stafflist') }}">Staff List</a></li>
                  </ol>
              </div>

          </div>
      </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h4 class="mb-sm-0 font-size-18"></h4>
        <div class="page-title-right">
          <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.admins.adminedit',$user->id) }}" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Edit Profile
                                 </a> 
                                </div></div>
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
           
              <div class="row">
               
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      {{ $user->name }}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Email</label>
                    <div class="col-lg-10">
                      {{ $user->email }}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Phone</label>
                    <div class="col-lg-10">
                      {{ $user->phone }}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Role</label>
                    <div class="col-lg-10">
                      @if ($user->roles->isNotEmpty())
                          @foreach ($user->roles as $role)
                              <span class="badge badge-primary">
                                  {{ $role->name }}
                              </span>
                          @endforeach
                      @endif
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Permission</label>
                    <div class="col-lg-10">
                      @if ($user->permissions->isNotEmpty())                                        
                          @foreach ($user->permissions as $permission)
                              <span class="badge badge-success">
                                  {{ $permission->name }}                                    
                              </span>
                          @endforeach            
                      @endif
                    </div>
                  </div>
                
              </div>
              <!-- /.row -->
           
           
          </div>
          
          <!-- /.box -->
        </div>
      </div>
    </div>
      </div>
    <!-- /.content -->
  </div>
  </div>
</div>
@endsection
