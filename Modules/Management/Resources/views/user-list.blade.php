@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>User List</h4>
</div>

<div class="timesheet-filters mb-2 filter-div">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4"><label class="filter-text">Employee Name</label></div>
                <div class="col-lg-5 filter" id="dropdown">
                    <select class="form-control option-adjust employee-filter " name="employee-filter" id="employee-name-filter">
                        <option value="">Select Employee</option>
                        @foreach($userList as $each_userlist)
                        <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-3"><label class="filter-text">Status</label></div>
                <div class="col-lg-5 " id="dropdown">
                    <select class="form-control option-adjust status-filter select2" name="status-filter" id="status-name-filter">
                    <option value=-1  >All</option>
                    <option value=0 >Inactive</option>
                    <option value=1 selected="selected">Active</option>
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>

    </div>
</div>


<table class="table table-bordered" id="candidates-table">
    <thead>
    <tr>
    <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Employee No</th>
            <th>Employee Contact</th>
            <th>Action</th>

        </tr>
    </thead>
</table>
@stop

@section('scripts')
<script>

    $(function () {
        $('#employee-name-filter').select2();

        function collectFilterData() {
            return {
                employeename: $("#employee-name-filter").val(),
                employeeno: $('#employee-no-filter').val(),
            }
        }
        function collectFilterStatusData() {
            return {

                status_id: $('#status-name-filter').val(),
            }
        }


        $('#status-name-filter').select2();
        table =  $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":'{{ route('management.userViewList') }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData(),collectFilterStatusData());

                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [1, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                status = (aData['status_color']).toLowerCase();
                /* Append the grade to the default row class name */
                if (status == "red") {
                    $(nRow).addClass('open');
                } else{
                    $(nRow).addClass('white');
                }
            },
            columns: [

                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'full_name', name: 'full_name'},
                {data: 'email', name: 'email'},
                {data: 'roles', name: 'roles'},
                {data: 'emp_no', name: 'emp_no'},
                {data: null, name: 'phone', render:function(data){
                    return data.phone_ext!=null?(data.phone+' x'+data.phone_ext):data.phone;
                }},


                {

                    data: null,
                    sortable: false,
                    render: function (row) {
                        actions = '';
                        var user = row.allocated_flag;



                        actions ='<a title="View"  href="{{ route("management.userViewMore")}}/'+ row.id +'" class="view btn fa fa-eye id="view_document"></a>';
                      return actions;

                    }
                },

            ]
         });
    });


    $(".employee-filter").change(function(){
            table.ajax.reload();
    });
    $(".status-filter").change(function(){
            table.ajax.reload();
    });

</script>
<style>
.timesheet-filters {
    padding-right: 0px;
    padding-bottom: 5px;
    z-index: 6000;
}
.select2-dropdown {
    z-index: 0 !important;
}
.filter-div{
    padding-bottom: 5px;
}
#dropdown{
    margin-left:-95px;
}
.filter-div{
    padding-bottom: 15px;
}
.filter-text{
    margin-top:6px;
}
html, body {
max-width: 100%;
overflow-x: hidden;
}
</style>
@stop


