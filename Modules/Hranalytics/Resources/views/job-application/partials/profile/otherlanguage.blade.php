@foreach ($otherlanguages as $otherlanguage)
@php
    $otherid=($loop->iteration)+1;
    $i=$loop->iteration+1;
    $langid = $otherlanguage->language_id;
@endphp
    <div class="language-container languageblock sublangblock{{$otherid}}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="pos">Language {{isset($loop->iteration) ? $loop->iteration+2 :"1"}}</div></label>
        <div class="form-group row {{ $errors->has('language_.'.$i) ? 'has-error' : '' }}"
            id="language_{{$i}}">
            <label class="col-sm-5 col-form-label">Language</label>
            <div class="col-sm-7">
                <select name="language_{{$otherid}}" id="" class="form-control languageblockselect" attr-expectedid="{{$langid}}" required>
                    <option value="">Select any from list</option>
                    @foreach ($languages as $language)

                        <option value="{{$language->id}}" @if ($langid==$language->id)
                            selected
                        @endif
                            >{{$language->language}}</option>
                    @endforeach

                </select>
                <div class="form-control-feedback">
                    {!! $errors->first('language_speak_'.$i) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        </div>
        <div class="form-group row {{ $errors->has('language_speak_'.$otherid) ? 'has-error' : '' }}"
            id="language_speak.{{$i}}">
            <label class="col-sm-5 col-form-label">Speaking/Oral Comprehension</label>
            <div class="col-sm-7">
                {{ Form::select('language_speak_'.$otherid,
                [null=>'Please select the appropriate answer from the dropdown list',
                "C - Fluent - this is my native language."=>"C - Fluent - this is my native language.",
                "B - Functional - this is my second language but I can get by."=>
                "B - Functional - this is my second language but I can get by.",
                "A - Limited - I am just learning the language."=>
                "A - Limited - I am just learning the language."],old('language_speak_'.$i,
                isset($otherlanguage->speaking) ? $otherlanguage->speaking :""),
                array('class' => 'form-control','required'=>TRUE,'id'=>'language_speak_'.$otherid)) }}
                    {!! $errors->first('language_speak_'.$otherid) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        <div class="form-group row {{ $errors->has('language_read_'.$otherid) ? 'has-error' : '' }}"
            id="language_read_{{$otherid}}">
            <label class="col-sm-5 col-form-label">Reading</label>
            <div class="col-sm-7">
                {{ Form::select('language_read_'.$otherid,
                [null=>'Please select the appropriate answer from the dropdown list',
                "C - Fluent - this is my native language."=>"C - Fluent - this is my native language.",
                "B - Functional - this is my second language but I can get by."=>
                "B - Functional - this is my second language but I can get by.",
                "A - Limited - I am just learning the language."=>
                "A - Limited - I am just learning the language."],old('language_read_'.$otherid,
                isset($otherlanguage->reading) ? $otherlanguage->reading :""),
                array('class' => 'form-control','required'=>TRUE,'id'=>'language_read'.$otherid)) }}
                    {!! $errors->first('language_read.'.$otherid) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        <div class="form-group row {{ $errors->has('language_write_'.$otherid) ? 'has-error' : '' }}"
            id="language_write_{{$i}}">
            <label class="col-sm-5 col-form-label">Writing</label>
            <div class="col-sm-7">

                {{ Form::select('language_write_'.$otherid,
                [null=>'Please select the appropriate answer from the dropdown list',
                "C - Fluent - this is my native language."=>"C - Fluent - this is my native language.",
                "B - Functional - this is my second language but I can get by."=>
                "B - Functional - this is my second language but I can get by.",
                "A - Limited - I am just learning the language."=>
                "A - Limited - I am just learning the language."],old('language_write'.$otherid,
                isset($otherlanguage->writing) ? $otherlanguage->writing :""),
                array('class' => 'form-control','required'=>TRUE,'id'=>'language_write_'.$otherid)) }}

                <div class="form-control-feedback">
                    {!! $errors->first('language_write.'.$otherid) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        </div>
        <div class="form-group row {{ $errors->has('language.'.$i) ? 'has-error' : '' }}" id="language.{{$otherid}}">
            <label class="col-sm-5 col-form-label"></label>
            <div class="col-sm-7">

            </div>
            @if($loop->iteration==$otherlanguages->count() )
            <div class="col-sm-12">
             <a href="javascript:void(0);"  class="remove-language  pull-right">
                 <i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
            </div>
            @else
            <div class="col-sm-12">
             <a href="javascript:void(0);"  class="remove-language hide-this-block pull-right">
                 <i class="fa fa-minus" aria-hidden="true"></i> Remove</a>
            </div>
            @endif
         </div>


</div>
@endforeach


