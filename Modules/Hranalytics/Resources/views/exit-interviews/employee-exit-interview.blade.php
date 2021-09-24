@extends('layouts.app')
@section('content')
<div class="table_title">

</div>
{{ Form::open(array('id'=>'employeeexitinterviewformm','class'=>'form-horizontal', 'method'=> 'POST')) }}
@include('hranalytics::exit-interviews.employee-exit-interview-form')
<div class="form-group row">
    <div class="col-sm-5"></div>
    <div class="col-sm-6">
        {{ Form::reset('Cancel', array('class'=>'btn cancel','onclick'=>'cancelExitInterview()'))}}
        {{ Form::submit('Submit',array('class'=>'btn submit'))}}
    </div>
</div>
{{ Form::close() }}
@endsection
@include('hranalytics::exit-interviews.partials.scripts')
