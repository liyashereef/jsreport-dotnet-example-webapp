<div class="container-fluid" id="contractamendments" style="padding:0">
    <div class="form-group row">
            <div class="col-sm-4 candidate-screen-head">Description</div>
            <div class="col-sm-4 candidate-screen-head">Attachment</div>
            <div class="col-sm-4 candidate-screen-head">Created By</div>
    </div>
    @php
$i=0;
@endphp
@foreach ($contractamendments as $amendments)
@php
$i++;
@endphp
    <div class="form-group row">
        <div class="col-sm-4 ">
            {{$amendments->amendment_description}}
        </div>
        <div class="col-sm-4 ">
        @if($amendments->amendment_attachment_id>0)
        <a
        href="{{route("contracts.downloadcontractattachment",[
                "contract_id"=>$contractid,"file_id"=>$amendments->amendment_attachment_id,"date"=>date("Y-m-d",strtotime($amendments->created_at)),"filetype"=>"amendment"
        ])}}"
         target="_blank" ><i class="fa fa-download" aria-hidden="true"></i></a>
        @else
        No attachment
        @endif

        </div>
        <div class="col-sm-4 ">
                {{$amendments->getCreateduser->first_name}} {{$amendments->getCreateduser->last_name}}
        </div>
    </div>
@endforeach



</div>
