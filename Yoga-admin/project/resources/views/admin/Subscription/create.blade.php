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
                     <form method="POST" action="{{ $addurl }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="package-name" class="form-label">Package Name</label>
                                 <select class="form-select" id="package-name" name="name" required>
                                    <option value="">Select Package</option>
                                    <option value="Free Trial" {{ old('name') == 'Free Trial' ? 'selected' : '' }}>Free Trial</option>
                                    <option value="Monthly" {{ old('name') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="Yearly" {{ old('name') == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="package-price" class="form-label">Price</label>
                                 <input value="{{ old('price') }}" name="price" min="0" class="form-control" type="number" id="package-price" placeholder="eg. 0" required>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="discount-price" class="form-label">Discount Price</label>
                                 <input value="{{ old('discount_price') }}" name="discount_price" min="0" class="form-control" type="number" id="discount-price" placeholder="eg. 0">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="validity-days" class="form-label">Validity Days</label>
                                 <input value="{{ old('validity_days') }}" name="validity_days" min="1" class="form-control" type="number" id="validity-days" placeholder="eg. 7" required>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="package-type" class="form-label">Package Type</label>
                                 <select name="type" class="form-select" id="package-type" required>
                                 <option value="free" {{ old('type') == 'free' ? 'selected' : '' }}>Free</option>
                                 <option value="paid" {{ old('type') == 'paid' ? 'selected' : '' }}>Paid</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="tax-id" class="form-label">Tax ID</label>
                                 @php
                                 $taxes = \App\Models\Mastertaxes::where('status',1)->orderby('name','DESC')->get(); 
                                 @endphp
                                 <select name="taxId" class="form-select">
                                    <option disabled value="">{{ __('Select Tax') }}</option>
                                    @if(isset($taxes) && count($taxes) > 0)
                                    @foreach ($taxes as $p)
                                    <option value="{{ $p->_id }}" 
                                    {{-- {{ (isset($a->taxId) && $a->taxId == $p->_id) ? 'selected' : '' }} --}}
                                    >
                                    {{ htmlspecialchars($p->taxvalue, ENT_QUOTES, 'UTF-8') }}
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="mb-3">
                                 <label for="package-status" class="form-label">Status</label>
                                 <select name="status" class="form-select" id="package-status" required>
                                 <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                 <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                 </select>
                              </div>
                           </div>
                           <!-- What You Got Features Section -->
                           <div class="col-12">
                              <div class="mb-3">
                                 <label class="form-label">Features (What You Get)</label>
                                 @php
                                       $posesList = \App\Models\Practiceyogaposes::where('status', 1)
                                          ->orderBy('name', 'ASC')
                                          ->get();
                                 @endphp
                                 <div id="features-container">
                                       <div class="feature-item row mb-2">
                                          <div class="col-md-5">
                                             <select name="what_you_got[0][posesId]" class="form-select" required>
                                                   <option value="">Select Pose</option>
                                                   @foreach($posesList as $pose)
                                                      <option value="{{ $pose->_id }}">{{ htmlspecialchars($pose->name, ENT_QUOTES, 'UTF-8') }}</option>
                                                   @endforeach
                                             </select>
                                          </div>
                                          <div class="col-md-5">
                                             <select name="what_you_got[0][is_free]" class="form-select" required>
                                                   <option value="true">True</option>
                                                   <option value="false">False</option>
                                             </select>
                                          </div>
                                          <div class="col-md-2">
                                             <button type="button" class="btn btn-danger remove-feature">Remove</button>
                                          </div>
                                       </div>
                                 </div>
                                 <button type="button" id="add-feature" class="btn btn-secondary mt-2">Add Feature</button>
                              </div>
                           </div>

                           <div class="box-footer">
                              <button type="submit" class="btn btn-rounded btn-primary" id="sa-success">Submit</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
         <!-- /.content -->
      </div>
   </div>
</div>
@section('scripts')
<script src="{{ asset('adminassets') }}/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
<script src="{{ asset('adminassets') }}/js/pages/form-editor.init.js"></script>
<script>          
   ClassicEditor.create(document.querySelector("#ckeditor-classic1"))
   
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('features-container');
    const addButton = document.getElementById('add-feature');
    
    // Add feature row
    addButton.addEventListener('click', function() {
        const featureCount = container.querySelectorAll('.feature-item').length;
        const newFeature = document.createElement('div');
        newFeature.className = 'feature-item row mb-2';
        
        // Get the poses options from the first select (if exists)
        const posesOptions = container.querySelector('.feature-item select[name^="what_you_got"]') ?
            container.querySelector('.feature-item select[name^="what_you_got"]').innerHTML :
            `<option value="">Select Pose</option>
             @foreach($posesList as $pose)
                <option value="{{ $pose->_id }}">{{ htmlspecialchars($pose->name, ENT_QUOTES, 'UTF-8') }}</option>
             @endforeach`;
        
        newFeature.innerHTML = `
            <div class="col-md-5">
                <select name="what_you_got[${featureCount}][posesId]" class="form-select" required>
                    ${posesOptions}
                </select>
            </div>
            <div class="col-md-5">
                <select name="what_you_got[${featureCount}][is_free]" class="form-select" required>
                    <option value="true">True</option>
                    <option value="false">False</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-feature">Remove</button>
            </div>
        `;
        
        container.appendChild(newFeature);
        
        // Re-index all feature items after adding
        reindexFeatureItems();
    });
    
    // Remove feature row
    container.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-feature')) {
            const featureItem = e.target.closest('.feature-item');
            if (container.querySelectorAll('.feature-item').length > 1) {
                featureItem.remove();
                reindexFeatureItems();
            } else {
                alert('At least one feature is required.');
            }
        }
    });
    
    // Function to re-index all feature items
    function reindexFeatureItems() {
        const featureItems = container.querySelectorAll('.feature-item');
        featureItems.forEach((item, index) => {
            // Update select names with new index
            const poseSelect = item.querySelector('select[name^="what_you_got"]');
            const isFreeSelect = item.querySelector('select[name^="what_you_got"][name$="[is_free]"]');
            
            poseSelect.name = `what_you_got[${index}][posesId]`;
            isFreeSelect.name = `what_you_got[${index}][is_free]`;
        });
    }
});
</script>
@endsection
@endsection