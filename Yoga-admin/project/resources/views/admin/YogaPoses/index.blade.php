@extends('layouts.admin') 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- start page title -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                  <div class="page-title-right">
                     <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#myModal">
                           <i class="bx bxs-add-to-queue font-size-16 align-middle me-2"></i> {{ $addbutton }}
                        </a> 
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
                     <form method="GET" action="{{ $listurl }}" class="mb-3 row">

                      <div class="col-md-3">
                          <input type="text" name="search" class="form-control"
                                 placeholder="Search name"
                                 value="{{ request('search') }}">
                      </div>

                      <div class="col-md-3">
                          <select name="categoryId" class="form-select">
                              <option value="">All Categories</option>
                              @foreach($categories as $cat)
                                  <option value="{{ $cat->_id }}" 
                                      {{ request('categoryId') == $cat->_id ? 'selected':'' }}>
                                      {{ $cat->name }}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                      
                      



                      <div class="col-md-2">
                          <select name="status" class="form-select">
                              <option value="">All Status</option>
                              <option value="1" {{ request('status')=='1' ? 'selected':'' }}>Active</option>
                              <option value="0" {{ request('status')=='0' ? 'selected':'' }}>Inactive</option>
                          </select>
                      </div>
                      <div class="col-md-3">
                          <select name="Type_of_tracking" class="form-select">
                              <option value="">Type_of_tracking</option>
                                  <option value="HBR" 
                                      {{ request('Type_of_tracking') == 'HBR' ? 'selected':'' }}>
                                      HBR
                                  </option>
                                  <option value="HBL" 
                                      {{ request('Type_of_tracking') == 'HBL' ? 'selected':'' }}>
                                      HBL
                                  </option>
                                  <option value="FB" 
                                      {{ request('Type_of_tracking') == 'FB' ? 'selected':'' }}>
                                      FB
                                  </option>
                              
                          </select>
                      </div>
                      <div class="col-md-2">
                          <input type="date" name="from_date" class="form-control"
                                 value="{{ request('from_date') }}">
                      </div>

                      <div class="col-md-2">
                          <input type="date" name="to_date" class="form-control"
                                 value="{{ request('to_date') }}">
                      </div>

                      <div class="col-md-2 mt-2">
                          <button class="btn btn-primary w-100">Filter</button>
                      </div>
                      <div class="col-md-2 mt-2">
                          <a class="btn btn-primary w-100" href="{{route('admin.yoga_poses')}}">Reset</a>
                      </div>

                  </form>
 
                     <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100 table-sm">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>Category Name</th>
                              <th>Name</th>
                              <th>Other Details</th>
                              <th>To do Steps</th>
                              <th>Image</th>
                              <th>Status</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if (!empty($data))
                           @foreach ($data as $index => $a)
                           <tr>
                              <?php 
                              // $poses = \App\Models\Practiceyogacategories::where('_id', $a->categoryId)->first();
                              ?>
                              <td>{{ $index + 1 }}</td>
                              <td>{{ $categories[(string)$a->categoryId]->name ?? '' }}</td>

                              <td>{{ $a->name ?? "" }}</td>
                              <td>
                                  <ul class="mb-0 ps-3">
                                      <li>Type of Tracking: {{ $a->Type_of_tracking ?? 'N/A' }}</li>
                                      <li>Angle: {{ $a->Angle ?? 'N/A' }}</li>
                                      <li>Symmetric: {{ $a->symetric ?? 'N/A' }}</li>
                                  </ul>
                              </td>

                              <td>@if (!empty($a->to_do) && is_array($a->to_do))
                                    <ul class="mb-0 ps-3">
                                       @foreach ($a->to_do as $item)
                                       <li>{{ $item['points'] ?? '' }}</li>
                                       @endforeach
                                    </ul>
                                    @endif</td>

                               <td>
                                 {{-- <img src="{{asset('project/public/adminassets/gift/')}}/{{ $a->image}}" width="50px"> --}}
                                 <img src="{{ $a->image }}"  width="50px" height="50px" style="object-fit:cover;">
                              </td>
                             
                              <td>
                                <a href="javascript:void(0)"
                                   class="toggle-status badge {{ $a->status == 1 ? 'bg-success' : 'bg-danger' }}"
                                   data-id="{{ $a->_id }}"
                                   data-status="{{ $a->status }}">
                                   {{ $a->status == 1 ? 'Active' : 'Inactive' }}
                                </a>
                            </td>

                              <td>
                                 <a href="{{ route($editurl, $a->_id) }}" class="btn btn-warning waves-effect waves-light btn-sm">
                                    <i class="mdi mdi-pencil font-size-12"></i>
                                 </a>
                                 <button id="modal-danger" data-userid="{{ $a->_id }}" type="button" class="btn btn-danger btn-sm waves-effect waves-light sddel" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                 <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                 </button>
                                 
                              </td>
                           </tr>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                     <div class="d-flex justify-content-center mt-3">
                         {{ $data->links() }}
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Add Modal -->
      <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-scroll="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel">Add {{ $title }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
               @csrf
               <div class="modal-body">

                  <div class="mb-3">
                        <label for="categoryId" class="form-label">Categories</label>
                        <select class="form-select" name="categoryId" id="categoryId" required>
                           <option value="">Select Categories</option>
                           @php
                              $categories = \App\Models\Practiceyogacategories::where('status',1)->orderby('name','DESC')->get(); 
                           @endphp
                           @if(isset($categories) && count($categories) > 0)
                           @foreach ($categories as $p)
                           <option value="{{ $p->_id }}">{{ htmlspecialchars($p->name, ENT_QUOTES, 'UTF-8') }}</option>
                           @endforeach
                           @endif
                        </select>
                  </div>
                  <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Type_of_tracking <span class="text-danger">*</span></label>
                      <div class="col-sm-10">

                     <select name="Type_of_tracking" required class="form-select">
                         <option value="">Type_of_tracking</option>
                             <option value="HBR" 
                                 >
                                 HBR
                             </option>
                             <option value="HBL" 
                                 >
                                 HBL
                             </option>
                             <option value="FB" 
                                 >
                                 FB
                             </option>
                         
                     </select>
                      </div>
                 </div>
                  {{-- Name --}}
                  <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input required class="form-control" type="text" id="name" name="name" placeholder="Enter name">
                  </div>
                  <div class="mb-3">
                        <label for="Angle" class="form-label">Angle</label>
                        <input required class="form-control" type="text" id="Angle" name="Angle" placeholder="Enter Angle">
                  </div>
                  <div class="mb-3">
                        <label for="symetric" class="form-label">symetric</label>
                        <input required class="form-control" type="text" id="symetric" name="symetric" placeholder="Enter symetric">
                  </div>
                  <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Position <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                         <input required  class="form-control" type="number" min="1" name="position" placeholder="Enter position">
                      </div>
                   </div>
                  <!-- <div class="mb-3">
                        <label for="to_do_steps" class="form-label">To do Steps</label>
                        <input required class="form-control" type="text" id="to_do_steps" name="to_do_steps" placeholder="Enter steps">
                  </div>

                   -->
                   <div class="col-12">
                        <div class="mb-3">
                           <label class="form-label">To Do Points</label>
                           <div id="todo-container">
                              <div class="todo-item row mb-2">
                                    <div class="col-md-10">
                                       <input type="text" name="to_do[0][points]" class="form-control" placeholder="Enter to do point" required>
                                    </div>
                                    <div class="col-md-2">
                                       <button type="button" class="btn btn-danger remove-todo">Remove</button>
                                    </div>
                              </div>
                           </div>
                           <button type="button" id="add-todo" class="btn btn-secondary mt-2">Add Point</button>
                        </div>
                  </div>


                  {{-- Image --}}
                  <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input required class="form-control" type="file" id="image" name="image" accept="image/*">
                  </div>

                  {{-- Status --}}
                  <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                           <option value="1">Active</option>
                           <option value="0">Inactive</option>
                        </select>
                     </div>

               </div>
               <div class="modal-footer">
                  <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
               </div>
            </form>

            </div>
         </div>
      </div>

      <!-- Delete Modal -->
     <div class="modal modal-danger fade bs-example-modal-center" id="modal-danger">
   <div class="modal-dialog">
      <div class="modal-content bg-danger">
         <div class="modal-header">
            <h4 class="modal-title">Delete</h4>
            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <p>Are you sure you want to delete this?</p>
         </div>
         <div class="modal-footer">
            <form method="POST" action="{{route('admin.destroy_yoga_poses')}}" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="user_id" name="_id" value="">
               <a class="btn btn-info float-right" onclick="$(this).closest('form').submit();">Delete</a>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>

   </div>
</div>

@section('scripts')
<script>
   $('.sddel').click('show.bs.modal', function (event) {
   
       var user_id = $(this).attr('data-userid')
   
       $('#user_id').val(user_id);
   })
   
$(document).on('click', '.toggle-status', function () {
    let id = $(this).data('id');
    let status = $(this).data('status');
    let badge = $(this);

    $.ajax({
        url: "{{ route('admin.toggle_routine_status') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            status: status
        },
        success: function (res) {
            if (res.status) {
                // Update badge UI instantly
                if (res.new_status == 1) {
                    badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                } else {
                    badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                }

                // Update the badge's data-status attribute
                badge.data('status', res.new_status);
            }
        }
    });
});
</script>

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('todo-container');
    const addButton = document.getElementById('add-todo');
    
    // Add todo row
    addButton.addEventListener('click', function() {
        const todoCount = container.querySelectorAll('.todo-item').length;
        const newTodo = document.createElement('div');
        newTodo.className = 'todo-item row mb-2';
        
        newTodo.innerHTML = `
            <div class="col-md-10">
                <input type="text" name="to_do[${todoCount}][points]" class="form-control" placeholder="Enter to do point" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-todo">Remove</button>
            </div>
        `;
        
        container.appendChild(newTodo);
    });
    
    // Remove todo row
    container.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-todo')) {
            const todoItem = e.target.closest('.todo-item');
            if (container.querySelectorAll('.todo-item').length > 1) {
                todoItem.remove();
                // Re-index the remaining items
                const items = container.querySelectorAll('.todo-item');
                items.forEach((item, index) => {
                    const input = item.querySelector('input[name^="to_do"]');
                    input.name = `to_do[${index}][points]`;
                });
            } else {
                alert('At least one to do point is required.');
            }
        }
    });
});
</script>
@endsection
@endsection
