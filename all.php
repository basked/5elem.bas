<?
require_once 'Parse5Elem.php';
require_once 'MySqlDB.php';
$m = new MySqlDB\MySqlDB();
$currProds=$m->getCurrentParsingASAM();
foreach ($currProds as $currProd){
    echo " <tr>
     <td> ".$currProd['date']."</td>
     <td>".$currProd['date_end']."</td>
     <td>".$currProd['cat_name']."</td>
     <td>".$currProd['prod_name']."</td>
     <td>".$currProd['opl_name']."</td>        
   </tr>";
}
$m->close();

/*mcat_name, p.name prod_name, o.name opl_name
 * */
