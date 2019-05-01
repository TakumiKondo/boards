<?php
include("config.php");
include("board.php");

$board = new board($_POST);

// 投稿ボタンが押された場合
if (isset($_POST["register"])) {
    $board->insertLogic();    // 登録処理
    $errorMessage = $board->getErrMessage();    // エラーメッセージ取得
}

// 削除ボタンが押された場合
if (isset($_POST["deletebtn"])) {
    $board->deleteLogic();
    $errorMessage = $board->getErrMessage();
}

// 一覧取得
$lists = $board->listLogic();

?>


<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>掲示板</title>
    </head>
    <body>
        <h1>投稿フォーム</h1>
        <form id="registerForm" name="registerForm" action="" method="POST">
            <fieldset>
                <legend>投稿フォーム</legend>
                <div><font color="#ff0000">
                <?php 
                
                if(isset($errorMessage)){
                    foreach ($errorMessage as $key => $msg) {
                       echo htmlspecialchars($msg, ENT_QUOTES); 
                    }
                }
                ?>
                </font></div>
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
                <label for="auth_key">認証キー</label><input type="password" id="auth_key" name="auth_key" value="" placeholder="8桁の認証キーを入力" maxlength="8">
                </div>
                <br>
                <input type="submit" id="register" name="register" value="投稿">
            </fieldset>
        </form>
        <br>
        <fieldset>          
            <legend>投稿一覧</legend>
	        
            <?php
            if (empty($lists)) {
            	exit();
            }
            while($row = $lists -> fetch(PDO::FETCH_ASSOC)) { ?>
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
				<form id="deleteForm" name="deleteForm" action="" method="POST">
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



