{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Shift Module Dropdown')

@section('content_header')
<h1>Shift Module Dropdown</h1>
@stop

@section('content')
<div id="message"></div>
<div class="add-new"  onclick="addnew()" data-title="Add New Dropdown">Add <span class="add-new-label">New</span></div>
<table class="table table-bordered" id="dropdown-table">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="75%">Dropdown</th>
            <th width="10%">Post Order</th>
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
            var table = $('#dropdown-table').DataTable({
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
                        columns: [0, 1],
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
                "url":'{{ route('shift-module-dropdown.list') }}',
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
            {data: 'dropdown_name', name: 'dropdown_name'},
            {data: 'post_order', name: 'post_order'},
            {data: null,
                orderable:false,
                render: function (o) {
                    var actions = '';
                    @can('edit_masters')
                        actions = '<a href="{{route("shift-module-dropdown.update", ["id" => ""])}}/'+ o.id +'" class="edit {{Config::get('globals.editFontIcon')}}" data-id=' + o.id + '></a>';
                          @endcan
                            @can('lookup-remove-entries')
                                actions +='<a href="#" class="delete {{Config::get('globals.deleteFontIcon')}}" data-id=' + o.id + '></a>';
                                 @endcan
                        return actions;
                },
            }
            ]
        });
        } catch(e){
            console.log(e.stack);
        }


        /* WorkType Delete  - Start */
        $('#dropdown-table').on('click', '.delete', function (e) {
            var id = $(this).data('id');
            var base_url ="{{ route('shift-module-dropdown.destroy',':id') }}";
            var url = base_url.replace(':id',id);
            var message = 'Dropdown has been deleted successfully';
            deleteRecord(url, table, message);
        });
        /* WorkType Delete  - End */

    });

 function addnew() {
        window.location.href = "{{route('shift-module-dropdown.add')}}";
    }

</script>
@stop
