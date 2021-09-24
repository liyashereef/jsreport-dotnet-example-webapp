@extends('layouts.app')
@section('content')
@section('css')
<style>
    footer {
        position: fixed;
    }

    #content-div {
        width: 97%;
    }

     .squareblock{
        border: solid 1px #000;
        text-align: center;
        height: 34px;
        font-size: 12px;
        width: 50%;
        margin-top: -34px;
        margin-left: 126px;
        border-radius: 6%;
        vertical-align: middle;
        line-height: 30px;
    }
    .squareblockcap{
        font-size: 12px;
        padding-right: 0px;
        padding-left: 40px;
    }

    .squareblocktd {
        padding-left: 0px;
    }

    body {
        overflow-y: hidden;
    }

</style>
@endsection
<div class="table_title">
    @php
    $schedule_grid_heading = 'Schedule Requests';
    @endphp
    @canany(['approve_all_employee_schedule_requests','approve_allocated_employee_schedule_requests'])
    @php
    $schedule_grid_heading = 'Schedule Approval';
    @endphp
    @endcanany
    <h4>Schedule Approval</h4>
</div>
<div style="padding-top: 1%;">
    <div class="form-group row">
        @include('employeescheduling::partials.schedule-approval-filters-section')
        <div id="schedule-summary" class="col-md-8" style="text-align: right;"></div>
    </div>

    @include('employeescheduling::partials.schedule-approval-header')

    <div style="{{isset($scheduleId)? 'max-height: 450px  !important;overflow-x: hidden !important;': ''}}">
        @include('employeescheduling::partials.schedule-approval-content')
    </div>

    <div class="row" style="padding-top: 10px;float: right;margin-right:0.1%;">
        @canany(['approve_all_employee_schedule_requests','approve_allocated_employee_schedule_requests'])
        @if(isset($enableApproveReject) && $enableApproveReject)
        <div class="approval_button_div" >
            <button class="btn btn-sm btn-primary approve-btn" id="approve_btn_element">&nbsp;Approve</button>
            <button class="btn btn-sm btn-primary"  id="cancel_btn_element">&nbsp;Reject</button>
        </div>
        @elseif(isset($rejectApprovedSchedules) && $rejectApprovedSchedules)
        <div class="approval_button_div">
            <button class="btn btn-sm btn-primary"  id="cancel_btn_element">&nbsp;Reject</button>
        </div>
        @endif
        @endcanany
    </div>
</div>

<div id="approval_note_modal" class="modal fade" role="dialog" aria-labelledby="approvalModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="approvalModalLabel"></h4>
                <button type="button" id="modal-close" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="value">
                    <label for="value" class="col-sm-3 control-label">Notes </label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="schedule_note" name="schedule_note" maxlength="255"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary approval_note_modal_save" id="approval_note_modal_save" style="display:none;">Approve</button>
                <button class="btn btn-sm btn-primary reject_note_modal_save" id="reject_note_modal_save" style="display:none;">Reject</button>
                <button class="btn btn-sm btn-primary" id="approval_note_modal_cancel" data-dismiss="modal" aria-hidden="true">Cancel</button>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
@include('employeescheduling::partials.schedule-approval-scripts')
@stop
