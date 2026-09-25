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
         {!! Form::open(array('route' => 'admin.admins.store','method'=>'POST')) !!}
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Add Staff</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.stafflist') }}">Staff List</a></li>
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
                              <label for="example-text-input" class="form-label">Username</label>
                              {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control','autocomplete' => 'off')) !!}
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Email</label>
                              {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Password</label>
                              {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Mobile</label>
                              {!! Form::text('phone', null, array('placeholder' => 'Phone','class' => 'form-control')) !!}
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Status</label>
                              <select name="status" class="form-select">
                                 <option value="1">Active</option>
                                 <option value="0">Inactive</option>
                              </select>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Role</label>
                              <select class="role form-control" name="role" id="role">
                                 <option value="">Select Role...</option>
                                 @foreach ($roles as $role)
                                 <option data-role-id="{{$role->id}}" data-role-slug="{{$role->slug}}" value="{{$role->id}}">{{$role->name}}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                        <div class="col-md-12">
                            <label for="example-text-input" class="col-sm-2 col-form-label">Select Permissions</label>
                              <div class="row" id="permissions_ckeckbox_list">
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
</div>
@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<script>
   $(document).ready(function(){
       var permissions_box = $('#permissions_box');
       var permissions_ckeckbox_list = $('#permissions_ckeckbox_list');
   
       permissions_box.hide(); // hide all boxes
   
      
       $('#role').on('change', function() {

           var role = $(this).find(':selected');    
           var role_id = role.data('role-id');
           var role_slug = role.data('role-slug');
   
           permissions_ckeckbox_list.empty();
   
           $.ajax({
               url: "create",
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
                       '<div class="col-md-3 form-check mb-3">'+
                           '<input class="form-check-input" type="checkbox" name="permissions[]" id="'+ element.slug +'" value="'+ element.id +'">'+
                           '<label class="form-check-label" for="'+ element.slug +'">'+ element.name +
                           '</label>'+
                        '</div>'
                       // '<div class="custom-control custom-checkbox">'+                         
                       //     '<input class="custom-control-input" type="checkbox" name="permissions[]" id="'+ element.slug +'" value="'+ element.id +'">' +
                       //     '<label class="custom-control-label" for="'+ element.slug +'">'+ element.name +'</label>'+
                       // '</div>'
                   );
   
               });
           });
       });
   });
   
</script>
@endsection
@endsection