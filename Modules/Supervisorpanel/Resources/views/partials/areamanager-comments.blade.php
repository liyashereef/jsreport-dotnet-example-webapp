<div class="col-sm-4 col-md-4">
    @if($can_write)
        <textarea name="{{$name}}" type="text" maxlength="256" class="form-control areamanager-notes">{{$answer or ''}}</textarea>
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    @else
        <span class="view-form-element">{{$answer or ''}}</span>
    @endif
</div>
<div class="col-sm-2 col-md-2">    
    <span class="view-note-date">{{$datetime or ''}}</span>
</div>