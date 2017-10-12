<?php
require('/phpQuery/phpQuery.php'); // подключаем phpQuery
require('/libs/helpers.php'); // подключаем файл для вывода отладочной инфы
require_once('MySqlDB.php');
header("Content-type: text/html; charset = utf-8"); // кодировка utf-8
class Parse5Element
{
    const PROXY_SERVER = "172.16.15.33";
    const PROXY_PORT = 3128;
    const PROXY_NAME = "gt-asup6";
    const PROXY_PASS = "teksab";
    const SITE = "https://5element.by";
    const CURR_PAGE = "/catalog.php"; // страница курса
    /**
     * Parse5Element constructor.
     */
    private $ch;
    public $html;
    // декодируем данные ответа сервера
    public function jdecoder ($json_str)
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

    public function __construct ()
    {
        $this->ch = nil;
    }
    // установка прокси для интернет соединения
    public function setProxy ()
    {
        if ($_SERVER['COMPUTERNAME'] == 'GT-ASUP6VM') {
            curl_setopt($this->ch, CURLOPT_PROXY, self::PROXY_SERVER);
            curl_setopt($this->ch, CURLOPT_PROXYPORT, self::PROXY_PORT);
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, self::PROXY_NAME . ":" . self::PROXY_PASS);
        }
    }
    // установка опций для запроса
    public function setOpt ()
    {
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); // возвращает результат в переменную а не в буфер
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true); //использовать редиректы
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // работа с https
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // работа с https
    }
    // установка значений для POST полей -
    public function setPostField ($sectionId, $categoryId, $currPage)
    {
        curl_setopt($this->ch, CURLOPT_URL, "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=" . $sectionId);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,
            "categoryId=" . $categoryId . "&currentPage=" . $currPage . "&itemsPerPage=150&viewType=1&sortName=popular&sortDest=desc&searchQuery=&fastFilterId=&filterInStock=1&filterInStore=0");
    }
    // возвращает данные контента
    public function getContent ($url)
    {
        $this->ch = curl_init($url);
        $this->setProxy();
        $this->setOpt();
        return curl_exec($this->ch);
        curl_close($this->ch);
    }

    // возвращает данные контента с учетом POST полей
    public function getContentPostFields ($sectionId, $categoryId, $currPage)
    {
        $this->ch = curl_init("https://5element.by/ajax/catalog_category_filter.php?SECTION_ID=" . $sectionId);
        $this->setProxy();
        $this->setOpt();
        $this->setPostField($sectionId, $categoryId, $currPage);
        return curl_exec($this->ch);
        curl_close($this->ch);
    }
}

// позвращает параметр sectId - нужен для запроса в getProductDesc()
function getSectionId ($url)
{
    $p = strpos($url, '-');
    $sectionId = substr($url, 9, $p - 9);
    return $sectionId;
}

/**
 * @param $sectionId
 * @param $categoryId
 * @return mixed
 */
// возвращает данные по продуктам первой страницы
function getProductDesc ($sectionId, $categoryId, $currPage)
{
    $f = new Parse5Element();
    $html = $f->jdecoder($f->getContentPostFields($sectionId, $categoryId, $currPage));
    $html = str_replace('\"', '"', $html);
    $html = str_replace('\/', '/', $html);
//echo $html;
    $pq = phpQuery::newDocument($html);
    $titles = $pq->find('.spec-product.js-product-item');
    $i = 0;
    foreach ($titles as $title) {
        $productDesc[$i]['name'] = trim(pq($title)->find('.spec-product-middle-title>a')->text());
        $productDesc[$i]['prodId'] = pq($title)->attr('data-id');
        $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($title)->find('span._price')->text()));
        $productDesc[$i]['code'] = trim(str_replace('Код товара:', '', pq($title)->find('.product-middle-patio-code')->text()));
        $i++;
    }
    return $productDesc;
    unset($f);
}

// возвращает данные по продуктам первой страницы
function getProductAllDesc ()
{
    $db = new \MySqlDB\MySqlDB();
    // данные из БД для вывода всех категорий и подкатегорий
    $catQCatDescs = $db->getCategories();
    $i = 1;
    $curPage = 1;
    $maxPage = $catQCatDescs[0]['maxPage'] + 1;
    foreach ($catQCatDescs as $catQCatDesc) {
        do {
            $prodDescs = getProductDesc($catQCatDesc['sectId'], $catQCatDesc['catId'], $curPage);

            foreach ($prodDescs as $prodDesc) {
                //   для вывода результата
                echo '-=' . $i . '=-' . '[' . $catQCatDesc['sectId'] . ','. $catQCatDesc['catName'] . ',' . $catQCatDesc['catId'] . ']=>' . '[' . $prodDesc['prodId'] . ',' .$prodDesc['code'] . ',' . $prodDesc['name'] . ',' . $prodDesc['price'] . ']' . "\n\r";
                $i++;
            }
            $curPage++;
        } while ($curPage <= $maxPage);
    }
}


// возвращает данные по категориям
function getCategoryDesc ($url)
{
    $f = new Parse5Element();
    $html = $f->jdecoder($f->getContent($url));
    $html = str_replace('\"', '"', $html);
    $html = str_replace('\/', '/', $html);
    $pq = phpQuery::newDocument($html);
    // $categoryId = $pq->find('div.spec-product.js-product-item')->attr('data-category-id');//->text();
    $categoryId = $pq->find('div.js-product-item')->attr('data-category-id');//->text();
    $cntProd = $pq->find('.pages-quant-amount')->text();
    $countProduct = trim(str_replace('из', '', $cntProd));
    $categoryDesc['catId'] = $categoryId;
    $categoryDesc['countProd'] = $countProduct;
    return $categoryDesc;
    unset($f);
}
// количество продуктов в категории
function getCountProduct ($url)
{
    $f = new Parse5Element();
    $html = $f->jdecoder($f->getContent($url));
    $pq = phpQuery::newDocument($html);
    $CountProduct = trim(str_replace('из', '', $pq->find('.pages-quant-amount')->text()));//->text();
    return $CountProduct;
    unset($f);
}

// функция возвращает набор символов
function getAllLink ()
{
    $f = new Parse5Element();
    /*начало декодирования символов в HTML*/
    $html = $f->jdecoder($f->getContent('https://5element.by/catalog'));
    $html = str_replace('\"', '"', $html);
    /*конец декодирования символов в HTML*/
    $pq = phpQuery::newDocument($html);
    $hrefs = $pq->find('.catalog-prod-col-item');
    $i = 0;
    foreach ($hrefs as $href) {
        // тут наменование категории
        $href_info[$i]['name'] = trim(pq($href)->find('.catalog-prod-col-item-text')->text());
        // тут линки категории
        $href_info[$i]['href'] = pq($href)->find('a')->attr('href');
        $i++;
    }
    return $href_info;
}


function InsertProductDB ()
{
    $db = new \MySqlDB\MySqlDB();
    $catQCatDescs = $db->getCategories();
    $i = 1;
    $curPage = 1;
    $maxPage = $catQCatDescs[0]['maxPage'] + 1;
    foreach ($catQCatDescs as $catQCatDesc) {
        do {
            $prodDescs = getProductDesc($catQCatDesc['sectId'], $catQCatDesc['catId'], $curPage);

            foreach ($prodDescs as $prodDesc) {
            // тут пишем в бд
                $db->InsertProduct(

                    (int)$catQCatDesc['id'],
                    (int) $prodDesc['prodId'],
                    $prodDesc['name'],
                    (int)$prodDesc['code'],
                    '',
                    (float) $prodDesc['price']);
              //   для вывода результата
                 echo '-=' . $i . '=-' . '[' . $catQCatDesc['sectId'] . ','. $catQCatDesc['catName'] . ',' . $catQCatDesc['catId'] . ']=>' . '[' . $prodDesc['prodId'] . ',' .$prodDesc['code'] . ',' . $prodDesc['name'] . ',' . $prodDesc['price'] . ']' . "\n\r";
                  $i++;
            }
            $curPage++;
        } while ($curPage <= $maxPage);
    }
  //  $db->close();
}


function InsertCategory ()
{
    $db = new \MySqlDB\MySqlDB();

    $dateIns = date('d.m.Y');
    $allCats = getAllLink();
    foreach ($allCats as $cat) {
        $catDesc = getCategoryDesc('https://5element.by' . $cat['href']);
        $catId = $catDesc['catId'];
        $catName = $cat['name'];
        $sectId = getSectionId($cat['href']);
        $cntPage = $catDesc['countProd'];
        $catURL = $cat['href'];
        $act = 1;
        $db->InsertCatgory(
            $catId,
            $catName,
            $sectId,
            $cntPage,
            $dateIns,
            $act,
            $catURL);
        echo 'catId=' . $catId . ';' .
            'catURL=' . $catURL . ';' .
            'catName=' . $catName . ';' .
            'sectId=' . $sectId . ';' .
            'cntPage=' . $cntPage . ';' . "\n\r";
    }
    //$db->close();
}
echo date("H:i:s");
echo "\n\r";
//$db= new \MySqlDB\MySqlDB();
//InsertProduct();
echo date("H:i:s");




