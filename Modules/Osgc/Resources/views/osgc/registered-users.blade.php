@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
   </head>
@section('content')
<div class="table_title">
    <h4>OSGC Registered Users</h4>
</div>



<div class="pull-right">
    <div class="col-md-12" style="float: right;">
        <input type="checkbox" class="largerCheckbox" checked name="course_completion_status" id="course_completion_status" value="">
        <label>Course Completed Users</label>
    </div>
    <!-- <div class="col-md-6">
    <a class="user-export btn"  href="{{route('osgc-users.user-export')}}">User Export</a>
    </div> -->
</div>




<table class="table table-bordered" id="osgc-users_table">
    <thead>
        <tr>
        <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Registered On</th>
            <th>Veteran Status</th>
            <th>Aboriginal descent Status</th>
            <th>Referral</th>
            <th>Payment Status</th>
            <th>Amount</th>
            <th>Course Name</th>
            <th>Last Module completed</th>
            <th>% Completed</th>
            <th>Days Tracker</th>
            <th>Registered Month</th>
            <th>Status</th>

        </tr>
    </thead>
</table>
@stop
@section('scripts')
<style>
.user-export{
    float: right;
}
.user-export {
    float: right;
    width: 200px;
    background-color: #f26222;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
    text-align: center;
    border-radius: 5px;
    padding: 5px 0px;
    margin-left: 5px;
    cursor: pointer;
    border: 0px;
}
</style>
<script>



    $(function(){
        if($('input[type="checkbox"]').prop("checked") == true){
            var course_completion_status=1;
        }else{
           var course_completion_status=0;
        }
        var url = "{{ route('registered-users.list',[':course_completion_status']) }}";
        url = url.replace(':course_completion_status', course_completion_status);
        

        $.fn.dataTable.ext.errMode = 'throw';
        try{
           
            table = $('#osgc-users_table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: url,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: [1, 2,3,4,5,6,7,8,9,10,11,12,13,14],
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [1, 2,3,4,5,6,7,8,9,10,11,12,13,14],
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: [1, 2,3,4,5,6,7,8,9,10,11,12,13,14],
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            "rowCallback": function (row, data, index) {
                var bg_color =  data.background_color;
                var color =  data.color;
                $(row).find('td:eq(10)').css('background-color', bg_color).css('color',color);

            },  
            columnDefs: [
             
             ],
             columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    "className": "text-center",
                    data: 'is_veteran',
                    name: 'is_veteran'
                },
                {
                    "className": "text-center",
                    data: 'indian_status',
                    name: 'indian_status'
                },
                {
                    "className": "text-center",
                    data: 'referral',
                    name: 'referral'
                },
                {
                    "className": "text-center",
                    data: 'status',
                    name: 'status'
                },
                
                {
                    "className": "text-center",
                    data: 'amount',
                    name: 'amount'
                },
                 
                {
                    data: 'course_title',
                    name: 'course_title'
                },
                 {
                    data: 'last_course_completion',
                    name: 'last_course_completion'
                },
                {
                    "className": "text-center",
                    data: 'percentage_completion',
                    name: 'percentage_completion'
                },
                {
                    "className": "text-center",
                    data: 'days_tracker',
                    name: 'days_tracker'
                },
               
                {
                    data: 'paid_date',
                    name: 'paid_date'
                },
               
                {
                    data: 'active',
                    name: 'active'
                },
                
            ]
        });

          } catch(e){
            console.log(e.stack);
        }

});

$("input[name='course_completion_status']").click(function(){
    if($(this).prop("checked") == true){
        course_completion_status=1;
    }else{
        course_completion_status=0;
    }
    var table = $('#osgc-users_table').DataTable();
    var url = "{{ route('registered-users.list',[':course_completion_status']) }}";
    url = url.replace(':course_completion_status', course_completion_status);
    table.ajax.url( url ).load();
});




</script>
<style>

.filter-div{
    margin-top: 20px;
}
.label-name{
    margin: 7px 12px 0px 36px;
}

.filter-wrapper{
   padding: 14px 0px 38px 0px;
}

.custom-datepicker{
    margin-bottom: -40px;
}
.Date_filter{
    margin-left:80px;
}
.filter{
    margin-left:-80px;
}
.end-date{
    margin-right:20px;
}

</style>

@stop
