<?php

class UserController {

	public static function getUsers() {
		$userDataService = new UserDataService();
		$result = $userDataService->getUsers();
		Utilities::returnJsonResult($result);
	}

	public static function getUser($id) {
		$userDataService = new UserDataService();
		$result = $userDataService->getUser($id);
		Utilities::returnJsonResult($result);
	}

	public static function getUserByAuth0Id($id) {
		$userDataService = new UserDataService();
		$result = $userDataService->getUserByAuth0Id($id);
		Utilities::returnJsonResult($result);
	}

	public static function addUser() {
		$required_params = array('firstName', 'lastName', 'email', 'password', 'role');
		$check_required_params = Utilities::checkRequiredParams($required_params);

		$userDataService = new UserDataService();
		$result = $userDataService->addUser();

		$msg = 'New User Account created successfully';
		// if the new user account was not created successfully, return FALSE with a message
		if ((!$result) || isset($result->success) && (!$result->success)) {
			$msg = isset($result->msg) ? $result->msg : 'Could not create a new account. Please try again later.';
			$result = FALSE;
		}

		Utilities::returnJsonResult($result, $msg);
	}

	public static function getUserAvatar($id) {
		$userDataService = new UserDataService();
		$result = $userDataService->getUserAvatar($id);
		Utilities::returnJsonResult($result);
	}

	public static function updateAccount() {
		$required_params = array('userId');
		$check_required_params = Utilities::checkRequiredParams($required_params);

		$userDataService = new UserDataService();
		$result = $userDataService->updateAccount();

		$msg = 'Account successfully updated';
		// if the update account did not succeed, return FALSE with a message
		if (isset($result->success) && (!$result->success)) {
			$msg = isset($result->msg) ? $result->msg : 'Could not update account. Please try again later.';
			$result = FALSE;
		}

		Utilities::returnJsonResult($result, $msg);
	}

}

