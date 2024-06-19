<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

$chat = mysql_fetch_assoc(mysql_query("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));

if (!isset($_GET['id']) && !is_numeric($_GET['id']) && $user['id'] != $chat['id_avtor'] || !isset($_GET['id']) && !is_numeric($_GET['id']) && $user['level'] < 3){header("Location: index.php?".SID);exit;}
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1",$db), 0)==0){header("Location: index.php?".SID);exit;}
$privat = mysql_fetch_assoc(mysql_query("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));



if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['ok']))
{
$name=esc($_POST['name'],1);
$password=esc($_POST['password'],1);


if (strlen2($name)>64){$err='Слишком большой заголовок комнаты';}
if (strlen2($name)<3){$err='Короткий заголовок';}
$mat=antimat($name);
if ($mat)$err[]='В заголовке обнаружен мат: '.$mat;

if (strlen2($password)>26){$err='Пароль слишком большой';}
if (strlen2($password)<2){$err='Пароль слишком короткий';}


$name=my_esc($_POST['name']);
$password=my_esc($_POST['password']);
if (!isset($err)){


mysql_query("UPDATE `privat_room` SET `name` = '$name', `password` = '$password' WHERE `id` = '$privat[id]' LIMIT 1");
$_SESSION['message'] = 'Изменения успешно приняты';
header("Location: room.php?id_room=$privat[id]");
exit;
}
}

$set['title']='Редактирование комнаты';
include_once '../sys/inc/thead.php';
title();
err();
aut(); // форма авторизации




echo "<form class='mess' method=\"post\" name='message' action=\"?id=$privat[id]\">\n";
echo "Название комнаты:<br />\n<input name=\"name\" size=\"16\" maxlength=\"32\" value=\"".htmlspecialchars($privat['name'])."\" type=\"text\" /><br />\n";
echo "Пароль:<br />\n<input name=\"password\" size=\"16\" maxlength=\"26\" value=\"".htmlspecialchars($privat['password'])."\" type=\"text\" /><br />\n";


echo "<input value=\"Готово\" type=\"submit\" name=\"ok\"/>\n";
echo "</form>\n";


echo'<div class="foot">';
echo "<img src='/style/icons/str.gif' alt='*'> <a href='index.php'>Комнаты</a> | <a href='room.php?id_room=$privat[id]'>".htmlspecialchars($privat['name'])."</a><br />\n";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>