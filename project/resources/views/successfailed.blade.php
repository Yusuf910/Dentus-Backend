<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<style>
.center {
  padding: 120px 0;
  border: 3px solid #fff;
  text-align: center;
}


button{background: #33ba7c;
    border: none;
    color: #fff;
    padding: 10px 30px 10px 30px;
    font-size: 20px;
    border-radius: 10px;}

</style>
</head>
<body>
<div class="center">
	@if($what == 'success')
	  <img src="{{asset('adminassets/')}}/checked.png" width="100px"><br>
	  <h1>Congratulation your payment successfully done!</h1>
	  <p>TrxnID: {{$trxnid}}</p>
	@else
	  <img src="{{asset('adminassets/')}}/remove.png" width="100px"><br>
	  <h1>Payment Failed</h1>
	@endif
</div>

</body>
</html>


