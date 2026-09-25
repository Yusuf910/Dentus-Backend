@extends('layouts.admin') 
@section('content')
<?php 
$name = '';
$astro_name = '';
$email = '';
$user_mobile  = '';
$astro_mobile  = '';
$status = '';
if (isset($_GET['name'])) 
{
   $name = $_GET['name'];
}
if (isset($_GET['astro_name'])) 
{
   $astro_name = $_GET['astro_name'];
}
if (isset($_GET['user_mobile'])) 
{
   $user_mobile = $_GET['user_mobile'];
}
if (isset($_GET['astro_mobile'])) 
{
   $astro_mobile = $_GET['astro_mobile'];
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
                  <h4 class="mb-sm-0 font-size-18">{{$title}}
                  </h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <!-- <a href="#" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i>
                        </a>  -->
                         <form method="GET" action="" enctype="multipart/form-data">
                           <span>
                          <input type="submit" name="export_file" value="Export" class="btn btn-primary waves-effect waves-light" >
                          </span>
                           </form>
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
                     <form class="form" action="{{ route('admin.master.tickethistory') }}" method="GET">
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
                                    <label for="example-text-input" class="form-label">Name</label>
                               
                                    <input class="form-control" type="text" value="{{$name}}" placeholder="Enter Name" name="name">
                                </div>
                            </div>

                            {{-- <div class="col-md-3">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label">Astrologer Name</label>
                             
                                  <input class="form-control" type="text" value="{{$astro_name}}" placeholder="Enter Name" name="astro_name">
                              </div>
                          </div> --}}


                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">User number</label>
                                    <input class="form-control" type="text" value="{{$user_mobile}}" placeholder="Enter Phone" name="user_mobile">
                                </div>
                            </div>

                            <div class="col-md-3">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label">Astrologer number</label>
                                  <input class="form-control" type="text" value="{{$astro_mobile}}" placeholder="Enter Phone" name="astro_mobile">
                              </div>
                          </div>


                            <!-- <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Phone number</label>
                                    <input class="form-control" type="text" value="{{$name}}" placeholder="Enter Name" name="name">
                                </div>
                            </div> -->





                        </div>
                        <div>
                           <input type="submit" value="Submit" class="btn btn-primary w-md">
                           <a href="{{ route('admin.master.tickethistory') }}"><button type="button" class="btn btn-primary w-md">Reset</button></a>
                        </div>
                     </form>
                    </div>
                  <div class="card-body table-responsive">
                    @include('includes.admin.form-success') 
                     <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                        <thead>
                           <tr>
                              <th>#ID</th>
                              <th>Admin</th>
                              <th>User Type</th>
                              <th>Name</th>
                              <th>Phone number</th>
                              <th>Type</th>
                              <th>Ticket id</th>
                              <th>Ticket Comment</th>
                              <th>Created At</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i=1; ?>
                           @foreach ($data as $a)
                           <tr>
                            <?php $ad = DB::table('admins')->where('id',$a->admin_id)->first(); ?>
                              <td>{{ $i }}</td>
                              <td>{{ $ad->name }}</td>

                              <td>
                               @if($a->type == 1)
                               <?php $fr = DB::table('astrologers')->where('id',$a->user_id)->first(); 
                                 ?>
                               Astrologer
                                @elseif($a->type == 2)
                                <?php $scd = DB::table('astrologers')->where('id',$a->user_id)->first(); 
                               ?>
                                 Astrologer
                                @elseif($a->type == 3)
                                <?php $thr = DB::table('users')->where('id',$a->user_id)->first();?>
                              User
                                @elseif($a->type == 4)
                                <?php $fur = DB::table('users')->where('id',$a->user_id)->first();?>
                                User
                                @elseif($a->type == 5)
                                <?php $fif = DB::table('users')->where('id',$a->user_id)->first();?>
                                User
                                @endif
                              </td>


                              <td>
                               @if($a->type == 1)
                               <?php $fr = DB::table('astrologers')->where('id',$a->user_id)->first(); 
                               $na = json_decode($fr->name,true);?>
                                {{ $na[1]}}
                                @elseif($a->type == 2)
                                <?php $scd = DB::table('astrologers')->where('id',$a->user_id)->first(); 
                                $sc = json_decode($scd->name,true);?>
                                {{ $sc[1] ?? ''}}
                                @elseif($a->type == 3)
                                <?php $thr = DB::table('users')->where('id',$a->user_id)->first();?>
                                {{ $thr->name }}
                                @elseif($a->type == 4)
                                <?php $fur = DB::table('users')->where('id',$a->user_id)->first();?>
                                {{ $fur->name }}
                                @elseif($a->type == 5)
                                <?php $fif = DB::table('users')->where('id',$a->user_id)->first();?>
                                {{ $fif->name }}
                                @endif
                              </td>


                              <td>
                               @if($a->type == 1)
                               <?php $fr = DB::table('astrologers')->where('id',$a->user_id)->first(); 
                                ?>
                                {{ $fr->mobile }}
                                @elseif($a->type == 2)
                                <?php $scd = DB::table('astrologers')->where('id',$a->user_id)->first(); 
                                ?>
                                {{$scd->mobile}}
                                @elseif($a->type == 3)
                                <?php $thr = DB::table('users')->where('id',$a->user_id)->first();?>
                                {{ $thr->mobile }}
                                @elseif($a->type == 4)
                                <?php $fur = DB::table('users')->where('id',$a->user_id)->first();?>
                                {{ $fur->mobile }}
                                @elseif($a->type == 5)
                                <?php $fif = DB::table('users')->where('id',$a->user_id)->first();?>
                                {{ $fif->mobile }}
                                @endif
                              </td>

                              
                              <td>
                                 @if($a->type == 1)
                                 <span class="btn btn-primary btn-sm">Astrologer Profile</span>
                                 @elseif($a->type == 2)
                                 <span class="btn btn-primary btn-sm">Astrologer service</span>
                                 @elseif($a->type == 3)
                                 <span class="btn btn-primary btn-sm">User add wallet</span>
                                 @elseif($a->type == 4)
                                 <span class="btn btn-primary btn-sm">User Deduct wallet</span>
                                 @elseif($a->type == 5)
                                 <span class="btn btn-primary btn-sm">User edit profile</span>
                                 @endif
                              </td>
                              <td>{{ $a->ticket_id }}</td>
                              <td>{{ $a->ticket_comment }}</td>
                              <td>{{$a->created_at}}</td>
                           </tr>
                           <?php $i++; ?>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                     {!! $data->links() !!}
                  </div>
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{$title}}</h5>
                  <form method="POST" action="" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <div class="row">
                       <div class="col-md-12">
                           <div class="mb-3">
                           <label for="example-text-input" class="form-label">Name</label>
                           <input required class="form-control" type="text" id="example-text-input" placeholder="name" name="name">
                           </div>
                       </div>
                        
                       <div class="col-md-12">
                           <div class="mb-3">
                               <label for="example-text-input" class="form-label">Status</label>
                               <select name="status" required class="form-control">
                               <option value="1">Active</option>
                               <option value="0">Inactive</option>
                               </select>
                           </div>
                       </div>
                   </div>                                    
               </div>
               <div class="modal-footer">
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
               </div>
            </div>
            <!-- /.modal-content -->
            </form>
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
   </div>
   <!-- End Page-content -->
</div>
<div class="modal  fade" id="modal-danger" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content bg-danger">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this?</p>
                    </div>
                    <div class="modal-footer">
                        <form id ="deleteform" method="POST" action="" enctype="multipart/form-data">
                            {{-- @method('DELETE') --}}
                            @csrf
                            <input type="hidden" id="user_id" name="id" value="">
                            <a class="btn btn-danger float-right" onclick="$(this).closest('form').submit();">Delete</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
@section('scripts')
<script>
   $('#modal-danger').on('show.bs.modal', function(event) {
       var button = $(event.relatedTarget)
       var user_id = button.data('userid')

       var modal = $(this)
       modal.find('.modal-footer #user_id').val(user_id)
   })
  
</script>
@endsection
@endsection