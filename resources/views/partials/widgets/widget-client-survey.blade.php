<style>
    #clientsurveytable_paginate{
        margin-top:15px !important;
    }

    #clientsurveytable_paginate{
        margin-top:15px !important;
    }
    .pull-left{
        width:93% !important;
    }
    .notesclass{
        white-space: normal !important;
    }
</style>
<!-- Demo Widget -->
<script>
    //Use function name as camalCase version of WidgetTag
    //Always use let instead of var
    widgets.define('widgetClientSurvey',function(payload) {
        let wc; //widget container


        function generateContent() {

            let datacontent = jQuery.parseJSON(payload.data);
            let databody = ""
            let i=1;
            datacontent.forEach(function(value) {
                databody+=`<tr><td>${i}</td>
                    <td>${value["client_name"]}</td>
                    <td>${value["client_contact"]}</td>
                    <td>${value["phone"]}</td>
                    <td>${value["rating"]}</td>
                    <td class="notesclass">${value["notes"]}</td>
                    <td>${value["created_by"]}</td>
                    <td>${value["created_at"]}</td>
                    </tr>`
                i++;
            });

            let content = `

<table class="table table-bordered "
          id="clientsurveytable" style="margin-left:4px !important;margin-top:5px !important">
            <thead>

                    <th>#</th>
                    <th>Client Name</th>
                    <th>Client Contact</th>
                    <th>Phone Number</th>
                    <th>Rating</th>
                    <th>Notes</th>
                    <th>Reviewed By</th>
                    <th>Date & Time</th>

            </thead>

            <tbody>
                ${databody}
            </tbody>
        </table>
            `;
            //..process
            return content;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.filter-content').html(`<input type="text"
            placeholder="Search" name="searchsurvey" style="max-width:40%!important;float:right" class="form-control searchsurveytext" />`);
            wc.find('.dasboard-card-body').html(el);
            $.fn.dataTable.ext.errMode = 'throw';
            var oTable = wc.find('#clientsurveytable').DataTable({
                fixedHeader: true,

            });
            wc.find('.dataTables_filter').hide()
            //wc.find('.dataTables_filter').addClass("pull-left");
            wc.find('.searchsurveytext').on("keyup",function(e){
                e.preventDefault();
                oTable.search($(this).val()).draw() ;
            })

        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            wc.on('click', function() {
                //alert('Widget body click');
            });

        }



        //Bind contents
        bindContent(generateContent());

        //Execute after content is added to dom
        afterBind();
    });
</script>
