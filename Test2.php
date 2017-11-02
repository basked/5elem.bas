<?php
error_reporting(E_ALL);
set_time_limit(100000); // время выполнения скрипта
header("Content-type: text/html; charset = utf-8"); // кодировка utf-8
require_once 'Parse5Elem.php';
require_once 'MySqlDB.php';
function insertCategoriesFrom5Elem ()
{
    echo "insertCategoriesFrom5Elemdate " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $m = new  \MySqlDB\MySqlDB();
    $catLinks = $p->getCategoriesLinks();
    if (!empty($catLinks)) {
        // $m->updateAct();
        /*!! ПРОДУМАТЬ КАК ОБНОВИТЬ СУЩЕСТВУЮЩИЕ КАТЕГОРИИ*/
        for ($i = 0; $i < count($catLinks); $i++) {
            if ($m->existCategorySAM($catLinks[$i]['id']) == 0) {
                if ($_SERVER['HTTP_HOST'] == "companysam.by") {
                    $m->insertCategorySAM(mb_convert_encoding($catLinks[$i]['name'], 'Windows-1251', 'auto'), $catLinks[$i]['id'], -1, 0);
                } else {
                    $m->insertCategorySAM($catLinks[$i]['name'], $catLinks[$i]['id'], -1, 0);
                }
            } else $m->inserLogSAM('1', 'Кетегория с ID=' . $catLinks[$i]['id'] . ' существует в s_p_category_5' . '\n\r');
        }
    }
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}

function updateCategoriesFrom5Elem ()
{
    echo "updateCategoriesFrom5Elem " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $m = new  \MySqlDB\MySqlDB();
    $catEmptyRoots = $m->getEmptyRootIdCategorySAM();
    if (!empty($catEmptyRoots)) {
        foreach ($catEmptyRoots as $catEmptyRoot) {
            $cd = $p->getCategotyDesc($catEmptyRoot['catId']);
            if (!empty($cd[UF_IB_RELATED_ID])) {
                if ($m->existCategorySAM($cd[UF_IB_RELATED_ID]) == 0) {
                    if ($_SERVER['HTTP_HOST'] == "companysam.by") {
                        $rootId = $m->insertCategorySAM(mb_convert_encoding($cd[NAME], 'Windows-1251', 'auto'), $cd[UF_IB_RELATED_ID], 1, 1);
                    } else {
                        $rootId = $m->insertCategorySAM($cd[NAME], $cd[UF_IB_RELATED_ID], 1, 1);
                    }
                } else {
                    $rootId = $m->getIdCategorySAM($cd[UF_IB_RELATED_ID]);
                }
                $m->updateRootId($catEmptyRoot['catId'], $rootId);
            }
        }
    }
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}

function insertProductFrom5Elem ($out_main_id = 0, $p_begCatId = 0)
{
    echo "getDescProductFrom5Elem " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $p->setCurlOptStatic();
    $p->setCurlOptURL('https://5element.by/ajax/catalog_category_list.php?SECTION_ID=0');
    $m = new  \MySqlDB\MySqlDB();
    $m->updateActMainSAM(0);
    if ($out_main_id == 0) {
        $main_id = $m->insertMainSAM(date("Y-m-d H:i:s"), 1);
    } else {
        $main_id = $out_main_id;
    }
    $catUniRoots = $m->getUniqueRootIdCategorySAM($p_begCatId); // уникальные Id главных категорий товаров
    foreach ($catUniRoots as $catUniRoot) {
        $curPage = 1;
        $i = 0;
        do {
            $postData = $p->getPostDataCat((int)$catUniRoot['catId'], $curPage, 150);
            $p->setCurlOptPostFields($postData);
            $html = $p->getCurlExec();
            $json = json_decode($html);
            $cnt = $json->count;
            $maxPage = floor($cnt / 150) + 1;
            $html = $p::getDecodeHTML($html);
            $pq = phpQuery::newDocument($html);
            $products = $pq->find('.spec-product.js-product-item');
            if (!empty($products)) {
                ob_start();
                foreach ($products as $product) {
                    $productDesc[$i]['name'] = trim(pq($product)->find('.spec-product-middle-title>a')->text());
                    $productDesc[$i]['prodId'] = pq($product)->attr('data-id');
                    $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($product)->find('span._price')->text()));
                    $productDesc[$i]['code'] = trim(str_replace('Код товара:', '', pq($product)->find('.product-middle-patio-code')->text()));
                    $productDesc[$i]['oplata_creditId'] = pq($product)->find('.product-item-sticker.product-item-sticker-credit.js-sticker')->attr('data-action-id');
                    $productDesc[$i]['oplata_name'] = pq($product)->find('.product-item-sticker.product-item-sticker-credit.js-sticker>img')->attr('title');
                    // делаем проверку на существование кредита
                    if (!empty($productDesc[$i]['oplata_creditId'])) {
                        if ($m->existOplataSAM($productDesc[$i]['oplata_creditId']) == 0) {
                            if ($_SERVER['HTTP_HOST'] == "companysam.by") {
                                $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'], mb_convert_encoding($productDesc[$i]['oplata_name'], 'Windows-1251', 'auto'));
                            } else {
                                $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'], $productDesc[$i]['oplata_name']);
                            }
                        } else {
                            $oplataId = $m->getIdFromCreditIdOplataSAM($productDesc[$i]['oplata_creditId']);
                        };
                    }
                    // делаем проверку на существование продукции
                    if (!empty($productDesc[$i]['prodId'])) {
                        if ($m->existProductSAM($productDesc[$i]['prodId']) == 0) {
                            //   $productId = $m->insertProductSAM($m->getIdCategorySAM($catUniRoot['catId']), $productDesc[$i]['prodId'],mb_convert_encoding( $productDesc[$i]['name'],'Windows-1251','auto'), $productDesc[$i]['code']/*, null, $productDesc[$i]['price']*/);
                            $productId = $m->insertProductSAM($m->getIdCategorySAM($catUniRoot['catId']), $productDesc[$i]['prodId'], $productDesc[$i]['name'], $productDesc[$i]['code']/*, null, $productDesc[$i]['price']*/);
                        } else {
                            $productId = $m->getIdFromProdIdProductSAM($productDesc[$i]['prodId']);
                        };
                    }
                    $m->insertCenaSAM($productId, $productDesc[$i]['price'], $oplataId, $main_id);
                    $i++;
                }
                ob_flush();
            }
            $curPage++;
            phpQuery::unloadDocuments();
            gc_collect_cycles();
        } while ($curPage <= $maxPage);
        $m->inserLogSAM($catUniRoot['catId'], "ID_MAIN=$main_id; Кол-во=$i");
    }

    $m->updateDateEndMainSAM($main_id, date("Y-m-d H:i:s"));
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}

function insertProductFromLimit5Elem ($out_main_id = 0, $offset = 0)
{
    $m = new  \MySqlDB\MySqlDB();
    if ($out_main_id == 0) {
        $main_id = $m->insertMainSAM(date("Y-m-d H:i:s"), 1);
    } else {
        $main_id = $out_main_id;
    }
    $catUniRoots = $m->getUniqueRootIdCategoryLimySAM($offset); // уникальные Id главных категорий товаров
    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, 'https://5element.by/ajax/catalog_category_list.php?SECTION_ID=0');
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch1, CURLOPT_HEADER, 0);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); // работа с https
    curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false); // работа с https
    curl_setopt($ch1, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'); //выставляем настройки браузера
   // if ($_SERVER['COMPUTERNAME'] == 'GT-ASUP6VM') {
        curl_setopt($ch1, CURLOPT_PROXY, "172.16.15.33");
        curl_setopt($ch1, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch1, CURLOPT_PROXYUSERPWD, "gt-asup6:teksab");
  //  }
    foreach ($catUniRoots as $catUniRoot) {
        $curPage = 1;
        $i = 0;
        do {
            $postData = Parse5Elem\Parse5Elem::getPostDataCat((int)$catUniRoot['catId'], $curPage, 150);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, $postData);
            curl_exec($ch1);
            $html = curl_multi_getcontent($ch1);
            $m->inserLogSAM($offset, "CategoryID=" . $catUniRoot['catId'] . " Html=" . $html);
            $json = json_decode($html);
            $cnt = $json->count;
            $maxPage = floor($cnt / 150) + 1;
            $html = Parse5Elem\Parse5Elem::getDecodeHTML($html);
            $pq = phpQuery::newDocument($html);
            $products = $pq->find('.spec-product.js-product-item');
            if (!empty($products)) {
                ob_start();
                foreach ($products as $product) {
                    $productDesc[$i]['name'] = trim(pq($product)->find('.spec-product-middle-title>a')->text());
                    $productDesc[$i]['prodId'] = pq($product)->attr('data-id');
                    $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($product)->find('span._price')->text()));
                    $productDesc[$i]['code'] = trim(str_replace('Код:', '', pq($product)->find('product-middle-patio-code')->text()));
                    $productDesc[$i]['oplata_creditId'] = pq($product)->find('.product-item-sticker.product-item-sticker-credit.js-sticker')->attr('data-action-id');
                    $productDesc[$i]['oplata_name'] = pq($product)->find('.product-item-sticker.product-item-sticker-credit.js-sticker>img')->attr('title');
                    // делаем проверку на существование кредита
                    if (!empty($productDesc[$i]['oplata_creditId'])) {
                        if ($m->existOplataSAM($productDesc[$i]['oplata_creditId']) == 0) {
                            if ($_SERVER['HTTP_HOST'] == "companysam.by") {
                                $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'], mb_convert_encoding($productDesc[$i]['oplata_name'], 'Windows-1251', 'auto'));
                            } else {
                                $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'], $productDesc[$i]['oplata_name']);
                            }
                        } else {
                            $oplataId = $m->getIdFromCreditIdOplataSAM($productDesc[$i]['oplata_creditId']);
                        };
                    }
                    // делаем проверку на существование продукции
                    if (!empty($productDesc[$i]['prodId'])) {
                        if ($m->existProductSAM($productDesc[$i]['prodId']) == 0) {
                            if ($_SERVER['HTTP_HOST'] == "companysam.by") {
                                $productId = $m->insertProductSAM($m->getIdCategorySAM($catUniRoot['catId']), $productDesc[$i]['prodId'], mb_convert_encoding($productDesc[$i]['name'], 'Windows-1251', 'auto'), $productDesc[$i]['code']/*, null, $productDesc[$i]['price']*/);
                            } else {
                                $productId = $m->insertProductSAM($m->getIdCategorySAM($catUniRoot['catId']), $productDesc[$i]['prodId'], $productDesc[$i]['name'], $productDesc[$i]['code']/*, null, $productDesc[$i]['price']*/);
                            }
                        } else {
                            $productId = $m->getIdFromProdIdProductSAM($productDesc[$i]['prodId']);
                        };
                    }
                    $m->insertCenaSAM($productId, $productDesc[$i]['price'], $oplataId, $main_id);
                    $i++;
                }
                ob_flush();
            }
            $curPage++;
            phpQuery::unloadDocuments();
            gc_collect_cycles();
        } while ($curPage <= $maxPage);
    }
    $m->updateDateEndMainSAM($main_id, date("Y-m-d H:i:s"));
    $m->close();
}

function getProductDetailFrom5Elem ()
{
    echo "getProductDetailFrom5Elem " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $p->setCurlOptStatic();
    $p->setCurlOptURL('https://5element.by/ajax/multipoint.php');
    $m = new  \MySqlDB\MySqlDB();
    $prodUniProds = $m->getUniqueCodProductSAM(); // уникальные Id товаров
    foreach ($prodUniProds as $prodUniProd) {
        $postData = $p->getPostDataProd((int)$prodUniProd['prodId']);
        $p->setCurlOptPostFields($postData);
        $html = $p->getCurlExec();
        $json = json_decode($html);
        var_dump($json);
        /*   $html = $p::getDecodeHTML($html);
           $pq = phpQuery::newDocument($html);
           $products = $pq->find('.spec-product.js-product-item');
           foreach ($products as $product) {
               $productDesc[$i]['name'] = trim(pq($product)->find('.spec-product-middle-title>a')->text());
               $productDesc[$i]['prodId'] = pq($product)->attr('data-id');
               $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($product)->find('span._price')->text()));
               $productDesc[$i]['code'] = trim(str_replace('Код товара:', '', pq($product)->find('.product-middle-patio-code')->text()));
               $m->insertProductSAM($catUniRoot['rootId'], $productDesc[$i]['prodId'], $productDesc[$i]['name'], $productDesc[$i]['code']/*, null, $productDesc[$i]['price']);*/
    }
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}

$out_main_id = (int)$_POST['out_main_id'];
$offset = (int)$_POST['offset'];
if (empty($out_main_id)) {
    $m = new \MySqlDB\MySqlDB();
    $m->updateActMainSAM(0);
    $main_id = $m->insertMainSAM(date("Y-m-d H:i:s"), 1);
    $m->close();
}
if (empty($offset)) {
    $offset = 0;
}
/*insertCategoriesFrom5Elem();
updateCategoriesFrom5Elem();*/

Parse5Elem\Parse5Elem::logToFile('log.txt', "Заупск парсинга для  Offset=$offset; Out_Main_id=$out_main_id ");
insertProductFromLimit5Elem($out_main_id, $offset);
Parse5Elem\Parse5Elem::logToFile('log.txt', "Окончание парсинга для  Offset=$offset; Out_Main_id=$out_main_id ");
