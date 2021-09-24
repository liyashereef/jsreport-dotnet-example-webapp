
    <div class="form-group row  {{ $errors->has('rfp_published_date') ? 'has-error' : '' }}" id="rfp_published_date">
        <label for="rfp_published_date" class="col-sm-5 col-form-label">RFP Published Date</label>
        <div class="col-sm-6">
             {{ Form::text('rfp_published_date',old('rfp_published_date',isset($rfpDetails->rfp_published_date) ? $rfpDetails->rfp_published_date :""),
                        array('placeholder'=>'RFP Published Date','class'=>'form-control datepicker', 'required' => true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_published_date', ':message') !!}</div>
        </div>
    </div>
    <div class="form-group row  {{ $errors->has('site_visit_available') ? 'has-error' : '' }}" id="site_visit_available">
        <label for="site_visit_available" class="col-sm-5 col-form-label">Is there a mandatory or optional site visit?</label>
        <div class="col-sm-6">
            {{ Form::select('site_visit_available',
                [null=>'Please Select','1'=>'Yes','0'=>'No'],
                old('site_visit_available',isset($rfpDetails->site_visit_available) ? $rfpDetails->site_visit_available :""),
                array(
                    'id'=> 'site_visit_available_control',
                    'class'=> 'form-control',
                    'required'=>true,
                )
            ) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_visit_available', ':message') !!}</div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('site_visit_deadline') ? 'has-error' : '' }}" id="site_visit_deadline">
        <label for="site_visit_deadline" class="col-sm-5 col-form-label">Site Visit Deadline<span class="mandatory">*</span></label>
        <div class="col-sm-6">
            {{ Form::text('site_visit_deadline',old('site_visit_deadline',isset($rfpDetails->site_visit_deadline) ? $rfpDetails->site_visit_deadline :""),array('placeholder'=>'Site Visit Deadline','class'=> 'form-control datepicker')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_visit_deadline', ':message') !!}</div>
        </div>
    </div>
    <div class="form-group row  {{ $errors->has('q_a_deadline_available') ? 'has-error' : '' }}" id="q_a_deadline_available">
        <label for="q_a_deadline_available" class="col-sm-5 col-form-label">Is there a question/answer deadline?</label>
        <div class="col-sm-6">
            {{ Form::select(
                'q_a_deadline_available',
                [null=>'Please Select','1'=>'Yes','0'=>'No'],
                old('site_visit_available',isset($rfpDetails->q_a_deadline_available) ? $rfpDetails->q_a_deadline_available :""),
                array(
                    'id'=> 'q_a_deadline_available_control',
                    'class'=> 'form-control',
                    'required'=>true
                )
            ) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('q_a_deadline_available', ':message') !!}</div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('qa_deadline') ? 'has-error' : '' }}" id="qa_deadline">
        <label for="qa_deadline" class="col-sm-5 col-form-label">QA Deadline<span class="mandatory">*</span></label>
        <div class="col-sm-6">
             {{ Form::text('qa_deadline',old('qa_deadline',isset($rfpDetails->qa_deadline) ? $rfpDetails->qa_deadline :""),array('placeholder'=>'QA Deadline','class'=>'form-control datepicker')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('qa_deadline', ':message') !!}</div>
        </div>
    </div>
    @php
    if(isset($rfpDetails->submission_deadline)){
        $time = date('h:i A', strtotime($rfpDetails->submission_deadline));
        $date=date('Y-m-d', strtotime($rfpDetails->submission_deadline));
    }
    @endphp
     <div class="form-group row" id="submission_deadline_date">
        <label for="submission_deadline_date" class="col-sm-5 col-form-label">Submission Deadline</label>
        <div class="col-sm-2">
            {{ Form::text('submission_deadline_date',isset($rfpDetails->submission_deadline) ? $date :"",array('placeholder'=>'Submission Deadline','class'=>'form-control datepicker','required'=>true)) }}
            <div class="form-control-feedback"></div>
        </div>
         <label for="submission_deadline_time" class="col-sm-2 col-form-label">Time</label>
        <div class="col-sm-2">
            {{ Form::text('submission_deadline_time',isset($rfpDetails->submission_deadline) ? $time :"",array('placeholder'=>'Time (HH:MM AM/PM)','class'=>'form-control','id'=>'timepicker','required'=>true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>

        <div class="form-control-feedback"></div>
        </div>
