@extends('layouts.admin')
@section('styles')
@endsection  
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ $listurl }}">{{ $listname }}</a></li>
                     </ol>
                  </div>
                  <div class=" pull-right">
                      <a href="{{ $sampleurl }}" class="waves-effect waves-light btn btn-md btn-primary"><i class="fa fa-download mr-15"></i> {{ $samplebutton }}</a>
                      
                   </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body table-responsive">
                     @include('includes.admin.form-success') 
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
                     <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="box-body">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group row">
                                 <label for="example-text-input" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                                 <div class="col-sm-10">
                                    <input type="file" name="importfile" class="form-control">
                                    <span class="form-text text-danger">*(File Format allowed: csv)</span>
                                 </div>
                              </div>
                              
                           </div>
                           <!-- /.col -->
                        </div>
                        <!-- /.row -->
                     </div>
                     <div class="box-footer">
                        <button type="submit" class="btn btn-rounded btn-primary model_img" alt="alert"  id="sa-success">Submit</button>
                     </div>
                  </div>
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
      <!-- /.modal -->
   </div>
   <!-- End Page-content -->
</div>
@section('js_user_page')
@endsection
@endsection