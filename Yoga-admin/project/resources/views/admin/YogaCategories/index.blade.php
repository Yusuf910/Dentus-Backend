@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal">
                           <i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
                        </a> 
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- end page title -->

         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body table-responsive">
                     @include('includes.admin.form-success') 
                     <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100 table-sm">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>Name</th>
                              <th>Image</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           @foreach ($data as $index => $a)
                           <tr>
                              <td>{{ $index + 1 }}</td>
                              <td>{{ $a->name ?? "" }}</td>
                               <td>
                                 {{-- <img src="{{ asset('yoga/category/' . ($a->image ?? '')) }}" width="50px" height="50px" style="object-fit:cover;"> --}}
                                 <img src="{{ $a->image ?? "" }}"  width="50px" height="50px" style="object-fit:cover;">

                              </td>
                             
                              <td>
                                 @if($a->status == 1)
                                    <span class="btn btn-primary btn-sm">Active</span>
                                 @else
                                    <span class="btn btn-danger btn-sm">Inactive</span>
                                 @endif
                              </td>
                              <td>
                                 <a href="{{ route($editurl, $a->_id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                    <i class="mdi mdi-pencil font-size-12"></i>
                                 </a>
                                 <button id="modal-danger" data-userid="{{ $a->_id }}" type="button" class="btn btn-danger btn-sm waves-effect waves-light sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button>
                                 
                              </td>
                           </tr>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Add Modal -->
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{ $title }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                  @csrf
                  <div class="modal-body">
                     <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input required class="form-control" type="text" name="name" placeholder="Enter name">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input required class="form-control" type="file" name="image" accept="image/*">
                     </div>
                     <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                           <option value="1">Active</option>
                           <option value="0">Inactive</option>
                        </select>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
                  </div>
               </form>
            </div>
         </div>
      </div>

      <!-- Delete Modal -->
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
            <form method="POST" action="{{route('admin.destroy_yoga_categories')}}" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="user_id" name="_id" value="">
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>

   </div>
</div>

@section('scripts')
<script>
   $('.sddel').click('show.bs.modal', function (event) {
   
       var user_id = $(this).attr('data-userid')
   
       $('#user_id').val(user_id);
   })
</script>
@endsection
@endsection
