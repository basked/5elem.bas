<?php
require_once 'Parse5Elem.php';
require_once 'MySqlDB.php';

function insertCategoriesFrom5Elem()
{
    echo "insertCategoriesFrom5Elemdate " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $m = new  \MySqlDB\MySqlDB();
    $catLinks = $p->getCategoriesLinks();
    for ($i = 0; $i < count($catLinks); $i++) {
        if ($m->existCategorySAM($catLinks[$i]['id']) == 0) {
            $m->insertCategorySAM($catLinks[$i]['name'], $catLinks[$i]['id'], 0, 0);
        }
    }
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}


function updateCategoriesFrom5Elem()
{
    echo "updateCategoriesFrom5Elem " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $m = new  \MySqlDB\MySqlDB();
    $catEmptyRoots = $m->getEmptyRootIdCategorySAM();
    foreach ($catEmptyRoots as $catEmptyRoot) {
        $cd = $p->getCategotyDesc($catEmptyRoot['catId']);
        $m->updateRootId($catEmptyRoot['catId'], $cd[UF_IB_RELATED_ID]);
    }
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}


function insertProductFrom5Elem()
{
    echo "getDescProductFrom5Elem " . date("H:i:s") . "\n\r";
    $p = new \Parse5Elem\Parse5Elem();
    $p->setCurlOptStatic();
    $p->setCurlOptURL('https://5element.by/ajax/catalog_category_list.php?SECTION_ID=0');
    $m = new  \MySqlDB\MySqlDB();
    $catUniRoots = $m->getUniqueRootIdCategorySAM(); // уникальные Id главных категорий товаров
    foreach ($catUniRoots as $catUniRoot) {
        $curPage = 1;
        $i = 0;
        do {
            $postData = $p->getPostDataCat((int)$catUniRoot['rootId'], $curPage, 150);
            $p->setCurlOptPostFields($postData);
            $html = $p->getCurlExec();
            $json = json_decode($html);
            $cnt = $json->count;
            $maxPage = floor($cnt / 150) + 1;
            $html = $p::getDecodeHTML($html);
            $pq = phpQuery::newDocument($html);
            $products = $pq->find('.spec-product.js-product-item');
            foreach ($products as $product) {
                $productDesc[$i]['name'] = trim(pq($product)->find('.spec-product-middle-title>a')->text());
                $productDesc[$i]['prodId'] = pq($product)->attr('data-id');
                $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($product)->find('span._price')->text()));
                $productDesc[$i]['code'] = trim(str_replace('Код товара:', '', pq($product)->find('.product-middle-patio-code')->text()));
                $m->insertProductSAM($catUniRoot['rootId'], $productDesc[$i]['prodId'], $productDesc[$i]['name'], $productDesc[$i]['code']/*, null, $productDesc[$i]['price']*/);
                $i++;

            }

            $curPage++;
        } while ($curPage <= $maxPage);
        echo "ID категории: ".$catUniRoot['rootId'].". Кол-во=".$i . "\n\r";
    }
    $m->close();
    $p->curlClose();
    echo date("H:i:s") . "\n\r";
}

insertProductFrom5Elem();



/*insertCategoriesFrom5Elem();
updateCategoriesFrom5Elem();
*/
