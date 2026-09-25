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
                        <a href="{{route('admin.create_yoga_pose_levels')}}" class="btn btn-primary waves-effect waves-light" data-bs-toggle="" data-bs-target=""><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
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
                     <form method="GET" action="{{ $listurl }}" class="row mb-3">

                      <div class="col-md-3">
                          <select name="posesId" class="form-select">
                              <option value="">All Pose Names</option>
                              @foreach($posesList as $id => $p)
                                  <option value="{{ $id }}" {{ request('posesId') == $id ? 'selected' : '' }}>
                                      {{ $p->name }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-2">
                         <select name="levelname" class="form-select">
                             <option value="">All Levels</option>
                             <option value="Beginner" {{ request('levelname') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                             <option value="Intermediate" {{ request('levelname') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                             <option value="Advanced" {{ request('levelname') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                         </select>
                     </div>


                      <div class="col-md-2">
                          <select name="status" class="form-select">
                              <option value="">All</option>
                              <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                              <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                          </select>
                      </div>

                      <div class="col-md-2">
                          <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                      </div>

                      <div class="col-md-2">
                          <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                      </div>

                      <div class="col-md-2 mt-2">
                          <button class="btn btn-primary w-100">Filter</button>
                      </div>
                      <div class="col-md-2 mt-2">
                          <a class="btn btn-primary w-100" href="{{route('admin.yoga_pose_levels')}}">Reset</a>
                      </div>

                  </form>

                     <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100 table-sm">
                        <thead>
                           <tr>
                                 <th>#ID</th>
                                 <th>Pose Name</th>
                                 <th>Level Name</th>
                                 {{-- <th>Duration (mins)</th>
                                 <th>Video</th> --}}
                                 <!-- <th>Steps</th> -->
                                 <th>Status</th>
                                 <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i = 1; ?>
                           @foreach ($data as $a)
                           <tr>
                              <?php
                              $poses = \App\Models\Practiceyogaposes::where('_id', $a->posesId)->first();
                              ?>

                                 <td>{{ $i }}</td>
                                 <td>{{ $posesList[(string)$a->posesId]->name ?? '' }}</td>
                                 <td>{{ $a->levelname ?? "" }}</td>
                                 {{-- <td>{{ $a->duration ?? 0 }}</td> --}}
                                 {{-- <td>
                                    @if(!empty($a->video))
                                    <a href="{{ $a->video }}" target="_blank" class="btn btn-info btn-sm">View</a>
                                    @else
                                    N/A
                                    @endif
                                 </td> --}}
                                 {{--<td>
                                    @if (!empty($a->to_do) && is_array($a->to_do))
                                    <ul class="mb-0 ps-3">
                                       @foreach ($a->to_do as $item)
                                       <li>{{ $item['points'] ?? '' }}</li>
                                       @endforeach
                                    </ul>
                                    @endif
                                 </td>--}}
                                 <td>
                                    <span class="btn {{ $a->status == 1 ? 'btn-primary btn-sm' : 'btn-danger btn-sm' }}">
                                       {{ $a->status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                 </td>
                                 <td>
                                    <a href="{{ route($editurl, $a->_id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                       <span class="mdi mdi-pencil d-block font-size-12"></span>
                                    </a>
                                    <button id="modal-danger" data-userid="{{ $a->_id }}" type="button" 
                                       class="btn btn-danger btn-sm waves-effect waves-light sddel" 
                                       data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                       <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                    </button>
                                 </td>
                           </tr>
                           <?php $i++; ?>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                     {{ $data->links() }}
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
                  <h5 class="modal-title" id="myModalLabel">Add Master Taxi</h5>
                  <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
               <div class="row">
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Image</label>
               <input required name="image" class="form-control" type="file" id="example-text-input" placeholder="eg. 1"  >
               </div>
               </div> 
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label"> Name</label>
               <input required class="form-control" type="text" id="example-text-input" placeholder="Enter Name" name="name">
               </div>
               </div> 
               {{-- <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Description</label>
               <textarea id="ckeditor-classic1" rows="5" class="form-control" placeholder="Enter the Description here..." spellcheck="false" name="heading_text"></textarea>
               </div>
               </div>  --}}
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Price</label>
               <input required name="fixed_price" min="1" class="form-control" type="number" id="example-text-input" placeholder="eg. 1">
               </div>
               </div>
               {{-- <?php $taxes = DB::table('master_taxes')->where('status',1)->get();?>
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Tax</label>
               <select name="tax_id" required class="form-select">
               <option value="">{{ __('Select Tax') }}</option>
               @if(isset($taxes) && count($taxes) > 0)
               @foreach ($taxes as $p)
               <option value="{{ $p->id }}">{{ htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') }}</option>
               @endforeach
               @endif
               </select>
               </div>
               </div>               --}}
               <div class="col-md-12">
               <div class="mb-3">
               <label for="example-text-input" class="form-label">Status</label>
               <select name="status" required class="form-select">
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
            <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
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
<footer class="footer">
   <div class="container-fluid">
      <div class="row">
         <div class="col-sm-6">
            <script>
               document.write(new Date().getFullYear())
            </script> © Yoga.
         </div>
         <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
      </div>
   </div>
</footer>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   $('.sddel').click('show.bs.modal', function (event) {
   
       var user_id = $(this).attr('data-userid')
   
       $('#user_id').val(user_id);
   })
</script>
<script>          
   ClassicEditor.create(document.querySelector("#ckeditor-classic1"))
</script>
@endsection
@endsection