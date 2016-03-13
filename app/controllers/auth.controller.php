<?php

class AuthController {

  public static function login() {
    $authDataService = new AuthDataService();
    $result = $authDataService->login();
    Utilities::returnJsonResult($result);
  }

	public static function logout() {
		$result = array();
		Utilities::returnJsonResult($result);
	}

  public static function register() {
    $authDataService = new AuthDataService();
    $result = $authDataService->register();
    Utilities::returnJsonResult($result);
  }

  public static function resetPassword() {
    $authDataService = new AuthDataService();
    $result = $authDataService->resetPassword();
    Utilities::returnJsonResult($result);
  }

	public static function authenticateUserToken() {
		$authDataService = new AuthDataService();

    if (!isset($_SERVER['HTTP_USERTOKEN']) || !$authDataService::verifyUserToken()) {
    	Utilities::returnJsonResult(FALSE, 'Unable to authenticate user token. Permission denied.', TRUE);
    }
  }

  public static function checkAuthorization($param) {
  	$authDataService = new AuthDataService();

  	$userId = Utilities::getAuthorizationParam($param);

  	if (!$authDataService->isAuthorized($userId)) {
    	Utilities::returnJsonResult(FALSE, 'You are not authorized to view the requested data. Permission denied.', TRUE);
    }
  }

  public static function checkAdminAuthorization() {
  	if (!Utilities::isCurrentUserAdmin()) {
    	Utilities::returnJsonResult(FALSE, 'You are not authorized to view the requested data. Permission denied.', TRUE);
    }
  }

  public static function checkUserAuthorization() {
    if (!Utilities::isCurrentUserAuthenticated()) {
      Utilities::returnJsonResult(FALSE, 'You are not authorized to view the requested data. Permission denied.', TRUE);
    }
  }

}
