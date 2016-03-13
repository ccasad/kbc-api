<?php

class Form {

	public $id;
	public $date;
	public $comment;
	public $stats;

	public function __construct($arr=null) {
		if (isset($arr) && is_array($arr)) {
			$this->populate($arr);
		}
	}

	public function populate($arr) {
		$this->id = (isset($arr['id']) && strlen($arr['id'])) ? $arr['id'] : '';
		$this->date = (isset($arr['form_date']) && strlen($arr['form_date'])) ? $arr['form_date'] : '';
		$this->comment = (isset($arr['comment']) && strlen($arr['comment'])) ? $arr['comment'] : '';
		
		$formDataService = new FormDataService();
		$this->stats = $formDataService->getFormStats($this->id);
	}

}
