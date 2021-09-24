@extends('layouts.app')
@section('content')

    <div class="container-fluid" style="padding-right:0px !important;padding-left:0px !important ">

        <div class="row">
            <div class="col-md-12"><div class="table_title position-static">
                <h4>Client Schedule</h4>
            </div></div>

        </div>
        <div class="container-fluid" style="padding-right:0px !important ">
         <div class="row beforeprojectselect filterbox" style="display: none;margin-top: 15px;">
            <div class="col-md-4" style="display: inline-block !important;vertical-align:top">
                <select id="project" placeholder="Select a Project" >

                    <option value=""></option>

                    @foreach ($customers as $value)
                        <option value="{{$value["id"]}}">{{$value["project_number"]}}-{{$value["client_name"]}}</option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-1" style="display: inline-block !important;vertical-align:top">Pay Period :</div>
            <div class="col-md-4" style="height:120px;overflow-y:auto;display: inline-block !important;vertical-align:top">

                <select id="payperiod" multiple="multiple">
                    <option value=""></option>
                    @foreach ($payperiods as $payperiod)
                        <option value="{{$payperiod->id}}">{{$payperiod->pay_period_name}} ({{$payperiod->short_name}})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1" style="display: inline-block !important;vertical-align:top">
                <a class="addblock customershifts btn btn-primary" >Initiate Schedule</a>

                <input type="hidden" name="hiddenemployeeid" id="hiddenemployeeid" />
                <input type="hidden" name="avghoursperweek" id="avghoursperweek" />
                <input type="hidden" name="hiddenscheduledate" id="hiddenscheduledate" />
                <input type="hidden" name="hiddenpayperiodid" id="hiddenpayperiodid" />
                <input type="hidden" name="hiddenschedules" id="hiddenschedules" />
                <input type="hidden" name="hiddenrejectedpayperiodid" id="hiddenrejectedpayperiodid" value="{{$rejectedpayperiods}}" />
                <input type="hidden" name="hiddenrejectedcustomer" id="hiddenrejectedcustomer" value="{{$rejectedcustomerid}}" />
                <input type="hidden" name="initialrequirementid" id="initialrequirementid" value="{{$initialrequirementid}}" />
                <input type="hidden" name="hiddencustomershifts" id="hiddencustomershifts" value="" />
                <input type="hidden" name="variance" id="variance" value="0" />
                <input type="hidden" name="scheduleindicator" id="scheduleindicator" value="0" />
                <input type="hidden" name="updatePastScheduleAllowed" id="updatePastScheduleAllowed" value="{{$updatePastScheduleAllowed}}" />
            </div>

        </div>
        <div class="row" style="display: none">
            <div class="col-md-5"></div>
            <div class="col-md-1 ">
                <div class="loader"></div>
            </div>
        </div>
        <div class="row afterprojectselect postprojectarea" style="background: oldlace;border-radius:5px">
            <div class="col-md-4" style="display: inline-block !important;vertical-align:top !important">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">&nbsp;</div>
                    </div>
                <div class="row">
                    <div class="col-md-5" style="font-weight:bold;display:inline-block !important">Project</div>
                    <div class="col-md-7 projectbox" style="display:inline-block !important">Project Text Box</div>
                </div>
                    <div class="row" style="margin-top: 20px;">
                    <div class="col-md-5" style="font-weight:bold;display:inline-block !important">Pay Period</div>
                    <div class="col-md-7 payperiodbox" style="display:inline-block !important;max-height: 100px;
                    overflow-y: auto;">Pay Period Text Box</div>
                </div>
                </div>
            </div>
            <div class="col-md-7 summarybox" style="display: inline-block !important;vertical-align:top !important">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">&nbsp;</div>
                    </div>
                    <div class="row" style="border-bottom:solid 1px rgb(0, 58, 99);font-weight:bold">
                        <div class="col-md-12" >
                            <span style="margin-left:-16px">Summary</span>
                        </div>
                    </div>
                    <div class="row" >
                        <div class="col-md-1 squareblockcap" style="display:inline-block">Contract Hours per Week</div>
                        <div class="col-md-2 " style="display:inline-block">
                            <div class="squareblock contracthours"></div>
                        </div>
                        <div class="col-md-1 squareblockcap" style="display:inline-block">Average Hours per Week</div>
                        <div class="col-md-2 " style="display:inline-block"><div class="squareblock avghoursperweek"></div></div>
                        <div class="col-md-1 squareblockcap" style="display:inline-block">Variance From Contract</div>
                        <div class="col-md-2 " style="display:inline-block"><div class="squareblock varianceblock"></div></div>
                        <div class="col-md-1 squareblockcap" style="display:inline-block;vertical-align:middle">Schedule Indicator</div>
                        <div class="col-md-2 " style="display:inline-block"><div class="squareblock schedindicator"></div></div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row afterprojectselect w-100" id="mainblock" style="overflow-x:hidden;overflow-y:scroll;">

        <div  id="schedules" class="schedules col-md-12" style="">

        </div>


    </div>
    <div class="row afterprojectselect">
      <div class="sparearea  col-md-12" id="sparearea">
        <table id="example" class="display" style="width:95%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Training</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Email</th>
                    <th>Cell Number</th>
                    <th>Security Clearance</th>
                    <th>Date Of Hire</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($spares as $sparesarray)
                    <tr>
                    <td>{{$sparesarray->getNameWithEmpNoAttribute()}}</td>
                    <td id="{{$sparesarray->id}}-training">
                        @php
                            if($sparesarray->employee_training){
                                $i=0;
                                foreach ($sparesarray->employee_training as $training) {
                                    if($i>0){
                                        echo("|<br/>");
                                    }
                                    echo($training->course_content->content_title);
                                    if($training->completed_date!=null){
                                        $completed_date = date("d M, Y",strtotime($training->completed_date));
                                        echo " (".$completed_date.")";
                                    }
                                    $i++;
                                }
                            }
                        @endphp

                    </td>
                    <td>{{$sparesarray->roles[0]->name}}</td>
                    <td>{{$sparesarray->employee_profile->employee_city}}</td>
                    <td>{{$sparesarray->employee_profile->employee_work_email}}</td>
                    <td>{{$sparesarray->employee_profile->cell_no}}</td>
                    <td>
                        @foreach ($sparesarray->securityClearanceUser as $securityclearance)
                            {{$securityclearance->securityClearanceLookups->security_clearance}}
                            @if ($securityclearance->valid_until!=null)
                                ({{date("d M, Y",strtotime($securityclearance->valid_until))}})<br/>
                            @endif


                        @endforeach
                    </td>
                    <td>{{date("d M, Y",strtotime($sparesarray->employee_profile->employee_doj))}}</td>


                    <td>
                        <span attr-id={{$sparesarray->id}}
                            attr-firstname={{$sparesarray->first_name}}
                            attr-lastname={{$sparesarray->last_name}} class="addsparesblock btn btn-primary">Add</span>
                    </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <button class="btn btn btn-danger cancelspares" >Done</button>
      </div>
    </div>

    <div class="row afterprojectselect filter-text" style="
    padding-top: 15px;
    padding-bottom: 15px;
    width: 98%;
    margin-left: 1%;border-radius:5px;display: none">

        <div class="col-md-9 "  style="padding-left:0px !important">
            <a id="addspares" class="addspares btn btn-primary" >Add Spares</a>
        </div>
        <div class="col-md-3" style="text-align: right">
            <a class="confirmschedule btn btn-primary" >Save</a>
            @if(empty($initialrequirementid))
            <a class="canceschedule btn btn-primary" >Cancel</a>
            @endif
            <a class="resetschedule btn btn-primary" >Reset</a>
        </div>

    </div>
    </div>

<div class="modal fade"
    id="supervisorModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Notes</h4>
                <button type="button" class="supervisorclose" data-dismiss="modal" aria-label="Close" style="float: right">
                    <span aria-hidden="true">×</span>
                </button>

            </div>
            {{ Form::open(array('url'=>'#','id'=>'supervisor-form','class'=>'form-horizontal', 'method'=> 'GET')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">

                <div class="form-group" >

                    <div class="col-sm-9">
                        <textarea class="form-control"  id="supervisornotes" rows="4" cols="50"></textarea>

                        <small class="help-block"></small>
                    </div>
                </div>



            </div>
            <div class="modal-footer">

                <a class="saveschedule btn btn-primary" >Submit</a>
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue cancelmodal','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>
  <div class="modal fade"
    id="myModal"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="myModalLabel"
    aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Schedule</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'category-form','class'=>'form-horizontal', 'method'=> 'GET')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="starttime" class="col-sm-3 control-label">Start Time</label>
                    <div class="col-sm-9">

                        {{ Form::text('starttime',null,array('class'=>'form-control timepicker','id'=>'starttime')) }}
                        <small class="help-block"></small>
                        <input type="hidden" name="editflag" id="editflag" value="0" />
                    </div>
                </div>
                <div class="form-group" id="name">
                    <label for="endtime" class="col-sm-3 control-label">End Time</label>
                    <div class="col-sm-9">
                        {{ Form::text('endtime',null,array('class'=>'form-control timepicker','id'=>'endtime')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="tags">

                </div>

            </div>
            <div class="modal-footer">


                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue cancelmodal','data-dismiss'=>'modal'))}}
                <button type="submit" class="button btn btn-danger white"  id="mdl_remove_cart">Remove</button>
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>

@stop
@section('scripts')
    @include('employeescheduling::partials.schedulecreatescripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


@endsection
@section('css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css">

    <style>

        .weekday{
            background:rgb(244, 132, 82);
            color: #fff;
        }
        .weekend{
            background:rgb(79, 66, 181);
            color: #fff;
        }
        .saveschedule{
            display: none;
        }

        .emptyallocation{
            width:40px;
            background: #003A63;
            border-radius: 20px;
            margin-left: 35%;
            cursor: pointer;
        }

        .allocatedblock{
            width:40px;
            background: #f26321;
            border-radius: 20px;
            margin-left: 35%;
            cursor: pointer;
        }
        .afterprojectselect{
            text-align: left;

        }



        /* Input field */
.select2-selection__rendered {
    color: #fff;
 }

/* Around the search field */



.cells{
    padding:10px
}
/* Selected option */
.select2-results__option[aria-selected=true] { color: #fff; }
.schedulesrow{
    background:#fff;
    width: 100%;
}
.schedules{
    padding: 0 !important;
}

.table-wrapper-scroll-y {
display: block;
}
.sparearea{
    padding-left: 20px ;
    padding-top: 20px ;
    display: none;
    padding-bottom: 20px ;
}
.squareblock{
    border:solid 1px #000;
    text-align: center;
    margin-top: 10px;
    margin-left: 10px;
    height: 80%;
    font-size: 12px;
    width: 90%;
}
.stickycol{
    border-right: solid 1px #DDEFEF;
    left: auto;
    position: absolute;
    top: auto;
}
.squareblock{
    padding-top: 15px;
}
.squareblockcap{
    text-align: center;
    padding-top: 10px;
    font-size: 12px;
}

.contentdiv{
    overflow-x: hidden;
}

body{
    overflow-x:hidden !important;
}

.sparespool{
    background:#F2351F;
    color: #fff;
}

.redbg{
    background: #F2351F;
    color:#fff;
}
.greenbg{
    background: #343F4E;
    color:#fff;
}
.image-wrapper {
    position: absolute;
    overflow-y:auto;
    background: #003A63;
    border-radius: 5px;
    min-width: 200px;
    max-width: 900px;
    max-height: 100px;
    max-height: 200px;
    padding: 5px;
    padding-right: 25px;
    color: #fff;
    transform: translate(-50%, -50%);
    opacity: 1;
    font-size: 13px;
    line-height: 16px;
    z-index: 2;
}



  .postprojectarea{
      display: none
  }

  .loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

    </style>
@endsection
