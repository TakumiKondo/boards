<?php
include("config.php");

// エラーメッセージの初期化
$errorMessage = "";

// 投稿ボタンが押された場合
if (isset($_POST["register"])) {

    // バリデーション
    if (empty(trim($_POST["comment"]))) {  // emptyは値が空のとき
        $errorMessage .= 'コメントが未入力です。';
    }
    if (empty(trim($_POST["auth_key"]))) {  // emptyは値が空のとき
        $errorMessage .= '認証キーが未入力です。';
    }

    // 登録処理
    if (strlen(trim($errorMessage)) == 0) {
        // 投稿者名
        $name = "名無しさん";	// デフォルト値
        if (strlen(trim($_POST["name"])) != 0) {
        	$name = $_POST["name"];
        }
        // タイトル
        $title = "タイトル無し";	// デフォルト値
        if (strlen(trim($_POST["title"])) != 0) {
        	$title = $_POST["title"];
        }
        // コメント
		$comment = $_POST["comment"];
		// 認証キー		
        $auth_key = $_POST["auth_key"];

        $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);

        // 3. エラー処理
        try {
            $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

            // 登録処理
			$stmt = $pdo->prepare("INSERT INTO boards.boards (name, title, comment, auth_key, created_date)
									 VALUES (:name, :title, :comment, :auth_key, :created_date)");
			$stmt->bindParam(':name', $name, PDO::PARAM_STR);
			$stmt->bindParam(':title', $title, PDO::PARAM_STR);
			$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
			$stmt->bindParam(':auth_key', $auth_key, PDO::PARAM_STR);
			$date = new DateTime();
			$date = $date->format('Y-m-d H:i:s');
			$stmt->bindParam(':created_date', $date, PDO::PARAM_STR);
			$stmt->execute();
        } catch (PDOException $e) {
            $errorMessage = 'データベースエラー';
        }
    }
}

// 一覧取得
$dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
$pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
$sql = 'SELECT * FROM boards ORDER BY created_date DESC LIMIT 30';
$stmt = $pdo->query($sql);

?>


<!doctype html>
<html>
    <head>
    		<link rel="stylesheet" type="text/css" href="./css/modal.css">
    		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    		<script src="./js/modal.js"></script>
            <meta charset="UTF-8">
            <title>掲示板</title>
    </head>
    <body>
        <h1>投稿フォーム</h1>
        <form id="registerForm" name="registerForm" action="" method="POST">
            <fieldset>
                <legend>投稿フォーム</legend>
                <div><font color="#ff0000"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES); ?></font></div>
                <div><label for="name">名前</label><input type="text" id="name" name="name" placeholder="投稿者名を入力（任意）" value="" size=30></div>
                <br>
                <div>
                <label for="title" valign="center">タイトル</label><input type="text" id="title" name="title" value="" placeholder="タイトルを入力（任意）" size=100>    
                </div>
                <br>
                <div valign="top">
                <label for="comment">コメント</label><textarea id="comment" name="comment" value="" placeholder="コメントを入力（必須）" rows=10 cols=100></textarea>
                </div>
                <br>
                <div>
                <label for="auth_key">認証キー</label><input type="password" id="auth_key" name="auth_key" value="" placeholder="8桁の認証キーを入力">
                </div>
                <br>
                <input type="submit" id="register" name="register" value="投稿">
            </fieldset>
        </form>
        <br>
            <fieldset>          
                <legend>投稿一覧</legend>
		        
                <?php
                if (empty($stmt)) {
                	exit();
                }
                while($row = $stmt -> fetch(PDO::FETCH_ASSOC)) { ?>
			        <?php
						$id = $row['id'];
						$name = $row['name'];
						$title = $row["title"];
						$comment = $row["comment"];
						$created_date = $row["created_date"];
						$updated_date = $row["updated_date"];
						print('<p>投稿者:'.htmlspecialchars($name).'</p>');
						print('<p>件名:'.htmlspecialchars($title).'</p>');
						print('<p>コメント:</p>');
						print('<p>'.nl2br(htmlspecialchars($comment)).'</p>');
						print('<p>投稿日時：'.htmlspecialchars($created_date).'</p>');
						print('<p>更新日時：'.htmlspecialchars($updated_date).'</p>');
					?>
					<form id="updateForm" name="updateForm" action="update.php" method="POST">
		                <input type="hidden" id="id" name="id" value=<?= $id ?>>
		                <input type="hidden" id="name" name="name" value=<?= $name ?>>
		                <input type="hidden" id="title" name="title" value=<?= $title ?>>
		                <input type="hidden" id="comment" name="comment" value="<?= htmlspecialchars($comment) ?>">
		                <input type="hidden" id="updated_date" name="updated_date" value=<?= urlencode(htmlspecialchars($updated_date)); ?>>
		                <input type="submit" id="updatebtn" name="updatebtn" value="編集">
		        	</form>
		        	<br>
					<form id="deleteForm" name="deleteForm" action="delete.php" method="POST">
		                <input type="hidden" id="id" name="id" value=<?= $id ?>>
		                <input type="password" id="auth_key" name="auth_key" value="" >
		                <input type="submit" id="deletebtn" name="deletebtn" value="削除">
		        	</form>
                    <hr>
                <?php } // end while ?>

            </fieldset>
        </form>
    </body>
</html>



