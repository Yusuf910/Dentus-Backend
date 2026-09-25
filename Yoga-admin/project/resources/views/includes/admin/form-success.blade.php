@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissible">
  <strong>Whoops!</strong> There were some problems with your input.<br><br>
  <ul>
     @foreach ($errors->all() as $error)
       <li>{{ $error }}</li>   
     @endforeach
  </ul>
</div>
@endif   
      
@if(Session::has('success'))
<div class ="alert alert-success alert-dismissible fade show " role="alert">
  <i class="mdi mdi-check-all me-2"></i>
  <strong>{{Session::get('success')}}</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label ="Close"></button>
</div>
@endif

@if(Session::has('delete'))
<div class ="alert alert-danger alert-dismissible fade show" role="alert">
  <i class="mdi mdi-check-all me-2"></i>
  <strong>{{Session::get('delete')}}</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label ="Close"></button>
</div>
@endif

@if(Session::has('update'))
<div class ="alert alert-info alert-dismissible fade show" role="alert">
  <i class="mdi mdi-check-all me-2"></i>
  <strong>{{Session::get('update')}}</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label ="Close"></button>
</div>
@endif

@if(Session::has('something'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="mdi mdi-alert-outline me-2"></i>
    <strong>{{Session::get('something')}}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label ="Close"></button>
</div>
@endif

@if(Session::has('failure'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="mdi mdi-block-helper me-2"></i>
    <strong>{{Session::get('failure')}}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label ="Close"></button>
</div>
@endif
@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="mdi mdi-block-helper me-2"></i>
    <strong>{{Session::get('error')}}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label ="Close"></button>
</div>
@endif




