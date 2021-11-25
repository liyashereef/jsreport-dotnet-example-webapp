@extends('adminlte::page')
@section('title', 'Visitor Screening Templates')
@section('content_header')
<h1>Visitor Screening Templates</h1>
@stop @section('content')
<style>
    .view{
        padding-right: 8%;
    }
    .dataTable a.edit, .dataTable .edit-disable {
        padding-right: 0%;
    }
</style>

<button class="add-new"  onclick="addnew()" data-title="Add New Visitor Screening Templates" >
    Add<span class="add-new-label">New</span>
</button>

<table class="table table-bordered" id="feedback-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
            <th>Customer</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>

            {{ Form::open(array('url'=>'#','id'=>'feedback-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
             {{ Form::hidden('id', null) }}
            <div class="modal-body">

                <div class="form-group row" id="name">
                    <label for="name" class="col-sm-3 control-label">Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control','required'=>'required')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="customer_id">
                    <label for="customer_id" class="col-sm-3 control-label">Customer</label>
                    <div class="col-sm-9">
                        {!!Form::select('customer_id[]',$customers,null, ['class' => 'form-control','id'=>'customerIds','multiple'=>"multiple",'style'=>'width: 591px;'])!!}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>

                <div class="form-group row" id="selectedOffices">
                </div>

            </div>

            <div class="modal-footer" style="text-align: right !important;">
            {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'mdl_save_change'))}}
                {{ Form::submit('Cancel',array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>

        </div>
    </div>
</div>

@stop @section('js')
<script>
     $('#customerIds').select2();
     $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#feedback-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
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
                        emailContent(table, 'Experiences Lookups');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('visitor-log-screening-templates-list') }}",
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
                    sortable:false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                        data: null,
                        orderable: false,
                        render: function (o) {
                           var customerList = "";
                           if(o.visitor_log_screening_template_customer_allocation){

                                $.each(o.visitor_log_screening_template_customer_allocation, function(key,value){
                                    customerList += value.customer.client_name_and_number+' , ';
                                });
                           }

                            return customerList.replace(/,\s*$/, "");
                        },
                    },
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                         var actions = '';
                         var template_question_url = '{{ route("visitor-log-screening-templates.questions",":id") }}';
                         var template_question_url = template_question_url.replace(':id', o.id);

                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + ' style="margin: 10px;"></a>';
                        actions += '<a href="'+template_question_url+'" class="fa fa-question-circle" data-id=' + o.id + '></a>'

                     return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Experience Save - Start*/
        $('#feedback-form').submit(function (e) {
            e.preventDefault();
            if($('#feedback-form input[name="id"]').val()){
                var message = 'Visitor screening template has been updated successfully';
            }else{
                var message = 'Visitor screening template has been created successfully';
            }
            formSubmit($('#feedback-form'), "{{ route('visitor-log-screening-templates.store') }}", table, e, message);
        });
        /* Experience Save - End*/


        /* Experience Edit - Start*/
        $("#feedback-table").on("click", ".edit", function (e) {
            $("#myModal #customerIds").val('').trigger('change');
            var id = $(this).data('id');
            var url = '{{ route("visitor-log-screening-templates.single",":id") }}';
            var url = url.replace(':id', id);
            $('#feedback-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    console.log('data', data);
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="name"]').val(data.name)
                        $('#myModal textarea[name="description"]').text(data.description);
                        var selectedOffices = '';
                        if(data.visitor_log_screening_template_customer_allocation){
                            selectedOffices += `<h4 class="" id="" style="margin-left: 6%;">Allocated Customers</h4> <hr>`;
                            selectedOffices += `<table  class="table table-bordered" id="selectedOfficesTable" style="width: 90%;margin-left: 5%;">`;
                                selectedOffices += `<tr>`;
                                selectedOffices += `<th>Customers</th>`;
                                selectedOffices += `<th> Action </th>`;
                                selectedOffices += `</tr>`;
                            $.each(data.visitor_log_screening_template_customer_allocation, function(key,value){
                                selectedOffices += `<tr id="`+value.id+`">`;
                                selectedOffices += `<td>`+value.customer.client_name_and_number+`</td>`;
                                selectedOffices += `<td> <a href="#" class="deleteOffice fa fa-trash-o" ' onClick=deleteOfficeAllocation(`+value.id+`);></a> </td>`;
                                selectedOffices += `</tr>`;
                            });
                            selectedOffices += `</table>`;
                        }
                        $('#selectedOffices').html(selectedOffices);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Visitor Screening Templates: " + data.name)
                    } else {
                        // alert(data);

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
        /* Experience Edit - End*/

         $('#feedback-table').on('click', '.delete', function (e) {
            id = $(this).data('id');
             var base_url = "{{ route('visitor-log-screening-templates.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action",
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
                            swal("Deleted", "Visitor templates and allocation has been deleted successfully", "success");
                            table.ajax.reload();
                        }
                     else{
                            swal("Warning", "Delete failed. Try again", "warning");
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
        });

    });

    function addnew(data=null) {

        $("#myModal").modal();
        $("#feedback-form")[0].reset();
        $('#myModal input[name="id"]').val('');
        $('#myModal textarea[name="description"]').text('');
        $("#myModal #customerIds").val('').trigger('change');
        $('#feedback-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
        $('#selectedOffices').html('');

    }
    function deleteOfficeAllocation(id){
             var base_url = "{{ route('visitor-log-screening-templates.office-allocation.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            swal({
                title: "Are you sure?",
                text: "You will not be able to undo this action",
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
                            swal("Deleted", "Allocation has been deleted successfully", "success");
                            $('#selectedOfficesTable #'+id).remove();
                        }
                     else {
                              swal("Warning", "Delete failed. Try again", "warning");
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
    }
</script>
@stop
