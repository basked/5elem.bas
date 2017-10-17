<?php
/**
 * Created by PhpStorm.
 * User: basked
 * Date: 10.10.2017
 * Time: 11:48
 */

namespace Parse5Elem;

require_once('phpQuery/phpQuery.php'); // подключаем phpQuery
require_once('libs/helpers.php'); // подключаем файл для вывода отладочной инфы

class Parse5Elem
{

    const PROXY_SERVER = "172.16.15.33";
    const PROXY_PORT = 3128;
    const PROXY_NAME = "gt-asup6";
    const PROXY_PASS = "teksab";
    const SITE = "https://5element.by";
    const CURR_PAGE = "/catalog.php"; // страница курса

    /**
     * @var хранит данные html из PHPQuery
     */
    public $html;

    /**
     * @var переменная для работы с  CURL(дескриптор сеанса)
     */
    private $ch;

    public static function getSectionId($url)
    {
        $p = strpos($url, '-');
        $sectionId = substr($url, 9, $p - 9);
        return $sectionId;
    }


    /**
     * Parse5Elem constructor.
     */
    public function __construct($url='')
    {
        $this->html = '';
        $this->ch = curl_init($url);
    }

    /**
     * возвращает массив ссылок на категории
     */
    public function getCategotyDesc($catecoryId)
    {
        $url = self::getCategoryAJAX_URL($catecoryId);
        $this->setCurlOptStatic();
        $this->setCurlOptURL($url);
        $html = $this->getCurlExec();
        $html = json_decode($html);
        $ds['ID_INPUT'] =$catecoryId;
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

    private function setCurlOptProxy()
    {
        curl_setopt($this->ch, CURLOPT_PROXY, self::PROXY_SERVER);
        curl_setopt($this->ch, CURLOPT_PROXYPORT, self::PROXY_PORT);
        curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, self::PROXY_NAME . ":" . self::PROXY_PASS);
    }


    /**
     * Устанавливает опции для дескриптора, которые будут неизменными
     * @param $url
     * @param array $postFields
     */
    public function setCurlOptStatic()
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
     * Посылает запрос на AJAX страницу
     * @param $sectionId
     * @return string
     */
    public static function getCategoryAJAX_URL($sectionId)
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
    public function getPostDataCat($categoryId, $currentPage, $itemsPerPage)
    {
        return array('categoryId' => $categoryId,
            'currentPage' => $currentPage,
            'itemsPerPage' => $itemsPerPage,
            'viewType' => 1,
            'sortName' => 'popular',
            'sortDest' => 'desc',
            'filterInStock' => 1,
            'filterInStore' => 0);

      }

    /**
     *  устанавливает значения post полей
     * @param $categoryId ид категории
     * @param $currentPage текущая страница
     * @param $itemsPerPage количество товаро
     */
    public function setCurlOptPostFields($postFields)
    {
        //  $postFields = array('categoryId' => $categoryId, 'currentPage' => $currentPage, 'itemsPerPage' => $itemsPerPage, 'viewType' => 1, 'sortName' => 'popular', 'sortDest' => 'desc', 'filterInStock' => 1, 'filterInStore' => 0);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
    }


    public function setCurlOptURL($url)
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
    }


    public function curlClose()
    {
        curl_close($this->ch);
    }

    public function getCurlExec()
    {
        return curl_exec($this->ch);
    }

    /**
     * Декодирует в кирилицу
     * @param $json_str
     * @return mixed
     */
    private static function jdecoder($json_str)
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

    public static function getDecodeHTML($response)
    {
        $res = self::jdecoder($response);
        $res = str_replace('\"', '"', $res);
        $res = str_replace('\/', '/', $res);
        return $res;
    }

    /**
     * @param $fileName файл_лога
     * @param $context  содержимое_для_записи
     */
    public function logToFile($fileName, $context)
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
}