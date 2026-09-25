@extends('layouts.admin')
@section('content')
<?php
$name = '';
$email = '';
$mobile = '';
if (isset($_GET['name']))
{
   $name = $_GET['name'];
}
if (isset($_GET['email']))
{
   $email = $_GET['email'];
}
if (isset($_GET['mobile']))
{
   $mobile = $_GET['mobile'];
}
?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Patient List</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
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
            <div class="card card-table show-entire">
               <div class="card-body">
                  <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <div class="col">
                           <div class="doctor-table-blk">
                              <h3>Patient List</h3>
                              <div class="doctor-search-blk">
                                 <div class="add-group">
                                    <a href="{{ route('admin.myuser.create_myuser') }}" class="btn btn-primary add-pluss ms-2"><img src="{{asset('content/admin')}}/img/icons/plus.svg" alt></a>
                                    <a href="{{ route('admin.myuser.myuserlist') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                 </div>

                              </div>
                           </div>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="{{ route('admin.myuser.myuserlist') }}?exp=export" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-03.svg" alt></a>
                            <a href="{{ route('admin.myuser.myuserlist') }}?exp=export"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-04.svg" alt></a>
                        </div>
                     </div>
                  </div>



                  <div class="staff-search-table">
                    <form method="GET" action="{{ route('admin.myuser.myuserlist') }}">
                    <div class="row">
                    <div class="col-12 col-md-6 col-xl-4">
                    <div class="input-block local-forms">
                    <label>Patient Name </label>
                    <input type="text" class="form-control" name="name" placeholder="User" value="{{$name}}">
                    </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="input-block local-forms">
                        <label>Patient Mobile </label>
                        <input type="text" class="form-control" name="mobile" placeholder="mobile" value="{{$mobile}}">
                        </div>
                        </div>

                    <div class="col-12 col-md-6 col-xl-4">
                    <div class="doctor-submit">
                    <button type="submit" class="btn btn-primary submit-list-form me-2">Search</button>
                    </div>
                    </div>
                    </div>
                    </form>
                    </div>

                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Mobile</th>
                              <th>Added On</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                        	@if($data->count() > 0)
                  			@foreach($data as $a)
                  			<tr>
                              <td class="profile-image"><a href="{{ route('admin.myuser.paitentprofile',$a->id) }}"><img width="28" height="28" src="{{asset('content/user/'.$a->image)}}" class="rounded-circle m-r-5" alt> {{$a->name}}</a></td>
                              <td><span class="badge badge-soft-success">{{$a->email}}</span></td>
                              <td><span class="badge badge-soft-success">{{$a->mobile}}</span></td>
                              <td><span class="badge badge-soft-success">{{$a->created_at->diffForHumans()}}</span></td>

                              <td>
                                 <a href="{{ route($editurl,$a->id) }}" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                 <a href="{{ route('admin.myuser.paitentprofile',$a->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>

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