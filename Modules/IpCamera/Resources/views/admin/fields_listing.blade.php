<style>
    .fields_lising_tbl th {
        background-color: rgba(221, 233, 237, 0.63);
    }
</style>

<p><h4>Choose Fields for Listing in Dashboard Widgets :</h4></p>
@if(!empty($fieldsList))
<table class="table table-responsive fields_lising_tbl">
    <thead>
        <tr>
            <th class="text-left">Enable</th>
            <th class="text-left" colspan="2">Display Name</th>
            <th class="text-left hidden" >System Name</th>
            <th class="text-left hidden">Field Type</th>
            <th class="text-left hidden">Model Name</th>
            <th class="text-left hidden" style="width: 100px;">Sort By</th>
            <th class="text-left hidden">Sort Order</th>
        </tr>
    </thead>

    <tbody>
    <input type="hidden" id="module_id" name="module_id" value="{{$moduleId}}" />
    <input type="hidden" id="module_type" name="module_type" value="{{$modelName}}" />
    <input type="hidden" id="layout_detail_id" name="layout_detail_id"/>
    @php($i=0)
    @foreach($fieldsList as $ky => $field)
    <tr>
        <td>
            @if(($field['model_name'] == 'ShiftModule' && $field['type'] != 'shift-module-optional') || ($field['type'] == 'widget' || $field['type'] == 'Widget'))
                @php($i++)
                <input class="form-control-sm is_active_checkbox" data-key="{{$ky}}" data-module-type="{{$modelName}}" data-module="{{$moduleId}}" type="checkbox" id="is_active_checkbox_{{$ky}}" name="is_active" checked disabled="disabled"/>
            @else
                <input class="form-control-sm is_active_checkbox" data-key="{{$ky}}" data-module-type="{{$modelName}}" data-module="{{$moduleId}}" type="checkbox" id="is_active_checkbox_{{$ky}}" name="is_active"/>
            @endif
            <input type="hidden" id="permission_text_{{$ky}}" name="permission_text" value="{{$field['permission_text']}}"/>
        </td>
        <td colspan="2">
            <input class="form-control display_name_text" type="text" id="display_name_text_{{$ky}}" name="display_name_text" value="{{$field['field_display_name']}}"  disabled="disabled"/>
        </td>
        <td class="hidden">
            <label id="field_system_name_{{$ky}}">{{$field['field_system_name']}}</label>
        </td>
        <td class="hidden">
            <label id="type_{{$ky}}">{{ucwords(strtolower($field['type']))}}</label>
        </td>
        <td class="hidden">
            <label id="visible_{{$ky}}">{{$field['visible']}}</label>
            <label id="model_name_{{$ky}}">{{$field['model_name']}}</label>
        </td>
        <td class="hidden">
            @if($field['default_sort'] == true && $field['type'] !== 'Widget')
            <input class="form-control-sm" type="radio" id="enable_sort_{{$ky}}" data-id="{{$ky}}" name="enable_sort" checked="checked" disabled="disabled"/>
            @else
            <input class="form-control-sm" type="radio" id="enable_sort_{{$ky}}" data-id="{{$ky}}" name="enable_sort" disabled="disabled"/>
            @endif
        </td>
        <td  class="hidden" style="width: 100px;">
            <select class="form-control sort_order" id="sort_order_{{$ky}}" name="sort_order" disabled="disabled">
                <option value="0" {{($field['default_sort_order'] == '0')? 'selected': ''}}>ASC</option>
                <option value="1"  {{($field['default_sort_order'] == '1')? 'selected': ''}}>DESC</option>
            </select>
        </td>
    </tr>
    @endforeach
</tbody>
</table>
@else
<table>
<tr>
    <td>No fields found</td>
</tr>
</table>
@endif

@if(!empty($fieldsList))
    <div type="button" id="save_fields" class="add-new save_fields" style="display: none;">Save</div>
@endif

<script>
    $('.display_name_text').on('keyup', function () {
         save_button_status();
    });

    $('.is_active_checkbox').on('click', function () {
         save_button_status();
    });

    $('input[type=radio][name=enable_sort]').on('click', function () {
        save_button_status();
        $('input[type=radio][name=enable_sort]').each(function(index, val){
            var isChecked = $('#' + $(this).attr('id')).prop('checked');
            if(!isChecked) {
                $('#sort_order_' + $(this).attr('data-id')).val('');
                $('#sort_order_' + $(this).attr('data-id')).attr('disabled', true);
            }else{
                $('#sort_order_' + $(this).attr('data-id')).attr('disabled', false);
                $('#sort_order_' + $(this).attr('data-id')).val(0);
            }
        });
    });

    $('.sort_order').on('change', function () {
         save_button_status();
    });

    function save_button_status() {
        $('#save_tab_configurations').hide();
        var checked_chk_box_count = $('.is_active_checkbox:checked').length;
        if(checked_chk_box_count > 0) {
            $('#save_fields').show();
        }else {
            $('#save_fields').hide();
        }
    }

    $('.save_fields, .is_active_checkbox').on('click', function () {
        $('#save_tab_configurations').hide();
        var fields_by_module_array = [];
        var module_id = $('#module_id').val();
        var module_type = $('#module_type').val();

        if (!document.getElementById("hidden_array_section").querySelector("#module_field_array_" + module_id + '_' + module_type)) {
            $('#hidden_array_section').append('<input type="hidden" id="module_field_array_' + module_id + '_' + module_type +'" name="module_field_array[]" class="module_hidden_input"/>');
        }

        //loop thorugh all checkbox
        $(".is_active_checkbox").each(function () {
            var current_element = $(this);
            var toggle = current_element.prop("checked");
            var key = current_element.attr('data-key');

            if (toggle) {
                if(($('#type_' + key).text() != "Widget") && (module_type != "ShiftModule")) {
                    $('#display_name_text_' + key).removeAttr("disabled");
                    $('#enable_sort_' + key).removeAttr("disabled");
                }else{
                    $('#display_name_text_' + key).attr("disabled", "disabled");
                    $('#enable_sort_' + key).attr("disabled", "disabled");
                }

                module_id = current_element.attr('data-module');
                module_type = current_element.attr('data-module-type');
                var display_name = $('#display_name_text_' + key).val();
                var field_name = $('#field_system_name_' + key).text();
                var type = $('#type_' + key).text();
                var model_name = $('#model_name_' + key).text();
                var enable_sort = $('#enable_sort_' + key).prop("checked");
                var sort_order = $('#sort_order_' + key).val();
                var layout_detail_id = $('#layout_detail_id').val();
                var permission_text = $('#permission_text_' + key).val();
                var visible = Number($('#visible_' + key).text());

                fields_by_module_array.push({
                    'module_id': module_id,
                    'display_name': display_name,
                    'field_name': field_name,
                    'type': type,
                    'model_name': model_name,
                    'enable_sort': (enable_sort)? 1: 0,
                    'sort_order': sort_order,
                    'key': key,
                    'layout_detail_id': layout_detail_id,
                    'permission_text': permission_text,
                    'visible': visible
                });
            } else {
                $('#display_name_text_' + key).attr("disabled", "disabled");
                $('#enable_sort_' + key).attr("disabled", "disabled");
            }
        });

        var hidden_element_id = '#module_field_array_' + module_id + '_' + module_type;
        $(hidden_element_id).val(JSON.stringify(fields_by_module_array));
        var array_values = JSON.parse($(hidden_element_id).val());
        if (array_values.length == 0) {
            $(hidden_element_id).remove();
        }
    });

    $('#save_fields').on('click', function () {
        $('#save_fields').hide();
        var checked_chk_box_count = $('.is_active_checkbox:checked').length;
        if(checked_chk_box_count === 0) {
            swal('Oops', 'Please enable any field', 'error');
            return false;
        }

        var total_drop_box_li_count = document.querySelectorAll('.module-droppable .li-droppable').length;
        var total_drop_box_count = document.querySelectorAll('#realImageDiv .module-droppable').length;
        if (total_drop_box_count === total_drop_box_li_count) {
            var module_fields_mapping_count = document.querySelectorAll('#hidden_array_section .module_hidden_input').length;
            if (total_drop_box_count === module_fields_mapping_count) {
                $('#save_tab_configurations').show();
            }else {
                $('#save_tab_configurations').hide();
                return false;
            }
        }else {
            $('#save_tab_configurations').hide();
            return false;
        }
    });
</script>
