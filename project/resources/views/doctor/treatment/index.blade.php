@extends('layouts.admin')
@section('content')
<?php $data2 = DB::table('users')->where('parent_id',auth()->user()->id)->where('status',1)->orderBy('name','ASC')->get();?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Treatments List</li>
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
                              <h3>Treatments</h3>
                              <div class="doctor-search-blk">
                                 
                                 <div class="add-group">
                                    <a data-bs-toggle="modal" data-bs-target="#centermodal" href="#" class="btn btn-primary add-pluss ms-2"><img src="{{asset('content/admin')}}/img/icons/plus.svg" alt></a>
                                    <a href="{{$listurl}}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                           
                           <a href="{{$listurl}}?exp=export" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-03.svg" alt></a>
                           <a href="{{$listurl}}?exp=export"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-04.svg" alt></a>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0 table-formatting">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Average Duration</th>
                              <th>Call patient before booking confirmation</th>
                              <th>Doctor</th>
                              <th>Treatment Fees</th>
                              <th>Tax</th>
                              <th>Instructions</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($data->count() > 0)
                           @foreach($data as $a)
                           <?php 
                           $treamentdoctor = App\Models\TreatmentDoctor::where('treatment_id',$a->id)->with('doctor')->get();
                			
                           ?>
                           <tr>
                              <td class="profile-image">{{$a->treatment_name}}</td>
                              <td>{{$a->average_duration}}</td>
                              <td>
                                 @if($a->call_before_confirmation==1)
                                    <span class="badge badge-soft-success">Yes</span></td>
                                @elseif($a->call_before_confirmation==0)
                                    <span class="badge badge-soft-warning">No </span>
                                @endif    

                              <td>
                              	<?php $docid = array(); ?>
                              	@if($treamentdoctor->count() > 0 )
                              		@foreach($treamentdoctor as $t)
                              		<?php array_push($docid, $t->doctor_id); ?>
                              		{{$t->doctor->name ?? ''}},
                              		@endforeach
                              	@endif
                              </td>
                              <td><span class="badge badge-soft-success">₹{{$a->treatment_fees}}</span></td>

                              @php $tx = DB::table('master_taxes')->where('id',$a->tax_id)->first(); @endphp
                               <td>{{ $tx->name ?? ""}}</td>
                              <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#centermodal{{$a->id}}"><i class="fas fa-eye"></i></button></td>
                              <td>
                              	 <button data-bs-toggle="modal" data-bs-target="#centermodaledit{{$a->id}}" type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
                                 <a href="#" class="btn btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#modal-danger" onclick="sendid('{{$a->id}}')" data-userid="{{$a->id}}"><i class="fas fa-trash"></i></a>
                              </td>
                           </tr>
                           <div class="modal fade" id="centermodal{{$a->id}}" tabindex="-1" role="dialog" aria-hidden="true">
						         <div class="modal-dialog modal-dialog-centered">
						            <div class="modal-content">
						               <div class="modal-header">
						                  <h4 class="modal-title" id="myCenterModalLabel">Instructions</h4>
						                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						               </div>
						               <div class="modal-body">
						                  <?=$a->instructions?>
						               </div>
						            </div>
						         </div>
						      </div>
						    <div class="modal fade" id="centermodaledit{{$a->id}}" tabindex="-1" role="dialog" aria-hidden="true">
							 <div class="modal-dialog modal-dialog-centered">
							    <div class="modal-content">
							       <div class="modal-header">
							          <h4 class="modal-title" id="myCenterModal2Label">Edit Treatment</h4>
							          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							       </div>
							       <div class="modal-body">
							       
							          {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$importurl, $a->id]]) !!}
							            {{ csrf_field() }}
							            <input type="hidden" value="{{$a->id}}" name="id">
							          	<div class="row">
							          		<div class="col-md-12">
							                   <div class="mb-3">
							                   <label for="treatment_name" class="form-label">Name*</label>
							                   <input type="text" required class="form-control" name="treatment_name" value="{{$a->treatment_name}}">
							                   </div>
							               </div>  
							               <div class="col-md-12">
							                   <div class="mb-3">
							                   <label for="average_duration" class="form-label">Average Duration*</label>
							                   <input type="text" required class="form-control" name="average_duration" value="{{$a->average_duration}}">
							                   </div>
							               </div>  
							               
							               <div class="col-md-12">
							                   <div class="mb-3">
							                   <label for="image" class="form-label">Instructions*</label>
							                   <input type="text" required class="form-control" name="instructions" value="{{$a->instructions}}">
							                   </div>
							               </div> 
							               <div class="col-md-12">
							                   <div class="mb-3">
							                   <label for="treatment_fees" class="form-label">Fees*</label>
							                   <input type="number" required min="1" class="form-control" name="treatment_fees" value="{{$a->treatment_fees}}">
							                   </div>
							               </div> 


                                    <div class="col-md-12">
							                   <div class="mb-3">
							                   <label for="treatment_fees" class="form-label">Tax*</label>
							                    <select class="form-control bundle_search" name="tax_id">
                                          <option value="">Select Tax</option>
                                          <?php $check = DB::table('master_taxes')->where('status',1)->get();
                                          foreach ($check as $n){
                                             ?>
                                          <option value="{{ $n->id }}"
                                          <?php
                                          if($n->id==$a->tax_id){
                                                echo "selected";
                                          }
                                          else{
                                                echo "";
                                          }
                                          ?>
                                          >{{$n->name }}
                                          </option>
                                          <?php } ?>

                                       </select>
							                   </div>
							               </div> 


							               <div class="col-md-12">
							                   <div class="mb-3">
							                   <label for="call_before_confirmation" class="form-label">Call Before Confirmation*</label>
							                   <select name="call_before_confirmation" required class="form-control">
							                    <option {{$a->call_before_confirmation == 1 ? 'selected' : ''}} value="1">Yes</option>
							                    <option {{$a->call_before_confirmation == 0 ? 'selected' : ''}} value="0">No</option>
							                 </select>
							                   </div>
							               </div>
							               <div class="col-md-12">
							                   <div class="mb-3">
							                   	
							                   <label for="doctors" class="form-label">Doctor member*</label>
							                   <select name="doctors[]" required class="form-control" multiple>
							                   	  
                                             <option @if(in_array(auth()->user()->id,$docid)) selected @endif value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                                             @foreach ($data2 as $aa)
								                   	 @if(in_array($aa->id,$docid))
								                   	 	<option selected value="{{ $aa->id }}">Dr. {{$aa->name ?? ''}}</option>
								                   	 @else
								                   	 	<option value="{{ $aa->id }}">Dr. {{$aa->name ?? ''}}</option>
								                   	 @endif
								                   @endforeach
							                 	</select>
							                   </div>
							               </div>    
							               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
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
          <h4 class="modal-title" id="myCenterModal2Label">Add Treatment</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   <label for="treatment_name" class="form-label">Name*</label>
                   <input type="text" required class="form-control" name="treatment_name">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="average_duration" class="form-label">Average Duration*</label>
                   <input type="text" required class="form-control" name="average_duration">
                   </div>
               </div>  
               
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="image" class="form-label">Instructions*</label>
                   <input type="text" required class="form-control" name="instructions">
                   </div>
               </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="treatment_fees" class="form-label">Fees*</label>
                   <input type="number" required min="1" class="form-control" name="treatment_fees">
                   </div>
               </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="call_before_confirmation" class="form-label">Call Before Confirmation*</label>
                   <select name="call_before_confirmation" required class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                 </select>
                   </div>
               </div>

               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="call_before_confirmation" class="form-label">Tax</label>
                    <select class="form-control bundle_search" name="tax_id">
                                <option value="">Select Tax</option>
                                <?php $check = DB::table('master_taxes')->where('status',1)->get();
                                foreach ($check as $n){
                                   ?>
                               <option value="{{ $n->id }}">{{$n->name }}
                              </option>
                               <?php } ?>

                             </select>
                   </div>
               </div>

               <div class="col-md-12">
                   <div class="mb-3">
                   	<label for="doctors" class="form-label">Doctor member*</label>
                   <select name="doctors[]" required class="form-control" multiple>
                   	 <option value="{{auth()->user()->id}}">{{auth()->user()->name}}</option> 
	                   @foreach ($data2 as $aa)
	                   	 <option value="{{ $aa->id }}">Dr. {{$aa->name ?? ''}}</option>
	                   @endforeach
                 	</select>
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