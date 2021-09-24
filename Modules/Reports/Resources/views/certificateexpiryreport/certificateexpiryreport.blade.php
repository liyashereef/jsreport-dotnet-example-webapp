@extends('layouts.app')
@section('content')
<div class="table_title">
    <h4>Document Expiry Report</h4>
</div>

<div class="row">

    <div class="col-lg-1">
        <label for="employeeName" class="labelStyle">Employee Name</label>
    </div>
    <div class="col-lg-2 employee">
        <select name="employeeName" id="employeeNameSelect" class="select" multiple="multiple">
            @foreach ($employeeName as $key => $value)
            <option value="{{$value->id }}">{{ $value->name_with_emp_no }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-2 employeeAll">
        <select name="employeeName" id="employeeNameAllSelect" class="select" multiple="multiple">
            @foreach ($employeeAll as $key => $value)
            <option value="{{$value->id }}">{{ $value->name_with_emp_no }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-1">
    </div>
    <div class="col-lg-1">
        <label for="SecurityClearance" class="labelStyle">Security Clearance</label>
    </div>
    <div class="col-lg-2">
        <select name="SecurityClearance" id="SecurityClearanceSelect" class="select" multiple="multiple">
            @foreach ($securityClearance as $key => $value)
            <option value="{{$value['id']}}">{{$value['security_clearance']}}</option>
            @endforeach
        </select>
    </div>
      <div class="col-lg-1">
    </div>
    <div class="col-lg-1">
        <label for="certificateOrSecurityClearance" class="labelStyle">Customer</label>
    </div>
    <div class="col-lg-2">
        <select name="customer" id="customer_id" class="form-control select">
        <option value=0 >Select Customer</option>
        @foreach ($customer as $key => $cstmr)
            <option value="{{$key}}">{{$cstmr}}</option>
        @endforeach
        </select>
    </div>
</div>
<div class="row" style="padding-top: 10px;">
    <div class="col-lg-1">
        <label for="certificateOrSecurityClearance" class="labelStyle">Certificate</label>
    </div>
    <div class="col-lg-2">
        <select name="certificate" id="certificateSelect" class="select" multiple="multiple">
            @foreach ($certificateMaster as $key => $value)
            <option value="{{$value['id']}}">{{$value['certificate_name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-1">
    </div>
    <div class="col-lg-1">
        <label for="status" class="labelStyle">Status</label>
    </div>
    <div class="col-lg-2">
        <select name="status" id="statusSelect" class="select">
            <option value=-1 >All</option>
            <option value=0 selected="selected">Expired</option>
            <option value=1 >Expiring in Days</option>
            <option value=2 >Expiring in Months</option>
            <option value=3 >Expiring in Years</option>
        </select>
    </div>
     <div class="col-lg-1">
    </div>
    <div class="col-lg-1" style="padding-top: 3px;">
        <button class="form-control button btn submit" id="filterbutton" name="filterbutton" type="button">Search</button>
    </div>
    <div class="col-lg-1"></div>
    <div class="col-lg-2" style="text-align: right;padding-top: 15px;">
        <input type="checkbox" name="activeEmployee" id="activeEmployee" checked>
        <label for="activeEmployee">Active Employees</label>
    </div>
</div>


<div class="row" id="reportdiv" style="padding: 12px;">
    <table id="resulttable" class="table table-bordered dataTable no-footer dtr-inline" style="width:100%">
        <thead>
            <tr>
                <th><input type="checkbox" name="selectAll" id="selectAll"></th>
                <th>Employee No.</th>
                <th>Employee Name</th>
                <th>Phone No.</th>
                <th>Email</th>
                <th>Document</th>
                <th>Expiry Date</th>
                <th>Status</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </table>
</div>

<span data-toggle="modal" data-target="#myModal" id="emailModal" hidden>Email</span>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4>Document Expiry Report Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="emailSubject">Email Subject</label>
                    <input type="text" class="form-control" id="subject">
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div id="editors">
                            <label for="emailBody">Email Body</label>
                            <textarea name="expiry_report_email_template" class="ckeditor" rows="20" id="editor"></textarea>
                            <span class="help-block" id="errorTermsAndCondition"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12" style='padding-top:10px; text-align: right !important;'>
                    <button class="button btn btn-primary blue" style='margin-right:5px' data-dismiss="modal" onclick="sendEmail()">Send</button>
                    <button class="button btn btn-primary blue allocate-cancel-btn " data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    
    var table;
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;

    CKEDITOR.replace('editor', {
        height: 500,
    });


    function showEmailModal() {
        $.ajax({
            "url": "{{route('reports.documentExpiryReportTemplate')}}",
            success: function (result) {
                if (result.email_body != "") {
                    CKEDITOR.instances['editor'].setData(result.email_body);
                    $('#subject').val(result.email_subject);
                }
            }});
    }

    function sendEmail() {

        var datatable_rows = [];
        $("input:checkbox[name=email]:checked").each(function () {
            datatable_rows.push({
                "employee_name": decodeURIComponent($(this).data('employee_name')),
                "employee_no": decodeURIComponent($(this).data('employee_no')),
                "email": decodeURIComponent($(this).data('email')),
                "document": decodeURIComponent($(this).data('document')),
                "expiry_date": decodeURIComponent($(this).data('expiry')),
                "status": decodeURIComponent($(this).data('status'))
            });
        });

        if (datatable_rows.length > 0) {
            var emailContent = CKEDITOR.instances['editor'].getData();
            if ($('#subject').val().length === 0) {
                swal({
                    icon: 'warning',
                    title: 'Oops',
                    text: 'Please fill email subject',
                });
            } else if (emailContent.length === 0) {
                swal({
                    icon: 'warning',
                    title: 'Oops',
                    text: 'Please fill email body',
                });
            } else {
                var jsonObjects = [{
                    empData: datatable_rows,
                    email_subject: $('#subject').val(),
                    email_body: emailContent
                }];
                var jason = JSON.stringify(jsonObjects);
                $.ajax({
                    "url": "{{route('reports.expiryMail')}}",
                    type: 'POST',
                    data: jason,
                    dataType: "json",
                    contentType: "application/json; charset=utf-8",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        if (result.status) {
                            swal({
                                icon: 'success',
                                title: 'Sent',
                                text: 'Mail sent successfully',
                            });
                            $('#myModal').modal('toggle');
                            $('#selectAll').prop('checked', false);
                            $("input:checkbox[name=email]").prop('checked', false);
                        }
                }});
            }

        }
    }

    $("#selectAll").click(function () {
        $("input:checkbox[name=email]").prop('checked', $(this).prop('checked'));
    });

    $(document).ready(function (e) {
        $('.employeeAll').hide();
        $(".select").select2();
        $('#employeeNameSelect').select2({
            id: '-1',
            placeholder: "Select employee"
        });
        $('#employeeNameAllSelect').select2({
            id: '-1',
            placeholder: "Select employee"
        });
        $('#SecurityClearanceSelect').select2({
            id: '-1',
            placeholder: 'Select security clearance'
        });
        $('#certificateSelect').select2({
            id: '-1',
            placeholder: 'Select certificate'
        });
        $('#statusSelect').select2({
            id: '-1',
            placeholder: 'Select status',
            minimumResultsForSearch: -1
        });
        $('#customer_id').select2({
            id: '0',
            placeholder: 'Select Customer'
        });

        filterData();

        $("#filterbutton").on("click", function (e) {
            table.destroy();
            $('#selectAll').prop('checked', false);
            filterData();
        });

        $("#activeEmployee").on('click', function() {
            $('#filterbutton').click();
            if ($('#activeEmployee').is(":checked"))
            {
                $('.employee').show();
                $('.employeeAll').hide();
            } else {
                $('.employee').hide();
                $('.employeeAll').show();
                $('#employeeNameAllSelect').select2({
                    id: '-1',
                    placeholder: "Select employee"
                });
            }
        })
    });

    function filterData() {
        var employeeActive;
        var employeeIds;
        if ($('#activeEmployee').is(":checked"))
        {
            employeeActive = 1;
            employeeIds = $('#employeeNameSelect').val();
        } else {
            employeeActive = 0;
            employeeIds = $('#employeeNameAllSelect').val();
        }
        table = $('#resulttable').DataTable({
            processing: false,
            serverSide: true,
            responsive: true,
            dom: 'Blfrtip',
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                ['10', '25', '50', '100', 'All']
            ],
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Document Expiry Report - ' + today,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var expiry = new RegExp('Expired');
                        var expiryDayCheck = new RegExp('day*');
                        var expiryMonthCheck = new RegExp('month*');
                        var expiryYearCheck = new RegExp('year*');

                        $('row c[r^="H"]', sheet).each(function () {
                            if (expiry.test($('is t', this).text())) {
                                $(this).attr('s', '12');
                            } else if (expiryDayCheck.test($('is t', this).text())) {
                                $(this).attr('s', '22');
                            } else if (expiryMonthCheck.test($('is t', this).text())) {
                                $(this).attr('s', '19');
                            } else if (expiryYearCheck.test($('is t', this).text())) {
                                $(this).attr('s', '19');
                            }
                        });
                    }
                },
                {
                    text: 'Email',
                    action: function (e, dt, node, config) {
                        var checkedCheckboxes = $("input:checkbox[name=email]:checked").length;
                        if (checkedCheckboxes == 0) {
                            swal({
                                icon: 'warning',
                                title: 'Oops',
                                text: 'Please select employee',
                            });
                        } else {
                            showEmailModal();
                            $('#emailModal').trigger('click');
                        }
                    }
                }],
            ajax: {
                "url": "{{route('reports.getCertificateExpiryReport')}}",
                type: "GET",
                "data": {
                    userId: employeeIds,
                    securityClearanceLookUpId: $('#SecurityClearanceSelect').val(),
                    certificateMasterId: $('#certificateSelect').val(),
                    statusId: $('#statusSelect').val(),
                    activeEmployee: employeeActive,
                    customerId: $('#customer_id').val(),
                },
                'global': true,
                "error": function (xhr, textStatus, thrownError) {
                    if (xhr.status === 401) {
                        window.location = "{{ route('login') }}";
                    }
                },
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                status = (aData['status_color']).toLowerCase();
                /* Append the grade to the default row class name */
                if (status == "red") {
                    $(nRow).addClass('open');
                } else if (status == "yellow") {
                    $(nRow).addClass('in_progress');
                } else {
                    $(nRow).addClass('certificate');
                }
            },
            columnDefs: [
                {width: 1, targets: 0},
                {width: 110, targets: 1},
                {"orderable": true, "targets": [2]},
                {"orderable": true, "targets": "_all"}
            ],
            "order": [[2, "asc"]],
            columns: [
                {data: null, name: 'checkbox', sortable: false, render: function (data) {
                        return '<td><input type="checkbox" name="email" id="" data-employee_name=' + encodeURIComponent(data.employee_name) + ' data-employee_no='+data.employee_no+ ' data-email=' + data.email + ' data-document=' + encodeURIComponent(data.security_clearance_or_certificate) + ' data-expiry=' + encodeURIComponent(data.expiry_date) + ' data-status=' + encodeURIComponent(data.status.textDiff) + '></td>';
                    }},
                {data: 'employee_no', name: 'employee_no'},
                {data: 'employee_name', name: 'employee_name', },
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'security_clearance_or_certificate', name: 'security_clearance_or_certificate'},
                {data: 'expiry_date',
                    render: function (data, type) {
                        if ( type === 'display' || type === 'filter' ) {
                            return moment(data).format('MMM DD, YYYY');
                        }
                        return data;
                    }
                },
                {data: 'status.daysDiff',
                    render: function ( data, type, row ) {
                        if ( type === 'display' || type === 'filter') {
                        actions = '';
                        if(row.doc_details){
                        var view_url = '{{ route("filedownload", [":id",":module"]) }}';
                        view_url = view_url.replace(':id',row.doc_details.attachment_id);
                        view_url = view_url.replace(':module', 'documents');
                        actions+='<a class="download" target="_blank"  href="' + view_url + '">'+row.status.textDiff+'</a>'
                          }
                          else
                          {
                         actions+='<a class="download" title="No document uploaded" href="#">'+row.status.textDiff+'</a>'  
                          }
                          return actions ;
                        }
                        return data;
                    }
                },
                {data: 'valid_until_text', name: 'valid_until_text', visible: false},
                {data: 'status.textDiff', name: 'status.textDiff', visible: false},
            
                
            ]
        });
        table.on('page.dt', function() {
            $('html, body').animate({
            scrollTop: $("html").offset().top
            }, 'slow');
            $('#selectAll').prop('checked', false);
        });
    }


</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script> --}}
<script src="sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
@endsection

@section('css')
<style>
    #resulttable_wrapper {
        width: 100%;
    }
    .labelStyle {
        margin-top: 6px;
        float: left;
        margin-right: -15px;
    }
    html, body {
    max-width: 100%;
    overflow-x: hidden;
    }
    footer {
        position: fixed;
    }
    .swal2-styled.swal2-confirm {
            background-color: #003A63 !important;
        }
</style>
@endsection
