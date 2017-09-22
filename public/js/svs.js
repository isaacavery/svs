/**
 * SVS Global JavaScript
 */

/**
 * Submit AJAX update request
 * resource (String) type of resource to open
 * type (String) Name of the field to update
 * val (String) Value to write to the database
 */
function updateSheet(type,val){
    var data = {'_token': csrf_token};
    data[type] = val;
    var callbackData = {type: type, val: val};
    $.ajax('/sheets/' + resource_id, {
        'data': data,
        'context': callbackData,
        'dataType': 'json',
        'success': function(res, status, jqXHR,){
            // Deal with response
            if(res.success){
                console.log(callbackData);
                var msg = "Success: " + res.message;
                <!-- $('#messages').append('<div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + msg + '</div>'); -->
                if(callbackData.type == 'comments'){
                    // Add new comment to the display
                    $('ul#comments').append('<li class="text-success">' + callbackData.val + '</li>');
                    $('textarea#comment').val('');
                }
            } else {
                var err = "Error: " + res.error;
                $('#messages').append('<div class="alert alert-danger alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + err + '</div>');
            }
        },
        'error': function(xhr){
            console.log("ERROR");
            console.log(error);
        },
        'method': 'PUT'
    });
}

function getSheet() {
    var data = {'_token': csrf_token};
    data[type] = val;
    var callbackData = {type: type, val: val};
    $.ajax('/sheets/' + resource_id, {
        'data': data,
        'context': callbackData,
        'dataType': 'json',
        'success': function(res, status, jqXHR,){
            // Deal with response
            if(res.success){
                console.log(callbackData);
                var msg = "Success: " + res.message;
                $('#messages').append('<div class="alert alert-success alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + msg + '</div>');
                if(callbackData.type == 'comments'){
                    // Add new comment to the display
                    $('ul#comments').append('<li class="text-success">' + callbackData.val + '</li>');
                    $('textarea#comment').val('');
                }
            } else {
                var err = "Error: " + res.error;
                $('#messages').append('<div class="alert alert-danger alert-dismissable" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + err + '</div>');
            }
        },
        'error': function(xhr){
            console.log("ERROR");
            console.log(error);
        },
        'method': 'PUT'
    });
}

function isDate(txtDate) {
    var currVal = txtDate;
    if (currVal == '')
        return false;

    //Declare Regex 
    var rxDatePattern = /^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/;
    var dtArray = currVal.match(rxDatePattern); // is format OK?

    if (dtArray == null)
        return false;

    //Checks for mm/dd/yyyy format.
    dtYear = dtArray[1];
    dtMonth = dtArray[3];
    dtDay = dtArray[5];

    if (dtMonth < 1 || dtMonth > 12)
        return false;
    else if (dtDay < 1 || dtDay > 31)
        return false;
    else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31)
        return false;
    else if (dtMonth == 2) {
        var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
        if (dtDay > 29 || (dtDay == 29 && !isleap))
            return false;
    else if (dtYear < 2000 || dtYear > 2018)
        return false;
    }
    return true;
}
/**
 * Scripts to be executed after the DOM is loaded
 */
$('document').ready(function(){
    // Got any global scripts you need to execute? Here's the place!
});