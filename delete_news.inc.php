<?php
if(is_integer($_GET['del']*1) && $_GET['del'] > 0){
    if ($news->deleteNews($_GET['del'])){
       header("Location: {$_SERVER['PHP_SELF']}");
   }else{
       $errMsg = "Ошибка при удалении новости.";
   }
 }else{
     $errMsg = "Неверный параметр.";
 }
?>