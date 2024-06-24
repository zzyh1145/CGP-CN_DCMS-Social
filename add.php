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

only_reg();

if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['ok']))
{

$name=esc($_POST['name'],1);
$password=esc($_POST['password'],1);

if (strlen2($name)>56){$err='Слишком большое название комнаты';}
if (strlen2($name)<3){$err='Короткое название комнаты';}
$mat=antimat($name);
if ($mat)$err[]='В названии комнаты обнаружен мат: '.$mat;

if (strlen2($password)>16){$err='Пароль должен состоять не больше чем из 16 символов';}
if (strlen2($password)<3){$err='Пароль слишком короткий';}

$name=my_esc($_POST['name']);
$password=my_esc($_POST['password']);

if (!isset($err)){








mysql_query("INSERT INTO `privat_room` (`id`, `id_user`, `name`, `password`, `id_avtor`) values('', '".$user['id']."', '$name', '$password', '".$user['id']."')");
$id_room = mysql_insert_id();
//mysql_query("INSERT INTO `privat_chat` (`id`, `id_user`, `msg`, `time` `id_room`) values('', '0', 'Комната успешно создана', '$time', '".$id_room."')");



}





$_SESSION['message'] = 'Комната успешно создана, не забудьте пароль: '.htmlspecialchars($password);
header("Location: room.php?id_room=".$id_room."");
exit;


}





$set['title']='Создание комнаты';
include_once '../sys/inc/thead.php';


title();


err();


aut(); // форма авторизации














echo "<form class='mess' method=\"post\" name='message' action=\"?\">\n";


echo "Название комнаты:<br />\n<input name=\"name\" size=\"16\" maxlength=\"56\" type=\"text\" /><br />\n";
echo "Пароль:<br />\n<input name=\"password\" size=\"16\" maxlength=\"16\" type=\"text\" /><br />\n";






echo "<input value=\"Создать\" type=\"submit\" name=\"ok\"/>\n";
echo "</form>\n";








echo'<div class="foot">';


echo "<img src='/style/icons/str.gif' alt='*'> <a href='index.php'>Приватные комнаты</a><br />\n";
echo "</div>";


include_once '../sys/inc/tfoot.php';


?>
