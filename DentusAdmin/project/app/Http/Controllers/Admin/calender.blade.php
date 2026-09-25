@extends('layouts.admin')
@section('styles')

    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <style>
        /* Custom Calendar Styling */
        #calendar {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
@section('content')
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
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            // Convert PHP array to JavaScript object
            var events = @json($events); 

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events, // Load events from PHP
                eventClick: function(info) {
                    window.location.href = info.event.url; // Redirect to appointment details
                    info.jsEvent.preventDefault();
                }
            });

            calendar.render();
        });
    </script>
@endsection
@endsection