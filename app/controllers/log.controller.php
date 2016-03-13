<?php

class LogController {
	
	public static function getLatestLogs() {
		$logDataService = new LogDataService();
		$result = $logDataService->getLatestLogs();
		Utilities::returnJsonResult($result);
	}
	
	public static function addLog() {
		$required_params = array('message', 'levelId');
		$check_required_params = Utilities::checkRequiredParams($required_params);
	
		$logDataService = new LogDataService();
		$result = $logDataService->addLog();
	
		Utilities::returnJsonResult($result);
	}
	
}