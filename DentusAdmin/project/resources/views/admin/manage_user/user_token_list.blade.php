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
                  <h4 class="mb-sm-0 font-size-18"> Un register user
                  </h4>

                  <div class="page-title-right">
                    <div class="d-flex flex-wrap gap-2">
                      
                       <!-- <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                          </button> -->
                    </div>
                 </div>
               </div>
            </div>
         </div>
     
         <form method="GET">
            @csrf
            <div class="col-md-12" style="margin-bottom: 5px;">
               

               <input class="form-control" type="hidden" value="{{ $start_date }}"
               placeholder="Enter date" name="start_date">

               <input class="form-control" type="hidden" value="{{ $end_date }}"
               placeholder="Enter date" name="end_date">


               <span>  <input type="submit" name="get_export_data" value="Export" class="btn btn-sm btn-dark">
               </span>
           </div>
        </form> 

         <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-3">
                     <form class="form" action="" method="GET">
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


                          


                            
                           
                        </div>
                        <div>
                           <input type="submit" value="Submit" class="btn btn-primary w-md">
                           <a href="{{ route('admin.master.token_user') }}"><button type="button" class="btn btn-primary w-md">Reset</button></a>
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
                    <div class="table-responsive">
                     <table id="" class="table table-bordered-1 table-hover display nowrap margin-top-10 w-p100">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>Mobile</th>
                          
                              <th>Added At</th>
                           </tr>
                        </thead>
                        <tbody>
                             <?php $i = 1; ?>
                             <?php $count =1; ?>
                             @foreach ($fetchdata as $user)
                             <tr>
                                 <td>{{$count++}}</td>
                              
                                 <td>{{$user->mobile }}</td>
                                 
                                 <td>{{date('d-m-y H:i:s',strtotime($user->created_at ))}}</td>
                             
                             </tr>
                             
                     @endforeach
                         </tbody>
                        <tfoot>
                           <tr>
                           <th>#</th>
                              <th>Mobile</th>
                              <th>Added At</th>
                           </tr>
                        </tfoot>
                     </table>
                     {{ $fetchdata->links()  }}
                  </div>
                     {{-- {!! $data->links(); !!} --}}
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

@section('scripts')
<script src="{{asset('project/public/adminassets_old')}}/js/vendors.min.js"></script>


<script>
   function myFunction() {
     alert("Are you sure recover account.");
   }
   </script>

@endsection
@endsection
