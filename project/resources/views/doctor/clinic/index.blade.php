<style type="text/css">
   
.view-btn{
   padding: 8px !important;
}  

.info-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.info-list li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
} 

</style>


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
                                    <a href="{{$addurl}}" class="btn btn-primary add-pluss ms-2"><img src="{{asset('content/admin')}}/img/icons/plus.svg" alt></a>
                                    <a href="{{$listurl}}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0 table-formatting">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Address</th>
                              <th>Details</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($data->count() > 0)
                           @foreach($data as $a)
                           @php
                               $descriptionWords = str_word_count($a->clinic_description, 2);
                               $limitedDescription = implode(' ', array_slice($descriptionWords, 0, 5));
                               $descriptionWords2 = str_word_count($a->address, 2);
                               $address = implode(' ', array_slice($descriptionWords2, 0, 5));
                           @endphp
                           <tr>
               <td class="profile-image"><img width="28" height="28" src="{{asset('content/doctor/clinic/'.$a->image ?? 'default.png')}}" class="rounded-circle m-r-5" alt>{{$a->clinic_name}}</td>
                        <td>{{$address}}</td>
                           <td>
                  <ul class="info-list">
  <li>Register Number:{{$a->register_number}}</li>
  <li>Description:{{ $limitedDescription }}{{ count($descriptionWords) > 5 ? '...' : '' }}</li>
  <li>Image  <a href="{{route('admin.master.clinicimages',$a->id)}}" type="button" class="btn btn-primary view-btn">
    <i class="fas fa-eye"></i></a>
  </li>
  <li>Timing  <a href="{{route('admin.master.clinictiming',$a->id)}}" type="button" class="btn btn-primary view-btn">
    <i class="fas fa-eye"></i></a>
  </li>
  <li>Holiday  <a href="{{route('admin.master.clinicholiday',$a->id)}}"  type="button" class="btn btn-primary view-btn">
    <i class="fas fa-eye"></i></a>
  </li>
  <li>Slots  <a href="{{route('admin.master.doctimeslots',$a->id)}}"  type="button" class="btn btn-primary view-btn">
    <i class="fas fa-eye"></i></a>
  </li>
</ul>

                           </td>
                           <td>
      <a href="{{route($editurl,$a->id)}}" type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
      <a href="#" class="btn btn-danger ms-1" href="#" data-bs-toggle="modal" data-bs-target="#modal-danger" onclick="sendid('{{$a->id}}')" data-userid="{{$a->id}}"><i class="fas fa-trash"></i></a>
                           </td>
                           </tr>
                           
						         
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	function sendid(id) {
	    console.log("User ID:", id); // Debugging output
	    $('#user_id').val(id);
	    
	};



</script>
@endsection
@endsection