<div class="form-group row" id="smart_phone">
    <label class="col-sm-5 col-form-label">Please indicate if you have a Smartphone? </label>
    <div class="col-sm-7">
        {{ Form::select('smart_phone',[null=>'Please Select','No'=>'No','Yes'=>'Yes'],old('smart_phone',isset($candidateJob)?(isset($candidateJob->candidate->smart_phone_type_id)? 'Yes' :'No'):null),array('class' => 'form-control landing-form','required'=>true,'onchange'=>'if($(this).val()=="Yes"){ $("#tech_summary").show(); $("#smart_phone_type_id select,#smart_phone_skill_level select").prop("required",true); } else { $("#tech_summary").hide(); $("#smart_phone_type_id select,#smart_phone_skill_level select").prop("required",false); $("#smart_phone_type_id select,#smart_phone_skill_level select").val(null); }')) }}
        <div class="form-control-feedback">
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div id="tech_summary" style="display:{{ isset($candidateJob->candidate->smart_phone_type_id)?'block':'none' }};">
    <div class="form-group row" id="smart_phone_type_id">
        <label class="col-sm-5 col-form-label">If you have a smart phone what kind of phone is it? </label>
        <div class="col-sm-7">
            {{ Form::select('smart_phone_type_id',[null=>'Please
            Select']+$lookups['smart_phones'],old('smart_phone_type_id',isset($candidateJob->candidate->smart_phone_type_id)
            ? $candidateJob->candidate->smart_phone_type_id :""),array('class' => 'form-control
            landing-form select2','required'=>isset($candidateJob->candidate->smart_phone_type_id))) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
    <div class="form-group row" id="smart_phone_skill_level">
        <label class="col-sm-5 col-form-label">How would you rate your proficiency with using apps on your mobile
            phone? </label>
        <div class="col-sm-7">
            {{ Form::select('smart_phone_skill_level',[null=>'Please Select',"No Knowledge"=>"No Knowledge","Basic Knowledge"=>"Basic Knowledge","Good Knowledge"=>"Good Knowledge","Advanced Knowledge"=>"Advanced
            Knowledge","Expert Knowledge"=>"Expert
            Knowledge"],old('smart_phone_skill_level',isset($candidateJob->candidate->smart_phone_skill_level) ?
            $candidateJob->candidate->smart_phone_skill_level :""),array('class' => 'form-control
            landing-form','required'=>isset($candidateJob->candidate->smart_phone_type_id))) }}
            <div class="form-control-feedback">
                <span class="help-block text-danger align-middle font-12"></span>
            </div>
        </div>
    </div>
</div>
