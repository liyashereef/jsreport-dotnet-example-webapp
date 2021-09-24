<div class="col-sm-6 col-md-6">    
    @if(auth()->user()->can('edit-survey') || (auth()->user()->can('submit-survey') && (!$completed)))
    <textarea name="{{$name}}" type="text" maxlength="500" class="form-control ">{{$answer or ''}}</textarea>
    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>    
    @else
        <span class="view-form-element">{{$answer or ''}}</span>
    @endif   
</div>