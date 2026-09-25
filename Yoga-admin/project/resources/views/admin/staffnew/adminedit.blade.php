@extends('layouts.admin') @section('content')
<div class="main-content">
  <div class="page-content">
      <div class="container-fluid">
    <!-- Content Header (Page header) -->
    

    <div class="row">
      <div class="col-12">
          <div class="page-title-box d-sm-flex align-items-center justify-content-between">
              <h4 class="mb-sm-0 font-size-18">Edit User</h4>

              <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                      <li class="breadcrumb-item active"><a href="{{ route('admin.admins.index') }}">Staff List</a></li>
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
            {!! Form::model($user, ['method' => 'PATCH','route' => ['admin.adminupdate', $user->id], 'files' => true]) !!}
          
              <div class="row">
                <div class="col-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                      {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Email</label>
                    <div class="col-lg-10">
                      {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control' ,'readonly')) !!}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-form-label col-lg-2">Phone</label>
                    <div class="col-lg-10">
                      {!! Form::text('phone', null, array('placeholder' => 'Phone','class' => 'form-control')) !!}
                    </div>
                  </div>


                  <?php
                  $admin_id = Auth()->guard('admin')->user()->id;
                  if ($admin_id == 1) {
                     ?>
                      <div class="form-group row">
                        <label for="example-text-input" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                          {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="example-text-input" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-10">
                          {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                        </div>
                      </div>

                      
                     <?php
                  }

               ?>




                  {{-- <div class="form-group row">
                    <label class="col-form-label col-lg-2">Image</label>
                    <div class="col-lg-10">
                      {!! Form::file('image', null, array('placeholder' => 'image','class' => 'form-control')) !!}
                    </div>
                  </div> --}}
             
             
             
                  
               
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
@section('js_user_page')

    <script>

        $(document).ready(function(){
            var permissions_box = $('#permissions_box');
            var permissions_ckeckbox_list = $('#permissions_ckeckbox_list');
            var user_permissions_box = $('#user_permissions_box');
            var user_permissions_ckeckbox_list = $('#user_permissions_ckeckbox_list');

            permissions_box.hide(); // hide all boxes


            $('#role').on('change', function() {
                var role = $(this).find(':selected');    
                var role_id = role.data('role-id');
                var role_slug = role.data('role-slug');

                permissions_ckeckbox_list.empty();
                user_permissions_box.empty();

                $.ajax({
                    url: "edit",
                    method: 'get',
                    dataType: 'json',
                    data: {
                        role_id: role_id,
                        role_slug: role_slug,
                    }
                }).done(function(data) {
                    
                    console.log(data);
                    
                    permissions_box.show();                        
                    // permissions_ckeckbox_list.empty();

                    $.each(data, function(index, element){
                        $(permissions_ckeckbox_list).append(       
                            '<div class="custom-control custom-checkbox">'+                         
                                '<input class="custom-control-input" type="checkbox" name="permissions[]" id="'+ element.slug +'" value="'+ element.id +'">' +
                                '<label class="custom-control-label" for="'+ element.slug +'">'+ element.name +'</label>'+
                            '</div>'
                        );

                    });
                });
            });
        });

    </script>

@endsection
@endsection
