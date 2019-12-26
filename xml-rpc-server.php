<?php

error_reporting(0);
require "NewsDB.class.php";

class XmlRpcNewsService extends NewsDB{

	protected function db2Arr(mysqli_result $data){
		$arr = array();
		while($row = $data->fetch_array(MYSQLI_ASSOC))
			$arr[] = $row;
		return $arr;
	}
	/* Метод возвращает новость по её идентификатору */
	function getNewsById($id){
		try{
			$sql = "SELECT msgs.id as id, msgs.title as title,category.name as category ,msgs.description as description,msgs.source as source, DATE_FORMAT(msgs.datetime, \" %d.%m.%Y \") as datetime
                    FROM msgs LEFT JOIN category ON(category.id = msgs.category)
                    WHERE msgs.id = " . $this->makeIntegerDateToDB($id);
			$result = mysqli_query($this->_db, $sql);
			if (!is_object($result)){
				throw new Exception("Ошибка:" . mysqli_error($this->_db));
			}
			return $this->db2Arr($result);
		}catch(Exception $e){
			return $e->getMessage();
		}
	}//getNewsById
	function xmlRpcGetNewsById($method_name, $args, $extra) {
		if (!is_array($args) || count($args) <> 1)
			return array('faultCode'=>-3, 'faultString'=>'Неверное количество параметров!');
		$id = $args[0];
		$result = $this->getNewsById($id);
		if(!is_array($result))
			return array('faultCode'=>-2, 'faultString'=>"Ошибка: $result!");
		elseif(empty($result))	
			return array('faultCode'=>-1, 'faultString'=>"Новость с идентификатором $id отсутствует!");
		else
			return base64_encode(serialize($result));
		}//xmlRpcGetNewsById
}//class XmlRpcNewsService

// Чтение запроса из потока
$xml_request = file_get_contents("php://input");
// Создание XML-RPC сервера
$xmlrpc_server = xmlrpc_server_create();
// Регистрация метода класс
xmlrpc_server_register_method($xmlrpc_server, "getNewsByID", array(new XmlRpcNewsService, "xmlRpcGetNewsById"));


// Отдаем правильный заголовок
header("Content-Type: text/xml; charset = utf8");
// Отдаем результат запроса
print xmlrpc_server_call_method($xmlrpc_server, $xml_request, null);
?>