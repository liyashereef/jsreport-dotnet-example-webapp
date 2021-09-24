@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Open Shift Approval</h4>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="check-controlline checkboxes" id="parttimefulltime">
        <div class="checkboxs">
            <input type="checkbox" id="parttimeCheck" class="chk" name="timecheck[]" value="1"
                checked="checked">
            <label for="parttimeCheck">
                Open
            </label>
        </div>
        <div class="checkboxs">
            <input type="checkbox" id="farttimeCheck" class="chk" name="timecheck[]" value="2">
            <label for="farttimeCheck">
                Closed
            </label>
        </div>
    </div>
    </div>
    <div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customer_details_arr,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
</div>
<br>
 <div class="container-fluid"  id="customerrormessage" style="color:#fff;background-color:#003A63;padding:9px;display:none">
        <div class="row">
                <div class="col-md-12" style="text-align:center;color:#fff">
                    A choice is required
                </div>
        </div>
 </div>
 <input id="backbuttonstate" type="text" value="0" style="display:none;" />
<table class="table table-bordered" id="openshift-table">
    <thead>
        <tr>
            <th class="sorting" width="5%">#</th>
            <th class="sorting" width="10%">Project Number</th>
            <th class="sorting" width="10%">Client Name</th>
            <th class="sorting" width="10%">Site Rate</th>
            <th class="sorting" width="10%">Date</th>
            <th class="sorting" width="10%">No of Positions</th>
            <th class="sorting" width="10%">Open Positions</th>
            <th class="sorting" width="10%">Notes</th>
            <th class="sorting" width="5%">Status</th>
            <th class="sorting" width="5%">Unread</th>
            <th class="sorting" width="5%">Map</th>
        </tr>
    </thead>
</table>
@stop
@section('scripts')
<script>


document.addEventListener('DOMContentLoaded', function () {
   var ibackbutton = document.getElementById("backbuttonstate");
   if (ibackbutton.value == "0") {
     // Page has been loaded for the first time - Set marker
     ibackbutton.value = "1";
   } else {
     // Back button has been fired.. Do Something different..
    // window.location.reload();
    $('#farttimeCheck').prop('checked',false);
   }
}, false);


    $(function () {
    $(".select2").select2();
    var checked=[1];
    var url = '{{ route('openshift.list',":checked") }}';
    url = url.replace(':checked', checked);
        $('#parttimefulltime').change(function(){
        var checked = [];
        var client_id = $('#clientname-filter').val() ?  $('#clientname-filter').val() : 0;
        $(':checkbox:checked').each(function(i){
            if($(this).prop("checked") == true){
               checked.push(($(this).val()));
            }

        });
        var table = $('#openshift-table').DataTable();
        var url = "{{ route('openshift.list',[':checked',':client_id']) }}";
        url = url.replace(':checked', checked);
        url = url.replace(':client_id', client_id);
        if((checked.length) > 0){
            $('#openshift-table_wrapper').css("display","block");
            $("#customerrormessage").css("display","none");
            table.ajax.url( url ).load();
        }else{

            $('#openshift-table_wrapper').css("display","none");
            $("#customerrormessage").css("display","block");


        }

      });

    $('#clientname-filter').on('change', function(e){
            var table = $('#openshift-table').DataTable();
            var client_id = $('#clientname-filter').val() ?  $('#clientname-filter').val() : 0;
            var checked = [];
            $(':checkbox:checked').each(function(i){
                if($(this).prop("checked") == true){
                checked.push(($(this).val()));
                }
            });
       url = "{{ route('openshift.list',[':checked',':client_id']) }}";
       url = url.replace(':checked', checked);
       url = url.replace(':client_id', client_id);
       table.ajax.url(url).load();
    });


        var table = $('#openshift-table').DataTable({
            fixedHeader: true,
            processing: false,
            serverSide: true,
            responsive: false,
            ajax: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
            [8, "desc"]
            ],
            dom: 'Blfrtip',
            buttons: [{
                extend: 'pdfHtml5',
                pageSize: 'A2',
                exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                        }

            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                        }

            },
            {
                extend: 'print',
                pageSize: 'A2',
                exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6,7,8]
                        }

            }
            ],

            lengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
            {
                    data: 'id',
                    render: function (o) {
                        return '<button  class="btn fa fa-plus-square buttons" data-id=' + o.id + '></button>';
                    },
                    orderable: false,
                    className: 'details-control',
                    data:  null,
                    defaultContent: ''

                },
            {
                data: 'project_number',
                name: 'project_number',
                defaultContent: "--",
            },
            {
                data: 'client_name',
                name: 'client_name',
                defaultContent: "--",
            },
             {
                data: 'site_rate',
                name: 'site_rate',
                defaultContent: "--",
            },
            {
                data: null,
                name: 'start_date',
                defaultContent: "--",
                render: function(data, type, row){
                return data.start_date.split(", ").join("<br/>");
                }
            },
            {
                data: 'no_of_shifts',
                name: 'no_of_shifts',
                className: "text-center" ,
                defaultContent: "--",
            },
             {
                data: 'remaining_shifts',
                name: 'remaining_shifts',
                className: "text-center" ,
                defaultContent: "--",
            },

             {
                data: null,
                name: null,
                defaultContent: "--",
                render: function (o) {
                        let notes = o.notes;
                        if(notes == "" || notes == null)  {
                            return '--';
                        }else{
                            if(notes.length > 25) {
                            return '<span class="show-btn nowrap" style="cursor:pointer;" onclick="$(this).hide();$(this).next().show();">' + notes.substr(0, 25) +
                                        '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                                        '</span><span style="cursor:pointer;display:none;" class="notes big-notes" onclick="$(this).hide();$(this).prev().show();">' +
                                        notes + '&nbsp;&nbsp;' +
                                        '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                                        '</span><br/>\r\n';
                            }else{
                                return notes;
                            }
                        }
                    }
            },
             {
                data: 'status',
                name: 'status',
                defaultContent: "--",
            },{
                data: 'unread',
                name: 'unread',
                className: "text-center"

            },
             {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        actions = '';
                        var map_url = '{{ route("openshift.plot-in-map", ":id") }}';
                        map_url = map_url.replace(':id', o.id);
                        actions += '<a title="Map" target="_blank" href="' + map_url +
                            '" class="fa fa-users"></a>';
                        return actions;
                    },
                }




          ]
      });

           $('#openshift-table tbody').on('click', 'td.details-control', function () {
            var id=$(this).closest('tr').find('.buttons').data('id');
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            var url = "{{ route('openshift.details',':id') }}"
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                success: function (data) {
                var row = table.row( tr );
                if ( row.child.isShown() ) {
                // This row is already open - close it
                tr.find('td.details-control').html('<button  class="btn fa fa-plus-square buttons" data-id=' + id + '></button>');
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                var status=tr.find('td.sorting_1').html();
                tr.find('td.details-control').html('<button  class="btn fa fa-minus-square buttons"  data-id=' + id + '></button>');
                row.child( format(data,status) ).show();
                tr.addClass('shown');
            }

                },
                error: function () {}
            });

        } );


  });

  function assignToEmployee(requirement_id, customer_id, multiple_shift_id, user_id, already_contacted_candidates) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('openshift.shift-availability') }}",
            type: 'GET',
            data: {"multiple_shift_id":multiple_shift_id, 'user_id':user_id, 'customer_id':customer_id, 'requirement_id':requirement_id},
            success: function (data) {
                if(data.success) {
                    gatewayCheckEventLog(data.requirement_id, data.customer_id,data.multiple_shift_id,user_id,[]);
                }else if(data.alreadyAssigned){
                    let already_assigned = data.alreadyAssigned;
                    gatewayCheckEventLog(requirement_id, customer_id,already_assigned,user_id,already_contacted_candidates);
                }else if((!data.success) && (data.msg) && (data.msg != "")){
                    swal("Error", data.msg,"error");
                }
            }
        });
  }

 /**To check if this candidate is already contacted for the same requirment
    **/
    function gatewayCheckEventLog(requirement_id, customer_id,shift_id,user_id,already_contacted_candidates) {
          var url= "{{route('openshift.delete',[":requirement_id",":shift_id",":user_id"])}}";
            url = url.replace(':requirement_id', requirement_id);
        url = url.replace(':shift_id', shift_id);
        url = url.replace(':user_id', user_id);
        if ($.inArray(user_id, already_contacted_candidates) != -1) {
            swal({
                title: "Already contacted",
                text: "Already contacted the candidate for the same requirement. Do you want to call this person again?",
                type: "info",
                showCancelButton: true,
                //confirmButtonClass: "btn-success",
                confirmButtonText: "Yes",
                showLoaderOnConfirm: true,
                closeOnConfirm: true
            }, function () {

            window.location = url;
            });
        } else {
            window.location = url;
        }
    }

/* Formatting function for row details - modify as you need */
function format ( d,status) {
    var html= '';
     var  action_html='<a  title="Event Log" href="javascript:;" class="fa fa-calendar fa-disabled" id="event-log"></a>';
    $.each(d,function(key,item){
     actions=(status=='Closed')?(action_html):(item.actions);
        if(item.editable==false && status=='Open')
        {
         actions=action_html;
        }
        html +='<tr><td>'+item.employee_id+'</td><td>'+item.employee+'</td><td>'+item.phone+'</td><td>'+item.email+'</td><td>'+uppercase(item.role)+'</td><td>'+item.address+'</td><td>'+item.city+'</td><td>'+'Start : '+item.startdate+ '\r\n<br/>End : '+item.enddate+'</td><td>'+'Start : '+item.starttime+'\r\n<br/>End : '+item.endtime+'</td><td>'+item.created_at+'</td><td>'+item.created_time+'</td><td>'+item.approved+'</td>@can('approve_openshift')<td>'+actions+'</td>@endcan</tr>';
    });
    return '<table  class="dataTable subtable">'+
        '<tr><th><b>Employee ID</b></th><th><b>Employee Name</b></th><th><b>Cell Phone</b></th><th><b>Email</b></th><th><b>Role</b></th><th><b>Address</b></th><th><b>City</b></th><th><b>Date</b></th><th><b>Time<b></th><th><b>Submitted Date</b></th><th><b>Submitted Time</b></th><th><b>Approved by</b></th>@can('approve_openshift')<th><b>Actions</b></th>@endcan</tr>'+
        '<tbody class="child_elements">'+html+
        '</tbody>'+
    '</table>';
}
</script>
<style type="text/css">
         /* #add-new-button{ margin-top: 0px;margin-right:5px;} */
        .checkboxs {
            padding-left: 20px;
        }
        .check-controlline .checkboxs {
            float: left;
            line-height: 16px;
        }
        </style>
@stop
