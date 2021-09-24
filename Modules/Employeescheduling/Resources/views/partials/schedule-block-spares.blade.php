

<tr>
    
    @for($i=0;$i<$noofdays;$i++)
        <td id="{{$employee}}-{{$loopdates[$i]}}"  class="cells" >
            <div  class="emptyallocation clickme" attr-starttime=""  attr-hours="0" attr-endtime=""
            attr-employeeid="{{$employee}}" attr-payperiod="{{$payperiodarray[$loopdates[$i]]}}" attr-date="{{$loopdates[$i]}}" >&nbsp;</div>
        </td>
    @endfor
</tr>

            