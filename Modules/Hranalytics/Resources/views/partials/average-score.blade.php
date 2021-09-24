<div class="col-sm-12 col-md-12 col-lg-12 average-score-div">
    <span style="color:orangered; margin-right:5px;">Average Score </span>
    @if($employeeTimesheetApprovalScore != null)
    <span class="col font-color-black" style="background-color:black;padding: 4px 15px 8px 11px;">{{number_format($employeeTimesheetApprovalScore->score,2)}}</span>
    @else
    <span class="col" style="color:white;background-color:black;padding: 4px 19px 6px 18px;">--</span>
    @endif
</div>
