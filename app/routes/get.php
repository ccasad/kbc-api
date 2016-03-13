<?php

$app->get('/user/:id',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('UserController', 'getUser')
);

$app->get('/user-by-auth0-id/:id',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('UserController', 'getUserByAuth0Id')
);

$app->get('/avatar/:uniqueid', array('UserController', 'getUserAvatar'));

$app->get('/stats',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('StatController', 'getStats')
);

$app->get('/stat/:id',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('StatController', 'getStat')
);

$app->get('/user-stats/:userId',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('StatController', 'getUserStats')
);

$app->get('/user-stat/:userId/:userStatId',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('StatController', 'getUserStat')
);

$app->get('/user-stat-goal/:userId/:userStatId',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('StatController', 'getUserStatGoal')
);

$app->get('/forms',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('FormController', 'getForms')
);

$app->get('/form/:id',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('FormController', 'getForm')
);

$app->get('/form-stats/:formId',
	function() {
		//AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('FormController', 'getFormStats')
);

$app->get('/reports',
	function() {
		AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('ReportController', 'getReports')
);

$app->get('/report/:id',
	function() {
		AuthController::checkAuthorization('id', TRUE, FALSE);
	},
	array('ReportController', 'getReport')
);