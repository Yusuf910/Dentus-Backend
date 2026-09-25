<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Pegasus</title>
      <link href="https://fonts.googleapis.com/css?family=Arizonia|Quicksand:300,400,500,600,700&display=swap" rel="stylesheet">
   </head>
   <?php $getdata = App\Models\Webinar::where('id',$data['webinarid'])->first(); ?>
   <body style="margin: 0; font-size:12pt;">
      <div style="background-image: url('{{asset('project/public/webinar/'.$getdata->certbackimage)}}'); background-repeat: no-repeat; background-position: center; background-size: contain; display: block;">
         <div style="padding: 75px;">

             <table style="width:100%;" align="right">
               <tr><td align="right">ID:{{$data['uniquerefrenceid']}}</td></tr>
             </table>
            <table style="width:100%;" align="center">
               <tr>
                  <td>
      <h1 style="text-align: center; font-size: 20pt; font-weight: 400; color:#624221; margin-bottom: 0px; margin-top:-10px">{{$getdata->certbackheading}}</h1>
                  </td>
               </tr>
               <tr>
                  <td align="center"><span style="font-size: 15pt; color:#624221;">Organized by</span></td>
               </tr>
            </table>
            <table style="width:100%;" align="center">
               <?php if ($getdata->certbacklogo): 
                  $logo = explode('|', $getdata->certbacklogo);
                  ?>
               <?php if (count($logo) > 0): ?>
                  <tr>
                  <?php foreach ($logo as $key): ?>
                     
      <td align="center"> <img src="{{asset('project/public/webinar/'.$key)}}" alt="certificate logo" style="width: 100px; display: block; margin: 0 auto;margin-top: 15px;"> </td>
                     
                  <?php endforeach ?>
                  </tr>
               <?php endif ?>
               <?php endif ?>
               
            </table>
            <table style="width:100%;" align="center">
               <tr>
                  
               </tr>
               <!-- <tr>
                  <td>
                      <h4 style="color: #F2914A;  margin: 8px; font-weight: 800; text-align: center; font-size: 32px; position: relative;     padding-bottom: 17px;">SAJU JACOB <div style="position: absolute; bottom: 0; width: 100px; height: 7px; border-radius: 30px; background-color: rgba(242,145,74,0.52); margin-left: 416px;/*left: calc(50% - 50px);*/"></div> 
                  </h4> </td>
                  </tr> -->
            </table>
            <table style="width:100%;" align="center">
               <tr>
                  <td>
                    
                     <?php $string = str_replace('$varname$', $data['name'], $getdata->certmidsentence);
                     $string2 = str_replace('$datename$', $data['datetime'], $string) ?>
                     <?=$string2?>
              
                  </td>
               </tr>
            </table>
            <table style="width:100%;" align="center">
               <tr>
                  <?php if ($per = json_decode($getdata->certbackpersons)): ?>
                    <?php foreach ($per as $kp): ?>
                       <td align="center">
                           <img src="{{asset('project/public/webinar/'.$kp[3])}}" width="100">
                           <p style="color: #3A3A3A; font-size: 10pt; margin: 5px;  font-weight: 600;">{{$kp[0]}}</p>
                           <p style="color: #3A3A3A;  font-size: 10pt; margin: 5px;"> {{$kp[1]}} </p>
                           <p style="color: #3A3A3A;  font-size: 10pt; margin: 5px; font-weight: 600;"> {{$kp[2]}} </p>
                        </td>
                    <?php endforeach ?>
                  <?php endif ?>
               </tr>
            </table>
            <!-- <table style="width:100%;" align="center">
               <tr>
                   <td>
                       <p style="font-size: 14pt; color: #3A3A3A; text-align: center;  font-weight: 800; margin-top: 35px;">This is a computer generated certificate, and doesn't require signature.</p>
                   </td>
               </tr>
               </table> -->
         </div>
      </div>
   </body>
</html>