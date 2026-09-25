@extends('layouts.admin')
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title}}</h4>
                  <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ $listurl }}">{{ $listname }}</a></li>
                        <li class="breadcrumb-item active">{{ $title}}</li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                     @csrf
                     <div class="card-body p-3">
                        <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" type="text" placeholder="Enter Name" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" placeholder="Enter Email"
                                    pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-zA-Z]{2,3}$" required>
                                <span class="text-danger" id="emailErrorMsg"></span>
                            </div>
                        </div>

                        <!-- Mobile -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="number" name="mobile" class="form-control" id="mobile-num" placeholder="Mobile Number"
                                    maxlength="16" onkeypress="return onlyNumberKey(event)" pattern="[789][0-9]{9}"
                                    oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        </div>
                        <div>
                           <button type="submit" class="btn btn-primary w-md">Submit</button>
                        </div>
                     </div>
                  </form>
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
               <script>document.write(new Date().getFullYear())</script> © Yoga.
            </div>
            <!-- <div class="col-sm-6">
               <div class="text-sm-end d-none d-sm-block">
               
               </div>
               </div> -->
         </div>
      </div>
   </footer>
</div>
<div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
   <div class="modal-dialog">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h4 class="modal-title">Delete</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you sure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form method="POST" action="" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="user_id" name="id" value="">
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
@section('scripts')
<script>
   function onlyNumberKey(evt) {
         
       // Only ASCII character in that range allowed
       var ASCIICode = (evt.which) ? evt.which : evt.keyCode
       if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
           return false;
       return true;
   }
</script> 
<script>
   $('#modal-danger').click('show.bs.modal', function (event) {
       var user_id = $(this).attr('data-userid')
       $('#user_id').val(user_id);
   })
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
</script>
@endsection
@endsection