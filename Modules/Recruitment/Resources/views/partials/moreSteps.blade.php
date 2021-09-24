<div id="more-step-content" style="display: none">
    <div class="form-group row new-fields el_fields" id="--name--_row_--position_num--" data-elid="--position_num--">
        <input type="hidden" name="step-id[--position_num--]">
        <input type="hidden" name="position[--position_num--]" value="--position_num--">
        <div class="col-sm-7" id="limit_--position_num--">                   
                                    Greater than
                                    <input type="number" class="form-control lower" style="display: inline;width:15%;" value="0" name="lower_limit[--position_num--]" id="lower_--position_num--" readonly />&nbsp;Less than or equal to&nbsp;<input type="number" class="form-control maxupto" style="display: inline;width:15%;" id="greater_--position_num--" oninput="updateResponseTime(this)" min="1" value="" name="upper_limit[--position_num--]" required />&nbsp;
                              <span> </span>            
            <small class="help-block"></small>
        </div>
    
        <div class="col-sm-3  text-center" id="high_--position_num--" >
            <input type="number" min="1" class="form-control clearForm" name="score[--position_num--]" id="score_--position_num--" required value="">
            <small class="help-block"></small>
        </div>
   
        <div class="col-sm-2">
            <a title="Add More" href="javascript:;" class="add_button" data-elid="--position_num--">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            &nbsp;
            <a href="javascript:void(0);" class="remove_button" title="Remove" data-elid="--position_num--">
                <i class="fa fa-minus" aria-hidden="true"></i>
            </a>
        </div>
   
        <div class="form-control-feedback"></div>
    </div>
</div>