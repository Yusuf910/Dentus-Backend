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
                  <h4 class="mb-sm-0 font-size-18">{{$title}}
                  </h4>

                  <div class="page-title-right">
                    <div class="d-flex flex-wrap gap-2">
                       <a href="{{route('admin.master.create_user')}}" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
                       </a>
                       <!-- <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                          </button> -->
                    </div>
                 </div>
               </div>
            </div>
         </div>
         <!-- <div class="col-md-12" style="margin-bottom: 5px;">
            <a href="{{route($exporturl)}}?exp=export{{$name != '' ? '&name='.$name : ''}}{{$email != '' ? '&email='.$email : ''}}{{$mobile != '' ? '&mobile='.$mobile : ''}}{{$status != '' ? '&status='.$status : ''}}{{$start_date != '' ? '&start_date='.$start_date : ''}}{{$end_date != '' ? '&end_date='.$end_date : ''}}" class="btn btn-sm btn-dark">Export</a></span>
         </div> -->
         <form method="GET" action="" enctype="multipart/form-data">
                           <span>


                              <input type="hidden" name="name" value="{{$name}}">
                              <input type="hidden" name="email" value="{{$email}}">
                              <input type="hidden" name="mobile" value="{{$mobile}}">
                              <input type="hidden" name="start_date" value="{{$start_date}}">
                              <input type="hidden" name="end_date" value="{{$end_date}}">
                          <input type="submit" name="export_file" value="Export" class="btn btn-primary waves-effect waves-light" >
                          </span>
                           </form>

         <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-3">
                     <form class="form" action="{{ route('admin.master.manage_user') }}" method="GET">
                        <div class="row">
                           <div class="col-md-3">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label"> Date From</label>
                                  <input class="form-control" type="date" value="{{$start_date}}" placeholder="Enter date" name="start_date">
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label"> Date To</label>
                                  <input class="form-control" type="date" value="{{$end_date}}" placeholder="Enter date" name="end_date">
                              </div>
                          </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Name</label>
                                    <input class="form-control" type="text" value="{{$name}}" placeholder="Enter Name" name="name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Email</label>
                                    <input class="form-control" type="text" value="{{$email}}" placeholder="Enter email" name="email">
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="example-text-input" class="form-label">Mobile</label>
                                    <input class="form-control" type="text" value="{{$mobile}}" placeholder="Enter mobile number" name="mobile">
                                </div>
                            </div>

                        </div>
                        <div>
                           <input type="submit" value="Submit" class="btn btn-primary w-md">
                           <a href="{{ route('admin.master.manage_user') }}"><button type="button" class="btn btn-primary w-md">Reset</button></a>
                        </div>
                     </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body table-responsive">
                    @include('includes.admin.form-success')
                     <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                        <thead>
                           <tr>
                            <th>#ID</th>
                            <th>User Details</th>
                            <th>Add Wallet</th>
                            <th>Nill Wallet</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>Delete Status</th>
                            <th>Register On</th>
                            <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i=1; ?>
                           @foreach ($data as $a)
                           @php
                           $lang = App\Models\AppLanguage::get();
                            $name = json_decode($a->name,true);
                            @endphp
                           <tr>
                              <td>{{ $i }}</td>
                              <td>
                                <ul>
                                   <li>Name: <a href="{{ route('admin.master.users_detail',$a->id) }}">{{$a->name}}</a></li>
                                   <li>Mail ID: {{$a->email}}</li>
                                   <li>Mobile: {{$a->country_code}} {{$a->mobile}}</li>
                                   <li>Gender: {{$a->gender}}</li>
                                   <li>Referral Code: {{$a->referral_code}}</li>

                                   <?php
                                   if($a->country_code == '91'){
                                     ?>
                                     <li>Wallet: &#8377; {{$a->wallet}}</li>
                                     <?php
                                   }
                                   else{
                                      ?>
                                      <li>Wallet: $ {{$a->wallet}}</li>
                                      <?php
                                   }
                                   ?>

                               </ul>
                             </td>
                             <td>



                                <?php
                                   $admin_id = Auth()->guard('admin')->user()->id;
                                   if ($admin_id == 1) {
                                      ?>
                                         <button data-toggle="modal" data-target="#modal-fill{{$a->id}}" id="{{$a->id}}"  type="button" class="btn btn-primary btn-sm">Add</button>
                                      <?php
                                   }
                                   else {
                                    ?>
                                    @if(Auth::guard('admin')->user()->hasPermission('add-wallet-user'))
                                    <button data-toggle="modal" data-target="#modal-fill{{$a->id}}" id="{{$a->id}}"  type="button" class="btn btn-primary btn-sm">Add</button>
                                    @endif
                                    <?php
                                   }

                                ?>

                             </td>
                             <td>

                                <?php
                                $admin_id = Auth()->guard('admin')->user()->id;
                                if ($admin_id == 1) {
                                   ?>
                                      <button data-toggle="modal" data-target="#modal-fille1{{$a->id}}" id="{{$a->id}}"  type="button" class="btn-success btn-xs mb-1">Deduct</button>

                                   <?php
                                  }
                                  else {
                                    ?>
                                    @if(Auth::guard('admin')->user()->hasPermission('nill-wallet-user'))
                                    <button data-toggle="modal" data-target="#modal-fille1{{$a->id}}" id="{{$a->id}}"  type="button" class="btn-success btn-xs mb-1">Deduct</button>
                                    @endif
                                    <?php
                                   }

                                ?>



                             <div class="modal modal-fill fade" data-backdrop="false" id="modal-fill{{$a->id}}" tabindex="-1">
                             <div class="modal-dialog">
                                <div class="modal-content">
                                   <div class="modal-header">
                                      <h5 class="modal-title">Add wallet</h5>
                                      <button type="button" class="close" data-dismiss="modal">
                                      <span aria-hidden="true">&times;</span>
                                      </button>
                                   </div>
                                   <div class="modal-body" id="modalbody">
                                      <div class="form-group">
                                         <p>Name<code>:  {{$a->name}}</code></p>
                                         <p>Mail ID<code>:  {{$a->email}}</code></p>
                                         <p>Mobile<code> : {{$a->phone}}</code></p>

                                         <?php
                                         if($a->country_code == '91'){
                                         ?>
                                        <p>Wallet Balance<code> : &#8377; {{$a->wallet}}</code></p>
                                         <?php
                                         }
                                         else{
                                            ?>
                                            <p>Wallet Balance :<code> $ {{$a->wallet}}</code></p>
                                            <?php
                                         }
                                         ?>



                                      <form action="{{route('admin.master.add_wallet')}}" method="POST">
                                         {{ csrf_field() }}
                                      <div class="form-group">
                                         <label class="control-label" for="inputName">Add Amount</label>
                                         <input type="number" min="1" class="form-control" id="inputName" name="addamount" step= "any" placeholder="Amount" required="">
                                      </div>
                                      <input type="hidden" name="user_id" value="{{$a->id}}">
                                      <div class="form-group">
                                         <label class="control-label" for="inputName">Add Comment</label>
                                         <input type="text" class="form-control" id="comment" name="comment" placeholder="comment">
                                      </div>
                                      <div class="form-group">
                                       <label class="control-label" for="inputName">Ticket Id</label>
                                       <input type="text" class="form-control" id="ticket_id" name="ticket_id" placeholder="Ticket Id" required>
                                    </div>
                                    <div class="form-group">
                                       <label class="control-label" for="inputName">Ticket Comment</label>
                                       <input type="text" class="form-control" id="ticket_comment" name="ticket_comment" placeholder="Ticket Comment" required>
                                    </div>
                                      <input type="submit" class="btn btn-secondary" value="Add">
                                      </form>
                                   </div>
                                   <div class="modal-footer">

                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                   </div>
                                </div>
                             </div>
                          </div>
                             </td>
                             
                             <td>
                                @if($a->status == 1)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>@endif
                             </td>
                             <td>{{$a->ip_address}}</td>
                             <td>
                              <?php
                                 if($a->is_delete == 1){
                                    ?>
                                       <span class="badge bg-danger">Delete</span>
                                       <?php
                                       if ($a->recover_account == 1) {
                                         ?>
                                         <a href="{{ route($recover_userurl,$a->id) }}" onclick="return confirm('Are you sure recover account?');" class="btn btn-warning waves-effect waves-light btn-sm">Recover data</a>
                                         <?php
                                       }
                                       ?>
                                       
                                    <?php
                                 }
                                 elseif($a->is_delete == 2){
                                    ?>
                                       <span class="badge bg-success">Relogin</span>

                                       <?php
                                          echo $a->relogin_time;

                                       if ($a->recover_account == 1) {
                                         ?>
                                         <a href="{{ route($recover_userurl,$a->id) }}" onclick="return confirm('Are you sure recover account?');" class="btn btn-warning waves-effect waves-light btn-sm">Recover data</a>
                                         <?php
                                       }
                                       ?>
                                    <?php
                                 }
                                 
                                 ?>
                             
                              </td>


                             <td>{{$a->added_on ?? $a->created_at ?? ''}}</td>

                              <td>
                                 <a href="{{ route($editurl,$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm"><span class="mdi mdi-pencil d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                                 {{-- <button id="modal-danger" data-userid="{{$a->id}}" type="button" class="btn btn-danger waves-effect waves-light btn-sm modal-danger" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                    <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button> --}}
                              </td>
                           </tr>

                         
                           <div class="modal modal-fill fade" data-backdrop="false" id="modal-fille1{{$a->id}}" tabindex="-1">
                            <div class="modal-dialog">
                               <div class="modal-content">
                                  <div class="modal-header">
                                     <h5 class="modal-title">Deduct wallet</h5>
                                     <button type="button" class="close" data-dismiss="modal">
                                     <span aria-hidden="true">&times;</span>
                                     </button>
                                  </div>
                                  <div class="modal-body" id="modalbody">
                                     <div class="form-group">
                                        <p>Name<code> : {{$a->name}}</code></p>
                                        <p>Email<code>: {{$a->email}}</code></p>
                                        <p>Mobile<code>: {{$a->phone}}</code></p>

                                        <?php
                                        if($a->country_code == '91'){
                                        ?>
                                       <p>Wallet Balance<code> : &#8377; {{$a->wallet}}</code></p>
                                        <?php
                                        }
                                        else{
                                           ?>
                                           <p>Wallet Balance<code>:  $ {{$a->wallet}}</code></p>
                                           <?php
                                        }
                                        ?>
                                     <form action="{{route('admin.master.deduct_wallet')}}" method="POST">
                                        {{ csrf_field() }}
                                     <div class="form-group">
                                        <label class="control-label" for="inputName">Deduct Amount</label>
                                        <input type="number" min="1" class="form-control" id="inputName" name="addamount" step= "any" placeholder="Name" required="">
                                     </div>
                                     <input type="hidden" name="user_id" value="{{$a->id}}">
                                     <div class="form-group">
                                        <label class="control-label" for="inputName">Add Comment</label>
                                        <input type="text" class="form-control" id="comment" name="comment" placeholder="comment">
                                     </div>
                                     <div class="form-group">
                                       <label class="control-label" for="inputName">Ticket Id</label>
                                       <input type="text" class="form-control" id="ticket_id" name="ticket_id" placeholder="Ticket Id" required>
                                    </div>
                                    <div class="form-group">
                                       <label class="control-label" for="inputName">Ticket Comment</label>
                                       <input type="text" class="form-control" id="ticket_comment" name="ticket_comment" placeholder="Ticket Comment" required>
                                    </div>
                                     <input type="submit" class="btn btn-secondary" value="Submit">
                                     </form>
                                  </div>
                           <?php $i++; ?>
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
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{$title}}</h5>
                  <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
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
                                   <label for="example-text-input" class="form-label">Position</label>
                                   <input required name="position" min="1" class="form-control" type="number" id="1-text-input" placeholder="eg. 1">
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
             <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
                @csrf
                <input type="text" id="user_id" name="id" value="">
                <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
             </form>
          </div>
       </div>
       <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
 </div>
@section('scripts')
<script src="{{asset('project/public/adminassets_old')}}/js/vendors.min.js"></script>
<script>
    $('.modal-danger').click('show.bs.modal', function (event) {
        var user_id = $(this).attr('data-userid')
        // alert(user_id);
        $('#user_id').val(user_id);
    })

 </script>

<script>
   function myFunction() {
     alert("Are you sure recover account.");
   }
   </script>

@endsection
@endsection
