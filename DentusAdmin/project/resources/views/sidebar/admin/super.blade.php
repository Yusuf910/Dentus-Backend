{{-- 
<li>
   <a href="javascript: void(0);" class="has-arrow
      @if(Request::segment(2) === 'stafflist' || Request::segment(2) === 'admins')
      {{ 'mm-active' }}
      @elseif(Request::segment(2) === 'roles')
      {{ 'mm-active' }}
      @endif">
   <i class="mdi mdi-view-dashboard"></i>
   <span data-key="t-apps">Admin Management</span>
   </a>
   <ul class="sub-menu" aria-expanded="false">
      <li @if(Request::segment(2)==='stafflist' ) {{ 'mm-active' }} @endif>
      <a href="{{ route('admin.stafflist') }}">
      <span data-key="t-calendar">Staff</span>
      </a>
      </li>
      <li @if(Request::segment(2)==='roles' ) {{ 'mm-active' }} @endif>
      <a href="{{ route('admin.roles.index') }}">
      <span data-key="t-calendar">Roles</span>
      </a>
      </li>
      <!-- <li class="@if(Request::segment(2) === 'permissions')
         {{ 'active menu-open' }}
         @endif">
         <a href="{{ route('admin.permissions.index') }}"><i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Permissions</a>
         </li>  -->
   </ul>
</li>
--}}
{{-- 
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-02.svg" alt></span> <span> Doctors </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li class="@if(Request::segment(3) === 'doctor_list')
         {{ 'active' }}
         @endif">
         <a href="{{ route('admin.doctor_list',3) }}">
         <span data-key="t-calendar">Pending Doctor's</span>
         </a>
      </li>
      <li class="@if(Request::segment(3) === 'doctor_list')
         {{ 'active' }}
         @endif">
         <a href="{{ route('admin.doctor_list',1) }}">
         <span data-key="t-calendar">Verified Doctor's</span>
         </a>
      </li>
      <li class="@if(Request::segment(3) === 'doctor_list')
         {{ 'active' }}
         @endif">
         <a href="{{ route('admin.doctor_list',2) }}">
         <span data-key="t-calendar">Unverified Doctor's</span>
         </a>
      </li>
   </ul>
</li>
--}}
<li class="submenu">
   <a href="#">
      <span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-02.svg" alt></span>
      <span>Doctors</span>
      <span class="menu-arrow"></span>
   </a>
   <ul style="display: none;">
      <li>
         <a href="{{ route('admin.user.mystaff', 3) }}"
            class="{{ Request::segment(3) === 'my-staff' && Request::segment(4) == 3 ? 'active menu-open' : '' }}">
            Pending Doctors
         </a>
      </li>
      <li>
         <a href="{{ route('admin.user.mystaff', 1) }}"
            class="{{ Request::segment(3) === 'my-staff' && Request::segment(4) == 1 ? 'active menu-open' : '' }}">
            Verified Doctors
         </a>
      </li>
      <li>
         <a href="{{ route('admin.user.mystaff', 2) }}"
            class="{{ Request::segment(3) === 'my-staff' && Request::segment(4) == 2 ? 'active menu-open' : '' }}">
            Unverified Doctors
         </a>
      </li>
      <li>
       <a href="{{ route('admin.user.mystaff', 0) }}"
          class="{{ Request::segment(3) === 'my-staff' && Request::segment(4) == 0 ? 'active menu-open' : '' }}">
          Deactivated Doctors
       </a>
    </li>
      <li>
         <a href="{{ route('admin.user.addmystaff') }}"
            class="{{ Request::segment(3) === 'addmy-staff' ? 'active menu-open' : '' }}">
            Add Doctor
         </a>
      </li>
   </ul>
</li>
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-03.svg" alt></span> <span>Patients </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li><a href="{{ route('admin.myuser.myuserlist') }}" class="@if(Request::segment(3) === 'my-user' || Request::segment(3) === 'create_myuser' || Request::segment(3) === 'patients-profile')
         {{ 'active menu-open' }}
         @endif">Patients List</a></li>
   </ul>
</li>
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{asset('adminassets')}}/img/icons/menu-icon-04.svg" alt></span> <span> Appointments </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      {{-- 
      <li><a href="{{ route('admin.appoint.mycalender') }}" class="@if(Request::segment(3) === 'my-calender' )
         {{ 'active menu-open' }}
         @endif">Calendar</a></li>
      --}}
      <li><a href="{{ route('admin.appoint.myappointment') }}" class="@if(Request::segment(3) === 'my-appointment' || Request::segment(3) === 'my-appointmentdetails' )
         {{ 'active menu-open' }}
         @endif">Appointment List</a></li>
         <li><a href="{{ route('admin.appoint.cancelmyappointment') }}" class="@if(Request::segment(3) === 'my-appointment' || Request::segment(3) === 'my-cancelmyappointment' )
         {{ 'active menu-open' }}
         @endif">Cancel Appointment List</a></li>
      {{-- 
      <li><a href="{{ route('admin.appoint.addappointment') }}" class="@if(Request::segment(3) === 'addappointment' )
         {{ 'active menu-open' }}
         @endif">Book Appointment</a></li>
      --}}
   </ul>
</li>

<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-04.svg" alt></span> <span> Blog </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li class="@if(Request::segment(3) === 'blog' || Request::segment(3) === 'create_blog' || Request::segment(3) === 'edit_blog' || Request::segment(3) === 'import_blog')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.master.blog') }}">
         <span data-key="t-calendar">Blog</span>
         </a>
      </li>
   </ul>
</li>

<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-02.svg" alt></span> <span> Seasonal Offers </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">

       <li class="@if(Request::segment(3) === 'seasonal_offer')
       {{ 'mm-active' }}
       @endif">
          <a href="{{ route('admin.master.seasonal_offer') }}">
             <span data-key="t-calendar">Seasonal Offers</span>
          </a>
       </li>

   </ul>
</li>

<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-02.svg" alt></span> <span> Package </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">

       <li class="@if(Request::segment(3) === 'packagelist')
       {{ 'active menu-open' }}
       @endif">
          <a href="{{ route('admin.package.packagelist') }}">
             <span data-key="t-calendar">Package List</span>
          </a>
       </li>

       <li class="@if(Request::segment(3) === 'subscriptionlist')
       {{ 'active menu-open' }}
       @endif">
          <a href="{{ route('admin.subscription.subscriptionlist') }}">
             <span data-key="t-calendar">User Subscription</span>
          </a>
       </li>

   </ul>
</li>


<li>
   <a href="{{ route('admin.appoint.mystats') }}" class="@if(Request::segment(3) === 'my-stats')
                  {{ 'active menu-open' }}
                 @endif"><span class="menu-side"><img src="{{asset('content/admin')}}/img/icons/menu-icon-16.svg" alt></span> <span>Earning & Statistics</span></a>
</li>

<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-02.svg" alt></span> <span> Master </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li>
         <a href="{{ route('admin.master.master_college_institutes') }}" 
         class="@if(Request::segment(3) === 'master_college_institutes')
        {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">College Institutes</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.master_degrees') }}"
         class="@if(Request::segment(3) === 'master_degrees')
        {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Degrees</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.masterlanguage') }}"
         class="@if(Request::segment(3) === 'masterlanguage')
        {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Language</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.master_registraion_councils') }}"
         class="@if(Request::segment(3) === 'master_registraion_councils')
         {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">RegistrationCouncil</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.master_services') }}" 
         class="@if(Request::segment(3) === 'master_services')
         {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Services</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.master_specialsations') }}" 
         class="@if(Request::segment(3) === 'master_specialsations')
         {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Specialisations</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.mastertax') }}" 
         class="@if(Request::segment(3) === 'mastertax')
         {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Tax</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.master_templates') }}"
         class="@if(Request::segment(3) === 'master_templates')
         {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Template</span>
         </a>
      </li>
      <li >
         <a href="{{ route('admin.master.master_drugs') }}"
         class="@if(Request::segment(3) === 'master_drugs')
         {{ 'active menu-open' }}
         @endif">
         <span data-key="t-calendar">Drugs</span>
         </a>
      </li>
   </ul>
</li>
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-02.svg" alt></span> <span> Notification </span> <span class="menu-arrow"></span></a>
   <ul style="display: none;">
      <li class="@if(Request::segment(3) === 'notification')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.notification.send_notification_to_user') }}">
         <span data-key="t-calendar">User</span>
         </a>
      </li>
      {{-- 
      <li class="@if(Request::segment(3) === 'notification')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.notification.send_notification_to_astrologer') }}">
         <span data-key="t-calendar">Doctor</span>
         </a>
      </li>
      --}}
      <li class="@if(Request::segment(3) === 'notification')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.notification.send_notification_to_all') }}">
         <span data-key="t-calendar">All</span>
         </a>
      </li>
   </ul>
</li>
<li class="submenu">
   <a href="#"><span class="menu-side"><img src="{{ asset('adminassets') }}/img/icons/menu-icon-16.svg" alt></span> <span>Settings</span><span class="menu-arrow"></span></a>
   <ul class="sub-menu" aria-expanded="false">
      <li class="@if(Request::segment(2) === 'settings')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.settings.edit',1) }}">
         <span data-key="t-calendar"> Settings</span>
         </a>
      </li>
      <li class="@if(Request::segment(2) === 'settings2')
         {{ 'mm-active' }}
         @endif">
         <a href="{{ route('admin.settings2',2) }}">
         <span data-key="t-calendar">Content Settings</span>
         </a>
      </li>
   </ul>
</li>