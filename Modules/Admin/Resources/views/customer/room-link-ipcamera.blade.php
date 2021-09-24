<div class="modal fade" id="link-ipcamera-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Permission Mapping</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'ipcamera-link-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}

            <div class="modal-body">
                <div class="form-group row" id="customer_name">
                    <label for="customer_name" class="col-sm-3 control-label" style="text-align: left;">Customer Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('customer_name',old('customer_name',null),array('class'=>'form-control','maxlength'=>'50','readonly'=>true))}}
                     <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="room_name">
                    <label for="room_name" class="col-sm-3 control-label" style="text-align: left;">Room Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('room_name',old('room_name',null),array('class'=>'form-control','maxlength'=>'50','readonly'=>true))}}
                     <small class="help-block"></small>
                        <div id="room-warnings" style="background-color: lightgoldenrodyellow; padding: 5px; display: none"> Following issues might cause the sensor triggers to be ignored by system.
                        </div>
                    </div>
                </div>
                <div class="form-group row" id="ipcamera_id">
                    <label for="ipcamera_id" class="col-sm-3 control-label" style="text-align: left;">IP Camera<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::select('ipcamera_id[]',[],null,array('class'=>'form-control select2 customer_select','multiple'=>'multiple','style'=>"width: 100%;")) }}
                     <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal','onclick'=>"cancel()"))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
