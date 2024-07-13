<?php
// 引入必要的文件
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

// 仅允许注册用户操作
only_reg();

// 处理表单提交
if (isset($_POST['name']) && isset($_POST['password']) && isset($_POST['ok'])) {
    // 对输入进行转义
    $name = esc($_POST['name'], 1);
    $password = esc($_POST['password'], 1);

    // 检查群聊名称长度
    if (strlen2($name) > 56) {
        $err = '群聊名称过长';
    }
    if (strlen2($name) < 3) {
        $err = '群聊名称过短';
    }

    // 检查房间名称是否含有敏感词
    $mat = antimat($name);
    if ($mat) {
        $err[] = '群聊名称中发现敏感词：' . $mat;
    }

    // 检查密码长度
    if (strlen2($password) > 16) {
        $err = '密码长度不能超过16个字符';
    }
    if (strlen2($password) < 3) {
        $err = '密码长度过短';
    }

    // 再次转义名称和密码
    $name = my_esc($_POST['name']);
    $password = my_esc($_POST['password']);

    // 如果没有错误，则插入数据库
    if (!isset($err)) {
        mysql_query("INSERT INTO `privat_room` (`id`, `id_user`, `name`, `password`, `id_avtor`) VALUES('', '" . $user['id'] . "', '$name', '$password', '" . $user['id'] . "')");
        $id_room = mysql_insert_id();
        // mysql_query("INSERT INTO `privat_chat` (`id`, `id_user`, `msg`, `time`, `id_room`) VALUES('', '0', '房间成功创建', '$time', '" . $id_room . "')");

        // 设置成功消息并重定向
        $_SESSION['message'] = '房间成功创建，请记住密码：' . htmlspecialchars($password);
        header("Location: room.php?id_room=" . $id_room);
        exit;
    }
}

// 设置页面标题
$set['title'] = '创建房间';
include_once '../sys/inc/thead.php';

// 显示标题
title();

// 显示错误消息
err();

// 显示登录表单
aut();

// 显示创建房间的表单
echo "<form class='mess' method=\\"post\\" name='message' action=\\"?\\">\\n";
echo "房间名称：<br />\\n<input name=\\"name\\" size=\\"16\\" maxlength=\\"56\\" type=\\"text\\" /><br />\\n";
echo "密码：<br />\\n<input name=\\"password\\" size=\\"16\\" maxlength=\\"16\\" type=\\"text\\" /><br />\\n";
echo "<input value=\\"创建\\" type=\\"submit\\" name=\\"ok\\"/>\\n";
echo "</form>\\n";

// 页面底部链接
echo '<div class="foot">';
echo "<img src='/style/icons/str.gif' alt='*' /> <a href='index.php'>私人房间</a><br />\\n";
echo "</div>";

// 包含页脚文件
include_once '../sys/inc/tfoot.php';
?>
