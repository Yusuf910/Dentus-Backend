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
                  <li class="breadcrumb-item active">Seasonal offer List</li>
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
                              <h3>Seasonal offer</h3>
                              <div class="doctor-search-blk">
                                 
                                 <div class="add-group">
                                    <a data-bs-toggle="modal" data-bs-target="#centermodal" href="#" class="btn btn-primary add-pluss ms-2"><img src="{{asset('content/admin')}}/img/icons/plus.svg" alt></a>
                                    
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
                              <th>Image</th>
                              <th>Treatment</th>
                              <th>Offer Validity</th>
                              <th>Discount</th>
                              <th>Offer Type</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($blogs->count() > 0)
                           @foreach($blogs as $a)
                           <tr>
                              <td class="profile-image"><img width="28" height="28" src="{{$a->image}}" class="rounded-circle m-r-5" alt></td>
                              <td>{{$a->treatment_name}}</td>
                              <td>{{$a->start_date}} to {{$a->end_date}}</td>
                              <td>{{$a->discount}}%</td>
                              <td>{{$a->offer_type}}</td>

                              <td>
                              	 <button data-bs-toggle="modal" data-bs-target="#centermodaledit{{$a->id}}" type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
                                 <a href="#" class="btn btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#modal-danger" onclick="sendid('{{$a->id}}')" data-userid="{{$a->id}}"><i class="fas fa-trash"></i></a>
                              </td>
                           </tr>
                           <div class="modal fade" id="centermodaledit{{$a->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                         <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                               <div class="modal-header">
                                  <h4 class="modal-title" id="myCenterModal2Label">Edit </h4>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                               </div>
                               <div class="modal-body">
                               
                                  {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$importurl, $a->id]]) !!}
                                    {{ csrf_field() }}
                                    <input type="hidden" value="{{$a->id}}" name="id">
                                       <div class="row">
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="offer_type" class="form-label">Name*</label>
                                              <input type="text" required class="form-control" name="offer_type" value="{{$a->offer_type}}">
                                              </div>
                                          </div>  
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="average_duration" class="form-label">Treatment*</label>
                                              <select name="treat_package_id" id="treat_package_id" required class="form-control">
                                                 <option value="">Select</option>
                                                 @foreach ($t as $tt)
                                                     <option {{$a->treat_package_id == $tt->id ? 'selected' : ''}} value="{{$tt->id}}">{{$tt->treatment_name}}</option>
                                                 @endforeach
                                             </select>
                                              </div>
                                          </div>  
                                          <div class="col-md-12">
                                               <div class="mb-3">
                                                   <label for="discount" class="form-label">Discount*</label>
                                                   <input type="number" max="99" value="{{$a->discount}}" min="1" required class="form-control" name="discount">
                                               </div>
                                           </div> 

                                           <!-- Start Date -->
                                           <div class="col-md-12">
                                               <div class="mb-3">
                                                   <label for="start_date" class="form-label">Start Date*</label>
                                                   <input id="start_date_edit" type="date" value="{{$a->start_date}}"  required class="form-control" name="start_date">
                                               </div>
                                           </div>

                                           <!-- End Date -->
                                           <div class="col-md-12">
                                               <div class="mb-3">
                                                   <label for="end_date" class="form-label">End Date*</label>
                                                   <input id="end_date_edit" type="date" value="{{$a->end_date}}" min="{{date('Y-m-d')}}" required class="form-control" name="end_date">
                                               </div>
                                           </div> 

                                           <!-- Image Upload -->
                                           <div class="col-md-12">
                                               <div class="mb-3">
                                                   <label for="image" class="form-label">Image*</label>
                                                   <input type="file" class="form-control" name="image">
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
<?php 
$admin = App\Models\SeasonalOffers::where('created_by', 0)
  ->where('status', 1)
  ->orderBy('start_date')
  ->get();

  $admin->each(function ($blog) {
      $treatment = DB::table('treatments')
          ->where('id', $blog->treat_package_id)
          ->first(['treatment_name']);

      if ($treatment) {
          $blog->treatment_name = $treatment->treatment_name;
      } else {
          $blog->treatment_name = null;
      }

      $blog->image = asset('content/SeasonalOffers') . '/' . $blog->image;
  });

?>
<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Add</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   <label for="offer_type" class="form-label">Offer Type*</label>
                   <select name="offer_type" id="offer_type" required class="form-control">
                    <option value="">select</option>
                      @foreach ($admin as $aa)
                         <option value="{{$aa->id}}" data-offer="{{ json_encode($aa) }}">{{$aa->offer_type ?? ''}}</option>
                      @endforeach
                      <option value="custom">Create Your Own</option>
                  </select>
               </div>  
               <div class="col-md-12" id="custom_offer_div" style="display: none;">
                    <div class="mb-3">
                        <label for="custom_offer_type" class="form-label">Custom Offer Type*</label>
                        <input type="text" id="custom_offer_type" class="form-control" name="custom_offer_type" placeholder="Enter your offer type">
                    </div>
                </div>

                <!-- Treatment -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="treat_package_id" class="form-label">Treatment*</label>
                        <select name="treat_package_id" id="treat_package_idadd" required class="form-control">
                            <option value="">Select</option>
                            @foreach ($t as $tt)
                                <option value="{{$tt->id}}">{{$tt->treatment_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>  

                <!-- Discount -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount*</label>
                        <input type="number" max="99" id="discount" min="1" required class="form-control" name="discount">
                    </div>
                </div> 

                <!-- Start Date -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date*</label>
                        <input type="date" id="start_date" min="{{date('Y-m-d')}}" required class="form-control" name="start_date">
                    </div>
                </div>

                <!-- End Date -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date*</label>
                        <input type="date" id="end_date" min="{{date('Y-m-d')}}" required class="form-control" name="end_date">
                    </div>
                </div> 

                <!-- Image Upload -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image*</label>
                        <input type="file" class="form-control" name="image">
                        <img id="offer_image" src="" alt="Offer Image" style="display:none; width:100px; margin-top:10px;">
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
<script>
$(document).ready(function () {
    $('#offer_type').on('change', function () {
        var selectedOption = $(this).find('option:selected');
        var offerData = selectedOption.data('offer');
        if (offerData) {
            if (offerData) {
                // Convert to string and trim in case of space issues
                var treatId = String(offerData.treat_package_id).trim();

                // Check if the option exists in the treatment select
                var $treatmentSelect = $('#treat_package_idadd');
                var $matchedOption = $treatmentSelect.find('option').filter(function () {
                    return $.trim($(this).val()) === treatId;
                });

                if ($matchedOption.length) {
                    $treatmentSelect.val(treatId).trigger('change');
                    console.log('Treatment selected:', treatId);
                } else {
                    console.warn('Treatment ID not found in options:', treatId);
                }

                // Fill other fields
                $('#discount').val(offerData.discount || '');
                $('#start_date').val(offerData.start_date || '');
                $('#end_date').val(offerData.end_date || '');

                if (offerData.image) {
                    $('#offer_image').attr('src', offerData.image).show();
                } else {
                    $('#offer_image').hide();
                }
            }
            $('#custom_offer_div').hide();
        } else {
            
            $('#custom_offer_div').show();
            $('#custom_offer_type').val('');
        }
    });

    $('#start_date').on('change', function () {
        let startDate = new Date($(this).val());

        if (!isNaN(startDate.getTime())) {
            startDate.setDate(startDate.getDate() + 1);

            let minEndDate = startDate.toISOString().split('T')[0];

            $('#end_date').attr('min', minEndDate);

            let currentEndDate = $('#end_date').val();
            if (currentEndDate && currentEndDate < minEndDate) {
                $('#end_date').val('');
                alert('End date must be at least one day after Start date.');
            }
        }
    });

    $('#start_date_edit').on('change', function () {
        let startDate = new Date($(this).val());

        if (!isNaN(startDate.getTime())) {
            // Add 1 day to start date
            startDate.setDate(startDate.getDate() + 1);

            // Format to yyyy-mm-dd
            let minEndDate = startDate.toISOString().split('T')[0];

            // Set min for end_date_edit
            $('#end_date_edit').attr('min', minEndDate);

            // Validate if current end date is invalid
            let currentEndDate = $('#end_date_edit').val();
            if (currentEndDate && currentEndDate < minEndDate) {
                $('#end_date_edit').val('');
                alert('End date must be at least 1 day after the Start date.');
            }
        }
    });
});

</script>


@endsection
@endsection