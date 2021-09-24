{{-- <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Position Information</label>
<div class="form-group row">
    <label for="reason_dropdown_1" class="col-sm-5 col-form-label">Date of Application</label>
    <div class="col-sm-7">
        {{Form::text('date_today',(isset($candidateJob->submitted_date) ? date('d/m/Y',strtotime($candidateJob->submitted_date)) : date('d/m/Y')),array('readonly'=>true,'class'=>'form-control'))}}
        <div class="form-control-feedback"></div>
    </div>
</div>
<div class="form-group row">
    <label for="reason_dropdown_1" class="col-sm-5 col-form-label">What position code have you applied for ?<br/><small>(Please find position code on "Indeed.ca" posting header)</small></label>
    <div class="col-sm-7">
        {{Form::text('job_id',$session_obj['job']->unique_key,array('readonly'=>true,'class'=>'form-control'))}}
        <div class="form-control-feedback"></div>
    </div>
</div>
<div class="form-group row align-button">
    <label for="reason_dropdown_1" class="col-sm-5 col-form-label">Position Description</label>
    <span class="col-sm-7 form-group">
        <a class="" target="_blank" href="{{ route('job.view-description',$session_obj['job']->id) }}" title="Click here for full job description">Full Job Description</a></span>
</div>
<div class="form-group row">
    <label for="reason_dropdown_1" class="col-sm-5 col-form-label">Wage per Hour</label>
     <div class="col-sm-1">
        <label class="col-sm-5 col-form-label"> Low </label>
    </div>
    <div class="col-sm-2">
        {{Form::text('wage_per_hour','$'.number_format($session_obj['job']->wage_low, 2),array('readonly'=>true,'class'=>'form-control'))}}
        <div class="form-control-feedback"></div>
    </div>
    <div class="col-sm-1">
        <label class="col-sm-5 col-form-label"> High </label>
    </div>
    <div class="col-sm-2">
        {{Form::text('wage_per_hour','$'.number_format($session_obj['job']->wage_high, 2),array('readonly'=>true,'class'=>'form-control'))}}
        <div class="form-control-feedback"></div>
    </div>
</div> --}}
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Orientation</label>
<div class="form-group row">
    <label for="orientation" class="col-sm-5 col-form-label">
        Did you attend an orientation session hosted by our SVP/COO prior to applying
    </label>
    <div class="col-sm-7">
        {{ Form::select('orientation',[null=>'Please Select','Yes' => 'Yes', 'No' => 'No'] ,old('orientation',isset($candidate->referalAvailibility->orientation) ? $candidate->referalAvailibility->orientation :""),array('class' => 'form-control', 'required' => 'true')) }}
        <div class="form-control-feedback"></div>
    </div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Referral</label>
<div class="form-group row  {{ $errors->has('job_post_finding') ? 'has-error' : '' }}" id="job_post_finding">
    <label for="job_post_finding" class="col-sm-5 col-form-label">
        How did you find out about this job posting
    </label>
    <div class="col-sm-7">
        {{ Form::select(
                'job_post_finding',
                [null=>'Please Select'] + $lookups['job_post_finding'],
                old(
                    'job_post_finding',
                    isset($candidate->referalAvailibility->job_post_finding) ? $candidate->referalAvailibility->job_post_finding :""),
                    array(
                        'id'=>'job_post_finding_options',
                        'class' => 'form-control',
                        'required' => 'true'
                )
            ) }}
        <div class="form-control-feedback">
            {!! $errors->first('job_post_finding', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}
        </div>
    </div>
</div>
<div id="job_post_referral" class="{{ @$candidate->referalAvailibility->job_post_finding!=3 ?'hide-this-block':'' }}">
<div class="form-group row {{ $errors->has('sponser_email') ? 'has-error' : '' }}" id="sponser_email">
    <label for="sponser_email" class="col-sm-5 col-form-label">Please enter the email address of the person who referred you to Commissionaires. Please make sure to accurately enter the email address or your sponsor will not get the referral credit.</label>
    <div class="col-sm-7">
        {{Form::email('sponser_email',(isset($candidate->referalAvailibility) ?$candidate->referalAvailibility->sponser_email:'') ,array('class'=>'form-control','placeholder'=>'Enter email address of your sponsor'))}}
        <div class="form-control-feedback">
            {!! $errors->first('sponser_email', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}
        </div>
    </div>
</div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Availability</label>
@include('recruitment::job-application.partials.profile.candidate-referral')
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Fit Assessment</label>
<label for="reason_dropdown_1" class="col-sm-12 col-form-label" id="answer-candidly" style="padding-left: 47px">
    Please answer candidly. We are trying to assess our brand awareness within the marketplace so your input will be helpful in future ad campaigns

</label>
<div class="form-group row {{ $errors->has('fit_assessment_why_apply_for_this_job') ? 'has-error' : '' }}" id="fit_assessment_why_apply_for_this_job">
    <label for="reason_dropdown_1" class="col-sm-5 col-form-label">
        Prior to our online ad, had you heard about Commissionaires?
    </label>
    <div class="col-sm-7">

        {{-- {{ Form::select('brand_awareness_id',$session_obj['brand_awareness'],$session_obj['brand_awareness',array('class' => 'form-control')) }} --}}
        {{-- {{ Form::select('brand_awareness_id',[null=>'Please Select']+$session_obj['brand_awareness'],old('brand_awareness_id',isset($candidateJob->brand_awareness_id) ?$candidateJob->brand_awareness_id :""),array('class' => 'form-control','required'=>TRUE,'onchange'=>'securityProviderOther($(this).find("option:selected").text())')) }} --}}
        {{ Form::select('brand_awareness_id',[null=>'Please Select'] + $session_obj['brand_awareness'],old('brand_awareness_id',isset($candidate->awareness->brand_awareness_id) ? $candidate->awareness->brand_awareness_id :""),array('class' => 'form-control', 'required' => 'true')) }}

        <div class="form-control-feedback">{!! $errors->first('brand_awareness_id', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}</div>
    </div>
</div>
<div class="form-group row {{ $errors->has('security_awareness_id') ? 'has-error' : '' }}" id="security_awareness_id">
    <label for="reason_dropdown_2" class="col-sm-5 col-form-label">
        Prior to our online ad, how familiar are you with Garda, G4S, Securitas or Palladin ?<span class="mandatory">*</span>
    </label>
    <div class="col-sm-7">
        {{ Form::select('security_awareness_id',[null=>'Please Select'] + $session_obj['security_awareness'],old('security_awareness_id',isset($candidate->awareness->security_awareness_id) ? $candidate->awareness->security_awareness_id :""),array('class' => 'form-control', 'required' => 'true')) }}
        <div class="form-control-feedback">{!! $errors->first('security_awareness_id', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}</div>
    </div>
</div>
<div class="form-group row {{ $errors->has('hours_per_week') ? 'has-error' : '' }}" id="security_awareness_id">
    <label for="reason_dropdown_2" class="col-sm-5 col-form-label">
        How many hours per week would you prefer to work?<span class="mandatory">*</span>
    </label>
    <div class="col-sm-7">
        {{Form::number('hours_per_week',isset($candidate->awareness->prefered_hours_per_week)?$candidate->awareness->prefered_hours_per_week:'',array('class'=>'form-control','min'=>0,'max'=>168))}}
        <div class="form-control-feedback">{!! $errors->first('security_awareness_id', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}</div>
    </div>
</div>

<div class="form-group row {{ $errors->has('candidate_commissionaires_understandings_id') ? 'has-error' : '' }}" id="candidate_commissionaires_understandings_id">
    <label for="commissionaires_understanding" class="col-sm-5 col-form-label">
        Please share your understanding of Commissionaires <b><u>PRIOR</u></b> to applying <span class="mandatory">*</span>
    </label>
    <div class="col-sm-7">
        @php
        $i=0;
        $selected_understandings = [];
        if(isset($candidate->comissionaires_understanding)){
            foreach($candidate->comissionaires_understanding as $val){
                $selected_understandings[] = $val->commissionaires_understanding_lookups_id;
            }
        }
        @endphp
        <ul class="nav flex-column">
            @foreach($lookups['commissionaires_understanding'] as $val=>$understanding)
                <li class="nav-item">
                    <label class="radio-button-label">
                        <input type="radio" name="candidate_commissionaires_understandings_id" value={{$val}}  @if (isset($candidate->comissionaires_understanding)) {{ in_array($val, $selected_understandings)  ? 'checked="checked"' : '' }} @endif></input>{{++$i}}{{' - '.$understanding}}
                    </label>
                </li>
            @endforeach
        </ul>
        <div class="form-control-feedback">{!! $errors->first('candidate_commissionaires_understandings_id', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}</div>
    </div>
</div>
{{-- <div class="form-group row {{ $errors->has('fit_assessment_why_apply_for_this_job') ? 'has-error' : '' }}" id="fit_assessment_why_apply_for_this_job">
    <label for="reason_dropdown_1" class="col-sm-5 col-form-label">Please elaborate why you are applying for this specific role, and why you think you would succeed in the role of - {{$session_obj['job']->unique_key}}/{{ $session_obj['job']->positionBeeingHired->position }}/{{$session_obj['job']->city}}</label>
    <div class="col-sm-7">
        {{Form::textarea('fit_assessment_why_apply_for_this_job',old('fit_assessment_why_apply_for_this_job',isset($candidateJob->fit_assessment_why_apply_for_this_job) ? $candidateJob->fit_assessment_why_apply_for_this_job :""),array('class'=>'form-control','placeholder'=>"Please elaborate in detail why you are applying for this role. Progress to the next stage will depend on your answer. If you do not answer, you will not move to the interview phase", 'maxlength'=>"500",'required'=>TRUE,'rows'=>6))}}
        <div class="form-control-feedback">{!! $errors->first('fit_assessment_why_apply_for_this_job', '<small class="help-block">:message</small>') !!}
            {!! $error_block !!}</div>
    </div>
</div> --}}
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Candidate Information</label>
@include('recruitment::job-application.partials.profile.candidate-basic-info')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 landing-form"><h4>Licensing Information and Security Guarding Experience</h4></label>
@include('recruitment::job-application.partials.profile.licensing-and-security-guarding-experience')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Please list all the positions you've held and years of experience</label>
@include('recruitment::job-application.partials.profile.positions-held-in-past')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Compensation</label>
@include('recruitment::job-application.partials.profile.wage-expectations')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Availability</label>
@include('recruitment::job-application.partials.profile.availability')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Security Clearance</label>
@include('recruitment::job-application.partials.profile.security-clearance')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Proximity to Client Site</label>
@include('recruitment::job-application.partials.profile.proximity')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Employment History (Start with most recent position first)</label>
@if ( isset($candidate) && Auth::user()!=null)
@php $count=count($candidate->employment_history) @endphp
@foreach($candidate->employment_history as $key=>$history)
@include('recruitment::job-application.partials.profile.employement-history', array('history' => $history,'key'=>$key,'maximum'=>$count))
@endforeach
@else
@include('recruitment::job-application.partials.profile.employement-history')
@endif

<div class="form-group row">

    <div class="col-sm-5"></div>
    <div class="col-sm-7">
        <a href="javascript:void(0);" class="add-position"><i class="fa fa-plus" aria-hidden="true"></i>Add Position</a>

    </div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">References <i style="color: #041c2d;">(By completing this form, I authorize Commissionaires Great Lakes to contact any former/current employers or provided references regarding my work history, performance, and any other claims I have made on my resume or during the interview)*</i></label>
@if ( isset($candidate) && Auth::user()!=null)
@php  $count=count($candidate->references) @endphp
@foreach($candidate->references as $key=>$references)
@include('recruitment::job-application.partials.profile.references', array('references' => $references,'key'=>$key,'maximum'=>$count))
@endforeach
@else
@include('recruitment::job-application.partials.profile.references')
@endif

<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-7">
        <a href="javascript:void(0);" class="add-reference"> <i class="fa fa-plus" aria-hidden="true"></i> Add Reference</a>
    </div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Education</label>
@if ( isset($candidate) && Auth::user()!=null)
 @php  $count=count($candidate->educations) @endphp
@foreach($candidate->educations as $key=>$educations)
@include('recruitment::job-application.partials.profile.education', array('educations' => $educations,'key'=> $key,'maximum'=>$count))
@endforeach
@else
@include('recruitment::job-application.partials.profile.education')
@endif

<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-7">
        <a href="javascript:void(0);" class="add-education"><i class="fa fa-plus" aria-hidden="true"></i> Add Education</a>
    </div>
</div>
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Languages</label>
@include('recruitment::job-application.partials.profile.languages')
<div class="form-group row addlang">
    <div class="col-sm-5"></div>
    <div class="col-sm-7">
         <a href="javascript:void(0);" class="add-languages">
             <i class="fa fa-plus" aria-hidden="true"></i> Add More Languages
        </a>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Special Skills </label>
@include('recruitment::job-application.partials.profile.skills')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Technical Summary </label>
@include('recruitment::job-application.partials.profile.technical_summary')

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Commissionaires Experience</label>
@include('recruitment::job-application.partials.profile.experiences')
@include('recruitment::job-application.partials.profile.indigenous_status')
@include('recruitment::job-application.partials.profile.misc')
<script>
    width = 100/Number($('.breadcrumb-arrow li').length)+'%';
        $('.breadcrumb-arrow li').css("width",width);
    </script>
