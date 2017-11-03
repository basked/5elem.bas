<html>
<head>
    <title>Управление загрузкой</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <script src="/js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body onload="load_p()">
<div class="container">
    <div class="row">
        <div class="col-sm-9">
            <form id="status">
                <input id="run" type="button" class="btn btn-success disabled" onclick="run_p(event)"
                       value="Запустить">
                <!--    <input id="info" type="button" class="btn btn-info" onclick="info_p()" value="Обновить">-->
                <input id="info_one_rec" type="button" class="btn btn-info" onclick="info_one_rec_p()"
                       value="Обновить статистику">
                <!--   <input id="test" type="button" class="btn btn-warring" onclick="test_p()" value="Тест">-->
            </form>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="row">

            <div class="panel panel-default">
                <div class="panel-heading text-center">Статистика загрузки</div>
                <div class="panel-body">
                    <table id="stat_body" class="table-condensed table-striped table-bordered center-block">
                        <!-- <table id="stat_body" class="table-responsive">-->

                    </table>
                    <table id="stat_body" class="table-condensed table-striped table-bordered center-block">
                        <!-- <table id="stat_body" class="table-responsive">-->
                        <thead>
                        <tr>
                            <th rowspan="2" class="text-center">№ запуска</th>
                            <th colspan="3" class="text-center">Время</th>
                            <th rowspan="2" class="text-right">Товаров</th>
                            <th rowspan="2" class="text-right">Активность</th>
                            <th rowspan="2" class="text-right">Потоки</th>
                        </tr>
                        <tr>
                            <th>Сервера</th>
                            <th>Запуска</th>
                            <th>Окончания</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td id="id"></td>
                            <td id="time_serv" class="text-center"></td>
                            <td id="date" class="text-center"></td>
                            <td id="date_end" class="text-center"></td>
                            <td id="cnt" class="text-right"></td>
                            <td id="act" class="text-right"></td>
                            <td id="thread" class="text-right"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


</div>


<div id="result"></div>


</body>
</html>