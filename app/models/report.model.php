<?php

class Report {

	public $id;
	public $title;

	public function __construct($arr=null) {
		if (isset($arr) && is_array($arr)) {
			$this->populate($arr);
		}
	}

	public function populate($arr) {
		$this->id = (isset($arr['id']) && strlen($arr['id'])) ? $arr['id'] : '';
		$this->title = (isset($arr['title']) && strlen($arr['title'])) ? $arr['title'] : '';
	}

}

// CREATE TABLE `casad_kbc`.`lookup_report` (
//   `id` INT NOT NULL COMMENT '',
//   `title` VARCHAR(200) NOT NULL COMMENT '',
//   PRIMARY KEY (`id`)  COMMENT '');

