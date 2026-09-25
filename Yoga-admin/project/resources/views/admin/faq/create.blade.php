@extends('layouts.admin')
@section('styles')
<link href="{{asset('project/public/adminassets')}}/css/jquery.Jcrop.css" rel="stylesheet" />
<link href="{{asset('project/public/adminassets')}}/css/Jcrop-style.css" rel="stylesheet" />
@endsection  
@section('content')
<div class="content-wrapper">
   <div class="container-full">
      <!-- Content Header (Page header) -->
      <div class="content-header">
         <div class="d-flex align-items-center">
            <div class="mr-auto">
               <h3 class="page-title">{{ $title }}</h3>
               <div class="d-inline-block align-items-center">
                  <nav>
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a href="{{ $listurl }}">{{ $listname }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                     </ol>
                  </nav>
               </div>
            </div>
         </div>
      </div>
      <!-- Main content -->
      <section class="content">
         <div class="row">
            <div class="col-lg-12 col-12">
               <!-- Basic Forms -->
               <div class="box">
                  <!-- <div class="box-header with-border">
                     <h4 class="box-title">Add Board Name</h4>
                     </div> -->
                  <!-- /.box-header -->
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
                              <label for="example-text-input" class="col-sm-2 col-form-label">Question <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required class="form-control" type="text" id="example-text-input" placeholder="Question" name="question">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">Answer<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <textarea id="description" rows="5" class="form-control" placeholder="answer" spellcheck="false" name="answer"></textarea>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="example-search-input" class="col-sm-2 col-form-label">Status<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <select name="status" required class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <!-- /.col -->
                     </div>
                     <!-- /.row -->
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer">
                     <button type="submit" class="btn btn-rounded btn-primary model_img" alt="alert"  id="sa-success">Submit</button>
                  </div>
               </div>
               <!-- /.box -->       
            </div>
         </div>
      </section>
      <!-- /.content -->
   </div>
</div>
@section('js_user_page')
<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
<script src="{{asset('project/public/adminassets')}}/js/jquery.Jcrop.js"></script>
<script src="{{asset('project/public/adminassets')}}/js/jquery.SimpleCropper.js"></script>
<script>
   $('.cropme').simpleCropper();
   CKEDITOR.replace('description');
</script>
@endsection
@endsection