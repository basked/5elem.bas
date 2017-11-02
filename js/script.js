function test_p() {
    if ($("#run").hasClass('disabled'))  {

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
        }
    });
    var id, date, date_end, cnt, act;
    if (act == 2) {

        $("#stat_body > tbody:last").append("<tr><td>" + id + "</td><td>" + date + "</td><td>" + date_end + "</td><td>" + cnt + "</td><td>" + act + "</td></tr>");
        /* $("#id").append("<p>" + id + "</p>");
         $("#date").append("<p>" + date + "</p>");
         $("#date_end").append("<p>" + date_end + "</p>");
         $("#cnt").append("<p>" + cnt + "</p>");
         $("#act").append("<p>" + act + "</p>");*/
    }
};

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
        /*    $("#id").append("<p>" + id + "</p>");
         $("#date").append("<p>" + date + "</p>");
         $("#date_end").append("<p>" + date_end + "</p>");
         $("#cnt").append("<p>" + cnt + "</p>");
         $("#act").append("<p>" + act + "</p>");
         */
        alert("Прасинг не завершен!");
        e.preventDefault(); //Отменили нативное действие
        (e.cancelBubble) ? e.cancelBubble : e.stopPropagation;

    } else {
        $.post("/../TestCurl.php");
    }

}