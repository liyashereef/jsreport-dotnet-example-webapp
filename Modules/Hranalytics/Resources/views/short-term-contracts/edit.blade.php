@extends('layouts.app')
@section('content')
<section class="candidate">
    <div class="table_title">
    	<h4>Short Term Contracts Edit</h4>
	</div>
    {{ Form::open(array('id'=>'stc-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
    {{csrf_field()}}
    {{ Form::hidden('id', old('id', $customer->id)) }}
    {{ Form::hidden('customer_stc_details_id', isset($customer_stc_details->id) ? old('customer_stc_details_id', $customer_stc_details->id) : null) }}
        @include('hranalytics::short-term-contracts.partials.form')
	{{ Form::close() }}
</section>
@endsection
@section('scripts')
 @include('hranalytics::short-term-contracts.partials.script')
@endsection
