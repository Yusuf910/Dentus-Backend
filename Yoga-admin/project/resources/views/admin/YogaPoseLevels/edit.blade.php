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
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                        <li class="breadcrumb-item "><a href="{{ $listurl }}">{{ $listname }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
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
                        <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                        <ul>
                           @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                     @endif
                     {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->_id]]) !!}
                        @csrf
                        <div class="row">

                           <!-- Yoga Pose First -->
                           <div class="col-md-6">
                                 <div class="mb-3">
                                    <label for="posesId" class="form-label">Yoga Pose</label>
                                    <select name="posesId" class="form-select" id="posesId" required>
                                       <option value="" disabled>Select Yoga Pose</option>
                                       @foreach($poses as $pose)
                                             <option value="{{ $pose->_id }}" {{ $a->posesId == $pose->_id ? 'selected' : '' }}>
                                                {{ $pose->name }}
                                             </option>
                                       @endforeach
                                    </select>
                                 </div>
                           </div>

                           <!-- Then Level Name -->
                           <div class="col-md-6">
                                 <div class="mb-3">
                                    <label for="levelname" class="form-label">Level Name</label>
                                    <select name="levelname" id="levelname" class="form-select" required>
                                       <option value="">Select Level</option>
                                       <option value="Beginner" {{ $a->levelname == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                       <option value="Intermediate" {{ $a->levelname == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                       <option value="Advanced" {{ $a->levelname == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                 </div>
                           </div>

                           <!-- Video URL -->
                           <div class="col-md-6">
                                 <div class="mb-3">
                                    <label for="video" class="form-label">Video URL</label>
                                    <input value="{{ $a->video }}" name="video" class="form-control" type="url" id="video" placeholder="Enter video URL" required>
                                 </div>
                           </div>

                           <!-- Duration -->
                           <div class="col-md-6">
                                 <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input value="{{ $a->duration }}" name="duration" min="1" class="form-control" type="number" id="duration" placeholder="Enter duration" required>
                                 </div>
                           </div>

                           <!-- About -->
                           <div class="col-md-12">
                                 <div class="mb-3">
                                    <label for="about" class="form-label">About</label>
                                    <textarea name="about" class="form-control" id="about" rows="3" placeholder="Enter description about the level" required>{{ $a->about }}</textarea>
                                 </div>
                           </div>

                           <!-- Status -->
                           <div class="col-md-6">
                                 <div class="mb-3">
                                    <label for="package-status" class="form-label">Status</label>
                                    <select name="status" class="form-select" id="package-status" required>
                                       <option value="1" {{ $a->status == 1 ? 'selected' : '' }}>Active</option>
                                       <option value="0" {{ $a->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                 </div>
                           </div>

                           <!-- To Do Points -->
                           <!-- <div class="col-12">
                                 <div class="mb-3">
                                    <label class="form-label">To Do Points</label>
                                    <div id="todo-container">
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
                                    </div>
                                    <button type="button" id="add-todo" class="btn btn-secondary mt-2">Add Point</button>
                                 </div>
                           </div> -->

                           <!-- Submit -->
                           <div class="box-footer">
                                 <button type="submit" class="btn btn-rounded btn-primary" id="sa-success">Update</button>
                           </div>
                        </div>
                     {!! Form::close() !!}

                  </div>
               </div>
            </div>
         </div>
         <!-- /.content -->
      </div>
   </div>
</div>
@endsection

@section('scripts')
<!-- CKEditor 5 Classic Build (CDN) -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
   ClassicEditor
      .create(document.querySelector("#about"))
      .catch(error => {
         console.error(error);
      });
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