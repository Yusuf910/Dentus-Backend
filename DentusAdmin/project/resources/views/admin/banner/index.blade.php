@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{$title}}
                  </h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal"><i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
                        </a> 
                        <!-- <button type="button" class="btn btn-info waves-effect waves-light"><i class="bx bx-check-double font-size-16 align-middle me-2"></i> Upload Excel
                           </button> -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- end page title -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body table-responsive">
                    @include('includes.admin.form-success') 
                     <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100 table-sm">
                        <thead>
                           <tr>
                              <th>#ID</th>
                              <th>Image</th>
                              <th>Main Type</th>
                              <th>For</th>
                              <th>Link Type</th>
                              <th>Position</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           <?php $i=1; ?>
                           @foreach ($data as $a)
                           <tr>
                              <td>{{ $i }}</td>
                              <td><img height="100" width="100" src="{{ asset('content/banner') }}/{{$a->image}}" alt=""></td>
                              <td><?php
                                 if ($a->maintype == 1) {
                                    echo "Top";
                                 }
                                 elseif ($a->maintype == 2) {
                                    echo "Middle";
                                 }
                                 elseif ($a->maintype == 3) {
                                    echo "Class";
                                 }

                                 
                                 else{
                                    echo "TopMiddle";
                                 }
                              ?></td>

                              <td><?php
                                 if ($a->type == 1) {
                                    echo "Website";
                                 }
                                 else{
                                    echo "App";
                                 }
                              ?></td>
                              <td><?php
                                 if ($a->linktype == 1) {
                                    echo "Astrologer";
                                 }
                                 elseif ($a->linktype == 2) {
                                    echo "Eshop Product";
                                 }
                                 elseif ($a->linktype == 3) {
                                    echo "Blogs";
                                 }
                                 elseif ($a->linktype == 4) {
                                    echo "Astro Video";
                                 }
                                 else{
                                    echo "Tutorial Video";
                                 }
                              ?></td>

                              <td><?= $a->position ?></td>
                              <td>
                                 @if($a->status == 1)
                                 <span class="btn btn-primary btn-sm">Active</span>
                                 @else
                                 <span class="btn btn-danger btn-sm">Inactive</span>
                                 @endif
                              </td>
                              <td>
                                 {{-- <a href="{{ route($editurl,$a->id) }}" class="btn btn-warning waves-effect waves-light btn-sm"><span class="mdi mdi-pencil d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a> --}}
                                 <!-- <button id="modal-danger" data-userid="{{$a['id']}}" type="button" class="btn btn-danger modal-danger waves-effect waves-light btn-sm" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                    <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button>                              -->
                                 <a href="#" href="#" data-userid="{{ $a->id }}" class='btn btn-danger waves-effect waves-light btn-sm' data-toggle="modal" data-target="#modal-danger" >
                              <span class="mdi mdi-trash-can d-block font-size-12"><span class="path1"></span><span class="path2"></span></span></a>
                               </td>
                           </tr>
                           <?php $i++; ?>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{$title}}</h5>
                  <form method="POST" action="{{ $addurl1 }}" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <div class="row">

                     <div class="col-md-12">
                        <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input  required class="form-control" type="file" id="image" placeholder="image" name="image" accept="image/*">
                        </div>
                    </div>  

                   

                    <div class="col-md-12">
                     <div class="mb-3">
                         <label for="example-text-input" class="form-label">Main Type</label>

                         <input type="radio" id="top" name="maintype" value="1" checked />
                         <label for="top">Top</label>

                         <input type="radio" id="dewey" name="maintype" value="2" />
                         <label for="dewey">Middle</label>

                         <input type="radio" id="dewey" name="maintype" value="3" />
                         <label for="dewey">Class</label>


                         <input type="radio" id="dewey" name="maintype" value="4" />
                         <label for="dewey">TopMiddle</label>

                     
                         </select>
                     </div>
                 </div>


                 <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">For</label>

                      <input type="radio" id="top" name="type" value="1" checked />
                      <label for="top">Website</label>

                      <input type="radio" id="dewey" name="type" value="2" />
                      <label for="dewey">App</label>


                  
                      </select>
                  </div>
              </div>



                 {{-- <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">For</label>
                      <select name="type" required class="form-control">
                      <option value="1">Website</option>
                      <option value="2">App</option>
                      </select>
                  </div>
              </div> --}}

                 <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Link Type</label>
                      <select name="linktype" required class="form-control">
                      <option value="1">Astrologer</option>
                      <option value="2">Eshop Product</option>
                      <option value="3">Blogs</option>
                      <option value="4">Astro Video</option>
                      <option value="5">Tutorial Video</option>
                      <option value="5">Tutorial Video</option>
                      <option value="6">Ultra Premium</option>
                      </select>
                  </div>
              </div>


              <div class="1 box">
               <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Astrologer</label>
                      <select name="link_id"  class="form-control">

                        <?php $data_astro = DB::table('astrologers')->where('status',1)->where('ultra_premium',0)->orderBy('position','asc')->get();?>
                     
                          @foreach ($data_astro as $a)

                          <?php
                             $name = json_decode($a->screen_name,true);
                              $a_name = $name[1] ?? 'Astro N';
                          ?>
                          <option {{old('link_id') == $a->id ? 'selected' : ''}} value="{{ $a->id }}">{{$a_name  ?? ''}}</option>
                          @endforeach
                        </select>


                     
                  </div>
              </div>
              </div>
              <div class="2 box">
               <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Eshop Product</label>
                      <select name="link_id"  class="form-control">

                        <?php $data_shop = DB::table('astro_shop_products')->where('status',1)->orderBy('position','asc')->get();?>
                     
                        @foreach ($data_shop as $s)

                        <?php
                           $name1 = json_decode($s->name ,true);
                            $s_name = $name1[1] ?? 'Astro N';
                        ?>
                        <option {{old('link_id') == $s->id ? 'selected' : ''}} value="{{ $s->id }}">{{$s_name  ?? ''}}</option>
                        @endforeach


                  
                      </select>
                  </div>
              </div>
              </div>
              <div class="3 box">
               <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Blogs</label>
                      <select name="link_id"  class="form-control">

                        <?php $data_blogs = DB::table('blogs')->where('status',1)->orderBy('position','asc')->get();?>
                     
                        @foreach ($data_blogs as $b)

                        <?php
                           $name2 = json_decode($b->title ,true);
                            $b_name = $name2[1] ?? 'Astro N';
                        ?>
                        <option {{old('link_id') == $b->id ? 'selected' : ''}} value="{{ $b->id }}">{{$b_name  ?? ''}}</option>
                        @endforeach

                      </select>
                  </div>
              </div>
              </div>

              <div class="4 box">
               
               <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Astro Video</label>
                      <select name="link_id"  class="form-control">

                        <?php $data_videos = DB::table('videos')->where('status',1)->where('type',"astrovideos")->orderBy('position','asc')->get();?>
                     
                        @foreach ($data_videos as $v)

                        <?php
                           $name3 = json_decode($v->name ,true);
                            $v_name = $name3[1] ?? 'Astro N';
                        ?>
                        <option {{old('link_id') == $v->id ? 'selected' : ''}} value="{{ $v->id }}">{{$v_name  ?? ''}}</option>
                        @endforeach


                      </select>
                  </div>
              </div>
              </div>

              <div class="5 box">
               
               <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Tutorial Video</label>
                      <select name="link_id"  class="form-control">

                        <?php $data_tvideos = DB::table('videos')->where('status',1)->where('type',"tutorialvideos")->orderBy('position','asc')->get();?>
                     
                        @foreach ($data_tvideos as $tv)

                        <?php
                           $name4= json_decode($tv->name ,true);
                            $tv_name = $name4[1] ?? 'Astro N';
                        ?>
                        <option {{old('link_id') == $tv->id ? 'selected' : ''}} value="{{ $tv->id }}">{{$tv_name  ?? ''}}</option>
                        @endforeach

                      </select>
                  </div>
              </div>
              </div>

              <div class="6 box">
               
               <div class="col-md-12">
                  <div class="mb-3">
                      <label for="example-text-input" class="form-label">Ultra Premium AStrologer</label>
                      <select name="link_id"  class="form-control">
                        <?php $data_astro1 = DB::table('astrologers')->where('status',1)->where('ultra_premium',1)->orderBy('position','asc')->get();?>
                     
                          @foreach ($data_astro1 as $ab)

                          <?php
                             $name5 = json_decode($ab->screen_name,true);
                              $a_name5 = $name5[1] ?? 'Astro N';
                          ?>
                          <option {{old('link_id') == $ab->id ? 'selected' : ''}} value="{{ $ab->id }}">{{$a_name5  ?? ''}}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
              </div>





                      
                       <div class="col-md-12">
                               <div class="mb-3">
                                   <label for="example-text-input" class="form-label">Position</label>
                                   <input required name="position" min="1" class="form-control" type="number" id="1-text-input" placeholder="eg. 1">
                               </div>
                           </div>


                           <div class="col-md-12">
                              <div class="mb-3">
                                  <label for="example-text-input" class="form-label">Status</label>
            
                                  <input type="radio" id="top" name="status" value="1" checked />
                                  <label for="top">Active</label>
            
                                  <input type="radio" id="dewey" name="status" value="0" />
                                  <label for="dewey">Inactive</label>
            
            
                              
                                  </select>
                              </div>
                          </div>


                       {{-- <div class="col-md-12">
                           <div class="mb-3">
                               <label for="example-text-input" class="form-label">Status</label>
                               <select name="status" required class="form-control">
                               <option value="1">Active</option>
                               <option value="0">Inactive</option>
                               </select>
                           </div>
                       </div> --}}
                   </div>                                    
               </div>
               <div class="modal-footer">
               <button type="submit" class="btn btn-primary waves-effect waves-light" alt="alert"  id="sa-success">Add</button>
               </div>
            </div>
            <!-- /.modal-content -->
            </form>
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
   </div>
   <!-- End Page-content -->
</div>
<div class="modal  fade" id="modal-danger" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content bg-danger">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this?</p>
                    </div>
                    <div class="modal-footer">
                        <form id ="deleteform" method="POST" action="{{$destroyurl}}" enctype="multipart/form-data">
                            {{-- @method('DELETE') --}}
                            @csrf
                            <input type="hidden" id="user_id" name="id" value="">
                            <a class="btn btn-danger float-right" onclick="$(this).closest('form').submit();">Delete</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>

<script>
  $('#modal-danger').on('show.bs.modal', function(event) {
       var button = $(event.relatedTarget)
       var user_id = button.data('userid')

       var modal = $(this)
       modal.find('.modal-footer #user_id').val(user_id)
   })
  
</script>


<script>
$(document).ready(function(){
    $("select").change(function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if(optionValue){
                $(".box").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else{
                $(".box").hide();
            }
        });
    }).change();
});
</script>


@endsection
@endsection