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
                  <li class="breadcrumb-item"><a href="{{$listurl}}">{{$listname}} </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  
                  <li class="breadcrumb-item active">{{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
      	@include('includes.admin.form-success') 
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body">
                  <!-- <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <div class="col">
                           <div class="doctor-table-blk">
                              <h3>{{$title}}</h3>
                              
                           </div>
                        </div>
                        
                     </div>
                  </div> -->
                  <form method="POST" action="{{ route('admin.master.timingupdateclinictiming',$data->id) }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                  <div class="row1">
                     <div class="col-md-12">

                        @php
                           $availability = $data->timings ? json_decode($data->timings, true) : [];
                        @endphp
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <div class="mb-3">
                          <h5 class="text-capitalize">{{ ucfirst($day) }}</h5>
                          
                          <!-- Morning Slot -->
                          <label for="{{ $day }}_morning_start">Morning:</label>
                          <div class="d-flex">
                              @php
                                  $morning = isset($availability['schedule'][$day]['morning'][0]) 
                                      ? explode(' - ', $availability['schedule'][$day]['morning'][0]) 
                                      : ['', ''];
                              @endphp

                              <input type="time" class="form-control me-2"
                                     name="schedule[{{ $day }}][morning_start]"
                                     value="{{ !empty($morning[0]) && strtotime($morning[0]) ? date('H:i', strtotime($morning[0])) : '' }}">

                              <input type="time" class="form-control"
                                     name="schedule[{{ $day }}][morning_end]"
                                     value="{{ !empty($morning[1]) && strtotime($morning[1]) ? date('H:i', strtotime($morning[1])) : '' }}">
                          </div>

                          <!-- Evening Slot -->
                          <label for="{{ $day }}_evening_start" class="mt-2">Evening:</label>
                          <div class="d-flex">
                              @php
                                  $evening = isset($availability['schedule'][$day]['evening'][0]) 
                                      ? explode(' - ', $availability['schedule'][$day]['evening'][0]) 
                                      : ['', ''];
                              @endphp

                              <input type="time" class="form-control me-2"
                                     name="schedule[{{ $day }}][evening_start]"
                                     value="{{ !empty($evening[0]) && strtotime($evening[0]) ? date('H:i', strtotime($evening[0])) : '' }}">

                              <input type="time" class="form-control"
                                     name="schedule[{{ $day }}][evening_end]"
                                     value="{{ !empty($evening[1]) && strtotime($evening[1]) ? date('H:i', strtotime($evening[1])) : '' }}">
                          </div>
                        </div>
                        @endforeach
                     </div>  
                     
                     <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
                    </div>
                   </form>
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
          <h4 class="modal-title" id="myCenterModal2Label">Add</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
               <div class="col-12 col-md-6 col-xl-6">
                  <div class="input-block local-forms">
                     <label>Image<span class="login-danger">*</span></label>
                     <input required type="file" class="form-control" name="images[]" multiple  accept="image/*">
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