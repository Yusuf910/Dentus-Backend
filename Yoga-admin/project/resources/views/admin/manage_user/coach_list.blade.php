@extends('layouts.admin')
@section('content')
<?php
   $name = '';key: 
   $email = '';
   $mobile = '';
   $status = '';
   $start_date = '';
   $end_date = '';
   if (isset($_GET['name']))
   {
      $name = $_GET['name'];
   }
   
   if (isset($_GET['mobile']))
   {
      $mobile = $_GET['mobile'];
   }
   
   if (isset($_GET['email']))
   {
      $email = $_GET['email'];
   }
   
   if (isset($_GET['status']))
   {
      $status = $_GET['status'];
   }
   
   if (isset($_GET['start_date']))
   {
      $start_date = $_GET['start_date'];
   }
   
   if (isset($_GET['end_date']))
   {
      $end_date = $_GET['end_date'];
   }
   ?>
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                   <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="{{route('admin.create_coach_list')}}" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Add Coach
                        </a>
                        <!-- <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                           </button> -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
           <div class="col-md-12" style="margin-bottom: 5px;">
            <form method="GET" action="" enctype="multipart/form-data">
               <span>
                   <input type="hidden" name="name" value="{{ request('name') }}">
                <input type="hidden" name="email" value="{{ request('email') }}">
                <input type="hidden" name="mobile" value="{{ request('mobile') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

               <input type="submit" name="export_file" value="Export" class="btn btn-dark btn-sm waves-effect waves-light" >
               </span>
            </form>
         </div>
         <!-- Filter Form -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body p-3">
                     <form class="form" action="" method="GET">
                        <div class="row">
                           <div class="col-md-3">
                              <div class="mb-3">
                                 <label class="form-label">Date From</label>
                                 <input class="form-control" type="date" value="{{$start_date}}" placeholder="Enter date" name="start_date" id="start_date" onchange="setMinEndDate()">
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="mb-3">
                                 <label class="form-label">Date To</label>
                                 <input class="form-control" type="date" value="{{$end_date}}" placeholder="Enter date" name="end_date" id="end_date">
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="mb-3">
                                 <label class="form-label">Name</label>
                                 <input class="form-control" type="text" value="{{$name}}" placeholder="Enter Name" name="name">
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="mb-3">
                                 <label class="form-label">Mobile</label>
                                 <input class="form-control" type="number" value="{{ $mobile }}" name="mobile" placeholder="Enter Mobile Number" id="mobile-input" >
                              </div>
                           </div>
                        </div>
                        <div>
                           <input type="submit" value="Submit" class="btn btn-primary w-md">
                           <a href="{{ route('admin.coach_list') }}" class="btn btn-outline-primary w-md">Reset</a>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
         <!-- User Table -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body table-responsive">
                     <table class="table table-bordered table-striped">
                        <thead>
                           <tr>
                              <th>#ID</th>
                              <th>Coach Name</th>
                              <th>Gender</th>
                              <th>Email</th>
                              <th>Mobile</th>
                              <th>Status</th>
                              <th>Register On</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @forelse ($users as $index => $a)
                           <tr>
                              <td>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</td>
                              <td>{{ $a['name'] ?? '-' }}</td>
                              <td>{{ ucfirst($a['gender'] ?? '-') }}</td>

                              <td>{{ $a['email'] ?? '-' }}</td>
                              <td>{{ $a['mobile'] ?? '-' }}</td>
                              <td>
                                 <span class="btn {{ $a['status'] == 1 ? 'btn-primary btn-sm' : 'btn-danger btn-sm' }}">
                                    {{ $a['status'] == 1 ? 'Active' : 'Inactive' }}
                                 </span>
                              </td>
                              <td>
                                 {{ \Carbon\Carbon::parse($a['createdAt'])->format('d M Y') }}
                              </td>
                              <td>
                                 {{-- @if($a->status != 1)
                                 <button class="btn btn-success btn-sm approve-btn" data-id="{{ $a->id }}">
                                 Approve
                                 </button>
                                 @endif
                                 @if($a->status != 0)
                                 <button class="btn btn-danger btn-sm reject-btn" data-id="{{ $a->id }}">
                                 Reject
                                 </button>
                                 @endif --}}
                                <a href="{{ route('admin.edit_coach_list', $a['_id'] ?? '') }}" 
                                class="btn btn-warning btn-sm waves-effect waves-light">
                                    <i class="mdi mdi-pencil d-block font-size-12"></i>
                                </a>

                                 {{-- <a href="{{ route('admin.master.view_user_detail', $a->id) }}" class="btn btn-secondary btn-sm waves-effect waves-light">
                                 <i class="mdi mdi-eye d-block font-size-12"></i>
                                 </a> --}}
                                 <button id="modal-danger" data-userid="{{ isset($a['_id']) ? $a['_id'] : '' }}" type="button" class="btn btn-danger btn-sm waves-effect waves-light sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button>
                              </td>
                           </tr>
                           @empty
                           <tr>
                              <td colspan="6" class="text-center">No users found.</td>
                           </tr>
                           @endforelse
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
         <div class="mt-3">
            {{ $users->links() }}
         </div>
      </div>
   </div>
   <div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
   <div class="modal-dialog">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h4 class="modal-title">Delete</h4>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you sure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form method="POST" action="{{route('admin.delete_coach_list')}}" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="user_id" name="_id" value="">
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               {{ date('Y') }} © Yoga.
            </div>
         </div>
      </div>
   </footer>
</div>
@section('scripts')
<script>
   $('.sddel').click('show.bs.modal', function (event) {
   
       var user_id = $(this).attr('data-userid')
   
       $('#user_id').val(user_id);
   })
</script>
<script>
      
   function setMinEndDate() {
       const startDate = document.getElementById('start_date').value;
       if (startDate) {
           document.getElementById('end_date').min = startDate;
           // If current end_date is before start_date, reset it
           const endDate = document.getElementById('end_date').value;
           if (endDate && new Date(endDate) < new Date(startDate)) {
               document.getElementById('end_date').value = startDate;
           }
       }
   }
   
   function validateMobile(input) {
       const mobileError = document.getElementById('mobile-error');
       if (input.value.length !== 10) {
           mobileError.classList.remove('d-none');
           return false;
       } else {
           mobileError.classList.add('d-none');
           return true;
       }
   }
   
   function resetForm() {
       document.querySelector('form').reset();
       document.getElementById('end_date').min = '';
       document.getElementById('mobile-error').classList.add('d-none');
   }
   
   // Initialize min date for end_date if start_date is already set
   document.addEventListener('DOMContentLoaded', function() {
       const startDate = document.getElementById('start_date').value;
       if (startDate) {
           document.getElementById('end_date').min = startDate;
       }
       const mobileInput = document.getElementById('mobile-input');
       if (mobileInput.value) {
           validateMobile(mobileInput);
       }
   });
</script>

@endsection
@endsection