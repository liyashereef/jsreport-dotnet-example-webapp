<style>
    .add-new{
       
        margin-top:-5px;
        margin-bottom:15px;
    }
</style>
<div class="table_title">
        <h4>Exit Interview Summary</h4>
</div>
    @canany(['create_all_exit_interview', 'create_exit_interview'])
        <div class="add-new" id="add-new-button" data-title="Add New Customer">Add <span class="add-new-label">New</span></div>
    @endcan
    <table class="table table-bordered" id="emp-table">
    <thead>
        <tr>
            <th class="sorting" width="2%"></th>
            <th class="sorting" width="5%">Id</th>
            <th class="sorting" width="5%">Regional Manager</th>
            <th class="sorting" width="2%">Date</th>
            <th class="sorting" width="10%">Site Details</th>
            <th class="sorting" width="10%">Employee Details</th>
            <th class="sorting" width="2%">Reason</th>
            <th class="sorting" width="10%">Reason Detail</th>
            <th class="sorting" width="15%">Explanation</th>


        </tr>
    </thead>
</table>
@endsection
