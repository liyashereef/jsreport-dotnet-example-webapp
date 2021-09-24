{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Roles And Permissions')

@section('content_header')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
    table td
    {
        text-align: left;
    }
</style>
</head>
<h1>Roles and Permissions</h1>
@stop
@section('content')
<div id="message"></div>
<div class="add-new" onclick="addnew()">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="role-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Roles</th>
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
            var table = $('#role-table').DataTable({
                bProcessing: false,
                responsive: true,
                order: [[1, "asc"]],
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
                        emailContent(table, 'Roles and Permissions');
                    }
                }
                ],
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('role.list') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                columns: [
                {data: 'DT_RowIndex', name: '',sortable:false},
                {data: null, name: 'name',
                render:function(o)
                {
                  return  changeFormat(o.name);
              }
          },
          {
            data: null,
            sortable: false,
            render: function (o) {
                var actions = '';
                @can('edit_masters')
                actions += '<a href="{{route("role.update", ["id" => ""])}}/'+ o.id +'" class="edit fa fa-pencil" data-id=' + o.id + '></a>';
                @endcan
                @can('lookup-remove-entries')
                actions += '<a href="#" class="delete fa fa-trash-o" data-id=' + o.id + '></a>' ;
                @endcan
                return actions;
            },
        }
        ]
    });
        } catch(e){
            console.log(e.stack);
        }

        $('#role-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url = "{{route('role.destroy',':id')}}"
            var url = base_url.replace(':id',id);
            var message = 'Role & Permission has been deleted successfully';
            deleteRecord(url, table, message);
        });

    });
    function addnew() {
        window.location.href = "{{route('role.update')}}";
    }
    function changeFormat(str) {
        if(str.length==3)
        {
         return str.toUpperCase();
     }
     else
     {
       var frags = str.split('_');
       for (i=0; i<frags.length; i++) {
          frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
      }
      return frags.join(' ');
  }
}
</script>
@stop
