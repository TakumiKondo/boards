<?php
include("config.php");

$errorMessage = "";

//編集ボタン押下 or 変更ボタン押下以外のアクセスは受け付けない
if(!(isset($_POST['updatebtn']) || isset($_POST["update"]))){
    echo "不正なアクセスです。";
    exit();
}

// 編集画面のPOST
if (isset($_POST["update"])) {
    $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
    $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

    // 認証キーのチェック
    $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND auth_key = :auth_key");
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->bindParam(':auth_key', $_POST['auth_key'], PDO::PARAM_STR);
    $stmt->execute();;
    if (empty($stmt->rowCount())) {
        $errorMessage .= "認証キーが間違っています。";
    }

    // 更新日時チェック
    $updated_date = urldecode($_POST['updated_date']);
    if (empty($updated_date)) {
        $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND updated_date is null");
    }else{
        $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND updated_date = :updated_date");
        $stmt->bindParam(':updated_date', $updated_date, PDO::PARAM_STR);      
    }
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->execute();
    if (empty($stmt->rowCount())) {
        $errorMessage .= "すでに更新済です。";
    }

    if (empty($errorMessage)) {
        // 更新処理
        try{
            $stmt = $pdo->prepare("UPDATE boards.boards SET comment = :comment, updated_date = now() WHERE id = :id");
            $stmt->bindParam(':comment', $_POST['comment'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            $stmt->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
            die();
        }
        header('Location: ./index.php');
    }
}


?>

<h1>編集フォーム</h1>
<form id="updateForm" name="updateForm" action="" method="POST">
    <fieldset>
        <!-- <legend>編集フォーム</legend> -->
        <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
        <input type="hidden" name="id" value=<?= $_POST['id'] ?> >
        <label for="name">名前：</label><?= $_POST['name'] ?>
        <input type="hidden" name="name" value="<?= $_POST['name'] ?>" >
        <br><br>
        <label for="title">タイトル：</label><?= $_POST['title'] ?>
        <input type="hidden" name="title" value="<?= $_POST['title'] ?>" >
        <br><br>
        <label for="comment">コメント</label><textarea id="comment" name="comment" placeholder="コメントを入力（必須）" rows=10 cols=100><?= $_POST['comment'] ?></textarea>
        <br><br>
        <label for="auth_key">認証キー</label><input type="password" id="auth_key" name="auth_key" value="" placeholder="8桁の認証キーを入力">
        <input type="hidden" name="updated_date" value=<?= $_POST['updated_date'] ?> >
        <br><br>    
        <input type="submit" id="update" name="update" value="変更">
        <?= '<a href="' . $_SERVER['HTTP_REFERER'] . '">前に戻る</a>' ?>
    </fieldset>
</form>

