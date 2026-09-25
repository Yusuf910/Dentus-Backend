@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
    <div class="content">
       <div class="page-header">
          <div class="row">
             <div class="col-sm-12">
                <ul class="breadcrumb">
                   <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard </a></li>
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
                   <h2>Hello, <span>{{ auth()->user()->name }}</span></h2>
                   <p>We’re glad you’ve taken the first step towards creating your profile. Please provide a few details so we can set up your account.</p>
                </div>
             </div>
             <div class="col-md-6 position-blk">
                <div class="morning-user-1 text-end">
                   {{-- <a href="subscription-pack.html" class="btn btn-primary btn-lg py-3">Buy Subscription</a> --}}
                   <!-- <button type="button" class="btn btn-secondary btn-lg py-3">Complete Profile</button> -->
                </div>
             </div>
          </div>
       </div>
        <div class="row">
          <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
             <div class="dash-widget">
                <div class="dash-boxs comman-flex-center">
                   <img src="{{ asset('adminassets') }}/img/icons/calendar.svg" alt>
                </div>
                <div class="dash-content dash-count">
                   <h4>Appointments</h4>
                   <h2><span class="counter-up">{{ $booking }}</span></h2>
                   {{-- <p><span class="passive-view"><i class="feather-arrow-up-right me-1"></i>40%</span> vs last month</p> --}}
                </div>
             </div>
          </div>
          <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
             <div class="dash-widget">
                <div class="dash-boxs comman-flex-center">
                   <img src="{{ asset('adminassets') }}/img/icons/profile-add.svg" alt>
                </div>
                <div class="dash-content dash-count">
                   <h4>Total Patients</h4>
                   <h2><span class="counter-up">{{ $user }}</span></h2>
                   {{-- <p><span class="passive-view"><i class="feather-arrow-up-right me-1"></i>20%</span> vs last month</p> --}}
                </div>
             </div>
          </div>
          <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
             <div class="dash-widget">
                <div class="dash-boxs comman-flex-center">
                   <img src="{{ asset('adminassets') }}/img/icons/calendar.svg" alt>
                </div>
                <div class="dash-content dash-count">
                   <h4>Total Doctors</h4>
                   <h2><span class="counter-up">{{ $doctor }}</span></h2>
                   {{-- <p><span class="passive-view"><i class="feather-arrow-up-right me-1"></i>40%</span> vs last month</p> --}}
                </div>
             </div>
          </div>
          {{--<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
             <div class="dash-widget">
                <div class="dash-boxs comman-flex-center">
                   <img src="{{ asset('adminassets') }}/img/icons/empty-wallet.svg" alt>
                </div>
                <div class="dash-content dash-count">
                   <h4>Earnings</h4>
                   <h2>₹ <span class="counter-up"> 20,250</span></h2>
                   <p><span class="passive-view"><i class="feather-arrow-up-right me-1"></i>30%</span> vs last month</p>
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
                            <tr>
                               <td>1</td>
                               <td class="table-image">
                                  <img width="28" height="28" class="rounded-circle" src="{{ asset('adminassets') }}/img/profiles/avatar-02.jpg" alt>
                                  <h2>Andrea Lalema</h2>
                               </td>
                               <td>21yrs</td>
                               <td>Dental Implant</td>
                               <td class="text-end">
                                  <button type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button>
                               </td>
                            </tr>
                            <tr>
                               <td>2</td>
                               <td class="table-image">
                                  <img width="28" height="28" class="rounded-circle" src="{{ asset('adminassets') }}/img/profiles/avatar-02.jpg" alt>
                                  <h2>Andrea Lalema</h2>
                               </td>
                               <td>21yrs</td>
                               <td>Dental Implant</td>
                               <td class="text-end">
                                  <button type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button>
                               </td>
                            </tr>
                            <tr>
                               <td>3</td>
                               <td class="table-image">
                                  <img width="28" height="28" class="rounded-circle" src="{{ asset('adminassets') }}/img/profiles/avatar-02.jpg" alt>
                                  <h2>Andrea Lalema</h2>
                               </td>
                               <td>21yrs</td>
                               <td>Dental Implant</td>
                               <td class="text-end">
                                  <button type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button>
                               </td>
                            </tr>
                         </tbody>
                      </table>
                   </div>
                </div>
             </div>
          </div>--}}
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




   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               <script>
                  document.write(new Date().getFullYear())
               </script> © Astrolight.
            </div>
            <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
         </div>
      </div>
   </footer>
</div>
@endsection
