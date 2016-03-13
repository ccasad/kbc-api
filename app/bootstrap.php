<?php
use Slim\Slim;

foreach (glob('app/controllers/*.php') as $filename) {
	require_once $filename;
}

foreach (glob('app/common/*.php') as $filename) {
	require_once $filename;
}

// need to manually call this because other dataservices extend this and it needs to be included before the ones extending it
require_once 'app/dataservice/base.dataservice.php';

foreach (glob('app/dataservice/*.php') as $filename) {
	require_once $filename;
}

// need to manually call this because other models extend this and it needs to be included before the ones extending it
require_once 'app/models/user.model.php';

foreach (glob('app/models/*.php') as $filename) {
	require_once $filename;
}

foreach (glob('app/routes/*.php') as $filename) {
	require_once $filename;
}

// set up the logging
$log = new LogDataService();
$GLOBALS['log'] = $log;

// get the user token for the user making the request to the API
$authDataService = new AuthDataService();
$user = $authDataService->getUserByToken();
$GLOBALS['current_user'] = $user;

// check the current_user to make sure they exist before moving on
Slim::getInstance()->hook('slim.before.dispatch', function () {
	$route = Slim::getInstance()->router()->getCurrentRoute()->getPattern();
	if (isset($route) && (strpos($route, 'login') === FALSE && strpos($route, 'forgot-password') === FALSE && strpos($route, 'reset-password') === FALSE && strpos($route, 'faqs') === FALSE && strpos($route, 'register') === FALSE)) {
		if (!isset($GLOBALS['current_user']) || !is_a($GLOBALS['current_user'], 'User')) {
			$GLOBALS['log']->warning('Unable to confirm current user. Permission denied.');
			Utilities::returnJsonResult(FALSE, 'Unable to confirm current user. Permission denied.', TRUE);
		}
	}
});
