<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">Please indicate the computer applications you've used in the past</label>
<?php $already_shown = 'Special Skills';?>
@foreach($lookups['skills_lookup'] as $each_skill)

@if($each_skill->category==null || $each_skill->category!=$already_shown)
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">{{ $each_skill->category }}</label>
<?php $already_shown = $each_skill->category;?>
@endif

<div class="form-group row  {{ $errors->has('skill') ? 'has-error' : '' }}" id="skill">
    <label class="col-sm-5 col-form-label">{{ $each_skill->skills }}</label>
    <div class="col-sm-7">
    	 {{ Form::select('skill[' .$each_skill->id.']',[null=>'Please Select',"No Knowledge"=>"No Knowledge","Basic Knowledge"=>"Basic Knowledge","Good Knowledge"=>"Good Knowledge","Advanced Knowledge"=>"Advanced Knowledge","Expert Knowledge"=>"Expert Knowledge"],old('skill[' .$each_skill->id.']',isset($candidateJob->candidate->skills[($each_skill->id)-1]->skill_level) ? $candidateJob->candidate->skills[($each_skill->id)-1]->skill_level :""),array('class' => 'form-control landing-form','required'=>TRUE)) }}
        <div class="form-control-feedback">
            {!! $errors->first('skill[' .$each_skill->id.']', '<small class="help-block">:message</small>') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
@endforeach
