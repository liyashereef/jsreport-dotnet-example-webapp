 <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Project Summary</h5>
    </label>
    <div class="form-group row  {{ $errors->has('total_annual_hours') ? 'has-error' : '' }}" id="total_annual_hours">
        <label for="total_annual_hours" class="col-sm-5 col-form-label">Total Annual Hours</label>
        <div class="col-sm-6">
             {{ Form::text('total_annual_hours',
                    old('total_annual_hours',isset($rfpDetails->total_annual_hours) ? $rfpDetails->total_annual_hours :""),
                    array('placeholder'=>'Total Annual Hours','class'=>'form-control', 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('total_annual_hours', ':message') !!}
            </div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('scope_summary') ? 'has-error' : '' }}" id="scope_summary">
        <label for="scope_summary" class="col-sm-5 col-form-label">Scope Summary</label>
        <div class="col-sm-6">
            {{ Form::textarea('scope_summary',
                old('scope_summary',isset($rfpDetails->scope_summary) ? $rfpDetails->scope_summary :""),
                array('placeholder'=>'Scope Summary','class'=> 'form-control', 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('scope_summary', ':message') !!}
            </div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('force_required') ? 'has-error' : '' }}" id="force_required">
        <label for="force_required" class="col-sm-5 col-form-label">Use of Force</label>
        <div class="col-sm-6">
            {{ Form::select('force_required',[0=>'No',1=>'Yes'],
                old('force_required',isset($rfpDetails->force_required) ? $rfpDetails->force_required :"0"),array('class'=> 'form-control', 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('force_required', ':message') !!}
            </div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('term') ? 'has-error' : '' }}" id="term">
        <label for="term" class="col-sm-5 col-form-label">Term (Years)</label>
        <div class="col-sm-6">
             {{ Form::number('term',old('term',isset($rfpDetails->term) ? $rfpDetails->term :""),
                    array('placeholder'=>'Term','class'=>'form-control','min'=>0, 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('term', ':message') !!}
            </div>
        </div>
    </div>
    <div class="form-group row  {{ $errors->has('option_renewal') ? 'has-error' : '' }}" id="option_renewal">
        <label for="option_renewal" class="col-sm-5 col-form-label">Option Renewal (Years)</label>
        <div class="col-sm-6">
             {{ Form::number('option_renewal',
                    old('option_renewal',isset($rfpDetails->option_renewal) ? $rfpDetails->option_renewal :""),
                    array('placeholder'=>'Option Renewal','class'=>'form-control','min'=>0, 'required' => true)) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
                {!! $errors->first('option_renewal', ':message') !!}
            </div>
        </div>
    </div>
