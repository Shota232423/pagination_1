<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <?php
//db接続処理
try {
    $db = new PDO('mysql:dbname=testing;host=localhost;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
    echo 'DB接続エラーが起こりました。'. $e->getMessage();
}
if (isset($_GET['page'])) {
    $page=$_GET['page'];
    if ($page>=7) {
        //1〜6ページの時はページネーションが1〜10
        //7ページ目は2〜11になる → 7-5 と 7+4
        //8ページ目は3〜12になる → 8-5 と 8+4
        $page_first = $page - 5;
        $page_last = $page + 4;
    }
} else {
    $page=1;
}

//全データ数を取得
$count_data = $db->prepare("SELECT COUNT(*) FROM pagination");
$count_data->execute();
$count_data = $count_data->fetch(PDO::FETCH_COLUMN);

//3個ずつ表示する
$pagination=3;

//ページごとのデータのヘッドのインデックス
$page_per_data_head = $pagination*($page-1);

//必要なページ数を計算
$pages = ceil($count_data/$pagination);

//LIMIT0、10　の意味は 1番目のデータから10個データを取得する。
//0が１というのは配列のインデックスと同じイメージです。
$comments = $db->prepare("SELECT comment FROM pagination LIMIT ?,5");
$comments->bindParam(1, $page_per_data_head, PDO::PARAM_INT);
$comments->execute();
$comments_list = $comments->fetchAll(PDO::FETCH_ASSOC);

?>
    <div class="comment_area">
        <?php foreach ($comments_list as $comment) { ?>
        <p><?php echo $comment['comment'];  ?></p>
        <br>
        <?php } ?>
    </div>
    <!--10ページ以下だった場合は全て表示-->
    <div class="pagination_area">
        <?php if ($pages<=10) {?>
        <?php for ($i=1;$i<=$pages;$i++) { ?>
        <a href="http://localhost:8888/pagination/index.php?page=<?php echo $i ?>"><?php echo $i ?></a>
        <?php } ?>

        <!--10ページより多かった、かつ現在6ページ以下の場合-->
        <?php } elseif ($pages>10&&$page<=6) {?>
        <?php for ($i=1;$i<=10;$i++) { ?>
        <a href="http://localhost:8888/pagination/index.php?page=<?php echo $i ?>"><?php echo $i ?></a>
        <?php } ?>

        <!--10ページより多かった、かつ現在6ページより大きい場合-->
        <?php } elseif ($pages>10&&$page>6) {
    if ($page_last>=$pages) {
        $page_last = $pages;
    } ?>
        <?php for ($i=$page_first;$i<=$page_last;$i++) { ?>
        <a href="http://localhost:8888/pagination/index.php?page=<?php echo $i ?>"><?php echo $i ?></a>
        <?php } ?>
        <?php
} ?>
    </div>

</body>

</html>