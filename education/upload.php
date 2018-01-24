<?php
session_start();
$UserID      = time();
$name    = $_REQUEST["n"];
$comment = $_REQUEST["c"];
date_default_timezone_set('Asia/Tokyo');
$nowDate = new DateTime('now');

require_once "db_setting.php";

// データベース書き込み
if(!(mb_strlen($name)==0 || mb_strlen($comment)==0)){
  $_SESSION['inputFlg'] = 1;
  $dbh = new PDO('mysql:host='.$host.';dbname='.$database, $user, $pass);
  $stmt = $dbh -> prepare("INSERT INTO ".$table." (UserID,name,comment,nowDate) VALUES (:UserID, :name, :comment, :nowDate)");
  $stmt->bindValue(':UserID', $UserID, PDO::PARAM_INT);
  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
  $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
  $stmt->bindParam(':nowDate', $nowDate->format('Y年m月d日 H時i分s秒'), PDO::PARAM_STR);
  $stmt->execute();
}else{
  $_SESSION['inputFlg'] = 0;
}

// index.phpにリダイレクト
header("Location: index.php");
