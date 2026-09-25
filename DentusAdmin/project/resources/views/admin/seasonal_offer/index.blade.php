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
                                       <a class="btn"><img src="{{ asset('adminassets') }}/assets/img/icons/search-normal.svg" alt></a>
                                    </form>
                                 </div>
                                 <div class="add-group">
                                    <a href="{{ $addurl }}" class="btn btn-primary add-pluss ms-2"><img src="{{ asset('adminassets') }}/assets/img/icons/plus.svg" alt></a>
                                    <a href="{{ route('admin.master.seasonal_offer') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{ asset('adminassets') }}/assets/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                           <!-- <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-01.svg" alt></a>
                              <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-02.svg" alt></a> -->
                           <a href="javascript:;" class=" me-2"><img src="{{ asset('adminassets') }}/assets/img/icons/pdf-icon-03.svg" alt></a>
                           <a href="javascript:;"><img src="{{ asset('adminassets') }}/assets/img/icons/pdf-icon-04.svg" alt></a>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0">
                        <thead>
                           <tr>
                              <th>User Type</th>
                              <th>Offer Type</th>
                              <th>Treatment Package</th>
                              <th>Discount</th>
                              {{-- <th>Offer Validity</th> --}}
                              <th>Image</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i=1; ?>
                           @foreach ($data as $a)
                           <tr>
                            @php $dr = DB::table('users')->where('id',$a->user_id)->first(); @endphp
                            @if($a->created_by == 0)
                              <td>SuperAdmin</td>
                            @else
                            <td>{{ $dr->name ?? ""}}-(Doctor)</td>
                            @endif
                              <td>{{ $a->offer_type ?? ""}}</td>
                              <td>{{ $a->treat_package_id ?? ""}}</td>
                              <td>{{ $a->discount ?? ""}}</td>
                              {{-- <td>{{ $a->offer_validity ?? ""}}</td> --}}
                              <td><img width="50" height="50" src="{{ asset('../content/SeasonalOffers/') }}/{{$a->image ?? "" }}" class="m-r-5" alt></td>
                              <td>
                                 <span class="badge {{ $a->status == 1 ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                 {{ $a->status == 1 ? 'Active ' : 'Inactive' }}
                                 </span>
                              </td>
                              {{-- <td>
                                 <button type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
                                 <button type="button" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                              </td> --}}
                              <td>
                                <a href="{{ route($editurl,$a->id) }}" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                <a href="#" class="btn btn-danger" href="#" data-toggle="modal" data-target="#modal-danger" data-userid="{{$a->id}}"><i class="fas fa-trash"></i></a>
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
   </div>

   <div class="modal modal-danger fade" id="modal-danger">
    <div class="modal-dialog">
       <div class="modal-content bg-danger">
          <div class="modal-header">
             <h4 class="modal-title">Delete</h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
             <p>Are you sure you want to delete this?</p>
          </div>
          <div class="modal-footer">
             <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="user_id" name="id" value="">
                <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
             </form>
          </div>
       </div>
       <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
            <h4 class="modal-title" id="myCenterModalLabel">Treatment & Session</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <ul style="list-style-type: none; padding-left: 0px;" class="mb-0">
               <li style="display: flex; justify-content: space-between;" class="mb-2">
                  <span>Teeth Whitening</span> <span>2 Sessions</span>
               </li>
               <li style="display: flex; justify-content: space-between;" class="mb-2">
                  <span>Dental Implant</span> <span>3 Sessions</span>
               </li>
               <li style="display: flex; justify-content: space-between;" class="mb-2">
                  <span>Root Canal Treatment</span> <span>1 Session</span>
               </li>
            </ul>
         </div>
      </div>
   </div>
</div>
<div id="delete_patient" class="modal fade delete-modal" role="dialog">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-body text-center">
            <img src="assets/img/sent.png" alt width="50" height="46">
            <h3>Are you sure want to delete this ?</h3>
            <div class="m-t-20"> <a href="#" class="btn btn-white" data-bs-dismiss="modal">Close</a>
               <button type="submit" class="btn btn-danger">Delete</button>
            </div>
         </div>
      </div>
   </div>
</div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })

</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
   $(document).ready(function() {
   $('.summernote').summernote();
   });


   $('#modal-danger').on('show.bs.modal', function (event) {

   var button = $(event.relatedTarget)

   var user_id = button.data('userid')



   var modal = $(this)

   modal.find('.modal-footer #user_id').val(user_id)

   // modal.find('form').attr('action','permissions/' + user_id);

})
</script>
@endsection
@endsection
