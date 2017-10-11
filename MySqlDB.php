<?php
/**
 * Created by PhpStorm.
 * User: gt-asup6vm
 * Date: 10.10.2017
 * Time: 11:00
 */

namespace MySqlDB;


class MySqlDB extends \mysqli
{
    const HOST = "localhost";
    const PORT = 3306;
    const USER = 'root';
    const PASS = '';
    const DB = '5elem_db';
    private $mysqli;

    /**
     * MySqlDB constructor.
     */
    public function __construct ()
    {
        $this->mysqli = mysqli_connect(self::HOST, self::USER, self::PASS, self::DB, self::PORT);
        if (mysqli_connect_errno($this->mysqli)) {
            echo "Не удалось подключиться к MySQL: " . mysqli_connect_error();
        }
    }

    public function InsertProduct (
        $categoryId,
        $prodId,
        $prodName,
        $prodCode,
        $price)
    {
        if (!($stmt = $this->mysqli->prepare("
            INSERT INTO `5elem_db`.`Product`
            (
            `categoryId`,
            `prodId`,
            `prodName`,
            `prodCode`,
            `price`)
            VALUES
            (
            ?,
            ?,
            ?,
            ?,
            ?)"))) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }


        if (!$stmt->bind_param("iisid",
            $categoryId,
            $prodId,
            $prodName,
            $prodCode,
            $price)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    public function InsertCatgory (
        $catId,
        $catName,
        $sectId,
        $cntPage,
        $dateIns,
        $act,
        $catURL)
    {
        if (!($stmt = $this->mysqli->prepare("
            INSERT INTO `5elem_db`.`Category`
            (
            `catId`,
            `catName`,
            `sectId`,
            `cntPage`,
            `dateIns`,
            `act`,
            `catURL`)
            VALUES
            (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }


        if (!$stmt->bind_param("isiisis",
            $catId,
            $catName,
            $sectId,
            $cntPage,
            $dateIns,
            $act,
            $catURL)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }


    public function getCategories ()
    {
        $query = "SELECT DISTINCT id, catURL, catId, catName, sectId, cntPage, cntPage DIV 150 AS maxPage
                    FROM
                        5elem_db.Category
                    WHERE
                        catId <> 0 AND sectId <> 0
                    ORDER BY maxpage DESC
                    ";

        if ($stmt = $this->mysqli->prepare($query)) {

            /* Запустить выражение */
            $stmt->execute();

            /* Определить переменные для результата */
            $stmt->bind_result($id,$catURL, $catId, $catName, $sectId, $cntPage, $maxPage);
            $i = 0;
            /* Выбрать значения */
            while ($stmt->fetch()) {
                $catDesc[$i]['id'] = $id;
                $catDesc[$i]['catId'] = $catId;
                $catDesc[$i]['sectId'] = $sectId;
                $catDesc[$i]['maxPage'] = $maxPage;
                $catDesc[$i]['catName'] = $catName;
                $i++;
            }
            /* Завершить запрос и закрыть соединение*/
            $stmt->close();
            $this->mysqli->close();
            return $catDesc;
        }
    }
}

/*
$db = new MySqlDB();
$db->InsertProduct(20,'484205','Чехол-книжка Samsung Clear View Standing Cover для Galaxy S8+ голубой','71649','','136.90');
*/