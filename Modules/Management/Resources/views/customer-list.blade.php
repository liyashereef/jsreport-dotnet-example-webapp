@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Customer List</h4>
</div>

<div class="timesheet-filters mb-2 filter-div">
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-6"><label class="filter-text">Customer Name</label></div>
                <div class="col-md-6 filter">
                    <select class="form-control option-adjust client-filter select2" name="clientname-filter" id="clientname-filter">
                        <option value="">Select Customer</option>
                        @foreach($customer_list as $each_customername)
                        <option value="{{ $each_customername->id}}">{{ $each_customername->client_name .' ('.$each_customername->project_number.')' }}
                        </option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-3"><label class="filter-text">Type</label></div>
                <div class="col-lg-6 " id="dropdown">
                    <select class="form-control option-adjust type-filter" name="type-filter" id="customer-type-name-filter">
                    <option value= 'ALL_CUSTOMER' selected="selected" >All</option>
                    <option value="{{ PERMANENT_CUSTOMER }}" >Permanent</option>
                    <option  value="{{ STC_CUSTOMER }}" >Short Term Contracts</option>
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-3"><label class="filter-text">Active</label></div>
                <div class="col-lg-6 " id="dropdown">
                    <select class="form-control option-adjust active-filter" name="active-filter" id="active-filter">
                    <option value="{{ACTIVE}}" selected="selected">Yes</option>
                    <option value="{{INACTIVE}}" >No</option>
                    </select>
                    <span class="help-block"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered" id="candidates-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Project No</th>
            <th>Client Name</th>
            <th>City</th>
            <th>Client Contact Name</th>
            <th>Client Contact Email</th>
            <th>Client Contact Phone Number</th>

            <th>Action</th>
        </tr>
    </thead>
</table>


@stop
@section('scripts')
<script>

    $(function () {
        $('#customerid').select2();

        function collectFilterData() {
            return {
                projectname: $("#clientname-filter").val(),
            }
        }
        function customerTypeData() {
            return {
                customerType: $("#customer-type-name-filter").val(),

            }
        }
        function customerActiveData() {
            return {
                active: $("#active-filter").val(),
            }
        }

        $('.select2').select2();
        table =  $('#candidates-table').DataTable({
            processing: false,
            fixedHeader: false,
            serverSide: true,
            responsive: true,
            ajax: {
                "url":'{{ route('management.customerViewList') }}',
                "data": function ( d ) {
                    return $.extend({}, d, collectFilterData(),customerTypeData(),customerActiveData());

                        },
                    "error": function (xhr, textStatus, thrownError) {
                    if(xhr.status === 401){
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'pdfHtml5',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                    }
                },
                {
                    extend: 'print',
                    pageSize: 'A2',
                    exportOptions: {
                        columns: 'th:not(:last-child)',
                        stripHtml: false,
                    }
                }
            ],
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            order: [
                [0, "asc"]
            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, -1],
                [10, 25, 50, 100, 500, "All"]
            ],
            columns: [

                {data: 'DT_RowIndex', name: '',sortable:false},
                {
                        data: 'project_number',
                        name: 'project_number'
                    },
                    {
                        data: 'client_name',
                        name: 'client_name'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'contact_person_name',
                        name: 'contact_person_name'
                    },
                    {
                        data: 'contact_person_email_id',
                        name: 'contact_person_email_id'
                    },
                    {
                        data: 'contact_person_phone',
                        name: 'contact_person_phone'
                    },
                {

                    data: null,
                    sortable: false,
                    render: function (row) {
                        actions = '';
                        var user = row.allocated_flag;



                        actions ='<a title="View"  href="{{ route("management.customerViewMore")}}/'+ row.id +'" class="view btn fa fa-eye id="view_document"></a>';
                      return actions;

                           }
                }

            ]

        });
    });

   $(".client-filter").change(function(){
            table.ajax.reload();
        });
   $(".type-filter").change(function(){
            table.ajax.reload();
        });
    $(".active-filter").change(function(){
            table.ajax.reload();
        });
   $(".select2").select2()

    </script>
    <style>

    .client-filter {
        padding-right: 0px;
        padding-bottom: 5px;
        z-index: 6000;
    }
    .filter-text{
    margin-top:6px;
}
    #clientname-filter {
        width: 365px;
    }
    .select2-dropdown {
        z-index: 0 !important;
    }
    .filter-div{
    padding-bottom: 20px;
    }
   .filter{
       margin-left:-95px;
   }
   html, body {
    max-width: 100%;
    overflow-x: hidden;
    }
    </style>
@stop


