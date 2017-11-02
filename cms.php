<html lang="en">
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Управление <? echo "CompanySam.by"; ?></h1>
            <p>Запус скрипта</p>
            <form id="status" action="all.php">
                <input type="submit" class="btn btn-success disabled" value="Запустить">
            </form>
        </div>
    </div>
</div>
<div id="result"></div>
<script>
    var id, date, date_end, cnt, act;
    /* прикрепить событие submit к форме */
    $("#status").submit(function (e) {

        var html = $.ajax({
            url: "all.php",
            async: false,
            dataType: "json",
            success: function (data) {
                alert(data);
                id = data[0].id;
                date = data[0].date;
                date_end = data[0].date_end;
                cnt = data[0].cnt;
                act = data[0].act;
            }
        });
        if (act == 0) {
            $("#result").append("<p>"+id+"</p>");
            $("#result").append("<p>"+date+"</p>");
            $("#result").append("<p>"+date_end+"</p>");
            $("#result").append("<p>"+cnt+"</p>");
            $("#result").append("<p>"+act+"</p>");
            alert("Прасинг не завершен!");
            e.preventDefault(); //Отменили нативное действие
            (e.cancelBubble) ? e.cancelBubble : e.stopPropagation;
        } else {
            
        }
    })
</script>


</body>
</html>