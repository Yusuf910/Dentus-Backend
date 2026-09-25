@extends('layouts.admin')
@section('content')

<div class="page-wrapper">
    <div class="content">
    <div class="row">
    <div class="col-sm-12">
    <ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Doctors </a></li>
    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
    <li class="breadcrumb-item active">Doctor Profile</li>
    </ul>
    </div>

    </div>
    <div class="card-box profile-header">
    <div class="row">
    <div class="col-md-12">
    <div class="profile-view">
    <div class="profile-img-wrap">
    <div class="profile-img">
    <a href="#"><img class="avatar" src="{{ asset('adminassets') }}/img/doctor-03.jpg" alt></a>
    </div>
    </div>
    <div class="profile-basic">
    <div class="row">
    <div class="col-md-5">
    <div class="profile-info-left">
    <h3 class="user-name m-t-0 mb-1">Dr. {{ $a->name ?? ''}}</h3>
    {{-- <p class="text-muted">{{ $userInfo->specialisations ?? ''}}</p> --}}
    @php
    // Check if $userInfo is not null and has the specialisations property
    if ($userInfo && !empty($userInfo->specialisations)) {
        // Split the specialisations string using the pipe character
        $specialisations = explode('|', $userInfo->specialisations);
        $lan = DB::table('master_specialsations')
            ->whereIn('id', $specialisations) // Use whereIn to find multiple IDs
            ->get();
    } else {
        $lan = collect(); // Create an empty collection if $userInfo is null or specialisations is empty
    }
   @endphp

    @if ($lan->isNotEmpty())
    @foreach ($lan as $lang)
        <p> {{ $lang->name }}</p>
    @endforeach
    @else
    <p>No specializations available</p> <!-- Message if no specializations are found -->
    @endif
   {{--<div class="progress progress-sm w-75">
      <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="staff-id">50% Profile Completed</div>
    {{-- <div class="staff-msg"><a href="{{ route('admin.edit_doctor_detail',$a->id) }}" class="btn btn-primary">Edit</a></div> --}}
    </div>
    </div>
    <div class="col-md-7">
    <ul class="personal-info">
    @php
    // Check if $userInfo is not null
    if ($userInfo && $userInfo->language_known) {
        $languages = explode('|', $userInfo->language_known);
        $lang = DB::table('master_langauages') // Ensure correct spelling of 'languages'
            ->whereIn('id', $languages) // Use whereIn to find multiple IDs
            ->get();
    } else {
        $lang = collect(); // Create an empty collection if $userInfo is null or has no languages
    }
  @endphp

    <li>
        <span class="title">Language:</span>
        @if ($lang->isNotEmpty())
        @foreach ($lang as $language)
            <span style="font-weight: 500;">{{ $language->name }},</span>
        @endforeach
        @else
            <span class="text">No languages known</span>
        @endif
        </li>
<li>
    <span class="title">Experience:</span>
    <span class="text">{{ !empty($userInfo->experience) ? $userInfo->experience : 'No Experience' }}</span>
</li>
<li>
    <span class="title">Gender:</span>
    <span class="text">{{ !empty($a->gender) ? $a->gender : 'No Gender' }}</span>
</li>
    </ul>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div class="profile-tabs mt-3">
    <ul class="nav nav-tabs nav-tabs-bottom">
    <li class="nav-item"><a class="nav-link active" href="#about-cont" data-bs-toggle="tab">About</a></li>
    <li class="nav-item"><a class="nav-link" href="#bottom-tab2" data-bs-toggle="tab">Professional</a></li>
    <li class="nav-item"><a class="nav-link" href="#bottom-tab3" data-bs-toggle="tab">Education</a></li>
    <li class="nav-item"><a class="nav-link" href="#bottom-tab4" data-bs-toggle="tab">Awards</a></li>
    <li class="nav-item"><a class="nav-link" href="#bottom-tab5" data-bs-toggle="tab">Medical Registration</a></li>
    <li class="nav-item"><a class="nav-link" href="#bottom-tab6" data-bs-toggle="tab">Timings</a></li>
    <li class="nav-item"><a class="nav-link" href="#bottom-tab7" data-bs-toggle="tab">Review</a></li>
    </ul>
    <div class="tab-content">

    <div class="tab-pane show active" id="about-cont">

    <div class="row">
    <div class="col-md-12">
    <div class="card-box">
    <div class="d-flex justify-content-center align-items-center mb-3">
    <div class="w-100"><h3 class="card-title mb-0">About</h3></div>
    @php $UserInformation = DB::table('user_information')->where('user_id',$a->id)->first(); @endphp
    {{-- <div class="flex-shrink-1">
        @if($UserInformation)
        <a href="{{ route('admin.about_dr',$a->id) }}" class="btn btn-primary">Edit</a>
        @else
       <a href="{{ route('admin.about_create',$a->id) }}" class="btn btn-primary">Add</a>
       @endif
    </div> --}}

    </div>


    <p>{{ $userInfo->about ?? ''}}</p>

    </div>
    </div>
    </div>
    </div>


    <div class="tab-pane" id="bottom-tab2">
    <div class="row">

    <div class="col-md-12">
    <div class="card-box">

    <div class="d-flex justify-content-center align-items-center mb-3">
    <div class="w-100"><h3 class="card-title mb-0">Professional</h3></div>
    {{-- <div class="flex-shrink-1">
        @if($UserInformation)
        <a href="{{ route('admin.professional_edit',$a->id) }}" class="btn btn-primary">Edit</a>
        @else
        <a href="{{ route('admin.add_professional',$a->id) }}" class="btn btn-primary">Add</a>
        @endif
    </div> --}}
    </div>
    <div class="row">
        @php
    // Check if $userInfo is not null and has the specialisations property
    if ($userInfo && !empty($userInfo->specialisations)) {
        // Split the specialisations string using the pipe character
        $specialisations = explode('|', $userInfo->specialisations);
        $lan = DB::table('master_specialsations')
            ->whereIn('id', $specialisations) // Use whereIn to find multiple IDs
            ->get();
    } else {
        $lan = collect(); // Create an empty collection if $userInfo is null or specialisations is empty
    }
@endphp

<div class="col-md-4">
    <h4 class="card-title mb-3">Specializations</h4>
    <ul class="spec">
        @if ($lan->isNotEmpty())
            @foreach ($lan as $lang)
                <li><i class="fas fa-check-circle color-1"></i> {{ $lang->name }}</li>
            @endforeach
        @else
            <li>No specializations available</li> <!-- Message if no specializations are found -->
        @endif
    </ul>
</div>

@php
// Check if $userInfo is not null and has the services property
if ($userInfo && !empty($userInfo->services)) {
    // Split the services string using the pipe character
    $services = explode('|', $userInfo->services);
    $ser = DB::table('master_services')
        ->whereIn('id', $services) // Use whereIn to find multiple IDs
        ->get();
} else {
    $ser = collect(); // Create an empty collection if $userInfo is null or services is empty
}
@endphp

<div class="col-md-4">
<h4 class="card-title mb-3">Services</h4>
<ul class="spec">
    @if ($ser->isNotEmpty())
        @foreach ($ser as $serv)
            <li><i class="fas fa-check-circle color-1"></i> {{ $serv->name }}</li>
        @endforeach
    @else
        <li>No services available</li> <!-- Message if no services are found -->
    @endif
</ul>
</div>


     <div class="col-md-4">
           <h4 class="card-title mb-3">Hospital Worked In</h4>
    <p>{{ $userInfo->hospital_worked_in ?? ''}}</p>
    </div>
    </div>

    </div>

    </div>

    </div>
    </div>

    <div class="tab-pane" id="bottom-tab3">
    <div class="row">
    <div class="col-md-12">
    <div class="card-box">
            <div class="d-flex justify-content-center align-items-center mb-3">
    <div class="w-100"><h3 class="card-title mb-0">Education</h3></div>
    {{-- <div class="flex-shrink-1">
        @if($UserInformation)
        <a href="{{ route('admin.education_edit',$a->id) }}" class="btn btn-primary">Edit</a>
        @else
        <a href="{{ route('admin.create_education',$a->id) }}" class="btn btn-primary">Add</a>
        @endif
    </div> --}}
    </div>

    <div class="experience-box">
    <ul class="experience-list">
    <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
        {{-- @php
        if (!empty($userInfo->college_institute)) {
            $college = DB::table('master_college_institutes')->where('id', $userInfo->college_institute)->first();
        }
    @endphp --}}

    <a href="#" class="name">{{ $userInfo->college_institute ?? ''}}</a>
    {{-- @php
    if (!empty($userInfo->degree)) {
        $degree = DB::table('master_degrees')->where('id', $userInfo->degree)->first();
    }
   @endphp --}}
    <div>{{ $userInfo->degree ?? ''}}</div>
    <span class="time">{{ $userInfo->year_of_completion ?? ''}}</span>
    </div>
    </div>
    </li>
    {{-- <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
    <a href="#/" class="name">International College of Medical Science (PG)</a>
    <div>MD - Obstetrics & Gynaecology</div>
    <span class="time">1997 - 2001</span>
    </div>
    </div>
    </li> --}}
    </ul>
    </div>
    </div>

    </div>
    </div>
    </div>

    <div class="tab-pane" id="bottom-tab4">
    <div class="row">
    <div class="col-md-12">
    <div class="card-box">

    <div class="d-flex justify-content-center align-items-center mb-3">

    <div class="w-100"><h3 class="card-title mb-0">Awards</h3></div>
    {{-- <div class="flex-shrink-1">
        @if($UserInformation)
        <a href="{{ route('admin.award',$a->id) }}" class="btn btn-primary">Edit</a>
        @else
        <a href="{{ route('admin.award_create',$a->id) }}" class="btn btn-primary">Add</a>
        @endif
    </div> --}}

    </div>

    <div class="experience-box">
    <ul class="experience-list">
    <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
    <a href="#/" class="name">{{ $userInfo->awards_name ?? ''}}</a>
    {{-- @php
    if (!empty($userInfo->award_college_institute)) {
        $college = DB::table('master_college_institutes')->where('id', $userInfo->award_college_institute)->first();
    }
   @endphp --}}
    <div>{{ $userInfo->award_college_institute ?? ''}}</div>
    <span class="time">{{ $userInfo->award_year ?? ''}}</span>
    </div>
    </div>
    </li>
    </ul>
    </div>
    </div>

    </div>
    </div>
    </div>


    <div class="tab-pane" id="bottom-tab5">
    <div class="row">
    <div class="col-md-12">
    <div class="card-box">
            <div class="d-flex justify-content-center align-items-center mb-3">
    <div class="w-100"><h3 class="card-title mb-0">Medical Registration</h3></div>
    {{-- <div class="flex-shrink-1">
        @if($UserInformation)
        <a href="{{ route('admin.medical_regis_edit',$a->id) }}" class="btn btn-primary">Edit</a>
        @else
        <a href="{{ route('admin.medical_regis_create',$a->id) }}" class="btn btn-primary">Add</a>
        @endif
    </div> --}}
    </div>

    <ul class="personal-info">


    <li>
    <span class="title">Registration Number</span>
    <span class="text">{{ $userInfo->registration_number ?? '-'}}</span>
    </li>
    <li>
    <span class="title">Registration Council</span>
    {{-- @php
    if (!empty($userInfo->registration_council)) {
        $register = DB::table('master_registraion_councils')->where('id', $userInfo->registration_council)->first();
    }
   @endphp --}}
    <span class="text">{{ $userInfo->registration_council ?? '-'}}</span>
    </li>
    <li>
    <span class="title">Registration Year</span>
    <span class="text">{{ $userInfo->registration_year ?? '-'}}</span>
    </li>
    </ul>
    </div>

    </div>
    </div>
    </div>


    <div class="tab-pane" id="bottom-tab6">
    <div class="row">
    <div class="col-md-12">
    <div class="card-box">
            <div class="d-flex justify-content-center align-items-center mb-3">
    <div class="w-100"><h3 class="card-title mb-0">Timings</h3></div>
    <div class="flex-shrink-1">
        {{-- @if($UserInformation)
        <a href="{{ route('admin.timing_dr_edit',$a->id) }}" class="btn btn-primary">Edit</a>
        @else
        <a href="{{ route('admin.timing_dr_create',$a->id) }}" class="btn btn-primary">Add</a>
        @endif --}}
    </div>
    </div>

    <div class="experience-box">
    <ul class="experience-list">
        <li>
            <div class="experience-user">
                <div class="before-circle"></div>
            </div>
            {{-- @php
            // Check if $userInfo is not null and has the doctor_availability property
            if ($userInfo && !empty($userInfo->doctor_availability)) {
                $jsonSchedule = $userInfo->doctor_availability;
                $schedule = json_decode($jsonSchedule, true);
            } else {
                $schedule = null; // Set to null if $userInfo is null or doctor_availability is empty
            }
        @endphp

<div class="experience-content">
    <div class="timeline-content">
        @if ($schedule && isset($schedule['schedule']))
            @foreach ($schedule['schedule'] as $day => $times)
                <h4>{{ ucfirst($day) }}</h4>
                <ul>
                    @if (!empty($times['start']) && !empty($times['end']))
                        <li>
                            <span class="time-box">Start: {{ $times['start'] }}</span> -
                            <span class="time-box">End: {{ $times['end'] }}</span>
                        </li>
                    @else
                        <li>No available slots for {{ ucfirst($day) }}.</li>
                    @endif
                </ul>
            @endforeach
        @else
            <p>No schedule available.</p>
        @endif
    </div>
</div> --}}
@php
    // Check if $userInfo is not null and has the doctor_availability property
    if ($userInfo && !empty($userInfo->doctor_availability)) {
        $jsonSchedule = $userInfo->doctor_availability;
        $schedule = json_decode($jsonSchedule, true);
    } else {
        $schedule = null; // Set to null if $userInfo is null or doctor_availability is empty
    }
@endphp

<div class="experience-content">
    <div class="timeline-content">
        @if ($schedule && isset($schedule['schedule']))
            @foreach ($schedule['schedule'] as $day => $times)
                <h4>{{ ucfirst($day) }}</h4>
                <ul>
                    @if (!empty($times['morning']))
                        <li>
                            <span class="time-box">Morning: {{ implode(', ', $times['morning']) }}</span>
                        </li>
                    @else
                        <li>No morning slots for {{ ucfirst($day) }}.</li>
                    @endif

                    @if (!empty($times['evening']))
                        <li>
                            <span class="time-box">Evening: {{ implode(', ', $times['evening']) }}</span>
                        </li>
                    @else
                        <li>No evening slots for {{ ucfirst($day) }}.</li>
                    @endif
                </ul>
            @endforeach
        @else
            <p>No schedule available.</p>
        @endif
    </div>
</div>



        </li>

    {{-- <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
    <a href="#/" class="name">Monday</a>
    <br>
    <span class="time-box">2:00PM - 3:00PM</span>
    <span class="time-box">2:00PM - 3:00PM</span>

    </div>
    </div>
    </li>
    <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
    <a href="#/" class="name">Monday</a>
    <br>
    <span class="time-box">2:00PM - 3:00PM</span>
    <span class="time-box">2:00PM - 3:00PM</span>

    </div>
    </div>
    </li>
    <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
    <a href="#/" class="name">Monday</a>
    <br>
    <span class="time-box">2:00PM - 3:00PM</span>
    <span class="time-box">2:00PM - 3:00PM</span>

    </div>
    </div>
    </li>

    <li>
    <div class="experience-user">
    <div class="before-circle"></div>
    </div>
    <div class="experience-content">
    <div class="timeline-content">
    <a href="#/" class="name">Monday</a>
    <br>
    <span class="time-box">2:00PM - 3:00PM</span>
    <span class="time-box">2:00PM - 3:00PM</span>

    </div>
    </div>
    </li> --}}

    </ul>
    </div>

    </div>

    </div>
    </div>
    </div>


    <div class="tab-pane" id="bottom-tab7">
    <div class="row">
    <div class="col-md-12">
    <div class="card-box">
    <h3 class="card-title">Rating</h3>

    </div>

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
@endsection
@endsection
