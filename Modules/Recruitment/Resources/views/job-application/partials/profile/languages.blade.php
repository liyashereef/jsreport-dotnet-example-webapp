<input type="hidden" name="otherlanguages"  id="otherlanguages" value="{{$otherlanguages->count()}}">
<div class="language-container"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">English</label>
<div class="form-group row {{ $errors->has('speaking_english') ? 'has-error' : '' }}" id="speaking_english">
    <label class="col-sm-5 col-form-label">Speaking/Oral Comprehension</label>
    <div class="col-sm-7">
        {{ Form::select('speaking_english',[null=>'Please select the appropriate answer from the dropdown list',"C - Fluent - this is my native language."=>"C - Fluent - this is my native language.","B - Functional - this is my second language but I can get by."=>"B - Functional - this is my second language but I can get by.","A - Limited - I am just learning the language."=>"A - Limited - I am just learning the language."],old('speaking_english',isset($candidate->languages[0]->speaking) ? $candidate->languages[0]->speaking :""),array('class' => 'form-control','required'=>TRUE,'id'=>'english')) }}
        <div class="form-control-feedback">
         {!! $errors->first('speaking_english') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('reading_english') ? 'has-error' : '' }}" id="reading_english">
    <label class="col-sm-5 col-form-label">Reading</label>
    <div class="col-sm-7">
        {{ Form::select('reading_english',[null=>'Please select the appropriate answer from the dropdown list',"C - Fluent - this is my native language."=>"C - Fluent - this is my native language.","B - Functional - this is my second language but I can get by."=>"B - Functional - this is my second language but I can get by.","A - Limited - I am just learning the language."=>"A - Limited - I am just learning the language."],old('reading_english',isset($candidate->languages[0]->reading) ? $candidate->languages[0]->reading :""),array('class' => 'form-control','required'=>TRUE,'id'=>'english')) }}
        <div class="form-control-feedback">
            {!! $errors->first('reading_english') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('writing_english') ? 'has-error' : '' }}" id="writing_english">
    <label class="col-sm-5 col-form-label">Writing</label>
    <div class="col-sm-7">
        {{ Form::select('writing_english',[null=>'Please select the appropriate answer from the dropdown list',"C - Fluent - this is my native language."=>"C - Fluent - this is my native language.","B - Functional - this is my second language but I can get by."=>"B - Functional - this is my second language but I can get by.","A - Limited - I am just learning the language."=>"A - Limited - I am just learning the language."],old('writing_english',isset($candidate->languages[0]->writing) ? $candidate->languages[0]->writing :""),array('class' => 'form-control','required'=>TRUE,'id'=>'english')) }}
        <div class="form-control-feedback">
            {!! $errors->first('writing_english') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>

<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">French</label>

<div class="form-group row {{ $errors->has('speaking_french') ? 'has-error' : '' }}" id="speaking_french">
    <label class="col-sm-5 col-form-label">Speaking/Oral Comprehension</label>
    <div class="col-sm-7">
        {{ Form::select('speaking_french',[null=>'Please select the appropriate answer from the dropdown list',"D - No Knowledge."=>"D - No Knowledge.","C - Fluent - this is my native language."=>"C - Fluent - this is my native language.","B - Functional - this is my second language but I can get by."=>"B - Functional - this is my second language but I can get by.","A - Limited - I am just learning the language."=>"A - Limited - I am just learning the language."],old('speaking_french',isset($candidate->languages[1]->speaking) ? $candidate->languages[1]->speaking :""),array('class' => 'form-control','required'=>TRUE,'id'=>'french')) }}
        <div class="form-control-feedback">
            {!! $errors->first('speaking_french') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('reading_french') ? 'has-error' : '' }}" id="reading_french">
    <label class="col-sm-5 col-form-label">Reading</label>
    <div class="col-sm-7">
        {{ Form::select('reading_french',[null=>'Please select the appropriate answer from the dropdown list',"D - No Knowledge."=>"D - No Knowledge.","C - Fluent - this is my native language."=>"C - Fluent - this is my native language.","B - Functional - this is my second language but I can get by."=>"B - Functional - this is my second language but I can get by.","A - Limited - I am just learning the language."=>"A - Limited - I am just learning the language."],old('reading_french',isset($candidate->languages[1]->reading) ? $candidate->languages[1]->reading :""),array('class' => 'form-control','required'=>TRUE,'id'=>'french')) }}
        <div class="form-control-feedback">
            {!! $errors->first('reading_french') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
<div class="form-group row {{ $errors->has('writing_french') ? 'has-error' : '' }}" id="writing_french">
    <label class="col-sm-5 col-form-label">Writing</label>
    <div class="col-sm-7">
        {{ Form::select('writing_french',[null=>'Please select the appropriate answer from the dropdown list',"D - No Knowledge."=>"D - No Knowledge.","C - Fluent - this is my native language."=>"C - Fluent - this is my native language.","B - Functional - this is my second language but I can get by."=>"B - Functional - this is my second language but I can get by.","A - Limited - I am just learning the language."=>"A - Limited - I am just learning the language."],old('writing_french',isset($candidate->languages[1]->writing) ? $candidate->languages[1]->writing :""),array('class' => 'form-control','required'=>TRUE,'id'=>'french')) }}
        <div class="form-control-feedback">
            {!! $errors->first('writing_french') !!}
            <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
</div>
@if (isset($otherlanguages) && $otherlanguages->count()>0)
    @include('recruitment::job-application.partials.profile.otherlanguage')
@endif

