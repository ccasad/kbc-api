<?php
/** 
 * APILogWriter: Custom log writer for our application
 *
 * We must implement write(mixed $message, int $level)
*/
class LogWriter {
	
  public static function write($message, $levelId = SlimLog::DEBUG) {
  	$logDataService = new LogDataService();
		$result = $logDataService->addLog($message, $levelId);
  }
  
}
