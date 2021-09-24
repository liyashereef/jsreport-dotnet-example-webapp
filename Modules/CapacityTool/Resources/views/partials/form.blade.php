
@foreach($lookups as $lookup)
<div class="{{($lookup->is_parent == 0) ? 'form-group row': 'form-group parent_'.$lookup->parent_id }}" id="question_{{$lookup->id}}">
   @if($lookup->is_parent == 1)
   <div class="form-group row">
    @endif
    <label for='question_id_{{$lookup->id}}' class="col-sm-5 col-form-label">{{$lookup->question}}</label>
    <div class="col-sm-6">
        @if($lookup->field_type == 'dropdown')
           {{ Form::select('answer_id_'.$lookup->id,[null=>'Select']+$lookup->answers, old('answers',isset($lookup->answer) ? $lookup->answer :""),array('class' => 'test form-control', 'data-answer-type'=>$lookup->answer_type , 'required', 'title'=>!empty($lookup->tooltip)? $lookup->tooltip : 'Please select an item in the list.')) }}
        @elseif($lookup->field_type == 'project_name')
           <select id="answer_id_{{$lookup->id}}"  title="@if(!empty($lookup->tooltip)) {{$lookup->tooltip}} @else 'Please select an item in the list.' @endif" class="test form-control select2 project_name">
                @foreach($lookup->answers as $project)
                    <option @if($project['id'] == $lookup->answer) selected @endif rel="{{$project['client_name']}}" value="{{$project['id']}}">{{$project['project_number']}}</option>
                @endforeach
            </select>
           <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
        </div>
    </div>
    <div class="form-group parent_projectname" id="">
    <div class="form-group row" id="question_">
        <label for='question_id_' class="col-sm-5 col-form-label">Project Name</label>
        <div class="col-sm-6">
            {{ Form::text('answer_id_'.$lookup->id, isset($lookup->project_name) ? old('',$lookup->project_name) : null, array('class'=>'form-control', 'placeholder'=>$lookup->question, 'data-answer-type'=>$lookup->answer_type , 'readonly')) }}
        </div>
        @elseif($lookup->field_type == 'textarea')
            {{ Form::textarea('answer_id_'.$lookup->id,  isset($lookup->answer) ? old('',$lookup->answer) : null, array('class'=>'form-control', 'placeholder'=>$lookup->question, 'data-answer-type'=>$lookup->answer_type , 'required')) }}
        @else
            @if($lookup->question == 'Name')
                {{ Form::text('answer_id_'.$lookup->id,  old('answer_id_'.$lookup->id, ucwords(isset(Auth::user()->last_name) ? Auth::user()->first_name.' '.Auth::user()->last_name : Auth::user()->first_name)), array('class'=>'form-control', 'placeholder'=>$lookup->question, 'data-answer-type'=>$lookup->answer_type , 'required', 'readonly')) }}
            @elseif($lookup->field_type == 'datepicker')
                {{ Form::text('answer_id_'.$lookup->id, isset($lookup->answer) ? old('',$lookup->answer) : null, array('class'=>'form-control datepicker', 'placeholder'=>$lookup->question, 'data-answer-type'=>$lookup->answer_type , 'required')) }}
            @else
                {{ Form::text('answer_id_'.$lookup->id, isset($lookup->answer) ? old('',$lookup->answer) : null, array('class'=>'form-control', 'placeholder'=>$lookup->question, 'data-answer-type'=>$lookup->answer_type , 'required')) }}
            @endif
        @endif
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>
@if($lookup->is_parent == 1)
</div>
 @endif
@endforeach

<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-6">
        {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'save'))}}
        {{ Form::button('Cancel', array('class'=>'btn cancel', 'type'=>'reset','onClick'=>'window.history.back();'))}}
    </div>
</div>
