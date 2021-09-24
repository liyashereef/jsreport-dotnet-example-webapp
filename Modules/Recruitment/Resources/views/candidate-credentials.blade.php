@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4 class="m-0">Candidates Credentials</h4>
</div>
@can('rec-create-candidate-credential')
<div class="add-new" data-title="Add new candidate credentials" data-toggle="modal" data-target="#myModal">Add <span class="add-new-label">New</span></div>
@endcan
    <table class="table table-bordered" id="candidates-credential-table">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>City</th>
                <th>Postal Code</th>
                <th>Email Address</th>
                <th>Phone</th>
                <th>Created Date</th>
                <th>Status</th>
                @canany(['rec-edit-candidate-credential', 'rec-delete-candidate-credential'])
                <th>Action</th>
                @endcan
            </tr>
        </thead>
    </table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add new candidate credentials</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'candidate-credential-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group col-sm-12" id="active">
                    <label class="switch" style="float:right;">
                      <input name="status" type="checkbox" checked>
                      <span class="slider round"></span>
                    </label>
                    <label style="float:right;padding-right: 5px;">Active</label>
                </div>

                <div class="form-group row mt-5" id="first_name">
                    <label for="first_name" class="col-sm-3 col-form-label">First Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('first_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="last_name">
                    <label for="last_name" class="col-sm-3 col-form-label">Last Name<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('last_name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id=city>
                    <label for="city" class="col-sm-3 col-form-label">City</label>
                    <div class="col-sm-9">
                        {{ Form::text('city',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="email">
                    <label for="email" class="col-sm-3 col-form-label">Email<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::email('email',"",array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                    <div class="col-sm-9">
                        {{ Form::tel('phone',null,array('class'=>'form-control phone')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group row" id="username">
                    <label for="username" class="col-sm-3 col-form-label">Username<span class="mandatory">*</span></label>
                    <div class="col-sm-9">
                        {{ Form::text('username',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password<span class="mandatory">*</span></label>
                    <div class="col-sm-7">
                        {{ Form::password('password',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div> --}}

                <div class="form-group row" id="email_script_block">
                    <label for="emailScript" class="col-sm-3 col-form-label">Email Script</label>
                    <div class="col-sm-9">
                         {{Form::textarea('emailScript',old('emailScript',@$mail_content['body']),array('class'=>'form-control ckeditor','id'=>'editor'))}}
                      {{--   {!! Form::textarea('emailScript',isset($mail_content)?($mail_content['body']):'',array('class'=>'form-control ckeditor','id'=>'editor')) !!} --}}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
<div class="modal fade" id="passwordModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'password-reset-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('candidate-id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="password_reset_mail">
                    <label for="emailScript" class="col-sm-3 col-form-label">Email Script</label>
                    <div class="col-sm-9">
                         {{Form::textarea('password_reset_mail',old('password_reset_mail',@$mail_content['body']),array('class'=>'form-control ckeditor','id'=>'editor1'))}}
                      {{--   {!! Form::textarea('emailScript',isset($mail_content)?($mail_content['body']):'',array('class'=>'form-control ckeditor','id'=>'editor')) !!} --}}
                        <small class="help-block"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{ Form::submit('Send', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@section('scripts')
<script>
$(function() {

    var table = $('#candidates-credential-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('recruitment.candidate-credentials.list') }}",
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    // exportOptions: {
                    //     @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                    //     columns: 'th:not(:last-child)',
                    //     @endcan
                    // }
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                /*{
                    extend: 'excelHtml5',
                    exportOptions: {
                        @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                        columns: 'th:not(:last-child)',
                        @endcan
                    }
                },*/
                {
                    extend: 'print',
                    pageSize: 'A2',
                    // exportOptions: {
                    //     @canany(['candidate-approval','edit-candidate','hr-tracking','hr-tracking-detailed-view','track_all_candidates'])
                    //     columns: 'th:not(:last-child)',
                    //     @endcan
                    //     stripHtml: false,
                    // }
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            "order": [[0, "asc"]],
            columns: [
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'postal_code',
                    name: 'postal_code'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    defaultContent: '--',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                @canany(['rec-edit-candidate-credential', 'rec-delete-candidate-credential'])
                {
                    data: null,
                    orderable: false,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('rec-edit-candidate-credential')
                        actions += '<a href="#" class="edit fa fa-pencil p-1" data-id=' + o.id + '></a>'
                        @endcan
                        @can('rec-delete-candidate-credential')
                        actions += '<a href="#" class="delete fa fa-trash-o p-1" data-id=' + o.id + '></a> ';
                        @endcan
                        actions += ' '
                        actions += ' <a href="#" class="fa fa-xl fa-lock password p-1" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
                @endcan
            ]
        });


    /* Save Candidate Credentials - Start*/
    @canany(['rec-create-candidate-credential', 'rec-edit-candidate-credential'])
    $('#candidate-credential-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
        var formData = new FormData($('#candidate-credential-form')[0]);
        if($('#candidate-credential-form input[name="id"]').val()){
            var message = 'User has been updated successfully';
        }else{
            var message = 'User has been created successfully';
        }
        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route("recruitment.candidate-credentials.store") }}',
            type: 'post',
            data: formData,
            success: function(data) {
                if (data.success) {
                    swal("Saved", message, "success");
                    $("#myModal").modal('hide');
                    if (table != null) {
                        table.ajax.reload();
                    }
                } else if (data.success == false) {
                    if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                        swal("Warning", data.message, "warning");
                    } else {
                        console.log(data);
                    }
                } else {
                    console.log(data);
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
    @endcan
    /* Save Candidate Credentials - End*/
     $("#candidates-credential-table").on("click", ".password", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.candidate-reset-password-show",":id") }}';
            var url = url.replace(':id', id);
            $('#password-reset-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                    CKEDITOR.instances['editor1'].setData(data.mail_content['body']);
                    $('#passwordModal input[name="candidate-id"]').val(data.candidate_id);
                    $("#passwordModal").modal();
                    $('#passwordModal .modal-title').text("Reset Password");
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
     $('#password-reset-form').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
        var formData = new FormData($('#password-reset-form')[0]);
        $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route("recruitment.candidate-reset-password-sendmail") }}',
            type: 'post',
            data: formData,
            success: function(data) {
                if (data.success) {
                    swal("Success", "Password reset mail successfully send", "success");
                    $( '#passwordModal').modal('hide')
                }
                 else {
                    console.log(data);
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
    /* Editing Candidate Credentials - Start */
    @can('rec-edit-candidate-credential')
    $("#candidates-credential-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("recruitment.candidate-credentials.single",":id") }}';
            var url = url.replace(':id', id);
            $('#candidate-credential-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                data: "id=" + id,
                success: function (data) {
                    if (data) {
                        if(data.status == 1) {
                            $('#myModal input[name="status"]').prop('checked', true);
                        } else {
                            $('#myModal input[name="status"]').prop('checked', false);
                        }
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="first_name"]').val(data.first_name);
                        $('#myModal input[name="last_name"]').val(data.last_name);
                        $('#myModal input[name="city"]').val(data.city);
                        $('#myModal input[name="postal_code"]').val(data.postal_code);
                        $('#myModal input[name="email"]').val(data.email);
                        $('#myModal input[name="phone"]').val(data.phone);
                        $('#myModal input[name="username"]').val(data.username);
                        $('#email_script_block').hide();
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit candidate credentials: " + data.first_name+' '+ data.last_name);
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
        @endcan
        /* Editing Candidate Credentials - End */

        /* Reset Form After Submit - Start */
        $('#myModal').on('hidden.bs.modal', function(){
            $('#myModal .modal-title').text("Add new candidate credentials");
            $(this).find('form')[0].reset();
            $('#candidate-credential-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        });
        /* Reset Form After Submit - End */

        /* Deleting Measurement Point Name - Start */
        @can('rec-delete-candidate-credential')
        $('#candidates-credential-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.candidate-credentials.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Candidate Credentials has been deleted successfully';
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action! Proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, remove",
                showLoaderOnConfirm: true,
                closeOnConfirm: false
            },
            function () {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        if (data.success) {
                            swal("Deleted", message, "success");
                            if (table != null) {
                                table.ajax.reload();
                            }
                        } else if (data.success == false) {
                            if (Object.prototype.hasOwnProperty.call(data, 'message') && data.message) {
                                swal("Warning", data.message, "warning");
                            } else {
                                swal("Warning", 'Data exists', "warning");
                            }
                        } else if (data.warning) {
                            swal("Warning", 'Competency exists for the category', "warning");
                        } else {
                            console.log(data);
                        }
                    },
                    error: function (xhr, textStatus, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                    },
                    contentType: false,
                    processData: false,
                });
            });

        });
        @endcan
        /* Deleting Measurement Point Name - End */
});
     $('.add-new').click(function(){
        $("input[name=id]").val('');
        $('#email_script_block').show();
        var emailScript = <?php echo json_encode($mail_content['body']) ?>;
        CKEDITOR.instances['editor'].setData(emailScript)

     });

    //Change username according to email address
    $("#myModal").on("input", "#email", function (e) {
          $('#myModal input[name="username"]').val($('#myModal input[name="email"]').val());
     });

</script>
<style>
#candidates-credential-table_wrapper{
    margin-top: 70px;
}

/* Toggle button in user creation - Start */
.switch {
    position: relative;
    display: inline-block;
    width: 47px;
    height: 20px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 13px;
    width: 13px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
}

input:checked+.slider {
    background-color: #003A63;
}

input:focus+.slider {
    box-shadow: 0 0 1px #003A63;
}

input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}
.fa-xl {
      font-size: 16px !important;
    }

/* Toggle button in user creation - End */
</style>
@stop
