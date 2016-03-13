<?php

class StatController {

	public static function getStats() {
		$statDataService = new StatDataService();
		$result = $statDataService->getStats();
		Utilities::returnJsonResult($result);
	}

	public static function getStat($id) {
		$statDataService = new StatDataService();
		$result = $statDataService->getStat($id);
		Utilities::returnJsonResult($result);
	}

	public static function getUserStats($userId) {
		$statDataService = new StatDataService();
		$result = $statDataService->getUserStats($userId);
		Utilities::returnJsonResult($result);
	}

	public static function getUserStat($userId, $userStatId) {
		$statDataService = new StatDataService();
		$result = $statDataService->getUserStat($userId, $userStatId);
		Utilities::returnJsonResult($result);
	}

	public static function getUserStatGoal($userId, $userStatId) {
		$statDataService = new StatDataService();
		$result = $statDataService->getUserStatGoal($userId, $userStatId);
		Utilities::returnJsonResult($result);
	}

	public static function getAllUserStats() {
		$statDataService = new StatDataService();
		$result = $statDataService->getAllUserStats();
		Utilities::returnJsonResult($result);
	}

	public static function addUserStat() {
		$statDataService = new StatDataService();
		$result = $statDataService->addUserStat();
		Utilities::returnJsonResult($result);
	}

	public static function updateUserStat() {
		$statDataService = new StatDataService();
		$result = $statDataService->updateUserStat();
		Utilities::returnJsonResult($result);
	}

	public static function updateUserStatGoal() {
		$statDataService = new StatDataService();
		$result = $statDataService->updateUserStatGoal();
		Utilities::returnJsonResult($result);
	}

	public static function deleteUserStat($userStatId, $userId) {
		$statDataService = new StatDataService();
		$result = $statDataService->deleteUserStat($userStatId, $userId);
		Utilities::returnJsonResult($result);
	}

	public static function addStat() {
		$statDataService = new StatDataService();
		$result = $statDataService->addStat();
		Utilities::returnJsonResult($result);
	}

	public static function updateStat() {
		$statDataService = new StatDataService();
		$result = $statDataService->updateStat();
		Utilities::returnJsonResult($result);
	}
}

