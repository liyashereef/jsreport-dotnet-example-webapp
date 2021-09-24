<div id="more-content" style="display: none">
    <div class="section-task-container el_fields container-fluid" id="--name--_row_--position_num--" data-elid="--position_num--">
        <div class="row section-header">
            {{ Form::hidden('client-section-id[]','') }}
            <div class="col-sm-1 section-data">{{ Form::number('sort[]','',array('class'=>'form-control', 'placeholder' =>'Sort', 'required' => 'required')) }}</div>
            <div class="col-sm-11 section-data">{{ Form::text('section[]','',array('class'=>'form-control','placeholder' => 'Section', 'required' => 'required'))}}</div>
        </div>
        <div class="row">
            <table class="table table-bordered">
                <thead>
                <th style="width: 5%">No</th>
                <th style="width: 55%">Process Step</th>
                <th>Target Date</th>
                <th style="width: 25%">Assigned To</th>
                <th></th>
                </thead>
                <tbody id="dynamic_step_row_--position_num--">
                </tbody>
            </table>
        </div>
        <div class="row add-remove-btn">
            <button type="button" title="Add More" href="javascript:;" class="col-sm-1 btn add_button" data-elid="--position_num--">
                <i class="fa fa-plus" aria-hidden="true"></i> Add Section
            </button>
            <button type="button" href="javascript:void(0);" class="col-sm-1 btn remove_button" title="Remove" data-elid="--position_num--" style="margin-left: 5px">
                <i class="fa fa-minus" aria-hidden="true"></i> Remove Section
            </button>
        </div>
    </div>
</div>
