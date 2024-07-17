<?php
// 引入系统文件和配置
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../group/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

// 检查是否提供了聊天室ID并确保其
if (!isset($_GET['id_room']) || !is_numeric($_GET['id_room'])) {
    header("Location: index.php?".SID);
    exit;
} elseif (@$_SESSION['room_pass'] == NULL) {
    @$_SESSION['room_pass'] = htmlspecialchars($_POST['password']);
}
// 检查提供的聊天室ID和密码是否匹配数据库中的密码
if (mysqli_result(mysqli_query("SELECT COUNT(*) FROM `privat_room` WHERE `id` = '".intval($_GET['id_room'])."' AND `password` = '".my_esc(htmlspecialchars(@$_SESSION['room_pass']))."' LIMIT 1",$db), 0) == 0) {
    header("Location: index.php?".SID);
    exit;
}
$chat = mysqli_fetch_assoc(mysqli_query("SELECT * FROM `privat_room` WHERE `id` = '".intval($_GET['id_room'])."' LIMIT 1"));

// 举报模块
if (isset($_GET['spam']) && isset($user)) {
    $mess = mysqli_fetch_assoc(mysqli_query("SELECT * FROM `news_komm` WHERE `id` = '".intval($_GET['spam'])."' limit 1"));
    $spamer = get_user($mess['id_user']);

    // 如果用户尚未举报过这条消息
    if (mysqli_result(mysqli_query("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'news'"),0) == 0) {
        if (isset($_POST['msg'])) {
            if ($mess['id_user'] != $user['id']) {
                // 检查举报文本的长度和内容
                $msg = mysqli_real_escape_string($_POST['msg']);
                if (strlen2($msg) < 3) $err = '请详细说明举报原因';
                if (strlen2($msg) > 1024) $err = '文本长度多于1024字';
                if (isset($_POST['types'])) $types = intval($_POST['types']); else $types = '0';
                if (!isset($err)) {
                    mysqli_query("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$chat[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'news', '".my_esc($mess['msg'])."')");
                    $_SESSION['message'] = '举报成功';
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
    // 显示举报表单
    if (mysqli_result(mysqli_query("select COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'news'"),0) == 0) {
        echo "<div class='mess'>造谣是违法行为,可能导致封号。如果一个人经常骚扰,诈骗你，你可以把他添加到黑名单中.</div>";
        echo "<form class='nav1' method='post' action='?id=$chat[id]&amp;spam=$mess[id]&amp;page=".intval($_GET['page'])."'>\\n";
        echo "<b>用户:</b> ";
        echo " ".status($spamer['id'])."  ".group($spamer['id'])." <a href='/info.php?id=$spamer[id]'>$spamer[nick]</a>\\n";
        echo "".medal($spamer['id'])." ".online($spamer['id'])." (".vremja($mess['time']).")<br />";
        echo "<b>违规行为:</b> <font color='green'>".output_text($mess['msg'])."</font><br />";
        echo "原因:<br />\\n<select name='types'>\\n";
        echo "<option value='1' selected='selected'>投放广告</option>\\n";
        echo "<option value='2' selected='selected'>欺诈</option>\\n";
        echo "<option value='3' selected='selected'>恶意攻击</option>\\n";
        echo "<option value='0' selected='selected'>其他</option>\\n";
        echo "</select><br />\\n";
        echo "评论:$tPane";
        echo "<textarea name='msg'></textarea><br/>";
        echo "<input value='提交' type='submit'/>";
        echo "</form>";
    } else {
        echo "<div class='mess'>关于 <font color='green'>$spamer[nick]</font> 投诉将通知管理员火速处理</div>";
    }
    echo "<div class='foot'>";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id=$news[id]&amp;page=".intval($_GET['page'])."'>返回</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}

// 消息发送部分
if (isset($user)) {
    mysql_query("UPDATE `notification` SET `read` = '1' WHERE `type` = 'news_komm' AND `id_user` = '$user[id]' AND `id_object` = '$chat[id]'");
}
if (isset($_POST['msg']) && isset($user)) {
    $msg = $_POST['msg'];
    if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
    $mat = antimat($msg);
    if ($mat) $err[] = '消息中检测到不文明用语: ' . $mat;
    if (strlen2($msg) > 1024) $err = '消息太长(多于1024字)';
    elseif (strlen2($msg) < 1) $err = '不可以发送空白信息';
    elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `privat_chat` WHERE `id_room` = '".intval($_GET['id_room'])."' AND `id_user` = '$user[id]' AND `msg` = '".my_esc($msg)."' LIMIT 1"),0) != 0) {
        $err = '消息重复';
    } elseif (!isset($err)) {
        mysql_query("INSERT INTO `privat_chat` (`id_user`, `time`, `msg`, `id_room`) values('$user[id]', '$time', '".my_esc($msg)."', '".intval($_GET['id_room'])."')");
        mysql_query("UPDATE `user` SET `balls` = '".($user['balls'] + 1)."', `rating_tmp` = '".($user['rating_tmp'] + 1)."'  WHERE `id` = '$user[id]' LIMIT 1");
        //$_SESSION['message'] = 'good';
        header('Location: ?id_room='.intval($_GET['id_room']).'&page='.intval($_GET['page']).'');
        exit;
    }
}
$set['title'] = htmlspecialchars($chat['name']);
include_once '../sys/inc/thead.php';
title();
err();
aut();
// 显示聊天室的管理工具
if ($user['level'] >= 3 || $user['id'] == $chat['id_avtor']) {
    echo'<div class="c2">';
    echo '[<img src="/style/icons/edit.gif" alt="*"> <a href="edit.php?id='.$chat['id'].'">编辑</a>] ';
    echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="delete.php?id_room='.$chat['id'].'">删除</a>] ';
    echo "</div>";
}

// 显示聊天记录
$k_post = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat_chat` WHERE `id_room` = '".intval($_GET['id_room'])."' "), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = mysql_query("SELECT * FROM `privat_chat` WHERE `id_room` = '".intval($_GET['id_room'])."' ORDER BY `id` $sort LIMIT $start, $set[p_str]");
echo "<table class='post'>\\n";
if ($k_post == 0) {
    echo "<div class='mess'>";
    echo "没有消息\\n";
    echo '</div>';
}
while ($post = mysql_fetch_assoc($q)) {
    $ank = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
    echo " ".group($ank['id'])." <a href='/info.php?id=$ank[id]'>$ank[nick]</a>\\n";
    echo "".medal($ank['id'])." ".online($ank['id'])." (".vremja($post['time']).")<br />";
    // 显示用户状态
    $status = mysql_fetch_assoc(mysql_query("SELECT * FROM `status` WHERE `pokaz` = '1' AND `id_user` = '$ank[id]' LIMIT 1"));
    if ($status['msg'] && $set['st'] == 1) {
        echo "<div class='st_1'></div>";
        echo "<div class='st_2'>";
        echo "".output_text($status['msg'])."";
        echo "</div>\\n";
    }
    echo output_text($post['msg'])."<br />\\n";
    if (isset($user)) {
        echo "<div style='text-align:right;'>";
        // 显示删除按钮（如果用户权限足够）
        if (isset($user) && ($user['level'] > $ank['level'] || $user['level'] != 0 && $user['id'] == $ank['id']))
            echo "<a href='delete.php?id=$post[id]'><img src='/style/icons/delete.gif' alt='*'></a>";
        echo "</div>";
    }
    echo "  </div>\\n";
}
echo "</table>\\n";
// 显示分页导航
if ($k_page > 1) str("news.php?id=".intval($_GET['id']).'&amp;',$k_page,$page);

// 显示发送消息的表单
if (isset($user)) {
    echo "<form method=\\"post\\" name='message' action=\\"?id_room=".intval($_GET['id_room'])."&amp;page=$page".$go_otv."\\">\\n";
    if ($set['web'] && is_file(H.'style/themes/'.$set['set_them'].'/altername_post_form.php'))
        include_once H.'style/themes/'.$set['set_them'].'/altername_post_form.php'; else
        echo "$tPanel<textarea name=\\"msg\\">$respons_msg</textarea><br />\\n";
    echo "<input value=\\"发送\\" type=\\"submit\\" />\\n";
    echo "</form>\\n";
}
echo'<div class="foot">';
echo "<img src='/style/icons/str.gif' alt='*'> <a href='index.php?exit'>退出聊天室</a><br />\\n";
echo "<img src='/style/icons/str.gif' alt='*'> <a href='index.php'>聊天室列表</a><br />\\n";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>
