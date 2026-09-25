@extends('layouts.admin')
@section('content')
<?php $data2 = App\Models\Treatment::whereNOTIN('status', [2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();?>
<style type="text/css">.error-text {
                              color: red;
                              font-size: 12px;
                              }</style>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item"><a href="{{route('admin.master.clinic')}}">Clinic </a></li>
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
                              <h3>Clinic {{$title}}</h3>
                              
                           </div>
                        </div>
                        
                     </div>
                  </div>
                  <div class="table-responsive">
                     <div class="tab-pane" id="bottom-tab6">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                           <div class="w-100">
                              <h3 class="card-title mb-0">Timings</h3>
                           </div>
                           <div class="flex-shrink-1"><a data-bs-toggle="modal" data-bs-target="#centermodal6" class="btn btn-primary">Edit</a></div>
                        </div>
                        <div class="experience-box">
                           <ul class="experience-list">
                              @if(isset($data->doctor_availability))
                                 
                                 @if(!is_null($data->doctor_availability))
                                 <?php $t = json_decode($data->doctor_availability, true); ?>
                                    @if($t)
                                       @foreach ($t['schedule'] as $day => $times)
                                       <?php //dd($times); ?>
                                       <li>
                                          <div class="experience-user">
                                             <div class="before-circle"></div>
                                          </div>
                                          <div class="experience-content">
                                             <div class="timeline-content">
                                                <a href="#/" class="name">{{ ucfirst($day) }}</a>
                                                <br>
                                                   @if (!empty($times['morning']))
                                                   <span class="time-box">{{ $times['morning'][0] }}</span>
                                                   @endif
                                                   @if (!empty($times['evening']))
                                                   <span class="time-box">{{ $times['evening'][0] }}</span>
                                                   @endif
                                             </div>
                                          </div>
                                       </li>
                                       @endforeach
                                    @endif
                                 @elseif (!empty($c->timings))
                                   <?php $t = $availability = json_decode($c->timings, true) ?? []; ?>
                                   
                                    @if($t)
                                       @foreach ($t['schedule'] as $day => $times)
                                       <?php //dd($times); ?>
                                       <li>
                                          <div class="experience-user">
                                             <div class="before-circle"></div>
                                          </div>
                                          <div class="experience-content">
                                             <div class="timeline-content">
                                                <a href="#/" class="name">{{ ucfirst($day) }}</a>
                                                <br>
                                                   @if (!empty($times['morning']))
                                                   <span class="time-box">{{ $times['morning'][0] }}</span>
                                                   @endif
                                                   @if (!empty($times['evening']))
                                                   <span class="time-box">{{ $times['evening'][0] }}</span>
                                                   @endif
                                             </div>
                                          </div>
                                       </li>
                                       @endforeach
                                    @endif
                               
                                 @endif
                              @elseif (!empty($check->doctor_availability))
                                <?php $t = $availability = json_decode($check->doctor_availability, true) ?? []; ?>
                                
                                 @if($t)
                                    @foreach ($t['schedule'] as $day => $times)
                                    <?php //dd($times); ?>
                                    <li>
                                       <div class="experience-user">
                                          <div class="before-circle"></div>
                                       </div>
                                       <div class="experience-content">
                                          <div class="timeline-content">
                                             <a href="#/" class="name">{{ ucfirst($day) }}</a>
                                             <br>
                                                @if (!empty($times['morning']))
                                                <span class="time-box">{{ $times['morning'][0] }}</span>
                                                @endif
                                                @if (!empty($times['evening']))
                                                <span class="time-box">{{ $times['evening'][0] }}</span>
                                                @endif
                                          </div>
                                       </div>
                                    </li>
                                    @endforeach
                                 @endif
                            
                              
                              @endif
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="centermodal6" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Timing</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">

         @php
           $availability = [];

             if (!empty($data->doctor_availability)) {
                 $availability = json_decode($data->doctor_availability, true) ?? [];
             }
             elseif (!empty($check->doctor_availability)) {
                 $availability = json_decode($check->doctor_availability, true) ?? [];

             }

       @endphp
          <form method="POST" action="{{ route('admin.master.timingupdateclininc',$id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
               <div class="col-md-12">
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    <div class="mb-3 day-schedule" data-day="{{ $day }}">
                       <h5 class="text-capitalize">{{ ucfirst($day) }}</h5>
                       
                       <!-- Morning Slot -->
                       <label for="{{ $day }}_morning_start">Morning:</label>
                       <div class="d-flex">
                           @php
                               $morning = isset($availability['schedule'][$day]['morning'][0]) ? explode(' - ', $availability['schedule'][$day]['morning'][0]) : ['', ''];
                           @endphp
                           <input type="time" class="form-control me-2 morning-time" name="schedule[{{ $day }}][morning_start]" value="{{ date('H:i', strtotime($morning[0])) }}">
                           <input type="time" class="form-control morning-time" name="schedule[{{ $day }}][morning_end]" value="{{ date('H:i', strtotime($morning[1])) }}">
                       </div>

                       <!-- Evening Slot -->
                       <label for="{{ $day }}_evening_start" class="mt-2">Evening:</label>
                       <div class="d-flex">
                           @php
                               $evening = isset($availability['schedule'][$day]['evening'][0]) ? explode(' - ', $availability['schedule'][$day]['evening'][0]) : ['', ''];
                           @endphp
                           <input type="time" class="form-control me-2 evening-time" name="schedule[{{ $day }}][evening_start]" value="{{ date('H:i', strtotime($evening[0])) }}">
                           <input type="time" class="form-control evening-time" name="schedule[{{ $day }}][evening_end]" value="{{ date('H:i', strtotime($evening[1])) }}">
                       </div>
                       <div class="form-check fs-6 mt-1">
                           <input class="form-check-input full-half-toggle" name="schedule[{{ $day }}][_isHalfDay]" type="radio" id="{{ $day }}_isHalfDay">
                           <label class="form-check-label" for="{{ $day }}_isHalfDay">Close for Half Day</label>
                        </div>
                        <span class="error-text">*For half day, please select either morning or evening timings.</span>
                        <div class="ms-3 mb-2 halfday-options d-none">
                           <div class="form-check">
                               <input class="form-check-input halfday-time-toggle" type="radio" name="schedule[{{ $day }}][_halfDaySlot]" value="morning" id="{{ $day }}_halfMorning">
                               <label class="form-check-label" for="{{ $day }}_halfMorning">Close Morning</label>
                           </div>
                           <div class="form-check">
                               <input class="form-check-input halfday-time-toggle" type="radio" name="schedule[{{ $day }}][_halfDaySlot]" value="evening" id="{{ $day }}_halfEvening">
                               <label class="form-check-label" for="{{ $day }}_halfEvening">Close Evening</label>
                           </div>
                       </div>

                        <div class="form-check fs-6">
                           <input class="form-check-input full-half-toggle" name="schedule[{{ $day }}][_isFullDay]" type="radio" id="{{ $day }}_isFullDay">
                           <label class="form-check-label" for="{{ $day }}_isFullDay">Close for Full Day</label>
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
@section('js_user_page')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	function sendid(id) {
	    console.log("User ID:", id); // Debugging output
	    $('#user_id').val(id);
	    
	};



</script>
<script>
    $(document).ready(function () {
       $('.full-half-toggle').on('change', function () {
           let container = $(this).closest('.day-schedule');
           let isHalfDay = container.find('input[name$="[_isHalfDay]"]:checked').length > 0;
           let isFullDay = container.find('input[name$="[_isFullDay]"]:checked').length > 0;

           if ($(this).attr('name').includes('_isFullDay')) {
               // If Full Day is selected, uncheck Half Day and its sub-options
               container.find('input[name$="[_isHalfDay]"]').prop('checked', false);
               container.find('.halfday-options').addClass('d-none');
               container.find('.halfday-time-toggle').prop('checked', false);
           } else if ($(this).attr('name').includes('_isHalfDay')) {
               // If Half Day is selected, uncheck Full Day
               container.find('input[name$="[_isFullDay]"]').prop('checked', false);
               container.find('.halfday-options').removeClass('d-none');
           }

           // Enable/Disable time inputs based on the state
           if (isFullDay) {
               container.find('.morning-time, .evening-time').prop('disabled', true);
           } else {
               container.find('.morning-time, .evening-time').prop('disabled', false);
           }
       });

       $('.halfday-time-toggle').on('change', function () {
           let container = $(this).closest('.day-schedule');
           let selected = $(this).val();

           if (selected === 'morning') {
               container.find('.morning-time').prop('disabled', true);
               container.find('.evening-time').prop('disabled', false);
           } else if (selected === 'evening') {
               container.find('.evening-time').prop('disabled', true);
               container.find('.morning-time').prop('disabled', false);
           }
       });
   });

</script>

@endsection
@endsection