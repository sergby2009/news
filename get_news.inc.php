<?php
    if ($artics = $news->getNews()){
        echo "<table border='1'>
                <caption>Новостей:" .count($artics). ".</caption>
                <tr>
                    <th>Заголовок</th>
                    <th>Дата</th>
                    <th>Описание</th>
                    <th>Источник</th>
                    <th>Удаление</th>
                </tr>";
            foreach ($artics as $article) {
                echo "<tr>";
                echo "<td>" . $article['title'] . "</td>";
                echo "<td>" . $article['datetime'] . "</td>";
                echo "<td>" . $article['description']."</td>";
                echo "<td><a href=\"" .$article['source']."\">".$article['source']."</a></td>";
                echo "<td><a href=\"" . $_SERVER['REQUEST_URI']."?del=".$article['id']."\">Удалить</a></td>";
                echo "</tr>";
            };// foreach
        echo "</table>";
    }else{
        $errMsg = "Произошла ошибка при выводе новостной ленты.";
    }
?>