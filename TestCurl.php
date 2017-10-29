<?php
error_reporting(E_ALL);
require_once 'MySqlDB.php';
require_once 'Parse5Elem.php';

$m = new \MySqlDB\MySqlDB();
$main_id = $m->insertMainSAM(date("Y-m-d H:i:s"), 1);
$m->close();
// build the individual requests, but do not execute them
$ch_1 = curl_init('http://5elem.bas/Test2.php');
/*$ch_2 = curl_init('http://5elem.bas/Test2.php');
$ch_3 = curl_init('http://5elem.bas/Test2.php');
$ch_4 = curl_init('http://5elem.bas/Test2.php');*/


curl_setopt($ch_1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_1, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch_1, CURLOPT_TIMEOUT_MS, 5000000);
/*curl_setopt($ch_2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_4, CURLOPT_RETURNTRANSFER, true);*/

curl_setopt($ch_1, CURLOPT_POSTFIELDS, array("offset" => 0, "out_main_id" => $main_id));
/*curl_setopt($ch_2, CURLOPT_POSTFIELDS, array("offset" => 100, "out_main_id" => $main_id));
curl_setopt($ch_3, CURLOPT_POSTFIELDS, array("offset" => 200, "out_main_id" => $main_id));
curl_setopt($ch_4, CURLOPT_POSTFIELDS, array("offset" => 300, "out_main_id" => $main_id));*/
// build the multi-curl handle, adding both $ch
$mh = curl_multi_init();
curl_multi_add_handle($mh, $ch_1);
/*curl_multi_add_handle($mh, $ch_2);
curl_multi_add_handle($mh, $ch_3);
curl_multi_add_handle($mh, $ch_4);*/


// execute all queries simultaneously, and continue when all are complete
$running = null;
do {
    curl_multi_exec($mh, $running);

} while ($running);

//close the handles
curl_multi_remove_handle($mh, $ch_1);
/*curl_multi_remove_handle($mh, $ch_2);
curl_multi_remove_handle($mh, $ch_3);
curl_multi_remove_handle($mh, $ch_4);*/
curl_multi_close($mh);

// all of our requests are done, we can now access the results
$response_1 = curl_multi_getcontent($ch_1);
/*$response_2 = curl_multi_getcontent($ch_2);
$response_3 = curl_multi_getcontent($ch_3);
$response_4 = curl_multi_getcontent($ch_4);*/

Parse5Elem::logToFile('export.html', $response_1);
/*var_dump($response_2);
var_dump($response_3);
var_dump($response_4);*/
//
