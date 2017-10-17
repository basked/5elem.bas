<?php

require_once 'Parse5Elem.php';

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
    $pd = getCategories();
    echo date("H:i:s") . "\n\r";
    for ($i = 0; $i < count($pd); $i++) {
        $cd = $p->getCategotyDesc($pd[$i]['id']);
       var_dump($cd);
      //  echo $cd['ID_INPUT']."==". $cd['ID']."==".$cd['UF_IB_RELATED_ID']."==".$cd['NAME']. "\n\r";
    }
    echo date("H:i:s") . "\n\r";
    echo "Всего: " . count($pd);
    $p->CurlClose();
}

function getProductFromCat()
{
    $p = new \Parse5Elem\Parse5Elem();
    $p->setCurlOptStatic();
    $url = $p::getCategoryAJAX_URL(267);
    $p->setCurlOptURL($url);
//   $p->setCurlOptURL($url);

    $postData = $p->getPostDataCat(24155, 1, 150);
    $p->setCurlOptPostFields($postData);
    $html = $p->getCurlExec();
    $json=json_decode($html);
    $cnt=$json->count;
    $html = $p::getDecodeHTML($html);
    echo "<h1>".$cnt."</h1>";

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
    var_dump($productDesc);
    $p->curlClose();
}
getCategoryDesc();
//getProductFromCat();