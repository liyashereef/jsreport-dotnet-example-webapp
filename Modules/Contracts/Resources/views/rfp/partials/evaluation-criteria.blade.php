
<div class="form-group row justify-content-center criteria_label_class" data-id="{{isset($key) ? $key :$i}}">
 <div class="col-sm-3 {{ $errors->has('criteria_name') ? 'has-error' : '' }}" id="criteria_name.{{isset($key) ? $key :$i}}">
         {{ Form::text('criteria_name[]',isset($criteria->criteria_name) ? $criteria->criteria_name :"",array('placeholder'=>'Criteria Name','class'=> 'form-control')) }}
         <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12">
             </span>{!! $errors->first('criteria_name', ':message') !!}</div>
 </div>
 <div class="col-sm-3 {{ $errors->has('points') ? 'has-error' : '' }}" id="points.{{isset($key) ? $key :$i}}">
     {{ Form::text('points[]',isset($criteria->points) ? $criteria->points :"",array('placeholder'=>'Points','class'=> 'form-control')) }}
     <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12">
             </span>{!! $errors->first('points', ':message') !!}</div>
 </div>
 <div class="col-sm-3 {{ $errors->has('notes') ? 'has-error' : '' }}" id="notes.{{isset($key) ? $key :$i}}">
     {{ Form::text('notes[]',isset($criteria->notes) ? $criteria->notes :"",array('placeholder'=>'Notes','class'=> 'form-control')) }}
     <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12">
             </span>{!! $errors->first('notes', ':message') !!}</div>
 </div>
 <div class="col-sm-1">
 	@if(isset($key) && $key !=0)
 	<a href="javascript:void(0);" class="remove_button" data-id="{{isset($key) ? $key :$i}}"  title="Remove field" onclick="removeCriteriaBlock($(this))"><i class="fa fa-minus" aria-hidden="true"></i></a>
 	@endif
 </div>

 </div>
