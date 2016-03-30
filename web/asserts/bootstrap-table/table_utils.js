/**
 * Created by wuwentao on 2016/2/16.
 */

function booleanFormatter(value, row, index) {
    if (value) {
        return '<div class="text-center"><span class="glyphicon glyphicon-ok text-success"></span></div>';
    } else {
        return '<div class="text-center"><span class="glyphicon glyphicon-remove text-danger"></span></div>';
    }
}