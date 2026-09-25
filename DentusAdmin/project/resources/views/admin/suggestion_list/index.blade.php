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
                        <!-- <form method="GET" action="" enctype="multipart/form-data">
                           <span>
                          <input type="submit" name="export_file" value="Export" class="btn btn-primary waves-effect waves-light" >
                          </span>
                           </form> -->
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
                              <th>Category Name</th>
                              <th>Subcategory Name</th>
                              <th>Name</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i=1; ?>
                           @foreach ($data as $a)
                           <tr>
                              <td>{{ $i }}</td>
                              <?php $dt = DB::table('suggestion_categories')->where('id',$a->category_id)->first() ?>
                              <td>{{ $dt->name }}</td>
                              <?php $dt_sub = DB::table('suggestion_sub_categories')->where('id',$a->sub_category_id)->first() ?>
                              <td>{{ $dt_sub->name }}</td>
                              <td>{{ $a->message }}</td>
                              <td>
                                 @if($a->status == 1)
                                 <span class="btn btn-primary btn-sm">Active</span>
                                 @else
                                 <span class="btn btn-danger btn-sm">Inactive</span>
                                 @endif
                              </td>
                              <td>
                                 <a href="{{ route($editurl,$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm"><span class="mdi mdi-pencil d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                                 {{-- <a href="#" href="#" data-userid="{{ $a->id }}" class='btn btn-danger waves-effect waves-light btn-sm' data-toggle="modal" data-target="#modal-danger" >
                              <span class="mdi mdi-trash-can d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>                           --}}
                               </td>
                           </tr>
                           <?php $i++; ?>
                           @endforeach
                           @endif
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
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{$title}}</h5>
                  <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <div class="row">
                   <div class="col-md-12">
                           <div class="mb-3">
                           <label for="example-text-input" class="form-label">Category Name</label>
                           <select class="form-select" name="category_id" id="category_id" required>
                                        <option value="">Select Name</option>
                                        <?php $check = DB::table('suggestion_categories')->whereNOTIN('status',[2])->get();
                                        foreach ($check as $n){
                                           ?>
                                       <option value="{{ $n->id }}" >{{$n->name }}
                                       </option>
                                       <?php } ?>

                                </select>
                           </div>
                       </div>

                       <div class="col-md-12">
                           <div class="mb-3">
                           <label for="example-text-input" class="form-label">Subcategory Name</label>
                           <select class="form-control" name="sub_category_id" id="sub_category_data">
                                        <option value="0" selected="selected">Select Subcategory</option>
                                    </select>
                           </div>
                       </div>

                       <div class="col-md-12">
                           <div class="mb-3">
                           <label for="example-text-input" class="form-label">Message</label>
                           <input required class="form-control" type="text" id="example-text-input" placeholder="message" name="message">
                           </div>
                       </div>
                        
                       <div class="col-md-12">
                           <div class="mb-3">
                               <label for="example-text-input" class="form-label">Status</label>
                               <select name="status" required class="form-control">
                               <option value="1">Active</option>
                               <option value="0">Inactive</option>
                               </select>
                           </div>
                       </div>
                   </div>                                    
               </div>
               <div class="modal-footer">
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
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
                        <form id ="deleteform" method="POST" action="{{$destroyurl}}" enctype="multipart/form-data">
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
   $('#modal-danger').on('show.bs.modal', function(event) {
       var button = $(event.relatedTarget)
       var user_id = button.data('userid')

       var modal = $(this)
       modal.find('.modal-footer #user_id').val(user_id)
   })
  
</script>
<script type="text/javascript">
        $(document).ready(function(){

          $('#category_id').on('change',function(){
            var id=$(this).val();
            //alert( id);
            $.ajax({
              url: "{{ route('admin.master.subcat_data') }}",
              method:'GET',
              data:{'ide':id},
               success:function(data)
                {
                 $('#sub_category_data').html(data);
                }
            });
          });
        });
      </script>
@endsection
@endsection