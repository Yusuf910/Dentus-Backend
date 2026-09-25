@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="index.html">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Dashboard</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="good-morning-blk">
         <div class="row">
            <div class="col-md-6">
               <div class="morning-user">
                  <h2>Hello, <span>Dr. {{ Auth()->guard('admin')->user()->name }}!</span></h2>
                  
               </div>
            </div>
            <div class="col-md-6 position-blk">
               <div class="morning-user-1 text-end">
                  
                  <!-- <button type="button" class="btn btn-secondary btn-lg py-3">Complete Profile</button> -->
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/calendar.svg" alt>
               </div>
               <div class="dash-content dash-count">
                  <h4>Appointments</h4>
                  <h2><span class="counter-up">{{ $currentAppointments }}</span></h2>
                  <p>
                      <span class="passive-view">
                          @if($appointmentStatus == 'High')
                              <i class="feather-arrow-up-right me-1 text-success"></i>
                          @else
                              <i class="feather-arrow-down-right me-1 text-danger"></i>
                          @endif
                          {{ $appointmentChange }}
                      </span> vs last month
                  </p>

               </div>
            </div>
         </div>
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/profile-add.svg" alt>
               </div>
               <div class="dash-content dash-count">
                   <h4>New Patients</h4>
                   <h2><span class="counter-up">{{ $currentNewPatients }}</span></h2>
                   <p>
                       <span class="passive-view">
                           @if($newPatientsStatus == 'High')
                               <i class="feather-arrow-up-right me-1 text-success"></i>
                           @else
                               <i class="feather-arrow-down-right me-1 text-danger"></i>
                           @endif
                           {{ $newPatientsChange }}
                       </span> vs last month
                   </p>
               </div>

            </div>
         </div>
        
         <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
            <div class="dash-widget">
               <div class="dash-boxs comman-flex-center">
                  <img src="{{asset('content/admin')}}/img/icons/empty-wallet.svg" alt>
               </div>
               <div class="dash-content dash-count">
                   <h4>Earnings</h4>
                   <h2>₹ <span class="counter-up">{{ number_format($currentEarnings, 2) }}</span></h2>
                   <p>
                       <span class="passive-view">
                           @if($earningsStatus == 'High')
                               <i class="feather-arrow-up-right me-1 text-success"></i>
                           @else
                               <i class="feather-arrow-down-right me-1 text-danger"></i>
                           @endif
                           {{ $earningsChange }}
                       </span> vs last month
                   </p>
               </div>

            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-12 col-xl-12">
            <div class="card">
               <div class="card-header pb-0">
                  <h4 class="card-title d-inline-block">Recent Patients </h4>
                  <a href="patients.html" class="float-end patient-views">Show all</a>
               </div>
               <div class="card-block table-dash">
                  <div class="table-responsive">
                     <table class="table mb-0 border-0 datatable custom-table">
                        <thead>
                           <tr>
                              <th>S.no</th>
                              <th>Patient name</th>
                              <th>Age</th>
                              <th>Treatment</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($recentBookings->count() > 0)
                           @foreach($recentBookings as $a)
                           <tr>
                              <td>{{$a->id}}</td>
                              <td class="table-image">
                                 <?php $member = App\Models\UserModels\Member::find('0'); ?>
                                 @if (
                                      ($a->user_id != $a->member_id && $a->member_type == 'relation') ||
                                      ($a->user_id == $a->member_id && $a->member_type == 'relation')
                                  )
                                  <?php $member = App\Models\UserModels\Member::find($a->member_id); ?>
                                    @if($member)
                                       <img width="28" height="28" class="rounded-circle" src="{{asset('project/public/member_images/' . $member->image)}}" alt>
                                       <h2>{{$member->name}}</h2>
                                    @endif
                                  @else
                                    <img width="28" height="28" class="rounded-circle" src="{{asset('content/user/' . $a->userdetail->image)}}" alt>
                                    <h2>{{$a->userdetail->name}}</h2>
                                 @endif
                              </td>
                              <td>
                                  @if (
                                      ($a->user_id != $a->member_id && $a->member_type == 'relation') ||
                                      ($a->user_id == $a->member_id && $a->member_type == 'relation')
                                  )
                                  {{$member->dob ? Carbon\Carbon::parse($member->dob)->age : 'N/A' }} @else
                                  {{ $a->userdetail->date_of_birth ? Carbon\Carbon::parse($a->userdetail->date_of_birth)->age : 'N/A'}}@endif
                              </td>
                              <td>Dental Implant</td>
                              <td class="text-end">
                                 <button type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button>
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
   <div class="notification-box">
      <div class="msg-sidebar notifications msg-noti">
         <div class="topnav-dropdown-header">
            <span>Messages</span>
         </div>
         <div class="drop-scroll msg-list-scroll" id="msg_list">
            <ul class="list-box">
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">R</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Richard Miles </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item new-message">
                        <div class="list-left">
                           <span class="avatar">J</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">John Doe</span>
                           <span class="message-time">1 Aug</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">T</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Tarah Shropshire </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">M</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Mike Litorus</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">C</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Catherine Manseau </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">D</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Domenic Houston </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">B</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Buster Wigton </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">R</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Rolland Webber </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">C</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author"> Claire Mapes </span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">M</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Melita Faucher</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">J</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Jeffery Lalor</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">L</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Loren Gatlin</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
               <li>
                  <a href="chat.html">
                     <div class="list-item">
                        <div class="list-left">
                           <span class="avatar">T</span>
                        </div>
                        <div class="list-body">
                           <span class="message-author">Tarah Shropshire</span>
                           <span class="message-time">12:28 AM</span>
                           <div class="clearfix"></div>
                           <span class="message-content">Lorem ipsum dolor sit amet, consectetur adipiscing</span>
                        </div>
                     </div>
                  </a>
               </li>
            </ul>
         </div>
         <div class="topnav-dropdown-footer">
            <a href="chat.html">See all messages</a>
         </div>
      </div>
   </div>
</div>
@endsection