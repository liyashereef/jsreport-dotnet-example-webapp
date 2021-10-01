@extends('layouts.app')
<style>
    
   
</style>
@section('content')
<!-- Container - Start -->

    <!-- Add document - Start -->

	{{ Form::open(array('url'=>'','id'=>'add-document-form', 'method'=> 'POST','autocomplete'=> "off")) }}
    {{ Form::hidden('document_type_id', isset($typeid) ? old('type_id',$typeid) : null,array('id'=>'type_id')) }}
    @if($typeid == EMPLOYEE)
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head">
    @endif
    @if($typeid == CLIENT)
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head">
    @endif
    @if($typeid == OTHER)
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head">
    @endif
        @if($typeid == EMPLOYEE)
        
		{{isset($employee_list) ? ($employee_list['employee_details']) : null}}
        @elseif($typeid == CLIENT)
        {{isset($client_list) ? ($client_list['project_number']." - ".$client_list['client_name']) : null}}
        @elseif($typeid == OTHER)
		{{isset($other_list) ? ($other_list['otherlist']['name']." - ".$other_list['otherlist']->otherCategory->category_name) : null}}
		@endif
	</div>
    
	@if($typeid == EMPLOYEE)
		@include('documents::view-employee-details')
    @elseif($typeid == CLIENT)
        @include('documents::view-client-details')
	@else
        @include('documents::view-other-details')
	@endif
    
    <div class='layout'>
    @if($typeid == EMPLOYEE || $typeid == CLIENT)
    
        <div class="form-group row"  id="document_category_id">
             <label for="document_category_id" class="col-sm-4 col-form-label">Select the document category</label>
             <div class="col-sm-6">
				@if($typeid == EMPLOYEE)
					{{ Form::select('document_category_id',[null=>'Please Select']+$employee_list['document_category'], isset($time_off_edit_details) ? old('leave_reason_id',$time_off_edit_details->leave_reason_id) : null, array('class' => 'form-control select2', 'id'=>'document_categories','required'=>true)) }}
				@else
					{{ Form::select('document_category_id',[null=>'Please Select']+$client_list['document_category'], isset($time_off_edit_details) ? old('leave_reason_id',$time_off_edit_details->leave_reason_id) : null, array('class' => 'form-control select2', 'id'=>'document_categories','required'=>true)) }}
				@endif    
             <div class="form-control-feedback">{!! $errors->first('document_category_id') !!}
                   <span class="help-block text-danger align-middle font-12"></span>
            </div>
                </div>
             </div>
    @endif
    
             <div class="form-group row"  id="document_name_id">
             <label for="document_name_id" class="col-sm-4 col-form-label">What is the name of the document</label>
             <div class="col-sm-6">
            @if($typeid == OTHER)
             {{ Form::select('document_name_id',[null=>'Please Select'] + $other_list['documentnames'], isset($time_off_edit_details) ? old('leave_reason_id',$time_off_edit_details->leave_reason_id) : null, array('class' => 'form-control select2', 'id'=>'document_names','required'=>true)) }}
            @else
            {{ Form::select('document_name_id',[null=>'Please Select'], isset($time_off_edit_details) ? old('leave_reason_id',$time_off_edit_details->leave_reason_id) : null, array('class' => 'form-control select2', 'id'=>'document_names','required'=>true)) }}
            @endif
             <div class="form-control-feedback">{!! $errors->first('years_security_experience') !!}
                   <span class="help-block text-danger align-middle font-12"></span>
            </div>
                </div>
             </div>
           @if(($typeid == OTHER && isset($other_list['is_valid']->is_valid)) || (($typeid == OTHER && empty($other_list['documentnames']))))
             <div class="form-group row"  id="document_expiry_date">
                <label for="document_expiry_date" class="col-sm-4 col-form-label">Valid Until</label>
                <div class="col-sm-6">
                    {{ Form::text('document_expiry_date',old('document_expiry_date', ''),array('placeholder'=>'Valid Until (Y-m-d)','class'=>'form-control datepicker','readonly' => 'true')) }}
                <div class="form-control-feedback">{!! $errors->first('document_expiry_date') !!}
                      <span class="help-block text-danger align-middle font-12"></span>
               </div>
             </div>
            </div>
            @endif
             <div class="form-group row"  id="document_description">
                <label for="document_description" class="col-sm-4 col-form-label">Please provide a brief description of uploaded document</label>
                <div class="col-sm-6">
                    {{ Form::textarea('document_description',null,array('class'=>'form-control','id'=>'description','placeholder'=>'Description')) }}
                    <div class="form-control-feedback">{!! $errors->first('document_description') !!}
                    <span class="help-block text-danger align-middle font-12"></span>
                    </div>
                </div>
             </div>
  
			  <div class="form-group row"  id="document_attachment">
                <label for="document_attachment" class="col-sm-4 col-form-label">Upload Document</label>
                <div class="col-sm-6">
			        <input type="file" class="form-control" name="document_attachment[]" required="true">
                    <div class="form-control-feedback">{!! $errors->first('document_attachment') !!}
                        <span class="help-block text-danger align-middle font-12"></span>
                    </div>
                </div>
             </div>
		    <div class="data-list-line form-group row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-xs-center text-sm-center text-md-center text-lg-center text-xl-center margin-top-1">
		        </div>
		        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xl-6 text-xs-center text-sm-left text-md-left text-lg-left text-xl-left margin-top-1">
		        	{{ Form::submit('Submit', array('class'=>'button btn btn-primary blue submit'))}}
		        </div>
		    </div>
		    {{ Form::close() }}
		</div>
</div>

<!-- Container - End -->
<style type="text/css">
    #description{
        margin-top: 0px;
        height: 120px;
        margin-bottom: 0px;
    }
.layout{
    margin-left: -35px;
}
    
    </style>
@stop
@section('scripts')
@include('documents::scripts') 
@stop
