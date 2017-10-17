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

$p = new \Parse5Elem\Parse5Elem();
$pd=getCategories();
echo date("H:i:s")."\n\r";
for ($i=0;$i<count($pd);$i++){
   $cd= $p->getCategotyDesc($pd[$i]['id']);
   var_dump($cd);
}
echo date("H:i:s")."\n\r";
echo "Всего: ".count($pd);
$p->CurlClose();