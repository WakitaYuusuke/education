<?php
session_start();
require_once "db_setting.php";
$UserID = $_SESSION["UserID"];
?>

<!DOCTYPE html>
<html>
 <head>
   <meta charset="utf-8">
   <title>コメント編集</title>
 </head>
 <body>
   <form method="post" action="editing.php">
     <h2>編集内容</h2>
     <textarea name="newComment"></textarea>
     <p><button type="submit">送信</button></p>

     <?php
     if(isset($_SESSION['inputFlg']) && $_SESSION['inputFlg'] == 0){
       echo "<h2>未入力です<h2>";
       $_SESSION['inputFlg'] = 1;
     }else if(isset($_SESSION['inputFlg']) && $_SESSION['inputFlg'] == 2){
       echo "<h2>更新しました<h2>";
       $_SESSION['inputFlg'] = 1;
     }
     ?>

   </form>
   <a href="index.php">TOPにもどる</a>
 </body>
</html>

<?php
if(isset($_REQUEST["newComment"])){
  $comment = $_REQUEST["newComment"];
  try{
    if(!(mb_strlen($comment) == 0)){
      $_SESSION['inputFlg'] = 2;
      $dbh = new PDO('mysql:host='.$host.';dbname='.$database, $user, $pass);
      $sql = "UPDATE test SET comment = :comment WHERE UserID = :UserID";
      $stmt = $dbh->prepare($sql);
      $params = array(':comment' => $comment, 'UserID' => $UserID);
      $stmt -> execute($params);
      header("Location: editing.php");
    }else if(mb_strlen($comment) == 0){
      $_SESSION['inputFlg'] = 0;
      header("Location: editing.php");
    }
    $dbh = null;
  }catch (PDOException $e) {
  print "エラー!: " . $e->getMessage() . "<br/>";
  die();
  }
}
?>
