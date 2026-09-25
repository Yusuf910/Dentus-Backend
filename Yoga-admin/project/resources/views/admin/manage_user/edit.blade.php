@extends('layouts.admin')
@section('content')
<div class="main-content">
<div class="page-content">
   <div class="container-fluid">
      <!-- start page title -->
      <div class="row">
         <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
               <h4 class="mb-sm-0 font-size-18">Edit User</h4>
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
      <!-- end page title -->
      <div class="row">
         <div class="col-12">
            <div class="card">
               {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->id], 'files' => true]) !!}
               <div class="card-body p-3">
                  <div class="row">
                        <div class="col-md-12">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Name</label>
                              <input class="form-control" name="username" value="{{ $a->name ?? '' }}" type="text">
                           </div>
                        </div>

                        <div class="col-md-12">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Email</label>
                              <input class="form-control" name="email" value="{{ $a->email }}" 
                                 pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-zA-Z]{2,4}" 
                                 type="email">
                           </div>
                        </div>

                        <div class="col-md-12">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Mobile</label>
                              <input type="number" name="mobile" tag="number" class="form-control" 
                                 value="{{ $a->mobile }}" placeholder="Mobile Number" 
                                 min="1" maxlength="15" 
                                 onkeypress="return onlyNumberKey(event)" 
                                 pattern="[789][0-9]{9}"
                                 oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" 
                                 required>
                           </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-12">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Gender</label>
                              <select class="form-select" name="gender">
                                 <option value="">Select Gender...</option>
                                 <option value="male" <?= ($a->gender == 'male') ? 'selected' : '' ?>>Male</option>
                                 <option value="female" <?= ($a->gender == 'female') ? 'selected' : '' ?>>Female</option>
                              </select>
                           </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                           <div class="mb-3">
                              <label for="example-text-input" class="form-label">Status</label>
                              <select class="role form-select" name="status">
                                 <option value="">Select Status...</option>
                                 <option value="1" <?= ($a->status == 1) ? 'selected' : '' ?>>Active</option>
                                 <option value="0" <?= ($a->status == 0) ? 'selected' : '' ?>>InActive</option>
                              </select>
                           </div>
                        </div>

                        <div class="col-md-12">
                           <button type="submit" class="btn btn-primary w-md">Submit</button>
                        </div>
                     </div>

                  </form>
               </div>
            </div>
            <!-- end col -->
         </div>
         <!-- end row -->
      </div>
      <!-- container-fluid -->
   </div>
   <!-- End Page-content -->
   <footer class="footer">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-6">
               <script>document.write(new Date().getFullYear())</script> © Yoga.
            </div>
            <!-- <div class="col-sm-6">
               <div class="text-sm-end d-none d-sm-block">
               
               </div>
               </div> -->
         </div>
      </div>
   </footer>
</div>
@section('scripts')
<script>
   function onlyNumberKey(evt) {
         
       // Only ASCII character in that range allowed
       var ASCIICode = (evt.which) ? evt.which : evt.keyCode
       if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
           return false;
       return true;
   }
</script> 
@endsection
@endsection