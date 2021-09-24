@extends('layouts.app')
@section('content')
<div class="table_title">
	@if(isset($rfpDetails))
    <h4>Edit RFP Summary </h4>
    @else
    <h4>Add RFP Summary </h4>
    @endif
</div>
 <div id="rfp" class="candidate-screen"><br>
{{ Form::open(array('id'=>'rfp-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
{{Form::hidden('id',isset($rfpDetails)? $rfpDetails->id:null)}}
 @include('contracts::rfp.partials.information')
 @include('contracts::rfp.partials.respondent')
 @include('contracts::rfp.partials.site-information')

{{-- Key RFP Response Submission Dates --}}
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Key RFP Response Submission Dates</h5>
    </label>
 @if (isset($rfpDetails))
@php $count=count($rfpDetails->responseSubmissionDates) @endphp
@include('contracts::rfp.partials.response-submission-date')
@foreach($rfpDetails->responseSubmissionDates as $key=>$date)
<div class="form-group row submit-date" data-id="{{isset($key) ? $key :$i}}"><label for="added_label" class="col-sm-5 label_class" id="submission_label_name.{{isset($key) ? $key :$i}}">{{ Form::text('submission_label_name[]',isset($date->response_submission_other_date_label) ? $date->response_submission_other_date_label :"",array('placeholder'=>'Label Name','class'=>'form-control')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></label><div class="col-sm-6 label_value_class" id="submission_label_value.{{isset($key) ? $key :$i}}">{{ Form::text('submission_label_value[]',isset($date->response_submission_other_date_value) ? $date->response_submission_other_date_value :"",array('placeholder'=>'Value','class'=>'form-control datepicker')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('submission_label_value', ':message') !!}</div></div><div class="col-sm-1"><a href="javascript:void(0);" class="remove_button" data-id="{{isset($key) ? $key :$i}}"  title="Remove field" onclick="removeSubmissionBlock($(this))"><i class="fa fa-minus" aria-hidden="true"></i></a></div></div> <div class="form-group" data-id="{{isset($key) ? $key :$i}}"></div>
@endforeach
@else
@include('contracts::rfp.partials.response-submission-date')
 <div class="form-group" data-id="-1"></div>
@endif
    <div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-7">
        <a href="javascript:void(0);" class="add-submission-dates"> <i class="fa fa-plus" aria-hidden="true"></i> Add Other Dates</a>
    </div>
</div>

{{-- Other Project Execution Dates --}}
<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Other Project Execution Dates</h5>
</label>
@if (isset($rfpDetails))
@php $count=count($rfpDetails->projectExecutionDates) @endphp
@include('contracts::rfp.partials.project-execution-date')
@foreach($rfpDetails->projectExecutionDates as $key=>$date)
<div class="form-group row execution-date" data-id="{{isset($key) ? $key :$i}}"><label for="added_label" class="col-sm-5 label_class" id="execution_label_name.{{isset($key) ? $key :$i}}">{{ Form::text('execution_label_name[]',isset($date->project_execution_other_date_label) ? $date->project_execution_other_date_label :"",array('placeholder'=>'Label Name','class'=>'form-control')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span></div></label><div class="col-sm-6 label_value_class" id="execution_label_value.{{isset($key) ? $key :$i}}">{{ Form::text('execution_label_value[]',isset($date->project_execution_other_date_value) ? $date->project_execution_other_date_value :"",array('placeholder'=>'Value','class'=>'form-control datepicker','id'=>'datepicker')) }}<div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('execution_label_value', ':message') !!}</div></div><div class="col-sm-1"><a href="javascript:void(0);" class="remove_button"    data-id="{{isset($key) ? $key :$i}}"  title="Remove field" onclick="removeExecutionBlock($(this))"><i class="fa fa-minus" aria-hidden="true"></i></a></div></div> <div class="form-group" data-id="{{isset($key) ? $key :$i}}"></div>
@endforeach
@else
@include('contracts::rfp.partials.project-execution-date')
<div class="form-group" data-id="-1"></div>
@endif

<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-7">
        <a href="javascript:void(0);" class="add-execution-dates"> <i class="fa fa-plus" aria-hidden="true"></i> Add Other Dates</a>
    </div>
</div>
 @include('contracts::rfp.partials.rfp-contact')

 {{-- Evaluation Criteria --}}
@php
$i=0;
@endphp
 <label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 orange">
        <h5>Evaluation Criteria</h5>
 </label>
 @if (isset($rfpDetails))
@php $count=count($rfpDetails->evaluationCriteria) @endphp
@foreach($rfpDetails->evaluationCriteria as $key=>$criteria)
@include('contracts::rfp.partials.evaluation-criteria', array('data' => $criteria,'key'=>$key,'maximum'=>$count))
@endforeach
@else
@include('contracts::rfp.partials.evaluation-criteria')

@endif
 <div class="form-group row">
     <div class="col-sm-5"></div>
     <div class="col-sm-7">
         <a href="javascript:void(0);" class="add-criteria"> <i class="fa fa-plus" aria-hidden="true"></i> Add Other Criteria</a>
     </div>
 </div>


 @include('contracts::rfp.partials.project-summary')
 @include('contracts::rfp.partials.unionization')
<div class="form-group row">
    <div class="offset-md-5 col-sm-6">
        {{ Form::reset('Cancel', array('class'=>'btn cancel','onclick'=>'window.history.back();'))}}
        {{ Form::submit('Submit',array('class'=>'btn submit'))}}
    </div>
</div>
{{ Form::close() }}
</div>
@endsection
@include('contracts::rfp.partials.script')
