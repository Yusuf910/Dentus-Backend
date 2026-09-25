@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Doctors </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Send User Notification</li>
               </ul>
            </div>
         </div>
      </div>
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
         {{ session('success') }}
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      @endif
      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
         {{ session('error') }}
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      @endif
      <div class="row">
         <div class="col-sm-12">
            <div class="card card-table show-entire">
               <div class="card-body p-4">
                  <form method="POST" action="{{ route('admin.notification.smt_notification')}}" enctype="multipart/form-data">
                     @csrf
                     <div class="row">
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Name</label>
                              <input type="text" name="id" id="uid" hidden>
                              <input type="text" name="mobile" id="mid" hidden>
                              <input type="text" autocomplete="off" id="search-box" name="name" value="" class="form-control"  placeholder="User Name.." required>
                              <div id="suggesstion-box" class="" >
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Title</label>
                              <input type="text" name="title" value="" class="form-control"  placeholder="Notification Title.." required>
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Message</label>
                              <textarea name="body" class="form-control" placeholder="Place your message here.."></textarea>
                           </div>
                        </div>
                        <!-- <div class="col-md-6">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Image</label>
                              <input type="file" class="form-control"  accept="image/*" name="image">
                           </div>
                        </div> -->
                     </div>
                     <div>
                        <button type="submit" class="btn btn-primary w-md">Submit</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
   $(document).ready(function(){
     $("#search-box").on('keyup',function(){
       var value=$(this).val();
       var key="";
       $.ajax({
           url:"{{ route('admin.notification.send_notification_to_user') }}",
           method:"GET",
           data:{'name':value},
           success:function(data){
             $("#suggesstion-box").html(data);
             $("#suggesstion-box").css({"overflow": "scroll", "max-height": "300px"});
           }
   
       });
     });
   
     $(document).on('click','tr',function(){
       var value=$(this).find("#name_val").text();
       var uid = $(this).find("#name_id").text();
       var mob = $(this).find("#mob").text();
       // var value=$("#name_val-"+key).text();
       $("#search-box").val(value);
       $("#uid").val(uid);
       $("#mid").val(mob);
       $("#suggesstion-box").html("");
     })
   
     $(document).on('click',document,function(){
       // var value=$(this).text();
        $("#search-box").html("");
       $("#suggesstion-box").html("");
       $("#suggesstion-box").css({"overflow": "none", "max-height": "0"});
     })
   
   });
</script>
<script>
   $('.sddel').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })
   
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@endsection
@endsection