<?php

class ReportController {

	public static function getReports() {
		$reportDataService = new ReportDataService();
		$result = $reportDataService->getReports();
		Utilities::returnJsonResult($result);
	}

	public static function getReport($id) {
		$reportDataService = new ReportDataService();
		$result = $reportDataService->getReport($id);
		Utilities::returnJsonResult($result);
	}

}

