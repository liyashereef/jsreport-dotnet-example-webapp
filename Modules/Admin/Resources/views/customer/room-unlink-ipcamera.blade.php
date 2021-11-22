<div class="modal fade" id="unlink-ipcamera-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Unlink Sensor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                
            </div>
            {{ Form::open(array('url'=>'#','id'=>'ipcamera-unlink-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('room_id', null) }}

            <div class="modal-body">
                <div class="form-group row" id="unlink_ipcamera_id">
                    <label for="unlink_ipcamera_id" class="col-sm-3 control-label" style="text-align: left;">IP Camera<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('unlink_ipcamera_id[]',[],null,array('class'=>'form-control select2 customer_select','multiple'=>'multiple', 'style'=>"width: 100%;")) }}
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

