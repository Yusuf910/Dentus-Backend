@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
            <div class="content">
               <div class="page-header">
                  <div class="row">
                     <div class="col-sm-12">
                        <ul class="breadcrumb">
                           <li class="breadcrumb-item"><a href="doctors.html">Doctors </a></li>
                           <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                           <li class="breadcrumb-item active">Doctors List</li>
                        </ul>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm-12">
                     <div class="card card-table show-entire">
                  @include('includes.admin.form-success') 

                        <div class="card-body">
                           <div class="page-table-header mb-2">
                              <div class="row align-items-center">
                                 <div class="col">
                                    <div class="doctor-table-blk">
                                       <h3>Doctors List</h3>
                                       <div class="doctor-search-blk">
                                          
                                          <div class="add-group">
                                            
                                             <a href="{{ route('admin.user.mystaff') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-auto text-end float-end ms-auto download-grp">
                                    <!-- <a href="javascript:;" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-01.svg" alt></a>
                                       <a href="javascript:;" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-02.svg" alt></a> -->
                                    <a href="{{ route('admin.user.mystaff') }}?exp=export" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-03.svg" alt></a>
                                    <a href="{{ route('admin.user.mystaff') }}?exp=export"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-04.svg" alt></a>
                                 </div>
                              </div>
                           </div>
                           <div class="table-responsive">
                              <table class="table border-0 custom-table comman-table datatable mb-0">
                                 <thead>
                                    <tr>
                                       <th>Name</th>
                                       <th>Role</th>
                                       <th>Profile Completion</th>
                                       <th>Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                 	@if($data)
                           			@foreach($data as $a)
                           			 <tr>
                                       <td class="profile-image">
                                          @if($a['final_completion_percentage'] > 0)
                                          <a href="{{ route('admin.user.mystaffdetail',$a['user_profile']->id) }}"><img width="28" height="28" src="{{asset('content/doctor/'.$a['user_profile']->image)}}" class="rounded-circle m-r-5" alt> {{$a['user_profile']->name}} {{$a['user_profile']->last_name}}</a>
                                          @else
                                          {{$a['user_profile']->name}} {{$a['user_profile']->last_name}}
                                          @endif
                                       </td>
                                       <td><span class="badge badge-soft-success">Staff</span></td>
                                       <td>{{ $a['final_completion_percentage'] }}%</td>
                                       <td>
                                          @if($a['final_completion_percentage'] > 0)
                                          <a href="{{ route('admin.user.mystaffdetail',$a['user_profile']->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>
                                          @else
                                          <a href="{{ route('admin.user.sendreminder',$a['user_profile']->id) }}" class="btn btn-primary">Send Reminder</a>
                                          @endif
                                          <!-- <button type="button" class="btn btn-danger"><i class="fas fa-trash"></i></button> -->
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
@endsection