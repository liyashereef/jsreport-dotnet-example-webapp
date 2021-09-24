@extends('layouts.app')
@section('content')
<div class="expense-send-statement-main">
    <div class="table_title">
        <h4> Expense Statements </h4>
    </div>


    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div class="row expense-send-statements">
        <nav class="col-lg-9 col-md-9 col-sm-8 ">
            <div class="nav nav-tabs expense" id="nav-tab" role="tablist">
                <a class="nav-item nav-link expense active" href="#">Expense Statements</a>
                <a class="nav-item nav-link expense" href="{{route('expense-statements-log.show')}}">Sent Statements
                    Log</a>
            </div>
        </nav>
    </div>
</div>
<div class="expense-send-statement">
    <div class="expense-recent">

        <div class="form-group row mx-0 d-flex align-items-center mail-form {{ $errors->has('user_id') ? 'has-error' : '' }}"
            id="user_id">
            <div class="align-items-center">
                <label for="user_id" class="">Employee</label>
            </div>
            <div class="col-sm-3">
                <select name="user_id" class="form-control select2" id="userid">
                    <option value="">Please Select</option>
                    @foreach ($userList as $key=>$user)
                    @php
                    $notExistingUserList=data_get($recentUserLists,'*.user_id');
                    @endphp
                    @if(!in_array($key,$notExistingUserList))
                    <option value="{{$key}}">{{$user}}</option>
                    @endif
                    @endforeach
                </select>
                <div class="form-control-feedback">
                    <span class="help-block text-danger align-middle font-12">
                    </span>{!! $errors->first('user_id', ':message') !!}
                </div>
            </div>
            <div id="button" class="button btn btn-primary blue submit add add_list" data-role="button">Add</div>
        </div>


        <section id="mail_sending_form"></section>

        @if(count($recentUserLists)>0)
        <h5>Recently Sent Mails</h5>
        @foreach ($recentUserLists as $recentUserList)
        <form id="form_{{$recentUserList->user->id}}" enctype="multipart/form-data">
            <div class="form-group row mx-0 d-flex align-items-center stat" id={{$recentUserList->user->id}}>
                <div class="align-items-center">
                    <label for="name" class="control-label mb-0" style="white-space: nowrap;">Name
                        <span class="mandatory" style="display:none;">*</span>
                    </label>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control" name="name" readonly id="name_{{$recentUserList->user->id}}"
                        value="{{$recentUserList->user->name.' '.$recentUserList->user->employee->employee_no}}">
                    <small class="help-block"></small>
                </div>&nbsp;&nbsp;
                {{-- @if($expenseSettingValue == 1) --}}
                <div class="align-items-center">
                    <label for="file_upload_id" class="control-label mb-0" style="white-space: nowrap;">
                        File Upload<span class="mandatory" style="display:none ;">*</span>
                    </label>
                </div>

                <div class="col-sm-3 ">
                    <input type="file" class="form-control" name="expense_send_statements"
                        id="expense_send_statements_{{$recentUserList->user->id}}" required="true">
                </div>
                <div class="form-control-feedback add_user">
                    <span class="help-block text-danger align-middle font-12">
                    </span>{!! $errors->first("file_upload_id", ":message") !!}
                </div>
                {{-- @endif --}}
                <div id="button_{{$recentUserList->user->id}}" class="button btn btn-primary blue submit addsend"
                    value={{$recentUserList->user->id}} data-role="button"
                    data-target="#form_{{$recentUserList->user->id}}">
                    Send Mail
                </div>
                <div id="{{$recentUserList->user->id}}" class="btn btn-primary ml-10 removeSendStatement">
                    Remove
                </div>
            </div>
            <input type="hidden" value="{{$recentUserList->user->id}}" name="user_id">
            <input type="hidden" value="{{$expenseSettingValue}}" name="expense_value">

        </form>
        @endforeach
        @endif
    </div>
</div>

@section('scripts')

<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script>

$('.select2').select2();
    var divId = 0;
        var userid;
        $('.mail-form').on('click', '.add_list' ,function(e) {
               var username = $('#userid option:selected').text();
                userid = $('#userid option:selected').val();
               
                var expenseSettingValue = {!! json_encode($expenseSettingValue)!!}
//console.log(expenseSettingValue == 1 );

                if ($('#userid').val() == "") {
                    alert("Please Select User")
                     e.preventDefault();
                           return false;
                }
                divId++;
               
                
                $expenseSendStatement = '<form id="form_'+userid+'" enctype="multipart/form-data">'+
                        '<div class="form-group row mx-0 d-flex align-items-center stat optionBox" id='+userid+'>'+
                        '<div class="align-items-center"> '+
                        '<label for="name" class="control-label mb-0" style="white-space: nowrap;">Name'+
                        '<span class="mandatory" style="display:none;">*</span>'+
                        '</label>'+
                        '</div>'+
                        '<div class="col-sm-3">'+
                        '<input type="text" class="form-control" name="name" readonly id="name_'+userid+'" value="'+username+'">'+
                        '<small class="help-block"></small>'+
                        '</div>&nbsp;&nbsp;'+
                        '<div class="align-items-center">'+
                        '<label for="file_upload_id" class="control-label mb-0" style="white-space: nowrap;">'+
                        'File Upload<span class="mandatory" style="display:none ;">*</span>'+
                        '</label>'+
                        '</div>'+
                        '<div class="col-sm-3 ">'+
                        '<input type="file" class="form-control" name="expense_send_statements" id="expense_send_statements_'+userid+'" required="true">'+
                        '</div>'+
                        '<div class="form-control-feedback add_user">'+
                        '<span class="help-block text-danger align-middle font-12">'+
                        '</span>{!! $errors->first("file_upload_id", ":message") !!}'+
                        '</div>'+
                        '<div id="button_'+userid+'" class="button btn btn-primary blue submit addsend" value='+userid+' data-role="button" data-target="#form_'+userid+'">Send Mail'+
                        '</div>'+
                        '<div class="remove btn btn-primary ml-10">Remove</div>'+
                        '</div>'+
                        '<input type="hidden" value="'+userid+'" name="user_id">'+
                        '<input type="hidden" value="'+expenseSettingValue+'" name="expense_value">'+
                        '</form>';
                        $('#mail_sending_form').append($expenseSendStatement);
                       

                        $('.optionBox').on('click','.remove',function() {
                            $(this).parent().remove();
                        });
                        
                 });

                 $(document.body).on('click', '.addsend' ,function(e){
                    
                    var buttonId = this.id;
                    var $form = $(this);
                    var formId = $(this).closest("form").attr('id');
                    jQuery.validator.setDefaults({
                            debug: true,
                            success: "valid"
                            });
                            $( '#'+formId ).validate({
                            rules: {
                                field: {
                                required: true,
                                extension: "jpeg|bmp|png|gif|svg|pdf"
                                }
                            }
                            });


                    e.preventDefault();
                    var dataTargeted = $(this).data('target');
                   
                    var forms = $(dataTargeted);
                   
                    let formData = forms.serializeArray();
                    var data = new FormData(forms[0]);
                  

                    $.ajax({
                        headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                        type: "POST",
                        url: "{{ route('expense-statements.store') }}",
                        contentType: false,
                        processData: false,
                        data: data,
                        success: function (data) {

                            if(data.success)
                            {
                                swal({
                                title: "Sent",
                                text: "Mail sent successfully",
                                type: "success"
                                },
                                function(){
                                    location.reload();
                                });
                           // swal("Sent", "Mail sent successfully", "success");
                            //$('#'+buttonId).removeClass('button btn btn-primary blue submit addsend');
                            //$('#'+buttonId).html("Mail sent successfully");
                            //$('#'+buttonId).css({ 'color': 'green'});
                            //$('#'+buttonId).html("Mail sent successfully");
                            } else {
                               //console.log(data.errors);
                            }

                            },
                            error: function(xhr, textStatus, thrownError) {
                                swal("Oops", xhr.responseJSON.errors.expense_send_statements, "warning");
                                //swal("Oops", "Please upload a file", "warning");
                             },
                            });
                            });


                $(document.body).on('click', '.removeSendStatement' ,function(e){
                             
                     var removeId = this.id;                  
                     var base_url = "{{ route('expense-statements.destroy',':id') }}";
                     var url = base_url.replace(':id', removeId);
                     var message = 'User has been removed from recent list successfully';
                     removeRecord(url, message);

                });

        function removeRecord(url,message) {
            var url = url;
                swal({
                    title: "Are you sure?",
                    text: "You will not be able to undo this action. Proceed?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, remove",
                    showLoaderOnConfirm: true,
                    closeOnConfirm: false,
                },
                function () {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function (data) {
                            if (data.success) {
                                swal({
                                title: "Removed",
                                text: message,
                                type: "success"
                                },
                                function(){
                                    location.reload();
                                });
                               // swal("Removed!", message, "success");
                               
                            } else if (data.success == false) {
                                swal("Warning", 'Data exists', "warning");
                            } else if (data.warning) {
                                swal("Warning", 'Competency exists for the category', "warning");
                            } else {
                                console.log(data);
                            }
                            //window.location.reload();
                        },
                        error: function (xhr, textStatus, thrownError) {
                            console.log(xhr.status);
                            console.log(thrownError);
                        },
                        contentType: false,
                        processData: false,
                    });
                    
                });
        }
</script>
@stop

<style>
    .add {
        background: #f36424 !important;
        border-color: #f36424 !important;
        height: max-content;
        width: 100px;
        font-weight: bold;
    }

    .addsend {
        background: #f36424 !important;
        border-color: #f36424 !important;
        height: max-content;
        width: 100px;
        font-weight: bold;
    }

    .expense-send-statement {
        /* margin-left: 45px; */
        /* border: 1px solid grey; */
        border-radius: 5px;
    }

    .expense-recent {
        margin-left: 1px;
        margin-top: 10px
    }

    .expense-send-statement-main {
        margin-left: 1px;
    }

    .expense-send-statements {
        /* margin-left: 32px; */
    }

    .ml-10 {
        margin-left: 10px;
    }
</style>
@stop