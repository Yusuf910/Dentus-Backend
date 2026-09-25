@extends('layouts.admin')
@section('content')


<div class="page-wrapper">
    <div class="content">

    <div class="page-header">
    <div class="row">
    <div class="col-sm-12">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="appointments.html">Treatments </a></li>
    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
    <li class="breadcrumb-item active">Treatments List</li>
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
    <h3>Treatments</h3>
    <div class="doctor-search-blk">
    <div class="top-nav-search table-search-blk">
    <form>
    <input type="text" class="form-control" placeholder="Search here">
    <a class="btn"><img src="assets/img/icons/search-normal.svg" alt></a>
    </form>
    </div>
    <div class="add-group">
    <a href="add-treatments.html" class="btn btn-primary add-pluss ms-2"><img src="assets/img/icons/plus.svg" alt></a>
    <a href="javascript:;" class="btn btn-primary doctor-refresh ms-2"><img src="assets/img/icons/re-fresh.svg" alt></a>
    </div>
    </div>
    </div>
    </div>
    <div class="col-auto text-end float-end ms-auto download-grp">
    <!-- <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-01.svg" alt></a>
    <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-02.svg" alt></a> -->
    <a href="javascript:;" class=" me-2"><img src="assets/img/icons/pdf-icon-03.svg" alt></a>
    <a href="javascript:;"><img src="assets/img/icons/pdf-icon-04.svg" alt></a>
    </div>
    </div>
    </div>

    <div class="table-responsive">
    <table class="table border-0 custom-table comman-table datatable mb-0 table-formatting">
    <thead>
    <tr>
    <th>Name</th>
    <th>Average Duration</th>
    <th>Call patient before booking confirmation</th>
    <th>Doctor to perform the treatments</th>
    <th>Treatment Fees</th>
    <th>Instructions</th>
    <th>Action</th>
    </tr>
    </thead>
    <tbody>
        @if (!empty($data))
        <?php $i=1; ?>
        @foreach ($data as $a)
    <tr>
    <td class="profile-image"><a href="#"><img width="28" height="28" src="{{ asset('adminassets') }}/img/treatments.png" class="rounded-circle m-r-5" alt> {{ $a->treatment_name }}</a></td>
    <td>{{ $a->average_duration }}</td>
    <td>
        @if($a->call_before_confirmation == 1)
        <span class="badge badge-soft-success">Yes</span>
        @else
        <span class="badge badge-soft-success">No</span>
        @endif
    </td>
    <td>Dr. Anuj Grover
    <br>Dr. Dhara Jha</td>
    <td><span class="badge badge-soft-success">₹{{ $a->treatment_fees }}</span></td>
    <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#centermodal-{{ $a->id }}"><i class="fas fa-eye"></i></button></td>
    <td><button type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
    <button type="button" class="btn btn-danger"><i class="fas fa-trash"></i></button></td>
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

    <!-- Instructions modal -->



<div class="modal fade" id="centermodal-{{ $a->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
    <div class="modal-header">
    <h4 class="modal-title" id="myCenterModalLabel">Instructions</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">

    <p>{{ $a->instructions }}</p>
    </div>
    </div>
    </div>
    </div>


        <!-- Instructions modal -->

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


@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>

<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })

</script>
@endsection
@endsection
