<div class="input-group custom-attachment-item doc-title-top" style= "{!!isset($i) &&  $i>($custom_lookup_count+1)?'display:none;':''!!}">
    <span class="checkbox-margin">
         {{ Form::checkbox('check-dummy', (isset($custom_key)?$custom_key:null),(isset($custom_key) && is_array(($mandatory_attachment_ids)) && in_array($custom_key, $mandatory_attachment_ids))?$custom_key:null,['class'=>'input-checkbox-size1','onclick'=>" $(this).parent().find('input:hidden').val($(this).is(':checked') ?this.value:0)"]) }}
         <input type="hidden" name="mandatory_attachements_new[]" value="{{(isset($custom_key) && is_array(($mandatory_attachment_ids)) && in_array($custom_key, $mandatory_attachment_ids))?$custom_key:null }}">
    </span>

    <input type="text" class="form-control" aria-label="Text input with checkbox" name="attachment_custom_label[]" value="{{(!isset($i)&&(isset($val)))?$val:""}}">
    <span class="input-group-btn">
        <button class="btn checkbox-right-margin" type="button" onclick="if($('.user-defined-attachements').find('.custom-attachment-item:visible').length > 1) { $(this).parents('.custom-attachment-item').hide().find('input').val('').prop('disabled',true).prop('checked',false);  }">
        <a href="javascript:void(0);" class="remove_button" title="Remove" >
            <i class="fa fa-minus" aria-hidden="true"></i></a>
     </button>
    </span>
</div>
