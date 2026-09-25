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
                              <th>Name</th>
                              <th>Price</th>
                              <th>Discount Price</th>
                              <th>Validity (Days)</th>
                              <th>Type</th>                              
                              <th>What You Got</th>                             
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
                              <td>{{ $a->name ?? "" }}</td>
                              @php
                              $price = isset($a->price) ? (float) (is_object($a->price) ? $a->price->__toString() : $a->price) : 0;
                              $discountPrice = isset($a->discount_price) ? (float) (is_object($a->discount_price) ? $a->discount_price->__toString() : $a->discount_price) : 0;
                              @endphp
                              <td>{{ number_format($price, 0) }}</td>
                              <td>{{ number_format($discountPrice, 0) }}</td>
                              <td>{{ $a->validity_days ?? 0 }}</td>
                              <td>{{ ucfirst($a->type ?? '') }}</td>
                              <td>
                                 @if (!empty($a->what_you_got) && is_array($a->what_you_got))
                                    <ul class="mb-0 ps-3">
                                          @foreach ($a->what_you_got as $item)
                                             @php
                                                $pose = \App\Models\Practiceyogaposes::where('_id', $item['posesId'])->first();
                                             @endphp
                                             <li>
                                                {{ $pose->name ?? 'N/A' }} - 
                                                {{ ($item['is_free'] ?? '0') == 'true' ? 'True' : 'False' }}
                                             </li>
                                          @endforeach
                                    </ul>
                                 @endif
                              </td>                            
                              <td>
                                 <span class="btn {{ $a->status == 1 ? 'btn-primary btn-sm' : 'btn-danger btn-sm' }}">
                                 {{ $a->status == 1 ? 'Active' : 'Inactive' }}
                                 </span>
                              </td>
                              <td>
                                 <a href="{{ route($editurl, $a->_id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                 <span class="mdi mdi-pencil d-block font-size-12"></span>
                                 </a>
                                 <button id="modal-danger" data-userid="{{ $a->_id }}" type="button" class="btn btn-danger btn-sm waves-effect waves-light sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
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
            <form method="POST" action="{{$destroyurl}}" enctype="multipart/form-data">
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