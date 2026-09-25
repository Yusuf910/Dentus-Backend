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
                        {{-- <a href="{{route('admin.create_package_users')}}" class="btn btn-primary waves-effect waves-light" data-bs-toggle="" data-bs-target=""><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
                        </a>  --}}
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
                    <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100 table-sm">
                        <thead>
                           <tr>
                                 <th>#ID</th>
                                 <th>Coach Name</th>
                                 <th>Files</th>
                                 <th>Gender</th>
                                 <th>Pose Name</th>
                                 <th>Level</th>
                                 {{-- <th>Duration (mins)</th>
                                 <th>Status</th> --}}
                                 <th>Created At</th>
                                 <th>Action</th>
                                 {{-- <th>Updated At</th> --}}
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                                 <?php $i = 1; ?>
                                 @foreach ($data as $a)
                                    <tr>
                                       <td>{{ $i }}</td>

                                       @php
                                             $username = \App\Models\Coach::where('_id', $a->userId)->first();
                                             $pose = \App\Models\Practiceyogaposes::where('_id', $a->posesId)->first();
                                             $level = \App\Models\Yogaposeslevels::where('_id', $a->levelId)->first();
                                       @endphp

                                       <td>{{ $username->name ?? 'N/A' }}</td>
                                       <td>
                                          @if(!empty($a->video_file))
                                             <a href="{{ $a->video_file }}" target="_blank" class="btn btn-sm btn-primary">
                                                   View
                                             </a>
                                          @else
                                             <span class="text-muted">N/A</span>
                                          @endif
                                       </td>

                                       <td>{{ ucfirst($a->gender ?? 'N/A') }}</td>
                                       <td>{{ $pose->name ?? 'N/A' }}</td>
                                       <td>{{ $level->levelname ?? 'N/A' }}</td>
                                       {{-- <td>{{ $a->duration ?? '0' }} mins</td>
                                       <td>
                                             <span class="btn 
                                                {{ $a->status == 1 ? 'btn-primary btn-sm' : 
                                                   ($a->status == 2 ? 'btn-warning btn-sm' : 'btn-danger btn-sm') }}">
                                                {{ $a->status == 1 ? 'Active' : ($a->status == 2 ? 'Pending' : 'Inactive') }}
                                             </span>
                                       </td> --}}
                                      <td>
                                          <ul class="table-ul">        
                                          <li>Date: {{date('d/m/Y', strtotime($a->created_at ?? "" ))}}</li>
                                          <li>Time: {{date('h:i:s A', strtotime($a->created_at ?? "" ))}}</li>                            
                                          </ul>
                                       </td>
                                       <td>
                                          <button id="modal-danger" data-userid="{{ $a->id }}" type="button" class="btn btn-danger btn-sm waves-effect waves-light sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                          <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                          </button>
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
                  <h5 class="modal-title" id="myModalLabel">Add Master Taxi</h5>
                  <form method="POST" action="#" enctype="multipart/form-data">
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
            <form method="POST" action="{{route('admin.delete_coachfile')}}" enctype="multipart/form-data">
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