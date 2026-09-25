@extends('layouts.admin')
@section('content')

<div class="page-wrapper">
    <div class="content">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    <div class="page-header">
    <div class="row">
    <div class="col-sm-12">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Personal Info</a></li>
    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
    <li class="breadcrumb-item active">Add Personal Info</li>
    </ul>
    </div>
    </div>
    </div>

    <div class="row">


    <div class="col-sm-12">
    <div class="card">
    <div class="card-body pt-4">
        {!! Form::model($a, ['method' => 'POST','enctype' => 'multipart/form-data','route' => [$editurl, $a->id]]) !!}
    <div class="row">

    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>First Name <span class="login-danger">*</span></label>
    <input class="form-control" type="text" name="name" value="{{ $a->name }}">
    </div>
    </div>
    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Last Name <span class="login-danger">*</span></label>
    <input class="form-control" type="text" name="last_name" value="{{ $a->last_name }}">
    </div>
    </div>




    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Email <span class="login-danger">*</span></label>
    <input class="form-control" type="text" readonly="" name="email" value="{{ $a->email }}">
    </div>
    </div>

    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Mobile No<span class="login-danger">*</span></label>
    <input class="form-control" type="text" readonly="" name="mobile" value="{{ $a->mobile }}">
    </div>
    </div>

    <div class="col-12 col-md-6 col-xl-6">
        <div class="input-block select-gender">
            <label class="gen-label">Gender<span class="login-danger">*</span></label>
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" name="gender" value="Male" class="form-check-input"
                           {{ (isset($a->gender) && $a->gender === 'Male') ? 'checked' : '' }}> Male
                </label>
            </div>
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" name="gender" value="Female" class="form-check-input"
                           {{ (isset($a->gender) && $a->gender === 'Female') ? 'checked' : '' }}> Female
                </label>
            </div>
        </div>
    </div>



    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Year  of  birth <span class="login-danger">*</span></label>
    <input class="form-control" type="text" name="date_of_birth" value="{{ $a->date_of_birth }}">
    </div>
    </div>

    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-top-form">
    <label class="local-top">Avatar <span class="login-danger">*</span></label>
    <div class="settings-btn upload-files-avator">
    <input type="file" accept="image/*" name="image" id="file" onchange="if (!window.__cfRLUnblockHandlers) return false; loadFile(event)" class="hide-input" data-cf-modified-8ad3ac3c0c659f3e92e759ca->
    <label for="file" class="upload">Choose File</label>
    </div>
    </div>
    </div>


    <div class="col-12">
    <div class="doctor-submit text-start">
    <button type="submit" class="btn btn-primary submit-form me-2">Save</button>
    </div>
    </div>
    </div>
    </form>
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
@endsection
@endsection
