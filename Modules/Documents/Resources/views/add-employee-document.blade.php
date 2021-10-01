@extends('layouts.app')
<style>
.filter{
        margin-left: -100px;

       }
</style>
@section('content')
<div class="table_title">
    <h4>Employee Documents</h4>
</div>
<div class="timesheet-filters mb-2 filter-div">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4"><label class="filter-text">Employee Name</label></div>
                <div class="col-lg-8 filter">
                    <select class="form-control option-adjust employee-filter select2" name="employee-filter" id="employee-name-filter">
                        <option value="">Select Employee</option>
                        @foreach($user_list as $each_userlist)
                        <option value="{{$each_userlist->id}}">{{ $each_userlist->first_name.' '.$each_userlist->last_name.' ('.$each_userlist->employee->employee_no.')'}}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pull-right" id="">
                <div class="check-controlline checkboxes" style="margin-top:70px;margin-right: 33px;">
                    <input type="hidden" name="requirements" value="" id="requirement_id_hidden">
                        <div class="checkboxs">
                             <input type="checkbox" id="activeCheck" class="chk" name="status_check" value="1"  checked="checked">
                             <label for="activeCheck">
                             Active
                            </label>
                        </div>
                        <div class="checkboxs">
                            <input type="checkbox" id="inactiveCheck" class="chk" name="status_check" value="0"  checked="checked">
                            <label for="inactiveCheck">
                            Inactive
                            </label>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>

<table class="table table-bordered" id="candidates-table">
    <thead>
        <tr>

            <th class="sorting" width="5%">Employee Details</th>
            <th class="sorting" width="5%">CGL 360 User Id</th>
            <th class="sorting" width="5%">Phone</th>
            <th class="sorting" width="5%">Email</th>
            <th class="sorting" width="2%">Actions</th>
        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>
    $(function () {
        $('#customerid').select2();
        $('#activeCheck').prop('checked', true);
        $('#inactiveCheck').prop('checked', true);

        function collectFilterData() {
            return {
                employeename: $("#employee-name-filter").val(),
                employeeno: $('#employee-no-filter').val(),
            }
        }

        $('.select2').select2();
        table =  $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":'{{ route('employee-document.list',[":checked"]) }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());

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
                [0, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [

                {
                    data: 'employee_details',
                    name: 'employee_details',
                    defaultContent: "--",
                },
                {
                    data: 'username',
                    name: 'username',
                    defaultContent: "--"
                },
                {
                    data: 'phonenumber',
                    name: 'phonenumber',
                    defaultContent: "--"

                },
                {
                    data: 'email',
                    name: 'email',
                    defaultContent: "--"

                },
                {

                    data: null,
                    sortable: false,
                    render: function (row) {
                        actions = '';
                        var user = row.allocated_flag;

                        @if(auth::user()->can('add_employee_document'))
                        actions = '<a title="Add" href="{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ row.type_id +'/'+ row.id  +'"  class="fa fa-plus size-adjust-icon" id="add_document"></a>';
                        @elseif(auth()->user()->can('add_allocated_employee_document'))
                        if(user==1)
                        {
                            actions = '<a title="Add" href="{{route("add-client.document", ["typeid" => "" , "id" => ""])}}/'+ row.type_id +'/'+ row.id  +'"  class="fa fa-plus size-adjust-icon" id="add_document"></a>';
                        }


                        @endif

                        actions +='<a title="View"  href="{{ route("documents.view-document",["typeid" => "", "id" => ""])}}/'+ row.type_id +'/'+ row.id +'" class="view btn fa fa-eye id="view_document"></a>';
                      return actions;

                           }

                }


            ]

        });
    });

    $(".employee-filter").change(function(){
            table.ajax.reload();
        });

    $("input[name='status_check']").change(function(){
        var length=  $('.chk:checked').length;
        if(length == 2) {
            var checked = ":checked";
        } else {
            //if active is checked
            if($('#activeCheck').is(':checked')){
                $('#activeCheck').prop('checked', true);
                var checked = $('#activeCheck').val();
            } else {
                $('#inactiveCheck').prop('checked', true);
                var checked = $('#inactiveCheck').val();
            }
              //if inactive is checked
            if($('#inactiveCheck').is(":checked")){
                $('#inactiveCheck').prop('checked', true);
                var checked = $('#inactiveCheck').val();
            }else{
                $('#activeCheck').prop('checked', true);
                var checked = $('#activeCheck').val();
            }
        }
        var table = $('#candidates-table').DataTable();
        var url = '{{ route('employee-document.list',[":checked"]) }}';
            url = url.replace(':checked', checked);
            table.ajax.url( url ).load();


    });


</script>
<style>
    .timesheet-filters {
    padding: 11px 5px;
}
.select2-dropdown {
    z-index: 0 !important;
}
.filter-div{
    padding-bottom: 15px;
}
.checkboxs {
            padding-left: 20px;
        }
        .check-controlline .checkboxs {
            float: left;
            line-height: 16px;
        }
        .pull-right {

    margin-top: -40px;

        }



/* .select2-results{
    z-index: -1 !important;
} */
</style>
@stop

