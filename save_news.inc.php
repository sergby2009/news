<?php
    if (isset($_POST['title']) && isset($_POST['category']) && isset($_POST['description']) && isset($_POST['source'])
        && $_POST['title'] != '' && $_POST['description'] != '' && $_POST['source'] != ''){
        if ($news->saveNews($_POST['title'],$_POST['category'],$_POST['description'],$_POST['source'])){
            header("Location: {$_SERVER['REQUEST_URI']}");
        }else{
            $errMsg = "Произошла ошибка при сохранении новости.";
        }
            ;
    }else{
        $errMsg = "Заполните все поля!";
    }
?>