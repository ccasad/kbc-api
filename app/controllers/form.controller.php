<?php

class FormController {

	public static function getForms() {
		$formDataService = new FormDataService();
		$result = $formDataService->getForms();
		Utilities::returnJsonResult($result);
	}

	public static function getForm($id) {
		$formDataService = new FormDataService();
		$result = $formDataService->getForm($id);
		Utilities::returnJsonResult($result);
	}

	public static function getFormStats($formId) {
		$formDataService = new FormDataService();
		$result = $formDataService->getFormStats();
		Utilities::returnJsonResult($result);
	}
}

