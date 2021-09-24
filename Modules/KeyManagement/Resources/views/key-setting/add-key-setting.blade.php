@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Customer Key Setting</h4>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$project_list,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
<table class="table table-bordered" id="candidates-table">
    <thead>
        <tr>
            <th class="sorting" width="2%"></th>
            <th class="sorting" width="10%">Project Number</th>
            <th class="sorting" width="15%">Client Name</th>
            <th class="sorting" width="15%">Client Contact Person</th>
            <th class="sorting" width="15%">Client Contact Person Email</th>
            <th class="sorting" width="15%">Client Contact Phone Number</th>
            @canany(['view_all_customers_keys','view_allocated_customers_keys'])
            <th class="sorting" width="5%">Keys</th>
            @endcan
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Add Key</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::open(array('url'=>'#','id'=>'customerkey-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ Form::hidden('id', null) }}
        <div class="modal-body">
            <div class="form-group" id="customer_feedback">
            <label for="customer_feedback" class="col-sm-3 control-label">Customer</label>
            <div class="col-sm-11">
            {{ Form::select('customer_id',[''=>'Select a customer']+$customerlist,null,array('class'=>'form-control select2 customer_select', 'style'=>"width: 100%;")) }}
            <small class="help-block"></small>
            </div>
            </div>
            <div class="form-group {{ $errors->has('whistleblower_subject') ? 'has-error' : '' }}" id="whistleblower_subject">
            <label for="whistleblower_subject" class="col-sm-3 control-label">Key ID</label>
            <div class="col-sm-11">
            {{ Form::text('key_id',null,array('class'=>'form-control','placeholder' => 'Key ID','maxlength'=>100)) }}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_subject', ':message') !!}</div>
            </div>
            </div>
            <div class="form-group {{ $errors->has('whistleblower_category_id') ? 'has-error' : '' }}" id="whistleblower_category_id">
            <label for="whistleblower_category" class="col-sm-3 control-label">Room Name</label>
            <div class="col-sm-11">
            {{ Form::text('room_name',null,array('class'=>'form-control','placeholder' => 'Room Name')) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_category_id', ':message') !!}</div>
            </div>
            </div>
            <div class="form-group {{ $errors->has('whistleblower_priority_id') ? 'has-error' : '' }}" id="whistleblower_priority_id">
            <label for="whsitleblower_priority" class="col-sm-3 control-label">Key Image</label>
            <div class="col-sm-11">
            <input type="file" class="form-control" name="key_image[]" required="true">
            <label id="key_image_name" ></label>
            <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_priority_id', ':message') !!}</div>
             </div>
            </div>
        </div>
        <div class="modal-footer">
        {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
        {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal', 'onclick'=>"cancel()"))}}
        </div>
        {{ Form::close() }}
        </div>
    </div>
</div>
@stop
@include('keymanagement::key-setting.partials.scripts')
