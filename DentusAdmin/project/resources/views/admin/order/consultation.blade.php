@extends('layouts.admin')
@section('content')
    <?php
    $name = '';
    $user_email = '';
    $mobile = '';
    $astrologer_mobile = '';
    $id = '';
    $orderid = '';
    $status = '';
    if (isset($_GET['name'])) {
        $name = $_GET['name'];
    }
    if (isset($_GET['mobile'])) {
        $mobile = $_GET['mobile'];
    }
    if (isset($_GET['astrologer_mobile'])) {
        $astrologer_mobile = $_GET['astrologer_mobile'];
    }
    if (isset($_GET['orderid'])) {
        $orderid = $_GET['orderid'];
    }
    if (isset($_GET['user_email'])) {
        $user_email = $_GET['user_email'];
    }
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
    }
    $start_date = '';
    $end_date = '';
    if (isset($_GET['start_date'])) {
        $start_date = $_GET['start_date'];
    }
    if (isset($_GET['end_date'])) {
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
                                            <a target="_blank" href="{{ route($exporturl, $cond) }}?exp=export{{ $cond != '' ? '&cond=' . $cond : '' }}{{ $start_date != '' ? '&start_date=' . $start_date : '' }}{{ $end_date != '' ? '&end_date=' . $end_date : '' }}{{ $name != '' ? '&name=' . $name : '' }}{{ $user_email != '' ? '&email=' . $user_email : '' }}{{ $mobile != '' ? '&mobile=' . $mobile : '' }}{{ $status != '' ? '&status=' . $status : '' }}"
                                                class="btn btn-sm btn-dark">Export</a></span>
                                        </div>
                                        <div class="col-md-12" style="margin-bottom: 5px;">
                                            <a target="_blank" href="{{ route($exporturl, $cond) }}?exp=export_feedback{{ $cond != '' ? '&cond=' . $cond : '' }}{{ $start_date != '' ? '&start_date=' . $start_date : '' }}{{ $end_date != '' ? '&end_date=' . $end_date : '' }}{{ $name != '' ? '&name=' . $name : '' }}{{ $user_email != '' ? '&email=' . $user_email : '' }}{{ $mobile != '' ? '&mobile=' . $mobile : '' }}{{ $status != '' ? '&status=' . $status : '' }}"
                                                class="btn btn-sm btn-dark">Export Feedback</a></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--  <div class="page-title-right">
                       <div class="d-flex flex-wrap gap-2">
                         <a href="add-admin.html" class="btn btn-primary waves-effect waves-light"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> Add
                                              </a>
                                               <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                                               </button>
                                             </div>
                                           </div> -->
                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-3">
                                <form class="form" action="{{ route('admin.order.consultation', $cond) }}" method="GET">
                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="example-text-input" class="form-label">Booking Date From</label>
                                                <input class="form-control" type="date" value="{{ $start_date }}"
                                                    placeholder="Enter date" name="start_date">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="example-text-input" class="form-label">Booking Date To</label>
                                                <input class="form-control" type="date" value="{{ $end_date }}"
                                                    placeholder="Enter date" name="end_date">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="example-text-input" class="form-label">Order Id</label>
                                                <input class="form-control" type="text" value="{{ $orderid }}"
                                                    placeholder="Enter OrderID" name="orderid">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="example-text-input" class="form-label">User Mobile No.</label>
                                                <input class="form-control" type="text" value="{{ $mobile }}"
                                                    placeholder="Enter Mobile" name="mobile">
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="example-text-input" class="form-label">Astrologer Mobile
                                                    No.</label>
                                                <input class="form-control" type="text" value="{{ $astrologer_mobile }}"
                                                    placeholder="Enter Astrologer Mobile" name="astrologer_mobile">
                                            </div>
                                        </div>



                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="example-text-input" class="form-label">Type</label>
                                                <select name="name" class="form-select" placeholder="Select Technician"
                                                    style="width: 100%;">
                                                    <option value="">Select Type...</option>
                                                    <option value="1">Video</option>
                                                    <option value="2">Audio</option>
                                                    <option value="3">Chat</option>
                                                    <option value="4">Schedule</option>
                                                    {{-- <option  value="4">Report</option>
                                                                <option  value="5">Broadcast</option> --}}

                                                </select>
                                                {{-- <input class="form-control" type="text" value="{{$name}}" placeholder="Enter Name" name="name"> --}}
                                            </div>
                                        </div>

                                    </div>
                                    <div>
                                        <input type="submit" value="Submit" class="btn btn-primary w-md">
                                        <a href="{{ route('admin.order.consultation', $cond) }}"><button type="button"
                                                class="btn btn-primary w-md">Reset</button></a>
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
                                <table id="datatable"
                                    class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                                    <thead>
                                        <tr>
                                            <th>#S.No</th>
                                            <th>OrderId</th>
                                            <th>User Details</th>
                                            <th>Booking Details</th>
                                            <th>Transaction Details</th>
                                            <th>Timing Details</th>
                                            {{-- <th>Transaction Details</th> --}}
                                            <th>Booking Remedies</th>
                                            <th>Booking Feedback</th>
                                            <th>Transcript</th>
                                            <th>Cancel Reason</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($data))
                                            <!--   <?php $i = 1; ?>-->


                                            <?php $i = 1; ?>
                                            <?php $count = ($data->currentpage() - 1) * $data->perpage() + 1; ?>


                                            @foreach ($data as $a)
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>{{ $a->id }}</td>
                                                    <td>
                                                        <ul>
                                                            <li>Name: {{ $a->userdetails->name }}</li>
                                                            <li>Wallet: &#8377; {{ $a->userdetails->wallet }}</li>
                                                            <li>Mobile: {{ $a->userdetails->mobile }}</li>
                                                        </ul>
                                                    </td>

                                                    <td>
                                                        <ul>
                                                            <li>Booking Type:
                                                                <?php
                                                                switch ($a->mode) {
                                                                    case 1:
                                                                        echo '<span class="label label-primary">Priority chat</span>';
                                                                        break;
                                                                
                                                                    case 2:
                                                                        echo '<span class="label label-warning">Priority audio</span>';
                                                                        break;
                                                                    case 3:
                                                                        echo '<span class="label label-info">Priority video</span>';
                                                                        break;
                                                                    case 4:
                                                                        echo '<span class="label label-success">Normal chat</span>';
                                                                        break;
                                                                    case 5:
                                                                        echo '<span class="label label-danger">Normal audio</span>';
                                                                        break;
                                                                    case 6:
                                                                        echo '<span class="label label-danger">Normal video</span>';
                                                                        break;
                                                                    case 7:
                                                                        echo '<span class="label label-danger">Free chat</span>';
                                                                        break;
                                                                    case 8:
                                                                        echo '<span class="label label-danger">Free audio</span>';
                                                                        break;
                                                                    case 9:
                                                                        echo '<span class="label label-danger">Free video</span>';
                                                                        break;
                                                                    case 10:
                                                                        echo '<span class="label label-danger">Premium</span>';
                                                                        break;
                                                                    case 11:
                                                                        echo '<span class="label label-danger">Ultapremium</span>';
                                                                        break;
                                                                
                                                                    default:
                                                                        break;
                                                                }
                                                                ?></li>
                                                            <li>Type:
                                                                <?php
                                                                switch ($a->type) {
                                                                    case 1:
                                                                        echo '<span class="label label-primary">Video</span>';
                                                                        break;
                                                                
                                                                    case 2:
                                                                        echo '<span class="label label-warning">Audio</span>';
                                                                        break;
                                                                    case 3:
                                                                        echo '<span class="label label-info">Chat</span>';
                                                                        break;
                                                                    case 4:
                                                                        echo '<span class="label label-success">Report</span>';
                                                                        break;
                                                                    case 5:
                                                                        echo '<span class="label label-danger">Broadcast</span>';
                                                                        break;
                                                                
                                                                    default:
                                                                        break;
                                                                }
                                                                ?></li>
                                                            <li>Booking Status:
                                                                <?php
                                                                if ($a->status == 0) {
                                                                    echo "<span class='label label-info'>Pending</span>";
                                                                } elseif ($a->status == 1 && $a->is_confirmed == 1) {
                                                                    echo "<span class='label label-info'>Confirmed</span>";
                                                                } elseif ($a->status == 1) {
                                                                    echo "<span class='label label-danger'>Accepted by Astrologer</span>";
                                                                } elseif ($a->status == 6) {
                                                                    echo "<span class='label label-warning'>Ongoing</span>";
                                                                } elseif ($a->status == 2) {
                                                                    echo "<span class='label label-success'>Completed</span>";
                                                                } elseif ($a->status == 3) {
                                                                    echo "<span class='label label-danger'>Canceled</span>";
                                                                } elseif ($a->status == 4) {
                                                                    echo "<span class='label label-info'>Refund</span>";
                                                                }
                                                                ?></li>

                                                            <a href="">
                                                                <li>Astrologer Details:
                                                                    <?php
                                                                    $name5 = json_decode($a->astrologerdetails->name, true);
                                                                    $a_name5 = $name5[1] ?? 'Astro N';
                                                                    echo $a_name5;
                                                                    ?></li>
                                                            </a>


                                                            <li>Astrologer Mobile:
                                                                <?php
                                                                echo $a->astrologerdetails->mobile;
                                                                ?></li>



                                                        </ul>
                                                    </td>


                                                    <td>
                                                        <ul>
                                                            <li>Amount Deducted: ₹ {{ $a->payable_amount }}</li>
                                                            <li>PG: {{ $a->gst_astro }}</li>
                                                            <li>TDS: {{ $a->tds_astro }}</li>
                                                            <li>Astrologer Comission Amount: ₹
                                                                {{ $a->astrologer_comission_amount }}</li>
                                                            <li>Admin Comission: ₹ {{ $a->total_astro_comission }}</li>
                                                        </ul>
                                                    </td>



                                                    <td>
                                                        <ul>
                                                            @if (in_array($a->type, [1, 2, 3, 5]))
                                                                <li>Start Time: <?php
                                                                if (empty($a->start_time)) {
                                                                    echo date('d M y h:i:s', strtotime($a->schedule_date_time));
                                                                } else {
                                                                    echo date('d M y h:i:s', strtotime($a->start_time));
                                                                }
                                                                // date('d M y h:i:s', strtotime($a->start_time))
                                                                ?></li>
                                                            @else
                                                            @endif

                                                            @if (in_array($a->type, [1, 2, 3, 5]))
                                                                <li>End Time:

                                                                    <?php
                                                                    if ($a->end_time) {
                                                                        echo date('d M y h:i:s', strtotime($a->end_time));
                                                                    }
                                                                    ?>
                                                                </li>
                                                            @else
                                                            @endif

                                                            <li>Added On:
                                                                <?php
                                                                $new_dater = date('d-m-Y h:i:s A', strtotime($a->created_at));
                                                                echo $new_dater;
                                                                ?></li>

                                                            @if (in_array($a->type, [1, 2, 3, 5]))
                                                                <li>Total Minutes: <?php echo gmdate('i:s', $a->total_seconds); ?></li>
                                                            @else
                                                            @endif
                                                        </ul>
                                                    </td>


                                                    {{-- <td>
                                                        @if ($a->refund_request_raised == 1)
                                                            <span class='label label-warning'>Yes</span>
                                                        @elseif($a->refund_request_raised == 2)
                                                            <span class='label label-success'>Solved</span>
                                                            <?php echo 'Refund request raised on : ' . date('d M y g:ia', strtotime($a->refund_request_on)); ?>
                                                        @else
                                                            <span class='label label-danger'>No</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $a->refund_request_reason }}</td> --}}
                                                    {{-- <td><button data-toggle="modal"
                                                            data-target="#modal-fill{{ $a->id }}"
                                                            id="{{ $a->id }}" type="button"
                                                            class="btn-success btn-xs mb-1">View</button></td>
                                                     --}}
                                                    <td>
                                                        <?php if($a->give_remedie==1) 
                                                        {
                                                            ?>
                                                        <button data-toggle="modal"
                                                            data-target="#modal-remed{{ $a->id }}"
                                                            id="{{ $a->id }}" type="button"
                                                            class="btn-success btn-xs mb-1">View</button>
                                                        <?php
                                                        }
                                                            ?>

                                                    </td>

                                                    <td>
                                                        <?php if($a->give_feedback==1) 
                                                        {
                                                            ?>
                                                        <button data-toggle="modal"
                                                            data-target="#modal-feed{{ $a->id }}"
                                                            id="{{ $a->id }}" type="button"
                                                            class="btn-success btn-xs mb-1">View</button>
                                                        <?php
                                                        }
                                                            ?>
                                                    </td>
                                                    <td>
                                                        <?php if($a->type==2) 
                                                        {
                                                            if ($a->ivr_recording) 
                                                            {
                                                                ?>
                                                                <a class="btn btn-success" target="_blank"
                                                                    href="<?php echo $a->ivr_recording; ?>">Recording</a>
                                                                <?php
                                                            }
                                                        }
                                                        elseif ($a->type==3) 
                                                        {
                                                            if ($a->status == 2) {
                                                            ?>
                                                                        <a href="{{ route('admin.order.chatlog', [$a->id]) }}"
                                                                            class="btn btn-sm btn-primary">Chat History</a>
                                                                 

                                                                    <?php
                                                            }
                                                        }

                                                        elseif ($a->type==1) 
                                                        {
                                                            if ($a->status == 2) {
                                                            ?>

                                                <a class="btn btn-success" target="_blank"
                                                href="<?php echo $a->ivr_recording; ?>">Recording</a>
                                                                        {{-- <a href="<?php echo $a->ivr_recording; ?>" target="_blank">
                                                                            <video id="video_loc" width="100px" height="200px">
                                                                            <source src="<?php echo $a->ivr_recording; ?>">
                                                                            </video> dddd
                                                                            </a> --}}
                                                                    <?php
                                                            }
                                                        }


                                                            ?>


                                                    </td>

                                                    <td>{{ $a->cancel_reason  }}</td>
                                                    

                                                    <td>
                                                        <?php if($a->status == 0) : ?>
                                                        <a href="{{ route('admin.order.updateOrder', [$a->id, 2]) }}"
                                                            class="btn btn-sm btn-primary"
                                                            onclick="return confirm('are you sure?')">Complete</a>

                                                        <a href="{{ route('admin.order.updateOrder', [$a->id, 3]) }}"
                                                            class="btn btn-sm btn-primary"
                                                            onclick="return confirm('are you sure?')">Cancel</a>
                                                        <?php endif; ?>

                                                        <?php if($a->refund_request_raised == 1) : ?>
                                                        <a href="{{ route('admin.order.refundRequestSolved', $a->id) }}>"
                                                            class="btn btn-sm btn-primary"
                                                            onclick="return confirm('are you sure?')">Is Refund Request
                                                            solved?</a>
                                                        <?php endif; ?>
                                                    </td>



                                                </tr>
                                                <?php $t2 = DB::table('booking_remedies')
                                                    ->where('booking_id', $a->id)
                                                    ->first(); ?>
                                                <div class="modal modal-remed fade" data-backdrop="false"
                                                    id="modal-remed{{ $a->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Booking Remedies</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body" id="modalbody">
                                                                @if (!empty($t2))
                                                                    <?php $usr_name = DB::table('users')
                                                                        ->where('id', $t2->user_id)
                                                                        ->first(); ?>
                                                                    <?php $astro_nme = DB::table('astrologers')
                                                                        ->where('id', $t2->astrologer_id)
                                                                        ->first();
                                                                    
                                                                    if ($astro_nme) {
                                                                        $name2 = json_decode($astro_nme->name, true);
                                                                    } else {
                                                                        $name2 = '';
                                                                    }
                                                                    
                                                                    if ($usr_name) {
                                                                        $username2 = $usr_name->name;
                                                                    } else {
                                                                        $username2 = '';
                                                                    }
                                                                    
                                                                    ?>
                                                                    <ul>
                                                                        <li>User Id: {{ $username2 ?? '' }}</li>
                                                                        <li>Astrologer Id: {{ $name2[1] ?? '' }}</li>
                                                                        <li>Purpose: {{ $t2->purpose }}</li>
                                                                        <?php
                                                                        $mantra = explode(',', $t2->mantras_ids);
                                                                        $mantraarray = [];
                                                                        if (count($mantra) > 0) {
                                                                            for ($i = 0; $i < count($mantra); $i++) {
                                                                                $m = DB::table('mantras')
                                                                                    ->select('title', 'description', 'mantra')
                                                                                    ->where('id', $mantra[$i])
                                                                                    ->where('status', 1)
                                                                                    ->first();
                                                                                if ($m) {
                                                                                    array_push($mantraarray, $m);
                                                                                }
                                                                            }
                                                                        }
                                                                        $m_array = [];
                                                                        foreach ($mantraarray as $mmkey) {
                                                                            $m_array[] = $mmkey->title;
                                                                        }
                                                                        ?>
                                                                        <li>Mantras :<?php echo implode(',', $m_array); ?></li>

                                                                        <?php
                                                                        $thread_colors = explode(',', $t2->thread_colors_ids);
                                                                        $thread_colorsarray = [];
                                                                        if (count($thread_colors) > 0) {
                                                                            for ($i = 0; $i < count($thread_colors); $i++) {
                                                                                $m = DB::table('thread_colors')
                                                                                    ->select('title', 'description')
                                                                                    ->where('id', $thread_colors[$i])
                                                                                    ->where('status', 1)
                                                                                    ->first();
                                                                                if ($m) {
                                                                                    array_push($thread_colorsarray, $m);
                                                                                }
                                                                            }
                                                                        }
                                                                        $t_array = [];
                                                                        foreach ($thread_colorsarray as $ttkey) {
                                                                            $t_array[] = $ttkey->title;
                                                                        }
                                                                        ?>
                                                                        <li>Thread Colors :<?php echo implode(',', $t_array); ?></li>


                                                                        <?php
                                                                        $master_donations = explode(',', $t2->donations_ids);
                                                                        $master_donationsarray = [];
                                                                        if (count($master_donations) > 0) {
                                                                            for ($i = 0; $i < count($master_donations); $i++) {
                                                                                $m = DB::table('master_donations')
                                                                                    ->select('title', 'description')
                                                                                    ->where('id', $master_donations[$i])
                                                                                    ->where('status', 1)
                                                                                    ->first();
                                                                                if ($m) {
                                                                                    array_push($master_donationsarray, $m);
                                                                                }
                                                                            }
                                                                        }
                                                                        $d_array = [];
                                                                        foreach ($master_donationsarray as $ddkey) {
                                                                            $d_array[] = $ddkey->title;
                                                                        }
                                                                        ?>

                                                                        <li>Donations :<?php echo implode(',', $d_array); ?></li>

                                                                        {{-- <li>Donations Ids: {{ $t2->donations_ids }}</li> --}}

                                                                        <?php
                                                                        $rudrakshi = explode(',', $t2->rudrakshi_ids);
                                                                        $rudrakshiarray = [];
                                                                        if (count($rudrakshi) > 0) {
                                                                            for ($i = 0; $i < count($rudrakshi); $i++) {
                                                                                $m = DB::table('rudrakshi')
                                                                                    ->select('title', 'description')
                                                                                    ->where('id', $rudrakshi[$i])
                                                                                    ->where('status', 1)
                                                                                    ->first();
                                                                                if ($m) {
                                                                                    array_push($rudrakshiarray, $m);
                                                                                }
                                                                            }
                                                                        }
                                                                        $r_array = [];
                                                                        foreach ($rudrakshiarray as $rrkey) {
                                                                            $r_array[] = $rrkey->title;
                                                                        }
                                                                        ?>

                                                                        <li>Rudrakshi :<?php echo implode(',', $r_array); ?></li>
                                                                        {{-- <li>Rudrakshi Ids: {{ $t2->rudrakshi_ids }}</li> --}}


                                                                        <?php
                                                                        $precious_stone = explode(',', $t2->precious_stone_ids);
                                                                        $precious_stonearray = [];
                                                                        if (count($precious_stone) > 0) {
                                                                            for ($i = 0; $i < count($precious_stone); $i++) {
                                                                                $m = DB::table('precious_stone')
                                                                                    ->select('title', 'description')
                                                                                    ->where('id', $precious_stone[$i])
                                                                                    ->where('status', 1)
                                                                                    ->first();
                                                                                if ($m) {
                                                                                    array_push($precious_stonearray, $m);
                                                                                }
                                                                            }
                                                                        }
                                                                        $p_array = [];
                                                                        foreach ($precious_stonearray as $ppkey) {
                                                                            $p_array[] = $ppkey->title;
                                                                        }
                                                                        ?>

                                                                        <li>Precious Stone :<?php echo implode(',', $p_array); ?></li>




                                                                        <?php
                                                                        $semi_precious_stone = explode(',', $t2->semi_precious_stone_ids);
                                                                        $semi_precious_stonearray = [];
                                                                        if (count($semi_precious_stone) > 0) {
                                                                            for ($i = 0; $i < count($semi_precious_stone); $i++) {
                                                                                $m = DB::table('semi_precious_stone')
                                                                                    ->select('title', 'description')
                                                                                    ->where('id', $semi_precious_stone[$i])
                                                                                    ->where('status', 1)
                                                                                    ->first();
                                                                                if ($m) {
                                                                                    array_push($semi_precious_stonearray, $m);
                                                                                }
                                                                            }
                                                                        }
                                                                        $s_p_array = [];
                                                                        foreach ($semi_precious_stonearray as $spkey) {
                                                                            $s_p_array[] = $spkey->title;
                                                                        }
                                                                        ?>

                                                                        <li>Semi Precious Stone :<?php echo implode(',', $s_p_array); ?></li>





                                                                        <li>Message: {{ $t2->message }}</li>
                                                                        {{-- <li>Status: {{ $t2->status }}</li> --}}
                                                                        <li>Created At: {{ $t2->created_at }}</li>

                                                                    </ul>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">

                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php $bkg = DB::table('booking_feedback')
                                                    ->where('booking_id', $a->id)
                                                    ->first(); ?>
                                                <div class="modal modal-feed fade" data-backdrop="false"
                                                    id="modal-feed{{ $a->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Booking Feedback</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body" id="modalbody">
                                                                @if ($bkg)
                                                                    <?php $user_name = DB::table('users')
                                                                        ->where('id', $bkg->user_id)
                                                                        ->first(); ?>
                                                                    <?php $astro_name = DB::table('astrologers')
                                                                        ->where('id', $bkg->astrologer_id)
                                                                        ->first();
                                                                    $name = json_decode($astro_name->name, true);
                                                                    ?>
                                                                    <ul>
                                                                        <li>User Id: {{ $user_name->name }}</li>
                                                                        <li>Astrologer Id: {{ $name[1] }}</li>
                                                                        <li>User Consultation Language:
                                                                            {{ $bkg->user_consultation_language }}</li>
                                                                        <li>Main Profile: {{ $bkg->main_profile }}</li>
                                                                        <li>Rashi: {{ $bkg->rashi }}</li>
                                                                        <li>Nakshtra: {{ $bkg->nakshtra }}</li>
                                                                        <li>Feedback On User: {{ $bkg->feedback_on_user }}
                                                                        </li>
                                                                        <li>Applicable: {{ $bkg->applicable }}</li>
                                                                        <li>Refund Amount: {{ $bkg->refund_amount }}</li>
                                                                        <li>Enter Amount: {{ $bkg->enter_amount }}</li>
                                                                        <li>Reason: {{ $bkg->reason }}</li>
                                                                        <li>Resolve: {{ $bkg->resolve }}</li>
                                                                        {{-- <li>Status: {{ $bkg->status }}</li> --}}
                                                                        <li>Created At: {{ $bkg->created_at }}</li>

                                                                    </ul>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">

                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php $t = App\Models\Transactions::where('booking_id', $a->id)->first(); ?>
                                                <div class="modal modal-fill fade" data-backdrop="false"
                                                    id="modal-fill{{ $a->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Transaction Details</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body" id="modalbody">
                                                                @if ($t)
                                                                    <ul>
                                                                        <li>Booking Txn Id: {{ $t->booking_txn_id }}</li>
                                                                        <li>Payment Mode: {{ $t->payment_mode }}</li>
                                                                        <li>Type: {{ $t->type }}</li>
                                                                        <li>Old Wallet: {{ $t->old_wallet }}</li>
                                                                        <li>Tax Amount: {{ $t->txn_amount }}</li>
                                                                        <li>Txn Amount: {{ $t->update_wallet }}</li>
                                                                        <li>Status: {{ $t->status }}</li>
                                                                        <li>Txn Mode: {{ $t->txn_mode }}</li>
                                                                        <li>Bank Name: {{ $t->bank_name }}</li>
                                                                        <li>Bank Txn Id: {{ $t->bank_txn_id }}</li>
                                                                        <li>IFSC: {{ $t->ifsc }}</li>
                                                                        <li>Account: {{ $t->account }}</li>
                                                                        <li>Created At: {{ $t->created_at }}</li>

                                                                    </ul>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">

                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--  <?php $i++; ?>-->
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
        </div>
        <!-- End Page-content -->

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
        $('#modal-danger').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var user_id = button.data('userid')

            var modal = $(this)
            modal.find('.modal-footer #user_id').val(user_id)
            // modal.find('form').attr('action','permissions/' + user_id);
        })
    </script>
@endsection
@endsection
