@extends('layouts.admin')
@section('content')
<?php $data2 = App\Models\Treatment::whereNOTIN('status', [2])->where('doctor_id',auth()->user()->id)->orderBy('updated_at', 'ASC')->get();?>
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Dashboard </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">{{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         @include('includes.admin.form-success') 
         <div class="col-sm-12">
            <div class="card card-table show-entire">
               <div class="card-body">
                  <div class="page-table-header mb-2">
                     <div class="row align-items-center">
                        <div class="col">
                           <div class="doctor-table-blk">
                              <h3>{{$title}}</h3>
                              <div class="doctor-search-blk">
                                 
                                 <div class="add-group">
                                    <a data-bs-toggle="modal" data-bs-target="#centermodal" href="#" class="btn btn-primary add-pluss ms-2"><img src="{{asset('content/admin')}}/img/icons/plus.svg" alt></a>
                                    <a href="{{$listurl}}" class="btn btn-primary doctor-refresh ms-2"><img src="{{asset('content/admin')}}/img/icons/re-fresh.svg" alt></a>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                           
                           <a href="javascript:;" class=" me-2"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-03.svg" alt></a>
                           <a href="javascript:;"><img src="{{asset('content/admin')}}/img/icons/pdf-icon-04.svg" alt></a>
                        </div>
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table border-0 custom-table comman-table datatable mb-0 table-formatting">
                        <thead>
                           <tr>
                              <th>Name</th>
                              <th>type</th>
                              <th>description</th>
                              <th>File</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($data->count() > 0)
                           @foreach($data as $a)
                           @php
                               $descriptionWords = str_word_count($a->description, 2);
                               $limitedDescription = implode(' ', array_slice($descriptionWords, 0, 5));
                               
                           @endphp
                           <tr>
                              <td class="profile-image">{{$a->name}}</td>
                              <td>{{$a->type}}</td>
                              <td>{{$limitedDescription}}</td>
                              <td>
                                 @if($a->type == 'video')
                                 <a target="_blank" href="{{$a->video_link}}" type="button" class="btn btn-primary" ><i class="fas fa-eye"></i></a>
                                 @else
                                 <img width="28" height="28" src="{{asset('content/blogs/'.$a->image ?? 'default.png')}}" class="rounded-circle m-r-5" alt>
                                 @endif
                              </td>
                              <td>
                                  <button data-bs-toggle="modal" data-bs-target="#centermodaledit{{$a->id}}" type="button" class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
                                 <a href="#" class="btn btn-danger" href="#" data-bs-toggle="modal" data-bs-target="#modal-danger" onclick="sendid('{{$a->id}}')" data-userid="{{$a->id}}"><i class="fas fa-trash"></i></a>
                              </td>
                           </tr>
                           <div class="modal fade" id="centermodaledit{{$a->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                               <div class="modal-content">
                                  <div class="modal-header">
                                     <h4 class="modal-title" id="myCenterModal2Label">Edit Blog</h4>
                                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                  
                                     {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$importurl, $a->id]]) !!}
                                       {{ csrf_field() }}
                                       <div class="row">
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="name" class="form-label">Name*</label>
                                              <input type="text" required class="form-control" name="name" value="{{$a->name}}">
                                              </div>
                                          </div>  
                                          
                                          
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="description" class="form-label">Description*</label>
                                              <textarea type="text" required class="form-control" name="description" value="">{{$a->description}}</textarea>
                                              </div>
                                          </div> 
                                          
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="type" class="form-label">Type*</label>
                                              <select name="type" required class="form-control">
                                               <option {{$a->type == 'video' ? 'selected' : ''}} value="video">Video</option>
                                               <option {{$a->type == 'blog' ? 'selected' : ''}} value="blog">Blog</option>
                                            </select>
                                              </div>
                                          </div>
                                          <div class="col-md-12">
                                              <div class="mb-3">
                                              <label for="video_link" class="form-label">Video Url</label>
                                              <input value="{{$a->video_link}}" type="url" class="form-control" name="video_link">
                                              </div>
                                          </div>  
                                         <div class="col-md-12"><div class="mb-3">
                                          <label for="video_link" class="form-label">OR</label></div> 
                                         <div class="col-12 col-md-6 col-xl-6">
                                             <div class="input-block local-forms">
                                                <label>Image<span class="login-danger">*</span></label>
                                                <input type="file" class="form-control" name="image"  accept="image/*">
                                             </div>
                                          </div> 
                                           @if($a->type == 'video')
                                          @else
                                           <div class="col-12 col-md-6 col-xl-6">
                                             <div class="input-block local-forms">
                                          <img width="100" height="100" src="{{asset('content/blogs/'.$a->image ?? 'default.png')}}" class="rounded-circle m-r-5" alt>
                                           </div>
                                          </div> 
                                          @endif  
                                          <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
                                      </div>
                                     </form>
                                  </div>
                               </div>
                            </div>
                           </div>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal modal-danger fade" id="modal-danger">
    <div class="modal-dialog">
       <div class="modal-content bg-danger">
          <div class="modal-header">
             <h4 class="modal-title">Delete</h4>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
             <p>Are you sure you want to delete this?</p>
          </div>
          <div class="modal-footer">
             <form method="POST" action="{{ $destroyurl }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="user_id" name="id" value="">
                <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
             </form>
          </div>
       </div>
       <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
 </div>
@section('js_user_page')
<div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
       <div class="modal-header">
          <h4 class="modal-title" id="myCenterModal2Label">Add Social Connect</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
       
          <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="name" class="form-label">Name*</label>
                   <input type="text" required class="form-control" name="name" value="">
                   </div>
               </div>  
               
               
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="description" class="form-label">Description*</label>
                   <textarea type="text" required class="form-control" name="description" value="">
                   </textarea>
               </div> 
               
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="type" class="form-label">Type*</label>
                   <select name="type" required class="form-control">
                    <option  value="video">Video</option>
                    <option  value="blog">Blog</option>
                 </select>
                   </div>
               </div>
               <div class="col-md-12">
                   <div class="mb-3">
                   <label for="video_link" class="form-label">Video Url</label>
                   <input type="url"  class="form-control" name="video_link">
                   </div>
               </div>  
               <div class="col-md-12"><div class="mb-3">
                   <label for="video_link" class="form-label">OR</label></div> 
              <div class="col-12 col-md-6 col-xl-6">
                  <div class="input-block local-forms">
                     <label>Image<span class="login-danger">*</span></label>
                     <input type="file" class="form-control" name="image"  accept="image/*">
                  </div>
               </div>    
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
           </div>
          </form>
       </div>
    </div>
 </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
   function sendid(id) {
       console.log("User ID:", id); // Debugging output
       $('#user_id').val(id);
       
   };



</script>
@endsection
@endsection