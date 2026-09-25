@extends('layouts.admin')
@section('content')

<div class="page-wrapper">
    <div class="content">

    <div class="page-header">
    <div class="row">
    <div class="col-sm-12">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Professional Details</a></li>
    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
    <li class="breadcrumb-item active">Add Professional Details</li>
    </ul>
    </div>
    </div>
    </div>

    <div class="row">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="col-sm-12">
    <div class="card">
    <div class="card-body pt-4">
        <form method="POST" action="{{ route('admin.add_professional_store') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
    <div class="row">


    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Experience(Yrs)</label>
    <select name="experience" class="form-control select" required>
        <option value="" disabled selected>Select Experience</option>
        @for ($i = 1; $i <= 10; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>

    </div>
    </div>




    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Language known</label>
    <select name="language_known[]" class="form-control select" multiple required>
        <option value="" disabled>{{ __('Select Language') }}</option>
        <?php $languages = DB::table('master_langauages')->where('status', 1)->get(); ?>
        @if(isset($languages) && count($languages) > 0)
            @foreach ($languages as $p)
                <option value="{{ $p->id }}">{{ htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') }}</option>
            @endforeach
        @endif
    </select>

    </div>
    </div>
    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Specialisations</label>
    <select name="specialisations[]" class="form-control select" multiple required>
        <option value="" disabled>{{ __('Select Specialisations') }}</option>
        <?php $specialisations = DB::table('master_specialsations')->where('status', 1)->get(); ?>
        @if(isset($specialisations) && count($specialisations) > 0)
            @foreach ($specialisations as $p)
                <option value="{{ $p->id }}">{{ htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') }}</option>
            @endforeach
        @endif
    </select>
    </div>
    </div>

    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Services</label>
    <select name="services[]" class="form-control select" multiple required>
        <option value="" disabled>{{ __('Select Services') }}</option>
        <?php $services = DB::table('master_services')->where('status', 1)->get(); ?>
        @if(isset($services) && count($services) > 0)
            @foreach ($services as $p)
                <option value="{{ $p->id }}">{{ htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') }}</option>
            @endforeach
        @endif
    </select>
    </div>
    </div>


    <div class="col-12 col-md-6 col-xl-6">
    <div class="input-block local-forms">
    <label>Hospital Worked In <span class="login-danger">*</span></label>
    <input class="form-control" type="text" name="hospital_worked_in">
    <input class="form-control" type="hidden" name="user_id" value="{{ $user_id }}">
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
