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
   </head>
   <body>
      <table style="width: 100%">
         <tr align="center">
            <td style="line-height: 1.4;">
               <strong style="font-size: 20px;">Dr.{{ $data['dr_name'] }}</strong><br>
               {{ $data['dr_spec'] }}<br>
               Medical Registration Number:{{ $data['clinic_register'] }}<br>
               {{ $data['clinic_address'] }}<br>
               {{$data['dr_email']}}, Phone: {{$data['dr_mobile']}}
            </td>
         </tr>
      </table>
      <table style="width: 100%">
         <tr>
            <td style="width: 49%;">
               <p style="line-height: 2;"> 
                  <strong style="font-size: 16px">Date: {{ $data['booking_created'] }}</strong><br>
               </p>
            </td>
            <td align="right" style="width: 49%;">
               <p style="line-height: 2;"> 
                  <strong style="font-size: 16px">Prescription id: {{ $data['booking_id'] }}</strong><br>
               </p>
            </td>
         </tr>
        
      </table>
      <table style="width: 100%">
           <p><strong>Name:</strong> {{ $data['user_name'] }}</p>
                {{-- <p><strong>Age:</strong> 30</p> --}}
               <p><strong>Gender:</strong> {{ $data['user_gender'] }}</p>
      </table>

      <table style="width:100%">

         <tr>
            <td>
               <h3>Diagnosis</h3>
               <p>{{ $data['diagnosis'] ?? 'N/A' }}
               </p>
            </td>
         </tr>
         <tr>
            <td>
               <h3>General Advice</h3>
               <p>{{ $data['advice'] ?? 'N/A' }}</p>
            </td>
         </tr>
         <tr>
            <td>
               <h3>Notes</h3>
               <p>{{ $data['notes'] ?? 'N/A' }}</p>
            </td>
         </tr>
         <tr>
            <td>
               <h3>Medicine</h3>
               <!-- <img src="http://15.206.121.126/Dentus/content/prescription.png" width="50"> -->
               @foreach($data['prescription'] as $medication)
               <p class="med-1">{{ $medication['drug'] }}</p>
               <p>{{ $medication['dosage'] }}, {{ implode(', ', $medication['time']) }}</p>
               <p>{{ $medication['repeat'] }},{{ $medication['taken'] }}, {{ $medication['duration'] }}</p>
               <hr>
               @endforeach
               {{-- <p class="med-1">Azee-500 Tablet</p>
               <p>2 tablet, Night</p>
               <p>Daily After food, for 5 Days</p>
               <hr> --}}
               
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
      <!-- <table style="width: 100%;">
         <tr align="right">
            <td>
               <img src="http://15.206.121.126/Dentus/content/clipart3527164.png" width="150">
               <p>Doctor's Signature</p>
               <p>22/20/2024</p>
            </td>
         </tr>
      </table> -->
      <table style="width: 100%;">
         <tr align="center">
            <td>
               <p style="color: #b5b5b5;">Powered By</p>
               <!-- <img src="https://dentusapp.com/images/logo.png" width="150"> -->
               <p style="font-size: 14px; color: #b5b5b5;">
                  Disclaimer: This prescription is based on the information provided by you in an online consultation.
               </p>
            </td>
         </tr>
      </table>
   </body>
</html>