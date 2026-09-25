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
                  <h2>Hello, <span>{{ Auth()->guard('admin')->user()->name }} {{ Auth()->guard('admin')->user()->last_name }}!</span></h2>
                  
               </div>
            </div>
            <div class="col-md-6 position-blk">
               <div class="morning-user-1 text-end">
                  
                  <!-- <button type="button" class="btn btn-secondary btn-lg py-3">Complete Profile</button> -->
               </div>
            </div>
         </div>
      </div>
      @if(auth()->user()->status == 1 && auth()->user()->parent_id == 0)
      
      @endif
   </div>
</div>

@endsection