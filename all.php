<?
require_once 'MySqlDB.php';
$m = new MySqlDB\MySqlDB();
$currPars = $m->getCurrentParsingSAM();
/*foreach ($currPars as $currPar) {
    echo "<table border='1'> <tr>
     <td>" . $currPar['id'] . "</td>  
     <td>" . $currPar['cnt'] . "</td>   	  
     <td>" . $currPar['date'] . "</td>
     <td>" . $currPar['date_end'] . "</td>
     <td>" . $currPar['act'] . "</td>
   </tr> </table>";
}*/
echo json_encode($currPars);
$m->close();