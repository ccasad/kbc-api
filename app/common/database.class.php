<?php

// ref: http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/

require_once('app/config/app.config.inc');

class Database {

	private $host = DB_HOST;
	private $user = DB_USER;
	private $pass = DB_PASS;
	private $dbname = DB_NAME;
	private $dbh;
	private $error;
	private $stmt;
	private $show_db_error = SHOW_DB_ERROR;

	public function __construct() {
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;

		$options = array(
				PDO::ATTR_PERSISTENT => FALSE,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => FALSE
		);

		try {
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
		} catch (Exception $e) {
    		if ($this->show_db_error) {
    			echo $e->getMessage();
    		}
		}
	}


	public function query($query) {
		try {
			$this->stmt = $this->dbh->prepare($query);
		} catch (Exception $e) {
			$this->stmt = false;
			if ($this->show_db_error) {
				echo $e->getMessage();
			}
		}
	}

	public function bind($param, $value, $type = null) {
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):				
					$type = PDO::PARAM_INT;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		
		if ($this->stmt) {
			$this->stmt->bindValue($param, $value, $type);
		}
	}

	public function execute() {
		if ($this->stmt) {
			try {
				$exec = $this->stmt->execute();
			} catch (Exception $e) {
				$exec = false;
				if ($this->show_db_error) {
					echo $e->getMessage();
				}
			}
			return $exec;
		}
		return false;
	}

	public function resultset() {
		$exec = $this->execute();
		if (!$exec) {
			return $exec;
		}

		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function single() {
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount() {
		return (int)$this->stmt->rowCount();
	}

	public function lastInsertId() {
		return (int)$this->dbh->lastInsertId();
	}

	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}

	public function inTransaction(){
		return $this->dbh->inTransaction();
	}

	public function endTransaction(){
		return $this->dbh->commit();
	}

	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}

	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
	
	public function quote($string){
		return $this->dbh->quote($string);
	}

}

