<?php

class Role {

	const ROLE_ADMIN = 3;
	const ROLE_USER = 2;

	public $id;
	public $title;
	public $bitMask;

	public function __construct($arr=null) {
		if (isset($arr) && is_array($arr)) {
			$this->populate($arr);
		}
	}

	public function populate($arr) {
		$this->id = (isset($arr['id']) && strlen($arr['id'])) ? $arr['id'] : '';
		$this->title = (isset($arr['role']) && strlen($arr['role'])) ? $arr['role'] : '';
		$this->bitMask = (isset($arr['bit_mask']) && strlen($arr['bit_mask'])) ? $arr['bit_mask'] : '';
	}

}