@extends('layouts.app')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('content')
<div class="table_title">
    <h4>Post Orders
        <?php
        $selected_customer_ids = (new \App\Services\HelperService())->getCustomerIds();
        if (!empty($selected_customer_ids)) {
            echo '<button type="button" class="dashboard-filter-customer-reset btn btn-primary float-right"> Reset Filter</button>';
        }
        ?>
    </h4>
</div>
<div class="col-md-6 customer_filter_main">
    <div class="row">
        <div class="col-md-3"><label class="filter-text customer-filter-text">Customer </label></div>
        <div class="col-md-6 filter customer-filter">
        {{ Form::select('clientname-filter',[''=>'Select customer']+$customerList,null,array('class'=>'form-control select2 option-adjust client-filter', 'id'=>'clientname-filter', 'style'=>"width: 100%;")) }}
        <span class="help-block"></span>
        </div>
    </div>
</div>
<br>
<div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            {{ Form::open(array('url'=>'#','id'=>'postorder-action-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null)}}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}" id="status">
                    <label for="status" class="col-sm-12 control-label">Choose Status</label>
                    <div class="col-sm-12">
                        {{ Form::select('status', [null=>'Please Select','1'=>'Approve','0'=>'Reject'], null,array('class'=>
                        'form-control','required'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn submit','id'=>'mdl_save_change'))}}
                {{ Form::button('Cancel', array('class'=>'btn cancel','data-dismiss'=>"modal", 'aria-hidden'=>true))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
    <table class="table table-bordered auto-refresh" id="post-order-list-table">
        <thead>
            <tr>
              <th class="dt-body-center text-center select_header">Select
                 <!-- <input name="select_all" value="1" id="example-select-all" type="checkbox"/> -->
             </th>
             <th class="sorting">Order</th>
             <th class="sorting">Topic</th>
             <th class="sorting">Group</th>
             <th class="sorting">Client Name</th>
             <th class="sorting">Description</th>
             <th class="sorting">Document</th>
             <th class="sorting">Uploaded By</th>
             <th class="sorting">Reviewed By</th>
             <th class="sorting">Status</th>
              <th class="sorting">Actions</th>
         </tr>
     </thead>
 </table>
    <div>
        <a onclick="getUrl()"
           class="btn btn-primary"
           id="downloadzip-btn"
           type="button" onclick="void(0)">Download</a>
    </div>
</div>
@stop
@section('scripts')
<script>
    $(".select2").select2()
    function collectFilterData() {
        return {
            client_id:$("#clientname-filter").val(),
        }
    }
    var table = $('#post-order-list-table').DataTable({
        processing: false,
        serverSide: true,
        responsive: true,
        ajax: {
            "url":"{{ route('post-order.list')}}",
            'global':true,
            "data": function ( d ) {
                    return $.extend({}, d, collectFilterData());
                        },
            "error": function (xhr, textStatus, thrownError) {
                if(xhr.status === 401){
                    window.location = "{{ route('login') }}";
                }
            },
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        order: [[ 0, "desc" ]],
        fnRowCallback: function (nRow, aData, iDisplayIndex) {
            status = aData['reviewed_status_id'];
            /* Append the grade to the default row class name */
            if (status == 1){
                $(nRow).addClass('closed');
            } else if(status == 0){
                $(nRow).addClass('open');
            } else {
                $(nRow).addClass('in_progress');
            }
            var info = table.page.info();
                // $('td', nRow).eq(0).html(iDisplayIndex + 1 + info.page * info.length);
        },
        columnDefs: [
            {
                'targets': '_all',
                'className': 'dt-center datatable-v-center'
            },
            {
                'targets': 0,
                'searchable':false,
                'orderable':false,
                'visible':true,
                'render': function (data, type, full, meta){
                    return '<input type="checkbox" data-attachment="'+full.attachment_id+'" ' +
                        'class="file-check"  name="employee_id_for_course_completion"' +
                        'value="' + $('<div/>').text(data).html() + '">';
                }
            },
            {
                'targets': 1,
                'searchable':false,
                'orderable':false,
                'visible':true,
                'render': function (data, type, full, meta){
                    return '<input type="number" ' +
                        'class="file-order-no remove-arrows-number align-center" ' +
                        'name="order" min="1" max="99" maxlength="2" ' +
                        'value="' + $('<div/>').text(data).html() + '">';
                }
            }
        ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
        columns: [
            {data: 'id', name: 'id'},
            {data: '', name: ''},
            {data: 'topic',
                name: 'topic',
                defaultContent: "--",
                className:"datatable-v-center topic-name"
            },
            {data: 'group', name: 'group',defaultContent: "--", className: "datatable-v-center"},
            {data: 'client_name', name: 'client_name',defaultContent: "--"},
            {data: null, name: 'description', className: "datatable-v-center",
                render: function(data) {
                    var description_str = data.description;
                    var description_str_clip = description_str;
                    if (description_str.length > 35) {
                        description_str =
                            '<span class="show-btn nowrap" ' +
                            'onclick="$(this).hide();$(this).next().show();">' +
                            description_str_clip.substr(0, 40) +
                            '..<a href="javascript:;" title="Expand" class="fa fa-chevron-circle-down cgl-font-blue"></a>' +
                            '</span>' +
                            '<span class = "notes big-notes" style="display:none" onclick="$(this).hide();$(this).prev().show();">' +
                            description_str + '&nbsp;&nbsp;' +
                            '<a href="javascript:;" title="Collapse" class="fa fa-chevron-circle-up cgl-font-blue"></a>' +
                            '</span><br/>\r\n';
                    }
                    return description_str;
                }
            },
            {data: null, name:'attachment_id',
                render: function(o) {
                    var url = '{{route('filedownload', [':attachment_id','post-order'])}}'
                    url = url.replace(':attachment_id', o.attachment_id);
                    return  '<a href="'+url+'" target="_blank">' +
                            '<i class="fa fa-download fa-lg" aria-hidden="true" style="margin: 15px;"></i>' +
                            '</a>';
                }
            },
            // {data: 'attachment_id', name: 'attachment_id',defaultContent: "--"},
            {data: 'created_user_full_name', name: 'created_user_full_name',defaultContent: "--"},
            {data: 'reviewed_user_full_name', name: 'reviewed_user_full_name',defaultContent: "--"},
            {data: 'reviewed_status', name: 'reviewed_status'},
            {
                data: null,
                sortable: false,
                render: function (o) {
                    var actions = '';
                    if(o.show_edit_button)
                    actions += '<a href="{{route("post-order.create.view", ["id?" => ""])}}/'+ o.id +'"  class="edit fa-2x  fa fa-pencil"></a>';
                    @can('approve_postorder')
                    actions += '  <a title="Approve/Reject" onclick="openModal(' +
                        o.id + ')" href="#" class="fa fa-podcast fa-2x link-ico"></a>';
                    @endcan
                    return actions;
                },
            }
        ]
    });

    $(".client-filter").change(function(){
            var table = $('#post-order-list-table').DataTable();
            table.ajax.reload();
        });

    $('#postorder-action-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var postorder_id = $('#postorder-action-form input[name="id"]').val()
        var url = "{{ route('post-order.update-status',':postorder_id') }}";
        url = url.replace(':postorder_id', postorder_id);
        var formData = new FormData($('#postorder-action-form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success) {
                    swal("Saved", "Status of the post order has been updated", "success");
                    $("#myModal").modal('hide');
                    table.ajax.reload();
                } else {
                    alert(data.message);
                }
            },
            fail: function (response) {
                alert('here');
            },
            error: function (xhr, textStatus, thrownError) {
                associate_errors(xhr.responseJSON.errors, $form);
            },
            contentType: false,
            processData: false,
        });
    });

    function getUrl() {
        let attOrderNumber = '';
        let attId = '';
        let attName = '';
        let attNameSeparator = '';
        let attTopicName = '';
        let fileObjStr;
        let fileArr = Array();
        let fileObj = {"module":"post-order"};
        if($(".file-check:checkbox:checked").length < 1) {
            swal({
                title: "Warning",
                text:  "No files selected",
                type: "warning"
            });
        } else {
            $.each($(".file-check:checkbox:checked"),function(i,v) {
                attOrderNumber = ($(v).closest('tr').find('.file-order-no').val());
                attNameSeparator = (attOrderNumber === '')? '' : '-';
                attId = $(v).data('attachment');
                attTopicName = $(v).closest('tr').find('.topic-name').text();
                attName = attOrderNumber+attNameSeparator+attTopicName;
                fileArr.push({"attachmentId":attId, "fileName":attName})
            });
            fileObj.files = fileArr;
            fileObjStr = btoa(JSON.stringify(fileObj));
            var url = '{{ route('filedownloadzip',":fileobj")}}';
            var url = url.replace(':fileobj', fileObjStr);
            window.open(url, '_blank');
        }
        return false;
    }
</script>
 <style type="text/css">
         .fa-2x{
        font-size:1.22em !important;
    };
    </style>
@stop
