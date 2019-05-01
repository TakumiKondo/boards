<?php
include("config.php");
include("board.php");

//編集ボタン押下 or 変更ボタン押下以外のアクセスは受け付けない
if(!(isset($_POST['updatebtn']) || isset($_POST["update"]))){
    echo "不正なアクセスです。";
    exit();
}

// 編集画面のPOST
if (isset($_POST["update"])) {
    $board = new board($_POST);
    $board->updateLogic();
    $errorMessage = $board->getErrMessage();
    if ($board->isValid()) {
        header('Location: ./index.php');
    }
}

?>

<h1>編集フォーム</h1>
<form id="updateForm" name="updateForm" action="" method="POST">
    <fieldset>
        <!-- <legend>編集フォーム</legend> -->
        <div><font color="#ff0000">
            <?php
            if(isset($errorMessage)){
                foreach ($errorMessage as $key => $error) {
                    echo htmlspecialchars($error, ENT_QUOTES);
                }
            }
            ?>
        </font></div>
        <input type="hidden" name="id" value=<?= $_POST['id'] ?> >
        <label for="name">名前：</label><?= $_POST['name'] ?>
        <input type="hidden" name="name" value="<?= $_POST['name'] ?>" >
        <br><br>
        <label for="title">タイトル：</label><?= $_POST['title'] ?>
        <input type="hidden" name="title" value="<?= $_POST['title'] ?>" >
        <br><br>
        <label for="comment">コメント</label><textarea id="comment" name="comment" placeholder="コメントを入力（必須）" rows=10 cols=100><?= $_POST['comment'] ?></textarea>
        <br><br>
        <label for="auth_key">認証キー</label><input type="password" id="auth_key" name="auth_key" value="" placeholder="8桁の認証キーを入力"  maxlength="8">
        <input type="hidden" name="updated_date" value=<?= $_POST['updated_date'] ?> >
        <br><br>    
        <input type="submit" id="update" name="update" value="変更">
        <?= '<a href="/">一覧画面に戻る</a>' ?>
    </fieldset>
</form>

