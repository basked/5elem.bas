<?php
error_reporting(E_ALL);
set_time_limit(10000); // время выполнения скрипта
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
                //    $m->insertCategorySAM(mb_convert_encoding($catLinks[$i]['name'],'Windows-1251','auto'), $catLinks[$i]['id'], -1, 0);
                $m->insertCategorySAM($catLinks[$i]['name'], $catLinks[$i]['id'], -1, 0);
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
                    //   $rootId=$m->insertCategorySAM(mb_convert_encoding($cd[NAME],'Windows-1251','auto'), $cd[UF_IB_RELATED_ID], 1, 1);
                      $rootId = $m->insertCategorySAM($cd[NAME], $cd[UF_IB_RELATED_ID], 1, 1);
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
                            $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'],mb_convert_encoding($productDesc[$i]['oplata_name'],'Windows-1251','auto'));
                            //   $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'], $productDesc[$i]['oplata_name']);
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

function insertProductFromLimit5Elem ($out_main_id = 0,$offset=0)
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
    $catUniRoots = $m->getUniqueRootIdCategoryLimySAM($offset); // уникальные Id главных категорий товаров

    foreach ($catUniRoots as $catUniRoot) {
        $curPage = 1;
        $i = 0;
        do {
            $postData = $p->getPostDataCat((int)$catUniRoot['catId'], $curPage, 150);

            $p->setCurlOptPostFields($postData);
            $html = $p->getCurlExec();
            $m->inserLogSAM(2, $p);
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
                            //  $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'],mb_convert_encoding($productDesc[$i]['oplata_name'],'Windows-1251','auto'));
                             $oplataId = $m->insertOplataSAM($productDesc[$i]['oplata_creditId'], $productDesc[$i]['oplata_name']);
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
    return $curPage;
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

//insertCategoriesFrom5Elem();
//updateCategoriesFrom5Elem();


$offset =  (int) $_POST['offset'];

$out_main_id =  (int) $_POST['out_main_id'];
echo "Param=$out_main_id ";
echo "Param=$offset ";
insertProductFromLimit5Elem($out_main_id, $offset);
echo "Completed for param=$offset execute";


//insertProductFromLimit5Elem(0, 100);

//insertProductFromLimit5Elem(0, 200);

//insertProductFromLimit5Elem(0, 300);

/*try{


    $forDelCens = $m->getRecordsForDeleteCenaSAM($lastCena['main_id'], $lastCena['catId']);
    foreach ($forDelCens as $forDelCen) {
        $m->deleteCenaSAM($forDelCen['id']);
    }
    $m->close();
    if (!empty($forDelCens)) {
        insertProductFrom5Elem($lastCena['main_id'], $lastCena['catId']);
    } else {
        insertProductFrom5Elem(0, 0);
    }} catch (Exception $e) {
    // код который может обработать исключение
    echo "FATALL_ERRRRORRRRRR".$e->getMessage();
    $m = new  \MySqlDB\MySqlDB();
    $lastCena = $m->getLastRecordCenaSAM();
    $forDelCens = $m->getRecordsForDeleteCenaSAM($lastCena['main_id'], $lastCena['catId']);
    foreach ($forDelCens as $forDelCen) {
        $m->deleteCenaSAM($forDelCen['id']);
    }
    $m->close();
    if (!empty($forDelCens)) {
        insertProductFrom5Elem($lastCena['main_id'], $lastCena['catId']);
    } else {
        insertProductFrom5Elem(0, 0);
    }
}
*/