@extends('adminlte::page')
@section('title', 'Activity Type')
@section('content_header')
<h1>Activity Type</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add Activity Type">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="work-hour-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Activity Type</th>
            <th>Description</th>
            <th>Sort order</th>
            <th>Created Date</th>
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
            {{ Form::open(array('url'=>'#','id'=>'work-hour-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id',null) }}
            <div class="modal-body">
                <div class="form-group" id="name">
                    <label for="subject" class="col-sm-3 control-label">Activity Type</label>
                    <div class="col-sm-9">
                        {{ Form::text('name',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="description">
                    <label for="description" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {{ Form::textArea('description',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group sortorder" id="sort_order">
                    <label for="sort_order" class="col-sm-3 control-label">Sort Order</label>
                    <div class="col-sm-3">
                        <select name="sort_order" id="sort_order" class="form-control">
                            <option value="">Please Select</option>
                            @foreach ($sortOrder as $key=>$sort)
                                <option value="{{$sort}}">{{$sort}}</option>
                            @endforeach
                        </select>
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
<script>
    $(function() {
        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#work-hour-table').DataTable({
                bProcessing: false,
                responsive: true,
                dom: 'lfrtBip',
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
                        action: function(e, dt, node, conf) {
                            emailContent(table, 'Feedback Lookups');
                        }
                    }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('work-hour-type.list') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [
                    [3, "asc"]
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },{
                        data: 'sort_order',
                        name: 'sort_order'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    
                    {
                        data: null,
                        sortable: false,
                        render: function(o) {
                            var actions = '';
                            @can('edit_masters')
                            if (o.is_editable == false) {
                                actions += '<a href="#" class="edit-disable fa fa-pencil"></a>'
                            } else {
                                actions += '<a href="#" attr-sort="'+o.sort_order+'" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                            }
                            @endcan
                            @can('lookup-remove-entries')
                            if (o.is_deletable == false) {
                                actions += '<a href="#" class="edit-disable fa fa-trash-o"></a>';
                            } else {
                                actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                            }
                            @endcan
                            return actions;
                        },
                    }
                ]
            });
        } catch (e) {
            console.log(e.stack);
        }
            
        /* Feedback Save - Start*/
        $('#work-hour-form').submit(function(e) {
            e.preventDefault();
            if ($('#work-hour-form input[name="id"]').val()) {
                var message = 'Activity type has been updated successfully';
            } else {
                var message = 'Activity type has been created successfully';
            }
            if($('#work-hour-form input[name="id"]').val() && $("#sort_order").find('option:selected').val()==""){
                swal("warning","Please select a sort order");
            }else{
                formSubmit($('#work-hour-form'), "{{ route('work-hour-type.store') }}", table, e, message);
            }
        });
        /* Feedback Save - End*/


        /* Feedback Edit - Start */
        $("#work-hour-table").on("click", ".edit", function(e) {
            $(".sortorder").show();
            var id = $(this).data('id');
            var sortOrder = $(this).attr('attr-sort');
            var url = '{{ route("work-hour-type.single",":id") }}';
            var url = url.replace(':id', id);
            $('#work-hour-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="name"]').val(data.name);
                        $('#myModal textarea[name="description"]').val(data.description);
                        $('#myModal .modal-title').text("Edit Activity Type: " + data.name);
                        $('#myModal #sort_order').val(sortOrder);
                        $("#myModal").modal();
                    } else {

                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Feedback Edit - End */


        /* Feedback Delete - Start */
        $('#work-hour-table').on('click', '.delete', function(e) {
            var id = $(this).data('id');
            var base_url = "{{ route('work-hour-type.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Activity type has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Feedback Delete - End */


    });

    $(".add-new").on("click",function(e){
        $(".sortorder").hide();
    })
</script>
@stop