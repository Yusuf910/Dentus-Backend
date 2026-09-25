@extends('layouts.admin') 
@section('styles')
<link href="{{asset('project/public/adminassets')}}/css/jquery.Jcrop.css" rel="stylesheet" />
<link href="{{asset('project/public/adminassets')}}/css/Jcrop-style.css" rel="stylesheet" />
@endsection 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">

         <!-- Page Header -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item "><a href="{{ $listurl }}">{{ $listname }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>

         <!-- Main Content -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body p-3">

                     {{-- Validation Errors --}}
                     @if ($errors->any())
                     <div class="alert alert-danger alert-dismissible">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <ul>
                           @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                     @endif

                     {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->_id]]) !!}
                     <div class="row">
                        <div class="col-12">

                           {{-- Name --}}
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $a->name }}" class="form-control" type="text" name="name" placeholder="Enter name">
                              </div>
                           </div>

                           {{-- Image --}}
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Image <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input name="image" class="form-control" type="file" accept="image/*">
                                 @if(!empty($a->image))
                                    <img src="{{ $a->image }}" width="80px" class="mt-2" style="object-fit:cover;">
                                    <a href="{{ $a->image }}" download class="ms-2">
                                       <i class="fa fa-download" aria-hidden="true"></i>
                                    </a>
                                 @endif
                              </div>
                           </div>

                           {{-- Status --}}
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <select name="status" required class="form-select">
                                    <option value="1" {{ $a->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $a->status == 0 ? 'selected' : '' }}>Inactive</option>
                                 </select>
                              </div>
                           </div>

                        </div>

                        {{-- Submit Button --}}
                        <div class="box-footer mt-3">
                           <button type="submit" class="btn btn-rounded btn-primary">Submit</button>
                        </div>

                     </div>
                     {!! Form::close() !!}

                  </div>
               </div>
            </div>
         </div>

      </div>
   </div>
</div>
@endsection
