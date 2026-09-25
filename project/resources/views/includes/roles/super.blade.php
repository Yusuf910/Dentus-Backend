<?php $allowedMenus = collect(auth()->user()->activepackage())->pluck('id')->toArray(); ?>
@if(auth()->user()->status == 1)
@if($allowedMenus)
   <li class="submenu {{ Request::is('doctors*') ? 'active menu-open' : '' }}">
       <a href="#">
           <span class="menu-side"><img src="{{ asset('content/admin/img/icons/menu-icon-02.svg') }}" alt></span>
           <span> Doctors </span> <span class="menu-arrow"></span>
       </a>
       <ul style="display: {{ Request::is('doctors*') ? 'block' : 'none' }};">
           @if(in_array(5, $allowedMenus))
            <li><a href="{{ route('admin.user.myqr') }}" class="@if(Request::segment(3) === 'myqr' ) {{ 'active menu-open' }} @endif">My QR </a></li>
           @endif
           @if(in_array(12, $allowedMenus))
            <li><a href="{{ route('admin.user.mystaff') }}" class="@if(Request::segment(3) === 'my-staff' || Request::segment(3) === 'my-staff-detail')
                     {{ 'active menu-open' }}
                    @endif">Doctor List</a></li>
           <li><a href="{{ route('admin.user.addmystaff') }}" class="@if(Request::segment(3) === 'addmy-staff')
                     {{ 'active menu-open' }}
                    @endif">Add Team</a></li>
            @endif        
           <!-- <li><a href="{{ url('app-template') }}" class="{{ Request::is('app-template') ? 'active' : '' }}">App Template</a></li> -->
           <!-- <li><a href="{{ url('patient-payment') }}" class="{{ Request::is('patient-payment') ? 'active' : '' }}">Patient’s Payment</a></li> -->
       </ul>
   </li>
   
@if(in_array(18, $allowedMenus))   
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-03.svg" alt></span> <span>Patients </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.myuser.myuserlist') }}" class="@if(Request::segment(3) === 'my-user' || Request::segment(3) === 'create_myuser' || Request::segment(3) === 'patients-profile')
                  {{ 'active menu-open' }}
                 @endif">Patients List</a></li>
   </ul>
</li>
@endif
@if(in_array(8, $allowedMenus))   
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-03.svg" alt></span> <span>Feedback </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.myuser.feedback') }}" class="@if(Request::segment(3) === 'feedback')
                  {{ 'active menu-open' }}
                 @endif">Feedback</a></li>
   </ul>
</li>
@endif
@if(in_array(17, $allowedMenus))   
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-03.svg" alt></span> <span>Seasonal Offer </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.myuser.seasonaloffer') }}" class="@if(Request::segment(3) === 'my-seasonaloffer')
                  {{ 'active menu-open' }}
                 @endif">Seasonal Offer</a></li>
   </ul>
</li>
@endif
@if(in_array(2, $allowedMenus))   
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
@endif
@if(in_array(2, $allowedMenus)) 
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-04.svg" alt></span> <span> Calendar </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.appoint.mycalender') }}" class="@if(Request::segment(3) === 'my-calender' )
                  {{ 'active menu-open' }}
                 @endif">Calendar</a></li>
   </ul>
</li>
@endif
@if(in_array(19, $allowedMenus)) 
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-04.svg" alt></span> <span> Treatments </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.master.treatment') }}" class="@if(Request::segment(3) === 'treatment' )
                  {{ 'active menu-open' }}
                 @endif">Treatments List</a></li>
      <!-- <li><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal" href="#">Add Treatments</a></li> -->
   </ul>
</li>
@endif
@if(in_array(16, $allowedMenus)) 
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-04.svg" alt></span> <span> Patients’ Plans </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.master.packtreatment') }}" class="@if(Request::segment(3) === 'packtreatment' )
                  {{ 'active menu-open' }}
                 @endif">Plan List</a></li>
      <!-- <li><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal">Add Plan</a></li> -->
   </ul>
</li>
@endif
@if(in_array(13, $allowedMenus)) 
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-04.svg" alt></span> <span> Clinic </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.master.clinic') }}" class="@if(Request::segment(3) === 'clinic' ) {{ 'active menu-open' }} @endif">Clinic List</a></li>
      <li><a href="{{ route('admin.master.create_clinic') }}">Add Clinic</a></li>
      
   </ul>
</li>
@endif
@if(in_array(15, $allowedMenus)) 
<li>
   <a href="{{ route('admin.appoint.mystats') }}" class="@if(Request::segment(3) === 'my-stats')
                  {{ 'active menu-open' }}
                 @endif"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-16.svg" alt></span> <span>Earning & Statistics</span></a>
</li>
@endif
@if(in_array(14, $allowedMenus))
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-13.svg" alt></span> <span> Social Connect</span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.master.blog') }}" class="@if(Request::segment(3) === 'blog' ) {{ 'active menu-open' }} @endif">Social Connect</a></li>
      <!-- <li><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal">Add Social Connect</a></li> -->
   </ul>
</li>
@endif
@if(in_array(11, $allowedMenus))
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-13.svg" alt></span> <span> Notification </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li class="@if(Request::segment(3) === 'notification')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.notification.send_notification_to_user') }}">
         <span data-key="t-calendar">User</span>
         </a>
      </li>
      
      {{-- <li class="@if(Request::segment(3) === 'notification')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.notification.send_notification_to_astrologer') }}">
         <span data-key="t-calendar">Doctor</span>
         </a>
      </li> --}}
     
      <li class="@if(Request::segment(3) === 'notification')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.notification.send_notification_to_all') }}">
         <span data-key="t-calendar">All</span>
         </a>
      </li>
   </ul>
</li>
@endif
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-13.svg" alt></span> <span> Settings</span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.settings3') }}" class="@if(Request::segment(3) === 'settings3' ) {{ 'active menu-open' }} @endif">Patients Payments</a></li>
      <li><a href="{{ route('admin.settings4','about_us') }}" class="@if(Request::segment(3) === 'about_us' ) {{ 'active menu-open' }} @endif">About Us</a></li>
      <li><a href="{{ route('admin.settings4','contact_us') }}" class="@if(Request::segment(3) === 'contact_us' ) {{ 'active menu-open' }} @endif">Contact Us</a></li>
      <li><a href="{{ route('admin.settings4','privacy_policy') }}" class="@if(Request::segment(3) === 'privacy_policy' ) {{ 'active menu-open' }} @endif">Privacy Policy</a></li>
      <li><a href="{{ route('admin.settings4','terms_and_condition') }}" class="@if(Request::segment(3) === 'terms_and_condition' ) {{ 'active menu-open' }} @endif">Terms of use</a></li>
      @if(in_array(7, $allowedMenus))
      <li><a href="{{ route('admin.settings2') }}" class="@if(Request::segment(3) === 'settings2' ) {{ 'active menu-open' }} @endif">Loyality Points Settings</a></li>
      @endif
   </ul>
   
</li>
@endif
@endif