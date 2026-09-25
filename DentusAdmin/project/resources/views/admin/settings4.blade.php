@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">Edit Free Call Config</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Free Call Config</li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
                @include('includes.admin.form-success') 
               <div class="card">
                  <div class="card-body p-3">
                        {!! Form::model($settings, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['admin.settings4update', $settings->id]]) !!}
                        @csrf
                        @method('PATCH')
                     <div class="row">
                        
                     
                        
                        <div class="col-md-12">
                           <div class="alert alert-light" role="alert">
                              Free Call Config
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="refer_inr" class="form-label">Free Call</label>
                              <input class="form-control" type="number" min="0" id="free_call" name="free_call" value="{{$settings->free_call}}">

                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="refer_usd" class="form-label">Free Call Minutes</label>
                              <input class="form-control" type="number"  min="0" id="free_call_minutes" name="free_call_minutes" value="{{$settings->free_call_minutes}}">

                           </div>
                        </div>


                        
                      
                        
                     </div>
                     <div>
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                     </div>
                  </div>
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
   </div>
   <!-- End Page-content -->
</div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
    ClassicEditor.create(document.querySelector("#ckeditor-classic1"))
    ClassicEditor.create(document.querySelector("#ckeditor-classic2"))

</script>
@endsection
@endsection
