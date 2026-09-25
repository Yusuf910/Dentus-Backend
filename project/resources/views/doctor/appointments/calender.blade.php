@extends('layouts.admin')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
 <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    

    <style>
        
       
     /*   .fc-event {
             height: 90px !important;
             font-size: 14px !important;
             padding: 5px !important;
         }
      .fc-timegrid-event-short .fc-event-main-frame{
         flex-direction: column;
      }*/

    </style>
@endsection
@section('content')
<?php 
$c1 = '';
$u1 = '';
if (isset($_GET['c1'])) 
{
   $c1 = $_GET['c1'];
}
if (isset($_GET['u1'])) 
{
   $u1 = $_GET['u1'];
}
?>
<div class="page-wrapper">
   <div class="content">
      <div class="row">
         <div class="col-sm-8 col-4">
            <ul class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{$listurl}}">Dashboard </a></li>
               <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
               <li class="breadcrumb-item active">Calendar</li>
            </ul>
         </div>
         <div class="col-sm-4 col-8 text-end m-b-30">
         </div>
      </div>
      <div class="row">
         <div class="col-lg-12">
            <div class="card-box mb-0">
               <div class="row">
                   <div class="row table-search-blk">
                     <form method="GET" action="{{ route('admin.appoint.mycalender') }}">
                        <div class="row">
                           <!-- <div class="col-md-2">
                               <div class="mb-3">
                               <label for="treatment_name" class="form-label">Clinic</label>
                               <select name="c1" class="form-control">
                                 <option value="">Select</option>
                                 @if($c->count() > 0)
                                 @foreach($c as $ab)
                                    <option {{$c1 == $ab->id ? 'selected' : ''}} value="{{$ab->id}}">{{$ab->clinic_name}}</option>
                                 @endforeach
                                 @endif
                                 </select>
                               </div>
                           </div>  --> 
                           @if(auth()->user()->parent_id == 0)
                           <div class="col-md-2">
                               <div class="mb-3">
                               <label for="average_duration" class="form-label">Doctor</label>
                               <select name="u1" class="form-control">
                                 <option value="">Select</option>
                                 <option {{$u1 == auth()->user()->id ? 'selected' : ''}} value="{{auth()->user()->id}}">{{auth()->user()->name}}</option>
                                 @if($u->count() > 0)
                                 @foreach($u as $ab1)
                                    <option {{$u1 == $ab1->id ? 'selected' : ''}} value="{{$ab1->id}}">{{$ab1->name}}</option>
                                 @endforeach
                                 @endif
                                 </select>
                               </div>
                           </div> 
                           
                           <div class="col-md-2">
                               
                               <button type="submit" class="btn btn-success" alt="alert" id="sa-success">Search</button>
                               <a href="{{ route('admin.appoint.mycalender') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                           </div>
                           @endif
                        </div>
                     </form>
                  </div>
                  <div class="col-md-12">
                     <div id="calendar"></div>
                  </div>
               </div>
            </div>
            <div class="modal fade none-border" id="event-modal">
               <div class="modal-dialog">
                  <div class="modal-content modal-md">
                     <div class="modal-header">
                        <h4 class="modal-title">Add Event</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                     </div>
                     <div class="modal-body"></div>
                     <div class="modal-footer text-center">
                        <button type="button" class="btn btn-primary submit-btn save-event">Create event</button>
                        <button type="button" class="btn btn-danger btn-lg delete-event" data-bs-dismiss="modal">Delete</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div id="add_event" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content modal-md">
         <div class="modal-header">
            <h4 class="modal-title">Add Event</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div>
         <div class="modal-body">
            <form>
               <div class="input-block">
                  <label>Event Name <span class="text-danger">*</span></label>
                  <input class="form-control" type="text">
               </div>
               <div class="input-block">
                  <label>Event Date <span class="text-danger">*</span></label>
                  <div class="cal-icon">
                     <input class="form-control datetimepicker" type="text">
                  </div>
               </div>
               <div class="m-t-20 text-center">
                  <button class="btn btn-primary submit-btn">Create Event</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Convert PHP array to JavaScript object
            var events = @json($events); 
            console.log(events);
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotMinTime: "07:00:00",
                slotMaxTime: "23:00:00",
                slotDuration: '00:10:00',
                allDaySlot: false,
                events: events,
                nowIndicator: true,
                eventClick: function(info) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            });

            calendar.render();
        });
    </script>
@endsection
@endsection