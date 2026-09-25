@extends('layouts.admin')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="main-content">
   <div class="page-content">
       <div class="container-fluid">
      <!-- Content Header (Page header) -->
      {{-- <div class="content-header">
         <div class="d-flex align-items-center">
            <div class="mr-auto">
               <h3 class="page-title">User Details</h3>
               <div class="d-inline-block align-items-center">
                  <nav>
                     <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Home</li>
                        <li class="breadcrumb-item active" aria-current="page">User Details</li>
                     </ol>
                  </nav>
               </div>
            </div>
         </div>
      </div> --}}
      <div class="row">
         <div class="col-12">
             <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                 <h4 class="mb-sm-0 font-size-18">User Details</h4>

                 <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                         <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                         <li class="breadcrumb-item active">User Details</li>
                     </ol>
                 </div>

             </div>
         </div>
     </div>
      <!-- Main content -->
      <div class="row">
         <div class="col-12">
             <div class="card">

                 <div class="card-body p-3">
         <div class="row">
            <div class="col-12 col-lg-5 col-xl-4">
               <div class="box box-widget widget-user">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header bg-black"
                     style="background: url('{{ asset('project/public/adminassets') }}/images/gallery/full/10.jpg') center center;">
                    
                  </div>
                  <h3 class="widget-user-username">{{ $a->name ?? '' }}</h3>
                  {{-- <div class="widget-user-image"> <img class="rounded-circle"
                     src="{{ asset('../admin/uploads/user/') }}/{{ $a->image }}"
                     alt="User Avatar"> </div> --}}
                  <div class="box-footer">
                     <div class="row">
                        <div class="col-sm-12">
                           <div class="description-block">
                              <h5 class="description-header">{{ $a->mobile ?? '' }}</h5>
                              <span
                                 class="description-text">{{ $a->email }}</span>
                           </div>
                           <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <!-- /.col -->
                     </div>
                     <!-- /.row -->
                  </div>
               </div>
            </div>
            <div class="col-12 col-lg-7 col-xl-8">
               <div class="row">

                  <table class="table table-striped">

                     <tbody>
                       <tr>
                         <th scope="row">DOB</th>
                         <td><strong>{{$a->dob}}</strong></td>

                       </tr>
                       <tr>
                         <th scope="row">Birth Time</th>
                         <td><strong>
                          {{$a->birth_time}}

                        {{-- //  if ($a->birth_time) {
                        //    $timestamp = strtotime($a->birth_time);
                        //    $new_date = date("d-m-Y", $timestamp);
                        //    echo $new_date; // Outputs: 31-03-2019
                        //  }

                         ?>
                        --}}
                        </strong></td>

                       </tr>
                       <tr>
                         <th scope="row">Place Of Birth</th>
                         <td><strong>{{$a->place_of_birth}}</strong></td>
                       </tr>
                       <tr>
                         <th scope="row">Gender</th>
                         <td><strong>{{$a->gender}}</strong></td>
                       </tr>


                       <tr>
                        <th scope="row">Address</th>
                        <td><strong>{{$a->address}}</strong></td>
                      </tr>

                      <tr>
                        <th scope="row">Device Type</th>
                        <td><strong>{{$a->device_type}}</strong></td>
                      </tr>

                      <tr>
                        <th scope="row">Login Time </th>
                        <td><strong>{{$a->loginTime}}</strong></td>
                      </tr>



                      <tr>
                        <th scope="row">Refferal code</th>
                        <td><strong>{{$a->referral_code}}</strong></td>
                      </tr>

                      <tr>
                        <th scope="row">Register On </th>
                        <td><strong>{{$a->created_at}}</strong></td>
                      </tr>

                      <tr>
                        <th scope="row">Device Type</th>
                        <td><strong>{{$a->device_type}}</strong></td>
                      </tr>



                     </tbody>
                   </table>





{{--
                  <div class="col-md-6 col-12">
                     <div class="media bg-white mb-20">
                        <div class="media-body">
                           <p><strong>{{ $a->device_id ?? '' }}</strong></p>
                           <p>Device Id</p>
                        </div>
                        <i class="fa fa-envelope text-info" aria-hidden="true"></i>
                     </div>
                  </div>
                  <div class="col-md-6 col-12">
                     <div class="media bg-secondary mb-20">
                        <div class="media-body">
                           <p><strong>{{ $a->device_token ?? '' }}</strong></p>
                           <p>Token</p>
                        </div>
                        <span class="fa fa-envelope lead text-info"></span>
                     </div>
                  </div> --}}
               </div>
            </div>
            <!-- /.col -->
         </div>
         <!-- /.row -->
         <div class="row">
            <div class="col-12">
               <div class="box">
                  <div class="box-header">
                     <h4 class="box-title align-items-start flex-column">

                        <?php
                        if($a->country_code == '91'){
                        ?>
                        Wallet Transaction History (Wallet Amount : &#8377; {{$a->wallet}})
                        <?php
                        }
                        else{
                           ?>
                         Wallet Transaction History (Wallet Amount : $ {{$a->wallet}})
                           <?php
                        }
                        ?>



                        <small class="subtitle">{{$temp->count()}} Found</small>
                     </h4>
                  </div>
                  <div class="box-body pl-10">
                     <div class="table-responsive">
                        <table id="example1"
                           class="table table-bordered-1 table-hover display nowrap margin-top-10 w-p100">
                           <thead>
                              <tr class="text-left">
                                   <th>ID</th>
                                   <th>User Name</th>
                                   <th>Transactions Name</th>
                                   <th>Booking Id</th>
                                   <th>Payment Mode</th>
                                   <th>Transactions For</th>
                                   <th>Type</th>
                                   <th>Old Wallet</th>
                                   <th>Transaction Amount</th>
                                   <th>Update Wallet</th>
                                   <th>Gst Perct</th>
                                   <th>Gst Amount</th>
                                   <th>Added On</th>
                              </tr>
                           </thead>
                           <tbody>
                              @if (!empty($temp))
                              <?php $i = 1; ?>
                              @foreach ($temp as $data)
                              <tr>
                                   <td ><?php echo $data->id; ?></td>
                                   <td class="center">{{$a->name}}</td>
                                   <td class="center">{{$data->txn_name}}</td>
                                   <td class="center">{{$data->booking_txn_id}}</td>
                                   <td class="center">{{$data->payment_mode}}</td>
                                   <td class="center">{{$data->txn_for }}</td>
                                   <td class="center">{{$data->type}}</td>
                                   <td class="center">{{$data->old_wallet}}</td>
                                   <td class="center">{{$data->txn_amount}}</td>
                                   <td class="center">{{$data->update_wallet}}</td>
                                   <td class="center">{{$data->gst_perct}}</td>
                                   <td class="center">{{$data->gst_amount}}</td>

                                      <td class="center">{{date("d M y g:ia",  strtotime($data->created_at))}}</td>
                              </tr>
                              <?php $i++; ?>
                              @endforeach
                              @endif
                           </tbody>
                           <tfoot>
                              <tr>
                                 <th>ID</th>
                                   <th>User Name</th>
                                   <th>Transactions Name</th>
                                   <th>Booking Id</th>
                                   <th>Payment Mode</th>
                                   <th>Transactions For</th>
                                   <th>Type</th>
                                   <th>Old Wallet</th>
                                   <th>Transaction Amount</th>
                                   <th>Update Wallet</th>
                                   <th>Gst Perct</th>
                                   <th>Gst Amount</th>
                                   <th>Added On</th>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- /.row -->








         <div class="row">
            <div class="col-12">
               <div class="box">
                  <div class="box-header">
                     <h4 class="box-title align-items-start flex-column">
                        Member History
                        <small class="subtitle">{{$member_count}} Found</small>
                     </h4>
                     <div class=" pull-right">
                        <!-- <a href="#" class="waves-effect waves-light btn btn-md btn-primary"><i class="fa fa-plus mr-15"></i> Add New</a>-->
                     </div>
                  </div>
                  <div class="box-body pl-10">
                     <div class="table-responsive">
                        <table id="example"
                           class="table table-bordered-1 table-hover display nowrap margin-top-10 w-p100">
                           <thead>
                              <tr class="text-left">
                                 <th class="">S.no</th>
                                 <th>Name</th>
                                 <th>Date Of Birth</th>
                                 <th>Time Of Birth</th>
                                 <th>Place Of Birth </th>
                                 <th>Gender </th>
                                 <th>Relation</th>
                              </tr>
                           </thead>
                           <tbody>


                              @if (!empty($m_count))
                              <?php $i = 1; ?>
                              @foreach ($m_count as $data3)
                              <tr>
                                 <td class="">{{$i}}</td>
                                 <td class="center">{{$data3->name}}</td>
                                <td class="center">{{$data3->dob }}</td>
                                <td class="center">{{$data3->tob }}</td>
                                <td class="center">{{$data3->pob }}</td>
                                <td class="center">{{$data3->gender}}</td>
                                <td class="center">{{$data3->relation}}</td>
                              </tr>
                              <?php $i++; ?>
                              @endforeach
                              @endif
                           </tbody>
                           <tfoot>
                              <tr>
                                 <th class="">S.no</th>
                                 <th>Name</th>
                                 <th>Date Of Birth</th>
                                 <th>Time Of Birth</th>
                                 <th>Place Of Birth </th>
                                 <th>Gender </th>
                                 <th>Relation</th>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>





         
         <div class="row">
            <div class="col-12">
               <div class="box">
                  <div class="box-header">
                     <h4 class="box-title align-items-start flex-column">
                        Refer Code History
                        <small class="subtitle">{{$ref_count}} Found</small>
                     </h4>
                     <div class=" pull-right">
                        <!-- <a href="#" class="waves-effect waves-light btn btn-md btn-primary"><i class="fa fa-plus mr-15"></i> Add New</a>-->
                     </div>
                  </div>
                  <div class="box-body pl-10">
                     <div class="table-responsive">
                        <table id="example"
                           class="table table-bordered-1 table-hover display nowrap margin-top-10 w-p100">
                           <thead>
                              <tr class="text-left">
                                 <th class="">S.no</th>
                                 <th>Refer User Name</th>
                                 <th>Code</th>
                                 <th>Time Of Birth</th>
                             
                                 <th>Relation</th>
                              </tr>
                           </thead>
                           <tbody>


                              @if (!empty($ref_data))
                              <?php $i = 1; ?>
                              @foreach ($ref_data as $data3)
                              <tr>
                                 <td class="">{{$i}}</td>
                                 <td class="center">{{$data3->name}}</td>
                                <td class="center">{{$data3->code }}</td>
                                <td class="center">{{$data3->tob }}</td>
                            
                                <td class="center">{{$data3->relation}}</td>
                              </tr>
                              <?php $i++; ?>
                              @endforeach
                              @endif
                           </tbody>
                           <tfoot>
                              <tr>
                                 <th class="">S.no</th>
                                 <th>Name</th>
                                 <th>Date Of Birth</th>
                                 <th>Time Of Birth</th>
                                 <th>Place Of Birth </th>
                                 <th>Gender </th>
                                 <th>Relation</th>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>





                 </div>
             </div>
         </div>
         <!-- end row -->
      <!-- /.content -->
   </div>
</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
@endsection
