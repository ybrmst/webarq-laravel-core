/**
 * Created by DanielSimangunsong on 1/19/2017.
 */

function prettyURL(str, separator, regex) {
    // Check for protocols
    if (isValidURL(str)) {
        return str;
    }
    // Default separator
    if (typeof separator === 'undefined') {
        separator = '-';
    }

    if (typeof regex === 'undefined') {
        str = str.replace(/[^a-z0-9?\/#]+/g, separator);
    } else {
        str = str.replace(regex, separator);
    }

    if (str.charAt(0) === separator) str = str.substr(1);
    if (str.charAt(str.length - 1) === separator) str = str.substr(0, str.length - 1);

    return str.toLowerCase();
}


/**
 * @param str string
 * @param protocols array
 * @todo Dynamic protocols
 */
function isValidURL(str, protocol) {
    var pattern = new RegExp("^(" + ['http', 'https', 'ftp', 'ftps', 'sftp'].join('?|') + "):\\/\\/" +
        "(((([a-z]|\\d|-|\\.|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|" +
        "(%[\\da-f]{2})|[!\\$&'\\(\\)\\*\\+,;=]|:)*@)?(((\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])" +
        "\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])" +
        "\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5]))|((([a-z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|" +
        "(([a-z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])([a-z]|\\d|-|\\.|_|~|" +
        "[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])*" +
        "([a-z]|\\d|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])))\\.)+" +
        "(([a-z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|" +
        "(([a-z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])([a-z]|\\d|-|\\.|_|~" +
        "|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])*([a-z]|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-" +
        "\\uFFEF])))\\.?)(:\\d*)?)(\\/((([a-z]|\\d|-|\\.|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|" +
        "(%[\\da-f]{2})|[!\\$&'\\(\\)\\*\\+,;=]|:|@)+(\\/(([a-z]|\\d|-|\\.|_" +
        "|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(%[\\da-f]{2})|[!\\$&'\\(\\)\\*\\+,;=]|:|@)*)*)?)?" +
        "(\\?((([a-z]|\\d|-|\\.|_|~|[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(%[\\da-f]{2})|" +
        "[!\\$&'\\(\\)\\*\\+,;=]|:|@)|[\\uE000-\\uF8FF]|\\/|\\?)*)?(\\#((([a-z]|\\d|-|\\.|_|~|" +
        "[\\u00A0-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFEF])|(%[\\da-f]{2})|[!\\$&'\\(\\)\\*\\+,;=]|:|@)|\\/|\\?)*)?$"
        , "i");

    return pattern.test(str);
}

function previewLoader(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(input).siblings('img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$(function () {

    $('input.previewLoader').change(function () {
        previewLoader(this);
    });

    $('.referrer').first().keyup(function () {
        var target = $(this).attr('data-referrer-target');
        var regex = new RegExp('\\W+', 'g');

        $(target).val(prettyURL($(this).val(), '-', regex));
    });

    $('.datepicker').datepicker({
        autoclose: true
    });

    $('.show-hide-element').change(function () {
        var myIpt = $(this).val();
        $.each($(this).data('hide'), function (i, v) {
            if (i == myIpt || i === 'x-000' || (i.startsWith('!') && myIpt != i.substring(1))) {
                $.each(v, function (j, w) {
                    $(w).parent('div.form-group').hide();
                });
            }
        });
        $.each($(this).data('show'), function (i, v) {
            if (i == myIpt || i === 'x-000' || (i.startsWith('!') && myIpt != i.substring(1))) {
                $.each(v, function (j, w) {
                    $(w).parent('div.form-group').show();
                });
            }
        });
    }).trigger('change');
});