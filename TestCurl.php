<?php

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // возвращает результат в переменную а не в буфер
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //использовать редиректы
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // работа с https
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // работа с https
    curl_setopt($ch, CURLOPT_URL, "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=143");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        "categoryId=24155&currentPage=1&itemsPerPage=150&viewType=1&sortName=popular&sortDest=desc&searchQuery=&fastFilterId=&filterInStock=1&filterInStore=0");
    $a=curl_exec($ch);
    $s=json_decode($a);
var_dump($s->updateSection->section->ID);
    curl_close($ch);


