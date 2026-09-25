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
         body{font-size: 15px;}
         p{margin-top: 5px;
         margin-bottom: 5px;}
         h3{margin-top: 5px;
         margin-bottom: 5px;}
         .med-1{font-weight: 600;}
         /* hr{height:1px;border-width:0;background-color:#efefef;}*/
      </style>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

   </head>
   <body>
      <table style="width: 100%">
         <tr align="center">
            <td style="line-height: 1.4;">
               <strong style="font-size: 20px;">Dr.{{ $dr_name }}</strong><br>
               {{ $dr_spec }}<br>
               Medical Registration Number:{{ $clinic_register }}<br>
               {{ $clinic_address }}<br>
               {{$dr_email}}, Phone: {{$dr_mobile}}
            </td>
         </tr>
      </table>
      <table style="width: 100%">
         <tr>
            <td style="width: 49%;">
               <p style="line-height: 2;"> 
                  <strong style="font-size: 16px">Date: {{ $booking_created }}</strong><br>
               </p>
            </td>
            <td align="right" style="width: 49%;">
               <p style="line-height: 2;"> 
                  <strong style="font-size: 16px">Prescription id: {{ $booking_id }}</strong><br>
               </p>
            </td>
         </tr>
      </table>
      <table style="width: 100%">
         <p><strong>Name:</strong> {{ $user_name }}</p>
         {{-- 
         <p><strong>Age:</strong> 30</p>
         --}}
         <p><strong>Gender:</strong> {{ $user_gender }}</p>
      </table>
      <table style="width:100%">
         <tr>
            <td>
               <h3>Diagnosis</h3>
               <p>{{ $diagnosis ?? 'N/A' }}
               </p>
            </td>
         </tr>
         <tr>
            <td>
               <h3>General Advice</h3>
               <p>{{ $advice ?? 'N/A' }}</p>
            </td>
         </tr>
         <tr>
            <td>
               <h3>Notes</h3>
               <p>{{ $notes ?? 'N/A' }}</p>
            </td>
         </tr>
         <tr>
            <td>
               <h3>Medicine</h3>
               <img src="{{asset('content')}}/prescription.png" width="50">
               @foreach($prescription as $medication)
               <p class="med-1">{{ $medication['drug'] }}</p>
               <p>{{ $medication['dosage'] }}, {{ implode(', ', $medication['time']) }}</p>
               <p>{{ $medication['repeat'] }},{{ $medication['taken'] }}, {{ $medication['duration'] }}</p>
               <hr>
               @endforeach
               {{-- 
               <p class="med-1">Azee-500 Tablet</p>
               <p>2 tablet, Night</p>
               <p>Daily After food, for 5 Days</p>
               <hr>
               --}}
            </td>
         </tr>
      </table>
      <!--   <table style="width: 100%;">
         <tr>
            <td>
               <h3>Special Instructions</h3>
               <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
            </td>
         </tr>
         </table> -->
      <table style="width: 100%;">
         <tr align="right">
            <td>
               <img src="{{asset('content')}}/clipart3527164.png" width="150">
               <p>Doctor's Signature</p>
               <p>22/20/2024</p>
            </td>
         </tr>
      </table>
      <table style="width: 100%;">
         <tr align="center">
            <td>
               <p style="color: #b5b5b5;">Powered By</p>
               <img src="https://dentusapp.com/images/logo.png" width="150">
               <p style="font-size: 14px; color: #b5b5b5;">
                  Disclaimer: This prescription is based on the information provided by you in an online consultation.
               </p>
            </td>
         </tr>
      </table>
   </body>
   <script>
    window.onload = function () {
        // Select the content you want to convert to PDF
        var element = document.body; // Full page, or use document.getElementById("content") for a specific section

        html2pdf(element, {
            margin: 10,
            filename: 'prescription.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, logging: true, dpi: 192, letterRendering: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        });
    };
</script>

</html>