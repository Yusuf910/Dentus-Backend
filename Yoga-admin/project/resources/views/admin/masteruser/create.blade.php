@extends('layouts.admin')
@section('styles')
<link href="{{asset('project/public/adminassets')}}/css/jquery.Jcrop.css" rel="stylesheet" />
<link href="{{asset('project/public/adminassets')}}/css/Jcrop-style.css" rel="stylesheet" />
@endsection  
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         
         <!-- end page title -->
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
                  <!-- <form method="POST" action="{{ route('admin.master.store_faqstaticfaq') }}" enctype="multipart/form-data"> -->
                    {{ csrf_field() }}
                  <div class="box-body">
                     <div class="row">
                        <div class="col-12">
                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                <input required class="form-control" type="text" id="question" placeholder="Question" name="question">
                              </div>
                           </div>
                           <div class="form-group row">
                              <div class="col-sm-10">
                                <textarea id="htmleditor_data" rows="5" class="form-control" placeholder="answer" spellcheck="false" name="answer"></textarea>
                              </div>
                           </div>
                           <input type="hidden" id="feature_photo" name="photo" value="">
                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">Position<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required name="position" min="1" class="form-control" type="number" id="position" placeholder="eg. 1">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="example-search-input" class="col-sm-2 col-form-label">Status<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <select name="status" id="status" required class="form-control">
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
                  <div class="box-footer">
                     <button onclick="upCpwd();" type="submit" class="btn btn-rounded btn-primary model_img" alt="alert"  id="sa-success">Add</button>
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
<script src="{{asset('/tinymce/js/tinymce/tinymce.min.js')}}"
        referrerpolicy="origin"></script>
<script>
   tinymce.init({
            selector: 'textarea#htmleditor_data',
            plugins: ['image', 'autolink', 'lists', 'autosave', 'code'],
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
               'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
               'forecolor backcolor emoticons | help',
             menu: {
               favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons'}
             },
    });

   function upCpwd() {
      var question = $('#question').val();
      var position = $('#position').val();
      var status = $('#status').val();
      var htmleditor_data = tinyMCE.get('htmleditor_data').getContent()
      
         var dataJson = new FormData();
         dataJson.append("_token", '{{ csrf_token() }}');
         dataJson.append("question", question);
         dataJson.append("answer", htmleditor_data);
         dataJson.append("position", position);
         dataJson.append("status", status);
         $.ajax({
                  type: 'post',
                  url: "{{ route('admin.master.store_faqstaticfaq') }}",
                  data: dataJson,
                  processData: false,
                  contentType: false,
                  async: false,
                  success: function(data) {
                     if (data.status) {
                        alert('success');
                     }
                     else
                     {
                        

                     }
                  }
         });
      
   }

   
</script>
@endsection
@endsection