@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ $title }}</a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active"> Staff List</li>
               </ul>
            </div>
         </div>
      </div>
      @if (count($errors) > 0)
      <div class="alert alert-danger alert-dismissible">
         <strong>Whoops!</strong> There were some problems with your input.<br><br>
         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
         <h4><i class="icon fa fa-ban"></i> Alert!</h4>
         <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
         </ul>
      </div>
      @endif
      <div class="row">
         <div class="col-sm-12">
             <div class="card">
                 <div class="card-body pt-4">
                     {!! Form::model($a, ['method' => 'PATCH', 'enctype' => 'multipart/form-data', 'route' => [$editurl, $a->id]]) !!}
                     {{ csrf_field() }}
     
                     <div class="row">
                         {{-- <input type="hidden" name="parent_id" value="{{ $id }}"> --}}
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>First Name <span class="login-danger">*</span></label>
                                 <input type="text" required class="form-control" name="name" value="{{ $a->name ?? '' }}">
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Last Name <span class="login-danger">*</span></label>
                                 <input type="text" required class="form-control" name="lname" value="{{ $a->last_name ?? '' }}">
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Password</label>
                                 <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Email <span class="login-danger">*</span></label>
                                 <input type="email" required class="form-control" name="email" value="{{ $a->email ?? '' }}">
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Mobile <span class="login-danger">*</span></label>
                                 <input type="text" required class="form-control" name="mobile" value="{{ $a->mobile ?? '' }}">
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>DOB <span class="login-danger">*</span></label>
                                 <input type="date" required max="{{ date('Y-m-d') }}" class="form-control" name="date_of_birth" value="{{ $a->date_of_birth ?? '' }}">
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Image</label>
                                 <input type="file" class="form-control" name="image" accept="image/*">
                                 @if ($a->image)
                                     <div class="mt-2">
                                         <img src="{{ asset('content/doctor/' . $a->image) }}" alt="Staff Image" width="100">
                                     </div>
                                 @endif
                             </div>
                         </div>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Gender <span class="login-danger">*</span></label>
                                 <select name="gender" required class="form-control">
                                     <option value="Male" {{ $a->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                     <option value="Female" {{ $a->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                 </select>
                             </div>
                         </div>
                         <?php
                         $data = DB::table('master_langauages')->where('status', 1)->orderBy('name', 'ASC')->get();
                         $selectedLanguages = explode('|', $a->userInformation->language_known ?? '');
                         ?>
                         <div class="col-12 col-md-6 col-xl-6">
                             <div class="input-block local-forms">
                                 <label>Language <span class="login-danger">*</span></label>
                                 <select name="language_known[]" class="form-control" multiple required>
                                     @foreach ($data as $aa)
                                         <option value="{{ $aa->id }}" {{ in_array($aa->id, $selectedLanguages) ? 'selected' : '' }}>
                                             {{ $aa->name ?? '' }}
                                         </option>
                                     @endforeach
                                 </select>
                             </div>
                         </div>
                         <div class="col-12">
                             <div class="doctor-submit text-start">
                                 <button type="submit" class="btn btn-primary submit-form me-2">Update</button>
                             </div>
                         </div>
                     </div>
                     {!! Form::close() !!}
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
@endsection