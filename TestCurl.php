<?php

function exportToFile ($fileName, $context)
{
    if (is_writable($fileName)) {
        if (!$handle = fopen($fileName, 'a')) {
            echo "Ошибка открытия файла ($fileName)!";
            exit();
        }
        if (fwrite($handle, $context) === FALSE) {
            echo "Ошибка записи содержимого в файл ($fileName)";
            exit();
        }
        echo "Содержимое записано в файл ($fileName)";
        fclose($handle);
    } else {
        echo "Файл $fileName не доступен для записи";
    }
}


$ch = curl_init();

if ($_SERVER['COMPUTERNAME'] == 'GT-ASUP6VM') {
    curl_setopt($ch, CURLOPT_PROXY, "172.16.15.33");
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, "gt-asup6:teksab");
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // возвращает результат в переменную а не в буфер
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //использовать редиректы
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // работа с https
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // работа с https
curl_setopt($ch, CURLOPT_URL, "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=157");
curl_setopt($ch, CURLOPT_POST, 1);
$a = curl_exec($ch);
$s = json_decode($a);

$categoryId = $s->updateSection->section->UF_IB_RELATED_ID;

$postField = "categoryId=" . $categoryId . "&currentPage=1&itemsPerPage=150&viewType=1&sortName=popular&sortDest=desc&searchQuery=&fastFilterId=&filterInStock=1&filterInStore=0";
curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);

$a = curl_exec($ch);
$stripa=stripslashes($a);
//exportToFile('export.html',$stripa);

$s = json_decode($a);

//exportToFile('export1.html',$s);
echo $a."\r\n";

echo $stripa;
/*
/
echo $s->updateSection->section->ID."\n\r";
echo $s->updateSection->section->UF_IB_RELATED_ID."\n\r";
echo $s->updateSection->section->NAME."\n\r";
echo $s->updateSection->section->SEO_NAME."\n\r";
echo $s->updateSection->section->DETAIL_URL."\n\r";
echo $s->updateSection->section->DETAIL_PICTURE."\n\r";
echo $s->updateSection->section->DATE_CREATE."\n\r";
*/
curl_close($ch);


