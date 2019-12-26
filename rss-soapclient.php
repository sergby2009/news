<?php
try{
    $options = ['soap_version' => SOAP_1_2,
        "trace"        => 1,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'classmap'     => ['']];
    $client = new SoapClient("http://php1.local/news.wsdl", $options);
    $client->__setCookie('XDEBUG_SESSION', 'PHPSTORM');

//  Количество новостей в базе
    $result = $client->getNewsCount();
    echo "<p>Всего новостей: {$result}</p>";
//  Количество новостей в категории политика
    $result = $client->getNewsCountByCat(1);
    echo "<p>Всего новостей в категории Политика: {$result}</p>";
//  Вывод новости по ID
    $result = $client->getNewsByID(6);
    $result = unserialize(base64_decode($result));
    var_dump($result);
} catch (SoapFault $exception) {
    echo "Операция {$exception->faultcode} вернула ошибку: {$exception->getMessage()}";
}//try