@extends('layouts.app')
@section('content')
<style>
    .add-new{
        margin-top:-5px;
        margin-bottom:15px;
    }
    .text-wrap{
    /* height: 40px; */
    width: 250px;
    /* word-wrap: pre; */
    overflow: hidden;
    text-overflow: ellipsis;
}
    /* .width-200{
        width:100px;
    } */
</style>
<div class="table_title">
    <h4> Employee Whistleblower Summary </h4>
</div>
@canany(['create_all_whistleblower','create_allocated_whistleblower','create_employee_whistleblower'])
{{-- <div class="add-new" data-title="Whistleblower Form">Add
    <span class="add-new-label">New</span>
</div> --}}
@endcan
@canany(['view_employee_whistleblower','view_allocated_whistleblower','view_all_whistleblower','create_all_whistleblower','create_allocated_whistleblower','create_employee_whistleblower'])
<table class="table table-bordered" id="candidates-table">
    <thead>
        <tr>
            <th class="sorting" width="2%"></th>
            <th class="sorting" width="10%">Date</th>
            {{-- <th class="sorting" width="15%">Employee Details</th> --}}
            <th class="sorting" width="15%">Created by</th>
            <th class="sorting" width="15%">Customer</th>
            <th class="sorting" width="15%">Subject</th>
            <!-- <th class="sorting" width="15%">Category</th> -->
            <th class="sorting" width="15%">Policy Violation</th>
            <th class="sorting" width="10%">Priority</th>
            <th class="sorting" width="25%">Note</th>
            <th class="sorting" width="25%">Status</th>
            <th class="sorting" width="25%">Regional Manager Notes</th>
            <th class="sorting" width="5%">Location</th>
            @canany(['create_all_whistleblower','create_allocated_whistleblower'])
            <th class="sorting" width="2%">Action</th>
           @endcan
        </tr>
    </thead>
</table>
@endcan
<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Whistleblower Form</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        </div>
        {{ Form::open(array('url'=>'#','id'=>'employee-whistleblower-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
        {{ Form::hidden('id', null) }}
        <div class="modal-body">
            <div class="form-group" id="customer_feedback">
            <label for="customer_feedback" class="col-sm-3 control-label">Date</label>
            <div class="col-sm-11">
                    {!!Form::text('date',old('time',date('d/m/Y', strtotime($current_date))), ['class' => 'form-control','readonly'=>true])!!}
            <small class="help-block"></small>
            </div>
            </div>

            <div class="form-group" id="created_by">
                <label for="created_by" class="col-sm-3 control-label">Created by</label>
                <div class="col-sm-11">
                    <input type="text" name="created_by" class="form-control" readonly>
                </div>
            </div>

            <div class="form-group" id="customer_id">
                <label for="customer_id" class="col-sm-3 control-label">Customer</label>
                <div class="col-sm-11">
                {!!Form::select('customer_id',[null=>'Please Select'] + $project_list,null, ['class' => 'form-control select2'])!!}
                </div>
            </div>

           {{-- <div class="form-group  {{ $errors->has('employee_id') ? 'has-error' : '' }}" id="employee_id">
            <label for="employee_name" class="col-sm-3 control-label">Employee Name</label>
            <div class="col-sm-11">
                    {{ Form::select('employee_id',@$employeelist, null,array('class' => 'form-control select2','required'=>true)) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('employee_id', ':message') !!}</div>
            </div>
            </div> --}}

            <div class="form-group {{ $errors->has('whistleblower_subject') ? 'has-error' : '' }}" id="whistleblower_subject">
            <label for="whistleblower_subject" class="col-sm-3 control-label">Subject</label>
            <div class="col-sm-11">
                {!!Form::text('whistleblower_subject',null, ['class' => 'form-control','placeholder'=>"Subject",'maxlength'=>50,'required'=>true])!!}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_subject', ':message') !!}</div>
            </div>
            </div>
            <div class="form-group {{ $errors->has('whistleblower_category_id') ? 'has-error' : '' }}" id="whistleblower_category_id">
            <label for="whistleblower_category" class="col-sm-3 control-label">Category</label>
            <div class="col-sm-11">
                    {{ Form::select('whistleblower_category_id',@$categorylist, old('whistleblower_category_id'),array('class' => 'form-control','required'=>true)) }}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_category_id', ':message') !!}</div>
            </div>
            </div>
            <div class="form-group {{ $errors->has('policy_id') ? 'has-error' : '' }}" id="policy_id">
            <label for="policy" class="col-sm-3 control-label">Policy Violation</label>
            <div class="col-sm-11">
                {!!Form::select('policy_id',@$policylist, old('policy_id'), ['class' => 'form-control','required'=>true])!!}
                <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('policy_id', ':message') !!}</div>
            </div>
            </div>
            <div class="form-group {{ $errors->has('whistleblower_priority_id') ? 'has-error' : '' }}" id="whistleblower_priority_id">
            <label for="whsitleblower_priority" class="col-sm-3 control-label">Priority</label>
            <div class="col-sm-11">
                    {!!Form::select('whistleblower_priority_id',@$prioritylist, old('whistleblower_priority_id'), ['class' => 'form-control','required'=>true])!!}
                    <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_priority_id', ':message') !!}</div>
             </div>
            </div>
            <div class="form-group {{ $errors->has('whistleblower_documentation') ? 'has-error' : '' }}" id="whistleblower_documentation">
            <label for="whistleblower_documentation" class="col-sm-3 control-label">Note</label>
            <div class="col-sm-11">
              {{ Form::textarea('whistleblower_documentation',null,array('class'=>'form-control','placeholder'=>"Documentation",'maxlength'=>10000,'required'=>true)) }}
              <div class="form-control-feedback"><span class="help-block text-danger align-middle font-12"></span>{!! $errors->first('whistleblower_documentation', ':message') !!}</div>
            </div>
            </div>
            <div class="form-group" id="status_id" style="display:none;">
            <label for="status" class="col-sm-3 control-label">Status <span class="mandatory">*</span> </label>
            <div class="col-sm-11">
            <select name="status"  style="width: 100%;" class="form-control">
                <option value=0 disabled="disabled" selected>Please select</option>
                @if($statusList)
                @foreach ($statusList as $type)
                <option  value="{{ $type->id }}">{{$type->name}} </option>
                @endforeach
                @endif
            </select>
            <span class="help-block" id="status_error"></span>
             </div>
            </div>
            <div class="form-group" id="reg_manager_notes" style="display:none;">
            <label for="reg_manager_notes" class="col-sm-6 control-label">Regional Manager Notes <span class="mandatory">*</span></label>
            <div class="col-sm-11">
              {{ Form::textarea('reg_manager_notes',null,array('class'=>'form-control','placeholder'=>"Regional Manager Notes",'maxlength'=>1000)) }}
              <span class="help-block" id="reg_manager_notes_error"></span>
            </div>
            </div>

        </div>
        <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue ','id'=>'whistleblower_submit'))}}
            {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
        </div>
        {{ Form::close() }}
        </div>
    </div>
</div>
<div class="modal fade" id="modalContent" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div id="modal-content" style="height: 500px;" class="modal-body">
            </div>
            <div align="center"  style="display: none;"  id="modal-img-content" style="height: 550px;" class="modal-body">
                <div style="text-align: center;" >
                    <img  style="left: 50%;max-width: 600px;"  height="400px" id="ImgContainer" src="">
                </div>

            </div>

        </div>
    </div>
</div>

{{-- location Modal --}}
<div class="modal fade" id="modalContent" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div id="modal-content" style="height: 500px;" class="modal-body">
            </div>
            <div align="center"  style="display: none;"  id="modal-img-content" style="height: 550px;" class="modal-body">
                <div style="text-align: center;" >
                    <img  style="left: 50%;max-width: 600px;"  height="400px" id="ImgContainer" src="">
                </div>

            </div>

        </div>
    </div>
</div>
@stop
@include('hranalytics::whistleblower.partials.scripts')

