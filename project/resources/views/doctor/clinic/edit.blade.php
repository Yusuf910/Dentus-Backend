@extends('layouts.admin')
@section('content')
<div class="page-wrapper">
   <div class="content">
      <div class="page-header">
         <div class="row">
            <div class="col-sm-12">
               <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{$listurl}}">{{$title}} </a></li>
                  <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                  <li class="breadcrumb-item active">Add {{$title}}</li>
               </ul>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-body pt-4">
                  @include('includes.admin.form-success') 
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
                   {!! Form::model($a, ['method' => 'PATCH','enctype' => 'multipart/form-data','route' => [$editurl, $a->id]]) !!}
                     {{ csrf_field() }}
                     <div class="row">
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Name <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="clinic_name" value="{{$a->clinic_name}}">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Description <span class="login-danger">*</span></label>
                              <textarea class="form-control" name="clinic_description" required><?=$a->clinic_description?></textarea>
                           </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Address <span class="login-danger">*</span></label>
                              <input name="address" value="{{$a->address}}" id="firstName1" type="text" class="form-control" placeholder="address" />
                           </div>
                        </div>
                        <div class="col-md-6">
                                       <div class="mb-3 loc-map" id="map" style="height: 300px;">
                                          
                                       </div>
                                    </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>State <span class="login-danger">*</span></label>
                              <select onchange="getCity()" name="state" id="state_id" required class="form-control">
                              <option value="">Select</option>
                                 @foreach ($State as $tt)
                                   <option {{$a->state == $tt->state_title ? 'selected' : ''}} value="{{$tt->id}}">{{$tt->state_title}}</option>
                                 @endforeach
                               </select>
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>City <span class="login-danger">*</span></label>
                              <select id="city_id" name="city" required class="form-control select2" style="width: 100%;">
                                 <option value="">Select</option>
                                 @foreach ($city as $ctt)
                                   <option {{$a->city == $ctt->name ? 'selected' : ''}} value="{{$ctt->id}}">{{$ctt->name}}</option>
                                 @endforeach
                              </select>
                           </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Pincode <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="pincode" value="{{$a->pincode}}">
                           </div>
                        </div>
                        <input type="hidden" name="latitude" id="latitude" value="{{$a->latitude}}" placeholder="latitude" class="form-control"/>
                        <input type="hidden" name="longitude" id="longitude" value="{{$a->longitude}}" placeholder="longitude" class="form-control"/>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Register Number <span class="login-danger">*</span></label>
                              <input type="text" required class="form-control" name="register_number" value="{{$a->register_number}}">
                           </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Image<span class="login-danger">*</span></label>
                              <input type="file" class="form-control" name="image"  accept="image/*">
                           </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-6">
                           <div class="input-block local-forms">
                              <label>Status <span class="login-danger">*</span></label>
                              <select name="status" required class="form-control">
                                <option <?= $a->status == 1 ? 'selected':'' ?> value="1">Active</option>
                                 <option <?= $a->status == 0 ? 'selected':'' ?> value="0">Inactive</option>
                             </select>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="doctor-submit text-start">
                              <button type="submit" class="btn btn-primary submit-form me-2">Save</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@section('js_user_page')
<script>
   function getCity() {
         var state_id = $('#state_id').val();
         if (state_id != "") 
         {
            
             var finallink = '{{ route("admin.master.load_city2",["state_id"]) }}';
             finallink = finallink.replace('state_id', state_id);
             if (finallink != "") {
                 $('#city_id').load(finallink);
                 $('#city_id').prop('disabled', false);
                 $('#city_id').select2();
             }    
         }
       }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWX-QNm_gVzt6U2K6xeU4cmF5dkX8XUQ0&callback=initialize&libraries=places,geometry"></script>
<script>
   let map, marker;
   let latitude = parseFloat("{{$a->latitude ?? 49.1777}}");
   let longitude = parseFloat("{{$a->longitude ?? -122.6600}}");
   function initialize() {
       var markers = [];
       
       const myLatLng = { lat: latitude, lng: longitude };
       map = new google.maps.Map(document.getElementById("map"), {
           center: myLatLng,
           zoom: 13,
           mapTypeId: "roadmap",
       });

       // Set marker on the map
       marker = new google.maps.Marker({
           position: myLatLng,
           map: map,
           draggable: true
       });

       markers.push(marker);
       // var defaultBounds = new google.maps.LatLngBounds(
       //     new google.maps.LatLng(51.601476, -0.140634),
       //     new google.maps.LatLng(51.501476, -0.240634));
       // map.fitBounds(defaultBounds);
       var input = document.getElementById('firstName1');
       // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
       var searchBox = new google.maps.places.SearchBox(input);
       google.maps.event.addListener(searchBox, 'places_changed', function() {
           var places = searchBox.getPlaces();
           if (places.length == 0) {
               return;
           }
           for (var i = 0, marker; marker = markers[i]; i++) {
               marker.setMap(null);
           }
           markers = [];
           var bounds = new google.maps.LatLngBounds();
           for (var i = 0, place; place = places[i]; i++) {
               var image = {
                   url: place.icon,
                   size: new google.maps.Size(71, 71),
                   origin: new google.maps.Point(0, 0),
                   anchor: new google.maps.Point(17, 34),
                   scaledSize: new google.maps.Size(25, 25)
               };
               var marker = new google.maps.Marker({
                   draggable: true,
                   map: map,
                   icon: image,
                   title: place.name,
                   position: place.geometry.location
               });
               // document.getElementById('cityname').value = place.address_components[0].long_name;
               // document.getElementById('countryname').value = place.address_components[3].long_name;
               document.getElementById('latitude').value = place.geometry.location.lat();
               document.getElementById('longitude').value = place.geometry.location.lng();
               // fetchWeather1('',place.address_components[0].long_name,place.address_components[3].long_name);
               // drag response
               google.maps.event.addListener(marker, 'dragend', function(e) {
                   displayPosition(this.getPosition());
               });
               // click response
               google.maps.event.addListener(marker, 'click', function(e) {
                   displayPosition(this.getPosition());
               });
               markers.push(marker);
               bounds.extend(place.geometry.location);
           }
           map.fitBounds(bounds);
       });
       google.maps.event.addListener(map, 'bounds_changed', function() {
           var bounds = map.getBounds();
           searchBox.setBounds(bounds);
       });

       function displayPosition(pos) {
           document.getElementById('latitude').value = pos.lat();
           document.getElementById('longitude').value = pos.lng();
       }
   }

   

     
</script>
@endsection
@endsection
