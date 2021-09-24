{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Visitor Log Templates')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<h1>Visitor Log Templates</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new" onclick="addnew()">Add<span class="add-new-label">New Template</span></div>
<table class="table table-bordered" id="template-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Template Name</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>
@stop


@section('js')
<script>

    $(function () {
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
                        columns: [0, 1]
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: ' ',
                    className: 'btn btn-primary fa fa-file-excel-o',
                    exportOptions: {
                        columns: [0, 1]
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
                        emailContent(table, 'Template');
                    }
                }
            ],
            processing: false,
            serverSide: true,
            responsive: false ,
            ajax: {
                "url":"{{ route('visitorlog-templates.list') }}",
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
                {data: 'template_name', name: 'template_name'}, 
                {
                    data: null,
                    sortable: false,
                    render: function (o) {
                        @can('edit_masters')
                           actions = '<a href="{{route("visitorlog-templates.update", ["id" => ""])}}/'+ o.id +'" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                        @endcan
                        @can('lookup-remove-entries')
                            actions +='<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>';
                        @endcan
                        return actions;
                    },
                }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }



        $('#template-table').on('click', '.delete', function (e) {
            id = $(this).data('id');
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
                    url: "{{route('visitorlog-templates.destroy')}}",
                    type: 'GET',
                    data: "id=" + id,
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
        window.location.href = "{{route('visitorlog-templates.add')}}";
    }
</script>
@stop