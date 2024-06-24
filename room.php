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

if (!isset($_GET['id_room']) && !is_numeric($_GET['id_room'])){header("Location: index.php?".SID);exit;} elseif (@$_SESSION['room_pass']==NULL) @$_SESSION['room_pass'] = htmlspecialchars($_POST['password']);
if (mysql_result(mysql_query("SELECT COUNT(*) FROM `privat_room` WHERE `id` = '".intval($_GET['id_room'])."' AND `password` = '".my_esc(htmlspecialchars(@$_SESSION['room_pass']))."' LIMIT 1",$db), 0)==0){header("Location: index.php?".SID);exit;}
$chat = mysql_fetch_assoc(mysql_query("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id_room'])."' LIMIT 1"));

/*


================================
用户举报模块与消息
================================

*/




if (isset($_GET['spam'])  && isset($user))




{




$mess = mysql_fetch_assoc(mysql_query("SELECT * FROM `news_komm` WHERE `id` = '".intval($_GET['spam'])."' limit 1"));




$spamer = get_user($mess['id_user']);




if (mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'news' AND `spam` = '".$mess['msg']."'"),0)==0)




{




if (isset($_POST['msg']))




{




if ($mess['id_user']!=$user['id'])




{




$msg=mysql_real_escape_string($_POST['msg']);









if (strlen2($msg)<3)$err='详细说明举报原因';




if (strlen2($msg)>1512)$err='文本长度超过的






512字数限制';









if(isset($_POST['types'])) $types=intval($_POST['types']);




else $types='0'; 




if (!isset($err))




{




mysql_query("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$chat[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'news', '".my_esc($mess['msg'])."')");
$_SESSION['message'] = 'Заявка на рассмотрение отправлена'; 




header("Location: ?id=$chat[id]&spam=$mess[id]&page=".intval($_GET['page'])."");
exit;




}




}




}




}









include_once '../sys/inc/thead.php';




title();




aut();




err();









if (mysql_result(mysql_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'news'"),0)==0)




{




echo "<div class='mess'>Ложная информация может привести к блокировке ника. 




Если вас постоянно достает один человек - пишет всякие гадости, вы можете добавить его в черный список.</div>";




echo "<form class='nav1' method='post' action='?id=$chat[id]&amp;spam=$mess[id]&amp;page=".intval($_GET['page'])."'>\n";
echo "<b>Пользователь:</b> ";




echo " ".status($spamer['id'])."  ".group($spamer['id'])." <a href=\"/info.php?id=$spamer[id]\">$spamer[nick]</a>\n";




echo "".medal($spamer['id'])." ".online($spamer['id'])." (".vremja($mess['time']).")<br />";




echo "<b>Нарушение:</b> <font color='green'>".output_text($mess['msg'])."</font><br />";




echo "Причина:<br />\n<select name='types'>\n";




echo "<option value='1' selected='selected'>Спам/Реклама</option>\n";




echo "<option value='2' selected='selected'>Мошенничество</option>\n";




echo "<option value='3' selected='selected'>Оскорбление</option>\n";




echo "<option value='0' selected='selected'>Другое</option>\n";




echo "</select><br />\n";




echo "Комментарий:$tPanel";




echo "<textarea name=\"msg\"></textarea><br />";




echo "<input value=\"Отправить\" type=\"submit\" />\n";




echo "</form>\n";




}else{




echo "<div class='mess'>Жалоба на <font color='green'>$spamer[nick]</font> будет рассмотрена в ближайшее время.</div>";




}









echo "<div class='foot'>\n";




echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id=$news[id]&amp;page=".intval($_GET['page'])."'>Назад</a><br />\n";




echo "</div>\n";




include_once '../sys/inc/tfoot.php';




exit;




}




/*




==================================




The End




==================================




*/




if (isset($user))




mysql_query("UPDATE `notification` SET `read` = '1' WHERE `type` = 'news_komm' AND `id_user` = '$user[id]' AND `id_object` = '$chat[id]'");










if (isset($_POST['msg']) && isset($user))




{




$msg=$_POST['msg'];




if (isset($_POST['translit']) && $_POST['translit']==1)$msg=translit($msg);









$mat=antimat($msg);




if ($mat)$err[]='В тексте сообщения обнаружен мат: '.$mat;









if (strlen2($msg)>1024){$err='Сообщение слишком длинное';}




elseif (strlen2($msg)<2){$err='Короткое сообщение';}




elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `privat_chat` WHERE `id_room` = '".intval($_GET['id_room'])."' AND `id_user` = '$user[id]' AND `msg` = '".my_esc($msg)."' LIMIT 1"),0)!=0){$err='Ваше сообщение повторяет предыдущее';}
elseif(!isset($err)){




mysql_query("INSERT INTO `privat_chat` (`id_user`, `time`, `msg`, `id_room`) values('$user[id]', '$time', '".my_esc($msg)."', '".intval($_GET['id_room'])."')");
mysql_query("UPDATE `user` SET `balls` = '".($user['balls']+1)."', `rating_tmp` = '".($user['rating_tmp']+1)."'  WHERE `id` = '$user[id]' LIMIT 1");




		/*




		==========================




		Уведомления об ответах




		==========================




		if (isset($user) && $respons==TRUE){




		$notifiacation=mysql_fetch_assoc(mysql_query("SELECT * FROM `notification_set` WHERE `id_user` = '".$ank_otv['id']."' LIMIT 1"));




			




			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])




			mysql_query("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$chat[id]', 'news_komm', '$time')");
		




		}









*/





//$_SESSION['message'] = 'good';
header('Location: ?id_room='.intval($_GET['id_room']).'&page='.intval($_GET['page']).'');
exit;




}




}




$set['title']='Комната - '.htmlspecialchars($chat['name']);
include_once '../sys/inc/thead.php';




title();




err();




aut(); // форма авторизации
























if ($user['level'] >= 3 || $user['id'] == $chat['id_avtor'])
{




echo'<div class="c2">';




echo '[<img src="/style/icons/edit.gif" alt="*"> <a href="edit.php?id='.$chat['id'].'">ред</a>] ';
echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="delete.php?id_room='.$chat['id'].'">удл</a>] ';
echo "</div>";




}



















$k_post=mysql_result(mysql_query("SELECT COUNT(*) FROM `privat_chat` WHERE `id_room` = '".intval($_GET['id_room'])."' "),0);
$k_page=k_page($k_post,$set['p_str']);




$page=page($k_page);




$start=$set['p_str']*$page-$set['p_str'];




$q=mysql_query("SELECT * FROM `privat_chat` WHERE `id_room` = '".intval($_GET['id_room'])."' ORDER BY `id` $sort LIMIT $start, $set[p_str]");
echo "<table class='post'>\n";









if ($k_post==0)




{




echo "<div class='mess'>";




echo "Нет сообщений\n";




echo '</div>';




}














while ($post = mysql_fetch_assoc($q))




{




$ank=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));




/*-----------зебра-----------*/ 




if ($num==0){




echo '<div class="nav1">';




$num=1;




}




elseif ($num==1){




echo '<div class="nav2">';




$num=0;}




/*---------------------------*/









echo " ".group($ank['id'])." <a href='/info.php?id=$ank[id]'>$ank[nick]</a>\n";




echo "".medal($ank['id'])." ".online($ank['id'])." (".vremja($post['time']).")<br />";




/*------------Вывод статуса-------------*/




$status=mysql_fetch_assoc(mysql_query("SELECT * FROM `status` WHERE `pokaz` = '1' AND `id_user` = '$ank[id]' LIMIT 1"));




if ($status['msg'] && $set['st']==1)




{




echo "<div class='st_1'></div>";




echo "<div class='st_2'>";




echo "".output_text($status['msg'])."";




echo "</div>\n";




}




/*---------------------------------------*/









echo output_text($post['msg'])."<br />\n";









if (isset($user)) 




{




echo "<div style='text-align:right;'>";









//if ($ank['id']!=$user['id']) // Жалоба
//echo "<a href=\"?id=".intval($_GET['id'])."&amp;spam=$post[id]&amp;page=$page\"><img src='/style/icons/blicon.gif' alt='*' title='Это спам'></a> "; 





if (isset($user) && ($user['level']>$ank['level'] || $user['level']!=0 && $user['id']==$ank['id']))




echo "<a href='delete.php?id=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a>";









echo "</div>";




}









echo "  </div>\n";




}




echo "</table>\n";














if ($k_page>1)str("news.php?id=".intval($_GET['id']).'&amp;',$k_page,$page); // Вывод страниц



















if (isset($user))




{




echo "<form method=\"post\" name='message' action=\"?id_room=".intval($_GET['id_room'])."&amp;page=$page".$go_otv."\">\n";
if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))




include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php';




else




echo "$tPanel<textarea name=\"msg\">$respons_msg</textarea><br />\n";




echo "<input value=\"Отправить\" type=\"submit\" />\n";




echo "</form>\n";




}














echo'<div class="foot">';




echo "<img src='/style/icons/str.gif' alt='*'> <a href='index.php?exit'>Покинуть комнату</a><br />\n";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='index.php'>Список комнат</a><br />\n";
echo "</div>";




include_once '../sys/inc/tfoot.php';




?>
