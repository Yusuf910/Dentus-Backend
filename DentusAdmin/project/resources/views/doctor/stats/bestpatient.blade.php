@extends('layouts.admin')
@section('content')
<?php
$date_filter = '';
$start_date = '';
$end_date = '';
if (isset($_GET['date_filter']))
{
   $date_filter = $_GET['date_filter'];
}

if (isset($_GET['start_date']))
{
   $start_date = $_GET['start_date'];
}
if (isset($_GET['end_date']))
{
   $end_date = $_GET['end_date'];
}


$exportUrl = route('admin.appoint.myappointment') . '?exp=export';

$params = [
    'name'       => $_GET['name'] ?? '',
    'email'      => $_GET['email'] ?? '',
    'mobile'     => $_GET['mobile'] ?? '',
    'start_date' => $_GET['start_date'] ?? '',
    'end_date'   => $_GET['end_date'] ?? '',
    'status'     => $_GET['status'] ?? '',
];

$queryString = http_build_query(array_filter($params));

if (!empty($queryString)) {
    $exportUrl .= '&' . $queryString;
}

?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item"><a href="{{route('admin.appoint.mystats')}}">Stats </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">{{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
      	@include('includes.admin.form-success')
         <div class="col-sm-12">
             <div class="card card-table show-entire">
                <div class="card-body">
                   <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <div class="col">
                           <div class="doctor-table-blk">
                              <h3>Best Patients</h3>
                           </div>
                        </div>
                       <div class="col-auto text-end float-end ms-auto download-grp">
                            <div class="input-block mb-0 me-2">
                                <select class="form-control" id="dateFilter1">
                                    <option value="" selected>Filter</option>
                                    <option {{$date_filter == '30' ? 'selected' : ''}}  value="30">30 Days</option>
                                    <option {{$date_filter == '60' ? 'selected' : ''}}  value="60">60 Days</option>
                                    <option {{$date_filter == '90' ? 'selected' : ''}}  value="90">90 Days</option>
                                    <option {{$date_filter == '365' ? 'selected' : ''}}  value="365">1 Year</option>
                                </select>
                            </div>
                            <div class="add-group">
                              <a href="{{ route('admin.appoint.bestpatient') }}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                           </div>
                        </div>
                     </div>
                  </div>

                   <div class="table-responsive">
                      <table class="table border-0 custom-table comman-table datatable mb-0">
                         <thead>
                            <tr>
                               <th>Name</th>
                               <th>Mobile</th>
                               <th>Revenue</th>
                            </tr>
                         </thead>
                         <tbody>
                            @if($bestCustomers)
                            @foreach($bestCustomers as $a)
                            <tr>
                                <td class="profile-image sorting_1">
                                    <img width="28" height="28"
                                         src="{{ asset('../content/user') . '/' . optional($a->userdetail)->image ?? '' }}"
                                         class="rounded-circle m-r-5" alt="">
                                    {{ optional($a->userdetail)->name . ' ' . optional($a->userdetail)->last_name }}
                                </td>

                               <td>{{$a->userdetail->mobile ?? ''}}</td>
                               <td><span class="badge badge-soft-success">&#8377; {{$a->total_spent}}</span></td>
                            </tr>
                            @endforeach
                            @endif

                         </tbody>
                      </table>
                   </div>
                </div>
             </div>
          </div>
      </div>
   </div>
</div>
@section('js_user_page')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#dateFilter1').change(function () {
            let selectedValue = $(this).val();
            if (selectedValue) {
                window.location.href = "{{ route('admin.appoint.bestpatient') }}?date_filter=" + selectedValue;
            }
        });
    });
</script>

@endsection
@endsection
