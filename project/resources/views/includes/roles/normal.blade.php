@if(auth()->user()->status == 1)
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-04.svg" alt></span> <span> Appointments </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.appoint.myappointment') }}" class="@if(Request::segment(3) === 'my-appointment' || Request::segment(3) === 'my-appointmentdetails' )
                  {{ 'active menu-open' }}
                 @endif">Appointment List</a></li>
      <li><a href="{{ route('admin.appoint.myappointmentpending') }}" class="@if(Request::segment(3) === 'my-appointmentpending' )
                  {{ 'active menu-open' }}
                 @endif">Pendings</a></li>
      <li><a href="{{ route('admin.appoint.addappointment') }}" class="@if(Request::segment(3) === 'add-appointment' )
                  {{ 'active menu-open' }}
                 @endif">Book Appointment</a></li>
   </ul>
</li>
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-04.svg" alt></span> <span> Calendar </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.appoint.mycalender') }}" class="@if(Request::segment(3) === 'my-calender' )
                  {{ 'active menu-open' }}
                 @endif">Calendar</a></li>
   </ul>
</li>
<li>
   <a href="{{ route('admin.appoint.mystats') }}" class="@if(Request::segment(3) === 'my-stats')
                  {{ 'active menu-open' }}
                 @endif"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-16.svg" alt></span> <span>Earning & Statistics</span></a>
</li>
@endif