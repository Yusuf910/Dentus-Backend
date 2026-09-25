@extends('layouts.admin')
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{$title}}
                  </h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
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
                  <div class="card-body table-responsive">
                     @include('includes.admin.form-success')
                     <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                        <thead>
                           <tr>
                              <th>#ID</th>
                              <th>Name</th>
                          
                              <th>Image</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i = 1; ?>
                           @foreach ($data as $a)
                           <tr>
                              <td>{{ $i }}</td>
                              <td>{{ $a->name ?? ""}}</td>
                           
                              <td><img src="{{asset('content/categories/')}}/{{ $a->image}}" width="50px"></td>
                              <td>
                                 <span class="btn {{ $a->status == 1 ? 'btn-primary btn-sm' : 'btn-danger btn-sm' }}">
                                    {{ $a->status == 1 ? 'Active' : 'Inactive' }}
                                 </span>
                              </td>
                              <td>
                                 <a href="{{ route($editurl,$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm"><span class="mdi mdi-pencil d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                                 <!-- <button id="modal-danger" data-userid="{{$a['id']}}" type="button" class="btn btn-danger modal-danger waves-effect waves-light btn-sm" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                    <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                    </button>                              -->
                                 <a href="#" href="#" data-userid="{{ $a->id }}" class='btn btn-danger waves-effect waves-light btn-sm' data-toggle="modal" data-target="#modal-danger">
                                    <span class="mdi mdi-trash-can d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                              </td>
                           </tr>
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
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{$title}}</h5>
                  <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="image" class="form-label">Name</label>
                           <input required class="form-control" type="text" id="example-text-input" placeholder="Enter Categorie Name" name="name">
                        </div>
                     </div>

                    



                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="image" class="form-label">Image</label>
                           <input required class="form-control" type="file" id="example-text-input" placeholder="Enter Categorie Name" name="image" accept="image/*">
                        </div>
                     </div>


                     <div class="col-md-12">
                        <div class="mb-3">
                           <label for="example-text-input" class="form-label">Status</label>
                           <select name="flag" required class="form-select">
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert" id="sa-success">Add</button>
               </div>
            </div>
            <!-- /.modal-content -->
            </form>
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
   </div>
   <!-- End Page-content -->
</div>
<div class="modal  fade" id="modal-danger" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h5 class="modal-title">Delete</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you sure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form id="deleteform" method="POST" action="{{$destroyurl}}" enctype="multipart/form-data">
               {{-- @method('DELETE') --}}
               @csrf
               <input type="hidden" id="user_id" name="id" value="">
               <a class="btn btn-danger float-right" onclick="$(this).closest('form').submit();">Delete</a>
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   $('#modal-danger').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget)
      var user_id = button.data('userid')

      var modal = $(this)
      modal.find('.modal-footer #user_id').val(user_id)
   })
</script>
<script>
   $(document).ready(function() {
      $("select").change(function() {
         $(this).find("option:selected").each(function() {
            var optionValue = $(this).attr("value");
            if (optionValue) {
               $(".box").not("." + optionValue).hide();
               $("." + optionValue).show();
            } else {
               $(".box").hide();
            }
         });
      }).change();
   });
</script>
@endsection
@endsection