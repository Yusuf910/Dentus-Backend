@extends('layouts.admin')
@section('content')


<div class="page-wrapper">
   <div class="content">
      <div class="row">
         <div class="col-sm-12">
            <ul class="breadcrumb">
               <li class="breadcrumb-item"><a href="index.html">Doctors </a></li>
               <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
               <li class="breadcrumb-item active">Doctor Profile</li>
            </ul>
         </div>
      </div>
      <div class="card-box profile-header">
      	@include('includes.admin.form-success') 
      	@if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
             @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
             @endforeach
          </ul>
        </div>
      @endif
         <div class="row">
            <div class="col-md-12">
               <div class="profile-view">
                  <div class="profile-img-wrap">
                     <div class="profile-img">
                        <a href="#"><img class="avatar" src="{{asset('../content/doctor/'.$a->image)}}" alt></a>
                     </div>
                  </div>

                  <div class="profile-basic">
                     <div class="row">
                        <div class="col-md-5">
                           <div class="profile-info-left">
                              <h3 class="user-name m-t-0 mb-1">Dr. {{$a->name}}</h3>
                              @php
							        $doctor_info = $a->UserInformationDetails;

							        
							        $languages = $a->UserInformationDetails?->getLanguageNames() ?? [];
							        $specialisations = $a->UserInformationDetails?->getSpecialisationNames() ?? [];

							        
							        $services = $a->UserInformationDetails?->getServiceNames() ?? [];
							        $c = $a->UserClinicDetails;
							        $rat = App\Models\UserModels\Rating::where('doctor_id', $a->id)->get();
							        	
							    @endphp
                              <p class="text-muted">{{ implode(', ', $specialisations) }}</p>
                              <div class="progress progress-sm w-75">
                                 <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{round($finalCompletionPercentage)}}%" aria-valuenow="{{round($finalCompletionPercentage)}}" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>

                              <div class="staff-id">{{round($finalCompletionPercentage)}}% Profile Completed</div>
                              <div class="staff-msg"><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal7" class="btn btn-primary">Edit</a></div>
                           </div>
                        </div>
                        <div class="col-md-7">
                           <ul class="personal-info">
                              <!-- <li>
                                 <span class="title">Phone:</span>
                                 <span class="text"><a href="#">+91 9876543234</a></span>
                                 </li>
                                 <li>
                                 <span class="title">Email:</span>
                                 <span class="text"><a href="#">arora@gmail.com</a></span>
                                 </li> -->
                              <li>
                                 <span class="title">Language:</span>
                                 <span class="text">{{ implode(', ', $languages) }}</span>
                              </li>
                              <li>
                                 <span class="title">Experience:</span>
                                 <span class="text">{{$a->UserInformationDetails->experience ?? ''}} yrs</span>
                              </li>
                              <li>
                                 <span class="title">Gender:</span>
                                 <span class="text">{{$a->gender ?? ''}}</span>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="profile-tabs mt-3">
         <ul class="nav nav-tabs nav-tabs-bottom">
            <li class="nav-item"><a class="nav-link active" href="#about-cont" data-bs-toggle="tab">About</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab2" data-bs-toggle="tab">Professional</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab3" data-bs-toggle="tab">Education</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab4" data-bs-toggle="tab">Awards</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab5" data-bs-toggle="tab">Medical Registration</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab6" data-bs-toggle="tab">Timings</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab7" data-bs-toggle="tab">Review</a></li>
         </ul>
         <div class="tab-content">
            <div class="tab-pane show active" id="about-cont">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                           <div class="w-100">
                              <h3 class="card-title mb-0">About</h3>
                           </div>
                           <div class="flex-shrink-1"><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal" class="btn btn-primary">Edit</a></div>
                        </div>
                        <?=$a->UserInformationDetails->about ?? '' ?>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane" id="bottom-tab2">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                           <div class="w-100">
                              <h3 class="card-title mb-0">Professional</h3>
                           </div>
                           <div class="flex-shrink-1"><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal2" class="btn btn-primary">Edit</a></div>
                        </div>
                        <div class="row">
                           <div class="col-md-4">
                              <h4 class="card-title mb-3">Specializations</h4>
                              <ul class="spec">
                              	@if($specialisations)
                                 	@foreach($specialisations as $s)		
                                 		<li><i class="fas fa-check-circle color-1"></i> {{$s}}</li>
                                 	@endforeach
                                 @endif
                              </ul>
                           </div>
                           <div class="col-md-4">
                              <h4 class="card-title mb-3">Services</h4>
                              <ul class="spec">
                                 @if($services)
                                 	@foreach($services as $s)		
                                 		<li><i class="fas fa-check-circle color-1"></i> {{$s}}</li>
                                 	@endforeach
                                 @endif
                              </ul>
                           </div>
                           <div class="col-md-4">
                              <h4 class="card-title mb-3">Hospital Worked In</h4>
                              <i class="fas fa-check-circle color-1"></i> {{$a->UserInformationDetails->hospital_worked_in ?? ''}}
                              
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane" id="bottom-tab3">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                           <div class="w-100">
                              <h3 class="card-title mb-0">Education</h3>
                           </div>
                           <div class="flex-shrink-1"><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal3" class="btn btn-primary">Edit</a></div>
                        </div>
                        <div class="experience-box">
                           <ul class="experience-list">
                              <li>
                                 <div class="experience-user">
                                    <div class="before-circle"></div>
                                 </div>
                                 <div class="experience-content">
                                    <div class="timeline-content">
                                       <a href="#/" class="name">{{$a->UserInformationDetails->degree ?? ''}}</a>
                                       <div>{{$a->UserInformationDetails->college_institute ?? ''}}</div>
                                       <span class="time">{{$a->UserInformationDetails->year_of_completion ?? ''}}</span>
                                       {{-- <span class="time">{{$a->UserInformationDetails->year_of_experience ?? ''}}</span> --}}
                                    </div>
                                 </div>
                              </li>
                              
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane" id="bottom-tab4">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                           <div class="w-100">
                              <h3 class="card-title mb-0">Awards</h3>
                           </div>
                           <div class="flex-shrink-1"><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal4" class="btn btn-primary">Edit</a></div>
                        </div>
                        <div class="experience-box">
                           <ul class="experience-list">
                              <li>
                                 <div class="experience-user">
                                    <div class="before-circle"></div>
                                 </div>
                                 <div class="experience-content">
                                    <div class="timeline-content">
                                       <a href="#/" class="name">{{$a->UserInformationDetails->awards_name ?? ''}}</a>
                                       <div>{{$a->UserInformationDetails->award_college_institute ?? ''}}</div>
                                       <span class="time">{{$a->UserInformationDetails->award_year ?? ''}}</span>
                                    </div>
                                 </div>
                              </li>
                              
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane" id="bottom-tab5">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                           <div class="w-100">
                              <h3 class="card-title mb-0">Medical Registration</h3>
                           </div>
                           <div class="flex-shrink-1"><a href="#" data-bs-toggle="modal" data-bs-target="#centermodal5" class="btn btn-primary">Edit</a></div>
                        </div>
                        <ul class="personal-info">
                           <li>
                              <span class="title">Registration Number</span>
                              <span class="text">{{$a->UserInformationDetails->registration_number ?? ''}}</span>
                           </li>
                           <li>
                              <span class="title">Registration Council</span>
                              <span class="text">{{$a->UserInformationDetails->registration_council ?? ''}}</span>
                           </li>
                           <li>
                              <span class="title">Registration Year</span>
                              <span class="text">{{$a->UserInformationDetails->registration_year ?? ''}}</span>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
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
                              @php
                                  $availabilityJson = $a->UserInformationDetails->doctor_availability ?? null;
                                  $t = !empty($availabilityJson) ? json_decode($availabilityJson, true) : null;
                              @endphp
                          
                              @if (is_array($t) && isset($t['schedule']) && is_array($t['schedule']) && count($t['schedule']) > 0)
                                  @foreach ($t['schedule'] as $day => $times)
                                      <li>
                                          <div class="experience-user">
                                              <div class="before-circle"></div>
                                          </div>
                                          <div class="experience-content">
                                              <div class="timeline-content">
                                                  <a href="#/" class="name">{{ ucfirst($day) }}</a><br>
                                                  @if (!empty($times['morning'][0]))
                                                      <span class="time-box">{{ $times['morning'][0] }}</span>
                                                  @endif
                                                  @if (!empty($times['evening'][0]))
                                                      <span class="time-box">{{ $times['evening'][0] }}</span>
                                                  @endif
                                              </div>
                                          </div>
                                      </li>
                                  @endforeach
                              @else
                                  <li>No availability schedule found.</li>
                              @endif
                          </ul>
                          
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tab-pane" id="bottom-tab7">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Rating({{$a->ratings_count}})</h3>
                        <span class="title">Avg: {{$a->ratings_avg_rating}}</span>
                        @if($rat->count() > 0)
                        <ul class="personal-info">
                           @foreach($rat as $r)
                           <li>
                              <span class="title">{{$r->userProfile->name}}</span>
                              <span class="text">Rating: {{$r->rating}}</span>
                              <span class="text">Review: {{$r->review}}</span>
                              <span class="text">Added On: {{$r->created_at}}</span>

                           </li>
                           @endforeach
						</ul>
                        @endif
                     </div>
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
<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModalLabel">About</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
          <form method="POST" action="{{ route('admin.user.aboutupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="published_date" class="form-label">About</label>
                   <textarea class="form-control" name="about"><?=$a->UserInformationDetails->about ?? '' ?></textarea>
                   </div>
               </div>  
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
<div class="modal fade" id="centermodal2" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Professional</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
          <form method="POST" action="{{ route('admin.user.professionalupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   	<?php $data = DB::table('master_specialsations')->where('status',1)->orderBy('name','ASC')->get();?>
                   <label for="specialisations" class="form-label">Specialisations</label>
                   <select name="specialisations[]" class="form-control" multiple>
                   	  <option value="">select</option>
	                   @foreach ($data as $aa)
	                   @if(in_array($aa->name,$specialisations))
	                        <option selected value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
	                    @else    
	                        <option value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
	                    @endif
	                   @endforeach
                 	</select>
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   	<?php $data = DB::table('master_services')->where('status',1)->orderBy('name','ASC')->get();?>
                   <label for="services" class="form-label">services</label>
                   <select name="services[]" class="form-control" multiple>
                   	  <option value="">select</option>
	                   @foreach ($data as $aa)
	                   @if(in_array($aa->name,$services))
	                        <option selected value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
	                    @else    
	                        <option value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
	                    @endif
	                   @endforeach
                 	</select>
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="published_date" class="form-label">Hospital Worked In</label>
                   <input type="text" class="form-control" name="hospital_worked_in" value="<?=$a->UserInformationDetails->hospital_worked_in ?? '' ?>">
                   </div>
               </div>  
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
<div class="modal fade" id="centermodal3" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Education</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
          <form method="POST" action="{{ route('admin.user.educationupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   	<?php $data = DB::table('master_degrees')->where('status',1)->orderBy('name','ASC')->get();?>
                   <label for="degree" class="form-label">Degree</label>
                   <select name="degree" class="form-control">
                   	  <option value="">select</option>
	                   @foreach ($data as $aa)
	                   	<option @if(isset($a->UserInformationDetails->degree)) {{$a->UserInformationDetails->degree == $aa->name ? 'selected':''}} @endif value="{{ $aa->name }}">{{$aa->name ?? ''}}</option>
	                   @endforeach
                 	</select>
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   	<?php $data = DB::table('master_college_institutes')->where('status',1)->orderBy('name','ASC')->get();?>
                   <label for="college_institute" class="form-label">College/Institute</label>
                   <select name="college_institute" class="form-control">
                   	  <option value="">select</option>
	                   @foreach ($data as $aa)
	                   	<option @if(isset($a->UserInformationDetails->college_institute)) {{$a->UserInformationDetails->college_institute == $aa->name ? 'selected':''}} @endif  value="{{ $aa->name }}">{{$aa->name ?? ''}}</option>
	                   @endforeach
                 	</select>
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="year_of_completion" class="form-label">Completion Year</label>
                   <input type="text" class="form-control" name="year_of_completion" value="<?=$a->UserInformationDetails->year_of_completion ?? '' ?>">
                   </div>
               </div>  
               {{-- <div class="col-md-12">
                   <div class="mb-3">
                   <label for="year_of_experience" class="form-label">Exp Year</label>
                   <input type="text" class="form-control" name="year_of_experience" value="<?=$a->UserInformationDetails->year_of_experience ?? '' ?>">
                   </div>
               </div>   --}}
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
<div class="modal fade" id="centermodal4" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Award</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
          <form method="POST" action="{{ route('admin.user.awardupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   <label for="award_college_institute" class="form-label">Award from(College/Institute)</label>
                   <input type="text" class="form-control" name="award_college_institute" value="<?=$a->UserInformationDetails->award_college_institute ?? '' ?>">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="awards_name" class="form-label">Name</label>
                   <input type="text" class="form-control" name="awards_name" value="<?=$a->UserInformationDetails->awards_name ?? '' ?>">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="award_year" class="form-label">Year</label>
                   <input type="text" class="form-control" name="award_year" value="<?=$a->UserInformationDetails->award_year ?? '' ?>">
                   </div>
               </div>  
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
<div class="modal fade" id="centermodal5" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Medical Registration</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
          <form method="POST" action="{{ route('admin.user.counsilupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_number" class="form-label">Registration Number</label>
                   <input type="text" class="form-control" name="registration_number" value="<?=$a->UserInformationDetails->registration_number ?? '' ?>">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_council" class="form-label">Registration council</label>
                   <input type="text" class="form-control" name="registration_council" value="<?=$a->UserInformationDetails->registration_council ?? '' ?>">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_year" class="form-label">Registration Year</label>
                   <input type="text" class="form-control" name="registration_year" value="<?=$a->UserInformationDetails->registration_year ?? '' ?>">
                   </div>
               </div>  
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
           </div>
          </form>
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
       		
	        $availability = $a->UserInformationDetails->doctor_availability ? json_decode($a->UserInformationDetails->doctor_availability, true) : [];

	    @endphp
          <form method="POST" action="{{ route('admin.user.timingupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
				        <div class="mb-3">
					        <h5 class="text-capitalize">{{ ucfirst($day) }}</h5>
					        
					        <!-- Morning Slot -->
					        <label for="{{ $day }}_morning_start">Morning:</label>
					        <div class="d-flex">
					            @php
					                $morning = isset($availability['schedule'][$day]['morning'][0]) ? explode(' - ', $availability['schedule'][$day]['morning'][0]) : ['', ''];
					            @endphp
					            <input type="time" class="form-control me-2" name="schedule[{{ $day }}][morning_start]" value="{{ date('H:i', strtotime($morning[0])) }}">
					            <input type="time" class="form-control" name="schedule[{{ $day }}][morning_end]" value="{{ date('H:i', strtotime($morning[1])) }}">
					        </div>

					        <!-- Evening Slot -->
					        <label for="{{ $day }}_evening_start" class="mt-2">Evening:</label>
					        <div class="d-flex">
					            @php
					                $evening = isset($availability['schedule'][$day]['evening'][0]) ? explode(' - ', $availability['schedule'][$day]['evening'][0]) : ['', ''];
					            @endphp
					            <input type="time" class="form-control me-2" name="schedule[{{ $day }}][evening_start]" value="{{ date('H:i', strtotime($evening[0])) }}">
					            <input type="time" class="form-control" name="schedule[{{ $day }}][evening_end]" value="{{ date('H:i', strtotime($evening[1])) }}">
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
<div class="modal fade" id="centermodal7" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Edit Profile</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ route('admin.user.profileupdate',$a->id) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
          	<div class="row">
          		<div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_number" class="form-label">Name</label>
                   <input type="text" required class="form-control" name="name" value="<?=$a->name ?? '' ?>">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_council" class="form-label">Mobile</label>
                   <input type="text" required class="form-control" name="mobile" value="<?=$a->mobile ?? '' ?>">
                   </div>
               </div>  
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_year" class="form-label">Email</label>
                   <input type="email" required class="form-control" name="email" value="<?=$a->email ?? '' ?>">
                   </div>
               </div>
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="date_of_birth" class="form-label">DOB</label>
                   <input type="date" required max="{{date('Y-m-d')}}" class="form-control" name="date_of_birth" value="<?=$a->date_of_birth ?? '' ?>">
                   </div>
               </div> 
               <div class="col-md-12">
                  <div class="mb-3">
                  <label for="experience" class="form-label">Expirence</label>
                  <input type="number" required min="1" value="{{$a->UserInformationDetails->experience ?? ''}}" class="form-control" name="experience">
                  </div>
              </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="image" class="form-label">Image</label>
                   <input type="file" class="form-control" name="image"  accept="image/*">
                   </div>
               </div> 
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="registration_year" class="form-label">Gender</label>
                   <select name="gender" required class="form-control">
                    <option {{$a->gender == 'Male' ? 'selected' : ''}} value="Male">Male</option>
                    <option {{$a->gender == 'Female' ? 'selected' : ''}} value="Female">Female</option>
                 </select>
                   </div>
               </div>
               <div class="col-md-12">
                   <div class="mb-3">
                   	<?php $data = DB::table('master_langauages')->where('status',1)->orderBy('name','ASC')->get();?>
                   <label for="language_known" class="form-label">Languages</label>
                   <select name="language_known[]" class="form-control" multiple required>
                   	  <option value="">select</option>
	                   @foreach ($data as $aa)
	                   	@if(in_array($aa->name,$languages))
	                        <option selected value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
	                    @else    
	                        <option value="{{ $aa->id }}">{{$aa->name ?? ''}}</option>
	                    @endif
	                   @endforeach
                 	</select>
                   </div>
               </div>    
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Update</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
@section('js_user_page')

@endsection
@endsection
