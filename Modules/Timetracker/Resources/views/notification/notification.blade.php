@extends('layouts.app')

@section('content')
<div class="table_title">
    <h4>All Notifications</h4>
</div>
<div id="message"></div>
<table class="table table-bordered" id="notification-table" width="100%">
    <thead>
        <tr>
            <th class="dt-body-center text-center"><input name="select_all" value="1" id="example-select-all" type="checkbox" class="sel-checkbox"/></th>
            <th></th>
            <th></th>
            <th id="delete"></th>
        </tr>
    </thead>
</table>
<div class="col-md-6 action-buttons top-50" style='display:none'>
    <button class="btn blue notification-read-btn" style='margin-right:5px'>Mark as Read</button>
    <button class="btn blue notification-delete-btn" style='margin-right:5px'>Delete</button>
    <button class="btn blue notification-cancel">Cancel</button>
</div>
@stop

@section('scripts')
<script>
    $(function () {
        $(".content-header").addClass('notification-header');

    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
    });
    $.fn.dataTable.ext.errMode = 'throw';
         try{
            table = $('#notification-table').DataTable({
                dom: 'Blfrtip',
                bProcessing: false,
                processing: true,
                serverSide: true,
                // fixedHeader: true,
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-pdf-o',
                    },
                    {
                        extend: 'excelHtml5',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-file-excel-o'
                    },
                    {
                        extend: 'print',
                        pageSize: 'A2',
                        //text: ' ',
                        //className: 'btn btn-primary fa fa-print'
                    },
                ],

            ajax: {
                "url":'{{ route('notification.getNotificationMessage') }}',
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            columnDefs: [{
            targets: 0,
                    'orderable':false,
                    'className': 'dt-body-center',
                    'visible':true,
                    'render': function (data, type, full, meta){
                    return '<input type="checkbox" class="sel-checkbox" id="notification_id" name="notification_id" value="' + $('<div/>').text(data).html() + '">';
                    }
            }],
            select: {
            style:    'os',
                    selector: 'td:first-child'
            },
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
            {
            data: 'notification_id',
                    name: 'notification_id',
                    orderable:false
            },
            {
            data: null,
                    name: 'notification.notification_message',
                    orderable:false,
                    render:function(data){
                    var user_img = 'no_avatar.jpg';
                            if ((typeof (data.notification.user_notification_guard) !== 'undefined' && data.notification.user_notification_guard != null)){
                    if ((typeof (data.notification.user_notification_guard.employee_profile.image) !== 'undefined')){
                    if (data.notification.user_notification_guard.employee_profile.image == null){
                    user_img = 'no_avatar.jpg';
                    } else{
                    user_img = data.notification.user_notification_guard.employee_profile.image;
                    }
                    }
                    }
                    return "<img height='35px' src='{{asset("images/uploads") }}/" + user_img + "' />&nbsp;&nbsp;&nbsp;" + ((data.notification.notification_message.replace(/&lt;/g, "<")).replace(/&gt;/g, ">"));
                    }
            },
            {
            data: null,
                    name: 'notification.created',
                    orderable:false,
                    render:function(data){
                    var created_at = data.notification.created.replace(/-/g, '/');
                            var date = new Date(created_at);
                            return date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear() + " at " + date.toLocaleString('en-US', { hour: 'numeric', minute:'numeric', hour12: true });
                    }

            },
            {
            data: null,
                    orderable:false,
                    render: function (o){
                    return '<a class="delete fa fa-trash-o fa-lg btn" data-id=' + o.notification_id + ' data-read=' + o.read + '>' + '</a>'; }
            }             ], 'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
    if (aData.read == 0)
            $(nRow).children(":first").css('cssText', 'border-left: 3px solid #3c8dbc !important')
    }
    });
    } catch(e){
            console.log(e.stack);
        }

            $("#notification-table_wrapper").addClass("no-datatoolbar datatoolbar");
            $('#notification-table').on('click', '#example-select-all', function(){
    var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
            // Handle click on checkbox to set state of "Select all" control
    $('#notification-table tbody').on('change', 'input[type="checkbox"]', function(){
        if (!this.checked){
            var el = $('#example-select-all').get(0);
                    if (el && el.checked && ('indeterminate' in el)){
                    //el.indeterminate = true;
                    $('#example-select-all').prop('checked', false);
            }
        }
    })


            $(".cancel").on('click', function(){
    $("#example-select-all input[name='allocation_type'], input:checkbox").prop('checked', false);
            $("#notification-table input[name='notification_id']").prop('checked', false);
            $(".action-buttons").hide();
    });
            $('#notification-table').on('click', '.sel-checkbox', function() {
    if ($('.sel-checkbox').is(":checked") && $('input[type="checkbox"]').length > 1)
            $(".action-buttons").show();
            else
            $(".action-buttons").hide();
    });
            $('.notification-read-btn').on('click', function (e) {
    notification_ids = [];
            $("#notification-table input[name=notification_id]:checked").each(function () { notification_ids.push($(this).val()); });
            notification_ids_str = (JSON.stringify(notification_ids));
            swal({
                title: "Are you sure?",
                text: "You want to mark the notification as read",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "cancel",
                confirmButtonText: "Confirm",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
    $.ajax({
    url: '{{route('notification.read')}}',
            method: 'POST',
            data:  {'notification_ids':notification_ids_str},
            success: function (data) {
            if (data.success) {
            swal("Read", "Notification has been marked as read", "success");
                    table.ajax.reload();
                    $('#example-select-all').prop('checked', false);
                    $(".action-buttons").hide();
                    var count = $("#notification_count").html();
                    count  = count - notification_ids.length;
                    if(count < 0){
                        count = 0;
                    }
                    $("#notification_count").html(count);
            } else {
            //alert(data);
            console.log(data);
                    swal("Oops", "Notification not marked as read", "warning");
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
    });
    });
    // });

 //Delete multiple notifications using checkbox and delete button
             $('.notification-delete-btn').on('click', function (e) {
            notification_ids = [];
            $("#notification-table input[name=notification_id]:checked").each(function () { notification_ids.push($(this).val()); });
            notification_ids_str = (JSON.stringify(notification_ids));
            swal({
                title: "Are you sure?",
                text: "You want to delete the notifications",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "cancel",
                confirmButtonText: "Delete",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
    url: '{{route('notification.multiDelete')}}',
            method: 'POST',
            data:  {'notification_ids':notification_ids_str},
            success: function (data) {
            if (data.success) {
            swal("Deleted", "Notifications has been deleted", "success");
                    table.ajax.reload();
                    $('#example-select-all').prop('checked', false);
                    $(".action-buttons").hide();
                    var count = $("#notification_count").html();
                    count  = count - notification_ids.length;
                    if(count < 0){
                        count = 0;
                    }
                    $("#notification_count").html(count);
            } else {
            //alert(data);
            console.log(data);
                    swal("Oops", "Notification was not deleted", "warning");
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
    });
    });


            //Delete Notification by clicking delete icon
            $("#notification-table").on("click", ".delete", function (e) {

            id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "You want to delete the notification",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "cancel",
                confirmButtonText: "Delete",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function(){
    $.ajax({
    url: '{{route('notification.delete')}}',
            type:'POST',
              data:{ id:id},
            success: function (data) {
            if (data.success) {
            swal("Deleted", "Notification has been deleted", "success");
                    table.ajax.reload();
                    $('#example-select-all').prop('checked', false);
                    var count = $("#notification_count").html();
                    count  = count - 1;
                    if(count < 0){
                        count = 0;
                    }
                    $("#notification_count").html(count);
            } else {
            //alert(data);
            console.log(data);
                    swal("Oops", "Notification was not deleted", "warning");
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
            });
        });

            $('.notification-cancel').on('click', function (e) {
    notification_ids = [];
            $("#notification-table input[name=notification_id]:checked").each(function () {
    $(this).prop('checked', false);
    });
            $('#example-select-all').prop('checked', false);
    });
    });


</script>
<style type="text/css">
    .unread
    {
        color:#ff3333 !important;
    }
    .dt-buttons
    {
        display:none !important;
    }
</style>
@stop
