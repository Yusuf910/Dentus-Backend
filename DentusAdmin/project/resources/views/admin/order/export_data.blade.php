@extends('layouts.admin')
@section('content')
<?php 
$astrologer_name = '';
$astrologer_mobile = '';
$start_date = '';
$end_date = '';

if (isset($_GET['astrologer_name'])) 
{
   $astrologer_name = $_GET['astrologer_name'];
}

if (isset($_GET['astrologer_mobile'])) 
{
   $astrologer_mobile = $_GET['astrologer_mobile'];
}

if (isset($_GET['start_date'])) 
{
   $start_date = $_GET['start_date'];
}
if (isset($_GET['end_date'])) 
{
   $end_date = $_GET['end_date'];
}
?>

<div class="main-content">
   <div class="page-content">
       <div class="container-fluid">
           <!-- start page title -->
           <div class="row">
               <div class="col-12">
                   <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                     <div class="content-header">
                        <div class="d-flex align-items-center">
                           <div class="mr-auto">
                              <h3 class="page-title">{{ $title }}</h3>
                              <div class="col-md-12" style="margin-bottom: 5px;">
                                 <a href="{{route($exporturl)}}?exp=export{{$astrologer_name != '' ? '&astrologer_name='.$astrologer_name : ''}}{{$astrologer_mobile != '' ? '&astrologer_mobile='.$astrologer_mobile : ''}}{{$start_date != '' ? '&start_date='.$start_date : ''}}{{$end_date != '' ? '&end_date='.$end_date : ''}}" class="btn btn-sm btn-dark">Export Booking</a></span>
                              </div> 


                              <div class="col-md-12" style="margin-bottom: 5px;">
                                 <a href="{{route($exporturl)}}?exp_gift=export_gift{{$astrologer_name != '' ? '&astrologer_name='.$astrologer_name : ''}}{{$astrologer_mobile != '' ? '&astrologer_mobile='.$astrologer_mobile : ''}}{{$start_date != '' ? '&start_date='.$start_date : ''}}{{$end_date != '' ? '&end_date='.$end_date : ''}}" class="btn btn-sm btn-dark">Export Gift</a></span>
                              </div> 
                           </div>
                           
                        </div>
                     </div>
                   </div>
               </div>
           </div>
           <!-- end page title -->
           
           <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-3">
                     <form class="form" action="{{ route('admin.order.export_data') }}" method="GET">
                        <div class="row">


                           <div class="col-md-3">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label"> Date From</label>
                                  <input class="form-control" type="date" value="{{ $start_date }}"
                                      placeholder="Enter date" name="start_date">
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label"> Date To</label>
                                  <input class="form-control" type="date" value="{{ $end_date }}"
                                      placeholder="Enter date" name="end_date">
                              </div>
                          </div>


                          
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Astrolger Name</label>
                                    <select name="astrologer_name" class="form-control select2 js-example-basic-multiple" style="width: 100%;">
                                                    <option value="">Astrolger Name...</option>
                                                    @foreach ($userdata as $p)
                                                        <option value="{{ $p->id }}"
                                                            <?= isset($_GET['astrologer_name']) ? ($_GET['astrologer_name'] == $p->id ? 'selected' : '') : '' ?>>
                                                            {{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                </div>
                            </div>





                            
                           
                        </div>
                        <div>
                           <input type="submit" value="Submit" class="btn btn-primary w-md">
                           <a href="{{ route('admin.order.export_data') }}"><button type="button" class="btn btn-primary w-md">Reset</button></a>
                        </div>
                     </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
        <!-- end row -->
         
           <!-- end row -->
       </div>
       <!-- container-fluid -->
   </div>
   <!-- End Page-content -->
   <footer class="footer">
       <div class="container-fluid">
           <div class="row">
               <div class="col-sm-6">
                   <script>
                   document.write(new Date().getFullYear())
                   </script> © Astrojyotish.
               </div>
               <!-- <div class="col-sm-6"><div class="text-sm-end d-none d-sm-block"></div></div> -->
           </div>
       </div>
   </footer>
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