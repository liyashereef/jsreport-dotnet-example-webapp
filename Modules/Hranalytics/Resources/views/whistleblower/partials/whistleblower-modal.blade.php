<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Performance Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::open(array('url'=>'#','id'=>'client-employee-rating-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ Form::hidden('id', null) }}
        {{ Form::hidden('employee_id', null, array('id'=>'employee_id')) }}
        {{ Form::hidden('customer_id', null, array('id'=>'customer_id')) }}
         {{ Form::hidden('feedback_id', null, array('id'=>'feedback_id')) }}
        <div class="modal-body">
            <div class="form-group" id="employee_rating_lookup_id">
            <label for="employee_rating_lookup_id" class="col-sm-3 control-label">Rating</label>
            <div class="col-sm-11">
                {!!Form::select('employee_rating_lookup_id',[null=>'Please Select'] + $rating_lookups,null, ['class' => 'form-control'])!!}
                <small class="help-block"></small>
            </div>
            </div>
            <div class="form-group" id="customer_feedback">
            <label for="customer_feedback" class="col-sm-3 control-label">Customer Feedback</label>
            <div class="col-sm-11">
                {{ Form::textarea('customer_feedback',null,array('class'=>'form-control')) }}
                <small class="help-block"></small>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
        </div>
        {{ Form::close() }}
        </div>
    </div>
</div>