<div class="pm-report-section table-responsive">
    <table class="pmr-table table" id="pm-report-table">
        <thead>
            <th style="width:8%">Site</th>
            <th style="width:8%">Project</th>
            <th style="width:8%">Group </th>
            <th style="width:12%">Task </th>
            <th style="width:10%">Assignee</th>
            <th>Last Update</th>
            <th style="width:6%">Due Date </th>
            <th >Completed Date </th>
            <!-- <th style="width:5%">Status Date</th>
            <th style="width:5%">Status</th>
            <th style="width:15%">Notes</th> -->
            <th style="width:60%">
               {{--  old code-start --}}
               {{--  <div class="row">
                    <div style="text-align: center; width:130px;">Status Date</div>
                    <div style="text-align: center; width:25%;">Status</div>
                    <div style="text-align: center; width:50%;">Notes</div>
                </div> --}}
                {{--  old code-end --}}
                 <table style="width:100%;">
                <tr>
                    <td style="text-align: center;border: 0px;  width:30% !important">Status Date</td>   
                    <td style="text-align: center;border: 0px;  width:20%;" >Status</td>   
                    <td style="text-align: center;border: 0px;  width:50%;">Notes</td>                    
                </tr>
            </table> 
            </th>
            @can('update_report')
            <th style="width:5%"></th>
            @endcan
        </thead>
        <tbody class="primary-tbody"></tbody>
    </table>
</div>
