<?php
include_once '../sys/inc/start.php'; // 引入启动文件
include_once '../sys/inc/compress.php'; // 引入压缩文件
include_once '../sys/inc/sess.php'; // 引入会话文件
include_once '../sys/inc/home.php'; // 引入主页文件
include_once '../sys/inc/settings.php'; // 引入设置文件
include_once '../group/db_connect.php'; // 引入数据库连接文件
include_once '../sys/inc/ipua.php'; // 引入IP和用户代理文件
include_once '../sys/inc/fnc.php'; // 引入功能函数文件
include_once '../sys/inc/user.php'; // 引入用户文件

// 从数据库中获取群聊信息
$chat = mysqli_fetch_assoc(mysqli_query("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));

// 检查是否提供了有效的群聊邀请码，用户权限，以及群聊是否存在
if (!isset($_GET['id']) && !is_numeric($_GET['id']) && $user['id'] != $chat['id_avtor'] || !isset($_GET['id']) && !is_numeric($_GET['id']) && $user['level'] < 3) {header("Location: index.php?".SID);exit;}
if (mysqli_result(mysqli_query("SELECT COUNT(*) FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1",$db), 0) == 0) {header("Location: index.php?".SID);exit;}
$privat = mysqli_fetch_assoc(mysqli_query("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));




// 如果设置了表单提交，处理表单数据
if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['ok']))
{
    $name = esc($_POST['name'], 1); // 清理输入
    $password = esc($_POST['password'], 1); // 清理输入

    // 检查输入的名称和密码是否符合要求
    if (strlen2($name) > 64) {$err = '群聊名称过长';}
    if (strlen2($name) < 3) {$err = '群聊名称过短';}
    $mat = antimat($name); // 检查是否包含特殊字符
    if ($mat) $err[] = '标题中包含特殊字符: ' . $mat;

    if (strlen2($password) > 26) {$err = '群聊邀请码过长';}
    if (strlen2($password) < 6) {$err = '群聊邀请码过短';}

    $name = my_esc($_POST['name']);
    $password = my_esc($_POST['password']);
    if (!isset($err)) { // 如果没有错误，更新数据库
        mysql_query("UPDATE `privat_room` SET `name` = '$name', `password` = '$password' WHERE `id` = '$privat[id]' LIMIT 1");
        $_SESSION['message'] = '群聊名称已成功更改';
        header("Location: room.php?id_room=$privat[id]");
        exit;
    }
}

// 设置页面标题
$set['title'] = '编辑群聊';
include_once '../sys/inc/thead.php'; // 引入头部文件

// 显示标题
title();

// 显示错误信息
err();

// 显示用户认证表单
aut(); // 显示用户认证表单

echo "<form class='mess' method='post' name='message' action='?id=" . htmlspecialchars($privat['id']) . "'>";
echo "群聊名称:<br />\\n";
echo '<input name="name" size="16" maxlength="32" value="' . htmlspecialchars($privat['name']) . '" type="text" />';
echo "<br />\\n";
echo "邀请码:<br />\\n";
echo '<input name="password" size="16" maxlength="26" value="' . htmlspecialchars($privat['password']) . '" type="text" />';
echo "<br />\\n"; 
echo "<input value=\"Готово\" type=\"submit\" name=\"ok\"/>\n";
echo "</form>\\n";

echo'<div class="foot">'; // 页脚
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='index.php'>群聊列表</a> | <a href='room.php?id_room=$privat[id]'>" . htmlspecialchars($privat['name']) . "</a><br />\\n";
echo "</div>";

include_once '../sys/inc/tfoot.php'; // 引入尾部文件
?>
