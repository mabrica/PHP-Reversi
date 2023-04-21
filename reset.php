<?php
session_start();

// セッションクリア
$_SESSION = [];

// 元のページにリダイレクト
header('Location: index.php');
exit;
