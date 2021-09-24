@extends('adminlte::page')
@section('title', 'Location Allocation')
@section('content_header')
<h1>Location Allocation</h1>
@stop
@section('content')
<fieldset>
    <div id="filter">
        <!-- <label><input type="radio" name="customer-contract-type" value="{{ PERMANENT_CUSTOMER }}" checked>&nbsp;Permanent</label>
        <label><input type="radio" name="customer-contract-type" value="{{ STC_CUSTOMER }}">&nbsp;Short Term Contracts</label> -->
    </div>
</fieldset>
<div class="row">
    <div class="col-md-12" id="allocation-container">
        <div class="form-group row">
            <div class="col-md-2">
                Choose location for allocation:
            </div>
            <div class="col-md-3">
                <select name="customer_id" id="customer_id" class="form-control">
                    <option value=0 selected>Select All</option>
                    @foreach($location_list as $id=>$data)
                    <option value={{$id}}>{{$data}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <input type="checkbox" name="filter" id="customer-filter" value="1">     Allocated
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered" id="allocation-table">
    <thead>
        <tr>
            <th class="dt-body-center text-center"><input name="select_all" value="1" id="example-select-all" type="checkbox"/></th>
            <th>Employee Id</th>
            <th>Employee Name</th>
            <th>Employee Email</th>
            <th>Role</th>
            <th>Location</th>
            <th>Unallocate</th>
        </tr>
    </thead>
</table>
<div class="col-md-6 allocation-controls top-25">
    <button class="btn blue allocate-submit-btn admin-btn" style='margin-right:5px'>Allocate</button>
    <button class="btn blue allocate-cancel-btn admin-btn">Cancel</button>
</div>
@stop
@section('js')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $('#customer_id').select2();//Added Select2 to project listing
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#allocation-table').DataTable({
                bProcessing: false,
                processing: true,
                serverSide: true,
                fixedHeader: true,
                dom: 'lfrtBip',
                buttons: [
                    
                ],
                ajax:{
                    url: '{{ route('location-allocation.list') }}', // Change this URL to where your json data comes from
                    type: "GET", // This is the default value, could also be POST, or anything you want.
                    data: function(d) {
                        d.ids_office_id = $('#customer_id').val();
                        d.filter = $('#customer-filter').val();
                    },
                    "error": function (xhr, textStatus, thrownError) {
                        if(xhr.status === 401){
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                'columnDefs': [
                    {
                        'targets': 0,
                        'searchable':false,
                        'orderable':false,
                        'className': 'dt-body-center',
                        'visible':true,
                        'render': function (data, type, full, meta){
                        return '<input type="checkbox" id="emp_id" name="employee_id" value="' + $('<div/>').text(data).html() + '">';
                        }
                    },
                    {
                        'targets': 6,
                        'searchable':false,
                        'orderable':false,
                        'visible':true
                    }
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'employee_profile.employee_no',
                        name: 'employee_profile.employee_no'
                    },
                    {
                        data: null,
                        render: function (o) {
                            if(o.last_name == ' ' || o.last_name == null) {
                               var last_name = " ";
                            }
                            else{
                                var last_name = o.last_name;
                            }
                          return o.first_name+'&nbsp'+last_name;
                        },
                        name:'first_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: null,
                        render: function (o) {
                            return uppercase(o.roles[0].name.replace('_', ' '));
                        },
                        name: 'roles.0.name'
                    },
                    {
                        data: 'ids_location_allocation.[, ].ids_office.name',
                        name: 'ids_location_allocation.[0].ids_office.name',
                        orderable:false
                    },
                    {
                        data: null,
                        render: function (o){
                            return (o.ids_location_allocation.length > 0) ? '<a class="unallocate btn fa fa-minus-square fa-lg" data-id=' + o.id + '>' + '</a>':'';
                        }
                    }
                ]
            });
        } catch(e){
            console.log(e.stack);
        }


        $("#allocation-table_wrapper").addClass("datatoolbar");

        // Handle click on "Select all" control
        $('#allocation-table').on('click', '#example-select-all', function(){
            var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        //Uncheck all checkbox on clicking cancel button
        $(".allocate-cancel-btn").on('click', function(){
            $("#example-select-all, #group1 input[name='allocation_type'], input:checkbox").prop('checked', false);
        });

        //Allocate the employee to the customer
        $('.allocate-submit-btn').on('click', function (e) {
            customer_id = $("select#customer_id").val();
            employee_id = $("#allocation-table input[name=employee_id]:checked").length;
            console.log(customer_id,employee_id);
            if (Number(customer_id) > 0  && Number(employee_id) > 0){
                employee_ids = [];
                $("#allocation-table input[name=employee_id]:checked").each(function () { employee_ids.push($(this).val()); });
                employee_ids = (JSON.stringify(employee_ids));
                $.ajax({
                url: '{{route('location-allocation.allocate')}}',
                        method: 'POST',
                        data:  {'ids_office_id': customer_id, 'employee_ids':employee_ids},
                        success: function (data) {
                            if (data.success) {
                                swal("Allocated", "Employee has been allocated", "success");
                                table.ajax.reload();
                            } else {
                                swal("Alert", "Employee(s) already allocated", "warning");
                            }
                        },
                        error: function (xhr, textStatus, thrownError) {
                        //alert(xhr.status);
                        //alert(thrownError);
                        console.log(xhr.status);
                        console.log(thrownError);
                        swal("Oops", "Something went wrong", "warning");
                        },
                });
            } else {
                var msg_str = "Please select ";
                var concat_str = "and ";
                if(Number(customer_id) <= 0 && Number(employee_id) <= 0){
                    msg_str = msg_str + "customer " + concat_str + "employee";
                } else if(Number(customer_id) <= 0){
                    msg_str = msg_str + "customer ";
                }
                else{
                    msg_str = msg_str + "employee"
                }
                swal("Alert",msg_str, "warning");
            }
        });

        //Unallocate the employee allocated to the location
        $("#allocation-table").on("click", ".unallocate", function (e) {
            id = $(this).data('id');
            customer_id = $("select#customer_id").val();
            $filter = ($("#customer-filter").prop('checked')==true) ? true : false;
            swal({
                title: "Are you sure?",
                text: "You won't be able to undo this action",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Unallocate",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },function(unalloc) {
                if (unalloc){
                    $.ajax({
                    url: '{{route('location-allocation.unallocate')}}',
                            type: 'POST',
                            data:  {'ids_office_id': customer_id, 'employee_id':id},
                            success: function (data) {
                            if (data.success) {
                                swal("Unallocated", "Employee has been unallocated", "success");
                                $(".allocate-cancel-btn").trigger('click');
                                if($filter){
                                    $("#customer-filter").prop('checked',true);
                                }
                                table.ajax.reload();
                            } else {
                                swal("Alert", "No Employee allocated", "warning");
                            }
                            },
                            error: function (xhr, textStatus, thrownError) {
                                //alert(xhr.status);
                                //alert(thrownError);
                                console.log(xhr.status);
                                console.log(thrownError);
                                swal("Oops", "Something went wrong", "warning");
                            },
                    });
                };
            });
        });


        /*Filters for customer - Start*/
        $('#customer-filter').on('change', function () {
            if($('#customer-filter').prop('checked') && $('#customer_id').val() != 0){
                $ids_location_id = $('#customer_id').val();
                table.ajax.url('location-allocation/list/'+$ids_location_id).load();
            }else{
                table.ajax.url('location-allocation/list').load();
            }
        });

        $('#customer_id').on('change', function () {
          if($('#customer-filter').prop('checked')){
               $ids_location_id = $('#customer_id').val();
               if($ids_location_id != 0){
                table.ajax.url('location-allocation/list/'+$ids_location_id).load();
               }else{
                table.ajax.url('location-allocation/list').load();
               }
            }
        });
        /*Filters for customer - End*/

      
    });
</script>
@stop
