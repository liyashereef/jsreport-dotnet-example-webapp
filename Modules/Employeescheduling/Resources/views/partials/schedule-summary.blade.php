
<style>
    .redbg{
        background: #F2351F;
        color:#fff;
    }
    .greenbg{
        background:#343F4E;
        color:#fff;
    }
    
    .outer-span {
        font-size: 12px;
        padding-right: 0px;
        padding-left: 10px;
    }
    
    .inner-span {
        border: solid 1px #000;
        font-size: 12px;
        margin-left: 10px;
        line-height: 35px;
        display: inline-block;
        width: 100px;
        text-align: center;
    }
    
    .inner-content {
        
    }
</style>

@if(!empty($scheduleObj))
<!--<div class="squareblockcap"  style="float: left;">
    @if($scheduleObj->contractual_hours == "" || $scheduleObj->contractual_hours == 0)
        Contract Hours <br/>&nbsp;&nbsp;&nbsp;&nbsp;per Week<div class="squareblock redbg" style="float: right;">Nil</div>
    @else
        Contract Hours <br/>&nbsp;&nbsp;&nbsp;&nbsp;per Week<div class="squareblock" style="float: right;">{{$scheduleObj->contractual_hours}}</div>
    @endif
    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
</div>
<div class="squareblockcap"  style="float: left;">
    Average Hours <br/>&nbsp;&nbsp;&nbsp;&nbsp;per Week<div class="squareblock" style="float: right;">{{$scheduleObj->avgworkhours}}</div>
</div>
<div class="squareblockcap"  style="float: left;">
    @php($varience_class = (($scheduleObj->variance < 0) || ($scheduleObj->contractual_hours == "") || ($scheduleObj->contractual_hours == 0))? 'redbg':'greenbg')
    Variance From <br/>&nbsp;&nbsp;&nbsp;&nbsp;Contract<div class="squareblock {{$varience_class}}" style="float: right;">{{($scheduleObj->contractual_hours == "") ? 'Invalid':$scheduleObj->variance}}</div>
</div>
<div class="squareblockcap"  style="float: left;">
    @php($indicator_class = ($scheduleObj->schedindicator == 1)? 'greenbg':'redbg')
    Schedule <br/>Indicator<div class="squareblock {{$indicator_class}}" style="float: right; width: 59%;">{{($scheduleObj->schedindicator == 1)? 'True':'False'}}</div>
</div>-->


<span class="outer-span">
    Contract Hours
    @if($scheduleObj->contractual_hours == "" || $scheduleObj->contractual_hours == 0)
    <span  class="redbg inner-span">
            <span  class="inner-content">Nil</span>
    </span>          
    @else
    <span class="inner-span">
        <span  class="inner-content">{{str_replace('.', ':', $scheduleObj->contractual_hours)}}</span>
    </span>
    @endif
</span>

<span class="outer-span">
    Average Hours
    <span class="inner-span">
        <span  class="inner-content">{{($scheduleObj->avgworkhours != "") ? str_replace('.', ':', $scheduleObj->avgworkhours) : ''}}</span>
    </span>
</span>

<span class="outer-span">
    @php($varience_class = (($scheduleObj->variance < 0) || ($scheduleObj->contractual_hours == "") || ($scheduleObj->contractual_hours == 0))? 'redbg':'greenbg')
     Variance From Contract
     <span  class="inner-span {{$varience_class}}">
        <span  class="inner-content">{{($scheduleObj->contractual_hours == "") ? 'Invalid':(($scheduleObj->variance != "")? str_replace('.', ':', $scheduleObj->variance): '')}}</span>
    </span>
</span>


<span class="outer-span">
    @php($indicator_class = ($scheduleObj->schedindicator == 1)? 'greenbg':'redbg')
    Schedule Indicator
    <span class="inner-span {{$indicator_class}}">
        <span  class="inner-content">{{($scheduleObj->schedindicator == 1)? 'True':'False'}}</span>
    </span>
</span>
@endif