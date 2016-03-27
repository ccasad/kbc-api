<?php

class Stat {

	public $id;
	public $name;
	public $description;
	public $formElementId;
	public $options;
	public $requiredYn;

	public $date;
	public $value;
	public $info;
	public $comment;
	public $exampleValue;

	public function __construct($arr=null) {
		if (isset($arr) && is_array($arr)) {
			$this->populate($arr);
		}
	}

	public function populate($arr) {
		$this->id = (isset($arr['id']) && strlen($arr['id'])) ? $arr['id'] : '';
		$this->name = (isset($arr['name']) && strlen($arr['name'])) ? $arr['name'] : '';
		$this->description = (isset($arr['description']) && strlen($arr['description'])) ? $arr['description'] : '';
		$this->formElementId = (isset($arr['form_element_id']) && strlen($arr['form_element_id'])) ? $arr['form_element_id'] : '';
		$this->options = (isset($arr['options']) && strlen($arr['options'])) ? $arr['options'] : '';
		$this->requiredYn = (isset($arr['required_yn']) && strlen($arr['required_yn'])) ? $arr['required_yn'] : '';

		$this->date = (isset($arr['stat_date']) && strlen($arr['stat_date'])) ? $arr['stat_date'] : '';
		$this->value = (isset($arr['stat_value']) && strlen($arr['stat_value'])) ? $arr['stat_value'] : '';
		$this->info = (isset($arr['stat_info']) && strlen($arr['stat_info'])) ? $arr['stat_info'] : '';
		$this->comment = (isset($arr['stat_comment']) && strlen($arr['stat_comment'])) ? $arr['stat_comment'] : '';
		$this->exampleValue = (isset($arr['example_value']) && strlen($arr['example_value'])) ? $arr['example_value'] : '';
	}

}