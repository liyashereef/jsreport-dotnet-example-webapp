@php
$i=0;
//$id= app('request')->input('id');
if ($id>=1)
$i=$id;
@endphp

<div class="language-container languageblock sublangblock{{$id}}">
    <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <div class="pos">Language {{isset($key) ? $key+1 :"1"}}</div></label>
        <div class="form-group row {{ $errors->has('language_.'.$i) ? 'has-error' : '' }}"
            id="language_{{$i}}">
            <label class="col-sm-5 col-form-label">Language</label>
            <div class="col-sm-7">
                <select name="language_{{$id}}" id="" class="form-control languageblockselectnew" required>
                    <option value="">Select any from list</option>
                    @foreach ($languages as $language)
                        <option value="{{$language->id}}">{{$language->language}}</option>
                    @endforeach

                </select>
                <div class="form-control-feedback">
                    {!! $errors->first('language_speak_'.$i) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        </div>
        <div class="form-group row {{ $errors->has('language_speak_'.$id) ? 'has-error' : '' }}"
            id="language_speak.{{$i}}">
            <label class="col-sm-5 col-form-label">Speaking/Oral Comprehension</label>
            <div class="col-sm-7">
                {{ Form::select('language_speak_'.$id,
                [null=>'Please select the appropriate answer from the dropdown list',
                "C - Fluent - this is my native language."=>"C - Fluent - this is my native language.",
                "B - Functional - this is my second language but I can get by."=>
                "B - Functional - this is my second language but I can get by.",
                "A - Limited - I am just learning the language."=>
                "A - Limited - I am just learning the language."],old('language_speak_'.$i,
                null),
                array('class' => 'form-control','required'=>TRUE,'id'=>'language_speak_'.$id)) }}
                    {!! $errors->first('language_speak_'.$id) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        <div class="form-group row {{ $errors->has('language_read_'.$id) ? 'has-error' : '' }}"
            id="language_read_{{$id}}">
            <label class="col-sm-5 col-form-label">Reading</label>
            <div class="col-sm-7">
                {{ Form::select('language_read_'.$id,
                [null=>'Please select the appropriate answer from the dropdown list',
                "C - Fluent - this is my native language."=>"C - Fluent - this is my native language.",
                "B - Functional - this is my second language but I can get by."=>
                "B - Functional - this is my second language but I can get by.",
                "A - Limited - I am just learning the language."=>
                "A - Limited - I am just learning the language."],old('language_read_'.$id,
                null),
                array('class' => 'form-control','required'=>TRUE,'id'=>'language_read'.$id)) }}
                    {!! $errors->first('language_read.'.$id) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        <div class="form-group row {{ $errors->has('language_write_'.$id) ? 'has-error' : '' }}"
            id="language_write_{{$i}}">
            <label class="col-sm-5 col-form-label">Writing</label>
            <div class="col-sm-7">

                {{ Form::select('language_write_'.$id,
                [null=>'Please select the appropriate answer from the dropdown list',
                "C - Fluent - this is my native language."=>"C - Fluent - this is my native language.",
                "B - Functional - this is my second language but I can get by."=>
                "B - Functional - this is my second language but I can get by.",
                "A - Limited - I am just learning the language."=>
                "A - Limited - I am just learning the language."],old('language_write'.$id,
                null),
                array('class' => 'form-control','required'=>TRUE,'id'=>'language_write_'.$id)) }}

                <div class="form-control-feedback">
                    {!! $errors->first('language_write.'.$id) !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                </div>
            </div>
        </div>
        <div class="form-group row {{ $errors->has('language.'.$i) ? 'has-error' : '' }}" id="language.{{$id}}">
            <label class="col-sm-5 col-form-label"></label>
            <div class="col-sm-7">

            </div>
            @if(isset($key) && $key==$count-1 &&  $key!=0 )
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
