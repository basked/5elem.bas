<?php


$ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // возвращает результат в переменную а не в буфер
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //использовать редиректы
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // работа с https
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // работа с https
    curl_setopt($ch, CURLOPT_URL, "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=157");
    curl_setopt($ch, CURLOPT_POST, 1);
$a=curl_exec($ch);
$s=json_decode($a);
//var_dump($s);
curl_close($ch);

$ch = curl_init("https://5element.by/ajax/catalog_category_list.php?SECTION_ID=157");
$categoryId=$s->updateSection->section->UF_IB_RELATED_ID;
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // возвращает результат в переменную а не в буфер
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //использовать редиректы
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // работа с https
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // работа с https
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=157");
curl_setopt($ch, CURLOPT_POSTFIELDS,
        "categoryId=.'$categoryId'.&currentPage=1&itemsPerPage=150&viewType=1&sortName=popular&sortDest=desc&searchQuery=&fastFilterId=&filterInStock=1&filterInStore=0");
// выводим инфу из секции JSON


var_dump(curl_exec($ch));
/*
//var_dump($s->updateSection);
echo $s->updateSection->section->ID."\n\r";
echo $s->updateSection->section->UF_IB_RELATED_ID."\n\r";
echo $s->updateSection->section->NAME."\n\r";
echo $s->updateSection->section->SEO_NAME."\n\r";
echo $s->updateSection->section->DETAIL_URL."\n\r";
echo $s->updateSection->section->DETAIL_PICTURE."\n\r";
echo $s->updateSection->section->DATE_CREATE."\n\r";
*/
curl_close($ch);


