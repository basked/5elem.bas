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
    const DB = 'user1111058_5elem_db';
    private $mysqli;

    /**
     * MySqlDB constructor.
     */
    public function __construct()
    {
        $this->mysqli = mysqli_connect(self::HOST, self::USER, self::PASS, self::DB, self::PORT);
        if (mysqli_connect_errno($this->mysqli)) {
            echo "Не удалось подключиться к MySQL: " . mysqli_connect_error();
        }
    }

    public function truncateTable($tableName)
    {
        $sql = "delete from `$tableName`";
        if (!($this->mysqli->query($sql))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }
        return $this->mysqli->affected_rows;
    }


    public function insertProduct(
        $categoryId,
        $prodId,
        $prodName,
        $prodCode,
        $prodURL,
        $price)
    {
        if (!($stmt = $this->mysqli->prepare(
            "INSERT INTO `s_pars_Product`
            (
            `categoryId`,
            `prodId`,
            `prodName`,
            `prodCode`,
            `prodURL`,
            `price`)
            VALUES
            (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }
        if (!$stmt->bind_param("iisisd",
            $categoryId,
            $prodId,
            $prodName,
            $prodCode,
            $prodURL,
            $price)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }

        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    public function insertCategory(
        $catId,
        $catName,
        $sectId,
        $cntPage,
        $dateIns,
        $act,
        $catURL,
        $idParsing)
    {
        if (!($stmt = $this->mysqli->prepare("
            INSERT INTO `s_pars_category`
            (
            `catId`,
            `catName`,
            `sectId`,
            `cntPage`,
            `dateIns`,
            `act`,
            `catURL`,
            `idParsing`)
            VALUES
            (
            ?,
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


        if (!$stmt->bind_param("isiisisi",
            $catId,
            $catName,
            $sectId,
            $cntPage,
            $dateIns,
            $act,
            $catURL,
         $idParsing)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    public function InsertMain(
        $name,
        $pars_date,
        $act)
    {
        if (!($stmt = $this->mysqli->prepare("
            INSERT INTO `s_pars_main`
            (
            `name`,
            `pars_date`,
            `act`)
            VALUES
            (
            ?,
            ?,
            ?
            )"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }


        if (!$stmt->bind_param("ssi",
            $name,
            $pars_date,
            $act)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    public function getCategories()
    {
        $query = "SELECT DISTINCT
                        catId, catName
                    FROM
                        s_pars_category c,
                        s_pars_main m
                    WHERE
                        m.id = c.idParsing AND catId <> 0
                            AND sectId <> 0
                            AND idParsing = (SELECT 
                                MAX(id)
                            FROM
                                s_pars_main)
                                        ";

        if ($stmt = $this->mysqli->prepare($query)) {

            /* Запустить выражение */
            $stmt->execute();

            /* Определить переменные для результата */
            $stmt->bind_result( $catId, $catName);
            $i = 0;
            /* Выбрать значения */
            while ($stmt->fetch()) {
                $catDesc[$i]['catId'] = $catId;
                $catDesc[$i]['catName'] = $catName;
                $i++;
            }
            /* Завершить запрос и закрыть соединение*/
            $stmt->close();
            return $catDesc;
        }
    }


public function getMaxId($table)
{
    $query = "SELECT max(id) as maxId
                    FROM
                      $table
                   ";

    if ($stmt = $this->mysqli->prepare($query)) {

        /* Запустить выражение */
        $stmt->execute();

        /* Определить переменные для результата */
        $stmt->bind_result($maxId);
        $i = 0;
        /* Выбрать значения */
        while ($stmt->fetch()) {
           $res = $maxId;
        }
        /* Завершить запрос и закрыть соединение*/
        $stmt->close();
        return $res;
    }
}
}


//$m= new MySqlDB();
//echo $m->getMaxId('s_pars_main');
//$m->InsertMain('Парсинг 18.10,2017', (string)date("H:i:s"),1);
//var_dump($m->getCategories());