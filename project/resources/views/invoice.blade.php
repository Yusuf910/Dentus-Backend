
<!DOCTYPE html>
    <html>
    <head>
    <style>
    body{font-size: 14px;}
    p{padding-top: 0px; padding-bottom: 0px; margin-bottom: 5px; margin-top: 5px;}
    </style>
    </head>
    <body>
    <table style="width: 100%; border-bottom: 1px solid #e4e4e4;">
        <tr>
            @php
            $logoPath = asset('content/admin/img/logo.png');
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        @endphp

        
    <td style="width: 30%; vertical-align: top;">
    <img src="{{$logoSrc}}" width="100">Dentus App
        </td>
    <td align="right" style="width: 70%; vertical-align: top;">
    <p style="font-size:30px;">INVOICE NO.</p>
    <p style="font-size:16px;">#4556</p>     
        </td>
        </tr>
      </table>
       </br>

    {{-- <table style="width: 100%">
    <td style="width: 40%; vertical-align: top;">
    <p><strong>Bill From</strong></p>
    <p style="font-size: 14px;"><span><strong>Company Name :</strong></span> Xyz Pvt. Ltd.</p>
    <p style="font-size: 14px;"><span><strong>Address :</strong></span> C-9/21 Rohini Sector-7</p>
    <p style="font-size: 14px;"><span><strong>Mobile No. :</strong></span> 9809347832</p>
        </td>
      </table> --}}
       </br>

    <table style="width: 100%;">
        <tr>
    <td style="width: 40%; vertical-align: top;">
    <p><strong>Bill To</strong></p>
    @php $name = $appointment->userdetail->name.' '.$appointment->userdetail->last_name;
        $gender = $appointment->userdetail->gender;
        if ($appointment->member_id > 0) {
            $name = ($appointment->memberdetail->name ?? $appointment->userdetail->name) . ' ' . ($appointment->memberdetail->last_name ?? $appointment->userdetail->last_name);
            $gender = $appointment->memberdetail->gender ?? $appointment->gender;
        }
      @endphp
    <p style="font-size: 14px;"><span><strong>Customer Name :</strong></span> {{ $name }}</p>
    <p style="font-size: 14px;"><span><strong>Mobile No. :</strong></span> {{ $appointment->user->mobile ?? $user->mobile}}</p>
        </td>

    <td align="right" style="width: 40%; vertical-align: top;">
    <p style="font-size: 14px;"><span><strong>Invoice Date :</strong></span> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    {{-- <p style="font-size: 14px;"><span><strong>Terms :</strong></span> Due on Receipt</p>
    <p style="font-size: 14px;"><span><strong>Due Date :</strong></span>25/08/2021</p>
    <p style="font-size: 14px;"><span><strong>P.O.# :</strong></span>00005</p> --}}
        </td>
        </tr>
       </table>

        </br>
    <table style="width:100%; border-collapse: collapse; border: 1px solid #000;" border="1">
    <tr align="left" style="background-color: #333; color: #fff;">
    <th style="font-size: 14px; text-align: center;">Booking Id</th>
    <th style="font-size: 14px; text-align: center;">Patient Name</th>
    <th style="font-size: 14px; text-align: center;">Doctor Name</th>
    <th style="font-size: 14px; text-align: center;">Clinic Name</th>
    <th style="font-size: 14px; text-align: center;">Consultation Type</th>
    <th style="font-size: 14px; text-align: center;">Appointment Type</th>
    <th style="font-size: 14px; text-align: center;">Symptoms</th>
    <th style="font-size: 14px; text-align: center;">Tax</th>
    <th style="font-size: 14px; text-align: center;">Total Amount</th>   
        </tr>

        <tr>
    <td style="font-size:16px; text-align: center;">{{ $appointment->id ?? ''}}</td>      
    <td style="font-size:16px; text-align: center;">{{ $name }}</td>      
    <td style="font-size:16px; text-align: center;">{{ $appointment->doctor->name ?? $doctor->name }}{{ $appointment->doctor->last_name ?? $doctor->last_name }}</td>      
    <td style="font-size:16px; text-align: center;">{{ $appointment->clinic ? $appointment->clinic->clinic_name : $clinic->clinic_name }}</td>      
    <td style="font-size:16px; text-align: center;">{{ $appointment->consultation_type ?? ''}}</td>
    <td style="font-size:16px; text-align: center;">{{ $appointment->appointment_type ?? ''}}</td>
    <td style="font-size:16px; text-align: center;">{{ $appointment->symptoms ?? ''}}</td>
    <td style="font-size:16px; text-align: center;">{{ $appointment->gst ?? ''}}</td>
    <td style="font-size:16px; text-align: center;">Rs {{ $appointment->subtotal ?? ''}}</td>
        </tr>

        </table>
        <br>


    <table style="width: 50%;" align="right">
        <tr>
            
    <!--  <td colspan="6"></td> -->
            
    <td style="text-align: right;">Subtotal</td>
    <td style="text-align: right;">Rs {{ $appointment->subtotal ?? ''}}</td>
        </tr>
        <tr>
            
    <!-- <td colspan="6"></td> -->
            
    {{-- <td style="text-align: right;">Discount</td>
    <td style="text-align: right;">Rs 200</td> --}}
        </tr>
        <tr>
            
    <!-- <td colspan="6"></td> -->
            
    <td style="text-align: right;">Taxes </td>
    <td style="text-align: right;">Rs {{ $appointment->gst }}</td>
         </tr>

    <tr style="background-color:#ededed;">
            
    <!--    <td colspan="6"></td> -->
            
    <td style="text-align: right;"><strong>Total Amount Paid</strong></td>
    <td style="text-align: right;"><strong>Rs {{ $appointment->subtotal + $appointment->gst }}</strong></td>
        </tr>
        </table>

    <table style="width: 100%;">
        <tr align="right">
            <td>
    <p style="font-size: 14px;">
    <span>Total In Words:</span> <strong>{{ \App\Helpers\NumberToWords::convert($appointment->subtotal + $appointment->gst) }}</strong></span>
            </p>
            </td>
            </tr>
           </table>
           </body>
           </html>