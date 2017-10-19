<?php
/**
 * Created by PhpStorm.
 * User: gt-asup6vm
 * Date: 10.10.2017
 * Time: 11:00
 */

namespace MySqlDB;


class MySqlDB
{
    const HOST = "localhost";
    const PORT = 3306;
    const USER = 'root';
    const PASS = '';
    const DB = 'user1111058_sam';
    public $mysql = nil;

    /**
     * MySqlDB constructor.
     */
    /*!!bas*/
    public function __construct()
    {
        $this->mysql = new \mysqli(self::HOST, self::USER, self::PASS, self::DB, self::PORT);
        if ($this->mysql->connect_errno) {
            printf("Соединение не удалось: %s\n", $this->mysql->connect_error);
            exit();
        }
    }

    public function close()
    {
        $this->mysql->close();
    }

    /**
     * Возвращает данные ввиде массива(ассоативного, числового или комбинированного) из БД взависимости от запроса
     * @param string $sql SQL запрос
     * @param integer $resType Тип MYSQLI_NUM, MYSQLI_ASSOC, MYSQLI_BOTH
     * @return mixed
     */
    public function getTempQuery($sql, $resType)
    {
        $result = $this->mysql->query($sql);
        if (!$result) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        while ($row = $result->fetch_array($resType)) {
            $rows[] = $row;
        }
        $result->close();
        return $rows;
    }

    /**
     * Очистить таблицу с данными
     * @param $tableName , наименование наблицы
     * @return int, сколько строк очищено
     */
    public function truncateTable($tableName)
    {
        $sql = "delete from $tableName";
        if (!($this->mysql->query($sql))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        return $this->mysql->affected_rows;
    }

//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ **/

    public function getIdCategorySAM($catId)
    {
        $res = $this->getTempQuery("SELECT id FROM s_pars_category_5 WHERE catId=$catId and act=1", MYSQLI_ASSOC);
        return (int)$res[0]['id'];
    }

    /**
     * Проверка на существование категории в САМ, по её Id
     * @param $idCat
     * @return mixed
     */
    public function existCategorySAM($catId)
    {
        $res = $this->getTempQuery("SELECT exists(select catId FROM s_pars_category_5 WHERE catId=$catId) AS exist", MYSQLI_ASSOC);
        return (int)$res[0]['exist'];
    }

    /**
     * Выбрать все уникальные идентификаторы категорий, которые используются в 5 Элементе
     * @return mixed
     */
    public function getUniqueRootIdCategorySAM()
    {
        return $this->getTempQuery("SELECT DISTINCT rootId FROM s_pars_category_5 WHERE act=1 ORDER BY rootId ASC", MYSQLI_ASSOC);
    }

    /**
     * Выбрать все уникальные идентификаторы подкатегорий, которые используются в 5 Элементе
     * @return mixed
     */
    public function getUniqueCatIdCategorySAM()
    {
        return $this->getTempQuery("SELECT DISTINCT catId FROM s_pars_category_5 ORDER BY catId ASC", MYSQLI_ASSOC);
    }

    /**
     * Выбрать все подкатегории, у которых не заполнены значение главной категории
     * @return mixed
     */
    public function getEmptyRootIdCategorySAM()
    {
        return $this->getTempQuery("SELECT DISTINCT catId FROM s_pars_category_5 WHERE rootId=0 ORDER BY catId ASC", MYSQLI_ASSOC);
    }


    /**
     * Вставить новую запись в классификатор категорий
     * @param $name , наименование
     * @param $catId , id в 5 элемент
     * @param $rootId , главная категория в 5 элемент
     * @param $act , актуальность
     */
    public function insertCategorySAM($name, $catId, $rootId, $act)
    {
        if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_category_5 (name,catId,rootId, act) VALUES(?,?,?,?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("siii", $name, $catId, $rootId, $act)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->insert_id;

    }

    /**
     * Обновляет значение главной категории по значению подкатегории
     * @param integer $catId Подкатегория
     * @param integer $rootId Главная категория
     */
    public function updateRootId($catId, $rootId)
    {
        if ($rootId != 0) {
            if (!($stmt = $this->mysql->prepare("UPDATE s_pars_category_5 SET rootId=?,act=1 WHERE catId=?"))
            ) {
                echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
            }
            if (!$stmt->bind_param("ii", $rootId, $catId)
            ) {
                echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
            }
            if (!$stmt->execute()) {
                echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
            }
            return $stmt->affected_rows;
        }
    }

//-------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ **/

//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ПРОДУКТАМИ **/

    /**
     * Вставить новую запись в классификатор категорий
     * @param $name , наименование
     * @param $catId , id в 5 элемент
     * @param $rootId , главная категория в 5 элемент
     * @param $act , актуальность
     */
    public function insertProductSAM($category_id, $prodId, $name, $cod)
    {
        if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_product_5 (category_id, prodId, name, cod) VALUES(?,?,?,?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("iisi",$category_id,$prodId, $name, $cod)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->insert_id;

    }
    /**
     * Выбрать все уникальные идентификаторы подкатегорий, которые используются в 5 Элементе
     * @return mixed
     */
    public function getUniqueCodProductSAM()
    {
        return $this->getTempQuery("SELECT DISTINCT prodId FROM s_pars_product_5  ORDER BY prodId ASC", MYSQLI_ASSOC);
    }


//------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С ПРОДУКТАМИ **/

    public function insertProduct(
        $categoryId,
        $prodId,
        $prodName,
        $prodCode,
        $prodURL,
        $price)
    {
        if (!($stmt = $this->mysql->prepare(
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
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
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
        $idMain)
    {
        if (!($stmt = $this->mysql->prepare('
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
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
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

    public function InsertMain(
        $name,
        $pars_date,
        $act)
    {
        if (!($stmt = $this->mysql->prepare("
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
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
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
                        m.id = c.idMain AND catId <> 0
                            AND sectId <> 0
                            AND idMain = (SELECT 
                                MAX(id)
                            FROM
                                s_pars_main)
                                        ";

        if ($stmt = $this->mysql->prepare($query)) {

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
    function getMaxId($table)
    {
        $query = "SELECT max(id) as maxId
                    FROM
                      $table
                   ";

        if ($stmt = $this->mysql->prepare($query)) {

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


//$m = new MySqlDB();
//echo $m->getIdCategorySAM(1139);

//echo $m->getMaxId('s_pars_main');
//var_dump($m->existCategorySAM(143));
//var_dump($m->tempQuery('SELECT * FROM s_pars_category_5',MYSQLI_NUM));
//var_dump($m->getUniqueCatIdCategorySAM());
//var_dump($m->getUniqueCatIdCategorySAM());
//echo $m->insertCategorySAM('Телик2', 2, 1, 1);
//var_dump($m->getCategories());