<!-- Site Details -->
<style>
    td.site-details-blue {
        background: #393f4f;
        color: white;
    }

    td.site-details-orange{
        background: #f36905;
        color: white;
    }

    td.site-details-green {
        background: #00b050;
        color: white;
    }

    td.site-details-red {
        background: #ff0000;
        color: white;
    }

    td.site-details-yellow {
        background: #ffff00;
        color: white;
    }

    td.site-details-black {
        background: black;
        color: white;
    }

    .site-detail-tbl td {
        border: 1px solid black;
    }

    .site-detail-tbl th {
        white-space: nowrap;
        border: 1px solid black !important;
    }
</style>
<script>
    widgets.define('widgetSiteDetails', function(payload) {
        let wc; //widget container
        let filters = payload.filters;
        const siteFilterKey = 'site-categories';

        function applyHeaderFilter() {
            let data = payload.data;
            //select box
            let options = `<option>--No site found--</option>`;
            if ((data.site_categories != null) && (data.site_categories != undefined)) {
                let sites = data.site_categories;
                //Generate options
                options = `<option value="">All</option>`;
                for(let site of sites) {
                    let selected = isFilterSelected(siteFilterKey, site.id) ? 'selected' : '';
                    options += `<option ${selected} value="${site.id}">${site.description}</option>`;
                }
            }
            let outupt = `<select class="w-sd-categories form-control">${options}</select>`;

            //Replace filter content with gen html
            $('body').find(`.${payload.widgetInfo.dataTargetId} .filter-content`).html(outupt);
        }

        function isFilterSelected(key, value) {
            let fv = filters[siteFilterKey];
            return fv == value;
        }

        function generateContent() {
            let data = payload.data;

            let content = ``;
            let tableBody = `<tr><td class="text-center">No data found</td></tr>`;
            if ((data.site_details.answers != null) && (data.site_details.answers != undefined)) {
                //table header
                let payPeriodHeader = ``;
                if ((data.site_details.pay_periods != null) && (data.site_details.pay_periods != undefined) && (data.site_details.pay_periods.length > 0)) {
                    let headers = data.site_details.pay_periods;
                    $.each(headers, function(key, payPeriod) {
                        payPeriodHeader += `<td class="site-details-orange text-center">${payPeriod}</td>`;
                    });
                }


                //table body
                let answers = data.site_details.answers;
                tableBody = (answers.length == 0)? '<tr><td class="text-center">No record</td></tr>':'';
                $.each(answers, function(indexKey, data) {
                    tableBody += `<tr><td class="site-details-blue">${indexKey}</td>`+payPeriodHeader+`</tr>`;
                    $.each(data, function(key, columns) {
                        if (key.length > 50) {
                            questionText = key.substr(0, 45) + `.....`;
                        } else {
                            questionText = key;
                        }
                        tableBody += `<tr><td class="sd-questions"  style="padding-left: 10px !important;" title="${key}">${questionText}</td>`;
                        $.each(columns, function(ky, row) {
                            tableBody += `<td class="site-details-${row}">&nbsp;</td>`;
                        });
                        tableBody += `</tr>`;
                    });
                });
            }

            return `<table class="table site-detail-tbl table-bordered tbl-line-height-1"><tbody>${tableBody}</tbody></table>`;
        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function refreshWithFilter() {
            $(".tooltip").remove();
            widgets.refresh(payload.widgetTag, filters);
        }

        function afterBind() {
            //After content render (eg:register envent listeners | init eg: select2)
            wc.find('.inner-page-nav').on('click', function() {
                window.open(payload.data.inner_page_url);
            });

            //Refresh widget on filter change
            wc.find(`.w-sd-categories`).on('change', function() {
                filters[siteFilterKey] = $(this).val();
                refreshWithFilter();
            });
            $(".sd-questions").tooltip();
        }

        applyHeaderFilter();
        bindContent(generateContent());
        afterBind();

        $(document).on("mouseover",".sd-questions",function(){
            $(this).tooltip();
        });
    });
</script>
