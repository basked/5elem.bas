<html>
<head>
    <title>Bootstrap Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="/js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <form id="status">
                <input id="run" type="button" class="btn btn-success disabled" onclick="run_p()" value="Запустить">
                <input id="info" type="button" class="btn btn-info" onclick="info_p()" value="Обновить">
                <input id="test" type="button" class="btn btn-warring" onclick="test_p()" value="Тест">
            </form>
        </div>
    </div>
</div>


<div class="container">
    <table  id="stat_body" class="table-condensed table-striped table-bordered ">
        <thead>
        <tr>
            <th>№ запуска</th>
            <th>Время запуска</th>
            <th>Время окончания</th>
            <th>Обработанно товаров</th>
            <th>Активность</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td id="id"></td>
            <td id="date"></td>
            <td id="date_end"></td>
            <td id="cnt"></td>
            <td id="act"></td>
        </tr>
        </tbody>
    </table>
</div>



<div id="result"></div>


</body>
</html>