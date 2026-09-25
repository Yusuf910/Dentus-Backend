@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Role List</h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Add
                        </a> 
                        <!-- <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                           </button> -->
                     </div>
                  </div>
               </div>
            </div>
         </div>        
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  @include('includes.admin.form-success')
                  <div class="card-body table-responsive">
                     <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                        <thead>
                           <tr>
                              <th>#ID</th>
                              <th>Role</th>
                              {{-- <th>Slug</th> --}}
                              <th>Permissions</th>
                              <th>Status</th>
                              <th>Created At</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @foreach ($roles as $key => $role)
                           <tr>
                              <td>{{ ++$i }}</td>
                              <td>{{ $role->name }}</td>
                              {{-- <td>{{ $role->slug }}</td> --}}
                              <td>
                                 @if ($role->permissions != null)
                                 @foreach ($role->permissions as $permission)
                                 <span class="text-primary">
                                 {{ $permission->name }},                                    
                                 </span>
                                 @endforeach
                                 @endif
                              </td>
                              <td>
                                 <span class="badge {{ $role->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                     {{ $role->status == 1 ? 'Active' : 'Inactive' }}
                                 </span>                                   
                             </td>
                              <td>{{ date('j F Y, h:i A', strtotime($role->created_at ?? '')) }}</td>
                      
                              <td>
                                 <!-- <a href="{{ route('admin.roles.create') }}"><i class="fa fa-plus"></i> </a> -->
                                 <a href="{{ route('admin.roles.edit',$role->id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                 <i class="mdi mdi-pencil d-block font-size-12"></i>
                                 </a>
                                 <button id="modal-danger" data-userid="{{$role['id']}}" type="button" class="btn btn-danger waves-effect waves-light btn-sm sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button> 
                                 <!-- <button id="modal-danger" data-userid="{{$role->id}}" type="button" class="btn btn-danger waves-effect waves-light btn-sm sdss" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                    <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                    </button>  -->
                                 <!-- <a  href="{{ route('admin.roles.show',$role->id) }}"><i class="fa fa-eye"></i> </a> -->
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
   </div>
   <!-- End Page-content -->
   <!-- sample modal content -->
   <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="myModalLabel">Add Role</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="mb-3">
                        <label for="example-text-input" class="form-label">Name</label>
                        <input class="form-control" type="text">
                     </div>
                  </div>
                  <div class="col-md-12">
                     <div class="mb-3">
                        <label for="example-text-input" class="form-label">Status</label>
                        <select class="form-select">
                           <option>Active</option>
                           <option>Inactive</option>
                        </select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-primary waves-effect waves-light">Add</button>
            </div>
         </div>
         <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
   </div>
   <!-- /.modal -->
   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               <script>
                  document.write(new Date().getFullYear())
               </script> © Repaireze.
            </div>
            <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
         </div>
      </div>
   </footer>
</div>
{{-- <div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
   <div class="modal-dialog">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h4 class="modal-title">Delete</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you sure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form method="POST" action="">
               @method('DELETE')
               @csrf
               <input type="hidden" id="user_id" name="user_id" >
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div> --}}
<div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
   <div class="modal-dialog">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h4 class="modal-title">Delete</h4>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you sure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form method="POST" action="{{route('admin.destroy_role')}}" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="user_id" name="id" value="">
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
{!! $roles->render() !!}
@section('scripts')
<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })
</script>
{{-- <script>
   $('.sdss').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       console.log(user_id);
       $('#user_id').val(user_id);
   })
</script> --}}
@endsection
@endsection