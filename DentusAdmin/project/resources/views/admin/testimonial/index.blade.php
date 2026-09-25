@extends('layouts.admin') 
@section('content')

<div class="main-content">
   <div class="page-content">
       <div class="container-fluid">
           <!-- start page title -->
           <div class="row">
               <div class="col-12">
                   <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                       <h4 class="mb-sm-0 font-size-18">Testimonial List

                     </h4>
                        <div class="page-title-right">
           <div class="d-flex flex-wrap gap-2">
            <a href="#" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Create New Testimonial
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
   <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                               <thead>
                                 <tr>
                                    <th>#ID</th>
                                    <th>Description</th>
                                    <th>Position</th>
                                    <th>in_home</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                               </thead>

                               <tbody>
                                 @if (!empty($data))
                                   <?php $i=1; ?>
                                   @foreach ($data as $a)
                                   @php 
                                    $lang = App\Models\AppLanguage::get();
                                    $name = json_decode($a->description,true);
                                    @endphp
                                    <tr>
                                       <td>{{ $i }}</td>
                                       <td>
                                       <ul>
                                          @foreach ($lang as $l)
                                          @if($name)
                                          <li>Name in {{$l->name}}: {{$name[$l->id]}}</li>
                                          @else 
                                          {{ $a->description }}
                                          @endif
                                          @endforeach
                                          
                                       </ul>
                                       </td>
                                       <td>{{ $a->position }}</td>
                                       <td>{{ $a->in_home }}</td>
                                       

                                       <td>
                                          @if($a->status == 1)
                                          <span class="btn btn-primary btn-sm">Active</span>
                                          @else
                                          <span class="btn btn-danger btn-sm">Inactive</span>
                                          @endif
                                       </td> 
                                       <td>
                                          <a href="{{ route($editurl,$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm"><span class="mdi mdi-pencil d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                                          <a href="#" data-userid="{{ $a->id }}" class='btn btn-danger waves-effect waves-light btn-sm' data-toggle="modal" data-target="#modal-danger" >
                                         <span class="mdi mdi-trash-can d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
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
         <div class="modal-dialog">
         <div class="modal-content">
         <div class="modal-header">
<h5 class="modal-title" id="myModalLabel">Add Testimonial</h5>
<form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
   {{ csrf_field() }}
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
       <div class="modal-body">
           <div class="row">

           @php $col = 4 @endphp
               @include('commonload.mullang',compact('col'))
                <div class="col-md-12">
         <div class="mb-3">
<label for="example-text-input" class="form-label">User</label>
<!-- <input required class="form-control" type="text" id="role_name" placeholder="Enter description" name="description"> -->
<select class="form-select" name="user_id" id="" required>
                                        <option value="">Select User</option>
                                        <?php $check = DB::table('master_users')->get();
                                        foreach ($check as $n){
                                           ?>

                                        <?php
                                        $name5 = json_decode($n->name,true);
                                        $a_name5 = $name5[1] ?? 'Astro N';
                                        ?>

                                       <option value="{{ $n->id }}" >{{$a_name5 }}
                                       </option>
                                       <?php } ?>

                                </select>
                 </div>
               </div> 
                <div class="col-md-12">
         <div class="mb-3">
<label for="example-text-input" class="form-label">Position</label>
<input required name="position" min="1" class="form-control" type="text" id="example-text-input" placeholder="eg. 1">
                 </div>
               </div> 

                        <div class="col-md-12">
                           <div class="mb-3">
                  <label for="example-text-input" class="form-label">In Home</label>

                  <select name="in_home" required class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                 </select>


                                   </div>
                                 </div> 

                                 <div class="col-md-12">
                                    <div class="mb-3">
                           <label for="example-text-input" class="form-label">Video URL</label>
                           <input required name="video_url" class="form-control" type="text" id="example-text-input" placeholder="Video URL">
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
             </div><!-- /.modal-content -->
            </form>
             </div><!-- /.modal-dialog -->
             </div><!-- /.modal -->
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
    <script>
      $('#modal-danger').on('show.bs.modal', function(event) {
       var button = $(event.relatedTarget)
       var user_id = button.data('userid')

       var modal = $(this)
       modal.find('.modal-footer #user_id').val(user_id)
   })
    </script>

<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
<script src="{{asset('project/public/adminassets')}}/js/jquery.Jcrop.js"></script>
<script src="{{asset('project/public/adminassets')}}/js/jquery.SimpleCropper.js"></script>
<script>
   $('.cropme').simpleCropper();
   CKEDITOR.replace('description');
</script>
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

<script>
   // $('.sddel').click('show.bs.modal', function (event) {
   //     var user_id = $(this).attr('data-userid')
   //     $('#user_id').val(user_id);
   // })
   $(document).ready(function(){
            <?php 
               $g = 1;
               foreach ($lang as $l) {
                  if ($g == 1) {
                     echo "$('#".$l->name."').keyup(function(e){
                        var str = $('#".$l->name."').val();";

                  }
                  else {
                     echo "$('#".$l->name."').val(str);$('#".$l->name."').attr('placeholder', str);";
                  }
                  $g++;
               }
               echo "});"
            ?>
            
        });
</script>

@endsection
@endsection