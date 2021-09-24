<div class="col-sm-5 row">
   @if(auth()->user()->can('edit-survey') || (auth()->user()->can('submit-survey') && (!$completed)))
    <div class="radio-inline col-sm-6 col-md-4">        
        <input type="radio" name="{{$name}}" 
               value="Yes" 
               class="@if(($show_if_yes == true))  show-yes @else show-yes-false @endif" 
               required
               @if(isset($answer) && strtolower($answer) == 'yes') checked @endif>
        <label class="padding-5"><b>Yes</b></label>
    </div>
    <div class="radio-inline col-sm-6 col-md-4">
        <input type="radio" name="{{$name}}" 
               value="No" class="@if(($show_if_yes != true))  show-no @else show-no-false @endif" 
               required
               @if(isset($answer) && strtolower($answer) == 'no') checked @endif>
        <label class="padding-5"><b>No</b></label>
    </div>
<!--    <input type='hidden' name="meta_{{$name}}" value=""/>-->
    <div class="form-control-feedback">
        <span class="help-block text-danger align-middle font-12"></span>
    </div>
     @else
    <div class="radio-inline col-sm-6 col-md-4">        
    @if(isset($answer) && strtolower($answer) == '')
    <span class="view-form-element">{{$answer or ''}}</span>
    @else
    <span class="@if(isset($answer) && strtolower($answer) == 'yes') view-form-element @if($show_if_yes == true)  show-yes-text @endif @endif">@if(isset($answer) && strtolower($answer) == 'yes') Yes @endif</span>
    <span class="@if(isset($answer) && strtolower($answer) == 'no') view-form-element @if($show_if_yes != true) show-no-text @endif @endif">@if(isset($answer) && strtolower($answer) == 'no') No @endif</span>
    @endif
    </div>    
    @endif
</div>   