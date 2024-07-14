<?php
// db_connect.php

// 数据库连接参数
$db_host = 'localhost'; // 数据库主机地址
$db_user = 'dcms_net_cn'; // 数据库用户名
$db_pass = '6cbGWYazifXcj2kd'; // 数据库密码
$db_name = 'dcms_net_cn'; // 数据库名称

// 创建数据库连接
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 检查连接是否成功
if (!$link) {
    die('数据库连接失败: ' . mysqli_connect_error());
}

// 设置字符集
mysqli_set_charset($link, 'utf8');

// 确保 $link 变量在其他文件中可用
global $link;

// 自定义函数
function dbresult($result, $row, $field = 0) {
  $numrows = mysqli_num_rows($result);
  if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
    mysqli_data_seek($result, $row);
    $resrow = (is_numeric($field)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
    if (isset($resrow[$field])) {
      return $resrow[$field];
    }
  }
  return null;
}

function dbquery($query) {
  global $link;
  return mysqli_query($link, $query);
}

function dbrows($result) {
  return mysqli_num_rows($result);
}

function dbarray($result) {
  return mysqli_fetch_array($result);
}

function dbassoc($result) {
  return mysqli_fetch_assoc($result);
}

function dbinsertid() {
  global $link;
  return mysqli_insert_id($link);
}
?>
