@extends('adminlte::page')
@section('title', config('app.name', 'Laravel').'-Training Policy')
@section('content_header')

<h1>Policy Dashboard</h1>
@stop
@section('content')
<div class="add-new" data-title="Add New Policy">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Reference Code</th>
            <th width="15%">Policy Name</th>
            <th width="15%">Category</th>
            <th width="30%">Description</th>
            <th width="5%">Content</th>
            <th width="5%">Active</th>
            <th width="5%">Broadcast</th>
            <th width="5%">Actions</th>
        </tr>
    </thead>
</table>
<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            {{ Form::hidden('reference_code',null) }}
            <div class="modal-body">
                <div class="form-group" id="policy_name">
                    <label for="policy_name" class="col-sm-3 control-label">Policy Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('policy_name',null,array('class' => 'form-control','required'=>TRUE)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="compliance_policy_category_id">
                    <label for="compliance_policy_category_id" class="col-sm-3 control-label">Category</label>
                    <div class="col-sm-9">
                        {{ Form::select('compliance_policy_category_id', [null=>'Please Select'] + $categoryList, null,
                        ['class' => 'form-control','id'=>'compliance_policy_category_id']) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="policy_description">
                    <label for="policy_description" class="col-sm-3 control-label">Policy Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('policy_description',null,array('class' => 'form-control','required'=>TRUE))
                        }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="policy_objectives">
                    <label for="policy_objectives" class="col-sm-3 control-label">Policy Objective</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('policy_objectives',null,array('class' => 'form-control','required'=>TRUE))
                        }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group" id="policy_file">
                    <label for="policy_file" class="col-sm-3 control-label">Upload PDF</label>
                    <div class="col-sm-9">
                        {{ Form::file('policy_file') }}
                        <label id="policy_file_name" ></label>
                        <style>
                            .progress {
                                position: relative;
                                width: 100%;
                                padding: 1px;
                                border-radius: 3px;
                                margin-top: 1%
                            }

                            .bar {
                                background-color: #F48452;
                                width: 0%;
                                height: 25px;
                                border-radius: 3px;
                            }

                            .percent {
                                position: absolute;
                                display: inline-block;
                                top: 1%;
                                left: 48%;
                                color: #003A63;
                            }
                        </style>
                        <small class="help-block"></small>
                        <div class="progress">
                            <div class="bar"></div>
                            <div class="percent">0%</div>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="compliance_policy_roles">
                    <label for="compliance_policy_roles" class="col-sm-3 control-label">Roles</label>
                    <div class="col-sm-9">

                        <select class="form-control" placeholder="Please Select" name="compliance_policy_roles[]" id="compliance_roles">
                            <option value="all_roles">All Roles</option>
                            @foreach ($complianceRoles as $key => $roles)
                                <option value="{{$key}}">{{$roles}}</option>
                            @endforeach
                        </select>
                        <small class="help-block"></small>
                    </div>
                </div>


                <div class="form-group" id="enable_agree_or_disagree">
                    <label for="enable_agree_or_disagree" class="col-sm-3 control-label">Enable Agree/Disagree</label>
                    <div class="col-sm-9">
                        {{ Form::checkbox('enable_agree_or_disagree', 1) }}
                        <small class="help-block"></small>
                    </div>
                </div>


                <div id="agree_disagree_div" style="display:none;">
                    <div class="form-group" id="agree_reasons">
                        <label for="agree_reasons" class="col-sm-3 control-label">If Agree</label>
                        <div class="col-sm-9">
                            {{ Form::text('agree_reasons', null,['class' =>"tagsinput"]) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="enable_agree_or_disagree">
                        <label for="enable_agree_or_disagree" class="col-sm-3 control-label">Enable Agree Textbox</label>
                        <div class="col-sm-9">
                            {{ Form::checkbox('enable_agree_textbox', 1) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="disagree_reasons">
                        <label for="disagree_reasons" class="col-sm-3 control-label">If Disagree</label>
                        <div class="col-sm-9">
                            {{ Form::text('disagree_reasons',null, [ 'class' =>"tagsinput"]) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
                    <div class="form-group" id="enable_disagree_textbox">
                        <label for="enable_disagree_textbox" class="col-sm-3 control-label">Enable Disagree Textbox</label>
                        <div class="col-sm-9">
                            {{ Form::checkbox('enable_disagree_textbox', 1) }}
                            <small class="help-block"></small>
                        </div>
                    </div>

                </div>

                <div class="form-group" id="status">
                    <label for="status" class="col-sm-3 control-label">Active</label>
                    <div class="col-sm-9">
                        {{ Form::checkbox('status', 1) }}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop @section('js')
<style type="text/css">
    .disabled {
    color: #999;
}
.no-click {
    pointer-events: none;
}
</style>
<script>

    $(function () {

         /* Solved tag label lengthy issue */
         $('div.bootstrap-tagsinput').css('overflow','auto');

        $('#compliance_roles').select2({
            multiple: true,
            width: '100%',
            placeholder: 'Please Select'
        });

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                        extend: 'pdfHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-pdf-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: ' ',
                        className: 'btn btn-primary fa fa-file-excel-o',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'print',
                        text: ' ',
                        className: 'btn btn-primary fa fa-print',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        text: ' ',
                        className: 'btn btn-primary fa fa-envelope-o',
                        action: function (e, dt, node, conf) {
                            emailContent(table, 'Policy');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('policy.list') }}",
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
                columns: [{
                        data: 'DT_RowIndex',
                        name: '',
                        sortable: false
                    },
                    {
                        data: 'reference_code',
                        name: 'reference_code'
                    },
                    {
                        data: 'policy_name',
                        name: 'policy_name'
                    },
                    {
                        data: 'category.compliance_policy_category',
                        name: 'category.compliance_policy_category'
                    },
                    {
                        data: 'policy_description',
                        name: 'policy_description'
                    },
                    {
                        data: 'policy_file',
                        name: 'policy_file'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (status){
                            return '<input disabled type="checkbox" ' + (Number(status)>0?"checked":"") + '>';
                        }
                    },
                    {
                        data: null,
                        name: 'broadcast-btn',
                        sortable: false,
                        render: function (data){
                            return (Number(data.is_broadcasted)==0)?'<button class="broad-cast-button button btn btn-primary blue" id="'+data.id+'">Broadcast</button>':'<button class="broad-cast-button button btn no-click" id="'+data.id+'">Broadcast</button>';
                        }
                    },
                    {
                        data: null,
                        sortable: false,
                        render: function (o) {
                            var actions = '';
                            @can('edit_masters')
                            if(o.policy_accept_count >0  &&  o.is_broadcasted==1)
                            {
                                actions += '<a href="#" class="edit fa fa-eye" data-id=' + o.id +' data-view="1"></a>'
                            } else {
                                actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id +
                                '></a>'
                            }
                            @endcan
                            @can('lookup-remove-entries')
                            actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id +
                                '></a>';
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }



        /* Policy Category Save - Start */
        var bar = $('.bar');
        var percent = $('.percent');
        $('#form').submit(function (e) {
            e.preventDefault();
            var $form = $('#form');
            var formData = new FormData($form[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    var percentVal = '0%';
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            bar.width(percentComplete + '%')
                            percent.html(percentComplete + '%');
                            if (percentComplete === 100) {
                                console.log('completed');
                            }
                        }
                    }, false);

                    return xhr;
                },
                url: "{{ route('policy.store') }}",
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                        console.log(data);
                        $('#myModal input[name="enable_agree_or_disagree"]').prop("checked", false);
                        $('#agree_disagree_div').hide();
                        $('#agree_disagree_div').find('.tagsinput').tagsinput('removeAll');

                        if(data.result.created == false){
                            swal("Saved", "Policy has been updated successfully \n Reference code is " +
                            data.result.reference_code, "success");
                        }else{
                            swal("Saved", "Policy has been created successfully \n Reference code is " +
                            data.result.reference_code, "success");
                        }
                        $("#form")[0].reset();
                        bar.width('0%')
                        percent.html('0%');
                        $("#myModal").modal('hide');
                        if (table != null) {
                            table.ajax.reload();
                        }
                    } else {
                        console.log('else', data);
                    }
                },
                fail: function (response) {
                    console.log(response);
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                    bar.width('0%')
                    percent.html('0%');
                },
                contentType: false,
                processData: false,
            });
        });
        /* Policy Category Save- End */
        $('select#compliance_policy_category_id').select2({
            dropdownParent: $("#myModal"),
            placeholder :'Please Select',
            width: '100%'
            });
        /* Policy Category Edit - Start */
        $("#table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var id_view = $(this).data('view');
            var url = '{{ route("policy.single",":id") }}';
            var url = url.replace(':id', id);
            $('#form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {

                        if(id_view != 1){ // remove id if the compliance cannot be edited
                            $('#myModal input[name="id"]').val(data.id)
                        }
                        $('#myModal input[name="policy_name"]').val(data.policy_name)
                        $('#myModal input[name="reference_code"]').val(data.reference_code)
                        $('#myModal textarea[name="policy_description"]').val(data.policy_description)
                        $('#myModal textarea[name="policy_objectives"]').val(data.policy_objectives)
                        $('#myModal select[name="compliance_policy_category_id"]').val(data.compliance_policy_category_id)
                        /***Setting selcted values in select2 mutiple - Begin ****/

                            var role = [];
                            var count=0;
                            if(data.roles)
                            {
                                $.each(data.roles,function(key , item){
                                    count++;
                                    role.push(item.role);
                                });
                            }
                            val_role = (($('#compliance_roles option').length-1) != count) ? role : 'all_roles';
                            $('#compliance_roles').val(val_role).trigger('change');

                        /***Setting selcted values in select2 mutiple - End ****/

                        if (data.policy_file != null) {
                            //$('#myModal file[name="policy_file"]').val(data.policy_file)
                            $('#myModal #policy_file_name').text(data.policy_file)
                            $('#myModal #policy_file_name').css('font-weight',500)
                        }
                        if (data.status) {
                            $('#myModal input[name="status"]').prop("checked", true);
                        } else {
                            $('#myModal input[name="status"]').prop("checked", false);
                        }
                        if(data.enable_agree_or_disagree)
                        {
                            $('#myModal input[name="enable_agree_or_disagree"]').prop("checked", true);
                            $('#agree_disagree_div').show();
                            agree_checked = (data.enable_agree_textbox) ? true :false;
                            disagree_checked = (data.enable_disagree_textbox) ? true :false;
                            $('#myModal input[name="enable_agree_textbox"]').prop("checked", agree_checked);
                            $('#myModal input[name="enable_disagree_textbox"]').prop("checked", disagree_checked);
                            if((data.agree_disagree_reasons))
                            {
                                $('#agree_disagree_div').find('.tagsinput').tagsinput('removeAll');

                                $.each(data.agree_disagree_reasons,function(item,value){
                                   // console.log(value);
                                   if(value.agree_or_disagree === 1)
                                   {
                                    $('input[name="agree_reasons"]').tagsinput('add', value.reason);
                                   }else{
                                    $('input[name="disagree_reasons"]').tagsinput('add', value.reason);
                                   }

                                });

                            }
                            //$('.bootstrap-tagsinput input').tagsinput('refresh');

                        }else{
                            $('#myModal input[name="enable_agree_or_disagree"]').prop("checked", false);
                            $('#agree_disagree_div').hide();
                            $('#agree_disagree_div').find('.tagsinput').tagsinput('removeAll');
                            $('#agree_disagree_div').find('input:checkbox').prop('checked',false);

                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Policy: " + data.policy_name);
                        if(id_view == 1) { // hide submit if the compliance cannot be edited
                            $('#mdl_save_change').hide();
                        } else {
                            $('#mdl_save_change').show();
                        }
                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Policy Category Edit - End */

        /* Policy Category Delete - Start */
        $('#table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('policy.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Policy has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Policy Category Delete- End */
        /**
        * Broad cast call
        **/

        $('#table').on('click', '.broad-cast-button',function(){
            var data = table.row( $(this).parents('tr') ).data();
            var url = '{{ route("policy.broadcast") }}';
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:url,
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.success) {
                        swal({title: "Mail Sent", text: "Mail to all employees have been sent", type: "success"},
                         function(){
                        location.reload();
                    }
                    );


                    } else {
                        alert(data);
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },

            });
        });



        /* Clear Uploaded File label - Start */
        $('.add-new').click(function(){
           $('#mdl_save_change').show();
           $('#policy_file_name').text('');
           $('#agree_disagree_div').hide();
           $('#agree_disagree_div').find('.tagsinput').tagsinput('removeAll');
           $('.bootstrap-tagsinput input').attr('size','');
           $('#compliance_roles').val("").change();
        });
        /* Clear Uploaded File label - End */

        /* Show/Hide agree div - Start */
        $('input[name=enable_agree_or_disagree]').click(function(){

         if( $(this).prop("checked"))
         {
            $('#agree_disagree_div').show();
            /* Solved tag label lengthy issue */
        $('div.bootstrap-tagsinput').css('overflow','auto');

         }else{
            $('#agree_disagree_div').hide();
            $('#agree_disagree_div').find('.tagsinput').tagsinput('removeAll');
            $('.bootstrap-tagsinput input').attr('size','');


         }

        });
        /* Show/Hide agree div - End*/








    });

</script>
@stop
