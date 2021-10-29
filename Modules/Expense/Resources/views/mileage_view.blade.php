@extends('layouts.app') 
@section('content')

<div style="margin-bottom:65px;" class="table_title">
    <h4 style="float:left;">Mileage Claim for {{$result->created_user->first_name.' '.$result->created_user->last_name}} on {{ date('M d, Y', strtotime($result->date)) }}</h4>
      <div style="float:right;margin-top: 15px;" class="btn submit pdf-hide" onClick="window.print();" >
                <a href="javascript:;"> Print </a>
    </div>
</div>


    
<div class="expense-card">
    <div class="row">
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Transaction Date</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -25px;">{{ date('M d, Y', strtotime($result->date)) }}</label>

            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4" ><b>Submitted By</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -30px;">{{$result->created_user->first_name.' '.$result->created_user->last_name.' ( '.$result->created_user->employee->employee_no.' )'}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-sm-4 col-form-label col-xs-4"><b>Description</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4 email-break">
                {{$result->description ?? '--'}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-4 col-sm-5 col-form-label col-xs-4"><b>Billable</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4" style="margin-left: -30px;">@if($result->billable == 1) Yes @else No @endif</label>
            </div>
        </div>


    </div>
    <div class="row">
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Starting Location</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -25px;"> {{$result->starting_location?? '--'}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Destination</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -30px;">{{$result->destination ?? '--'}}</label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-sm-4 col-form-label col-xs-4"><b>Status</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4"> {{$result->status->status ?? '--'}}</label>
            </div>
        </div>

        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-4 col-sm-5 col-form-label col-xs-4"><b>Rate</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4" style="margin-left: -30px;">{{$result->rate ?? ''}} </label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b> Starting Odometer</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -25px;">{{$result->starting_km ?? ''}} 
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Ending Odometer</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4 email-break" style="margin-left: -30px;">{{$result->ending_km ?? ''}}</label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-sm-4 col-form-label col-xs-4"><b>Total km</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4 email-break">
                {{$result->total_km ?? ''}}
                </label>
            </div>
        </div>

        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-4 col-sm-5 col-form-label col-xs-4"><b>Total Amount</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4" style="margin-left: -30px;"> ${{$result->amount ?? '0'}}</label>
            </div>
        </div> 
       
       
    </div>
    <div class="row">

        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Project Name</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -25px;"> {{$result->customer->client_name ?? ''}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Project Number</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -30px;">{{$result->customer->project_number ?? ''}}</label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-sm-4 col-form-label  col-xs-4"><b>Claim Reimburse</b></label>
                <label class="col-md-7 col-sm-7 col-form-label col-xs-4 email-break">@if($result->claim_reimbursement == 1) Yes @else No @endif</label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-4 col-sm-5  col-form-label col-xs-4"><b>Vehicle Type</b></label>
                <label class="col-md-7 col-sm-7 col-form-label  col-xs-4" style="margin-left: -30px;">@if($result->vehicle_type == 1) Company <br>({{$result->vehicle->make ?? ''}}-{{$result->vehicle->number ?? ''}}) @else Private @endif</label>
            </div>
        </div>
    
        </div>
</div>
@if($result->status_id !=1)  
<div class="expense-card">
    <div class="row">
    <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>@if($result->status_id ==2) Rejected On @else Approved On @endif </b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -25px;">{{\Illuminate\Support\Str::limit($result->updated_at , 10,'') ?? ''}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b> @if($result->status_id ==2) Rejected By @else Approved By @endif</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -30px;"> {{$result->approved_by_user->first_name.' '.$result->approved_by_user->last_name}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3  col-sm-3 col-form-label  col-xs-4"><b>Comments</b></label>
                <label class="col-md-8 col-sm-8  col-form-label  col-xs-4">  {{$result->approver_comments ?? '--'}}</label>
            </div>
        </div>

    </div>
</div>
@endif

@if($result->status_id ==5)     
<div class="expense-card">
    <div class="row">
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Reimbursed On</b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -25px;">{{str_limit($result->updated_at , 10,'') ?? ''}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-sm-6 col-form-label  col-xs-4"><b>Reimbursed By </b></label>
                <label class="col-md-7 col-sm-6 col-form-label col-xs-4" style="margin-left: -30px;"> {{$result->finance_controller->first_name.' '.$result->finance_controller->last_name}}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-3 col-md-3">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-sm-3   col-form-label  col-xs-4"><b>Comments</b></label>
                <label class="col-md-8 col-sm-8  col-form-label  col-xs-4">  {{$result->finance_comments ?? '--'}}</label>
            </div>
        </div>

    </div>
</div>
@endif

 {{ Form::open(array('url'=>'#','id'=>'expense-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
 <input type="hidden" id="mileage_id" name="mileage_id" value="{{$mileage_id}}">
 @if($result->status_id!=5)
<div class="expense-card pdf-hide">
 
      <div class="row">
      {{--  <div class="col-xs-10 col-sm-10 col-md-4">
            <div class="row styled-form-readonly">
                <label class="col-md-5 col-form-label  col-xs-4"><b> GL Code</b></label>
                <label class="col-md-7 col-form-label col-xs-4"> {{ Form::select('expense_gl_codes_id',[null=>'Please Select']+$gl_code, isset($result) ? old('expense_gl_codes_id',$result->expense_gl_codes_id) : null,array('class' => 'form-control col-md-8', 'id'=>'expense_gl_codes_id')) }}
                </label>
            </div>
        </div>
        <div class="col-xs-10 col-sm-12 col-md-4">
            <div class="row styled-form-readonly">
                <label class="col-md-5   col-form-label  col-xs-4"><b>Cost Center</b></label>
                <label class="col-md-7   col-form-label  col-xs-4">  {{ Form::select('cost_center_id',[null=>'Please Select']+$cost_center, isset($result) ? old('cost_center_id',$result->cost_center_id) : null,array('class' => 'form-control col-md-8', 'id'=>'cost_center_id')) }}</label>
            </div>
        </div>
        @if($finance_controller_status==1)
        <div class="col-xs-10 col-sm-12 col-md-4">
            <div class="row styled-form-readonly">
                <label class="col-md-5   col-form-label  col-xs-4"><b>Payment Mode</b></label>
                <label class="col-md-7   col-form-label  col-xs-4">   {{ Form::select('payment_mode_id',[null=>'Please Select']+$payment_mode, isset($result) ? old('payment_mode_id',$result->payment_mode_id) : null,array('class' => 'form-control col-md-8', 'id'=>'payment_mode_id')) }}</label>
            </div>
        </div>
        @endif --}}
        @if($finance_controller_status==1) 
        <div class="col-xs-10 col-sm-10 col-md-4">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-form-label  col-xs-4"><b>Comments</b></label>
                <label class="col-md-8 col-form-label col-xs-4"> 
                {{ Form::textArea('finance_comments', null, array('class'=>'form-control', 'placeholder'=>'Enter Your Comments','rows'=>4 ,'id'=>'finance_comments')) }}    
                </label>
            </div>
        </div>
        @else
        <div class="col-xs-10 col-sm-10 col-md-4">
            <div class="row styled-form-readonly">
                <label class="col-md-3 col-form-label  col-xs-4"><b>Comments</b></label>
                <label class="col-md-8 col-form-label col-xs-4"> 
                {{ Form::textArea('approver_comments', null, array('class'=>'form-control', 'placeholder'=>'Enter Your Comments','rows'=>4 ,'id'=>'approver_comments')) }}    
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
        @elseif($result->status_id !=5 && ($approver_status == 1 || auth()->user()->can('view_all_mileage_claim')))
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


@endsection
@section('scripts')
<script>
    function updateStatus(action){

if(action=='approve'){
   expense_status = 3;
}else if(action=='reject'){
   expense_status = 2;
}else if(action=='reimburse'){
   expense_status = 5;
}

 if((action=='reject') && ($('#approver_comments').val() == '')){
    swal({
                        title: 'Warning',
                        text: 'Please enter the comments',
                        type: "warning",
                        button: "OK",
        });
}else{

if(action!='cancel'){
     var data = {
       status_id : expense_status,
       mileage_id : $('#mileage_id').val(),
       finance_comments : $('#finance_comments').val(),
       approver_comments : $('#approver_comments').val()   
      };

 swal({
                title: "Are you sure?",
                text: "You want to "+action+" this request?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
       headers: {
           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       url: "{{route('mileage-claims.updateMileageClaim')}}",
       type: 'POST',
       data: data,
       success: function (data) {
           if (data.success) {
            swal({
                        title: 'Success',
                        text: 'Mileage claim has been updated',
                        type: "success",
                        button: "OK",
                    }, function () {
                        redirect_url = "{{ route('mileage-dashboard.index') }}";
                        window.location = redirect_url;
                    });
               
           } else {
            swal("Warning", "This request cannot be approved", "warning");
           }
       },
       error: function (xhr, textStatus, thrownError) {
           console.log(xhr.status);
           console.log(thrownError);
       },
        });
     });


 
}else{
    window.location = "{{ route('mileage-dashboard.index') }}";
}
}
}
</script>
@endsection
@section('css')
    <style>
@media print{@page {size: landscape}
#sidebar{
    display: none;
}
}
    </style>
@endsection
