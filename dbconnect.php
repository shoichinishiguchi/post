<?php
  try{
    $db = new PDO('mysql:dbname=mini_bbs;host=localhost;charset=utf8','root','root');
  }catch (PDOException $e){
    echo 'DB接続エラー:' .$e->getMessage();
  }
  // htmlspecialcharsのショートカット
  function html($value){
    return  htmlspecialchars($value, ENT_QUOTES);
  }
  function makeLink($value){
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)",'<a href="\1\2">\1\2</a>', $value);
  }
?>
