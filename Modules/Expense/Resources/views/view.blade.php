@extends('layouts.app')
@section('content')


<div style="margin-bottom:65px;" class="table_title">
    <h4 style="float:left;">Expense Claim for {{$result->created_user->first_name.' '.$result->created_user->last_name}} on {{ date('M d, Y', strtotime($result->date)) }}</h4>
    <div style="float:right;margin-top: 15px;" class="btn submit pdf-hide" onClick="window.print();">
        <a href="javascript:;"> Print </a>
    </div>
</div>
@if($result->attachment_id != '')
<div class="expense-card">
    <div class="row">
        <div class="col-md-7 col-sm-8">

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Transaction Date</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{ date('M d, Y', strtotime($result->date)) }}</label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Submitted By</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->created_user->first_name.' '.$result->created_user->last_name}}</label>
                </div>
            </div>

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Billable</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">@if($result->billable == 1) Yes @else No @endif</label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Employee Number</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->created_user->employee->employee_no ?? '--'}}</label>
                </div>
            </div>

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Payment Mode </b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->mode_of_payment->mode_of_payment ?? '--'}}</label>
                </div>

                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Project Number</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->customer->project_number ?? '--'}}</label>
                </div>
            </div>

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Status</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->status->status ?? ''}} </label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Project Name</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->customer->client_name ?? '--'}}</label>
                </div>
            </div>

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Total Amount</b><br><span style="font-size:12px;">(Subtotal+Tax+Tip)</span></label>
                    <label class="col-md-7 expense-labels col-xs-7">${{$result->amount ?? '0'}} </label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Category</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->expenseCategory->name ?? '--'}} </label>
                </div>
            </div>

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Tip</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">${{$result->tip ?? '0'}} </label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b> Claim Reimburse </b></label>
                    <label class="col-md-7 expense-labels col-xs-7">@if($result->claim_reimbursement == 1) Yes @else No @endif</label>
                </div>
            </div>


            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Tax</b> <br><span style="font-size:12px;">(@ {{$result->tax_percentage ?? '0'}}%)</span></label>
                    <label class="col-md-7 expense-labels col-xs-7"> {{$result->tax_amount ? '$'.$result->tax_amount   :  '--'}}</label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Description</b></label>
                    <label class="col-md-7 expense-labels col-xs-7"> {{$result->description ?? '--'}}</label>
                </div>

            </div>

            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Subtotal</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">${{$result->amount - $result->tax_amount - $result->tip}} </label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Participants</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->participants ?? '--'}} </label>
                </div>
            </div>
            <div class="row styled-form-readonly">
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>GL Code</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{ $result->expenseGlCode->gl_code ?? '--'}} </label>
                </div>
                <div class="col-md-5 col-sm-5">
                    <label class="col-md-4 expense-labels  col-xs-4"><b>Cost Center</b></label>
                    <label class="col-md-7 expense-labels col-xs-7">{{$result->cost_center->center_number ?? '--'}} </label>
                </div>
            </div>

        </div>

        <div class="col-md-5 col-sm-4">
            <a id="pdfLink" target="_blank" href="">
                <img style="max-width:400px;margin-top:20px;margin-left:20px;padding:5px;border:2px solid #e4dede;background-color: #e6e6e6;max-height: 600px;" width="90%" id="ImgContainer" src="">
            </a>
        </div>
    </div>
</div>
@else
<div class="expense-card">
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">

                <label class="col-md-5 expense-labels col-xs-4"><b>Total Amount</b><br><span style="font-size:12px;">(Subtotal+Tax+Tip)</span></label>
                <label class="col-md-7 expense-labels  col-xs-4">${{$result->amount}}</label>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels  col-xs-3"><b>Submitted By</b></label>
                <label class="col-md-7 expense-labels col-xs-4">{{$result->created_user->first_name.' '.$result->created_user->last_name}}
                </label>
            </div>
        </div>
        <div class="col-xs-4 col-sm-2 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels col-xs-4"><b>Project Name</b></label>
                <label class="col-md-8 expense-labels  col-xs-4">{{$result->customer->client_name ?? '--'}}</label>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels col-xs-4"><b>Billable</b></label>
                <label class="col-md-8 expense-labels  col-xs-4">@if($result->billable == 1) Yes @else No @endif</label>
            </div>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels col-xs-4"><b> Participants</b></label>
                <label class="col-md-7 expense-labels col-xs-4"> {{$result->participants ?? '--'}}
                </label>
            </div>
        </div>


    </div>

    <div class="row">

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels col-xs-4"><b>Tip</b><br></label>
                <label class="col-md-7 expense-labels  col-xs-4">${{$result->tip ?? '0'}}</label>
            </div>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels  col-xs-4"><b>Employee Number</b></label>
                <label class="col-md-7 expense-labels col-xs-4">{{$result->created_user->employee->employee_no ?? '' }}</label>
            </div>
        </div>
        <div class="col-xs-4 col-sm-2 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels col-xs-4"><b>Project Number</b></label>
                <label class="col-md-8 expense-labels  col-xs-4">{{$result->customer->project_number ?? '--'}}</label>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels  col-xs-4"><b>Payment Mode</b></label>
                <label class="col-md-8 expense-labels col-xs-4">{{$result->mode_of_payment->mode_of_payment ?? '--'}}
                </label>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels  col-xs-4"><b>Claim Reimburse</b></label>
                <label class="col-md-8 expense-labels col-xs-4 email-break">@if($result->claim_reimbursement == 1) Yes @else No @endif</label>
            </div>
        </div>


    </div>
    <div class="row">

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels col-xs-4"><b>Tax</b> <br><span style="font-size:12px;">(@ {{$result->tax_percentage ?? '0'}}%)</span></label>
                <label class="col-md-7 expense-labels  col-xs-4 email-break">
                    {{$result->tax_amount ? '$'.$result->tax_amount   :  '--'}}
                </label>
            </div>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                {{-- <label class="col-md-3 expense-labels  col-xs-4"><b>Reason</b></label>
                <label class="col-md-7 expense-labels col-xs-4 email-break">{{$result->no_attachment_reason ?? '--'}}</label> --}}

                <label class="col-md-5 expense-labels  col-xs-4"><b>Transaction Date</b></label>
                <label class="col-md-7 expense-labels col-xs-4">{{ date('M d, Y', strtotime($result->date)) }}</label>


            </div>
        </div>
        <div class="col-xs-4 col-sm-2 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels  col-xs-4"><b>Reason</b></label>
                <label class="col-md-7 expense-labels col-xs-4 email-break">{{$result->no_attachment_reason ?? '--'}}</label>
            </div>
        </div>


        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels  col-xs-4"><b> Category</b></label>
                <label class="col-md-8 expense-labels col-xs-4">{{$result->expenseCategory->name ?? '--'}}
                </label>
            </div>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels col-xs-4"><b>Description</b></label>
                <label class="col-md-8 expense-labels  col-xs-4 email-break">
                    {{$result->description ?? '--'}}
                </label>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels  col-xs-4"><b>Subtotal</b></label>
                <label class="col-md-7 expense-labels col-xs-4 email-break">${{$result->amount - $result->tax_amount - $result->tip}}</label>
            </div>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                {{-- <label class="col-md-3 expense-labels  col-xs-4"><b>Project Number</b></label>
                <label class="col-md-7 expense-labels col-xs-4">{{$result->customer->project_number ?? '' }}</label> --}}
                <label class="col-md-5 expense-labels  col-xs-4"><b>Submitted Date</b></label>
                <label class="col-md-7 expense-labels col-xs-4">{{ date('M d, Y', strtotime($result->created_at)) }}</label>
            </div>
        </div>
        <div class="col-xs-4 col-sm-2 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels  col-xs-4"><b> Status</b></label>
                <label class="col-md-7 expense-labels col-xs-4">{{ $result->status->status ?? '--'}}
                </label>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels  col-xs-4"><b> GL Code</b></label>
                <label class="col-md-8 expense-labels col-xs-4">{{ $result->expenseGlCode->gl_code ?? '--'}}
                </label>
            </div>
        </div>

        <div class="col-xs-2 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-4 expense-labels col-xs-4"><b>Cost Center</b></label>
                <label class="col-md-8 expense-labels  col-xs-4 email-break">
                    {{$result->cost_center->center_number ?? '--'}}
                </label>
            </div>
        </div>

    </div>
</div>
@endif

@if($result->status_id !=1)
<div class="expense-card">
    <div class="row">
        <div class="col-xs-10 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels  col-xs-4 col-sm-7"><b>@if($result->status_id ==2) Rejected On @else Approved On @endif </b></label>
                <label class="col-md-5 expense-labels col-xs-4 col-sm-5">{{str_limit($result->updated_at , 10,'') ?? ''}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels  col-xs-4 col-sm-5"><b>@if($result->status_id ==2) Rejected By @else Approved By @endif </b></label>
                <label class="col-md-7 expense-labels col-xs-4 col-sm-7"> {{$result->approved_by_user->first_name.' '.$result->approved_by_user->last_name}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3   expense-labels  col-xs-4 col-sm-4"><b> Comments</b></label>
                <label class="col-md-7   expense-labels  col-xs-4 col-sm-7"> {{$result->approver_comments ?? '--'}}</label>
            </div>
        </div>

    </div>
</div>
@endif


@if($result->status_id ==5)
<div class="expense-card">
    <div class="row">
        <div class="col-xs-10 col-sm-2 col-md-2">
            <div class="row styled-form-readonly">
                <label class="col-md-5 expense-labels  col-xs-4 col-sm-7"><b>Reimbursed On</b></label>
                <label class="col-md-5 expense-labels col-xs-4 col-sm-5">{{str_limit($result->updated_at , 10,'') ?? ''}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels  col-xs-4 col-sm-5"><b>Reimbursed By </b></label>
                <label class="col-md-7 expense-labels col-xs-4 col-sm-7"> {{$result->finance_controller->first_name.' '.$result->finance_controller->last_name}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3   expense-labels  col-xs-4 col-sm-4"><b>Comments</b></label>
                <label class="col-md-7   expense-labels  col-xs-4 col-sm-7"> {{$result->finance_comments ?? '--'}}</label>
            </div>
        </div>

    </div>
</div>
@endif

{{ Form::open(array('url'=>'#','id'=>'expense-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
<input type="hidden" id="expense_id" name="expense_id" value="{{$expense_id}}">

@if($result->status_id!=5)
<div class="expense-card pdf-hide">

    <div class="row">
        <div class="col-xs-10 col-sm-10 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 expense-labels  col-xs-4" style="margin:6px -40px 0px 0px;"><b> GL Code</b> @if($finance_controller_status==1 && $result->status_id!=1 && $result->claim_reimbursement !=0)<span class="mandatory">*</span>@endif</label>
                <label class="col-md-8 expense-labels col-xs-4"> {{ Form::select('expense_gl_codes_id',[null=>'Please Select']+$gl_code, isset($result) ? old('expense_gl_codes_id',$result->expense_gl_codes_id) : null,array('class' => 'form-control col-md-12', 'id'=>'expense_gl_codes_id', 'required'=>'required')) }}
                    <small id="gl_codes_required" class="help-block"></small>
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3   expense-labels  col-xs-4" style="margin:6px -15px 0px 0px;"><b>Cost Center</b> @if($finance_controller_status==1 && $result->status_id!=1 && $result->claim_reimbursement !=0)<span class="mandatory">*</span>@endif</label>
                <label class="col-md-7   expense-labels  col-xs-4"> {{ Form::select('cost_center_id',[null=>'Please Select']+$cost_center, isset($result) ? old('cost_center_id',$result->cost_center_id) : null,array('class' => 'form-control col-md-12', 'id'=>'cost_center_id')) }}
                    <small id="cost_center_required" class="help-block"></small>
                </label>
            </div>
        </div>
        @if($finance_controller_status==1 && $result->status_id!=1 && $result->claim_reimbursement !=0)
        <div class="col-xs-10 col-sm-12 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-4   expense-labels  col-xs-4" style="margin:6px -24px 0px 0px;"><b>Payment Mode</b> @if($finance_controller_status==1 && $result->status_id!=1 && $result->claim_reimbursement !=0)<span class="mandatory">*</span>@endif</label>
                <label class="col-md-7   expense-labels  col-xs-4"> {{ Form::select('payment_mode_id',[null=>'Please Select']+$payment_mode, isset($result) ? old('payment_mode_id',$result->payment_mode_id) : null,array('class' => 'form-control col-md-12', 'id'=>'payment_mode_id','required'=>TRUE)) }}
                    <small id="payment_mode_required" class="help-block"></small>
                </label>
            </div>
        </div>

        <div class="col-xs-10 col-sm-10 col-md-5">
            <div class="row styled-form-readonly">
                <label class="col-md-2 expense-labels  col-xs-4" style="margin:6px -30px 0px 0px;"><b>Comments</b></label>
                <label class="col-md-8 expense-labels col-xs-4">
                    {{ Form::textArea('finance_comments',  null, array('class'=>'form-control', 'placeholder'=>'Enter Your Comments','rows'=>4 ,'id'=>'finance_comments')) }}
                </label>
            </div>
        </div>
        @else
        <div class="col-xs-10 col-sm-10 col-md-5">
            <div class="row styled-form-readonly">
                <label class="col-md-2 expense-labels  col-xs-4" style="margin:6px -30px 0px 0px;"><b>Comments</b></label>
                <label class="col-md-9 expense-labels col-xs-4">
                    {{ Form::textArea('approver_comments',  null, array('class'=>'form-control', 'placeholder'=>'Enter Your Comments','rows'=>4 ,'id'=>'approver_comments')) }}
                </label>
            </div>
        </div>
        @endif

    </div>
</div>
@endif

<br>
<div class="form-group row pdf-hide">
    <div class="col-sm-5"></div>
    <div class="col-sm-6">
        @if($finance_controller_status==1 && $result->status_id!=1 && $result->claim_reimbursement !=0)
        @if($result->status_id ==4)
        <input type="button" class="button btn blue btn-primary blue" id="reimburse" value="Reimburse" onclick="updateStatus(this.id);">
        @endif
        <input type="button" class="button btn blue btn-primary blue" id="cancel" value="Cancel" onclick="updateStatus(this.id);">
        @elseif($result->status_id !=5 && ($approver_status == 1 || auth()->user()->can('view_all_expense_claim')))
        @if($result->status_id !=3)
        <input type="button" class="button btn blue btn-primary blue" id="approve" value="Approve" onclick="updateStatus(this.id);">
        @endif
        @if($result->status_id !=2)
        <input type="button" class="button btn btn-primary blue" id="reject" value="Reject" onclick="updateStatus(this.id);">
        @endif
        <input type="button" class="button btn blue btn-primary blue" id="cancel" value="Cancel" onclick="updateStatus(this.id);">
        @else
        <input type="button" class="button btn blue btn-primary blue" id="cancel" value="Cancel" onclick="updateStatus(this.id);">

        @endif


    </div>
</div>
{{Form::close()}}

<div class="modal fade" id="modalContent" data-backdrop="static" tabindex="-1" role="dialog" style="overflow-y:auto;" aria-labelledby="myModalLabel" aria-hidden="true" data-focus-on="input:first">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div align="center" id="modal-img-content" style="height: 550px;" class="modal-body">
                <div style="text-align: center;">
                    <img style="left: 50%;max-width: 600px;" height="400px" id="ImgModalContainer" src="">
                </div>

            </div>

        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        var view_url = '{{ route("filedownload", [":id",":module",":attachment"]) }}';
        var img_id = {!!json_encode($result->attachment_id) !!};
        view_url = view_url.replace(':id', img_id);
        view_url = view_url.replace(':module', 'Expense_Claim');
        view_url = view_url.replace(':attachment', false);
        var contentType='';
        var xhttp = new XMLHttpRequest();
        xhttp.open('HEAD', view_url);
        xhttp.onreadystatechange = function () {
            if (this.readyState == this.DONE) {
                contentType = this.getResponseHeader("Content-Type");
                if(contentType === 'application/pdf'){            
                 $('#pdfLink').attr('href', view_url);
                $('#pdfLink img').css({'max-width':'150px','margin-top':'130px','margin-left':'100px'});
                 view_url = "{{asset('images/pdf-image.jpg') }}"            
                 } else {
                 $('#pdfLink').attr('href', '#');
                 $('#pdfLink img').css({'max-width':'400px','margin-top':'20px','margin-left':'20px'});
                 }
                $('#ImgContainer').attr('src', view_url);
            }

        };
        xhttp.send();
    });


    //$('#modalContent').modal('show');

    function updateStatus(action) {

        if (action == 'approve') {
            expense_status = 3;
        } else if (action == 'reject') {
            expense_status = 2;
        } else if (action == 'reimburse') {
            expense_status = 5;
        }

        var validate = true;
        if (($('#cost_center_id').val() == '') || ($('#expense_gl_codes_id').val() == '') || ($('#payment_mode_id').val() == '')) {
            validate = false;
        }

        if ((action == 'reimburse' || action == 'approve') && (validate == false)) {
            if ($('#cost_center_id').val() == '') {
                $('#cost_center_required').text('Cost Center is required');
            } else {
                $('#cost_center_required').text('');
            }

            if ($('#expense_gl_codes_id').val() == '') {
                $('#gl_codes_required').text('GL code is required');
            } else {
                $('#gl_codes_required').text('');
            }

            if ($('#payment_mode_id').val() == '') {
                $('#payment_mode_required').text('Payment mode is required');
            } else {
                $('#payment_mode_required').text('');
            }
        } else if ((action == 'reject') && ($('#approver_comments').val() == '')) {
            swal({
                title: 'Warning',
                text: 'Please enter the comments',
                type: "warning",
                button: "OK",
            });
        } else {

            if (action != 'cancel') {
                var data = {
                    status_id: expense_status,
                    expense_id: $('#expense_id').val(),
                    gl_code_id: $('#expense_gl_codes_id').val(),
                    cost_center_id: $('#cost_center_id').val(),
                    finance_comments: $('#finance_comments').val(),
                    approver_comments: $('#approver_comments').val(),
                    mode_of_payment_id: $('#payment_mode_id').val()

                };

                swal({
                        title: "Are you sure?",
                        text: "You want to " + action + " this request?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        showLoaderOnConfirm: true,
                        closeOnConfirm: false
                    },
                    function() {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{route('expense-claims.updateExpense')}}",
                            type: 'POST',
                            data: data,
                            success: function(data) {
                                if (data.success) {
                                    swal({
                                        title: 'Success',
                                        text: 'Expense claim has been updated',
                                        type: 'success',
                                        button: "OK",
                                    }, function() {
                                        redirect_url = "{{ route('expense-dashboard.index') }}";
                                        window.location = redirect_url;
                                    });

                                } else {
                                    swal("Warning", "This request cannot be approved", "warning");
                                }
                            },
                            error: function(xhr, textStatus, thrownError) {
                                console.log(xhr.status);
                                console.log(thrownError);
                            },
                        });
                    });

            } else {
                window.location = "{{ route('expense-dashboard.index') }}";
            }
        }
    }
</script>
@endsection
@section('css')
<style>
    @media print {
        @page {
            size: landscape
        }

        #sidebar {
            display: none;
        }
    }
</style>
@endsection
