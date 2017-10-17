<?php

require_once 'Parse5Elem.php';
require_once 'MySqlDB.php';

function getCategories()
{
    $p = new \Parse5Elem\Parse5Elem();
    $p->setCurlOptStatic();
    $url = "https://5element.by/catalog";
    $p->setCurlOptURL($url);
    $html = $p->getCurlExec();
    $pq = \phpQuery::newDocument($html);
    $titles = $pq->find('.nested-list-item-subcat');
    $i = 0;
    foreach ($titles as $title) {
        $productDesc[$i]['name'] = trim(pq($title)->find('a')->text());
        $productDesc[$i]['href'] = pq($title)->find('a')->attr('href');
        $productDesc[$i]['id'] = (int)$p::getSectionId($productDesc[$i]['href']);
        $i++;
    }
    $p->CurlClose();
    return $productDesc;

}

function getCategoryDesc()
{
    $p = new \Parse5Elem\Parse5Elem();
    $m = new \MySqlDB\MySqlDB();
    $m->InsertMain('Парсинг 17.10.2017', (string)date("H:i:s"), 1);
    $pd = getCategories();
    echo date("H:i:s") . "\n\r";
    for ($i = 0; $i < count($pd); $i++) {
        $cd = $p->getCategotyDesc($pd[$i]['id']);
        $idParsing = $m->getMaxId('s_pars_main');
        $m->insertCategory($cd['UF_IB_RELATED_ID'], $cd['NAME'], $cd['ID_INPUT'], 0, null, 0, $cd['DETAIL_URL'], $idParsing);
        //  var_dump($cd);
        //  echo $cd['ID_INPUT']."==". $cd['ID']."==".$cd['UF_IB_RELATED_ID']."==".$cd['NAME']. "\n\r";
    }
    echo date("H:i:s") . "\n\r";
    echo "Всего: " . count($pd);
    $p->CurlClose();
    $m->close();
}

function getProductFromCat()
{
    $p = new \Parse5Elem\Parse5Elem();
    $p->setCurlOptStatic();
    $m = new \MySqlDB\MySqlDB();
    $qCategoties = $m->getCategories();
    foreach ($qCategoties as $qCategory) {
        $curPage = 1;
        $i = 0;
        do {
            $url = $p::getCategoryAJAX_URL(0);
            $p->setCurlOptURL($url);
            $postData = $p->getPostDataCat($qCategory['catId'], $curPage, 150);
            $p->setCurlOptPostFields($postData);
            $html = $p->getCurlExec();
            $json = json_decode($html);
            $cnt = $json->count;
            $maxPage=floor($cnt/150)+1;
            $html = $p::getDecodeHTML($html);
            $pq = phpQuery::newDocument($html);
            $titles = $pq->find('.spec-product.js-product-item');
            foreach ($titles as $title) {
                $productDesc[$i]['name'] = trim(pq($title)->find('.spec-product-middle-title>a')->text());
                $productDesc[$i]['prodId'] = pq($title)->attr('data-id');
                $productDesc[$i]['price'] = trim(str_replace(' ', '', pq($title)->find('span._price')->text()));
                $productDesc[$i]['code'] = trim(str_replace('Код товара:', '', pq($title)->find('.product-middle-patio-code')->text()));
                $m->insertProduct($qCategory['catId'], $productDesc[$i]['prodId'],$productDesc[$i]['name'], $productDesc[$i]['code'],null, $productDesc[$i]['price'] );
                $i++;
            }
            $curPage++;
        } while ($curPage <= $maxPage);}
    var_dump($productDesc);
    $p->curlClose();
    $m->close();

}
//getCategoryDesc();
getProductFromCat();