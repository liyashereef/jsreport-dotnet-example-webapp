@extends('adminlte::page')

@section('title', 'Email Accounts Master')

@section('content_header')

<h1>Email Accounts Master</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Email Account">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="email-account-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Display Name</th>
            <th>Email Address</th>
            <th>Username</th>
            <th>SMTP Server</th>
            <th>Port</th>
            <th>Encryption</th>
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Customers Shift</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'email-accounts-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">

                <div class="form-group row" id="display_name" >
                       <label for="display_name" class="col-sm-3">Display name<span class="mandatory">*</span></label>
                       <div class="col-sm-9">
                       <input type="text" class="form-control has-error" name="display_name"  placeholder="Display name" value="" />
                      <small class="help-block"></small>
                       </div>
                </div>

                <div class="form-group row" id="email_address" >
                       <label for="email_address" class="col-sm-3 control-label" style="text-align: left;">Email Address<span class="mandatory">*</span></label>
                       <div class="col-sm-9">
                       <input type="email" class="form-control has-error" name="email_address"  placeholder="Email Address" value="" />
                      <small class="help-block"></small>
                       </div>
                </div>
                <div class="form-group row" id="user_name" >
                       <label for="user_name" class="col-sm-3 control-label" style="text-align: left;">Username<span class="mandatory">*</span></label>
                       <div class="col-sm-9">
                       <input type="text" class="form-control has-error" name="user_name"  placeholder="Username" value="" />
                      <small class="help-block"></small>
                       </div>
                </div>
                <div class="form-group row" id="password" >
                       <label for="password" class="col-sm-3 control-label" style="text-align: left;">Password<span class="mandatory">*</span></label>
                       <div class="col-sm-9">
                       <input type="password" class="form-control has-error" name="password"  placeholder="Password" value="" />
                      <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="smtp_server" >
                       <label for="smtp_server" class="col-sm-3 control-label" style="text-align: left;">SMTP Server<span class="mandatory">*</span></label>
                       <div class="col-sm-9">
                       <input type="text" class="form-control has-error" name="smtp_server"  placeholder="SMTP Server" value="" />
                      <small class="help-block"></small>
                       </div>
                </div>
                <div class="form-group row" id="port" >
                       <label for="port" class="col-sm-3 control-label" style="text-align: left;">Port<span class="mandatory">*</span></label>
                       <div class="col-sm-9">
                       <input type="text" class="form-control has-error" name="port"  placeholder="Port" value="" />
                      <small class="help-block"></small>
                       </div>
                </div>
                <div class="form-group row" id="encryption" >
                        <label for="encryption" class="col-sm-3 control-label" style="text-align: left;">Encryption<span class="mandatory">*</span></label>
                        <div class="col-sm-9">
                        <select name="encryption" id="encryption" class="select2 form-control">
                        <option value="" disabled selected>Select</option>
                            <option value="0">None</option>
                            <option value="1">SSL</option>
                            <option value="2">TLS</option>
                        </select>
                        <small class="help-block"></small>
                        </div>
                </div>
                <div class="form-group row" id="default" style="display:none;">
                       <label for="default" class="col-sm-3 control-label" style="text-align: left;">Default</label>
                       <div class="col-sm-9">
                       <input type="checkbox" name="default"  value="1">
                      <small class="help-block"></small>
                      </div>
                </div>

            </div>
          <div class="modal-footer">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
            {{ Form::reset('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
          </div>
        {{ Form::close() }}
    </div>
</div>
</div>
@stop
@section('js')
<script>

    $(function () {

        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#email-account-table').DataTable({
                dom: 'lfrtBip',
                bProcessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function (xlsx) {
                      var sheet = xlsx.xl.worksheets['sheet1.xml'];
                      var col = $('col', sheet);
                      $(col[1]).attr('width', 40);
                  }

              },
              {
                extend: 'print',
                text: ' ',
                className: 'btn btn-primary fa fa-print',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                text: ' ',
                className: 'btn btn-primary fa fa-envelope-o',
                action: function (e, dt, node, conf) {
                    emailContent(table, 'Customer Shift');
                }
            }
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: {
                "url":'{{ route('email-accounts.list') }}',
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            columns: [
            {data: 'DT_RowIndex', name: '',sortable:false},
            {
                data:'display_name',
                name:'display_name'
            },
            {data: 'email_address', name: 'email_address'},
            {data: 'user_name', name: 'user_name'},
            {data: 'smtp_server', name: 'smtp_server'},
            {data: 'port', name: 'port'},
            {data: 'encryption', name: 'encryption'},
            {data: null,
                orderable:false,
                render: function (a) {
                    var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + a.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + a.id + '></a>';
                        @endcan
                        return actions;
                },
            }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }


        $('#email-accounts-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            $form.find('.form-group').removeClass('has-error').find('.help-block').text('');

            var formData = new FormData($('#email-accounts-form')[0]);
            var url= "{{ route('email-accounts.store') }}";

                $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,

                success: function (data) {
                    if (data.success) {
                    if($('#email-accounts-form input[name="id"]').val()){
                        var title = 'Updated';
                        var message = 'Email account details has been updated successfully';
                    }else{
                        var title = 'Created';
                        var message = 'Email account details has been created successfully';
                    }
                     swal({
                          title: title,
                          text: message,
                          type: "success",
                          confirmButtonText: "OK",
                        },function(){
                            $('.form-group').removeClass('has-error').find('.help-block').text('');
                           location.reload();
                        });

                    } else {
                        console.log(data);
                        swal("Oops", "Invalid Credentials", "warning");
                    }
                },
                fail: function (response) {
                    console.log(response);
                    swal("Oops", "Something went wrong", "warning");
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    associate_errors(xhr.responseJSON.errors, $form);
                    swal("Oops", "Invalid Credentials", "warning");
                },
                contentType: false,
                processData: false,
            });
        });


        /*Edit Customer Shift - Start*/
        $("#email-account-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("email-account.single",":id") }}';
            var url = url.replace(':id', id);
            $('#email-accounts-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        console.log(data);
                        $('#myModal input[name="id"]').val(data.id)
                        if (data.default == 1) {
                            $('#myModal input[name="default"]').prop("checked", true);
                        } else {
                            $('#myModal input[name="default"]').prop("checked", false);
                        }
                        $('#myModal select[name="encryption"] option[value="'+data.encryption+'"]').prop('selected',true);
                        $('#myModal input[name="display_name"]').val(data.display_name)
                        $('#myModal input[name="email_address"]').val(data.email_address)
                        $('#myModal input[name="user_name"]').val(data.user_name)
                        $('#myModal input[name="password"]').val(data.password)
                        $('#myModal input[name="smtp_server"]').val(data.smtp_server)
                        $('#myModal input[name="port"]').val(data.port)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Email Account: " + data.display_name);

                    } else {
                        console.log(data);
                        swal("Oops", "Could not save data", "warning");
                    }
                },
                error: function (xhr, textStatus, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    swal("Oops", "Something went wrong", "warning");
                },
                contentType: false,
                processData: false,
            });
        });
        /* Edit Customer Shift - End*/


        /* Customer Shift Delete  - Start */
        $('#email-account-table').on('click', '.delete', function (e) {
            e.preventDefault();
            var table = $('#email-account-table').DataTable();
            var id = $(this).data('id');
            var base_url ="{{ route('email-account.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Email account details has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Customer Shift Delete  - End */
    });

</script>
@endsection

