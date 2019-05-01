<?php
include("db.php");

class board {
	private $id;
	private $name;
	private $title;
	private $comment;
	private $authKey;
	private $validMessage;

	function __construct($param) {
		$this->id = $param['id'] ?? null;
		$this->name = $param['name'] ?? null;
		$this->title = $param['title'] ?? null;
		$this->comment = $param['comment'] ?? null;
		$this->authKey = $param['auth_key'] ?? null;
		$this->updatedDate = $param['updated_date'] ?? null;
		$this->validMessage = array();
	}

	/**
	* 削除処理の呼び出し
	*/
	public function deleteLogic(){
		$this->isValidAuthKeyMatch();
		if ($this->isValid()) {
			$this->delete();
		}
	}

	/**
	* 削除処理
	*/
	private function delete(){
		$db = new db();
		$pdo = $db->pdo();
	    $stmt = $pdo->prepare("DELETE FROM boards.boards WHERE id = :id");
	    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
	    $stmt->execute();
	}

	/**
	* 一覧取得処理の呼び出し
	* 
	*/
	public function listLogic(){
		return $this->findAll();
	}

	/**
	* 一覧取得処理
	* 
	*/
	private function findAll(){
		$db = new db();
		$sql = 'SELECT * FROM boards ORDER BY created_date DESC LIMIT 30';
		$pdo = $db->pdo();
		return $pdo->query($sql);
	}

	/**
	* 登録処理の呼び出し
	* 
	*/
	public function insertLogic(){
		$this->isValidName($this->name);
		$this->isValidTitle($this->title);
		$this->isValidComment($this->comment);
		$this->isValidAuthKey($this->authKey);

		if ($this->isValid()) {
			$db = new db();
			$pdo = $db->pdo();
			$this->insert($db);	
		}
	}

	/** 
	* 登録処理
	* 
	*/
	private function insert(){
		$db = new db();
        $pdo = $db->pdo();
		$stmt = $pdo->prepare("INSERT INTO boards (name, title, comment, auth_key, created_date) VALUES (:name, :title, :comment, :auth_key, :created_date)");
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $this->comment, PDO::PARAM_STR);
		$stmt->bindParam(':auth_key', $this->authKey, PDO::PARAM_STR);
		$date = new DateTime();
		$date = $date->format('Y-m-d H:i:s');
		$stmt->bindParam(':created_date', $date, PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	* 更新処理の呼び出し
	* 
	*/
	public function updateLogic(){
		$this->isValidAuthKeyMatch();
		$this->isValidUpdateDate();
		$this->isValidComment($this->comment);
		$this->isValidAuthKey($this->authKey);
		
		if ($this->isValid()) {
			$db = new db();
			$pdo = $db->pdo();
			$this->update($db);
		}
	}

	/**
	* 更新処理
	*/
	private function update() {
		$db = new db();
		$pdo = $db->pdo();
        $stmt = $pdo->prepare("UPDATE boards.boards SET comment = :comment, updated_date = now() WHERE id = :id");
        $stmt->bindParam(':comment', $this->comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
	}

	private function isValidAuthKeyMatch() {
		$db = new db();
		$pdo = $db->pdo();
	    $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND auth_key = :auth_key");
	    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
	    $stmt->bindParam(':auth_key', $this->authKey, PDO::PARAM_STR);
	    $stmt->execute();
	    if (empty($stmt->rowCount())) {
	        $this->validMessage[] = "認証キーが間違っています。";
	    }
	}

	private function isValidUpdateDate() {
		$db = new db();
		$pdo = $db->pdo();
	    $updatedDate = urldecode($this->updatedDate);
	    if (empty($updatedDate)) {
	        $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND updated_date is null");
	    }else{
	        $stmt = $pdo->prepare("SELECT id FROM boards.boards WHERE id = :id AND updated_date = :updated_date");
	        $stmt->bindParam(':updated_date', $updatedDate, PDO::PARAM_STR);      
	    }
	    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
	    $stmt->execute();
	    if (empty($stmt->rowCount())) {
	        $this->validMessage[] = "すでに更新済です。";
	    }
	}

	public function isValid(){
		if (empty($this->validMessage)) {
			return true;
		}
		return false;
	}

	public function getErrMessage() {
		return $this->validMessage;
	}

	private function isValidName($name) {
		if (empty(trim($name))) {
			$this->name = "名無しさん";
		}
	}

	private function isValidTitle($title) {
		if (empty(trim($title))) {
			$this->title = "タイトル無し";
		}
	}

	private function isValidComment($comment) {
		if (empty(trim($comment))) {
			$this->validMessage[] = "コメントが入力されていません。";
		}
	}

	private function isValidAuthKey($authKey) {
		if (empty(trim($authKey))) {
			$this->validMessage[] = "認証キーが入力されていません。";
		}
		if (strlen(trim($authKey)) <> 8) {
			$this->validMessage[] = "認証キーは8桁で入力してください。";
		}
	}

}