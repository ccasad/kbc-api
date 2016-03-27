<?php

use Slim\Slim;
require_once('app/config/app.config.inc');
require_once('JSON.php');

class Utilities {

  public static function isCurrentUserAuthenticated() {
    if (isset($GLOBALS['current_user']) && is_a($GLOBALS['current_user'], 'User') ) {
      return TRUE;
    }
    return FALSE;
  }

  public static function isCurrentUserAdmin() {
    if (isset($GLOBALS['current_user']) && is_a($GLOBALS['current_user'], 'User') && $GLOBALS['current_user']->isAdmin()) {
      return TRUE;
    }
    return FALSE;
  }

	public static function checkRequiredParams($required_params) {
		$request = self::getRequestData();
		$request_array = array();
		if ($request) {
			$request_array = get_object_vars($request);
		}

		$required_params_msg = '';
		if (!empty($required_params)) {
			foreach ($required_params as $required_param) {
				if (!array_key_exists($required_param, $request_array)) {
					$required_params_msg .= $required_param.', ';
				}
			}

			if (!empty($required_params_msg)) {
				$required_params_msg .= ' are required';
			}
		}

		if (!empty($required_params_msg)) {
			if (SHOW_REQUIRED_PARAMETERS_ERROR) {
				echo $required_params_msg;
			}

			self::returnJsonResult(FALSE, 'All required parameters are not provided.', TRUE);
		}

		return TRUE;
	}

	public static function json_decode_nice($json, $assoc = FALSE){ 
    $json = str_replace(array("\n","\r"),"",$json); 
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json); 
    return json_decode($json,$assoc); 
	} 

	public static function getRequestData() {
		$request = Slim::getInstance()->request()->getBody();

		$jsonSrv = new Services_JSON();
		$json_decoded_request = $jsonSrv->decode($request);

		//$json_decoded_request = json_decode($request);
		return $json_decoded_request;
	}

	public static function getAuthorizationParam($paramName) {
		$params = new stdClass();

		// check the request body for the authorization param
		$request = Slim::getInstance()->request()->getBody();
		if ($request) {
			$params = json_decode($request);

			// note dynamic property
			if (isset($params->$paramName)) {
				return $params->$paramName;
			}
		}

		// not found in body so check in url for the authorization param
		$request = Slim::getInstance()->router->getCurrentRoute()->getParams();
		if ($request) {
			$params = (object)$request;

			// note dynamic property
			if (isset($params->$paramName)) {
				return $params->$paramName;
			}
		}

		// authorization param not found at all
		return 0;
	}

	public static function returnJsonResult($result, $msg = '', $dieWhenDone=FALSE) {
		try {
			if ($result === FALSE) {
				$arr = array('status' => array('success' => FALSE));
			} else {
				$arr = array('status' => array('success' => TRUE));
				if (!is_bool($result)) {
					$arr['data'] = $result;
				}
			}
			if (!empty($msg)) {
				$arr['status']['msg'] = $msg;
			}
			$json = json_encode($arr);

		} catch (Exception $e) {
			$json = json_encode(array('status' => array('success' => FALSE, 'msg' => 'Failed to encode data into valid JSON object.')));
		}

		$response = Slim::getInstance()->response();    
		
		$response->write(")]}',\n".$json);

		if ($dieWhenDone) {
			die();
		}
	}

	//checks if date is valid, if no returns FALSE else returns $date. borrowed from http://php.net/manual/en/function.checkdate.php
	public static function checkValidDateTime($date, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    if(($d && $d->format($format))) {
    	return $date;
    }
    return FALSE;
	}

	public static function checkValidYear($year) {
		//just making sure year is a 4 digit number
		if(is_int($year) && ($year > 999 && $year < 10000)) {
			return $year;
		}
		return FALSE;
	}

	public static function checkValidMonth($month) {
		if(is_int($month) && ($month > 0 && $month < 13)) {
			return $month;
		}
		return FALSE;
	}
	
	public static function getRandomString($length=10) {
    return bin2hex(openssl_random_pseudo_bytes(($length/2)));
	}

	public static function sendEmailRegistered($to) {

		$subject = 'KBC Registration';

		$body = <<<EOF
		<html>
			<head>
				<title>KBC Registration</title>
			</head>
			<body>
				<h3>Welcome to the Kelly's Bootcamp mobile app!</h3>
				<p>The purpose of this app is to help KBC participants keep track of the stats that they perform while at the "Lot". You can simply enter a stat you've performed and the app will store them for the next you perform the stat so you can see if you've improved.</p>
				<p>If you have any issues with this app or would like to suggest enhancements, send an email to Chris Casad, <a href="mailto:ccasad@gmail.com">ccasad@gmail.com</a>. Please don't bother Kelly with anything about this app. Thanks.</p>
			</body>
		</html>
EOF;

		return self::sendEmail($to, $subject, $body);
	}

	public static function sendEmailPasswordReset($to, $password) {

		$subject = 'KBC Password Reset';

		$body = <<<EOF
		<html>
			<head>
				<title>KBC Password Reset</title>
			</head>
			<body>
				<p>Looks like you forgot your password. No problem, it happens to the best of us.</p>
				<p><strong>Temporary password: $password</strong></p>
				<p>Use the temporary password to login to the KBC app and then be sure to change it immediately.</p>
			</body>
		</html>
EOF;

		return self::sendEmail($to, $subject, $body);
	}

	public static function sendEmail($to, $subject, $body) {
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <no-reply@kellysbootcamp.net>' . "\r\n";

		return mail($to, $subject, $body, $headers);
	}
}
