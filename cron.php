<?php
require_once 'MySqlDB.php';
$m= new \MySqlDB\MySqlDB();
$m->inserLogSAM(5,date("Y-m-d H:i:s").' Выполнена задача крон');
$m->close();

