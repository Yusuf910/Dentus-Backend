@extends('layouts.admin') 
@section('content')
<div class="content-wrapper">
    <div class="container-full">
       <!-- Content Header (Page header) -->
       <div class="content-header">
          <div class="d-flex align-items-center">
             <div class="mr-auto">
                <h3 class="page-title">{{ $title }}</h3>
             </div>
             <div class=" pull-right">
                <a href="{{ $importurl }}" class="waves-effect waves-light btn btn-md btn-primary"><i class="fa fa-upload mr-15"></i> {{ $importbutton }}</a>
                <a href="{{ $addurl }}" class="waves-effect waves-light btn btn-md btn-primary"><i class="fa fa-plus mr-15"></i> {{ $addbutton }}</a>
             </div>
          </div>
       </div>
       <!-- Main content -->
       <section class="content">
          <div class="row">
             <div class="col-12">
                <div class="box">
                   <!-- /.box-header -->
                   <div class="box-body">
                   	@include('includes.admin.form-success') 
                      <div class="table-responsive">
                         <table id="example" class="table table-bordered-1 table-hover display nowrap margin-top-10 w-p100">
                            <thead>
                               <tr>
                                  <th>#ID</th>
                                  <th>Name</th>
                                  <th>Status</th>
                                  <th>Action</th>
                               </tr>
                            </thead>
                            <tbody>
                            	@if (!empty($data))
                           		<?php $i=1; ?>
                           		@foreach ($data as $a)
	                               <tr>
	                                  <td>{{ $i }}</td>
	                                  <td>{{ $a->name }}</td>
	                                  <td>
	                                  	@if($a->flag == 1)
	                                  	<span class="badge badge-success-light">Active</span></td>
	                                  	@else
	                                  	<span class="badge badge-danger-light">Inactive</span></td>
	                                  	@endif
	                                  	
	                                  <td>
	                                     <a href="{{ route($editurl,$a->id) }}" class="waves-effect waves-light btn btn-warning-light btn-circle mx-5"><span class="icon-Write"><span class="path1"></span><span class="path2"></span></span></a>
	                                     <a href="#" class="waves-effect waves-light btn btn-danger-light btn-circle" href="#" data-toggle="modal" data-target="#modal-danger" data-userid="{{$a->id}}"><span class="icon-Trash1 font-size-18"><span class="path1"></span><span class="path2"></span></span></a>
	                                  </td>
	                               </tr>
	                            <?php $i++; ?>
                            	@endforeach
                            	@endif
                            </tbody>
                            <tfoot>
                               <tr>
                                  <th>#ID</th>
                                  <th>Name</th>
                                  <th>Status</th>
                                  <th>Action</th>
                               </tr>
                            </tfoot>
                         </table>
                      </div>
                   </div>
                   <!-- /.box-body -->
                </div>
                <!-- /.box -->          
             </div>
             <!-- /.col -->
          </div>
          <!-- /.row -->
       </section>
       <!-- /.content -->
    </div>
 </div>
<div class="modal modal-danger fade" id="modal-danger">
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
            <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
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
@section('js_user_page')
    <script>
        $('#modal-danger').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) 
            var user_id = button.data('userid') 
            
            var modal = $(this)
            modal.find('.modal-footer #user_id').val(user_id)
            // modal.find('form').attr('action','permissions/' + user_id);
        })
    </script>

@endsection
@endsection