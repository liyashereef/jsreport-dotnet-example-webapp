@extends('adminlte::page')
@section('title', 'Shift Timings')
@section('content_header')
<h1>Schedule Shift Timings</h1>
@stop @section('content')
<table class="table table-bordered" id="shift-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Shift Name</th>
            <th>From</th>
            <th>To</th>
            <th>Displayable</th>
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
            {{ Form::open(array('url'=>'#','id'=>'shift-form','class'=>'form-horizontal', 'method'=> 'POST')) }}
            {{ Form::hidden('id', null) }}
             {{ Form::hidden('shift_name', null) }}
            <div class="modal-body">
                <div class="form-group {{ $errors->has('shift_name') ? 'has-error' : '' }} row" id="shift_name">
                    <label for="shift_name" class="col-sm-3 control-label">Shift Name</label>
                    <div class="col-sm-9">
                        {{ Form::text('shift_name',null,array('class'=>'form-control','readonly'=>true)) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                 <div class="form-group {{ $errors->has('from') ? 'has-error' : '' }} row" id="from">
                    <label for="from" class="col-sm-3 control-label">From</label>
                    <div class="col-sm-9">
                        {{ Form::time('from',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                  <div class="form-group {{ $errors->has('to') ? 'has-error' : '' }} row" id="to">
                    <label for="to" class="col-sm-3 control-label">To</label>
                    <div class="col-sm-9">
                         {{ Form::time('to',null,array('class'=>'form-control')) }}
                        <small class="help-block"></small>
                    </div>
                </div>
                  <div class="form-group {{ $errors->has('displayable') ? 'has-error' : '' }} row" id="displayable">
                    <label for="displayable" class="col-sm-3 control-label">Displayable</label>
                    <div class="col-sm-9">
                         {{ Form::select('displayable',[null=>'Please Select',"1"=>"Yes","0"=>"No"],old('displayable'),array('class' => 'form-control')) }}
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
    $(function () {
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#shift-table').DataTable({
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
                        emailContent(table, 'Schedule Assignment Type');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('schedule-shift-timings.list') }}",
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
                    data: 'shift_name',
                    name: 'shift_name',
                },
                 {
                    data: 'from',
                    name: 'from',
                },
                 {
                    data: 'to',
                    name: 'to',
                },
                 {
                    data: null,
                    name: null,
                     render: function (o) {
                        if(o.displayable==0){
                            return 'No';
                        }
                        else
                        {
                            return 'Yes';
                        }
                     }

                },
                {
                    data: null,
                    orderable:false,
                    render: function (o) {
              var shiftname=o.shift_name;
              shiftname=shiftname.replace(/ /g,"_").toLowerCase();
              actions = '<a href="#" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + shiftname + '></a>';
                    return actions;
                    },
                }

            ]
        });
        } catch(e){
            console.log(e.stack);
        }

        /* Posting data to ExperienceController - Start*/
        $('#shift-form').submit(function (e) {
            e.preventDefault();
            if($('#shift-form input[name="id"]').val()){
                var message = 'Shift timing has been updated successfully';
            }else{
                var message = 'Shift timing has been created successfully';
            }
            formSubmit($('#shift-form'), "{{ route('schedule-shift-timings.store') }}", table, e, message);
        });
        /* Posting data to ExperienceController - End*/

        $("#shift-table").on("click", ".edit", function (e) {
            var name = $(this).data('id');
            var url = '{{ route("schedule-shift-timings.single",":name") }}';
            var url = url.replace(':name', name);
            $('#shift-form').find('.form-group').removeClass('has-error').find('.help-block').text(
                '');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        var shift_name=transformData(data.shift_name);
                        $('#myModal input[name="shift_name"]').val(shift_name)
                        $('#myModal input[name="id"]').val(data.id)
                        $('#myModal input[name="from"]').val(data.from)
                        $('#myModal input[name="to"]').val(data.to)
                          $('#myModal select[name="displayable"]').val(data.displayable);
                        $("#myModal").modal();
                        $('#myModal .modal-title').text("Edit Shift Timings: " +shift_name )
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


function transformData(str) {
    var frags = str.split('_');
    /*if(frags.length==2)
    {
    frags[0] = frags[0].charAt(0).toUpperCase() + frags[0].slice(1);
    }
    else
    {*/
    for (i=0; i<frags.length; i++) {
        frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
    }
    // }
    var shift_timing_transformed = frags.join(' ');
    // special conditions for satutory holiday
    /*if(shift_timing_transformed == "Statutory Holidays"){
        shift_timing_transformed = "Statutory holidays";
    }*/
    return shift_timing_transformed;
}


    });

</script>
@stop
