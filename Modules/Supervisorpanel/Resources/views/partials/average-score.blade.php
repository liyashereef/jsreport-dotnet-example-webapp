<div class="col-sm-12 col-md-12 col-lg-12 average-score-div">
    <span style="color:orangered; margin-right:5px;">Average Score </span>
    @if($average_score_arr['total'] >=0)
    @if($score_class['total'] =='black')
    <span class="col" style="color:white;background-color:black;padding: 4px 19px 6px 18px;">--</span>
    @else
    <span class="col font-color-{{$score_class['total']}}" style="background-color:{{$score_class['total']}};padding: 4px 15px 8px 11px;">{{number_format($average_score_arr['total'],3)}}</span>
    @endif
    @else
    <span class="col" style="color:white;background-color:black;padding: 4px 19px 6px 18px;">--</span>
    @endif
</div> 