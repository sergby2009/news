<?php

require_once('db.inc.php');
require_once('INewsDB.class.php');

/**
 * Class NewsDB
 */
class NewsDB implements INewsDB
{

    private static $sql_DropDatabase = "DROP DATABASE " . SQL_DB_NAME;
    private static $sql_CreateDatabase = "CREATE DATABASE " . SQL_DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci";


    private static $sql_CreateTableCategory = "CREATE TABLE category (
                                              id int(11) NOT NULL AUTO_INCREMENT,
                                              name varchar(255) DEFAULT NULL,
                                              PRIMARY KEY (id)
                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    private static $sql_CreateTableMsgs =    "CREATE TABLE msgs (
                                              id int(11) NOT NULL AUTO_INCREMENT,
                                              title varchar(500) DEFAULT NULL,
                                              category int(11) NOT NULL,
                                              description text,
                                              source varchar(500) DEFAULT NULL,
                                              datetime date DEFAULT NULL,
                                              PRIMARY KEY (id)
                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    private static $sql_InsertTableCategoryData1 = "INSERT INTO category (name) VALUES ('Политика')";
    private static $sql_InsertTableCategoryData2 = "INSERT INTO category (name) VALUES ('Культура')";
    private static $sql_InsertTableCategoryData3 = "INSERT INTO category (name) VALUES ('Спорт')";

    private $_db;

    /**
     * Подключение к БД (в случае отсутствия создание новой базы данных)
     * @param bool $recreate - принудительное пересоздание базы данных при true
     */

    function __construct($recreate = false)
    {
        $this->_db = new mysqli(SQL_SERVER_HOST, SQL_LOGIN, SQL_PASSWORD);

        if ($this->_db->connect_error) {
            die('Соединение с СУБД завершилось с ошибкой:' . $this->_db->connect_error);
        }

        if ($recreate) {
            if (!$this->reCreateDB(SQL_DB_NAME)) {
                die('Пересоздание базы данных завершилось с ошибкой.');
            }
        } elseif (!$this->_db->select_db(SQL_DB_NAME)) {
            if (!$this->createDB(SQL_DB_NAME)) {
                die('Создание базы данных завершилась с ошибкой.');
            }
        }
    }

    /**
     * @param $dbName
     * @return bool
     */
    private function createDB($dbName)
    {
        $all_query_ok = TRUE; // контроль удачного завершения запроса

        $this->_db->autocommit(FALSE); // отключение autocommit'a

        $this->_db->query(self::$sql_CreateDatabase) ? $this->_db->select_db($dbName) : $all_query_ok = FALSE;

        if (!$this->createTables()) $all_query_ok = FALSE;

        $all_query_ok ? $this->_db->commit() : $this->_db->rollback();

        $this->_db->autocommit(TRUE); // включение autocommit'a

        return $all_query_ok ? $this->_db->select_db($dbName): FALSE;
    }

    private function createTables()
    {
        $all_query_ok = TRUE; // контроль удачного завершения запроса

        // Создание таблицы category
        $this->_db->query(self::$sql_CreateTableCategory) ? null : $all_query_ok = FALSE;

        // Создание таблицы msgs
        $this->_db->query(self::$sql_CreateTableMsgs) ? null : $all_query_ok = FALSE;
        // Заполнение таблицы category данными
        $this->_db->query(self::$sql_InsertTableCategoryData1) ? null : $all_query_ok = FALSE;
        $this->_db->query(self::$sql_InsertTableCategoryData2) ? null : $all_query_ok = FALSE;
        $this->_db->query(self::$sql_InsertTableCategoryData3) ? null : $all_query_ok = FALSE;

        return $all_query_ok;
    }

    /**
     * Пересоздание базы данных
     * Возвращает невозможности возвращает false
     *
     * @param $dbName - имя базы данных
     *
     * @return bool
     */
    private function reCreateDB($dbName)
    {
        $all_query_ok = TRUE; // контроль удачного завершения запроса

        $this->_db->autocommit(FALSE); // отключение autocommit'a

        $this->_db->query(self::$sql_DropDatabase) ? null : $all_query_ok = FALSE;
        $this->_db->query(self::$sql_CreateDatabase) ? $this->_db->select_db($dbName) : $all_query_ok = FALSE;

        if (!$this->createTables()) $all_query_ok = FALSE;
        $all_query_ok ? $this->_db->commit() : $this->_db->rollback();

        $this->_db->autocommit(TRUE); // включение autocommit'a

        return $all_query_ok ? $this->_db->select_db($dbName) : FALSE;
    }

    /**
     *    Добавление новой записи в новостную ленту
     *
     * @param string $title - заголовок новости
     * @param string $category - категория новости
     * @param string $description - текст новости
     * @param string $source - источник новости
     *
     * @return boolean - результат успех/ошибка
     */

    function saveNews($title, $category, $description, $source)
    {
        $title = $this->makeStringDateToDB($title);
        $category = $this->makeIntegerDateToDB($category);
        $description = $this->makeStringDateToDB($description);
        $source = $this->makeStringDateToDB($source);
        $dt = date('Y-m-d');
        $stmt =  $this->_db->stmt_init();
        $sql = 'INSERT INTO msgs (title,category,description,source,datetime) VALUES (?,?,?,?,?)';
        if ($stmt->prepare($sql)){
            $stmt->bind_param("sisss", $title, $category, $description, $source, $dt);
            if (!$stmt->execute()){
//              echo "Отсутствует возможность сохранить эти данные.";
                $errMsg = "Отсутствует возможность сохранить эти данные.";
            }
            $stmt->close();
        }
    }

    /**
     *    Выборка всех записей из новостной ленты
     *
     * @return array - результат выборки в виде массива
     */

    function getNews()
    {
        $array = [];
        $sql = 'SELECT title,category,description,source,datetime FROM msgs';
        $result = mysqli_query($this->_db, $sql);
        while ($row = mysqli_fetch_assoc($result)){
            $array[]=$row;
        }
        print_r($array);
        return $array;
    }

    /**
     * Удаление записи из новостной ленты
     *
     * @param integer $id - идентификатор удаляемой записи
     *
     * @return boolean - результат успех/ошибка
     */

    function deleteNews($id)
    {
        $stmt =  $this->_db->stmt_init();
        $sql = 'DELETE FROM msgs WHERE msgs.id = ?';
        if ($stmt->prepare($sql)){
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()){
                echo "Отсутствует возможность удалить эту запись.";
            }
            $stmt->close();
        }
    }

    /**
     * Деструктор объекта
     */
    function __destruct()
    {
        unset($this->_db);
    }

    /**
     * @param $data
     * @return mixed
     */
    private function makeStringDateToDB($data)
    {
       $data = trim(strip_tags((string)$data));
       return $this->_db->real_escape_string($data);
    }

    /**
     * @param $data
     */
    private function makeIntegerDateToDB($data)
    {
        return $data * 1;
    }
}

/*
$obj = new NewsDB();
//$obj->saveNews('название',1,'описание','основной текст');
//$obj->saveNews('название',1,'описание','основной текст');
//$obj->saveNews('название',1,'описание','основной текст');
$obj->deleteNews(2);
$obj->getNews();
*/
?>