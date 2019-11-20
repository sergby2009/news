<?php
 $errMsg = '';
 require_once('NewsDB.class.php');

 $news = new NewsDB();

if ($_SERVER['REQUEST_METHOD'] == 'POST') include_once('save_news.inc.php');
?>

<!doctype html>
<html lang="ru">
<head>
	<title>Новостная лента</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<h1>Последние новости</h1>

<?php if (!empty($errMsg)) echo 'Ошибка:', $errMsg; ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

Заголовок новости:<br />
<input type="text" name="title" width="100%"/><br />
Выберите категорию:<br />
<select name="category">
<option value="1">Политика</option>
<option value="2">Культура</option>
<option value="3">Спорт</option>
</select>
<br />
Текст новости:<br />
<textarea name="description" cols="50" rows="5"></textarea><br />
Источник:<br />
<input type="text" name="source" /><br />
<br />
<input type="submit" value="Добавить!" />

</form>

<?php
include_once('get_news.inc.php');
?>

</body>
</html>