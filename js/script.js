function test_p() {
    if ($("#run").hasClass('disabled')) {
        enable_run_p();
    } else {
        disable_run_p();

    }
}

function enable_run_p() {
    $("#run").removeClass("disabled");
    return 1;
}

function disable_run_p() {
    $("#run").addClass("disabled");
    return 0;
}

function info_p(e) {
    var html = $.ajax({
        url: "all.php",
        async: false,
        dataType: "json",
        success: function (data) {
            id = data[0].id;
            date = data[0].date;
            date_end = data[0].date_end;
            cnt = data[0].cnt;
            act = data[0].act;
            thread = data[0].thread;
        }
    });
    var id, date, date_end, cnt, act;
    $("#stat_body > tbody:last").append("<tr><td>" + id + "</td><td>" + date + "</td><td>" + date_end + "</td><td>" + cnt + "</td><td>" + act + "</td><td>" + thread + "</td></tr>");

    if (act == 1) {
        enable_run_p();
    }
    return act;
};

function load_p() {
    if (info_p() == 1) {
        enable_run_p();
    }
}
function run_p(e) {
    // $("#status").submit(function (e) {
    var html = $.ajax({
        url: "all.php",
        async: false,
        dataType: "json",
        success: function (data) {
            id = data[0].id;
            date = data[0].date;
            date_end = data[0].date_end;
            cnt = data[0].cnt;
            act = data[0].act;
        }
    });
    var id, date, date_end, cnt, act;

    if (act == 2) {
        alert("Прасинг не завершен!");
        e.preventDefault(); //Отменили нативное действие
        (e.cancelBubble) ? e.cancelBubble : e.stopPropagation;

    } else {
        if ($("#run").hasClass('disabled')) {
            alert("Управление запуском заблокировано на странице");
        } else {
        }
        $.post("/../TestCurl.php");
    }

}