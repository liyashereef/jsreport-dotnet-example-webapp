{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')
@section('title', 'Policy and Procedure')
@section('content_header')
<h1>Policy and Procedure</h1>
@stop

@section('content')
<div class="add-new" data-title="Add New Policy">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="tracking-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Policy</th>
            <th>Description</th>
            
            <th>Action</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="myModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Tracking Process</h4>
            </div>
            {{ Form::open(array('url'=>'#','id'=>'tracking-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
            <div class="modal-body">
           
                <div class="form-group" id="policy">
                    <label for="policy" class="col-sm-3 control-label">Policy</label>
                    <div class="col-sm-9">
                        {{ Form::text('policy',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                <div class="form-group" id="description">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9">
                        {{ Form::textarea('description',null,array('class'=>'form-control')) }}
                            <small class="help-block"></small>
                        </div>
                    </div>
           
            <div class="form-group" id="ratings">
                        <label for="ratings" class="col-sm-3 control-label">Specify Rating</label>
                        <div class="col-sm-9">
                        {!! Form::checkbox('selectAll', "all",false,['class'=>'checkall']); !!}
                        {!! Form::label('Select All', null) !!}
                        <br>
            @foreach($lookups as $row)
            {!! Form::checkbox('ratings[]', $row['id'],false,['class'=>'checkitem','id'=>'rating_'.$row['id']]); !!}
            {!! Form::label('ratings', $row['rating']) !!}
            <br>
            @endforeach
            </div>
            </div>

            </div>
            <div class="modal-footer">
                {{ Form::submit('Save', array('class'=>'button btn btn-primary','id'=>'mdl_save_change'))}}
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
        var table = $('#tracking-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
             buttons: [
             {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2]
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
                        emailContent(table, 'Process Steps');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('rating-policy.list') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 0, "asc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
               { data: 'DT_RowIndex',
                    name: '',
                    sortable:false},
                {data: 'policy', name: 'policy'},
                {data: 'description', name: 'description'},
                
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions = '';
                        @can('edit_masters')
                        actions += '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>'
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

        /* Tracking Process Save - Start */
        $('#tracking-form').submit(function (e) {
            e.preventDefault();
            if($('#tracking-form input[name="id"]').val()){
                var message = 'Policy has been updated successfully';
            }else{
                var message = 'Policy has been created successfully';
            }
            formSubmit($('#tracking-form'), "{{ route('rating-policy.store') }}", table, e, message);
        });
        /* Tracking Process Save - End */

        /* Tracking Process Edit - Start */
        $("#tracking-table").on("click", ".edit", function (e) {
            var id = $(this).data('id');
            var url = '{{ route("rating-policy.single",":id") }}';
            var url = url.replace(':id', id);
            $('#tracking-form').find('.form-group').removeClass('has-error').find('.help-block').text('');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    $("#tracking-form").trigger('reset');
                    if (data) {
                        $('#myModal input[name="id"]').val(data.id);
                        $('#myModal input[name="policy"]').val(data.policy);
                        $('#myModal textarea[name="description"]').val(data.description);
                       
                        $.each(data.rating_allocation, function(key, value) {
                        $('#myModal input:checkbox[id="rating_'+value.employee_rating_id+'"]').prop('checked', true); 
                        });
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Policy: " + data.policy)
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
        /* Tracking Process Edit - End */


        /* Tracking Process Delete - Start */
        $('#tracking-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('rating-policy.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            var message = 'Policy has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* Tracking Process Delete - End */
       
         $(".checkall").change(function(){
            $(".checkitem").prop('checked',$(this).prop("checked"))
         })
   



    });

    

</script>
@stop
