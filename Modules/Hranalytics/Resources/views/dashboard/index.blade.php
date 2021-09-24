@extends('layouts.landing')

@section('content')
<section class="tabbed-content">
    <div id="dashboard_content" style="margin-bottom: 1% !important;margin-top: -0.50em !important;height: 95%;margin-left:3px !important;margin-right:3px !important;">
        <div class="" id="dashboard_tab_element" style="margin-left: -8px !important;"></div>
        <div class="row" id="dashboard_tab_details"></div>
    </div>
</section>

@endsection
@section('scripts')
@include('partials.widgets.widgets')
<script type="text/javascript">
    //Un completed request pool
    $.xhrPool = [];

    function abortRequests() {
        $.xhrPool.forEach(function(jqXHR) {
            jqXHR.abort();
        });
        $.xhrPool = [];
    }

    //Filter cache
    let filterCache = new Map();

    const logger = {
        log(str) {
            console.log(str);
        },
        error(str) {
            console.error(str);
        },
        warn(str) {
            console.warn(str)
        }
    }

    //Route configruation
    const routes = {
        getTabs: "{{route('recruitment-analytics.tabs')}}",
        getDashboardDetails: "{{route('recruitment-analytics.tab-details')}}",
        //Widgets API's
    }
    //Dashboard script
    const dashboard = {
        data: {
            tabs: [],
            booted: false,
            activeTab: null,
        },
        reloadIframe() {
            // iframe reload on Dashboard
            if (document.getElementById('iframe') != undefined && document.getElementById('iframe').src != null) {
                document.getElementById('iframe').src += '';
            }
        },
        convertCamelToKebab(string) {
            return string.replace(/([a-z0-9]|(?=[A-Z]))([A-Z])/g, '$1-$2').toLowerCase();
        },
        transformObjectForApi(object) {
            let output = {};
            for (const [key, value] of Object.entries(object)) {
                output[this.convertCamelToKebab(key)] = value;
            }
            return output;
        },
        loadTabs() {
            let root = this;
            $.ajax({
                type: "GET",
                url: routes.getTabs,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                    $.xhrPool.push(jqXHR);
                },
                success: function(response) {
                    root.setupTabs(response.data);
                },
                complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                    let index = $.xhrPool.indexOf(jqXHR);
                    if (index > -1) {
                        $.xhrPool.splice(index, 1);
                    }
                }
            });
        },
        setupTabs(tabs) {
            //TODO:rewrite this function
            let root = this;
            this.data.tabs = tabs;
            //Bind tabs
            $('#dashboard_tab_element').html(root.buildTabs());
            //Register events for tab
            $('body').find('.arrow').hide();

            let tabs_count = $('body').find('.dashboard-tabs > .tab-config').length;
            if (tabs_count > 0) {
                load_tab_structure(1, 6);
                load_first_tab_content();
            }

            function load_tab_structure(minIndex, maxIndex) {
                $('body').find('.arrow').hide();
                $('body').find('.tab-config').hide();
                $('body').find('.arrow').addClass('hidden');
                $('body').find('.arrow').removeClass('show');
                $('body').find('.tab-config').removeClass('tab-end');
                $('body').find('.tab-config').removeClass('tab-start')

                let index = 1;
                $('body').find('.tab-config').each(function() {
                    if (index <= maxIndex && index >= minIndex) {
                        $('body').find('.tab_' + index).show();
                    } else {
                        $('body').find('.tab_' + index).hide();
                    }

                    if (index > maxIndex) {
                        $('body').find('.next-arrow').show();
                        $('body').find('.next-arrow').removeClass('hidden');
                        $('body').find('.next-arrow').addClass('show');
                    }

                    if (index < minIndex) {
                        $('body').find('.prev-arrow').show();
                        $('body').find('.prev-arrow').removeClass('hidden');
                        $('body').find('.prev-arrow').addClass('show');
                    }

                    if (index == maxIndex) {
                        $('body').find('.tab_' + index).addClass('tab-end');
                    }

                    if (index == minIndex) {
                        $('body').find('.tab_' + index).addClass('tab-start');
                    }
                    index++;
                });
                $('body').find('.tab-config').css('width', '16%');
                // $('body').find('.tab-config').css('width', '100%');

                //trim and limit tabs character content
                $('body').find('.tab-config').each(function() {
                    let tab_selected = $(this);
                    let tab_group = tab_selected.attr('data-tab-group');
                    let tabs_count = $('body').find('.tab_group_' + tab_group).length;
                    let calculated_character_length = parseInt(80 / tabs_count);
                    let tab_text = tab_selected.text().trim();
                    let tab_text_length = tab_text.length;
                    if (tab_text_length > calculated_character_length) {
                        tab_selected.text(tab_text.substring(0, calculated_character_length) + '..');
                    }
                });
            }

            function load_first_tab_content() {

                let firstTabId = $('body').find('.tab-config.tab-start').attr('data-tab');
                if (firstTabId !== "") {
                    selected_tab = firstTabId;
                    dashboard.loadTabDetails(selected_tab);
                }
            }

            $('body').find('.tab-config').on('click', function() {
                let selected_tab = $(this).attr('data-tab');
                let tabs_count = $('body').find('.dashboard-tabs > .tab-config').length;
                let end_tab = $(this).hasClass('tab-end');
                let start_tab = $(this).hasClass('tab-start');
                let tab_index = $(this).attr('data-tab-index');
                if (end_tab && tabs_count != tab_index) {
                    let minIndex = Number(tab_index) - 0;
                    let maxIndex = Number(tab_index) + 5;
                    load_tab_structure(minIndex, maxIndex);
                    dashboard.loadTabDetails(selected_tab);
                } else if (start_tab && tab_index >= 6) {
                    let minIndex = Number(tab_index) - 5;
                    let maxIndex = Number(tab_index) + 0;
                    load_tab_structure(minIndex, maxIndex);
                    dashboard.loadTabDetails(selected_tab);
                } else {
                    dashboard.loadTabDetails(selected_tab);
                }
            });

            $('body').find('.next-arrow').on('click', function() {
                let tab_index = $('body').find('.tab-end').attr('data-tab-index');
                let minIndex = Number(tab_index) + 1;
                let maxIndex = Number(tab_index) + 6;
                load_tab_structure(minIndex, maxIndex);
                load_first_tab_content();
            });

            $('body').find('.prev-arrow').on('click', function() {
                let tab_index = $('body').find('.tab-start').attr('data-tab-index');
                if (tab_index < 7) {
                    logger.log('in');
                    let minIndex = 1;
                    let maxIndex = 6;
                    load_tab_structure(minIndex, maxIndex);
                } else {
                    let minIndex = Number(tab_index) - 6;
                    let maxIndex = Number(tab_index) - 1;
                    load_tab_structure(minIndex, maxIndex);
                }

                load_first_tab_content();
            });
        },
        buildTabs() {
            let els = '';
            let tabGroup = '';
            this.data.tabs.forEach(function(data, i) {
                i++;
                let defaultTab = i == 1 ? 'dashboard-default-selected-tab' : '';
                tabGroup = parseInt(i / 7);

                els += `<a data-tab="${data.id}"
                    data-tab-group="${tabGroup}"
                    data-tab-index="${i}"
                    class="tab_${i} nav-item nav-link expense tab-config ${defaultTab} tab_group_${tabGroup}"
                    title="${data.name}"
                    id="tab_${data.id}"
                    href="javascript:void(0)">${data.name}
                </a>`;
            });

            return `<div id="dashboard-tab-area" class="nav nav-tabs expense dashboard-tabs" role="tablist" style="flex-wrap: inherit;">
                        <a class="nav-item nav-link arrow prev-arrow" data-tab-index="0" href="#">
                            <span style="color:white;">&#10229;</span>
                        </a>
                        ${els}
                        <a class="nav-item nav-link arrow next-arrow" data-tab-index="${this.data.tabs.length+1}" href="#">
                            <span style="color:white;">&#10230;</span>
                        </a>
                    </div>`;
        },
        async clearTabDetails() {
            $('.dashboard-tables').hide();
            return ($('.dashboard-tables').length > 0) ? $('.dashboard-tables').is(":hidden") : true;
        },
        async loadTabDetails(tabId) {
            let root = this;
            abortRequests();
            $(".image-wrapper").remove(); //for schedule widget

            //Hide all tabs
            if (!await root.clearTabDetails()) {
                logger.log(`Failed to hide/clear tab[${tabId}] details`);
                return;
            }

            let element_uid = 'tabuid_' + tabId;
            $('.tab-config').removeClass('active');
            $('#tab_' + tabId).addClass('active');

            let tabElement = document.getElementById(element_uid);
            if (tabElement) {
                clear_status = await root.clearTabDetails();
                $('#' + element_uid).show();

                $('.payperiod_element:visible').select2();
                $('.payperiod_element:visible').val($('.payperiod_element:visible').val()).trigger('change');
                $('.emp-schedule-no-data').show();
                $('.emp-schedule-with-data').hide();
            } else {
                let expandSideMenu = '';
                let parentDivWidth = $(window).width();
                let parentDivHeight = (($('#content').height() - $('#content').offset().top));

                $.ajax({
                    type: "GET",
                    url: routes.getDashboardDetails,
                    data: {
                        'tab_id': tabId,
                        'parentDivWidth': parentDivWidth,
                        'parentDivHeight': parentDivHeight,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#dashboard_tab_details').append(response.html);
                        $('.dashboard-tables').hide();
                        $('#' + element_uid).show();
                        logger.log(`----- Showing Tab: [${element_uid}] -----`);
                        root.loadWidgetFieldsOfTab(tabId);
                    }
                });
            }

        },
        loadDefaultSelectedTabContent() {
            let selected_tab = $('.dashboard-default-selected-tab').attr('data-tab');
            this.LoadTabDetails(selected_tab);
        },
        loadSingleWidget(tag, filters = {}) {
            let target = $(`.${tag}:visible`);
            if (target.length <= 0) {
                logger.log(`Loading single widget [${tag}] : element not found in dom`);
            }
            if (target.length > 1) {
                logger.error(`Load: ${target.length} item(s) found`);
            }
            //Do load widgets
            if (target.length == 1) {
                let attr = this.unpackWidgetInfo($(target).data('attr'));
                this.callWidgetApi(attr, filters);
            }
        },
        unpackWidgetInfo(base64dt) {
            return JSON.parse(atob(base64dt));
        },
        loadWidgetFieldsOfTab(tabId, useFilterCache = false) {
            let root = this;
            let widgets = $(`#tabuid_${tabId}`).find('.js-widget');

            if (widgets.length < 0) {
                logger.log(`Load: No widges detected for tab: ${tabId}`);
            }
            logger.log(`Load: Loading ${widgets.length} widgets for tab ${tabId}`);

            widgets.each(function(i, widget) {
                let filters = {};
                let widgetAttrs = root.unpackWidgetInfo($(widget).data('attr'));
                if (useFilterCache) {
                    filters = filterCache[widgetAttrs.dataTargetId];
                }
                let attr = {
                    ...widgetAttrs,
                    tabId
                }
                root.callWidgetApi(attr, filters);
            });
        },
        callWidgetApi(widgetInfo, filters = {}) {
            let root = this;

            if (widgetInfo === null || widgetInfo === undefined) {
                logger.error(`Load: Field details invalid widget data provided`);
            }

            //Cache filters into cache
            filterCache[widgetInfo.dataTargetId] = filters;
            //Send request to server
            $.ajax({
                type: "GET",
                url: widgetInfo.dataApiUrl,
                bFilter: true,
                async: true,
                data: root.transformObjectForApi({
                    ...widgetInfo,
                    ...filters,
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                    $.xhrPool.push(jqXHR);
                },
                success: function(response) {
                    //For new widgets
                    if (response.type !== undefined && response.type === 'json') {
                        //add filters to the payload
                        response.filters = filters;
                        //add widget info
                        response.widgetInfo = widgetInfo;
                        //Generte widgets using factory
                        try {
                            WidgetFactory.build(response.widgetTag, response);
                        } catch (e) {
                            logger.error(e)
                        }
                        return;
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    //TODO:check
                    if (textStatus === "abort") {
                        $('#tabuid_' + widgetInfo.tabId).remove();
                    }
                },
                complete: function(jqXHR) { // when some of the requests completed it will splice from the array
                    let index = $.xhrPool.indexOf(jqXHR);
                    if (index > -1) {
                        $.xhrPool.splice(index, 1);
                    }
                }
            });

        },
        init() {
            let root = this;
            $.fn.dataTable.ext.errMode = 'throw';

            //load dynamic contents
            this.loadTabs();
        }
    };

    const WidgetFactory = {
        _camelize(str) {
            let arr = str.split('-');
            let capital = arr.map((item, index) => index ? item.charAt(0).toUpperCase() + item.slice(1).toLowerCase() : item);
            // ^-- change here.
            return capital.join("");
        },
        //Generating widgets by type and payload
        build(widgetTag, payload) {
            logger.log(`Gen: Building widget tag: [${widgetTag}] of data type ${typeof payload.data}`);

            let targetFn = this._camelize(widgetTag);

            logger.log(`Gen: Trying to run build function: [${targetFn}]`);

            if (widgets.hasOwnProperty(targetFn)) {
                widgets[targetFn](payload);
            } else {
                logger.error(`Gen: widgets does not contain function ${targetFn}`);
            }
        }
    };

    const dsCore = {
        clock: 0,
        interval_msec: 1000,
        date: null,
        datetimeformat(date_obj, onlytime) {
            if (onlytime) {
                let hr_split_arr = date_obj.split(":");
                datetime_str = hr_split_arr[0] + ':' + hr_split_arr[1];
                return datetime_str;
            }
            let date_str = date_obj.getDate();
            if (date_str < 10) date_str = '0' + date_str;
            let month_str = (date_obj.getMonth()) + 1;
            if (month_str < 10) month_str = '0' + month_str;
            let year_str = date_obj.getFullYear();
            let hour_str = date_obj.getHours();
            if (hour_str < 10) hour_str = '0' + hour_str;
            let minute_str = date_obj.getMinutes();
            if (minute_str < 10) minute_str = '0' + minute_str;
            let datetime_str = year_str + '-' + month_str + '-' + date_str + ' ' + hour_str + ':' + minute_str;
            return datetime_str;
        },
        tConvert(time) {
            if (!time) {
                return ''
            }
            // Check correct time format and split into components
            time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];
            if (time.length > 1) { // If time format correct
                time = time.slice(1); // Remove full string match value
                time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
                time[0] = +time[0] % 12 || 12; // Adjust hours
            }
            time.splice(3, 1);
            return time.join(''); // return adjusted time or original string
        },
        updateClock() {
            let root = this;
            // clear timer
            clearTimeout(this.clock);
            if (this.date == null) {
                this.date = new Date('{{ \Carbon::now() }}');
            } else {
                this.date = new Date(this.date.getTime() + 1 * 1000);
            }
            //logger.log(date);
            let hours = Number(this.date.getHours());
            let minutes = Number(this.date.getMinutes());
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            let strTime = (('0' + hours).slice(-2)) + ' : ' + (('0' + minutes).slice(-2)) + ' ' + ampm;
            $("#myclock").html(strTime);
            // set timer
            this.clock = setTimeout(function() {
                root.updateClock();
            }, this.interval_msec);
        },
        init() {
            let root = this;
            let isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            if (!isSafari) {
                clock = setTimeout(function() {
                    root.updateClock();
                }, root.interval_msec);
            }
            $('#content .row').each(function() {
                let c = 12 / Number($(this).find('.card-table').length);
                $(this).find('.card-padding').addClass('col-lg-' + c).addClass('col-lg-' + c);
            });
        }
    }

    //Run on document ready
    $(function() {
        dsCore.init();
        dashboard.init();
    });
</script>
@endsection
