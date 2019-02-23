<?php
include("config.php");

if (isset($_POST["deletebtn"])) {

    $dsn = sprintf('mysql: host=%s; dbname=%s; charset=utf8', $db['host'], $db['dbname']);
    $pdo = new PDO($dsn, $db['user'], $db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

	// 認証キーのチェック
    $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND auth_key = :auth_key");
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->bindParam(':auth_key', $_POST['auth_key'], PDO::PARAM_STR);
    $stmt->execute();
    if (empty($stmt->rowCount())) {
    	echo "認証キーが間違っています。<br>";
    	echo '<a href="' . $_SERVER['HTTP_REFERER'] . '">前に戻る</a>';
    	exit();
    }

	// 削除処理
	try{
	    $stmt = $pdo->prepare("DELETE FROM boards.boards WHERE id = :id");
	    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
	    $stmt->execute();
	} catch(PDOException $e) {
		echo $e->getMessage();
		die();
	}
	
}else{
	echo "不正なアクセスです。";
	exit();
}

header('Location: ./index.php');


