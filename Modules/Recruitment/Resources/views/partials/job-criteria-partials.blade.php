
<div id="more-content" style="display: none">
    <div class="form-group row new-fields el_fields" id="--name--_row_--position_num--" data-elid="--position_num--">
        <input type="hidden" name="step-id[--position_num--]" >
        <input type="hidden" name="pos[--position_num--]">
        <input type="hidden" name="position[--position_num--]" value="--position_num--">
    {{--   <div class="col-sm-1" id="position_--position_num--"  align="center">
            --position_num--

            <small class="help-block"></small>
        </div> --}}
        <div class="col-sm-4" align="center" id="criteria_name_--position_num--">
            {{ Form::select('criteria_name[--position_num--]',['0'=>'Please select']+$score_lookups,null,array('class'=>'form-control','onChange'=>'populateType($(this));')) }}
             {{-- <input  name="criteria_name[--position_num--]"  placeholder="Criteria" required  class="form-control" > --}}
            <small class="help-block"></small>
        </div>
    
        <div class="col-sm-2" align="center" id="weight_--position_num--" >
             <input type="number" max="100" min="0" step="1"  name="weight[--position_num--]" class="form-control weightage"> 
            <small class="help-block"></small>
        </div>
  
        <div class="col-sm-3" align="center" id="type_id_--position_num--">
           {{ Form::select('type_id[--position_num--]',['0'=>'Please select type']+$match_types,null,array('class'=>'form-control type','readonly'=>'readonly','style'=>' pointer-events: none;')) }}
            <small class="help-block"></small>
        </div>
    
        <div class="col-sm-2" align="center">
            &nbsp;
            <a href="javascript:void(0);" class="step_remove_button"  title="Remove" data-elid="--position_num--">
                <i class="fa fa-minus" aria-hidden="true"></i>
            </a>
            &nbsp;
            <a title="Add More" href="javascript:;" class="step_add_button"  data-elid="--position_num--">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            
             &nbsp;
              <a href="#" id="mapping_--position_num--" class="mapping fa fa-link-o" style="display:none;"  data-elid="--position_num--"> <i class="fa fa-link" aria-hidden="true"></i></a>
        </div>
    </td>
        <div class="form-control-feedback"></div>
    </div>
</div>
