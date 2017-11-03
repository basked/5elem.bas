<?php
require_once 'MySqlDB.php';
require_once 'Parse5Elem.php';
define("HOST","http://5elem.bas"); // при переносе на сервер заменить на http://companysam.by/parsing , потому что Cron не распознаёт $_SERVER['HTTP_HOST']

$m = new \MySqlDB\MySqlDB();

$main_id = $m->insertMainSAM(date("Y-m-d H:i:s"), 2);

$m->inserLogSAM(7,date("Y-m-d H:i:s")." Начало мультипарсинга для Main_id=$main_id ");

$ch = array();
//Parse5Elem\Parse5Elem::logToFile('log.txt', "Начало мультипарсинга для Main_id=$main_id ");
$mh = curl_multi_init();
for ($n = 0; $n <= 3; $n++) {
    $m->inserLogSAM(7,date("Y-m-d H:i:s")." ".HOST."/Test2.php");
    $ch[$n] = curl_init(HOST.'/Test2.php');
    curl_setopt($ch[$n], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch[$n], CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch[$n], CURLOPT_TIMEOUT_MS, 120000);
    curl_setopt($ch[$n], CURLOPT_POSTFIELDS, array("offset" => ($n * 100), "out_main_id" => $main_id));
    curl_multi_add_handle($mh, $ch[$n]);
    sleep(1);
}


$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

for ($n = 0; $n <= count($ch);$n++) {
    curl_multi_remove_handle($mh, $ch[$n]);
}

$m->inserLogSAM(7,date("Y-m-d H:i:s")." Окончание мультипарсинга для Main_id=$main_id ");
$m->close();
//Parse5Elem\Parse5Elem::logToFile('log.txt', "Окончание мультипарсинга для Main_id=$main_id ");
curl_multi_close($mh);