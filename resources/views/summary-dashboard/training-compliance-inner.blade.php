@extends('layouts.app')

@section('content')

<!-- Training Permanent -->
<style>
    .mandatoryHeading {
        background-color: #003b63;
        color: white;
        white-space: nowrap;
        width: 10px;
        padding-left: 44px !important;
        padding-right: 44px !important;
    }

    .heading {
        background-color: #003b63;
        color: white;
        cursor: pointer;
        white-space: nowrap;
        padding-left: 44px !important;
    }

    .guardHeading {
        background-color: #f36905;
        color: white;
        cursor: pointer;
        text-align: center;
        border: 1px solid white !important;
        white-space: nowrap;
        z-index: 400;
    }

    .unattended {
        border: 1px solid black;
        white-space: nowrap;
    }

    .incomplete {
        cursor: pointer;
        border: 1px solid black;
        white-space: nowrap;
    }

    .courseDueDate {
        border: 1px solid black;
        white-space: nowrap;
        text-align: center;
    }

    .equal-width {
        width: 100%;
    }

    .firstcellwidth {
        position: relative;
        background: #003b63;
    }

    .secondCol {
        position: relative !important;
        background: white;
        z-index: 300;

    }

    .firstcellwidth {
        width: 350px !important;
    }

    .cellwidth {
        min-width: 350px !important
    }

    #training-container {
        max-height: 80vh;
        max-width: 100%;
        overflow: auto;
    }

    .custom-alert {
        background-color: #333f50;
        color: white;
        max-height: 50px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="table_title">
                <h4>Training Compliance - Mandatory Courses</h4>
            </div>
        </div>
        <div class="col-md-4">
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="js-training-section" id="training-container"></div>
        </div>
    </div>
</div>

<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    const trainingWidget = function(payload) {
        let wc; //widget container

        function generateContent() {
            let data = payload.data;

            //table header
            let tableHeader = ``;
            let tableBody = ``;
            let contentTableId = 'training-table';

            if (data.tableDetails.details.length <= 0) {
                return `
                <div class="alert alert-light custom-alert" role="alert">
                    <h6>Please select atleast one customer</h5>
                </div>
                `;
            }
            if (data.tableDetails.name.length && data.tableDetails.details.length) {
                tableHeader =
                    `<thead>
                    <tr>
                        <th class="firstcol cellwidth mandatoryHeading">Mandatory Courses</th>
                        <th class="seccol cellwidth guardHeading">Deadline</th>`;

                let headers = data.tableDetails.name;
                let guardNameShort;
                $.each(headers, function(key, guardName) {
                    // guardNameShort = guardName.first_name.length > 8 ? guardName.first_name.slice(0, 4) + '...' : guardName.first_name;
                    guardNameShort = guardName.first_name;
                    tableHeader += `<th class="cellwidth guardHeading titlename" title="${ guardName.full_name }">${ guardNameShort }</th>`;
                });
                tableHeader += `</tr></thead>`;

                //table body
                if (data.tableDetails.details.length) {
                    tableBody = `<tbody>`;
                    let body = data.tableDetails.details;
                    let courseNameShort;
                    $.each(body, function(key, columns) {
                        // courseNameShort = columns['course'].length > 17 ? columns['course'].slice(0, 15) + '...' : columns['course'];
                        courseNameShort = columns['course'];
                        tableBody += `<tr>
                            <td class="firstcellwidth heading titlename" title="${ columns['course'] }">${ courseNameShort }</td>
                            <td class="secondCol cellwidth courseDueDate">${columns['course_due_date']}</td>`;
                        $.each(columns['allocation_data'], function(key, row) {
                            if (row['completed_date'] === '00-MMM-00') {
                                tableBody += `<td class="cellwidth unattended" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };">${ row['completed_date'] }</td>`;
                            } else {
                                tableBody += `<td class="cellwidth incomplete titlename" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };" title="${ row['completed_date'] }">${ row['completed_date'] }</td>`;
                            }
                        });
                        tableBody += `</tr>`;
                    });
                    $.each(body, function(key, columns) {
                        courseNameShort = columns['course'].length > 17 ? columns['course'].slice(0, 15) + '...' : columns['course'];
                        tableBody += `<tr>
                            <td class="firstcellwidth heading titlename" title="${ columns['course'] }">${ courseNameShort }</td>
                            <td class="secondCol cellwidth courseDueDate">${columns['course_due_date']}</td>`;
                        $.each(columns['allocation_data'], function(key, row) {
                            if (row['completed_date'] === '00-MMM-00') {
                                tableBody += `<td class="cellwidth unattended" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };">${ row['completed_date'] }</td>`;
                            } else {
                                tableBody += `<td class="cellwidth incomplete titlename" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };" title="${ row['completed_date'] }">${ row['completed_date'] }</td>`;
                            }
                        });
                        tableBody += `</tr>`;
                    });
                    $.each(body, function(key, columns) {
                        courseNameShort = columns['course'].length > 17 ? columns['course'].slice(0, 15) + '...' : columns['course'];
                        tableBody += `<tr>
                            <td class="firstcellwidth heading titlename" title="${ columns['course'] }">${ courseNameShort }</td>
                            <td class="secondCol cellwidth courseDueDate">${columns['course_due_date']}</td>`;
                        $.each(columns['allocation_data'], function(key, row) {
                            if (row['completed_date'] === '00-MMM-00') {
                                tableBody += `<td class="cellwidth unattended" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };">${ row['completed_date'] }</td>`;
                            } else {
                                tableBody += `<td class="cellwidth incomplete titlename" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };" title="${ row['completed_date'] }">${ row['completed_date'] }</td>`;
                            }
                        });
                        tableBody += `</tr>`;
                    });
                    $.each(body, function(key, columns) {
                        courseNameShort = columns['course'].length > 17 ? columns['course'].slice(0, 15) + '...' : columns['course'];
                        tableBody += `<tr>
                            <td class="firstcellwidth heading titlename" title="${ columns['course'] }">${ courseNameShort }</td>
                            <td class="secondCol cellwidth courseDueDate">${columns['course_due_date']}</td>`;
                        $.each(columns['allocation_data'], function(key, row) {
                            if (row['completed_date'] === '00-MMM-00') {
                                tableBody += `<td class="cellwidth unattended" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };">${ row['completed_date'] }</td>`;
                            } else {
                                tableBody += `<td class="cellwidth incomplete titlename" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };" title="${ row['completed_date'] }">${ row['completed_date'] }</td>`;
                            }
                        });
                        tableBody += `</tr>`;
                    });
                    tableBody += `</tbody>`;
                }

            } else {}

            return `<table id="${contentTableId}" class="table equal-width tbl-line-height-1">${tableHeader}${tableBody}</table>`;
        }

        function bindContent(el) {
            $('#training-container').html(el);
        }

        function afterBind() {
            $(".titlename").tooltip()
            processTableFreeze(wc);
        }

        function processTableFreeze(wc) {
            let contentDivId = 'training-container';
            let contentTableId = 'training-table';


            // $("#"+divId+" .dasboard-card-body").bind('scroll', function() {
            //     console.log('Event worked');
            //     debugger
            // }); 
            $("#" + contentDivId).scroll(function(e) { //detect a scroll event on the tbody
                /*
                Setting the thead left value to the negative valule of tbody.scrollLeft will make it track the movement
                of the tbody element. Setting an elements left value to that of the tbody.scrollLeft left makes it maintain 			it's relative position at the left of the table.    
                */
                let wc = $('#training-container');
                wc.find('th').css("cssText", "position: sticky;top: 0;z-index:299");
                wc.find('.firstcol').css("cssText", "position: relative;z-index:300");
                wc.find('.seccol').css("cssText", "position: relative;z-index:400");

                let calculatedLeft = $("#" + contentDivId).scrollLeft();
                let calculatedTop = $("#" + contentDivId).scrollTop();

                wc.find('.firstcellwidth').css("left", calculatedLeft + "px"); //fix the first cell of the header
                wc.find('.secondCol').css("left", calculatedLeft + "px"); //fix the first cell of the header

                wc.find('.firstcol').css("left", calculatedLeft + "px"); //fix the first cell of the header
                wc.find('.firstcol').css("top", calculatedTop + "px"); //fix the first cell of the header

                wc.find('.seccol').css("left", calculatedLeft + "px"); //fix the first cell of the header
                wc.find('.seccol').css("top", calculatedTop + "px"); //fix the first cell of the header
                console.log(calculatedLeft, calculatedTop)
            });
            $("#" + contentDivId).trigger("scroll");

        }


        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();

        $(document).on("mouseover", ".titlename", function() {
            $(this).tooltip();
        })
    }
</script>

@stop


@section('scripts')

<script>
    const gp = {

        init() {
            let q = globalUtils.uraQueryParamToJson(window.location.href);
            q.cIds = globalUtils.decodeFromCsv(q.cIds);
            $.get({
                url: "{{route('training-course-widget', ['mandatory' => 1, 'spares' => 0])}}",
                type: "GET",
                data: q,
                global: false,
                success: function(res) {
                    trainingWidget(res);
                },
            });
        }
    };

    //Document ready init
    $(function() {
        gp.init();
    });
</script>

@stop