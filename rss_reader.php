<?php
const RSS_URL = "http://p1.local/rss.xml";
const RSS_NAME = "news.xml";

/**
 * @param $url путь где лежит rss.xml
 * @param $filename путь куда сохранить копию rss.xml
 */
function download($url,$filename){
    $file = file_get_contents($url);
    if ($file)
        file_put_contents($filename,$file);
}

if (!file_exists(RSS_NAME)){
    download(RSS_URL,RSS_NAME);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <title>Новостная лента</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<h1>Последние новости</h1>
<?php
    $sxml = simplexml_load_file(RSS_NAME);
    foreach ($sxml->channel->item as $item) {
        echo <<<RSS_NAME
        <h3>{$item->title}</h3>
        <p>
            <strong>Категория: {$item->category}</strong><br>
            {$item->description}<br>
            <a href="{%item->link}">Читать дальше...></a>
        </p>
RSS_NAME;
    }//foreach
    if (time() > filemtime(RSS_NAME) + 3600){
        download(RSS_URL,RSS_URL);
    }

?>
</body>
</html>