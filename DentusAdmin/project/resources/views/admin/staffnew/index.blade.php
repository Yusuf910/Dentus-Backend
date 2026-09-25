@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Staff List</h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Create New User
                        </a> 
                     </div>
                  </div>
               </div>
            </div>
         </div>
         {{-- @if ($message = Session::get('success'))
         <div class="alert alert-success">
            <p>{{ $message }}</p>
         </div>
         @endif --}}
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
                              {{-- 
                              <th>Username</th>
                              --}}
                              <th>Name</th>
                              <th>Email</th>
                              <th>Mobile No.</th>
                              <th>Status.</th>
                              <th>CreatedAt</th>
                              <th>Roles</th>
                              <th>Permission</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i=1; ?>
                           @foreach ($data as $a)
                           @if($a->id ==1)
                           @elseif($a->id != Auth()->guard('admin')->user()->id)
                           <tr>
                              <td>{{$i}}</td>
                              {{-- 
                              <td>{{$a->email}}</td>
                              --}}
                              <td>{{$a->name ?? "" }}</td>
                              <td>{{$a->email ?? "" }}</td>
                              <td>{{$a->phone ?? "" }}</td>
                              <td>
                                <span class="btn btn-sm {{ $a->status == 1 ? 'btn-primary' : 'btn-danger' }}">
                                    {{ $a->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                              </td>
                              
                              <td>{{date('d/m/Y | h:i:s A', strtotime($a->created_at ?? "" ))}}</td>
                              <td>
                                 @if ($a->roles->isNotEmpty())
                                 @foreach ($a->roles as $role)
                                 <span class="text-primary">
                                 {{ $role->name }}
                                 </span>
                                 @endforeach
                                 @endif
                              </td>
                              <td>
                                 @if ($a->permissions->isNotEmpty())
                                 @foreach ($a->permissions as $permission)
                                 <span class="text-primary">
                                 {{ $permission->name }},
                                 </span>
                                 @endforeach
                                 @endif  
                              </td>
                              <td>
                                 <a href="{{ route('admin.admins.create') }}"><i class="fa fa-plus"></i> </a><br>
                                 <a href="{{ route('admin.admins.edit',$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                 <i class="mdi mdi-pencil d-block font-size-12"></i>
                                 </a>
                                 <button id="modal-danger" data-userid="{{$a['id']}}" type="button" class="btn btn-danger waves-effect waves-light btn-sm sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button>
                                 {{-- <a href="#" data-toggle="modal" data-target="#modal-danger" data-userid="{{$a['id']}}" class="btn btn-danger waves-effect waves-light btn-sm">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </a> --}}
                                 {{-- <a  href="{{ route('admin.admins.show',$a->id) }}"><i class="fa fa-eye"></i> </a> --}}
                              </td>
                           </tr>
                           @endif
                           <?php $i++; ?>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                     {!! $data->links() !!}
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
</div>
<div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
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
            <form method="POST" action="{{route('admin.destroy_staff')}}" enctype="multipart/form-data">
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
<footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               <script>
                  document.write(new Date().getFullYear())
               </script> © Maskmee.
            </div>
            <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
         </div>
      </div>
   </footer>
@section('scripts')
<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })
</script>
{{-- <script>
  $('#modal-danger').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var user_id = button.data('userid');

    var modal = $(this);
    modal.find('form').attr('action', '/admins/' + user_id); // This is correct for Route::resource
});

   
</script> --}}
@endsection
@endsection