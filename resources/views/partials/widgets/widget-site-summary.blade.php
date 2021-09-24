<!-- Site Summary -->
<style>
    .site-summary-tbl td,th {
        border: none !important;
    }

    td.site-summary-bg-red,
    th.site-summary-bg-red {
        color: white;
        background-color: #f23c22;
        vertical-align: middle !important;
    }

    td.site-summary-label {
        background: #393f4f;
        color: white;
        white-space: nowrap !important;
    }

    .detla-green-value-color {
        background: #00b050;
        color: white;
    }

    .detla-red-value-color {
        background: #f23c22;
        color: white;
    }

    .site-summary-values {
        white-space: nowrap !important;
    }
</style>

<script>
    widgets.define('widgetSiteSummary', function(payload) {
        let wc; //widget container

        function timeToDecimal(t) {
            let arr = t.toString().split(':');
            let dec = parseInt((arr[1]/6)*10, 10);
            return parseFloat(parseInt(arr[0], 10) + '.' + (dec<10?'0':'') + dec);
        }

        function decimalToTime(time) {
            return (function(i) {return i + (Math.round(((time-i) * 60), 10) / 100);})(parseInt(time, 10));
        }

        function generateContent() {
            let output = {
                siteNumber: '',
                client: '',
                address: '',
                fmName: '',
                fmPhone: '',
                fmEmail: '',
                amName: '',
                amPhone: '',
                amEmail: '',
                suName: '',
                suPhone: '',
                suEmail: '',
                hpw: 0,
                hpwActual: 0,
                delta: 0,
                deltaColor: ''
            };

            let data = payload.data;
            if ((data.customer != null) && (data.customer != undefined)) {
                if (data.customer.details != null) {
                    let cd = data.customer.details;
                    output.siteNumber = cd.project_number? cd.project_number:'';
                    output.client = cd.client_name? cd.client_name: '';
                    output.address = cd.billing_address? cd.billing_address: '';
                    output.fmName = cd.contact_person_name? cd.contact_person_name: '';
                    output.fmPhone = cd.contact_person_phone? cd.contact_person_phone: '';
                    output.fmEmail = cd.contact_person_email_id? cd.contact_person_email_id: '';
                }

                if ((data.customer.areamanager != null) && (data.customer.areamanager != undefined)) {
                    let am = data.customer.areamanager;
                    output.amName = am.first_name? am.first_name: '';
                    output.amName += am.last_name? ' ' + am.last_name: '';
                    output.amEmail = am.email? am.email: '';
                    output.amPhone = am.phone? am.phone: '';
                }

                if ((data.customer.supervisor != null) && (data.customer.supervisor != undefined)) {
                    let su = data.customer.supervisor;
                    output.suName = su.first_name? su.first_name: '';
                    output.suName += su.last_name? ' ' + su.last_name: '';
                    output.suEmail = su.email? su.email: '';
                    output.suPhone = su.phone? su.phone: '';
                }
            }

            if ((data.hpw != null) && (data.hpw != undefined)) {
                let hpw = data.hpw;
                output.hpw = hpw.hours_per_week ? parseFloat(hpw.hours_per_week).toFixed(2) : 0;
                output.hpwActual = parseFloat(timeToDecimal(hpw.actual_hours)).toFixed(2);
                output.delta = parseFloat((output.hpwActual - output.hpw)).toFixed(2);
                output.deltaColor = (parseInt(output.delta) >= 0) ?
                    'detla-green-value-color' :
                    'detla-red-value-color';
            }

            //Select box filter
            let filter = `<select class="form-control site_summary_select" id="site_summary_select">
                                <option>Current</option>
                         </select>`;

            //Return generated html
            return `
            <table class="table site-summary-tbl tbl-line-height-1">
                <thead>
                    <tr>
                        <th class="site-summary-bg-red" colspan="4" style="padding-left: 10px !important;">Profile</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">Site Number</td>
                        <td class="site-number site-summary-values" title="${output.siteNumber}">${limitStringValue(output.siteNumber)}</td>
                        <td class="site-summary-label">Area Manager</td>
                        <td class="area-mgmr site-summary-values" title="${output.amName}">${limitStringValue(output.amName)}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">Client</td>
                        <td class="client site-summary-values" title="${output.client}">${limitStringValue(output.client)}</td>
                        <td class="site-summary-label">AM Phone</td>
                        <td class="am-phone site-summary-values" title="${output.amPhone}">${limitStringValue(output.amPhone)}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">Address</td>
                        <td class="address site-summary-values" title="${output.address}">${limitStringValue(output.address)}</td>
                        <td class="site-summary-label">AM Email</td>
                        <td class="am-email site-summary-values" title="${output.amEmail}">${limitStringValue(output.amEmail)}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">Client Contact Name</td>
                        <td class="fm-name site-summary-values" title="${output.fmName}">${limitStringValue(output.fmName)}</td>
                        <td class="site-summary-label">Supervisor</td>
                        <td class="su-name site-summary-values" title="${output.suName}">${limitStringValue(output.suName)}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">CC Phone</td>
                        <td class="fm-phone site-summary-values" title="${output.fmPhone}">${limitStringValue(output.fmPhone)}</td>
                        <td class="site-summary-label">SU Phone</td>
                        <td class="su-phone site-summary-values" title="${output.suPhone}">${limitStringValue(output.suPhone)}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">CC Email</td>
                        <td class="fm-email site-summary-values" title="${output.fmEmail}">${limitStringValue(output.fmEmail)}</td>
                        <td class="site-summary-label">SU Email</td>
                        <td class="su-email site-summary-values" title="${output.suEmail}">${limitStringValue(output.suEmail)}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-bg-red" colspan="3" style="padding-left: 10px !important;">Hours Per Week</td>
                        <td class="site-summary-bg-red site-filter">${filter}</td>
                    </tr>
                    <tr>
                        <td class="site-summary-label" style="padding-left: 10px !important;">HPW</td>
                        <td class="hpw site-summary-values">${output.hpw}</td>
                        <td class="site-summary-label">Actual</td>
                        <td class="hpw-actual site-summary-values">${output.hpwActual}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="site-summary-label text-center">Delta</td>
                        <td colspan="2" class="text-center detla ${output.deltaColor}">${output.delta}</td>
                    </tr>
                </tbody>
            </table>
            `;

        }

        function bindContent(el) {
            wc = $('body').find(`.${payload.widgetInfo.dataTargetId}`);
            wc.find('.dasboard-card-body').html(el);
        }

        function afterBind() {
            // After content render (eg:register envent listeners | init eg: select2)
            wc.find('.inner-page-nav').on('click', function() {
                window.open(payload.data.inner_page_url);
            });
            $(".site-summary-values").tooltip();
        }
        //Main
        bindContent(generateContent());
        afterBind();

        $(document).on("mouseover",".site-summary-values",function(){
            $(this).tooltip();
        });

        function limitStringValue(strValue, limit = 25) {
            return (strValue.length > limit)? strValue.substring(0,(limit-3)) + '...': strValue;
        }
    });
</script>
