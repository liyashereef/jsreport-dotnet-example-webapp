@extends('layouts.cgl360_facility_scheduling_layout')

@section('css')

<style>
   .help-block {
        font-weight:bold;
    }
    .section-display{
        display:none;
    }
    .table-bordered th {
        font-size: 14px;
        color: #ffffff;
    }
    .table_title {
        /* margin: 0px; */
        font-family: Montserrat;
        font-weight: bold;
        font-size: 16pt;
        color: rgb(51,63,80);
    }
    .table_title h4 {
        margin: 0px 0px;
    }
    button{
        font-family: MicrosoftJhengHeiUI !important;
    }
</style>
@stop
@section('content')
<div class="container-fluid">
    <div class="row table_title">
        <h4 class="col-md-10"> Profile</h4>
        <div class="col-md-2 d-flex flex-row-reverse">
            <button class="btn btn-primary blue"  data-toggle="modal" data-target="#resetPasswordModal">Reset Password</button>
            <button class="btn btn-primary blue" style="margin-right: 7px;" id="editProfile">Edit Profile</button>
        </div>
    </div>

    <div class="box box-primary">
        {{ Form::open(array('id'=>'profile-update-form', 'class'=>'form-horizontal', 'method'=> 'POST')) }}
            <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">First Name <span class="mandatory section-display"> *</span></label>
                                <div class="input-group input-field">
                                    <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-user"></i></div>
                                    <input type="text" placeholder="First Name" class="form-control editable-fields" id="first_name"  name="first_name" readonly>
                                </div>
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="first_name_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="name">Last Name </label>
                                <div class="input-group input-field">
                                    <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-user-o"></i></div>
                                    <input type="text" placeholder="Last Name" class="form-control editable-fields" id="last_name" name="last_name" readonly>
                                </div>
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="last_name_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="name">Username</label>
                                <div class="input-group input-field">
                                    <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-user-plus"></i></div>
                                    <input type="text" class="form-control" id="username"  readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Email <span class="mandatory section-display"> *</span></label>
                                <div class="input-group input-field">
                                    <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-envelope"></i></div>
                                    <input type="email" placeholder="Email" name="email" id="email" class="form-control editable-fields"  readonly>
                                </div>
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="email_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Alternate Email </label>
                                <div class="input-group input-field">
                                    <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-envelope-o"></i></div>
                                    <input type="email" placeholder="Alternate Email" name="alternate_email" id="alternate_email" class="form-control editable-fields" readonly>
                                </div>
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="alternate_email_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                            <label for="name">Phone <span class="mandatory section-display"> *</span></label>
                                <div class="input-group input-field">
                                    <div class="input-group-addon" style="width: 2.6rem"><i class="fa fa-phone"></i></div>
                                    <input type="text" placeholder="Phone Number" class="form-control phone editable-fields has" id="phoneno"  name="phoneno" readonly>
                                </div>
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="phoneno_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="box-footer pull-right">
                {{ Form::submit('Submit', array('class'=>'button btn btn-primary blue','id'=>'saveProfile','style'=>'margin: 0px 0px 10px 0px;font-family: Montserrat;'))}}
            </div>
        {{ Form::close() }}
    </div>
</div>
<div class="container-fluid">
    <div class="table_title">
        <h4  style="margin-bottom: 20px; margin-top: 1%;"> Booking History</h4>
    </div>

    <table class="table table-bordered" id="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Facility</th>
                <th>Service</th>
                <th>Schedule Start</th>
                <th>Schedule End</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody id="tableBody">
        </tbody>
    </table>
</div>
   <!--Start-- password rest form  -->
   <div class="modal fade" id="resetPasswordModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myModalLabel">Reset Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    {{ Form::open(array('url'=>'#','id'=>'reset-password-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
                        <div class="modal-body">
                            <div class="form-group row" id="">
                                <label id="current_password_label" class="col-sm-3 control-label">Password </label>
                                <input type="password" placeholder="Current password" class="form-control" id="current_password" required  name="current_password">
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="current_password_error"></span>
                                </div>
                            </div>

                            <div class="form-group row" id="">
                                <label id="new_password_label" class="col-sm-3 control-label"> New Password</label>
                                <input type="password" placeholder="New password" class="form-control" id="new_password" required name="new_password">
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="new_password_error"></span>
                                </div>
                            </div>

                            <div class="form-group row" id="">
                                <label id="confirm_password_label" class="col-sm-3 control-label"> Confirm Password</label>
                                <input type="password" placeholder="Confirm password" class="form-control" id="confirm_password" required name="confirm_password">
                                <div class="form-control-feedback">
                                    <span class="help-block text-danger align-middle font-12" id="confirm_password_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            {{ Form::submit('Submit', array('class'=>'button btn btn-primary blue','id'=>'reset_password'))}}
                            {{ Form::reset('Cancel', array('class'=>'button btn btn-primary blue','id'=>'reset_password_cancel','data-dismiss'=>'modal'))}}
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
    </div>
    <!--End-- password rest form -->

</div>

@stop
@section('scripts')

<script>
    /*To accept phone number in a specific format*/
    $(".phone").mask("(999)999-9999");

    const profile = {
        ref:{
          user:[],
          bookedLists:[]
        },
        init(){
          this.getLoggedUserProfile();
          this.registerEventListeners();
          this.getBookedHistory();
        },
        registerEventListeners() {

            let root = this;
            //Load default actions.
            $(".editable-fields").attr("readonly", true);
            $('.box-footer').hide();

            /** On clicking `Edit Profile` button
               *Remove readonly attribute to textbox in profile edit form.
               *Except username.
               *Show profile form `Save` and `Cancel` buttons
               *Hide `Edit Profile` button.
            */
            $("body").on("click", "#editProfile", function(){
                $(".editable-fields").attr("readonly", false);
                $('.box-footer').show();
                $('#editProfile').hide();
                $('.mandatory').removeClass('section-display');
                $('.help-block').text('');
            });

           //Facility details submitting
           $('#profile-update-form').submit(function (e){
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();
                let url = "{{ route('facility.profile-update') }}";
                $('.help-block').text('');
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    // content: {
                    //     element: "input",
                    //     attributes: {
                    //         placeholder: "Type your password",
                    //         type: "password",
                    //     },
                    // },
                    }).then((value) => {
                        //swal button clik on `OK` and password is null.
                        // if (value === '') {
                        //     swal({
                        //         text: "You need to enter your password",
                        //         icon: "warning",
                        //          buttons: {
                        //             confirm: true,
                        //         }
                        //     }).then((value) => {
                        //         $("#saveProfile").click();
                        //     });
                        // }
                        //swal button clik on `OK` and password provided.
                        if(value != null){
                            // formData.push({name: 'password', value: value});
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: url,
                                type: 'POST',
                                data: formData,
                                success: function (data) {
                                    if (data.success) {
                                        swal({
                                            title: "Success",
                                            text : 'Profile successfully updated',
                                            icon: "success",
                                            confirmButtonText: "OK",
                                        });
                                        root.getLoggedUserProfile();
                                    }else{
                                        if(data.message != null){
                                            swal({
                                                    title: "Try Again",
                                                    text: data.message,
                                                    icon: "warning",
                                                    confirmButtonText: "OK",
                                                });
                                        }
                                    }
                                },
                                error: function (xhr, textStatus, thrownError) {
                                    if(xhr.status === 401) {
                                        window.location = "{{ route('facility.login') }}";
                                    }
                                    var errors = xhr.responseJSON.errors;
                                    $.each(errors, function (key, value) {
                                        let idValue = '#'+key+'_error';
                                        $(idValue).text(value[0])
                                    });
                                }
                            });
                        }
                });
            });

            $('#reset-password-form').submit(function (e){
                $('.help-block').text('');
                e.preventDefault();
                let $form = $(this);
                let formData = $(this).serializeArray();

                let new_password = $('#new_password').val();
                let confirm_password = $('#confirm_password').val();

                if(new_password == confirm_password){
                    $.ajax({
                        url: "{{route('facility.profile-password-reset')}}",
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        success: function(data) {

                            if(data.success){
                                swal({
                                    title: "Successful",
                                    text : 'Password successfully updated.',
                                    icon: "success",
                                    confirmButtonText: "OK",
                                });
                                $('#reset-password-form')[0].reset();
                                $("#resetPasswordModal").modal("hide");
                            }else{
                                if(data.message != null){
                                    swal({
                                        title: "Try Again",
                                        text: data.message,
                                        icon: "warning",
                                        confirmButtonText: "OK",
                                    });
                                 }
                            }


                        },
                        error: function (xhr, textStatus, thrownError) {
                            if(xhr.status === 401) {
                                window.location = "{{ route('facility.login') }}";
                            }
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                let idValue = '#'+key+'_error';
                                $(idValue).text(value[0])
                            });
                        }
                    });
                }else{
                    $('#confirm_password_error').html('New and confirm passwords do not match');
                }
            });

            $("body").on("click", "#reset_password_cancel", function(){
                $('#reset-password-form')[0].reset();
            });

        },
        getLoggedUserProfile(){
            let root = this;
            $.ajax({
                url: "{{route('facility.logged.user.profile')}}",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                success: function(data) {
                    root.ref.user = data;
                    root.setLoggedUserProfile();

                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('facility.login') }}";
                    }
                }
            });
        },
        setLoggedUserProfile(){
            let root = this;
            //All defalut deatils to text box.
            $('#first_name').val(root.ref.user.first_name);
            $('#last_name').val(root.ref.user.last_name);
            $('#email').val(root.ref.user.email);
            $('#alternate_email').val(root.ref.user.alternate_email);
            $('#phoneno').val(root.ref.user.phoneno);
            $('#username').val(root.ref.user.username);

            $(".editable-fields").attr("readonly", true);
            $('.box-footer').hide();
            $('#editProfile').show();
            $('.mandatory').addClass('section-display');
            $('.help-block').text('');
        },
        getBookedHistory(){
            let root = this;
            $.ajax({
                url: "{{route('facility.user.booking-history')}}",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                success: function(data) {
                    root.ref.bookedLists = data;
                    let tableRows = '';
                    $.each(root.ref.bookedLists, function(index, value) {
                        let serviceName = '';
                        if(value.service_name !=null){
                            serviceName = value.service_name;
                        }
                        // console.log(value.created_at.date);
                        tableRows += `<tr>
                                        <td class="">${index+1}</td>
                                        <td class="">${value.facility_name}</td>
                                        <td class="">${serviceName}</td>
                                        <td class="" data-order="${value.booking_date_start}">${moment(value.booking_date_start).format('MMMM D, YYYY h:mm A')}</td>
                                        <td class="" data-order="${value.booking_date_end}">${moment(value.booking_date_end).format('MMMM D, YYYY h:mm A')}</td>
                                        <td class="" data-order="${value.created_at.date}">${moment(value.created_at.date).format('MMMM D, YYYY')}</td>
                                        `;
                        tableRows += `</tr>`;
                    });

                    $('#tableBody').html(tableRows).after(function(e){
                        root.initDataTable();
                    });
                },
                error: function(xhr, textStatus, thrownError) {
                    if(xhr.status === 401) {
                        window.location = "{{ route('facility.login') }}";
                    }
                }
            });
        },
        initDataTable(){
            let root = this;
            var screenheight = screen.height;
            try{
                root.ref.dataTable = $('#table').DataTable({
                    // dom: 'Blfrtip',
                    // buttons: [
                    //     {
                    //         extend: 'excelHtml5',
                    //         text: '  Excel',
                    //     },
                    // ],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, -1],
                        [10, 25, 50, 100, 500, "All"]
                    ],
                    destroy: true,
                });
            } catch(e){
                console.log(e.stack);
            }
        },

    };

    // Code to run when the document is ready.
    $(function() {
        profile.init();
    });


</script>
@stop
