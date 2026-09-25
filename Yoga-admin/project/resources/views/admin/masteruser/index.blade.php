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
                              <th>Name Details</th>
                              <th>Image</th>
                              <th>Position</th>
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
                           $name = json_decode($a->name,true);
                           @endphp
                           <tr>
                              <td>{{ $i }}</td>
                              <td>
                                 <ul>
                                    @foreach ($lang as $l)
                                    <li>Name in {{$l->name}}: {{$name[$l->id]}}</li>
                                    @endforeach
                                    
                                 </ul>
                              </td>
                              <td><img height="100" width="100" src="{{ asset('content/master-user') }}/{{$a->image}}" alt=""></td>
                              <td>{{ $a->position }}</td>
                              <td>
                                 @if($a->status == 1)
                                 <span class="btn btn-primary btn-sm">Active</span>
                                 @else
                                 <span class="btn btn-danger btn-sm">Inactive</span>
                                 @endif
                              </td>
                              <td>
                                 <a href="{{ route($editurl,$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm"><span class="mdi mdi-pencil d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                                 {{-- <button id="modal-danger" data-userid="{{$a['id']}}" type="button" class="btn btn-danger waves-effect waves-light btn-sm sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button>                               --}}
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
               <label for="image" class="form-label">Image</label>
               <input class="form-control" type="file" id="image" placeholder="" name="image">
               </div>
               </div> 
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Position*</label>
               <input required name="position" min="1" class="form-control" type="number" id="1-text-input" placeholder="eg. 1">
               </div>
               </div>
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Status*</label>
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
<div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
   <div class="modal-dialog">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h4 class="modal-title">Delete</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you shure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="user_id" name="id" >
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
@section('scripts')
<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })
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