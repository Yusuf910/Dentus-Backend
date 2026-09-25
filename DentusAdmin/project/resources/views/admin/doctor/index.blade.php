@extends('layouts.admin')
@section('content')


<div class="page-wrapper">
    <div class="content">

    <div class="page-header">
    <div class="row">
    <div class="col-sm-12">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Doctors </a></li>
    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
    <li class="breadcrumb-item active">Doctors List</li>
    </ul>
    </div>
    </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
    <div class="row">
    <div class="col-sm-12">
    <div class="card card-table show-entire">
    <div class="card-body">

    <div class="page-table-header mb-2">
    <div class="row align-items-center">
    <div class="col">
    <div class="doctor-table-blk">
    <h3>Doctors List</h3>
    <div class="doctor-search-blk">
    <div class="top-nav-search table-search-blk">
    <form>
    <input type="text" class="form-control" placeholder="Search here">
    <a class="btn"><img src="assets/img/icons/search-normal.svg" alt></a>
    </form>
    </div>
    <div class="add-group">
    <!-- <a href="add-doctor.html" class="btn btn-primary add-pluss ms-2"><img src="assets/img/icons/plus.svg" alt></a> -->
    <a href="javascript:;" class="btn btn-primary doctor-refresh ms-2"><img src="{{ asset('adminassets') }}/assets/img/icons/re-fresh.svg" alt></a>
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
    <th>Name</th>
    <th>Role</th>
    <th>Specialization</th>
    <th>Approved</th>
    <th>Action</th>
    </tr>
    </thead>
    <tbody>
        @if (!empty($data))
        <?php $i=1; ?>
        @foreach ($data as $a)
    <tr>
     @php $main_info = DB::table('user_information')->where('user_id',$a->id)->first(); @endphp
    {{-- <td class="profile-image"><a href="{{ route('admin.doctor_detail',$a->id) }}">
    <img width="28" height="28" src="{{ asset('user/'.$a->image) }}" class="rounded-circle m-r-5" alt> Dr. {{ $a->name }}</a></td> --}}
    <td class="profile-image"><a href="{{ route('admin.doctor_detail',$a->id) }}">
        <img width="28" height="28" src="{{ asset('adminassets') }}/img/user.jpg" class="rounded-circle m-r-5" alt> Dr. {{ $a->name }}</a></td>
    @if($a->parent_id == 0)
    <td><span class="badge badge-soft-success">Head</span></td>
    @else
    <td><span class="badge badge-soft-success">Team</span></td>
    @endif

    @php
    // Check if $main_info is not null and has the specialisations property
    if ($main_info && $main_info->specialisations) {
        $spec = explode('|', $main_info->specialisations);
        $specla = DB::table('master_specialsations')
            ->whereIn('id', $spec) // Use whereIn to find multiple IDs
            ->first(); // Get only the first record
    } else {
        $specla = null; // Set to null if $main_info is null or has no specialisations
    }
 @endphp

    @if ($specla)
        <td>{{ $specla->name }}</td>
    @else
        <td></td> <!-- Optionally display something if $specla is null -->
    @endif

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
    </td>


    <td>
        <a href="{{ route('admin.doctor_detail',$a->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>
    </td>
</tr>
    <?php $i++; ?>
    @endforeach
    @endif

    {{-- @php $team = DB::table('users')->where('parent_id',auth()->user()->id)->get();  @endphp
    @foreach ($team as $item)
    @php
    $user_info = DB::table('user_information')->where('user_id', $item->id)->first();
    @endphp
    <tr>
    <td class="profile-image"><a href=""><img width="28" height="28" src="{{ asset('user') }}/{{$item->image}}" class="rounded-circle m-r-5" alt> Dr. {{ $item->name }}</a></td>
    <td><span class="badge badge-soft-success">Team</span></td>
    @php
    if($user_info->specialisations){
    $spec = explode('|', $user_info->specialisations);
    $specla = DB::table('master_specialsations')
        ->whereIn('id', $spec) // Use whereIn to find multiple IDs
        ->first(); // Get only the first record
    }
    @endphp

    @if ($specla)
        <td>{{ $specla->name ?? ''}}</td>
        @else
        <td></td>
    @endif
    <td>
        <a href="{{ route('user.doctor_detail',$item->id) }}" class="btn btn-primary"><i class="fas fa-eye"></i></a>

        <!-- <button type="button" class="btn btn-danger"><i class="fas fa-trash"></i></button> -->
    </td>

    </tr>
    @endforeach --}}



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
@endsection
@endsection
