@php

                foreach ($payperiod as $payperiods) {
                    try {
                        echo '<input class="payperiodsweekcontrol week1" week="1" attr-payperiod="'.$payperiods.'" type="hidden" name="week1-payp'.$payperiods.'" id="week1-payp'.$payperiods.'" value="'.$payperiodsumarray[$payperiods."-1"].'" />';
                    } catch (\Throwable $th) {
                        echo '<input class="payperiodsweekcontrol week1" week="1" attr-payperiod="'.$payperiods.'" type="hidden" name="week1-payp'.$payperiods.'" id="week1-payp'.$payperiods.'" value="0" />';
                    }
                    try {
                        echo '<input class="payperiodsweekcontrol week2" week="2" attr-payperiod="'.$payperiods.'" type="hidden" name="week2-payp'.$payperiods.'" id="week2-payp'.$payperiods.'" value="'.$payperiodsumarray[$payperiods."-2"].'" />';
                    } catch (\Throwable $th) {
                        echo '<input class="payperiodsweekcontrol week2" week="2" attr-payperiod="'.$payperiods.'" type="hidden" name="week2-payp'.$payperiods.'" id="week2-payp'.$payperiods.'" value="0" />';
                    }
                }


            @endphp
            <input type="hidden" name="contracthours" id="contracthours" value="{{$inpcontracthours}}" />

<div id="tooltip" style="position: fixed;display:none;z-index:999;background:#fff;border-radius:7px;text-align:center">
    <p id="timing">09:00 Am to 16:00 pm</p>
    <p id="difference"></p>
</div>
<table style="max-width:inherit!important">
    <tr style="height: 67px;background:#fff">
        <td style="vertical-align: top !important">
            <div id="mainheaddivleft" style="position: fixed;width:400px;overflow:hidden;background:#fff;color:#fff;height:67px"  >
                Employee<br/></div></td>
        <td style="vertical-align: top !important">
            <div id="mainheaddiv" style="position: fixed;width: 100%;overflow-x:scroll;overflow-y:hidden;"  >
                <table id="mainheadtable" style="">
                        <thead>
                            <tr role="row">
                                           @for ($i=0;$i<$noofdays;$i++)
                            <th scope="col" @if (date("D",strtotime($loopdates[$i]))=="Sun" || date("D",strtotime($loopdates[$i]))=="Sat")
                                style="background:#003A63;color:#fff;text-align:center" class="cells"
                            @else
                                style="background:#f26321;color:#fff;text-align:center"
                            @endif >
                                <span style="text-align:center">{{date("l",strtotime($loopdates[$i]))}}</span><br/>
                                <span style="text-align:center">{{date("d-m-Y",strtotime($loopdates[$i]))}}</span>
                            </th>
                            @endfor
                            </tr>
                        </thead>
                </table>
           </div>
        </td>
    </tr>
    <tr >
        <td valign="top" style="width:30% !important">
            <table id="nametable"  style="margin-top: 30px">

                <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td id="{{$employee["id"]}}" class="cells" >
                        {{$employee["first_name"]}} {{$employee["last_name"]}}
                        @php
                            if(in_array($employee["id"],$trainedemployeesarray)){
                                    $usertrainingdetail = $traininguserarray[$employee["id"]];
                                    $trainingsdone ="";
                                    foreach ($usertrainingdetail as $key => $value) {
                                        $trainingsdone.="".$value." | ";
                                    }
                                    echo '<span style="padding-left:10px;cursor:pointer" title="Click here for training details" attr-title="'.$trainingsdone.'" class="trainingdetail '.$employee["id"].'-training"><i class="fas fa-book-reader"></i></span>';
                            }
                        @endphp

                        <input type="hidden" name="emp-{{$employee["id"]}}" id="emp-{{$employee["id"]}}" class="assignedemployees" value="" />
                    </td>
                </tr>
            @endforeach
            @if($extrausers)
                @foreach ($extrausers as $user)
                    <tr>
                        <td id="{{$user->id}}" class="cells" >
                            {{$user->first_name}} {{$user->last_name}}
                            @php
                            if(in_array($employee["id"],$trainedemployeesarray)){
                                    $usertrainingdetail = $traininguserarray[$employee["id"]];
                                    $trainingsdone ="";
                                    foreach ($usertrainingdetail as $key => $value) {
                                        $trainingsdone.="".$value." | ";
                                    }
                                    echo '<span style="padding-left:10px;cursor:pointer" title="'.$trainingsdone.'" class="trainingdetail {{$employee["id"]}}-training"><i class="fas fa-book-reader"></i></span>';
                            }
                        @endphp
                            <input type="hidden" name="emp-{{$user->id}}" id="emp-{{$user->id}}" class="assignedemployees" value="" />
                        </td>
                    </tr>
                @endforeach
            @endif
                </tbody>
            </table>
        </td>
        <td id="contentblock" valign="top"  style="width:70% !important;overflow:hidden !important">

            <div class="contentdiv">
            <table id="scheduletable"  style="margin-top:30px">

            <tbody>
                @foreach ($employees as $employee)
                <tr>

                    @for($i=0;$i<$noofdays;$i++)
                        <td id="{{$employee["id"]}}-{{$loopdates[$i]}}"  class="cells" >
                            <div  class="emptyallocation clickme" attr-starttime=""  attr-hours="0" attr-endtime=""
                            attr-employeeid="{{$employee["id"]}}" attr-payperiod="{{$payperiodarray[$loopdates[$i]]}}" attr-date="{{$loopdates[$i]}}" >&nbsp;</div>
                        </td>
                    @endfor
                </tr>
                @endforeach
                @if($extrausers)
                @foreach ($extrausers as $user)
                <tr>

                    @for($i=0;$i<$noofdays;$i++)
                        <td id="{{$user->id}}-{{$loopdates[$i]}}"  class="cells" >
                            <div  class="emptyallocation clickme" attr-starttime=""  attr-hours="0" attr-endtime=""
                            attr-employeeid="{{$user->id}}" attr-payperiod="{{$payperiodarray[$loopdates[$i]]}}" attr-date="{{$loopdates[$i]}}" >&nbsp;</div>
                        </td>
                    @endfor
                </tr>
                @endforeach
                @endif

            </tbody>
        </table>
            </div>
    </td>
    </tr>
</table>
