@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{$listurl}}">Subscription </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Edit Subscription</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
<div class="card">
    <div class="card-body">

        <h4>Edit Subscription</h4>

        @include('includes.admin.form-success')

        <form method="POST"
              action="{{ route('admin.subscription.update_subscription', $subscription->id) }}">
            @csrf
            <div class="mb-3">
                <label>User</label>
                <input type="text"
                       class="form-control"
                       value="{{ $subscription->user->name }}"
                       disabled>
            </div>

            <!-- PACKAGE INFO (READ-ONLY) -->
            <div class="mb-3">
                <label>Package</label>
                <input type="text"
                       class="form-control"
                       value="{{ $subscription->subscription->name }}"
                       disabled>
            </div>

            <!-- START DATE (READ-ONLY) -->
            <div class="mb-3">
                <label>Start Date</label>
                <input type="date"
                       class="form-control"
                       value="{{ $subscription->start_date }}"
                       disabled>
            </div>

            <!-- EXPIRY DATE (EDITABLE) -->
            <div class="mb-3">
                <label>Expiry Date</label>
                <input type="date"
                       name="end_date"
                       class="form-control"
                       value="{{ $subscription->end_date }}"
                       required>
            </div>

            <!-- STATUS -->
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $subscription->status == 1 ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ $subscription->status == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <button class="btn btn-primary">
                Update Subscription
            </button>

        </form>
    </div>
</div>
</div>
      </div>
   </div>
</div>
@endsection
