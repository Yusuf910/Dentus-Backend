@extends('layouts.admin')
@section('content')
<?php $data2 = App\Models\Treatment::whereNOTIN('status', [2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">{{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
      	@include('includes.admin.form-success') 
         <div class="col-sm-12">
            <div class="card card-table show-entire">
               <div class="card-body">
                  <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <div class="col">
                           <div class="doctor-table-blk">
                              <h3>{{$title}}</h3>
                              <div class="doctor-search-blk">
                                 
                                 <div class="add-group">
                                    <a data-bs-toggle="modal" data-bs-target="#centermodal" href="#" class="btn btn-primary add-pluss ms-2"><img src="{{asset('content/admin')}}/img/icons/plus.svg" alt></a>
                                    <a href="{{$listurl}}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                           
                           <a href="javascript:;" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-03.svg" alt></a>
                           <a href="javascript:;"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-04.svg" alt></a>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0 table-formatting">
                        <thead>
                           <tr>
                              <th>Plan Name</th>
                              <th>Select Treatment & Session</th>
                              <th>Package Validity</th>
                              <th>Package Price</th>
                              <th>Discount Price</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($data->count() > 0)
                           @foreach($data as $a)
                           <?php 
                           // $treamentdoctor = App\Models\TreatmentDoctor::where('treatment_id',$a->id)->with('doctor')->get();
                			
                           ?>
                           <tr>
                              <td class="profile-image">{{$a->name}}</td>
                              <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#centermodal{{$a->id}}"><i class="fas fa-eye"></i></button></td>
                              <td><span class="badge badge-soft-success">{{$a->validity}} {{$a->type}}</span></td>
                              <td><span class="badge badge-soft-success">{{$a->price}}</span></td>
                              <td><span class="badge badge-soft-success">{{$a->discount_price}}</span></td>
                              <td>
                              	 <button data-bs-toggle="modal" data-bs-target="#centermodaledit{{$a->id}}" type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
                                 <a href="#" class="btn btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#modal-danger" onclick="sendid('{{$a->id}}')" data-userid="{{$a->id}}"><i class="fas fa-trash"></i></a>
                              </td>
                           </tr>
                           <div class="modal fade" id="centermodal{{$a->id}}" tabindex="-1" role="dialog" aria-hidden="true">
   						         <div class="modal-dialog modal-dialog-centered">
   						            <div class="modal-content">
   						               <div class="modal-header">
   						                  <h4 class="modal-title" id="myCenterModalLabel">Treatment & Session</h4>
   						                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   						               </div>
   						               <div class="modal-body">
   						                  <ul style="list-style-type: none; padding-left: 0px;" class="mb-0">
                                            @if($a->featurelist->count())
                                            @foreach($a->featurelist as $aa)
                                            <li style="display: flex; justify-content: space-between;" class="mb-2">
                                              <span>{{$aa->name}} </span> <span>{{$aa->quantity}} Sessions</span>
                                            </li>
                                             @endforeach
                                             @endif
                                            
                                       </ul>
   						               </div>
   						            </div>
   						         </div>
   						      </div>
						         <div class="modal fade" id="centermodaledit{{$a->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                               <div class="modal-content">
                                  <div class="modal-header">
                                     <h4 class="modal-title" id="myCenterModal2Label">Edit Patients’ Plans</h4>
                                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                  
                                     {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$importurl, $a->id]]) !!}
                                       {{ csrf_field() }}
                                       <div class="row">
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="name" class="form-label">Name*</label>
                                              <input type="text" required class="form-control" name="name" value="{{$a->name}}">
                                              </div>
                                          </div>  
                                          
                                          
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="description" class="form-label">Description*</label>
                                              <input type="text" required class="form-control" name="description" value="{{$a->description}}">
                                              </div>
                                          </div> 
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="price" class="form-label">Price*</label>
                                              <input type="number" required min="1" class="form-control" name="price" value="{{$a->price}}">
                                              </div>
                                          </div> 
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="price" class="form-label">Discount Price*</label>
                                              <input type="number" required min="0" class="form-control" name="discount_price" value="{{$a->discount_price}}">
                                              </div>
                                          </div> 
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="type" class="form-label">Type*</label>
                                              <select name="type" required class="form-control">
                                               <option {{$a->type == 'Month' ? 'selected' : ''}} value="Month">Month</option>
                                               <option {{$a->type == 'Year' ? 'selected' : ''}} value="Year">Year</option>
                                            </select>
                                              </div>
                                          </div>
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="validity" class="form-label">Validity*</label>
                                              <input value="{{$a->validity}}" type="number" min="1" required class="form-control" name="validity">
                                              </div>
                                          </div>  
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                                <label for="doctors" class="form-label">Premium Features*</label>
                                              
                                                 @foreach ($data2 as $aa)
                                                 @php
                                                     // Find the existing quantity from $a->featurelist based on treatment_id
                                                     $existingFeature = $a->featurelist->firstWhere('treatment_id', $aa->id);
                                                     $quantityValue = $existingFeature ? $existingFeature->quantity : ''; 
                                                 @endphp
                                                    <div class="d-flex">
                                                      <input type="text" readonly class="form-control me-2" value="{{ $aa->treatment_name }}">
                                                      <input type="hidden" value="{{ $aa->id }}" name="treatment_id[{{ $aa->id }}]">
                                                      <input type="hidden" value="{{ $aa->treatment_name }}" name="treatment_name[{{ $aa->id }}]">
                                                      <input type="number" min="1" class="form-control" name="quantity[{{ $aa->id }}]" placeholder="Quantity" value="{{ $quantityValue }}">
                                                  </div>
                                                 @endforeach
                                             
                                              </div>
                                          </div>    
                                          <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
                                      </div>
                                     </form>
                                  </div>
                               </div>
                            </div>
                           </div>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal modal-danger fade" id="modal-danger">
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
             <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
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
@section('js_user_page')
<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Add Patients’ Plans</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   <label for="name" class="form-label">Name*</label>
                   <input type="text" required class="form-control" name="name">
                   </div>
               </div>  
               
               
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="description" class="form-label">Description*</label>
                   <input type="text" required class="form-control" name="description">
                   </div>
               </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="price" class="form-label">Price*</label>
                   <input type="number" required min="1" class="form-control" name="price">
                   </div>
               </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="price" class="form-label">Discount Price*</label>
                   <input type="number" required min="0" class="form-control" name="discount_price">
                   </div>
               </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="type" class="form-label">Type*</label>
                   <select name="type" required class="form-control">
                    <option value="Month">Month</option>
                    <option value="Year">Year</option>
                 </select>
                   </div>
               </div>
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="validity" class="form-label">Validity*</label>
                   <input type="number" min="1" required class="form-control" name="validity">
                   </div>
               </div>  
               
               <div class="col-md-12">
                   <div class="mb-3">
                   	<label for="doctors" class="form-label">Premium Features*</label>
                   
	                   @foreach ($data2 as $aa)
	                   	 <div class="d-flex">
                           <input type="text" readonly class="form-control me-2" value="{{ $aa->treatment_name }}">
                           <input type="hidden" value="{{ $aa->id }}" name="treatment_id[{{ $aa->id }}]">
                           <input type="hidden" value="{{ $aa->treatment_name }}" name="treatment_name[{{ $aa->id }}]">
                           <input type="number" min="1" class="form-control" name="quantity[{{ $aa->id }}]" placeholder="Quantity">
                       </div>
	                   @endforeach
                 	
                   </div>
               </div>    
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	function sendid(id) {
	    console.log("User ID:", id); // Debugging output
	    $('#user_id').val(id);
	    
	};



</script>
@endsection
@endsection