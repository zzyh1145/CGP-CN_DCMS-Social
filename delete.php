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

// 获取群聊信息
$chat = dbassoc(dbquery("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id'])."' OR `id` = '".intval($_GET['id_room'])."' LIMIT 1"));

// 检查用户权限和请求参数
if (!isset($_GET['id']) && !is_numeric($_GET['id']) && $user['id'] != $chat['id_avtor'] || 
    !isset($_GET['id_room']) && !is_numeric($_GET['id_room']) && $user['level'] < 3) {
    header("Location: index.php?".SID);
    exit;
}

// 删除评论
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `privat_chat` WHERE `id` = '".intval($_GET['id'])."'"), 0) == 1) {
    $post = dbassoc(dbquery("SELECT * FROM `privat_chat` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
    $ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));

    // 检查用户权限
    if (isset($user) && ($user['level'] > $ank['level'])) {
        dbquery("DELETE FROM `privat_chat` WHERE `id` = '$post[id]'");
        $_SESSION['message'] = '评论成功删除';
    }

    // 重定向到之前的页面或首页
    if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL) {
        header("Location: ".$_SERVER['HTTP_REFERER']);
    } else {
        header("Location: index.php?".SID);
    }
    exit;
}

// 删除聊天室
if (isset($_GET['id_room']) && dbresult(dbquery("SELECT COUNT(*) FROM `privat_room` WHERE `id` = '".intval($_GET['id_room'])."'"), 0) == 1) {
    $post = dbassoc(dbquery("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id_room'])."' LIMIT 1"));

    dbquery("DELETE FROM `privat_room` WHERE `id` = '$post[id]'");
    dbquery("DELETE FROM `privat_chat` WHERE `id_room` = '$post[id]'");
    $_SESSION['message'] = '群聊已成功删除';
    header("Location: index.php?".SID);
    exit;
}
?>

