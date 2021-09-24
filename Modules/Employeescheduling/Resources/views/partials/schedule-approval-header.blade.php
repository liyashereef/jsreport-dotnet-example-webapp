
@if(isset($scheduleObj) && $scheduleObj)
<style>
    .card-body-1 {
        padding: 0.25rem !important;
        background-color: #fff !important;
        /*margin-right: 1.25em !important;*/
    }

    .custom-schedule-detail-section {
        cursor: pointer;
        width: auto;
    }

    .reason_1, .reason_2 {
        color: #f48452 !important;
    }
    
</style>
@php($status = (($scheduleObj->status_notes != '') ? 'Reason Notes : '.$scheduleObj->status_notes: (($scheduleLastStatusNotes != '')?  'Reason Notes : '.$scheduleLastStatusNotes: '')))
<div class="panel-group custom-schedule-detail-section">
    <div class="card">
        <div class="card-body-1">
            @php($updated_by = ($scheduleObj->statusUpdatedUser? ucwords(strtolower($scheduleObj->statusUpdatedUser->getFullNameAttribute())): ($scheduleObj->updatedUser? ucwords(strtolower($scheduleObj->updatedUser->getFullNameAttribute())) : '')))
            @php($updated_at = ($scheduleObj->status_update_date && $scheduleObj->statusUpdatedUser)? date('d-m-Y', strtotime($scheduleObj->status_update_date)): (($scheduleObj->updatedUser && $scheduleObj->updated_at)? date('d-m-Y', strtotime($scheduleObj->updated_at)): ''))

            @if($updated_by != '' && $updated_at != '')
            @php($css_col_value1 = 2)
            @php($css_col_value2 = 3)
            @else
            @php($css_col_value1 = 3)
            @php($css_col_value2 = 3)
            @endif

            <div class="container">
                <!--Row with two equal columns-->
                <div class="row">
                    <div class="col-md-{{$css_col_value2}}">Customer:</div>
                    <div class="col-md-{{$css_col_value1}}">Status:</div>
                    <div class="col-md-{{$css_col_value2}}">Created:</div>

                    @if($updated_by != '' && $updated_at != '')
                    <div class="col-md-{{$css_col_value2}}">Updated:</div>
                    @endif

                    @if($scheduleObj->supervisornotes != '')
                    <div class="col-md-1">&nbsp;</div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-{{$css_col_value2}}"><span class="detail-section-span">{{$scheduleObj->customer? ($scheduleObj->customer->client_name.' ('.$scheduleObj->customer->project_number.')'): ''}}</span></div>
                    <div class="col-md-{{$css_col_value1}}">
                        <span class="detail-section-span schedule-status reason_{{$scheduleObj->status}}" title="{!!$status!!}">
                            @if($status !== '')
                            @endif
                            {{(($scheduleObj->status == '2') ? 'Rejected': (($scheduleObj->status == '1') ? 'Approved': 'Pending'))}}
                            @if($status !== '')
                            @endif
                        </span>
                        @if(isset($scheduleId))
                        &nbsp;<i id="export_to_image" class="fa fa-download" title="Export to PNG" style="color: black !important;"></i>
                        @endif
                    </div>
                    <div class="col-md-{{$css_col_value2}}"><span class="detail-section-span">{{$scheduleObj->createdUser? ucwords(strtolower($scheduleObj->createdUser->getFullNameAttribute())): ''}}&nbsp;{{$scheduleObj->created_at? '('.date('d-m-Y', strtotime($scheduleObj->created_at)).')': ''}}</span></div>

                    @if($updated_by != '' && $updated_at != '')
                    <div class="col-md-{{$css_col_value2}}">{{$updated_by}}&nbsp;({{$updated_at}})</div>
                    @endif
                </div>

                
                @if($scheduleObj->supervisornotes != '')
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">Supervisor Note:&nbsp;{{$scheduleObj->supervisornotes}}</div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">{!!$status!!}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif