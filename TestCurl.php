<?php
include 'phpQuery/phpQuery.php'; // подключаем phpQuery
include  'libs/helpers.php'; // подключаем файл для вывода отладочной инфы
function jdecoder ($json_str)
{
    $cyr_chars = array(
        '\u0430' => 'а', '\u0410' => 'А',
        '\u0431' => 'б', '\u0411' => 'Б',
        '\u0432' => 'в', '\u0412' => 'В',
        '\u0433' => 'г', '\u0413' => 'Г',
        '\u0434' => 'д', '\u0414' => 'Д',
        '\u0435' => 'е', '\u0415' => 'Е',
        '\u0451' => 'ё', '\u0401' => 'Ё',
        '\u0436' => 'ж', '\u0416' => 'Ж',
        '\u0437' => 'з', '\u0417' => 'З',
        '\u0438' => 'и', '\u0418' => 'И',
        '\u0439' => 'й', '\u0419' => 'Й',
        '\u043a' => 'к', '\u041a' => 'К',
        '\u043b' => 'л', '\u041b' => 'Л',
        '\u043c' => 'м', '\u041c' => 'М',
        '\u043d' => 'н', '\u041d' => 'Н',
        '\u043e' => 'о', '\u041e' => 'О',
        '\u043f' => 'п', '\u041f' => 'П',
        '\u0440' => 'р', '\u0420' => 'Р',
        '\u0441' => 'с', '\u0421' => 'С',
        '\u0442' => 'т', '\u0422' => 'Т',
        '\u0443' => 'у', '\u0423' => 'У',
        '\u0444' => 'ф', '\u0424' => 'Ф',
        '\u0445' => 'х', '\u0425' => 'Х',
        '\u0446' => 'ц', '\u0426' => 'Ц',
        '\u0447' => 'ч', '\u0427' => 'Ч',
        '\u0448' => 'ш', '\u0428' => 'Ш',
        '\u0449' => 'щ', '\u0429' => 'Щ',
        '\u044a' => 'ъ', '\u042a' => 'Ъ',
        '\u044b' => 'ы', '\u042b' => 'Ы',
        '\u044c' => 'ь', '\u042c' => 'Ь',
        '\u044d' => 'э', '\u042d' => 'Э',
        '\u044e' => 'ю', '\u042e' => 'Ю',
        '\u044f' => 'я', '\u042f' => 'Я',
        '\u2116' => '№',
        '&quot;' => '"',
        '\r' => '',
        '\n' => '<br />',
        '\t' => ''
    );
    foreach ($cyr_chars as $key => $value) {
        $json_str = str_replace($key, $value, $json_str);
    }
    return $json_str;
}
function logToFile ($fileName, $context)
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
curl_setopt($ch, CURLOPT_URL, "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=1403");
curl_setopt($ch, CURLOPT_POST, 1);
$a = curl_exec($ch);
$s = json_decode($a);

$categoryId = $s->updateSection->section->UF_IB_RELATED_ID;


/* $data = array('name' => 'Foo', 'file' => '@/home/user/test.png');

curl_setopt($ch, CURLOPT_URL, 'http://localhost/upload.php');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);*/


//$postField = "categoryId=$categoryId&currentPage=0&itemsPerPage=150&viewType=1&sortName=popular&sortDest=desc&searchQuery=&fastFilterId=&filterInStock=1&filterInStore=0";
$postField = array('categoryId'=>$categoryId,'currentPage'=>1,'itemsPerPage'=>150,'viewType'=>1,'sortName'=>'popular','sortDest'=>'desc','filterInStock'=>1,'filterInStore'=>0);



curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);

$html = curl_exec($ch);
$html=jdecoder($html);
$html = str_replace('\"', '"', $html);
$html = str_replace('\/', '/', $html);


//$s = json_decode($html);
//var_dump($s);
//$html = stripcslashes($jsr);
//exportToFile('export.html',$stripa);
//
//var_dump($res);
//exportToFile('export1.html',$html);


$pq = phpQuery::newDocument($html);
//$titles = $pq->find('.spec-product.js-product-item');
$titles = $pq->find('.spec-product.js-product-item');
$i = 0;
foreach ($titles as $title) {
    $productDesc[$i]['name'] = trim(pq($title)->find('.spec-product-middle-title>a')->text());
    $productDesc[$i]['prodId'] = pq($title)->attr('data-id');
    $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($title)->find('span._price')->text()));
    $productDesc[$i]['code'] = trim(str_replace('Код товара:', '', pq($title)->find('.product-middle-patio-code')->text()));
    $i++;

}
var_dump($productDesc);


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


