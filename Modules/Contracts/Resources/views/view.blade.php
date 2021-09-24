@extends('layouts.app')
 @section('content')
<div class="table_title">
    <h4>Capacity Tool</h4>
</div>

@foreach($lookups as $lookup)
{{-- {{dump($lookup)}} --}}
<div class="form-group row" id="question_{{$lookup->id}}">
    <label for='question_id_{{$lookup->id}}' class="col-sm-5 col-form-label">{{$lookup->question}}</label>
    <div class="col-sm-6 col-form-label">
        
        <div> {{ $lookup->answer }} </div>
        {{-- Form::text('answer_id_'.$lookup->id, $lookup->answer , array('class'=>'form-control', 'placeholder'=>$lookup->question)) --}}
       
        <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div>
    </div>
</div>
@endforeach
@endsection 

