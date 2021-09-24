<div class="modal fade" id="unlink-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Unlink Sensor</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'sensor-unlink-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('room_id', null) }}

            <div class="modal-body">
                <div class="form-group row" id="unlink_sensor_id">
                    <label for="unlink_sensor_id" class="col-sm-3 control-label" style="text-align: left;">Sensors<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('unlink_sensor_id[]',[],null,array('class'=>'form-control select2 customer_select','multiple'=>'multiple', 'style'=>"width: 100%;")) }}
                     <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal','onclick'=>"cancel()"))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

