<?php
/**
 * Created by PhpStorm.
 * User: basked
 * Date: 10.10.2017 
 * Time: 11:48
 */

namespace Parse5Elem;
//ini_set('max_execution_time', 1800);
require_once('phpQuery/phpQuery.php'); // подключаем phpQuery
require_once('libs/helpers.php'); // подключаем файл для вывода отладочной инфы

class Parse5Elem
{
    const PROXY_SERVER = "172.16.15.33";
    const PROXY_PORT = 3128;
    const PROXY_NAME = "gt-asup6";
    const PROXY_PASS = "teksab";
    const SITE = "https://5element.by";
    const CURR_PAGE = "/catalog"; // страница курса

    /**
     * @var хранит данные html из PHPQuery
     */

    public $html;
    /**
     * @var переменная для работы с  CURL(дескриптор сеанса)
     */
    private $ch;

    /**
     * Parse5Elem constructor.
     */
    public function __construct ($url = '')
    {
        $this->html = '';
        $this->ch = curl_init($url);
    }

    public static function getDecodeHTML ($response)
    {
        $res = self::jdecoder($response);
        $res = str_replace('\"', '"', $res);
        $res = str_replace('\/', '/', $res);
        return $res;
    }

    /**
     * Декодирует в кирилицу
     * @param $json_str
     * @return mixed
     */
    private
    static function jdecoder ($json_str)
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
			'\u00a0' => ' ',
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

    /**
     * Запись информации в Log файл
     * @param $fileName файл_лога
     * @param $context  содержимое_для_записи
     */
    public static function logToFile ($fileName, $context)
    {
        if (is_writable($fileName)) {
            if (!$handle = fopen($fileName, 'a')) {
               // echo "Ошибка открытия файла ($fileName)!";
                exit();
            }
            if (fwrite($handle, date("Y-m-d H:i:s").' '.$context.PHP_EOL) === FALSE) {
               // echo "Ошибка записи содержимого в файл ($fileName)";
                exit();
            }
          //  echo "Содержимое записано в файл ($fileName)"."\n\r";
            fclose($handle);
        } else {
            //echo "Файл $fileName не доступен для записи";
        }
    }

    /**
     * Возвращает все ссылки категорий
     * @return array
     */
    public function getCategoriesLinks ()
    {
        $arrCategories = array();
        $this->setCurlOptURL('https://5element.by/catalog');
        $this->setCurlOptStatic();
        $html = $this->getCurlExec();
        $pq = \phpQuery::newDocument($html);
        //  $links = $pq->find('.catalog-prod-col-item>a', '.catalog-prod-col-item-nested-item>a');
        $links = $pq->find('.catalog-prod-col-item>a, .catalog-prod-col-item-nested-item>a');
        $i = 0;
        foreach ($links as $link) {
            if (strpos(trim(pq($link)->attr('href')), '/action/') === false) { // исключаем акции
                $arrCategories[$i]['href'] = trim(pq($link)->attr('href'));
                $arrCategories[$i]['name'] = trim(pq($link)->text());
                $arrCategories[$i]['id'] = (int)self::getSectionId($arrCategories[$i]['href']);
                $i++;
            }
        }
        return $arrCategories;
    }

    /**
     *  Устанавливает значение URL
     * @param $url
     */
    public function setCurlOptURL ($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }

    /**
     * Устанавливает опции для дескриптора, которые будут неизменными
     * @param $url
     * @param array $postFields
     */
    public function setCurlOptStatic ()
    {   // для работы с сетью через прокси
        if ($_SERVER['COMPUTERNAME'] == 'GT-ASUP6VM') {
            $this->setCurlOptProxy();
        }
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); // возвращает результат в переменную а не в буфер
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true); //использовать редиректы
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // работа с https
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // работа с https
        curl_setopt($this->ch, CURLOPT_POST, 1);
    }

    /**
     * Устанавливает прокси-соединение
     */
    private function setCurlOptProxy ()
    {
        curl_setopt($this->ch, CURLOPT_PROXY, self::PROXY_SERVER);
        curl_setopt($this->ch, CURLOPT_PROXYPORT, self::PROXY_PORT);
        curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, self::PROXY_NAME . ":" . self::PROXY_PASS);
    }

    /**
     *  Выполнить запрос
     * @return mixed
     */
    public function getCurlExec ()
    {
        return curl_exec($this->ch);
    }

    public static function getSectionId ($url)
    {
        $p = strpos($url, '-');
        $sectionId = substr($url, 9, $p - 9);
        return $sectionId;
    }

    /**
     * Возвращает массив ссылок на категории
     */
    public  function getCategotyDesc ($catecoryId)
    {
        $url = self::getCategoryAJAX_URL($catecoryId);
        $this->setCurlOptStatic();
        $this->setCurlOptURL($url);
        $html = $this->getCurlExec();
        $html = json_decode($html);
        $ds['ID_INPUT'] = $catecoryId;
        $ds['ID'] = $html->updateSection->section->ID;
        $ds['UF_IB_RELATED_ID'] = $html->updateSection->section->UF_IB_RELATED_ID;
        $ds['NAME'] = $html->updateSection->section->NAME;
        $ds['SEO_NAME'] = $html->updateSection->section->SEO_NAME;
        $ds['DETAIL_URL'] = $html->updateSection->section->DETAIL_URL;
        $ds['DETAIL_PICTURE'] = $html->updateSection->section->DETAIL_PICTURE;
        $ds['DATE_CREATE'] = $html->updateSection->section->DATE_CREATE;
        $ds['COUNT'] = $html->count;
        return $ds;
    }

    /**
     * Посылает запрос на AJAX страницу
     * @param $sectionId
     * @return string
     */
    public static function getCategoryAJAX_URL ($sectionId)
    {
        return "https://5element.by/ajax/catalog_category_list.php?SECTION_ID=$sectionId";
    }

    /**
     * Поля для запроса продуктов в категории
     * @param $categoryId
     * @param $currentPage
     * @param $itemsPerPage
     * @return array
     */
    public function getPostDataCat ($categoryId, $currentPage, $itemsPerPage)
    {
        return array('categoryId' => (int)$categoryId,
            'currentPage' => (int)$currentPage,
            'itemsPerPage' => (int)$itemsPerPage,
            'viewType' => 1,
            'sortName' => 'popular',
            'sortDest' => 'desc',
            'filterInStock' => 1,
            'filterInStore' => 0);
    }
    /**
     * Поля для запроса информации о продукте
     * @param $categoryId
     * @param $currentPage
     * @param $itemsPerPage
     * @return array
     */
    public function getPostDataProd ($productId)
    {

        return array(   'deliveryGetState[tab]'=>'shop',
                        'deliveryGetState[shopView]'=>'list',
                        'deliveryGetState[productId]'=>$productId,
                        'deliveryGetState[forCheckout]'=>0,
                        'deliveryGetState[mkpShowAll]'=>false,
                        'deliveryGetState[showMap]'=>false,
                        'deliveryGetState[data][shopLocationId]'=>31379,
                        'deliveryGetState[data][homeLocationId]'=>31379,
                        'deliveryGetState[data][tab]'=>'shop',
                        'deliveryGetState[data][shopId]'=>0,
                        'deliveryGetState[data][deliveryProductId]'=>0,
                        'deliveryGetState[data][deliveryProductCategoryId]'=>0,
                        'deliveryGetState[data][deliveryShopId]'=>0,
                        'deliveryGetState[data][price]'=>0);
    }

    /**
     *  Устанавливает значения post полей
     * @param $categoryId ид категории
     * @param $currentPage текущая страница
     * @param $itemsPerPage количество товаро
     */
    public  function setCurlOptPostFields ($postFields)
    {
        //  $postFields = array('categoryId' => $categoryId, 'currentPage' => $currentPage, 'itemsPerPage' => $itemsPerPage, 'viewType' => 1, 'sortName' => 'popular', 'sortDest' => 'desc', 'filterInStock' => 1, 'filterInStore' => 0);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
    }

    /**
     * Закрывает дескриптор сеанса
     */
    public  function curlClose ()
    {
        curl_close($this->ch);
    }


}
