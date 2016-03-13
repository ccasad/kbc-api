<?php

class BaseDataService {

	public $database;
	private $request;

	public function __construct() {
		$this->database = new Database();
	}

	public function selectQuery($query, $params = array()) {
		$this->queryDatabase($query, $params);
		$result = $this->getResults();
		return $result;
	}

	// Should be used for SELECT operations
	public function getResults() {
		$result = $this->database->resultset();
		return $result;
	}

	public function queryDatabase($query, $params) {
		$this->database->query($query);

		if(!empty($params)) {
			$bind_params = $params['bind_params'];
			if (!empty($bind_params)) {
				foreach ($bind_params as $key => $value) {
					$this->database->bind($key, $value);
				}
			}
		}
	}

	public function getRequestData() {
		return Utilities::getRequestData();
	}

	// function is named in this manner since it alters the state of the database, Eg: INSERT, UPDATE, DELETE
	public function alterQuery($query, $params = array()) {
		$this->queryDatabase($query, $params);
		$result = $this->executeQuery();
		return $result;
	}

	// Should be used for INSERT, UPDATE, DELETE operations
	public function executeQuery() {
		$exec = $this->database->execute();
		return $exec;
	}

  public function lastInsertId() {
    $lastInsertId = $this->database->lastInsertId();
    return $lastInsertId;
  }
	
	public function rowCount() {
		return $this->database->rowCount();
	}
	
	public function beginTransaction() {
		return $this->database->beginTransaction();
	}
	
	public function inTransaction() {
		return $this->database->inTransaction();
	}

	public function cancelTransaction() {
		return $this->database->cancelTransaction();
	}
	
	public function endTransaction() {
		return $this->database->endTransaction();
	}
	
	public function quote($string) {
		return $this->database->quote($string);
	}

}