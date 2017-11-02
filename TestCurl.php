<?php
require_once 'MySqlDB.php';
require_once 'Parse5Elem.php';

$m = new \MySqlDB\MySqlDB();
$m->updateActMainSAM(0);
$main_id = $m->insertMainSAM(date("Y-m-d H:i:s"), 2);
$m->close();
$ch = array();
Parse5Elem\Parse5Elem::logToFile('log.txt', "Начало мультипарсинга для Main_id=$main_id ");
$mh = curl_multi_init();
for ($n = 0; $n <= 3; $n++) {
    $ch[$n] = curl_init($_SERVER['HTTP_HOST'].'/Test2.php');
    curl_setopt($ch[$n], CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch[$n], CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch[$n], CURLOPT_TIMEOUT_MS, 50000);
    curl_setopt($ch[$n], CURLOPT_POSTFIELDS, array("offset" => ($n * 100), "out_main_id" => $main_id));
    curl_multi_add_handle($mh, $ch[$n]);
}

// execute all queries simultaneously, and continue when all are complete
$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running);

for ($n = 0; $n <= count($ch);$n++) {
    curl_multi_remove_handle($mh, $ch[$n]);
}
Parse5Elem\Parse5Elem::logToFile('log.txt', "Окончание мультипарсинга для Main_id=$main_id ");
curl_multi_close($mh);