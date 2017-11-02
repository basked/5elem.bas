<?
require_once 'MySqlDB.php';
$m = new MySqlDB\MySqlDB();
$currPars = $m->getCurrentParsingSAM();
echo json_encode($currPars);
$m->close();