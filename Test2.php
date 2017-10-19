<?php
require_once 'Parse5Elem.php';
require_once 'MySqlDB.php';

echo date("H:i:s") . "\n\r";

$p = new \Parse5Elem\Parse5Elem();
$m = new  \MySqlDB\MySqlDB();
$catLinks = array();
$catLinks = $p->getCategoriesLinks();

for ($i = 0; $i < count($catLinks); $i++) {
    if ($m->existCategorySAM($catLinks[$i]['id']) == 0) {
        if ($catLinks[$i]['name'] == '') {
            $p::logToFile('export.html', $catLinks[$i]['id'] . $catLinks[$i]['name']);
        } else {
            $m->insertCategorySAM($catLinks[$i]['name'], $catLinks[$i]['id'], 0, 1);
        }
    } else{ $p::logToFile('export.html',  $catLinks[$i]['id']."->".$catLinks[$i]['href']."\r\n");};
}

echo date("H:i:s") . "\n\r";

