
$(window).on('load', function () {
    // executes when complete page is fully loaded, including all frames, objects and images
    $('#sidebar').css('height', $('#content-div').height() + 60);
    $('#sidebar').css('min-height', '92vh');
});
$(function () {
    $('#sidebar').css('height', $('#content-div').height() + 60);
    $('#sidebar').css('min-height', '92vh');
    $('button[type="submit"]').prop("disabled", false);
    $(".phone").mask("(999)999-9999");
    $(".phone-mask").mask("(000)000-0000");
    $(".phone-w-s").mask("(999) 999-9999");
    $(".datepicker").mask("9999-99-99");
    //$(".datepicker-startdate").mask("99-99-9999");
    $(".postal-code").mask("a9a9a9");
    $(".postal-code-mask").mask("S0S0S0");
    $(".project-number").mask("9999999");
    $(".time").mask("99:99 aa");
    if ($('#timepicker').length > 0) {
        $('#timepicker').timepicki();
    }

    var _rLabels = $('input,textarea,select').filter('[required]')
        .parents('.form-group')
        .find('label:first');
    _rLabels.each(function (i, item) {
        if ($(item).find('span.mandatory').length <= 0) {
            $(item).append('<span class="mandatory">*</span>');
        }
    });

    /*Datepicker scroll bar issue - Start*/
    $(".modal").scroll(function () {
        $('.datepicker').blur();
    });

    /*Datepicker scroll bar issue - End*/
    $('.datepicker').not('.gj-textbox-md').each(function () {
        $(this).datepicker({
            format: "yyyy-mm-dd",
            //maxDate: "+900y",
            showOtherMonths: true
        });
    });

    /*Datepicker month and year issue - End*/
    $('.datepicker-startdate').each(function () {
        $(this).datepicker({

            format: "mm-dd",
            changeYear: false,
            //maxDate: "+900y",
            showOtherMonths: true
        }).focus(function () {
            $(".ui-datepicker-year").hide();
        });
    });
});
/**/

// To make Pace works on Ajax calls
$(document).ajaxStart(function () {
    $('body').find('.button').prop('disabled', true);
    try {
        $('body').loading({
            stoppable: false,
            message: 'Please wait...'
        });
    } catch (e) {
        console.error(e);
    }
});
$(document).ajaxComplete(function () {
    $('body').find('.button').prop('disabled', false);
    $('body').loading('stop');
    $('input.datepicker').each(function () {
        if (!$(this).hasClass("gj-textbox-md")) {
            console.log('date picker ajax', this);
            $(this).datepicker({
                format: "yyyy-mm-dd",
                showOtherMonths: true
            });
        }
    });
    refreshSideMenu();
});
$(".nav-link").click(refreshSideMenu);
// refresh side menu on datatable length change
$(document).on('change', 'div.dataTables_length select', refreshSideMenu);

function refreshSideMenu() {
    setTimeout(function () {
        $('#sidebar').hide();
        $('#sidebar').css('height', $('#content-div').height() + 150);
        $('#sidebar').show();
        $('#sidebar').css('height', $('#content-div').height() + 100);
        $("table.dataTable").DataTable().columns.adjust();
    }, 200);
}



/*
 * Function for showing validation messages in bootstarp modal
 * error: json error messages $form: form ID
 * */
function associate_errors(errors, $form, multimodal) {
    var $group;
    $form.find('.form-group').removeClass('has-error').find('.help-block').text('');
    $.each(errors, function (key, value) {
        if (null != multimodal && multimodal) {
            key = key.replace('.', '_');
        }
        $group = $form.find("[id='" + key + "']");
        $group.addClass('has-error').find('.help-block').text(value[0]);
    });
    if ($group.length > 0) {
        $('html, body').animate({
            scrollTop: $(".has-error").offset().top - 120
        }, 1000);
    }
}


/**
 * Get lattitude and longitude
 *
 * @param {string} address
 */
function getLocationCoordinate(address) {
    var position = null;
    var googleApiKey = "AIzaSyCcQJW9vzC7cLEaekdaJcC0H-dlJ8lRUMs";
    //console.log('getLocationCoordinate called');
    if (address != null) {
        var postal_code = address.toUpperCase().replace(/\W/g, '').replace(/(...)/, '$1 ');
        //console.log('inside of  address != null ', address, postal_code);
        $.getJSON({
            url: 'https://maps.google.com/maps/api/geocode/json',
            //type: 'GET',
            data: {
                key: googleApiKey,
                address: postal_code,
                sensor: false
            },
            async: false,
            success: function (data, textStatus) {
                //console.log('inside of  success ' + textStatus, data);
                try {
                    position = data.results[0].geometry.location;
                    console.log('Located Postal Code :' + postal_code);
                } catch (err) {
                    console.log('Unable to locate :' + postal_code);
                }
            },
            fail: function (result) {
                console.log("error ", result);
            }
        });
    }
    return position;
}

/** Update lattitude and longitude */

function updateLatLong(model, url, resource_id, latLng) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        },
        url: url,
        type: 'GET',
        data: {
            'model': model,
            'id': resource_id,
            'lat': latLng.lat,
            'lng': latLng.lng
        },
        success: function (data) {
            if (!data.success) {
                console.log('Can not able to update location co-ordinates');
            }
        },
        fail: function (response) {
            console.log('Failed Resopnse');
            console.log(response);
        }
    });
}

/**
 * Open a modal popup
 *
 * @param {*} id
 */
function openModal(id) {
    $('#myModal form')[0].reset();
    $('#myModal').find('input[name="id"]').val(id);
    $('#myModal').modal();
}

/**
 *
 * Email grid content: Will load the email client with prefilled message body
 * @param {datattable} table description
 * @param {string} subject email-subject
 *
 */
function emailContent(table, subject) {
    var data = table.buttons.exportData({
        "stripHtml": true,
        "columns": ':visible',
        "modifier": {
            "selected": true
        }
    });
    var headerArray = data.header;
    var rowsArray = data.body;
    var innerRowItem = '';
    for (var h = 0, hen = rowsArray.length; h < hen; h++) {
        var innerRowsArray = rowsArray[h];
        for (var i = 0, ien = innerRowsArray.length; i < ien; i++) {
            var outerCount = [i];
            var checker = 'false';
            for (var j = 0, jen = headerArray.length; j < ien; j++) {

                if (!["Action", "Actions", "Unallocate"].includes($.trim(headerArray[i]))) {
                    if (outerCount = [j] & checker == 'false') {
                        checker = 'true';
                        innerRowItem += headerArray[i];
                    }
                }
            }
            if (innerRowsArray[i] != '') {
                innerRowItem += ': ';
            }
            innerRowItem += (innerRowsArray[i]) ? innerRowsArray[i] : ' %0D%0A';
            if (innerRowsArray[i] != '') {
                innerRowItem += '%0D%0A';
            }
        }
        innerRowItem += '%0D%0A';

    }
    /*var body = innerRowItem.replace(/[\. ,:-]+/g, "%20");
    mail_to_url = 'mailto:?subject=' + subject + '&body=' + body;
    if(mail_to_url.length > 2048)
    {*/
    swal({
        title: "Information",
        text: "The email content length is bigger enough to pass in url or \nit contains characters which are not supported in system mail function, so the \"mailto\" system function may not work as expected. \nPlease export the list and then attach with your email.",
        showCancelButton: false,
        confirmButtonText: "OK",
        showLoaderOnConfirm: true,
        closeOnConfirm: false
    });

    /*}else{
    window.location = mail_to_url;
    }*/

}

/* To format a date */
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}


/* Function for date&time conversion - Start */
function datetime(created_at) {
    var datetime = new Date(created_at);
    var time = datetime.toLocaleString([], {
        hour: '2-digit',
        minute: '2-digit'
    });
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var date = datetime.getDate();
    var month = datetime.getMonth(); //January is 0 not 1
    var year = datetime.getFullYear();
    return monthNames[month] + " " + date + ", " + year + " at " + time;
}
/* Function for date&time conversion - End */

/* Function for init upppercase conversion - Start */
function uppercase(str) {
    var array1 = str.split(' ');
    var newarray1 = [];
    for (var x = 0; x < array1.length; x++) {
        newarray1.push(array1[x].charAt(0).toUpperCase() + array1[x].slice(1));
    }
    return newarray1.join(' ');
}

/* Function for init upppercase conversion - End */
function camelcase(str) {
    if (str != null) {
        convertedcase = str.toLowerCase().replace(/\b[a-z]/g, function (letter) {
            return letter.toUpperCase();
        });
        return convertedcase.trim();
    }
    return '';
}

/*Format to sentence (Replace '_' with ' ' and each word first letter caps) - Start*/
function sentenceCase(str) {
    formatted_text = str.replace(/_/g, ' ');
    var splitStr = formatted_text.toLowerCase().split(' ');
    for (var i = 0; i < splitStr.length; i++) {
        // You do not need to check if i is larger than splitStr length, as your for does that for you
        // Assign it back to the array
        splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
    }
    // Directly return the joined string
    return splitStr.join(' ');
}
/*Format to sentence (Replace '_' with ' ') - End*/

/* To convert a date */
function convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [pad(d.getFullYear()), pad(d.getMonth() + 1), d.getDate()].join('/');
}
