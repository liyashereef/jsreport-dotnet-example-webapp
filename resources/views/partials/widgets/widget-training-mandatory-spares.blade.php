<!-- Training Spares -->
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
    .equal-width {   width: 100%; }


.firstcellwidthspares{
    position: relative;
    background: #003b63;
}

.secondColspares{
    position: relative !important;
    background:white;
    z-index: 300;

}
.firstcellwidthspares{
        width:350px !important;
    }

    .cellwidthspares{
        min-width:350px !important  
    }

</style>

<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetMandatoryTrainingSpares',function(payload) {
        let wc; //widget container

        function generateContent() {
            let data = payload.data;
            let contentsparesDivId=payload.widgetInfo.dataTargetId+"table";

            //table header
            let tableHeader = ``;
            let tableBody = ``;
            if(data.tableDetails.name.length && data.tableDetails.details.length) {
                tableHeader =
                `<thead>
                    <tr>
                        <th class="firstcolspares cellwidthspares mandatoryHeading">Mandatory Courses</th>
                        <th class="seccolspares cellwidthspares guardHeading">Deadline</th>`;

                let headers = data.tableDetails.name;
                let guardNameShort;
                $.each(headers, function(key, guardName){
                    guardNameShort = guardName.first_name.length > 8 ? guardName.first_name.slice(0,4)+'...' : guardName.first_name;
                    tableHeader +=`<th class="cellwidthspares guardHeading titlename" title="${ guardName.full_name }">${ guardNameShort }</th>`;
                });
                tableHeader += `</tr></thead>`;

                //table body
                if(data.tableDetails.details.length) {
                    tableBody = `<tbody>`;
                    let body = data.tableDetails.details;
                    let courseNameShort;
                    $.each(body, function(key,columns) {
                        courseNameShort = columns['course'].length > 17 ? columns['course'].slice(0,15)+'...' : columns['course'];
                        tableBody += `<tr>
                            <td class="firstcellwidthspares heading titlename" title="${ columns['course'] }">${ courseNameShort }</td>
                            <td class="secondColspares cellwidthspares courseDueDate">${columns['course_due_date']}</td>`;
                        $.each(columns['allocation_data'], function(key, row) {
                            if (row['completed_date'] === '00-MMM-00') {
                                tableBody += `<td class="cellwidthspares unattended" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };">${ row['completed_date'] }</td>`;
                            } else {
                                tableBody += `<td class="cellwidthspares incomplete titlename" style="background-color: ${ row['color_code'] }; color: ${ row['color_code'] };" title="${ row['completed_date'] }">${ row['completed_date'] }</td>`;
                            }
                        });
                        tableBody += `</tr>`;
                    });
                    tableBody +=`</tbody>`;
                }

            } else {
            }

            return `<table id="${contentsparesDivId}"  class="table equal-width tbl-line-height-1">${tableHeader}${tableBody}</table>`;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            let contentSparesDivId=payload.widgetInfo.dataTargetId+"content";
            wc.find('.dasboard-card-body').attr("id",contentSparesDivId)
            wc.find('.dasboard-card-body').html(el);
        }

        function bindCustomWidgetTitle() {
            wc.find('.widget-mandatory-training-spares-tittle').text('Mandatory Training - Spares');
        }

        function afterBind() {
            wc.find('.inner-page-nav').on('click', function() {
                window.open(payload.data.inner_page_url);
            });

            $(".titlename").tooltip()
            processSparesTableFreeze(wc);

        }

        function processSparesTableFreeze(wc){
                let contentsparesDivId=payload.widgetInfo.dataTargetId+"content";
                let contentsparesTableId=payload.widgetInfo.dataTargetId+"table";
                

                // $("#"+divId+" .dasboard-card-body").bind('scroll', function() {
                //     console.log('Event worked');
                //     debugger
                // }); 
                // console.log(contentsparesDivId)
                // debugger
                $("#"+contentsparesDivId).scroll(function(e) { //detect a scroll event on the tbody
                /*
                Setting the thead left value to the negative valule of tbody.scrollLeft will make it track the movement
                of the tbody element. Setting an elements left value to that of the tbody.scrollLeft left makes it maintain 			it's relative position at the left of the table.    
                */
                // alert("eve")
                wc.find('th').css("cssText","position: sticky;top: 0;z-index:299");
                wc.find('.firstcolspares').css("cssText","position: relative;z-index:300");
                wc.find('.seccolspares').css("cssText","position: relative;z-index:400");

                let calculatedSparesLeft=$("#"+contentsparesDivId).scrollLeft();
                let calculatedSparesTop=$("#"+contentsparesDivId).scrollTop();

                wc.find('.firstcellwidthspares').css("left",calculatedSparesLeft+"px"); //fix the first cell of the header
                wc.find('.secondColspares').css("left",calculatedSparesLeft+"px"); //fix the first cell of the header
                
                wc.find('.firstcolspares').css("left",calculatedSparesLeft+"px"); //fix the first cell of the header
                wc.find('.firstcolspares').css("top",calculatedSparesTop+"px"); //fix the first cell of the header

                wc.find('.seccolspares').css("left",calculatedSparesLeft+"px"); //fix the first cell of the header
                wc.find('.seccolspares').css("top",calculatedSparesTop+"px"); //fix the first cell of the header

            });
            $("#"+contentsparesDivId).trigger("scroll");
        }


        //Bind contents
        bindContent(generateContent());
        bindCustomWidgetTitle();

        //Execute after content is added to dom
        afterBind();

        $(document).on("mouseover",".titlename",function(){
            $(this).tooltip();
        })
    });
</script>
