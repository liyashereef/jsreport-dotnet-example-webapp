
@extends('layouts.app') 
@section('content')
<div class="table_title">
   
</div>
 {{ Form::open(array('id'=>'stc-formm','class'=>'form-horizontal', 'method'=> 'POST' )) }} 
@include('hranalytics::exit-interviews.employee-exit-interview-summary-form')
{{ Form::close() }}
 @section('scripts') 
@include('hranalytics::exit-interviews.partials.scripts') 
@stop
