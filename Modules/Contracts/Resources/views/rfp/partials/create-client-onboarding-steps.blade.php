<template id="more-step-content" style="display: none">
    <tr class="el_fields" id="--name--_row_--position_num--" data-elid="--position_num--">
        {{ Form::hidden('client-step-id[--section_id--][]','') }}
        <td>
            {{ Form::number('client-step-sort-order[--section_id--][]','',
                array('class'=>'form-control','placeholder'=>'Sort','required'=>'required')) }}
        </td>
        <td>
            {{ Form::text('client-step[--section_id--][]','',
                array('class'=>'form-control','placeholder'=>'Step','required'=>'required')) }}
        </td>
        <td>
            {{ Form::text('client-step-target-date[--section_id--][]','',
                array('class'=>'form-control datepicker','placeholder'=>'Target Date','required'=>'required')) }}
        </td>
        <td>
            {{ Form::select('client-step-assignee[--section_id--][]',$employeeLookup,null,
                array('class'=>'form-control assignee','placeholder'=>'Please Select','required'=>'required')) }}
        </td>
        <td class="action-data">
            <a title="Add More" href="javascript:;" class="step_add_button" data-elid="--position_num--">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            <a title="Remove Step" href="javascript:;" class="step_remove_button" data-elid="--position_num--">
                <i class="fa fa-minus" aria-hidden="true"></i>
            </a>
        </td>
    </tr>
</template>
