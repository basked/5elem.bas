<?php
/**
 * Created by PhpStorm.
 * User: gt-asup6vm
 * Date: 10.10.2017
 * Time: 11:00
 */

namespace MySqlDB;


use Parse5Elem\Parse5Elem;

class MySqlDB
{
    const HOST = "localhost";
    const PORT = 3306;
    const USER = 'root';
    const PASS = '';
    const DB = 'user1111058_sam';


    /**
     * MySqlDB constructor.
     */
    /*!!bas*/
    public function __construct ()
    {
        $this->mysql = new \mysqli(self::HOST, self::USER, self::PASS, self::DB, self::PORT);
        if ($this->mysql->connect_errno) {
            printf("Соединение не удалось: %s\n", $this->mysql->connect_error);
            exit();
        }
    }

    public function close ()
    {
        $this->mysql->close();
    }

    /**
     * Очистить таблицу с данными
     * @param $tableName , наименование наблицы
     * @return int, сколько строк очищено
     */
    public function truncateTable ($tableName)
    {
        $sql = "delete from $tableName";
        if (!($this->mysql->query($sql))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        return $this->mysql->affected_rows;
    }

    public function inserLogSAM ($code, $name)
    {
        if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_log(code, name, ins_date) VALUES(?,?,?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("iss", $code, $name,date("Y-m-d H:i:s"))
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->insert_id;
    }


//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ЖУРНАЛОМ ПАРСИНГА  **/

    public function insertMainSAM ($date, $act)
    {
        if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_main_5(date, act) VALUES(?,?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("si", $date, $act)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->insert_id;
    }

    /**
     * Установить дату завершения парсинга
     * @param int $id ИД ЗАПУСКА ПАРСИНГА
     * @param $date_end ДАТА ОКОНЧАНИЯ ПАРСИНГА
     * @return int
     */
    public function updateDateEndMainSAM ($id, $date_end)
    {
        if (!($stmt = $this->mysql->prepare("UPDATE s_pars_main_5 SET act=1, date_end=? WHERE id=?"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("si", $date_end, $id)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->affected_rows;
    }


    /**
     * Возврачает количество потоков для ID парсинга
     * @param int $id
     * @return int
     */
    public function getThreadMainSAM ($id)
    {
        $res = $this->getTempQuery("SELECT thread FROM s_pars_main_5 WHERE id=$id", MYSQLI_ASSOC);
        return (int)$res[0]['thread'];
    }

    /**
     *  Уменьшает или увеличивает счётчик потоков
     * @param int $id
     * @param $operation
     * @return int
     */
    public function updateThreadMainSAM ($id, $operation)
    {
        if (!($stmt = $this->mysql->prepare("UPDATE s_pars_main_5 set thread=thread".$operation." 1 WHERE  id=?"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("i", $id)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->affected_rows;
    }

    /**
     *  Деактивирует все статусы
     * @param int $act
     * @return int
     */
    public function updateActMainSAM ($act)
    {
        if (!($stmt = $this->mysql->prepare("UPDATE s_pars_main_5 SET act=?"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("i", $act)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->affected_rows;
    }

//-------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С ЖУРНАЛОМ ПАРСИНГА**/


//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ЗАСАПУСКОМ ПАРСИНГА  **/

    public function getIdCategorySAM ($catId)
    {
        $res = $this->getTempQuery("SELECT id FROM s_pars_category_5 WHERE catId=$catId and act=1", MYSQLI_ASSOC);
        return (int)$res[0]['id'];
    }
//-------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С ЗАСАПУСКОМ ПАРСИНГА **/

//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ **/

    /**
     * Возвращает данные ввиде массива(ассоативного, числового или комбинированного) из БД взависимости от запроса
     * @param string $sql SQL запрос
     * @param integer $resType Тип MYSQLI_NUM, MYSQLI_ASSOC, MYSQLI_BOTH
     * @return mixed
     */
    public function getTempQuery ($sql, $resType)
    {
        $result = $this->mysql->query($sql);
        if (!$result) {
            $err = "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
            echo $err;
            Parse5Elem::logToFile('log.txt', $err . ' [' . $sql . ']');
        }
        while ($row = $result->fetch_array($resType)) {
            $rows[] = $row;
        }
        $result->close();
        return $rows;
    }

    /**
     * Проверка на существование категории в САМ, по её Id
     * @param $idCat
     * @return mixed
     */
    public function existCategorySAM ($catId)
    {
        if (!empty($catId)) {
            $res = $this->getTempQuery("SELECT exists(select catId FROM s_pars_category_5 WHERE catId=$catId) AS exist", MYSQLI_ASSOC);
            return (int)$res[0]['exist'];
        } else {
            return -1;
        };
    }

    /**
     * Выбрать все уникальные идентификаторы категорий, которые используются в 5 Элементе
     * @return mixed
     */
    public function getUniqueRootIdCategorySAM ($catId)
    {
        return $this->getTempQuery("SELECT DISTINCT catId FROM s_pars_category_5 WHERE catId>=$catId and rootId=1 AND act=1 ORDER BY catId ASC", MYSQLI_ASSOC);
    }

    /**
     * Выбрать все уникальные идентификаторы категорий, которые используются в 5 Элементе с оффсетом
     * @return mixed
     */
    public function getUniqueRootIdCategoryLimySAM ($offset)
    {
        return $this->getTempQuery("SELECT DISTINCT catId FROM s_pars_category_5 WHERE  rootId=1 AND act=1 ORDER BY catId ASC LIMIT 100 OFFSET $offset", MYSQLI_ASSOC);
    }

    /**
     * Выбрать все уникальные идентификаторы подкатегорий, которые используются в 5 Элементе
     * @return mixed
     */
    public function getUniqueCatIdCategorySAM ()
    {
        return $this->getTempQuery("SELECT DISTINCT catId FROM s_pars_category_5 ORDER BY catId ASC", MYSQLI_ASSOC);
    }

    /**
     * Выбрать все подкатегории, у которых не заполнены значение главной категории
     * @return mixed
     */
    public function getEmptyRootIdCategorySAM ()
    {
        return $this->getTempQuery("SELECT DISTINCT catId FROM s_pars_category_5 WHERE rootId=-1 ORDER BY catId ASC", MYSQLI_ASSOC);
    }


    /**
     * Вставить новую запись в классификатор категорий
     * @param $name , наименование
     * @param $catId , id в 5 элемент
     * @param $rootId , главная категория в 5 элемент
     * @param $act , актуальность
     */
    public function insertCategorySAM ($name, $catId, $rootId, $act)
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
    public function updateRootId ($catId, $rootId)
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

    /**
     * Деактивирует все категории
     * @param integer $catId Подкатегория
     * @param integer $rootId Главная категория
     */
    public function updateAct ()
    {
        if (!($stmt = $this->mysql->prepare("UPDATE s_pars_category_5 SET act=0"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->affected_rows;
    }
//-------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С КАТЕГОРИЯМИ **/

//-------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ПРОДУКТАМИ **/

    /**
     *  Проверка на существование ID продукта в 5 Элементе
     * @param int $creditId
     * @return int
     */
    public function existProductSAM ($prodId)
    {
        if (!empty($prodId)) {
            $res = $this->getTempQuery("SELECT exists(select prodId FROM s_pars_product_5 WHERE prodId=$prodId) AS exist", MYSQLI_ASSOC);
            return (int)$res[0]['exist'];
        } else {
            return -1;
        }
    }

    public function getIdFromProdIdProductSAM ($prodId)
    {
        if (!empty($prodId)) {
            $res = $this->getTempQuery("SELECT id FROM s_pars_product_5 WHERE prodId=$prodId", MYSQLI_ASSOC);
            return (int)$res[0]['id'];
        } else return -1;
    }

    /**
     * Вставить новую запись в классификатор категорий
     * @param $name , наименование
     * @param $catId , id в 5 элемент
     * @param $rootId , главная категория в 5 элемент
     * @param $act , актуальность
     */
    public function insertProductSAM ($category_id, $prodId, $name, $cod)
    {
        if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_product_5 (category_id, prodId, name, cod) VALUES(?,?,?,?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("iisi", $category_id, $prodId, $name, $cod)
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
    public function getUniqueCodProductSAM ()
    {
        return $this->getTempQuery("SELECT DISTINCT prodId FROM s_pars_product_5  ORDER BY prodId ASC", MYSQLI_ASSOC);
    }



//------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С ПРОДУКТАМИ **/


//------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ОПЛАТОЙ **/

    /**
     *  Проверка на существование ID кредита в 5 Элементе
     * @param int $creditId
     * @return int
     */
    public function existOplataSAM ($creditId)
    {
        if (!empty($creditId)) {
            $res = $this->getTempQuery("SELECT exists(select creditId FROM s_pars_oplata_5 WHERE creditId=$creditId) AS exist", MYSQLI_ASSOC);
            return (int)$res[0]['exist'];
        } else {
            return -1;
        }
    }

    /**
     * Выбрать id по ID кредита в 5 Элементе
     * @param int $creditId , Id кредита в 5 Элементе
     * @return mixed
     */
    public function getIdFromCreditIdOplataSAM ($creditId)
    {
        if (!empty($creditId)) {
            $res = $this->getTempQuery("SELECT id FROM s_pars_oplata_5 WHERE creditId=$creditId", MYSQLI_ASSOC);
            return (int)$res[0]['id'];
        } else {
            return -1;
        }
    }


    /**
     * Вставить новую запись в классификатор оплат
     * @param $creditId , id в 5 элемент
     * @param $name , наименование
     */
    public
    function insertOplataSAM ($creditId, $name)
    {
        if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_oplata_5 (creditId, name) VALUES(?,?)"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("is", $creditId, $name)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->insert_id;

    }

//------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С ОПЛАТОЙ **/


//------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ЦЕНОЙ **/

    /**
     *  Находит последнюю вставленную позицию по цене
     * @return mixed
     */
    public function getLastRecordCenaSAM ()
    {
        $res = $this->getTempQuery("SELECT
                                              c.main_id,
                                              cat.catId
                                            FROM s_pars_cena_5 c, s_pars_product_5 p, s_pars_category_5 cat
                                            WHERE cat.id = p.category_id
                                                  AND p.id = c.product_id AND cat.rootId = 1 AND cat.act = 1
                                            ORDER BY c.id DESC
                                            LIMIT 1", MYSQLI_ASSOC);
        return $res[0];
    }

    /**
     *  Находит ИД цены для удаления определённой категории для
     * @param int $main_id ИД запуска скрипта
     * @param int $catId ИД категории
     * @return mixed
     */
    public function getRecordsForDeleteCenaSAM ($main_id, $catId)
    {
        $res = $this->getTempQuery("SELECT c.id
                                            FROM s_pars_product_5 p, s_pars_category_5 cat, s_pars_cena_5 c, s_pars_main_5 m
                                            WHERE p.category_id = cat.id AND c.product_id = p.id AND c.main_id = m.id AND m.id = $main_id AND m.date_end IS NULL AND cat.catId = $catId", MYSQLI_ASSOC);
        return $res;
    }

    public function insertCenaSAM ($productId, $cena, $oplata_id, $main_id)
    {
        if ($this->existCenaSAM($productId, $main_id) == 0) {
            if (!($stmt = $this->mysql->prepare("INSERT INTO s_pars_cena_5 (product_id,cena,oplata_id,main_id) VALUES(?,?,?,?)"))
            ) {
                echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
            }
            if (!$stmt->bind_param("idii", $productId, $cena, $oplata_id, $main_id)
            ) {
                echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
            }
            if (!$stmt->execute()) {
                echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
            }
            return $stmt->insert_id;
        }

    }

    public function existCenaSAM ($productId, $main_id)
    {
        if (!empty($productId)) {
            $res = $this->getTempQuery("SELECT exists(select Id FROM s_pars_cena_5 WHERE product_id=$productId and main_id=$main_id) AS exist", MYSQLI_ASSOC);
            return (int)$res[0]['exist'];
        } else {
            return -1;
        }
    }

    /**
     *  Удаляет сену по ID
     * @param int $id ID для удаления
     * @return int
     */
    public function deleteCenaSAM ($id)
    {
        if (!($stmt = $this->mysql->prepare("DELETE FROM  s_pars_cena_5 WHERE id=?"))
        ) {
            echo "Не удалось подготовить запрос: (" . $this->mysql->errno . ") " . $this->mysql->error;
        }
        if (!$stmt->bind_param("i", $id)
        ) {
            echo "Не удалось привязать параметры: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Не удалось выполнить запрос: (" . $stmt->errno . ") " . $stmt->error;
        }
        return $stmt->affected_rows;
    }


//------------ОКОНЧАНИЕ->ФУНКЦИИ ДЛЯ РАБОТЫ С ЦЕНОЙ **/

//------------НАЧАЛО->ФУНКЦИИ ДЛЯ РАБОТЫ С ТЕСТОВЫЕ ФУНКЦИИ **/

    public function getCurrentParsingSAM ()
    {
        $res = $this->getTempQuery("SELECT 
                                            m.id, m.date, m.date_end, count(*) cnt ,m.act,m.thread,current_timestamp time_serv
                                        FROM
                                            s_pars_main_5 m,
                                            s_pars_category_5 c,
                                            s_pars_product_5 p,
                                            s_pars_cena_5 cn,
                                            s_pars_oplata_5 o
                                        WHERE
                                            m.id = cn.main_id
                                                AND c.id = p.category_id
                                                AND cn.product_id = p.id
                                                AND cn.oplata_id = o.id
                                                AND cn.main_id IN (SELECT id FROM s_pars_main_5 where m.act in (0,1,2) )
                                                GROUP BY  m.id, m.date, m.date_end,m.act,m.thread
                                                ORDER BY m.id DESC
                                                ", MYSQLI_ASSOC);
        return $res;
    }

//------------ОКОНЧАНИ->ФУНКЦИИ ДЛЯ РАБОТЫ С ТЕСТОВЫЕ ФУНКЦИИ **/
}

/*
$m = new MySqlDB();
$m->updateThreadMainSAM(63,'+');
echo $m->getThreadMainSAM(63);
*/

//echo $m->getMaxId('s_pars_main');
//var_dump($m->existCategorySAM(143));
//var_dump($m->tempQuery('SELECT * FROM s_pars_category_5',MYSQLI_NUM));
//var_dump($m->getUniqueCatIdCategorySAM());
//var_dump($m->getUniqueCatIdCategorySAM());
//echo $m->insertCategorySAM('Телик2', 2, 1, 1);
//var_dump($m->getCategories());