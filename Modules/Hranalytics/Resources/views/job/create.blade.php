@extends('layouts.app') 
@section('content')
<div class="table_title">
    <h4>Open Posting Requisition Forms </h4>
</div>
{{ Form::open(array('id'=>'job-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
@include('hranalytics::job.partials.form')
<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-6">
        {{ Form::reset('Cancel', array('class'=>'btn cancel','onclick'=>'cancelJobReq()'))}}
        {{ Form::submit('Post Job',array('class'=>'btn submit'))}}
    </div>
</div>
{{ Form::close() }} 
@endsection 
@include('hranalytics::job.partials.scripts')