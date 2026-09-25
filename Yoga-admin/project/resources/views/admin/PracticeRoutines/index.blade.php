@extends('layouts.admin')

@section('content')
<div class="main-content">
<div class="page-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Routine List</a></li>
                        
                    </ol>
                </div>

            </div>
        </div>
    </div>  
    <form method="GET" action="{{ $listurl }}" class="row mb-3">

        <div class="col-md-3">
            <label class="form-label">Name</label>

            <input type="text" name="search" class="form-control"
                   placeholder="Search Routine Name"
                   value="{{ request('search') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">All Users</label>

            <select name="userId" class="form-select">
                <option value="">All Users</option>
                @foreach($users as $id => $u)
                    <option value="{{ $id }}" {{ request('userId') == $id ? 'selected' : '' }}>
                        {{ $u->name ?? 'Unknown' }}({{$u->email}},{{$u->mobile}})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Filter By Pose</label>
            <select name="poseId" class="form-select">
                <option value="">Filter By Pose</option>
                @foreach($allPoses as $id => $p)
                    <option value="{{ $id }}" {{ request('poseId') == $id ? 'selected' : '' }}>
                        {{ $p->name }}({{$p->Type_of_tracking}})
                    </option>
                @endforeach
            </select>
        </div>

       

        <div class="col-md-3">
            <label class="form-label">Date From</label>
            <input type="date" name="from_date" class="form-control"
                   value="{{ request('from_date') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Date To</label>

            <input type="date" name="to_date" class="form-control"
                   value="{{ request('to_date') }}">
        </div>

        <div class="col-md-2 mt-3">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2 mt-3">
            <a href="{{route('admin.practice_routines')}}" class="btn btn-primary w-100">Reset</a>
        </div>
    </form>

    <!-- TABLE -->
    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Routine Name</th>
                        <th>User Name</th>
                        <th>Poses List</th>
                        <th>Status</th>
                        <th>Added On</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($data as $i => $a)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $a->name }}</td>

                        <td>{{ $users[(string)$a->userId]->name ?? 'Unknown' }}</td>

                        <td>
                            <ul>
                                @foreach ($a->poseslist as $p)
                                    <li>
                                        {{ $allPoses[(string)$p['posesId']]->name ?? 'N/A' }}
                                        (Level:
                                            {{ $allLevels[(string)$p['levelId']]->levelname ?? 'N/A' }},
                                        Duration: {{ $p['duration'] ?? 0 }} min)
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        <td>
                            @if ($a->status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Archived</span>
                            @endif
                        </td>
                        <td>
                                 {{ \Carbon\Carbon::parse($a['createdAt'])->format('d M Y h:i a') }}
                              </td>
                        <td>
                            <a href="{{ route('admin.practice_routine_details', $a->_id) }}"
                               class="btn btn-info btn-sm">View</a>
                            <button id="modal-danger" data-userid="{{ $a->_id }}" type="button" 
                                                   class="btn btn-danger btn-sm waves-effect waves-light sddel" 
                                                   data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                                <i class="mdi mdi-trash-can d-block font-size-12"></i>
                                             </button>   
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $data->links() }}

        </div>
    </div>

</div>
</div>
</div>
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
            <form method="POST" action="{{route('admin.destroy_practice_routines')}}" enctype="multipart/form-data">
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
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>
   $('.sddel').click('show.bs.modal', function (event) {
   
       var user_id = $(this).attr('data-userid')
   
       $('#user_id').val(user_id);
   })
</script>
<script>          
   ClassicEditor.create(document.querySelector("#ckeditor-classic1"))
</script>
@endsection
@endsection
