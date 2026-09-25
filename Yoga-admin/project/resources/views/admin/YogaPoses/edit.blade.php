@extends('layouts.admin') 
@section('styles')
<link href="{{asset('project/public/adminassets')}}/css/jquery.Jcrop.css" rel="stylesheet" />
<link href="{{asset('project/public/adminassets')}}/css/Jcrop-style.css" rel="stylesheet" />
@endsection 
@section('content')
<div class="main-content">
   <div class="page-content">
      <div class="container-fluid">
         <!-- Page Header -->
         <div class="row">
            <div class="col-12">
               <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>
                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item "><a href="{{ $listurl }}">{{ $listname }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                     </ol>
                  </div>
               </div>
            </div>
         </div>
         <!-- Main Content -->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body p-3">
                     {{-- Validation Errors --}}
                     @if ($errors->any())
                     <div class="alert alert-danger alert-dismissible">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <ul>
                           @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                     @endif
                     {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->_id]]) !!}
                     <div class="row">
                        <div class="col-12">
                           @php
                           $categories = \App\Models\Practiceyogacategories::where('status',1)->orderby('name','DESC')->get(); 
                           @endphp
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Category Name <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <select name="categoryId" class="form-select" id="categoryId" required>
                                    <option disabled value="">{{ __('Select Category') }}</option>
                                    @if(isset($categories) && count($categories) > 0)
                                    @foreach ($categories as $p)
                                    <option value="{{ $p->_id }}" 
                                    {{ (isset($a->categoryId) && $a->categoryId == $p->_id) ? 'selected' : '' }}>
                                    {{ htmlspecialchars($p->name ?? "" , ENT_QUOTES, 'UTF-8') }}
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Type_of_tracking <span class="text-danger">*</span></label>
                              <div class="col-sm-10">

                             <select name="Type_of_tracking" required class="form-select">
                                 <option value="">Type_of_tracking</option>
                                     <option value="HBR" 
                                         {{ 'HBR' == $a->Type_of_tracking ? 'selected':'' }}>
                                         HBR
                                     </option>
                                     <option value="HBL" 
                                         {{ 'HBL' == $a->Type_of_tracking ? 'selected':'' }}>
                                         HBL
                                     </option>
                                     <option value="FB" 
                                         {{ 'FB' == $a->Type_of_tracking ? 'selected':'' }}>
                                         FB
                                     </option>
                                 
                             </select>
                              </div>
                         </div>
                           {{-- Name --}}
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $a->name }}" class="form-control" type="text" name="name" placeholder="Enter name">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Angle <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $a->Angle }}" class="form-control" type="text" name="Angle" placeholder="Enter Angle">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">symetric <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $a->symetric }}" class="form-control" type="text" name="symetric" placeholder="Enter symetric">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Position <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $a->position }}" class="form-control" type="text" name="position" placeholder="Enter position">
                              </div>
                           </div>
                           {{-- To do Steps --}}
                           <!-- <div class="form-group row">
                              <label class="col-sm-2 col-form-label">To do Steps <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input required value="{{ $a->to_do_steps }}" class="form-control" type="text" name="to_do_steps" placeholder="Enter steps">
                              </div> -->
                           {{-- Image --}}
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Image <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <input name="image" class="form-control" type="file" accept="image/*">
                                 @if(!empty($a->image))
                                 <img src="{{ $a->image }}" width="80px" class="mt-2" style="object-fit:cover;">
                                 <a href="{{ $a->image }}" download class="ms-2">
                                 <i class="fa fa-download" aria-hidden="true"></i>
                                 </a>
                                 @endif
                              </div>
                           </div>
                           <div class="col-12">
                                 <div class="mb-3">
                                    <label class="form-label">To Do Points</label>
                                    <div id="todo-container">
                                       @if(!empty($a->to_do))
                                       @foreach($a->to_do as $index => $todo)
                                             <div class="todo-item row mb-2">
                                                <div class="col-md-10">
                                                   <input type="text" name="to_do[{{ $index }}][points]" class="form-control" value="{{ $todo['points'] }}" placeholder="Enter to do point" required>
                                                   <input type="hidden" name="to_do[{{ $index }}][_id]" value="{{ $todo['_id'] ?? (string) new \MongoDB\BSON\ObjectId() }}">
                                                </div>
                                                <div class="col-md-2">
                                                   <button type="button" class="btn btn-danger remove-todo">Remove</button>
                                                </div>
                                             </div>
                                       @endforeach
                                       @endif
                                    </div>
                                    <button type="button" id="add-todo" class="btn btn-secondary mt-2">Add Point</button>
                                 </div>
                           </div>
                           {{-- Status --}}
                           <div class="form-group row">
                              <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                              <div class="col-sm-10">
                                 <select name="status" required class="form-select">
                                 <option value="1" {{ $a->status == 1 ? 'selected' : '' }}>Active</option>
                                 <option value="0" {{ $a->status == 0 ? 'selected' : '' }}>Inactive</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        {{-- Submit Button --}}
                        <div class="box-footer mt-3">
                           <button type="submit" class="btn btn-rounded btn-primary">Submit</button>
                        </div>
                     </div>
                     {!! Form::close() !!}
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
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
                <input type="hidden" name="to_do[${todoCount}][_id]" value="${(new Date()).getTime()}">
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
                    const pointsInput = item.querySelector('input[name^="to_do"][name$="[points]"]');
                    const idInput = item.querySelector('input[name^="to_do"][name$="[_id]"]');
                    
                    pointsInput.name = `to_do[${index}][points]`;
                    if(idInput) {
                        idInput.name = `to_do[${index}][_id]`;
                    }
                });
            } else {
                alert('At least one to do point is required.');
            }
        }
    });
});
</script>
@endsection