<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<?php
    //header("Content-Type : text/html; charset = utf8");
    $output = array();
    // Основная функция
    function makeRequest($request_xml, &$output){
        // Запрос данных с использованием XML-RPC
        $param = array(
            'http' => array(
                'method' => "POST",
                'header' => "User-Agent: PHPRPC/1.0\r\n".
                    "Content-Type: text/xml \r\n".
    //                          "Authorization: Basic " . base64_encode("$https_user:$https_password"). "\r\n",
                    "Content-length: " . strlen($request_xml) . "\r\n",
                'content'=>"$request_xml"
    //              'timeout'=>"60"
            )
        );

        $context = stream_context_create($param);
        $retVal = file_get_contents("http://php1.local/xml-rpc-server.php", false, $context);
        // Обработка результата XML-RPC запроса
        $result = xmlrpc_decode($retVal);
        if (is_array($result) && xmlrpc_is_fault($result)){
            $output = $result;
        }else{
            $output = unserialize(base64_decode($result));
        }
    }//makeRequest

    $id = 6;
    // Подготовка XML-RPC запроса
    $xml_rpc_request = xmlrpc_encode_request('getNewsByID', array($id));
    // Запрос
    makeRequest($xml_rpc_request, $output);
    // Вывод результатов
    echo "Finish!!!";
    var_dump($output);
?>
</body>
</html>
