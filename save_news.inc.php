<?php
    if (isset($_POST['title']) && isset($_POST['category']) && isset($_POST['description']) && isset($_POST['source'])){
        $news->saveNews($_POST['title'],$_POST['category'],$_POST['description'],$_POST['source']);
    }else{
        $errMsg = "Заполните все поля!";
    }
?>