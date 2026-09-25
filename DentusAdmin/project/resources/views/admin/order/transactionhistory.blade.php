@extends('layouts.admin')
@section('content')
<?php 
$name = '';
$email = '';
$user_mobile  = '';
$status = '';
if (isset($_GET['name'])) 
{
   $name = $_GET['name'];
}
if (isset($_GET['user_mobile'])) 
{
   $user_mobile = $_GET['user_mobile'];
}


// print_r($user_mobile); die;
if (isset($_GET['email'])) 
{
   $email = $_GET['email'];
}
if (isset($_GET['status'])) 
{
   $status = $_GET['status'];
}
$start_date = '';
$end_date = '';
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
                                 <a href="{{route($exporturl)}}?exp=export{{$name != '' ? '&name='.$name : ''}}{{$user_mobile != '' ? '&user_mobile='.$user_mobile : ''}}{{$start_date != '' ? '&start_date='.$start_date : ''}}{{$end_date != '' ? '&end_date='.$end_date : ''}}" class="btn btn-sm btn-dark">Export</a></span>
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
                     <form class="form" action="{{ route('admin.order.transactionhistory') }}" method="GET">
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
                                  <label for="example-text-input" class="form-label">User Mobile</label>
                                  <input class="form-control" type="number" value="{{ $user_mobile }}"
                                      placeholder="Enter User Mobile" name="user_mobile">
                              </div>
                          </div>




                            
                           
                        </div>
                        <div>
                           <input type="submit" value="Submit" class="btn btn-primary w-md">
                           <a href="{{ route('admin.order.transactionhistory') }}"><button type="button" class="btn btn-primary w-md">Reset</button></a>
                        </div>
                     </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
        <!-- end row -->
         <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">
                           <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                               <thead>
                                 <tr>
                                    <th>#S.No</th>
                                    <th>Txn Name</th>  
                                    <th>User Name</th>
                                    <th>Booking Txn Id</th>                                            
                                    <th>Payment Mode</th>                                            
                                    <th>Status</th>                                            
                                    <th>Txn For</th>                                            
                                    <th>Type</th> 
                                    <th>Old Wallet</th>                                             
                                    <th>Txn Amount</th>  
                                    <th>Message</th>  
                                    <th>Update Wallet</th>  
                                    <th>Created At</th>        
                                 </tr>
                               </thead>
                               <tbody>
                                 @if (!empty($data))
                                 <!--   <?php $i=1; ?>-->
                                 @foreach ($data as $a)
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$a->txn_name}}</td>
                                    <td><ul>
                                          <li>Name: <a href="">{{$a->user->name ?? ''}}</a></li>
                                          <!-- <li>email: {{$a->user->email ?? ''}}</li> -->
                                          <li>phone: {{$a->user->mobile ?? ''}}</li>
                                          <!-- <li>gender: {{$a->user->gender ?? ''}}</li> -->
                                          <li>Wallet: &#8377; {{$a->user->wallet ?? ''}}</li>
                                       </ul>
                                    </td>
                                    <td>{{$a->booking_txn_id}}</td>
                                    <td>{{$a->payment_mode}}</td>

                                    <td>

                                    @if($a->status == 1)
                                    <span class="btn btn-primary btn-sm">Success</span>
                     
                                    @else
                                    <span class="btn btn-danger btn-sm">Failed</span>
                                    @endif
                                    
                                    </td>


                                    <td>{{$a->txn_for}}</td>
                                    <td>{{$a->type}}</td>
                                    <td>{{$a->old_wallet}}</td>
                                    <td>{{$a->txn_amount}}</td>
                                    <td>{{$a->message}}</td>
                                    <td>{{$a->update_wallet}}</td>
                                    <td>{{$a->created_at}}</td>
                                 </tr>
                                 <!--  <?php $i++; ?>-->
                                 @endforeach
                                 @endif
                              </tbody>
                           </table>
                           {!! $data->links(); !!}
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