@foreach ($lang as $l)
<div class="col-md-{{$col}}">
   <div class="mb-3">
      <label for="{{$l->name}}" class="form-label name_en">Name(In {{$l->name}})*</label>
      <input required class="form-control" type="text" id="{{$l->name}}" placeholder="Name(In {{$l->name}})" name="name[{{$l->id}}]">
   </div>
</div>
@endforeach