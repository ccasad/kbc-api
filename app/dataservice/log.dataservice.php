<?php
use Slim\Slim;

class LogDataService extends BaseDataService {

  // This method is used to write a log from the front end
  public function addLog() {
  	$request = $this->getRequestData();

		$message = $request->message;
		$levelId = $request->levelId;
		$class = $request->class;
		$function = $request->function;
		$args = $request->args;

  	$result = $this->insertLog($message, $levelId, $class, $function, $args, TRUE);
  
  	return $result;
  }

  public function info($message, $class=null, $function=null, $args=null) {

  	$result = $this->insertLog($message, Log::INFO, $class, $function, $args, FALSE);
  	
  	return $result;
  }

  public function warning($message, $class=null, $function=null, $args=null) {

  	$result = $this->insertLog($message, Log::WARNING, $class, $function, $args, FALSE);
  	
  	return $result;
  }

  public function error($message, $class=null, $function=null, $args=null) {

  	$result = $this->insertLog($message, Log::ERROR, $class, $function, $args, FALSE);
  	
  	return $result;
  }

	public function insertLog($message, $levelId, $class=null, $function=null, $args=array(), $frontEnd=FALSE) {

		$request = Slim::getInstance()->request();
		$rootUri = $request->getRootUri();
		$resourceUri = $request->getResourceUri();
		$url = $rootUri . $resourceUri;

		$requestData = null;
		if (!$frontEnd) {
			$requestData = Utilities::getRequestData();		
			if ($requestData) {				
				$requestData = var_export(Utilities::getRequestData(), true);
			}
		}

		if ($args && is_array($args) || is_object($args)) {
			$args = var_export($args, true);
		}

		$message = var_export($message, true);

		$query = <<<EOF
			INSERT INTO log 
			(level_id, message, url, url_data, class, function, arguments, front_end_yn)
			VALUES
			(:levelId, :message, :url, :urlData, :class, :function, :args, :frontEnd)
EOF;

		$params = array(
			'bind_params' => array(
				':message' => $message,
				':levelId' => $levelId,
				':url' => $url,
				':urlData' => $requestData,
				':class' => $class,
				':function' => $function,
				':args' => $args,
				':frontEnd' => ($frontEnd) ? 'Y' : 'N',
			)
		);

		return $this->alterQuery($query, $params);
	}

}
