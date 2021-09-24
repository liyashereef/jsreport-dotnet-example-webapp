<?php $already_shown = null;?>
@php ($i = 0)
@foreach($lookups['screening_questions'] as $screening_question)
@if($screening_question->category==null || $screening_question->category!=$already_shown)
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">{{ $screening_question->category }}</label>
<?php $already_shown = $screening_question->category;?>
@endif
<div class="form-group row"  {{ $errors->has('answer.'.$screening_question->id) ? 'has-error' : '' }}" id="answer.{{$screening_question->id}}">
    <label class="col-sm-5 col-form-label">{{$screening_question->screening_question}}</label>
    <div class="col-sm-7">
        @php($j=$i++)
        {{Form::textarea('answer[' .$screening_question->id. ']',
            old('answer',isset($candidate->screening_questions[$j]->answer) 
            ? $candidate->screening_questions[$j]->answer :""),array('class'=>'form-control','placeholder'=>"", 'maxlength'=>"500",'rows'=>6,'required'=>TRUE))}}
        <!-- Set Scores -->
        <input type="hidden" name="_sc[{{$screening_question->id}}]"
        value="{{isset($candidate->screening_questions[$j])
            ?$candidate->screening_questions[$j]->score
            :''}}"/>
        <div class="form-control-feedback">
        	 {!! $errors->first('answer.'.$screening_question->id) !!}
                <span class="help-block text-danger align-middle font-12"></span>
        </div>
    </div>
</div>
@endforeach

