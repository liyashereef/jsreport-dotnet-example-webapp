
    <div class="form-group row  {{ $errors->has('announcement_date') ? 'has-error' : '' }}" id="announcement_date">
        <label for="announcement_date" class="col-sm-5 col-form-label">Announcement Date</label>
        <div class="col-sm-6">
             {{ Form::text('announcement_date',
                        old('announcement_date',isset($rfpDetails->announcement_date) ? $rfpDetails->announcement_date :""),
                        array('placeholder'=>'Announcement Date','class'=>'form-control datepicker', 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('announcement_date', ':message') !!}
            </div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('project_start_date') ? 'has-error' : '' }}" id="project_start_date">
        <label for="project_start_date" class="col-sm-5 col-form-label">Project Start Date</label>
        <div class="col-sm-6">
            {{ Form::text('project_start_date',
                    old('project_start_date',isset($rfpDetails->project_start_date) ? $rfpDetails->project_start_date :""),
                    array('placeholder'=>'Project Start Date','class'=> 'form-control datepicker', 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('project_start_date', ':message') !!}
            </div>
        </div>
    </div>
