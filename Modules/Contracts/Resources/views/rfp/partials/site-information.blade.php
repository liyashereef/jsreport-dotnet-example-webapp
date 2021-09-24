 <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Site Information</h5>
    </label>
    <div class="form-group row  {{ $errors->has('rfp_site_name') ? 'has-error' : '' }}" id="rfp_site_name">
        <label for="rfp_site_name" class="col-sm-5 col-form-label">RFP Name</label>
        <div class="col-sm-6">
             {{ Form::text('rfp_site_name',
                        old('rfp_site_name',isset($rfpDetails->rfp_site_name) ? $rfpDetails->rfp_site_name :""),
                        array('placeholder'=>'Site Name','class'=>'form-control', 'required' => true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_site_name', ':message') !!}</div>
        </div>
    </div>

     <div class="form-group row  {{ $errors->has('rfp_site_address') ? 'has-error' : '' }}" id="rfp_site_address">
        <label for="rfp_site_address" class="col-sm-5 col-form-label">Site Address</label>
        <div class="col-sm-6">
            {{ Form::text('rfp_site_address',old('rfp_site_address',isset($rfpDetails->rfp_site_address) ? $rfpDetails->rfp_site_address :""),
                    array('placeholder'=>'Site Address','class'=> 'form-control', 'required' => true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_site_address', ':message') !!}</div>
        </div>
    </div>
     <div class="form-group row  {{ $errors->has('rfp_site_city') ? 'has-error' : '' }}" id="rfp_site_city">
        <label for="rfp_site_city" class="col-sm-5 col-form-label">Site City</label>
        <div class="col-sm-6">
             {{ Form::text('rfp_site_city',old('rfp_site_city',isset($rfpDetails->rfp_site_city) ? $rfpDetails->rfp_site_city :""),
                    array('placeholder'=>'Site City','class'=>'form-control', 'required' => true)) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_site_city', ':message') !!}</div>
        </div>
    </div>
    <div class="form-group row  {{ $errors->has('rfp_site_postalcode') ? 'has-error' : '' }}" id="rfp_site_postalcode">
        <label for="rfp_site_postalcode" class="col-sm-5 col-form-label">Site Postal Code</label>
        <div class="col-sm-6">
             {{ Form::text('rfp_site_postalcode',old('rfp_site_postalcode',isset($rfpDetails->rfp_site_postalcode) ? $rfpDetails->rfp_site_postalcode :""),
                    array(
                        'placeholder'=>'Site Postal Code',
                        'class'=>'form-control postal-code',
                        'pattern'=>"(.){6,6}",'maxlength'=>6,
                        'onkeyup'=>"postalCode($(this))",
                        'required' => true
                    )
            ) }}
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('rfp_site_postalcode', ':message') !!}</div>
        </div>
    </div>
