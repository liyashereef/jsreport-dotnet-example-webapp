@extends('adminlte::page')
@section('title', 'Email Groups')
@section('content_header')
<h1>Email Groups</h1>
@stop
@section('content')
<style>
element.style {
    width: 300px;
}
</style>
<div class="add-new" data-title="Add New Group">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="email-group-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Group Name</th>
            <th>Customer</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <span id="field_error"></span>

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Email Group</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'email-group-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <input type="hidden" name="group-id" id="group-id" />

            <div class="modal-body">

            <div class="form-group" id="group_name">
            <label for="group_name" class="col-sm-3">Group Name <span class="mandatory">*</span></label>
            <div class="col-sm-9">

                <input type="text" class="form-control has-error" name="group_name" id="group_name" placeholder="Group Name" value="" />

                <small class="help-block"></small>
            </div>
            </div>

                <div  class="form-group row" id="customer">
                <label class="customer col-sm-3">  Select Customers <span class="mandatory">*</span></label>
                <div class="col-sm-9" >
                    <select name="customer[]"  class="form-control select2 customer_select" id="customer" multiple>
                    @foreach($customer as $id=>$data)
                    <option value={{$id}}>{{$data}}</option>
                    @endforeach
                </select>
                <span class="help-block"></span>
                </div>
                </div>



            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary blue','id'=>'save_groups'))}}
                {{ Form::reset('Cancel', array('class'=>'btn btn-primary blue','data-dismiss'=>'modal'))}}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>



@stop
@section('js')
<script type="text/javascript">

     $.fn.dataTable.ext.errMode = 'throw';
        try{

        var table = $('#email-group-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            columnDefs: [
            { width: 200, targets: 0 }
            ],
            fixedColumns: true,
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
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2]
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
            ajax: "{{ route('email-group.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [2, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: '',
                    sortable:false,
                    width:"5%",
                },
                {
                    data: 'group_name',
                    name: 'group_name',

                },
                {
                    data: 'customer.[, ]',
                    name: 'customer.[0]',
                    defaultContent:'--',
                    orderable:false
                 },
                 {
            data: null,
            sortable: false,
            render: function (o) {
                var actions = '';
                actions += '<a href="#" class="edit fa fa-pencil" data-id=' + o.id + '></a>'
                @can('lookup-remove-entries')
                actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                @endcan
                return actions;
            },
        }
            ]
        });
        } catch(e){

        }
        $(".select2").select2({
        width:'100%'
        });

        $("#customer").change(function() {
        var selected=[];
        jQuery.each($(this).val(), function(index,value){
            selected.push(parseInt(value));
        });

    });

    $("#email-group-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("email-group.single",":id") }}';
            var url = url.replace(':id', id);
            $('#email-group-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="group_name"]').val(data.group_name)
                        $(".select2").select2({width:'100%'});
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit group : " + data.group_name );
                        $('#myModal select[name="customer[]"] ').val('');
                        if(data.allocation!=null && data.allocation.length>0)
                        {
                            $.each(data.allocation, function(key, value) {
                                 $('#myModal select[name="customer[]"]  option[value="'+value.customer_id+'"]').prop("selected", true).change();
                            });
                        }



                    } else {
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

    $('#email-group-form').submit(function (e) {
        e.preventDefault();
        var table = $('#email-group-table').DataTable();
        if($('#email-group-form input[name="id"]').val()){
          var message = 'Email Group has been updated successfully';
        }else{
          var message = 'Email Group has been created successfully';
        }
        formSubmit($('#email-group-form'), "{{ route('email-groups.store') }}", table, e, message);
       });

       $('#email-group-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var table = $('#email-group-table').DataTable();
            var base_url ="{{ route('email-group.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Email group has been deleted successfully';
            console.log('url');
            deleteRecord(url, table, message);
        });

        $('.add-new').click(function(){
            $('#myModal select[name="customer[]"]').prop('selected',false);
            $('.customer_select').val('').trigger('change');
            $('#myModal select[name="customer[]"]').prop('disabled',false);
            $('#myModal input[name="group_name"]').val('');
            $('#myModal input[name="id"]').val('');
         });





</script>
@stop
