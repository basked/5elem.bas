<html>
<head>
    <title>Управление загрузкой</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="js/script.js"></script>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/latest/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>


</head>
<body onload="load_p()">
<div class="container">
    <div class="row">
        <div class="col">
            <table id="stat_body" class="table-condensed table-striped table-bordered">
                <!-- <table id="stat_body" class="table-responsive">-->
                <thead>
                <tr class="success">
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
    <p></p>
    <div class="row">
        <div class="col">
            <form id="status">
                <input id="run" type="button" class="btn btn-success btn-md disabled" onclick="run_p(event)"
                       value="Запустить обновление">
                <input id="info_one_rec" type="button" class="btn btn-info btn-md" onclick="info_one_rec_p()"
                       value="Последнее обновлние">
                <!--   <input id="info_all_rec" type="button" class="btn btn-info btn-md" onclick="info_all_rec_p()"
                       value="Показать все">-->
                <!--   <input id="test" type="button" class="btn btn-warring" onclick="test_p()" value="Тест">-->
            </form>
        </div>
    </div>
</div>


<div id="result"></div>


</body>
</html>