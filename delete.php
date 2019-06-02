<?php session_start(); ?>
<?php require('dbconnect.php'); ?>
<?php
  if(isset($_SESSION['id'])){
    $id = $_GET['id'];

    //投稿を検査する
    $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
    $messages -> execute(array($id));
    $message = $messages -> fetch();

    if ($message['member_id']==$_SESSION['id']){
      //削除する
      $del = $db-> prepare('DELETE FROM posts WHERE id=?');
      $del -> execute(array($id));
    }
  }
  header('Location: index.php');
  exit();
 ?>
<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="style.css">

<title>よくわかるPHPの教科書</title>
</head>
<body>

</body>
</html>
