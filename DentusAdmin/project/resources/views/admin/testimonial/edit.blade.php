@extends('layouts.admin') 
@section('styles')
<link href="{{asset('project/public/adminassets')}}/css/jquery.Jcrop.css" rel="stylesheet" />
<link href="{{asset('project/public/adminassets')}}/css/Jcrop-style.css" rel="stylesheet" />
@endsection 
@section('content')
<div class="main-content">
   <div class="page-content">
       <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <div class="row">
         <div class="col-12">
             <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                 <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
   
                 <div class="page-title-right">
                     <ol class="breadcrumb m-0">
                         <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                         <li class="breadcrumb-item active"><a href="{{ $listurl }}">{{ $listname }}</a></li>
                     </ol>
                 </div>
   
             </div>
         </div>
     </div>
      <!-- Main content -->
      <div class="row">
         <div class="col-12">
             <div class="card">
 
                 <div class="card-body p-3">
                  @if (count($errors) > 0)
                  <div class="alert alert-danger alert-dismissible">
                     <strong>Whoops!</strong> There were some problems with your input.<br><br>
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                     <ul>
                         @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                         @endforeach
                      </ul>
                  </div>
                  @endif
                  {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->id]]) !!}
                  
                     <div class="row">
                     @php
                     $lang = App\Models\AppLanguage::get();
                     $name = json_decode($a->description,true);
                     @endphp
                     @if($name)
                     @foreach ($lang as $l)
                           <div class="form-group row">
                              <label for="{{$l->name}}" class="col-sm-2 col-form-label">Name(In {{$l->name}})* <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $name[$l->id] }}" class="form-control" type="text" id="{{$l->name}}" placeholder="Enter Name(In {{$l->name}})" name="name[{{$l->id}}]">
                              </div>
                           </div>
                           @endforeach
                           @else 
                           <div class="form-group row">
                              <label for="" class="col-sm-2 col-form-label">Description<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                              <input required value="{{$a->description }}" class="form-control" type="text" id="" placeholder="Enter Name" name="description">
                              </div>
                           </div>
                                 @endif
                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">User<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                              <select class="form-control" name="user_id" required>
                                    <option value="">Select User</option>
                                    <?php $check = DB::table('users')->where('status',1)->get();
                                    foreach ($check as $n){
                                       ?>
                                   <option value="{{ $n->id }}"
                                    <?php
                                    if($n->id==$a->user_id){
                                        echo "selected";
                                    }
                                    else{
                                        echo "";
                                    }
                                    ?>
                                    >{{$n->name }}
                                  </option>
                                   <?php } ?>

                            </select>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">Position<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{$a->position}}" name="position" class="form-control" type="number" id="example-text-input" placeholder="eg. 1">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">In_home<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{$a->in_home}}" name="in_home" class="form-control" type="number" id="example-text-input" placeholder="eg. 1">
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="example-text-input" class="col-sm-2 col-form-label">Video URL<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{$a->video_url}}" name="video_url" class="form-control" type="text" id="example-text-input" placeholder="Video URL">
                              </div>
                           </div>


                           <div class="form-group row">
                              <label for="example-search-input" class="col-sm-2 col-form-label">Status<span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <select name="status" required class="form-control">
                                    <option <?= $a->status == 1 ? 'selected':'' ?> value="1">Active</option>
                                    <option <?= $a->status == 0 ? 'selected':'' ?> value="0">Inactive</option>
                                 </select>
                              </div>
                           </div>
                      
                     </div>
                     <!-- /.row -->
                 
                  <div class="box-footer">
                     <button type="submit" class="btn btn-rounded btn-primary model_img" alt="alert"  id="sa-success">Submit</button>
                  </div>
               </div>
               <!-- /.box -->       
            </div>
         </div>
      </div>
       </div>
      <!-- /.content -->
   </div>
   </div>
</div>
@section('js_user_page')
<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
<script src="{{asset('project/public/adminassets')}}/js/jquery.Jcrop.js"></script>
<script src="{{asset('project/public/adminassets')}}/js/jquery.SimpleCropper.js"></script>
<script>
   $('.cropme').simpleCropper();
   CKEDITOR.replace('description');
</script>
@endsection
@endsection