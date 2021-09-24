 <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Unionization</h5>
    </label>
    <div class="form-group row  {{ $errors->has('site_unionized') ? 'has-error' : '' }}" id="site_unionized">
        <label for="site_unionized" class="col-sm-5 col-form-label">Is the site unionized?</label>
        <div class="col-sm-6">
             {{ Form::select('site_unionized',[0=>'No',1=>'Yes'],
                old('site_unionized', isset($rfpDetails->site_unionized) ? $rfpDetails->site_unionized :"0"),array('class'=>'form-control', 'required' => true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('site_unionized', ':message') !!}</div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('union_name') ? 'has-error' : '' }}" style="display:{{old('site_unionized', isset($rfpDetails) ? $rfpDetails->site_unionized : 'site_unionized') == "1" ? '' : 'none' }}" id="union_name">
        <label for="v" class="col-sm-5 col-form-label">If yes,which union?<span class="mandatory">*</span></label>
        <div class="col-sm-6">
            {{ Form::text('union_name',old('union_name',isset($rfpDetails->union_name) ? $rfpDetails->union_name :""),array('placeholder'=>'Union','class'=> 'form-control')) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('union_name', ':message') !!}</div>
        </div>
    </div>
     <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Notes</h5>
    </label>

     <div class="form-group row  {{ $errors->has('summary_notes') ? 'has-error' : '' }}" id="summary_notes">
        <label for="summary_notes" class="col-sm-5 col-form-label">Summary</label>
        <div class="col-sm-6">
            {!! Form::textarea('summary_notes', old('summary_notes',isset($rfpDetails->summary_notes) ? $rfpDetails->summary_notes :""), ['placeholder'=>'Summary','class'=> 'form-control','id' => 'keterangan', 'rows' => 4, 'cols' => 54, 'style' => 'resize:none']) !!}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('summary_notes', ':message') !!}</div>
        </div>
    </div>
