<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">What are your wage expectations ?</label>
<div class="form-group row {{ $errors->has('wage_expectations') ? 'has-error' : '' }}" id="wage_expectations">
    <label for="wage_expectations" class="col-sm-5 col-form-label">Wage ($/Hour)</label>
    <label class="col-sm-1 col-form-label">$</label>
    <div class="col-sm-6">
        {{Form::text('wage_expectations',old('wage_expectations',isset($candidate->wageexpectation->wage_expectations) ? number_format($candidate->wageexpectation->wage_expectations, 2,'.','') :""),array('class'=>'form-control','placeholder'=>"Please state your minimum wage expectations",/*'step'=>"0.01",'min'=>"14.0",*/'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('wage_expectations_from') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
{{-- <div class="form-group row {{ $errors->has('wage_expectations_to') ? 'has-error' : '' }}" id="wage_expectations_to">
    <label for="wage_expectations_to" class="col-sm-5 col-form-label">To ($/Hour)</label>
    <label class="col-sm-1 col-form-label">$</label>
    <div class="col-sm-6">
        {{Form::text('wage_expectations_to',old('wage_expectations_to',isset($candidateJob->candidate->wageexpectation->wage_expectations_to) ? number_format($candidateJob->candidate->wageexpectation->wage_expectations_to, 2,'.','') :""),array('class'=>'form-control','placeholder'=>"Please enter your ideal wage",/*'step'=>"0.01",'min'=>"14.1",*/'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('wage_expectations_to', '<small class="help-block">:message</small>') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div> --}}
<div class="form-group row {{ $errors->has('wage_last_hourly') ? 'has-error' : '' }}" id="wage_last_hourly">
    <label for="wage_last_hourly" class="col-sm-5 col-form-label">What was your last hourly wage within the security guarding industry?</label>
    <label class="col-sm-1 col-form-label">$</label>
    <div class="col-sm-6">
        {{Form::text('wage_last_hourly',old('wage_last_hourly',isset($candidate->wageexpectation->wage_last_hourly) ? number_format($candidate->wageexpectation->wage_last_hourly, 2,'.','') :""),array('class'=>'form-control','placeholder'=>"Please enter your most recent hourly wage",/*'step'=>"0.01",'min'=>"14.0",*/'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('wage_last_hourly') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('wage_last_hours_per_week') ? 'has-error' : '' }}" id="wage_last_hours_per_week">
    <label for="wage_last_hours_per_week" class="col-sm-5 col-form-label" >
        How many hours per week were you working at this wage?
    </label>
    <?php $hoursArr = range(0,100); unset($hoursArr[0]) ?>
    <div class="col-sm-5">
        {{ Form::select('wage_last_hours_per_week',
                            [null=>'Please select from the dropdown list']+$hoursArr,
                            old('wage_last_hours_per_week',
                                isset($candidate->wageexpectation->wage_last_hours_per_week) ?
                                explode("." ,$candidate->wageexpectation->wage_last_hours_per_week)[0] :""
                                ),
                            array(
                                'class' => 'form-control select2',
                                'required'=>TRUE,
                            )
                        ) }}
        <div class="form-control-feedback"> {!! $errors->first('wage_last_hours_per_week') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
    <div class="col-sm-2" style="padding: 5px 0px;"> hours per week </div>
</div>
<div class="form-group row {{ $errors->has('current_paystub') ? 'has-error' : '' }}" id="current_paystub">
    <label for="current_paystub" class="col-sm-5 col-form-label" >Can you validate your current wage with a paystub as evidence if we pay a higher wage?</label>
    <div class="col-sm-7">
        {{ Form::select('current_paystub',[null=>'Please select from the dropdown list',"Yes"=>"Yes","No"=>"No"],old('current_paystub',isset($candidate->wageexpectation->current_paystub) ? $candidate->wageexpectation->current_paystub :""),array('class' => 'form-control','required'=>TRUE)) }}
        <div class="form-control-feedback"> {!! $errors->first('current_paystub') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('wage_last_provider') ? 'has-error' : '' }}" id="wage_last_provider">
    <label for="wage_last_provider" class="col-sm-5 col-form-label">Who was the security provider that paid that wage?</label>
    <div class="col-sm-7">
        {{ Form::select('wage_last_provider', $lookups['security_provider'],
            old('wage_last_provider',
                isset($candidate->wageexpectation->wage_last_provider) ? $candidate->wageexpectation->wage_last_provider :""),
                array(
                    'class' => 'form-control wage_provider select2',
                    'required'=>TRUE,
                    'onchange'=>'securityProviderOther($(this).find("option:selected").text(),$(this).find("option:selected").val())',
                    'placeholder' => 'Please select from the dropdown list. If your previous provider is not listed, please select "other"'
                )
            ) }}
        <div class="form-control-feedback">{!! $errors->first('wage_last_provider') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>


<div class="form-group row {{ !isset($candidate) || (isset($candidate->wageexpectation->wage_last_provider) && $candidate->wageexpectation->wageprovider->security_provider!='Other')?'hide-this-block':'' }}" id="wage_last_provider_other">
    <label for="wage_last_provider_other" class="col-sm-5 col-form-label">
        Please enter the name of the security provider that paid your previous wage?
    </label>
    <div class="col-sm-7">
        {{ Form::text('wage_last_provider_other',
            old('wage_last_provider_other',
            isset($candidate->wageexpectation->wage_last_provider_other) 
            ? $candidate->wageexpectation->wage_last_provider_other 
            :""
            ),array('class' => 'form-control security_provider_details',
            'onkeyup'=>"replacingSecurityDetailsHtml($(this).val())")) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<div class="" id="security_provider_details_block">
@php
if(isset($candidate->wageexpectation->wage_last_provider))
{
$security_provider_name=isset($candidate->wageexpectation->wage_last_provider) && $candidate->wageexpectation->wageprovider->security_provider!='Other' ? $candidate->wageexpectation->wageprovider->security_provider : $candidate->wageexpectation->wage_last_provider_other;
}
@endphp
<div class="form-group row {{ $errors->has('security_provider_strengths') ? 'has-error' : '' }}" id="security_provider_strengths">
    <label for="security_provider_strengths" class="col-sm-5 col-form-label">{{(isset($candidate->wageexpectation->wage_last_provider))?'What were  the strengths of'. ' '.$security_provider_name. '?':' '}}</label>
    <div class="col-sm-7">
        {{Form::textarea('security_provider_strengths',old('security_provider_strengths',isset($candidate->wageexpectation->security_provider_strengths) ? $candidate->wageexpectation->security_provider_strengths :""),array('class'=>'form-control security_provider_details','placeholder'=>"Accepted only 1000 characters", 'maxlength'=>"1000",'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('security_provider_strengths') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('security_provider_notes') ? 'has-error' : '' }}" id="security_provider_notes">
    <label for="security_provider_notes" class="col-sm-5 col-form-label">{{((isset($candidate->wageexpectation->wage_last_provider))?'What do you hope to get from Commissionaires that you feel '. ' '.$security_provider_name. ' was not providing?':' ')}}</label>
    <div class="col-sm-7">
        {{Form::textarea('security_provider_notes',old('security_provider_notes',isset($candidate->wageexpectation->security_provider_notes) ? $candidate->wageexpectation->security_provider_notes :""),array('class'=>'form-control security_provider_details','placeholder'=>"Accepted only 1000 characters", 'maxlength'=>"1000",'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('security_provider_notes') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('rate_experience') ? 'has-error' : '' }}" id="rate_experience">
    <label for="rate_experience" class="col-sm-5 col-form-label">{{(isset($candidate->wageexpectation->wage_last_provider))?'How would you rate your experience at'. ' '.$security_provider_name. '?':' '}}</label>
    <div class="col-sm-7">
          {{ Form::select('rate_experience',[null=>'Please select from the dropdown list.']+$lookups['experience_ratings'],old('last_role_held',isset($candidate->wageexpectation->rate_experience) ? $candidate->wageexpectation->rate_experience :""),array('class' => 'form-control security_provider_details','required'=>TRUE)) }}
        <div class="form-control-feedback">{!! $errors->first('rate_experience') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
</div>


<div class="form-group row {{ $errors->has('last_role_held') ? 'has-error' : '' }}" id="last_role_held">
    <label for="last_role_held" class="col-sm-5 col-form-label" >What was your previous role (at your last wage rate)?</label>
    <div class="col-sm-7">
        {{ Form::select('last_role_held',[null=>'Please select from the dropdown list. Leave blank if this does not apply to you']+$lookups['positions_lookups']+[0=>'Other'],old('last_role_held',isset($candidate->wageexpectation->last_role_held) ? $candidate->wageexpectation->last_role_held :""),array('class' => 'form-control select2','required'=>TRUE)) }}
        <div class="form-control-feedback">{!! $errors->first('last_role_held') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('explanation_wage_expectation') ? 'has-error' : '' }}" id="explanation_wage_expectation">
    <label for="explanation_wage_expectation" class="col-sm-5 col-form-label">Please justify/explain your wage expectation. Why do you think you're worth the wage you are asking for?</label>
    <div class="col-sm-7">
        {{Form::textarea('explanation_wage_expectation',old('explanation_wage_expectation',isset($candidate->wageexpectation->explanation_wage_expectation) ? $candidate->wageexpectation->explanation_wage_expectation :""),array('class'=>'form-control','placeholder'=>"Accepted only 500 characters", 'maxlength'=>"500",'rows'=>6,'required'=>TRUE))}}
        <div class="form-control-feedback">{!! $errors->first('explanation_wage_expectation') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
