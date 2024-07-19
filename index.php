<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
if (isset($_GET['exit'])) unset($_SESSION['room_pass']);
$set['title'] = '个人群聊';
include_once '../sys/inc/thead.php';
title();
aut();

error_reporting(0);


// 加入申请
$k_post = dbquery('SELECT COUNT(*) FROM `privat_room`');
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `privat_room` ORDER BY `id` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>\n";
if ($k_post == 0) {
	echo '<div class="mess">';
	echo '没有群聊';
	echo '</div>';
}
while ($post = dbassoc($q)) {
	/*-----------分割线-----------*/
	if ($num == 0) {
		echo '<div class="nav2">';
		$num = 1;
	} elseif ($num == 1) {
		echo '<div class="nav1">';
		$num = 0;
	}
	/*---------------------------*/
	if ($post['id_avtor'] == $user['id'])
		echo "<img src='blue.png' alt='*' />";
	else
		echo "<img src='red.png' alt='*' />";
	echo "<a href='?id_rooms=$post[id]'>" . htmlspecialchars($post['name']) . "</a> \n";
	echo "(" . dbresult(dbquery("SELECT COUNT(`id_user`) FROM `privat_chat` WHERE `id_room` = '$post[id]' AND `time` > '" . (time() - 300) . "'  "), 0) . ")\n";
	if (@$_SESSION['room_pass'] == $post['password']) {
		echo '<font color = red>离开其他群聊</font>';
	}
	echo '<br />';
	if (isset($_GET['id_rooms']) && $_GET['id_rooms'] == $post['id']) {
		$pass = NULL;
		if ($user['level'] >= 3 || @$_SESSION['room_pass'] == $post['password'] || $user['id'] == $post['id_avtor']) $pass = $post['password'];
		echo '<form action = "room.php?id_room=' . $post['id'] . '" method = POST>';
		if (isset($_GET['id_rooms']) && $_GET['id_rooms'] == $post['id'] && !empty($_SESSION['room_pass']) && $_SESSION['room_pass'] != $post['password']) echo '<font color = red>你还没离开这个群聊</font> <a href="?exit">退出</a><br /><br />';
		echo '输入群聊邀请码 <br />
<input name = password size = 10 value = "' . $pass . '"> 
<input type = submit value = ok>
<br />';
	}
	echo "   </div>\n";
}
echo "</table>\n";
echo "<div class='foot'>";
echo " <img src='/style/icons/ok.gif' alt='*' />  <a href='?exit'>退出群聊</a><br/>";
echo " <img src='/style/icons/ok.gif' alt='*' />  <a href='add.php'>创建群聊</a><br/>";
echo "</div>";
echo '<br /><font color = dimgray><img src="blue.png" alt="*" /> - 我的群聊<br />
<img src="red.png" alt="*" /> - 其他群聊<br />&nbsp;
<font color = red></font> - 你已经在这个群聊里<br /><br />
<p>如果想切换群聊,请退出所有群聊↑</p></font><br />';
if ($k_page > 1) str('index.php?', $k_page, $page);
// 页面加载
include_once '../sys/inc/tfoot.php';
