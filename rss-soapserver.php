<?php
require "NewsDB.class.php";
class SOAPNewsDB extends NewsDB
{
    protected function db2Arr(mysqli_result $data){
        $arr = array();
        while($row = $data->fetch_array(MYSQLI_ASSOC))
            $arr[] = $row;
        return $arr;
    }

    /* Метод возвращает новость по её идентификатору */
    function getNewsByID($id){
        try{
            $sql = "SELECT msgs.id as id, msgs.title as title,category.name as category ,msgs.description as description,msgs.source as source, DATE_FORMAT(msgs.datetime, \" %d.%m.%Y \") as datetime
                    FROM msgs LEFT JOIN category ON(category.id = msgs.category)
                    WHERE msgs.id = " . $this->makeIntegerDateToDB($id);
            $result = mysqli_query($this->_db, $sql);
            if (!is_object($result)){
                throw new Exception("Ошибка:" . mysqli_error($this->_db));
            }
            return base64_encode(serialize($this->db2Arr($result)));
        }catch (Exception $e){
            throw new SoapFault("Ошибка при запросе новости", $e->getMessage());
        }
    }//getNewsByID

    /* Метод считает количество всех новостей */
    function getNewsCount(){
        try{
            $sql = "SELECT count(*) FROM msgs";
            $result = mysqli_query($this->_db,$sql);
            if (!is_object($result))
                throw new Exception("Ошибка:" . mysqli_error($this->_db));
            return $result;
        }catch(Exception $e){
            throw new SoapFault('Ошибка при запросе количества новостей', $e->getMessage());
        }
    }//getNewsCount

    /* Метод считает количество новостей в указанной категории */
    function getNewsCountByCat($cat_id){
        try{
            $sql = "SELECT count(*) FROM msgs WHERE category = " . $this->makeIntegerDateToDB($cat_id);
            $result = mysqli_query($this->_db,$sql);
            if (!is_object($result))
                throw new Exception("Ошибка:" . mysqli_error($this->_db));
            return $result;
        }catch(Exception $e){
            throw new SoapFault('Ошибка при запросе количества новостей в категории', $e->getMessage());
        }
    }//getNewsCountByCat
}

header("Content-Type: text/xml; charset=utf-8");
header("Cache-Control: no-store, no-cache");
header("Expires:".date('r'));

ini_set("soap.wsdl_cache_enabled","0");

$server = new SoapServer("http://php1.local/news.wsdl");
$server->setClass('SOAPNewsDB');
$server->handle();




