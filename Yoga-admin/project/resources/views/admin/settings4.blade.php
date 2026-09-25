@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
          <div class="col-12">
             <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Privacy Policy
                </h4>
                <div class="page-title-right">
                   <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                      <li class="breadcrumb-item active"><a href="{{ route('admin.settings.edit',1) }}">Privacy Policy
                      </a></li>
                   </ol>
                </div>
             </div>
          </div>
       </div>
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body p-3">
                      {!! Form::model($settings, ['method' => 'PATCH','enctype'=>'multipart/form-data','route' => ['admin.settings4update', $settings->id]]) !!}
                      @csrf
                      @method('PATCH')
                      @include('includes.admin.form-success')      
                      <div>
                        <textarea id="ckeditor-classic1" name="privacy_policy" class="form-control">{{ old('privacy_policy', htmlspecialchars($settings->privacy_policy, ENT_QUOTES, 'UTF-8')) }}</textarea>
                    </div>                                 
                     <div class="mt-3">
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                     </div>
                     {!! Form::close() !!}
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
   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               <script>document.write(new Date().getFullYear())</script> © Taskaid.
            </div>            
         </div>
      </div>
   </footer>
</div>
</div>
@section('scripts')
<script>
   $(document).ready(function(){
       $('#role_name').on('input', function(e){
        var str = $(this).val();
        str = str.replace(/[\W_]+/g, '-').replace(/^-+|-+$/g, '').toLowerCase();
        $('#role_slug').val(str).attr('placeholder', str);
       });
   });
</script> 
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   ClassicEditor.create(document.querySelector("#ckeditor-classic1"))   
</script>
@endsection
@endsection