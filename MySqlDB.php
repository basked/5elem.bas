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
    const DB = 'user1111058_sam';
    private $mysqli;

    /**
     * MySqlDB constructor.
     */
    /*!!bas*/
    public function __construct ()
    {
        $mysqli = new mysqli(self::HOST, self::USER, self::PASS, self::DB, self::PORT);
        if ($mysqli->connect_errno) {
            printf("Соединение не удалось: %s\n", $mysqli->connect_error);
            exit();
        }
    }

    public function tempQuery ($sql)
    {
        if (!($q = $this->mysqli->query($sql))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }
        return $q->fetch_all();
    }

    /**
     * Очистить таблицу с данными
     * @param $tableName , наименование наблицы
     * @return int, сколько строк очищено
     */
    public function truncateTable ($tableName)
    {
        $sql = "delete from `$tableName`";
        if (!($this->mysqli->query($sql))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }
        return $this->mysqli->affected_rows;
    }

//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ **/

    /**
     * Проверка на существование категории в САМ, по её Id
     * @param $idCat
     * @return mixed
     */
    public function existCategorySAM ($idCat)
    {
        if ($result = $this->mysqli->query("SELECT exists(select catId from s_pars_category_5 where catId=$idCat) as exist")) {
            $row = $result->fetch_row();  // выводим строку
            $result->close();
            return $row[0];
        }
    }

    /**
     * Выбрать все уникальные идентификаторы категорий, которые используются в 5 Элементе
     * @return mixed
     */
    public function getUniqueCatIdCategorySAM ()
    {
        $q = $this->mysqli->query("SELECT DISTINCT catId FROM s_pars_category_5 ORDER BY catId DESC ");
        $rows = $q->fetch_all(); // выводим все записи запроса
        $q->close();
        return $rows;
    }

    /**
     * Вставить новую запись в классификатор категорий
     * @param $name , наименование
     * @param $catId , id в 5 элемент
     * @param $rootId , главная категория в 5 элемент
     * @param $act , актуальность
     */
    public function insertCategorySAM (
        $name,
        $catId,
        $rootId,
        $act)
    {
        if (!($stmt = $this->mysqli->prepare(
            "INSERT INTO s_pars_category_5
             (
               name,
               catId,
               rootId,
               act)
             VALUES
             (
              ?,
              ?,
              ?,
              ?)"
        ))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
        }
        if (!$stmt->bind_param("siii",
            $name,
            $catId,
            $rootId,
            $act)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

//-------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ **/


    public function insertProduct (
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

    public function insertCategory (
        $catId,
        $catName,
        $sectId,
        $cntPage,
        $dateIns,
        $act,
        $catURL,
        $idMain)
    {
        if (!($stmt = $this->mysqli->prepare('
            INSERT INTO `s_pars_category`
            (
            `catId`,
            `catName`,
            `sectId`,
            `cntPage`,
            `dateIns`,
            `act`,
            `catURL`,
            `idMain`)
            VALUES
            (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?)'))
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
            $idMain)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
    }

    public function InsertMain (
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

    public function getCategories ()
    {
        $query = "SELECT DISTINCT
                        catId, catName
                    FROM
                        s_pars_category c,
                        s_pars_main m
                    WHERE
                        m.id = c.idMain AND catId <> 0
                            AND sectId <> 0
                            AND idMain = (SELECT 
                                MAX(id)
                            FROM
                                s_pars_main)
                                        ";

        if ($stmt = $this->mysqli->prepare($query)) {

            /* Запустить выражение */
            $stmt->execute();

            /* Определить переменные для результата */
            $stmt->bind_result($catId, $catName);
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


    public
    function getMaxId ($table)
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


$m = new MySqlDB();
//echo $m->getMaxId('s_pars_main');
//echo $m->existCategorySAM(2);

var_dump($m->tempQuery('SELECT * FROM s_pars_category_5'));
//var_dump($m->getUniqueCatIdCategorySAM());
//$m->insertCategorySAM('Телик2', 2, 1, 1);
//var_dump($m->getCategories());