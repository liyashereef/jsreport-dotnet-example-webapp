{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Work Types')

@section('content_header')
<h1>Work Types</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" data-title="Add New Work Type">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Work Type</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Work Type</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'type-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
                <div class="form-group row" id="type">
                    <label for="year" class="col-sm-3 control-label">Work Type</label>
                    <div class="col-sm-9">
                      {{ Form::text('type',null,array('class'=>'form-control','placeholder' => 'Work Type','maxlength'=>100)) }}
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
@stop
@section('js')
<script>

    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
            var table = $('#type-table').DataTable({
                dom: 'lfrtBip',
                bProcessing: false,
                buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1,],
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
                    columns: [0, 1]
                }
            },
            {
                text: ' ',
                className: 'btn btn-primary fa fa-envelope-o',
                action: function (e, dt, node, conf) {
                    emailContent(table, 'Work Types');
                }
            }
            ],
            processing: true,
            serverSide: true,
            fixedHeader: true,
            ajax: {
                "url":'{{ route('worktype.list') }}',
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                }
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 1, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
            {data: 'DT_RowIndex', name: '',sortable:false},
            {data: 'type', name: 'type'},
            {data: null,
                orderable:false,
                render: function (o) {
                    var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit  {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
                        @endcan
                        @can('lookup-remove-entries')
                        actions += '<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                },
            }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }


        /* Worktype Store - Start*/
        $('#type-form').submit(function (e) {
            e.preventDefault();
            if($('#type-form input[name="id"]').val()){
                var message = 'Work type has been updated successfully';
            }else{
                var message = 'Work type has been created successfully';
            }
            formSubmit($('#type-form'), "{{ route('worktype.store') }}", table, e, message);
        });
        /* Worktype Store - End*/


        /*Edit Worktypes - Start*/
        $("#type-table").on("click", ".edit", function (e) {
            id = $(this).data('id');
            var url = '{{ route("worktype.single",":id") }}';
            var url = url.replace(':id', id);
            $('#type-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="type"]').val(data.type)
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Work Type: " + data.type)
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
        /* Edit Worktypes - End*/


        /* WorkType Delete  - Start */
        $('#type-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url ="{{ route('worktype.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Work type has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* WorkType Delete  - End */

    });

</script>
@stop
