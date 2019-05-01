<?php
class db {
	private $db;

	function __construct(){
		$this->db = array();
		$this->db['host'] = "localhost";  // DBサーバのURL
		$this->db['user'] = "root";  // ユーザー名
		$this->db['pass'] = "root";  // ユーザー名のパスワード
		$this->db['dbname'] = "boards";  // データベース名
		$this->db['dbTypre'] = "mysql";  // データベース名
	}

	public function pdo(){
		$dsn = sprintf($this->db['dbTypre'] . ': host=%s; dbname=%s; charset=utf8', $this->db['host'], $this->db['dbname']);
		$pdo = new PDO($dsn, $this->db['user'], $this->db['pass'], array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
		return $pdo;
	}
}