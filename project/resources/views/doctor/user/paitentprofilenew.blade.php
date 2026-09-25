@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="row">
         <div class="col-sm-7 col-6">
            <ul class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{$listurl}}">Patients </a></li>
               <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
               <li class="breadcrumb-item active">Patients Profile</li>
            </ul>
         </div>
         <div class="col-sm-5 col-6 text-end m-b-30">
            <!-- <a href="#" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Edit Profile</a> -->
         </div>
      </div>
      <div class="card-box profile-header">
         <div class="row">
            <div class="col-md-12">
               <div class="profile-view">
                  <div class="profile-img-wrap">
                     <div class="profile-img">
                        <a href="#">
                           @if($id == 1)
                           <img class="avatar" src="{{asset('project/public/member_images/')}}/{{$a->image}}" alt>
                           @else
                           <img class="avatar" src="{{asset('content/user/'.$a->image)}}" alt>
                           @endif
                        </a>
                     </div>
                  </div>
                  <div class="profile-basic">
                     <div class="row">
                        <div class="col-md-5">
                           <div class="profile-info-left">
                              <h3 class="user-name m-t-0 mb-1">{{$a->name}} {{$a->last_name}}</h3>
                              <p class="text-muted mb-1">{{$a->gender}}</p>
                              <p class="text-muted">{{$a->date_of_birth}}</p>
                              
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
                                 <span class="title">Email:</span>
                                 <span class="text">{{$a->email}}</span>
                              </li>
                              <li>
                                 <span class="title">Mobile:</span>
                                 <span class="text">+91 {{$a->mobile}}</span>
                              </li>
                              <!-- <li>
                                 <span class="title">Gender:</span>
                                 <span class="text">Male</span>
                                 </li> -->
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
            <li class="nav-item"><a class="nav-link active" href="#about-cont" data-bs-toggle="tab">Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab2" data-bs-toggle="tab">Past Prescription</a></li>
            @if($id == 0)
            <li class="nav-item"><a class="nav-link" href="#bottom-tab3" data-bs-toggle="tab">Loyalty Points</a></li>
            <li class="nav-item"><a class="nav-link" href="#bottom-tab4" data-bs-toggle="tab">Package subscription</a></li>
            @endif
            <li class="nav-item"><a class="nav-link" href="#bottom-tab5" data-bs-toggle="tab">Medical Record</a></li>
            
         </ul>
         <div class="tab-content">
            <div class="tab-pane show active" id="about-cont">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Bookings</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>Doctor</th>
                                    <!-- <th>Specialization</th> -->
                                    <th>Consultation Type</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @if($appointmentsWithDoctor->count() > 0)
                                 	@foreach($appointmentsWithDoctor as $s)	
                                 	@php

								        $specialisations = $s->doctordetail->UserInformationDetails?->getSpecialisationNames() ?? [];
									@endphp
	                                 <tr>
	                                    <td class="profile-image"><a href="{{route('admin.user.mystaffdetail',$s->doctor_id)}}"><img width="28" height="28" src="{{asset('content/doctor/'.$s->doctordetail->image ?? 'default.png')}}" class="rounded-circle m-r-5" alt> Dr. {{$s->doctordetail->name ?? 'NA'}}</a></td>
	                                    <!-- <td>{{ implode(', ', $specialisations) }}</td> -->
	                                    <td><span class="badge badge-soft-success">{{$s->consultation_type}}</span></td>
	                                    <td>{{date('d M Y h:i a', strtotime($s->schedule_date.' '.$s->schedule_time))}}</td>
	                                    <td>
	                                       <a href="{{route('admin.appoint.myappointmentdetails',$s->id)}}"><button type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button></a>
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
            <div class="tab-pane" id="bottom-tab2">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Past Prescription</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Prescription ID</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @if($prescritptionDetails->count())
                                 	@php
								        $i =1;
									@endphp
                                 	@foreach($prescritptionDetails as $p)	
                                 	
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>#{{$p->id}}</td>
                                    <td>{{date('d M Y h:i a', strtotime($p->updated_at))}}</td>
                                    <td>
                                       <a target="_blank" href="{{$p->prescriptionlist[0]->viewurl ?? '#'}}"><button  type="button" class="btn btn-primary"><i class="fas fa-eye"></i></button></a>
                                       
                                    </td>
                                 </tr>
                                <?php $i++; ?>
                                 @endforeach
                                 @endif
                                 
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?php 

            $list = App\Models\UserModels\UserSubscriptionsPack::where('status', 1)
                ->where('user_id', $a->id)
                ->orderBy('end_date', 'asc')
                ->get();
        		?>
            @if($id == 0)
            <?php //$credits = $a->userPayments->whereIN('action', ['credit','debit'])->whereIN('type',[2,3])->values();
             ?>
            <div class="tab-pane" id="bottom-tab3">
               <div class="col-md-4">
                  <div class="dash-widget">
                     <div class="dash-content dash-count">
                        <h4>Total Loyalty Points</h4>
                        <h2 class="mb-0"><span class="counter-up">{{$a->wallet}}</span></h2>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="card bg-white">
                     <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-solid nav-justified" role="tablist">
                           <li class="nav-item" role="presentation"><a class="nav-link active" href="#solid-justified-tab1" data-bs-toggle="tab" aria-selected="true" role="tab">Earning History</a></li>
                           <li class="nav-item" role="presentation"><a class="nav-link" href="#solid-justified-tab2" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Redemption History</a></li>
                        </ul>
                        <div class="tab-content">
                           <div class="tab-pane active show" id="solid-justified-tab1" role="tabpanel">
                              <div class="row">
                                 <div class="col-sm-12">
                                    <div class="card card-table show-entire">
                                       <div class="card-body">
                                          <div class="table-responsive">
                                             <table class="table border-0 custom-table comman-table  mb-0">
                                                <thead>
                                                   <tr>
                                                      <th>S.no</th>
                                                      <th>Id</th>
                                                      <th>date</th>
                                                      <th>Description</th>
                                                      <th>Point</th>
                                                   </tr>
                                                </thead>
                                                <tbody>
                                                   @if($credits)
                                                   @php
                                                        $i =1;
                                                   @endphp
                                                   @foreach($credits as $pp)  
                                                   <tr>
                                                      <td>{{$i}}</td>
                                                      <td>#{{$pp->id}}</td>
                                                      <td>{{date('d M Y h:i a', strtotime($pp->created_at))}}</td>
                                                      <td>Points Added</td>
                                                      <td><span class="badge badge-soft-success fs-6">+ {{$pp->amount}}</span></td>
                                                   </tr>
                                                   <?php $i++; ?>
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
                           <div class="tab-pane" id="solid-justified-tab2" role="tabpanel">
                              <div class="row">
                                 <div class="col-sm-12">
                                    <div class="card card-table show-entire">
                                       <div class="card-body">
                                          <div class="table-responsive">
                                             <table class="table border-0 custom-table comman-table  mb-0">
                                                <thead>
                                                   <tr>
                                                      <th>S.no</th>
                                                      <th>Id</th>
                                                      <th>date</th>
                                                      <th>Description</th>
                                                      <th>Point</th>
                                                   </tr>
                                                </thead>
                                                <tbody>
                                                   @if($debits)
                                                   @php
                                                        $i =1;
                                                   @endphp
                                                   @foreach($debits as $pp)  
                                                   <tr>
                                                      <td>{{$i}}</td>
                                                      <td>#{{$pp->id}}</td>
                                                      <td>{{date('d M Y h:i a', strtotime($pp->created_at))}}</td>
                                                      <td>Debit</td>
                                                      <td><span class="badge badge-soft-danger fs-6">- {{$pp->amount}}</span></td>
                                                   </tr>
                                                   <?php $i++; ?>
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
                        </div>
                     </div>
                  </div>
               </div>
               
            </div>
            @endif
            <div class="tab-pane" id="bottom-tab4">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Package subscription</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>Package Name</th>
                                    <th>Validity</th>
                                    <th>Treatments</th>
                                    <th>Valid Till</th>
                                    <th>Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                              	 @if($list->count())
                                 	@php
								        $i =1;
									@endphp
                                 	@foreach($list as $l)
                                 	<?php 
                                 	$l->payment = App\Models\UserModels\UserPayment::where('id', $l->payment_id)
				                        ->select('amount', 'tax_amount', 'payment_status', 'payment_method')
				                        ->first();
				                
				                    $l->user_prem = App\Models\UserModels\UserPremiumAddonsPack::where('subscription_id', $l->id)->with('featurelist')
				                        ->select('pack_feature_id', 'treatment_id', 'quantity') ->withCount([
				                            'bookings as used_quantity' => function ($query) use ($l) {
				                                $query->where('plan_id', $l->id)
				                                      ->whereColumn('treatment_id', 'user_premium_addons_pack.treatment_id')
				                                      ->whereIn('status', [0, 1, 4]);
				                            }
				                        ])
				                        ->get();
				                    $l->quantity = App\Models\UserModels\UserPremiumAddonsPack::where('subscription_id', $l->id)
				                        ->select('quantity')
				                        ->sum('quantity');

				                    $l->used = App\Models\UserModels\Booking::where('plan_id', $l->id)
				                                ->whereIn('status', [0, 1, 4])
				                                ->count();
				                    $userProfile = App\Models\UserModels\UserProfile::where('id', $l->user_id)->first();
				                    $userName = $userProfile ? $userProfile->name : 'Unknown';
				                
				                    $userPack = App\Models\UserModels\PackTreatment::where('id', $l->pack_id)->first();
				                    
				                    $userPackName = $userPack ? $userPack->name : 'Unknown';
				                    $userPackType = $userPack ? $userPack->type : 'Unknown';
				                    
				                    $l->user_id = $userName;
				                    $l->pack_id = $userPackName;
				                    $l->type = $userPackType; 
				                    
                                 	?>	
	                                 <tr>
	                                    <td>{{$l->pack_id}}</td>
	                                    <td>{{$l->type}}</td>
	                                    <td>{{$l->used}}/{{$l->quantity}} taken</td>
	                                    <td>{{date('d/m/Y', strtotime($l->end_date))}}</td>
	                                    <td>
	                                    	@if($l->status == 1)
	                                    	<span class="badge badge-soft-success">Active</span>
	                                    	@else
	                                    	<span class="badge badge-soft-danger">Expired</span>
											@endif
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
            <div class="tab-pane" id="bottom-tab5">
               <div class="row">
                  <div class="col-md-12">
                     <div class="card-box">
                        <h3 class="card-title">Medical Record</h3>
                        <div class="table-responsive">
                           <table class="table border-0 custom-table comman-table datatable mb-0">
                              <thead>
                                 <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                    @if($lmr)
                                    @foreach($lmr as $l1)
                                    <?php 
                                    
                                
                                    ?> 
                                    <tr>
                                       <td>{{$l1->id}}</td>
                                       <td>{{date('d/m/Y', strtotime($l1->created_at))}}</td>
                                       <td>{{$l1->type}}</td>
                                       <td>
                                          @if($l1->type == 'prescription')
                                              @if(!empty($l1->prescription))
                                                  <a target="_blank" href="{{ asset('project/storage/app/public/prescriptions/').'/'.$l1->pdf ?? '#' }}">
                                                      <button type="button" class="btn btn-primary">
                                                          <i class="fas fa-eye"></i>
                                                      </button>
                                                  </a>
                                              @else
                                                  <a target="_blank" href="{{ $l1->image ?? '#' }}">
                                                      <button type="button" class="btn btn-primary">
                                                          <i class="fas fa-eye"></i>
                                                      </button>
                                                  </a>
                                              @endif
                                          @else
                                              <a target="_blank" href="{{ $l1->image ?? '#' }}">
                                                  <button type="button" class="btn btn-primary">
                                                      <i class="fas fa-eye"></i>
                                                  </button>
                                              </a>
                                          @endif

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
      </div>
   </div>
</div>
@section('js_user_page')
@endsection
@endsection