function test_p() {
    if ($("#run").hasClass('disabled')) {
        enable_run_p();
    } else {
        disable_run_p();

    }
}

function test() {
    alert('basket');
}
function clear_table_body() {
    $("#stat_body>tbody>tr>td").remove();
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
    var id, date, date_end, cnt, act, thread, time_serv;
    var html = $.ajax({
        url: "all.php",
        async: false,
        dataType: "json",
        success: function (data) {
            id = data[0].id;
            time_serv = data[0].time_serv;
            date = data[0].date;
            date_end = data[0].date_end;
            cnt = data[0].cnt;
            act = data[0].act;
            thread = data[0].thread;
        }
    });

    $("#stat_body > tbody:last").append("<tr><td>" + id + "</td><td>" + time_serv + "</td><td>" + date + "</td><td>" + date_end + "</td><td>" + cnt + "</td><td>" + act + "</td><td>" + thread + "</td></tr>");

    if ((act == 1) || (act === null)) {
        enable_run_p();
    }
    act==2?disable_run_p():enable_run_p();
    return act;
};

function info_all_rec_p(e) {
    var id, date, date_end, cnt, act, thread, time_serv;
    var html = $.ajax({
        url: "all.php",
        async: false,
        dataType: "json",
        success: function (data) {
            clear_table_body();
            i=data.length-1;
            do{ // for (i=0;i<data.length;i++) {
                id = data[i].id;
                time_serv = data[i].time_serv;
                date = data[i].date;
                date_end = data[i].date_end;
                cnt = data[i].cnt;
                act = data[i].act;
                thread = data[i].thread;
                $("#stat_body > tbody:first").append("<tr><td>" + id + "</td><td>" + time_serv + "</td><td>" + date + "</td><td>" + date_end + "</td><td>" + cnt + "</td><td>" + act + "</td><td>" + thread + "</td></tr>");
                i=i-1;
            } while (i>=0);
        }
    });
    $("#id").text(id);
    $("#time_serv").text(time_serv);
    $("#date").text(date);
    $("#date_end").text(date_end);
    $("#cnt").text(cnt);
    $("#act").text(act);
    $("#thread").text(thread);
    act==2?disable_run_p():enable_run_p();
    return act;
};




function info_one_rec_p(e) {
    var id, date, date_end, cnt, act, thread, time_serv;
    var html = $.ajax({
        url: "all.php",
        async: false,
        dataType: "json",
        success: function (data) {
            clear_table_body();
            id = data[0].id;
            time_serv = data[0].time_serv;
            date = data[0].date;
            date_end = data[0].date_end;
            cnt = data[0].cnt;
            act = data[0].act;
            thread = data[0].thread;
            $("#stat_body > tbody:last").append("<tr><td>" + id + "</td><td>" + time_serv + "</td><td>" + date + "</td><td>" + date_end + "</td><td>" + cnt + "</td><td>" + act + "</td><td>" + thread + "</td></tr>");
        }
    });

    $("#id").text(id);
    $("#time_serv").text(time_serv);
    $("#date").text(date);
    $("#date_end").text(date_end);
    $("#cnt").text(cnt);
    $("#act").text(act);
    $("#thread").text(thread);
    act==2?disable_run_p():enable_run_p();
    return act;
};

function load_p() {
    if (info_one_rec_p() == 1) {
        enable_run_p();
    }
}

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}


function run_p(e) {
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
    var id, date, date_end, cnt, act, thread, time_serv;

    if (act == 2) {
        alert("Прасинг не завершен!");
        e.preventDefault(); //Отменили нативное действие
        (e.cancelBubble) ? e.cancelBubble : e.stopPropagation;

    } else {
        if ($("#run").hasClass('disabled')) {
            alert("Управление запуском заблокировано на странице");
        } else {
        }
        $.post("TestCurl.php");
        sleep(5000);
        info_one_rec_p();
    }

}