<?php

class Log {
	
  const ERROR     = 4;
  const WARNING   = 5;
  const INFO      = 7;

	public $id;
	public $message;
	public $levelId;
	public $url;
	public $urlData;
	public $class;
	public $function;
	public $args;
	
	public function __construct($arr=null) {
		if (isset($arr) && is_array($arr)) {
			$this->populate($arr);
		}
	}
	
	public function populate($arr) {
	
		$this->id = (isset($arr['id']) && strlen($arr['id'])) ? $arr['id'] : '';
		$this->message = (isset($arr['message']) && strlen($arr['message'])) ? $arr['message'] : '';
		$this->levelId = (isset($arr['level_id']) && strlen($arr['level_id'])) ? $arr['level_id'] : '';
		$this->url = (isset($arr['url']) && strlen($arr['url'])) ? $arr['url'] : '';
		$this->urlData = (isset($arr['url_data']) && strlen($arr['url_data'])) ? $arr['url_data'] : '';
		$this->class = (isset($arr['class']) && strlen($arr['class'])) ? $arr['class'] : '';
		$this->function = (isset($arr['function']) && strlen($arr['function'])) ? $arr['function'] : '';
		$this->args = (isset($arr['arguments']) && strlen($arr['arguments'])) ? $arr['arguments'] : '';

	}
	
}