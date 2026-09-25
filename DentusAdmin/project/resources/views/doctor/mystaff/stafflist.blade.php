@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Doctors </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">{{ $title }}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <div class="card card-table show-entire">
               <div class="card-body">
                  <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <div class="col">
                           <div class="doctor-table-blk">
                              <h3>{{ $title }}</h3>

                              <div class="doctor-search-blk">
                                 <div class="top-nav-search table-search-blk">
                                    <form>
                                       <input type="text" class="form-control" placeholder="Search here">
                                       <a href="#" class="btn btn-primary doctor-refresh ms-2"><img src="{{ asset('adminassets') }}/img/icons/re-fresh.svg" alt></a>

                                    </form>
                                 </div>
                                 <div class="add-group">
                                    <a href="{{$addurl1}}" class="btn btn-primary add-pluss ms-2"><img src="{{ asset('adminassets') }}/img/icons/plus.svg" alt></a>
                                    <a href="javascript:;" class="btn btn-primary doctor-refresh ms-2"><img src="{{ asset('adminassets') }}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div>

                              {{-- <div class="doctor-search-blk">
                                 <div class="add-group">
                                    <a href="#" class="btn btn-primary doctor-refresh ms-2"><img src="{{ asset('adminassets') }}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div> --}}
                           </div>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                           <!-- <a href="javascript:;" class=" me-2"><img src="{{ asset('adminassets') }}/img/icons/pdf-icon-01.svg" alt></a>
                              <a href="javascript:;" class=" me-2"><img src="{{ asset('adminassets') }}/img/icons/pdf-icon-02.svg" alt></a> -->
                           <a href="javascript:;" class=" me-2"><img src="{{ asset('adminassets') }}/img/icons/pdf-icon-03.svg" alt></a>
                           <a href="javascript:;"><img src="{{ asset('adminassets') }}/img/icons/pdf-icon-04.svg" alt></a>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0">
                        <thead>
                           <tr>
                              <th>Name</th>
                              {{-- <th>Role</th>
                              <th>Staff</th>
                              <th>Specialization</th>
                              <th>Approved</th>
                              <th>Status</th> --}}
                              <th>Status</th>

                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($data->count() > 0)
                           @foreach($data as $a)
                           {{-- @php
                           $doctor_info = $a->UserInformationDetails;
                           $specialisation_ids = $doctor_info && $doctor_info->specialisations
                           ? explode('|', $doctor_info->specialisations)
                           : [];
                           $specialisations = \App\Models\MasterSpecialsation::whereIn('id', $specialisation_ids)->get();
                           $specialisations_data = $specialisations->map(function ($specialisation) {
                           return $specialisation->name;
                           })->toArray();
                           @endphp --}}
                           <tr>
                              <td class="profile-image"><a href="{{ route('admin.user.mystaffdetail',$a->id) }}"><img width="28" height="28" src="{{asset('../content/doctor/'.$a->image)}}" class="rounded-circle m-r-5" alt> Dr. {{$a->name}}</a></td>
                              @if($a->parent_id == 0)
                                        <td><span class="badge badge-soft-success">Doctor</span></td>
                                        @else
                                        @php $dr_m = DB::table('users')->where('id',$a->parent_id)->first(); @endphp
                                        <td><span class="badge badge-soft-success">
                                        {{$dr_m->name}} - Staff
                                       </span></td>
                                @endif
                              {{-- <td><span class="badge badge-soft-success">Staff</span></td>                             
                              <td>
                                 <a href="{{ route('admin.user.staff_list', [$a->id]) }}" 
                                    class="btn btn-warning waves-effect waves-light btn-sm" 
                                    style="color:white">
                                    view
                                 </a>
                             </td>

                              <td>#</td>
                              <td>
                                 @if ($a->approved == 3)
                                 <a href="{{ route('admin.doctor_toggle_approve', $a->id) }}" class="badge badge-soft-success">
                                 Approve
                                 </a>
                                 <a href="{{ route('admin.doctor_toggle_reject', $a->id) }}" class="badge badge-soft-danger">
                                 Reject
                                 </a>
                                 @elseif ($a->approved == 1)
                                 <span class="badge badge-soft-success">Approved</span>
                                 @else
                                 <span class="badge badge-soft-danger">Rejected</span>
                                 @endif
                              </td> --}}
                              {{-- <td>
                                 @if($a->status == 1)
                                 <button class="btn btn-warning toggle-status"
                                             data-id="{{ $a->id }}"
                                             data-status="{{ $a->status }}">
                                         Active
                                     </button>
                                 @elseif($a->status == 0 || $a->status == 3)
                                     <button class="btn btn-warning toggle-status"
                                             data-id="{{ $a->id }}"
                                             data-status="{{ $a->status }}">
                                         Inactive
                                     </button>
                                 @else
                                     <span class="badge badge-soft-danger">Rejected</span>
                                 @endif
                             </td> --}}
                             <td>

                                <span class="badge {{ $a->status == 1 ? 'badge-soft-success' : 'badge-soft-danger' }}">

                                {{ $a->status == 1 ? 'Active' : 'Inactive' }}

                                </span>                                   

                             </td>
                                                                                  
                                 
                              <td>
                                 {{-- <a href="{{ route($editurl,$a->id) }}" class="btn btn-primary"><span class="fas fa-pencil-alt"><span class="path1"></span><span class="path2"></span></span></a> --}}
                                 {{-- <a href="{{ route('admin.user.mystaffdetail',$a->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>                                --}}
                                   
                                   <a href="#" href="#" data-userid="{{ $a->id }}" class='btn btn-danger' data-toggle="modal" data-target="#modal-danger" >

                                    <span class="fas fa-trash"><span class="path1"></span><span class="path2"></span></span></a>   
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
<div class="modal  fade" id="modal-danger" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">

   <div class="modal-dialog" role="document">

      <div class="modal-content bg-danger">

         <div class="modal-header">

            <h5 class="modal-title">Delete</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span></button>

         </div>

         <div class="modal-body">

            <p>Are you sure you want to delete this?</p>

         </div>

         <div class="modal-footer">

            <form id ="deleteform" method="POST" action="{{$destroyurl}}" enctype="multipart/form-data">

               {{-- @method('DELETE') --}}

               @csrf

               <input type="hidden" id="user_id" name="id" value="">
               

               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>

               {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}

            </form>

         </div>

      </div>

      <!-- /.modal-content -->

   </div>

   <!-- /.modal-dialog -->

</div>

@section('scripts')
<script src="{{ asset('project/public/adminassets_old') }}/js/vendors.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
   $(document).on('click', '.toggle-status', function() {
       var button = $(this);
       var id = button.data('id');
       var status = button.data('status');
   
       var isActive = status == 1; 
       var titleText = isActive ? "Are you sure you want to deactivate this?" : "Are you sure you want to activate this?";
       var confirmText = isActive ? "Yes, Deactivate it!" : "Yes, Activate it!";
   
       Swal.fire({
           title: titleText,
           icon: "warning",
           showCancelButton: true,
           confirmButtonColor: "#28a745",
           cancelButtonColor: "#d33",
           confirmButtonText: confirmText
       }).then((result) => {
           if (result.isConfirmed) {
               $.ajax({
                   url: "{{ route('admin.user.update_status') }}",  
                   type: "POST",
                   data: {
                       id: id,
                       status: isActive ? 0 : 1, 
                       _token: "{{ csrf_token() }}"
                   },
                   success: function(response) {
                       if (response.success) {
                        Swal.fire("Updated!", response.message, "success").then(() => {
                               window.location.reload();

                           // Swal.fire("Updated!", response.message, "success").then(() => {
                           //     button.text(response.newStatus == 1 ? 'Active' : 'Inactive');
                           //     button.data('status', response.newStatus);
                           });
                       } else {
                           Swal.fire("Error!", response.message, "error");
                       }
                   },
                   error: function(xhr) {
                       Swal.fire("Error!", "Something went wrong. Please try again.", "error");
                   }
               });
           }
       });
   });
   </script>

<script>
   $('#modal-danger').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var user_id = button.data('userid')
   
        var modal = $(this)
        modal.find('.modal-footer #user_id').val(user_id)
    });
</script>

@endsection
@endsection
