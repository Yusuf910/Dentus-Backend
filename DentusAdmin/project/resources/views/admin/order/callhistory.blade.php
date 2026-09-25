@extends('layouts.admin')
@section('content')
<?php 
$name = '';
$email = '';
$mobile = '';
$status = '';
if (isset($_GET['name'])) 
{
   $name = $_GET['name'];
}
if (isset($_GET['mobile'])) 
{
   $mobile = $_GET['mobile'];
}
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
                                 <a href="{{route($exporturl)}}?exp=export{{$name != '' ? '&name='.$name : ''}}{{$email != '' ? '&email='.$email : ''}}{{$mobile != '' ? '&mobile='.$mobile : ''}}{{$status != '' ? '&status='.$status : ''}}" class="btn btn-sm btn-dark">Export</a></span>
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
                       <div class="card-body table-responsive">
                           <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                               <thead>
                                 <tr>
                                    <th>#S.No</th>
                                    <th>Astrologer Name</th>  
                                    <th>User Name</th>
                                    <th>Booking Id</th>                                            
                                    <th>Start Time</th>                                            
                                    <th>End Time</th>                                            
                                    <th>Total Seconds</th> 
                                    <th>Duration</th>                                             
                                    <th>Total Call Price</th>        
                                    <th>Total Astrologer Comission</th>        
                                    <th>Added On</th>   
                                 </tr>
                               </thead>
                               <tbody>
                                 @if (!empty($data))
                                 <!--   <?php $i=1; ?>-->
                                 @foreach ($data as $a)
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td><a href="{{ route('admin.astrologers_detail',[1,$a->astrologer->id ?? 0]) }}">{{$a->astrologer->name ?? ''}}</a></td>
                                    <td><ul>
                                          <li>Name: <a href="{{ route('admin.user.users_detail',$a->user->id ?? 0) }}">{{$a->user->name ?? ''}}</a></li>
                                          <li>email: {{$a->user->email ?? ''}}</li>
                                          <li>phone: {{$a->user->phone ?? ''}}</li>
                                          <li>gender: {{$a->user->gender ?? ''}}</li>
                                          <li>Wallet: &#8377; {{$a->user->wallet ?? ''}}</li>
                                       </ul>
                                    </td>
                                    <td>{{$a->id}}</td>
                                    <td>
                                       
                                      
                                       {{$a->schedule_date_time}}</td>
                                    <td>{{$a->end_time}}</td>
                                    <td>{{$a->total_seconds	}}</td>
                                    <td>{{$a->time_minutes}}</td>
                                    <td>{{$a->price_per_mint}}</td>
                                    <td>{{$a->total_astro_comission}}</td>
                                    <td>{{$a->created_at}}1</td>
                                    {{-- <td>{{$a->created_at}}</td> --}}
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