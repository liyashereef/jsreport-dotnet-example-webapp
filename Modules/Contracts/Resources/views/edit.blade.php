@extends('layouts.app')
@section('content')
<section class="candidate">
    <div class="table_title">
    	<h4>Capacity Tool Edit</h4>
	</div>
    {{ Form::open(array('id'=>'capacity-tool-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    {{csrf_field()}}
    {{ Form::hidden('id', old('id', $capacity_tool_entry_id)) }}
    {{ Form::hidden('capacity_tool_id', isset($capacity_tool_entry_id) ? old('capacity_tool_id', $capacity_tool_entry_id) : null) }}
        @include('capacitytool::partials.form')
	{{ Form::close() }}
</section>
@endsection
@section('scripts')
 @include('capacitytool::partials.script')
@endsection
