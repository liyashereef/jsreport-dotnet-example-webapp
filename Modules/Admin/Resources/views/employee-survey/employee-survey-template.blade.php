{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Templates')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h1>Employee Survey Template</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" onclick="addnew()">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="template-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Template Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
@stop


@section('js')
<script>

    $(function () {
         var entries_id = {{  json_encode($entries)}};
        $.fn.dataTable.ext.errMode = 'throw';
        try{
        var table = $('#template-table').DataTable({
            bProcessing: false,
            responsive: true,
            dom: 'lfrtBip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-pdf-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: ' ',
                    className: 'btn btn-primary fa fa-print',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    text: ' ',
                    className: 'btn btn-primary fa fa-envelope-o',
                    action: function (e, dt, node, conf) {
                        emailContent(table, 'Template');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":"{{ route('employee-survey-template.list') }}",
                "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [[ 2, "desc" ]],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: 'survey_name', name: 'survey_name'},
                {data: 'start_date', name: 'start_date'},
                {data: 'expiry_date', name: 'expiry_date'},
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        var actions='';
                        var url="{{ route('employee-survey-template.update',[':id',':is_view']) }}";
                        var url = url.replace(':id?', o.id);
                        var url = url.replace(':is_view', '/1');
                        if($.inArray( parseInt(o.id), entries_id )==-1){
                            @can('edit_masters')
                            actions= '<a href="{{route("employee-survey-template.update", ["id" => ""])}}/'+ o.id +'" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                            @endcan
                            @can('lookup-remove-entries')
                                actions +='<a href="#"  style="padding-right:8%;" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                            @endcan
                        }
                        else
                        {
                            @can('edit_masters')
                            actions= '<a href="#" class="fa fa-pencil edit-disable"></a>';
                            @endcan
                            @can('lookup-remove-entries')
                            actions +='<a href="#"  style="padding-right:8%;" class="fa fa-trash-o edit-disable"></a>';
                            @endcan 
                        }
                            
                               actions +='<a href="'+url+'"  class="fa fa-eye" data-id=' + o.id + '></a>';
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }



        $('#template-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{ route('employee-survey-template.destroy',':id') }}";
            var url = base_url.replace(':id', id);
            console.log(e);
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
                            swal("Deleted", "Template has been deleted successfully", "success");
                            table.ajax.reload();
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
        });




    });
    function addnew() {
        window.location.href = "{{route('employee-survey-template.add')}}";
    }
</script>
@stop
