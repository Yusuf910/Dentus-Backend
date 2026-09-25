<!DOCTYPE html>
<html>
   <head>
      <style>
         body{font-size: 14px;}
         p{padding-top: 0px; padding-bottom: 0px; margin-bottom: 5px; margin-top: 5px;}
      </style>
   </head>
   <body>
      <table style="width: 100%;">
         <tr>
            <td style="width: 30%; vertical-align: top;">
               <img src="{{ asset('assets/images') }}/logo-light-text3.png" width="250"></
            </td>
         </tr>
      </table>
      <table style="width: 100%; border-bottom: 1px solid #e4e4e4;">
         <tr>
            <td align="Center" style="width: 100%; vertical-align: top;">
               <p style="font-size:18px;">Printed Books Exempt as Ministry of Finance Notification No. 2/2017-Center Tax (Rate) dated 28-06-2017.</p>
               <p style="font-size:18px;">Tariff Item No. 4901, 4903 and 4905 under Center Good and Services Tax Act, 2017 (12 of 2017)</p>
               <p style="font-size:26px;">Bill Of Supply</p>
            </td>
         </tr>
      </table>
      <table style="width: 100%;">
         <tr>
            <td style="width: 40%; vertical-align: top;">
               <p style="font-size: 14px;"><strong>GSTIN :</strong> {{$settings['ecomm_invoice_gstin']}}</p>
               <p style="font-size: 14px;"><strong>Reference No :</strong> {{$order['refnofull']}}</p>
               <p style="font-size: 14px;"><strong>Executive Date :</strong> {{date('d/m/Y',strtotime($order['created_at']))}}</p>
               <p style="font-size: 14px;"><strong>Booking Remarks :</strong> </p>
            </td>
            <td align="Center" style="width: 30%; vertical-align: top;">
               <p style="font-size: 14px;"><strong>Order No :</strong> {{$order['order_no']}}</p>
               <p style="font-size: 14px;"><strong>Order Date :</strong> {{date('d/m/Y',strtotime($order['created_at']))}}</p>
               <p style="font-size: 14px;"><strong>Payment Term :</strong> Online</p>
               <p style="font-size: 14px;"><strong>Freight Terms :</strong> Paid</p>
            </td>
            <td align="right" style="width: 30%; vertical-align: top;">
               <p style="font-size: 14px;"><strong>Original</strong></p>
            </td>
         </tr>
      </table>
      <br>
      <br>
      <?php $address1 = App\Models\OrderAddress::where('order_id',$order['id'])->where('address_type',1)->first();
            $address2 = App\Models\OrderAddress::where('order_id',$order['id'])->where('address_type',1)->first();
      ?>
      <table style="width: 100%;">
         <tr>
            @if($address1)
            <td style="width: 40%; vertical-align: top;">
               <p style="border-bottom: 1px solid #e4e4e4;"><strong>Billing Address</strong></p>
               <p style="font-size: 14px;">{{ucfirst($address1->name)}}</p>
               <p style="font-size: 14px;">{{$address1->address}}{{', '.$address1->locality ?? ''}}</p>
               <p style="font-size: 14px;">{{$address1->city}} {{$address1->pincode}}</p>
               <p style="font-size: 14px;">{{$address1->state}}</p>
               <p style="font-size: 14px;">{{$address1->country}}</p>
               <p style="font-size: 14px;">{{$address1->type}}</p>
               <p style="font-size: 14px;"><strong>Mobile :</strong> {{$address1->mobile}}</p>
            </td>
            @endif
            @if($address2)
            <td align="left" style="width: 40%; vertical-align: top;">
               <p style="border-bottom: 1px solid #e4e4e4;"><strong>Delivery Address</strong></p>
               <p style="font-size: 14px;">{{ucfirst($address2->name)}}</p>
               <p style="font-size: 14px;">{{$address2->address}}{{', '.$address2->locality ?? ''}}</p>
               <p style="font-size: 14px;">{{$address2->city}} {{$address2->pincode}}</p>
               <p style="font-size: 14px;">{{$address2->state}}</p>
               <p style="font-size: 14px;">{{$address2->country}}</p>
               <p style="font-size: 14px;">{{$address2->type}}</p>
               <p style="font-size: 14px;"><strong>Mobile :</strong> {{$address2->mobile}}</p>
            </td>
            @endif
         </tr>
      </table>
      </br>
      <?php $ol = App\Models\OrderProduct::where('order_id',$order['id'])->orderBy('id','ASC')->get(); ?>
      <table style="width:100%; border-collapse: collapse; border: 1px solid #000;" border="1">
         <tr align="left" style="color: #000;">
            <th style="font-size: 14px; text-align: center;">SI.NO</th>
            <th style="font-size: 14px; text-align: center;">Product</th>
            <th style="font-size: 14px; text-align: center;">Unit Price</th>
            <th style="font-size: 14px; text-align: center;">Quantity</th>
            <th style="font-size: 14px; text-align: center;">Net Amount</th>
            <th style="font-size: 14px; text-align: center;">Tax Rate</th>
            <th style="font-size: 14px; text-align: center;">Tax Type</th>
            <th style="font-size: 14px; text-align: center;">Tax Amount</th>
            <th style="font-size: 14px; text-align: center;">Total</th>
         </tr>
         @if($ol->count() > 0)
         <?php $ic = 1; ?>
         @foreach($ol as $a)
         <?php 
            $taxrate1 = '0%';
            $taxrate2 = '0%';
            $taxtype1 = 'None';
            $taxtype2 = 'None';
            if ($address2->state == 'Uttar Pradesh' || $address2->state == 'Uttar Pradesh') {
               $txrate = round($a->tax_percentage/2);
               $taxrate1 = $txrate.'%';
               $taxrate2 = $txrate.'%';
               $taxtype1 = 'CGST';
               $taxtype2 = 'SGST';
            }
            else {
               $taxrate1 = $a->tax_percentage.'%';
               $taxrate2 = '';
               $taxtype1 = 'IGST';
               $taxtype2 = '';
            }
         ?>
         @if($a->type == 1)
         <?php 
            if ($a->type == 1 && $a->book_online_support  == 1) {
               $hsn = 'HSN:'.$a->book->hsn_code_online_support;
               $ex = '(Ebook)';
            }
            else {
               $hsn = 'HSN:'.$a->book->hsn_code;
               $ex = '(Physical Book)';
               $taxtype1 = 'None';
               $taxtype2 = 'None';
            }
            
         ?>
         <tr>
            <td style="font-size:16px;">{{$ic}}</td>
            <td style="font-size:16px;">{{$a->product_name}}|{{$ex}}|{{$hsn}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)$a->price, 2, '.', '')}}</td>
            <td style="text-align: center;">{{$a->qty}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)($a->total_price - $a->total_tax), 2, '.', '')}}</td>
            <td style="text-align: center;">{{$taxrate1}}<br>{{$taxrate2}}</td>
            <td style="text-align: center;">{{$taxtype1}}<br>{{$taxtype2}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$a->total_tax}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$a->total_price}}</td>
         </tr>
         @elseif($a->type == 2)
         <tr>
            <td style="font-size:16px;">{{$ic}}</td>
            <td style="font-size:16px;">{{$a->product_name}}|{{'HSN:'.$a->uniform->hsn_code}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)($a->total_price - $a->total_tax)/$a->qty, 2, '.', '')}}</td>
            <td style="text-align: center;">{{$a->qty}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)($a->total_price - $a->total_tax), 2, '.', '')}}</td>
            <td style="text-align: center;">{{$taxrate1}}<br>{{$taxrate2}}</td>
            <td style="text-align: center;">{{$taxtype1}}<br>{{$taxtype2}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$a->total_tax}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$a->total_price}}</td>
         </tr>
         @elseif($a->type == 3)
         <tr>
            <td style="font-size:16px;">{{$ic}}</td>
            <td style="font-size:16px;">{{$a->product_name}}|{{'HSN:'.$a->notebook->hsn_code}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)$a->price, 2, '.', '')}}</td>
            <td style="text-align: center;">{{$a->qty}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)($a->total_price - $a->total_tax), 2, '.', '')}}</td>
            <td style="text-align: center;">{{$taxrate1}}<br>{{$taxrate2}}</td>
            <td style="text-align: center;">{{$taxtype1}}<br>{{$taxtype2}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$a->total_tax}}</td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$a->total_price}}</td>
         </tr>
         @endif
         <?php $ic++; ?>
         @endforeach
         @endif
         <tr>
            <td>
               <strong></strong>
            </td>
            <td style="font-size:16px;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"><strong>Tax</strong></td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)$order['tax_amt'], 2, '.', '')}}</td>
            <td style="text-align: center;"></td>
            
         </tr>
         <tr>
            <td>
               <strong></strong>
            </td>
            <td style="font-size:16px;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"><strong>Delivery Charges</strong></td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{$order['delivery_price'] ?? 0}}</td>
            <td style="text-align: center;"></td>
         </tr>
         <tr>
            <td>
               <strong></strong>
            </td>
            <td style="font-size:16px;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"><strong>Coupan Discount</strong></td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>-{{$order['coupon_discount'] ?? 0}}</td>
            <td style="text-align: center;"></td>
         </tr>
         <tr>
            <td>
               <strong></strong>
            </td>
            <td style="font-size:16px;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"><strong>Sub-Total</strong></td>
            <td style="text-align: center;"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)($order['total_mrp']+$order['delivery_price']-$order['coupon_discount']), 2, '.', '')}}</td>
            <td style="text-align: center;"></td>
         </tr>
      </table>
      <table style="width: 50%;" align="right">
         <tr>
            <!-- <td colspan="6"></td> -->
            <td style="width: 50%;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: right;"><strong>Total</strong></td>
            <td style="text-align: center;"><strong><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{number_format((double)($order['net_amount']), 2, '.', '')}}</strong></td>
            <td style="text-align: center;"></td>
         </tr>
      </table>
      <table style="width: 100%;">
         <tr align="right">
            <td style="width: 40%; vertical-align: top;">
               <p style="font-size: 14px;"><strong>E.& O.E.</strong></p>
               <p style="font-size: 14px;"><strong>For Goyal Brothers Prakashan</strong></p>
            </td>
         </tr>
      </table>
      <table>
         <tr style="height:10px;"></tr>
      </table>
      <table>
      <table style="width: 100%">
         <tr align="right">
            <td>Authorized Signature _______________________</td>
         </tr>
      </table>
   </body>
</html>