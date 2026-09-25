
<div class="content-wrapper">
  <div class="container-full">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="d-flex align-items-center">
        <div class="mr-auto">
          <h3 class="page-title">Create New Permission</h3>
          <div class="d-inline-block align-items-center">
            <nav>
               <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{ route('admin.home') }}"><i class="mdi mdi-home-outline"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.home') }}">Home</a></li>
                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.permissions.index') }}">Permission List</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add Permission</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12 col-12">
          <!-- Basic Forms -->
          <div class="box">
            <!-- <div class="box-header with-border">
                    <h4 class="box-title">Add Board Name</h4>
                  </div> -->
            <!-- /.box-header -->
            @if (count($errors) > 0)
              <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                   @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                   @endforeach
                </ul>
              </div>
            @endif
            <form method="POST" action="{{ route('admin.permissions.store') }}" enctype="multipart/form-data">
              {{ csrf_field() }}
            <div class="box-body">
              <div class="row">
                <div class="col-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                       {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control','id' => 'name')) !!}
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="example-text-input" class="col-sm-2 col-form-label">Slug</label>
                    <div class="col-sm-10">
                       {!! Form::text('slug', null, array('placeholder' => 'Slug','class' => 'form-control','id'=>'slug')) !!}
                    </div>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <input type="submit" class="btn btn-primary">
            </div>
          </div>
         </form>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js" integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM=" crossorigin="anonymous"></script>
    <script>

        $(document).ready(function(){
            $('#name').keyup(function(e){
                var str = $('#name').val();
                str = str.replace(/\W+(?!$)/g, '-').toLowerCase();//rplace stapces with dash
                $('#slug').val(str);
                $('#slug').attr('placeholder', str);
            });
        });
        
    </script>


