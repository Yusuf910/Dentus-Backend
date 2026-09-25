<!DOCTYPE html>
<html>
   <head>
      <style>
         table.bottomBorder {
         border-collapse: collapse;
         }
         table.bottomBorder td,
         table.bottomBorder th {
         border: 1px solid #000;
         padding: 10px;
         text-align: left;
         }
         body{font-size: 16px;}

         p{margin-top: 5px;
    margin-bottom: 5px;}
      </style>
   </head>
   <body>
      
      <table style="width: 100%">
        
         <tr>
            <td style="width: 30%;"><img src="https://dentusapp.com/images/logo.png" width="250"></td>
            <td align="right" style="width: 70%;">
               <h2>BILL OF REFUND</h2>
               <p>Invoice #{{$a->invoice_no}}</p>
               <p>Invoice Date: {{date('d/m/Y',strtotime($a->updated_at))}}</p>
            </td>
         </tr>
      </table>
      <table style="width: 100%">
         <tr>
            <td style="width: 49%;">
               <p style="line-height: 2;"> <strong style="font-size: 20px">Billing From</strong><br>
                  <strong>{{$a->clinicdetail->name ?? $a->doctordetail->name ?? ''}}</strong>
               </p>
            </td>
            <td align="right" style="width: 49%;">
               <p style="line-height: 2;"> <strong style="font-size: 20px">Billing To</strong><br>
                  <strong>{{$a->userdetail->name}}</strong>
               </p>
            </td>
         </tr>
          
      </table>
     
    
      <table style="width:100%; border-collapse: collapse;" border="1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{$a->treatment_name}}</td>
                    <td>1</td>
                    <td>Rs {{$a->subtotal}}</td>
                    <td>Rs {{$a->total_amount}}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Subtotal</td>
                    <td>Rs.{{$a->subtotal}}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Tax</td>
                    <td>Rs.{{$a->gst}}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" style="text-align: right;">Total</td>
                    <td>Rs.{{$a->total_amount}}</td>
                </tr>
            </tfoot>
        </table>
      <table style="width: 100%;">
         <tr>
            <td>
               <p>
                  The invoice is a computer generated invoice, hence no signature is required
               </p>
            </td>
         </tr>
      </table>
      <table style="width: 100%; position: fixed;
            bottom: 0;" align="center" >
         <tr align="center">
            <td><p>Powered By</p><img src="https://dentusapp.com/images/logo.png" width="150"></td>
         </tr>
      </table>
   </body>
</html>