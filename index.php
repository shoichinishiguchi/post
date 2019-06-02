<?php session_start(); ?>
<?php require('dbconnect.php'); ?>
<?php
   if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
     //ログインしている
     $_SESSION['time'] = time();

    $members = $db->prepare('SELECT *  FROM members WHERE id=?');
    $members -> execute(array($_SESSION['id']));
    $member = $members-> fetch();
  }else{
    //ログインしていない
    header('Location: login.php');
    exit();
  }
  //投稿を記録する
  if (!empty($_POST)) {
    if(!is_numeric($_GET['res'])){
      $_POST['reply_post_id']=0;
    }
    if ($_POST['message'] != ''){
      $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?,reply_post_id=? ,created=NOW()');
      $message -> execute(array(
        $member['id'],
        $_POST['message'],
        $_POST['reply_post_id']
      ));
      header('Location: index.php');
      exit();
    }
  }
   //投稿を取得する
   $page = $_GET['page'];
   if($page == ""){
     $page=1;
   }
   $page = max($page, 1);

   //最終ページを取得
   $counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
   $cnt = $counts->fetch();
   $maxPage = ceil($cnt['cnt']/5);
   $page = min($page, $maxPage);

   $start = ($page - 1) * 5;

   $posts = $db->prepare('SELECT m.name, m.picture,p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
   $posts -> bindParam(1, $start, PDO::PARAM_INT);
   $posts -> execute();

   if (isset($_GET['res'])){
     $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY  p.created DESC');
     $response -> execute(array($_GET['res']));

     $table = $response -> fetch();
     $message = '@'.$table['name'].' '.$table['message'];
   }
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
<header id="head">
<h1>ひとこと掲示板</h1>
</header>
<main>
  <div style="text-align:right"><a href="logout.php">ログアウト</a></div>
  <form action="" method="post">
    <dl>
      <dt><?php echo html($member['name']);?>さん、メッセージをどうぞ</dt>
      <dd>
        <textarea name="message" cols="50" rows="5"><?php echo html($message); ?></textarea>
        <input type="hidden" name="reply_post_id" value="<?php echo html($_GET['res']); ?>" />
      </dd>
    </dl>
    <div>
      <input type="submit" value="投稿する"/>
    </div>
  </form>
  <?php foreach($posts as $post): ?>
  <div class="msg">
    <?php if(strlen($post['picture'])>14): ?>
    <img src="member_picture/<?php echo html($post['picture']); ?>" width="48" height="48" alt="<?php echo html($post['name']); ?>"/>
    <?php endif; ?>
    <p><?php echo makeLink(html($post['message'])); ?><span class="name">(<?php echo html($post['name']);?>)</span>[<a href="index.php?res=<?php echo html($post['id']);?>">Re</a>]</p>
    <p class="day"><a href="view.php?id=<?php echo html($post['id']);?>"><?php echo html($post['created']); ?></a>
    <?php if($post['reply_post_id']>0): ?>
      <a href="view.php?id=<?php echo html($post['reply_post_id']); ?>">返信元のメッセージ</a>
  <?php endif; ?>
  <?php
  if($_SESSION['id'] == $post['member_id']):
  ?>
    [<a href="delete.php?id=<?php echo html($post['id']);?>" style="color: #F33;">削除</a>]
  <?php  endif; ?>
  </p>
  </div>
<?php endforeach; ?>
<ul class="paging">
  <?php if($page > 1):?>
    <li><a href="index.php?page=<?php echo ($page-1);?>">前のページへ</a></li>
  <?php else: ?>
    <li>前のページへ</li>
  <?php endif; ?>
  <?php if($page < $maxPage): ?>
    <li><a href="index.php?page=<?php echo ($page+1);?>">次のページへ</a>
    </li>
  <?php else: ?>
    <li>次のページへ</li>
  <?php endif; ?>
</ul>
</main>
</body>
</html>
