@extends('layouts.admin')
@section('content')
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Edit User</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);"> User</a></li>
                                <li class="breadcrumb-item active">Edit User</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->id], 'files' => true]) !!}
                        <div class="card-body p-3">
                            <div class="row">
                              <div class="col-md-12">
                               <div class="mb-3">
                                <label for="example-text-input" class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ $a->name }}" type="text">
                            </div>
                        </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Email</label>
                        <input class="form-control" name="email" value="{{ $a->email }}" type="text">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Mobile</label>
                        <input class="form-control" name="mobile" value="{{ $a->mobile }}" type="text">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Dob</label>
                        <input class="form-control" name="dob" value="{{ $a->dob }}" type="text">
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Gender</label>
                        <select name="gender" required class="form-control">
                            <option value="">Select Your Gender..</option>
                               <option @if($a->gender == "Male"  ) selected @endif value="Male">Male</option>
                               <option @if($a->gender == "Female"  ) selected @endif value="Female">Female</option>
                             </select>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Free Calls</label>
                        <input class="form-control" name="free_calls" value="{{ $a->free_calls }}" type="number" min="0">
                    </div>
                </div>


                <?php $tkt = DB::table('ticket_list')->where('user_id',$a->id)->first() ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Ticket Id</label>
                        <input class="form-control" name="ticket_id" value="{{ $a->ticket_comment ?? ''}}" type="text" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="example-text-input" class="form-label">Ticket Commnet</label>
                        <input class="form-control" name="ticket_comment" value="{{ $a->ticket_comment ?? ''}}" type="text" required>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="mb-3">
                     <label for="example-text-input" class="form-label">Status</label>
                     <select class="role form-control" name="status" id="">
                        <option value="">Select Status...</option>
                        <option value="0" <?= ($a->status == 0) ? 'selected' : '' ?>>InActive</option>
                        <option value="1" <?= ($a->status == 1) ? 'selected' : '' ?>>Active</option>
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
</div> <!-- end col -->
</div>

<!-- end row -->

</div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
<footer class="footer">
<div class="container-fluid">
<div class="row">
<div class="col-sm-6">
    <script>document.write(new Date().getFullYear())</script> © Astrolight.
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
            <p>Are you shure you want to delete this?</p>
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
