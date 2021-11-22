@extends('adminlte::page')
@section('title', 'Process Tab')
@section('content_header')
<h1>Process Tab</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new"  data-title="Add New Process Tab">Add
    <span class="add-new-label">New</span>
</div>
<table class="table table-bordered" id="process-tab-table">
    <thead>
        <tr>
            <th width="10%">Step Order</th>
            <th>Display Name</th>
            <th width="15%">Created Date</th>
            <th width="10%">Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Question</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                
            </div>
            {{ Form::open(array('url'=>'#','id'=>'process-tab-form','class'=>'form-horizontal', 'method'=> 'POST')) }} {{ Form::hidden('id',null) }}
            <div class="modal-body">
              <!-- Active Toggle button - Start -->
             {{--  <div class="form-group col-sm-12" id="active" style="display:block;">
                <label class="switch" style="float:right;">
                  <input name="is_active" type="checkbox" value="1">
                  <span class="slider round"></span>
              </label>
              <label style="float:right;padding-right: 5px;">Active</label>
            </div> --}}
          <!-- Active Toggle button - End -->
             <div class="form-group row" id="display_name">
              <label for="display_name" class="col-sm-12">Display Name</label>
               <div class="col-sm-12">
                {{ Form::text('display_name',null,array('class' => 'form-control', 'Placeholder'=>'Display Name')) }}
                 <small class="help-block"></small>
               </div>
            </div>

            <div class="form-group row" id="system_name">
                <label for="system_name" class="col-sm-12">System Name</label>
                 <div class="col-sm-12">
                  {{ Form::text('system_name',null,array('readonly','class' => 'form-control', 'Placeholder'=>'System Name')) }}
                   <small class="help-block"></small>
                 </div>
              </div>

             <div class="form-group row" id="order">
               <label for="order" class="col-sm-12">Order</label>
                 <div class="col-sm-2">
                  {{ Form::number('order',null,array('class' => 'form-control', 'Placeholder'=>'Order')) }}
                  <small class="help-block"></small>
            </div>
          </div>

          <div class="form-group row" id="instructions">
               <label for="instructions" class="col-sm-12">Instructions</label>
                 <div class="col-sm-12">
                  <textarea name="instructions" rows="6" class="form-control"></textarea>
                  <small class="help-block"></small>
            </div>
          </div>
           <div class="form-group row" id="detailed_help">
             <label for="detailed_help" class="col-sm-12">Detailed Help</label>
          <div id="editors" class="col-sm-12">
                 <textarea name="detailed_help" class="ckeditor" class="form-control" rows="20"  id="editor"></textarea>
                   <span class="help-block"></span>
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
@stop
@section('js')
<script>
    $(function () {

        $.fn.dataTable.ext.errMode = 'throw';
        try {
            var table = $('#process-tab-table').DataTable({
                dom: 'lfrtBip',
                bprocessing: false,
                buttons: [{
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Services');
                    }
                }
                ],
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('recruitment.process-tab.list') }}",
                    "error": function (xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                order: [[ 1, "desc" ]],
                lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
                ],
                columns: [
                {
                    data: 'order',
                    name: 'order',
                },
                {
                    data: 'display_name',
                    name: 'display_name'
                },
                {
                data: 'created_at',
                name: 'created_at'
                },
                {
                data: null,
                orderable: false,
                render: function (o) {
                 var actions = "";
                @can('edit_masters')
                actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' +o.id + '></a>';
                @endcan
                 return actions;
             },
         }
         ]
     });
        } catch (e) {
            console.log(e.stack);
        }


        /* Service Store - Start*/


        $('#process-tab-form').submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            CKEDITOR.instances.editor.updateElement();
            var editor=( CKEDITOR.instances.editor.getData());
            var formData = new FormData($('#process-tab-form')[0]);
            formData.append('detailed_help', editor);
            url = "{{ route('recruitment.process-tab.store') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success) {
                         swal("Saved", "Process tab has been created successfully", "success");
                          $("#myModal").modal('hide');
                         table.ajax.reload();
                    } else {
                        alert(data);
                    }
                },
                fail: function (response) {
                    alert('here');
                },
                error: function (xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form,true);
                },
                contentType: false,
                processData: false,
            });
        });
        /* Service Store - End*/

        /* Service Edit - Start*/
        $("#process-tab-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("recruitment.process-tab.single",":id") }}';
            var url = url.replace(':id', id);
            $('#process-tab-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="system_name"]').val(data.system_name);
                        $('#myModal textarea[name="instructions"]').text(data.instructions);
                        $('#myModal input[name="display_name"]').val(data.display_name)
                        $('#myModal input[name="order"]').val(data.order)
                        if( Object.entries(data).length==0)
                        {
                        CKEDITOR.instances['editor'].setData('')
                        }
                        else{
                        CKEDITOR.instances['editor'].setData(data.detailed_help)
                        }
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Process Tab")
                    } else {
                        console.log(data);
                        swal("Oops", "Edit was unsuccessful", "warning");
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
        /* Service Edit - End*/

        /* Service Delete  - Start */
        $('#process-tab-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('recruitment.process-tab.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Process tab has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Service Delete  - End */



    });

    //on change display name change system name
    $('#myModal input[name="display_name"]').change(function() {
        var str = $('#myModal input[name="display_name"]').val();
        $('#myModal input[name="system_name"]').val(str.split(/[ ,]+/).filter(function(v){return v!==''}).join('_'));
    });


    $('.add-new').click(function(){
        $('#myModal textarea').empty();
    });


</script>
@stop
