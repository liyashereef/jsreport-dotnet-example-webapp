@extends('adminlte::page')
@section('title', 'URA Rates')
@section('content_header')
<h1>URA Rate</h1>
@stop @section('content')
<div class="row">
    <div class="col-md-12">
        <form method="POST" id="ura-rates-form" url="#">
            {{ csrf_field() }}
            <div class="form-group" id="amount">
                <div class="row">
                    <div class="col-md-2"> <label for="amount">Amount </label></div>
                    <div class="col-md-4">

                        {{ Form::number('amount',old('amount',$amount),array('class'=>'form-control','required'=>true,'placeholder'=>'0.00'))}}
                        <small class="help-block"></small>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class='button btn btn-primary blue' id="ura-rating-btn">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <table class="table table-bordered" id="ura-rates-table">
            <thead>
                <tr>
                    <th></th>
                    <th>URA Rate</th>
                    <th>Effective Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
@section('js')
<script>
    const ura = {
        table: null,
        init() {
            let root = this;
            $('#ura-rating-btn').click(function(e) {
                swal({
                        title: "Are you sure?",
                        text: "Please confirm",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Yes, I am sure',
                        cancelButtonText: "No, cancel it",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            root.onRateSave();
                        }
                    });
            });
            this.initTable();
        },
        onRateSave() {
            let root = this;
            let $form = $('#ura-rates-form');
            let formData = new FormData($('#ura-rates-form')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('ura.rates.store')}}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        swal("Success", "URA rates has been successfully updated", "success");
                        $('.form-group').removeClass('has-error').find('.help-block').text('');
                        root.table.ajax.reload();
                    } else {
                        //alert(data);
                        swal("Alert", "Something went wrong", "warning");
                    }
                },
                error: function(xhr, textStatus, thrownError) {
                    associate_errors(xhr.responseJSON.errors, $form);
                    swal("Oops", "Something went wrong", "warning");
                },
            });
        },
        initTable() {
            let root = this;
            this.table = $('#ura-rates-table').DataTable({
                responsive: true,
                // dom: 'Blfrtip',
                serverSide: true,
                fixedHeader: true,
                ajax: {
                    "url": "{{ route('ura.rates.list') }}",
                    "error": function(xhr, textStatus, thrownError) {
                        if (xhr.status === 401) {
                            window.location = "{{ route('login') }}";
                        }
                    }
                },
                // buttons: [{
                //         extend: 'pdfHtml5',
                //         pageSize: 'A2',
                //         //text: ' ',
                //         //className: 'btn btn-primary fa fa-file-pdf-o',
                //     },
                //     {
                //         extend: 'excelHtml5',
                //         //text: ' ',
                //         //className: 'btn btn-primary fa fa-file-excel-o'
                //     },
                //     // {
                //     //     extend: 'print',
                //     //     pageSize: 'A2',
                //     //     //text: ' ',
                //     //     //className: 'btn btn-primary fa fa-print'
                //     // },
                // ],
                order: [
                    [0, 'desc']
                ],
                // lengthMenu: [
                //     [10, 25, 50, 100, 500, -1],
                //     [10, 25, 50, 100, 500, "All"]
                // ],
                columns: [{
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        render: function(o) {
                            return '$' + o;
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(o) {
                            return moment(o).format("MMMM DD, Y H:mm A")
                        }
                    },
                    {
                        data: 'deleted_at',
                        name: 'deleted_at',
                        render: function(o) {
                            if (!o) {
                                return '--';
                            }
                            return moment(o).format("MMMM DD, Y H:mm A")
                        }
                    },
                ],
            });
        },

    }

    $(function() {
        ura.init();
    });
</script>
@endsection