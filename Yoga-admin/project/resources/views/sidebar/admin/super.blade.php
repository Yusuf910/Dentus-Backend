<li class="@if(Request::segment(3) === 'user_list')
   {{ 'active menu-open' }}
   @endif">
   <a href="{{ route('admin.user_list') }}">
   <i class="mdi mdi-account-multiple"></i>
   <span data-key="t-horizontal">Manage User</span>
   </a>
</li>

<li class="@if(Request::segment(3) === 'coach_list')
   {{ 'active menu-open' }}
   @endif">
   <a href="{{ route('admin.coach_list') }}">
   <i class="mdi mdi-whistle"></i>
   <span data-key="t-horizontal">Manage Coach</span>
   </a>
</li>

<li>
   <a href="javascript:void(0);" 
      class="has-arrow {{ (Request::segment(2) === 'faq' || Request::segment(2) === 'admins') ? 'mm-active' : '' }}">
   <i class="mdi mdi-database-cog-outline"></i>
   <span data-key="t-apps">Master</span>
   </a>
   <ul class="sub-menu" aria-expanded="false">
      <li class="{{ Request::segment(3) === 'yoga_categories' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.yoga_categories') }}">
         <i class="mdi mdi-shape-outline"></i>
         <span data-key="t-calendar">Categories</span>
         </a>
      </li>
      <li class="{{ Request::segment(3) === 'yoga_poses' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.yoga_poses') }}">
         <i class="mdi mdi-human-handsup"></i>
         <span data-key="t-calendar">Yoga Poses</span>
         </a>
      </li>
      <li class="{{ Request::segment(3) === 'yoga_pose_levels' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.yoga_pose_levels') }}">
         <i class="mdi mdi-ladder"></i>
         <span data-key="t-calendar">Yoga Poses Level</span>
         </a>
      </li>
      <li class="{{ Request::segment(3) === 'practice_routines' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.practice_routines') }}">
         <i class="mdi mdi-repeat"></i>
         <span data-key="t-calendar">Practice Routines</span>
         </a>
      </li>
      <li class="{{ Request::segment(3) === 'practice_yoga_coach_file' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.practice_yoga_coach_file') }}">
         <i class="mdi mdi-yoga"></i>
         <span data-key="t-calendar">Practice Coach Files </span>
         </a>
      </li>
      <li class="{{ Request::segment(3) === 'video_zip_files' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.video_zip_files') }}">
         <i class="mdi mdi-zip-box"></i>
         <span data-key="t-calendar">Callibration </span>
         </a>
      </li>
      <li class="{{ Request::segment(3) === 'faq' ? 'active menu-open' : '' }}">
         <a href="{{ route('admin.faq') }}">
         <i class="mdi mdi-comment-question-outline"></i>
         <span data-key="t-calendar">FAQ</span>
         </a>
      </li>
   </ul>
</li>

<li class="treeview
   @if(Request::segment(2) === 'package_users')
   {{ 'active menu-open' }}
   @endif">
   <a href="{{ route('admin.package_users') }}">
   <i class="mdi mdi-gift-outline"></i>
   <span data-key="t-horizontal">Package</span>
   </a>
</li>

<li class="treeview
   @if(Request::segment(2) === 'purchase_history')
   {{ 'active menu-open' }}
   @endif">
   <a href="{{ route('admin.purchase_history') }}">
   <i class="mdi mdi-history"></i>
   <span data-key="t-horizontal">Purchase History</span>
   </a>
</li>

<li>
   <a href="javascript: void(0);" class="has-arrow">
   <i class="mdi mdi-cog-outline"></i>
   <span data-key="t-apps">Settings</span>
   </a>
   <ul class="sub-menu" aria-expanded="false">
      <li class="@if(Request::segment(2) === 'settings2')
         {{ 'active menu-open' }}@endif" >
         <a href="{{ route('admin.settings2',1) }}">
         <i class="mdi mdi-tune"></i>
         Content Setting
         </a>
      </li>
   </ul>
</li>
