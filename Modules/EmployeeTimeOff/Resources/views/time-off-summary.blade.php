@extends('layouts.app')

@section('content')
<div class="table_title">
    <h4>Employee Summary</h4>
</div>
<div>
    @php $i = 0 @endphp
    <table class="table table-bordered" id="timeoff-table">
        <thead>
            <tr>


            </tr>
            <tr>
                <th rowspan="2">#</th>
                <th class="sorting" rowspan="2">Employee Id</th>
                <th class="sorting" rowspan="2">Project Id</th>
                <th class="sorting" rowspan="2">Project Name</th>
                <th class="sorting" rowspan="2">Employee Name</th>
                @foreach($requestType as $type)
                <th colspan=4 class="sorting"> {{$type}}</th>
                @endforeach
            </tr>
            <tr>
                @foreach($requestType as $type)
                @if($i++<=$count)
                <th class="sorting"> Claim</th>
                <th class="sorting"> Approve</th>
                <th class="sorting"> Reject</th>
                <th class="sorting"> Remain</th>
                @endif
                @endforeach
            </tr>
   {{-- </tr> --}}
        </thead>
    </table>
</div>
@stop
@section('scripts')
<script>

    var columnArr = [
        {data: 'DT_RowIndex', name: ''},
        {data: 'employee_number', name: 'employee_number'},
        {data: 'project_number', name: 'project_number'},
        {data: 'client_name', name: 'client_name'},
        {data: 'employee_name', name: 'employee_name'},
        @foreach($requestType as $type)
        {data: 'timeoff.{{$type}}.days_requested', name: 'timeoff.{{$type}}.days_requested'},
        {data: 'timeoff.{{$type}}.days_approved', name: 'timeoff.{{$type}}.days_approved'},
        {data: 'timeoff.{{$type}}.days_rejected', name: 'timeoff.{{$type}}.days_rejected'},
        {data: 'timeoff.{{$type}}.days_remaining', name: 'timeoff.{{$type}}.days_remaining'},
        @endforeach
    ]
    var table = $('#timeoff-table').DataTable({
        processing: false,
        serverSide: true,
        scrollX:true,
        // responsive: true,
        ajax: {
            "url":"{{ route('timeoff.summary') }}",
            "error": function (xhr, textStatus, thrownError) {
                if(xhr.status === 401){
                    window.location = "{{ route('login') }}";
                }
            },
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        order: [[ 0, "desc" ]],
        lengthMenu: [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
       columnDefs: [
        {"className": "nowrap dt-center", "targets": "_all"}
      ],
        columns: columnArr
    });
    </script>
@stop
