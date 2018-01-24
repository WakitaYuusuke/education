<!DOCTYPE html>
<html>
  <head>
    <script src="jquery/jquery-3.2.1.min.js"></script>
    <link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.css">
    <meta charset="utf-8">
    <title>サンプル掲示板</title>
    <link rel="stylesheet" href="test.css">
    <link rel="stylesheet" href="common.css">
  </head>
  <body>
    <?php
    session_start();
    ?>

    <div class="contents">
      <h1>サンプル掲示板</h1>

      <hr>

      <form method="post" action="upload.php" class="form-inline">
        <div class="form-group">
		      <label class="sr-only" for="InputEmail">名前</label>
          <h2>名前</h2>
          <input type="text" name="n" id="inputName"/>
        </div>

        <h2>コメント</h2>
        <textarea name="c"><?php
        if(isset($_SESSION["resFlg"]) && $_SESSION["resFlg"] == "YES"){
          echo  "<<<".$_SESSION["UserID"];
          $_SESSION["resFlg"] = "NO";
        }?></textarea>
        <p><button type="submit" class="btn btn-default">送信</button></p>

        <?php
        if(isset($_SESSION["inputFlg"]) && $_SESSION["inputFlg"] == 0){
          echo "<h2>未入力の項目があります<h2>";
          $_SESSION["inputFlg"] = 1;
        }?>

      </form>

      <hr>

      <h2>登録データ</h2>

      <ul>

<?php
pager();

//submitから取得して振り分け
if(isset($_REQUEST["action"])){
  $flag = $_REQUEST["action"];
  $_SESSION["UserID"] = $_REQUEST["UserID"];

  switch ($flag){
    case "response":
      $_SESSION["resFlg"] = "YES";
      header("Location: index.php");
      break;
    case "editing":
      header("Location: editing.php");
      break;
    case "delete":
      header("Location: delete.php");
      break;
  }
}

function pager(){
  require_once "db_setting.php";

  // $totalRecorde レコード総数
  // $totalPage ページ総数
  // $limit一ページ当たりに表示できるレコード数
  // $currentPage 現在のページ
  // $nextOffsetNum 次のページの先頭コメントのレコードナンバー
  // $nextPage,$beforePage ページャーのリンク先

  $totalRecorde = 0;
  $totalPage = 0;
  $limit = 5;
  $nextOffsetNum = 0;
  $nextPage = "<a href='index.php?addPage=1'>次へ</a>";
  $beforePage = "<a href='index.php?addPage=-1'>前へ</a>";

  // 現在のページ数の初期化と更新
  if(!(isset($_SESSION["currentPage"]))){
    $_SESSION["currentPage"] = 0;
  }else if(isset($_GET["addPage"]) && isset($_SESSION["currentPage"])){
    $_SESSION["currentPage"] += $_GET['addPage'];
  }

  $nextOffsetNum += $_SESSION["currentPage"] * $limit;

  try {
    // データベース読み込み
    // レコード総数を取得
    $dbh = new PDO('mysql:host='.$host.';dbname='.$database, $user, $pass);
    $sql = "select *from test";
    $stmt = $dbh->query($sql);
    $stmt->execute();
    $totalRecorde = $stmt->rowCount();

    $totalPage = ceil($totalRecorde / $limit);

   //コメント一覧のselect文
   //現在ページが1ページ目のとき以外sessionからsql文をとる
   if($_SESSION["currentPage"] == 0){
     $sql = "SELECT * FROM test LIMIT ".$limit;
   }else{
     $sql = "SELECT * FROM test LIMIT ".$limit." OFFSET ".$nextOffsetNum;
   }

    foreach($dbh->query($sql) as $row) {
        // print_r($row);
        echo "<li class='sentence'>";
        echo "<p>".$row["nowDate"]."</p>";
        echo "<span class='id'>".$row["UserID"]."</span> : ";
        echo "<span class='name'>".$row["name"]."</span>";
        echo "<p class='comment'>".$row["comment"]."</p>";
        echo "<div class='del-btn'>";
        echo "<form method='post' action='index.php'>";
        echo "<input type='hidden' name='UserID' value='".$row["UserID"]."'>";
        echo "<button type='submit' name='action' value='response'>返信</button> ";
        echo "<button type='submit' name='action' value='editing'>編集</button> ";
        echo "<button type='submit' name='action' value='delete'>削除</button>";
        echo "</div>";
        echo "</form>";

    }
    $dbh = null;
  } catch (PDOException $e) {
    print "エラー!: " . $e->getMessage() . "<br/>";
    die();
  }

   //現在のページによってページャーリンクの表示を条件分岐
    if(!($totalPage <= 1)){
      echo "<center>";
      switch($_SESSION["currentPage"] + 1){
        case 1:
          echo $nextPage;
          break;
        case $totalPage:
          echo $beforePage;
          break;
        default:
          echo $beforePage;
          echo '　　　　　　　';
          echo $nextPage;
      }
      echo "</center>";
    }



    // デバッグ用出力
    // echo '<br>  ';
    // echo 'totalRecorde:'.$totalRecorde."<br>";
    // echo 'totalPage:'.$totalPage."<br>";
    // echo "currentPage:".$_SESSION['currentPage']."<br>";
    // echo "sql:".$sql."<br>";

    // session_destroy();

}

?>
      </ul>
    </div>
  </body>
</html>
