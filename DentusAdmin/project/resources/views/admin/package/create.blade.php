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
                  <li class="breadcrumb-item active">Add Subscription</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body pt-4">
                  @include('includes.admin.form-success') 
                     @if (count($errors) > 0)
                    <div class="alert alert-danger">
                      <strong>Whoops!</strong> There were some problems with your input.<br><br>
                      <ul>
                         @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                         @endforeach
                      </ul>
                    </div>
                  @endif
                  <form method="POST" action="{{ route('admin.subscription.store_subscription') }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                                <label>Doctors <span class="login-danger">*</span></label>
                                 <select name="user_id" class="form-control" required>
                                   <option value="">Select</option>
                                   @foreach($users as $u)
                                       <option value="{{ $u->id }}">{{ $u->name }}</option>
                                   @endforeach
                               </select>
                              </div>
                           </div>
                        <div class="mb-3">
                            <label>Package</label>
                            <select name="subscription_id" id="subscription_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach($packages as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="premiumBox"></div>

                        <input type="hidden" name="amount" id="amount">
                        <input type="hidden" name="tax_amount" id="tax_amount">
                        <input type="hidden" name="extra_amount" value="0">
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5>Payment Details</h5>

                                <table class="table table-borderless">
                                    <tr>
                                        <td>Basic Package</td>
                                        <td class="text-end">₹ <span id="base_price">0</span></td>
                                    </tr>

                                    <tr>
                                        <td>Premium Features</td>
                                        <td class="text-end">₹ <span id="premium_price">0</span></td>
                                    </tr>

                                    <tr>
                                        <td>Service Fee & Tax</td>
                                        <td class="text-end">₹ <span id="tax_price">0</span></td>
                                    </tr>

                                    <tr class="fw-bold border-top">
                                        <td>Total Payable</td>
                                        <td class="text-end">₹ <span id="total_price">0</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                           <div class="doctor-submit text-start">
                              <button type="submit" class="btn btn-primary submit-form me-2">Save</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

<script>
$('#subscription1_id').on('change', function () {
    let id = $(this).val();
    $('#premiumBox').html('');

    if (!id) return;

    $.get("{{ url('admin/user-subscription/subscription/detail') }}/" + id, function (res) {

        let tax = res.subscription.master_tax.taxvalue;
        let price = res.subscription.price;
        let taxAmount = (price * tax) / 100;

        $('#amount').val(price);
        $('#tax_amount').val(taxAmount.toFixed(2));

        let html = '<h5>Premium Addons</h5>';

        res.subscription.features.forEach(f => {
            if (f.pivot.price > 0) {
                html += `
                    <div>
                        <input type="checkbox" name="premium_features[]" value="${f.pivot.id}">
                        ${f.name} - ₹${f.pivot.price}
                    </div>
                `;
            }
        });

        $('#premiumBox').html(html);
    });
});


</script>
<script>
let basePrice = 0;
let premiumTotal = 0;
let taxPercent = 0;

$('#subscription_id').on('change', function () {

    let id = $(this).val();
    if (!id) return;

    $.get("{{ url('admin/user-subscription/subscription/detail') }}/" + id, function (res) {

        basePrice = parseFloat(res.subscription.price);
        taxPercent = parseFloat(res.subscription.master_tax.taxvalue);

        $('#base_price').text(basePrice.toFixed(2));

        premiumTotal = 0;
        let html = '<h6 class="mt-3">Premium Addons</h6>';

        res.subscription.features.forEach(f => {
            let price = parseFloat(f.pivot.price);

            if (price > 0) {
                html += `
                    <div>
                        <input type="checkbox"
                               class="premium-check"
                               data-price="${price}"
                               name="premium_features[]"
                               value="${f.pivot.id}">
                        ${f.id == 131 ? '✔️ ' : ''}${f.name} - ₹${price} - (${f.pivot.unlimited == 1 ? '∞' : f.pivot.quantity} quantity)
                    </div>
                `;
            }
        });

        $('#premiumBox').html(html);

        calculateTotal();
    });
});

$(document).on('change', '.premium-check', function () {
    premiumTotal = 0;

    $('.premium-check:checked').each(function () {
        premiumTotal += parseFloat($(this).data('price'));
    });

    $('#premium_price').text(premiumTotal.toFixed(2));
    calculateTotal();
});

function calculateTotal() {
    let subTotal = basePrice + premiumTotal;
    let taxAmount = (subTotal * taxPercent) / 100;
    let total = subTotal + taxAmount;

    $('#tax_price').text(taxAmount.toFixed(2));
    $('#total_price').text(total.toFixed(2));

    // Hidden fields (API compatible)
    $('#amount').val(subTotal.toFixed(2));
    $('#tax_amount').val(taxAmount.toFixed(2));
}
</script>

@endsection
@endsection
