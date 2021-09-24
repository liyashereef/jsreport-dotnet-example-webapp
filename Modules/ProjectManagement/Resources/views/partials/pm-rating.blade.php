<!-- Task Rate Modal -->
<div class="modal fade" id="task-rate-modal" data-backdrop="static" role="dialog" aria-labelledby="task-rate-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rate Task</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'task-rate-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('project_id', null) }}
            {{ Form::hidden('group_id', null) }}

            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Task Name</label>
                    <div class="col-sm-12">
                        {{ Form::text('name',null,array('class'=>'form-control','placeholder' => 'Task Name','maxlength'=>100,'readonly'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6 mb-3 form-group" id="deadline_rating_id">
                        <label class="col-sm-5 control-label">Deadline</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="deadline_rating_id" required>
                                <option value="">Select Rating</option>
                                @foreach($ratings as $each_rating_id=>$each_rating)
                                <option value="{{ $each_rating_id}}">{{ $each_rating}}
                                </option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>

                    </div>
                    <div class="col-md-6 mb-3 form-group" id="deadline_weightage">
                        <label class="col-sm-12 control-label">Deadline Weightage</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="deadline_weightage" required>
                                <option value="" selected disabled>Select Percentage</option>
                                @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                                    @endfor
                            </select>
                            <small class="help-block"></small>
                        </div>

                    </div>
                </div>


                <div class="form-row">
                    <div class="col-md-6 mb-3 form-group" id="value_add_rating_id">
                        <label class="col-sm-5 control-label">Value Add</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="value_add_rating_id" required>
                                <option value="">Select Rating</option>
                                @foreach($ratings as $each_rating_id=>$each_rating)
                                <option value="{{ $each_rating_id}}">{{ $each_rating}}
                                </option>
                                @endforeach
                            </select>
                            <small class="help-block"></small>
                        </div>

                    </div>
                    <div class="col-md-6 mb-3 form-group" id="value_add_weightage">
                        <label class="col-sm-12 control-label">Value Add Weightage</label>
                        <div class="col-sm-12">

                            <select class="form-control" name="value_add_weightage" required>
                                <option value="" selected disabled>Select Percentage</option>
                                @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                                    @endfor
                            </select>
                            <small class="help-block"></small>
                        </div>

                    </div>
                </div>

                {{--
                <div class="form-group" id="value_add_rating_id">
                    <label class="col-sm-3 control-label">Value Add</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="value_add_rating_id" required>
                            <option value="">Select Rating</option>
                            @foreach($ratings as $each_rating_id=>$each_rating)
                            <option value="{{ $each_rating_id}}">{{ $each_rating}}
                </option>
                @endforeach
                </select>
                <small class="help-block"></small>
            </div>
        </div> --}}

        <div class="form-row">
            <div class="col-md-6 mb-3 form-group" id="initiative_rating_id">
                <label class="col-sm-5 control-label">Initiative</label>
                <div class="col-sm-12">
                    <select class="form-control" name="initiative_rating_id" required>
                        <option value="">Select Rating</option>
                        @foreach($ratings as $each_rating_id=>$each_rating)
                        <option value="{{ $each_rating_id}}">{{ $each_rating}}
                        </option>
                        @endforeach
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
            <div class="col-md-6 mb-3 form-group" id="initiative_weightage">
                <label class="col-sm-12 control-label">Initiative Weightage</label>
                <div class="col-sm-12">

                    <select class="form-control" name="initiative_weightage" required>
                        <option value="" selected disabled>Select Percentage</option>
                        @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                            @endfor
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
        </div>


        <div class="form-row">
            <div class="col-md-6 mb-3 form-group" id="commitment_rating_id">
                <label class="col-sm-5 control-label">Commitment</label>
                <div class="col-sm-12">
                    <select class="form-control" name="commitment_rating_id" required>
                        <option value="">Select Rating</option>
                        @foreach($ratings as $each_rating_id=>$each_rating)
                        <option value="{{ $each_rating_id}}">{{ $each_rating}}
                        </option>
                        @endforeach
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
            <div class="col-md-6 mb-3 form-group" id="commitment_weightage">
                <label class="col-sm-12 control-label">Commitment Weightage</label>
                <div class="col-sm-12">

                    <select class="form-control" name="commitment_weightage" required>
                        <option value="" selected disabled>Select Percentage</option>
                        @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                            @endfor
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
        </div>



        <div class="form-row">
            <div class="col-md-6 mb-3 form-group" id="complexity_rating_id">
                <label class="col-sm-5 control-label">Complexity</label>
                <div class="col-sm-12">
                    <select class="form-control" name="complexity_rating_id" required>
                        <option value="">Select Rating</option>
                        @foreach($ratings as $each_rating_id=>$each_rating)
                        <option value="{{ $each_rating_id}}">{{ $each_rating}}
                        </option>
                        @endforeach
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
            <div class="col-md-6 mb-3 form-group" id="complexity_weightage">
                <label class="col-sm-12 control-label">Complexity Weightage</label>
                <div class="col-sm-12">
                    <select class="form-control" name="complexity_weightage" required>
                        <option value="" selected disabled>Select Percentage</option>
                        @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                            @endfor
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
        </div>


        <div class="form-row">
            <div class="col-md-6 mb-3 form-group" id="efficiency_rating_id">
                <label class="col-sm-5 control-label">Efficiency</label>
                <div class="col-sm-12">
                    <select class="form-control" name="efficiency_rating_id" required>
                        <option value="">Select Rating</option>
                        @foreach($ratings as $each_rating_id=>$each_rating)
                        <option value="{{ $each_rating_id}}">{{ $each_rating}}
                        </option>
                        @endforeach
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
            <div class="col-md-6 mb-3 form-group" id="efficiency_weightage">
                <label class="col-sm-12 control-label">Efficiency Weightage</label>
                <div class="col-sm-12">
                    <select class="form-control" name="efficiency_weightage" required>
                        <option value="" selected disabled>Select Percentage</option>
                        @for ($i = 0; $i <= 20 ; $i++) <option value="{{$i*5}}">{{$i*5}}%</option>
                            @endfor
                    </select>
                    <small class="help-block"></small>
                </div>

            </div>
        </div>



        <div class="form-group" id="rating_notes">
            <label class="col-sm-5 control-label">Notes</label>
            <div class="col-sm-12">
                {!! Form::textarea('rating_notes',null,['class'=>'form-control', 'rows' => 4]) !!}
                <small class="help-block"></small>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
        {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
    </div>
    {{ Form::close() }}
</div>
</div>
</div>
