@extends('layouts.app')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
@section('css')
    <style>
        .chartview{
            display:none
        }

        #originalnotes{
            display: none;
        }


        .originalnotes{
            display: none;
        }
        span.select2-container {
            z-index:10050;
        }
        .select2-search__field
        {
            width: 200px !important;
        }
        .active{
            background: #003A63 !important;
            color: #fff;
        }
        i{
            cursor: pointer;
        }
        .select2-selection__choice {
    color: #fff;
}

.labelclass{
    font-weight: bold
}

#readmorenotes{
    cursor: pointer
}


.readmorenotes{
    cursor: pointer
}
    </style>
    <style>

        .profileImage {
            width: 16rem;
            height: 16rem;
            border-radius: 50%;
            font-size: 2.5rem;
            color: #fff;
            text-align: center;
            line-height: 16rem;
            margin: 3rem 0;
        }
    
        .user-image-div {
            text-align: left;
            padding-left: 0px !important;
        }
        #candidate-data-left-panel li:last-child {
            margin-bottom: 17px;
        }
        .sidebar-nav{
            width: 99%;
        }
        .clip-td{
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        .ssd-table td {
            padding: 0.45rem 0.20rem;
            border: 1px solid #003A63;
            text-align: left;
            vertical-align: middle;
        }
        table tr:first-child td {
            border-top: 1px solid #E2E2E7;
        }
        table tr td:first-child {
            border-left: 1px solid #E2E2E7;
        }
        table tr:last-child td {
            border-bottom: 1px solid #E2E2E7;
        }
        table tr td:last-child {
            border-right: 1px solid #E2E2E7;
        }
        .ssd-text{
            display: block;
            font-weight: bold;
            color: #00395c;
            text-indent: 0;
            line-height: 20px;
            font-size: 13px;
        }
        .ssd-text:hover{
            background: rgba(255, 255, 255, 0.2);
            color: #F48452;
            text-decoration: none;
            text-indent: 0;
        }
        .pts-txt{
            font-size: 13px;
        }
        .filter_checkbox {
            vertical-align: middle;
        }
        input.largerCheckbox{
            margin-top: 5px;
            width: 18px !important;
            height: 17px !important;
        }
        .ssd-table{
            border-collapse: collapse;
        }
        .ssd-cb{
            color: white !important;
        }
        .padding-top-filter
        {
            padding-top: 10px;
        }
    </style>
@endsection
@section('content')
<div class="container_fluid">

    <div class="row " style="padding-left:13px">
        <div class=" col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 candidate-screen-head document-screen-head ">
            Employee Feedback
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="container_fluid" style="padding:0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1 mb-2 labelclass">
                                Customer
                            </div>
                            <div class="col-md-4 ">
                                {{$employeeFeedback->customer->project_number}}-{{$employeeFeedback->customer->client_name}}
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-1 mb-2 labelclass">
                                Address To
                            </div>
                            <div class="col-md-1">
                                {{$employeeFeedback->department->name}}
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-1 mb-2 labelclass" >
                                Created By
                            </div>
                            <div class="col-md-2">
                                {{$employeeFeedback->create_user->getFullNameAttribute()}}
                            </div>
                            <div class="col-md-1 mb-2 labelclass" style="text-align: right">
                                Updated By
                            </div>
                            <div class="col-md-1">
                                {{isset($employeeFeedback->update_user)?$employeeFeedback->update_user->getFullNameAttribute():"--"}}
                            </div>
                            <div class="col-md-1 mb-2 labelclass" style="text-align: right">
                                Status
                            </div>
                            <div class="col-md-1">
                                {{$employeeFeedback->userstatus->name}}
                            </div>
                        </div>
                    
                        <div class="row mb-2">
                            <div class="col-md-1 mb-2 labelclass">
                                Subject
                            </div>
                            <div class="col-md-4">
                                {{ucfirst($employeeFeedback->subject)}}
                            </div>
                            <div class="col-md-1 mb-2 labelclass" style="text-align: right">
                                Notes
                            </div>
                            <div class="col-md-6">
                                <span id="ellipsisnotes">
                                    
                                    @if (strlen($employeeFeedback->message)>75)
                                        {{substr(ucfirst($employeeFeedback->message),0,75)}}... <span id="readmorenotes" class="fa fa-angle-double-down ">&nbsp;</span>
                                    @else
                                        {{ucfirst($employeeFeedback->message)}}
                                    @endif
                                    
                                </span>
                                <span id="originalnotes">{{ucfirst($employeeFeedback->message)}}</span>
                            </div>
                            
                        </div>                        
                        <div class="row">
                     
                            
                            
                        </div>
                    

                        <div class="row " style="padding-left:13px !important ">
                            <div class="col-md-12 mb-2 document-list-label labelclass" >STATUS UPDATE</div>
                        </div>   
                        <div class="row mt-2 mb-2" style="font-weight: bold; @if ($employeeFeedback->approvalfeedback->count()===0)
                            display:none                            
                        @endif
                        ">
                            <div class="col-md-6 "  >Notes</div>
                            <div class="col-md-2 "  >Status</div>
                            <div class="col-md-2 "  >Updated By</div>
                            <div class="col-md-2 "  >Updated At</div>
                        </div>
       
                        @foreach ($employeeFeedback->approvalfeedback as $item)

                        <div class="row mt-2 mb-2" >
                            <div class="col-md-6 " @if ($loop->iteration==1)
                                {{-- style="font-weight:bold" --}}
                            @endif >
                            <span id="ellipsisnotes-{{$item->id}}" class="ellipsisnotes">
                                    
                                @if (strlen(ucfirst($item->notes))>75)
                                    {{substr(ucfirst($item->notes),0,75)}}... 
                                    <span attr-id="{{$item->id}}"
                                          class="fa fa-angle-double-down readmorenotes">&nbsp;</span>
                                @else
                                    {{ucfirst($item->notes)}}
                                @endif
                                
                            </span>
                            <span attr-id="{{$item->id}}" id="originalnotes-{{$item->id}}" class="originalnotes">
                                {{ucfirst($item->notes)}}</span>

                                
                            
                                
                                    
                            </div>
                            <div class="col-md-2 " @if ($loop->iteration==1)
                                {{-- style="font-weight:bold" --}}
                            @endif>
                                {{isset($item->userstatus)?$item->userstatus->name:""}}<br/>
                                
                            </div>
                            <div class="col-md-2 " @if ($loop->iteration==1)
                                {{-- style="font-weight:bold" --}}
                            @endif>
                                
                                {{$item->create_user->getFullNameAttribute()}}<br/>
                                
                            </div>
                            <div class="col-md-2 " @if ($loop->iteration==1)
                                {{-- style="font-weight:bold" --}}
                            @endif>
                                
                                {{$item->created_at->format("d M Y h:i a")}}<br/>
                                
                            </div>
                        </div>
                        @endforeach
                        <div class="row" style="padding-top: 20px">
                            <div class="col-md-1 mb-4 labelclass">
                                Status
                            </div>
                            <div class="col-md-2">
                                <select name="status" id="status"
                                required class="form-control">
                                    <option value="">Select Any</option>
                                    @foreach ($statusLookup as $statusFlag)
                                        <option value="{{$statusFlag->id}}">{{$statusFlag->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-4 labelclass">
                                Notes
                            </div>
                            <div class="col-md-8">
                                <textarea name="approverNotes" id="approverNotes"  rows="3" class="form-control"></textarea>
                            </div>
                            
                        </div>
                        <div class="row" style="padding-top: 10px">
                            <div class="col-md-1 mb-2 "></div>
                                <div class="col-md-2 mb-2 ">
                                    <button class="btn btn-primary form-control saveapproval" >Save</button>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    

    
    <div class="row" style="padding-top: 10px">
        <div class="col-md-4 mb-2 "></div>

    </div>
    {{-- <div class="row" >
        <div class="col-md-12 mb-12 ">
            <div id="map" style="min-height:550px;" class="embed-responsive-item" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;L o a d i n g . . . . . . </div>

        </div>
    </div> --}}

</div>
   <form id="filtertableform" method="post">
<div class="row tableview" style="padding-bottom:0px !important;text-align: right;align-items: center;display: none" >
        <div class="col-md-5"></div>
        <div class="col-md-2 mb-2 labelclass">
            Start Date
        </div>
        <div class="col-md-2 mb-2 labelclass">
            <input type="text" name="tablestartdate" id="tablestartdate"  class="form-control datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' -365 days'))}}" />
        </div>
        <div class="col-md-1  col-lg-1" style="margin-left:-10px;">
                End Date
            </div>
            <div class="col-md-2 mb-2 labelclass">
                <input type="text" name="tableenddate" id="tableenddate"  class="form-control datepicker" value="{{date('Y-m-d', strtotime(date("Y-m-d"). ' +1 days'))}}" />
            </div>
        <div class="col-md-1 col-lg-1">
            <button class="btn btn-primary filtertable" id="filterbutton" name="filterbutton" type="button" >Search</button>
        </div>
        <div class="col-md-3" style="padding-right: 0px !important;">


        </div>
    </div>
</form>




    


</div>




@stop

@section('scripts')
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ config('globals.google_api_key') }}"></script>

    <script>
    $(document).on("click",".saveapproval",function(e){
            e.preventDefault();
            let id= {!! json_encode($id) !!};
            let status= $("#status").val();
            let notes= $("#approverNotes").val();
            if(status==""){
                swal("Warning","Please choose any status","warning")
            }else if(notes==""){
                swal("Warning","Notes cannot be empty","warning")
            }else{
                $.ajax({
                    type: "post",
                    url: "{{route("employee.savefeedbackapproval")}}",
                    data: {id:id,status:status,notes:notes},
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        var jqdata = jQuery.parseJSON(response);
                        let title="";
                        let text="";
                        let type="";
                        if(jqdata.code==200){
                            title="Success ";
                            text=jqdata.message;
                            type="success";
                        }else{
                            title="Warning ";
                            text=jqdata.message;
                            type="warning";
                        }

                        swal({
                            title: title,
                            text: text,
                            type: type,
                            showCancelButton: false,
                            confirmButtonColor: '#DD6B55',
                            confirmButtonText: 'Ok',
                            cancelButtonText: "No, cancel it",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function(isConfirm) {
                            history.go(-1)
                        });

                    }
                });
            }
        })

        $(document).on("click","#readmorenotes",function(e){
            $(this).hide();
            $("#ellipsisnotes").hide();
            $("#originalnotes").show();
        })


        $(document).on("click",".readmorenotes",function(e){
            let id=$(this).attr("attr-id");
            $(this).hide();
            $("#ellipsisnotes-"+id).hide();
            $("#originalnotes-"+id).show();
        })
    </script>

@endsection
