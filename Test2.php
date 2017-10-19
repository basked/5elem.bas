<?php
require_once 'Parse5Elem.php';
$p= new \Parse5Elem\Parse5Elem();
echo date("H:i:s") . "\n\r";
$p->getCategoriesLinks();
echo date("H:i:s") . "\n\r";